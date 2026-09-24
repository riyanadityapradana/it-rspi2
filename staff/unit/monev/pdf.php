<?php
require __DIR__.'/bootstrap.php';

require_once __DIR__.'/report.php';

try {
    $id = (int)($_GET['id'] ?? 0);
    mn_need($id > 0, 'Inspeksi tidak ditemukan atau akses ditolak.');
    $report = mn_pdf_load_report($id);
    session_write_close();
    require_once __DIR__.'/pdf_renderer.php';
    $binary = mn_build_inspection_pdf($report);
    while (ob_get_level()) ob_end_clean();
    $file = 'Laporan-Inspeksi-Keamanan-'.$id.'.pdf';
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="'.$file.'"');
    header('Content-Length: '.strlen($binary));
    header('Cache-Control: no-store, max-age=0');
    header('Pragma: no-cache');
    header('X-Content-Type-Options: nosniff');
    echo $binary;
} catch (DomainException $e) {
    while (ob_get_level()) ob_end_clean();
    http_response_code(404);
    header('Content-Type: text/plain; charset=UTF-8');
    header('Cache-Control: no-store');
    echo $e->getMessage();
} catch (Throwable $e) {
    while (ob_get_level()) ob_end_clean();
    error_log('Monev PDF: '.$e->getMessage());
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    header('Cache-Control: no-store');
    echo 'PDF tidak dapat dibuat. Periksa log aplikasi.';
}
