<?php
require_once("../config/koneksi.php");
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kepala Ruangan') {
    header('Location: ../main_login/form_login.php?error=Akses ditolak!');
    exit;
}
if (!isset($_GET['id'])) {
    header('Location: dashboard_admin.php?unit=pengajuan&err=Pengajuan tidak ditemukan!');
    exit;
}
$id_pengajuan = intval($_GET['id']);
$id_kepala = $_SESSION['id_user'];
$q = mysqli_query($config, "UPDATE tb_pengajuan_barang SET status='Ditolak', id_kepala='$id_kepala', waktu_acc=NOW() WHERE id_pengajuan='$id_pengajuan' AND status='Menunggu'");
if ($q && mysqli_affected_rows($config) > 0) {
    header('Location: dashboard_admin.php?unit=pengajuan&msg=Pengajuan berhasil ditolak!');
    exit;
} else {
    header('Location: dashboard_admin.php?unit=pengajuan&err=Gagal menolak pengajuan!');
    exit;
} 