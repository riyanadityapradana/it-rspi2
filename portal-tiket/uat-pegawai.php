<?php
require_once __DIR__ . '/access_helpers.php';
pt_public_require(true);
require_once __DIR__.'/uat_helpers.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store'); header('Referrer-Policy: no-referrer'); header('X-Content-Type-Options: nosniff');
if ($_SERVER['REQUEST_METHOD'] !== 'GET') { header('Allow: GET'); http_response_code(405); echo json_encode(['message'=>'Metode permintaan tidak didukung.']); exit; }
try {
    pt_uat_search_access(pt_connection(), $_GET['token'] ?? null);
    pt_session(); header('Referrer-Policy: no-referrer');
    $csrf = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!is_string($csrf) || !hash_equals($_SESSION['pt_csrf'], $csrf)) throw new DomainException('Sesi pencarian tidak valid. Muat ulang halaman UAT.', 403);
    $now=time(); $rate=$_SESSION['pt_search_rate'] ?? ['start'=>$now,'count'=>0];
    if ($now-$rate['start']>=60) $rate=['start'=>$now,'count'=>0];
    $_SESSION['pt_search_rate']=['start'=>$rate['start'],'count'=>$rate['count']+1];
    if ($rate['count']>=60) { header('Retry-After: 60'); throw new DomainException('Pencarian terlalu sering. Coba lagi sebentar.',429); }
    echo json_encode(pt_employee_search($_GET['q'] ?? '', is_scalar($_GET['page'] ?? 1) ? (int)($_GET['page'] ?? 1) : 1), JSON_UNESCAPED_UNICODE);
} catch (DomainException $e) {
    http_response_code(in_array($e->getCode(),[403,404,410,429],true)?$e->getCode():503); echo json_encode(['message'=>$e->getMessage()]);
} catch (Throwable $e) {
    http_response_code(503); echo json_encode(['message'=>'Pencarian pegawai belum dapat diakses. Silakan coba lagi.']);
}
