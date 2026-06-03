<?php
error_reporting(0);
ini_set('display_errors', 0);
if (ob_get_level()) {
    ob_clean();
}
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
    $barcodeSize = 10.6;
    $barcodeX = $x + (($width - $barcodeSize) / 2);
    $barcodeY = $y + 5;
    $style = [
        'border' => 0,
        'padding' => 0,
        'fgcolor' => [0, 0, 0],
        'bgcolor' => false
    ];

    $pdf->SetFont('helvetica', '', 6.4);
    $pdf->SetXY($x, $y);
    $pdf->MultiCell($width, 3.5, $judul, 0, 'C');

    if ($payloadQr !== '') {
        $pdf->write2DBarcode($payloadQr, 'QRCODE,H', $barcodeX, $barcodeY, $barcodeSize, $barcodeSize, $style, 'N');
    }

    $pdf->SetFont('helvetica', 'U', 5.7);
    $pdf->SetXY($x, $barcodeY + $barcodeSize + 1.4);
    $pdf->MultiCell($width, 2.8, '(' . $nama . ')', 0, 'C');

    if ($nip !== '') {
        $pdf->SetFont('helvetica', '', 5);
        $pdf->SetXY($x, $barcodeY + $barcodeSize + 4.2);
        $pdf->MultiCell($width, 2.8, 'NIP. ' . $nip, 0, 'C');
    }
}

// Mulai PDF
class MYPDF extends TCPDF {
    public function Header() {
        // Logo di kiri; blok teks di-center optik sebagai satu grup di sebelah kanan logo
        $textLeft  = 23;              // mulai tepat setelah logo
        $textWidth = 97.9 - $textLeft; // sampai margin kanan (~74.9 mm)

        $this->Image('../../../assets/img/logo.jpg', 7.1, 6.3, 15.6);

        $this->SetY(5);
        $this->SetX($textLeft);
        $this->SetFont('helvetica', 'B', 7);
        $this->Cell($textWidth, 4.2, 'PT. PELITA INSANI MULIA', 0, 1, 'C');

        $this->SetX($textLeft);
        $this->SetFont('helvetica', '', 6.4);
        $this->Cell($textWidth, 2.8, 'RUMAH SAKIT PELITA INSANI MARTAPURA', 0, 1, 'C');

        // "Terakreditasi ..." + bintang di-center sebagai satu kesatuan
        $this->SetFont('helvetica', '', 5.7);
        $akreditasi = 'Terakreditasi KARS Versi SNARS Edisi 1 Tingkat Madya';
        $bintangW = 9;
        $gap = 1.5;
        $akreditasiW = $this->GetStringWidth($akreditasi);
        $unitW = $akreditasiW + $gap + $bintangW;
        $unitY = $this->GetY();
        $unitX = (($textLeft + 97.9) - $unitW) / 2; // center di dalam region teks
        $this->SetXY($unitX, $unitY);
        $this->Cell($akreditasiW, 2.8, $akreditasi, 0, 0, 'L');
        $this->Image('../../../assets/img/bintang.png', $unitX + $akreditasiW + $gap, $unitY - 0.2, $bintangW);
        $this->Ln(2.8);

        $this->SetX($textLeft);
        $this->SetFont('helvetica', '', 5);
        $this->Cell($textWidth, 2.8, 'Jl. Sekumpul No. 66 Martapura - Telp. (0511) 4722210, 4722220, Kalimantan Selatan', 0, 1, 'C');

        $html = '<span style="color:black;">Fax. (0511) 4722230, </span><span style="color:red;">Emergency Call (0511) 4722222</span> <span>Email: </span><span style="color:blue;">rs.pelitainsani@gmail.com</span>';
        $this->writeHTMLCell($textWidth, 2.8, $textLeft, '', $html, 0, 1, false, true, 'C', true);

        $this->SetX($textLeft);
        $this->Cell($textWidth, 2.8, 'Website: www.pelitainsani.com', 0, 1, 'C');

        $this->Ln(0.7);
        $this->Line(7.1, 23.3, 97.6, 23.3);
        $this->Ln(3.5);
    }
}

$pageFormat = [105, 148]; // A6 portrait dalam millimeter
$pdf = new MYPDF('P', 'mm', $pageFormat, true, 'UTF-8', false);
$pdf->SetTitle('Surat Perintah Lembur On Call');
$pdf->setViewerPreferences(['PrintScaling' => 'None']); // jangan di-scale saat dicetak (tetap A6 walau kertas A4)
$pdf->SetMargins(7.1, 26.2, 7.1); // Margin disesuaikan untuk A6
$pdf->SetHeaderMargin(3.5);
$pdf->SetAutoPageBreak(true, 7.1);
$pdf->AddPage('P', $pageFormat);
$pdf->SetFont('helvetica', '', 6.4);
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
<tr><td>Jam</td><td>:</td><td style="font-size:6px; white-space:nowrap;">' . date('h:i A', strtotime($dataLembur['jam_mulai'])) . ' - ' . date('h:i A', strtotime($dataLembur['jam_selesai'])) . ($totalJamLembur !== '' ? ' (Total: ' . $totalJamLembur . ')' : '') . '</td></tr>
</table>';

// Kegiatan
$html .= '<p>Pekerjaan yang dilakukan:</p><ol>';
while ($k = mysqli_fetch_assoc($qKegiatan)) {
    $html .= '<li>' . htmlspecialchars($k['kegiatan']) . '</li>';
}
$html .= '</ol><br>';

// Lokasi dan Tanggal
$tglCetak = date('d-m-Y');
$html .= '<p style="text-align:right;">Martapura, '.$tglCetak.'</p><br>';

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

$signatureY = max($pdf->GetY() + 0.7, 84.9);
if ($signatureY + 38.9 > ($pdf->getPageHeight() - 7.1)) {
    $pdf->AddPage('P', $pageFormat);
    $signatureY = 29.7;
}

$leftX = 7.1;
$rightX = 52.3;
$colWidth = 45.3;

tulis_ttd_qr($pdf, $leftX, $signatureY, $colWidth, 'Penerima Tugas,', $staff['nama_lengkap'], $staffNip, $staffQr);
tulis_ttd_qr($pdf, $rightX, $signatureY, $colWidth, 'Pemberi Tugas,', $pimpinan['nama_lengkap'], $pimpinanNip, $pimpinanQr);

$pdf->SetFont('helvetica', '', 6.4);
$pdf->SetXY(7.1, $signatureY + 28.3);
$pdf->MultiCell(90.5, 3.5, "Mengetahui,\n\n\n.......................................", 0, 'C');

if (ob_get_length()) ob_end_clean();
$pdf->Output('surat_lembur_'.$staff['nama_lengkap'].'.pdf', 'I');
