<?php
require_once __DIR__.'/criteria_rules.php';
function mn_bundles($id) {
    $pic=mn_assignee_sql('pic_area','b');
    return mn_all("SELECT b.*,($pic) pic_area_id,l.nama_lokasi,COALESCE(b.pic_area_nama,u.nama_lengkap,'-') nama_pic FROM inspeksi_bundle b JOIN tb_lokasi l ON l.lokasi_id=b.unit_area_id LEFT JOIN tb_user u ON u.id_user=b.pic_area_id WHERE b.inspeksi_id=? ORDER BY b.id",[$id]);
}
function mn_bundle($id,$bundleId) {
    foreach (mn_bundles($id) as $b) if ((int)$b['id']===(int)$bundleId) return $b;
    throw new DomainException('Bundle tidak ditemukan pada formulir ini.');
}
function mn_bundle_pic_access($id,$uid) {
    foreach (mn_bundles($id) as $b) if ((int)$b['pic_area_id']===$uid) return true;
    return false;
}
function mn_bundle_create($h,$unit,$pic,$criteria) {
    global $config;
    mn_need(count($criteria)===10,'Master harus memiliki tepat 10 kriteria aktif untuk membuat bundle.');
    mn_need(mn_one('SELECT lokasi_id FROM tb_lokasi WHERE lokasi_id=?',[$unit]),'Pilih unit/area yang valid.');
    $exam=mn_one('SELECT nik FROM monev_pegawai_akun WHERE user_id=?',[$h['pemeriksa_id']]);
    mn_need($pic['nik']!==($exam['nik'] ?? '') && $pic['nik']!==($h['verifikator_nik'] ?? ''),'PIC bundle harus berbeda dari Pemeriksa dan Verifikator.');
    mn_query('INSERT INTO inspeksi_bundle (inspeksi_id,unit_area_id,pic_area_nik,pic_area_nama) VALUES (?,?,?,?)',[$h['id'],$unit,$pic['nik'],$pic['nama']]);
    $bid=$config->insert_id;
    foreach ($criteria as $k) mn_query('INSERT INTO inspeksi_detail_checklist (inspeksi_id,bundle_id,kriteria_id,kategori_snapshot,deskripsi_snapshot) VALUES (?,?,?,?,?)',[$h['id'],$bid,$k['id'],$k['kategori'],$k['deskripsi']]);
    return $bid;
}
function mn_bundle_validate($h,$b) {
    mn_need($b['disimpan_pada'],'Simpan checklist bundle '.$b['nama_lokasi'].' terlebih dahulu.');
    mn_need(trim($b['ruang_diperiksa'].$b['komputer_diperiksa'].$b['dok_printer_diperiksa'])!=='','Isi cakupan bundle '.$b['nama_lokasi'].'.');
    $rows=mn_all('SELECT * FROM inspeksi_detail_checklist WHERE inspeksi_id=? AND bundle_id=?',[$h['id'],$b['id']]);
    mn_need(count($rows)>0,'Checklist bundle kosong.');
    foreach ($rows as $d) {
        mn_need($d['disimpan_pada'] && in_array($d['hasil'],['S','TS','TA','BV'],true),'Simpan seluruh hasil checklist bundle.');
        mn_need($d['lokasi_id_perangkat']!=='' || $d['hasil']==='TA','Isi lokasi/ID perangkat di bundle '.$b['nama_lokasi'].'.');
        if (in_array($d['hasil'],['TA','BV'],true) && !mn_server_not_applicable($b['nama_lokasi'],$d['kategori_snapshot'])) mn_need(trim((string)$b['alasan_ta_bv'])!=='','Isi alasan TA/BV bundle '.$b['nama_lokasi'].'.');
    }
    if ($b['pic_area_nik']) mn_employee($b['pic_area_nik']); else mn_account($b['pic_area_id']);
    mn_need(!$b['pic_area_id'] || !in_array((int)$b['pic_area_id'],[(int)$h['pemeriksa_id'],(int)$h['verifikator_id']],true),'PIC bundle harus berbeda dari Pemeriksa dan Verifikator.');
}
function mn_bundle_sign($h,$b,$uid,$key,&$created,$method='langsung') {
    mn_need($uid===(int)$h['pemeriksa_id'] || $uid===(int)$b['pic_area_id'],'Anda tidak berhak mengesahkan bundle ini.');
    mn_bundle_validate($h,$b);
    $path=mn_signature_upload($key,$created);
    mn_need($path,'Tanda tangan PIC bundle wajib diisi.');
    $old=mn_signed($h['id'],'PIC/Kepala Unit',$b['id']);
    mn_need(!$old || !$old['path_tanda_tangan'],'PIC bundle ini sudah mengesahkan.');
    if ($old) mn_query('UPDATE inspeksi_pengesahan SET dibatalkan_pada=NOW() WHERE id=?',[$old['id']]);
    mn_query('INSERT INTO inspeksi_pengesahan (inspeksi_id,bundle_id,peran,user_id,path_tanda_tangan,nama_penanda,nik_penanda,metode) VALUES (?,?,?,?,?,?,?,?)',[$h['id'],$b['id'],'PIC/Kepala Unit',$uid,$path,$b['nama_pic'],$b['pic_area_nik'],$method]);
}
function mn_bundle_detail_save($h,$d,$input,$photoKey,&$created,&$oldFiles) {
    mn_need(is_array($input),'Checklist tidak lengkap. Muat ulang halaman sebelum menyimpan.');
    $hasil=$input['hasil'] ?? ''; mn_need(in_array($hasil,['S','TS','TA','BV'],true),'Pilih hasil setiap kriteria dalam bundle.');
    $lokasi=mn_text($input['lokasi'] ?? '',255); $catatan=mn_text($input['catatan'] ?? '');
    $foto=mn_upload($photoKey,$created) ?: $d['path_foto_bukti'];
    if ($foto!==$d['path_foto_bukti'] && $d['path_foto_bukti']) $oldFiles[]=$d['path_foto_bukti'];
    $changed=$hasil!==$d['hasil'] || $lokasi!==$d['lokasi_id_perangkat'] || $catatan!==(string)$d['catatan_bukti'] || $foto!==$d['path_foto_bukti'];
    $t=mn_one('SELECT * FROM inspeksi_temuan_perbaikan WHERE detail_checklist_id=?',[$d['id']]);
    if ($t && $t['status_temuan']==='Selesai terverifikasi') mn_need(!$changed,'Butir dengan temuan terverifikasi tidak dapat diubah.');
    mn_query('UPDATE inspeksi_detail_checklist SET hasil=?,lokasi_id_perangkat=?,catatan_bukti=?,path_foto_bukti=?,disimpan_pada=NOW() WHERE id=?',[$hasil,$lokasi,$catatan,$foto,$d['id']]);
    if ($hasil==='TS' && !$t) mn_query('INSERT INTO inspeksi_temuan_perbaikan (inspeksi_id,detail_checklist_id,no_temuan,waktu_ditemukan,kondisi_risiko) VALUES (?,?,?,?,?)',[$h['id'],$d['id'],'MNV-'.$h['id'].'-'.$d['id'],$h['tgl_waktu_inspeksi'],$catatan]);
    elseif ($hasil!=='TS' && $t) {
        mn_need($t['status_temuan']==='Terbuka' && !$t['pic_perbaikan_id'] && !$t['tgl_pelaksanaan'] && !$t['path_bukti_perbaikan'] && !$t['tindakan_segera'] && !$t['pencegahan'] && !$t['target_selesai'] && !$t['no_laporan_insiden'],'Butir dengan tindak lanjut tidak dapat diubah dari TS.');
        mn_query('DELETE FROM inspeksi_temuan_perbaikan WHERE id=?',[$t['id']]);
    }
    return $changed;
}
function mn_save_bundles($h,$uid,&$created,&$oldFiles) {
    if (isset($_POST['bundle']) || !empty($_POST['add_bundle'])) mn_need(($_POST['bundle_payload_complete'] ?? '')==='1','Data formulir terpotong. Kurangi ukuran unggahan atau hubungi administrator sebelum mencoba lagi.');
    $bundles=mn_bundles($h['id']); $newId=null;
    if (!empty($_POST['add_bundle'])) {
        mn_need(is_array($_POST['new_bundle'] ?? null),'Bundle baru tidak lengkap.');
        $new=$_POST['new_bundle']; $pic=mn_employee($new['pic_area_nik'] ?? '');
        $criteria=mn_all('SELECT * FROM master_kriteria_inspeksi WHERE is_active=1 ORDER BY urutan,id');
        $newId=mn_bundle_create($h,(int)($new['unit_area_id'] ?? 0),$pic,$criteria);
        $bundles=mn_bundles($h['id']);
    }
    $coverageKeys=['ruang_diperiksa','komputer_diperiksa','dok_printer_diperiksa','objek_belum_diperiksa_alasan','alasan_ta_bv'];
    $anyChanged=$newId!==null;
    foreach ($bundles as $b) {
        $isNew=(int)$b['id']===(int)$newId;
        // The old single-bundle payload remains accepted for existing clients/tests.
        $input=$isNew ? $_POST['new_bundle'] : ($_POST['bundle'][$b['id']] ?? (count($bundles)===1 ? $_POST : null));
        mn_need(is_array($input),'Data seluruh bundle wajib dikirim. Muat ulang halaman.');
        $unit=(int)($input['unit_area_id'] ?? $b['unit_area_id']);
        $nik=mn_text($input['pic_area_nik'] ?? $b['pic_area_nik'],30);
        $unitRow=mn_one('SELECT lokasi_id,nama_lokasi FROM tb_lokasi WHERE lokasi_id=?',[$unit]);
        mn_need($unitRow,'Unit bundle tidak valid.');
        $changed=$unit!==(int)$b['unit_area_id'] || $nik!==(string)$b['pic_area_nik'];
        $name=$b['pic_area_nama']; $legacy=$b['pic_area_id'];
        if ($nik!==(string)$b['pic_area_nik']) { $pic=mn_employee($nik); $name=$pic['nama']; $legacy=null; }
        $exam=mn_one('SELECT nik FROM monev_pegawai_akun WHERE user_id=?',[$uid]);
        mn_need(!$nik || ($nik!==($exam['nik'] ?? '') && $nik!==($h['verifikator_nik'] ?? '')),'PIC bundle harus berbeda dari Pemeriksa dan Verifikator.');
        $values=[];
        foreach ($coverageKeys as $key) { $value=mn_text($input[$key] ?? ''); $changed=$changed || $value!==(string)$b[$key]; $values[]=$value; }
        foreach (mn_all('SELECT * FROM inspeksi_detail_checklist WHERE inspeksi_id=? AND bundle_id=?',[$h['id'],$b['id']]) as $d) {
            $entry=$isNew ? ($input['detail'][$d['kriteria_id']] ?? null) : ($_POST['detail'][$d['id']] ?? null);
            if (mn_server_not_applicable($unitRow['nama_lokasi'],$d['kategori_snapshot'])) {
                $entry=['hasil'=>'TA','lokasi'=>$unitRow['nama_lokasi'],'catatan'=>'Ruang server tidak tersedia'];
                // Hidden criteria cannot accept a forged new evidence upload.
                unset($_FILES[$isNew ? 'foto_new_'.$d['kriteria_id'] : 'foto_'.$d['id']]);
            }
            $detailChanged=mn_bundle_detail_save($h,$d,$entry,$isNew ? 'foto_new_'.$d['kriteria_id'] : 'foto_'.$d['id'],$created,$oldFiles);
            $changed=$changed || $detailChanged;
        }
        mn_query('UPDATE inspeksi_bundle SET unit_area_id=?,pic_area_id=?,pic_area_nik=?,pic_area_nama=?,ruang_diperiksa=?,komputer_diperiksa=?,dok_printer_diperiksa=?,objek_belum_diperiksa_alasan=?,alasan_ta_bv=?,disimpan_pada=NOW() WHERE id=?',array_merge([$unit,$legacy,$nik ?: null,$name],$values,[$b['id']]));
        if ($changed) mn_query('UPDATE inspeksi_pengesahan SET dibatalkan_pada=NOW() WHERE inspeksi_id=? AND bundle_id=? AND dibatalkan_pada IS NULL',[$h['id'],$b['id']]);
        $anyChanged=$anyChanged || $changed;
        $signatureKey=$isNew ? 'signature_pic_new' : 'signature_pic_'.$b['id'];
        if (!empty($_POST[$signatureKey])) mn_bundle_sign($h,mn_bundle($h['id'],$b['id']),$uid,$signatureKey,$created);
    }
    if ($anyChanged) mn_query("UPDATE inspeksi_pengesahan SET dibatalkan_pada=NOW() WHERE inspeksi_id=? AND bundle_id IS NULL AND dibatalkan_pada IS NULL",[$h['id']]);
    // Retain legacy coverage for integrations; bundle coverage is authoritative.
    $first=mn_bundles($h['id'])[0]; $values=[]; foreach ($coverageKeys as $key) $values[]=$first[$key]; $values[]=$h['id'];
    mn_query('UPDATE inspeksi_cakupan SET ruang_diperiksa=?,komputer_diperiksa=?,dok_printer_diperiksa=?,objek_belum_diperiksa_alasan=?,alasan_ta_bv=? WHERE inspeksi_id=?',$values);
}
