<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../config/portal_tiket.php';
require_once __DIR__ . '/portal_test_helpers.php';
$project = dirname(__DIR__);
$root = dirname($project) . '/.codex_tmp/portal-access-' . bin2hex(random_bytes(5));
$schema = 'codex_portal_access_' . bin2hex(random_bytes(5));
foreach (['portal-tiket','main_login','config','storage/login-security','native-sessions'] as $dir) mkdir($root . '/' . $dir,0700,true);
foreach (glob($project . '/portal-tiket/*.php') as $file) copy($file,$root . '/portal-tiket/' . basename($file));
foreach (['login_security.php','captcha.php','form_login.php'] as $file) copy($project . '/main_login/' . $file,$root . '/main_login/' . $file);
mkdir($root.'/library/tcpdf/include/barcodes',0700,true);
foreach(['tcpdf_barcodes_2d.php','include/barcodes/qrcode.php'] as $file)copy($project.'/library/tcpdf/'.$file,$root.'/library/tcpdf/'.$file);
copy($project . '/config/portal_tiket.php',$root . '/config/portal_tiket.php');
copy($project . '/config/telegram.php',$root . '/config/telegram.php');
$db=new mysqli('localhost',getenv('DB_USERNAME')?:'root',getenv('DB_PASSWORD')?:'');
$db->query("CREATE DATABASE `$schema` CHARACTER SET utf8mb4");$db->select_db($schema);$db->set_charset('utf8mb4');
$server=null;$keep=false;$checks=0;
function ac_check($ok,$label){global $checks;if(!$ok)throw new RuntimeException($label);++$checks;}
function ac_request(&$cookie,$path,$fields=null,$headers=[],$method=null){
    global $base;
    $ch=curl_init($base.$path);curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_HEADER=>true,CURLOPT_COOKIE=>$cookie,CURLOPT_TIMEOUT=>8,CURLOPT_HTTPHEADER=>$headers]);
    if($fields!==null)curl_setopt_array($ch,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>http_build_query($fields)]);
    if($method!==null)curl_setopt($ch,CURLOPT_CUSTOMREQUEST,$method);
    if($method==='HEAD')curl_setopt($ch,CURLOPT_NOBODY,true);
    $raw=curl_exec($ch);if($raw===false)throw new RuntimeException('Fixture HTTP unavailable');
    $status=curl_getinfo($ch,CURLINFO_HTTP_CODE);$size=curl_getinfo($ch,CURLINFO_HEADER_SIZE);curl_close($ch);
    $head=substr($raw,0,$size);
    if(preg_match('/Set-Cookie: (portal_tiket=[^;\r\n]+)/i',$head,$m))$cookie=$m[1];
    preg_match('/Location: ([^\r\n]+)/i',$head,$m);
    return ['status'=>$status,'headers'=>$head,'body'=>substr($raw,$size),'location'=>$m[1]??''];
}
function ac_state(&$cookie,$query=''){return json_decode(ac_request($cookie,'test-state.php'.$query)['body'],true);}
function ac_form(&$cookie,$url='verifikasi-akses.php'){
    $page=ac_request($cookie,$url);
    preg_match('/name="captcha_id" value="([a-f0-9]{32})"/',$page['body'],$id);
    preg_match('/name="csrf_token" value="([a-f0-9]{64})"/',$page['body'],$csrf);
    if($page['status']!==200||!isset($id[1],$csrf[1]))throw new RuntimeException('CAPTCHA form unavailable');
    $state=ac_state($cookie);
    return ['csrf_token'=>$csrf[1],'captcha_id'=>$id[1],'captcha'=>$state['pt_access_captchas'][$id[1]]['code']];
}
try{
    cr_sql_file($db,$project.'/Database/2026-10-20_portal_tiket_layanan_it.sql',$schema);
    cr_sql_file($db,$project.'/Database/2026-10-20_portal_tiket_reservasi_nomor.sql',$schema);
    cr_sql_file($db,$project.'/Database/2026-10-21_portal_tiket_identitas_qr.sql',$schema);
    $db->query('CREATE TABLE tb_lokasi(lokasi_id INT PRIMARY KEY,nama_lokasi VARCHAR(150))');
    $db->query("INSERT INTO tb_lokasi VALUES (1,'Unit Uji')");
    $db->query('CREATE TABLE pegawai(id INT PRIMARY KEY,nama VARCHAR(150),stts_aktif VARCHAR(20))');
    $db->query("INSERT INTO pegawai VALUES (1,'Pengaju Uji','AKTIF')");
    $db->query("INSERT INTO master_sistem(nama_sistem,deskripsi) VALUES ('Sistem Uji','Fixture')");
    $stmt=$db->prepare("INSERT INTO tiket_pengajuan(jenis_tiket,id_sistem,kontak_pengaju,unit_pemohon,nama_pengaju,ttd_pengaju,status_tiket,token_uat,token_expired_at) VALUES ('Change Request',1,'6281234567890','Unit Uji','Pengaju Uji',?,'Menunggu UAT',?,'2099-01-01 00:00:00')");
    $signature=cr_image();$token=bin2hex(random_bytes(32));$stmt->bind_param('ss',$signature,$token);$stmt->execute();$ticket=$db->insert_id;
    $db->query("INSERT INTO tiket_detail_request(id_tiket,nama_perubahan_fitur,kondisi_masalah,perubahan_diharapkan,dampak_modul) VALUES ($ticket,'Judul Uji','Masalah Uji','Harapan Uji','Dampak Uji')");
    $db->query("INSERT INTO tiket_analisis_it(id_tiket,id_staf_it,prioritas,analisis_unit_it,keputusan,nama_staf_it,tgl_analisis) VALUES ($ticket,1,'Sedang','Analisis Uji','Keputusan Uji','Analis Uji',NOW())");
    $db->query("INSERT INTO tiket_serah_terima(id_tiket,deskripsi_penyelesaian,id_staf_penyerah,nama_staf_penyerah) VALUES ($ticket,'Penyelesaian Uji',1,'Penyerah Uji')");
    $settings=['PORTAL_DB_HOST'=>'localhost','PORTAL_DB_DATABASE'=>$schema,'PORTAL_PEGAWAI_HOST'=>'localhost','PORTAL_PEGAWAI_DATABASE'=>$schema,
        'PORTAL_SECURITY_PATH'=>$root.'/storage/portal-security','PORTAL_TELEGRAM_ENABLED'=>'0','PORTAL_PUBLIC_URL'=>''];
    $prefix="<?php\n";foreach($settings as $key=>$value)$prefix.='putenv('.var_export($key.'='.$value,true).");\n";
    file_put_contents($root.'/config/portal_tiket.php',$prefix.substr(file_get_contents($project.'/config/portal_tiket.php'),5));
    file_put_contents($root.'/portal-tiket/test-state.php', <<<'PHP'
<?php
require __DIR__.'/access_helpers.php';pt_public_session();
if(isset($_GET['age']))$_SESSION['pt_public_access']['last_activity']=time()-(int)$_GET['age'];
if(isset($_GET['future']))$_SESSION['pt_public_access']['last_activity']=time()+100;
if(isset($_GET['expire']))foreach($_SESSION['pt_access_captchas']??[] as $id=>$value)$_SESSION['pt_access_captchas'][$id]['expires']=time()-1;
if(isset($_GET['reset']))itrspi_login_security_db(pt_access_storage().'/attempts.sqlite')->exec('DELETE FROM login_attempts');
if(isset($_GET['block']))itrspi_login_security_db(pt_access_storage().'/attempts.sqlite')->exec('UPDATE login_attempts SET blocked_until='. (time()-1));
$state=$_SESSION;$state['fixture_gc_lifetime']=(int)ini_get('session.gc_maxlifetime');$state['fixture_session_name']=session_name();$state['fixture_session_path']=session_save_path();
header('Content-Type: application/json');echo json_encode($state);
PHP
    );
    file_put_contents($root.'/router.php','<?php $path=parse_url($_SERVER["REQUEST_URI"],PHP_URL_PATH);'
        .'if(str_starts_with($path,"/storage/")){http_response_code(403);return true;}'
        .'if(str_starts_with($path,"/assets/")){$asset=realpath('.var_export($project,true).'.$path);'
        .'if($asset&&str_starts_with($asset,realpath('.var_export($project.'/assets',true).').DIRECTORY_SEPARATOR)){'
        .'$types=["css"=>"text/css","js"=>"application/javascript","png"=>"image/png","woff2"=>"font/woff2","ttf"=>"font/ttf"];'
        .'header("Content-Type: ".($types[pathinfo($asset,PATHINFO_EXTENSION)]??"application/octet-stream"));readfile($asset);return true;}}return false;');
    $socket=stream_socket_server('tcp://127.0.0.1:0',$errno,$error);$address=stream_socket_get_name($socket,false);fclose($socket);
    $server=proc_open([PHP_BINARY,'-d','session.save_path='.$root.'/native-sessions','-d','sys_temp_dir='.$root,'-d','upload_tmp_dir='.$root,'-S',$address,'-t',$root,$root.'/router.php'],[0=>['pipe','r'],1=>['file',$root.'/http.log','a'],2=>['file',$root.'/http.log','a']],$pipes);
    if(!is_resource($server))throw new RuntimeException('Server unavailable');fclose($pipes[0]);
    for($i=0;$i<40;$i++){if($probe=@stream_socket_client('tcp://'.$address,$errno,$error,.1)){fclose($probe);break;}usleep(50000);}
    $base='http://'.$address.'/portal-tiket/';$cookie='';
    foreach(['index.php','change-request.php','fitur-baru.php','daftar-tiket.php','detail-tiket.php?id='.$ticket,'uat.php?token='.$token,'verifikasi-ttd.php?token='.$token.'&jenis=analisis&id=1&hash=bad'] as $route){
        $page=ac_request($cookie,$route);
        ac_check($page['status']===303&&str_starts_with($page['location'],'verifikasi-akses.php?lanjut='),'Direct route requires CAPTCHA: '.$route);
        ac_check(!str_contains($page['body'],'Judul Uji')&&!str_contains($page['body'],'Form Change Request'),'No private/menu HTML before CAPTCHA');
        if(str_starts_with($route,'uat.php'))$uatNext=$page['location'];
    }
    foreach(['pegawai.php?q=Peng','uat-pegawai.php?token='.$token.'&q=Peng'] as $route){
        $page=ac_request($cookie,$route);$json=json_decode($page['body'],true);
        ac_check($page['status']===403&&$json['captcha_required']===true&&!isset($json['results']),'AJAX cannot bypass CAPTCHA');
    }
    foreach(['change-request.php','fitur-baru.php','uat.php?token='.$token] as $route)ac_check(ac_request($cookie,$route,['csrf'=>'forged'])['status']===303,'Unverified POST blocked before writes');
    ac_check((int)$db->query('SELECT COUNT(*) FROM tiket_pengajuan')->fetch_row()[0]===1 && $db->query("SELECT status_tiket FROM tiket_pengajuan WHERE id_tiket=$ticket")->fetch_row()[0]==='Menunggu UAT','Rejected POSTs preserve database');
    $page=ac_request($cookie,$uatNext);$form=ac_form($cookie,$uatNext);
    ac_check(!str_contains($page['body'],'menu-change-request')&&str_contains($page['body'],'Verifikasi sebelum melanjutkan'),'Gate replaces landing menus');
    ac_check(str_contains($page['headers'],'no-store'),'Gate not cached on existing session');
    $firstCookie='';$fresh=ac_request($firstCookie,'verifikasi-akses.php');
    ac_check(str_contains($fresh['headers'],'HttpOnly')&&str_contains($fresh['headers'],'SameSite=Lax')&&str_contains($fresh['headers'],'path=/portal-tiket/'),'Scoped secure session cookie flags');
    $state=ac_state($cookie);
    ac_check($state['fixture_gc_lifetime']===7200&&$state['fixture_session_name']==='portal_tiket'&&str_contains($state['fixture_session_path'],'portal-security/sessions'),'Public sessions persist two hours independently from IT login cleanup');
    for($i=0;$i<7;++$i)ac_form($firstCookie);
    ac_check(count(ac_state($firstCookie)['pt_access_captchas'])===5,'Refresh/multiple tabs cannot grow challenges beyond five');
    ac_check(strlen($form['captcha'])===5 && preg_match('/^[23456789ABCDEFGHJKLMNPQRSTUVWXYZ]{5}$/D',$form['captcha']),'Login-standard five-character code');
    $challenge=ac_state($cookie)['pt_access_captchas'][$form['captcha_id']];
    ac_check($challenge['expires']-time()>=295&&$challenge['expires']-time()<=300,'CAPTCHA expires in five minutes');
    $image=ac_request($cookie,'captcha.php?id='.$form['captcha_id']);$size=getimagesizefromstring($image['body']);
    ac_check($image['status']===200&&substr($image['body'],0,8)==="\x89PNG\r\n\x1a\n"&&$size[0]===220&&$size[1]===70,'Same PNG dimensions and renderer as IT login');
    ac_check(str_contains($image['headers'],'no-store'),'CAPTCHA images not cached');
    $other='';ac_check(ac_request($other,'captcha.php?id='.$form['captcha_id'])['status']===404,'CAPTCHA image bound to browser session');
    $bad=$form;$bad['csrf_token']='forged';ac_request($cookie,$uatNext,$bad);
    ac_check(!isset(ac_state($cookie)['pt_public_access']),'Forged CSRF cannot grant access');
    $bad=$form;$bad['captcha']='WRONG';ac_request($cookie,$uatNext,$bad);
    $state=ac_state($cookie);ac_check(!isset($state['pt_access_captchas'][$form['captcha_id']]),'Wrong answer consumes challenge');
    $locked=ac_request($cookie,$uatNext);ac_check(str_contains($locked['body'],'id="portal-access-submit" disabled')&&str_contains($locked['body'],'CAPTCHA salah'),'Wrong answer locks button and shows message');
    $nextForm=ac_form($other);$blocked=ac_request($other,'verifikasi-akses.php',$nextForm,['X-Forwarded-For: 203.0.113.78']);
    ac_check($blocked['status']===303&&str_contains($blocked['headers'],'Retry-After:'),'New browser and spoofed forwarded IP cannot bypass delay');
    ac_check(isset(ac_state($other)['pt_access_captchas'][$nextForm['captcha_id']]),'Blocked attempt preserves unused challenge');
    $login=ac_request($cookie,'../main_login/form_login.php');
    ac_check($login['status']===200&&!str_contains($login['body'],'id="loginButton" disabled'),'Portal failure does not lock IT login');
    ac_state($cookie,'?block=1');
    ac_check(ac_request($other,'verifikasi-akses.php',$nextForm)['status']===303 && isset(ac_state($other)['pt_public_access']),'Attempt allowed after server delay expires');
    $oldCookie=$cookie;$form=ac_form($cookie,$uatNext);$form['captcha']=strtolower($form['captcha']);
    $pass=ac_request($cookie,$uatNext,$form);
    ac_check($pass['status']===303&&$pass['location']==='uat.php?token='.$token,'Successful CAPTCHA returns to original magic link');
    ac_check($cookie!==$oldCookie&&!isset(ac_state($cookie)['pt_access_captchas']),'Success rotates session ID and clears challenges');
    $intruder='';$own=ac_form($intruder);$stolen=$form;$stolen['csrf_token']=$own['csrf_token'];ac_request($intruder,'verifikasi-akses.php',$stolen);
    ac_check(!isset(ac_state($intruder)['pt_public_access']),'Solved CAPTCHA cannot be replayed in another browser session');ac_state($cookie,'?reset=1');
    ac_check(ac_request($oldCookie,'index.php')['status']===303,'Old session ID does not inherit access');
    foreach(['index.php','change-request.php','fitur-baru.php','daftar-tiket.php','detail-tiket.php?id='.$ticket,'uat.php?token='.$token] as $route){
        $page=ac_request($cookie,$route);
        if(in_array($route,['change-request.php','fitur-baru.php'],true)){
            ac_check($page['status']===303&&str_contains($page['location'],'?form='),'Verified form reserves normally');$page=ac_request($cookie,$page['location']);
        }
        ac_check($page['status']===200&&!str_contains($page['body'],'id="portal-access-form"'),'One verification covers navigation: '.$route);
    }
    $uat=ac_request($cookie,'uat.php?token='.$token);preg_match('/name="csrf" value="([a-f0-9]+)"/',$uat['body'],$csrf);
    $search=ac_request($cookie,'uat-pegawai.php?token='.$token.'&q=Peng',null,['X-CSRF-Token: '.$csrf[1]]);
    ac_check($search['status']===200&&count(json_decode($search['body'],true)['results'])===1,'Verified UAT employee AJAX still works');
    ac_state($cookie,'?age=7190');$page=ac_request($cookie,'index.php');$last=ac_state($cookie)['pt_public_access']['last_activity'];
    ac_check($page['status']===200&&time()-$last<3,'Activity before two-hour limit renews session');
    ac_state($cookie,'?age=7200');$rejected=ac_request($cookie,'uat.php?token='.$token,['csrf'=>$csrf[1],'status_pengujian_uat'=>'Berhasil','id_pegawai_penerima'=>'1','ttd_penerima'=>$signature]);
    ac_check($rejected['status']===303&&$db->query("SELECT status_tiket FROM tiket_pengajuan WHERE id_tiket=$ticket")->fetch_row()[0]==='Menunggu UAT','Expired POST never changes UAT status');
    ac_state($cookie,'?age=7200');$api=ac_request($cookie,'uat-pegawai.php?token='.$token.'&q=Peng');
    ac_check($api['status']===403&&json_decode($api['body'],true)['captcha_required'],'Two idle hours expire AJAX access');
    $expiry=ac_request($cookie,'uat.php?token='.$token);
    ac_check($expiry['status']===303&&str_starts_with($expiry['location'],'verifikasi-akses.php'),'Expired HTML session requires CAPTCHA again');
    ac_check(!str_contains($expiry['location'],$token),'Bearer token not exposed in intermediary URL');
    $newForm=ac_form($cookie,$expiry['location']);ac_request($cookie,$expiry['location'],$newForm);
    ac_check(ac_request($cookie,'uat.php?token='.$token)['status']===200,'Reverification restores original UAT navigation');
    ac_state($cookie,'?future=1');ac_check(ac_request($cookie,'index.php')['status']===303,'Future timestamp cannot extend access');
    $newForm=ac_form($cookie);ac_state($cookie,'?expire=1');ac_request($cookie,'verifikasi-akses.php',$newForm);
    ac_check(!isset(ac_state($cookie)['pt_public_access']),'Expired CAPTCHA rejected');ac_state($cookie,'?reset=1');
    foreach([['captcha'=>['bad']],['captcha_id'=>['bad']],['csrf_token'=>['bad']]] as $malformed){
        $f=ac_form($cookie);$bad=array_replace($f,$malformed);$r=ac_request($cookie,'verifikasi-akses.php',$bad);
        ac_check($r['status']===303&&!isset(ac_state($cookie)['pt_public_access']),'Malformed arrays rejected without fatal error');ac_state($cookie,'?reset=1');
    }
    foreach(['https://evil.example/','//evil.example/','%0d%0aLocation:evil', ['bad']] as $unsafe){
        $url='verifikasi-akses.php?'.http_build_query(['lanjut'=>$unsafe]);$f=ac_form($cookie,$url);$r=ac_request($cookie,$url,$f);
        ac_check($r['location']==='index.php','Untrusted redirect target discarded');ac_state($cookie,'?age=7200');ac_request($cookie,'index.php');
    }
    $security=$root.'/storage/portal-security';
    ac_check(trim(file_get_contents($security.'/.htaccess'))==='Require all denied','Runtime security storage automatically protected');
    rename($security.'/attempts.sqlite',$security.'/attempts-saved.sqlite');mkdir($security.'/attempts.sqlite');
    $empty='';$unavailable=ac_request($empty,'verifikasi-akses.php');
    ac_check($unavailable['status']===503&&str_contains($unavailable['body'],'id="portal-access-submit" disabled')&&!str_contains($unavailable['body'],'SQLSTATE'),'Unavailable limiter fails closed with disabled submit');
    preg_match('/name="csrf_token" value="([a-f0-9]{64})"/',$unavailable['body'],$unavailableCsrf);
    $unavailable=ac_request($empty,'verifikasi-akses.php',['csrf_token'=>$unavailableCsrf[1],'captcha_id'=>'bad','captcha'=>'WRONG']);
    ac_check($unavailable['status']===503&&!isset(ac_state($empty)['pt_public_access']),'Broken limiter rejects valid-CSRF POST without granting access');
    rmdir($security.'/attempts.sqlite');rename($security.'/attempts-saved.sqlite',$security.'/attempts.sqlite');
    rename($security,$security.'-saved');file_put_contents($security,'not a directory');
    $r=ac_request($cookie,'index.php');ac_check($r['status']===503&&!str_contains($r['body'],$root)&&!str_contains($r['body'],'Form Change Request'),'Unavailable session storage fails closed without menus/paths');
    unlink($security);rename($security.'-saved',$security);
    $plain='';$r=ac_request($plain,'verifikasi-akses.php',null,[],'HEAD');ac_check($r['status']===200&&$r['body']==='','HEAD CAPTCHA page has no body');
    $log=file_get_contents($root.'/http.log');ac_check(!preg_match('/PHP (Warning|Fatal error|Parse error)/',$log),'HTTP regression has no PHP warnings/fatals');
    echo 'PASS '.$checks.' isolated public CAPTCHA checks.'.PHP_EOL;
    if(in_array('--keep-for-browser',$argv,true)){$keep=true;echo json_encode(['root'=>$root,'schema'=>$schema,'token'=>$token,'ticket'=>$ticket]).PHP_EOL;}
}finally{
    if(is_resource($server)){proc_terminate($server);proc_close($server);}
    if(!$keep){
        if(preg_match('/^codex_portal_access_[a-f0-9]{10}$/D',$schema))$db->query("DROP DATABASE `$schema`");
        $remove=function($path)use(&$remove,$root){if($path!==$root&&!str_starts_with($path,$root.'/')&&!str_starts_with($path,$root.DIRECTORY_SEPARATOR))throw new RuntimeException('Unsafe fixture cleanup');foreach(new FilesystemIterator($path)as$item){if($item->isDir())$remove($item->getPathname());else unlink($item->getPathname());}rmdir($path);};$remove($root);
    }
}
