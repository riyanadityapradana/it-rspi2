<?php

require_once(__DIR__ . '/scan_helpers.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    scan_json_response(array('success' => false, 'message' => 'Metode tidak valid'), 405);
}

scan_require_pin();

$barang_id = isset($_POST['barang_id']) ? intval($_POST['barang_id']) : 0;
$lokasi_tujuan = isset($_POST['lokasi_tujuan']) ? intval($_POST['lokasi_tujuan']) : 0;
$tanggal_mutasi = isset($_POST['tanggal_mutasi']) && $_POST['tanggal_mutasi'] !== '' ? scan_escape($config, $_POST['tanggal_mutasi']) : date('Y-m-d');
$petugas = isset($_POST['petugas']) ? trim($_POST['petugas']) : '';
$keterangan = isset($_POST['keterangan']) ? trim($_POST['keterangan']) : '';

if ($barang_id <= 0 || $lokasi_tujuan <= 0) {
    scan_json_response(array('success' => false, 'message' => 'Data pemindahan tidak lengkap'), 400);
}

$barang = scan_get_barang_by_id($config, $barang_id);
if (!$barang) {
    scan_json_response(array('success' => false, 'message' => 'Barang tidak ditemukan'), 404);
}

$lokasi_asal = intval($barang['last_penyerahan_lokasi_id']);
if ($lokasi_asal <= 0) {
    $lokasi_asal = intval($barang['lokasi_id']);
}
if ($lokasi_asal <= 0) {
    scan_json_response(array('success' => false, 'message' => 'Lokasi asal barang belum tersedia'), 400);
}

$note = scan_escape($config, scan_public_note($petugas, $keterangan));

mysqli_begin_transaction($config);
$insert = mysqli_query(
    $config,
    "INSERT INTO tb_mutasi_barang (barang_id, lokasi_asal, lokasi_tujuan, tanggal_mutasi, id_user, keterangan)
    VALUES ('{$barang_id}', '{$lokasi_asal}', '{$lokasi_tujuan}', '{$tanggal_mutasi}', NULL, '{$note}')"
);

if ($insert && barang_sync_snapshot($config, $barang_id, array('lokasi_id' => $lokasi_tujuan, 'kondisi' => 'Bekas'))) {
    mysqli_commit($config);
    scan_json_response(array('success' => true, 'message' => 'Pemindahan barang berhasil disimpan'));
}

mysqli_rollback($config);
scan_json_response(array('success' => false, 'message' => 'Gagal menyimpan pemindahan: ' . mysqli_error($config)), 500);
