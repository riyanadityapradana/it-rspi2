<?php
ob_clean();
error_reporting(0);
ini_set('display_errors', 0);
require_once('../../../config/koneksi.php');
require_once('../../../library/tcpdf/tcpdf.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data lembur
$qLembur = mysqli_query($config, "SELECT * FROM tb_lembur WHERE id_lembur = '$id'");
$dataLembur = mysqli_fetch_assoc($qLembur);

$id_staff    = $dataLembur['id_staff'];
$id_pimpinan = $dataLembur['id_pimpinan'];

// Ambil data user (staff & pimpinan)
$staff    = mysqli_fetch_assoc(mysqli_query($config, "SELECT * FROM tb_user WHERE id_user = '$id_staff'"));
$pimpinan = mysqli_fetch_assoc(mysqli_query($config, "SELECT * FROM tb_user WHERE id_user = '$id_pimpinan'"));

// Ambil kegiatan lembur
$qKegiatan = mysqli_query($config, "SELECT * FROM tb_kegiatan_lembur WHERE id_lembur = '$id'");

function tgl_indo_hari($tanggal) {
    $hari = [
        'Sunday'    => 'Minggu',
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu'
    ];
    $bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $date = date_create($tanggal);
    $hari_text = $hari[date_format($date, 'l')];
    $tanggal_text = date_format($date, 'j');
    $bulan_text = $bulan[(int)date_format($date, 'n')];
    $tahun = date_format($date, 'Y');
    return "$hari_text, $tanggal_text $bulan_text $tahun";
}

function ambil_tanggal_verifikasi($dataLembur) {
    foreach (['waktu_verifikasi', 'tanggal_verifikasi', 'tgl_verifikasi'] as $field) {
        if (isset($dataLembur[$field]) && $dataLembur[$field] !== '') {
            return date('Y-m-d', strtotime($dataLembur[$field]));
        }
    }

    return date('Y-m-d');
}

function buat_payload_qr_lembur($label, $nip, $tanggal, $idLembur) {
    return $label . '|NIP:' . $nip . '|TANGGAL:' . $tanggal . '|ID_LEMBUR:' . $idLembur;
}

function hitung_total_jam_lembur($tanggal, $jamMulai, $jamSelesai) {
    $mulai = strtotime($tanggal . ' ' . $jamMulai);
    $selesai = strtotime($tanggal . ' ' . $jamSelesai);

    if ($mulai === false || $selesai === false) {
        return '';
    }

    if ($selesai < $mulai) {
        $selesai = strtotime('+1 day', $selesai);
    }

    $totalMenit = (int)(($selesai - $mulai) / 60);
    $jam = intdiv($totalMenit, 60);
    $menit = $totalMenit % 60;

    if ($menit === 0) {
        return $jam . ' JAM';
    }

    return $jam . ' JAM ' . $menit . ' MENIT';
}

function tulis_ttd_qr($pdf, $x, $y, $width, $judul, $nama, $nip, $payloadQr = '') {
    $barcodeSize = 15;
    $barcodeX = $x + (($width - $barcodeSize) / 2);
    $barcodeY = $y + 7;
    $style = [
        'border' => 0,
        'padding' => 0,
        'fgcolor' => [0, 0, 0],
        'bgcolor' => false
    ];

    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetXY($x, $y);
    $pdf->MultiCell($width, 5, $judul, 0, 'C');

    if ($payloadQr !== '') {
        $pdf->write2DBarcode($payloadQr, 'QRCODE,H', $barcodeX, $barcodeY, $barcodeSize, $barcodeSize, $style, 'N');
    }

    $pdf->SetFont('helvetica', 'U', 8);
    $pdf->SetXY($x, $barcodeY + $barcodeSize + 2);
    $pdf->MultiCell($width, 4, '(' . $nama . ')', 0, 'C');

    if ($nip !== '') {
        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetXY($x, $barcodeY + $barcodeSize + 6);
        $pdf->MultiCell($width, 4, 'NIP. ' . $nip, 0, 'C');
    }
}

// Mulai PDF
class MYPDF extends TCPDF {
    public function Header() {
        // Logo dan header - disesuaikan untuk A5
        $this->Image('../../../assets/img/logo.jpg', 3, 6, 22);
        $this->SetY(7);
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(0, 6, 'PT. PELITA INSANI MULIA', 0, 1, 'C');
        $this->SetFont('helvetica', '', 9);
        $this->Cell(0, 4, 'RUMAH SAKIT PELITA INSANI MARTAPURA', 0, 1, 'C');
        $this->SetFont('helvetica', '', 8);
        $this->Cell(0, 4, 'Terakreditasi KARS Versi SNARS Edisi 1 Tingkat Madya', 0, 1, 'C');
        $this->Image('../../../assets/img/bintang.png', 110, 16, 16);
        $this->SetFont('helvetica', '', 7);
        $this->Cell(0, 4, 'Jl. Sekumpul No. 66 Martapura - Telp. (0511) 4722210, 4722220, Kalimantan Selatan', 0, 1, 'C');
        $html = '<span style="color:black;">Fax. (0511) 4722230, </span><span style="color:red;">Emergency Call (0511) 4722222</span> <span>Email: </span><span style="color:blue;">rs.pelitainsani@gmail.com</span>';
        $this->writeHTMLCell(0, 4, '', '', $html, 0, 1, false, true, 'C', true);
        $this->Cell(0, 4, 'Website: www.pelitainsani.com', 0, 1, 'C');
        $this->Ln(1);
        $this->Line(10, 33, 138, 33);
        $this->Ln(5);
    }
}

$pageFormat = [148, 210]; // A5 portrait dalam millimeter
$pdf = new MYPDF('P', 'mm', $pageFormat, true, 'UTF-8', false);
$pdf->SetTitle('Surat Perintah Lembur On Call');
$pdf->SetMargins(10, 37, 10); // Margin disesuaikan untuk A5
$pdf->SetHeaderMargin(5);
$pdf->SetAutoPageBreak(true, 10);
$pdf->AddPage('P', $pageFormat);
$pdf->SetFont('helvetica', '', 9);
$totalJamLembur = hitung_total_jam_lembur($dataLembur['tanggal_lembur'], $dataLembur['jam_mulai'], $dataLembur['jam_selesai']);

// Judul
$html = '<h3 style="text-align:center;">SURAT PERINTAH LEMBUR "ON CALL"</h3><br>';

// Info pemberi tugas
$html .= '<p>Dengan ini Saya:</p>';
$html .= '<table width="100%" cellpadding="2">
<tr>
    <td width="24%">Nama</td>
    <td width="3%">:</td><td width="73%">'.$pimpinan['nama_lengkap'].'</td>
</tr>
<tr>
<td>Bagian / Jabatan</td><td>:</td>
<td>'.$pimpinan['role'].'</td>
</tr>
</table>';

$html .= '<p>Memberikan Perintah Lembur "On Call" Kepada:</p>';
$html .= '<table width="100%" cellpadding="2">
<tr><td width="24%">Nama</td><td width="3%">:</td><td width="73%">'.$staff['nama_lengkap'].'</td></tr>
<tr><td>Jabatan</td><td>:</td><td>'.$staff['role'].'</td></tr>
<tr><td>Hari / Tanggal</td><td>:</td><td>' . tgl_indo_hari($dataLembur['tanggal_lembur']) . '</td></tr>
<tr><td>Jam</td><td>:</td><td style="font-size:9px; white-space:nowrap;">' . date('h:i A', strtotime($dataLembur['jam_mulai'])) . ' - ' . date('h:i A', strtotime($dataLembur['jam_selesai'])) . ($totalJamLembur !== '' ? ' (Total: ' . $totalJamLembur . ')' : '') . '</td></tr>
</table>';

// Kegiatan
$html .= '<p>Pekerjaan yang dilakukan:</p><ol>';
while ($k = mysqli_fetch_assoc($qKegiatan)) {
    $html .= '<li>' . htmlspecialchars($k['kegiatan']) . '</li>';
}
$html .= '</ol><br>';

// Lokasi dan Tanggal
$tglCetak = date('d-m-Y');
$html .= '<p style="text-align:center;">Martapura, '.$tglCetak.'</p><br>';

$pdf->writeHTML($html, true, false, true, false, '');

$tanggalLembur = isset($dataLembur['tanggal_lembur']) ? $dataLembur['tanggal_lembur'] : date('Y-m-d');
$tanggalVerifikasi = ambil_tanggal_verifikasi($dataLembur);
$staffNip = isset($staff['nip']) ? $staff['nip'] : '';
$pimpinanNip = isset($pimpinan['nip']) ? $pimpinan['nip'] : '';
$staffQr = buat_payload_qr_lembur('PENERIMA_TUGAS', $staffNip, $tanggalLembur, $id);
$pimpinanQr = '';

if (isset($dataLembur['status_lembur']) && $dataLembur['status_lembur'] == 'Diterima' && $pimpinanNip !== '') {
    $pimpinanQr = buat_payload_qr_lembur('PEMBERI_TUGAS', $pimpinanNip, $tanggalVerifikasi, $id);
}

$signatureY = max($pdf->GetY() + 1, 120);
if ($signatureY + 55 > ($pdf->getPageHeight() - 10)) {
    $pdf->AddPage('P', $pageFormat);
    $signatureY = 42;
}

$leftX = 10;
$rightX = 74;
$colWidth = 64;

tulis_ttd_qr($pdf, $leftX, $signatureY, $colWidth, 'Penerima Tugas,', $staff['nama_lengkap'], $staffNip, $staffQr);
tulis_ttd_qr($pdf, $rightX, $signatureY, $colWidth, 'Pemberi Tugas,', $pimpinan['nama_lengkap'], $pimpinanNip, $pimpinanQr);

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(10, $signatureY + 40);
$pdf->MultiCell(128, 5, "Mengetahui,\n\n\n.......................................", 0, 'C');

if (ob_get_length()) ob_end_clean();
$pdf->Output('surat_lembur_'.$staff['nama_lengkap'].'.pdf', 'I');
?>
