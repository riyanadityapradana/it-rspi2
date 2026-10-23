<?php
session_start();
require_once __DIR__ . '/login_security.php';
itrspi_login_no_cache();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: form_login.php');
    exit;
}

$loginUsername = itrspi_login_post_string('username');
// Pertahankan password apa adanya, termasuk spasi yang merupakan bagian password.
$loginPassword = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
$_SESSION['itrspi_login_old_username'] = substr($loginUsername, 0, 255);

$csrf = itrspi_login_post_string('csrf_token');
if (empty($_SESSION['itrspi_login_csrf']) || !hash_equals($_SESSION['itrspi_login_csrf'], $csrf)) {
    itrspi_login_reject('Sesi formulir tidak valid. Silakan coba lagi dengan formulir terbaru.');
}

try {
    $attempt = itrspi_login_reserve_attempt();
} catch (Throwable $e) {
    error_log('IT-RSPI login limiter: ' . $e->getMessage());
    itrspi_login_reject('Pengamanan login sedang tidak tersedia. Silakan hubungi administrator.');
}

if ($attempt['wait'] > 0) {
    header('Retry-After: ' . $attempt['wait']);
    itrspi_login_reject('Percobaan login dibatasi. Tunggu hingga hitung mundur selesai.');
}

if (!itrspi_login_check_captcha(itrspi_login_post_string('captcha_id'), itrspi_login_post_string('captcha'))) {
    itrspi_login_reject('Kode CAPTCHA salah atau kedaluwarsa. Silakan tunggu 30 detik sebelum mencoba lagi.', $attempt['lease']);
}

if ($loginUsername === '' || $loginPassword === '') {
    itrspi_login_reject('Username dan password wajib diisi. Silakan tunggu 30 detik sebelum mencoba lagi.', $attempt['lease']);
}
if (strlen($loginUsername) > 255 || strlen($loginPassword) > 4096) {
    itrspi_login_reject('Username atau password tidak valid. Silakan tunggu 30 detik sebelum mencoba lagi.', $attempt['lease']);
}

// Hubungkan ke database aplikasi hanya setelah pembatasan dan CAPTCHA lolos.
// koneksi.php mendefinisikan $username/$password milik database; input login memakai nama terpisah.
require_once __DIR__ . '/../config/koneksi.php';
$stmt = $config->prepare('SELECT * FROM tb_user WHERE username = ? LIMIT 1');
$stmt->bind_param('s', $loginUsername);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user || !password_verify($loginPassword, $user['password'])) {
    itrspi_login_reject('Username atau password tidak valid. Silakan tunggu 30 detik sebelum mencoba lagi.', $attempt['lease']);
}

$destinations = [
    'Kepala Ruangan' => '../admin/dashboard_admin.php?unit=beranda',
    'Staff' => '../staff/dashboard_staff.php?unit=beranda',
];
if (!isset($destinations[$user['role']])) {
    itrspi_login_reject('Role pengguna tidak dikenali. Silakan hubungi administrator dan tunggu 30 detik sebelum mencoba lagi.', $attempt['lease']);
}

try {
    itrspi_login_clear_attempt($attempt['lease']);
} catch (Throwable $e) {
    error_log('IT-RSPI login limiter reset: ' . $e->getMessage());
}

session_regenerate_id(true);
foreach (['id_user', 'nip', 'username', 'nama_lengkap', 'email', 'no_hp', 'role', 'foto', 'status'] as $field) {
    $_SESSION[$field] = $user[$field];
}
unset($_SESSION['itrspi_login_error'], $_SESSION['itrspi_login_old_username'],
    $_SESSION['itrspi_login_captchas'], $_SESSION['itrspi_login_csrf'], $_SESSION['id_calon'], $_SESSION['login_type']);

header('Location: ' . $destinations[$user['role']], true, 303);
exit;
