-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 26, 2026 at 07:35 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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
-- Table structure for table `tb_bahasa`
--

CREATE TABLE `tb_bahasa` (
  `id_bahasa` int(11) NOT NULL,
  `id_calon` int(11) NOT NULL,
  `nama_bahasa` varchar(50) DEFAULT NULL,
  `tingkat` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_barang`
--

CREATE TABLE `tb_barang` (
  `barang_id` int(11) NOT NULL,
  `kode_inventaris` varchar(50) NOT NULL,
  `pengajuan_id` int(11) DEFAULT NULL,
  `nama_barang` varchar(150) NOT NULL,
  `jenis_barang` enum('Komputer & Laptop','Komponen Komputer & Laptop','Printer & Scanner','Komponen Printer & Scanner','Komponen Network','Kamera & Aksesoris','Perangkat Mobile','Aksesoris Perangkat Mobile') NOT NULL,
  `nomor_seri` varchar(150) DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  `spesifikasi` text DEFAULT NULL,
  `tanggal_terima` date DEFAULT curdate(),
  `kondisi` enum('baru','bekas','rusak','dalam perbaikan','-') DEFAULT '-',
  `lokasi_id` int(11) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `foto` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-----------------------------------------------------

--
-- Table structure for table `tb_calon`
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

--
-- Table structure for table `tb_cuti`
--

CREATE TABLE `tb_cuti` (
  `id_cuti` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nip` varchar(30) NOT NULL,
  `jenis_cuti` enum('Izin Cuti Melahirkan','Izin Cuti Tahunan','Izin Cuti Menikah') NOT NULL,
  `banyak_hari` int(11) NOT NULL,
  `mulai_tanggal` date NOT NULL,
  `sampai_tanggal` date NOT NULL,
  `masuk_tanggal` date NOT NULL,
  `alasan` varchar(250) NOT NULL,
  `status_lembur` enum('Menunggu','Diterima','Ditolak') NOT NULL,
  `id_pimpinan` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_cuti`
--

INSERT INTO `tb_cuti` (`id_cuti`, `id_user`, `nip`, `jenis_cuti`, `banyak_hari`, `mulai_tanggal`, `sampai_tanggal`, `masuk_tanggal`, `alasan`, `status_lembur`, `id_pimpinan`) VALUES
(2, 12, '635.090125', 'Izin Cuti Melahirkan', 2, '2026-02-25', '2026-02-26', '2026-02-27', '', 'Diterima', 10),
(3, 1, '234.030221', 'Izin Cuti Melahirkan', 6, '2026-05-04', '2026-05-09', '2026-02-10', 'keluar kota', 'Menunggu', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tb_keahlian`
--

CREATE TABLE `tb_keahlian` (
  `id_keahlian` int(11) NOT NULL,
  `id_calon` int(11) NOT NULL,
  `nama_keahlian` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_kegiatan_lembur`
--

CREATE TABLE `tb_kegiatan_lembur` (
  `id_kegiatan` int(11) NOT NULL,
  `id_lembur` int(11) NOT NULL,
  `kegiatan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_kegiatan_lembur`
--

INSERT INTO `tb_kegiatan_lembur` (`id_kegiatan`, `id_lembur`, `kegiatan`) VALUES
(23, 13, 'hteh'),
(24, 13, 'eee2'),
(25, 13, 'rer');

-- --------------------------------------------------------

--
-- Table structure for table `tb_lembur`
--

CREATE TABLE `tb_lembur` (
  `id_lembur` int(11) NOT NULL,
  `id_staff` int(11) NOT NULL,
  `tanggal_lembur` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `status_lembur` enum('Menunggu','Diterima','Ditolak') DEFAULT 'Menunggu',
  `id_pimpinan` int(11) DEFAULT NULL,
  `waktu_input` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_lembur`
--

INSERT INTO `tb_lembur` (`id_lembur`, `id_staff`, `tanggal_lembur`, `jam_mulai`, `jam_selesai`, `status_lembur`, `id_pimpinan`, `waktu_input`) VALUES
(13, 12, '2026-02-24', '16:37:00', '18:39:00', 'Diterima', 10, '2026-02-24 06:36:08');

-- --------------------------------------------------------

--
-- Table structure for table `tb_logbook`
--

CREATE TABLE `tb_logbook` (
  `id_log` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `tanggal_log` datetime DEFAULT NULL,
  `judul_log` varchar(200) NOT NULL,
  `deskripsi_log` text NOT NULL,
  `catatan_log` text NOT NULL,
  `tanggal_selesai` datetime DEFAULT NULL,
  `status_log` varchar(50) DEFAULT 'Belum'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `tb_lokasi`
--

CREATE TABLE `tb_lokasi` (
  `lokasi_id` int(11) NOT NULL,
  `nama_lokasi` varchar(100) NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_lokasi`
--

INSERT INTO `tb_lokasi` (`lokasi_id`, `nama_lokasi`, `keterangan`) VALUES
(1, 'IT', 'Unit IT dan Komputer'),
(2, 'Keuangan', 'Unit Keuangan'),
(3, 'FO Ralan', 'Front Office atau Pendaftaran Rawat Jalan'),
(4, 'FO Ranap (IGD)', 'Front Office atau Pendaftaran Rawat Inap (IGD)'),
(5, 'Kecubung', 'Counter Kecubung'),
(6, 'Yakut C', 'Counter Yakut C'),
(7, 'Counter Lt.3', 'Counter Rawat Inap Lt.3'),
(8, 'Counter Lt.2', 'Counter Rawat Inap Lt.2'),
(9, 'Manajemen', 'Unit Manajemen'),
(10, 'Radiologi', 'Ruang Radiologi'),
(11, 'Lab', 'Ruang Laboratorium'),
(12, 'PL Anak', 'Poliklinik Anak'),
(13, 'PL Kandungan', 'Poliklinik Kandungan'),
(14, 'PL Penyakit Dalam', 'Poliklinik Penyakit Dalam'),
(15, 'Farmasi Ralan', 'Farmasai Rawat Jalan'),
(16, 'Farmasi Ranap', 'Farmasai Rawat Inap'),
(17, 'Pelayanan Medis, Keperawatan & MPP', 'Ruang Pelayanan Medis, Keperawatan & MPP'),
(18, 'Gizi', 'Ruang Gizi'),
(19, 'Farmasi', 'Farmasi Tengah/ditempat Karu Farmasi'),
(20, 'PONEK', 'PONEK Lt.1'),
(21, 'Kasir', 'Kasir Lt.1'),
(22, 'IGD', 'Ini tempat nya bisa di dokter IGD atau Perawat IGD'),
(23, 'Rekam Medik (RM)', '-'),
(24, 'PL Mata', 'Poli Mata'),
(25, 'PL Paru', 'Poli Paru'),
(26, 'PL Gigi', 'Poli GIGI'),
(27, 'PL Rehab', 'Poli Rehab Medik'),
(28, 'R.Direktur', 'Ruangan Direktur'),
(29, 'R.Komdik 1', 'Ruangan Rapat Komdik 1'),
(30, 'R.Komdik 2', 'Ruangan Rapat Komdik 2'),
(31, 'R. Aula RSPI', 'Ruangan Rapat Aula RS'),
(32, 'TU', 'Tata Usaha'),
(33, 'PL Syaraf', 'Poli Syaraf Dr. Made'),
(34, 'HD', 'Ruang Hemodialisa');

-- --------------------------------------------------------

--
-- Table structure for table `tb_mutasi_barang`
--

CREATE TABLE `tb_mutasi_barang` (
  `mutasi_id` int(11) NOT NULL,
  `barang_id` int(11) NOT NULL,
  `lokasi_asal` int(11) DEFAULT NULL,
  `lokasi_tujuan` int(11) DEFAULT NULL,
  `tanggal_mutasi` date DEFAULT curdate(),
  `id_user` int(11) DEFAULT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `tb_organisasi`
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
-- Table structure for table `tb_pendidikan`
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
-- Table structure for table `tb_pengajuan`
--

CREATE TABLE `tb_pengajuan` (
  `pengajuan_id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `nama_barang` varchar(150) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `perkiraan_harga` decimal(15,2) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `status` enum('diajukan','disetujui','ditolak','selesai') DEFAULT 'diajukan',
  `tanggal_pengajuan` date DEFAULT curdate(),
  `tanggal_acc` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `tb_pengalaman`
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
-- Table structure for table `tb_penyerahan`
--

CREATE TABLE `tb_penyerahan` (
  `penyerahan_id` int(11) NOT NULL,
  `barang_id` int(11) NOT NULL,
  `lokasi_id` int(11) NOT NULL,
  `kondisi` enum('baru','bekas','rusak','dalam perbaikan') NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Table structure for table `tb_perbaikan_barang`
--

CREATE TABLE `tb_perbaikan_barang` (
  `perbaikan_id` int(11) NOT NULL,
  `barang_id` int(11) NOT NULL,
  `tanggal_lapor` datetime DEFAULT NULL,
  `penyerahan_id` int(11) NOT NULL,
  `deskripsi_kerusakan` text DEFAULT NULL,
  `tindakan_perbaikan` enum('Service luar','Service sendiri','-') DEFAULT '-',
  `status` enum('diajukan','proses','selesai','tidak_dapat_diperbaiki') DEFAULT 'diajukan',
  `tanggal_selesai` datetime DEFAULT NULL,
  `teknisi` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `unit_melapor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `tb_remote`
--

CREATE TABLE `tb_remote` (
  `id_remote` int(11) NOT NULL,
  `ip_add` varchar(25) NOT NULL,
  `password` varchar(25) NOT NULL,
  `nama_desktop` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_remote`
--

INSERT INTO `tb_remote` (`id_remote`, `ip_add`, `password`, `nama_desktop`) VALUES
(3, '631 825 847', 'riyanap210896', 'AndyDesk Laptop Riyan'),
(5, '1204420211', 'unitit2025', 'Lpt Thinkpad'),
(6, '1489744944', 'unitit2025', 'Ltp Assus Silver'),
(7, '323572946', 'unitit2025', 'Komp IGD Dr'),
(8, '1051896797/192.168.1.157', 'unitit2025', 'LALA PC'),
(9, '1830046703', 'unitit2025', 'Ltp Dell Hitam'),
(10, '1094780395', 'unitit2025', 'ALI PC'),
(11, '446013800', 'unitit2025', 'Shobbah Gizi'),
(12, '429134846', 'unitit2025', 'FO UGD'),
(13, '882946671', 'unitit2025', 'Akbar PC'),
(14, '1347725702', 'unitit2025', 'TAB IGD'),
(15, '1401148702/192.168.1.85', 'unitit2025', 'Liza PC'),
(16, '1801141769', 'unitit2025', 'dr Hari (PC Dirumah)'),
(17, '298558894/192.168.1.222', 'unitit2025', 'Radiologi (1)'),
(18, '1657319494/192.168.1.12', 'pelita66 / unitit2025', 'Hadi-IT'),
(19, '777978487/192.168.1.234', 'unitit2025', 'Radiologi (2)'),
(20, '1680432738/192.168.30.139', 'unitit2025', 'Kecubung Depan'),
(21, '759422039/192.168.1.199', 'unitit2025', 'Komp Mba Dian Lt.3'),
(22, '1860847190/192.168.30.42', 'pelita66', 'Yakut C (Tengah)'),
(23, '1192673838/192.168.30.197', 'unitit2025', 'Yakut C (Bu atul)'),
(24, '1728108092/192.168.1.189', 'unitit2025', 'Dapur Gizi Lt.4'),
(25, '1511302749', 'unitit2025', 'Poli GIGI'),
(26, '1189546646', 'unitit2025', 'Mba Yeni'),
(27, '1736522227', 'unitit2025', 'ICU'),
(28, '1491443216', '-', 'Mba Yuna Mjm'),
(29, '1009268831/192.168.2.81', 'unitit2025', 'Komputer Dr Made');

-- --------------------------------------------------------

--
-- Table structure for table `tb_sertifikasi`
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
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id_user` int(11) NOT NULL,
  `nip` varchar(30) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `tmp_lahir` varchar(50) NOT NULL,
  `tgl_lahir` date NOT NULL DEFAULT current_timestamp(),
  `jbtn` varchar(50) NOT NULL,
  `pendidikan` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `role` enum('Kepala Ruangan','Staff') NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id_user`, `nip`, `username`, `password`, `nama_lengkap`, `tmp_lahir`, `tgl_lahir`, `jbtn`, `pendidikan`, `alamat`, `email`, `no_hp`, `role`, `foto`, `status`, `created_at`, `updated_at`) VALUES
(1, '234.030221', 'ali', '$2y$10$AKkVgsZmhsXun4wHTaxoyewmYuWJJtgPAQ12y1CJJPSY7K7a3I71y', 'ALI IWANSYAH', '', '2026-02-25', '', '', '', 'unititrspi@gmail.com', '081253534891', 'Staff', 'user_1_699e90ca70218.jpeg', 'aktif', '2026-01-10 03:24:23', '2026-02-25 06:03:54'),
(2, '662.140725', 'ika', '$2y$10$nhtEVX3lQCQGqHpu0B120O6kN/5j.XfGxFY/GQxecTRdAh4NK9jpW', 'Ika Aprillia, S.Kom', '', '2026-02-25', '', '', '', 'unititrspi@gmail.com', '087753560464', 'Staff', 'user_2_699471dbcd340.png', 'aktif', '2026-01-10 03:21:34', '2026-02-17 13:49:15'),
(3, '22', 'da', '12345', 'dss', '', '2026-02-25', '', '', '', 'ss', '22', 'Staff', '1758164121_depositphotos_316270922-stock-illustration-salesman-employee-providing-a-customer.jpg', 'nonaktif', '2025-09-13 04:29:58', '2026-02-24 01:03:54'),
(4, '629.271224', 'rizki', '12345', 'Muhammad Rizki Ilham Pratama', '', '2026-02-25', '', '', '', 'unititrspi@gmail.com', '', 'Staff', '1758164461_depositphotos_316270922-stock-illustration-salesman-employee-providing-a-customer.jpg', 'aktif', '2025-09-18 03:01:01', '2025-09-18 03:01:43'),
(10, '097.011113', 'admin', '$2y$10$SzwDz8O2teScUtfw2pzMau.jdreAWywzeJi9j0UQFQFYC27VqUO1O', 'Qhusnul Arinda, Amd. Far', '', '2026-02-25', '', '', '', 'qhusnl.arienda@gmail.com', '085751094503', 'Kepala Ruangan', '../assets/upload/1768006227_depositphotos_531920820-stock-illustration-photo-available-vector-icon-default.jpg', 'aktif', '2026-01-10 00:50:27', '2026-01-10 00:50:27'),
(12, '635.090125', 'riyan', '$2y$10$akXHkBMhThGUY2bD0hAfSuVkQimRQu0p8DriEx/jaeCfK/Fjnxeju', 'Riyan Aditya Pradana, S.Kom', 'Banjarbaru', '1996-08-21', 'STAFFIT', 'S1 Teknik Informatika', 'Komp. Mustika Raya Permai I Blok C 6', 'riyanadityapradanaa@gmail.com', '082130304411', 'Staff', '635.090125.png', 'aktif', '2026-01-10 01:05:21', '2026-02-26 06:15:38'),
(13, '527.010623', 'hadi', '$2y$10$bgOA8.B/1SphzBuXSVGMbe86U9Ibfn0wFK0.4Qmp.eR6bLifj6aUC', 'Abdul Hadi, S.Kom', '', '2026-02-25', '', '', '', 'unititrspi@gmail.com', '085822823436', 'Staff', '../assets/upload/1768015138_depositphotos_531920820-stock-illustration-photo-available-vector-icon-default.jpg', 'aktif', '2026-01-10 03:18:58', '2026-01-10 03:18:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_bahasa`
--
ALTER TABLE `tb_bahasa`
  ADD PRIMARY KEY (`id_bahasa`),
  ADD KEY `id_calon` (`id_calon`);

--
-- Indexes for table `tb_barang`
--
ALTER TABLE `tb_barang`
  ADD PRIMARY KEY (`barang_id`),
  ADD KEY `pengajuan_id` (`pengajuan_id`);

--
-- Indexes for table `tb_calon`
--
ALTER TABLE `tb_calon`
  ADD PRIMARY KEY (`id_calon`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `tb_cuti`
--
ALTER TABLE `tb_cuti`
  ADD PRIMARY KEY (`id_cuti`);

--
-- Indexes for table `tb_keahlian`
--
ALTER TABLE `tb_keahlian`
  ADD PRIMARY KEY (`id_keahlian`),
  ADD KEY `id_calon` (`id_calon`);

--
-- Indexes for table `tb_kegiatan_lembur`
--
ALTER TABLE `tb_kegiatan_lembur`
  ADD PRIMARY KEY (`id_kegiatan`),
  ADD KEY `id_lembur` (`id_lembur`);

--
-- Indexes for table `tb_lembur`
--
ALTER TABLE `tb_lembur`
  ADD PRIMARY KEY (`id_lembur`);

--
-- Indexes for table `tb_logbook`
--
ALTER TABLE `tb_logbook`
  ADD PRIMARY KEY (`id_log`);

--
-- Indexes for table `tb_lokasi`
--
ALTER TABLE `tb_lokasi`
  ADD PRIMARY KEY (`lokasi_id`);

--
-- Indexes for table `tb_mutasi_barang`
--
ALTER TABLE `tb_mutasi_barang`
  ADD PRIMARY KEY (`mutasi_id`),
  ADD KEY `barang_id` (`barang_id`),
  ADD KEY `lokasi_asal` (`lokasi_asal`),
  ADD KEY `lokasi_tujuan` (`lokasi_tujuan`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `tb_organisasi`
--
ALTER TABLE `tb_organisasi`
  ADD PRIMARY KEY (`id_organisasi`),
  ADD KEY `id_calon` (`id_calon`);

--
-- Indexes for table `tb_pendidikan`
--
ALTER TABLE `tb_pendidikan`
  ADD PRIMARY KEY (`id_pendidikan`),
  ADD KEY `id_calon` (`id_calon`);

--
-- Indexes for table `tb_pengajuan`
--
ALTER TABLE `tb_pengajuan`
  ADD PRIMARY KEY (`pengajuan_id`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `tb_pengalaman`
--
ALTER TABLE `tb_pengalaman`
  ADD PRIMARY KEY (`id_pengalaman`),
  ADD KEY `id_calon` (`id_calon`);

--
-- Indexes for table `tb_penyerahan`
--
ALTER TABLE `tb_penyerahan`
  ADD PRIMARY KEY (`penyerahan_id`),
  ADD KEY `barang_id` (`barang_id`),
  ADD KEY `lokasi_id` (`lokasi_id`);

--
-- Indexes for table `tb_perbaikan_barang`
--
ALTER TABLE `tb_perbaikan_barang`
  ADD PRIMARY KEY (`perbaikan_id`),
  ADD KEY `barang_id` (`barang_id`);

--
-- Indexes for table `tb_remote`
--
ALTER TABLE `tb_remote`
  ADD PRIMARY KEY (`id_remote`);

--
-- Indexes for table `tb_sertifikasi`
--
ALTER TABLE `tb_sertifikasi`
  ADD PRIMARY KEY (`id_sertifikasi`),
  ADD KEY `id_calon` (`id_calon`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `nip` (`nip`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_bahasa`
--
ALTER TABLE `tb_bahasa`
  MODIFY `id_bahasa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_barang`
--
ALTER TABLE `tb_barang`
  MODIFY `barang_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `tb_calon`
--
ALTER TABLE `tb_calon`
  MODIFY `id_calon` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_cuti`
--
ALTER TABLE `tb_cuti`
  MODIFY `id_cuti` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_keahlian`
--
ALTER TABLE `tb_keahlian`
  MODIFY `id_keahlian` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_kegiatan_lembur`
--
ALTER TABLE `tb_kegiatan_lembur`
  MODIFY `id_kegiatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `tb_lembur`
--
ALTER TABLE `tb_lembur`
  MODIFY `id_lembur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tb_logbook`
--
ALTER TABLE `tb_logbook`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

--
-- AUTO_INCREMENT for table `tb_lokasi`
--
ALTER TABLE `tb_lokasi`
  MODIFY `lokasi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `tb_mutasi_barang`
--
ALTER TABLE `tb_mutasi_barang`
  MODIFY `mutasi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `tb_organisasi`
--
ALTER TABLE `tb_organisasi`
  MODIFY `id_organisasi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_pendidikan`
--
ALTER TABLE `tb_pendidikan`
  MODIFY `id_pendidikan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_pengajuan`
--
ALTER TABLE `tb_pengajuan`
  MODIFY `pengajuan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `tb_pengalaman`
--
ALTER TABLE `tb_pengalaman`
  MODIFY `id_pengalaman` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_penyerahan`
--
ALTER TABLE `tb_penyerahan`
  MODIFY `penyerahan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `tb_perbaikan_barang`
--
ALTER TABLE `tb_perbaikan_barang`
  MODIFY `perbaikan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tb_remote`
--
ALTER TABLE `tb_remote`
  MODIFY `id_remote` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `tb_sertifikasi`
--
ALTER TABLE `tb_sertifikasi`
  MODIFY `id_sertifikasi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_bahasa`
--
ALTER TABLE `tb_bahasa`
  ADD CONSTRAINT `tb_bahasa_ibfk_1` FOREIGN KEY (`id_calon`) REFERENCES `tb_calon` (`id_calon`) ON DELETE CASCADE;

--
-- Constraints for table `tb_barang`
--
ALTER TABLE `tb_barang`
  ADD CONSTRAINT `tb_barang_ibfk_1` FOREIGN KEY (`pengajuan_id`) REFERENCES `tb_pengajuan` (`pengajuan_id`);

--
-- Constraints for table `tb_keahlian`
--
ALTER TABLE `tb_keahlian`
  ADD CONSTRAINT `tb_keahlian_ibfk_1` FOREIGN KEY (`id_calon`) REFERENCES `tb_calon` (`id_calon`) ON DELETE CASCADE;

--
-- Constraints for table `tb_kegiatan_lembur`
--
ALTER TABLE `tb_kegiatan_lembur`
  ADD CONSTRAINT `tb_kegiatan_lembur_ibfk_1` FOREIGN KEY (`id_lembur`) REFERENCES `tb_lembur` (`id_lembur`) ON DELETE CASCADE;

--
-- Constraints for table `tb_mutasi_barang`
--
ALTER TABLE `tb_mutasi_barang`
  ADD CONSTRAINT `tb_mutasi_barang_ibfk_1` FOREIGN KEY (`barang_id`) REFERENCES `tb_barang` (`barang_id`),
  ADD CONSTRAINT `tb_mutasi_barang_ibfk_2` FOREIGN KEY (`lokasi_asal`) REFERENCES `tb_lokasi` (`lokasi_id`),
  ADD CONSTRAINT `tb_mutasi_barang_ibfk_3` FOREIGN KEY (`lokasi_tujuan`) REFERENCES `tb_lokasi` (`lokasi_id`),
  ADD CONSTRAINT `tb_mutasi_barang_ibfk_4` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`);

--
-- Constraints for table `tb_organisasi`
--
ALTER TABLE `tb_organisasi`
  ADD CONSTRAINT `tb_organisasi_ibfk_1` FOREIGN KEY (`id_calon`) REFERENCES `tb_calon` (`id_calon`) ON DELETE CASCADE;

--
-- Constraints for table `tb_pendidikan`
--
ALTER TABLE `tb_pendidikan`
  ADD CONSTRAINT `tb_pendidikan_ibfk_1` FOREIGN KEY (`id_calon`) REFERENCES `tb_calon` (`id_calon`) ON DELETE CASCADE;

--
-- Constraints for table `tb_pengajuan`
--
ALTER TABLE `tb_pengajuan`
  ADD CONSTRAINT `tb_pengajuan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`);

--
-- Constraints for table `tb_pengalaman`
--
ALTER TABLE `tb_pengalaman`
  ADD CONSTRAINT `tb_pengalaman_ibfk_1` FOREIGN KEY (`id_calon`) REFERENCES `tb_calon` (`id_calon`) ON DELETE CASCADE;

--
-- Constraints for table `tb_penyerahan`
--
ALTER TABLE `tb_penyerahan`
  ADD CONSTRAINT `tb_penyerahan_ibfk_1` FOREIGN KEY (`barang_id`) REFERENCES `tb_barang` (`barang_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tb_penyerahan_ibfk_2` FOREIGN KEY (`lokasi_id`) REFERENCES `tb_lokasi` (`lokasi_id`);

--
-- Constraints for table `tb_perbaikan_barang`
--
ALTER TABLE `tb_perbaikan_barang`
  ADD CONSTRAINT `tb_perbaikan_barang_ibfk_1` FOREIGN KEY (`barang_id`) REFERENCES `tb_barang` (`barang_id`);

--
-- Constraints for table `tb_sertifikasi`
--
ALTER TABLE `tb_sertifikasi`
  ADD CONSTRAINT `tb_sertifikasi_ibfk_1` FOREIGN KEY (`id_calon`) REFERENCES `tb_calon` (`id_calon`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
