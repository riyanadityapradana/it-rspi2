<?php
require_once("config/koneksi.php");

echo "<h2>Update Database IT-RSPI</h2>";
echo "<p>Menjalankan update database...</p>";

$queries = [
    "ALTER TABLE `tb_barang` ADD `penyerahan` VARCHAR(100) NOT NULL DEFAULT '' AFTER `stok`",
    "ALTER TABLE `tb_barang` ADD `stts_brg` ENUM('Baik', 'Rusak') NOT NULL DEFAULT 'Baik' AFTER `penyerahan`",
    "ALTER TABLE `tb_barang` ADD `keterangan_rusak` TEXT NULL AFTER `stts_brg`",
    "ALTER TABLE `tb_barang` ADD `status_perbaikan` ENUM('Belum Ada Perbaikan','Dapat Diperbaiki', 'Tidak Dapat Diperbaiki') NULL DEFAULT NULL;",
    "ALTER TABLE `tb_barang` ADD `keterangan_perbaikan` TEXT NULL AFTER `status_perbaikan`",
    "ALTER TABLE `tb_barang` MODIFY `stts_brg` ENUM('Baik', 'Rusak') NULL DEFAULT NULL;",
    "ALTER TABLE `tb_barang` MODIFY `status_perbaikan` ENUM('Belum Ada Perbaikan','Dapat Diperbaiki', 'Tidak Dapat Diperbaiki') NULL DEFAULT NULL;",
    "ALTER TABLE `tb_pengajuan_barang` MODIFY `status` ENUM('Menunggu','Disetujui','Ditolak','Selesai') DEFAULT 'Menunggu'"
];

$success_count = 0;
$error_count = 0;

foreach ($queries as $query) {
    try {
        $result = mysqli_query($config, $query);
        if ($result) {
            echo "<p style='color: green;'>✓ Berhasil: " . htmlspecialchars($query) . "</p>";
            $success_count++;
        } else {
            echo "<p style='color: red;'>✗ Gagal: " . htmlspecialchars($query) . " - " . mysqli_error($config) . "</p>";
            $error_count++;
        }
    } catch (Exception $e) {
        echo "<p style='color: orange;'>⚠ Warning: " . htmlspecialchars($query) . " - " . $e->getMessage() . "</p>";
        // Field mungkin sudah ada, ini bukan error fatal
    }
}

echo "<hr>";
echo "<h3>Hasil Update:</h3>";
echo "<p>Berhasil: $success_count</p>";
echo "<p>Gagal: $error_count</p>";

if ($error_count == 0) {
    echo "<p style='color: green; font-weight: bold;'>Database berhasil diupdate! Fitur penyerahan barang sudah siap digunakan.</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>Ada beberapa error dalam update database. Silakan periksa manual di phpMyAdmin.</p>";
}

echo "<p><a href='staff/dashboard_staff.php?unit=barang'>Kembali ke Data Barang</a></p>";
?> 