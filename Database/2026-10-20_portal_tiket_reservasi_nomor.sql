-- Upgrade instalasi portal yang sudah ada. Tidak mengubah tabel/data tiket.
-- Membutuhkan tabel tiket_pengajuan dan tiket_nomor_bulanan dari instalasi awal.
-- Nomor dicadangkan backend saat formulir dibuka, memakai penghitung yang sama.
-- Awalan: REQ untuk Change Request; NEWFEAT untuk Fitur Baru.
USE `it-rspi`;
SET @portal_tiket_sql_mode_sebelumnya = @@SESSION.sql_mode;
SET SESSION sql_mode = CONCAT_WS(',', @@SESSION.sql_mode, 'STRICT_TRANS_TABLES');
DELIMITER $$
CREATE OR REPLACE TRIGGER `trg_tiket_pengajuan_nomor` BEFORE INSERT ON `tiket_pengajuan`
FOR EACH ROW
BEGIN
    DECLARE v_periode CHAR(6);
    DECLARE v_urutan INT UNSIGNED;
    IF NEW.no_tiket IS NOT NULL AND NEW.no_tiket <> '' THEN
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
