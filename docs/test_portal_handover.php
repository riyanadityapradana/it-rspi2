<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__.'/../staff/unit/portal-tiket/handover_helpers.php';
require_once __DIR__.'/../portal-tiket/public_helpers.php';
require_once __DIR__.'/portal_test_helpers.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
if (($argv[1] ?? '') === '--worker') {
    $schema=$argv[2] ?? '';
    if (!preg_match('/^codex_portal_handover_[a-f0-9]{10}$/D',$schema)) exit(2);
    $db=new mysqli('localhost',getenv('DB_USERNAME')?:'root',getenv('DB_PASSWORD')?:'',$schema);
    try {
        pti_handover_save($db,$db,['id_user'=>1,'role'=>'Staff','pti_handover_csrf'=>str_repeat('a',64)],'POST',$argv[3],
            ['action'=>'save_handover','csrf'=>str_repeat('a',64),'deskripsi_penyelesaian'=>'Uji konkurensi serah terima'],$db);
        echo 'saved';
    } catch (DomainException $e) { if ($e->getCode()!==409) throw $e; echo 'conflict'; }
    exit;
}
$schema='codex_portal_handover_'.bin2hex(random_bytes(5));
$db=new mysqli('localhost',getenv('DB_USERNAME')?:'root',getenv('DB_PASSWORD')?:'');
$db->query("CREATE DATABASE `$schema` CHARACTER SET utf8mb4");$db->select_db($schema);$db->set_charset('utf8mb4');
$checks=0;$keep=false;
function ho_check($value,$label) { global $checks;if(!$value)throw new RuntimeException($label);++$checks; }
function ho_reject($fn,$label,$code=null) { try{$fn();}catch(DomainException $e){ho_check($code===null||$code===$e->getCode(),$label);return;}throw new RuntimeException('Not rejected: '.$label); }
function ho_ticket($status='Dikerjakan',$type='Change Request') {
    global $db;
    pt_query($db,"INSERT INTO tiket_pengajuan(jenis_tiket,id_sistem,kontak_pengaju,unit_pemohon,nama_pengaju,ttd_pengaju,status_tiket) VALUES (?,1,'628123456789','Unit Uji','Pemohon Uji','PRIVATE_SIGNATURE',?)",[$type,$status]);
    $id=(string)$db->insert_id;
    pt_query($db,'INSERT INTO tiket_detail_request(id_tiket,nama_perubahan_fitur) VALUES (?,?)',[$id,'Permintaan Uji '.$id]);
    if($status==='Dikerjakan')pt_query($db,"INSERT INTO tiket_analisis_it(id_tiket,id_staf_it,prioritas,analisis_unit_it,keputusan) VALUES (?,101,'Sedang','Analisis uji','Dikerjakan')",[$id]);
    return $id;
}
try {
    cr_sql_file($db,__DIR__.'/../Database/2026-10-20_portal_tiket_layanan_it.sql',$schema);
    $db->query('CREATE TABLE tb_user(id_user INT PRIMARY KEY,nip VARCHAR(40),nama_lengkap VARCHAR(100),role VARCHAR(30),status VARCHAR(20))');
    $db->query("INSERT INTO tb_user VALUES (1,'IT-ST01','Staff Uji','Staff','aktif'),(2,'IT-HD02','Kepala Uji','Kepala Ruangan','aktif'),(3,'IT-ST01','Nonaktif','Staff','nonaktif'),(4,'UNKNOWN','Tanpa Pemetaan','Staff','aktif'),(5,'','Tanpa NIP','Staff','aktif'),(6,'DUP','Ambigu','Staff','aktif'),(7,'INACTIVE','Pegawai Nonaktif','Staff','aktif')");
    $db->query('CREATE TABLE pegawai(id INT PRIMARY KEY,nik VARCHAR(40),nama VARCHAR(100),stts_aktif VARCHAR(20))');
    $db->query("INSERT INTO pegawai VALUES (101,'IT-ST01','Staf Aktif','AKTIF'),(102,'IT-HD02','Kepala Aktif','AKTIF'),(103,'DUP','Duplikat Satu','AKTIF'),(104,'DUP','Duplikat Dua','AKTIF'),(105,'INACTIVE','Pegawai Nonaktif','NONAKTIF')");
    $db->query("INSERT INTO master_sistem(nama_sistem,deskripsi,is_active) VALUES ('Sistem Uji','Fixture serah terima',1)");
    $db->query('CREATE TABLE tb_lokasi(lokasi_id INT PRIMARY KEY,nama_lokasi VARCHAR(150))');$db->query("INSERT INTO tb_lokasi VALUES (1,'Unit Uji')");
    $staff=['id_user'=>1,'role'=>'Staff','nip'=>'IT-HD02','pti_handover_csrf'=>str_repeat('a',64)];
    $head=['id_user'=>2,'role'=>'Kepala Ruangan','pti_handover_csrf'=>$staff['pti_handover_csrf']];
    $post=['action'=>'save_handover','csrf'=>$staff['pti_handover_csrf'],'deskripsi_penyelesaian'=>'  Penyelesaian lengkap <script>window.handoverXss=1</script>  '];
    $id=ho_ticket();
    $save=function($p,$ticket=null,$session=null,$method='POST')use($db,$id,$staff){return pti_handover_save($db,$db,$session??$staff,$method,$ticket??$id,$p,$db);};
    foreach(['GET','PUT','DELETE'] as $method)ho_reject(function()use($save,$post,$method){$save($post,null,null,$method);},'Method rejected',405);
    foreach([[],['id_user'=>3,'role'=>'Staff'],['id_user'=>1,'role'=>'Kepala Ruangan'],['id_user'=>999,'role'=>'Staff'],['id_user'=>'1 OR 1=1','role'=>'Staff']] as $session)ho_reject(function()use($save,$post,$session){$save($post,null,$session);},'Identity rejected',403);
    foreach([['csrf','bad'],['csrf',[]],['action','save_analysis'],['action',[]],['deskripsi_penyelesaian',''],['deskripsi_penyelesaian','   '],['deskripsi_penyelesaian',[]],['deskripsi_penyelesaian',str_repeat('x',10001)]] as [$key,$value])ho_reject(function()use($save,$post,$key,$value){$save(array_replace($post,[$key=>$value]));},'Invalid '.$key);
    foreach(['csrf','deskripsi_penyelesaian'] as $key){$bad=$post;unset($bad[$key]);ho_reject(function()use($save,$bad){$save($bad);},'Required '.$key);}
    foreach(['0','1 OR 1=1',[],'9999'] as $ticket)ho_reject(function()use($save,$post,$ticket){$save($post,$ticket);},'Invalid/missing ticket');
    foreach([4,5,6,7] as $uid)ho_reject(function()use($save,$post,$staff,$uid){$save($post,null,array_replace($staff,['id_user'=>$uid]));},'Unmapped/blank/ambiguous/inactive employee',422);
    ho_check((int)pt_rows($db,'SELECT COUNT(*) n FROM tiket_serah_terima')[0]['n']===0,'Validation leaves no handover');
    $blocked=[];
    foreach(['Menunggu Analisis','Menunggu UAT','Selesai','Ditolak'] as $status){
        $other=ho_ticket($status);$blocked[$status]=$other;
        ho_reject(function()use($save,$post,$other){$save($post,$other);},'Blocked status '.$status,409);
        $row=pt_rows($db,'SELECT status_tiket,token_uat,token_expired_at FROM tiket_pengajuan WHERE id_tiket=?',[$other])[0];
        ho_check($row['status_tiket']===$status&&$row['token_uat']===null&&$row['token_expired_at']===null,'Blocked status/token unchanged');
    }
    pt_query($db,"UPDATE tiket_pengajuan SET token_uat=?,token_expired_at='2000-01-01 00:00:00' WHERE id_tiket=?",[str_repeat('b',64),$id]);
    $before=pt_rows($db,'SELECT status_tiket,token_uat,token_expired_at FROM tiket_pengajuan WHERE id_tiket=?',[$id])[0];
    $db->query("CREATE TRIGGER ho_fail_update BEFORE UPDATE ON tiket_pengajuan FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Forced handover status failure'");
    try{$save($post);throw new RuntimeException('Update failure not raised');}catch(mysqli_sql_exception $e){
        ho_check((int)pt_rows($db,'SELECT COUNT(*) n FROM tiket_serah_terima')[0]['n']===0,'Insert rolled back on failed status/token update');
        ho_check(pt_rows($db,'SELECT status_tiket,token_uat,token_expired_at FROM tiket_pengajuan WHERE id_tiket=?',[$id])[0]===$before,'Original status/token/expiry restored');
    }
    $db->query('DROP TRIGGER ho_fail_update');
    $db->query("CREATE TRIGGER ho_fail_insert BEFORE INSERT ON tiket_serah_terima FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Forced handover insert failure'");
    try{$save($post);throw new RuntimeException('Insert failure not raised');}catch(mysqli_sql_exception $e){ho_check(pt_rows($db,'SELECT status_tiket,token_uat,token_expired_at FROM tiket_pengajuan WHERE id_tiket=?',[$id])[0]===$before,'Failed insert leaves status/token untouched');}
    $db->query('DROP TRIGGER ho_fail_insert');
    $message=$save($post+['id_tiket'=>'999','id_staf_penyerah'=>'999','tgl_serah_terima'=>'2000-01-01 00:00:00','token_uat'=>'FORGED','token_expired_at'=>'2099-01-01 00:00:00','status_pengujian_uat'=>'Berhasil','nama_penerima'=>'FORGED','ttd_penerima'=>'FORGED','catatan_pengujian'=>'FORGED']);
    $handover=pt_rows($db,'SELECT * FROM tiket_serah_terima WHERE id_tiket=?',[$id])[0];
    $header=pt_rows($db,'SELECT status_tiket,token_uat,token_expired_at FROM tiket_pengajuan WHERE id_tiket=?',[$id])[0];
    ho_check($handover['id_staf_penyerah']===101,'SIMRS ID from authenticated account, ignoring forged identity');
    ho_check($handover['deskripsi_penyelesaian']===trim($post['deskripsi_penyelesaian']),'Description saved');
    ho_check(abs(strtotime($handover['tgl_serah_terima'])-time())<10,'Timestamp generated at save');
    ho_check($header['status_tiket']==='Menunggu UAT','Status updated');
    ho_check(strlen($header['token_uat'])===64&&ctype_xdigit($header['token_uat'])&&$header['token_uat']!==$before['token_uat'],'Random 256-bit token replaces old token');
    ho_check($header['token_expired_at']===pt_uat_expiry(new DateTimeImmutable($handover['tgl_serah_terima']))->format('Y-m-d H:i:s'),'Expiry one calendar month');
    foreach(['status_pengujian_uat','catatan_pengujian','nama_penerima','ttd_penerima'] as $key)ho_check($handover[$key]===null,'Applicant-only field remains NULL '.$key);
    ho_check(!str_contains($message,$header['token_uat']),'Token not returned in success message');
    ho_check(!str_contains(json_encode(ptp_detail($db,(int)$id)),$header['token_uat']),'Public detail does not expose token');
    ho_reject(function()use($save,$post){$save($post);},'Repeated POST rejected',409);
    ho_check(count(pt_rows($db,'SELECT id FROM tiket_serah_terima WHERE id_tiket=?',[$id]))===1,'No duplicate handover');
    ho_check(pt_rows($db,'SELECT status_tiket,token_uat,token_expired_at FROM tiket_pengajuan WHERE id_tiket=?',[$id])[0]===$header,'Repeated POST does not rotate token or extend expiry');
    $feature=ho_ticket('Dikerjakan','Fitur Baru');$save($post,$feature,$head);
    $featureRow=pt_rows($db,'SELECT id_staf_penyerah FROM tiket_serah_terima WHERE id_tiket=?',[$feature])[0];$featureToken=pt_rows($db,'SELECT token_uat FROM tiket_pengajuan WHERE id_tiket=?',[$feature])[0]['token_uat'];
    ho_check($featureRow['id_staf_penyerah']===102,'Kepala Ruangan and feature ticket supported');
    ho_check($featureToken!==$header['token_uat'],'Different tickets use different tokens');
    $concurrent=ho_ticket();$db->begin_transaction();pt_rows($db,'SELECT id_tiket FROM tiket_pengajuan WHERE id_tiket=? FOR UPDATE',[$concurrent]);$workers=[];
    try{
        for($i=0;$i<2;$i++){$proc=proc_open([PHP_BINARY,__FILE__,'--worker',$schema,$concurrent],[0=>['pipe','r'],1=>['pipe','w'],2=>['pipe','w']],$pipes);if(!is_resource($proc))throw new RuntimeException('Worker failed');fclose($pipes[0]);$workers[]=[$proc,$pipes];}
        usleep(250000);$db->commit();$results=[];
        foreach($workers as [$proc,$pipes]){$out=stream_get_contents($pipes[1]);$err=stream_get_contents($pipes[2]);fclose($pipes[1]);fclose($pipes[2]);if(proc_close($proc)!==0)throw new RuntimeException($err);$results[]=trim($out);}$workers=[];sort($results);
        ho_check($results===['conflict','saved'],'Concurrent submissions have one winner');
        ho_check(count(pt_rows($db,'SELECT id FROM tiket_serah_terima WHERE id_tiket=?',[$concurrent]))===1,'Concurrency creates one handover');
    }finally{$db->rollback();foreach($workers as [$proc,$pipes]){proc_terminate($proc);foreach($pipes as $pipe)if(is_resource($pipe))fclose($pipe);proc_close($proc);}}
    if(in_array('--keep-for-http',$argv,true)){
        $ready=ho_ticket();$readyHead=ho_ticket('Dikerjakan','Fitur Baru');
        $folder=__DIR__.'/../../.codex_tmp/portal-handover-preview/sessions';if(!is_dir($folder))mkdir($folder,0700,true);session_save_path($folder);$sessions=[];
        foreach(['staff'=>[1,'Staff'],'head'=>[2,'Kepala Ruangan'],'inactive'=>[3,'Staff'],'unmapped'=>[4,'Staff']] as $label=>$account){session_id('phandover'.bin2hex(random_bytes(16)));session_start();$_SESSION=['id_user'=>$account[0],'role'=>$account[1],'nip'=>'fixture','foto'=>''];$sessions[$label]=session_id();session_write_close();}
        $keep=true;echo json_encode(['schema'=>$schema,'sessions'=>$sessions,'ready'=>$ready,'ready_head'=>$readyHead,'processed'=>$id,'blocked'=>$blocked]).PHP_EOL;
    }
    echo 'PASS '.$checks.' isolated handover checks.'.PHP_EOL;
}finally{if(!$keep&&preg_match('/^codex_portal_handover_[a-f0-9]{10}$/D',$schema))$db->query("DROP DATABASE `$schema`");}
