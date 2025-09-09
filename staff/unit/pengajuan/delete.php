
<?php
require_once("../config/koneksi.php");
if (!isset($_GET['id'])) {
    header('Location: dashboard_staff.php?unit=pengajuan&err=Pengajuan tidak ditemukan!');
    exit;
}
$pengajuan_id = intval($_GET['id']);
$id_user = $_SESSION['id_user'];
// Cek status
$q = mysqli_query($config, "SELECT status FROM tb_pengajuan WHERE pengajuan_id='$pengajuan_id' AND id_user='$id_user'");
$data = mysqli_fetch_assoc($q);
if (!$data) {
    header('Location: dashboard_staff.php?unit=pengajuan&err=Pengajuan tidak ditemukan!');
    exit;
}
if ($data['status'] !== 'diajukan') {
    header('Location: dashboard_staff.php?unit=pengajuan&err=Pengajuan tidak bisa dihapus!');
    exit;
}
$q = mysqli_query($config, "DELETE FROM tb_pengajuan WHERE pengajuan_id='$pengajuan_id' AND id_user='$id_user' AND status='diajukan'");
if ($q) {
    header('Location: dashboard_staff.php?unit=pengajuan&msg=Pengajuan barang berhasil dihapus!');
    exit;
} else {
    header('Location: dashboard_staff.php?unit=pengajuan&err=Gagal menghapus pengajuan!');
    exit;
}
