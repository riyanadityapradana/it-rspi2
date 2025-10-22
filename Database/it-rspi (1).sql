-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 06, 2025 at 05:37 AM
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
  `pengajuan_id` int(11) DEFAULT NULL,
  `nama_barang` varchar(150) NOT NULL,
  `jenis_barang` enum('Komputer & Laptop','Komponen Komputer & Laptop','Printer & Scanner','Komponen Printer & Scanner','Komponen Network') NOT NULL,
  `nomor_seri` varchar(150) DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  `harga` decimal(15,2) DEFAULT NULL,
  `spesifikasi` text DEFAULT NULL,
  `tanggal_terima` date DEFAULT curdate(),
  `kondisi` enum('baru','bekas','rusak','dalam perbaikan','-') DEFAULT '-',
  `lokasi_id` int(11) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `foto` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_barang`
--

INSERT INTO `tb_barang` (`barang_id`, `pengajuan_id`, `nama_barang`, `jenis_barang`, `nomor_seri`, `ip_address`, `jumlah`, `harga`, `spesifikasi`, `tanggal_terima`, `kondisi`, `lokasi_id`, `keterangan`, `foto`) VALUES
(10, 4, 'Solution Digital Persona U are U 4500 Free SDK', 'Komponen Komputer & Laptop', 'SG20E17377', '', 1, 0.00, '- PC based\r\n- Need Komputer\r\n- Interface : USB Kabel\r\n- Free SDK : VB6, C++,C#, Java dan Linux', '2025-09-12', 'baru', 3, 'untuk mentes absensi SIMRS dan untuk cadangan mengganti finger bpjs di fo ralan', ''),
(11, NULL, 'Laptop lenovo thinkpad', 'Komputer & Laptop', '-', '192.168.1.104', 1, 0.00, 'Intel® Core™ i5-8350U CPU 1.70GHz\r\nRAM 16 GB, Stroge 238 GB, Stystem 64-bit, Sistem Operasi WIndows 11', '2025-06-11', 'baru', 1, 'Untuk penunjang akreditasi dan untuk acara agenda2 rapat kedepannya', ''),
(12, 3, 'POWER SUPLLY EZMAX 600W RGB 80 PLUS', 'Komponen Komputer & Laptop', 'T2521531', '', 1, 280000.00, 'Power Supply Varro Ezmax 600W RGB 80+ 8pin • Silent Fan 120mm • Optimum power output • Ultra High Efficiency • Built-in 120mm with RGB fan • 1 x 20 + 4 pin Motherboard • 1 x P4 + 4 pin CPU +12V • 1 x P6 + 2 pin PCI-E • 4 x Sata • 2 x Molex Garansi Resmi : 2 Tahun Spesifikasi: -Brand : Varro Prime -Capacity : 600 Watt -Version : Standard ATX -80+ -Input Current : 5A -Voltage : 230V -Frekuensi : 50-60Hz -Case Colour : Black -Fan Size : 12cm -Fan Colour : Rainbow RGB LED -CPU Cable : 8-Pin (452mm) Isi Paket: 1 X PSU 1 X Kabel Power', '2025-07-16', 'baru', 1, 'Untuk komputer Hadi IT', 'barang_68ca3e5015b59'),
(13, 8, 'ADAPTOR LCD/LED MONITOR LG', 'Komponen Komputer & Laptop', '-', '', 1, 70000.00, 'Adaptor LCD/LED Monitor LG 19V - 0,84A Original', '2025-06-21', 'baru', 1, 'Untuk Monitor Rizky IT karna punya dia di pasang di mba shobah Gizi', ''),
(14, 9, 'RAM 8 GB DDR 4 Merek KingSton dan V-Gen', 'Komponen Komputer & Laptop', '-', '', 2, 0.00, '-', '2025-07-03', 'baru', 1, 'Upgrade PC Desktop (Unit CPU / PC Rakitan) punya riyan it', ''),
(15, 10, 'V-GeN SSD 128 GB', 'Komponen Komputer & Laptop', '-', '', 1, 0.00, 'SSD 128GB V-GeN\r\n\r\nCapacity : 128GB\r\nDimensi : 100 x 70 x 6 mm\r\nSpeed : Read up to 510 MB/s\r\nWrite up to 410 MB/s\r\nInterface : SATA 3 - 6 GB/s\r\nForm Factor : 2.5 inch\r\nWarranty : 3 years one to one replacement\r\nType : Internal Storage\r\nSupported : UDMA Mode 6\r\nTRIM Support : Yes (Requires OS Support)\r\nGarbage Collection : Yes\r\nS.M.A.R.T : Yes\r\nWrite Cache : Yes\r\nHost Protect Area : Yes\r\nAPM : Yes\r\nNCQ : Yes\r\n48-Bit : Yes\r\nSecurity : AES 256-Bit Full Disk Encryption (FDE)\r\nTCG/Opal V2.0 , Encryption Drive (IEEE1667)\r\nVolume : +/- 20 gr', '2025-07-03', 'baru', 1, 'Mengganti SSD karna rusak bekas di pakai buat AKRE PC Desktop (Unit CPU / PC Rakitan) punya riyan it', ''),
(16, NULL, 'POWER SUPLLY EZMAX 600W RGB 80 PLUS', 'Komponen Komputer & Laptop', 'T2513680', '', 1, 0.00, 'Power Supply Varro Ezmax 600W RGB 80+ 8pin • Silent Fan 120mm • Optimum power output • Ultra High Efficiency • Built-in 120mm with RGB fan • 1 x 20 + 4 pin Motherboard • 1 x P4 + 4 pin CPU +12V • 1 x P6 + 2 pin PCI-E • 4 x Sata • 2 x Molex Garansi Resmi : 2 Tahun Spesifikasi: -Brand : Varro Prime -Capacity : 600 Watt -Version : Standard ATX -80+ -Input Current : 5A -Voltage : 230V -Frekuensi : 50-60Hz -Case Colour : Black -Fan Size : 12cm -Fan Colour : Rainbow RGB LED -CPU Cable : 8-Pin (452mm) Isi Paket: 1 X PSU 1 X Kabel Power', '2025-07-16', 'baru', 15, 'Untuk Mengganti komputer farmasi di ralan yang PSU nya konslet', 'barang_68ca3e3937268'),
(17, 11, 'Webcame Logitech C270 HD 720p', 'Komponen Komputer & Laptop', '-', '', 1, 320000.00, 'Menggunakan webcam C270 anda akan mendapatkan panggilan video HD 720p dan foto 3 Mega-pixel. Built-in mikrofonnya menggunakan teknologi RightSound yang menghasilkan percakapan yang jernih tanpa noise latar belakang yang mengganggu. Dalam cahaya remang-remang C270 secara otomatis akan menyesuaikan gambar menjadi lebih baik berkat RightLight teknologi. Mendukung aplikasi Skype, Google Hangouts, Yahoo Messenger dan aplikasi pesan instan popular lainnya. Sistem operasi : Windows XP (SP2 atau lebih baru), Windows vista, Windows 7 (32 bit atau 64 bit), Windows 8 dan Windows10. Spesifikasi Teknik : Panggilan video HD (1280 x 720 piksel) dengan sistem yang direkomendasikan. Perekaman video: Hingga 1280 x 720 piksel. Foto: Hingga 3,0 megapiksel (ditingkatkan menggunakan software). Mikrofon bawaan dengan teknologi Logitech RightSound. Bersertifikat Hi-Speed USB 2.0 (direkomendasikan). Klip universal cocok dengan berbagai laptop, monitor LCD atau CRT. Dimensi kemasan : Tinggi x Lebar x Tebal (cm) : 21 x 16 x 9. Isi Kemasan : - Webcam dengan kabel sepanjang 150 cm. - Dokumentasi pengguna.', '2025-03-03', 'baru', 4, 'Untuk penunjang Akreditasi dan kegiatan rapat menggunakan zoom dan lainnya', 'barang_68c7d219032d0'),
(21, NULL, 'Monitor Dell 20\"', 'Komponen Komputer & Laptop', '-', '', 1, 0.00, '-', '2025-09-17', 'bekas', 20, 'Punya Mas Hadi IT', 'barang_68ca2bbf44e8e'),
(22, 13, 'Uninterruptible Power Systems (UPS)', 'Komponen Komputer & Laptop', '-', '', 1, 1.00, '-', '2025-05-07', 'baru', 1, 'Untuk Komputer Riyan IT', 'barang_68ca535099e9b.jpeg'),
(23, NULL, 'PC Desktop (Unit CPU / PC Rakitan)', 'Komputer & Laptop', '-', '192.168.1.222', 1, 1.00, 'Intel® Core™ i5-14400F (16CPUs),~2.5 GHz\nRAM 16 GB, Stystem 64-bit, Sistem Operasi WIndows 11 Pro Education', '2023-01-01', 'baru', 10, 'Penempatan di Radiologi dalam samping Lab', 'barang_68cb8cb278b27.png'),
(24, NULL, 'PC DELL Desktop (Unit CPU / PC Rakitan)', 'Komputer & Laptop', '-', '192.168.1.234', 1, 1.00, 'Merek : DELL\r\nIntel® Core™ i5-9500 CPU @ 3.00 GHz(6CPUs),~3.0 GHz\r\nRAM 8 GB, Stystem 64-bit, Sistem Operasi WIndows 11 Pro', '2023-01-01', 'baru', 10, 'Penempatan di Radiologi dalam samping Lab', 'barang_68cb8f31d69f2.png'),
(25, NULL, 'PC Desktop (Unit CPU / PC Rakitan)', 'Komputer & Laptop', '-', '192.168.1.134', 1, 1.00, '12th Gen Intel® core™ i5-12400 (12 CPUs), ~2.5GHz\nRAM 16 GB, Stystem 64-bit, Sistem Operasi WIndows 11 Home', '2025-03-03', 'baru', 1, 'Penempatan di Ruang IT (PC UTAMA ALI)', 'barang_68cba80f33b7b.jpeg'),
(26, 13, 'Uninterruptible Power Systems (UPS)', 'Komponen Komputer & Laptop', '-', '', 1, 1.00, '-', '2025-05-07', 'baru', 1, 'Untuk Komputer ALI IT', 'barang_1758177555_8886.png'),
(27, NULL, 'Printer Epson L3210', 'Printer & Scanner', 'XAGKF68723', '', 1, 0.00, 'Epson L3210 adalah printer inkjet dengan teknologi EcoTank yang memungkinkan penggunaan tinta dalam jumlah besar. Resolusinya mencapai 5760 x 1440 dpi, jadi hasil cetaknya tajam, baik untuk dokumen hitam-putih maupun foto berwarna. Kecepatan cetaknya juga cukup oke, yaitu 10 ipm (halaman per menit) untuk hitam-putih dan 5 ipm untuk warna. Printer ini mendukung ukuran kertas A4, legal, hingga foto 4R, plus kompatibel dengan Windows dan Mac OS.\r\n\r\nBobotnya hanya 3,9 kg dengan dimensi 375 x 347 x 179 mm, jadi tidak makan tempat di meja kerja. Sayangnya, konektivitasnya masih mengandalkan USB 2.0, belum ada Wi-Fi seperti seri yang lebih tinggi.\r\n\r\nMeski begitu, untuk penggunaan sederhana, spesifikasi ini sudah cukup memadai. Dengan garansi hingga 2 tahun atau 30.000 cetakan, kamu juga bisa lebih tenang soal perawatan.', '2025-09-20', 'baru', 16, 'Diterima langsung oleh karu farmasi (Rollah), dan printer Epson L121 di tarik ke Unit IT', 'barang_68ce1c5fad7ca.jpeg'),
(28, NULL, 'Printer Epson L121', 'Komponen Printer & Scanner', 'X9LU382924', '', 1, 1.00, '-', '2025-09-20', 'bekas', 1, 'Bekas dari Farmasi rawat inap, karna di farmasi ranap sudah ada yang baru', 'barang_68ce1d9813869.jpeg'),
(29, NULL, 'Uninterruptible Power Systems (UPS)', 'Komponen Komputer & Laptop', '1B1QP2400866', '', 1, 0.00, 'Type : CE600D\r\nTegangan Input : 220 Vac\r\nFrekuensi Input : 50 Hz', '2025-09-09', 'baru', 5, 'Sudah di pasang dan di terima oleh karu kecubung, di pasang untuk kegunaan backup komputer & printer', 'barang_68db3563ed635.jpeg'),
(30, NULL, 'PC Desktop (Unit CPU / PC Rakitan)', 'Komputer & Laptop', '-', '192.168.30.167', 1, 0.00, 'Intel® core™ i3-4170 CPU @ 3.70GHz (4 CPUs), ~3.7GHz\r\nRAM 16 GB, Stystem 64-bit, Sistem Operasi WIndows 11 Home', '2025-09-09', 'bekas', 5, 'Ini komputer Mas Malik dan diserahkan ke kecubung karna permintaan dari karu kecubung ingin penambahan 1 pc', 'barang_68db375e0d9e8.jpeg'),
(31, NULL, 'Uninterruptible Power Systems (UPS)', 'Komponen Komputer & Laptop', '1B1QP2401638', '', 1, 0.00, 'Type : CE600D\nTegangan Input : 220 Vac\nFrekuensi Input : 50 Hz', '2025-09-09', 'baru', 6, 'Untuk membackup komputer dan printer dan diterima langsung oleh karu yakut c (bu atul) saat pemasangan', 'barang_68db46df7fcc5.jpeg'),
(32, NULL, 'Uninterruptible Power Systems (UPS)', 'Komponen Komputer & Laptop', '1B1QP2401663', '', 1, 0.00, 'Type : CE600D\r\nTegangan Input : 220 Vac\r\nFrekuensi Input : 50 Hz', '2025-09-09', 'baru', 22, 'di pasang di komputer dokter IGD', 'barang_68dcdded1528a.jpeg'),
(33, NULL, 'Uninterruptible Power Systems (UPS)', 'Komponen Komputer & Laptop', '1B1QP2400837', '', 1, 0.00, 'Type : CE600D\r\nTegangan Input : 220 Vac\r\nFrekuensi Input : 50 Hz', '2025-09-09', 'baru', 15, 'Sudah diserahkan oleh petugas farmasi ralan', 'barang_68dcde9fcd83d.jpeg'),
(34, NULL, 'RAM 8 GB DDR 3 Merek VENOMRX', 'Komponen Komputer & Laptop', 'T2301835', '', 1, 0.00, 'Product Name 8GB PC 12800\r\nKategori Memory RAM Komputer\r\nBrand VenomRX\r\nTahun Rilis 2012\r\nHardware\r\nMemory Module DIMM\r\nCompatible Device Desktop\r\nMulti-Channel Dual Channel Channel\r\nMemory\r\nKapasitas Memori 8 GB\r\nFrekuensi 1600 MHz\r\nMemory Type DDR3', '2025-09-10', 'baru', 22, 'di pasang di komputer Dokter IGD karna yang dulu cuman 4 GB saja RAM nya', 'barang_68dce0d0ebe54.jpeg'),
(35, NULL, 'Keyboard Logitech K120', 'Komponen Komputer & Laptop', '2320MR11CC48', '', 1, 1.00, 'https://www.logitech.com/en-hk/products/keyboards/k120-usb-standard-computer.920-002584.html', '2025-10-03', 'baru', 15, 'langsung di diganti karna yang lama rusak (info : mas hadi)', 'barang_68df263cd1fb7.jpeg'),
(36, NULL, 'SSD ADATA SU650 128GB', 'Komponen Komputer & Laptop', '-', '', 1, 1.00, '-', '2025-10-01', 'bekas', 22, 'SSD Bekas punya Dito IT  dipasang di komputer dokter IGD karna permasalahan yang sebelumnya sering mati sendiri dan bluscreen', 'barang_68df29dd1bb2d.jpg');

-- --------------------------------------------------------

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
-- Dumping data for table `tb_calon`
--

INSERT INTO `tb_calon` (`id_calon`, `username`, `password`, `nama_lengkap`, `email`, `no_hp`, `jenis_kelamin`, `tanggal_lahir`, `alamat`, `divisi_lamaran`, `foto`, `status`, `created_at`, `updated_at`) VALUES
(2, 'firda', '$2y$10$1oB36s/z0n87mzzfD1LzQuYCTUF1qBbqrvsb4LnNWzYrlZf8m32GC', 'www', NULL, '908221222222', NULL, NULL, NULL, NULL, NULL, 'belum tes', '2025-07-15 14:48:38', '2025-07-15 14:48:38');

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
(18, 10, 'printer yakut c habis tinta, terlanjur kosong sehingga harus di cleaning berkali-kali untuk menaikkan tinta ke dalam head printer');

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
(10, 3, '2025-10-02', '00:00:00', '00:00:00', 'Menunggu', NULL, '2025-10-02 07:59:55');

-- --------------------------------------------------------

--
-- Table structure for table `tb_logbook`
--

CREATE TABLE `tb_logbook` (
  `id_log` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `tanggal_log` date NOT NULL,
  `judul_log` varchar(200) NOT NULL,
  `deskripsi_log` text NOT NULL,
  `catatan_log` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_logbook`
--

INSERT INTO `tb_logbook` (`id_log`, `id_user`, `tanggal_log`, `judul_log`, `deskripsi_log`, `catatan_log`) VALUES
(12, 3, '2025-10-02', 'IT Support', 'Mouse Komputer Farmasi Ralan error klik', 'Restart Windows'),
(13, 3, '2025-10-02', 'IT Support', 'komputer Manajemen Elita sharing printer', 'belum dihidupkan dan kabel belum terpasang'),
(14, 3, '2025-10-01', 'IT Support', 'Bantu perbaiki CCTV mati, modem Akses Point dapur bawah direstart', 'cabut pasang adaptor listrik'),
(15, 3, '2025-10-01', 'IT Support', 'Backup dan install ulang windows komputer manajemen Elita', 'Manufaktur motherboard jadul susah terintegrasi antara ssd dan windows'),
(16, 3, '2025-10-03', 'IT Support', 'keyboard farmasi ralan error', 'ganti keyboard'),
(17, 3, '2025-10-03', 'IT Support', 'Printer Poli Mata tidak bisa dihidupkan', 'dibulak-balik kabel listrik');

-- --------------------------------------------------------

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
(22, 'IGD', 'Ini tempat nya bisa di dokter IGD atau Perawat IGD');

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
-- Dumping data for table `tb_mutasi_barang`
--

INSERT INTO `tb_mutasi_barang` (`mutasi_id`, `barang_id`, `lokasi_asal`, `lokasi_tujuan`, `tanggal_mutasi`, `id_user`, `keterangan`) VALUES
(3, 10, 1, 3, '2025-09-04', 5, 'Dipasang FO 5 di rawat jalan dikarenakan yang  di FO 5 rusak sudah tidak respon lagi fingernya'),
(4, 17, 1, 4, '2025-09-15', 5, 'Dikarenakan ada penambahan fitur di simrs yaitu di bagian persetujuan rawat inap yang memerlukan kamera untuk  mengambil gambar pasien'),
(5, 21, 1, 20, '2025-09-17', 5, 'Pemintaan dari mba arien karna bekas di ponek besarnya cuman 14\" aja');

-- --------------------------------------------------------

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
-- Dumping data for table `tb_pengajuan`
--

INSERT INTO `tb_pengajuan` (`pengajuan_id`, `id_user`, `nama_barang`, `unit`, `jumlah`, `perkiraan_harga`, `keterangan`, `status`, `tanggal_pengajuan`, `tanggal_acc`) VALUES
(3, 3, 'POWER SUPLLY EZMAX 600W RGB 80 PLUS', 'Unit IT', 0, 260000.00, 'Untuk hadi it', 'disetujui', '2025-07-07', '2025-07-10'),
(4, 5, 'Solution Digital Persona U are U 4500 Free SDK', 'Unit IT', 0, 1200000.00, 'untuk mentes absensi SIMRS dan untuk cadangan mengganti finger bpjs di fo ralan', 'disetujui', '2025-07-28', '2025-07-29'),
(5, 5, 'SSD M2 128 GB', 'Unit IT', 1, 200000.00, 'Upgrade Mini PC untuk Keperluan APM BPJS', 'disetujui', '2025-09-12', '2025-09-12'),
(6, 5, 'RAM 8 GB (Untuk Mini PC)', 'Unit IT', 1, 150000.00, 'Upgrade Mini PC untuk Keperluan APM BPJS', 'disetujui', '2025-09-12', '2025-09-12'),
(7, 5, 'Fingerprint Sidik Jari Fingerspot U are U 4500 USB PC Based', 'Unit IT', 1, 1500000.00, 'untuk Keperluan untuk mentes website APM BPJS dan nantinya akan di pakai buat APM BPJS Juga di bawah, jika tidak buat  cadangan di FO karna di FO ralan juga banyak mulai bermasalah', 'disetujui', '2025-09-12', '2025-09-12'),
(8, 5, 'ADAPTOR LCD/LED MONITOR LG', 'Unit IT', 0, 60000.00, 'Mengganti punya rizky', 'disetujui', '2025-06-09', '2025-06-20'),
(9, 5, 'RAM 8 GB DDR 4 Merek KingSton dan V-Gen', 'Unit IT', 0, 250000.00, 'Upgrade PC Desktop (Unit CPU / PC Rakitan) punya riyan it', 'disetujui', '2025-06-17', '2025-06-20'),
(10, 5, 'V-GeN SSD 128 GB', 'Unit IT', 0, 160000.00, 'Upgrade PC Desktop (Unit CPU / PC Rakitan) punya riyan it', 'disetujui', '2025-06-17', '2025-06-20'),
(11, 5, 'Webcame Logitech C270 HD 720p', 'Unit IT', 0, 320000.00, 'Untuk penunjang Akreditasi dan kegiatan rapat-rapat ketika Zoom', 'disetujui', '2025-02-18', '2025-02-24'),
(13, 5, 'Uninterruptible Power Systems (UPS)', 'Unit IT', 3, 1.00, 'Untuk semua komputer unit staff IT', 'disetujui', '2025-04-16', '2025-04-28'),
(15, 5, 'Kabel Duct TC5  Protector Pelindung Kabel', 'Unit IT', 8, 25000.00, 'Untuk merapikan kabel lan di counter lt 3 dan aula', 'disetujui', '2025-09-23', '2025-09-24');

-- --------------------------------------------------------

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
-- Table structure for table `tb_perbaikan_barang`
--

CREATE TABLE `tb_perbaikan_barang` (
  `perbaikan_id` int(11) NOT NULL,
  `barang_id` int(11) NOT NULL,
  `tanggal_lapor` date DEFAULT curdate(),
  `deskripsi_kerusakan` text DEFAULT NULL,
  `tindakan_perbaikan` enum('Service luar','Service sendiri','-') DEFAULT '-',
  `status` enum('diajukan','proses','selesai','tidak_dapat_diperbaiki') DEFAULT 'diajukan',
  `tanggal_selesai` date DEFAULT NULL,
  `biaya_perbaikan` decimal(15,2) DEFAULT 0.00,
  `teknisi` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

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
(8, '1051896797', 'unitit2025', 'LALA PC'),
(9, '1830046703', 'unitit2025', 'Ltp Dell Hitam'),
(10, '1094780395', 'unitit2025', 'ALI PC'),
(11, '446013800', 'unitit2025', 'Shobbah Gizi'),
(12, '429134846', 'unitit2025', 'FO UGD'),
(13, '882946671', 'unitit2025', 'Akbar PC'),
(14, '1347725702', 'unitit2025', 'TAB IGD'),
(15, '1 598 186 144', 'unitit2025', 'Liza PC'),
(16, '1801141769', 'unitit2025', 'dr Hari (PC Dirumah)'),
(17, '298558894/192.168.1.222', 'unitit2025', 'Radiologi (1)'),
(18, '1657319494/192.168.1.12', 'pelita66 / unitit2025', 'Hadi-IT'),
(19, '777978487/192.168.1.234', 'unitit2025', 'Radiologi (2)');

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

INSERT INTO `tb_user` (`id_user`, `nip`, `username`, `password`, `nama_lengkap`, `email`, `no_hp`, `role`, `foto`, `status`, `created_at`, `updated_at`) VALUES
(1, '097.011113', 'admin', 'admin', 'Qhusnul Arinda, Amd. Far', 'arien@gmail.com', '082130304411', 'Kepala Ruangan', '1753849951_004170300_1636348075-young-man-engineer-making-program-analyses_1303-20402.png', 'aktif', '2024-11-30 16:00:00', '2025-07-30 04:32:31'),
(2, '1', 'ali', '12345', 'Ali Iwansyah', 'unititrspi@gmail.com', '08125353489', 'Staff', '1758164309_depositphotos_316270922-stock-illustration-salesman-employee-providing-a-customer.jpg', 'aktif', '2025-09-18 02:58:29', '2025-09-18 03:01:37'),
(3, '527.010623', 'hadi', '12345', 'Abdul Hadi, S.Kom', 'unititrspi@gmail.com', '085822823436', 'Staff', '1758164121_depositphotos_316270922-stock-illustration-salesman-employee-providing-a-customer.jpg', 'aktif', '2025-09-13 04:29:58', '2025-09-18 02:59:38'),
(4, '629.271224', 'rizki', '12345', 'Muhammad Rizki Ilham Pratama', 'unititrspi@gmail.com', '', 'Staff', '1758164461_depositphotos_316270922-stock-illustration-salesman-employee-providing-a-customer.jpg', 'aktif', '2025-09-18 03:01:01', '2025-09-18 03:01:43'),
(5, '635.090125', 'riyan', '12345', 'Riyan Aditya Pradana, S.Kom', 'riyanadityapradanaa@gmail.com', '082130304411', 'Staff', '1753883681_1753801450_IMG_20250227_182823-removebg-preview.png', 'aktif', '2025-03-11 16:00:00', '2025-07-30 13:54:41');

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
  MODIFY `barang_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `tb_calon`
--
ALTER TABLE `tb_calon`
  MODIFY `id_calon` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_keahlian`
--
ALTER TABLE `tb_keahlian`
  MODIFY `id_keahlian` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_kegiatan_lembur`
--
ALTER TABLE `tb_kegiatan_lembur`
  MODIFY `id_kegiatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tb_lembur`
--
ALTER TABLE `tb_lembur`
  MODIFY `id_lembur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tb_logbook`
--
ALTER TABLE `tb_logbook`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tb_lokasi`
--
ALTER TABLE `tb_lokasi`
  MODIFY `lokasi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `tb_mutasi_barang`
--
ALTER TABLE `tb_mutasi_barang`
  MODIFY `mutasi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `pengajuan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tb_pengalaman`
--
ALTER TABLE `tb_pengalaman`
  MODIFY `id_pengalaman` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_perbaikan_barang`
--
ALTER TABLE `tb_perbaikan_barang`
  MODIFY `perbaikan_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_remote`
--
ALTER TABLE `tb_remote`
  MODIFY `id_remote` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `tb_sertifikasi`
--
ALTER TABLE `tb_sertifikasi`
  MODIFY `id_sertifikasi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
