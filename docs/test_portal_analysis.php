<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../staff/unit/portal-tiket/analysis_helpers.php';
require_once __DIR__ . '/portal_test_helpers.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Worker dipakai dua proses bersamaan untuk menguji lock tiket yang sama.
if (($argv[1] ?? '') === '--worker') {
    $schema = $argv[2] ?? '';
    if (!preg_match('/^codex_portal_analysis_[a-f0-9]{10}$/D', $schema)) exit(2);
    $db = new mysqli('localhost', getenv('DB_USERNAME') ?: 'root', getenv('DB_PASSWORD') ?: '', $schema);
    try {
        pti_analysis_save($db,$db,['id_user'=>1,'role'=>'Staff','pti_analysis_csrf'=>str_repeat('a',64)],'POST',$argv[3],
            ['action'=>'save_analysis','csrf'=>str_repeat('a',64),'prioritas'=>'Tinggi','analisis_unit_it'=>'Uji konkurensi','keputusan'=>'Dikerjakan'],$db);
        echo 'saved';
    } catch (DomainException $e) { if ($e->getCode() !== 409) throw $e; echo 'conflict'; }
    exit;
}
$schema = 'codex_portal_analysis_' . bin2hex(random_bytes(5));
$db = new mysqli('localhost', getenv('DB_USERNAME') ?: 'root', getenv('DB_PASSWORD') ?: '');
$db->query("CREATE DATABASE `$schema` CHARACTER SET utf8mb4"); $db->select_db($schema); $db->set_charset('utf8mb4');
$checks = 0; $keep = false;
function an_check($value,$label) { global $checks; if (!$value) throw new RuntimeException($label); ++$checks; }
function an_reject($fn,$label,$code=null) { try { $fn(); } catch (DomainException $e) { an_check($code===null || $e->getCode()===$code,$label); return; } throw new RuntimeException('Not rejected: '.$label); }
function an_ticket($status='Menunggu Analisis',$type='Change Request') {
    global $db;
    pt_query($db,"INSERT INTO tiket_pengajuan(jenis_tiket,id_sistem,kontak_pengaju,unit_pemohon,nama_pengaju,ttd_pengaju,status_tiket) VALUES (?,1,'628123456789','Unit Uji','Pemohon Uji','fixture',?)",[$type,$status]);
    return (string)$db->insert_id;
}
try {
    cr_sql_file($db,__DIR__.'/../Database/2026-10-20_portal_tiket_layanan_it.sql',$schema);
    $db->query('CREATE TABLE tb_user(id_user INT PRIMARY KEY,nip VARCHAR(40),nama_lengkap VARCHAR(100),role VARCHAR(30),status VARCHAR(20))');
    $db->query("INSERT INTO tb_user VALUES (1,'IT-ST01','Staff Uji','Staff','aktif'),(2,'IT-HD02','Kepala Uji','Kepala Ruangan','aktif'),(3,'IT-ST01','Nonaktif','Staff','nonaktif'),(4,'UNKNOWN','Tanpa Pemetaan','Staff','aktif'),(5,'','Tanpa NIP','Staff','aktif'),(6,'DUP','Ambigu','Staff','aktif'),(7,'INACTIVE','Pegawai Nonaktif','Staff','aktif')");
    $db->query('CREATE TABLE pegawai(id INT PRIMARY KEY,nik VARCHAR(40),nama VARCHAR(100),stts_aktif VARCHAR(20))');
    $db->query("INSERT INTO pegawai VALUES (101,'IT-ST01','Staf Aktif','AKTIF'),(102,'IT-HD02','Kepala Aktif','AKTIF'),(103,'DUP','Duplikat Satu','AKTIF'),(104,'DUP','Duplikat Dua','AKTIF'),(105,'INACTIVE','Pegawai Nonaktif','NONAKTIF')");
    $db->query("INSERT INTO master_sistem(nama_sistem,deskripsi,is_active) VALUES ('Sistem Uji','Fixture analisis',1)");
    $db->query('CREATE TABLE tb_lokasi(lokasi_id INT PRIMARY KEY,nama_lokasi VARCHAR(150))');
    $db->query("INSERT INTO tb_lokasi VALUES (1,'Unit Uji')");
    $staff = ['id_user'=>1,'role'=>'Staff','pti_analysis_csrf'=>str_repeat('a',64),'nip'=>'IT-HD02'];
    $head = ['id_user'=>2,'role'=>'Kepala Ruangan','pti_analysis_csrf'=>$staff['pti_analysis_csrf']];
    $post = ['action'=>'save_analysis','csrf'=>$staff['pti_analysis_csrf'],'prioritas'=>'Tinggi','analisis_unit_it'=>'  Analisis Uji  ','keputusan'=>' Keputusan Uji '];
    $id = an_ticket();
    $save = function ($p,$ticket=null,$session=null,$method='POST') use($db,$staff,$id) { return pti_analysis_save($db,$db,$session ?? $staff,$method,$ticket ?? $id,$p,$db); };
    foreach (['GET','PUT','DELETE'] as $method) an_reject(function()use($save,$post,$method){$save($post,null,null,$method);},'Method '.$method,405);
    foreach ([[],['id_user'=>3,'role'=>'Staff'],['id_user'=>1,'role'=>'Kepala Ruangan'],['id_user'=>'1 OR 1=1','role'=>'Staff'],['id_user'=>999,'role'=>'Staff']] as $session) {
        an_reject(function()use($save,$post,$session){$save($post,null,$session);},'Invalid identity',403);
    }
    foreach ([['csrf','bad'],['csrf',[]],['action','forged'],['action',[]],['prioritas',''],['prioritas','Urgent'],['prioritas',['Tinggi','Rendah']],['analisis_unit_it',' '],['analisis_unit_it',[]],['analisis_unit_it',str_repeat('a',10001)],['keputusan',''],['keputusan',[]],['keputusan',str_repeat('a',10001)]] as [$key,$value]) {
        an_reject(function()use($save,$post,$key,$value){$save(array_replace($post,[$key=>$value]));},'Invalid '.$key);
    }
    foreach (['csrf','prioritas','analisis_unit_it','keputusan'] as $key) { $bad=$post;unset($bad[$key]);an_reject(function()use($save,$bad){$save($bad);},'Required '.$key); }
    foreach (['0','1 OR 1=1',[], '9999'] as $ticket) an_reject(function()use($save,$post,$ticket){$save($post,$ticket);},'Invalid ticket');
    foreach ([4,5,6,7] as $uid) an_reject(function()use($save,$post,$staff,$uid){$save($post,null,array_replace($staff,['id_user'=>$uid]));},'Missing/inactive/ambiguous mapping',422);
    an_check((int)pt_rows($db,'SELECT COUNT(*) n FROM tiket_analisis_it')[0]['n']===0,'Rejected writes create no analysis');
    foreach (['Dikerjakan','Menunggu UAT','Selesai','Ditolak'] as $status) {
        $other=an_ticket($status);
        an_reject(function()use($save,$post,$other){$save($post,$other);},'Blocked status '.$status,409);
        an_check(pt_rows($db,'SELECT status_tiket FROM tiket_pengajuan WHERE id_tiket=?',[$other])[0]['status_tiket']===$status,'Blocked status unchanged');
    }
    // Kegagalan UPDATE setelah INSERT wajib membatalkan keduanya.
    $db->query("CREATE TRIGGER an_fail_update BEFORE UPDATE ON tiket_pengajuan FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Forced status failure'");
    try { $save($post); throw new RuntimeException('Status failure not raised'); }
    catch (mysqli_sql_exception $e) {
        an_check((int)pt_rows($db,'SELECT COUNT(*) n FROM tiket_analisis_it')[0]['n']===0,'Insert rolled back when status update fails');
        an_check(pt_rows($db,'SELECT status_tiket FROM tiket_pengajuan WHERE id_tiket=?',[$id])[0]['status_tiket']==='Menunggu Analisis','Status remains waiting after rollback');
    }
    $db->query('DROP TRIGGER an_fail_update');
    $db->query("CREATE TRIGGER an_fail_insert BEFORE INSERT ON tiket_analisis_it FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Forced insert failure'");
    try { $save($post); throw new RuntimeException('Insert failure not raised'); }
    catch (mysqli_sql_exception $e) { an_check(pt_rows($db,'SELECT status_tiket FROM tiket_pengajuan WHERE id_tiket=?',[$id])[0]['status_tiket']==='Menunggu Analisis','Failed insert does not update status'); }
    $db->query('DROP TRIGGER an_fail_insert');
    $save($post+['id_staf_it'=>'999','id_tiket'=>'999','status_tiket'=>'Selesai','nip'=>'IT-HD02']);
    $analysis=pt_rows($db,'SELECT * FROM tiket_analisis_it WHERE id_tiket=?',[$id])[0];
    an_check($analysis['id_staf_it']===101,'SIMRS employee ID from account, ignoring forged POST/session NIP');
    an_check($analysis['analisis_unit_it']==='Analisis Uji' && $analysis['keputusan']==='Keputusan Uji','Validated input saved');
    an_check(pt_rows($db,'SELECT status_tiket FROM tiket_pengajuan WHERE id_tiket=?',[$id])[0]['status_tiket']==='Dikerjakan','Status changed atomically');
    an_reject(function()use($save,$post){$save($post);},'Repeat POST conflicts',409);
    an_check(count(pt_rows($db,'SELECT id FROM tiket_analisis_it WHERE id_tiket=?',[$id]))===1,'No duplicate analysis');
    foreach (['Sedang','Rendah'] as $priority) {
        $next=an_ticket('Menunggu Analisis','Fitur Baru');
        $save(array_replace($post,['prioritas'=>$priority]),$next,$head);
        $a=pt_rows($db,'SELECT prioritas,id_staf_it FROM tiket_analisis_it WHERE id_tiket=?',[$next])[0];
        an_check($a['prioritas']===$priority && $a['id_staf_it']===102,'Head role / feature / priority '.$priority);
    }
    // Dua koneksi benar-benar berlomba; transaksi ditahan agar keduanya mengantre.
    $concurrent=an_ticket();
    $db->begin_transaction(); pt_rows($db,'SELECT id_tiket FROM tiket_pengajuan WHERE id_tiket=? FOR UPDATE',[$concurrent]);
    $workers=[];
    try {
        for($i=0;$i<2;$i++) {
            $proc=proc_open([PHP_BINARY,__FILE__,'--worker',$schema,$concurrent],[0=>['pipe','r'],1=>['pipe','w'],2=>['pipe','w']],$pipes);
            if(!is_resource($proc))throw new RuntimeException('Cannot start concurrency worker');
            fclose($pipes[0]);$workers[]=[$proc,$pipes];
        }
        usleep(250000);$db->commit();$results=[];
        foreach($workers as [$proc,$pipes]) { $out=stream_get_contents($pipes[1]);$err=stream_get_contents($pipes[2]);fclose($pipes[1]);fclose($pipes[2]);$exit=proc_close($proc);if($exit!==0)throw new RuntimeException($err);$results[]=trim($out); }
        $workers=[];sort($results);
        an_check($results===['conflict','saved'],'Concurrent submissions have one winner');
        an_check(count(pt_rows($db,'SELECT id FROM tiket_analisis_it WHERE id_tiket=?',[$concurrent]))===1,'Concurrent submissions create one analysis');
    } finally { $db->rollback();foreach($workers as [$proc,$pipes]) { proc_terminate($proc);foreach($pipes as $pipe)if(is_resource($pipe))fclose($pipe);proc_close($proc); } }
    if (in_array('--keep-for-http',$argv,true)) {
        $waiting=an_ticket();
        $folder=__DIR__.'/../../.codex_tmp/portal-analysis-preview/sessions';if(!is_dir($folder))mkdir($folder,0700,true);
        session_save_path($folder);$sessions=[];
        foreach(['staff'=>[1,'Staff'],'head'=>[2,'Kepala Ruangan'],'inactive'=>[3,'Staff'],'unmapped'=>[4,'Staff']] as $key=>$account) {
            session_id('panalysis'.bin2hex(random_bytes(16)));session_start();$_SESSION=['id_user'=>$account[0],'role'=>$account[1],'foto'=>'','nip'=>'fixture'];$sessions[$key]=session_id();session_write_close();
        }
        $keep=true;echo json_encode(['schema'=>$schema,'sessions'=>$sessions,'waiting'=>$waiting,'processed'=>$id]) . PHP_EOL;
    }
    echo 'PASS '.$checks.' isolated analysis checks.' . PHP_EOL;
} finally {
    if(!$keep && preg_match('/^codex_portal_analysis_[a-f0-9]{10}$/D',$schema))$db->query("DROP DATABASE `$schema`");
}
