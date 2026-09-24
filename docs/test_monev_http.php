<?php
// CLI-only local Apache check. Creates and removes one inspection, test files, and test sessions.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
ob_start();
require __DIR__.'/../config/koneksi.php';
require __DIR__.'/../staff/unit/monev/helpers.php';
$base='http://localhost/it-rspi2'; $sessions=[]; $inspectionId=0; $paths=[]; $checks=0;
function http_check($condition,$label) { global $checks; if (!$condition) throw new RuntimeException('FAILED: '.$label); $checks++; }
function test_session($user) {
    global $sessions;
    $sid='monevhttp'.bin2hex(random_bytes(12)); session_id($sid);
    if (!session_start()) throw new RuntimeException('Tidak dapat membuat sesi uji pada session.save_path PHP.');
    $_SESSION=['id_user'=>$user['id_user'],'role'=>$user['role'],'nip'=>'monev-test','foto'=>'','monev_csrf'=>bin2hex(random_bytes(24))];
    $token=$_SESSION['monev_csrf']; session_write_close(); $sessions[]=$sid;
    return ['cookie'=>session_name().'='.$sid,'csrf'=>$token];
}
function request_monev($path,$auth=null,$post=null) {
    global $base;
    $ch=curl_init($base.$path);
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_HEADER=>true,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_TIMEOUT=>20]);
    if ($auth) curl_setopt($ch,CURLOPT_COOKIE,$auth['cookie']);
    if ($post!==null) curl_setopt_array($ch,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>array_merge(['csrf'=>$auth['csrf'] ?? ''],$post)]);
    $response=curl_exec($ch); if ($response===false) throw new RuntimeException(curl_error($ch));
    $size=curl_getinfo($ch,CURLINFO_HEADER_SIZE); $status=curl_getinfo($ch,CURLINFO_HTTP_CODE); curl_close($ch);
    return ['status'=>$status,'headers'=>substr($response,0,$size),'body'=>substr($response,$size)];
}
try {
    $staff=mn_all("SELECT id_user,role FROM tb_user WHERE status='aktif' AND role='Staff' ORDER BY id_user LIMIT 3");
    $head=mn_one("SELECT id_user,role FROM tb_user WHERE status='aktif' AND role='Kepala Ruangan' ORDER BY id_user LIMIT 1");
    http_check(count($staff)===3 && $head,'active accounts available');
    $exam=test_session($staff[0]); $outsider=test_session($staff[2]); $admin=test_session($head);
    $r=request_monev('/staff/unit/monev/action.php'); http_check($r['status']===302,'unauthenticated redirect');
    $r=request_monev('/assets/upload/monev/'); http_check($r['status']===403,'Apache upload directory protected');
    $r=request_monev('/staff/dashboard_staff.php?unit=monev',$exam); http_check($r['status']===200 && strpos($r['body'],'Temuan TS per unit')!==false,'staff dashboard render');
    $r=request_monev('/admin/dashboard_admin.php?unit=monev&view=master',$admin); http_check($r['status']===200 && strpos($r['body'],'Tambah kriteria')!==false,'admin master render');
    $unitAreaId=mn_one('SELECT lokasi_id FROM tb_lokasi ORDER BY lokasi_id LIMIT 1')['lokasi_id'];
    $r=request_monev('/staff/unit/monev/pegawai.php?q=abc'); http_check($r['status']===302,'employee search requires login');
    $r=request_monev('/staff/unit/monev/pegawai.php?q=ab',$exam);
    http_check($r['status']===200 && json_decode($r['body'],true)['results']===[], 'employee search minimum length');
    $employees=mn_employee_rows("SELECT nik,nama FROM pegawai WHERE stts_aktif='AKTIF' AND nik<>? ORDER BY nik LIMIT 3",[mn_one('SELECT nip FROM tb_user WHERE id_user=?',[$staff[0]['id_user']])['nip']]);
    http_check(count($employees)>=2,'remote active employees available');
    $r=request_monev('/staff/unit/monev/pegawai.php?q='.rawurlencode($employees[0]['nik']),$exam);
    $result=json_decode($r['body'],true);
    http_check($r['status']===200 && in_array($employees[0]['nik'].' - '.$employees[0]['nama'],array_column($result['results'],'text'),true),'remote employee search by NIK');
    $r=request_monev('/staff/dashboard_staff.php?unit=monev&view=new',$exam);
    http_check($r['status']===200 && substr_count($r['body'],'data-employee-search=')===2 && strpos($r['body'],'name="pic_area_nik"')!==false,'both AJAX selectors rendered');
    http_check((bool)preg_match('/name="no_formulir" value="INSP-[0-9]{8}-[0-9]{3}" readonly/',$r['body']),'automatic number preview is read-only');
    $post=['action'=>'create','unit_area_id'=>$unitAreaId,'pic_area_nik'=>$employees[0]['nik'],'verifikator_nik'=>$employees[1]['nik'],'periode'=>'TEST HTTP sementara','tgl_waktu_inspeksi'=>date('Y-m-d').'T09:00','jenis_inspeksi'=>'Rutin'];
    $r=request_monev('/staff/unit/monev/action.php',$exam,array_merge($post,['csrf'=>'invalid'])); http_check($r['status']===422,'HTTP CSRF enforcement');
    $r=request_monev('/staff/unit/monev/action.php',$exam,$post); $data=json_decode($r['body'],true);
    http_check($r['status']===200 && !empty($data['ok']),'HTTP create');
    parse_str(parse_url($data['redirect'],PHP_URL_QUERY),$query); $inspectionId=(int)$query['id'];
    $h=mn_one('SELECT * FROM inspeksi_header WHERE id=?',[$inspectionId]);
    http_check((bool)preg_match('/^INSP-'.date('Ymd').'-[0-9]{3}$/D',$h['no_formulir']),'HTTP create persists generated daily number');
    http_check($h['pic_area_nik']===$employees[0]['nik'] && $h['verifikator_nama']===$employees[1]['nama'],'employee assignment and name snapshot persisted');
    $r=request_monev('/staff/dashboard_staff.php?unit=monev&id='.$inspectionId,$exam);
    http_check($r['status']===200 && strpos($r['body'],mn_e($employees[0]['nik'].' - '.$employees[0]['nama']))!==false,'employee assignment displayed in detail');
    $r=request_monev('/staff/unit/monev/pdf.php?id='.$inspectionId,$exam);
    http_check($r['status']===200 && substr($r['body'],0,4)==='%PDF','employee assignment PDF export');
    // Continue the legacy-account approval regression on this disposable inspection only.
    mn_query('UPDATE inspeksi_header SET pic_area_nik=NULL,verifikator_nik=NULL,pic_area_id=?,verifikator_id=? WHERE id=?',[$head['id_user'],$staff[1]['id_user'],$inspectionId]);
    $bundleId=mn_bundles($inspectionId)[0]['id'];
    mn_query('UPDATE inspeksi_bundle SET pic_area_nik=NULL,pic_area_nama=NULL,pic_area_id=? WHERE id=?',[$head['id_user'],$bundleId]);
    $rows=mn_all('SELECT * FROM inspeksi_detail_checklist WHERE inspeksi_id=? ORDER BY id',[$inspectionId]);
    $first=$rows[0]['id'];
    $post=['action'=>'save','id'=>$inspectionId,'version'=>1,'ruang_diperiksa'=>'Unit test HTTP'];
    foreach ($rows as $d) { $post['detail['.$d['id'].'][hasil]']='S'; $post['detail['.$d['id'].'][lokasi]']='PC-TEST'; $post['detail['.$d['id'].'][catatan]']='Uji sementara'; }
    $temp=tempnam(sys_get_temp_dir(),'mnphoto'); $paths[]=$temp; file_put_contents($temp,'not an image');
    $post['foto_'.$first]=new CURLFile($temp,'image/jpeg','bukti.jpg');
    $r=request_monev('/staff/unit/monev/action.php',$exam,$post); http_check($r['status']===422,'forged image rejected');
    http_check((int)mn_one('SELECT version FROM inspeksi_header WHERE id=?',[$inspectionId])['version']===1,'failed upload rolls back');
    file_put_contents($temp,base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Y9ZlGkAAAAASUVORK5CYII='));
    $post['foto_'.$first]=new CURLFile($temp,'image/png','bukti.png');
    $r=request_monev('/staff/unit/monev/action.php',$exam,$post); $data=json_decode($r['body'],true);
    http_check($r['status']===200 && !empty($data['ok']),'HTTP photo upload');
    $file=mn_one('SELECT path_foto_bukti FROM inspeksi_detail_checklist WHERE id=?',[$first])['path_foto_bukti'];
    $r=request_monev('/staff/unit/monev/file.php?id='.$first,$exam); http_check($r['status']===200 && strpos($r['headers'],'image/png')!==false && strlen($r['body'])===filesize($temp),'authorized photo download');
    $r=request_monev('/staff/unit/monev/file.php?id='.$first,$outsider); http_check($r['status']===404,'unassigned staff cannot read photo');
    $r=request_monev('/assets/upload/monev/'.$file,$exam); http_check($r['status']===403,'direct file protected even with session');
    $r=request_monev('/staff/dashboard_staff.php?unit=monev&status=Draft&area='.$unitAreaId,$exam); http_check($r['status']===200 && strpos($r['body'],'Cetak PDF')!==false && strpos($r['body'],'<th>Aksi</th>')!==false,'history list PDF action render');
    $r=request_monev('/staff/unit/monev/pdf.php?id='.$inspectionId,$exam); http_check($r['status']===200 && stripos($r['headers'],'Content-Type: application/pdf')!==false && substr($r['body'],0,4)==='%PDF' && strlen($r['body'])>5000,'direct PDF export');
    $r=request_monev('/staff/unit/monev/print.php?id='.$inspectionId,$exam); http_check($r['status']===200 && stripos($r['headers'],'Content-Type: application/pdf')!==false && substr($r['body'],0,4)==='%PDF','legacy print URL exports PDF');
    $r=request_monev('/staff/unit/monev/pdf.php?id='.$inspectionId,$outsider); http_check($r['status']===404,'unassigned staff cannot export PDF');
    $r=request_monev('/staff/dashboard_staff.php?unit=monev&id='.$inspectionId,$exam); http_check($r['status']===200 && strpos($r['body'],'mn-checklist-form')!==false && strpos($r['body'],'Cetak PDF')!==false && strpos($r['body'],'Tanda Tangan Langsung')!==false,'editable detail PDF and direct signature action render');
    http_check(strpos($r['body'],'Tambah Checklist')!==false && strpos($r['body'],'name="new_bundle[unit_area_id]"')!==false && substr_count($r['body'],'name="new_bundle[detail][')===60 && strpos($r['body'],'name="kriteria_id"')===false,'new bundle includes ten complete criteria and no single-criterion dropdown');
    http_check(strpos($r['body'],'name="signature_pic_'.$bundleId.'"')!==false && strpos($r['body'],'name="signature_pic_new"')!==false,'PIC canvases rendered per bundle');
    $version=mn_one('SELECT version FROM inspeksi_header WHERE id=?',[$inspectionId])['version'];
    $post=['action'=>'save','id'=>$inspectionId,'version'=>$version,'add_bundle'=>1,'bundle_payload_complete'=>'1','bundle['.$bundleId.'][ruang_diperiksa]'=>'Unit test HTTP','new_bundle[unit_area_id]'=>$unitAreaId,'new_bundle[pic_area_nik]'=>$employees[0]['nik'],'new_bundle[ruang_diperiksa]'=>'Cakupan unit kedua'];
    foreach ($rows as $d) {
        $post['detail['.$d['id'].'][hasil]']='S'; $post['detail['.$d['id'].'][lokasi]']='PC-TEST'; $post['detail['.$d['id'].'][catatan]']='Uji sementara';
        $post['new_bundle[detail]['.$d['kriteria_id'].'][hasil]']='S'; $post['new_bundle[detail]['.$d['kriteria_id'].'][lokasi]']='PC-SECOND'; $post['new_bundle[detail]['.$d['kriteria_id'].'][catatan]']='Perangkat tambahan';
    }
    $r=request_monev('/staff/unit/monev/action.php',$exam,$post);
    http_check($r['status']===200 && !empty(json_decode($r['body'],true)['ok']),'HTTP save all and add complete bundle');
    http_check((int)mn_one('SELECT COUNT(*) n FROM inspeksi_detail_checklist WHERE inspeksi_id=?',[$inspectionId])['n']===20,'HTTP two bundles share document header');
    $secondBundle=mn_bundles($inspectionId)[1]['id'];
    $version=mn_one('SELECT version FROM inspeksi_header WHERE id=?',[$inspectionId])['version'];
    $r=request_monev('/staff/unit/monev/action.php',$exam,['action'=>'submit','id'=>$inspectionId,'version'=>$version]);
    http_check($r['status']===200 && !empty(json_decode($r['body'],true)['ok']),'submit without simultaneous signatures');
    $h=mn_one('SELECT * FROM inspeksi_header WHERE id=?',[$inspectionId]);
    http_check($h['status_inspeksi']==='Menunggu Pengesahan' && !mn_signed($inspectionId,'PIC/Kepala Unit'),'pending PIC approval after submit');
    $img=imagecreatetruecolor(320,120); $white=imagecolorallocate($img,255,255,255); $black=imagecolorallocate($img,20,20,20);
    imagefill($img,0,0,$white); imagesetthickness($img,4); imageline($img,35,80,145,42,$black); imageline($img,145,42,285,74,$black);
    ob_start(); imagepng($img); $png=ob_get_clean(); imagedestroy($img);
    $r=request_monev('/staff/unit/monev/action.php',$admin,['action'=>'approve','bundle_id'=>$bundleId,'id'=>$inspectionId,'version'=>$h['version'],'signature_data'=>'data:image/png;base64,'.base64_encode($png)]);
    http_check($r['status']===200 && !empty(json_decode($r['body'],true)['ok']),'later PIC approval with canvas signature');
    $r=request_monev('/staff/unit/monev/pdf.php?id='.$inspectionId,$exam);
    http_check($r['status']===200 && substr($r['body'],0,4)==='%PDF' && strpos($r['body'],'/Subtype /Image')!==false,'PDF embeds approved canvas signature');
    http_check(mn_one('SELECT status_inspeksi FROM inspeksi_header WHERE id=?',[$inspectionId])['status_inspeksi']==='Menunggu Pengesahan','partial approval keeps pending status');
    $r=request_monev('/staff/dashboard_staff.php?unit=monev&id='.$inspectionId,$exam);
    http_check(strpos($r['body'],'name="signature_pemeriksa"')!==false && strpos($r['body'],'name="signature_verifikator"')!==false && strpos($r['body'],'Tanda tangan PIC tersimpan:')!==false,'global canvases displayed with saved first-bundle PIC');
    $version=mn_one('SELECT version FROM inspeksi_header WHERE id=?',[$inspectionId])['version'];
    $r=request_monev('/staff/unit/monev/action.php',$exam,['action'=>'onsite','id'=>$inspectionId,'version'=>$version,'signature_pemeriksa'=>'data:image/png;base64,'.base64_encode($png),'signature_verifikator'=>'data:image/png;base64,'.base64_encode($png)]);
    http_check($r['status']===200 && !empty(json_decode($r['body'],true)['ok']) && mn_one('SELECT status_inspeksi FROM inspeksi_header WHERE id=?',[$inspectionId])['status_inspeksi']==='Menunggu Pengesahan','global signatures cannot bypass missing second PIC');
    $version=mn_one('SELECT version FROM inspeksi_header WHERE id=?',[$inspectionId])['version'];
    $r=request_monev('/staff/unit/monev/action.php',$exam,['action'=>'sign_bundle','id'=>$inspectionId,'version'=>$version,'bundle_id'=>$secondBundle,'signature_data'=>'data:image/png;base64,'.base64_encode($png)]);
    http_check($r['status']===200 && !empty(json_decode($r['body'],true)['ok']) && mn_one('SELECT status_inspeksi FROM inspeksi_header WHERE id=?',[$inspectionId])['status_inspeksi']==='Selesai','last bundle signature completes report');
    $r=request_monev('/staff/dashboard_staff.php?unit=monev&id='.$inspectionId,$exam);
    http_check(substr_count($r['body'],'Status: Selesai')===2 && strpos($r['body'],'Modul tidak dapat dimuat')===false,'both signed bundles show finished accordion headers');
    $r=request_monev('/staff/unit/monev/pdf.php?id='.$inspectionId,$exam);
    http_check($r['status']===200 && substr($r['body'],0,4)==='%PDF','onsite signed PDF');
    echo "MONEV_HTTP_OK ($checks checks)\n";
} finally {
    if ($inspectionId) {
        foreach (mn_all('SELECT path_tanda_tangan FROM inspeksi_pengesahan WHERE inspeksi_id=?',[$inspectionId]) as $s) {
            if (preg_match('/^[a-f0-9]{48}\.png$/D',(string)$s['path_tanda_tangan'])) @unlink(__DIR__.'/../assets/upload/monev/'.$s['path_tanda_tangan']);
        }
        foreach (mn_all('SELECT path_foto_bukti FROM inspeksi_detail_checklist WHERE inspeksi_id=?',[$inspectionId]) as $d) {
            if (preg_match('/^[a-f0-9]{48}\.(jpg|png|webp)$/D',(string)$d['path_foto_bukti'])) @unlink(__DIR__.'/../assets/upload/monev/'.$d['path_foto_bukti']);
        }
        mn_query('DELETE FROM inspeksi_header WHERE id=? AND periode=?',[$inspectionId,'TEST HTTP sementara']);
    }
    foreach ($paths as $path) @unlink($path);
    foreach ($sessions as $sid) { session_id($sid); session_start(); $_SESSION=[]; session_destroy(); }
    ob_end_flush();
}
