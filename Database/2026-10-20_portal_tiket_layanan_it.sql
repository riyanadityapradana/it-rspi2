-- Portal Tiket Layanan IT - instalasi pertama pada 192.168.1.108 / it-rspi.
-- Referensi pegawai: 192.168.1.4 / sik9 / pegawai.id (INT signed).
-- Sesuai keputusan integrasi: ID pegawai disimpan sebagai referensi logis;
-- validasi staf dilakukan aplikasi. FOREIGN KEY lintas server tidak dibuat.
-- Memerlukan hak CREATE, REFERENCES, CREATE ROUTINE, ALTER ROUTINE, EXECUTE,
-- dan TRIGGER. Jalankan SELURUH file melalui Import phpMyAdmin atau mysql CLI.
-- Lima tabel bisnis + satu tabel penghitung nomor bulanan (aman untuk konkurensi).
-- Instalasi ditolak jika salah satu tabel portal sudah ada.
-- DDL MariaDB melakukan implicit commit; ini bukan skrip upgrade tabel lama.

USE `it-rspi`;
SET NAMES utf8mb4;
SET @portal_tiket_sql_mode_sebelumnya = @@SESSION.sql_mode;
SET SESSION sql_mode = CONCAT_WS(',', @@SESSION.sql_mode, 'STRICT_TRANS_TABLES');

DELIMITER $$
DROP PROCEDURE IF EXISTS `portal_tiket_install`$$
CREATE PROCEDURE `portal_tiket_install`()
BEGIN
    IF EXISTS (
        SELECT 1 FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN (
            'master_sistem', 'tiket_pengajuan', 'tiket_detail_request',
            'tiket_analisis_it', 'tiket_serah_terima', 'tiket_nomor_bulanan'
        )
    ) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Tabel portal sudah ada. Periksa SHOW CREATE TABLE dan gunakan migrasi terpisah; instalasi dibatalkan.';
    END IF;

    CREATE TABLE `master_sistem` (
        `id_sistem` INT NOT NULL AUTO_INCREMENT,
        `nama_sistem` VARCHAR(100) NOT NULL,
        `deskripsi` TEXT NOT NULL,
        `is_active` BOOLEAN NOT NULL DEFAULT TRUE,
        PRIMARY KEY (`id_sistem`),
        KEY `idx_master_sistem_aktif` (`is_active`, `nama_sistem`),
        CONSTRAINT `chk_master_sistem_aktif` CHECK (`is_active` IN (0, 1))
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    CREATE TABLE `tiket_nomor_bulanan` (
        `periode` CHAR(6) CHARACTER SET ascii COLLATE ascii_bin NOT NULL
            COMMENT 'YYYYMM; dikunci oleh trigger saat mengalokasikan nomor',
        `urutan_terakhir` INT UNSIGNED NOT NULL,
        PRIMARY KEY (`periode`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    CREATE TABLE `tiket_pengajuan` (
        `id_tiket` INT NOT NULL AUTO_INCREMENT,
        `no_tiket` VARCHAR(32) NOT NULL DEFAULT ''
            COMMENT 'Diisi otomatis: REQ/NEWFEAT-YYMM-001; urutan minimum 3 digit',
        `jenis_tiket` ENUM('Change Request', 'Fitur Baru') NOT NULL,
        `id_sistem` INT NOT NULL,
        `kontak_pengaju` VARCHAR(25) NOT NULL COMMENT 'Nomor WhatsApp; simpan sebagai teks',
        `tgl_pengajuan` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `unit_pemohon` VARCHAR(150) NOT NULL,
        `nama_pengaju` VARCHAR(150) NOT NULL,
        `ttd_pengaju` LONGTEXT NOT NULL COMMENT 'Gambar tanda tangan Base64',
        `status_tiket` ENUM('Menunggu Analisis', 'Dikerjakan', 'Ditolak', 'Menunggu UAT', 'Selesai')
            NOT NULL DEFAULT 'Menunggu Analisis',
        `token_uat` VARCHAR(64) CHARACTER SET ascii COLLATE ascii_bin NULL DEFAULT NULL,
        `token_expired_at` DATETIME NULL DEFAULT NULL,
        PRIMARY KEY (`id_tiket`),
        UNIQUE KEY `uq_tiket_pengajuan_nomor` (`no_tiket`),
        UNIQUE KEY `uq_tiket_pengajuan_token` (`token_uat`),
        KEY `idx_tiket_pengajuan_sistem` (`id_sistem`),
        KEY `idx_tiket_pengajuan_status_tanggal` (`status_tiket`, `tgl_pengajuan`),
        KEY `idx_tiket_pengajuan_tanggal` (`tgl_pengajuan`),
        CONSTRAINT `fk_tiket_pengajuan_sistem` FOREIGN KEY (`id_sistem`)
            REFERENCES `master_sistem` (`id_sistem`) ON UPDATE RESTRICT ON DELETE RESTRICT,
        CONSTRAINT `chk_tiket_pengajuan_jenis` CHECK (`jenis_tiket` IN ('Change Request', 'Fitur Baru')),
        CONSTRAINT `chk_tiket_pengajuan_status` CHECK (`status_tiket` IN (
            'Menunggu Analisis', 'Dikerjakan', 'Ditolak', 'Menunggu UAT', 'Selesai')),
        CONSTRAINT `chk_tiket_pengajuan_token` CHECK (
            (`token_uat` IS NULL AND `token_expired_at` IS NULL) OR
            (`token_uat` IS NOT NULL AND CHAR_LENGTH(TRIM(`token_uat`)) > 0 AND `token_expired_at` IS NOT NULL)
        )
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    CREATE TABLE `tiket_detail_request` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `id_tiket` INT NOT NULL,
        `nama_perubahan_fitur` VARCHAR(255) NOT NULL,
        `kondisi_masalah` TEXT NULL,
        `perubahan_diharapkan` TEXT NULL,
        `dampak_modul` TEXT NULL,
        `latar_belakang` TEXT NULL,
        `deskripsi_alur_kerja` TEXT NULL,
        `target_pengguna` VARCHAR(255) NULL,
        `output_diharapkan` TEXT NULL,
        PRIMARY KEY (`id`),
        KEY `idx_tiket_detail_request_tiket` (`id_tiket`),
        CONSTRAINT `fk_tiket_detail_request_tiket` FOREIGN KEY (`id_tiket`)
            REFERENCES `tiket_pengajuan` (`id_tiket`) ON UPDATE RESTRICT ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    CREATE TABLE `tiket_analisis_it` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `id_tiket` INT NOT NULL,
        `id_staf_it` INT NOT NULL COMMENT 'Referensi logis 192.168.1.4/sik9/pegawai.id; validasi aplikasi',
        `tgl_analisis` DATETIME NULL DEFAULT NULL COMMENT 'Dicatat saat simpan analisis; NULL untuk data lama',
        `nama_staf_it` VARCHAR(150) NULL DEFAULT NULL COMMENT 'Salinan nama pegawai saat analisis disimpan',
        `prioritas` ENUM('Tinggi', 'Sedang', 'Rendah') NOT NULL,
        `analisis_unit_it` TEXT NOT NULL,
        `keputusan` TEXT NOT NULL,
        PRIMARY KEY (`id`),
        KEY `idx_tiket_analisis_it_tiket` (`id_tiket`),
        KEY `idx_tiket_analisis_it_staf` (`id_staf_it`),
        CONSTRAINT `fk_tiket_analisis_it_tiket` FOREIGN KEY (`id_tiket`)
            REFERENCES `tiket_pengajuan` (`id_tiket`) ON UPDATE RESTRICT ON DELETE RESTRICT,
        CONSTRAINT `chk_tiket_analisis_it_prioritas` CHECK (`prioritas` IN ('Tinggi', 'Sedang', 'Rendah'))
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    CREATE TABLE `tiket_serah_terima` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `id_tiket` INT NOT NULL,
        `tgl_serah_terima` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `deskripsi_penyelesaian` TEXT NOT NULL,
        `id_staf_penyerah` INT NOT NULL COMMENT 'Referensi logis 192.168.1.4/sik9/pegawai.id; validasi aplikasi',
        `nama_staf_penyerah` VARCHAR(150) NULL DEFAULT NULL COMMENT 'Salinan nama pegawai saat serah terima disimpan',
        `status_pengujian_uat` ENUM('Berhasil', 'Diterima dengan catatan') NULL DEFAULT NULL,
        `catatan_pengujian` TEXT NULL,
        `nama_penerima` VARCHAR(150) NULL DEFAULT NULL,
        `ttd_penerima` LONGTEXT NULL COMMENT 'NULL selama menunggu UAT; Base64 setelah diterima',
        PRIMARY KEY (`id`),
        KEY `idx_tiket_serah_terima_tiket` (`id_tiket`),
        KEY `idx_tiket_serah_terima_staf` (`id_staf_penyerah`),
        CONSTRAINT `fk_tiket_serah_terima_tiket` FOREIGN KEY (`id_tiket`)
            REFERENCES `tiket_pengajuan` (`id_tiket`) ON UPDATE RESTRICT ON DELETE RESTRICT,
        CONSTRAINT `chk_tiket_serah_terima_status` CHECK (
            `status_pengujian_uat` IS NULL OR `status_pengujian_uat` IN ('Berhasil', 'Diterima dengan catatan')),
        CONSTRAINT `chk_tiket_serah_terima_uat` CHECK (
            (`status_pengujian_uat` IS NULL AND `nama_penerima` IS NULL
                AND `ttd_penerima` IS NULL AND `catatan_pengujian` IS NULL) OR
            (`status_pengujian_uat` IS NOT NULL
                AND `nama_penerima` IS NOT NULL AND CHAR_LENGTH(TRIM(`nama_penerima`)) > 0
                AND `ttd_penerima` IS NOT NULL AND CHAR_LENGTH(TRIM(`ttd_penerima`)) > 0
                AND (`status_pengujian_uat` = 'Berhasil' OR
                    (`catatan_pengujian` IS NOT NULL AND CHAR_LENGTH(TRIM(`catatan_pengujian`)) > 0)))
        )
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
END$$

CALL `portal_tiket_install`()$$
DROP PROCEDURE `portal_tiket_install`$$

CREATE TRIGGER `trg_tiket_pengajuan_nomor` BEFORE INSERT ON `tiket_pengajuan`
FOR EACH ROW
BEGIN
    DECLARE v_periode CHAR(6);
    DECLARE v_urutan INT UNSIGNED;

    IF NEW.no_tiket IS NOT NULL AND NEW.no_tiket <> '' THEN
        -- Hanya backend tepercaya yang menetapkan variabel koneksi ini setelah
        -- mengambil reservasi nomor dari sesi formulir, bukan dari input POST.
        IF NOT ((BINARY NEW.no_tiket <=> BINARY @portal_tiket_no_reservasi)
            AND (NEW.tgl_pengajuan <=> @portal_tiket_tgl_reservasi)) THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'no_tiket harus dibuat otomatis atau berasal dari reservasi formulir server.';
        END IF;
    ELSE
        SET v_periode = DATE_FORMAT(NEW.tgl_pengajuan, '%Y%m');
        INSERT INTO `tiket_nomor_bulanan` (`periode`, `urutan_terakhir`)
            VALUES (v_periode, 1)
            ON DUPLICATE KEY UPDATE `urutan_terakhir` = `urutan_terakhir` + 1;
        SELECT `urutan_terakhir` INTO v_urutan
            FROM `tiket_nomor_bulanan` WHERE `periode` = v_periode;
        SET NEW.no_tiket = CONCAT(IF(NEW.jenis_tiket = 'Fitur Baru', 'NEWFEAT-', 'REQ-'), DATE_FORMAT(NEW.tgl_pengajuan, '%y%m'), '-',
            LPAD(CAST(v_urutan AS CHAR), GREATEST(3, CHAR_LENGTH(CAST(v_urutan AS CHAR))), '0'));
    END IF;
END$$
DELIMITER ;
SET SESSION sql_mode = @portal_tiket_sql_mode_sebelumnya;
