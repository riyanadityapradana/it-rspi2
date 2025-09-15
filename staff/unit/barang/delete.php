<?php
require_once("../config/koneksi.php");
if (!isset($_GET['id'])) {
    header('Location: dashboard_staff.php?unit=barang&err=Barang tidak ditemukan!');
    exit;
}
$barang_id = $_GET['id'];
// Ambil nama file foto sebelum hapus
$foto = null;
$getFoto = mysqli_query($config, "SELECT foto FROM tb_barang WHERE barang_id='".mysqli_real_escape_string($config, $barang_id)."'");
if ($getFoto && mysqli_num_rows($getFoto) > 0) {
    $row = mysqli_fetch_assoc($getFoto);
    $foto = $row['foto'];
}
$q = mysqli_query($config, "DELETE FROM tb_barang WHERE barang_id='".mysqli_real_escape_string($config, $barang_id)."'");
if ($q) {
    // Hapus file foto jika ada
    if (!empty($foto) && file_exists(__DIR__ . '/foto-barang/' . $foto)) {
        unlink(__DIR__ . '/foto-barang/' . $foto);
    }
    header('Location: dashboard_staff.php?unit=barang&msg=Barang berhasil dihapus!');
    exit;
} else {
    header('Location: dashboard_staff.php?unit=barang&err=Gagal menghapus barang!');
    exit;
}
