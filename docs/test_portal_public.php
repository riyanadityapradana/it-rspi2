<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../portal-tiket/public_helpers.php';
require_once __DIR__ . '/portal_test_helpers.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$schema = 'codex_portal_public_' . bin2hex(random_bytes(5));
$db = new mysqli('localhost',getenv('DB_USERNAME') ?: 'root',getenv('DB_PASSWORD') ?: '');
$db->query("CREATE DATABASE `$schema` CHARACTER SET utf8mb4"); $db->select_db($schema); $db->set_charset('utf8mb4');
$checks = 0; $keep = false; $server = null;
function pp_check($value,$label) { global $checks; if (!$value) throw new RuntimeException($label); ++$checks; }
function pp_http($path,$method='GET') {
    $body = file_get_contents('http://127.0.0.1:8101/it-rspi2/portal-tiket/'.$path,false,stream_context_create(['http'=>['method'=>$method,'ignore_errors'=>true,'follow_location'=>0,'timeout'=>5]]));
    $headers=$http_response_header ?? []; preg_match('/\s(\d{3})\s/',$headers[0] ?? '',$m);
    return ['status'=>(int)($m[1] ?? 0),'body'=>$body,'headers'=>implode("\n",$headers)];
}
try {
    cr_sql_file($db,__DIR__.'/../Database/2026-10-20_portal_tiket_layanan_it.sql',$schema);
    $db->query("INSERT INTO master_sistem(nama_sistem,deskripsi,is_active) VALUES ('SIMRS Uji','Sistem aktif',1),('Sistem Lama','Nonaktif',0)");
    $statuses=['Menunggu Analisis','Dikerjakan','Menunggu UAT','Selesai','Ditolak'];
    for($i=1;$i<=43;$i++) {
        $type=$i%2===0?'Fitur Baru':'Change Request';
        pt_query($db,"INSERT INTO tiket_pengajuan(jenis_tiket,id_sistem,kontak_pengaju,tgl_pengajuan,unit_pemohon,nama_pengaju,ttd_pengaju,status_tiket,token_uat,token_expired_at)
            VALUES (?,?,'PRIVATE_WHATSAPP','2026-10-20 08:00:00','PRIVATE_UNIT','PRIVATE_REQUESTER','PRIVATE_SIGNATURE',?,?,'2026-10-27 08:00:00')",[$type,$i===43?2:1,$statuses[($i-1)%5],'PRIVATE_TOKEN_'.$i]);
        $title=$i===3?'Progress 100%_!':($i===2?'<script>window.publicXss=1</script>':'Permintaan Uji '.$i);
        pt_query($db,'INSERT INTO tiket_detail_request(id_tiket,nama_perubahan_fitur,kondisi_masalah,perubahan_diharapkan,dampak_modul,latar_belakang,deskripsi_alur_kerja,target_pengguna,output_diharapkan) VALUES (?,?,?,?,?,?,?,?,?)',
            [$i,$title,$type==='Change Request'?'Kondisi lengkap':null,$type==='Change Request'?'Perubahan lengkap':null,$type==='Change Request'?'Dampak lengkap':null,$type==='Fitur Baru'?'Kebutuhan lengkap':null,$type==='Fitur Baru'?'Alur lengkap':null,$type==='Fitur Baru'?'Pengguna unit':null,$type==='Fitur Baru'?'Output lengkap':null]);
    }
    $db->query("INSERT INTO tiket_detail_request(id_tiket,nama_perubahan_fitur,kondisi_masalah) VALUES (1,'Secondary Needle','Rincian kedua')");
    $db->query("INSERT INTO tiket_analisis_it(id_tiket,id_staf_it,prioritas,analisis_unit_it,keputusan) VALUES (1,101,'Tinggi','Analisis lengkap <script>window.publicXss=1</script>','Keputusan lengkap'),(1,102,'Sedang','Analisis lanjutan','Keputusan lanjutan')");
    $db->query("INSERT INTO tiket_serah_terima(id_tiket,deskripsi_penyelesaian,id_staf_penyerah,status_pengujian_uat,catatan_pengujian,nama_penerima,ttd_penerima) VALUES
        (1,'Penyelesaian awal',101,NULL,NULL,NULL,NULL),
        (1,'Penyelesaian lengkap',102,'Diterima dengan catatan','Catatan hasil pengujian','PRIVATE_RECIPIENT','PRIVATE_RECIPIENT_SIGNATURE')");
    $filters=ptp_filters(['q'=>[],'status'=>[],'jenis'=>'bad','page'=>'-1']);
    pp_check($filters===['q'=>'','status'=>'','jenis'=>'','page'=>1],'Invalid filters normalized');
    pp_check(ptp_filters(['q'=>"\xff"])['q']==='','Invalid UTF8 normalized');
    pp_check(mb_strlen(ptp_filters(['q'=>str_repeat('x',120)])['q'])===100,'Search length bounded');
    $list=ptp_list($db,ptp_filters([]));
    pp_check($list['total']===43 && $list['pages']===3 && count($list['rows'])===20,'Count and pagination');
    pp_check($list['rows'][0]['id_tiket']===43,'Stable newest first');
    pp_check(!str_contains(json_encode($list),'PRIVATE_'),'List omits private fields');
    $keys=array_keys($list['rows'][0]);sort($keys);$allowed=['id_tiket','no_tiket','tgl_pengajuan','jenis_tiket','status_tiket','nama_sistem','nama_perubahan_fitur'];sort($allowed);
    pp_check($keys===$allowed,'Only public list columns selected');
    $all=[];for($page=1;$page<=3;$page++)$all=array_merge($all,array_column(ptp_list($db,ptp_filters(['page'=>(string)$page]))['rows'],'id_tiket'));
    pp_check(count($all)===43 && count(array_unique($all))===43,'No duplicates/omissions across pages or multiple details');
    $last=ptp_list($db,ptp_filters(['page'=>'999']));pp_check($last['page']===3&&count($last['rows'])===3,'Out of range page clamped');
    $empty=ptp_list($db,ptp_filters(['q'=>'NO_MATCH']));pp_check($empty['total']===0&&$empty['pages']===1&&$empty['rows']===[],'Empty result');
    foreach(['Secondary Needle','Sistem Lama','%','_','!'] as $q)pp_check(ptp_list($db,ptp_filters(['q'=>$q]))['total']===1,'Literal/public search '.$q);
    foreach(["' OR 1=1 --",'PRIVATE_WHATSAPP','PRIVATE_REQUESTER','PRIVATE_TOKEN_1'] as $q)pp_check(ptp_list($db,ptp_filters(['q'=>$q]))['total']===0,'Private/injection search excluded');
    foreach($statuses as $status){$rows=ptp_list($db,ptp_filters(['status'=>$status]));pp_check(count(array_unique(array_column($rows['rows'],'status_tiket')))===1&&$rows['rows'][0]['status_tiket']===$status,'Status filter '.$status);}
    foreach(['Change Request','Fitur Baru'] as $type){$rows=ptp_list($db,ptp_filters(['jenis'=>$type]));pp_check(count(array_unique(array_column($rows['rows'],'jenis_tiket')))===1&&$rows['rows'][0]['jenis_tiket']===$type,'Type filter '.$type);}
    pp_check(ptp_list($db,ptp_filters(['q'=>'Permintaan Uji 41','status'=>'Menunggu Analisis','jenis'=>'Change Request']))['total']===1,'Combined filters');
    pp_check(count(array_unique(array_map('ptp_status_class',$statuses)))===5,'Distinct status classes');
    pp_check(ptp_status_class('invalid')==='unknown','Status class allowlist');
    $detail=ptp_detail($db,1);
    pp_check(count($detail['details'])===2&&count($detail['analyses'])===2&&count($detail['handovers'])===2,'All relational request/analysis/handover records');
    pp_check(!str_contains(json_encode($detail),'PRIVATE_'),'Detail omits contacts/names/signatures/tokens');
    pp_check(!isset($detail['analyses'][0]['id_staf_it'])&&!isset($detail['handovers'][0]['id_staf_penyerah']),'Internal employee IDs excluded');
    pp_check($detail['details'][0]['kondisi_masalah']==='Kondisi lengkap','CR detail fields');
    pp_check(ptp_detail($db,2)['details'][0]['output_diharapkan']==='Output lengkap','Feature detail fields');
    pp_check($detail['handovers'][0]['status_pengujian_uat']===null&&$detail['handovers'][1]['catatan_pengujian']==='Catatan hasil pengujian','Pending and completed UAT');
    foreach([0,-1,9999,'1 OR 1=1',[],2147483648] as $bad)pp_check(ptp_detail($db,$bad)===null,'Invalid/missing detail ID');
    $back=ptp_list_url(ptp_filters(['q'=>'Feature & Test','status'=>'Selesai','jenis'=>'Fitur Baru','page'=>'2']));parse_str(parse_url($back,PHP_URL_QUERY),$params);
    pp_check($params['q']==='Feature & Test'&&$params['page']==='2'&&$params['status']==='Selesai','Back/pagination preserve filters');
    pp_check(ptp_date(null)==='-'&&str_contains(ptp_date('2026-10-20 08:00:00'),'20-10-2026'),'Date formatting');
    if(in_array('--http',$argv,true)) {
        $preview=__DIR__.'/../../.codex_tmp/portal-public-preview';if(!is_dir($preview))mkdir($preview,0700,true);
        $env=getenv();$env['PORTAL_DB_HOST']='localhost';$env['PORTAL_DB_DATABASE']=$schema;
        $server=proc_open([PHP_BINARY,'-S','127.0.0.1:8101','-t',dirname(__DIR__,2)],[0=>['pipe','r'],1=>['file',$preview.'/http.log','a'],2=>['file',$preview.'/http.log','a']],$pipes,null,$env);
        if(!is_resource($server))throw new RuntimeException('HTTP server unavailable');fclose($pipes[0]);
        for($i=0;$i<40;$i++){if($sock=@fsockopen('127.0.0.1',8101,$errno,$errstr,.1)){fclose($sock);break;}usleep(50000);}
        $httpStart=$checks;
        $listing=pp_http('daftar-tiket.php');pp_check($listing['status']===200,'Guest list HTTP200');
        pp_check(!str_contains($listing['headers'],'Set-Cookie:')&&!str_contains($listing['headers'],'Location:'),'No session/auth redirect');
        pp_check(stripos($listing['headers'],'Cache-Control: no-store')!==false,'Current status not cached');
        pp_check(str_contains($listing['body'],'Nama Perubahan/Fitur')&&str_contains($listing['body'],'detail-tiket.php?'),'Required columns and detail link');
        pp_check(!str_contains($listing['body'],'PRIVATE_'),'No private data in HTML list');
        $page3=pp_http('daftar-tiket.php?page=3');pp_check(str_contains($page3['body'],'Halaman 3 dari 3'),'Page three HTTP');
        $searched=pp_http('daftar-tiket.php?q=Secondary+Needle');pp_check(str_contains($searched['body'],'dari 1 tiket'),'Secondary detail search HTTP');
        $empty=pp_http('daftar-tiket.php?q=NO_MATCH');pp_check(str_contains($empty['body'],'Belum ada tiket yang sesuai'),'Empty state HTTP');
        $xss=pp_http('daftar-tiket.php?q='.urlencode('"><script>window.publicXss=1</script>'));
        pp_check(!str_contains($xss['body'],'<script>window.publicXss=1</script>')&&str_contains($xss['body'],'&lt;script&gt;'),'Escaped search');
        $cr=pp_http('detail-tiket.php?id=1&q=Secondary+Needle&page=2');pp_check($cr['status']===200,'Guest CR detail HTTP200');
        pp_check(str_contains($cr['body'],'Analisis lengkap &lt;script&gt;')&&!str_contains($cr['body'],'<script>window.publicXss=1</script>'),'Escaped analysis');
        pp_check(str_contains($cr['body'],'Penyelesaian lengkap')&&str_contains($cr['body'],'Menunggu pengujian pemohon')&&str_contains($cr['body'],'Catatan hasil pengujian'),'Completion/UAT shown');
        pp_check(!str_contains($cr['body'],'PRIVATE_'),'No private data in HTML detail');
        pp_check(str_contains($cr['body'],'q=Secondary+Needle')&&str_contains($cr['body'],'page=2'),'Back link preserves search/page');
        $feature=pp_http('detail-tiket.php?id=2');pp_check(str_contains($feature['body'],'Output lengkap')&&str_contains($feature['body'],'Kebutuhan lengkap'),'Guest feature detail');
        pp_check(str_contains($feature['body'],'Hasil analisis IT belum tersedia.')&&str_contains($feature['body'],'Hasil penyelesaian belum tersedia.'),'Pending result states');
        foreach(['','0','9999','1+OR+1=1','%5B%5D'] as $bad)pp_check(pp_http('detail-tiket.php?id='.$bad)['status']===404,'Invalid detail HTTP404');
        pp_check(pp_http('detail-tiket.php?id[]=1')['status']===404,'Array ID HTTP404');
        foreach(['daftar-tiket.php','detail-tiket.php?id=1'] as $url)pp_check(pp_http($url,'POST')['status']===405,'Public pages read-only');
        $head=pp_http('daftar-tiket.php','HEAD');pp_check($head['status']===200&&$head['body']==='','HEAD supported');
        pt_query($db,"UPDATE tiket_pengajuan SET status_tiket='Dikerjakan' WHERE id_tiket=4");$fresh=pp_http('detail-tiket.php?id=4');pp_check(str_contains($fresh['body'],'portal-status-working'),'Changed database status shown immediately');
        $db->query('RENAME TABLE master_sistem TO master_sistem_unavailable');
        try { $failed=pp_http('daftar-tiket.php');pp_check($failed['status']===503&&str_contains($failed['body'],'belum dapat dimuat')&&!str_contains($failed['body'],'SQLSTATE')&&!str_contains($failed['body'],$schema),'Generic database failure'); }
        finally { $db->query('RENAME TABLE master_sistem_unavailable TO master_sistem'); }
        echo 'HTTP checks: '.($checks-$httpStart).PHP_EOL;
    }
    if(in_array('--keep-for-browser',$argv,true)){$keep=true;echo json_encode(['schema'=>$schema,'cr'=>1,'feature'=>2,'total'=>43]).PHP_EOL;}
    echo 'PASS '.$checks.' isolated public-ticket checks.'.PHP_EOL;
} finally {
    if(is_resource($server)){proc_terminate($server);proc_close($server);}
    if(!$keep&&preg_match('/^codex_portal_public_[a-f0-9]{10}$/D',$schema))$db->query("DROP DATABASE `$schema`");
}
