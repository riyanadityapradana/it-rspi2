<?php
// Jalankan melalui CLI. Seluruh sesi, penyimpanan, dan koneksi DB adalah fixture terisolasi.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$root = sys_get_temp_dir() . '/itrspi-login-test-' . bin2hex(random_bytes(6));
mkdir($root . '/main_login', 0777, true);
mkdir($root . '/config');
mkdir($root . '/sessions');
mkdir($root . '/storage/login-security', 0777, true);
foreach (['form_login.php', 'form_login_action.php', 'login_security.php', 'captcha.php'] as $file) {
    copy(__DIR__ . '/../main_login/' . $file, $root . '/main_login/' . $file);
}

file_put_contents($root . '/config/koneksi.php', <<<'PHP'
<?php
file_put_contents(__DIR__ . '/../db-opened', '1', FILE_APPEND);
// Meniru koneksi.php asli yang mendefinisikan kredensial database pada scope pemanggil.
$username = 'fixture-db-user';
$password = 'fixture-db-password';
class FixtureResult {
    private $user;
    public function __construct($user) { $this->user = $user; }
    public function fetch_assoc() { return $this->user; }
}
class FixtureStatement {
    private $params;
    public function bind_param($types, &...$params) { $this->params = &$params; }
    public function execute() {
        file_put_contents(__DIR__ . '/../bound-username', $this->params[0]);
    }
    public function get_result() {
        $roles = ['demo' => 'Staff', 'head' => 'Kepala Ruangan', 'unknown-role' => 'Unknown', 'spaced' => 'Staff'];
        $name = $this->params[0];
        if (!isset($roles[$name])) { return new FixtureResult(null); }
        return new FixtureResult(['id_user' => 17, 'nip' => '123456', 'username' => $name,
            'nama_lengkap' => 'Uji Login', 'email' => 'test@example.invalid', 'no_hp' => '080000000',
            'role' => $roles[$name], 'foto' => null, 'status' => 'Aktif',
            'password' => password_hash($name === 'spaced' ? ' correct-password ' : 'correct-password', PASSWORD_DEFAULT)]);
    }
    public function close() {}
}
class FixtureConnection {
    public function prepare($sql) {
        if ($sql !== 'SELECT * FROM tb_user WHERE username = ? LIMIT 1') {
            throw new RuntimeException('Query harus menggunakan parameter terikat');
        }
        return new FixtureStatement();
    }
}
$config = new FixtureConnection();
PHP
);
// Endpoint ini HANYA dibuat di direktori fixture sementara, tidak di aplikasi.
file_put_contents($root . '/fixture.php', <<<'PHP'
<?php
session_start();
if (isset($_GET['expire'])) {
    foreach ($_SESSION['itrspi_login_captchas'] as &$challenge) { $challenge['expires'] = time() - 1; }
}
header('Content-Type: application/json');
echo json_encode($_SESSION);
PHP
);

$socket = stream_socket_server('tcp://127.0.0.1:0', $errno, $error);
$address = stream_socket_get_name($socket, false);
fclose($socket);
$log = $root . '/server.log';
$server = proc_open([PHP_BINARY, '-d', 'session.save_path=' . $root . '/sessions', '-S', $address, '-t', $root],
    [0 => ['pipe', 'r'], 1 => ['file', $log, 'a'], 2 => ['file', $log, 'a']], $pipes, $root);
fclose($pipes[0]);
$base = 'http://' . $address;
$checks = 0;

function check($condition, $message)
{
    global $checks;
    if (!$condition) { throw new RuntimeException($message); }
    $checks++;
    echo 'PASS ' . $message . PHP_EOL;
}

function request(&$cookie, $path, $fields = null, $headers = [])
{
    global $base;
    $curl = curl_init($base . $path);
    curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => true,
        CURLOPT_TIMEOUT => 10, CURLOPT_COOKIE => $cookie, CURLOPT_HTTPHEADER => $headers]);
    if ($fields !== null) {
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($fields));
    }
    $raw = curl_exec($curl);
    if ($raw === false) { throw new RuntimeException(curl_error($curl)); }
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $head = substr($raw, 0, curl_getinfo($curl, CURLINFO_HEADER_SIZE));
    $body = substr($raw, curl_getinfo($curl, CURLINFO_HEADER_SIZE));
    curl_close($curl);
    if (preg_match('/Set-Cookie: (PHPSESSID=[^;\r\n]+)/i', $head, $match)) { $cookie = $match[1]; }
    return ['status' => $status, 'headers' => $head, 'body' => $body];
}

function form(&$cookie)
{
    $page = request($cookie, '/main_login/form_login.php');
    preg_match('/name="csrf_token" value="([^"]+)"/', $page['body'], $csrf);
    preg_match('/name="captcha_id" value="([^"]+)"/', $page['body'], $id);
    $session = json_decode(request($cookie, '/fixture.php')['body'], true);
    return ['csrf_token' => $csrf[1], 'captcha_id' => $id[1],
        'captcha' => $session['itrspi_login_captchas'][$id[1]]['code'],
        'username' => 'demo', 'password' => 'correct-password'];
}

function store()
{
    global $root;
    $db = new PDO('sqlite:' . $root . '/storage/login-security/attempts.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}

function resetWait() { store()->exec('DELETE FROM login_attempts'); }
function databaseOpens() {
    global $root;
    return file_exists($root . '/db-opened') ? strlen(file_get_contents($root . '/db-opened')) : 0;
}

function removeFixture($path)
{
    global $root;
    // Semua penghapusan terbatas pada fixture yang dibuat oleh proses ini.
    if (strpos($path, $root) !== 0) { throw new RuntimeException('Unsafe cleanup path'); }
    foreach (new FilesystemIterator($path) as $item) {
        if ($item->isDir()) { removeFixture($item->getPathname()); }
        else { unlink($item->getPathname()); }
    }
    rmdir($path);
}

$failed = false;
try {
    for ($i = 0; $i < 50; $i++) {
        $probe = @stream_socket_client('tcp://' . $address, $errno, $error, 0.1);
        if ($probe) { fclose($probe); break; }
        usleep(100000);
    }
    $cookie = '';
    $a = form($cookie);
    $page = request($cookie, '/main_login/form_login.php');
    check($page['status'] === 200 && strpos($page['headers'], 'no-store') !== false, 'form dan token tidak di-cache');
    $image = request($cookie, '/main_login/captcha.php?id=' . $a['captcha_id']);
    check($image['status'] === 200 && substr($image['body'], 0, 8) === "\x89PNG\r\n\x1a\n", 'CAPTCHA berupa PNG dan kode tab sebelumnya tetap berlaku');
    check(strpos($image['headers'], 'no-store') !== false, 'gambar CAPTCHA tidak di-cache');
    $otherCookie = '';
    check(request($otherCookie, '/main_login/captcha.php?id=' . $a['captcha_id'])['status'] === 404, 'CAPTCHA terikat sesi');
    check(request($cookie, '/main_login/form_login_action.php')['status'] === 302, 'GET handler tidak memproses login');
    $invalidCsrf = $a;
    $invalidCsrf['csrf_token'] = 'forged';
    request($cookie, '/main_login/form_login_action.php', $invalidCsrf);
    check(databaseOpens() === 0 && (int) store()->query('SELECT COUNT(*) FROM login_attempts')->fetchColumn() === 0,
        'CSRF palsu ditolak sebelum kueri kredensial');

    $badCaptcha = $a;
    $badCaptcha['captcha'] = 'WRONG';
    request($cookie, '/main_login/form_login_action.php', $badCaptcha);
    $until = (int) store()->query('SELECT blocked_until FROM login_attempts')->fetchColumn();
    check($until - time() >= 29 && databaseOpens() === 0, 'CAPTCHA salah memulai jeda 30 detik tanpa kueri kredensial');
    $page = request($cookie, '/main_login/form_login.php');
    check(strpos($page['body'], 'id="loginButton" disabled') !== false && strpos($page['body'], 'Kode CAPTCHA salah') !== false,
        'refresh menampilkan pesan, hitung mundur, dan tombol terkunci');
    $newCookie = '';
    $b = form($newCookie);
    $b['username'] = 'different-user';
    $blocked = request($newCookie, '/main_login/form_login_action.php', $b, ['X-Forwarded-For: 203.0.113.99']);
    check($blocked['status'] === 303 && strpos($blocked['headers'], 'Retry-After:') !== false && databaseOpens() === 0,
        'sesi baru, username berbeda, header IP palsu, dan POST langsung tetap dibatasi');
    check((int) store()->query('SELECT blocked_until FROM login_attempts')->fetchColumn() === $until,
        'percobaan selama jeda tidak memperpanjang waktu');
    check(isset(json_decode(request($newCookie, '/fixture.php')['body'], true)['itrspi_login_captchas'][$b['captcha_id']]),
        'percobaan saat jeda tidak menghabiskan CAPTCHA yang belum dipakai');

    echo 'Menunggu batas 30 detik asli...' . PHP_EOL;
    while (time() <= $until) { usleep(200000); }
    $b['username'] = 'demo';
    $b['password'] = 'wrong-password';
    $b['captcha'] = strtolower($b['captcha']);
    request($newCookie, '/main_login/form_login_action.php', $b);
    check(databaseOpens() === 1 && (int) store()->query('SELECT blocked_until FROM login_attempts')->fetchColumn() > time(),
        'setelah 30 detik boleh mencoba; CAPTCHA huruf kecil diterima dan password salah memulai jeda baru');
    check(file_get_contents($root . '/bound-username') === 'demo',
        'username formulir tidak tertimpa variabel username koneksi database');
    $session = json_decode(request($newCookie, '/fixture.php')['body'], true);
    check(empty($session['id_user']), 'password salah tidak membuat sesi login');
    resetWait();
    request($newCookie, '/main_login/form_login_action.php', $b);
    check(databaseOpens() === 1, 'CAPTCHA yang sudah dipakai tidak dapat diulang');

    resetWait();
    $expired = form($cookie);
    request($cookie, '/fixture.php?expire=1');
    request($cookie, '/main_login/form_login_action.php', $expired);
    check(databaseOpens() === 1 && store()->query('SELECT COUNT(*) FROM login_attempts')->fetchColumn() == 1,
        'CAPTCHA kedaluwarsa ditolak dan memulai jeda');
    resetWait();
    $missing = form($cookie);
    unset($missing['captcha']);
    request($cookie, '/main_login/form_login_action.php', $missing);
    check(databaseOpens() === 1, 'menghapus field CAPTCHA tidak melewati pemeriksaan');
    resetWait();
    $malformed = form($cookie);
    $malformed['captcha'] = ['invalid'];
    $malformed['username'] = ['invalid'];
    check(request($cookie, '/main_login/form_login_action.php', $malformed)['status'] === 303 && databaseOpens() === 1,
        'payload array ditolak tanpa fatal error');

    resetWait();
    $success = form($cookie);
    $oldCookie = $cookie;
    $result = request($cookie, '/main_login/form_login_action.php', $success);
    $session = json_decode(request($cookie, '/fixture.php')['body'], true);
    check(strpos($result['headers'], '../staff/dashboard_staff.php?unit=beranda') !== false && $cookie !== $oldCookie,
        'login benar menuju beranda dan mengganti ID sesi');
    check($session['id_user'] === 17 && $session['role'] === 'Staff' && $session['nip'] === '123456'
        && $session['email'] === 'test@example.invalid' && array_key_exists('foto', $session) && $session['status'] === 'Aktif',
        'seluruh identitas dan hak akses Staff dipertahankan');
    check(store()->query('SELECT COUNT(*) FROM login_attempts')->fetchColumn() == 0 && !isset($session['itrspi_login_captchas'], $session['itrspi_login_csrf']),
        'login berhasil menghapus jeda dan challenge');

    // Proses PHP berbeda mensimulasikan worker/sesi yang mencoba bersamaan.
    $workers = [];
    $workerCode = '$_SERVER["REMOTE_ADDR"]="198.51.100.27"; require ' . var_export($root . '/main_login/login_security.php', true)
        . '; echo json_encode(itrspi_login_reserve_attempt());';
    for ($i = 0; $i < 8; $i++) {
        $process = proc_open([PHP_BINARY, '-r', $workerCode], [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['file', $log, 'a']], $workerPipes);
        fclose($workerPipes[0]);
        $workers[] = [$process, $workerPipes[1]];
    }
    $allowed = 0;
    foreach ($workers as [$process, $output]) {
        $value = json_decode(stream_get_contents($output), true);
        fclose($output);
        $exit = proc_close($process);
        if ($exit !== 0 || !is_array($value)) { throw new RuntimeException('Worker gagal'); }
        if ($value['wait'] === 0) { $allowed++; }
    }
    check($allowed === 1, 'delapan worker bersamaan hanya mendapat satu reservasi');
    $anotherCookie = '';
    $another = form($anotherCookie);
    request($anotherCookie, '/main_login/form_login_action.php', array_merge($another, ['captcha' => 'WRONG']));
    check(store()->query('SELECT COUNT(*) FROM login_attempts')->fetchColumn() == 2, 'IP berbeda memiliki jeda sendiri');
    resetWait();

    $headCookie = '';
    $head = form($headCookie);
    $head['username'] = 'head';
    $headResult = request($headCookie, '/main_login/form_login_action.php', $head);
    $headSession = json_decode(request($headCookie, '/fixture.php')['body'], true);
    check(strpos($headResult['headers'], '../admin/dashboard_admin.php?unit=beranda') !== false
        && $headSession['role'] === 'Kepala Ruangan', 'Kepala Ruangan diarahkan ke dashboard admin');
    $invalidRoleCookie = '';
    $invalidRole = form($invalidRoleCookie);
    $invalidRole['username'] = 'unknown-role';
    request($invalidRoleCookie, '/main_login/form_login_action.php', $invalidRole);
    $invalidRoleSession = json_decode(request($invalidRoleCookie, '/fixture.php')['body'], true);
    check(empty($invalidRoleSession['id_user']) && store()->query('SELECT COUNT(*) FROM login_attempts')->fetchColumn() == 1,
        'role tidak dikenal ditolak sebelum membuat sesi dan dikenai jeda');
    resetWait();
    $spaceCookie = '';
    $space = form($spaceCookie);
    $space['username'] = 'spaced';
    $space['password'] = ' correct-password ';
    request($spaceCookie, '/main_login/form_login_action.php', $space);
    check(json_decode(request($spaceCookie, '/fixture.php')['body'], true)['username'] === 'spaced',
        'spasi yang menjadi bagian password tetap dapat digunakan');
    $injectionCookie = '';
    $injection = form($injectionCookie);
    $injection['username'] = "' OR 1=1 -- ";
    request($injectionCookie, '/main_login/form_login_action.php', $injection);
    check(empty(json_decode(request($injectionCookie, '/fixture.php')['body'], true)['id_user'])
        && file_get_contents($root . '/bound-username') === trim($injection['username']),
        'payload SQL injection diperlakukan sebagai nilai username biasa');
    resetWait();
    $beforeUnavailable = databaseOpens();
    rename($root . '/storage/login-security', $root . '/storage/unavailable');
    $unavailable = request($anotherCookie, '/main_login/form_login.php');
    $rejected = request($anotherCookie, '/main_login/form_login_action.php', $another);
    check(strpos($unavailable['body'], 'Pengamanan login sedang tidak tersedia') !== false
        && strpos($unavailable['body'], 'id="loginButton" disabled') !== false && $rejected['status'] === 303 && databaseOpens() === $beforeUnavailable,
        'penyimpanan rusak menolak login dengan pesan umum');
    rename($root . '/storage/unavailable', $root . '/storage/login-security');
    echo 'Selesai: ' . $checks . ' pemeriksaan lulus. Database asli tidak diakses.' . PHP_EOL;
} catch (Throwable $e) {
    $failed = true;
    fwrite(STDERR, 'FAIL ' . $e->getMessage() . PHP_EOL);
    fwrite(STDERR, file_get_contents($log));
} finally {
    proc_terminate($server);
    proc_close($server);
    gc_collect_cycles();
    removeFixture($root);
}
exit($failed ? 1 : 0);
