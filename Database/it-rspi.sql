-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 14 Jul 2025 pada 16.54
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
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('belum tes','tes selesai','lulus','tidak lulus') DEFAULT 'belum tes',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_calon`
--

INSERT INTO `tb_calon` (`id_calon`, `username`, `password`, `nama_lengkap`, `email`, `no_hp`, `jenis_kelamin`, `tanggal_lahir`, `alamat`, `foto`, `status`, `created_at`, `updated_at`) VALUES
(1, 'rendy', '12345', 'Ahmad Rendy F', 'rendyf@gmail.com', '082130304411', 'L', '2022-08-16', '-', NULL, 'belum tes', '2025-07-13 15:09:59', '2025-07-13 15:09:59');

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
(5, '635.090125', 'riyan', '12345', 'Riyan Aditya Pradana, S.Kom', 'riyanadityapradanaa@gmail.com', '082130304411', 'Staff', NULL, 'aktif', '2025-03-11 16:00:00', '2025-07-14 14:50:27');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tb_calon`
--
ALTER TABLE `tb_calon`
  ADD PRIMARY KEY (`id_calon`),
  ADD UNIQUE KEY `username` (`username`);

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
-- Indeks untuk tabel `tb_remote`
--
ALTER TABLE `tb_remote`
  ADD PRIMARY KEY (`id_remote`);

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
-- AUTO_INCREMENT untuk tabel `tb_calon`
--
ALTER TABLE `tb_calon`
  MODIFY `id_calon` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT untuk tabel `tb_remote`
--
ALTER TABLE `tb_remote`
  MODIFY `id_remote` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `tb_kegiatan_lembur`
--
ALTER TABLE `tb_kegiatan_lembur`
  ADD CONSTRAINT `tb_kegiatan_lembur_ibfk_1` FOREIGN KEY (`id_lembur`) REFERENCES `tb_lembur` (`id_lembur`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
