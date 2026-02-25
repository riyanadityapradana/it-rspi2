<?php
ob_clean();
error_reporting(0);
ini_set('display_errors', 0);
require_once('../../../config/koneksi.php');
require_once('../../../library/tcpdf/tcpdf.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
// Ambil data cuti
$qCuti = mysqli_query($config, "SELECT * FROM tb_cuti WHERE id_cuti = '$id'");
$dataCuti = mysqli_fetch_assoc($qCuti);
if (!$dataCuti) {
    echo 'Data tidak ditemukan';
    exit;
}

$id_user    = $dataCuti['id_user'];
$id_pimpinan = isset($dataCuti['id_pimpinan']) ? $dataCuti['id_pimpinan'] : '';

$user    = mysqli_fetch_assoc(mysqli_query($config, "SELECT * FROM tb_user WHERE id_user = '$id_user'"));
$pimpinan = $id_pimpinan ? mysqli_fetch_assoc(mysqli_query($config, "SELECT * FROM tb_user WHERE id_user = '$id_pimpinan'")) : ['nama_lengkap'=>'','nip'=>''];

function bulan_indo($n){
    $bulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    return $bulan[(int)$n];
}
function tgl_indo($tanggal){
    if(empty($tanggal)) return '';
    $d = date_create($tanggal);
    return date_format($d,'j').' '.bulan_indo(date_format($d,'n')).' '.date_format($d,'Y');
}

$pdf = NULL;
class MYPDF extends TCPDF {
    public function Header() {
        // Logo dan header
        $this->Image('../../../assets/img/logo.jpg', 8, 5, 32); // Ganti logo sesuai file kamu
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 10, 'PT. PELITA INSANI MULIA', 0, 1, 'C');
        $this->SetFont('helvetica', '', 12);
        $this->Cell(0, 5, 'RUMAH SAKIT PELITA INSANI MARTAPURA  ', 0, 1, 'C');
        $this->SetFont('helvetica', '', 12);
        $this->Cell(0, 5, 'Terakreditasi KARS Versi SNARS Edisi 1 Tingkat Madya', 0, 1, 'C');
        $this->Image('../../../assets/img/bintang.png', 159, 13 , 27); // posisi X:160, Y:18, width:5mm
        $this->SetFont('helvetica', '', 10);
        $this->Cell(0, 5, 'Jl. Sekumpul No. 66 Martapura - Telp. (0511) 4722210, 4722220, Kalimantan Selatan', 0, 1, 'C');
        $html = '<span style="color:black;">Fax. (0511) 4722230, </span><span style="color:red;">Emergency Call (0511) 4722222</span> <span>Email: </span><span style="color:blue;">rs.pelitainsani@gmail.com</span>';
        $this->writeHTMLCell(0, 5, '', '', $html, 0, 1, false, true, 'C', true);
        $this->Cell(0, 5, 'Website: www.pelitainsani.com', 0, 1, 'C');
        $this->Ln(4);
        $this->Line(10, 40, 200, 40);
        $this->Ln(5);
    }
}

$pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetMargins(15, 50, 15);
$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 11);

$tglCetak = date('j').' '.bulan_indo(date('n')).' '.date('Y');

$pdf->SetFont('times', '', 11);

// ===== BAGIAN KIRI =====
$pdf->SetXY(15, 48);
$pdf->MultiCell(90, 6,
"Nomor     :       /SI/PEG/      /" . date('Y') . "
Lampiran : -
Perihal     : Izin Cuti Tahunan",
0, 'L');

// ===== BAGIAN KANAN =====
$pdf->SetXY(148, 48);
$pdf->MultiCell(80, 6,
"Martapura, $tglCetak

Kepada Yth :
Direktur RS. Pelita Insani
c.c Kabag Kepegawaian",
0, 'L');


$html = '';
$html .= '<p style="margin:0 0 4px 0; font-family: times; font-size: 11px;">Dengan hormat,</p>';
$html .= '<p style="margin:0 0 10px 0; font-family: times; font-size: 11px; line-height: 1.5;">Saya yang bertandatangan dibawah ini :</p>';

// helper untuk nilai default
function val($arr, $key){
    return isset($arr[$key]) && $arr[$key] !== '' ? htmlspecialchars($arr[$key]) : '-';
}

$html .= '<table width="100%" style="font-size:11px; font-family: times; line-height: 1.6;">';
$html .= '<tr><td width="18%"><strong>Nama</strong></td><td width="2%">:</td><td width="80%">'.val($user,'nama_lengkap').'</td></tr>';
$html .= '<tr><td><strong>NIP</strong></td><td>:</td><td>'.val($user,'nip').'</td></tr>';
$html .= '<tr><td><strong>Tempat Tgl Lahir</strong></td><td>:</td><td>'.(val($user,'tempat_lahir') != '-' ? val($user,'tempat_lahir').' ' . (isset($user['tanggal_lahir'])?tgl_indo($user['tanggal_lahir']):'') : '-').'</td></tr>';
$html .= '<tr><td><strong>Jabatan</strong></td><td>:</td><td>'.val($user,'role').'</td></tr>';
$html .= '<tr><td><strong>Pendidikan</strong></td><td>:</td><td>'.val($user,'pendidikan').'</td></tr>';
$html .= '<tr><td><strong>Alamat</strong></td><td>:</td><td>'.val($user,'alamat').'</td></tr>';
$html .= '</table>';

$mulai = tgl_indo($dataCuti['mulai_tanggal']);
$sampai = tgl_indo($dataCuti['sampai_tanggal']);
$masuk = tgl_indo($dataCuti['masuk_tanggal']);
$hari = intval($dataCuti['banyak_hari']);

$html .= '<p style="text-align:justify; line-height:1.6; font-family: times; font-size: 11px; margin: 8px 0;">Bahwa sehubungan dengan <strong>'.(isset($dataCuti['alasan']) && $dataCuti['alasan'] !== '' ? htmlspecialchars($dataCuti['alasan']) : 'keperluan keluarga').'</strong>, saya mengajukan izin cuti tahunan sebanyak <strong>'.$hari.' hari kerja</strong> yaitu sejak tanggal <strong>'.$mulai.'</strong> sampai dengan tanggal <strong>'.$sampai.'</strong>, dan saya akan masuk bekerja kembali pada tanggal <strong>'.$masuk.'</strong>.</p>';

$html .= '<p style="text-align:justify; line-height:1.6; font-family: times; font-size: 11px; margin: 12px 0;">Demikian surat permohonan ini saya sampaikan. Besar harapan saya agar permohonan ini dapat dikabulkan. Atas perhatian dan bantuan Bapak/Ibu saya ucapkan terima kasih.</p>';
// Tanda tangan tampat QR code, karena sudah ditempatkan di bagian bawah
//$html .= '<table width="100%" cellpadding="8" style="font-family: times; font-size: 11px;">';
//$html .= '<tr>';
//$html .= '<td align="center">Mengetahui,<br/>Kepala Ruangan<br/><br/><br/><u>'.(val($pimpinan,'nama_lengkap')!='-'?val($pimpinan,'nama_lengkap'):'__________________').'</u><br/>NIP. '.(isset($pimpinan['nip']) && $pimpinan['nip'] ? htmlspecialchars($pimpinan['nip']):'_______________').'</td>';
//$html .= '<td align="center">Pegawai yang bersangkutan,<br/><br/><br/><u>'.val($user,'nama_lengkap').'</u><br/>NIP. '.val($user,'nip').'</td>';
//$html .= '</tr>';
//$html .= '<tr>';
//$html .= '<td colspan="2" align="center" style="padding-top:120px;">';
//$html .= 'Mengetahui,<br/>';
//$html .= '<em>Kabag Kepegawaian, SDM & Pemasaran</em><br/>';
//$html .= '<em>RS Pelita Insani</em><br/>';
//$html .= '<div style="width:220px; height:25px; margin:8px auto 6px auto;"></div>';
//$html .= 'Nanda Alhumaira, B.Marketing<br/>NIP. 005.120813';
//$html .= '</td>';
//$html .= '</tr>';
//$html .= '</table>';


// Tanda tangan dan QR code akan dirender terpisah agar bisa ditempatkan dengan presisi di dalam kotak tanda tangan
// write main content
$pdf->writeHTML($html, true, false, true, false, '');

// ------- Render signatures with QR codes placed inside signature boxes -------
$y = $pdf->GetY();
$leftMargin = 15;
$rightMargin = 15;
$pageWidth = $pdf->getPageWidth();
$usableWidth = $pageWidth - $leftMargin - $rightMargin;
$colWidth = $usableWidth / 2;

// smaller barcode size as requested
$barcodeSize = 18; // mm
$labelAreaHeight = 12; // space for label above barcode
$gapAfterBarcode = 4;

$style = array('border'=>0, 'padding'=>0, 'fgcolor'=>array(0,0,0), 'bgcolor'=>false);

// starting Y for labels
$labelY = $y + 6;

// Left column (Kepala Ruangan)
$pdf->SetFont('times', '', 11);
$pdf->SetXY($leftMargin, $labelY);
$pdf->MultiCell($colWidth, 5, "Mengetahui:\nKepala Ruangan", 0, 'C');
$leftBarcodeX = $leftMargin + ($colWidth - $barcodeSize) / 2;
$leftBarcodeY = $labelY + $labelAreaHeight;
$pimpinan_nip = isset($pimpinan['nip']) && $pimpinan['nip'] ? $pimpinan['nip'] : '';
if ($pimpinan_nip) {
    $pdf->write2DBarcode($pimpinan_nip, 'QRCODE,H', $leftBarcodeX, $leftBarcodeY, $barcodeSize, $barcodeSize, $style, 'N');
}
$leftNameY = $leftBarcodeY + $barcodeSize + $gapAfterBarcode;
$pimpinan_name = isset($pimpinan['nama_lengkap']) && $pimpinan['nama_lengkap'] ? htmlspecialchars($pimpinan['nama_lengkap']) : '__________________';
$pimpinan_nip_label = isset($pimpinan['nip']) && $pimpinan['nip'] ? htmlspecialchars($pimpinan['nip']) : '_______________';
$pdf->SetXY($leftMargin, $leftNameY);
$pdf->MultiCell($colWidth, 5, "{$pimpinan_name}\nNIP. {$pimpinan_nip_label}", 0, 'C');

// Right column (Pegawai)
$pdf->SetXY($leftMargin + $colWidth, $labelY);
$pdf->MultiCell($colWidth, 5, "Pegawai yang bersangkutan:", 0, 'C');
$rightBarcodeX = $leftMargin + $colWidth + ($colWidth - $barcodeSize) / 2;
$rightBarcodeY = $labelY + $labelAreaHeight;
$user_nip = isset($user['nip']) && $user['nip'] ? $user['nip'] : '';
if ($user_nip) {
    $pdf->write2DBarcode($user_nip, 'QRCODE,H', $rightBarcodeX, $rightBarcodeY, $barcodeSize, $barcodeSize, $style, 'N');
}
$rightNameY = $rightBarcodeY + $barcodeSize + $gapAfterBarcode;
$user_name = isset($user['nama_lengkap']) && $user['nama_lengkap'] ? htmlspecialchars($user['nama_lengkap']) : '__________________';
$user_nip_label = isset($user['nip']) && $user['nip'] ? htmlspecialchars($user['nip']) : '_______________';
$pdf->SetXY($leftMargin + $colWidth, $rightNameY);
$pdf->MultiCell($colWidth, 5, "{$user_name}\nNIP. {$user_nip_label}", 0, 'C');

// Middle Kabag signature centered (below the two)
$midY = max($leftNameY, $rightNameY) + 18;
$pdf->SetXY($leftMargin, $midY);
$pdf->MultiCell($usableWidth, 5, "Mengetahui,\nKabag Kepegawaian, SDM & Pemasaran\nRS Pelita Insani\n\n\nNanda Alhumaira, B.Marketing\nNIP. 005.120813", 0, 'C');

if (ob_get_length()) ob_end_clean();
$pdf->Output('surat_cuti_'.$user['nama_lengkap'].'.pdf', 'I');
?>
