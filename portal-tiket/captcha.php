<?php
require_once __DIR__ . '/access_helpers.php';
try { pt_public_session(); } catch (Throwable $e) { pt_access_unavailable(); }
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET','HEAD'], true)) { http_response_code(405); header('Allow: GET, HEAD'); exit; }
$id = is_string($_GET['id'] ?? null) ? $_GET['id'] : '';
$challenge = $_SESSION['pt_access_captchas'][$id] ?? null;
if (!$challenge || $challenge['expires'] <= time() || !extension_loaded('gd')) { http_response_code(404); exit; }
$code = $challenge['code']; session_write_close();
if ($_SERVER['REQUEST_METHOD'] === 'HEAD') { header('Content-Type: image/png'); exit; }
itrspi_captcha_render($code);
