<?php
error_reporting(0);
ini_set('display_errors', 0);
if (ob_get_level() === 0) {
    ob_start();
}

require_once("../../../config/koneksi.php");
require_once("../../../library/tcpdf/tcpdf.php");

$barang_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$mode = isset($_GET['mode']) ? strtolower(trim($_GET['mode'])) : 'qr';
$qty = isset($_GET['qty']) ? intval($_GET['qty']) : 1;
if ($qty < 1) $qty = 1;
if ($qty > 100) $qty = 100;

if ($barang_id <= 0) {
    echo 'ID barang tidak valid';
    exit;
}

$barang_q = mysqli_query(
    $config,
    "SELECT barang_id, kode_inventaris, nama_barang FROM tb_barang WHERE barang_id='{$barang_id}' LIMIT 1"
);
$barang = $barang_q ? mysqli_fetch_assoc($barang_q) : null;
if (!$barang) {
    echo 'Data barang tidak ditemukan';
    exit;
}

$payload = (string) intval($barang['barang_id']);
$kode = trim((string) $barang['kode_inventaris']);
$nama = trim((string) $barang['nama_barang']);
$kode_label = $kode !== '' ? $kode : 'Tanpa kode inventaris';

function label_short_text($text, $limit)
{
    $text = trim((string) $text);
    if (strlen($text) <= $limit) {
        return $text;
    }
    return substr($text, 0, max(0, $limit - 3)) . '...';
}

$pdf = new TCPDF('P', 'mm', array(30, 30), true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(1, 1, 1);
$pdf->SetAutoPageBreak(false, 0);
$pdf->setCellPaddings(0, 0, 0, 0);

$style = array(
    'border' => 0,
    'padding' => 0,
    'fgcolor' => array(0, 0, 0),
    'bgcolor' => false,
    'text' => false,
    'position' => 'C'
);

for ($i = 0; $i < $qty; $i++) {
    $pdf->AddPage();

    if ($mode === 'code128' || $mode === 'barcode') {
        $pdf->write1DBarcode($payload, 'C128', 1.5, 4, 27, 10, 0.28, $style, 'N');
        $pdf->SetXY(1, 16);
    } else {
        $pdf->write2DBarcode($payload, 'QRCODE,M', 5.5, 1.5, 19, 19, $style, 'N');
        $pdf->SetXY(1, 21);
    }

    $pdf->SetFont('helvetica', 'B', 5.2);
    $pdf->MultiCell(28, 3, 'ID: ' . label_short_text($payload, 24), 0, 'C', false, 1);
    $pdf->SetFont('helvetica', '', 4.4);
    $pdf->SetX(1);
    $pdf->MultiCell(28, 3, label_short_text($kode_label, 34), 0, 'C', false, 1);
    $pdf->SetX(1);
    $pdf->MultiCell(28, 3, label_short_text($nama, 34), 0, 'C', false, 1);
}

$safe_name = preg_replace('/[^A-Za-z0-9_\-]/', '_', $payload);
if (ob_get_level() > 0 && ob_get_length() > 0) {
    ob_clean();
}
$pdf->Output('label_barang_' . $safe_name . '.pdf', 'I');
exit;
