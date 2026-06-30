<?php

require_once(__DIR__ . '/scan_helpers.php');

scan_require_pin();

$identifier = isset($_GET['kode']) ? trim($_GET['kode']) : '';
if ($identifier === '') {
    scan_json_response(array(
        'success' => false,
        'message' => 'Kode / ID barang kosong'
    ), 400);
}

$lookup_result = scan_get_barang_by_identifier($config, $identifier);
$barang = $lookup_result['barang'];
if (!$barang) {
    scan_json_response(array(
        'success' => false,
        'message' => isset($lookup_result['error']) ? $lookup_result['error'] : 'Barang tidak ditemukan'
    ), isset($lookup_result['status']) ? intval($lookup_result['status']) : 404);
}

scan_json_response(array(
    'success' => true,
    'lookup' => isset($lookup_result['lookup']) ? $lookup_result['lookup'] : '',
    'barang' => array(
        'barang_id' => intval($barang['barang_id']),
        'kode_inventaris' => $barang['kode_inventaris'],
        'nama_barang' => $barang['nama_barang'],
        'jenis_barang' => $barang['jenis_barang'],
        'nomor_seri' => $barang['nomor_seri'],
        'kondisi' => barang_normalize_kondisi($barang['kondisi'], $barang['kondisi']),
        'lokasi_id' => $barang['lokasi_id'],
        'lokasi_saat_ini' => $barang['lokasi_saat_ini'],
        'last_penyerahan_id' => $barang['last_penyerahan_id'],
        'last_penyerahan_lokasi_id' => $barang['last_penyerahan_lokasi_id'],
        'last_penyerahan_lokasi' => $barang['last_penyerahan_lokasi']
    )
));
