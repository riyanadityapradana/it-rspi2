<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require_once __DIR__.'/../staff/unit/portal-tiket/uat_helpers.php';
require_once __DIR__.'/../portal-tiket/public_helpers.php';
require_once __DIR__.'/portal_test_helpers.php';
mysqli_report(MYSQLI_REPORT_ERROR|MYSQLI_REPORT_STRICT);
$checks=0;$keep=false;$originalUrl=getenv('PORTAL_PUBLIC_URL');putenv('PORTAL_PUBLIC_URL');
function ul_check($v,$label){global $checks;if(!$v)throw new RuntimeException($label);++$checks;}
function ul_reject($fn,$label,$code=null){try{$fn();}catch(DomainException $e){ul_check($code===null||$e->getCode()===$code,$label);return;}throw new RuntimeException('Not rejected: '.$label);}
foreach([
    ['2026-01-31 10:11:12','2026-02-28 10:11:12'],['2028-01-31 10:11:12','2028-02-29 10:11:12'],
    ['2026-03-31 10:11:12','2026-04-30 10:11:12'],['2026-12-31 10:11:12','2027-01-31 10:11:12'],
    ['2028-02-29 10:11:12','2028-03-29 10:11:12'],['2026-10-21 10:11:12','2026-11-21 10:11:12']
] as [$from,$to])ul_check(pt_uat_expiry(new DateTimeImmutable($from))->format('Y-m-d H:i:s')===$to,'Calendar month expiry '.$from);
foreach([null,'','0000-00-00 00:00:00','2026-02-31 10:00:00','invalid',[]] as $bad)ul_check(pt_uat_deadline($bad)===null,'Invalid deadline');
$server=['SCRIPT_NAME'=>'/it-rspi2/staff/dashboard_staff.php','HTTP_HOST'=>'192.168.1.108'];
ul_check(pti_uat_base_url($server)==='http://192.168.1.108/it-rspi2/portal-tiket','Intranet origin');
ul_check(pti_uat_base_url(array_replace($server,['HTTPS'=>'on','HTTP_HOST'=>'it.hospital.test:8443','SCRIPT_NAME'=>'/it-rspi2/admin/dashboard_admin.php']))==='https://it.hospital.test:8443/it-rspi2/portal-tiket','HTTPS/admin/port');
ul_check(pti_uat_base_url(array_replace($server,['SCRIPT_NAME'=>'/staff/dashboard_staff.php','HTTP_HOST'=>'[::1]:8103']))==='http://[::1]:8103/portal-tiket','Root install and IPv6');
foreach(['evil.test/path','evil.test@safe.test',"evil.test\r\nX:bad",'evil.test?x=1','evil.test#x','evil.test:99999',''] as $host)ul_reject(function()use($server,$host){pti_uat_base_url(array_replace($server,['HTTP_HOST'=>$host]));},'Invalid host rejected');
putenv('PORTAL_PUBLIC_URL=https://portal.hospital.test/services/portal-tiket/');ul_check(pti_uat_base_url($server)==='https://portal.hospital.test/services/portal-tiket','Canonical URL override');
foreach(['javascript:alert(1)','https://user:pass@portal.test/portal-tiket','https://portal.test/path?token=x','https://portal.test/path#frag'] as $bad){putenv('PORTAL_PUBLIC_URL='.$bad);ul_reject(function()use($server){pti_uat_base_url($server);},'Invalid canonical URL');}putenv('PORTAL_PUBLIC_URL');
$schema='codex_portal_uat_'.bin2hex(random_bytes(5));$db=new mysqli('localhost',getenv('DB_USERNAME')?:'root',getenv('DB_PASSWORD')?:'');
$db->query("CREATE DATABASE `$schema` CHARACTER SET utf8mb4");$db->select_db($schema);$db->set_charset('utf8mb4');
try{
    cr_sql_file($db,__DIR__.'/../Database/2026-10-20_portal_tiket_layanan_it.sql',$schema);
    $db->query('CREATE TABLE tb_user(id_user INT PRIMARY KEY,nama_lengkap VARCHAR(100),role VARCHAR(30),status VARCHAR(20))');
    $db->query("INSERT INTO tb_user VALUES (1,'Staff Uji','Staff','aktif'),(2,'Kepala Uji','Kepala Ruangan','aktif'),(3,'Akun Nonaktif','Staff','nonaktif')");
    $db->query("INSERT INTO master_sistem(nama_sistem,deskripsi,is_active) VALUES ('Sistem Uji','Uji magic link',1)");
    $now=new DateTimeImmutable('2026-10-21 10:00:00');$tokens=[];
    foreach(['active','expired','boundary','done','missing','other'] as $index=>$kind){
        $token=in_array($kind,['missing'],true)?null:bin2hex(random_bytes(32));$tokens[$kind]=$token;
        $date=$kind==='expired'?'2026-10-20 10:00:00':($kind==='boundary'?'2026-10-21 10:00:00':($kind==='missing'?null:'2026-11-21 10:00:00'));
        $status=$kind==='done'?'Selesai':($kind==='other'?'Dikerjakan':'Menunggu UAT');
        pt_query($db,"INSERT INTO tiket_pengajuan(jenis_tiket,id_sistem,kontak_pengaju,unit_pemohon,nama_pengaju,ttd_pengaju,status_tiket,token_uat,token_expired_at) VALUES ('Change Request',1,'PRIVATE_CONTACT','PRIVATE_UNIT','PRIVATE_REQUESTER','PRIVATE_SIGNATURE',?,?,?)",[$status,$token,$date]);
        $id=$db->insert_id;pt_query($db,"INSERT INTO tiket_serah_terima(id_tiket,deskripsi_penyelesaian,id_staf_penyerah,nama_staf_penyerah) VALUES (?,'Hasil penyelesaian <script>window.uatXss=1</script>',101,'Petugas Uji')",[$id]);
    }
    $active=pti_uat_link_state($db,1,$server,$now);ul_check($active['active']&&!$active['expired'],'Active token enables copy');
    ul_check($active['url']==='http://192.168.1.108/it-rspi2/portal-tiket/uat.php?token='.$tokens['active'],'Full magic link URL');
    foreach([2,3] as $id){$s=pti_uat_link_state($db,$id,$server,$now);ul_check(!$s['active']&&$s['expired'],'Expired/boundary disables copy');}
    ul_check(!pti_uat_link_state($db,5,$server,$now)['active']&&pti_uat_link_state($db,5,$server,$now)['url']==='','Missing token disables copy');
    ul_check(pti_uat_link_state($db,6,$server,$now)['url']==='','Other status does not expose token');
    $review=pt_uat_review($db,$tokens['active'],$now);ul_check($review['ticket']['id_tiket']===1&&$review['handover']!==null,'Valid token review');
    ul_check($review['ticket']['nama_pengaju']==='PRIVATE_REQUESTER'&&!str_contains(json_encode($review),$tokens['active']),'Authorized review includes applicant but omits bearer token field');
    foreach(['expired','boundary','done','other'] as $kind)ul_reject(function()use($db,$tokens,$kind,$now){pt_uat_review($db,$tokens[$kind],$now);},'Expired/status-invalid token rejected',410);
    foreach(['',[],"' OR 1=1 --",str_repeat('f',64)] as $token)ul_reject(function()use($db,$token,$now){pt_uat_review($db,$token,$now);},'Invalid/unknown token',404);
    ul_check(!str_contains(json_encode(ptp_detail($db,1)),$tokens['active']),'Public detail excludes token');
    $rotated=bin2hex(random_bytes(32));pt_query($db,'UPDATE tiket_pengajuan SET token_uat=? WHERE id_tiket=1',[$rotated]);
    ul_reject(function()use($db,$tokens,$now){pt_uat_review($db,$tokens['active'],$now);},'Rotated token invalid',404);pt_query($db,'UPDATE tiket_pengajuan SET token_uat=? WHERE id_tiket=1',[$tokens['active']]);
    if(in_array('--keep-for-http',$argv,true)){
        // Timestamps relative to execution time for HTTP/browser fixtures.
        pt_query($db,'UPDATE tiket_pengajuan SET token_expired_at=? WHERE id_tiket IN (1,4,6)',[(new DateTimeImmutable('now'))->modify('+1 day')->format('Y-m-d H:i:s')]);
        pt_query($db,'UPDATE tiket_pengajuan SET token_expired_at=? WHERE id_tiket IN (2,3)',[(new DateTimeImmutable('now'))->modify('-1 day')->format('Y-m-d H:i:s')]);
        $folder=__DIR__.'/../../.codex_tmp/portal-uat-preview/sessions';if(!is_dir($folder))mkdir($folder,0700,true);session_save_path($folder);$sessions=[];
        foreach(['staff'=>[1,'Staff'],'head'=>[2,'Kepala Ruangan'],'inactive'=>[3,'Staff']] as $label=>$account){session_id('puat'.bin2hex(random_bytes(16)));session_start();$_SESSION=['id_user'=>$account[0],'role'=>$account[1],'foto'=>'','nip'=>'fixture'];$sessions[$label]=session_id();session_write_close();}
        $keep=true;echo json_encode(['schema'=>$schema,'sessions'=>$sessions]).PHP_EOL;
    }
    echo 'PASS '.$checks.' UAT-link backend checks.'.PHP_EOL;
}finally{
    if(!$keep&&preg_match('/^codex_portal_uat_[a-f0-9]{10}$/D',$schema))$db->query("DROP DATABASE `$schema`");
    $originalUrl===false?putenv('PORTAL_PUBLIC_URL'):putenv('PORTAL_PUBLIC_URL='.$originalUrl);
}
