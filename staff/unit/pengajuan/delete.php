<?php
require_once("../config/koneksi.php");
if (!isset($_GET['id'])) {
    header('Location: dashboard_staff.php?unit=pengajuan&err=Pengajuan tidak ditemukan!');
    exit;
}
$id_pengajuan = $_GET['id'];
$id_staff = $_SESSION['id_user'];
// Cek status
$q = mysqli_query($config, "SELECT status FROM tb_pengajuan_barang WHERE id_pengajuan='".intval($id_pengajuan)."' AND id_staff='$id_staff'");
$data = mysqli_fetch_assoc($q);
if (!$data) {
    header('Location: dashboard_staff.php?unit=pengajuan&err=Pengajuan tidak ditemukan!');
    exit;
}
if ($data['status'] !== 'Menunggu') {
    header('Location: dashboard_staff.php?unit=pengajuan&err=Pengajuan tidak bisa dihapus!');
    exit;
}
$q = mysqli_query($config, "DELETE FROM tb_pengajuan_barang WHERE id_pengajuan='".intval($id_pengajuan)."' AND id_staff='$id_staff'");
if ($q) {
    header('Location: dashboard_staff.php?unit=pengajuan&msg=Pengajuan barang berhasil dihapus!');
    exit;
} else {
    header('Location: dashboard_staff.php?unit=pengajuan&err=Gagal menghapus pengajuan!');
    exit;
}
