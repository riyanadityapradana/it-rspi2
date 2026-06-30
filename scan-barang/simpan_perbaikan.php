<?php

require_once(__DIR__ . '/scan_helpers.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    scan_json_response(array('success' => false, 'message' => 'Metode tidak valid'), 405);
}

scan_require_pin();

$barang_id = isset($_POST['barang_id']) ? intval($_POST['barang_id']) : 0;
$tanggal_lapor = scan_escape($config, scan_normalize_datetime(isset($_POST['tanggal_lapor']) ? $_POST['tanggal_lapor'] : ''));
$deskripsi = isset($_POST['deskripsi_kerusakan']) ? scan_escape($config, $_POST['deskripsi_kerusakan']) : '';
$tindakan_input = isset($_POST['tindakan_perbaikan']) ? strtolower(trim($_POST['tindakan_perbaikan'])) : 'service_sendiri';
$petugas = isset($_POST['petugas']) ? trim($_POST['petugas']) : '';
$keterangan = isset($_POST['keterangan_perbaikan']) ? trim($_POST['keterangan_perbaikan']) : '';
$unit_melapor = isset($_POST['unit_melapor']) && $_POST['unit_melapor'] !== '' ? intval($_POST['unit_melapor']) : 0;

if ($barang_id <= 0 || $deskripsi === '') {
    scan_json_response(array('success' => false, 'message' => 'Data perbaikan tidak lengkap'), 400);
}

$barang = scan_get_barang_by_id($config, $barang_id);
if (!$barang) {
    scan_json_response(array('success' => false, 'message' => 'Barang tidak ditemukan'), 404);
}

$penyerahan_id = intval($barang['last_penyerahan_id']);
if ($penyerahan_id <= 0) {
    scan_json_response(array('success' => false, 'message' => 'Barang belum memiliki data penyerahan'), 400);
}

if ($unit_melapor <= 0) {
    $unit_melapor = intval($barang['last_penyerahan_lokasi_id']);
}
if ($unit_melapor <= 0) {
    $unit_melapor = intval($barang['lokasi_id']);
}

$tindakan = $tindakan_input === 'service_luar' || $tindakan_input === 'service luar' ? 'Service luar' : 'Service sendiri';
$status = $tindakan === 'Service luar' ? 'diajukan' : 'proses';

$bukti_struk_sql = "''";
if ($tindakan === 'Service luar') {
    if (isset($_FILES['bukti_struk']) && $_FILES['bukti_struk']['error'] === UPLOAD_ERR_OK) {

        $file = $_FILES['bukti_struk'];
        $allowed_ext = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp');
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_ext, true)) {
            scan_json_response(array('success' => false, 'message' => 'Format bukti struk tidak diperbolehkan'), 400);
        }
        if ($file['size'] > 2 * 1024 * 1024) {
            scan_json_response(array('success' => false, 'message' => 'Ukuran bukti struk maksimal 2 MB'), 400);
        }

        $target_dir = __DIR__ . '/../staff/unit/perbaikan/bukti_struk/';
        if (!is_dir($target_dir) && !mkdir($target_dir, 0755, true)) {
            scan_json_response(array('success' => false, 'message' => 'Folder upload bukti struk tidak dapat dibuat'), 500);
        }

        $bukti_name = 'scan_' . $barang_id . '_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], $target_dir . $bukti_name)) {
            scan_json_response(array('success' => false, 'message' => 'Gagal mengupload bukti struk'), 500);
        }
        $bukti_struk_sql = "'" . scan_escape($config, $bukti_name) . "'";
    } elseif (isset($_FILES['bukti_struk']) && $_FILES['bukti_struk']['error'] !== UPLOAD_ERR_NO_FILE) {
        scan_json_response(array('success' => false, 'message' => 'Gagal membaca file bukti struk'), 400);
    }
}

$teknisi_sql = "NULL";
if ($tindakan === 'Service sendiri' && $petugas !== '') {
    $teknisi_sql = "'" . scan_escape($config, $petugas) . "'";
}

$unit_sql = $unit_melapor > 0 ? "'{$unit_melapor}'" : "NULL";
$status_sql = scan_escape($config, $status);
$tindakan_sql = scan_escape($config, $tindakan);
$note = scan_escape($config, scan_public_note($petugas, $keterangan));

mysqli_begin_transaction($config);
$insert = mysqli_query(
    $config,
    "INSERT INTO tb_perbaikan_barang (barang_id, tanggal_lapor, penyerahan_id, deskripsi_kerusakan, tindakan_perbaikan, status, tanggal_selesai, teknisi, keterangan, bukti_struk, unit_melapor)
    VALUES ('{$barang_id}', '{$tanggal_lapor}', '{$penyerahan_id}', '{$deskripsi}', '{$tindakan_sql}', '{$status_sql}', NULL, {$teknisi_sql}, '{$note}', {$bukti_struk_sql}, {$unit_sql})"
);

if ($insert) {
    $snapshot_kondisi = 'Dalam Perbaikan';
    if ($status === 'selesai') {
        $snapshot_kondisi = 'Baik';
    } elseif ($status === 'tidak_dapat_diperbaiki') {
        $snapshot_kondisi = 'Rusak';
    }

    if (barang_sync_snapshot($config, $barang_id, array('kondisi' => $snapshot_kondisi))) {
        mysqli_commit($config);
        scan_json_response(array('success' => true, 'message' => 'Perbaikan barang berhasil disimpan'));
    }
}

mysqli_rollback($config);
scan_json_response(array('success' => false, 'message' => 'Gagal menyimpan perbaikan: ' . mysqli_error($config)), 500);
