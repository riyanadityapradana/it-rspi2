<?php
require_once("../config/koneksi.php");
if (!isset($_GET['id'])) {
    header('Location: dashboard_staff.php?unit=barang&err=Barang tidak ditemukan!');
    exit;
}
$barang_id = $_GET['id'];
$q = mysqli_query($config, "DELETE FROM tb_barang WHERE barang_id='".mysqli_real_escape_string($config, $barang_id)."'");
if ($q) {
    header('Location: dashboard_staff.php?unit=barang&msg=Barang berhasil dihapus!');
    exit;
} else {
    header('Location: dashboard_staff.php?unit=barang&err=Gagal menghapus barang!');
    exit;
}
