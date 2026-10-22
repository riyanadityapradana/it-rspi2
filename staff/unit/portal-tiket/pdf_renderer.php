<?php
require_once __DIR__.'/pdf_helpers.php';
require_once __DIR__.'/../../../library/tcpdf/tcpdf.php';

class PtiTicketPdf extends TCPDF
{
    public $sectionPages = [];
    public function Footer() {
        $this->SetY(-12); $this->SetFont('helvetica','',8);
        $this->Cell(0,6,'Halaman '.$this->getAliasNumPage().' dari '.$this->getAliasNbPages(),0,0,'R');
    }
    public function reserveSpace($height) {
        if ($this->GetY()+$height > $this->getPageHeight()-$this->getBreakMargin()) $this->AddPage();
    }
    public function formTitle($title,$number) {
        $this->SetFont('dejavuserif','B',14);$this->MultiCell(170,0,$title,0,'C',false,1);
        $this->Ln(2);$this->SetFont('dejavuserif','',9);$this->MultiCell(170,0,'No Tiket: '.$number,0,'R',false,1);$this->Ln(5);
    }
    public function sectionLabel($title) {
        $this->reserveSpace(18);$this->Ln(3);$this->SetFont('dejavuserif','I',10.5);$this->MultiCell(170,0,$title,0,'L',false,1);$this->Ln(2);
    }
    public function field($label,$value) {
        $value=trim((string)$value);if($value==='')$value='Belum tercatat';
        $this->SetFont('dejavuserif','',10.5);
        $labelHeight=$this->getStringHeight(64,$label);$this->reserveSpace(max(8,$labelHeight));
        $page=$this->getPage();$start=$this->GetY();
        $this->MultiCell(64,0,$label,0,'L',false,1,20,$start);$labelEnd=$this->GetY();
        $this->SetXY(84,$start);$this->Cell(4,6,':',0,0);
        // MultiCell mengalirkan teks panjang ke halaman berikutnya tanpa pemotongan.
        $this->MultiCell(102,0,$value,0,'L',false,1,88,$start);
        if($this->getPage()===$page)$this->SetY(max($labelEnd,$this->GetY()));
        $this->SetX(20);$this->Ln(2);
    }
    public function priorities($selected) {
        $this->reserveSpace(11);$y=$this->GetY();$this->SetFont('dejavuserif','',10.5);$this->SetXY(20,$y);$this->Cell(64,7,'Prioritas');$this->Cell(4,7,':');
        foreach(['Tinggi','Sedang','Rendah'] as $i=>$label){$x=88+$i*33;$this->Rect($x,$y+2,3,3);if($selected===$label){$this->Line($x+.5,$y+3.5,$x+1.3,$y+4.4);$this->Line($x+1.3,$y+4.4,$x+2.6,$y+2.6);}$this->SetXY($x+4.5,$y);$this->Cell(28,7,$label);}
        $this->SetXY(20,$y+10);
    }
    public function signatures($left,$right,$placeDate) {
        $this->SetFont('dejavuserif','',10);
        $nameHeight=max($this->getStringHeight(80,$left['name'] ?? 'Belum tercatat'),$this->getStringHeight(80,$right['name'] ?? 'Belum tercatat'));
        $height=58+$nameHeight;$this->reserveSpace($height);$start=$this->GetY();
        $this->MultiCell(170,0,'Martapura, '.pti_pdf_date($placeDate),0,'R',false,1);$y=$this->GetY()+3;
        foreach([$left,$right] as $i=>$sign){
            $x=20+$i*90;$this->SetFont('dejavuserif','',10);$this->MultiCell(80,10,$sign['role'],0,'C',false,1,$x,$y);
            $imageY=$y+12;
            if(!empty($sign['png'])){
                // Gunakan hanya PNG yang divalidasi, jangan membuka URL/path dari input.
                try {$png=pt_signature($sign['png']);$raw=base64_decode(substr($png,22),true);$size=getimagesizefromstring($raw);$scale=min(62/$size[0],26/$size[1]);$w=$size[0]*$scale;$h=$size[1]*$scale;
                    $this->Image('@'.$raw,$x+(80-$w)/2,$imageY+(28-$h)/2,$w,$h,'PNG');
                }catch(DomainException $e){$this->SetFont('dejavuserif','I',8);$this->MultiCell(80,10,'Tanda tangan belum tersedia',0,'C',false,1,$x,$imageY+8);}
            } elseif(!empty($sign['qr'])) {
                $this->write2DBarcode($sign['qr'],'QRCODE,M',$x+26,$imageY,28,28,['border'=>false,'padding'=>2,'fgcolor'=>[0,0,0],'bgcolor'=>[255,255,255]],'N');
            } else {
                $this->SetFont('dejavuserif','I',8);$this->MultiCell(80,10,'Tanda tangan belum tersedia',0,'C',false,1,$x,$imageY+8);
            }
            $this->SetFont('dejavuserif','',10);$this->MultiCell(80,$nameHeight,$sign['name'] ?? 'Nama belum tercatat',0,'C',false,1,$x,$imageY+31);
            $this->SetFont('dejavuserif','',8);$this->MultiCell(80,5,$sign['caption'],0,'C',false,1,$x,$imageY+31+$nameHeight);
        }
        // Image/QR tidak memajukan kursor; lanjutkan di bawah kedua kolom.
        $this->SetXY(20,$start+$height);
    }
}

function pti_pdf_date($value)
{
    if(!is_string($value))return 'tanggal belum tercatat';
    $date=DateTimeImmutable::createFromFormat('!Y-m-d H:i:s',$value);
    return $date && $date->format('Y-m-d H:i:s')===$value ? $date->format('d-m-Y') : 'tanggal belum tercatat';
}

function pti_build_ticket_pdf($report,$pdf=null)
{
    $pdf=$pdf ?? new PtiTicketPdf('P','mm','A4',true,'UTF-8',false);
    $pdf->setPrintHeader(false);$pdf->setPrintFooter(true);$pdf->SetMargins(20,18,20);$pdf->SetAutoPageBreak(true,18);$pdf->setCellPaddings(0,.6,0,.6);
    $pdf->SetCreator('IT-RSPI');$pdf->SetAuthor('IT-RSPI');$pdf->SetTitle('Portal Tiket IT - '.$report['ticket']['no_tiket']);
    $ticket=$report['ticket'];$feature=$ticket['jenis_tiket']==='Fitur Baru';$system=trim((string)($ticket['nama_sistem'] ?? '')) ?: 'Sistem IT';
    $title=$feature?'Form Permintaan Fitur Baru':'Form Change Request';
    $pdf->AddPage();$pdf->sectionPages['pengajuan']=$pdf->getPage();$pdf->formTitle($title.' '.$system,$ticket['no_tiket']);
    $pdf->sectionLabel('*Diisi oleh Pemohon');$pdf->field('Tanggal Pengajuan',pti_pdf_date($ticket['tgl_pengajuan']));$pdf->field('Unit Pemohon',$ticket['unit_pemohon']);
    foreach($report['details'] as $index=>$detail){
        if(count($report['details'])>1)$pdf->sectionLabel('Rincian Pengajuan '.($index+1));
        foreach(pt_request_definition($ticket['jenis_tiket'])['fields'] as $key=>$field)$pdf->field($field['label'],$detail[$key] ?? null);
    }
    if(!$report['details'])$pdf->field('Rincian Pengajuan',null);
    foreach($report['analyses'] as $record){
        $pdf->sectionLabel('*Diisi oleh Unit IT');$pdf->priorities($record['prioritas']);$pdf->field('Analisis Unit IT',$record['analisis_unit_it']);$pdf->field('Keputusan/Persetujuan',$record['keputusan']);
        $pdf->signatures(['role'=>'Diajukan oleh,','name'=>$ticket['nama_pengaju'],'png'=>$ticket['ttd_pengaju'],'caption'=>'Nama Terang & Gelar'],
            ['role'=>'Dianalisis oleh,','name'=>$record['nama_staf_it'],'qr'=>pti_pdf_qr_payload($ticket,'analisis',$record),'caption'=>'System Analyst / Staf IT'],$record['tgl_analisis']);
    }
    if(!$report['analyses']){
        $pdf->sectionLabel('*Diisi oleh Unit IT');$pdf->field('Analisis Unit IT',null);
        $pdf->signatures(['role'=>'Diajukan oleh,','name'=>$ticket['nama_pengaju'],'png'=>$ticket['ttd_pengaju'],'caption'=>'Nama Terang & Gelar'],['role'=>'Dianalisis oleh,','name'=>null,'caption'=>'System Analyst / Staf IT'],null);
    }
    // Pemisahan wajib, termasuk jika pengajuan/analisis sudah memakai beberapa halaman.
    $handoverTitle='Form Serah Terima Penyelesaian '.($feature?'Fitur Baru':'Change Request').' '.$system;
    foreach($report['handovers'] ?: [null] as $index=>$record){
        $pdf->AddPage();$pdf->sectionPages['serah_terima_'.($index+1)]=$pdf->getPage();$pdf->formTitle($handoverTitle,$ticket['no_tiket']);
        $pdf->field('Tanggal Serah Terima',pti_pdf_date($record['tgl_serah_terima'] ?? null));
        $names=implode("\n",array_column($report['details'],'nama_perubahan_fitur'));$pdf->field($feature?'Nama Fitur':'Nama Perubahan',$names);$pdf->field('Unit Pemohon',$ticket['unit_pemohon']);
        $pdf->field('Deskripsi Penyelesaian',$record['deskripsi_penyelesaian'] ?? null);
        $status=$record['status_pengujian_uat'] ?? null;
        $pdf->field('Status Pengujian (UAT)',$status==='Berhasil'?'Berhasil dan sistem berfungsi dengan baik.':$status);
        if(trim((string)($record['catatan_pengujian'] ?? ''))!=='')$pdf->field('Catatan Pengujian',$record['catatan_pengujian']);
        $pdf->sectionLabel('Pernyataan:');$pdf->SetFont('dejavuserif','',10.5);
        $statement=$feature?'seluruh permintaan yang diajukan dalam Form Permintaan Fitur Baru':'seluruh perubahan atau perbaikan yang diajukan dalam Form Change Request';
        $pdf->MultiCell(170,0,'Dengan ini menyatakan bahwa '.$statement.' '.$system.' telah diselesaikan oleh tim IT, diuji coba, dan diserahterimakan kepada unit pemohon untuk digunakan dalam operasional.',0,'J',false,1);$pdf->Ln(6);
        $pdf->signatures(['role'=>'Diserahkan oleh (Tim IT),','name'=>$record['nama_staf_penyerah'] ?? null,'qr'=>$record?pti_pdf_qr_payload($ticket,'serah_terima',$record):null,'caption'=>'Nama Terang & Gelar'],
            ['role'=>"Diterima dan Disetujui oleh\n(Unit Pemohon),",'name'=>$record['nama_penerima'] ?? null,'png'=>$record['ttd_penerima'] ?? null,'caption'=>'Nama Terang & Gelar'],$record['tgl_serah_terima'] ?? null);
    }
    return $pdf->Output('','S');
}
