<?php
require_once("../config/koneksi.php");

if (!isset($_GET['id'])) {
    header('Location: dashboard_staff.php?unit=pemindahan&err=Data pemindahan tidak ditemukan!');
    exit;
}

$id_pemindahan = $_GET['id'];

// Cek apakah data pemindahan ada
$cek = mysqli_query($config, "SELECT 1 FROM tb_pemindahan_barang WHERE id_pemindahan='".mysqli_real_escape_string($config, $id_pemindahan)."'");
if (mysqli_num_rows($cek) == 0) {
    header('Location: dashboard_staff.php?unit=pemindahan&err=Data pemindahan tidak ditemukan!');
    exit;
}

// Hapus data pemindahan
$q = mysqli_query($config, "DELETE FROM tb_pemindahan_barang WHERE id_pemindahan='".mysqli_real_escape_string($config, $id_pemindahan)."'");
if ($q) {
    header('Location: dashboard_staff.php?unit=pemindahan&msg=Data pemindahan berhasil dihapus!');
    exit;
} else {
    header('Location: dashboard_staff.php?unit=pemindahan&err=Gagal menghapus data pemindahan!');
    exit;
}
