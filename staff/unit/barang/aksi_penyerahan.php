<?php
require_once("../../../config/koneksi.php");
require_once(__DIR__ . "/barang_helpers.php");
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../dashboard_staff.php?unit=barang');
    exit;
}

$barang_id = isset($_POST['barang_id']) ? intval($_POST['barang_id']) : 0;
$lokasi_id = isset($_POST['lokasi_id'][0]) ? intval($_POST['lokasi_id'][0]) : 0;
$kondisi = isset($_POST['kondisi'][0]) ? trim($_POST['kondisi'][0]) : '';
$keterangan = isset($_POST['keterangan_unit'][0]) ? trim($_POST['keterangan_unit'][0]) : '';
$unit_index = isset($_POST['unit_index']) ? intval($_POST['unit_index']) : 0;

if ($barang_id <= 0 || $lokasi_id <= 0) {
    header('Location: ../../dashboard_staff.php?unit=barang&err=Data penyerahan tidak lengkap');
    exit;
}

$q_jumlah = mysqli_query($config, "SELECT jumlah FROM tb_barang WHERE barang_id='$barang_id'");
$row_jumlah = mysqli_fetch_assoc($q_jumlah);
$jumlah = isset($row_jumlah['jumlah']) ? intval($row_jumlah['jumlah']) : 0;

$q_penyerahan_count = mysqli_query($config, "SELECT COUNT(*) as count FROM tb_penyerahan WHERE barang_id='$barang_id'");
$row_count = mysqli_fetch_assoc($q_penyerahan_count);
$jumlah_penyerahan_current = isset($row_count['count']) ? intval($row_count['count']) : 0;

if ($jumlah <= 0) {
    header('Location: ../../dashboard_staff.php?unit=barang&err=Jumlah barang tidak valid');
    exit;
}

if ($jumlah_penyerahan_current >= $jumlah) {
    header('Location: ../../dashboard_staff.php?unit=barang&err=Semua unit sudah diserahkan!');
    exit;
}

$penyerahan_ids = [];
$q_ids = mysqli_query($config, "SELECT penyerahan_id FROM tb_penyerahan WHERE barang_id='$barang_id' ORDER BY penyerahan_id ASC");
while ($row = mysqli_fetch_assoc($q_ids)) {
    $penyerahan_ids[] = $row['penyerahan_id'];
}

$target_id = isset($penyerahan_ids[$unit_index]) ? $penyerahan_ids[$unit_index] : null;
if ($target_id) {
    $query = mysqli_query($config, "UPDATE tb_penyerahan SET lokasi_id='$lokasi_id', kondisi='$kondisi', keterangan='$keterangan' WHERE penyerahan_id='$target_id'");
} else {
    $query = mysqli_query($config, "INSERT INTO tb_penyerahan (barang_id, lokasi_id, kondisi, keterangan) VALUES ('$barang_id', '$lokasi_id', '$kondisi', '$keterangan')");
}

if ($query) {
    $snapshot_ok = barang_sync_snapshot($config, $barang_id, ['lokasi_id' => $lokasi_id, 'kondisi' => $kondisi]);
    if (!$snapshot_ok) {
        header('Location: ../../dashboard_staff.php?unit=barang&err=Gagal sinkronisasi data barang: ' . mysqli_error($config));
        exit;
    }

    $next_unit = $unit_index + 1;
    if ($next_unit < $jumlah) {
        header('Location: ../../dashboard_staff.php?unit=barang&msg=Unit ' . ($unit_index + 1) . ' berhasil diserahkan, lanjut unit ' . ($next_unit + 1) . '&continue_barang_id=' . $barang_id . '&next_unit=' . $next_unit);
        exit;
    }

    header('Location: ../../dashboard_staff.php?unit=barang&msg=Semua unit berhasil diserahkan!');
    exit;
}

header('Location: ../../dashboard_staff.php?unit=barang&err=Gagal menyerahkan unit ' . ($unit_index + 1) . ': ' . mysqli_error($config));
exit;
