<?php
require_once("../../../config/koneksi.php");
require_once(__DIR__ . "/barang_helpers.php");
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../dashboard_staff.php?unit=barang');
    exit;
}

$barang_id = isset($_POST['barang_id']) ? intval($_POST['barang_id']) : 0;
$lokasi_asal = isset($_POST['lokasi_asal']) ? intval($_POST['lokasi_asal']) : 0;
$lokasi_tujuan = isset($_POST['lokasi_tujuan']) ? intval($_POST['lokasi_tujuan']) : 0;
$tanggal_mutasi = isset($_POST['tanggal_mutasi']) ? mysqli_real_escape_string($config, $_POST['tanggal_mutasi']) : date('Y-m-d');
$keterangan = isset($_POST['keterangan']) ? mysqli_real_escape_string($config, trim($_POST['keterangan'])) : '';
$id_user = isset($_SESSION['id_user']) ? intval($_SESSION['id_user']) : 0;

if ($barang_id <= 0 || $lokasi_tujuan <= 0) {
    header('Location: ../../dashboard_staff.php?unit=barang&err=Data pemindahan tidak lengkap');
    exit;
}

$id_user_sql = $id_user > 0 ? "'{$id_user}'" : "NULL";

mysqli_begin_transaction($config);
$ins = mysqli_query($config, "INSERT INTO tb_mutasi_barang (barang_id, lokasi_asal, lokasi_tujuan, tanggal_mutasi, id_user, keterangan) VALUES ('{$barang_id}', '{$lokasi_asal}', '{$lokasi_tujuan}', '{$tanggal_mutasi}', {$id_user_sql}, '{$keterangan}')");
if ($ins) {
    if (barang_sync_snapshot($config, $barang_id, ['lokasi_id' => $lokasi_tujuan, 'kondisi' => 'Bekas'])) {
        mysqli_commit($config);
        header('Location: ../../dashboard_staff.php?unit=barang&msg=Pemindahan barang berhasil disimpan');
        exit;
    }

    mysqli_rollback($config);
    header('Location: ../../dashboard_staff.php?unit=barang&err=Gagal update snapshot barang: ' . mysqli_error($config));
    exit;
}

mysqli_rollback($config);
header('Location: ../../dashboard_staff.php?unit=barang&err=Gagal menyimpan pemindahan: ' . mysqli_error($config));
exit;
