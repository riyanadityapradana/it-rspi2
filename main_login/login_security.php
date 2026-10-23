<?php
// CAPTCHA dan pembatasan login lokal; tidak memerlukan layanan internet.
const ITRSPI_LOGIN_DELAY = 30;
const ITRSPI_CAPTCHA_TTL = 300;

function itrspi_login_no_cache()
{
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
}

function itrspi_login_security_db($storage = null)
{
    static $databases = [];
    $storage = $storage ?? __DIR__ . '/../storage/login-security/attempts.sqlite';
    if (!isset($databases[$storage])) {
        $db = new PDO('sqlite:' . $storage);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->exec('PRAGMA busy_timeout = 5000');
        $db->exec('CREATE TABLE IF NOT EXISTS login_attempts (
            ip_hash TEXT PRIMARY KEY,
            blocked_until INTEGER NOT NULL,
            lease TEXT NOT NULL
        )');
        $databases[$storage] = $db;
    }
    return $databases[$storage];
}

function itrspi_login_ip_key()
{
    // Header X-Forwarded-For dapat dipalsukan; gunakan alamat koneksi langsung.
    return hash('sha256', isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown');
}

function itrspi_login_wait($storage = null)
{
    $stmt = itrspi_login_security_db($storage)->prepare('SELECT blocked_until FROM login_attempts WHERE ip_hash = ?');
    $stmt->execute([itrspi_login_ip_key()]);
    return max(0, (int) $stmt->fetchColumn() - time());
}

function itrspi_login_reserve_attempt($storage = null)
{
    $db = itrspi_login_security_db($storage);
    // Reservasi atomik mencegah dua sesi/IP yang sama lolos secara bersamaan.
    $db->exec('BEGIN IMMEDIATE');
    try {
        $remaining = itrspi_login_wait($storage);
        if ($remaining > 0) {
            $db->exec('ROLLBACK');
            return ['wait' => $remaining, 'lease' => null];
        }
        $lease = bin2hex(random_bytes(16));
        $stmt = $db->prepare('INSERT OR REPLACE INTO login_attempts (ip_hash, blocked_until, lease) VALUES (?, ?, ?)');
        $stmt->execute([itrspi_login_ip_key(), time() + ITRSPI_LOGIN_DELAY, $lease]);
        if (random_int(1, 100) === 1) {
            $cleanup = $db->prepare('DELETE FROM login_attempts WHERE blocked_until < ?');
            $cleanup->execute([time() - 86400]);
        }
        $db->exec('COMMIT');
        return ['wait' => 0, 'lease' => $lease];
    } catch (Throwable $e) {
        $db->exec('ROLLBACK');
        throw $e;
    }
}

function itrspi_login_clear_attempt($lease, $storage = null)
{
    // Jangan hapus reservasi baru jika pemeriksaan sebelumnya berjalan lama.
    $stmt = itrspi_login_security_db($storage)->prepare('DELETE FROM login_attempts WHERE ip_hash = ? AND lease = ?');
    $stmt->execute([itrspi_login_ip_key(), $lease]);
}

function itrspi_login_csrf_token()
{
    if (empty($_SESSION['itrspi_login_csrf'])) {
        $_SESSION['itrspi_login_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['itrspi_login_csrf'];
}

function itrspi_login_create_captcha($sessionKey = 'itrspi_login_captchas')
{
    if (!extension_loaded('gd')) {
        throw new RuntimeException('Ekstensi GD diperlukan untuk CAPTCHA login.');
    }
    $challenges = isset($_SESSION[$sessionKey]) && is_array($_SESSION[$sessionKey]) ? $_SESSION[$sessionKey] : [];
    foreach ($challenges as $key => $challenge) {
        if ($challenge['expires'] <= time()) {
            unset($challenges[$key]);
        }
    }
    // Beberapa tab dapat memiliki kode sendiri; batasi ukuran sesi.
    while (count($challenges) >= 5) {
        array_shift($challenges);
    }
    $alphabet = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < 5; $i++) {
        $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
    }
    $id = bin2hex(random_bytes(16));
    $challenges[$id] = ['code' => $code, 'expires' => time() + ITRSPI_CAPTCHA_TTL];
    $_SESSION[$sessionKey] = $challenges;
    return $id;
}

function itrspi_login_check_captcha($id, $answer, $sessionKey = 'itrspi_login_captchas')
{
    $challenge = isset($_SESSION[$sessionKey][$id]) ? $_SESSION[$sessionKey][$id] : null;
    // Sekali pakai, termasuk bila jawaban atau kredensial salah.
    unset($_SESSION[$sessionKey][$id]);
    return $challenge !== null && $challenge['expires'] > time()
        && strlen($answer) === 5 && hash_equals($challenge['code'], strtoupper($answer));
}

function itrspi_login_post_string($key)
{
    return isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : '';
}

function itrspi_login_reject($message, $failedLease = null)
{
    if ($failedLease !== null) {
        try {
            // Hitung jeda dari saat kegagalan diketahui, termasuk jika kueri lambat.
            $stmt = itrspi_login_security_db()->prepare('UPDATE login_attempts SET blocked_until = ? WHERE ip_hash = ? AND lease = ?');
            $stmt->execute([time() + ITRSPI_LOGIN_DELAY, itrspi_login_ip_key(), $failedLease]);
        } catch (Throwable $e) {
            error_log('IT-RSPI login limiter failure: ' . $e->getMessage());
            $message = 'Pengamanan login sedang tidak tersedia. Silakan hubungi administrator.';
        }
    }
    $_SESSION['itrspi_login_error'] = $message;
    header('Location: form_login.php', true, 303);
    exit;
}

function itrspi_captcha_render($code)
{
    $image = imagecreatetruecolor(220, 70);
    $background = imagecolorallocate($image, 238, 248, 255);
    imagefill($image, 0, 0, $background);
    for ($i = 0; $i < 12; $i++) {
        $noise = imagecolorallocate($image, random_int(140, 200), random_int(180, 220), random_int(210, 245));
        imageline($image, random_int(0, 219), random_int(0, 69), random_int(0, 219), random_int(0, 69), $noise);
    }
    // Perbesar karakter GD lalu putar tiap karakter; tidak bergantung font eksternal.
    for ($i = 0; $i < 5; $i++) {
        $glyph = imagecreatetruecolor(28, 42);
        imagefill($glyph, 0, 0, $background);
        $small = imagecreatetruecolor(9, 15);
        imagefill($small, 0, 0, $background);
        $ink = imagecolorallocate($small, random_int(25, 50), random_int(65, 100), random_int(120, 165));
        imagestring($small, 5, 0, 0, $code[$i], $ink);
        imagecopyresized($glyph, $small, 0, 0, 0, 0, 28, 42, 9, 15);
        $rotated = imagerotate($glyph, random_int(-15, 15), $background);
        imagecopy($image, $rotated, 13 + $i * 40, random_int(8, 14), 0, 0, imagesx($rotated), imagesy($rotated));
        imagedestroy($small);
        imagedestroy($glyph);
        imagedestroy($rotated);
    }
    header('Content-Type: image/png');
    imagepng($image);
    imagedestroy($image);
}
