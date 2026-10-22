-- Upgrade non-destruktif untuk instalasi portal yang sudah ada (MariaDB 10.4).
-- Tanggal analisis lama tidak dapat direkonstruksi: tetap NULL.
-- Nama lama yang NULL dibaca dari master pegawai SIMRS berdasarkan ID tersimpan.
USE `it-rspi`;
SET NAMES utf8mb4;
ALTER TABLE `tiket_analisis_it`
    ADD COLUMN IF NOT EXISTS `tgl_analisis` DATETIME NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS `nama_staf_it` VARCHAR(150) NULL DEFAULT NULL;
ALTER TABLE `tiket_serah_terima`
    ADD COLUMN IF NOT EXISTS `nama_staf_penyerah` VARCHAR(150) NULL DEFAULT NULL;
