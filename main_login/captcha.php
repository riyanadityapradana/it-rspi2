<?php
session_start();
require_once __DIR__ . '/login_security.php';
itrspi_login_no_cache();
header('X-Content-Type-Options: nosniff');

$id = isset($_GET['id']) && is_string($_GET['id']) ? $_GET['id'] : '';
$challenge = isset($_SESSION['itrspi_login_captchas'][$id]) ? $_SESSION['itrspi_login_captchas'][$id] : null;
if (!$challenge || $challenge['expires'] <= time() || !extension_loaded('gd')) {
    http_response_code(404);
    exit;
}
$code = $challenge['code'];
session_write_close();

itrspi_captcha_render($code);
