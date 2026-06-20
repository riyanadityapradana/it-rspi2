<?php
require_once("../../../config/koneksi.php");
require_once(__DIR__ . "/barang_helpers.php");

header('Content-Type: application/json');

$unit_kode = isset($_GET['unit_kode']) ? $_GET['unit_kode'] : '';
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

$reference = barang_get_kode_inventaris_reference($config, $unit_kode, $tanggal);

echo json_encode([
    'success' => true,
    'last' => $reference['last'],
    'next' => $reference['next'],
    'prefix' => $reference['prefix'],
    'last_number' => $reference['last_number']
]);
