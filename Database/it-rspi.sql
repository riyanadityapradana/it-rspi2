-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 31 Jul 2025 pada 11.49
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `it-rspi`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_bahasa`
--

CREATE TABLE `tb_bahasa` (
  `id_bahasa` int(11) NOT NULL,
  `id_calon` int(11) NOT NULL,
  `nama_bahasa` varchar(50) DEFAULT NULL,
  `tingkat` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_barang`
--

CREATE TABLE `tb_barang` (
  `kode_barang` varchar(15) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `spesifikasi` text DEFAULT NULL,
  `jenis_barang` enum('Komputer & Laptop','Komponen Komputer & Laptop','Printer & Scanner','Komponen Printer & Scanner','Komponen Network') DEFAULT NULL,
  `penyerahan` varchar(50) NOT NULL,
  `stok` int(11) DEFAULT 0,
  `stts_brg` enum('Baik','Rusak') DEFAULT NULL,
  `status_perbaikan` enum('Belum Ada Perbaikan','Dapat Diperbaiki','Tidak Dapat Diperbaiki') DEFAULT NULL,
  `keterangan_rusak` varchar(100) NOT NULL,
  `keterangan_perbaikan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_barang`
--

INSERT INTO `tb_barang` (`kode_barang`, `nama_barang`, `spesifikasi`, `jenis_barang`, `penyerahan`, `stok`, `stts_brg`, `status_perbaikan`, `keterangan_rusak`, `keterangan_perbaikan`) VALUES
('BRG/RSPI-001', 'Webcame Logitech C270 HD 720p', '-', 'Komponen Komputer & Laptop', 'Yakult C', 1, 'Baik', 'Dapat Diperbaiki', 'rrr', 'ddd'),
('BRG/RSPI-002', 'RAM 16 GB DDR 4 Merek KingSton', 'ww', 'Komponen Komputer & Laptop', 'Kecubung', 1, 'Rusak', 'Tidak Dapat Diperbaiki', 'sss', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_calon`
--

CREATE TABLE `tb_calon` (
  `id_calon` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `divisi_lamaran` varchar(50) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('belum tes','tes selesai','lulus','tidak lulus') DEFAULT 'belum tes',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_calon`
--

INSERT INTO `tb_calon` (`id_calon`, `username`, `password`, `nama_lengkap`, `email`, `no_hp`, `jenis_kelamin`, `tanggal_lahir`, `alamat`, `divisi_lamaran`, `foto`, `status`, `created_at`, `updated_at`) VALUES
(2, 'firda', '$2y$10$1oB36s/z0n87mzzfD1LzQuYCTUF1qBbqrvsb4LnNWzYrlZf8m32GC', 'www', NULL, '908221222222', NULL, NULL, NULL, NULL, NULL, 'belum tes', '2025-07-15 14:48:38', '2025-07-15 14:48:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_keahlian`
--

CREATE TABLE `tb_keahlian` (
  `id_keahlian` int(11) NOT NULL,
  `id_calon` int(11) NOT NULL,
  `nama_keahlian` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_kegiatan_lembur`
--

CREATE TABLE `tb_kegiatan_lembur` (
  `id_kegiatan` int(11) NOT NULL,
  `id_lembur` int(11) NOT NULL,
  `kegiatan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_kegiatan_lembur`
--

INSERT INTO `tb_kegiatan_lembur` (`id_kegiatan`, `id_lembur`, `kegiatan`) VALUES
(6, 4, 'Perbaikan Sever'),
(7, 4, 'Menambah Jaringan'),
(8, 5, 'Menjadi Petugas Presentasi'),
(9, 6, 'fff'),
(10, 6, 'aaa'),
(11, 6, 'qqqq'),
(17, 9, 'aaaa');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_lembur`
--

CREATE TABLE `tb_lembur` (
  `id_lembur` int(11) NOT NULL,
  `id_staff` int(11) NOT NULL,
  `tanggal_lembur` date NOT NULL,
  `status_lembur` enum('Menunggu','Diterima','Ditolak') DEFAULT 'Menunggu',
  `id_pimpinan` int(11) DEFAULT NULL,
  `waktu_input` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_lembur`
--

INSERT INTO `tb_lembur` (`id_lembur`, `id_staff`, `tanggal_lembur`, `status_lembur`, `id_pimpinan`, `waktu_input`) VALUES
(4, 2, '2025-05-31', 'Ditolak', 1, '2025-05-31 03:08:03'),
(5, 5, '2025-05-31', 'Diterima', 1, '2025-05-31 03:11:46'),
(6, 2, '2025-06-07', 'Diterima', 1, '2025-06-07 04:11:00'),
(9, 5, '2025-07-14', 'Menunggu', NULL, '2025-07-14 14:52:01');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_logbook`
--

CREATE TABLE `tb_logbook` (
  `id_log` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `tanggal_log` date NOT NULL,
  `judul_log` varchar(200) NOT NULL,
  `deskripsi_log` text NOT NULL,
  `catatan_log` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_organisasi`
--

CREATE TABLE `tb_organisasi` (
  `id_organisasi` int(11) NOT NULL,
  `id_calon` int(11) NOT NULL,
  `nama_organisasi` varchar(100) DEFAULT NULL,
  `tahun_mulai` year(4) DEFAULT NULL,
  `tahun_selesai` year(4) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_pemindahan_barang`
--

CREATE TABLE `tb_pemindahan_barang` (
  `id_pemindahan` int(11) NOT NULL,
  `kode_barang` varchar(15) NOT NULL,
  `tanggal_pemindahan` date NOT NULL,
  `ke_unit` varchar(50) NOT NULL,
  `alasan_pemindahan` text NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_pemindahan_barang`
--

INSERT INTO `tb_pemindahan_barang` (`id_pemindahan`, `kode_barang`, `tanggal_pemindahan`, `ke_unit`, `alasan_pemindahan`, `id_user`) VALUES
(0, 'BRG/RSPI-001', '2025-07-30', 'Manajemen', 'ss', 7);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_pendidikan`
--

CREATE TABLE `tb_pendidikan` (
  `id_pendidikan` int(11) NOT NULL,
  `id_calon` int(11) NOT NULL,
  `jenjang` varchar(50) DEFAULT NULL,
  `nama_sekolah` varchar(100) DEFAULT NULL,
  `jurusan` varchar(100) DEFAULT NULL,
  `tahun_masuk` year(4) DEFAULT NULL,
  `tahun_lulus` year(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_pengajuan_barang`
--

CREATE TABLE `tb_pengajuan_barang` (
  `id_pengajuan` int(11) NOT NULL,
  `id_staff` int(11) NOT NULL,
  `kode_barang` varchar(15) NOT NULL,
  `satuan` varchar(15) NOT NULL,
  `jumlah` varchar(15) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `bidang_pengajuan` varchar(50) NOT NULL,
  `status` enum('Menunggu','Disetujui','Ditolak') DEFAULT 'Menunggu',
  `id_kepala` int(11) DEFAULT NULL,
  `tgl_pengajuan` datetime NOT NULL DEFAULT current_timestamp(),
  `waktu_acc` timestamp NULL DEFAULT NULL,
  `keterangan_acc` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_pengajuan_barang`
--

INSERT INTO `tb_pengajuan_barang` (`id_pengajuan`, `id_staff`, `kode_barang`, `satuan`, `jumlah`, `keterangan`, `bidang_pengajuan`, `status`, `id_kepala`, `tgl_pengajuan`, `waktu_acc`, `keterangan_acc`) VALUES
(6, 7, 'BRG/RSPI-001', '1', '1', 'sss', 'Divis Unit IT', 'Disetujui', 1, '2025-07-19 11:44:18', '2025-07-19 03:44:50', NULL),
(7, 7, 'BRG/RSPI-002', '1', '1', 'eee', 'Divis Unit IT', 'Disetujui', 1, '2025-07-19 11:48:33', '2025-07-19 03:49:05', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_pengalaman`
--

CREATE TABLE `tb_pengalaman` (
  `id_pengalaman` int(11) NOT NULL,
  `id_calon` int(11) NOT NULL,
  `nama_perusahaan` varchar(100) DEFAULT NULL,
  `posisi` varchar(100) DEFAULT NULL,
  `tahun_masuk` year(4) DEFAULT NULL,
  `tahun_keluar` year(4) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_remote`
--

CREATE TABLE `tb_remote` (
  `id_remote` int(11) NOT NULL,
  `ip_add` varchar(25) NOT NULL,
  `password` varchar(25) NOT NULL,
  `nama_desktop` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_remote`
--

INSERT INTO `tb_remote` (`id_remote`, `ip_add`, `password`, `nama_desktop`) VALUES
(3, '1 529 038 096', 'riyanap210896', 'AndyDesk Laptop Riyan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_sertifikasi`
--

CREATE TABLE `tb_sertifikasi` (
  `id_sertifikasi` int(11) NOT NULL,
  `id_calon` int(11) NOT NULL,
  `nama_sertifikasi` varchar(100) DEFAULT NULL,
  `penyelenggara` varchar(100) DEFAULT NULL,
  `tahun` year(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_user`
--

CREATE TABLE `tb_user` (
  `id_user` int(11) NOT NULL,
  `nip` varchar(30) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `role` enum('Kepala Ruangan','Staff') NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_user`
--

INSERT INTO `tb_user` (`id_user`, `nip`, `username`, `password`, `nama_lengkap`, `email`, `no_hp`, `role`, `foto`, `status`, `created_at`, `updated_at`) VALUES
(1, '097.011113', 'admin', 'admin', 'Qhusnul Arinda, Amd. Far', 'arien@gmail.com', '082130304411', 'Kepala Ruangan', NULL, 'aktif', '2024-11-30 16:00:00', '2025-07-14 08:10:31'),
(3, '12333', 'siswa', '12345', 'aaqqqssdddsss', 'kadatahunah@gmail.com', '', 'Staff', '1752481627_WhatsApp Image 2025-05-27 at 09.20.43.jpeg', 'aktif', '2025-07-13 16:13:58', '2025-07-14 08:27:07'),
(7, '635.090125', 'riyan', '12345', 'Riyan Aditya Pradana, S.Kom', 'riyanadityapradanaa@gmail.com', '', 'Staff', '1753876878_IMG_20250227_182823-removebg-preview.png', 'aktif', '2025-07-29 15:04:10', '2025-07-30 12:01:18');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tb_bahasa`
--
ALTER TABLE `tb_bahasa`
  ADD PRIMARY KEY (`id_bahasa`),
  ADD KEY `id_calon` (`id_calon`);

--
-- Indeks untuk tabel `tb_barang`
--
ALTER TABLE `tb_barang`
  ADD PRIMARY KEY (`kode_barang`);

--
-- Indeks untuk tabel `tb_calon`
--
ALTER TABLE `tb_calon`
  ADD PRIMARY KEY (`id_calon`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `tb_keahlian`
--
ALTER TABLE `tb_keahlian`
  ADD PRIMARY KEY (`id_keahlian`),
  ADD KEY `id_calon` (`id_calon`);

--
-- Indeks untuk tabel `tb_kegiatan_lembur`
--
ALTER TABLE `tb_kegiatan_lembur`
  ADD PRIMARY KEY (`id_kegiatan`),
  ADD KEY `id_lembur` (`id_lembur`);

--
-- Indeks untuk tabel `tb_lembur`
--
ALTER TABLE `tb_lembur`
  ADD PRIMARY KEY (`id_lembur`);

--
-- Indeks untuk tabel `tb_logbook`
--
ALTER TABLE `tb_logbook`
  ADD PRIMARY KEY (`id_log`);

--
-- Indeks untuk tabel `tb_organisasi`
--
ALTER TABLE `tb_organisasi`
  ADD PRIMARY KEY (`id_organisasi`),
  ADD KEY `id_calon` (`id_calon`);

--
-- Indeks untuk tabel `tb_pemindahan_barang`
--
ALTER TABLE `tb_pemindahan_barang`
  ADD PRIMARY KEY (`id_pemindahan`);

--
-- Indeks untuk tabel `tb_pendidikan`
--
ALTER TABLE `tb_pendidikan`
  ADD PRIMARY KEY (`id_pendidikan`),
  ADD KEY `id_calon` (`id_calon`);

--
-- Indeks untuk tabel `tb_pengajuan_barang`
--
ALTER TABLE `tb_pengajuan_barang`
  ADD PRIMARY KEY (`id_pengajuan`),
  ADD KEY `id_staff` (`id_staff`),
  ADD KEY `id_kepala` (`id_kepala`);

--
-- Indeks untuk tabel `tb_pengalaman`
--
ALTER TABLE `tb_pengalaman`
  ADD PRIMARY KEY (`id_pengalaman`),
  ADD KEY `id_calon` (`id_calon`);

--
-- Indeks untuk tabel `tb_remote`
--
ALTER TABLE `tb_remote`
  ADD PRIMARY KEY (`id_remote`);

--
-- Indeks untuk tabel `tb_sertifikasi`
--
ALTER TABLE `tb_sertifikasi`
  ADD PRIMARY KEY (`id_sertifikasi`),
  ADD KEY `id_calon` (`id_calon`);

--
-- Indeks untuk tabel `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `nip` (`nip`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tb_bahasa`
--
ALTER TABLE `tb_bahasa`
  MODIFY `id_bahasa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tb_calon`
--
ALTER TABLE `tb_calon`
  MODIFY `id_calon` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `tb_keahlian`
--
ALTER TABLE `tb_keahlian`
  MODIFY `id_keahlian` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tb_kegiatan_lembur`
--
ALTER TABLE `tb_kegiatan_lembur`
  MODIFY `id_kegiatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `tb_lembur`
--
ALTER TABLE `tb_lembur`
  MODIFY `id_lembur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `tb_logbook`
--
ALTER TABLE `tb_logbook`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `tb_organisasi`
--
ALTER TABLE `tb_organisasi`
  MODIFY `id_organisasi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tb_pendidikan`
--
ALTER TABLE `tb_pendidikan`
  MODIFY `id_pendidikan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tb_pengajuan_barang`
--
ALTER TABLE `tb_pengajuan_barang`
  MODIFY `id_pengajuan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `tb_pengalaman`
--
ALTER TABLE `tb_pengalaman`
  MODIFY `id_pengalaman` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tb_remote`
--
ALTER TABLE `tb_remote`
  MODIFY `id_remote` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `tb_sertifikasi`
--
ALTER TABLE `tb_sertifikasi`
  MODIFY `id_sertifikasi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `tb_bahasa`
--
ALTER TABLE `tb_bahasa`
  ADD CONSTRAINT `tb_bahasa_ibfk_1` FOREIGN KEY (`id_calon`) REFERENCES `tb_calon` (`id_calon`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_keahlian`
--
ALTER TABLE `tb_keahlian`
  ADD CONSTRAINT `tb_keahlian_ibfk_1` FOREIGN KEY (`id_calon`) REFERENCES `tb_calon` (`id_calon`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_kegiatan_lembur`
--
ALTER TABLE `tb_kegiatan_lembur`
  ADD CONSTRAINT `tb_kegiatan_lembur_ibfk_1` FOREIGN KEY (`id_lembur`) REFERENCES `tb_lembur` (`id_lembur`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_organisasi`
--
ALTER TABLE `tb_organisasi`
  ADD CONSTRAINT `tb_organisasi_ibfk_1` FOREIGN KEY (`id_calon`) REFERENCES `tb_calon` (`id_calon`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_pendidikan`
--
ALTER TABLE `tb_pendidikan`
  ADD CONSTRAINT `tb_pendidikan_ibfk_1` FOREIGN KEY (`id_calon`) REFERENCES `tb_calon` (`id_calon`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_pengalaman`
--
ALTER TABLE `tb_pengalaman`
  ADD CONSTRAINT `tb_pengalaman_ibfk_1` FOREIGN KEY (`id_calon`) REFERENCES `tb_calon` (`id_calon`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_sertifikasi`
--
ALTER TABLE `tb_sertifikasi`
  ADD CONSTRAINT `tb_sertifikasi_ibfk_1` FOREIGN KEY (`id_calon`) REFERENCES `tb_calon` (`id_calon`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
