-- Update tabel tb_barang untuk menambahkan field penyerahan dan stts_brg
-- Jalankan query ini di phpMyAdmin atau MySQL client

-- Menambahkan field penyerahan
ALTER TABLE `tb_barang` ADD `penyerahan` VARCHAR(100) NOT NULL DEFAULT '' AFTER `stok`;

-- Menambahkan field stts_brg
ALTER TABLE `tb_barang` ADD `stts_brg` ENUM('Baik', 'Rusak') NOT NULL DEFAULT 'Baik' AFTER `penyerahan`;

-- Ubah default stts_brg dan status_perbaikan menjadi NULL
ALTER TABLE `tb_barang` MODIFY `stts_brg` ENUM('Baik', 'Rusak') NULL DEFAULT NULL;
ALTER TABLE `tb_barang` MODIFY `status_perbaikan` ENUM('Belum Ada Perbaikan','Dapat Diperbaiki', 'Tidak Dapat Diperbaiki') NULL DEFAULT NULL;

-- Menambahkan field keterangan_rusak
ALTER TABLE `tb_barang` ADD `keterangan_rusak` TEXT NULL AFTER `stts_brg`;

-- Menambahkan field status_perbaikan
ALTER TABLE `tb_barang` ADD `status_perbaikan` ENUM('Belum Ada Perbaikan','Dapat Diperbaiki', 'Tidak Dapat Diperbaiki') NULL DEFAULT NULL AFTER `keterangan_rusak`;

-- Menambahkan field keterangan_perbaikan
ALTER TABLE `tb_barang` ADD `keterangan_perbaikan` TEXT NULL AFTER `status_perbaikan`;

-- Menambahkan status 'Selesai' ke enum status di tb_pengajuan_barang (opsional)
ALTER TABLE `tb_pengajuan_barang` MODIFY `status` ENUM('Menunggu','Disetujui','Ditolak','Selesai') DEFAULT 'Menunggu'; 