<?php
// CLI-only integration checks in an isolated temporary database. No application data is changed.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
ob_start();
session_save_path(sys_get_temp_dir()); session_id('monevtest'.bin2hex(random_bytes(8))); session_start();
require __DIR__.'/../config/koneksi.php';
require __DIR__.'/../staff/unit/monev/helpers.php';
$original=$config->query('SELECT DATABASE() db')->fetch_assoc()['db'];
$testDb='it_rspi2_monev_test_'.bin2hex(random_bytes(6));
$checks=0; $createdSignatureFiles=[];
function check($condition,$label) { global $checks; if (!$condition) throw new RuntimeException('FAILED: '.$label); $checks++; }
function test_signature_data() {
    check(function_exists('imagecreatetruecolor') && function_exists('imagepng'), 'GD available for signature test');
    $img=imagecreatetruecolor(320,120); $white=imagecolorallocate($img,255,255,255); $black=imagecolorallocate($img,20,20,20);
    imagefill($img,0,0,$white); imagesetthickness($img,4); imageline($img,35,80,145,42,$black); imageline($img,145,42,285,74,$black);
    ob_start(); imagepng($img); $png=ob_get_clean(); imagedestroy($img);
    return 'data:image/png;base64,'.base64_encode($png);
}
function run_action($uid,$post,$expect=true) {
    global $config,$mnUser,$mnUrl,$mnEndpoint,$mnBase;
    $_SESSION['id_user']=$uid; $_SESSION['monev_csrf']='test-csrf';
    $_SERVER['SCRIPT_NAME']='/it-rspi2/staff/unit/monev/action.php'; $_SERVER['REQUEST_METHOD']='POST';
    $_POST=array_merge(['csrf'=>'test-csrf'],$post); $_FILES=[];
    ob_start(); require __DIR__.'/../staff/unit/monev/action.php'; $body=ob_get_clean();
    $result=json_decode($body,true);
    check(is_array($result) && ($result['ok'] ?? null)===$expect, ($post['action'] ?? '').' response: '.$body);
    return $result;
}
function version_post($id,$action,$extra=[]) {
    $h=mn_one('SELECT version FROM inspeksi_header WHERE id=?',[$id]);
    return array_merge(['id'=>$id,'version'=>$h['version'],'action'=>$action],$extra);
}
try {
    $config->query("CREATE DATABASE `$testDb` CHARACTER SET utf8mb4"); $config->select_db($testDb);
    $config->query('CREATE TABLE tb_user (id_user INT PRIMARY KEY,nama_lengkap VARCHAR(100),role VARCHAR(30),status VARCHAR(20),nip VARCHAR(30)) ENGINE=InnoDB');
    $config->query('CREATE TABLE tb_lokasi (lokasi_id INT PRIMARY KEY,nama_lokasi VARCHAR(100)) ENGINE=InnoDB');
    $config->query("INSERT INTO tb_user VALUES (1,'Pemeriksa','Staff','aktif','EMP001'),(2,'PIC','Kepala Ruangan','aktif','EMP002'),(3,'Verifikator','Staff','aktif','EMP003'),(4,'PIC Perbaikan','Staff','aktif','EMP004'),(5,'Bukan Peserta','Staff','aktif','EMP005'),(6,'Nonaktif','Staff','nonaktif','EMP006')");
    $config->query('CREATE TABLE pegawai (nik VARCHAR(30) PRIMARY KEY,nama VARCHAR(100),stts_aktif VARCHAR(20))');
    $config->query("INSERT INTO pegawai VALUES ('EMP001','Pemeriksa','AKTIF'),('EMP002','PIC','AKTIF'),('EMP003','Verifikator','AKTIF'),('EMP006','Nonaktif','KELUAR'),('EMP007','Tanpa akun','AKTIF')");
    $mnPegawaiConnection=$config;
    check(mn_employee_search('EM',1)['results']===[], 'search requires three characters');
    $search=mn_employee_search('EMP',1);
    check(count($search['results'])===4, 'search only active employees including unmapped');
    check(mn_employee_search('EMP002',1)['results'][0]['text']==='EMP002 - PIC','NIK and name label');
    check(mn_employee_search('Tanpa',1)['results'][0]['id']==='EMP007','search by employee name');
    check(mn_employee_search("%' OR 1=1 --",1)['results']===[], 'search parameterized and literal wildcards');
    for ($p=1;$p<=25;$p++) mn_query("INSERT INTO pegawai VALUES (?,?,'AKTIF')",['PAGE'.str_pad($p,3,'0',STR_PAD_LEFT),'Pagination '.$p]);
    $firstPage=mn_employee_search('PAGE',1); $secondPage=mn_employee_search('PAGE',2);
    check(count($firstPage['results'])===20 && $firstPage['pagination']['more'] && count($secondPage['results'])===5 && !$secondPage['pagination']['more'],'bounded paginated employee search');
    check(!array_intersect(array_column($firstPage['results'],'id'),array_column($secondPage['results'],'id')),'search pages do not repeat employees');
    $config->query("INSERT INTO tb_lokasi VALUES (1,'IT')");
    $sql=file_get_contents(__DIR__.'/../Database/2026-09-22_monev_keamanan.sql');
    for ($i=0;$i<2;$i++) { $config->multi_query($sql); do { if ($r=$config->store_result()) $r->free(); } while ($config->more_results() && $config->next_result()); }
    check((int)mn_one('SELECT COUNT(*) n FROM master_kriteria_inspeksi')['n']===10,'migration idempotent');
    check(mn_form_number('2040-01-01')==='INSP-20400101-001','new day starts at 001');
    $config->begin_transaction();
    check(mn_form_number('2040-01-01',true)==='INSP-20400101-001','first reserved number');
    $config->rollback();
    check(mn_form_number('2040-01-01')==='INSP-20400101-001','rollback does not consume number');
    $config->begin_transaction(); mn_form_number('2040-01-01',true); $config->commit();
    check(mn_form_number('2040-01-01')==='INSP-20400101-002','daily increment');
    check(mn_form_number('2040-01-02')==='INSP-20400102-001','next day resets');
    $workers=[];
    for ($i=0;$i<2;$i++) {
        $process=proc_open([PHP_BINARY,__DIR__.'/test_monev_number_worker.php',$testDb],[1=>['pipe','w'],2=>['pipe','w']],$pipes);
        check(is_resource($process),'start concurrent numbering worker');
        $workers[]=[$process,$pipes];
    }
    $numbers=[];
    foreach ($workers as [$process,$pipes]) {
        $numbers[]=trim(stream_get_contents($pipes[1])); $error=stream_get_contents($pipes[2]);
        fclose($pipes[1]); fclose($pipes[2]); check(proc_close($process)===0,'number worker success: '.$error);
    }
    sort($numbers); check($numbers===['INSP-20400103-001','INSP-20400103-002'],'simultaneous transactions receive unique numbers');
    mn_query('INSERT INTO inspeksi_nomor_harian VALUES (?,999)',['2040-01-04']);
    try { mn_form_number('2040-01-04'); check(false,'reject exhausted daily sequence'); } catch (DomainException $e) { check(true,'reject exhausted daily sequence'); }
    run_action(1,['action'=>'employee_map','pegawai_nik'=>'EMP002','user_id'=>2],false);
    foreach ([1,2,3] as $mappedId) run_action(2,['action'=>'employee_map','pegawai_nik'=>'EMP00'.$mappedId,'user_id'=>$mappedId]);
    run_action(2,['action'=>'employee_map','pegawai_nik'=>'EMP007','user_id'=>2],false);
    $create=['action'=>'create','unit_area_id'=>1,'pic_area_nik'=>'EMP002','verifikator_nik'=>'EMP003','periode'=>'2026-09','tgl_waktu_inspeksi'=>date('Y-m-d').'T09:00','jenis_inspeksi'=>'Rutin'];
    run_action(1,array_merge($create,['csrf'=>'invalid']),false);
    run_action(1,array_merge($create,['verifikator_nik'=>'EMP001']),false);
    run_action(1,array_merge($create,['pic_area_nik'=>'EMP006']),false);
    run_action(1,array_merge($create,['pic_area_nik'=>'EMP003']),false);
    run_action(1,array_merge($create,['pic_area_nik'=>'FORGED']),false);
    $new=run_action(1,array_merge($create,['no_formulir'=>'FORGED','tgl_waktu_inspeksi'=>'2020-01-01T09:00'])); parse_str(parse_url($new['redirect'],PHP_URL_QUERY),$q); $id=(int)$q['id'];
    check(mn_one('SELECT no_formulir FROM inspeksi_header WHERE id=?',[$id])['no_formulir']==='INSP-'.date('Ymd').'-001','server generates number by creation date and ignores posted number');
    mn_query('UPDATE inspeksi_header SET no_formulir=? WHERE id=?',['INSP-20391231-009',$id]);
    check(mn_form_number('2039-12-31')==='INSP-20391231-010','existing documents seed sequence');
    check(count(mn_all('SELECT * FROM inspeksi_detail_checklist WHERE inspeksi_id=?',[$id]))===10,'10 criteria snapshots');
    $unmapped=run_action(1,array_merge($create,['verifikator_nik'=>'EMP007']));
    parse_str(parse_url($unmapped['redirect'],PHP_URL_QUERY),$uq); $unmappedId=(int)$uq['id'];
    check(mn_header($unmappedId)['verifikator_id']===null,'unmapped employee can be assigned without granting account access');
    $config->query("INSERT INTO tb_user VALUES (7,'Mapped later','Staff','aktif','EMP007')");
    check(mn_header($unmappedId)['verifikator_id']===null,'profile NIP alone cannot grant approval identity');
    run_action(2,['action'=>'employee_map','pegawai_nik'=>'EMP007','user_id'=>7]);
    check((int)mn_header($unmappedId)['verifikator_id']===7,'admin mapping resolves assignee');
    $config->query("INSERT INTO tb_user VALUES (8,'Duplicate mapping','Staff','aktif','EMP007')");
    check((int)mn_header($unmappedId)['verifikator_id']===7,'duplicate profile NIP does not change mapped identity');
    $config->query("UPDATE tb_user SET status='nonaktif' WHERE id_user=7");
    check(mn_header($unmappedId)['verifikator_id']===null,'inactive mapped account cannot approve');
    run_action(5,version_post($id,'save'),false);
    run_action(1,version_post($id,'submit'),false);
    $save=['detail'=>[],'ruang_diperiksa'=>'Unit uji','alasan_ta_bv'=>'Tidak berlaku / belum diverifikasi'];
    $rows=mn_all('SELECT * FROM inspeksi_detail_checklist WHERE inspeksi_id=? ORDER BY id',[$id]);
    foreach ($rows as $i=>$d) $save['detail'][$d['id']]=['hasil'=>$i===0?'TS':($i===1?'TA':'S'),'lokasi'=>'PC-UJI','catatan'=>$i===0?'Layar tidak dikunci':''];
    $oldVersion=version_post($id,'save',$save);
    run_action(1,$oldVersion);
    run_action(1,$oldVersion,false);
    run_action(1,version_post($id,'save',$save));
    check((int)mn_one('SELECT COUNT(*) n FROM inspeksi_temuan_perbaikan WHERE inspeksi_id=?',[$id])['n']===1,'TS upsert does not duplicate');
    $t=mn_one('SELECT * FROM inspeksi_temuan_perbaikan WHERE inspeksi_id=?',[$id]);
    run_action(1,version_post($id,'submit'),false);
    $finding=['temuan_id'=>$t['id'],'pic_perbaikan_id'=>4,'kondisi_risiko'=>'Layar tidak dikunci','tindakan_segera'=>'Aktifkan kunci layar','pencegahan'=>'Edukasi','target_selesai'=>date('Y-m-d'),'status_temuan'=>'Dalam proses'];
    run_action(1,version_post($id,'finding',$finding));
    foreach ([substr($t['waktu_ditemukan'],0,10), date('Y-m-d',strtotime('+2 days'))] as $executionDate) {
        run_action(1,version_post($id,'finding',array_merge($finding,['tgl_pelaksanaan'=>$executionDate])));
        check(mn_one('SELECT tgl_pelaksanaan FROM inspeksi_temuan_perbaikan WHERE id=?',[$t['id']])['tgl_pelaksanaan']===$executionDate,'execution on discovery date or future date persists');
    }
    $beforeDiscovery=date('Y-m-d',strtotime(substr($t['waktu_ditemukan'],0,10).' -1 day'));
    run_action(1,version_post($id,'finding',array_merge($finding,['tgl_pelaksanaan'=>$beforeDiscovery])),false);
    check(mn_one('SELECT tgl_pelaksanaan FROM inspeksi_temuan_perbaikan WHERE id=?',[$t['id']])['tgl_pelaksanaan']===$executionDate,'invalid execution date leaves saved value unchanged');
    run_action(1,version_post($id,'finding',array_merge($finding,['tgl_pelaksanaan'=>'2026-02-30'])),false);
    run_action(1,version_post($id,'finding',$finding));
    check(mn_one('SELECT tgl_pelaksanaan FROM inspeksi_temuan_perbaikan WHERE id=?',[$t['id']])['tgl_pelaksanaan']===null,'execution date remains optional before verification');
    run_action(5,version_post($id,'finding',$finding),false);
    run_action(4,version_post($id,'finding',array_merge($finding,['pic_perbaikan_id'=>2])),false);
    run_action(1,version_post($id,'submit'));
    check(mn_one('SELECT status_inspeksi FROM inspeksi_header WHERE id=?',[$id])['status_inspeksi']==='Menunggu Pengesahan','submit changes status to waiting approval');
    check(mn_signed($id,'Pemeriksa')!==null,'examiner signature');
    run_action(1,version_post($id,'save',$save),false);
    run_action(1,version_post($id,'approve'),false);
    run_action(3,version_post($id,'approve'),false);
    run_action(2,version_post($id,'return',['catatan_validasi'=>'Lengkapi bukti']));
    check(mn_signed($id,'Pemeriksa')===null,'return revokes signature');
    check((int)mn_one('SELECT COUNT(*) n FROM inspeksi_pengesahan WHERE inspeksi_id=?',[$id])['n']===1,'revoked signature retained');
    run_action(1,version_post($id,'submit'));
    $firstBundle=mn_bundles($id)[0]['id'];
    run_action(2,version_post($id,'approve',['bundle_id'=>$firstBundle,'signature_data'=>test_signature_data()]));
    $sig=mn_one("SELECT path_tanda_tangan FROM inspeksi_pengesahan WHERE inspeksi_id=? AND peran='PIC/Kepala Unit' AND dibatalkan_pada IS NULL",[$id])['path_tanda_tangan'];
    check(preg_match('/^[a-f0-9]{48}\.png$/D',$sig) && is_file(__DIR__.'/../assets/upload/monev/'.$sig),'direct signature stored');
    $createdSignatureFiles[]=__DIR__.'/../assets/upload/monev/'.$sig;
    run_action(2,version_post($id,'approve'),false);
    run_action(3,version_post($id,'verify',['temuan_id'=>$t['id'],'hasil_pemeriksaan_ulang'=>'Baik']),false);
    $finding['tgl_pelaksanaan']=date('Y-m-d');
    run_action(4,version_post($id,'finding',$finding));
    // Simulate an already stored image reference; actual upload/download is checked separately via HTTP.
    mn_query('UPDATE inspeksi_temuan_perbaikan SET path_bukti_perbaikan=? WHERE id=?',[str_repeat('a',48).'.jpg',$t['id']]);
    run_action(2,version_post($id,'verify',['temuan_id'=>$t['id'],'hasil_pemeriksaan_ulang'=>'Baik']),false);
    run_action(3,version_post($id,'verify',['temuan_id'=>$t['id'],'hasil_pemeriksaan_ulang'=>'Kunci layar aktif']));
    run_action(2,version_post($id,'return',['catatan_validasi'=>'Koreksi cakupan saja']));
    $changed=$save; $changed['detail'][$rows[0]['id']]['lokasi']='PC-LAIN';
    run_action(1,version_post($id,'save',$changed),false);
    run_action(1,version_post($id,'save',$save));
    run_action(1,version_post($id,'submit',['signature_data'=>test_signature_data()]));
    run_action(2,version_post($id,'approve',['bundle_id'=>$firstBundle,'signature_data'=>test_signature_data()]));
    run_action(3,version_post($id,'approve',['signature_data'=>test_signature_data()]));
    check(mn_one('SELECT status_inspeksi FROM inspeksi_header WHERE id=?',[$id])['status_inspeksi']==='Selesai','complete only after approvals and findings');
    run_action(4,version_post($id,'finding',$finding),false);
    run_action(2,version_post($id,'return',['catatan_validasi'=>'Terlambat']),false);
    run_action(1,['action'=>'master','kategori'=>'X','deskripsi'=>'X','is_active'=>1],false);
    run_action(2,['action'=>'master','id'=>1,'kategori'=>'Baru','deskripsi'=>'Kriteria diperbarui','is_active'=>1,'urutan'=>1]);
    check(mn_one('SELECT deskripsi_snapshot FROM inspeksi_detail_checklist WHERE id=?',[$rows[0]['id']])['deskripsi_snapshot']!== 'Kriteria diperbarui','historical snapshots unchanged');
    // Render the integrated detail view to catch missing columns and PHP warnings.
    $_GET=['id'=>$id]; $_SERVER['SCRIPT_NAME']='/it-rspi2/admin/dashboard_admin.php';
    ob_start(); require __DIR__.'/../staff/unit/monev/page.php'; $html=ob_get_clean();
    check(strpos($html,'Kunci layar aktif')!==false && strpos($html,'Modul tidak dapat dimuat')===false,'detail page render');
    $drawing=test_signature_data();
    $firstBundle=mn_bundles($unmappedId)[0]['id'];
    $bundleSave=['bundle_payload_complete'=>'1','bundle'=>[$firstBundle=>['ruang_diperiksa'=>'Unit pertama']],'detail'=>[]];
    foreach (mn_all('SELECT id FROM inspeksi_detail_checklist WHERE inspeksi_id=?',[$unmappedId]) as $row) $bundleSave['detail'][$row['id']]=['hasil'=>'S','lokasi'=>'PC-PERTAMA','catatan'=>'Baik'];
    run_action(1,version_post($unmappedId,'save',$bundleSave));
    run_action(1,version_post($unmappedId,'sign_bundle',['bundle_id'=>$firstBundle,'signature_data'=>$drawing]));
    check(mn_signed($unmappedId,'PIC/Kepala Unit',$firstBundle)!==null,'PIC signs only first bundle');
    run_action(1,version_post($unmappedId,'save',$bundleSave));
    check(mn_signed($unmappedId,'PIC/Kepala Unit',$firstBundle)!==null,'unchanged save preserves bundle approval');
    $config->query("INSERT INTO tb_lokasi VALUES (2,'Unit Kedua')");
    $config->query("INSERT INTO pegawai VALUES ('EMP005','PIC Kedua','AKTIF')");
    run_action(2,['action'=>'employee_map','pegawai_nik'=>'EMP005','user_id'=>5]);
    $headerBefore=mn_one('SELECT no_formulir FROM inspeksi_header WHERE id=?',[$unmappedId]);
    $newBundle=['unit_area_id'=>2,'pic_area_nik'=>'EMP005','ruang_diperiksa'=>'Cakupan kedua','detail'=>[]];
    foreach (mn_all('SELECT id FROM master_kriteria_inspeksi WHERE is_active=1') as $k) $newBundle['detail'][$k['id']]=['hasil'=>'S','lokasi'=>'PC-KEDUA','catatan'=>'Catatan kedua'];
    $adding=array_merge($bundleSave,['add_bundle'=>1,'new_bundle'=>$newBundle]);
    $bad=$adding; unset($bad['new_bundle']['detail'][1]); run_action(1,version_post($unmappedId,'save',$bad),false);
    check(count(mn_bundles($unmappedId))===1,'incomplete bundle rolls back without damaging existing bundle');
    $stale=version_post($unmappedId,'save',$adding); run_action(1,$stale); run_action(1,$stale,false);
    $secondBundle=mn_bundles($unmappedId)[1]['id'];
    check((int)mn_one('SELECT COUNT(*) n FROM inspeksi_detail_checklist WHERE inspeksi_id=?',[$unmappedId])['n']===20,'second bundle adds all ten criteria');
    check(mn_one('SELECT no_formulir FROM inspeksi_header WHERE id=?',[$unmappedId])===$headerBefore,'bundle retains same document number');
    check(mn_bundle($unmappedId,$secondBundle)['ruang_diperiksa']==='Cakupan kedua' && mn_bundle($unmappedId,$firstBundle)['ruang_diperiksa']==='Unit pertama','coverage separated by bundle');
    check(!mn_signed($unmappedId,'PIC/Kepala Unit',$secondBundle),'first PIC signature cannot approve second bundle');
    run_action(5,version_post($unmappedId,'sign_bundle',['bundle_id'=>$firstBundle,'signature_data'=>$drawing]),false);
    run_action(1,version_post($unmappedId,'sign_bundle',['bundle_id'=>mn_bundles($id)[0]['id'],'signature_data'=>$drawing]),false);
    // Changes revoke only the affected PIC and the global signatures.
    $allSave=$bundleSave; $allSave['bundle'][$secondBundle]=['ruang_diperiksa'=>'Cakupan kedua'];
    foreach (mn_all('SELECT id FROM inspeksi_detail_checklist WHERE bundle_id=?',[$secondBundle]) as $row) $allSave['detail'][$row['id']]=['hasil'=>'S','lokasi'=>'PC-KEDUA','catatan'=>'Catatan kedua'];
    $firstDetail=array_key_first($allSave['detail']); $allSave['detail'][$firstDetail]['catatan']='Koreksi';
    run_action(1,version_post($unmappedId,'save',$allSave));
    check(!mn_signed($unmappedId,'PIC/Kepala Unit',$firstBundle),'editing bundle revokes its PIC signature');
    run_action(1,version_post($unmappedId,'sign_bundle',['bundle_id'=>$firstBundle,'signature_data'=>$drawing]));
    run_action(5,version_post($unmappedId,'onsite',['signature_pemeriksa'=>$drawing]),false);
    run_action(1,version_post($unmappedId,'onsite',['signature_pemeriksa'=>$drawing,'signature_verifikator'=>$drawing]));
    check(mn_one('SELECT status_inspeksi FROM inspeksi_header WHERE id=?',[$unmappedId])['status_inspeksi']==='Menunggu Pengesahan','two global signatures plus first PIC cannot finish multi-bundle report');
    run_action(1,version_post($unmappedId,'save',$allSave),false);
    run_action(5,version_post($unmappedId,'sign_bundle',['bundle_id'=>$secondBundle,'signature_data'=>'invalid']),false);
    run_action(5,version_post($unmappedId,'sign_bundle',['bundle_id'=>$secondBundle,'signature_data'=>$drawing]));
    check(mn_one('SELECT status_inspeksi FROM inspeksi_header WHERE id=?',[$unmappedId])['status_inspeksi']==='Selesai','last bundle PIC completes document');
    check((int)mn_one('SELECT COUNT(*) n FROM inspeksi_pengesahan WHERE inspeksi_id=? AND dibatalkan_pada IS NULL AND bundle_id IS NULL',[$unmappedId])['n']===2,'only two global approvals');
    check((int)mn_one("SELECT COUNT(DISTINCT bundle_id) n FROM inspeksi_pengesahan WHERE inspeksi_id=? AND peran='PIC/Kepala Unit' AND dibatalkan_pada IS NULL",[$unmappedId])['n']===2,'one partial approval per bundle');
    // Render the production report loader and PDF with two independently signed units.
    require_once __DIR__.'/../staff/unit/monev/report.php';
    if (!defined('K_PATH_CACHE')) define('K_PATH_CACHE',rtrim(sys_get_temp_dir(),'/\\').DIRECTORY_SEPARATOR);
    require_once __DIR__.'/../staff/unit/monev/pdf_renderer.php';
    $report=mn_pdf_load_report($unmappedId);
    $nonIt=$report['bundles'][1];
    $serverRows=array_filter($nonIt['details'],function($d){return $d['kategori_snapshot']==='Ruang Server';});
    check(count($serverRows)===3 && count($nonIt['details'])===10,'non IT retains ten rows and respects snapshotted category after master rename');
    foreach ($serverRows as $d) check($d['hasil']==='TA' && $d['lokasi_id_perangkat']==='Unit Kedua' && $d['catatan_bukti']==='Ruang server tidak tersedia','backend overrides posted non IT server values');
    check(count(mn_visible_bundle_details($nonIt))===7,'PDF excludes only non IT server category rows');
    check(count(mn_visible_bundle_details($report['bundles'][0]))===10,'PDF keeps all ten IT rows');
    check(count(array_filter(mn_visible_bundle_details($nonIt),function($d){return $d['hasil']==='TA';}))===0,'PDF TA recap excludes automatic server defaults');
    check(count($report['bundles'])===2 && count($report['bundles'][0]['details'])===10 && count($report['bundles'][1]['details'])===10,'PDF report contains both complete bundles');
    check(count($report['active_signatures'])===2 && $report['bundles'][0]['signature']['bundle_id']!==$report['bundles'][1]['signature']['bundle_id'],'PDF separates global and partial signatures');
    $pdf=mn_build_inspection_pdf($report);
    check(substr($pdf,0,4)==='%PDF' && strlen($pdf)>5000 && strpos($pdf,'/Subtype /Image')!==false,'two-bundle signed PDF renders');
    $units=[1=>'IT',2=>'Unit Kedua'];
    ob_start();
    foreach ($report['bundles'] as $bundle) mn_render_bundle($bundle,$bundle['details'],$units,false,false,$report['header']);
    $bundleHtml=ob_get_clean();
    check(substr_count($bundleHtml,'Status: Selesai')===2 && strpos($bundleHtml,'Unit Kedua - PIC Kedua')!==false,'saved bundle headers show unit PIC and status');
    ob_start(); mn_render_bundle([],mn_all('SELECT * FROM master_kriteria_inspeksi WHERE is_active=1 ORDER BY urutan,id'),$units,true,true); $newBundleHtml=ob_get_clean();
    check(substr_count($newBundleHtml,'class="mn-results"')===10 && strpos($newBundleHtml,'name="new_bundle[ruang_diperiksa]"')!==false && strpos($newBundleHtml,'name="signature_pic_new"')!==false,'new UI renders ten criteria coverage and PIC canvas');
    check(strpos($newBundleHtml,'checked')===false && strpos($newBundleHtml,'name="kriteria_id"')===false,'new bundle has no selected result or single-criterion dropdown');
    run_action(1,version_post($unmappedId,'sign_bundle',['bundle_id'=>$secondBundle,'signature_data'=>$drawing]),false);
    // Upgrade a legacy report twice and retain its data and PIC signature.
    mn_query("INSERT INTO inspeksi_header (periode,tgl_waktu_inspeksi,unit_area_id,pic_area_id,pemeriksa_id,verifikator_id,jenis_inspeksi) VALUES ('Legacy',NOW(),1,2,1,3,'Rutin')");
    $legacyId=$config->insert_id;
    mn_query("INSERT INTO inspeksi_detail_checklist (inspeksi_id,kriteria_id,kategori_snapshot,deskripsi_snapshot,hasil,lokasi_id_perangkat) VALUES (?,1,'Lama','Butir historis','S','PC-LAMA')",[$legacyId]);
    mn_query("INSERT INTO inspeksi_cakupan (inspeksi_id,ruang_diperiksa) VALUES (?,'Cakupan lama')",[$legacyId]);
    mn_query("INSERT INTO inspeksi_pengesahan (inspeksi_id,peran,user_id) VALUES (?,'PIC/Kepala Unit',2)",[$legacyId]);
    require_once __DIR__.'/../Database/monev_bundle_upgrade.php';
    mn_upgrade_bundles($config); mn_upgrade_bundles($config);
    $legacyBundles=mn_bundles($legacyId);
    check(count($legacyBundles)===1 && $legacyBundles[0]['ruang_diperiksa']==='Cakupan lama','idempotent migration preserves legacy coverage');
    check(mn_signed($legacyId,'PIC/Kepala Unit',$legacyBundles[0]['id'])!==null,'migration binds historical PIC signature to first bundle');
    check(mn_one('SELECT bundle_id FROM inspeksi_detail_checklist WHERE inspeksi_id=?',[$legacyId])['bundle_id']===$legacyBundles[0]['id'],'migration preserves and links historical checklist');
    echo "MONEV_INTEGRATION_OK ($checks checks)\n";
} finally {
    if ($config->query('SELECT DATABASE()')->fetch_row()[0]===$testDb) {
        foreach (mn_all('SELECT path_tanda_tangan FROM inspeksi_pengesahan WHERE path_tanda_tangan IS NOT NULL') as $s) if (preg_match('/^[a-f0-9]{48}\.png$/D',$s['path_tanda_tangan'])) @unlink(__DIR__.'/../assets/upload/monev/'.$s['path_tanda_tangan']);
    }
    foreach ($createdSignatureFiles as $file) @unlink($file);
    $config->select_db($original);
    if (preg_match('/^it_rspi2_monev_test_[a-f0-9]{12}$/D',$testDb)) $config->query("DROP DATABASE IF EXISTS `$testDb`");
    session_destroy();
    ob_end_flush();
}
