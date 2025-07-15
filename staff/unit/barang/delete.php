<?php
require_once("../config/koneksi.php");
if (!isset($_GET['id'])) {
    header('Location: dashboard_staff.php?unit=barang&err=Barang tidak ditemukan!');
    exit;
}
$kode_barang = $_GET['id'];
$q = mysqli_query($config, "DELETE FROM tb_barang WHERE kode_barang='".mysqli_real_escape_string($config, $kode_barang)."'");
if ($q) {
    header('Location: dashboard_staff.php?unit=barang&msg=Barang berhasil dihapus!');
    exit;
} else {
    header('Location: dashboard_staff.php?unit=barang&err=Gagal menghapus barang!');
    exit;
}
