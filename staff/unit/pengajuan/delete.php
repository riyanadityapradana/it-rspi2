<?php
require_once("../config/koneksi.php");
if (!isset($_GET['id'])) {
    header('Location: dashboard_staff.php?unit=pengajuan&err=Pengajuan tidak ditemukan!');
    exit;
}

$pengajuan_id = (int) $_GET['id'];
$id_user = $_SESSION['id_user'];

$stmt = $config->prepare("SELECT status FROM tb_pengajuan WHERE pengajuan_id = ? AND id_user = ? LIMIT 1");
$stmt->bind_param('ii', $pengajuan_id, $id_user);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    header('Location: dashboard_staff.php?unit=pengajuan&err=Pengajuan tidak ditemukan!');
    exit;
}
if ($data['status'] !== 'diajukan') {
    header('Location: dashboard_staff.php?unit=pengajuan&err=Pengajuan tidak bisa dihapus!');
    exit;
}

$stmt = $config->prepare("DELETE FROM tb_pengajuan WHERE pengajuan_id = ? AND id_user = ? AND status = 'diajukan'");
$stmt->bind_param('ii', $pengajuan_id, $id_user);
$success = $stmt->execute();
$stmt->close();

if ($success) {
    header('Location: dashboard_staff.php?unit=pengajuan&msg=Pengajuan barang berhasil dihapus!');
    exit;
}

header('Location: dashboard_staff.php?unit=pengajuan&err=Gagal menghapus pengajuan!');
exit;
