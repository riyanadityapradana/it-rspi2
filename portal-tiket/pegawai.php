<?php
require_once __DIR__ . '/access_helpers.php';
pt_public_require(true);
require_once __DIR__ . '/request_helpers.php';
pt_session();
header('Content-Type: application/json; charset=utf-8');
try {
    $csrf = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!hash_equals($_SESSION['pt_csrf'], $csrf)) { http_response_code(403); echo json_encode(['message' => 'Sesi pencarian tidak valid. Buka formulir terlebih dahulu.']); exit; }
    $now = time();
    $rate = $_SESSION['pt_search_rate'] ?? ['start' => $now, 'count' => 0];
    if ($now - $rate['start'] >= 60) $rate = ['start' => $now, 'count' => 0];
    if (++$rate['count'] > 60) { http_response_code(429); header('Retry-After: 60'); echo json_encode(['message' => 'Pencarian terlalu sering. Coba lagi sebentar.']); exit; }
    $_SESSION['pt_search_rate'] = $rate;
    echo json_encode(pt_employee_search($_GET['q'] ?? '', is_scalar($_GET['page'] ?? 1) ? (int) ($_GET['page'] ?? 1) : 1), JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('Portal employee search: ' . $e->getMessage());
    http_response_code(503);
    echo json_encode(['message' => 'Pencarian pegawai belum dapat diakses. Silakan coba lagi.']);
}
