<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (!isset($_GET['id'])) {
    echo '<script>window.location="?unit=cuti";</script>';
    exit;
}
$id = intval($_GET['id']);

$query = mysqli_query($config, "SELECT id_user FROM tb_cuti WHERE id_cuti='{$id}'") or die(mysqli_error($config));
$data = mysqli_fetch_array($query);
if (!$data) {
    echo '<script>window.location="?unit=cuti&msg=notfound";</script>';
    exit;
}

$id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : '';
$allowDelete = ($data['id_user'] == $id_user) || (isset($_SESSION['nip']) && $_SESSION['nip'] === '662.140725' && strtolower($_SESSION['role']) === 'staff');
if (!$allowDelete) {
    echo '<script>alert("Anda tidak berwenang menghapus data ini.");window.location="?unit=cuti";</script>';
    exit;
}

$del = mysqli_query($config, "DELETE FROM tb_cuti WHERE id_cuti='{$id}'") or die(mysqli_error($config));
if ($del) {
    echo '<script>window.location="?unit=cuti&msg=deleted";</script>';
    exit;
} else {
    echo '<script>alert("Gagal menghapus data.");window.location="?unit=cuti";</script>';
    exit;
}
