<?php
require_once("../../../config/koneksi.php");
require_once(__DIR__ . "/barang_helpers.php");
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../dashboard_staff.php?unit=barang');
    exit;
}

$barang_id = isset($_POST['barang_id']) ? intval($_POST['barang_id']) : 0;
$tanggal_lapor = isset($_POST['tanggal_lapor']) && $_POST['tanggal_lapor'] !== '' ? mysqli_real_escape_string($config, $_POST['tanggal_lapor']) : date('Y-m-d H:i:s');
$deskripsi_kerusakan = isset($_POST['deskripsi_kerusakan']) ? mysqli_real_escape_string($config, trim($_POST['deskripsi_kerusakan'])) : '';
$tindakan_input = isset($_POST['tindakan_perbaikan']) ? trim($_POST['tindakan_perbaikan']) : '';
$tindakan_normalized = strtolower(str_replace(' ', '_', $tindakan_input));
if ($tindakan_normalized === 'service_sendiri') {
    $tindakan_perbaikan = 'Service sendiri';
} elseif ($tindakan_normalized === 'service_luar') {
    $tindakan_perbaikan = 'Service luar';
} else {
    $tindakan_perbaikan = 'Service luar';
}
$status_perbaikan = isset($_POST['status_perbaikan']) ? mysqli_real_escape_string($config, $_POST['status_perbaikan']) : 'diajukan';
$keterangan_perbaikan = isset($_POST['keterangan_perbaikan']) ? mysqli_real_escape_string($config, trim($_POST['keterangan_perbaikan'])) : '';
$unit_melapor = isset($_POST['unit_melapor']) && $_POST['unit_melapor'] !== '' ? intval($_POST['unit_melapor']) : null;
$bukti_struk_sql = "''";

if ($barang_id <= 0) {
    header('Location: ../../dashboard_staff.php?unit=barang&err=Data perbaikan tidak lengkap');
    exit;
}

if ($tindakan_perbaikan === 'Service luar') {
    if (!isset($_FILES['bukti_struk']) || $_FILES['bukti_struk']['error'] !== UPLOAD_ERR_OK) {
        header('Location: ../../dashboard_staff.php?unit=barang&err=Bukti struk wajib diupload untuk service luar');
        exit;
    }

    $file = $_FILES['bukti_struk'];
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $maxSize = 2 * 1024 * 1024;

    if (!in_array($ext, $allowed, true)) {
        header('Location: ../../dashboard_staff.php?unit=barang&err=Format bukti struk tidak diperbolehkan');
        exit;
    }

    if ($file['size'] > $maxSize) {
        header('Location: ../../dashboard_staff.php?unit=barang&err=Ukuran bukti struk maksimal 2 MB');
        exit;
    }

    $target_dir = dirname(__DIR__) . '/perbaikan/bukti_struk/';
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $bukti_struk_name = 'barang_' . $barang_id . '_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
    $target_file = $target_dir . $bukti_struk_name;
    if (!move_uploaded_file($file['tmp_name'], $target_file)) {
        header('Location: ../../dashboard_staff.php?unit=barang&err=Gagal mengupload bukti struk');
        exit;
    }

    $bukti_struk_sql = "'" . mysqli_real_escape_string($config, $bukti_struk_name) . "'";
}

$unit_sql = $unit_melapor !== null ? "'{$unit_melapor}'" : "NULL";
if ($tindakan_perbaikan === 'Service sendiri') {
    $teknisi_val = isset($_SESSION['nama_lengkap']) ? mysqli_real_escape_string($config, $_SESSION['nama_lengkap']) : (isset($_SESSION['username']) ? mysqli_real_escape_string($config, $_SESSION['username']) : '');
    $teknisi_sql = $teknisi_val !== '' ? "'{$teknisi_val}'" : "NULL";
} else {
    $teknisi_sql = "NULL";
}

$ins_sql = "INSERT INTO tb_perbaikan_barang (barang_id, tanggal_lapor, deskripsi_kerusakan, tindakan_perbaikan, status, tanggal_selesai, teknisi, keterangan, unit_melapor, bukti_struk) VALUES ('{$barang_id}', '{$tanggal_lapor}', '{$deskripsi_kerusakan}', '{$tindakan_perbaikan}', '{$status_perbaikan}', NULL, {$teknisi_sql}, '{$keterangan_perbaikan}', {$unit_sql}, {$bukti_struk_sql})";

mysqli_begin_transaction($config);
$ins = mysqli_query($config, $ins_sql);
if ($ins) {
    $snapshot_kondisi = '';
    if ($status_perbaikan === 'tidak_dapat_diperbaiki') {
        $snapshot_kondisi = 'Rusak';
    } elseif ($status_perbaikan === 'selesai') {
        $snapshot_kondisi = 'Baik';
    } elseif ($status_perbaikan === 'proses' || $status_perbaikan === 'diajukan') {
        $snapshot_kondisi = 'Dalam Perbaikan';
    }

    if (barang_sync_snapshot($config, $barang_id, ['kondisi' => $snapshot_kondisi])) {
        mysqli_commit($config);
        header('Location: ../../dashboard_staff.php?unit=barang&msg=Perbaikan berhasil disimpan');
        exit;
    }

    mysqli_rollback($config);
    header('Location: ../../dashboard_staff.php?unit=barang&err=Gagal update kondisi barang: ' . mysqli_error($config));
    exit;
}

mysqli_rollback($config);
header('Location: ../../dashboard_staff.php?unit=barang&err=Gagal menyimpan perbaikan: ' . mysqli_error($config));
exit;
