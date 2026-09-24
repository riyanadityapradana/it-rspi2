<?php
require __DIR__.'/bootstrap.php';
try {
    $kind = ($_GET['kind'] ?? '') === 'perbaikan' ? 'perbaikan' : 'checklist';
    $row = $kind === 'perbaikan'
        ? mn_one('SELECT inspeksi_id,path_bukti_perbaikan path FROM inspeksi_temuan_perbaikan WHERE id=?',[(int)($_GET['id'] ?? 0)])
        : mn_one('SELECT inspeksi_id,path_foto_bukti path FROM inspeksi_detail_checklist WHERE id=?',[(int)($_GET['id'] ?? 0)]);
    mn_need($row,'Bukti tidak ditemukan.'); mn_header($row['inspeksi_id']);
    mn_need(preg_match('/^[a-f0-9]{48}\.(jpg|png|webp)$/D',(string)$row['path']),'Bukti tidak ditemukan.');
    $path=__DIR__.'/../../../assets/upload/monev/'.$row['path']; mn_need(is_file($path),'Bukti tidak ditemukan.');
    while (ob_get_level()) ob_end_clean();
    header('Content-Type: '.(new finfo(FILEINFO_MIME_TYPE))->file($path));
    header('X-Content-Type-Options: nosniff'); header('Cache-Control: private, no-store');
    header('Content-Disposition: inline; filename="bukti.'.pathinfo($path,PATHINFO_EXTENSION).'"');
    header('Content-Length: '.filesize($path)); readfile($path);
} catch (Throwable $e) { http_response_code(404); echo 'Bukti tidak ditemukan atau akses ditolak.'; }
