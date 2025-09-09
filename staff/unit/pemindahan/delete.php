<?php
require_once("../config/koneksi.php");

if (!isset($_GET['id'])) {
    header('Location: dashboard_staff.php?unit=pemindahan&err=Data mutasi tidak ditemukan!');
    exit;
}

$mutasi_id = $_GET['id'];

// Cek apakah data mutasi ada
$cek = mysqli_query($config, "SELECT 1 FROM tb_mutasi_barang WHERE mutasi_id='".mysqli_real_escape_string($config, $mutasi_id)."'");
if (mysqli_num_rows($cek) == 0) {
    header('Location: dashboard_staff.php?unit=pemindahan&err=Data mutasi tidak ditemukan!');
    exit;
}

// Hapus data mutasi
$q = mysqli_query($config, "DELETE FROM tb_mutasi_barang WHERE mutasi_id='".mysqli_real_escape_string($config, $mutasi_id)."'");
if ($q) {
    header('Location: dashboard_staff.php?unit=pemindahan&msg=Data mutasi berhasil dihapus!');
    exit;
} else {
    header('Location: dashboard_staff.php?unit=pemindahan&err=Gagal menghapus data mutasi!');
    exit;
}
