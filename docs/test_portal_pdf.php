<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
ob_start();
require_once __DIR__.'/../staff/unit/portal-tiket/pdf_renderer.php';
require_once __DIR__.'/../staff/unit/portal-tiket/access.php';
require_once __DIR__.'/portal_test_helpers.php';
class PtiPdfProbe extends PtiTicketPdf {
    public $texts=[],$imagePlacements=[],$qrs=[],$footers=[];
    public function _destroy($destroyall=false,$preserve_objcopy=false) { if($destroyall)parent::_destroy($destroyall,$preserve_objcopy); }
    public function MultiCell($w,$h,$txt,$border=0,$align='J',$fill=false,$ln=1,$x=null,$y=null,$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=0,$valign='T',$fitcell=false) {
        $page=$this->getPage();$top=$y ?? $this->GetY();$r=parent::MultiCell($w,$h,$txt,$border,$align,$fill,$ln,$x,$y,$reseth,$stretch,$ishtml,$autopadding,$maxh,$valign,$fitcell);
        $this->texts[]=['text'=>$txt,'page'=>$page,'endPage'=>$this->getPage(),'top'=>$top,'end'=>$this->GetY(),'html'=>$ishtml];return $r;
    }
    public function Image($file,$x=null,$y=null,$w=0,$h=0,$type='',$link='',$align='',$resize=false,$dpi=300,$palign='',$ismask=false,$imgmask=false,$border=0,$fitbox=false,$hidden=false,$fitonpage=false,$alt=false,$altimgs=[]) {
        $page=$this->getPage();$r=parent::Image($file,$x,$y,$w,$h,$type,$link,$align,$resize,$dpi,$palign,$ismask,$imgmask,$border,$fitbox,$hidden,$fitonpage,$alt,$altimgs);
        $this->imagePlacements[]=['page'=>$page,'endPage'=>$this->getPage(),'x'=>$x,'y'=>$y,'w'=>$w,'h'=>$h,'type'=>$type];return $r;
    }
    public function write2DBarcode($code,$type,$x=null,$y=null,$w=null,$h=null,$style=[],$align='',$distort=false) {
        $page=$this->getPage();$r=parent::write2DBarcode($code,$type,$x,$y,$w,$h,$style,$align,$distort);
        $this->qrs[]=['page'=>$page,'endPage'=>$this->getPage(),'x'=>$x,'y'=>$y,'w'=>$w,'h'=>$h,'payload'=>json_decode($code,true)];return $r;
    }
    public function Footer() { parent::Footer();$this->footers[]=['page'=>$this->getPage(),'y'=>$this->GetY(),'x'=>$this->GetX()]; }
}
function pp_check($ok,$label){global $checks;if(!$ok)throw new RuntimeException($label);$checks++;}
function pp_reject($fn,$code,$label){try{$fn();}catch(DomainException $e){pp_check($e->getCode()===$code,$label);return;}throw new RuntimeException('Not rejected: '.$label);}
function pp_ticket($type='Change Request',$status='Selesai',$long=false,$longHandover=false){
    global $db;
    pt_query($db,'INSERT INTO tiket_pengajuan(jenis_tiket,id_sistem,kontak_pengaju,unit_pemohon,nama_pengaju,ttd_pengaju,status_tiket,token_uat,token_expired_at) VALUES (?,1,?,?,?,?,?,?,?)',[$type,'PRIVATE_CONTACT','Unit Pengujian','Pemohon Uji',cr_image(),$status,bin2hex(random_bytes(32)),'2000-01-01 00:00:00']);$id=$db->insert_id;
    $description=($long?str_repeat('Uraian panjang yang harus tetap tercetak utuh. ',40):'Uraian singkat').' ENDREQUEST';
    pt_query($db,'INSERT INTO tiket_detail_request(id_tiket,nama_perubahan_fitur,kondisi_masalah,perubahan_diharapkan,dampak_modul,latar_belakang,deskripsi_alur_kerja,target_pengguna,output_diharapkan) VALUES (?,?,?,?,?,?,?,?,?)',[$id,'Nama '.$type,$description,'Perubahan yang diharapkan','Dampak integrasi',$description,'Alur kerja baru','Pengguna layanan','Output yang diharapkan']);
    pt_query($db,"INSERT INTO tiket_analisis_it(id_tiket,id_staf_it,nama_staf_it,tgl_analisis,prioritas,analisis_unit_it,keputusan) VALUES (?,101,'Analis Uji','2026-10-21 10:00:00','Sedang','Analisis unit IT lengkap','Disetujui ENDANALYSIS')",[$id]);
    pt_query($db,"INSERT INTO tiket_serah_terima(id_tiket,id_staf_penyerah,nama_staf_penyerah,tgl_serah_terima,deskripsi_penyelesaian,status_pengujian_uat,catatan_pengujian,nama_penerima,ttd_penerima) VALUES (?,102,'Penyerah Uji','2026-10-22 08:00:00',?,'Diterima dengan catatan','Catatan pengujian UAT','Penerima Uji',?)",[$id,($longHandover?str_repeat('Penyelesaian sangat panjang. ',300):'Penyelesaian lengkap').' ENDHANDOVER',cr_image()]);return (string)$id;
}
$checks=0;$keep=false;$server=null;$schema='codex_ticket_pdf_'.bin2hex(random_bytes(5));$db=new mysqli('localhost',getenv('DB_USERNAME')?:'root',getenv('DB_PASSWORD')?:'');$db->query("CREATE DATABASE `$schema` CHARACTER SET utf8mb4");$db->select_db($schema);$db->set_charset('utf8mb4');
try {
    cr_sql_file($db,__DIR__.'/../Database/2026-10-20_portal_tiket_layanan_it.sql',$schema);
    $db->query("INSERT INTO master_sistem(nama_sistem,deskripsi,is_active) VALUES ('SIMRS','Sistem untuk uji cetak',0)");
    $db->query('CREATE TABLE tb_user(id_user INT PRIMARY KEY,nama_lengkap VARCHAR(100),nip VARCHAR(40),role VARCHAR(30),status VARCHAR(20))');
    $db->query("INSERT INTO tb_user VALUES (1,'Staff Uji','UJI1','Staff','aktif'),(2,'Kepala Uji','UJI2','Kepala Ruangan','aktif'),(3,'Nonaktif','UJI3','Staff','nonaktif'),(4,'Calon','UJI4','Calon Pegawai','aktif')");
    $db->query('CREATE TABLE pegawai(id INT PRIMARY KEY,nama VARCHAR(150))');$db->query("INSERT INTO pegawai VALUES (101,'Analis Master'),(102,'Penyerah Master')");
    $ids=['cr'=>pp_ticket(),'feature'=>pp_ticket('Fitur Baru'),'long'=>pp_ticket('Change Request','Selesai',true),'long_feature'=>pp_ticket('Fitur Baru','Selesai',true),'long_handover'=>pp_ticket('Change Request','Selesai',false,true)];
    foreach(['Menunggu Analisis','Dikerjakan','Menunggu UAT','Ditolak'] as $status){$id=pp_ticket('Change Request',$status);pp_reject(function()use($db,$id){pti_pdf_load($db,$id,$db);},409,'Non-completed direct print rejected');$ids[$status]=$id;}
    foreach([null,[],0,'0','1 OR 1=1','999999'] as $bad)pp_reject(function()use($db,$bad){pti_pdf_load($db,$bad,$db);},404,'Invalid/missing ticket');
    pp_check(pti_internal_user($db,['id_user'=>1,'role'=>'Staff']) && pti_internal_user($db,['id_user'=>2,'role'=>'Kepala Ruangan']),'Both IT roles authorized');
    pp_check(!pti_internal_user($db,['id_user'=>3,'role'=>'Staff']) && !pti_internal_user($db,['id_user'=>4,'role'=>'Calon Pegawai']),'Inactive/other roles denied');
    $reports=[];$preview=__DIR__.'/../../.codex_tmp/ticket-pdf-preview';if(!is_dir($preview))mkdir($preview,0700,true);
    foreach(['cr','feature','long','long_feature','long_handover'] as $kind){
        $report=pti_pdf_load($db,$ids[$kind],$db);$reports[$kind]=$report;
        pp_check(!array_key_exists('token_uat',$report['ticket'])&&!array_key_exists('kontak_pengaju',$report['ticket']),'Export omits bearer and WhatsApp');
        pp_check($report['analyses'][0]['nama_staf_it']==='Analis Uji','Staff snapshot retained despite master rename');
        $pdf=new PtiPdfProbe('P','mm','A4',true,'UTF-8',false);$pdf->SetCompression(false);$binary=pti_build_ticket_pdf($report,$pdf);file_put_contents($preview.'/'.$kind.'.pdf',$binary);
        file_put_contents($preview.'/'.$kind.'-layout.json',json_encode(['pages'=>$pdf->getNumPages(),'sections'=>$pdf->sectionPages,'images'=>$pdf->imagePlacements,'qrs'=>$pdf->qrs,'texts'=>$pdf->texts],JSON_PRETTY_PRINT | JSON_INVALID_UTF8_SUBSTITUTE));
        pp_check(str_starts_with($binary,'%PDF-')&&str_ends_with(trim($binary),'%%EOF'),'Actual PDF binary '.$kind);
        pp_check(abs($pdf->getPageWidth()-210)<.1&&abs($pdf->getPageHeight()-297)<.1,'A4 dimensions '.$kind);
        $handoverPage=$pdf->sectionPages['serah_terima_1'];
        pp_check($handoverPage>=2,'Handover always starts new page '.$kind);
        $analysis=array_values(array_filter($pdf->texts,function($row){return str_contains($row['text'],'ENDANALYSIS');}))[0];
        pp_check($analysis['endPage']<$handoverPage,'Handover begins after complete analysis '.$kind);
        pp_check(count($pdf->imagePlacements)===2&&count($pdf->qrs)===2,'Both PNG signatures and both staff QR rendered '.$kind);
        pp_check($pdf->imagePlacements[0]['page']<$handoverPage&&$pdf->imagePlacements[1]['page']>=$handoverPage,'Canvas signatures on correct form '.$kind);
        pp_check($pdf->qrs[0]['x']>105&&$pdf->qrs[0]['page']<$handoverPage&&$pdf->qrs[1]['x']<105&&$pdf->qrs[1]['page']>=$handoverPage,'IT QR signature columns '.$kind);
        foreach(array_merge($pdf->imagePlacements,$pdf->qrs) as $image)pp_check($image['page']===$image['endPage']&&$image['y']+$image['h']<=279.1,'Signature/QR stays above footer '.$kind);
        foreach($pdf->qrs as $qr)pp_check(!array_key_exists('token',$qr['payload'])&&strlen($qr['payload']['sha256'])===64,'Archive QR has fingerprint and no bearer token');
        pp_check(count(array_unique(array_column($pdf->footers,'page')))===$pdf->getNumPages(),'Every page gets footer '.$kind);
        foreach($pdf->footers as $footer)pp_check($footer['y']>=284&&$footer['x']>=189,'Page number bottom right '.$kind);
        pp_check(!array_filter($pdf->texts,function($r){return $r['html'];}),'User text never interpreted as HTML');
        pp_check(str_contains($binary,'Halaman 1 dari '.$pdf->getNumPages()),'Resolved page number present in PDF stream '.$kind);
        for($pageNumber=1;$pageNumber<=$pdf->getNumPages();$pageNumber++)pp_check(str_contains($binary,'Halaman '.$pageNumber.' dari '.$pdf->getNumPages()),'Every PDF page number resolves '.$kind);
        pp_check(str_contains($binary,mb_convert_encoding('ENDREQUEST','UTF-16BE','UTF-8')) && str_contains($binary,mb_convert_encoding('ENDHANDOVER','UTF-16BE','UTF-8')),'Actual PDF retains end of request and completion text '.$kind);
        pp_check(count(array_filter($pdf->texts,function($r){return str_contains($r['text'],'ENDREQUEST');}))===1,'Request end marker retained '.$kind);
        if(in_array($kind,['cr','feature'],true))pp_check($pdf->getNumPages()===2,'Short cycle is two pages '.$kind);
        if(in_array($kind,['long','long_feature'],true))pp_check($handoverPage===3,'Two-page request pushes handover to page three '.$kind);
        if($kind==='long_handover')pp_check($pdf->getNumPages()>2&&$pdf->imagePlacements[1]['page']===$pdf->getNumPages(),'Long completion flows before receiver signature');
        echo $kind.': '.$pdf->getNumPages().' pages; handover starts '.$handoverPage.PHP_EOL;
    }
    $expected=pt_it_signature($reports['cr']['ticket'],'analisis',$reports['cr']['analyses'][0]);$qr=json_decode(pti_pdf_qr_payload($reports['cr']['ticket'],'analisis',$reports['cr']['analyses'][0]),true);
    pp_check($expected['hash']===$qr['sha256'],'Archive QR fingerprint matches UAT signature record');
    pt_query($db,'UPDATE tiket_analisis_it SET nama_staf_it=NULL,tgl_analisis=NULL WHERE id_tiket=?',[$ids['cr']]);$legacy=pti_pdf_load($db,$ids['cr'],$db);
    pp_check($legacy['analyses'][0]['nama_staf_it']==='Analis Master'&&$legacy['analyses'][0]['tgl_analisis']===null,'Legacy names resolved without inventing date');
    pt_query($db,"UPDATE tiket_pengajuan SET ttd_pengaju='https://example.invalid/private.png' WHERE id_tiket=?",[$ids['cr']]);
    pt_query($db,"UPDATE tiket_serah_terima SET ttd_penerima='data:image/svg+xml;base64,PHN2Zz4=' WHERE id_tiket=?",[$ids['cr']]);
    $unsafe=pti_pdf_load($db,$ids['cr'],$db);pp_check($unsafe['ticket']['ttd_pengaju']===null&&$unsafe['handovers'][0]['ttd_penerima']===null,'Unsafe image input rejected');
    $pdf=new PtiPdfProbe();pti_build_ticket_pdf($unsafe,$pdf);pp_check(count($pdf->imagePlacements)===0,'Invalid image URLs not fetched by renderer');
    pt_query($db,'UPDATE tiket_pengajuan SET ttd_pengaju=? WHERE id_tiket=?',[cr_image(),$ids['cr']]);
    pt_query($db,'UPDATE tiket_serah_terima SET ttd_penerima=? WHERE id_tiket=?',[cr_image(),$ids['cr']]);
    if(in_array('--keep-for-http',$argv,true) || in_array('--http',$argv,true)){
        $sessions=[];if(!is_dir($preview.'/sessions'))mkdir($preview.'/sessions',0700,true);session_save_path($preview.'/sessions');
        foreach(['staff'=>[1,'Staff'],'head'=>[2,'Kepala Ruangan'],'inactive'=>[3,'Staff'],'other'=>[4,'Calon Pegawai'],'mismatch'=>[1,'Kepala Ruangan']] as $label=>$auth){session_id('ticketpdf'.bin2hex(random_bytes(12)));session_start();$_SESSION=['id_user'=>$auth[0],'role'=>$auth[1],'nip'=>'fixture','foto'=>''];$sessions[$label]=session_id();session_write_close();}
        if(in_array('--http',$argv,true)){
            $env=getenv();$env['DB_SERVER']='localhost';$env['DB_DATABASE']=$schema;$env['PORTAL_DB_HOST']='localhost';$env['PORTAL_DB_DATABASE']=$schema;
            $server=proc_open([PHP_BINARY,'-d','session.save_path='.$preview.'/sessions','-d','sys_temp_dir='.$preview,'-d','upload_tmp_dir='.$preview,'-S','127.0.0.1:8109','-t',dirname(__DIR__,2)],[0=>['pipe','r'],1=>['file',$preview.'/http.log','a'],2=>['file',$preview.'/http.log','a']],$pipes,null,$env);
            if(!is_resource($server))throw new RuntimeException('Test HTTP server unavailable');fclose($pipes[0]);
            for($i=0;$i<40;$i++){if($sock=@fsockopen('127.0.0.1',8109,$errno,$errstr,.1)){fclose($sock);break;}usleep(50000);}
            $get=function($path,$auth=null,$method='GET')use($sessions){$ch=curl_init('http://127.0.0.1:8109/it-rspi2/'.$path);curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_HEADER=>true,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_TIMEOUT=>15,CURLOPT_CUSTOMREQUEST=>$method]);if($auth)curl_setopt($ch,CURLOPT_COOKIE,'PHPSESSID='.$sessions[$auth]);if($method==='HEAD')curl_setopt($ch,CURLOPT_NOBODY,true);$r=curl_exec($ch);if($r===false)throw new RuntimeException(curl_error($ch));$n=curl_getinfo($ch,CURLINFO_HEADER_SIZE);$status=curl_getinfo($ch,CURLINFO_HTTP_CODE);curl_close($ch);return ['code'=>$status,'headers'=>substr($r,0,$n),'body'=>substr($r,$n)];};
            $httpStart=$checks;$url='staff/unit/portal-tiket/pdf.php?id='.$ids['feature'];
            $guest=$get($url);pp_check($guest['code']===302&&str_contains($guest['headers'],'/it-rspi2/main_login/form_login.php'),'Unauthenticated print redirects to login');
            foreach(['inactive','other','mismatch'] as $bad)pp_check($get($url,$bad)['code']===403,'Invalid print session denied');
            foreach(['staff','head'] as $auth){
                $r=$get($url,$auth);pp_check($r['code']===200 && str_starts_with($r['body'],'%PDF-'),'Authorized direct PDF response');
                pp_check(str_contains($r['headers'],'Content-Type: application/pdf')&&str_contains($r['headers'],'Content-Disposition: inline;'),'Inline PDF headers');
                pp_check(str_contains($r['headers'],'Cache-Control: no-store')&&str_contains($r['headers'],'Referrer-Policy: no-referrer'),'Private PDF headers');
                pp_check(!str_contains(substr($r['body'],0,100),'<html')&&!str_contains(substr($r['body'],0,100),'Warning'),'No dashboard HTML/PHP warning corrupts binary');
                $dashboard=$get(($auth==='staff'?'staff/dashboard_staff.php':'admin/dashboard_admin.php').'?unit=portal_tiket',$auth);
                pp_check($dashboard['code']===200 && str_contains($dashboard['body'],'fas fa-search') && str_contains($dashboard['body'],'fas fa-print'),'Both role dashboards display action icons');
                preg_match_all('/<tr>\s*<td><strong>(.*?)<\/tr>/s',$dashboard['body'],$rows);
                foreach($rows[0] as $row)pp_check(str_contains($row,'fa-print')===(str_contains($row,'>Selesai</span>')),'Printer appears only for completed row');
            }
            foreach(['Menunggu Analisis','Dikerjakan','Menunggu UAT','Ditolak'] as $status)pp_check($get('staff/unit/portal-tiket/pdf.php?id='.$ids[$status],'staff')['code']===409,'Direct URL denies incomplete status');
            foreach(['','0','9999','1+OR+1=1','%5B%5D'] as $id)pp_check($get('staff/unit/portal-tiket/pdf.php?id='.$id,'staff')['code']===404,'Bad print ID rejected');
            pp_check($get('staff/unit/portal-tiket/pdf.php?id[]=1','staff')['code']===404,'Array print ID rejected');
            $head=$get($url,'staff','HEAD');pp_check($head['code']===200&&$head['body']===''&&str_contains($head['headers'],'Content-Length:'),'HEAD has no PDF body');
            pp_check($get($url,'staff','POST')['code']===405,'Print endpoint read-only');
            $db->query('RENAME TABLE master_sistem TO master_sistem_unavailable');
            try{$failure=$get($url,'staff');pp_check($failure['code']===503&&!str_contains($failure['body'],$schema)&&!str_contains($failure['body'],'SQLSTATE'),'Unavailable data gives safe error');}
            finally{$db->query('RENAME TABLE master_sistem_unavailable TO master_sistem');}
            echo 'HTTP checks: '.($checks-$httpStart).PHP_EOL;
        }
        if(in_array('--keep-for-http',$argv,true)){$keep=true;echo json_encode(['schema'=>$schema,'ids'=>$ids,'sessions'=>$sessions]).PHP_EOL;}
    }
    echo 'PASS '.$checks.' ticket PDF checks.'.PHP_EOL;
}finally{
    if(is_resource($server)){proc_terminate($server);proc_close($server);}
    if(!$keep&&isset($sessions))foreach($sessions as $sid){$f=$preview.'/sessions/sess_'.$sid;if(is_file($f))unlink($f);}
    if(!$keep&&preg_match('/^codex_ticket_pdf_[a-f0-9]{10}$/D',$schema))$db->query("DROP DATABASE `$schema`");
}
