<?php
ob_clean();
error_reporting(0);
ini_set('display_errors', 0);
require_once('../../../config/koneksi.php');
require_once('../../../library/tcpdf/tcpdf.php');

$id = $_GET['id'];

// Ambil data lembur
$qLembur = mysqli_query($config, "SELECT * FROM tb_lembur WHERE id_lembur = '$id'");
$dataLembur = mysqli_fetch_assoc($qLembur);

$id_staff    = $dataLembur['id_staff'];
$id_pimpinan = $dataLembur['id_pimpinan'];

// Ambil data user (staff & pimpinan)
$staff    = mysqli_fetch_assoc(mysqli_query($config, "SELECT * FROM tb_user WHERE kode_user = '$id_staff'"));
$pimpinan = mysqli_fetch_assoc(mysqli_query($config, "SELECT * FROM tb_user WHERE kode_user = '$id_pimpinan'"));

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

// Mulai PDF
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
        $this->Image('../../../assets/img/bintang.png', 160, 15 , 15); // posisi X:160, Y:18, width:5mm
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
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 11);

// Judul
$html = '<h3 style="text-align:center;">SURAT PERINTAH LEMBUR "ON CALL"</h3><br>';

// Info pemberi tugas
$html .= '<p>Dengan ini Saya:</p>';
$html .= '<table cellpadding="2">
<tr><td width="120">Nama</td><td width="10">:</td><td>'.$pimpinan['nama_karyawan'].'</td></tr>
<tr><td>Bagian / Jabatan</td><td>:</td><td>'.$pimpinan['level'].'</td></tr>
</table>';

$html .= '<p>Memberikan Perintah Lembur "On Call" Kepada:</p>';
$html .= '<table cellpadding="2">
<tr><td width="120">Nama</td><td width="10">:</td><td>'.$staff['nama_karyawan'].'</td></tr>
<tr><td>Jabatan</td><td>:</td><td>'.$staff['level'].'</td></tr>
<tr><td>Hari / Tanggal</td><td>:</td><td>' . tgl_indo_hari($dataLembur['tanggal_lembur']) . '</td></tr>
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

// Tanda tangan
$html .= '
<table width="100%" cellpadding="10">
<tr>
    <td align="center">Penerima Tugas,<br><br><br><br><u>('.$staff['nama_karyawan'].')</u></td>
    <td align="center">Pemberi Tugas,<br><br><br><br><u>('.$pimpinan['nama_karyawan'].')</u></td>
</tr>
<tr>
    <td colspan="2" align="center"><br><br>Mengetahui,<br><br><br><br><br><u>.......................................</u></td>
</tr>
</table>
';

$pdf->writeHTML($html, true, false, true, false, '');
if (ob_get_length()) ob_end_clean();
$pdf->Output('surat_lembur_'.$staff['nama_karyawan'].'.pdf', 'I');
?>
