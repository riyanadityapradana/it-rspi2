-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 30 Jul 2025 pada 15.48
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

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tb_pemindahan_barang`
--
ALTER TABLE `tb_pemindahan_barang`
  ADD PRIMARY KEY (`id_pemindahan`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
