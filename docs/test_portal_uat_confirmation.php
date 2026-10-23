<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__.'/../portal-tiket/uat_helpers.php';
require_once __DIR__.'/../portal-tiket/signature_helpers.php';
require_once __DIR__.'/../portal-tiket/public_helpers.php';
require_once __DIR__.'/portal_test_helpers.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
if (($argv[1] ?? '') === '--worker') {
    $schema = $argv[2] ?? '';
    if (!preg_match('/^codex_uat_confirm_[a-f0-9]{10}$/D', $schema)) exit(2);
    $db = new mysqli('localhost', getenv('DB_USERNAME') ?: 'root', getenv('DB_PASSWORD') ?: '', $schema);
    try {
        pt_uat_confirm($db, $argv[3], 'POST', ['csrf'=>str_repeat('a',64),'status_pengujian_uat'=>'Berhasil','id_pegawai_penerima'=>'2','ttd_penerima'=>cr_image()], ['pt_csrf'=>str_repeat('a',64)], null, $db);
        echo 'saved';
    } catch (DomainException $e) { if ($e->getCode() !== 409) throw $e; echo 'conflict'; }
    exit;
}
$schema = 'codex_uat_confirm_'.bin2hex(random_bytes(5));
$db = new mysqli('localhost', getenv('DB_USERNAME') ?: 'root', getenv('DB_PASSWORD') ?: '');
$db->query("CREATE DATABASE `$schema` CHARACTER SET utf8mb4"); $db->select_db($schema); $db->set_charset('utf8mb4');
$checks=0; $keep=false; $server=null; $cookieFile=null;
function uc_check($v,$label) { global $checks; if (!$v) throw new RuntimeException($label); $checks++; }
function uc_reject($fn,$label,$code=null) { try { $fn(); } catch (DomainException $e) { uc_check($code===null || $e->getCode()===$code,$label); return; } throw new RuntimeException('Not rejected: '.$label); }
function uc_ticket($type='Change Request',$status='Menunggu UAT',$hasHandover=true) {
    global $db;
    $token=bin2hex(random_bytes(32));
    pt_query($db,'INSERT INTO tiket_pengajuan(jenis_tiket,id_sistem,kontak_pengaju,unit_pemohon,nama_pengaju,ttd_pengaju,status_tiket,token_uat,token_expired_at) VALUES (?,1,?,?,?,?,?,?,?)',
        [$type,'628123456789','Unit Uji','Pengaju Awal',cr_image(),$status,$token,(new DateTimeImmutable())->modify('+1 month')->format('Y-m-d H:i:s')]);
    $id=(int)$db->insert_id;
    pt_query($db,'INSERT INTO tiket_detail_request(id_tiket,nama_perubahan_fitur,kondisi_masalah,perubahan_diharapkan,dampak_modul,latar_belakang,deskripsi_alur_kerja,target_pengguna,output_diharapkan) VALUES (?,?,?,?,?,?,?,?,?)',
        [$id,'Permintaan '.$type,'Masalah saat ini','Perubahan diharapkan','Dampak modul','Latar belakang','Alur kerja baru','Unit pengguna','Output baru']);
    pt_query($db,"INSERT INTO tiket_analisis_it(id_tiket,id_staf_it,nama_staf_it,tgl_analisis,prioritas,analisis_unit_it,keputusan) VALUES (?,101,'Analis Uji',NOW(),'Sedang','Analisis lengkap','Disetujui')",[$id]);
    if ($hasHandover) pt_query($db,"INSERT INTO tiket_serah_terima(id_tiket,deskripsi_penyelesaian,id_staf_penyerah,nama_staf_penyerah) VALUES (?,'Penyelesaian lengkap',101,'Penyerah Uji')",[$id]);
    return ['id'=>$id,'token'=>$token];
}
try {
    cr_sql_file($db,__DIR__.'/../Database/2026-10-20_portal_tiket_layanan_it.sql',$schema);
    $db->query('CREATE TABLE pegawai(id INT PRIMARY KEY,nama VARCHAR(150),stts_aktif VARCHAR(20))');
    $db->query("INSERT INTO pegawai VALUES (1,'Pengaju Awal','AKTIF'),(2,'Penerima Pengganti','AKTIF'),(3,'Pegawai Nonaktif','NONAKTIF'),(4,'Penerima <script>window.uatXss=1</script>','AKTIF'),(5,'Nama Ganda','AKTIF'),(6,'Nama Ganda','AKTIF')");
    $db->query("INSERT INTO master_sistem(nama_sistem,deskripsi,is_active) VALUES ('Sistem Uji','Uji penerimaan UAT',1)");
    $csrf=str_repeat('a',64); $session=['pt_csrf'=>$csrf];
    $post=['csrf'=>$csrf,'status_pengujian_uat'=>'Berhasil','id_pegawai_penerima'=>'2','nama_penerima'=>'Nama bebas palsu','ttd_penerima'=>cr_image()];
    $ticket=uc_ticket();
    $save=function($input,$token=null,$method='POST',$auth=null,$now=null)use($db,$ticket,$session){pt_uat_confirm($db,$token??$ticket['token'],$method,$input,$auth??$session,$now,$db);};
    uc_check(pt_uat_default_receiver('Pengaju Awal',$db)['id']===1,'Applicant automatically maps to active master employee');
    foreach(['Nama Ganda','Tidak Terdaftar','Pegawai Nonaktif'] as $name)uc_check(pt_uat_default_receiver($name,$db)===null,'No guessed default for ambiguous/missing/inactive employee');
    foreach (['GET','PUT','DELETE'] as $method) uc_reject(function()use($save,$post,$method){$save($post,null,$method);},'POST only',405);
    foreach (['',[],"' OR 1=1",str_repeat('f',64)] as $token) uc_reject(function()use($save,$post,$token){$save($post,$token);},'Invalid or unknown token',404);
    foreach (['',[],str_repeat('b',64)] as $csrfBad) uc_reject(function()use($save,$post,$csrfBad){$save(array_replace($post,['csrf'=>$csrfBad]));},'CSRF rejected',403);
    uc_reject(function()use($save,$post){$save($post,null,'POST',[]);},'Missing session rejected',403);
    foreach ([['status_pengujian_uat',''],['status_pengujian_uat','Ditolak'],['status_pengujian_uat',[]],['id_pegawai_penerima',' '],['id_pegawai_penerima',[]],['id_pegawai_penerima','2 OR 1=1'],['id_pegawai_penerima','0'],['id_pegawai_penerima','3'],['id_pegawai_penerima','999'],['id_pegawai_penerima','Penerima Pengganti'],['catatan_pengujian',[]],['catatan_pengujian',str_repeat('a',10001)],['ttd_penerima',''],['ttd_penerima',[]],['ttd_penerima',cr_image(true)],['ttd_penerima','data:image/svg+xml;base64,PHN2Zz4=']] as [$field,$bad]) {
        uc_reject(function()use($save,$post,$field,$bad){$save(array_replace($post,[$field=>$bad]));},'Invalid '.$field);
    }
    uc_reject(function()use($save,$post){$save(array_replace($post,['status_pengujian_uat'=>'Diterima dengan catatan','catatan_pengujian'=>' ']));},'Notes required',422);
    foreach (['status_pengujian_uat','id_pegawai_penerima','ttd_penerima'] as $field) { $input=$post;unset($input[$field]); uc_reject(function()use($save,$input){$save($input);},'Required '.$field); }
    uc_check(pt_rows($db,'SELECT status_tiket FROM tiket_pengajuan WHERE id_tiket=?',[$ticket['id']])[0]['status_tiket']==='Menunggu UAT','Invalid payload never changes ticket');
    uc_check(pt_rows($db,'SELECT nama_penerima FROM tiket_serah_terima WHERE id_tiket=?',[$ticket['id']])[0]['nama_penerima']===null,'Invalid payload never changes handover');
    foreach (['Dikerjakan','Selesai','Ditolak','Menunggu Analisis'] as $status) { $other=uc_ticket('Change Request',$status); uc_reject(function()use($save,$post,$other){$save($post,$other['token']);},'Other status '.$status,409); }
    $missing=uc_ticket('Change Request','Menunggu UAT',false); uc_reject(function()use($save,$post,$missing){$save($post,$missing['token']);},'Missing handover',409);
    $db->query('RENAME TABLE pegawai TO pegawai_unavailable');
    try {
        uc_reject(function()use($save,$post){$save($post);},'Master outage prevents acceptance',503);
        uc_check(pt_rows($db,'SELECT status_tiket FROM tiket_pengajuan WHERE id_tiket=?',[$ticket['id']])[0]['status_tiket']==='Menunggu UAT','Master outage keeps waiting status');
    } finally {$db->query('RENAME TABLE pegawai_unavailable TO pegawai');}
    $expired=uc_ticket(); pt_query($db,"UPDATE tiket_pengajuan SET token_expired_at='2026-01-01 12:00:00' WHERE id_tiket=?",[$expired['id']]);
    foreach (['2026-01-01 12:00:00','2026-01-01 12:00:01'] as $now) uc_reject(function()use($save,$post,$expired,$now){$save($post,$expired['token'],'POST',null,new DateTimeImmutable($now));},'Expiry boundary rejected',410);
    $db->query("CREATE TRIGGER uc_fail_ticket BEFORE UPDATE ON tiket_pengajuan FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Forced ticket failure'");
    try { $save($post); throw new RuntimeException('Failure not raised'); } catch (mysqli_sql_exception $e) {
        uc_check(pt_rows($db,'SELECT status_pengujian_uat,nama_penerima,ttd_penerima FROM tiket_serah_terima WHERE id_tiket=?',[$ticket['id']])[0]===['status_pengujian_uat'=>null,'nama_penerima'=>null,'ttd_penerima'=>null],'Ticket update failure rolls back receiver data');
        uc_check(pt_rows($db,'SELECT status_tiket FROM tiket_pengajuan WHERE id_tiket=?',[$ticket['id']])[0]['status_tiket']==='Menunggu UAT','Ticket remains waiting on failure');
    } finally { $db->query('DROP TRIGGER uc_fail_ticket'); }
    $db->query("CREATE TRIGGER uc_fail_handover BEFORE UPDATE ON tiket_serah_terima FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Forced handover failure'");
    try { $save($post); throw new RuntimeException('Failure not raised'); } catch (mysqli_sql_exception $e) {
        uc_check(pt_rows($db,'SELECT status_tiket FROM tiket_pengajuan WHERE id_tiket=?',[$ticket['id']])[0]['status_tiket']==='Menunggu UAT','Handover failure leaves ticket waiting');
    } finally { $db->query('DROP TRIGGER uc_fail_handover'); }
    $before=pt_rows($db,'SELECT token_uat,token_expired_at FROM tiket_pengajuan WHERE id_tiket=?',[$ticket['id']])[0];
    $save($post+['id_tiket'=>$missing['id'],'id_staf_penyerah'=>'999','tgl_serah_terima'=>'2000-01-01','status_tiket'=>'Ditolak','token_expired_at'=>'2099-01-01']);
    $review=pt_uat_review($db,$ticket['token']);
    uc_check($review['completed'] && $review['ticket']['status_tiket']==='Selesai','Successful UAT completes ticket and allows read-only review');
    uc_check($review['handover']['nama_penerima']==='Penerima Pengganti' && $review['handover']['status_pengujian_uat']==='Berhasil','Canonical master name saved; forged free name ignored');
    uc_check($review['handover']['catatan_pengujian']===null && str_starts_with($review['handover']['ttd_penerima'],'data:image/png;base64,'),'Optional notes and safe receiver signature');
    uc_check(pt_rows($db,'SELECT token_uat,token_expired_at FROM tiket_pengajuan WHERE id_tiket=?',[$ticket['id']])[0]===$before,'Original token and expiry retained for read-only receipt');
    uc_check($review['handover']['id_staf_penyerah']===101 && !str_starts_with($review['handover']['tgl_serah_terima'],'2000'),'Client cannot change IT identity or date');
    uc_reject(function()use($save,$post){$save(array_replace($post,['nama_penerima'=>'Overwritten']));},'Replay rejected',409);
    uc_check(pt_uat_review($db,$ticket['token'])['handover']['nama_penerima']==='Penerima Pengganti','Replay never overwrites receipt');
    uc_reject(function()use($db,$ticket,$before){pt_uat_review($db,$ticket['token'],new DateTimeImmutable($before['token_expired_at']));},'Completed receipt still expires',410);
    uc_check(!array_key_exists('ttd_penerima',ptp_detail($db,$ticket['id'])['handovers'][0]),'Anonymous public detail excludes receiver signature');
    $feature=uc_ticket('Fitur Baru'); $save(array_replace($post,['status_pengujian_uat'=>'Diterima dengan catatan','catatan_pengujian'=>' Catatan <script>window.uatXss=1</script> ']),$feature['token']);
    uc_check(pt_uat_review($db,$feature['token'])['handover']['catatan_pengujian']==='Catatan <script>window.uatXss=1</script>','Feature ticket saves notes');
    $history=uc_ticket();pt_query($db,"INSERT INTO tiket_serah_terima(id_tiket,deskripsi_penyelesaian,id_staf_penyerah,nama_staf_penyerah) VALUES (?,'Penyelesaian kedua',101,'Penyerah Uji')",[$history['id']]);
    $save($post,$history['token']);$rows=pt_rows($db,'SELECT status_pengujian_uat FROM tiket_serah_terima WHERE id_tiket=? ORDER BY id',[$history['id']]);
    uc_check($rows[0]['status_pengujian_uat']===null && $rows[1]['status_pengujian_uat']==='Berhasil','Only latest handover updated');
    $concurrent=uc_ticket();$db->begin_transaction();pt_rows($db,'SELECT id_tiket FROM tiket_pengajuan WHERE id_tiket=? FOR UPDATE',[$concurrent['id']]);$workers=[];
    try {
        for($i=0;$i<2;$i++) { $proc=proc_open([PHP_BINARY,__FILE__,'--worker',$schema,$concurrent['token']],[0=>['pipe','r'],1=>['pipe','w'],2=>['pipe','w']],$pipes); if(!is_resource($proc))throw new RuntimeException('Worker unavailable');fclose($pipes[0]);$workers[]=[$proc,$pipes]; }
        usleep(250000);$db->commit();$results=[];
        foreach($workers as [$proc,$pipes]) {$out=stream_get_contents($pipes[1]);$err=stream_get_contents($pipes[2]);fclose($pipes[1]);fclose($pipes[2]);if(proc_close($proc)!==0)throw new RuntimeException($err);$results[]=trim($out);} $workers=[];sort($results);
        uc_check($results===['conflict','saved'],'Concurrent acceptance has exactly one winner');
        uc_check(pt_uat_review($db,$concurrent['token'])['completed'],'Concurrent confirmation is coherent');
    } finally { $db->rollback(); foreach($workers as [$proc,$pipes]){proc_terminate($proc);foreach($pipes as $pipe)if(is_resource($pipe))fclose($pipe);proc_close($proc);} }
    if (in_array('--http',$argv,true)) {
        $preview=__DIR__.'/../../.codex_tmp/uat-confirm-preview';if(!is_dir($preview.'/sessions'))mkdir($preview.'/sessions',0700,true);
        $cookieFile=$preview.'/cookies-'.bin2hex(random_bytes(5)).'.txt';
        $env=getenv();$env['PORTAL_DB_HOST']='localhost';$env['PORTAL_DB_DATABASE']=$schema;$env['PORTAL_PEGAWAI_HOST']='localhost';$env['PORTAL_PEGAWAI_DATABASE']=$schema;$env['PORTAL_PUBLIC_URL']='';
        $env['PORTAL_SECURITY_PATH']=$preview.'/security';
        $server=proc_open([PHP_BINARY,'-d','session.save_path='.$preview.'/sessions','-d','sys_temp_dir='.$preview,'-d','upload_tmp_dir='.$preview,'-S','127.0.0.1:8106','-t',dirname(__DIR__,2)],[0=>['pipe','r'],1=>['file',$preview.'/http.log','a'],2=>['file',$preview.'/http.log','a']],$pipes,null,$env);
        if(!is_resource($server))throw new RuntimeException('HTTP server unavailable');fclose($pipes[0]);
        for($i=0;$i<40;$i++){if($sock=@fsockopen('127.0.0.1',8106,$errno,$errstr,.1)){fclose($sock);break;}usleep(50000);}
        $publicCookie=cr_public_access_cookie('http://127.0.0.1:8106/it-rspi2/portal-tiket/', $preview.'/security');
        $request=function($target,$method='GET',$input=null,$headers=[])use($cookieFile,$publicCookie){
            $ch=curl_init('http://127.0.0.1:8106/it-rspi2/portal-tiket/'.$target);
            curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_HEADER=>true,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_COOKIEJAR=>$cookieFile,CURLOPT_COOKIEFILE=>$cookieFile,CURLOPT_TIMEOUT=>10,CURLOPT_CUSTOMREQUEST=>$method]);
            curl_setopt($ch,CURLOPT_COOKIE,$publicCookie);
            if($method==='HEAD')curl_setopt($ch,CURLOPT_NOBODY,true);
            if($input!==null)curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($input));
            if($headers)curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
            $raw=curl_exec($ch);if($raw===false)throw new RuntimeException(curl_error($ch));$n=curl_getinfo($ch,CURLINFO_HEADER_SIZE);$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);curl_close($ch);
            return ['code'=>$code,'headers'=>substr($raw,0,$n),'body'=>substr($raw,$n)];
        };
        $httpStart=$checks;
        foreach(['Change Request','Fitur Baru'] as $type){
            $pending=uc_ticket($type);$url='uat.php?token='.$pending['token'];$get=$request($url);
            uc_check($get['code']===200 && str_contains($get['body'],'id="open-uat-verification"'),'Pending guest form HTTP200');
            uc_check(str_contains($get['body'],'value="1" selected>Pengaju Awal</option>') && !str_contains($get['body'],'name="nama_penerima"'),'Default receiver selected from master, no free text');
            uc_check(!str_contains($get['body'],'SHA-256:') && !str_contains($get['body'],'Verifikasi QR Tim IT'),'Hashes and verification links removed from UAT');
            uc_check(str_contains($get['headers'],'Referrer-Policy: no-referrer') && str_contains($get['headers'],'Cache-Control: no-store'),'Bearer response privacy headers');
            preg_match('/name="csrf" value="([a-f0-9]+)"/',$get['body'],$m);uc_check(strlen($m[1]??'')===64,'CSRF rendered for guest');
            $search='uat-pegawai.php?'.http_build_query(['token'=>$pending['token'],'q'=>'Peng']);
            uc_check($request($search)['code']===403,'Employee AJAX requires CSRF');
            $found=$request($search,'GET',null,['X-CSRF-Token: '.$m[1]]);$result=json_decode($found['body'],true);
            uc_check($found['code']===200 && in_array('1',array_column($result['results'],'id'),true) && in_array('2',array_column($result['results'],'id'),true),'Employee AJAX reads live secondary master');
            $short=$request('uat-pegawai.php?'.http_build_query(['token'=>$pending['token'],'q'=>'Pe']),'GET',null,['X-CSRF-Token: '.$m[1]]);
            uc_check($short['code']===200 && json_decode($short['body'],true)['results']===[],'Employee AJAX minimum three letters');
            $inactive=$request('uat-pegawai.php?'.http_build_query(['token'=>$pending['token'],'q'=>'Nonaktif']),'GET',null,['X-CSRF-Token: '.$m[1]]);
            uc_check(json_decode($inactive['body'],true)['results']===[],'Inactive employees excluded from AJAX');
            $db->query('RENAME TABLE pegawai TO pegawai_unavailable');
            try {
                uc_check($request('uat-pegawai.php?'.http_build_query(['token'=>$pending['token'],'q'=>'Pe']),'GET',null,['X-CSRF-Token: '.$m[1]])['code']===200,'Short query never touches secondary database');
                $outage=$request($search,'GET',null,['X-CSRF-Token: '.$m[1]]);
                uc_check($outage['code']===503 && !str_contains($outage['body'],'pegawai_unavailable') && !str_contains($outage['body'],$schema),'Secondary outage returns safe AJAX error');
            } finally { $db->query('RENAME TABLE pegawai_unavailable TO pegawai'); }
            $input=array_replace($post,['csrf'=>$m[1],'id_pegawai_penerima'=>'4','status_pengujian_uat'=>$type==='Fitur Baru'?'Diterima dengan catatan':'Berhasil','catatan_pengujian'=>$type==='Fitur Baru'?'Catatan <script>window.uatXss=1</script>':'']);
            uc_check($request($url,'POST',array_replace($input,['csrf'=>'bad']))['code']===403,'HTTP CSRF rejected');
            $invalid=$request($url,'POST',array_replace($input,['ttd_penerima'=>'']));uc_check($invalid['code']===422 && str_contains($invalid['body'],'data-open-on-error="true"'),'HTTP validation reopens modal');
            uc_check(str_contains($invalid['body'],'Penerima &lt;script&gt;') && !str_contains($invalid['body'],'<script>window.uatXss=1</script>'),'Failed input escaped and retained');
            $saved=$request($url,'POST',$input);uc_check($saved['code']===303 && str_contains($saved['headers'],'Location: '.$url),'Success redirects to same receipt');
            $completed=$request($url);uc_check($completed['code']===200 && str_contains($completed['body'],'Hasil Serah Terima Pemohon') && str_contains($completed['body'],'portal-status-done'),'Reload shows completed receipt');
            uc_check(!str_contains($completed['body'],'data-uat-open') && !str_contains($completed['body'],'id="uat-verification"'),'Completed receipt hides form and action');
            uc_check(str_contains($completed['body'],'alt="Tanda tangan penerima"') && str_contains($completed['body'],'Penerima &lt;script&gt;') && !str_contains($completed['body'],'<script>window.uatXss=1</script>'),'Receiver signature and escaped name rendered');
            if($type==='Fitur Baru')uc_check(str_contains($completed['body'],'Catatan &lt;script&gt;'),'Escaped testing notes rendered');
            uc_check($request($url,'POST',array_replace($input,['nama_penerima'=>'Replay']))['code']===409,'HTTP replay rejected');
            uc_check(pt_uat_review($db,$pending['token'])['handover']['nama_penerima']==='Penerima <script>window.uatXss=1</script>','HTTP replay preserves canonical receiver name');
            uc_check($request($search,'GET',null,['X-CSRF-Token: '.$m[1]])['code']===410,'Completed token cannot search employees');
            $protected=pt_uat_review($db,$pending['token']);$sig=pt_it_signature($protected['ticket'],'analisis',$protected['analyses'][0]);
            uc_check($request('verifikasi-ttd.php?'.http_build_query(['token'=>$pending['token'],'jenis'=>'analisis','id'=>$sig['id'],'hash'=>$sig['hash']]))['code']===200,'QR verification still works on completed ticket');
            pt_query($db,"UPDATE tiket_pengajuan SET token_expired_at='2000-01-01 00:00:00' WHERE id_tiket=?",[$pending['id']]);
            $gone=$request($url);uc_check($gone['code']===410 && !str_contains($gone['body'],'Penerima &lt;script&gt;') && !str_contains($gone['body'],'id="uat-request"'),'Completed receipt expiry hides private content');
        }
        $unknown=$request('uat.php?token='.str_repeat('f',64));uc_check($unknown['code']===404 && !str_contains($unknown['body'],'id="uat-request"'),'Unknown token no private data');
        uc_check($request('uat-pegawai.php?token='.str_repeat('f',64).'&q=Peng')['code']===404,'Unknown token cannot search employees');
        uc_check($request('uat-pegawai.php?token='.$expired['token'].'&q=Peng')['code']===410,'Expired token cannot search employees');
        $pending=uc_ticket();$url='uat.php?token='.$pending['token'];$head=$request($url,'HEAD');uc_check($head['code']===200 && $head['body']==='','HEAD supported');
        uc_check($request($url,'PUT')['code']===405,'Unsupported HTTP method rejected');
        uc_check($request('../assets/dist/js/portal-uat-verification.js')['code']===200,'Verification script asset reachable');
        echo 'HTTP checks: '.($checks-$httpStart).PHP_EOL;
    }
    if (in_array('--keep-for-http',$argv,true)) {
        $keep=true; echo json_encode(['schema'=>$schema,'pending'=>uc_ticket(),'feature'=>uc_ticket('Fitur Baru'),'completed'=>$ticket,'expired'=>$expired]).PHP_EOL;
    }
    echo 'PASS '.$checks.' UAT confirmation checks.'.PHP_EOL;
} finally {
    if(is_resource($server)){proc_terminate($server);proc_close($server);}
    if($cookieFile && is_file($cookieFile))unlink($cookieFile);
    if(!$keep && preg_match('/^codex_uat_confirm_[a-f0-9]{10}$/D',$schema)) $db->query("DROP DATABASE `$schema`");
}
