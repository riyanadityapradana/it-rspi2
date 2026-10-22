<?php
ob_start();
header('Cache-Control: no-store, max-age=0'); header('Referrer-Policy: no-referrer'); header('X-Content-Type-Options: nosniff');
if (!in_array($_SERVER['REQUEST_METHOD'],['GET','HEAD'],true)) { header('Allow: GET, HEAD'); http_response_code(405); exit('Metode permintaan tidak didukung.'); }
try {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION['id_user'])) {
        $base=rtrim(str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME'],4)),'/');
        header('Location: '.$base.'/main_login/form_login.php'); exit;
    }
    require_once __DIR__.'/../../../config/koneksi.php';
    require_once __DIR__.'/access.php';
    if (!pti_internal_user($config,$_SESSION)) throw new DomainException('Akses cetak tiket ditolak.',403);
    session_write_close();
    require_once __DIR__.'/pdf_helpers.php';
    $report=pti_pdf_load(pt_connection(),$_GET['id'] ?? null);
    require_once __DIR__.'/pdf_renderer.php';
    $binary=pti_build_ticket_pdf($report);
    $filename='Portal-Tiket-'.preg_replace('/[^A-Za-z0-9_-]/','-', $report['ticket']['no_tiket']).'.pdf';
    while(ob_get_level())ob_end_clean();
    header('Content-Type: application/pdf'); header('Content-Disposition: inline; filename="'.$filename.'"');
    header('Content-Length: '.strlen($binary)); header('Pragma: no-cache');
    if ($_SERVER['REQUEST_METHOD'] !== 'HEAD') echo $binary;
} catch (DomainException $e) {
    while(ob_get_level())ob_end_clean();http_response_code(in_array($e->getCode(),[403,404,409],true)?$e->getCode():422);
    header('Content-Type: text/plain; charset=utf-8');echo $e->getMessage();
} catch (Throwable $e) {
    while(ob_get_level())ob_end_clean();http_response_code(503);header('Content-Type: text/plain; charset=utf-8');
    error_log('Portal ticket PDF unavailable.');echo 'PDF belum dapat dibuat. Silakan coba lagi beberapa saat.';
}
