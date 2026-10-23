<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }

// Seluruh kredensial, transport, pegawai, dan tiket di sini berupa fixture terisolasi.
$project = dirname(__DIR__);
$tmp = dirname($project) . '/.codex_tmp/portal-telegram-' . bin2hex(random_bytes(5));
mkdir($tmp, 0777, true); mkdir($tmp . '/sessions');
ini_set('error_log', $tmp . '/errors.log');
putenv('TELEGRAM_BOT_TOKEN=000:fixture-only'); putenv('TELEGRAM_CHANNEL_ID=-100123');
putenv('TELEGRAM_CHAT_ID=-100456'); putenv('TELEGRAM_MESSAGE_THREAD_ID=77');
putenv('PORTAL_TELEGRAM_ENABLED=1'); putenv('PT_TELEGRAM_TEST_STATE=' . $tmp);
require_once $project . '/config/portal_tiket.php';
require_once __DIR__ . '/portal_test_helpers.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = new mysqli('localhost', getenv('DB_USERNAME') ?: 'root', getenv('DB_PASSWORD') ?: '');
$schema = 'codex_portal_telegram_' . bin2hex(random_bytes(5));
$db->query("CREATE DATABASE `$schema` CHARACTER SET utf8mb4"); $db->select_db($schema); $db->set_charset('utf8mb4');
putenv('PORTAL_DB_HOST=localhost'); putenv('PORTAL_DB_DATABASE=' . $schema);
putenv('PORTAL_PEGAWAI_HOST=localhost'); putenv('PORTAL_PEGAWAI_DATABASE=' . $schema);
$checks = 0; $servers = [];
function tg_check($condition, $label) {
    global $checks;
    if (!$condition) throw new RuntimeException($label);
    ++$checks;
}
function tg_events() {
    $file = getenv('PT_TELEGRAM_TEST_STATE') . '/events';
    return is_file($file) ? array_map(function ($line) { return json_decode($line, true); }, file($file, FILE_IGNORE_NEW_LINES)) : [];
}
function tg_mode($mode) { file_put_contents(getenv('PT_TELEGRAM_TEST_STATE') . '/mode', $mode); }
function tg_start($router, $root) {
    global $servers, $tmp;
    $socket = stream_socket_server('tcp://127.0.0.1:0', $errno, $error);
    $address = stream_socket_get_name($socket, false); fclose($socket);
    $server = proc_open([PHP_BINARY, '-d', 'session.save_path=' . $tmp . '/sessions',
        '-d', 'sys_temp_dir=' . $tmp, '-d', 'upload_tmp_dir=' . $tmp,
        '-S', $address, '-t', $root, $router],
        [0 => ['pipe', 'r'], 1 => ['file', $tmp . '/http.log', 'a'], 2 => ['file', $tmp . '/http.log', 'a']], $pipes);
    if (!is_resource($server)) throw new RuntimeException('Server unavailable');
    fclose($pipes[0]); $servers[] = $server;
    for ($i = 0; $i < 50; ++$i) {
        if ($probe = @stream_socket_client('tcp://' . $address, $errno, $error, .1)) { fclose($probe); return 'http://' . $address; }
        usleep(50000);
    }
    throw new RuntimeException('Server did not start');
}

$stub = <<<'PHP'
<?php
// Tes memakai sender/pembuat payload produksi; hanya transport jaringan yang diganti.
function telegram_http_post($endpoint, $payload, $timeout, $connectTimeout) {
    $path = getenv('PT_TELEGRAM_TEST_STATE');
    $observer = new mysqli('localhost', getenv('DB_USERNAME') ?: 'root', getenv('DB_PASSWORD') ?: '', getenv('PORTAL_DB_DATABASE'));
    $observer->set_charset('utf8mb4');
    $row = $observer->query('SELECT p.id_tiket,p.no_tiket,p.jenis_tiket,p.nama_pengaju,p.unit_pemohon,s.nama_sistem,d.nama_perubahan_fitur FROM tiket_pengajuan p JOIN tiket_detail_request d ON d.id_tiket=p.id_tiket JOIN master_sistem s ON s.id_sistem=p.id_sistem ORDER BY p.id_tiket DESC LIMIT 1')->fetch_assoc();
    $observer->close();
    file_put_contents($path . '/events', json_encode(compact('endpoint','payload','timeout','connectTimeout','row'), JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND | LOCK_EX);
    $mode = trim(file_get_contents($path . '/mode'));
    if ($mode === 'exception') throw new RuntimeException('Secret URL ' . $endpoint);
    if ($mode === 'timeout') return ['response'=>false,'http_code'=>0,'error_code'=>28];
    if ($mode === 'down') return ['response'=>'unavailable','http_code'=>503,'error_code'=>0];
    if ($mode === 'rejected') return ['response'=>'{"ok":false,"description":"Rejected"}','http_code'=>200,'error_code'=>0];
    if ($mode === 'invalid') return ['response'=>'not JSON','http_code'=>200,'error_code'=>0];
    return ['response'=>'{"ok":true,"result":{"message_id":123}}','http_code'=>200,'error_code'=>0];
}
PHP;
file_put_contents($tmp . '/stub.php', $stub); require $tmp . '/stub.php';
require $project . '/portal-tiket/request_helpers.php';
require $project . '/config/telegram.php';
try {
    cr_sql_file($db, $project . '/Database/2026-10-20_portal_tiket_layanan_it.sql', $schema);
    cr_sql_file($db, $project . '/Database/2026-10-20_portal_tiket_reservasi_nomor.sql', $schema);
    $db->query('CREATE TABLE tb_lokasi(lokasi_id INT PRIMARY KEY,nama_lokasi VARCHAR(150)) ENGINE=InnoDB');
    $db->query("INSERT INTO tb_lokasi VALUES (1,'Unit & Pelayanan')");
    $db->query('CREATE TABLE pegawai(id INT PRIMARY KEY,nama VARCHAR(150),stts_aktif VARCHAR(20)) ENGINE=InnoDB');
    $db->query("INSERT INTO pegawai VALUES (1,'Pengaju <Uji> & Tim','AKTIF')");
    $db->query("INSERT INTO master_sistem(nama_sistem,deskripsi,is_active) VALUES ('Sistem <Uji>','Fixture',1)");
    $_SESSION = ['pt_csrf' => str_repeat('a', 64)];
    $input = ['id_sistem'=>'1','unit_id'=>'1','pegawai_id'=>'1','kontak_pengaju'=>'081234567890',
        'nama_perubahan_fitur'=>"Judul <b>Uji</b> & Data\nBaru",'kondisi_masalah'=>'Masalah',
        'perubahan_diharapkan'=>'Harapan','dampak_modul'=>'Dampak','latar_belakang'=>'Latar',
        'deskripsi_alur_kerja'=>'Alur','target_pengguna'=>'Unit','output_diharapkan'=>'Output',
        'ttd_pengaju'=>cr_image(),'nama_pengaju'=>'FORGED','nama_sistem'=>'FORGED','unit_pemohon'=>'FORGED'];
    tg_mode('success');
    foreach ([['response'=>'{"ok":false}','http_code'=>200,'error_code'=>0],
        ['response'=>'invalid JSON','http_code'=>200,'error_code'=>0],
        ['response'=>'{"ok":true}','http_code'=>503,'error_code'=>0],
        ['response'=>false,'http_code'=>0,'error_code'=>28]] as $failure) {
        tg_check(!telegram_message_result($failure)['ok'], 'HTTP, JSON, and API success must all agree');
    }
    foreach (['Change Request', 'Fitur Baru'] as $type) {
        $draft = pt_reserve($db, $type); $before = count(tg_events());
        tg_check(count(tg_events()) === $before, 'Opening form does not send');
        try { pt_save_request($db, $draft, $input, 'forged', $type); throw new RuntimeException('CSRF bypass'); }
        catch (DomainException $e) { tg_check(count(tg_events()) === $before, 'Invalid CSRF does not send'); }
        $db->query("CREATE TRIGGER tg_fail BEFORE INSERT ON tiket_detail_request FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Test failure'");
        try { pt_save_request($db, $draft, $input, $_SESSION['pt_csrf'], $type); throw new RuntimeException('Rollback missing'); }
        catch (mysqli_sql_exception $e) {
            tg_check(count(tg_events()) === $before && !pt_rows($db, 'SELECT id_tiket FROM tiket_pengajuan WHERE no_tiket=?', [$draft['no_tiket']]), 'Rolled-back ticket does not send');
        }
        $db->query('DROP TRIGGER tg_fail');
        foreach (['success','rejected','down','timeout','invalid','exception'] as $mode) {
            tg_mode($mode); $draft = pt_reserve($db, $type); $before = count(tg_events());
            $id = pt_save_request($db, $draft, $input, $_SESSION['pt_csrf'], $type);
            $events = tg_events(); $event = end($events); $text = $event['payload']['text'];
            tg_check(count($events) === $before + 1, $type . ' sends one attempt: ' . $mode);
            tg_check((int) $event['row']['id_tiket'] === $id && $event['row']['no_tiket'] === $draft['no_tiket'], 'Independent DB observer sees committed header AND detail before send');
            tg_check(str_contains($text, $draft['no_tiket']) && str_contains($text, $type)
                && str_contains($text, 'Sistem &lt;Uji&gt;') && str_contains($text, 'Pengaju &lt;Uji&gt; &amp; Tim')
                && str_contains($text, 'Unit &amp; Pelayanan') && str_contains($text, 'Judul &lt;b&gt;Uji&lt;/b&gt; &amp; Data Baru'), 'Canonical fields and escaped HTML');
            tg_check(!str_contains($text, 'FORGED') && !str_contains($text, '6281234567890') && !str_contains($text, 'token_uat'), 'Notification excludes spoofed fields and private contacts/tokens');
            tg_check(str_contains($text, $type === 'Fitur Baru' ? 'Nama Fitur Baru:' : 'Judul Perubahan:')
                && $event['payload']['parse_mode'] === 'HTML' && mb_strlen(html_entity_decode(strip_tags($text))) <= 4096, 'Correct title, HTML mode, within Telegram limit');
            tg_check($event['payload']['chat_id'] === '-100123' && $event['payload']['message_thread_id'] === '77'
                && $event['timeout'] === 3 && $event['connectTimeout'] === 2, 'Configured target/topic and bounded timeout');
            tg_check(pt_rows($db, 'SELECT status_tiket FROM tiket_pengajuan WHERE id_tiket=?', [$id])[0]['status_tiket'] === 'Menunggu Analisis', 'Ticket survives notification outcome: ' . $mode);
            tg_check(pt_save_request($db, $draft, $input, $_SESSION['pt_csrf'], $type) === $id && count(tg_events()) === $before + 1, 'Retry does not duplicate notification: ' . $mode);
        }
    }
    $before = count(tg_events()); putenv('PORTAL_TELEGRAM_ENABLED=0');
    $id = pt_save_new_feature($db, pt_reserve($db, 'Fitur Baru'), $input, $_SESSION['pt_csrf']);
    tg_check($id > 0 && count(tg_events()) === $before, 'Explicit disable preserves ticket');
    putenv('PORTAL_TELEGRAM_ENABLED=1'); putenv('TELEGRAM_BOT_TOKEN=');
    $id = pt_save_new_feature($db, pt_reserve($db, 'Fitur Baru'), $input, $_SESSION['pt_csrf']);
    tg_check($id > 0 && count(tg_events()) === $before, 'Missing token preserves ticket without transport');
    putenv('TELEGRAM_BOT_TOKEN=000:fixture-only'); tg_mode('success');
    putenv('TELEGRAM_CHANNEL_ID='); putenv('TELEGRAM_MESSAGE_THREAD_ID=');
    tg_check(telegram_send_channel_message('Legacy plain <text>')['ok'], 'Legacy sender succeeds');
    $events = tg_events(); $event = end($events);
    tg_check($event['payload'] === ['chat_id'=>'-100456','text'=>'Legacy plain <text>'], 'Legacy plain text and CHAT_ID fallback unchanged');
    putenv('TELEGRAM_CHANNEL_ID=-100123');

    // Form/controller sebenarnya melalui HTTP; stub transport di-load router, bukan input pengguna.
    file_put_contents($tmp . '/router.php', '<?php require __DIR__."/stub.php"; return false;');
    putenv('PORTAL_SECURITY_PATH=' . $tmp . '/security');
    $base = tg_start($tmp . '/router.php', dirname($project)) . '/it-rspi2/portal-tiket/';
    $cookie = cr_public_access_cookie($base, $tmp . '/security');
    $request = function ($url, $fields = null) use (&$cookie) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true,CURLOPT_HEADER=>true,CURLOPT_COOKIE=>$cookie,CURLOPT_TIMEOUT=>8]);
        if ($fields !== null) curl_setopt_array($ch,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>http_build_query($fields)]);
        $raw = curl_exec($ch); if ($raw === false) throw new RuntimeException('HTTP request failed');
        $status = curl_getinfo($ch,CURLINFO_HTTP_CODE); $length = curl_getinfo($ch,CURLINFO_HEADER_SIZE); curl_close($ch);
        $headers = substr($raw,0,$length);
        if (preg_match('/Set-Cookie: (portal_tiket=[^;\r\n]+)/i',$headers,$m)) $cookie=$m[1];
        preg_match('/Location: ([^\r\n]+)/i',$headers,$m);
        return ['status'=>$status,'location'=>$m[1]??'','body'=>substr($raw,$length)];
    };
    foreach (['change-request.php','fitur-baru.php'] as $file) {
        foreach (['success','timeout','exception'] as $mode) {
            tg_mode($mode); $before = count(tg_events()); $get = $request($base . $file);
            tg_check($get['status'] === 303, 'HTTP form reserves ticket');
            $url = $base . $get['location']; $get = $request($url);
            preg_match('/name="csrf" value="([a-f0-9]+)"/',$get['body'],$csrf);
            tg_check($get['status'] === 200 && isset($csrf[1]) && count(tg_events()) === $before, 'GET form does not notify');
            $result = $request($url, $input + ['csrf'=>$csrf[1]]);
            tg_check($result['status'] === 303 && $result['location'] !== '' && count(tg_events()) === $before + 1, 'Real controller keeps success PRG on ' . $file . '/' . $mode);
            $success = $request($base . $result['location']);
            tg_check($success['status'] === 200 && str_contains($success['body'], 'Pengajuan berhasil disimpan')
                && !str_contains($success['body'], 'fixture-only'), 'Guest sees success without Telegram errors/secrets');
            tg_check($request($url, $input + ['csrf'=>$csrf[1]])['status'] === 303 && count(tg_events()) === $before + 1, 'HTTP duplicate POST does not resend');
        }
    }

    // Transport produksi terhadap server loopback, termasuk timeout nyata, tanpa Telegram asli.
    file_put_contents($tmp . '/api.php', <<<'PHP'
<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path === '/timeout') sleep(5);
if ($path === '/down') { http_response_code(503); echo 'Unavailable'; return; }
header('Content-Type: application/json');
echo json_encode(['ok'=>true,'received'=>$_POST]);
PHP
    );
    $api = tg_start($tmp . '/api.php', $tmp);
    foreach (['/success','/down','/stream','/timeout'] as $path) {
        $worker = 'require ' . var_export($project . '/config/telegram.php', true) . ';'
            . '$start=microtime(true);$r=telegram_http_post(' . var_export($api . $path,true)
            . ',["chat_id"=>"fixture","text"=>"Uji & teks"],3,2);echo json_encode(["result"=>$r,"elapsed"=>microtime(true)-$start]);';
        $command = $path === '/stream' ? [PHP_BINARY,'-n','-r',$worker] : [PHP_BINARY,'-r',$worker];
        $process = proc_open($command,[0=>['pipe','r'],1=>['pipe','w'],2=>['file',$tmp.'/errors.log','a']],$pipes);
        fclose($pipes[0]); $value=json_decode(stream_get_contents($pipes[1]),true); fclose($pipes[1]); $exit=proc_close($process);
        tg_check($exit===0 && is_array($value),'Real transport worker runs');
        if ($path==='/success' || $path==='/stream') {
            $body=json_decode($value['result']['response'],true);
            tg_check($body['received']['text']==='Uji & teks' && telegram_message_result($value['result'])['ok'],'Production POST via cURL/stream encodes fields and parses success');
        } elseif ($path==='/down') tg_check(!telegram_message_result($value['result'])['ok'] && $value['result']['http_code']===503,'Production HTTP503 classified as failure');
        else tg_check($value['elapsed']>=2.5 && $value['elapsed']<4.5 && $value['result']['error_code']===28,'Real cURL timeout bounded to three seconds');
    }
    $worker = 'require ' . var_export($project . '/config/telegram.php',true)
        . ';echo json_encode(telegram_send_channel_message("No cURL",["require_curl"=>true]));';
    $process = proc_open([PHP_BINARY,'-n','-r',$worker],[0=>['pipe','r'],1=>['pipe','w'],2=>['file',$tmp.'/errors.log','a']],$pipes);
    fclose($pipes[0]); $value=json_decode(stream_get_contents($pipes[1]),true); fclose($pipes[1]); $exit=proc_close($process);
    tg_check($exit===0 && !$value['ok'] && $value['reason']==='curl_unavailable','Missing cURL fails gracefully without network call');
    $logs = file_get_contents($tmp . '/errors.log') . file_get_contents($tmp . '/http.log');
    tg_check(!str_contains($logs,'000:fixture-only') && !str_contains($logs,'api.telegram.org/bot') && !str_contains($logs,'081234567890'), 'Logs redact bot URL/token and private contact');
    echo 'PASS ' . $checks . ' Telegram checks; no real Telegram message sent.' . PHP_EOL;
} finally {
    foreach ($servers as $server) { proc_terminate($server); proc_close($server); }
    if (preg_match('/^codex_portal_telegram_[a-f0-9]{10}$/D',$schema)) $db->query("DROP DATABASE `$schema`");
    // Hanya direktori fixture unik yang dibuat tes ini.
    $remove = function ($path) use (&$remove, $tmp) {
        if ($path !== $tmp && strpos($path, $tmp . DIRECTORY_SEPARATOR) !== 0 && strpos($path, $tmp . '/') !== 0) throw new RuntimeException('Unsafe cleanup path');
        foreach (new FilesystemIterator($path) as $item) { if ($item->isDir()) $remove($item->getPathname()); else unlink($item->getPathname()); }
        rmdir($path);
    };
    $remove($tmp);
}
