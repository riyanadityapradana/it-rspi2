<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require_once __DIR__.'/../portal-tiket/uat_helpers.php';
require_once __DIR__.'/../portal-tiket/signature_helpers.php';
require_once __DIR__.'/../portal-tiket/public_helpers.php';
require_once __DIR__.'/../staff/unit/portal-tiket/analysis_helpers.php';
require_once __DIR__.'/../staff/unit/portal-tiket/handover_helpers.php';
require_once __DIR__.'/portal_test_helpers.php';
mysqli_report(MYSQLI_REPORT_ERROR|MYSQLI_REPORT_STRICT);
$schema='codex_uat_detail_'.bin2hex(random_bytes(5));$db=new mysqli('localhost',getenv('DB_USERNAME')?:'root',getenv('DB_PASSWORD')?:'');$db->query("CREATE DATABASE `$schema` CHARACTER SET utf8mb4");$db->select_db($schema);$db->set_charset('utf8mb4');$checks=0;$keep=false;
function ud_check($v,$label){global $checks;if(!$v)throw new RuntimeException($label);++$checks;}
function ud_reject($fn,$code,$label){try{$fn();}catch(DomainException $e){ud_check($e->getCode()===$code,$label);return;}throw new RuntimeException('Not rejected: '.$label);}
try{
    cr_sql_file($db,__DIR__.'/../Database/2026-10-20_portal_tiket_layanan_it.sql',$schema);
    // Migrasi aman diulang dan tidak menghapus data yang sudah tersedia.
    cr_sql_file($db,__DIR__.'/../Database/2026-10-21_portal_tiket_identitas_qr.sql',$schema);
    cr_sql_file($db,__DIR__.'/../Database/2026-10-21_portal_tiket_identitas_qr.sql',$schema);
    ud_check(in_array('tgl_analisis',array_column(pt_rows($db,'SHOW COLUMNS FROM tiket_analisis_it'),'Field'),true),'Analysis migration columns');
    $db->query('CREATE TABLE pegawai(id INT PRIMARY KEY,nik VARCHAR(40),nama VARCHAR(150),stts_aktif VARCHAR(20))');$db->query("INSERT INTO pegawai VALUES (101,'STAFF','Analis Uji, S.Kom','AKTIF'),(102,'HEAD','Penyerah Uji, S.Kom','AKTIF')");
    $db->query('CREATE TABLE tb_user(id_user INT PRIMARY KEY,nip VARCHAR(40),nama_lengkap VARCHAR(100),role VARCHAR(30),status VARCHAR(20))');$db->query("INSERT INTO tb_user VALUES (1,'STAFF','Akun Staff','Staff','aktif'),(2,'HEAD','Akun Kepala','Kepala Ruangan','aktif')");
    $db->query("INSERT INTO master_sistem(nama_sistem,deskripsi,is_active) VALUES ('Sistem Uji','Fixture QR',1)");
    $staff=['id_user'=>1,'role'=>'Staff','pti_analysis_csrf'=>str_repeat('a',64)];$head=['id_user'=>2,'role'=>'Kepala Ruangan','pti_handover_csrf'=>str_repeat('b',64)];
    $ids=[];$tokens=[];$signature=cr_image();
    foreach(['Change Request','Fitur Baru'] as $type){
        pt_query($db,"INSERT INTO tiket_pengajuan(jenis_tiket,id_sistem,kontak_pengaju,unit_pemohon,nama_pengaju,ttd_pengaju) VALUES (?,1,'628123456789','Unit Pemohon Uji','Pemohon Uji',?)",[$type,$signature]);$id=$db->insert_id;$ids[]=$id;
        pt_query($db,'INSERT INTO tiket_detail_request(id_tiket,nama_perubahan_fitur,kondisi_masalah,perubahan_diharapkan,dampak_modul,latar_belakang,deskripsi_alur_kerja,target_pengguna,output_diharapkan) VALUES (?,?,?,?,?,?,?,?,?)',[$id,'Permintaan '.$type,'Kondisi lengkap','Harapan lengkap','Dampak lengkap','Latar lengkap','Alur lengkap','Pengguna lengkap','Output lengkap']);
        pti_analysis_save($db,$db,$staff,'POST',(string)$id,['action'=>'save_analysis','csrf'=>$staff['pti_analysis_csrf'],'prioritas'=>'Sedang','analisis_unit_it'=>'Analisis <script>window.uatXss=1</script>','keputusan'=>'Keputusan lengkap','tgl_analisis'=>'2000-01-01','nama_staf_it'=>'FORGED'],$db);
        pti_handover_save($db,$db,$head,'POST',(string)$id,['action'=>'save_handover','csrf'=>$head['pti_handover_csrf'],'deskripsi_penyelesaian'=>'Penyelesaian lengkap','nama_staf_penyerah'=>'FORGED'],$db);
        $tokens[$id]=pt_rows($db,'SELECT token_uat FROM tiket_pengajuan WHERE id_tiket=?',[$id])[0]['token_uat'];
    }
    $a=pt_rows($db,'SELECT tgl_analisis,nama_staf_it FROM tiket_analisis_it WHERE id_tiket=1')[0];
    ud_check(abs(strtotime($a['tgl_analisis'])-time())<10,'Actual analysis timestamp, ignores POST date');
    ud_check($a['nama_staf_it']==='Analis Uji, S.Kom','Authoritative analyst snapshot');
    ud_check(pt_rows($db,'SELECT nama_staf_penyerah FROM tiket_serah_terima WHERE id_tiket=1')[0]['nama_staf_penyerah']==='Penyerah Uji, S.Kom','Authoritative handover snapshot');
    $db->query("UPDATE pegawai SET nama='Nama Berubah' WHERE id=101");
    $review=pt_uat_review($db,$tokens[1],null,$db);
    ud_check($review['analyses'][0]['nama_staf_it']==='Analis Uji, S.Kom','Snapshot survives master-name change');
    ud_check($review['ticket']['nama_pengaju']==='Pemohon Uji'&&$review['ticket']['unit_pemohon']==='Unit Pemohon Uji'&&$review['ticket']['kontak_pengaju']==='628123456789','Full request metadata behind token');
    ud_check(str_starts_with($review['ticket']['ttd_pengaju'],'data:image/png;base64,'),'Applicant PNG signature');
    ud_check($review['details'][0]['kondisi_masalah']==='Kondisi lengkap','CR request detail');
    ud_check(pt_uat_review($db,$tokens[2],null,$db)['details'][0]['output_diharapkan']==='Output lengkap','Feature request detail');
    $server=['SCRIPT_NAME'=>'/it-rspi2/portal-tiket/uat.php','HTTP_HOST'=>'127.0.0.1:8104'];
    pt_prepare_signatures($review,$tokens[1],$server);
    foreach(['analyses'=>'analisis','handovers'=>'serah_terima'] as $group=>$kind){
        $s=$review[$group][0]['signature'];ud_check(strlen($s['hash'])===64&&ctype_xdigit($s['hash']),'SHA256 '.$kind);
        ud_check(str_starts_with($s['qr'],'data:image/svg+xml;base64,'),'Local QR SVG '.$kind);
        $svg=base64_decode(substr($s['qr'],26));ud_check(str_contains($svg,'shape-rendering="crispEdges"')&&str_contains($svg,'fill="#fff"'),'QR quiet zone and sharp modules');
        parse_str(parse_url($s['url'],PHP_URL_QUERY),$p);ud_check($p['token']===$tokens[1]&&$p['jenis']===$kind&&$p['hash']===$s['hash'],'QR URL bound to token/type/hash');
        ud_check(pt_verify_signature($review,$kind,$s['id'],$s['hash'])['nama']===$s['nama'],'QR verification matches authoritative name');
        ud_reject(function()use($review,$kind,$s){pt_verify_signature($review,$kind,$s['id'],str_repeat('0',64));},404,'Forged hash rejected');
        ud_reject(function()use($review,$kind,$s){pt_verify_signature($review,$kind,999,$s['hash']);},404,'Wrong record rejected');
    }
    $foreign=pt_uat_review($db,$tokens[2],null,$db);$s=$review['analyses'][0]['signature'];
    ud_reject(function()use($foreign,$s){pt_verify_signature($foreign,'analisis',$s['id'],$s['hash']);},404,'Cross-ticket signature rejected');
    $tampered=$review;$tampered['analyses'][0]['keputusan']='Diubah';
    ud_reject(function()use($tampered,$s){pt_verify_signature($tampered,'analisis',$s['id'],$s['hash']);},404,'Content change invalidates digest');
    foreach([['bad',1,$s['hash']],['analisis',0,$s['hash']],['analisis',1,[]]] as [$kind,$id,$hash])ud_reject(function()use($review,$kind,$id,$hash){pt_verify_signature($review,$kind,$id,$hash);},404,'Malformed verification rejected');
    $db->query('UPDATE tiket_analisis_it SET nama_staf_it=NULL,tgl_analisis=NULL WHERE id_tiket=2');
    $legacy=pt_uat_review($db,$tokens[2],null,$db);ud_check($legacy['analyses'][0]['nama_staf_it']==='Nama Berubah'&&$legacy['analyses'][0]['tgl_analisis']===null,'Legacy name lookup without invented date');
    $db->query("UPDATE tiket_pengajuan SET ttd_pengaju='data:image/svg+xml;base64,PHN2Zz4=' WHERE id_tiket=2");
    ud_check(pt_uat_review($db,$tokens[2],null,$db)['ticket']['ttd_pengaju']===null,'Unsafe signature MIME rejected');
    ud_check(!str_contains(json_encode(ptp_detail($db,1)),'628123456789'),'Anonymous public detail still excludes contact');
    $db->query('RENAME TABLE tiket_detail_request TO details_not_available');
    try{
        ud_reject(function()use($db){pt_uat_review($db,str_repeat('f',64),null,$db);},404,'Unknown token rejected before detail lookup');
        pt_query($db,"UPDATE tiket_pengajuan SET token_expired_at='2000-01-01 00:00:00' WHERE id_tiket=1");
        ud_reject(function()use($db,$tokens){pt_uat_review($db,$tokens[1],null,$db);},410,'Expired token rejected before detail lookup');
    }finally{$db->query('RENAME TABLE details_not_available TO tiket_detail_request');pt_query($db,'UPDATE tiket_pengajuan SET token_expired_at=? WHERE id_tiket=1',[(new DateTimeImmutable())->modify('+1 day')->format('Y-m-d H:i:s')]);}
    if(in_array('--keep-for-http',$argv,true)){
        // Browser/HTTP fallback for the legacy name uses the fixture secondary connection.
        $keep=true;echo json_encode(['schema'=>$schema,'cr'=>1,'feature'=>2]).PHP_EOL;
    }
    echo 'PASS '.$checks.' complete UAT/QR checks.'.PHP_EOL;
}finally{if(!$keep&&preg_match('/^codex_uat_detail_[a-f0-9]{10}$/D',$schema))$db->query("DROP DATABASE `$schema`");}
