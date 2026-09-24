-- Monev Keamanan Fisik dan Kerahasiaan Data. Jalankan pada database aplikasi.
CREATE TABLE IF NOT EXISTS inspeksi_nomor_harian (
 tanggal DATE PRIMARY KEY, urutan SMALLINT UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS monev_pegawai_akun (
 nik VARCHAR(30) PRIMARY KEY, user_id INT NOT NULL UNIQUE,
 nama_pegawai VARCHAR(100) NOT NULL, dipetakan_oleh INT NOT NULL,
 dipetakan_pada DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES tb_user(id_user),
 FOREIGN KEY (dipetakan_oleh) REFERENCES tb_user(id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS master_kriteria_inspeksi (
 id INT AUTO_INCREMENT PRIMARY KEY, kategori VARCHAR(100) NOT NULL,
 deskripsi TEXT NOT NULL, is_active TINYINT(1) NOT NULL DEFAULT 1,
 urutan INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS inspeksi_header (
 id INT AUTO_INCREMENT PRIMARY KEY,
 no_formulir VARCHAR(100) NOT NULL DEFAULT '', revisi VARCHAR(30) NOT NULL DEFAULT '', tgl_berlaku VARCHAR(30) NOT NULL DEFAULT '',
 periode VARCHAR(50) NOT NULL, tgl_waktu_inspeksi DATETIME NOT NULL,
 unit_area_id INT NOT NULL, pic_area_id INT NULL, pemeriksa_id INT NOT NULL, verifikator_id INT NULL,
 pic_area_nik VARCHAR(30) NULL, pic_area_nama VARCHAR(100) NULL,
 verifikator_nik VARCHAR(30) NULL, verifikator_nama VARCHAR(100) NULL,
 jenis_inspeksi ENUM('Rutin','Sewaktu-Waktu','Verifikasi Perbaikan','Setelah Insiden') NOT NULL,
 status_inspeksi ENUM('Draft','Menunggu Pengesahan','Selesai') NOT NULL DEFAULT 'Draft',
 catatan_validasi TEXT NULL, version INT NOT NULL DEFAULT 1,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 FOREIGN KEY (unit_area_id) REFERENCES tb_lokasi(lokasi_id),
 FOREIGN KEY (pic_area_id) REFERENCES tb_user(id_user), FOREIGN KEY (pemeriksa_id) REFERENCES tb_user(id_user),
 FOREIGN KEY (verifikator_id) REFERENCES tb_user(id_user),
 INDEX (status_inspeksi, unit_area_id), INDEX (pemeriksa_id), INDEX (tgl_waktu_inspeksi)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS inspeksi_bundle (
 id INT AUTO_INCREMENT PRIMARY KEY, inspeksi_id INT NOT NULL,
 unit_area_id INT NOT NULL, pic_area_id INT NULL, pic_area_nik VARCHAR(30) NULL, pic_area_nama VARCHAR(100) NULL,
 ruang_diperiksa TEXT NULL, komputer_diperiksa TEXT NULL, dok_printer_diperiksa TEXT NULL,
 objek_belum_diperiksa_alasan TEXT NULL, alasan_ta_bv TEXT NULL,
 disimpan_pada DATETIME NULL, legacy_header_id INT NULL UNIQUE,
 FOREIGN KEY (inspeksi_id) REFERENCES inspeksi_header(id) ON DELETE CASCADE,
 FOREIGN KEY (unit_area_id) REFERENCES tb_lokasi(lokasi_id),
 FOREIGN KEY (pic_area_id) REFERENCES tb_user(id_user), INDEX (inspeksi_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS inspeksi_detail_checklist (
 id INT AUTO_INCREMENT PRIMARY KEY, inspeksi_id INT NOT NULL, bundle_id INT NULL, kriteria_id INT NOT NULL,
 kategori_snapshot VARCHAR(100) NOT NULL, deskripsi_snapshot TEXT NOT NULL,
 hasil ENUM('S','TS','TA','BV') NOT NULL DEFAULT 'BV', lokasi_id_perangkat VARCHAR(255) NOT NULL DEFAULT '',
 catatan_bukti TEXT NULL, path_foto_bukti VARCHAR(255) NULL,
 disimpan_pada DATETIME NULL,
 INDEX idx_inspeksi_details (inspeksi_id), INDEX idx_kriteria_details (kriteria_id),
 FOREIGN KEY (inspeksi_id) REFERENCES inspeksi_header(id) ON DELETE CASCADE,
 FOREIGN KEY (bundle_id) REFERENCES inspeksi_bundle(id) ON DELETE CASCADE,
 FOREIGN KEY (kriteria_id) REFERENCES master_kriteria_inspeksi(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS inspeksi_cakupan (
 id INT AUTO_INCREMENT PRIMARY KEY, inspeksi_id INT NOT NULL UNIQUE,
 ruang_diperiksa TEXT NULL, komputer_diperiksa TEXT NULL, dok_printer_diperiksa TEXT NULL,
 objek_belum_diperiksa_alasan TEXT NULL, alasan_ta_bv TEXT NULL,
 FOREIGN KEY (inspeksi_id) REFERENCES inspeksi_header(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS inspeksi_temuan_perbaikan (
 id INT AUTO_INCREMENT PRIMARY KEY, inspeksi_id INT NOT NULL, detail_checklist_id INT NOT NULL UNIQUE,
 no_temuan VARCHAR(100) NOT NULL UNIQUE, waktu_ditemukan DATETIME NOT NULL,
 kondisi_risiko TEXT NULL, tindakan_segera TEXT NULL, pencegahan TEXT NULL,
 pic_perbaikan_id INT NULL, target_selesai DATE NULL, tgl_pelaksanaan DATE NULL,
 path_bukti_perbaikan VARCHAR(255) NULL, verifikator_id INT NULL, tgl_verifikasi DATE NULL,
 hasil_pemeriksaan_ulang TEXT NULL,
 status_temuan ENUM('Terbuka','Dalam proses','Selesai terverifikasi') NOT NULL DEFAULT 'Terbuka',
 no_laporan_insiden VARCHAR(100) NOT NULL DEFAULT '',
 FOREIGN KEY (inspeksi_id) REFERENCES inspeksi_header(id) ON DELETE CASCADE,
 FOREIGN KEY (detail_checklist_id) REFERENCES inspeksi_detail_checklist(id) ON DELETE CASCADE,
 FOREIGN KEY (pic_perbaikan_id) REFERENCES tb_user(id_user), FOREIGN KEY (verifikator_id) REFERENCES tb_user(id_user),
 INDEX (status_temuan,target_selesai)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS inspeksi_pengesahan (
 id INT AUTO_INCREMENT PRIMARY KEY, inspeksi_id INT NOT NULL,
 bundle_id INT NULL,
 peran ENUM('Pemeriksa','PIC/Kepala Unit','Verifikator') NOT NULL,
 user_id INT NOT NULL, tgl_pengesahan DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 path_tanda_tangan VARCHAR(255) NULL,
 nama_penanda VARCHAR(100) NULL, nik_penanda VARCHAR(30) NULL,
 metode VARCHAR(20) NOT NULL DEFAULT 'akun',
 dibatalkan_pada DATETIME NULL,
 FOREIGN KEY (inspeksi_id) REFERENCES inspeksi_header(id) ON DELETE CASCADE,
 FOREIGN KEY (user_id) REFERENCES tb_user(id_user), INDEX (inspeksi_id,dibatalkan_pada)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- Kriteria awal usulan, bukan salinan formulir resmi. Dapat diubah melalui master.
INSERT INTO master_kriteria_inspeksi (kategori,deskripsi,urutan)
SELECT s.kategori,s.deskripsi,s.urutan FROM (
 SELECT 'Ruang Server' kategori,'Akses ruang server dibatasi hanya untuk petugas berwenang.' deskripsi,1 urutan
 UNION ALL SELECT 'Ruang Server','Pintu dan lemari perangkat server terkunci saat tidak digunakan.',2
 UNION ALL SELECT 'Ruang Server','Suhu, kebersihan, dan kondisi lingkungan ruang server terpantau.',3
 UNION ALL SELECT 'Ruang Server','UPS, proteksi kebakaran, dan penataan kabel dalam kondisi baik.',4
 UNION ALL SELECT 'Komputer','Komputer dikunci ketika ditinggalkan oleh pengguna.',5
 UNION ALL SELECT 'Komputer','Akun dan kata sandi digunakan secara pribadi serta tidak dibagikan.',6
 UNION ALL SELECT 'Komputer','Layar komputer terhindar dari pandangan pihak yang tidak berwenang.',7
 UNION ALL SELECT 'Dokumen dan Printer','Dokumen berisi data pasien tidak ditinggalkan terbuka di meja atau printer.',8
 UNION ALL SELECT 'Dokumen dan Printer','Dokumen rahasia disimpan di tempat terkunci dan dimusnahkan secara aman.',9
 UNION ALL SELECT 'Media Penyimpanan','Media penyimpanan dan pemindahan data dibatasi kepada pihak berwenang.',10
) s WHERE NOT EXISTS (SELECT 1 FROM master_kriteria_inspeksi);
