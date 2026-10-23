<?php
require_once __DIR__ . '/../config/portal_tiket.php';
require_once __DIR__ . '/../main_login/login_security.php';

const PT_PUBLIC_IDLE_TTL = 7200;

function pt_access_storage()
{
    $path = getenv('PORTAL_SECURITY_PATH') ?: __DIR__ . '/../storage/portal-security';
    if (!is_dir($path) && !@mkdir($path, 0700, true) && !is_dir($path)) throw new RuntimeException('Portal security directory unavailable');
    $deny = $path . '/.htaccess';
    if (!is_file($deny) && @file_put_contents($deny, "Require all denied\n", LOCK_EX) === false) throw new RuntimeException('Portal security protection unavailable');
    return $path;
}

function pt_public_session()
{
    if (session_status() === PHP_SESSION_ACTIVE && session_name() !== 'portal_tiket') {
        session_write_close(); session_id('');
    }
    if (session_status() !== PHP_SESSION_ACTIVE) {
        $path = pt_access_storage() . '/sessions';
        if (!is_dir($path) && !@mkdir($path, 0700) && !is_dir($path)) throw new RuntimeException('Portal sessions unavailable');
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_trans_sid', '0');
        ini_set('session.gc_maxlifetime', (string) PT_PUBLIC_IDLE_TTL);
        session_save_path($path);
        session_name('portal_tiket');
        session_set_cookie_params(['lifetime'=>0, 'httponly'=>true, 'samesite'=>'Lax',
            'secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'path'=>rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/') . '/']);
        if (!@session_start()) throw new RuntimeException('Portal session unavailable');
    }
    if (empty($_SESSION['pt_csrf'])) $_SESSION['pt_csrf'] = bin2hex(random_bytes(32));
    itrspi_login_no_cache();
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: no-referrer');
}

function pt_access_active($now = null, $touch = true)
{
    $now = $now ?? time();
    $last = $_SESSION['pt_public_access']['last_activity'] ?? null;
    if (!is_int($last) || $last > $now || $now - $last >= PT_PUBLIC_IDLE_TTL) {
        unset($_SESSION['pt_public_access']);
        return false;
    }
    if ($touch) $_SESSION['pt_public_access']['last_activity'] = $now;
    return true;
}

function pt_access_target($route, $query = [])
{
    $allowed = ['index.php'=>[], 'change-request.php'=>['form'], 'fitur-baru.php'=>['form'],
        'daftar-tiket.php'=>['q','status','jenis','page'], 'detail-tiket.php'=>['id','q','status','jenis','page'],
        'uat.php'=>['token'], 'verifikasi-ttd.php'=>['token','jenis','id','hash']];
    if (!isset($allowed[$route])) return 'index.php';
    $params = [];
    foreach ($allowed[$route] as $key) {
        if (is_string($query[$key] ?? null) && strlen($query[$key]) <= 1000) $params[$key] = $query[$key];
    }
    return $route . ($params ? '?' . http_build_query($params) : '');
}

function pt_access_challenge_url($target)
{
    $targets = $_SESSION['pt_access_targets'] ?? [];
    foreach ($targets as $id=>$entry) {
        if ($entry['expires'] <= time()) unset($targets[$id]);
        elseif ($entry['url'] === $target) return 'verifikasi-akses.php?lanjut=' . $id;
    }
    while (count($targets) >= 8) array_shift($targets);
    $id = bin2hex(random_bytes(16));
    $targets[$id] = ['url'=>$target, 'expires'=>time()+PT_PUBLIC_IDLE_TTL];
    $_SESSION['pt_access_targets'] = $targets;
    return 'verifikasi-akses.php?lanjut=' . $id;
}

function pt_access_next($id)
{
    $entry = is_string($id) ? ($_SESSION['pt_access_targets'][$id] ?? null) : null;
    return $entry && $entry['expires'] > time() ? $entry['url'] : 'index.php';
}

function pt_access_unavailable($json = false)
{
    http_response_code(503);
    header('Cache-Control: no-store');
    if ($json) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['message'=>'Pengamanan portal sedang tidak tersedia. Silakan coba lagi.']);
    } else {
        header('Content-Type: text/html; charset=utf-8');
        echo '<!doctype html><html lang="id"><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Verifikasi Portal | IT-RSPI</title><p>Pengamanan portal sedang tidak tersedia. Silakan coba lagi atau hubungi Unit IT.</p></html>';
    }
    exit;
}

function pt_public_require($json = false)
{
    try {
        pt_public_session();
        if (pt_access_active()) {
            if (!$json && $_SERVER['REQUEST_METHOD'] === 'GET') $_SESSION['pt_access_last_page'] = pt_access_target(basename($_SERVER['SCRIPT_NAME']), $_GET);
            return;
        }
        $target = $json ? ($_SESSION['pt_access_last_page'] ?? 'index.php') : pt_access_target(basename($_SERVER['SCRIPT_NAME']), $_GET);
        if ($json && basename($_SERVER['SCRIPT_NAME']) === 'uat-pegawai.php') $target = pt_access_target('uat.php', $_GET);
        $url = pt_access_challenge_url($target);
        if ($json) {
            http_response_code(403); header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['message'=>'Verifikasi CAPTCHA diperlukan. Sesi akses berlaku selama 2 jam tanpa aktivitas.', 'captcha_required'=>true, 'captcha_url'=>$url]);
        } else header('Location: ' . $url, true, 303);
        exit;
    } catch (Throwable $e) { error_log('Portal access security unavailable.'); pt_access_unavailable($json); }
}
