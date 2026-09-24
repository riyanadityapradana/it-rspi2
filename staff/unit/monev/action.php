<?php
require __DIR__.'/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
$created = []; $oldFiles = []; $inTransaction = false;
try {
    mn_need($_SERVER['REQUEST_METHOD'] === 'POST', 'Gunakan metode POST.');
    mn_need(is_string($_POST['csrf'] ?? null) && hash_equals($_SESSION['monev_csrf'], $_POST['csrf']), 'Sesi formulir kedaluwarsa. Muat ulang halaman.');
    $action = $_POST['action'] ?? ''; $id = (int)($_POST['id'] ?? 0); $uid = (int)$mnUser['id_user'];
    $config->begin_transaction(); $inTransaction = true;
    if ($action === 'employee_map') {
        mn_need($mnUser['role'] === 'Kepala Ruangan', 'Hanya Kepala Ruangan dapat memetakan akun pegawai.');
        $employee = mn_employee($_POST['pegawai_nik'] ?? '');
        $account = mn_account($_POST['user_id'] ?? 0);
        mn_need(!mn_one('SELECT nik FROM monev_pegawai_akun WHERE user_id=? AND nik<>?',[$account,$employee['nik']]), 'Akun sudah dipetakan ke pegawai lain.');
        mn_query('INSERT INTO monev_pegawai_akun (nik,user_id,nama_pegawai,dipetakan_oleh) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE user_id=VALUES(user_id),nama_pegawai=VALUES(nama_pegawai),dipetakan_oleh=VALUES(dipetakan_oleh),dipetakan_pada=CURRENT_TIMESTAMP',[$employee['nik'],$account,$employee['nama'],$uid]);
        $redirect = $mnUrl.'&view=master';
    } elseif ($action === 'master') {
        mn_need($mnUser['role'] === 'Kepala Ruangan', 'Hanya Kepala Ruangan dapat mengelola kriteria.');
        $kategori = mn_text($_POST['kategori'] ?? '',100); $deskripsi = mn_text($_POST['deskripsi'] ?? '');
        mn_need($kategori !== '' && $deskripsi !== '', 'Kategori dan deskripsi wajib diisi.');
        $params = [$kategori,$deskripsi,isset($_POST['is_active']) ? 1 : 0,(int)($_POST['urutan'] ?? 0)];
        if ($id) { $params[]=$id; mn_query('UPDATE master_kriteria_inspeksi SET kategori=?,deskripsi=?,is_active=?,urutan=? WHERE id=?',$params); }
        else mn_query('INSERT INTO master_kriteria_inspeksi (kategori,deskripsi,is_active,urutan) VALUES (?,?,?,?)',$params);
        $redirect = $mnUrl.'&view=master';
    } elseif ($action === 'create') {
        mn_need($mnUser['role'] === 'Staff', 'Pembuatan inspeksi dilakukan oleh Staff pemeriksa.');
        $pic = mn_employee($_POST['pic_area_nik'] ?? ''); $ver = mn_employee($_POST['verifikator_nik'] ?? '');
        $examiner = mn_one('SELECT nik FROM monev_pegawai_akun WHERE user_id=?',[$uid]);
        mn_need($pic['nik'] !== $ver['nik'] && $pic['nik'] !== ($examiner['nik'] ?? '') && $ver['nik'] !== ($examiner['nik'] ?? ''), 'Pemeriksa, PIC area, dan verifikator harus tiga pegawai berbeda.');
        $unit = (int)($_POST['unit_area_id'] ?? 0);
        mn_need(mn_one('SELECT lokasi_id FROM tb_lokasi WHERE lokasi_id=?',[$unit]), 'Unit/area tidak valid.');
        $jenis = $_POST['jenis_inspeksi'] ?? '';
        mn_need(in_array($jenis,['Rutin','Sewaktu-Waktu','Verifikasi Perbaikan','Setelah Insiden'],true), 'Jenis inspeksi tidak valid.');
        $periode = mn_text($_POST['periode'] ?? '',50); mn_need($periode !== '', 'Periode wajib diisi.');
        $waktu = mn_date($_POST['tgl_waktu_inspeksi'] ?? '',true,true);
        $kriteria = mn_all('SELECT * FROM master_kriteria_inspeksi WHERE is_active=1 ORDER BY urutan,id');
        mn_need(count($kriteria)>0, 'Aktifkan minimal satu kriteria terlebih dahulu.');
        $formNumber = mn_form_number(null, true);
        mn_query('INSERT INTO inspeksi_header (no_formulir,revisi,tgl_berlaku,periode,tgl_waktu_inspeksi,unit_area_id,pic_area_nik,pic_area_nama,pemeriksa_id,verifikator_nik,verifikator_nama,jenis_inspeksi) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)',[
            $formNumber,mn_text($_POST['revisi'] ?? '',30),mn_text($_POST['tgl_berlaku'] ?? '',30),$periode,$waktu,$unit,$pic['nik'],$pic['nama'],$uid,$ver['nik'],$ver['nama'],$jenis]);
        $id = $config->insert_id;
        mn_bundle_create(['id'=>$id,'pemeriksa_id'=>$uid,'verifikator_nik'=>$ver['nik']],$unit,$pic,$kriteria);
        mn_query('INSERT INTO inspeksi_cakupan (inspeksi_id) VALUES (?)',[$id]);
        $redirect = $mnUrl.'&id='.$id;
    } else {
        $h = mn_header($id,true);
        mn_need((int)($_POST['version'] ?? 0) === (int)$h['version'], 'Data telah berubah di sesi lain. Muat ulang sebelum menyimpan.');
        if ($action === 'save') {
            mn_need(mn_editor($h), 'Checklist hanya dapat diubah pemeriksa saat Draft.');
            mn_save_bundles($h,$uid,$created,$oldFiles);
        } elseif ($action === 'submit' || ($action==='onsite' && $h['status_inspeksi']==='Draft')) {
            mn_need(mn_editor($h),'Hanya pemeriksa dapat mengajukan Draft.');
            foreach (mn_bundles($id) as $bundle) mn_bundle_validate($h,$bundle);
            foreach (mn_all('SELECT * FROM inspeksi_temuan_perbaikan WHERE inspeksi_id=?',[$id]) as $t) {
                mn_need(trim($t['kondisi_risiko']) !== '' && $t['pic_perbaikan_id'] && $t['target_selesai'], 'Lengkapi kondisi/risiko, PIC perbaikan, dan target setiap temuan.');
                mn_account($t['pic_perbaikan_id']);
            }
            mn_validate_assignees($h);
            $signature = mn_signature_upload('signature_data', $created);
            if ($action==='submit') mn_sign($id,'Pemeriksa',$uid,$signature);
            mn_query("UPDATE inspeksi_header SET status_inspeksi='Menunggu Pengesahan',catatan_validasi=NULL WHERE id=?",[$id]);
        } elseif ($action === 'finding') {
            mn_need($h['status_inspeksi'] !== 'Selesai','Inspeksi selesai tidak dapat diubah.');
            $t = mn_one('SELECT * FROM inspeksi_temuan_perbaikan WHERE id=? AND inspeksi_id=?',[(int)($_POST['temuan_id'] ?? 0),$id]);
            mn_need($t && $t['status_temuan'] !== 'Selesai terverifikasi','Temuan tidak tersedia untuk perubahan.');
            mn_need($uid === (int)$h['pemeriksa_id'] || $uid === (int)$t['pic_perbaikan_id'],'Hanya pemeriksa atau PIC perbaikan dapat memperbarui temuan.');
            $pic = mn_account($_POST['pic_perbaikan_id'] ?? 0);
            mn_need($uid === (int)$h['pemeriksa_id'] || $pic === (int)$t['pic_perbaikan_id'], 'PIC perbaikan hanya dapat ditugaskan oleh pemeriksa.');
            mn_need($pic !== (int)$h['verifikator_id'], 'PIC perbaikan harus berbeda dari verifikator.');
            $status = $_POST['status_temuan'] ?? '';
            mn_need(in_array($status,['Terbuka','Dalam proses'],true),'Status temuan tidak valid.');
            $target = mn_date($_POST['target_selesai'] ?? '',false,true); $pelaksanaan = mn_date($_POST['tgl_pelaksanaan'] ?? '');
            mn_need($target >= substr($t['waktu_ditemukan'],0,10), 'Target tidak boleh sebelum temuan ditemukan.');
            mn_need(!$pelaksanaan || $pelaksanaan >= substr($t['waktu_ditemukan'],0,10), 'Tanggal pelaksanaan tidak boleh sebelum tanggal temuan ditemukan.');
            $risiko = mn_text($_POST['kondisi_risiko'] ?? ''); mn_need($risiko !== '', 'Kondisi/risiko wajib diisi.');
            $bukti = mn_upload('bukti_perbaikan',$created) ?: $t['path_bukti_perbaikan'];
            if ($bukti !== $t['path_bukti_perbaikan'] && $t['path_bukti_perbaikan']) $oldFiles[]=$t['path_bukti_perbaikan'];
            mn_query('UPDATE inspeksi_temuan_perbaikan SET kondisi_risiko=?,tindakan_segera=?,pencegahan=?,pic_perbaikan_id=?,target_selesai=?,tgl_pelaksanaan=?,path_bukti_perbaikan=?,status_temuan=?,no_laporan_insiden=? WHERE id=?',[
                $risiko,mn_text($_POST['tindakan_segera'] ?? ''),mn_text($_POST['pencegahan'] ?? ''),$pic,$target,$pelaksanaan,$bukti,$status,mn_text($_POST['no_laporan_insiden'] ?? '',100),$t['id']]);
            // A changed corrective action needs a new verifier approval; retain its audit history.
            mn_query("UPDATE inspeksi_pengesahan SET dibatalkan_pada=NOW() WHERE inspeksi_id=? AND peran='Verifikator' AND dibatalkan_pada IS NULL",[$id]);
        } elseif ($action === 'verify') {
            mn_validate_assignees($h);
            mn_need($h['status_inspeksi'] === 'Menunggu Pengesahan' && $uid === (int)$h['verifikator_id'], 'Hanya verifikator yang ditugaskan dapat memverifikasi laporan yang diajukan.');
            $t = mn_one('SELECT * FROM inspeksi_temuan_perbaikan WHERE id=? AND inspeksi_id=?',[(int)($_POST['temuan_id'] ?? 0),$id]);
            mn_need($t && $t['status_temuan'] !== 'Selesai terverifikasi' && $t['tgl_pelaksanaan'] && $t['path_bukti_perbaikan'], 'Isi pelaksanaan dan bukti perbaikan sebelum verifikasi.');
            $ulang = mn_text($_POST['hasil_pemeriksaan_ulang'] ?? ''); mn_need($ulang !== '', 'Hasil pemeriksaan ulang wajib diisi.');
            mn_query("UPDATE inspeksi_temuan_perbaikan SET verifikator_id=?,tgl_verifikasi=CURDATE(),hasil_pemeriksaan_ulang=?,status_temuan='Selesai terverifikasi' WHERE id=?",[$uid,$ulang,$t['id']]);
            mn_finish($id);
        } elseif ($action === 'approve') {
            mn_validate_assignees($h);
            mn_need($h['status_inspeksi'] === 'Menunggu Pengesahan','Laporan belum menunggu pengesahan.');
            if (isset($_POST['bundle_id'])) {
                $bundle=mn_bundle($id,(int)$_POST['bundle_id']);
                mn_need($uid===(int)$bundle['pic_area_id'],'Anda bukan PIC bundle ini.');
                mn_bundle_sign($h,$bundle,$uid,'signature_data',$created,'akun');
            } else {
                mn_need($uid===(int)$h['verifikator_id'],'Anda bukan verifikator dokumen.');
                mn_need(!mn_one("SELECT id FROM inspeksi_temuan_perbaikan WHERE inspeksi_id=? AND status_temuan<>'Selesai terverifikasi' LIMIT 1",[$id]),'Verifikasi seluruh temuan sebelum mengesahkan laporan.');
                $signature=mn_signature_upload('signature_data',$created);
                mn_need($signature,'Tanda tangan Verifikator wajib diisi.');
                mn_sign($id,'Verifikator',$uid,$signature);
            }
            mn_finish($id);
        } elseif ($action === 'sign_bundle') {
            mn_need($h['status_inspeksi']!=='Selesai','Dokumen sudah selesai.');
            mn_bundle_sign($h,mn_bundle($id,(int)($_POST['bundle_id'] ?? 0)),$uid,'signature_data',$created);
            mn_finish($id);
        } elseif ($action === 'onsite') {
            mn_need($h['status_inspeksi']==='Menunggu Pengesahan','Laporan tidak dapat ditandatangani pada status ini.');
        } elseif ($action === 'return') {
            mn_need($h['status_inspeksi'] === 'Menunggu Pengesahan' && (mn_bundle_pic_access($id,$uid) || $uid===(int)$h['verifikator_id']),'Anda tidak berhak mengembalikan laporan ini.');
            $note=mn_text($_POST['catatan_validasi'] ?? ''); mn_need($note !== '', 'Alasan pengembalian wajib diisi.');
            mn_query("UPDATE inspeksi_header SET status_inspeksi='Draft',catatan_validasi=? WHERE id=?",[$note,$id]);
            mn_query('UPDATE inspeksi_pengesahan SET dibatalkan_pada=NOW() WHERE inspeksi_id=? AND dibatalkan_pada IS NULL',[$id]);
        } else throw new DomainException('Aksi tidak dikenali.');
        if ($action==='onsite') mn_sign_onsite($h,$uid,$created);
        mn_query('UPDATE inspeksi_header SET version=version+1,updated_at=NOW() WHERE id=?',[$id]);
        $redirect=$mnUrl.'&id='.$id;
    }
    $config->commit(); $inTransaction=false;
    foreach ($oldFiles as $file) if (preg_match('/^[a-f0-9]{48}\.(jpg|png|webp)$/D',$file)) @unlink(__DIR__.'/../../../assets/upload/monev/'.$file);
    echo json_encode(['ok'=>true,'redirect'=>$redirect]);
} catch (Throwable $e) {
    if ($inTransaction) $config->rollback();
    foreach ($created as $file) @unlink($file);
    http_response_code($e instanceof DomainException ? 422 : 500);
    if (!($e instanceof DomainException)) error_log('Monev: '.$e->getMessage());
    echo json_encode(['ok'=>false,'message'=>$e instanceof DomainException ? $e->getMessage() : 'Penyimpanan gagal. Periksa koneksi atau hubungi administrator.']);
}
