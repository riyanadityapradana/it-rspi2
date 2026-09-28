<?php
// Standalone regression: renders real TCPDF images without application/database writes.
define('K_PATH_CACHE', rtrim(sys_get_temp_dir(), '/\\').DIRECTORY_SEPARATOR);
require_once __DIR__.'/../staff/unit/monev/pdf_renderer.php';

class MnPhotoLayoutTestPdf extends MnInspectionPdf {
    public $captions = [];
    public $photos = [];
    public function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false, $ln=1, $x=null, $y=null, $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0, $valign='T', $fitcell=false) {
        $page = $this->getPage();
        $start = $y === null ? $this->GetY() : $y;
        $result = parent::MultiCell($w,$h,$txt,$border,$align,$fill,$ln,$x,$y,$reseth,$stretch,$ishtml,$autopadding,$maxh,$valign,$fitcell);
        if (strpos($txt, 'Bukti ') === 0) $this->captions[] = ['page'=>$page, 'endPage'=>$this->getPage(), 'top'=>$start, 'bottom'=>$this->GetY()];
        return $result;
    }
    public function Image($file, $x=null, $y=null, $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false, $alt=false, $altimgs=[]) {
        $page = $this->getPage();
        $result = parent::Image($file,$x,$y,$w,$h,$type,$link,$align,$resize,$dpi,$palign,$ismask,$imgmask,$border,$fitbox,$hidden,$fitonpage,$alt,$altimgs);
        $this->photos[] = ['page'=>$page, 'endPage'=>$this->getPage(), 'x'=>$x, 'y'=>$y, 'w'=>$w, 'h'=>$h];
        return $result;
    }
}

$checks = 0;
function photo_check($ok, $message) {
    global $checks;
    if (!$ok) throw new RuntimeException($message);
    ++$checks;
}
$files = [];
try {
    $photos = [];
    $sizes = [[800,1600],[1600,800],[200,3000],[800,800],[1600,800],[800,1600]];
    foreach ($sizes as $i=>$size) {
        $file = tempnam(sys_get_temp_dir(), 'mnphoto_');
        $files[] = $file;
        $image = imagecreatetruecolor($size[0],$size[1]);
        imagefill($image,0,0,imagecolorallocate($image,220,235,245));
        imagepng($image,$file);
        imagedestroy($image);
        $caption=mn_pdf_checklist_caption(2,['nama_lokasi'=>'Farmasi Ralan'],$i+1,[
            'lokasi_id_perangkat'=>'Farmasi - PC 01','catatan_bukti'=>$i===2 ? str_repeat('Keterangan foto panjang. ',20) : 'Foto uji',
            'hasil'=>'S','kategori_snapshot'=>'Komputer','deskripsi_snapshot'=>'Layar terkunci saat ditinggalkan.'
        ]);
        photo_check(strpos($caption,'Bundle Ke-2 - Farmasi Ralan')!==false && strpos($caption,'Lokasi/Bukti: Farmasi - PC 01')!==false && strpos($caption,'Hasil: S')!==false && strpos($caption,'Kriteria Inspeksi: Komputer')!==false,'Checklist photo carries bundle and criterion context');
        if ($i===5) {
            $caption=mn_pdf_repair_caption(['no_temuan'=>'MNV-26-289','deskripsi_snapshot'=>'Layar terkunci.','lokasi_id_perangkat'=>'Farmasi - PC 02']);
            photo_check($caption==="Bukti perbaikan MNV-26-289\nButir checklist: Layar terkunci.\nLokasi/ID: Farmasi - PC 02",'Repair caption places criterion and location below title');
        }
        $photos[] = ['path'=>$file, 'label'=>$caption];
    }
    $pdf = new MnPhotoLayoutTestPdf('P','mm','A4',true,'UTF-8',false);
    $pdf->setPrintHeader(false);
    $pdf->SetMargins(12,12,12);
    $pdf->SetAutoPageBreak(true,14);
    mn_pdf_render_photo_appendix($pdf, []);
    photo_check($pdf->getNumPages()===0, 'Empty appendix must not add pages');
    mn_pdf_render_photo_appendix($pdf,$photos);
    photo_check(count($pdf->photos)===6 && count($pdf->captions)===6, 'All images and captions render');
    photo_check($pdf->getNumPages()>1, 'Images must paginate');
    foreach ($pdf->photos as $i=>$box) {
        $caption = $pdf->captions[$i];
        photo_check($caption['page']===$box['page'] && $caption['endPage']===$box['endPage'], 'Caption and photo stay on one page');
        photo_check($caption['bottom']<=$box['y'], 'Caption must not overlap its photo');
        photo_check($box['y']+$box['h'] <= $pdf->getPageHeight()-14 && $box['x']+$box['w'] <= $pdf->getPageWidth()-12, 'Photo stays within printable bounds');
        photo_check(abs($box['w']/$box['h']-$sizes[$i][0]/$sizes[$i][1])<0.00001, 'Photo preserves aspect ratio');
        if ($i && $pdf->photos[$i-1]['page']===$caption['page']) {
            $previous=$pdf->photos[$i-1];
            photo_check($caption['top'] >= $previous['y']+$previous['h']+4.9, 'Next caption starts below previous photo');
        }
    }
    // A note taller than a page must flow without truncation or negative image size.
    $long=$photos[0]; $long['label'].="\n".str_repeat("Catatan panjang harus tetap tercetak.\n",100);
    mn_pdf_render_photo_appendix($pdf,[$long]);
    $last=end($pdf->photos);
    photo_check($last['w']>0 && $last['h']>0 && $last['y']+$last['h']<=$pdf->getPageHeight()-14,'Multi-page caption keeps image dimensions positive and inside page');
    $bytes = $pdf->Output('', 'S');
    photo_check(substr($bytes,0,4)==='%PDF' && strpos($bytes,'/Subtype /Image')!==false, 'Real PDF contains images');
    $rowsPdf=new MnInspectionPdf('P','mm','A4',true,'UTF-8',false);
    $rowsPdf->setPrintHeader(false);
    $rowsPdf->SetMargins(12,12,12);
    $rowsPdf->SetAutoPageBreak(true,14);
    $rowsPdf->SetFont('dejavusans','',8.7);
    $rowsPdf->AddPage(); $rowsPdf->SetY(263);
    $rowsPdf->writeHTML(mn_pdf_styles().'<table cellpadding="3">'.mn_pdf_field('Kondisi dan risiko',str_repeat("Catatan baris uji\n",8)).'</table>');
    photo_check($rowsPdf->getNumPages()===2 && $rowsPdf->GetY()<75,'Finding row moves intact to next page when remaining space is insufficient');
    $rowsPdf->writeHTML(mn_pdf_styles().'<table cellpadding="3">'.mn_pdf_field('Tindakan',str_repeat("Catatan sangat panjang\n",160)).'</table>');
    photo_check($rowsPdf->getNumPages()>3,'Oversized finding row can continue across pages');
    $rowsPdf->Output('','S');
    echo "MONEV_PDF_PHOTOS_OK ($checks checks)\n";
} finally {
    foreach ($files as $file) if (is_file($file)) unlink($file);
}
