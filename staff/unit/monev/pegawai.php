<?php
require __DIR__.'/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
try {
    $result = mn_employee_search($_GET['q'] ?? '', $_GET['page'] ?? 1);
} catch (Throwable $e) {
    http_response_code($e instanceof DomainException ? 422 : 500);
    $result = ['message'=>$e instanceof DomainException ? $e->getMessage() : 'Pencarian pegawai gagal.'];
}
while (ob_get_level()) ob_end_clean();
echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
