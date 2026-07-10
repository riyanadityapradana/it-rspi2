-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 30, 2026 at 03:10 PM
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
  `jenis_barang` enum('Komputer & Laptop','Komponen Komputer & Laptop','Printer & Scanner','Komponen Printer & Scanner','Komponen Network','Kamera & Aksesoris','Perangkat Mobile','Aksesoris Perangkat Mobile','CCTV & Keamanan') NOT NULL,
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
-- Dumping data for table `tb_barang`
--

INSERT INTO `tb_barang` (`barang_id`, `kode_inventaris`, `pengajuan_id`, `nama_barang`, `jenis_barang`, `nomor_seri`, `ip_address`, `jumlah`, `spesifikasi`, `tanggal_terima`, `kondisi`, `lokasi_id`, `keterangan`, `foto`) VALUES
(10, '', 4, 'Solution Digital Persona U are U 4500 Free SDK', 'Komponen Komputer & Laptop', 'SG20E17377', '', 1, '- PC based\r\n- Need Komputer\r\n- Interface : USB Kabel\r\n- Free SDK : VB6, C++,C#, Java dan Linux', '2025-09-12', 'baru', 3, 'untuk mentes absensi SIMRS dan untuk cadangan mengganti finger bpjs di fo ralan', 'barang_695ded3303663.jpg'),
(11, '', NULL, 'Laptop lenovo thinkpad', 'Komputer & Laptop', '-', '192.168.1.104', 1, 'Intel® Core™ i5-8350U CPU 1.70GHz\r\nRAM 16 GB, Stroge 238 GB, Stystem 64-bit, Sistem Operasi WIndows 11', '2025-06-11', 'baru', 1, 'Untuk penunjang akreditasi dan untuk acara agenda2 rapat kedepannya', ''),
(12, '', 3, 'POWER SUPLLY EZMAX 600W RGB 80 PLUS', 'Komponen Komputer & Laptop', 'T2521531', '', 1, 'Power Supply Varro Ezmax 600W RGB 80+ 8pin • Silent Fan 120mm • Optimum power output • Ultra High Efficiency • Built-in 120mm with RGB fan • 1 x 20 + 4 pin Motherboard • 1 x P4 + 4 pin CPU +12V • 1 x P6 + 2 pin PCI-E • 4 x Sata • 2 x Molex Garansi Resmi : 2 Tahun Spesifikasi: -Brand : Varro Prime -Capacity : 600 Watt -Version : Standard ATX -80+ -Input Current : 5A -Voltage : 230V -Frekuensi : 50-60Hz -Case Colour : Black -Fan Size : 12cm -Fan Colour : Rainbow RGB LED -CPU Cable : 8-Pin (452mm) Isi Paket: 1 X PSU 1 X Kabel Power', '2025-07-16', 'baru', 1, 'Untuk komputer Hadi IT', 'barang_68ca3e5015b59'),
(13, '', 8, 'ADAPTOR LCD/LED MONITOR LG', 'Komponen Komputer & Laptop', '-', '', 1, 'Adaptor LCD/LED Monitor LG 19V - 0,84A Original', '2025-06-21', 'baru', 1, 'Untuk Monitor Rizky IT karna punya dia di pasang di mba shobah Gizi', ''),
(14, '', 9, 'RAM 8 GB DDR 4 Merek KingSton dan V-Gen', 'Komponen Komputer & Laptop', '-', '', 2, '-', '2025-07-03', 'baru', 1, 'Upgrade PC Desktop (Unit CPU / PC Rakitan) punya riyan it', ''),
(15, '', 10, 'V-GeN SSD 128 GB', 'Komponen Komputer & Laptop', '-', '', 1, 'SSD 128GB V-GeN\r\n\r\nCapacity : 128GB\r\nDimensi : 100 x 70 x 6 mm\r\nSpeed : Read up to 510 MB/s\r\nWrite up to 410 MB/s\r\nInterface : SATA 3 - 6 GB/s\r\nForm Factor : 2.5 inch\r\nWarranty : 3 years one to one replacement\r\nType : Internal Storage\r\nSupported : UDMA Mode 6\r\nTRIM Support : Yes (Requires OS Support)\r\nGarbage Collection : Yes\r\nS.M.A.R.T : Yes\r\nWrite Cache : Yes\r\nHost Protect Area : Yes\r\nAPM : Yes\r\nNCQ : Yes\r\n48-Bit : Yes\r\nSecurity : AES 256-Bit Full Disk Encryption (FDE)\r\nTCG/Opal V2.0 , Encryption Drive (IEEE1667)\r\nVolume : +/- 20 gr', '2025-07-03', 'baru', 1, 'Mengganti SSD karna rusak bekas di pakai buat AKRE PC Desktop (Unit CPU / PC Rakitan) punya riyan it', ''),
(16, '', NULL, 'POWER SUPLLY EZMAX 600W RGB 80 PLUS', 'Komponen Komputer & Laptop', 'T2513680', '', 1, 'Power Supply Varro Ezmax 600W RGB 80+ 8pin • Silent Fan 120mm • Optimum power output • Ultra High Efficiency • Built-in 120mm with RGB fan • 1 x 20 + 4 pin Motherboard • 1 x P4 + 4 pin CPU +12V • 1 x P6 + 2 pin PCI-E • 4 x Sata • 2 x Molex Garansi Resmi : 2 Tahun Spesifikasi: -Brand : Varro Prime -Capacity : 600 Watt -Version : Standard ATX -80+ -Input Current : 5A -Voltage : 230V -Frekuensi : 50-60Hz -Case Colour : Black -Fan Size : 12cm -Fan Colour : Rainbow RGB LED -CPU Cable : 8-Pin (452mm) Isi Paket: 1 X PSU 1 X Kabel Power', '2025-07-16', 'baru', 15, 'Untuk Mengganti komputer farmasi di ralan yang PSU nya konslet', 'barang_68ca3e3937268'),
(17, '', 11, 'Webcame Logitech C270 HD 720p', 'Kamera & Aksesoris', '-', '', 1, 'Menggunakan webcam C270 anda akan mendapatkan panggilan video HD 720p dan foto 3 Mega-pixel. Built-in mikrofonnya menggunakan teknologi RightSound yang menghasilkan percakapan yang jernih tanpa noise latar belakang yang mengganggu. Dalam cahaya remang-remang C270 secara otomatis akan menyesuaikan gambar menjadi lebih baik berkat RightLight teknologi. Mendukung aplikasi Skype, Google Hangouts, Yahoo Messenger dan aplikasi pesan instan popular lainnya. Sistem operasi : Windows XP (SP2 atau lebih baru), Windows vista, Windows 7 (32 bit atau 64 bit), Windows 8 dan Windows10. Spesifikasi Teknik : Panggilan video HD (1280 x 720 piksel) dengan sistem yang direkomendasikan. Perekaman video: Hingga 1280 x 720 piksel. Foto: Hingga 3,0 megapiksel (ditingkatkan menggunakan software). Mikrofon bawaan dengan teknologi Logitech RightSound. Bersertifikat Hi-Speed USB 2.0 (direkomendasikan). Klip universal cocok dengan berbagai laptop, monitor LCD atau CRT. Dimensi kemasan : Tinggi x Lebar x Tebal (cm) : 21 x 16 x 9. Isi Kemasan : - Webcam dengan kabel sepanjang 150 cm. - Dokumentasi pengguna.', '2025-03-03', 'baru', 4, 'Untuk penunjang Akreditasi dan kegiatan rapat menggunakan zoom dan lainnya', 'barang_68c7d219032d0'),
(21, '', NULL, 'Monitor Dell 20\"', 'Komponen Komputer & Laptop', '-', '', 1, '-', '2025-09-17', 'bekas', 20, 'Punya Mas Hadi IT', 'barang_68ca2bbf44e8e'),
(22, '', 13, 'Uninterruptible Power Systems (UPS)', 'Komponen Komputer & Laptop', '-', '', 1, '-', '2025-05-07', 'baru', 1, 'Untuk Komputer Riyan IT', 'barang_68ca535099e9b.jpeg'),
(23, '', NULL, 'PC Desktop (Unit CPU / PC Rakitan)', 'Komputer & Laptop', '-', '192.168.1.222', 1, 'Intel® Core™ i5-14400F (16CPUs),~2.5 GHz\r\nRAM 16 GB, Stystem 64-bit, Sistem Operasi WIndows 11 Pro Education', '2023-01-01', 'baru', 10, 'Penempatan di Radiologi dalam samping Lab', 'barang_695ded7d30a7d.jpg'),
(24, '', NULL, 'PC DELL Desktop (Unit CPU / PC Rakitan)', 'Komputer & Laptop', '-', '192.168.1.234', 1, 'Merek : DELL\r\nIntel® Core™ i5-9500 CPU @ 3.00 GHz(6CPUs),~3.0 GHz\r\nRAM 8 GB, Stystem 64-bit, Sistem Operasi WIndows 11 Pro', '2023-01-01', 'baru', 10, 'Penempatan di Radiologi dalam samping Lab', 'barang_695ded92611c7.jpg'),
(25, '', NULL, 'PC Desktop (Unit CPU / PC Rakitan)', 'Komputer & Laptop', '-', '192.168.1.134', 1, '12th Gen Intel® core™ i5-12400 (12 CPUs), ~2.5GHz\nRAM 16 GB, Stystem 64-bit, Sistem Operasi WIndows 11 Home', '2025-03-03', 'baru', 1, 'Penempatan di Ruang IT (PC UTAMA ALI)', 'barang_68cba80f33b7b.jpeg'),
(26, '', 13, 'Uninterruptible Power Systems (UPS)', 'Komponen Komputer & Laptop', '-', '', 1, '-', '2025-05-07', 'baru', 1, 'Untuk Komputer ALI IT', 'barang_695dedae3ad12.jpg'),
(27, '001/LOG/FARM/VI/2026', NULL, 'Printer Epson L3210', 'Printer & Scanner', 'XAGKF68723', '', 1, 'Epson L3210 adalah printer inkjet dengan teknologi EcoTank yang memungkinkan penggunaan tinta dalam jumlah besar. Resolusinya mencapai 5760 x 1440 dpi, jadi hasil cetaknya tajam, baik untuk dokumen hitam-putih maupun foto berwarna. Kecepatan cetaknya juga cukup oke, yaitu 10 ipm (halaman per menit) untuk hitam-putih dan 5 ipm untuk warna. Printer ini mendukung ukuran kertas A4, legal, hingga foto 4R, plus kompatibel dengan Windows dan Mac OS.\r\n\r\nBobotnya hanya 3,9 kg dengan dimensi 375 x 347 x 179 mm, jadi tidak makan tempat di meja kerja. Sayangnya, konektivitasnya masih mengandalkan USB 2.0, belum ada Wi-Fi seperti seri yang lebih tinggi.\r\n\r\nMeski begitu, untuk penggunaan sederhana, spesifikasi ini sudah cukup memadai. Dengan garansi hingga 2 tahun atau 30.000 cetakan, kamu juga bisa lebih tenang soal perawatan.', '2025-09-20', 'bekas', 16, 'Diterima langsung oleh karu farmasi (Rollah), dan printer Epson L121 di tarik ke Unit IT', 'barang_68ce1c5fad7ca.jpeg'),
(28, '-', NULL, 'Printer Epson L121', 'Printer & Scanner', 'X9LU382924', '', 1, '-', '2025-09-20', 'bekas', 1, 'Bekas dari Farmasi rawat inap, karna di farmasi ranap sudah ada yang baru', 'barang_68ce1d9813869.jpeg'),
(29, '', NULL, 'Uninterruptible Power Systems (UPS)', 'Komponen Komputer & Laptop', '1B1QP2400866', '', 1, 'Type : CE600D\r\nTegangan Input : 220 Vac\r\nFrekuensi Input : 50 Hz', '2025-09-09', 'baru', 5, 'Sudah di pasang dan di terima oleh karu kecubung, di pasang untuk kegunaan backup komputer & printer', 'barang_68db3563ed635.jpeg'),
(30, '', NULL, 'PC Desktop (Unit CPU / PC Rakitan)', 'Komputer & Laptop', '-', '192.168.30.167', 1, 'Intel® core™ i3-4170 CPU @ 3.70GHz (4 CPUs), ~3.7GHz\r\nRAM 16 GB, Stystem 64-bit, Sistem Operasi WIndows 11 Home', '2025-09-09', 'bekas', 5, 'Ini komputer Mas Malik dan diserahkan ke kecubung karna permintaan dari karu kecubung ingin penambahan 1 pc', 'barang_68db375e0d9e8.jpeg'),
(31, '', NULL, 'Uninterruptible Power Systems (UPS)', 'Komponen Komputer & Laptop', '1B1QP2401638', '', 1, 'Type : CE600D\nTegangan Input : 220 Vac\nFrekuensi Input : 50 Hz', '2025-09-09', 'baru', 6, 'Untuk membackup komputer dan printer dan diterima langsung oleh karu yakut c (bu atul) saat pemasangan', 'barang_68db46df7fcc5.jpeg'),
(32, '', NULL, 'Uninterruptible Power Systems (UPS)', 'Komponen Komputer & Laptop', '1B1QP2401663', '', 1, 'Type : CE600D\r\nTegangan Input : 220 Vac\r\nFrekuensi Input : 50 Hz', '2025-09-09', 'baru', 22, 'di pasang di komputer dokter IGD', 'barang_68dcdded1528a.jpeg'),
(33, '', NULL, 'Uninterruptible Power Systems (UPS)', 'Komponen Komputer & Laptop', '1B1QP2400837', '', 1, 'Type : CE600D\r\nTegangan Input : 220 Vac\r\nFrekuensi Input : 50 Hz', '2025-09-09', 'baru', 15, 'Sudah diserahkan oleh petugas farmasi ralan', 'barang_68dcde9fcd83d.jpeg'),
(34, '', NULL, 'RAM 8 GB DDR 3 Merek VENOMRX', 'Komponen Komputer & Laptop', 'T2301835', '', 1, 'Product Name 8GB PC 12800\nKategori Memory RAM Komputer\nBrand VenomRX\nTahun Rilis 2012\nHardware\nMemory Module DIMM\nCompatible Device Desktop\nMulti-Channel Dual Channel Channel\nMemory\nKapasitas Memori 8 GB\nFrekuensi 1600 MHz\nMemory Type DDR3', '2025-09-10', 'baru', 22, 'di pasang di komputer Dokter IGD karna yang dulu cuman 4 GB saja RAM nya', 'barang_68dce0d0ebe54.jpeg'),
(35, '', NULL, 'Keyboard Logitech K120', 'Komponen Komputer & Laptop', '2320MR11CC48', '', 1, 'https://www.logitech.com/en-hk/products/keyboards/k120-usb-standard-computer.920-002584.html', '2025-10-03', 'baru', 15, 'langsung di diganti karna yang lama rusak (info : mas hadi)', 'barang_68df263cd1fb7.jpeg'),
(36, '', NULL, 'SSD ADATA SU650 128GB', 'Komponen Komputer & Laptop', '-', '', 1, '-', '2025-10-01', 'bekas', 22, 'SSD Bekas punya Dito IT  dipasang di komputer dokter IGD karna permasalahan yang sebelumnya sering mati sendiri dan bluscreen', 'barang_68df29dd1bb2d.jpg'),
(37, '', 16, 'SanDisk 32 GB', 'Komponen Komputer & Laptop', '-', '', 1, '-', '2025-10-07', 'baru', 1, 'Untuk Instal Ulang windows', 'barang_1759826299_8183.jpeg'),
(38, '', 16, 'SanDisk 32 GB', 'Komponen Komputer & Laptop', '-', '', 1, '-', '2025-10-07', 'baru', 1, 'Untuk keperluan Bakcup data unit IT', 'barang_1759826327_7021.jpeg'),
(39, '', NULL, 'POWER SUPLLY EZMAX 600W RGB 80 PLUS', 'Komponen Komputer & Laptop', 'T2527590', '', 1, 'Power Supply Varro Ezmax 600W RGB 80+ 8pin • Silent Fan 120mm • Optimum power output • Ultra High Efficiency • Built-in 120mm with RGB fan • 1 x 20 + 4 pin Motherboard • 1 x P4 + 4 pin CPU +12V • 1 x P6 + 2 pin PCI-E • 4 x Sata • 2 x Molex Garansi Resmi : 2 Tahun Spesifikasi: -Brand : Varro Prime -Capacity : 600 Watt -Version : Standard ATX -80+ -Input Current : 5A -Voltage : 230V -Frekuensi : 50-60Hz -Case Colour : Black -Fan Size : 12cm -Fan Colour : Rainbow RGB LED -CPU Cable : 8-Pin (452mm) Isi Paket: 1 X PSU 1 X Kabel Power', '2025-10-10', 'baru', 23, 'Upgrade PC Lama (Untuk Karyawan RM Baru)', 'barang_68e8817d59e07.jpeg'),
(40, '', NULL, 'SSD ADATA SU650 256GB', 'Komponen Komputer & Laptop', '404021078462', '', 1, '-', '2025-10-10', 'baru', 23, 'Upgrade PC Lama (Untuk Karyawan RM Baru)', 'barang_68e881dbbc12f.jpeg'),
(41, '', NULL, 'RAM 8 GB DDR 3 Merek VENOMRX', 'Komponen Komputer & Laptop', 'T2301841', '', 1, 'Product Name 8GB PC 12800\r\nKategori Memory RAM Komputer\r\nBrand VenomRX\r\nTahun Rilis 2012\r\nHardware\r\nMemory Module DIMM\r\nCompatible Device Desktop\r\nMulti-Channel Dual Channel Channel\r\nMemory\r\nKapasitas Memori 8 GB\r\nFrekuensi 1600 MHz\r\nMemory Type DDR3', '2025-10-10', 'baru', 23, 'Upgrade PC Lama (Untuk Karyawan RM Baru)', 'barang_68e882893ba13.jpeg'),
(42, '', NULL, 'SSD ADATA SU650 512GB', 'Komponen Komputer & Laptop', 'SN4P1121306844', '', 1, '-', '2025-10-10', 'baru', 20, 'yang dulu pakai hardisk kondisinya yaitu hardisknya sering tidak terbaca saat menyalakan komputer dan atau saat berhasil masuk windows kadang kembali mati , Indekasi Hardisk sudah melemah/Rusak', 'barang_68e8aade575b1.jpeg'),
(43, '', NULL, 'Hardisk SEGATE 500GB', 'Komponen Komputer & Laptop', 'SN5W6DT65JY', '', 1, '-', '2021-11-11', 'rusak', 20, 'INI BEKAS PC PONEK, KONDISI HARDISK LEMAH KARNA SERING TIDAK TERBACA SAAT NYALAKAN PC, DAN KADANG SERING MATI (BLUESCREEN)', 'barang_68e9a907b0139.jpeg'),
(44, '', NULL, 'Logitech MK120 Plug and Play USB Combo', 'Komponen Komputer & Laptop', 'SN2506LOA0E579', '', 1, 'Desain anti tumpahan cairan. Jangan benamkan keyboard ke dalam cairan.\r\n\r\nTinggi keyboard yang dapat disesuaikan\r\n\r\nNumber pad 10 tombol\r\n\r\nLampu indikator caps lock\r\n\r\nLampu indikator num lock\r\n\r\nMaksimal 10 juta keystroke (tidak termasuk tombol number lock)\r\n\r\nJenis tombol: Deep profile\r\n\r\nMouse\r\n\r\nTeknologi sensor: Penelusuran optik\r\n\r\nJumlah tombol: 3 (Klik Kiri/Kanan, Klik Tengah)\r\n\r\nScrolling: line-by-line\r\n\r\nScroll Wheel: Ya, optik\r\n\r\nKeberlanjutan\r\n\r\nPlastik mouse: 72% bahan Post Consumer Recycled (PCR)\r\n\r\nPlastik keyboard: 54% bahan Post Consumer Recycled (PCR)', '2025-10-13', 'baru', 23, 'untuk keperluan komputer yang di pasang karyawan RM Baru', 'barang_68ec60649c3b2.jpeg'),
(45, '', NULL, 'Monitor PC DELL', 'Komponen Komputer & Laptop', '-', '', 1, '-', '2025-07-09', 'bekas', 23, 'Monitor Untuk Komputer Mas HADI', 'barang_68ec62f6c0351.jpeg'),
(46, '', NULL, 'PREMIUM INK (TINTA EPSON HITAM)', 'Komponen Printer & Scanner', '-', '', 4, '-', '2025-10-15', 'baru', 1, 'Stok IT', 'barang_68f048ada465a.jpeg'),
(68, '', NULL, 'RAM 4 GB DDR 3 Merek KingSton', 'Komponen Komputer & Laptop', 'T2322042', '', 1, '-', '2025-10-18', 'baru', 21, 'Upgrade RAM + Memperbaiki karna yang dulu error', 'barang_68f2f71bc57a9.jpeg'),
(69, '', NULL, 'Printer Epson L121', 'Printer & Scanner', 'X9LU666594', '', 1, 'https://www.bhinneka.com/epson-ecotank-l121-sku3337608496#attr=369083,369084', '2025-10-21', 'baru', 21, 'Pengajuan Printer baru dari kasir , yang dulu di kembalikan ke unit IT', 'barang_68f741102637a.jpeg'),
(70, '(Tidak ada)', NULL, 'Printer Epson LX-310', 'Printer & Scanner', 'Q7FYJ98905', '', 1, '-', '2025-10-21', 'baru', 21, 'Pengajuan dari kasir karna epson LX yang dulu sudah tidak layak pakai dan sering nyangkut kertasnya', 'barang_68f742415e972.jpeg'),
(71, '(Tidak ada)', NULL, 'Printer Epson L220', 'Printer & Scanner', 'WN5P170005', '', 1, '-', '2025-10-21', 'bekas', 1, 'Printer ini bekas di kasir ditarik ke IT dulu karna di kasir sudah ada yang baru yaitu Epson L121, Kondisi Printer : Tinta banjir dan ???', 'barang_68f7433887191.jpeg'),
(72, '(Tidak ada)', NULL, 'Printer Epson LX-310', 'Printer & Scanner', 'Q7FY072147', '', 1, '-', '2025-10-21', 'bekas', 1, 'Ini bekas di kasir lalu diserahkan dahulu ke unit IT karna di kasir dapat printer Epson LX-310 baru di tanggal 21/10/25', 'barang_68f744a101700.jpeg'),
(73, '(Tidak ada)', NULL, 'RAM 4 GB DDR 4 Merek VENOMRX', 'Komponen Komputer & Laptop', 'T2304775 DAN T2304772', '', 2, '-', '2025-07-03', 'bekas', 1, 'RAM bekas komputer Dito/Riyan , Kondisi masih perkiraan 60%', 'barang_68f8802a6fccf.jpeg'),
(74, '(Tidak ada)', 22, 'UGREEN Kabel USB Type C Fast Charging 3A 1m 2m 3m For Samsung Oppo Vivo Xiaomi Realmi', 'Komponen Komputer & Laptop', 'KUTC2', '', 1, 'Ganti Merek yaitu PROFFTECH', '2025-11-03', 'baru', 1, 'Untuk Acara Bu Haji + Backup IT kalo ada acara-acara', 'barang_1762130424_5394.jpeg'),
(75, '(Tidak ada)', NULL, 'Ugreen Adapter Ethernet USB 3.0 to LAN RJ45', 'Komponen Komputer & Laptop', '-', '(Beum Ada)', 1, 'Deskripsi :\r\n- Merek : UGREEN\r\n- Model : 50922\r\n- Material : Aluminium Case\r\n\r\n- Interface Type:USB to EthernetLan\r\n- Transmission Speed:600 Mbps\r\n- Transmission Rate:1000M\r\n- Transmission Rate:10/100/1000Mbps\r\n- Type:Gigabit Ethernet\r\n- Adapter Socket:RJ45', '2026-04-09', 'baru', 1, 'dipakai mas ali pas di bulan desember', 'barang_69dc52d8bed2f.jpeg'),
(76, '(Tidak ada)', NULL, 'SSD ADATA SU650 512GB', 'Komponen Komputer & Laptop', '4P1121306832', '', 1, '-', '2025-11-04', 'baru', 3, 'Untuk Upgrade Komputer FO Ralan 2 dikarenakan keluhan nya sering takang dan setelah di cek memang benar ada kendala di haridisk', 'barang_690975b5a581f.jpeg'),
(77, '(Tidak ada)', NULL, 'USB 3.0 to Gigabit Ethernet Adapter', 'Komponen Network', 'Model : CM209 | P/N:50922', '', 1, 'Connector : USB-A 3.0, RJ45 (mOMbps)\r\nInput : 5.OV 0.9A Max\r\nCompatible Systems : macOS/Windows/Linux/Android/iOS\r\nNote : Driver-free for Windows 8/10/11, mac OS systems. However, driver\r\ninstallation is needed for Windows XP and Windows 7.', '2025-11-06', 'baru', 24, 'Untuk penyambung LAN ke Laptop Poli , yang dulu infonya rusak', 'barang_690d646ddef39.jpeg'),
(78, '(Tidak ada)', NULL, 'DEEPCOOL CK-11509', 'Komponen Komputer & Laptop', '107369', '', 1, '-', '2025-11-11', 'baru', 3, 'Untuk mengganti Kipas proccesor komputer asuransi (FO 2) karna indikasi kipas lama sudah tidak berputar lagi mengakibatkan pc panas dan overhead lalu menyebabkan mati-mati dan lag', 'barang_6913e6094fcec.jpeg'),
(79, '(Tidak ada)', NULL, 'RAM 8 GB DDR 3 KVR16N11/8 Merek KingSton', 'Komponen Komputer & Laptop', 'T2530438', '', 1, '-', '2025-11-11', 'baru', 3, 'Untuk Upgrade performa komputer asuransi (FO 2), jadi sekarang kapasitas RAM PC 10 GB (8+2)', 'barang_6913e6fad90c7.jpeg'),
(80, '(Tidak ada)', 7, 'Fingerprint Sidik Jari Fingerspot U are U 4500 USB PC Based', 'Komponen Komputer & Laptop', 'TH20E12502', '', 1, '-', '2025-10-23', 'baru', 34, 'Untuk menunjang keperluan APM Baru dan nanti nya akan di pakai jika app APM baru sudah selesai', 'barang_1762912215_6676.jpeg'),
(81, '(Tidak ada)', NULL, 'RAM 8 GB DDR 3 KVR16N11/8 Merek KingSton', 'Komponen Komputer & Laptop', '-', '', 1, '-', '2025-11-11', 'bekas', 1, 'Ram Bekas FO Ralan (2) yang biasa di pakai buat asuransi, Kondisi Perkiraan 60% (Masih bisa di pakai)', 'barang_6916a38c49ce4.jpeg'),
(82, '(Tidak ada)', NULL, 'SSD Varro Evolution 512GB', 'Komponen Komputer & Laptop', 'T2225097', '', 1, '-', '2025-11-12', 'rusak', 3, 'Ini SSD dari komputer Ralan 4 Rusaknya karna SSD tidak terbaca lagi saat menyalakan komputer dan di tes menggunakan external juga tidak terbaca', 'barang_6916a660553e6.jpeg'),
(83, '(Tidak ada)', NULL, 'Tripod Kamera/Webcame Merek NeePho', 'Kamera & Aksesoris', '-', '', 1, '-', '2025-11-14', 'baru', 4, 'Untuk kamera persetujuan ranap', 'barang_6916aa46b8ca7.jpeg'),
(84, '(Tidak ada)', NULL, 'Logitech MK120 Keyboard dan Mouse + Play USB Combo', 'Komponen Komputer & Laptop', '-', '', 1, '-', '2025-04-01', 'baru', 24, 'Ini di awalnya ampun rizky di bawa dan diserahkan ke Poi Mata', 'barang_692f8dfee52e1.jpeg'),
(85, '(Tidak ada)', NULL, 'Keyboard REXUX RX-KM8 Wireless', 'Komponen Komputer & Laptop', '-', '', 1, 'Teknologi: Nano USB\r\n\r\nJumlah tombol: 106 tombol\r\n\r\nTegangan: DC 1,9V 3.3V\r\n\r\nArus: < 10MAh\r\n\r\nDaya tahan tombol: 10 juta klik\r\n\r\nBerat: 480 gram\r\n\r\nDimensi: 442x137x31mm', '2025-12-02', 'bekas', 1, 'Bekas Poli Mata (Sepaket dengan Mouse)', 'barang_692f8f212017e.jpeg'),
(86, '(Tidak ada)', NULL, 'Mouse REXUX RX-KM8 Wireless', 'Komponen Komputer & Laptop', '-', '', 1, 'Material: Plastik ABS\r\n\r\nSensitivitas: 1600DPI\r\n\r\nJumlah tombol: 104 tombol\r\n\r\nMode kerja: Nirkabel 2,4 GHz\r\n\r\nDaya tahan tombol: 1 juta kali\r\n\r\nSensor: Optik\r\n\r\nChipset: PIXART 3065\r\n\r\nTegangan: DC 1,9V 3,3V\r\n\r\nArus:< 10MAh\r\n\r\nDimensi: 108x60x26 mm\r\n\r\nBerat: 50 gram\r\n\r\nUSB: Nano USB\r\n\r\nTegangan USB: DC 4.77V 5,25V', '2025-12-02', 'bekas', 1, 'Bekas Poli Mata', 'barang_692f8fa0b6b69.jpeg'),
(87, '(Tidak ada)', NULL, 'Logitech MK120 Keyboard dan Mouse + Play USB Combo', 'Komponen Komputer & Laptop', '2448LOK0A059', '', 1, 'Keyboard\r\n\r\nDesain anti tumpahan cairan. Jangan benamkan keyboard ke dalam cairan.\r\n\r\nTinggi keyboard yang dapat disesuaikan\r\n\r\nNumber pad 10 tombol\r\n\r\nLampu indikator caps lock\r\n\r\nLampu indikator num lock\r\n\r\nMaksimal 10 juta keystroke (tidak termasuk tombol number lock)\r\n\r\nJenis tombol: Deep profile\r\n\r\nMouse\r\n\r\nTeknologi sensor: Penelusuran optik\r\n\r\nJumlah tombol: 3 (Klik Kiri/Kanan, Klik Tengah)\r\n\r\nScrolling: line-by-line\r\n\r\nScroll Wheel: Ya, optik\r\n\r\nKeberlanjutan\r\n\r\nPlastik mouse: 72% bahan Post Consumer Recycled (PCR)\r\n\r\nPlastik keyboard: 54% bahan Post Consumer Recycled (PCR)', '2025-12-03', 'baru', 20, 'Untuk Ika IT Keyboardnya dan mousenya untuk riyan', 'barang_692f95b64df4d.jpeg'),
(88, '(Tidak ada)', NULL, 'SWITCH HUB TP-LINK 5 PORT GIGABIT', 'Komponen Network', '22472M6011722', '', 1, 'Port: 5 x 10/100/1000Mbps RJ45 (Auto Negotiation, Auto MDI/MDIX).\r\nTipe: Unmanaged Switch (Plug and Play, tanpa perlu konfigurasi).\r\nFitur Utama:\r\nGreen Technology: Menghemat daya hingga 85%.\r\nIEEE 802.3X Flow Control: Mencegah paket hilang saat buffer penuh.\r\nMDI/MDIX Otomatis: Tidak perlu kabel crossover.\r\nKinerja: Mendukung transfer file besar dengan kecepatan tinggi.\r\nDesain: Fanless (tanpa kipas) untuk operasi hening, bisa dipasang di dinding (wall-mount).\r\nBodi: Seringkali berbahan metal (besi) untuk daya tahan dan disipasi panas lebih baik.\r\nIndikator LED: Menunjukkan status koneksi dan aktivitas port', '2025-12-26', 'baru', 1, 'Cadangan Buat Stok', 'barang_695c8a5770cef.jpeg'),
(89, '(Tidak ada)', NULL, 'SWITCH HUB TP-LINK 5 PORT GIGABIT', 'Komponen Network', '2253629008719', '', 1, 'Port: 5 x 10/100/1000Mbps RJ45 (Auto Negotiation, Auto MDI/MDIX).\r\nTipe: Unmanaged Switch (Plug and Play, tanpa perlu konfigurasi).\r\nFitur Utama:\r\nGreen Technology: Menghemat daya hingga 85%.\r\nIEEE 802.3X Flow Control: Mencegah paket hilang saat buffer penuh.\r\nMDI/MDIX Otomatis: Tidak perlu kabel crossover.\r\nKinerja: Mendukung transfer file besar dengan kecepatan tinggi.\r\nDesain: Fanless (tanpa kipas) untuk operasi hening, bisa dipasang di dinding (wall-mount).\r\nBodi: Seringkali berbahan metal (besi) untuk daya tahan dan disipasi panas lebih baik.\r\nIndikator LED: Menunjukkan status koneksi dan aktivitas port', '2025-12-26', 'baru', 1, 'Buat Cadangan Stok', 'barang_695c8ad62d4f5.jpeg'),
(90, '(Tidak ada)', NULL, 'MikroTik ROUTERBOARD RB450GX4', 'Komponen Network', 'T2533984', '', 1, 'Routerboard RB450Gx4 (716MHz Quad Core CPU, 1 GB DDR RAM, 512MB NAND Storage) dengan RouterOS (Level 5) 5 (lima) buah port gigabit 10/100/1000 slot mikro-SD. Tidak bisa dipasangkan wireless card. Sudah termasuk 1 buah adaptor 24 Volt.', '2026-01-02', 'baru', 1, 'dI peruntukan di pasien Lt 2 karna yang dulu  rusak Info : mas Hadi', 'barang_695c8cf910832.jpeg'),
(91, '(Tidak ada)', NULL, 'Webcame Logitech C270 HD 720p', 'Komponen Komputer & Laptop', '2510APN9N729', '', 1, '', '2025-12-14', 'baru', 1, 'Untuk Cadangan dan diperentukan utama untuk agenda rapat online', 'barang_695c8e66b3422.jpeg'),
(92, '(Tidak ada)', NULL, 'Ugreen Adapter Ethernet USB 3.0 to LAN RJ45', 'Komponen Network', '-', '', 1, 'Deskripsi :\r\n- Merek : UGREEN\r\n- Model : 50922\r\n- Material : Aluminium Case\r\n\r\n- Interface Type:USB to EthernetLan\r\n- Transmission Speed:600 Mbps\r\n- Transmission Rate:1000M\r\n- Transmission Rate:10/100/1000Mbps\r\n- Type:Gigabit Ethernet\r\n- Adapter Socket:RJ45', '2025-01-22', 'rusak', 24, 'Info tidak bisa terhubung lagi jadi ketika di colok ke kabel lan dia tidak terbaca walaupun indikator lannya nyala', 'barang_695c920840ef3.jpg'),
(93, '(Tidak ada)', NULL, 'kabel vga 1.5 m / Kabel vga male male 1.5 m / Kabel vga jantan jantan 1.5 m / Cable VGA 1.5 m', 'Komponen Komputer & Laptop', '-', '', 1, '- Panjang 1M\r\n\r\n- Warna Hitam\r\n\r\n- Untuk menghubungkan komputer dengan monitor, proyektor, infocus atau\r\n\r\nlayar LCD', '2026-01-02', 'baru', 3, 'untuk minitor FO ralan 2 (Asuransi)', 'barang_695defa626cec.jpg'),
(94, '(Tidak ada)', NULL, 'Profftech VGA Cable Male to Male / Kabel VGA / 1.5m', 'Komponen Komputer & Laptop', '-', '', 1, '- Connector Type : Standard VGA Male-Male (VGA 3+4)\r\n- Connector : Gold plated\r\n- Terminal Pin : 15 Pin 24k Gold Plated\r\n- Conductor : 30AWG, Pure Copper\r\n- Shielding : Aluminum Foil + Anti-Jamming Materials\r\n- Jacket shell : PVC Environmental Material.', '2026-01-05', 'baru', 9, 'Untuk Komputer Mas Adi ahmad', 'barang_695df0730bfc9.png'),
(95, '(Tidak ada)', NULL, 'PC Desktop (Unit CPU / PC Rakitan)', 'Komputer & Laptop', '192.168.2.81', '192.168.2.81', 1, 'Sistem Operasi\r\n\r\nWindows 11 Pro 64-bit\r\n\r\nVersi: 10.0 (Build 25200)\r\n\r\nBahasa Sistem\r\n\r\nEnglish (Regional Setting: English)\r\n\r\nMotherboard / Sistem\r\n\r\nSystem Manufacturer: To Be Filled By O.E.M.\r\n\r\nSystem Model: B550M-HDV\r\n\r\nBIOS\r\n\r\nVersi BIOS: P3.30\r\n\r\nProsesor\r\n\r\nAMD Ryzen 3 3200G with Radeon Vega Graphics\r\n\r\n4 Core (4 CPUs)\r\n\r\nKecepatan ± 3.60 GHz\r\n\r\nMemori (RAM)\r\n\r\n8192 MB (8 GB RAM)\r\n\r\nGrafis\r\n\r\nRadeon™ Vega Graphics (terintegrasi)\r\n\r\nDirectX\r\n\r\nDirectX Version: 12\r\n\r\nPage File (Virtual Memory)\r\n\r\n5132 MB digunakan\r\n\r\n4558 MB tersedia', '2026-01-14', 'baru', 33, NULL, 'barang_696747a603806.jpeg'),
(96, '(Tidak ada)', NULL, 'Uninterruptible Power Systems (UPS)', 'Komponen Komputer & Laptop', '1B1QP2500032', '', 1, 'Type : CE600D\r\nTegangan Input : 220 Vac\r\nFrekuensi Input : 50 Hz', '2026-01-14', 'baru', 11, NULL, 'barang_696749cb5ef76.jpeg'),
(97, '(Tidak ada)', NULL, 'Uninterruptible Power Supply (UPS)', 'Komponen Komputer & Laptop', '1B1QP2401972', '', 1, 'Type : CE600D\r\nTegangan Input : 220 Vac\r\nFrekuensi Input : 50 Hz', '2026-01-14', 'baru', 11, NULL, 'barang_69674a4e25557.jpeg'),
(98, '(Tidak ada)', NULL, 'PC Desktop (Unit CPU / PC Rakitan)', 'Komputer & Laptop', '192.168.1.90', '192.168.1.90', 1, 'VGA: Intel(R) HD Graphics\r\nOS: Windows 10 32-bit\r\nProcessor: i3-3210CPU @ 3.20GHZ (4 CPUs), ~3.2GHz\r\nRAM : 8 GB + 8 GB (Baru) = 16 GB\r\nDriver sekarang: 10.18.10.3496\r\nModel driver: WDDM 1.3', '2013-01-09', 'bekas', 20, NULL, 'barang_69688df63e5b1.jpeg'),
(100, '(Tidak ada)', NULL, 'RAM 8 GB DDR 3 Merek KingSton', 'Komponen Komputer & Laptop', 'T2625004', '', 1, 'kapasitas 8GB, tipe DDR3, kecepatan 1600MHz (PC12800), tersedia dalam bentuk DIMM (untuk PC Desktop) dan SODIMM (untuk Laptop), serta biasanya menggunakan voltase 1.35V (untuk versi PC3L/low voltage) atau 1.5V', '2026-01-15', 'baru', 20, NULL, 'barang_696af19988d30.jpeg'),
(101, '(Tidak ada)', NULL, 'POWER SUPLLY EZMAX 600W RGB 80 PLUS', 'Komponen Komputer & Laptop', 'T2527599', '', 1, 'Power Supply Varro Ezmax 600W RGB 80+ 8pin • Silent Fan 120mm • Optimum power output • Ultra High Efficiency • Built-in 120mm with RGB fan • 1 x 20 + 4 pin Motherboard • 1 x P4 + 4 pin CPU +12V • 1 x P6 + 2 pin PCI-E • 4 x Sata • 2 x Molex Garansi Resmi : 2 Tahun Spesifikasi: -Brand : Varro Prime -Capacity : 600 Watt -Version : Standard ATX -80+ -Input Current : 5A -Voltage : 230V -Frekuensi : 50-60Hz -Case Colour : Black -Fan Size : 12cm -Fan Colour : Rainbow RGB LED -CPU Cable : 8-Pin (452mm) Isi Paket: 1 X PSU 1 X Kabel Power', '2026-01-15', 'baru', 1, NULL, 'barang_696af2c617b8b.jpeg'),
(103, '(Tidak ada)', NULL, 'RAM 8 GB DDR 3 Merek KingSton', 'Komponen Komputer & Laptop', 'T2530407', '', 1, 'kapasitas 8GB, tipe DDR3, kecepatan 1600MHz (PC12800), tersedia dalam bentuk DIMM (untuk PC Desktop) dan SODIMM (untuk Laptop), serta biasanya menggunakan voltase 1.35V (untuk versi PC3L/low voltage) atau 1.5V', '2026-01-17', 'baru', 1, NULL, 'barang_696af55b1749e.jpeg'),
(104, '(Tidak ada)', NULL, 'PC Desktop (Unit CPU / PC Rakitan)', 'Komputer & Laptop', '0', '192.168.1.85', 1, 'Current Date/Tme:\r\nn-usday, February 12, 2026, PM\r\nComputer Name: PæZA\r\n• Windows 11 Pro 64-bit (10.0, 26200)\r\nOperating System.\r\nEnghsh (Regional Setting: Enghsh)\r\nLanguage:\r\nSystem Maru-facturer:\r\nTo Be Filled By O.E.M.\r\nSystem Model:\r\nB550M-HDV\r\nBIOS: P3 30\r\nAND Ryzen 3 3200G with Radeon Vega Graphics\r\n(4 CPUs), N3.6GHz\r\nProcessor:\r\nMemory:\r\n81931B RAM\r\nPage file:\r\n74441B used, 1787tv1B available\r\nDirectX Version: DrectX 12', '2026-02-11', 'baru', 32, NULL, 'barang_698d58fa94392.jpeg'),
(105, '(Tidak ada)', NULL, 'NYK 2 PORT USB SWITCH PRINTER', 'Komponen Printer & Scanner', 'Tidak ada', '', 1, '-Feature: 2 Prot(lubang) USB Switch, USB 2.0 Port, Sk=aklar yang mudah digunakan', '2026-02-16', 'baru', 32, NULL, 'barang_69929369bce63.jpeg'),
(106, '(Tidak ada)', NULL, 'Webcame Logitech C270 HD 720p', 'Komponen Komputer & Laptop', '2435APW68C48', '', 1, 'Resolusi Maks.: 720p/30fps\r\nKamera mega pixel: 0.9\r\nJenis fokus: fixed focus\r\nJenis lensa: plastik\r\nMikrofon internal: Mono\r\nJangkauan mikrofon: Maksimal 1 m\r\nBidang pandang diagonal (dFoV): 55°\r\nUniversal mounting clip cocok dengan berbagai laptop, LCD, atau layar', '2025-05-14', 'bekas', 1, NULL, 'barang_699508070cb7a.jpeg'),
(107, '(Tidak ada)', NULL, 'Printer & Scanner Epson L3210', 'Printer & Scanner', 'XAGK926120', '', 1, 'Fungsi: Cetak (Print), Pindai (Scan), Salin (Copy)\r\nTeknologi: Ink Tank System (EcoTank), cetak foto tanpa bingkai hingga 4R\r\nKecepatan Cetak (A4): Hingga 33 ppm (Hitam/Draft), 15 ppm (Warna/Draft)\r\nResolusi Cetak: \r\n dpi\r\nResolusi Scan: \r\n dpi (Flatbed Scanner)\r\nResolusi Copy: \r\n dpi\r\nKapasitas Tinta: Tinta 003 (Hitam: 4.500 halaman, Warna: 7.500 halaman)\r\nKonektivitas: USB 2.0\r\nSistem Operasi: Windows XP/Vista/7/8/8.1/10, Mac OS X 10.6.8 atau lebih baru\r\nKapasitas Kertas: Hingga 100 lembar (input), 30 lembar (output)\r\nDimensi (WxDxH): \r\n mm\r\nFitur Khusus: Pengisian tinta tanpa tumpah, desain ringkas, hemat daya (14 Watt saat beroperasi)', '2024-11-18', 'bekas', 8, NULL, 'barang_699512c4975c5.jpeg'),
(108, '(Tidak ada)', NULL, 'Keyboard Wireless Bluetooth Touchpad Untuk Ipad Tablet Android PC Laptop', 'Aksesoris Perangkat Mobile', 'A1820-black-9.7', '', 1, 'Keyboard Wireless merek Goojodoq ukuran 10 inch warna putih.\r\nBarang baru ya gaes, ini hadiah dan gakepake.\r\nSupport buat Ipad, Teblet android, Laptop, Pc, smartphone dll\r\nBarang lengkap ada box, kabel charger dan manual book.', '2026-02-21', 'baru', 7, NULL, 'barang_699933012fb8d.jpeg'),
(109, '(Tidak ada)', NULL, 'PC Desktop (Unit CPU / PC Rakitan)', 'Komputer & Laptop', '5', '192.168.1.71', 1, 'AMD A4-4000 APU with Radeon HD Graphics\r\nCPU: AMD A4-4000 APU @ 3.0 GHz\r\nRAM: 8 GB\r\nGrafis: Radeon HD (lebih bagus dari Intel HD lama)\r\nMotherboard: Biostar Hi-Fi A55S3\r\nPlatform: Lebih baru dari Core 2 Duo', '2019-09-09', 'bekas', 25, NULL, 'barang_699e968a108a4.jpeg'),
(110, '(Tidak ada)', NULL, 'RAM 8 GB DDR 4 Merek KingSton', 'Komponen Komputer & Laptop', 'T2430525', '', 1, '-', '2026-02-11', 'bekas', 9, NULL, 'barang_69a7932164b8a.jpeg'),
(111, '(Tidak ada)', NULL, 'Printer dot matrix 24-pin EPSON LQ-310', 'Printer & Scanner', 'R9JY017953', '', 1, 'Printing Method Impact dot matrix\r\nPixel Resolutions Max. 24 pin: up to 360 x 360 dpi\r\nPrint Speed Black High Speed Draft: 10cpi: 347 cps; 12cpi: 416 cps | Draft: 10cpi: 260 cps; 12cpi: 312 cps; 15cpi: 390 cps; (Condensed) 17cpi: 222 cps; (Condensed) 20cpi: 260 cps\r\nAntarmuka / Interface USB 2.0, parallel (Bi-directional) and Serial RS232\r\nO/S Compatibility Microsoft® Windows® 2000 / XP / 7, Microsoft® Windows Vista®\r\nMemori Standar 128KB\r\nMedia Type Continuous Paper, Cut Sheet, Envelope, Roll Paper\r\nVoltase AC 120V / AC 220 – 240V\r\nDimensi Produk 362 x 275 x 154mm', '2019-06-20', 'bekas', 1, NULL, 'barang_69a79dac47c47.jpeg'),
(112, '(Tidak ada)', NULL, 'SAMSUNG MONITOR 22 INCH ESSENTIAL S3 IPS FHD 1080P 5MS 100HZ', 'Komponen Komputer & Laptop', '0WK1HNAY900309P', '', 1, 'Display Size: 22 Inch\r\n\r\nDisplay Type: IPS Flat\r\n\r\nDisplay Resolution: FHD 1920 x 1080\r\n\r\nAspect Ratio: 16:9\r\n\r\nRefresh Rate: Max 100Hz\r\n\r\nResponse Time: 5ms GTG\r\n\r\nBrightness: 250 cd/㎡\r\n\r\nContrast Ratio: 1000:1\r\n\r\nViewing Angle: 178° Horizontal / 178° Vertical\r\n\r\nColor Support: Max 16.7 Million Colors\r\n\r\nColor Gamut: 72 Percent NTSC', '2026-03-06', 'baru', 22, NULL, 'barang_69aa21cb7e97a.jpeg'),
(113, '(Tidak ada)', NULL, 'Cartridge tinta orisinal Canon PG-47 Black (hitam)', 'Komponen Printer & Scanner', 'PG-47', '', 1, '-', '2026-03-06', 'baru', 35, NULL, 'barang_69aa22d685483.jpeg'),
(114, '(Tidak ada)', NULL, 'Cartridge warna Canon PIXMA CL-57 (Cyan, Magenta, Yellow)', 'Komponen Printer & Scanner', 'CL-57', '', 1, 'Cartridge warna Canon PIXMA CL-57 (Cyan, Magenta, Yellow) adalah tinta orisinal berkualitas tinggi berkapasitas 13-15 ml, dirancang khusus untuk printer Canon seri E (E400, E410, E470, E3170, E4270). Produk ini menghasilkan cetakan tajam, tahan lama (ChromaLife100)', '2026-03-05', 'baru', 35, NULL, 'barang_69aa236b92f52.jpeg'),
(115, '001/LOG/NICU/III/2026', NULL, 'Handphone Infinix Smart 20 (X6840)', 'Perangkat Mobile', 'IMEI 1 : 350991273949970 & IMEI 2 : 350991276917958', '', 1, 'Layar: 6,78 inci IPS LCD, resolusi HD+ (\r\n piksel), refresh rate 120Hz.\r\nChipset: MediaTek Helio G81 (12nm).\r\nMemori: RAM 4GB (LPDDR4X) + Penyimpanan Internal 64GB/128GB (eMMC 5.1).\r\nKamera Belakang: 8MP + 2MP (Dual-LED flash).\r\nKamera Depan: 8MP.\r\nBaterai & Pengisian Daya: 5200mAh, 10W/15W Wired (USB Type-C).\r\nOS: Android 16, XOS 16.\r\nFitur Lain: 4G LTE, Wi-Fi 5, Bluetooth 5, NFC, Fingerprint & Face Unlock, Dual Stereo Speaker, IP64 (tahan debu/percikan air), ketebalan 7.7mm', '2026-03-09', 'baru', 36, NULL, 'barang_69ae688b70994.jpeg'),
(117, '(Tidak ada)', NULL, 'PC Desktop (Unit CPU / PC Rakitan)', 'Komputer & Laptop', '25/03/26', '(Beum Ada)', 1, 'Spesifikasi PC\r\nProcessor (CPU):\r\nAMD A4-4000 APU with Radeon HD Graphics @ 3.00 GHz\r\nRAM:\r\n10.0 GB (yang bisa dipakai sekitar 5.21 GB usable)\r\nSystem Type:\r\n64-bit Operating System\r\nGraphics (GPU):\r\nIntegrated (Radeon HD Graphics dari APU AMD A4-4000)\r\nPen & Touch:\r\nTidak support (No Pen or Touch Input)', '2026-03-25', 'bekas', 1, NULL, 'barang_69ca1d596e6fc.jpeg'),
(118, '(Tidak ada)', NULL, 'PC Desktop (Unit CPU / PC Rakitan)', 'Komputer & Laptop', '09/09/2019', '(Beum Ada)', 1, 'spesifikasi:\r\nCPU: Intel Core 2 Duo E8400 (Dual-core 3.0 GHz)\r\nRAM: 3 GB\r\nOS: Windows 10 Pro 64-bit\r\nLayar sentuh: Tidak ada', '2019-09-09', 'bekas', 1, NULL, 'barang_69ca2ad0b40c0.jpeg'),
(119, '003/LOG/IT/IV/2026', NULL, 'PC Desktop (Unit CPU / PC Rakitan)', 'Komputer & Laptop', 'Belum Ada', '192.168.1.45', 1, '-', '2026-04-13', 'baru', 1, NULL, 'barang_69dc46e8e63e2.jpeg'),
(120, '001/LOG/ICU/IV/2026', NULL, 'Printer & Scanner Epson L3211', 'Printer & Scanner', 'XE4Q144226', '', 1, 'Epson EcoTank L3211 adalah printer multifungsi ukuran A4 yang bisa digunakan untuk mencetak, memindai, dan menyalin dokumen. Printer ini menggunakan teknologi Epson Micro Piezo™ dengan sistem tangki tinta yang terkenal hemat biaya, serta memakai tinta Epson 003 yang mampu mencetak hingga sekitar 4.500 halaman hitam dan 7.500 halaman warna.\r\n\r\nDari segi performa, printer ini memiliki kecepatan cetak hingga 33 ppm (draft) dengan standar ISO sekitar 10 ipm untuk hitam dan 5 ipm untuk warna, serta mampu menghasilkan cetakan berkualitas tinggi termasuk foto borderless hingga ukuran 4R. Selain itu, fitur pemindai (scanner) juga mendukung resolusi tinggi sehingga hasil scan tetap tajam.\r\n\r\nUntuk konektivitas, printer ini hanya menggunakan USB 2.0 tanpa dukungan Wi-Fi, sehingga penggunaannya lebih cocok untuk satu perangkat langsung. Secara keseluruhan, printer ini cocok untuk kebutuhan rumahan atau kantor kecil yang membutuhkan hasil cetak bagus dengan biaya operasional yang rendah.', '2026-04-13', 'baru', 35, NULL, 'barang_69dc59226197f.jpeg'),
(121, '(Tidak ada)', 24, 'Type-C To HDMI 4k 30Hz (MIKUSO)', 'Komponen Komputer & Laptop', '44', '', 1, 'Tidak ada', '2026-04-10', 'baru', 1, NULL, 'barang_69dc98df61bc3.jpeg'),
(122, '(Tidak ada)', NULL, 'Converter USB 3.0 to HDMI', 'Komponen Komputer & Laptop', 'Tidak ada', '', 1, '-', '2026-04-13', 'baru', 1, NULL, 'barang_69dc99aff09ca.jpeg'),
(123, '(Tidak ada)', NULL, 'EZVIZ CCTV Outdoor', 'CCTV & Keamanan', 'BE9909852', '', 1, 'EZVIZ smart home Camera (CS-H3c)', '2026-04-10', 'baru', 1, NULL, 'barang_69dc9e0a9b84e.jpeg'),
(124, '-', NULL, 'EZVIZ CCTV INDOOR', 'CCTV & Keamanan', 'BH7352313, BH7350442', '', 2, 'EZVIZ 2MP', '2026-04-15', 'baru', 1, NULL, ''),
(125, '001/LOG/HD/IV/2026', NULL, 'Handphone Infinix X6840', 'Perangkat Mobile', '9526331000009274', '', 1, '-', '2026-04-18', 'baru', 34, NULL, 'barang_69e2f23e53990.jpeg'),
(126, '', 26, 'Kabel data Vention Mini USB 2.0 to Mini-USB', 'Komponen Komputer & Laptop', 'Tidak Ada', '', 1, '-', '2026-04-10', 'baru', 1, NULL, 'barang_1777267531_6222.jpeg'),
(127, '(Tidak ada)', NULL, 'Keyboard Wireless Bluetooth Touchpad Untuk Ipad Tablet Android PC Laptop', 'Komponen Komputer & Laptop', '5/5/2026', '', 1, '-', '2026-05-05', 'baru', 12, NULL, 'barang_69f9a91b99069.jpeg'),
(128, '(Tidak ada)', NULL, 'Laptop Dell Inspiron 3593', 'Komputer & Laptop', '08/04/25', '192.168.1.84', 1, 'OS: Windows 11 Home Single Language 64-bit\r\nProcessor: Intel Core i7-1065G7 (Gen 10) - 4 core / 8 thread, base 1.3 GHz (boost sampai ±3.9 GHz)\r\nRAM: 8 GB\r\nDirectX: Versi 12\r\nBIOS: 1.30.0\r\nGrafis: Intel Iris Plus Graphics (integrated)\r\nLayar: 15.6 inci (Full HD biasanya)', '2025-04-08', 'bekas', 1, NULL, 'barang_69fa9a15b7b59.jpeg'),
(129, '056/IT/LOG/RSPI/2023', NULL, 'Tenda N301 Modem Router Wireless WiFi 300Mbps (2 antene)', 'Komponen Network', 'E0682013319018641', '', 1, 'standard IEEE802.11n/g/b , IEEE802.3/3u\r\nInterfacenya 1xWAN 10/100Mbps dan 3xLAN 10/100Mbps\r\nAntena : 2x 5dBi fixed\r\nSecurity : WEP/WPA/WPA2\r\nIndikator LED : SYS, WLAN, WAN, LAN 1 - 3\r\nFitur Tambahan : Universal Repeater, WISP, WDS, AP WiFi Radio On/Off, Port Forward, DHCP Server, DDNS, MAC/IP Filtering, QoS.', '2023-07-11', 'rusak', 1, NULL, 'barang_6a018fa6ac8e5.jpeg'),
(130, '', 27, 'Tinta Pigment Printer Epson isi 1 Liter PREMIUM INK ( Made in Korea ) - Black', 'Komponen Printer & Scanner', '25/04/26', '', 5, '-', '2026-04-21', 'baru', 1, NULL, 'barang_1778559751_9224.jpeg'),
(131, '', 28, 'VENTION KABEL HDMI TO HDMI 10M GOLD PLATE HIGH QUALITY AAC', 'Komponen Komputer & Laptop', '22/4/2026', '', 1, '- MIKUSO CBL-022 10M\r\n- 4Kx2K HDMI Cable HDMI Round Cable\r\n- Panjang kabel 10 meter\r\n- Plug And Play Connection\r\n- High-Speed USB Cable\r\n- Gold Plated\r\n- Cocok untuk semua Device HDMI ( Projector, DVD Player, Home Theater, PS3, XBOX, Laptop, Komputer dll )', '2026-04-22', 'baru', 1, NULL, 'barang_1778560110_1312.jpeg'),
(132, '004/LOG/IT/V/2026', NULL, 'TP-LINK EAP110 EAP-110 Outdoor 300Mbps Wireless N Outdoor Access Point', 'Komponen Network', '22520C8003753', '', 1, 'TP LINK EAP 110 Outdoor Wireless Access point\r\nproducts/details/cat-5693_EAP110-\r\n\r\n300Mbps Wireless N Outdoor Access Point\r\nEAP110-Outdoor\r\nBuilt for outdoor Wi-Fi applications\r\nUp to 300Mbps Wi-Fi with 2x2 MIMO technology\r\nHigh transmission power and high gain antennas provide a long-range coverage area\r\nDurable, weatherproof enclosure to withstand fair and foul weather\r\nPassive PoE(Power over Ethernet) support and simple mounting design allow for flexible deployment and convenient installation\r\nFree Auranet Controller Software lets administrators easily manage hundreds of EAPs\r\nCaptive portal provides a convenient method for guest authentication\r\nSupports management vlan for an enhanced network management', '2026-05-18', 'baru', 1, NULL, 'barang_6a0aa37e00d8e.jpeg'),
(133, '(Tidak ada)', NULL, 'ADATA SSD SU650 256GB', 'Komponen Komputer & Laptop', '4P3520701593', '', 1, '- Capacity: 256GB,\r\n- NAND Flash: 3D NAND,\r\n- Interface: SATA 6Gb/s (SATA III),\r\n- Form factor: 2.5,\r\n- MTBF: 2,000,000 hours,\r\n- Performance (Max): Read 520MBps; Write 450MBps,\r\n- Dimensions (L x W x H): 100.45 x 69.85 x 7mm,\r\n- Weight: 47.5g,\r\n- Operating temperature: 0deg. C-70deg. C,\r\n- Storage temperature: -40deg. C-85deg. C,\r\n- Shock resistance: 1500G/0.5ms,\r\n- Error correction: ECC,', '2026-05-18', 'baru', 10, NULL, 'barang_6a0aa4b22922f.jpeg'),
(134, '005/LOG/IT/VI/2026', NULL, 'PC Desktop (Unit CPU / PC Rakitan)', 'Komputer & Laptop', 'Tidak ada', '192.168.1.55', 1, '-', '2026-06-12', 'baru', 1, NULL, 'barang_6a2b6ffa16012.jpeg'),
(135, '(Tidak ada)', NULL, 'RAM 8 GB DDR 3 Merek KingSton', 'Komponen Komputer & Laptop', 'T2535644', '', 1, '-', '2026-06-10', 'baru', 1, NULL, 'barang_6a2b70c7f0e69.jpeg'),
(136, '001/LOG/FO/VI/2026', NULL, 'Printer Brother DCP-T220', 'Printer & Scanner', 'E80714H3H183305', '', 1, 'Fungsi : Print, Scan, Copy, Dimensi Produk (Lebar X Kedalaman X Tinggi) : 435 mm × 359 mm × 159 mm, TipePrinter : Inkjet Printer, KecepatanCetak : 16 (Mono) / 9 (Col) ipm-FPOT: 6.5 (Mono) / 10 (Col) seconds', '2026-06-12', 'baru', 3, NULL, 'barang_6a2b7342ad487.jpeg'),
(137, '(Tidak ada)', NULL, 'Printer Epson L3210', 'Printer & Scanner', 'XAGK017668', '', 1, '-', '2024-07-18', 'bekas', 38, NULL, 'barang_6a33e0e133a6e.jpeg'),
(138, '002/LOG/FARM/VI/2026', NULL, 'PRINTER THERMAL XPRINTER XP-480B', 'Printer & Scanner', 'T424BUE258210003', '', 1, 'Interface : USB & Bluetooth\r\nSupport OS : Android, Windows, Linux & IOS\r\nPrint Speed : 152mm/s\r\nResolution : 203DPI (8 dot/mm)\r\nPaper Type : Thermal Label Paper\r\nMax Paper Size : 100x115mm (width x diameter)\r\nPaper thickness : 0.06 - 0.25mm\r\nInput Power : AC100-240V 1A 60/50Hz', '2026-06-20', 'baru', 15, NULL, 'barang_6a360433618f6.jpeg'),
(139, '003/LOG/FARM/VI/2026', NULL, 'PRINTER THERMAL XPRINTER XP-480B', 'Printer & Scanner', 'T424BUE259180008', '', 1, 'Interface : USB & Bluetooth\r\nSupport OS : Android, Windows, Linux & IOS\r\nPrint Speed : 152mm/s\r\nResolution : 203DPI (8 dot/mm)\r\nPaper Type : Thermal Label Paper\r\nMax Paper Size : 100x115mm (width x diameter)\r\nPaper thickness : 0.06 - 0.25mm\r\nInput Power : AC100-240V 1A 60/50Hz', '2026-06-23', 'baru', 16, NULL, 'barang_6a39f2fd53a45.jpeg'),
(140, '(Tidak ada)', NULL, 'Solution Digital Persona U are U 4500 Free SDK', 'Komponen Komputer & Laptop', 'VD20E15992', '', 1, 'Sensor: Sensor Sidik Jari Optik Digital PersonaResolusi Gambar: 512 dpi (8-bit grayscale / 256 skala abu-abu)Area Pemindaian: 14,6 mm (lebar) × 18,1 mm (panjang)Kecepatan Verifikasi: Kurang dari 1 detik (<1 detik)Antarmuka: Kabel USB (Kompatibel dengan USB 1.0, 1.1, dan 2.0)Keamanan: Enkripsi data 128-bitDimensi Fisik: 79 × 49 × 19 mmSistem Operasi: Kompatibel dengan semua sistem Windows dan Linux', '2026-06-30', '-', NULL, NULL, 'barang_6a432c79d104c.jpeg');

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
(3, 1, '234.030221', 'Izin Cuti Melahirkan', 6, '2026-05-04', '2026-05-09', '2026-02-10', 'keluar kota', 'Ditolak', 10),
(4, 1, '234.030221', 'Izin Cuti Tahunan', 2, '2026-04-02', '2026-04-03', '2026-04-04', 'cuti tahunan', 'Diterima', 10),
(6, 4, '629.271224', 'Izin Cuti Tahunan', 1, '2026-04-04', '2026-04-04', '2026-04-06', 'Acara keluarga', 'Diterima', 10),
(7, 2, '662.140725', 'Izin Cuti Tahunan', 1, '2026-04-30', '2026-04-30', '2026-04-02', 'perjalanan balik dari pelatihan', 'Diterima', 10),
(8, 12, '635.090125', 'Izin Cuti Tahunan', 1, '2026-06-15', '2026-06-15', '2026-06-17', 'Menghadiri acara keluarga', 'Diterima', 10),
(9, 2, '662.140725', 'Izin Cuti Tahunan', 1, '2026-06-15', '2026-06-15', '2026-06-17', 'Acara Keluarga', 'Diterima', 10);

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
(29, 15, 'Karena jalur LAN dari server ke HD putus koneksi jaringan lantai 1 semua tidak terhubung ke server, kondisi urgent Jalur ulang kabel LAN dari NICU ke HD'),
(30, 16, 'Deploy serta perbaikan picare versi 1.1.0'),
(31, 17, 'Awalnya laporan tidak bisa masuk windows, pas dicek cpu panas ternyata kipas prosessor mati dan penggantian kipas prosessor dilakukan'),
(32, 18, 'Perbaikan display antrian farmasi'),
(33, 19, 'perbaikan printer error loket 2 FO'),
(34, 19, 'install ulang system operasi windows komputer Akbar RM'),
(35, 20, 'Mengisi, menguras pembuangan tinta dan cleaning deep printer FO loket 2 dan loket 5'),
(36, 21, 'persiapan presentasi hasil workshop implementasi apotek online bpjs kesehatan'),
(37, 22, 'Jaringan di yakut C mati disebabkan oleh switch hub error, restart switch hub'),
(38, 23, 'Menindaklanjuti laporan FO 5 terkait printer tidak dapat mencetak. Ditemukan paper jam dan antrean print menumpuk. Dilakukan penghapusan antrean print, penarikan kertas yang tersangkut, serta restart printer. Setelah itu, printer kembali normal dan dapat digunakan.'),
(39, 24, 'Menindaklanjuti laporan dari Poli Mata terkait printer yang tidak dapat menyala.Dilakukan pengecekan pada sambungan listrik dan kabel power printer. Setelah dilakukan lepas pasang kabel power, printer kembali menyala dan dapat digunakan normal.'),
(40, 25, 'Perbaikan picare masalah no rawat dan no reg double'),
(41, 26, 'Perbaikan masalah Anjungan Pasien Mandiri baru terkait tampilan atau animasi'),
(42, 27, 'Perbaikan di Anjungan Pasien Mandiri baru terkait print-an tiket'),
(43, 28, 'reset printer error kasir tidak bisa ngeprint'),
(44, 28, 'cleaning via tombol kombinasi printer loket 5 hasil print putus-putus '),
(45, 28, 'analisis jaringan poli rawat jalan'),
(46, 29, 'Melakukan pembuatan desain tampilan informasi display Poli Gigi untuk dr. Shella menggunakan Canva serta melakukan penambahan data informasi pada sistem display.'),
(47, 30, 'Perbaikan koneksi Cloudflare Tunnel pada server 192.168.1.108 dengan me-restart service cloudflared yang berstatus DOWN. Setelah restart, koneksi kembali normal.');

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
(15, 13, '2026-03-26', '02:01:00', '06:02:00', 'Diterima', 10, '2026-03-27 07:07:56'),
(16, 4, '2026-04-15', '16:00:00', '20:00:00', 'Diterima', 10, '2026-04-27 02:03:48'),
(17, 13, '2026-05-01', '09:15:00', '11:40:00', 'Diterima', 10, '2026-05-02 01:48:28'),
(18, 4, '2026-05-06', '16:00:00', '18:00:00', 'Diterima', 10, '2026-05-07 07:47:54'),
(19, 13, '2026-05-05', '21:09:00', '23:30:00', 'Diterima', 10, '2026-05-12 02:26:22'),
(20, 13, '2026-05-09', '19:20:00', '21:30:00', 'Diterima', 10, '2026-05-12 02:28:18'),
(21, 2, '2026-05-11', '16:00:00', '18:00:00', 'Diterima', 10, '2026-05-25 02:51:13'),
(22, 13, '2026-05-24', '20:54:00', '21:57:00', 'Diterima', 10, '2026-05-25 02:54:39'),
(23, 12, '2026-05-20', '17:16:00', '18:00:00', 'Diterima', 10, '2026-05-25 02:54:52'),
(24, 12, '2026-05-18', '17:23:00', '17:35:00', 'Diterima', 10, '2026-05-25 03:03:47'),
(25, 4, '2026-03-13', '16:00:00', '18:00:00', 'Diterima', 10, '2026-05-25 03:09:13'),
(26, 4, '2026-05-07', '16:00:00', '17:00:00', 'Diterima', 10, '2026-05-25 03:16:08'),
(27, 4, '2026-05-18', '16:00:00', '17:00:00', 'Diterima', 10, '2026-05-25 03:17:25'),
(28, 13, '2026-06-08', '19:40:00', '21:45:00', 'Diterima', 10, '2026-06-09 03:10:27'),
(29, 12, '2026-06-16', '13:55:00', '15:15:00', 'Diterima', 10, '2026-06-15 12:03:13'),
(30, 12, '2026-06-15', '10:08:00', '11:08:00', 'Diterima', 10, '2026-06-15 12:08:16');

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
-- Dumping data for table `tb_logbook`
--

INSERT INTO `tb_logbook` (`id_log`, `id_user`, `tanggal_log`, `judul_log`, `deskripsi_log`, `catatan_log`, `tanggal_selesai`, `status_log`) VALUES
(12, 3, '2025-10-02 00:00:00', 'IT Support', 'Mouse Komputer Farmasi Ralan error klik', 'Restart Windows', NULL, 'Belum'),
(13, 3, '2025-10-02 00:00:00', 'IT Support', 'komputer Manajemen Elita sharing printer', 'belum dihidupkan dan kabel belum terpasang', NULL, 'Belum'),
(14, 3, '2025-10-01 00:00:00', 'IT Support', 'Bantu perbaiki CCTV mati, modem Akses Point dapur bawah direstart', 'cabut pasang adaptor listrik', NULL, 'Belum'),
(15, 3, '2025-10-01 00:00:00', 'IT Support', 'Backup dan install ulang windows komputer manajemen Elita', 'Manufaktur motherboard jadul susah terintegrasi antara ssd dan windows', NULL, 'Belum'),
(16, 3, '2025-10-03 00:00:00', 'IT Support', 'keyboard farmasi ralan error', 'ganti keyboard', NULL, 'Belum'),
(17, 3, '2025-10-03 00:00:00', 'IT Support', 'Printer Poli Mata tidak bisa dihidupkan', 'dibulak-balik kabel listrik', NULL, 'Belum'),
(18, 12, '2025-12-22 08:00:00', 'Penanganan Gangguan Koneksi Display Antrian FO Rawat Jalan', 'Menindaklanjuti laporan gangguan pada display antrian panggil di FO Rawat Jalan, di mana monitor bagian atas tidak menampilkan informasi meskipun komputer dalam kondisi menyala.', 'Dilakukan pengecekan awal terhadap kondisi komputer dan koneksi perangkat. Ditemukan bahwa sistem display antrian tidak terhubung dengan baik ke monitor, meskipun unit komputer dalam keadaan aktif. Sebagai langkah penanganan, dilakukan proses restart komputer, dan setelah itu koneksi antara komputer dan monitor kembali normal sehingga display antrian dapat berfungsi kembali.', '2025-12-22 08:10:00', 'Selesai'),
(19, 12, '2025-12-22 08:30:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang Komdik 1 dan Aula RSPI pada hari Senin, 22 Desember 2025.', 'Rincian kegiatan sebagai berikut: Pukul 08.30 – selesai Ruang Komdik 1 Kegiatan Rapat Koordinasi Penambahan Ruang OK dengan jumlah peserta ±12 orang. (Meliputi persiapan ruangan, pengecekan perangkat pendukung kegiatan, dan kesiapan teknis acara) Pukul 09.00 – selesai Ruang Aula RSPI Kegiatan Instalasi Alat AGD dengan jumlah peserta ±15 orang. (Meliputi persiapan ruangan, penataan fasilitas, dan dukungan teknis selama kegiatan berlangsung)', '2025-12-22 16:00:00', 'Selesai'),
(20, 12, '2025-12-22 09:05:00', 'Perbaikan Kualitas Hasil Cetak Printer Counter Lantai 2', 'Melakukan pengecekan dan perbaikan printer di lantai 2 berdasarkan laporan hasil cetak yang tidak bagus. Pemeriksaan difokuskan pada kualitas tinta dan kondisi head printer.', 'Setelah dilakukan proses cleaning pada printer, kualitas hasil cetak mengalami peningkatan dan saat ini sudah terlihat lebih baik serta dapat digunakan kembali untuk kebutuhan operasional.', '2025-12-22 09:27:00', 'Selesai'),
(21, 12, '2025-12-22 09:40:00', 'Penanganan Kendala Printer Sharing di Ruang Keucubung', 'Menindaklanjuti laporan gangguan pencetakan di ruang Keucubung berupa proses cetak yang tersendat, hasil cetak terpotong, serta printer yang terkadang tidak dapat digunakan.', 'Setelah dilakukan pengecekan, diketahui bahwa printer digunakan dengan metode sharing dari Komputer A ke Komputer B. Kendala terjadi pada Komputer B saat melakukan proses cetak. Hasil pemeriksaan menunjukkan kemungkinan gangguan disebabkan oleh Komputer A yang masuk ke mode sleep, sehingga koneksi printer sharing menjadi tidak stabil. Sebagai tindak lanjut, pengaturan Power & Sleep pada Komputer A diubah menjadi Never agar koneksi printer tetap aktif dan proses pencetakan berjalan normal.', '2025-12-22 10:00:00', 'Selesai'),
(22, 12, '2025-12-22 13:15:00', 'Pergantian Mouse di Ruang PONEK', 'Menindaklanjuti laporan dari user terkait gangguan pada mouse wireless di ruang PONEK yang mengalami pergerakan sendiri dan respon yang tersendat.', 'Dilakukan pengecekan awal dengan mengganti baterai mouse wireless, dan setelah penggantian baterai kondisi mouse kembali normal. Namun demikian, atas permintaan user, dilakukan pergantian perangkat mouse wireless menjadi mouse kabel untuk menunjang kenyamanan dan kelancaran penggunaan.', '2025-12-22 13:40:00', 'Selesai'),
(23, 12, '2025-12-22 14:00:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses penerbitan E-ICV untuk 2 pasien umum yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2025-12-22 15:00:00', 'Selesai'),
(24, 12, '2025-12-23 08:00:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang Komdik 1, 2 dan Ruang Keuangan pada hari Selasa, 23 Desember 2025.', 'Rincian kegiatan sebagai berikut:\r\n1. jam 09.30 - selesai, R. Komdik 1, Rapat Mingguan\r\n2. ⁠jam 09.00 - selesai, R. Komdik 2, zoom AMPSR (2 orang)\r\n3. ⁠jam 10.00 - 12.00, R. keuangan, Webinar Analisa Klaim BPJS (4 orang)', '2025-12-23 10:00:00', 'Selesai'),
(25, 12, '2025-12-23 09:00:00', 'Penanganan Komputer Tidak Menyala di Counter Rawat Inap Lantai 3', 'Menindaklanjuti laporan gangguan komputer di counter Rawat Inap Lantai 3 yang tidak dapat menyala pada pukul 09.00 WIB.', 'Berdasarkan laporan dari unit terkait, komputer tidak dapat menyala. Setelah dilakukan pengecekan, ditemukan salah satu kabel pada perangkat komputer dalam kondisi tercabut. Dilakukan pemasangan kembali kabel yang terlepas dan memastikan seluruh koneksi terpasang dengan aman. Setelah penanganan, komputer dapat menyala dan berfungsi kembali dengan normal.', '2025-12-23 09:50:00', 'Selesai'),
(26, 12, '2025-12-23 10:10:00', 'Penanganan Gangguan Booting Komputer di Dapur Gizi Lantai 4', 'Menindaklanjuti laporan gangguan pada komputer di Dapur Gizi Lantai 4 yang tidak dapat masuk ke sistem Windows dan menampilkan layar hitam saat dinyalakan.', 'Setelah dilakukan pengecekan, diketahui bahwa perangkat penyimpanan tidak terbaca pada BIOS. Dilakukan tindakan lepas–pasang kabel SATA dan memastikan koneksi terpasang dengan baik. Setelah pemasangan ulang dan dilakukan proses penyalaan kembali, komputer dapat booting normal dan sistem Windows berhasil dijalankan.', '2025-12-23 10:52:00', 'Selesai'),
(27, 12, '2025-12-23 10:55:00', 'Penanganan Gangguan Printer di Counter Perawat IGD', 'Menindaklanjuti laporan dari counter perawat IGD terkait gangguan printer yang tidak dapat digunakan untuk mencetak.', 'Setelah dilakukan pengecekan, ditemukan adanya kertas yang menyangkut pada printer sehingga proses cetak tidak dapat berjalan. Kertas yang menyangkut kemudian dikeluarkan dan printer dapat digunakan kembali.\r\nSelanjutnya, saat dilakukan percobaan cetak, hasil cetakan warna hitam terlihat putus-putus. Dilakukan proses cleaning printer, dan setelah tindakan tersebut kualitas hasil cetak kembali normal.', '2025-12-23 11:30:00', 'Selesai'),
(28, 12, '2025-12-23 11:24:00', 'Penanganan Drive D Tidak Terdeteksi di Dapur Gizi', 'Menindaklanjuti laporan gangguan pada komputer di Dapur Gizi terkait drive D yang tidak muncul pada sistem.', 'Setelah dilakukan pengecekan, ditemukan bahwa drive penyimpanan tidak terdeteksi oleh sistem. Dilakukan tindakan lepas–pasang kabel SATA untuk memastikan koneksi perangkat penyimpanan terpasang dengan baik. Setelah dilakukan pemasangan ulang dan pengecekan kembali, drive D dapat terdeteksi dan digunakan secara normal.', '2025-12-23 11:50:00', 'Selesai'),
(29, 12, '2025-12-24 09:30:00', 'Penanganan Gangguan Printer di Ruang Keperawatan (Mba Yeni)', 'Menindaklanjuti laporan dari ruang keperawatan (Mba Yeni) terkait gangguan printer yang tidak dapat digunakan untuk mencetak.', 'Setelah dilakukan pengecekan, ditemukan adanya kertas yang menyangkut pada printer sehingga proses cetak tidak dapat berjalan. Kertas yang menyangkut kemudian dikeluarkan dan printer dapat digunakan kembali.', '2025-12-24 09:55:00', 'Selesai'),
(30, 12, '2025-12-24 08:30:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang Komdik keuangan dan Aula RSPI pada hari Rabu, 24 Desember 2025.', 'Rincian kegiatan sebagai berikut:\r\n1. Pukul 08.30 – selesai\r\nRuang Keuangan\r\nKegiatan Rapat Koordinasi RAB\r\n2. Aula jam 10.00 - selesai, R. Aula, rakor pemantapan Relawan Posko Kesehatan (50 orang)', '2025-12-24 10:30:00', 'Selesai'),
(31, 12, '2025-12-24 10:32:00', 'Pergantian Keyboard di Ruang PONEK', 'Menindaklanjuti laporan dari unit PONEK terkait permintaan pergantian keyboard yang dikatakan mengalami kerusakan.', 'Setelah dilakukan pengecekan, diketahui bahwa keyboard wireless tidak dapat digunakan karena dongle tidak ditemukan. Atas permintaan user, dilakukan pergantian keyboard agar aktivitas kerja dapat berjalan dengan lancar.', '2025-12-24 10:43:00', 'Selesai'),
(32, 12, '2025-12-24 13:15:00', 'Pergantian Mouse di Dapur Lt 4', 'Menindaklanjuti laporan dari unit Dapur terkait gangguan pada mouse yang tidak dapat digerakkan.', 'Setelah dilakukan pengecekan, mouse tidak dapat digunakan sebagaimana mestinya. Dilakukan pergantian mouse untuk memastikan kelancaran aktivitas kerja di unit Dapur', '2025-12-24 13:20:00', 'Selesai'),
(33, 12, '2025-12-24 13:30:00', 'Penanganan Gangguan UPS di Unit TU', 'Menindaklanjuti laporan dari unit TU terkait UPS yang tidak dapat menyimpan daya dan mengeluarkan bunyi alarm secara terus-menerus.', 'Setelah dilakukan pengecekan, diketahui bahwa kabel power UPS tidak terpasang dengan benar atau dalam kondisi longgar pada sumber listrik. Dilakukan pemasangan ulang kabel power hingga terhubung dengan baik. Setelah penanganan, UPS dapat menyimpan daya dan alarm berhenti berbunyi.', '2025-12-24 13:55:00', 'Selesai'),
(34, 12, '2025-12-24 11:15:00', 'Penanganan Printer Tidak Menyala di Ruang USG Radiologi', 'Menindaklanjuti laporan dari unit Radiologi (Ruang USG) terkait printer yang tidak menyala saat akan digunakan untuk mencetak hasil pemeriksaan USG.', 'Setelah dilakukan pengecekan, dilakukan tindakan berupa cabut–pasang (plug in/out) kabel power printer. Setelah pemasangan kembali, printer dapat menyala dan digunakan untuk mencetak hasil USG sesuai kebutuhan dokter.', '2025-12-24 11:30:00', 'Selesai'),
(35, 12, '2026-01-05 09:00:00', 'Penanganan Komputer Tidak Menyala di Unit TU', 'Menerima laporan dari Liza (TU) terkait komputer yang tidak dapat menyala. Setelah dilakukan pengecekan, komputer sebenarnya dalam kondisi menyala namun tidak menampilkan tampilan pada monitor.', 'Dilakukan pembersihan RAM dan baterai CMOS. Setelah tindakan tersebut, komputer kembali berfungsi normal dan tampilan monitor muncul dengan baik.', '2026-01-05 05:20:00', 'Selesai'),
(36, 12, '2026-01-05 08:10:00', 'Penanganan Gangguan Koneksi Display Antrian FO Rawat Jalan', 'Menindaklanjuti laporan gangguan pada display antrian panggil di FO Rawat Jalan, di mana monitor bagian atas tidak menampilkan informasi meskipun komputer dalam kondisi menyala.', 'Dilakukan pengecekan awal terhadap kondisi komputer dan koneksi perangkat. Ditemukan bahwa sistem display antrian tidak terhubung dengan baik ke monitor, meskipun unit komputer dalam keadaan aktif. Sebagai langkah penanganan, dilakukan proses restart komputer, dan setelah itu koneksi antara komputer dan monitor kembali normal sehingga display antrian dapat berfungsi kembali.', '2026-01-05 08:15:00', 'Selesai'),
(37, 12, '2026-01-05 09:29:00', 'Pergantian Kabel Power dan Kabel VGA Monitor', 'Menindaklanjuti laporan dari Mas Adi terkait tampilan monitor yang berubah warna menjadi kuning–putih secara bergantian serta monitor yang terkadang mati dan menyala sendiri. Dilakukan pengecekan pada perangkat monitor dan koneksi kabel.', 'Dilakukan pergantian kabel power monitor dan kabel VGA dengan yang baru. Setelah pergantian kabel, tampilan monitor kembali normal dan tidak ditemukan gangguan lanjutan.', '2026-01-05 09:40:00', 'Selesai'),
(38, 12, '2026-01-05 09:55:00', 'Penanganan Komputer Tidak Menyala di Unit PONEK', 'Menerima laporan dari unit PONEK terkait komputer yang tidak dapat menyala. Setelah dilakukan pengecekan, ditemukan bahwa kabel power yang terhubung ke komputer dalam kondisi tidak berfungsi dengan baik.', 'Dilakukan pergantian kabel power komputer dengan kabel baru. Setelah dilakukan penggantian, komputer dapat menyala dan berfungsi normal kembali.', '2026-01-05 10:35:00', 'Selesai'),
(39, 12, '2026-01-05 09:30:00', 'Penanganan Gangguan Jaringan WiFi Indoor Poli', 'Menerima laporan dari perawat poli terkait jaringan WiFi indoor poli yang tidak dapat terhubung. Setelah dilakukan pengecekan, dilakukan tindakan restart pada perangkat access point.', 'Restart access point dilakukan dengan cara mencabut dan memasang kembali kabel LAN pada perangkat di Poli Gigi. Setelah tindakan tersebut, koneksi WiFi kembali normal dan dapat digunakan.', '2026-01-05 10:55:00', 'Selesai'),
(40, 12, '2026-01-05 08:45:00', 'Update Patch SIMRS Khanza Terkait IDRG di Unit RM', 'Melakukan update patch SIMRS Khanza terkait pembaruan IDRG pada unit Rekam Medis (RM) bersama IKA IT guna memastikan sistem berjalan sesuai dengan ketentuan dan kebutuhan operasional.', 'Proses update patch berjalan dengan lancar dan sistem dapat digunakan kembali tanpa kendala.', '2026-01-05 08:55:00', 'Selesai'),
(41, 12, '2026-01-05 11:59:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses penerbitan E-ICV untuk 12 pasien umum dan 9 pasein pancar yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-01-05 14:55:00', 'Selesai'),
(42, 12, '2026-01-06 08:25:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang Pelayanan Medis, Keperawatan & MPP (Mba Yeni), Komdik 1 dan Komdik 2 pada hari Selasa, 06 Januari 2026.', 'Rincian kegiatan sebagai berikut: 1. Pukul 08.30 – selesai R. Pelayanan Medis, Keperawatan & MPP (Mba Yeni) Kegiatan Zoom ON JOB TRAINING BAGI RUMAH SAKIT UNIT PELAPOR BARU SKDR. 2. R. Komdik 1 jam 09.00 - selesai kegiatan Rapat Mingguan, 3. R. Komdik 2, Zoom webinar Hospital Analytic Series - Pertemuan 4 : Data-Driven Hospital 2026 : Teknik Analisis Data Layanan untuk Pengambilan Keputusan Strategis 2026', '2026-01-06 09:35:00', 'Selesai'),
(43, 12, '2026-01-06 09:55:00', 'Penanganan Kendala User Account Mlite pada perawat poli', 'Menindaklanjuti laporan dari perawat poli terkait user account atas nama Netty, di mana tombol HPHT tidak dapat diklik serta data SOAP perawat tidak dapat tersimpan. Selain itu, dilaporkan bahwa data pasien dengan dokter penanggung jawab dr. Fathur tidak muncul pada akun Netty.', 'Dilakukan pengecekan pada pengaturan user account dan sistem SIMRS. Setelah dilakukan penyesuaian dan pengecekan ulang, fungsi tombol HPHT, penyimpanan SOAP perawat, serta tampilan data pasien pada akun Netty kembali berjalan normal.', '2026-01-06 11:59:00', 'Selesai'),
(44, 12, '2026-01-06 09:33:00', 'Penanganan Gangguan Jaringan Internet di Counter Lantai 3', 'Menindaklanjuti laporan gangguan jaringan internet di counter lantai 3, di mana koneksi internet tidak dapat digunakan dan pada komputer muncul ikon bola dunia (tidak terhubung jaringan).', 'Dilakukan restart komputer serta pengecekan koneksi jaringan dengan mencabut dan memasang kembali kabel LAN pada switch hub. Setelah dilakukan tindakan tersebut, koneksi internet kembali normal dan dapat digunakan.', '2026-01-06 11:15:00', 'Selesai'),
(45, 12, '2026-01-06 13:15:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses penerbitan E-ICV untuk 7 pasien umum dan 2 pasein pancar yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-01-06 15:30:00', 'Selesai'),
(46, 12, '2026-01-06 13:40:00', 'Penanganan Komputer Mati Mendadak di Unit Kepegawaian', 'Menerima laporan dari Mba Nia (Kepegawaian) terkait komputer yang tiba-tiba mati sendiri dan tidak dapat dinyalakan kembali. Setelah dilakukan pengecekan pada perangkat komputer, ditemukan bahwa tombol ON/OFF pada power supply dalam kondisi OFF.', 'Dilakukan pengaktifan kembali tombol ON/OFF pada power supply dan dilakukan percobaan menyalakan komputer. Setelah tindakan tersebut, komputer kembali menyala dan berfungsi normal.', '2026-01-06 13:45:00', 'Selesai'),
(47, 12, '2026-01-06 14:36:00', 'Penanganan Kendala User Account Mlite pada Perawat Poli', 'Menindaklanjuti laporan kendala pada user account Mlite perawat poli atas nama nety, di mana setelah selesai mengisi SOAP, status pasien tidak berubah menjadi “Berkas Diterima” sebagaimana mestinya.', 'Dilakukan pengecekan sistem dan penanganan langsung dengan melakukan penambahan/penyesuaian kode melalui akses SSH pada server, tepatnya pada file:\r\nrspiweb-lite4/plugins/dokter_ralan/view/admin/display.html\r\nSetelah dilakukan penyesuaian kode, fungsi perubahan status pasien kembali berjalan normal.', '2026-01-06 14:57:00', 'Selesai'),
(48, 12, '2026-01-06 15:01:00', 'Persiapan Agenda Acara di Komdik 1', 'Menindaklanjuti permintaan dari Mba Cindy (Keuangan) untuk mempersiapkan kebutuhan agenda acara yang akan dilaksanakan di Komdik 1.', 'Dilakukan persiapan dan pengecekan sarana pendukung acara guna memastikan kegiatan dapat berjalan dengan lancar.', '2026-01-06 15:09:00', 'Selesai'),
(49, 12, '2026-01-07 08:10:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang Komdik 1 dan keuangan pada hari Rabu, 07Januari 2026.', 'Rincian kegiatan sebagai berikut:\r\n1. jam 09.00 - selesai, R. Komdik 1, Rakor Awal SK komite Medik (7 orang)\r\n2. jam 10.00 - selesai, R. Komdik 1, Rakor Laporan Kinerja 2025 dan Proker 2026 (31 orang)\r\n3. Jam 09.30 - selesai, R keuangan , Rakor keuangan ', '2026-01-10 10:04:00', 'Selesai'),
(50, 12, '2026-01-07 10:06:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses penerbitan E-ICV untuk 4 pasien umum dan 3 pasein pancar yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-01-07 11:10:00', 'Selesai'),
(51, 12, '2026-01-07 10:00:00', 'Penanganan Perubahan Status Pasien Tidak Sesuai di Poli Penyakit Dalam', 'Menindaklanjuti laporan dari Kepala Ruangan Perawat Poli (Mba Kokon) terkait status pasien di Poli Penyakit Dalam yang tiba-tiba berubah menjadi “Selesai Periksa”, padahal perawat belum melakukan pengisian SOAP dan dokter belum melakukan pemeriksaan.', 'Dilakukan penyesuaian status pasien melalui user admin dengan mengubah kembali status menjadi “Belum” agar proses pelayanan dapat dilanjutkan sesuai alur yang berlaku.', '2026-01-07 10:40:00', 'Selesai'),
(52, 12, '2026-01-07 09:14:00', 'Pergantian Browser pada tampilan Display Poli Penyakit Dalam', 'Menindaklanjuti laporan terkait tampilan display di Poli Penyakit Dalam yang terlihat terlalu diperbesar (zoom) sehingga mengganggu tampilan informasi.', 'Sebagai langkah antisipasi sementara, dilakukan pergantian browser ke browser lain. Setelah pergantian browser, tampilan display kembali normal dan dapat digunakan.', '2026-01-07 09:25:00', 'Selesai'),
(53, 12, '2026-01-07 14:55:00', 'Penanganan Kendala WhatsApp Web di Unit Kepegawaian', 'Menindaklanjuti laporan dari Mba Nia (Kepegawaian) terkait WhatsApp Web yang tidak dapat mengirim pesan.', 'Dilakukan penanganan dengan menutup (close) browser Chrome kemudian membukanya kembali. Setelah tindakan tersebut, WhatsApp Web kembali dapat digunakan untuk mengirim pesan dengan normal.', '2026-01-07 15:10:00', 'Selesai'),
(54, 12, '2026-01-08 08:05:00', 'Penanganan Gangguan Koneksi Display Antrian FO Rawat Jalan', 'Menindaklanjuti laporan gangguan pada display antrian panggil di FO Rawat Jalan, di mana suara panggilan nomor antrian tidak ada informasi meskipun komputer dalam kondisi menyala', 'Dilakukan pengecekan awal yaitu dengan cara restart kmputer dan masuk kek website nya kembali sehingga display antrian dapat berfungsi kembali.', '2026-01-08 08:50:00', 'Selesai'),
(55, 12, '2026-01-08 08:30:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang Komdik 1 dan Aula Rs pada hari Kamis, 08 Januari 2026.', 'Rincian kegiatan sebagai berikut: 1. jam 08.30 - selesai, R. Aula, Rapat Bulanan 2 .jam 10.00 - selesai, R. Komdik 2 Webinar Hospital Analytic Materi 5 (3 orang)', '2026-01-08 10:10:00', 'Selesai'),
(56, 12, '2026-01-08 11:00:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses penerbitan E-ICV untuk 17 pasien umum  yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-01-08 13:52:00', 'Selesai'),
(57, 12, '2026-01-09 09:21:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses penerbitan E-ICV untuk 9 pasien pancar dan 5 dari umum yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-01-09 10:54:00', 'Selesai'),
(58, 12, '2026-01-10 09:23:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses penerbitan E-ICV untuk 4 pasien Pancar yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-01-10 09:57:00', 'Selesai'),
(59, 12, '2026-01-10 08:06:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang Komdik 1 dan Aula Rs pada hari Sabtu, 10Januari 2026.', 'Rincian kegiatan sebagai berikut: 1.jam 09.30 - selesai, R. Komdik 1, Presentasi PT Bumi Tenviro Engingeering (13 orang) , 2. ⁠jam 10.00 - selesai, R. Komdik 2, Webinar Hospital Analytic Materi 6 (5 orang)', '2026-01-10 09:00:00', 'Selesai'),
(60, 12, '2026-01-09 08:03:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang Komdik 1 dan Aula Rs pada hari Jumat, 09Januari 2026.', 'Rincian kegiatan sebagai berikut: \r\n1Jam 09.00 - selesai, R. Komdik 1, Rapat Komite Keperawatan (17 orang),\r\n2 ⁠jam 09.30 - selesai, R. Komdik 2, zoom mhs stikes borneo (2 orang),\r\n3 ⁠jam 14.00 - selesai, R. Komdik 1, presentasi penawaran penyusunan dok UKL UPL (9 orang)', '2026-01-09 13:04:00', 'Selesai'),
(61, 12, '2026-01-12 08:47:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang Komdik 1 dan Aula Rs pada hari Sabtu, 12Januari 2026.', 'Rincian kegiatan sebagai berikut: 1. jam 09.00 - selesai R. Komdik 1, Persiapan Kredensial dan Rekredensial (32 orang), 2. ⁠jam 13.00 - selesai R. Komdik 1, Rapat RKA RAB (11 orang)', '2026-01-12 13:02:00', 'Selesai'),
(62, 12, '2026-01-12 09:16:00', 'Pemasangan UPS untuk Alat Radiologi', 'Melakukan pemasangan UPS pada alat radiologi guna menjaga kestabilan daya listrik dan mencegah gangguan operasional akibat listrik padam atau tidak stabil.', 'UPS terpasang dengan baik dan alat radiologi dapat beroperasi dengan normal setelah pemasangan.', '2026-01-12 09:57:00', 'Selesai'),
(63, 12, '2026-01-12 11:05:00', 'Pemasangan Mouse Wireless di Unit TU', 'Menindaklanjuti permintaan dari Mas Yudi (TU) terkait mouse yang digunakan mengalami gangguan, di mana pergerakan kursor tersendat dan sering terhenti.', 'Dilakukan pemasangan mouse wireless sebagai pengganti. Setelah pemasangan, pergerakan kursor kembali normal dan perangkat dapat digunakan dengan baik.', '2026-01-12 11:26:00', 'Selesai'),
(64, 12, '2026-01-12 14:10:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses penerbitan E-ICV untuk 1 pasien UMUM yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-01-12 14:26:00', 'Selesai'),
(65, 12, '2026-01-12 14:30:00', 'Penarikan dan mejalur Kabel LAN untuk Jaringan Internet di Ruangan Radiologi dan Poli Anak', 'Melakukan penarikan dan pemasangan kabel LAN untuk kebutuhan jaringan internet di ruangan baru Radiologi dan Poli Anak bersama Mas Hadi.', 'Kabel LAN terpasang dengan baik dan siap digunakan untuk mendukung koneksi jaringan internet di masing-masing ruangan.', '2026-01-12 18:00:00', 'Selesai'),
(66, 12, '2026-01-13 08:22:00', 'Crimping RJ45 dan Pemasangan Switch Hub Jaringan', 'Pada hari berikutnya, melanjutkan pekerjaan instalasi jaringan dengan melakukan pemasangan konektor RJ45 (crimping) pada kabel LAN yang telah ditarik sebelumnya serta memasang switch hub di lokasi yang telah ditentukan.', 'Dilakukan pengecekan koneksi jaringan internet untuk memastikan seluruh titik jaringan berfungsi dengan baik. Hasil pengecekan menunjukkan jaringan internet berjalan normal.', '2026-01-13 13:26:00', 'Selesai'),
(67, 12, '2026-01-13 10:35:00', 'Penanganan Kendala SIMRS Lambat di Unit Radiologi', 'Menindaklanjuti laporan dari unit Radiologi terkait SIMRS yang mengalami kendala (lambat/tidak responsif). Setelah dilakukan pengecekan, dilakukan tindakan restart pada komputer.', 'Setelah restart, SIMRS dapat berfungsi kembali. Namun demikian, kondisi komputer terindikasi berjalan lambat sehingga akan dilakukan pengecekan dan tindak lanjut lebih lanjut.', '2026-01-13 10:58:00', 'Pending'),
(68, 12, '2026-01-13 08:27:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang Komdik 1 dan Aula Rs pada hari Sabtu, 13Januari 2026.', 'Rincian kegiatan sebagai berikut: \r\n1 jam 09.30 - selesai, R. Komdik 1, Rapat Mingguan ,\r\n2 ⁠jam 09.00 - selesai, R. SDM, asesmen pegawai (3 orang) -> Lanjut R. Komdik 2,\r\n3 jam 10.00 - selesai, R. Komdik 1, Rapat data kunjungan dan sistem Pembayaran (15 orang),\r\n4 jam 13.30 - selesai, R. Komdik 1, rakor Pokja PMKP (12 orang),\r\n5 ⁠jam 14.00 - selesai, R. Komdik 2, zoom PT Ide Inovatif Indonesia (9 orang)', '2026-01-13 15:59:00', 'Selesai'),
(69, 12, '2026-01-14 09:09:00', 'Instalasi Aplikasi SIMRS dan Pemindahan Data ke Komputer Baru', 'Melakukan penginstalan dan konfigurasi beberapa aplikasi pendukung operasional di komputer baru, antara lain aplikasi Mlite, SIMRS, serta aplikasi pendukung lainnya yang dibutuhkan untuk kegiatan pelayanan.', 'Selain instalasi aplikasi, dilakukan juga proses pemindahan data dari komputer lama ke komputer baru guna memastikan data kerja tetap tersedia dan dapat digunakan sebagaimana mestinya. Setelah proses instalasi dan pemindahan data selesai, dilakukan pengecekan fungsi aplikasi dan sistem secara keseluruhan.', '2026-01-14 11:27:00', 'Selesai'),
(70, 12, '2026-01-14 11:15:00', 'Pemasangan UPS di Unit Laboratorium', 'Melakukan pemasangan UPS di unit Laboratorium guna menjaga kestabilan pasokan listrik dan melindungi perangkat dari gangguan listrik seperti mati mendadak atau tegangan tidak stabil.', 'UPS terpasang di 2 komputer dengan baik dan telah dilakukan pengecekan fungsi. Perangkat di Laboratorium dapat beroperasi dengan normal setelah pemasangan.', '2026-01-14 19:41:00', 'Selesai'),
(71, 12, '2026-01-14 09:05:00', 'Penanganan Komputer Mati Berulang di Unit TU', 'Menindaklanjuti laporan dari Liza (TU) terkait komputer yang sering mati secara berulang. Setelah dilakukan pengecekan pada perangkat, tidak ditemukan kerusakan yang terlihat secara langsung.', 'Diduga gangguan disebabkan oleh PSU yang mulai lemah atau kabel power yang terhubung ke UPS dalam kondisi longgar. Sebagai solusi sementara, alur listrik komputer dihubungkan langsung ke stop kontak dan dilakukan penggantian kabel power. Setelah tindakan tersebut, komputer dapat menyala dan digunakan kembali.', '2026-01-14 10:00:00', 'Pending'),
(72, 12, '2026-01-14 13:05:00', 'Penyiapan Display Antrian Farmasi Rawat Jalan dan Pemberian Tutorial', 'Melakukan penyiapan dan pengaktifan display antrian Farmasi Rawat Jalan sesuai permintaan unit terkait.', 'Selain penyiapan display, diberikan juga tutorial kepada petugas mengenai cara mengakses dan menampilkan antrian farmasi agar dapat digunakan secara mandiri dalam kegiatan operasional.', '2026-01-14 13:25:00', 'Selesai'),
(73, 12, '2026-01-14 14:33:00', 'Penataan Ulang dan Setup Letak Kerja Radiologi', 'Melakukan penataan ulang dan setup letak kerja di unit Radiologi sehubungan dengan perpindahan ruangan.', 'Dilakukan pengaturan ulang posisi komputer, perangkat pendukung, serta penyesuaian tata letak kerja agar sesuai dengan kebutuhan operasional dan alur kerja di unit Radiologi.', '2026-01-14 08:33:00', 'Selesai'),
(74, 12, '2026-01-14 13:43:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang Komdik 1 pada hari Rabu, 14Januari 2026.', 'Rincian kegiatan sebagai berikut: jam 14.00 - selesai, R. Komdik 1, Rapat tim Komite Medik (12 orang)', '2026-01-14 13:57:00', 'Selesai'),
(75, 12, '2026-01-15 13:35:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang Komdik 1 dan komdik 2 pada hari Kamis, 15Januari 2026.', 'Rincian Kegiatan :\r\n1. ⁠jam 14.00 - selesai, R. Komdik 1, Rapat Pengurus PPNI RSPI (belum dittd dir jar pa yudi tunggu besok soalnya jam 2 juga acaranya, koordinasi kembali sama pa yudi apakah jadi kegiatannya)\r\n2. ⁠jam 14.00 - selesai, R. Komdik 2, Rapat Gizi', '2026-01-15 13:50:00', 'Selesai'),
(76, 12, '2026-01-15 10:25:00', 'Penanganan Microsoft Word Not Responding', 'Menindaklanjuti laporan terkait aplikasi Microsoft Word yang mengalami kondisi Not Responding. Pengguna meminta agar hasil pekerjaan tidak hilang karena belum sempat melakukan penyimpanan.', 'Dilakukan restart aplikasi/komputer, kemudian membuka kembali Microsoft Word. Sistem Auto Recovery berhasil menampilkan kembali dokumen pekerjaan sebelumnya sehingga data tidak hilang.', '2026-01-15 10:37:00', 'Selesai'),
(77, 12, '2026-01-15 10:28:00', 'Upgrade Perangkat dan Penukaran Komputer untuk Unit PONEK', 'Melakukan penambahan RAM 8 GB jadi total sekarang menajadi 16 GB serta pergantian PSU pada komputer bekas Poli Syaraf dr. Made yang akan dipindahkan penggunaannya ke unit PONEK. Penukaran perangkat dilakukan karena spesifikasi komputer milik dr. Made memiliki spesifikasi paling tinggi dan lebih sesuai untuk kebutuhan operasional PONEK.', 'Sebelum dilakukan penukaran perangkat, dilakukan proses backup data pada komputer PONEK lama dan dipindahkan ke komputer bekas Poli Syaraf dr. Made. Setelah upgrade dan pemindahan data selesai, dilakukan pengecekan fungsi sistem dan perangkat, dan komputer siap digunakan di unit PONEK.', '2026-01-15 14:32:00', 'Selesai'),
(78, 12, '2026-01-15 15:17:00', 'Penyesuaian dan Koneksi Ulang SSID WiFi ke Display Antrian dan Laptop Poli', 'Melakukan penyesuaian dan koneksi ulang SSID WiFi yang sebelumnya telah disetting oleh Mas Hadi, dari RS.RALAN menjadi RS-RALAN, ke beberapa perangkat display antrian ruang tunggu poli serta laptop poli di lokasi THT dan Poli Anak.', 'Setelah dilakukan penyesuaian nama SSID dan koneksi ulang pada perangkat terkait, jaringan WiFi dapat terhubung dengan baik dan digunakan secara normal.', '2026-01-15 15:51:00', 'Selesai'),
(79, 12, '2026-01-15 14:21:00', 'Penanganan Perubahan Sistem Operasi Akibat Dual Boot', 'Menindaklanjuti pengaduan dari Pele terkait tampilan Windows yang kembali ke tampilan lama. Setelah dilakukan pengecekan, diketahui bahwa sistem masuk ke Windows 7.', 'Ditemukan terdapat dual boot pada perangkat dan salah satu hard disk/SSD tidak terbaca sehingga sistem mendeteksi drive lain dan otomatis masuk ke Windows 7. Dilakukan tindakan cabut dan pasang ulang hard disk/SSD, kemudian komputer dinyalakan kembali. Setelah tindakan tersebut, sistem kembali normal dan masuk ke Windows 10.', '2026-01-15 02:34:00', 'Selesai'),
(80, 12, '2026-01-17 08:15:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang Komdik 1 dan R.Keuangan pada hari Sabtu, 17Januari 2026.', 'Rincian Kegiatan :\r\n1. R.Keuangan, Zoom Meet Analisis Kinerja Keuangan : Cara Membaca Angka Keuangan RS untuk Menurunkan Biaya & Meningkatkan Margin,\r\n2. Komdik 1, Reset Ulang Kompetensi Pelayanan Rumah Sakit Berbasis Kompetensi', '2026-01-17 09:50:00', 'Selesai'),
(81, 12, '2026-01-17 09:39:00', 'Penukaran Sementara Komputer dan Penanganan Data di Unit TU', 'Melakukan penukaran sementara komputer bekas unit PONEK untuk digunakan oleh Liza (TU) guna mendukung kelancaran pekerjaan selama proses identifikasi gangguan pada komputer sebelumnya.', 'Dilakukan penyalinan (copy) file-file penting milik Liza ke komputer pengganti serta melakukan pengaturan (setting) printer agar dapat digunakan. Setelah komputer pengganti siap digunakan, dilakukan identifikasi lebih lanjut terhadap permasalahan pada komputer awal.', '2026-01-17 12:49:00', 'Selesai'),
(82, 12, '2026-01-21 08:35:00', 'Persiapan dan Operasional Agenda Rapat Pelatihan BHD Tahap 1', 'Melakukan persiapan agenda acara di aula untuk kegiatan Pelatihan IHT BHD, meliputi penyiapan perangkat dan sarana pendukung acara.', 'Selain persiapan, bertugas sebagai operator selama pelaksanaan kegiatan Pelatihan BHD untuk memastikan acara berjalan dengan lancar.', '2026-01-21 14:24:00', 'Selesai'),
(83, 12, '2026-01-21 10:30:00', 'Aktivasi Windows pada Komputer Unit Manajemen', 'Melakukan aktivasi sistem operasi Windows pada komputer milik Mas Surya (Manajemen) guna memastikan sistem dapat digunakan secara optimal dan sesuai dengan lisensi yang berlaku.', 'Proses aktivasi Windows berhasil dilakukan dan sistem berfungsi normal setelah aktivasi.', '2026-01-21 11:00:00', 'Selesai'),
(84, 12, '2026-01-20 09:23:00', 'Operasional Kegiatan Persiapan BHD di Komdik 1', 'Bertugas sebagai operator di Komdik 1 dalam rangka persiapan kegiatan Pelatihan BHD yang dilaksanakan pada tanggal 21 dan 26.', 'Melakukan pengoperasian dan pengawasan perangkat pendukung kegiatan guna memastikan acara persiapan BHD berjalan dengan lancar.', '2026-01-20 12:17:00', 'Selesai'),
(85, 12, '2026-01-22 08:11:00', 'Penyesuaian Tampilan Warna Status Dokter Ralan pada Website Mlite', 'Menindaklanjuti permintaan dari Mia (Poli) untuk melakukan penyesuaian tampilan warna pada halaman Dokter Ralan di website Mlite.', 'Dilakukan pengaturan tampilan agar warna kembali ke kondisi default. Selain itu, ditambahkan penyesuaian logika tampilan dengan ketentuan sebagai berikut:\r\n1. Status Belum dan SEP Belum ditampilkan dengan warna merah,\r\n2. Status SEP ditampilkan dengan keterangan “Mohon Konfirmasi FO”,\r\nPenyesuaian dilakukan untuk memudahkan identifikasi status pasien oleh petugas.', '2026-01-22 09:20:00', 'Selesai'),
(86, 12, '2026-01-22 09:19:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang Komdik 1 dan R. komdik 2 pada hari Kamis, 22Januari 2026.', 'Rincian Kegiatan : 1. R.komdik 1 menyiapkan acara google meet untuk pertemuan memabahan sidak radiologi, 2. rapat sama org pondok, 3. rapat Pokja MFK komdik 2', '2026-01-22 13:23:00', 'Selesai'),
(87, 12, '2026-01-22 18:23:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses penerbitan E-ICV untuk 15 pasien UMUM yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-01-22 16:13:00', 'Selesai'),
(88, 12, '2026-01-22 16:13:00', 'Rekap dan Pencocokan Data Stok Dokumen dan Vaksin', 'Melakukan rekap data secara bertahap serta mencocokkan stok dokumen dan vaksin sebagai bahan penyusunan laporan vaksin.', 'Data dokumen dan stok vaksin dicocokkan dengan data yang tersedia untuk memastikan kesesuaian dan keakuratan laporan.', '2026-01-22 17:57:00', 'Selesai'),
(89, 12, '2026-01-23 08:30:00', 'Pengisian Tinta Hitam Printer Unit TU', 'Melakukan pengisian tinta hitam pada printer milik Liza (TU) untuk mendukung kelancaran proses pencetakan dokumen.', 'Setelah pengisian tinta, dilakukan pengecekan dan printer dapat digunakan dengan normal.', '2026-01-23 08:50:00', 'Selesai'),
(90, 12, '2026-01-23 08:56:00', 'Penanganan Aplikasi Fingerprint (Absensi) Mengalami Hang', 'Menerima laporan dari Lala (Kepegawaian) bahwa aplikasi fingerprint (absensi) mengalami hang dan tidak dapat menarik data dari mesin fingerprint.\r\n\r\nTindakan yang Dilakukan :\r\n\r\n1.Membuka menu Run (Windows + R),\r\n\r\n2. Mengetik dan membuka services.msc,\r\n\r\n3. Mencari service mysql_FINAP,\r\n\r\n4. Melakukan restart service mysql_FINAP,\r\n\r\n5. Membuka kembali aplikasi fingerprint\r\n\r\n6. Mencoba ulang proses penarikan data dari mesin fingerprint', 'Setelah service MySQL di-restart, aplikasi fingerprint kembali normal dan berhasil menarik data absensi dari mesin fingerprint.', '2026-01-23 09:26:00', 'Selesai'),
(91, 12, '2026-01-23 09:59:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haj', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses penerbitan E-ICV untuk 10 pasien UMUM yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-01-23 11:58:00', 'Selesai'),
(92, 12, '2026-01-23 10:33:00', 'Penanganan Tampilan Layar Monitor Terbalik di Unit PONEK', 'Menerima laporan dari unit PONEK terkait tampilan layar komputer yang terbalik (rotate 180°) sehingga mengganggu penggunaan.', 'Melakukan perbaikan dengan menggunakan shortcut keyboard: Menekan Ctrl + Alt + Panah Atas , Hasil Tampilan layar monitor kembali normal dan komputer dapat digunakan seperti biasa.', '2026-01-23 10:50:00', 'Selesai'),
(93, 12, '2026-01-23 13:16:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang Komdik 1 dan R. komdik 2 pada hari Kamis, 23 Januari 2026', 'Rincian Kegiatan : 1. ⁠jam 14.00 - selesai, R. Komdik 1, Undangan Presentasi Alkes (9 orang), 2. ⁠jam 14.30 - 15.30 , R. Komdik 2, zoom pertemuan diseminasi satu sehat', '2026-01-23 13:30:00', 'Selesai'),
(94, 12, '2026-01-23 15:23:00', 'Penanganan Gangguan Jaringan di Poli Penyakit Dalam', 'Menerima laporan dari Poli Penyakit Dalam terkait jaringan komputer yang tidak dapat terhubung (tidak ada koneksi jaringan/internet).\r\nTindakan yang Dilakukan: Melakukan pengecekan fisik jaringan dengan cara melepas dan memasang kembali (re-plug) kabel LAN pada komputer terkait.', 'Setelah kabel LAN dilepas dan dipasang kembali, koneksi jaringan berfungsi normal dan komputer dapat digunakan kembali.', '2026-01-23 15:35:00', 'Selesai'),
(95, 12, '2026-01-26 08:30:00', 'Persiapan dan Operasional Agenda Rapat Pelatihan BHD Tahap 2', 'Melakukan persiapan agenda acara di aula untuk kegiatan Pelatihan IHT BHD, meliputi penyiapan perangkat dan sarana pendukung acara.', 'Selain persiapan, bertugas sebagai operator selama pelaksanaan kegiatan Pelatihan BHD untuk memastikan acara berjalan dengan lancar.', '2026-01-26 14:28:00', 'Selesai'),
(96, 12, '2026-01-24 09:05:00', 'Kegiatan Akreditasi – Pokja PMKP', 'Melaksanakan tugas akreditasi pada Pokja PMKP. Tindakan yang Dilakukan :\r\n1. Mencari dan mengumpulkan dokumen PPK (Panduan Praktik Klinis).\r\n2. Mencari dan mengumpulkan dokumen CP (Clinical Pathway) sesuai kebutuhan unit.\r\n3.Melakukan pengeditan dan penyesuaian dokumen agar sesuai dengan standar akreditasi yang berlaku.', 'Dokumen PPK dan CP berhasil dikumpulkan dan dilakukan pengeditan awal sebagai persiapan kebutuhan akreditasi PMKP lalu diserahkan ke dr taufiq untuk di periksa di tanggal 26', '2026-01-24 13:09:00', 'Pending'),
(97, 12, '2026-01-24 10:15:00', 'Menindaklanjuti kendala pada sound system di aula, di mana suara lagu atau musik tidak keluar ke speaker saat diputar.', '1. Melakukan pengecekan jalur audio dari perangkat pemutar ke speaker.\r\n2. Melakukan penyesuaian dan pengaturan ulang pada mixer (level volume, input, dan output).\r\n3. Memastikan koneksi kabel audio dan speaker terpasang dengan benar.', 'Sound system aula kembali berfungsi normal dan suara musik dapat keluar melalui speaker dengan baik.', '2026-01-24 11:56:00', 'Selesai'),
(98, 12, '2026-01-24 08:33:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haj', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses pengesahan E-ICV untuk 1 pasien UMUM yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-01-24 08:57:00', 'Selesai'),
(99, 12, '2026-01-27 09:00:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang Komdik 1 dan R. komdik 2 pada hari Senin, 27 Januari 2026', 'Rincian Kegiatan : 1. ⁠jam 09.00 - selesai, R. Komdik 1, Undangan Rapat Mingguan, 2. ⁠jam 09.30 - Selesai , R. Komdik 2, zoom monitoring dan evaluasi permasalahan Program Jaminan Kesehatan Nasional di Provinsi Kalimantan Selatan yang sudah dilaksanakan pada 3 s.d. 6 Desember', '2026-01-27 09:59:00', 'Selesai'),
(100, 12, '2026-01-27 12:41:00', 'Menindaklanjuti kendala pada display antrian tunggu Poli THT yang mengalami error jaringan dan tidak dapat menampilkan konten dengan normal.', 'Melakukan pengecekan koneksi jaringan pada perangkat display antrian Poli THT.\r\nDitemukan konflik IP Address (IP address bertabrakan) dengan jaringan Poli Kandungan BPJS.\r\nMelakukan penyesuaian dan pengaturan ulang IP Address melalui Mikrotik agar tidak terjadi konaflik jaringan.\r\nMelakukan pengujian ulang koneksi jaringan dan tampilan display.', 'Display antrian tunggu Poli THT kembali berfungsi normal dan jaringan tidak lagi mengalami konflik IP.', '2026-01-27 13:06:00', 'Selesai'),
(101, 12, '2026-01-27 10:47:00', 'Menindaklanjuti laporan dari Mia (Poli) terkait aplikasi MLite yang berjalan sangat lambat di Poli Kandungan BPJS.', 'Tindakan yang Dilakukan :\r\nMelakukan pengecekan kondisi aplikasi dan jaringan yang digunakan.\r\nDitemukan bahwa koneksi jaringan menggunakan WiFi “PAK RT” yang tidak stabil.\r\nMengalihkan koneksi jaringan ke WiFi Indoor Poli yang lebih stabil dan sesuai peruntukan.\r\nMelakukan pengujian ulang kecepatan dan akses aplikasi MLite.', 'Aplikasi MLite kembali berjalan normal dan responsif setelah menggunakan jaringan WiFi Indoor Poli.', '2026-01-27 11:00:00', 'Selesai'),
(102, 12, '2026-01-31 09:44:00', 'Pengisian Tinta Printer', 'Melakukan pengisian tinta printer di ruangan Customer Service (CS) lantai 5 (HARSA) untuk memastikan perangkat dapat digunakan kembali secara optimal dalam mendukung aktivitas pelayanan.', 'Pengisian tinta berhasil dilakukan dan printer dapat berfungsi dengan normal. Tidak ditemukan kendala selama proses pengisian.', '2026-01-31 09:58:00', 'Selesai'),
(103, 12, '2026-01-31 08:08:00', 'Kegiatan Pokja PMKP – Penilaian Clinical Pathway', 'Melaksanakan tugas sebagai anggota Pokja PMKP dalam kegiatan penilaian dan penelaahan Clinical Pathway sesuai dengan standar mutu dan keselamatan pasien yang berlaku.', 'Penilaian dilakukan terhadap kesesuaian Clinical Pathway dengan pedoman yang telah ditetapkan. Hasil penilaian akan digunakan sebagai bahan evaluasi dan perbaikan mutu pelayanan.', '2026-01-31 12:11:00', 'Dalam Proses / Berjalan'),
(104, 12, '2026-01-31 09:18:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haj', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses pengesahan E-ICV untuk 10 pasien UMUM dan pancar yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-01-31 10:21:00', 'Selesai'),
(105, 12, '2026-01-30 13:03:00', 'Kegiatan Pokja PMKP – Penilaian Clinical Pathway', 'Melaksanakan tugas sebagai anggota Pokja PMKP dalam kegiatan penilaian dan penelaahan Clinical Pathway sesuai dengan standar mutu dan keselamatan pasien yang berlaku.', 'Penilaian dilakukan terhadap kesesuaian Clinical Pathway dengan pedoman yang telah ditetapkan. Hasil penilaian akan digunakan sebagai bahan evaluasi dan perbaikan mutu pelayanan.', '2026-01-30 16:33:00', 'Dalam Proses / Berjalan'),
(106, 12, '2026-02-02 08:26:00', 'Kegiatan Pokja PMKP – Penilaian Clinical Pathway', 'Melaksanakan tugas sebagai anggota Pokja PMKP dalam kegiatan penilaian dan penelaahan Clinical Pathway sesuai dengan standar mutu dan keselamatan pasien yang berlaku.', 'Penilaian dilakukan terhadap kesesuaian Clinical Pathway dengan pedoman yang telah ditetapkan. Hasil penilaian akan digunakan sebagai bahan evaluasi dan perbaikan mutu pelayanan.', '2026-02-02 09:58:00', 'Dalam Proses / Berjalan'),
(107, 12, '2026-02-02 08:44:00', 'Pengisian Tinta Printer', 'Melakukan pengisian tinta printer di ruangan Tata Usaha (TU) lantai 4 (Liza) dan melakukan Cleaning Head pada printer untuk memastikan perangkat dapat digunakan kembali secara optimal dalam mendukung aktivitas pelayanan.', 'Pengisian tinta berhasil dilakukan dan printer dapat berfungsi dengan normal. Tidak ditemukan kendala selama proses pengisian.', '2026-02-02 09:05:00', 'Selesai'),
(108, 12, '2026-02-02 13:18:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses pengesahan E-ICV untuk 7 pasien UMUM dan pancar yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-02-02 14:17:00', 'Selesai');
INSERT INTO `tb_logbook` (`id_log`, `id_user`, `tanggal_log`, `judul_log`, `deskripsi_log`, `catatan_log`, `tanggal_selesai`, `status_log`) VALUES
(109, 12, '2026-02-02 10:00:00', 'Pembaruan 1 Data Pasien dan Pelaporan ke BKK Banjarmasin', 'Melakukan pembaruan data pada website SINKARKES dikarenakan terdapat kesalahan penginputan nama pasien jemaah, serta memastikan data telah diperbaiki sesuai dengan identitas yang benar.', 'Setelah dilakukan koreksi data, selanjutnya dilakukan pelaporan dan pengajuan perubahan data melalui WhatsApp kepada pihak BKK Banjarmasin sebagai tindak lanjut administrasi. Proses berjalan dengan baik.', '2026-02-02 11:59:00', 'Selesai'),
(110, 12, '2026-02-02 13:04:00', 'Penambahan Grafik pada Aplikasi Dokumentasi Akreditasi (APDA)', 'Melakukan penambahan dan pengaturan tampilan grafik pada website Aplikasi Dokumentasi Akreditasi (APDA) untuk meningkatkan visualisasi data dan kemudahan pemantauan informasi.', 'Grafik ditambahkan dan diuji pada hak akses user admin dan user pokja. Fitur berjalan dengan baik dan dapat ditampilkan sesuai dengan role masing-masing pengguna.', '2026-02-02 15:23:00', 'Dalam Proses / Berjalan'),
(111, 12, '2026-02-03 08:26:00', 'Penanganan Printer Tidak Dapat Mencetak – Reset Waste Ink Pad', 'Menerima laporan dari counter lantai 3 terkait printer yang tidak dapat melakukan proses pencetakan. Setelah dilakukan pengecekan, lampu indikator printer berkedip sebanyak dua kali secara terus-menerus dan pada komputer muncul pesan bahwa bantalan tinta (ink pad / waste ink pad) telah penuh.', 'Dilakukan proses reset waste ink pad pada printer sehingga perangkat dapat kembali digunakan secara normal. Setelah reset, printer berhasil melakukan proses pencetakan tanpa kendala.', '2026-02-03 09:35:00', 'Selesai'),
(112, 12, '2026-02-03 10:00:00', 'Troubleshooting Printer – Output Hitam Pudar (Unit Kecubung)', 'Menerima laporan dari unit Kecubung terkait permasalahan printer dengan gejala hasil cetak warna hitam pudar dan terputus-putus (broken print).', 'Dilakukan pengecekan kondisi printer dan uji cetak. Proses head cleaning dilakukan melalui utility printer namun hasil cetak belum normal. Selanjutnya dilakukan reset manual menggunakan tombol pada printer dan dilakukan jeda waktu (idle) beberapa saat. Setelah proses reset selesai, dilakukan uji cetak ulang dan hasil cetak kembali normal.', '2026-02-03 10:50:00', 'Selesai'),
(113, 12, '2026-02-03 11:00:00', 'Instalasi SIMRS di Komputer Poli Paru', 'Menerima permintaan dari Poli Paru untuk melakukan instalasi aplikasi SIMRS pada komputer poli guna mendukung kegiatan visite apoteker, dikarenakan keterbatasan perangkat di unit apotek.', 'Dilakukan proses instalasi dan konfigurasi aplikasi SIMRS pada komputer Poli Paru. Setelah instalasi selesai, dilakukan pengujian akses dan fungsi aplikasi untuk memastikan sistem dapat digunakan dengan normal dalam mendukung proses pelayanan.', '2026-02-03 11:25:00', 'Selesai'),
(114, 12, '2026-02-03 13:09:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses pengesahan E-ICV untuk 7 pasien UMUM dan pancar yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-02-03 14:19:00', 'Selesai'),
(115, 12, '2026-02-03 12:01:00', 'Troubleshooting Jaringan LAN Counter Lantai 3', 'Menerima laporan gangguan jaringan di Counter Lantai 3 dimana koneksi tidak dapat digunakan.', 'Dilakukan pengecekan fisik jaringan dan ditemukan kabel LAN dalam kondisi longgar pada switch hub. Kabel LAN dipasang kembali dengan benar dan dilakukan penataan ulang posisi switch hub ke lokasi yang lebih aman agar tidak mudah tersentuh atau terinjak kaki. Setelah perbaikan, koneksi jaringan kembali normal.', '2026-02-03 12:20:00', 'Selesai'),
(116, 12, '2026-02-03 14:54:00', 'Instalasi SIMRS Mapping Obat di Komputer Farmasi ralan', 'Menerima permintaan dari Farmasi ralan untuk melakukan instalasi aplikasi SIMRS untuk pada komputer farmasi guna mendukung kegiatan mapping obat, dikarenakan keterbatasan perangkat di unit apotek.', 'Dilakukan proses instalasi dan konfigurasi aplikasi SIMRS pada komputer Farmasi ralan. Setelah instalasi selesai, dilakukan pengujian akses dan fungsi aplikasi untuk memastikan sistem dapat digunakan dengan normal dalam mendukung proses pelayanan.', '2026-02-03 15:10:00', 'Selesai'),
(117, 12, '2026-02-03 08:47:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang Komdik 1 pada hari Selasa, 03 Februari 2026', 'Rincian Kegiatan : 1. ⁠jam 09.00 - selesai, R. Komdik 1, Undangan Rapat Mingguan', '2026-02-03 09:05:00', 'Selesai'),
(118, 12, '2026-02-04 09:20:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di R. komdik 2 pada hari Rabu, 04 Februari 2026', 'Rincian Kegiatan :\r\n1. R. Komdik 2, zoom Validasi Data Pencatatan dan Pelaporan Program Malaria Tahun 2025', '2026-02-04 09:41:00', 'Selesai'),
(119, 12, '2026-02-04 14:02:00', 'Troubleshooting Printer Label TSC Mencetak Terus-Menerus (IGD)', 'Menerima laporan dari IGD terkait printer label TSC yang mengalami gangguan berupa proses pencetakan berlangsung terus-menerus tanpa perintah cetak, dengan jumlah cetakan mencapai lebih dari 20 lembar.', 'Dilakukan pengecekan pada history print di komputer server dan komputer client (printer sharing) untuk memastikan tidak terdapat antrian cetak aktif. Setelah dipastikan tidak ada proses cetak yang berjalan, dilakukan tindakan power reset dengan melepas dan memasang kembali kabel daya (power) pada printer label TSC. Setelah dilakukan reset, printer kembali berfungsi normal dan tidak lagi mencetak secara terus-menerus.', '2026-02-04 15:00:00', 'Selesai'),
(120, 12, '2026-02-04 10:06:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses pengesahan E-ICV untuk 4 pasien UMUM  yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-02-04 11:00:00', 'Selesai'),
(121, 12, '2026-02-05 15:35:00', 'Penanganan Aplikasi Fingerprint (Absensi) Mengalami Hang', 'Menerima laporan dari Elya(Kepegawaian) bahwa aplikasi fingerprint (absensi) mengalami hang dan tidak dapat menarik data dari mesin fingerprint. Tindakan yang Dilakukan : 1.Membuka menu Run (Windows + R), 2. Mengetik dan membuka services.msc, 3. Mencari service mysql_FINAP, 4. Melakukan restart service mysql_FINAP, 5. Membuka kembali aplikasi fingerprint 6. Mencoba ulang proses penarikan data dari mesin fingerprint', 'Setelah service MySQL di-restart, aplikasi fingerprint kembali normal dan berhasil menarik data absensi dari mesin fingerprint.', '2026-02-05 16:00:00', 'Selesai'),
(122, 12, '2026-02-05 15:04:00', 'Troubleshooting Jaringan LAN Komputer Mas Malik Manajemen', 'Menerima laporan gangguan jaringan di Komputer Mas Malik dimana koneksi tidak dapat digunakan.', 'Dilakukan pengecekan di Mikrotik apakah IP bertabaran atau sama dengan komputer lain dan hasilnya tidak ada dan lanjut ke pengecekan fisik jaringan yaitu di swich nya dan ternyata kabel lan nya sudah tidak memungkinkan dan dilakukan pergantian kabel LAN yang baru', '2026-02-05 03:30:00', 'Selesai'),
(123, 12, '2026-02-05 14:00:00', 'Rapat Pokja ', 'Membahas Pokja PMKP', 'Membahas Pokja PMKP', '2026-02-05 15:00:00', 'Selesai'),
(124, 12, '2026-02-07 11:09:00', 'Penanganan Printer Tidak Dapat Mencetak – Paper Jam (NICU)', 'Menerima laporan dari unit NICU terkait printer di counter lantai 2 yang tidak dapat melakukan proses pencetakan.', 'Dilakukan pengecekan pada printer di counter lantai 2 dan ditemukan kondisi paper jam (kertas menyangkut) saat proses cetak. Kertas yang menyangkut dilepaskan secara perlahan untuk menghindari kerusakan komponen printer. Setelah itu dilakukan restart printer (power off dan on). Setelah tindakan tersebut, printer kembali berfungsi normal.', '2026-02-07 12:16:00', 'Selesai'),
(125, 12, '2026-02-07 09:01:00', 'Rapat Unit IT', 'Rapat Unit IT', 'Rapat Unit IT', '2026-02-07 11:55:00', 'Selesai'),
(126, 12, '2026-02-05 11:02:00', 'Pemindahan Akses Mlite Perawat ke Unit Kecubung', 'Menerima permintaan dari unit Kecubung terkait pemindahan akses aplikasi Mlite untuk perawat yang sebelumnya terdaftar di unit NICU dan Yakut C.', 'Dilakukan proses penyesuaian dan pemindahan hak akses pengguna Mlite perawat dari unit NICU dan Yakut C ke unit Kecubung sesuai dengan permintaan. Setelah perubahan dilakukan, dilakukan pengecekan akses untuk memastikan akun dapat digunakan dengan normal di unit Kecubung.', '2026-02-05 11:50:00', 'Selesai'),
(127, 12, '2026-02-05 00:55:00', 'Penambahan Hak Akses SIMRS – Data Hais dan Billing Pembayaran', 'Melakukan penambahan hak akses pengguna pada sistem SIMRS untuk modul Data Hais dan Billing Pembayaran sesuai dengan kebutuhan operasional.', 'Dilakukan konfigurasi dan penyesuaian hak akses SIMRS untuk pengguna Gebrilia, sehingga dapat mengakses menu Data Hais dan Billing Pembayaran. Setelah pengaturan selesai, dilakukan pengecekan untuk memastikan akses berjalan dengan normal.', '2026-02-05 12:30:00', 'Selesai'),
(128, 12, '2026-02-05 08:30:00', 'Perbaikan Microsoft Office – Konflik Versi (Farmasi Ralan)', 'Menerima laporan dari Farmasi Rawat Jalan terkait permasalahan saat membuka file Microsoft Excel, dimana file terbuka menggunakan Office 2007 meskipun telah terpasang versi Office lain.', 'Dilakukan pengecekan pada komputer dan ditemukan terdapat dua versi Microsoft Office yang terinstal dan keduanya tidak menggunakan versi penuh (full version), sehingga menyebabkan konflik aplikasi. Sebagai langkah perbaikan, dilakukan uninstall kedua versi Office tersebut. Selanjutnya dilakukan instalasi Microsoft Office 2019 versi penuh (full version). Setelah instalasi selesai, dilakukan pengujian dan file Excel dapat dibuka dengan normal tanpa kendala.', '2026-02-05 09:54:00', 'Selesai'),
(129, 12, '2026-02-09 08:30:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di R. komdik 2 pada hari Senin, 09 Februari 2026', 'Rincian Kegiatan : 1 ⁠jam 09.00 - selesai, R.Komdik 1, Rakor Laporan Kunjungan Ranap Ralan (6 orang),\r\n2 ⁠jam 10.00 - selesai, R. Komdik 1, Rakor Evaluasi Hasil Google Review (10 orang),\r\n3. ⁠jam 11.00 - selesai, R. Komdik 1, Rakor Pelebaran Konter Lt.3 (8 orang),', '2026-02-09 11:00:00', 'Selesai'),
(130, 12, '2026-02-09 08:30:00', 'Operasional Agenda Rapat Pelatihan BHL', 'Melakukan persiapan agenda acara di aula untuk kegiatan Pelatihan IHT BHL, meliputi penyiapan perangkat dan sarana pendukung acara.', 'Selain persiapan, bertugas sebagai operator selama pelaksanaan kegiatan Pelatihan BHL untuk memastikan acara berjalan dengan lancar.', '2026-02-09 10:40:00', 'Selesai'),
(131, 12, '2026-02-09 13:14:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses pengesahan E-ICV untuk 15 pasien UMUM yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-02-09 15:28:00', 'Selesai'),
(132, 12, '2026-02-09 11:19:00', 'Penanganan Permasalahan SD Card Tidak Dapat Diakses (Unit Pemasaran)', 'Menerima laporan dari Adri (Pemasaran) terkait SD Card yang sebelumnya digunakan sebagai media penyimpanan pada kamera Canon, namun saat dipasang ke komputer data tidak dapat dibuka dan sistem selalu meminta untuk dilakukan format.', 'Dilakukan pengecekan awal terhadap SD Card dan teridentifikasi kemungkinan kerusakan pada sistem file (file system corruption). Dilakukan upaya pemeriksaan akses data menggunakan komputer tanpa melakukan format untuk menghindari kehilangan data. Pengguna diberikan edukasi agar tidak melakukan format sebelum proses recovery data dilakukan. Selanjutnya direkomendasikan proses data recovery menggunakan perangkat lunak khusus pemulihan data SD Card.', '2026-02-09 14:27:00', 'Selesai'),
(133, 12, '2026-02-10 09:40:00', 'Update Microsoft Office Versi 2019', 'Menerima laporan dari Ponek terkait permasalahan saat membuka file Microsoft word, dimana file tersebut  saat membuka hasil nya hancur atau tidak rapi, setelah di cek disana menggunakan Office 2007 jadi dilakukan unistall versi lama dan install versi tinggi yaitu 2019', 'permasalahan selesai', '2026-02-10 10:27:00', 'Selesai'),
(134, 12, '2026-02-10 11:36:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses pengesahan E-ICV untuk 2 pasien UMUM yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-02-10 23:56:00', 'Selesai'),
(135, 12, '2026-02-10 10:52:00', 'Penggantian Switch Hub Jaringan – Counter Perawat Lantai 3', 'Melakukan penanganan gangguan jaringan di counter perawat lantai 3 yang sering mengalami koneksi putus–nyambung.', 'Setelah dilakukan pengecekan, diketahui switch hub di lokasi tersebut sudah mengalami penurunan fungsi (indikasi kerusakan) sehingga menyebabkan jaringan tidak stabil. Dilakukan penukaran/penggantian switch hub dengan perangkat yang layak pakai. Setelah penggantian, dilakukan pengujian koneksi dan jaringan kembali stabil serta dapat digunakan dengan normal.', '2026-02-10 11:18:00', 'Selesai'),
(136, 12, '2026-02-10 08:30:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di R. komdik 2, R.Aula, R.Komdik 1. pada hari Selasa, 10 Februari 2026', 'Rincian Kegiatan : 1 jam 09.30  - selesai, R. Komdik 1, Rapat Mingguan ,\r\n2 jam 10.00 - selesai, R.Komdik 2, Zoom Kemenkes (2 orang) ⁠,\r\n3 ⁠ jam 11.00 (setelah rapat bulanan) R. Komdik 1, Rapat IPAL (8 orang),\r\n4 ⁠jam 13.00- selesai, R. Aula, Rapat Pokja MFK,\r\n5 ⁠jam 14.00 - selesai, R. Aula, Rapat Bulanan,\r\n6 jam setelah rapat bulanan, R. Aula, Sosialisasi Risk Register Unit,', '2026-02-10 14:00:00', 'Selesai'),
(137, 12, '2026-02-16 08:21:00', 'Penarikan Kabel LAN untuk Koneksi Jaringan Komputer Manajemen', 'Menindaklanjuti permintaan dari Mas Adi (Manajemen) untuk penambahan koneksi jaringan menggunakan kabel LAN.\r\n\r\nDilakukan penarikan kabel LAN dari switch hub yang berada di komputer Mas Malik menuju komputer Mas Adi Manajemen, kemudian dilakukan pengecekan koneksi jaringan.', 'Koneksi jaringan LAN pada komputer Mas Adi Manajemen berfungsi normal setelah dilakukan penarikan kabel dan pengujian koneksi.', '2026-02-16 09:03:00', 'Selesai'),
(138, 12, '2026-02-16 11:20:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses pengesahan E-ICV untuk 15 pasien UMUM dan Pancar yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-02-16 14:29:00', 'Selesai'),
(139, 12, '2026-02-16 13:29:00', 'Penyesuaian dan Pengembangan Fitur Data Barang pada Website IT-RSPI', 'Melakukan penyesuaian dan pengembangan fitur pada website IT-RSPI khususnya pada modul Data Barang. Pengembangan yang dilakukan meliputi penambahan fitur:\r\n-Data Barang,\r\n-Data Pemindahan Barang,\r\n-Data Perbaikan Barang,\r\n-Data Barang Rusak,\r\n-Grafik monitoring data barang,', 'Penambahan fitur bertujuan untuk mempermudah proses pendataan, monitoring, serta pelaporan kondisi dan pergerakan barang secara terstruktur dan terintegrasi pada sistem IT-RSPI.', '2026-02-16 16:47:00', 'Dalam Proses / Berjalan'),
(140, 12, '2026-02-16 10:24:00', 'Pemasangan NYK 2 Port USB Switch Printer di Unit TU', 'Melakukan pemasangan NYK 2 Port USB Switch Printer di Unit TU dengan tujuan untuk mempermudah pengalihan penggunaan printer antar pengguna.', 'Konfigurasi penggunaan printer sebagai berikut:\r\n1. Liza dan Mas Sam menggunakan printer melalui USB Switch untuk kebutuhan cetak bergantian.\r\n2. Mas Yudi menggunakan metode sharing printer melalui komputer Mas Sam.\r\nPemasangan dan pengaturan berjalan normal dan printer dapat digunakan sesuai kebutuhan masing-masing user.', '2026-02-16 11:43:00', 'Selesai'),
(141, 12, '2026-02-18 14:23:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses pengesahan E-ICV untuk 15 pasien UMUM dan pancar yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-02-18 16:53:00', 'Selesai'),
(142, 12, '2026-02-18 10:47:00', 'Penarikan dan Ekspor Data Pasien dengan SEP ke Excel', 'Melakukan penarikan data pasien yang telah memiliki SEP BPJS pada rentang tanggal 1 sampai 15 menggunakan SQLyog.', 'Data hasil penarikan kemudian dilakukan export ke format Excel untuk kebutuhan pelaporan dan pengolahan data lebih lanjut.', '2026-02-18 11:08:00', 'Selesai'),
(143, 12, '2026-02-18 08:01:00', 'Penanganan Kendala Suara pada Display Panggilan Antrian Loket Pendaftaran (FO)', 'Menindaklanjuti laporan dari loket pendaftaran/FO terkait display panggilan antrian tidak mengeluarkan suara saat pemanggilan nomor antrian.', 'Penanganan dilakukan dengan:\r\n1. Melepas dan memasang kembali kabel HDMI,\r\n2.Melakukan restart PC\r\n3. Memasang kembali kabel HDMI setelah restart\r\nSetelah dilakukan tindakan tersebut, suara panggilan antrian kembali berfungsi normal.', '2026-02-18 08:30:00', 'Selesai'),
(144, 12, '2026-02-19 09:00:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di R. komdik 1 pada hari Selasa, 19 Februari 2026', 'Rincian Kegiatan : 1 jam 09.30 - selesai, R. Komdik 1, Rapat Mingguan ', '2026-02-19 09:17:00', 'Selesai'),
(145, 12, '2026-02-20 08:30:00', 'Persiapan Zoom Meeting di Ruang Komdik 2', 'Melakukan persiapan dan pengaturan aplikasi Zoom di ruang Komdik 2 untuk mendukung kegiatan rapat atau meeting daring.', 'Zoom berhasil disiapkan dan dapat digunakan dengan normal.', '2026-02-20 08:13:00', 'Selesai'),
(146, 12, '2026-02-20 09:37:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses pengesahan E-ICV untuk 3 pasien UMUM dan pancar yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-02-20 10:03:00', 'Selesai'),
(147, 12, '2026-02-20 13:56:00', 'Penanganan Kendala Printer di Unit Yakut C', 'Menindaklanjuti laporan kendala printer di unit Yakut C dengan melakukan pengecekan dan perbaikan ternyata terdapat kertas menyangkut setelah di coba di atasi printer normal kembali', 'Printer berhasil diperbaiki dan dapat digunakan kembali dengan normal.', '2026-02-20 14:30:00', 'Selesai'),
(148, 12, '2026-02-20 10:42:00', 'Penanganan Kendala Update WhatsApp FO Rawat Jalan', 'Menangani permasalahan WhatsApp di FO Rawat Jalan yang tidak dapat melakukan pembaruan melalui Play Store.', 'Setelah dilakukan penanganan, aplikasi WhatsApp dapat diperbarui dan digunakan kembali.', '2026-02-20 11:16:00', 'Selesai'),
(149, 12, '2026-02-20 13:45:00', 'Edukasi Penggunaan Alat Absensi Fingers', 'Memberikan edukasi kepada Elya terkait penggunaan alat absensi Fingers.', 'Edukasi meliputi cara menambahkan data sidik jari dan telapak tangan pada mesin absensi.', '2026-02-20 14:20:00', 'Selesai'),
(150, 12, '2026-02-20 11:46:00', 'Edukasi Network Sharing PC di Unit TU', 'Memberikan edukasi kepada Liza TU terkait penggunaan network sharing antar PC.', 'User telah memahami cara membuka dan mengakses network sharing pada PC masing-masing.', '2026-02-20 11:58:00', 'Selesai'),
(151, 12, '2026-02-21 09:00:00', 'Penarikan Data Absensi Finger dan Perbaikan Sementara Aplikasi Fingersport', 'Melakukan penarikan data absensi pegawai yang didaftarkan melalui alat finger pada malam hari ke dalam aplikasi Fingersport. Selain itu, dilakukan pengecekan dan perbaikan sementara terhadap aplikasi Fingersport yang mengalami kendala agar dapat kembali digunakan.', '1. Data absensi berhasil ditarik dari alat finger ke aplikasi Fingersport,\r\n2. Dilakukan perbaikan sementara pada aplikasi Fingersport yang mengalami gangguan,\r\n3. Aplikasi dapat digunakan kembali, namun perlu pemantauan lanjutan untuk memastikan stabilitas sistem,', '2026-02-21 09:54:00', 'Selesai'),
(152, 12, '2026-02-21 11:32:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses pengesahan E-ICV untuk 2 pasien UMUM yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-02-21 11:55:00', 'Selesai'),
(153, 12, '2026-02-21 10:01:00', 'Restart / Stop Paksa VM 101 (Win-Ten) pada Server Proxmox', 'Melakukan tindakan restart dan stop paksa terhadap virtual machine VM 101 (Win-Ten) pada server Proxmox karena mengalami kondisi hang dan tidak merespons perintah normal.', '1. VM 101 (Win-Ten) mengalami hang dan tidak dapat dilakukan restart maupun shutdown secara normal,\r\n2. Setelah dilakukan pengecekan pada menu Summary, penggunaan CPU terpantau hampir mencapai 100%,\r\n3. Dilakukan stop paksa (force stop) untuk menghentikan VM,\r\n4. Tindakan dilakukan guna mencegah gangguan terhadap performa server dan VM lainnya', '2026-02-21 10:50:00', 'Selesai'),
(154, 12, '2026-02-21 10:54:00', 'Penanganan Gangguan Keyboard di Counter Perawat Lantai 3', 'Menindaklanjuti laporan dari counter perawat lantai 3 terkait keyboard pada perangkat tablet (tab) yang mengalami kendala, khususnya pada tombol spasi yang tidak berfungsi.', '1. Keyboard lama mengalami kerusakan pada tombol spasi sehingga tidak dapat digunakan secara normal,\r\n2. Dilakukan penggantian perangkat dengan keyboard Bluetooth baru,\r\n3. Melakukan koneksi ulang (pairing) antara tablet dan keyboard Bluetooth,\r\n4.Pengujian dilakukan dan keyboard berfungsi dengan normal setelah pergantian,', '2026-02-21 11:40:00', 'Selesai'),
(155, 12, '2026-02-23 09:14:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses pengesahan E-ICV untuk 1 pasien UMUM yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-02-23 22:12:00', 'Selesai'),
(156, 12, '2026-02-23 10:34:00', 'Penanganan Permasalahan Jaringan Internet di Unit Manajemen', 'Menindaklanjuti laporan dari Mbak Nurma (Manajemen) terkait jaringan internet yang tidak dapat digunakan pada komputer kerja.', 'Dilakukan pengecekan koneksi jaringan pada komputer dan switch hub. Ditemukan kabel LAN belum terpasang dengan sempurna baik di sisi komputer maupun di switch hub. Selanjutnya dilakukan lepas-pasang dan pemasangan ulang kabel LAN hingga terhubung dengan baik. Setelah dilakukan perbaikan, koneksi jaringan internet kembali normal dan dapat digunakan.', '2026-02-23 10:00:00', 'Selesai'),
(157, 12, '2026-02-14 12:03:00', 'Penataan Ulang Posisi Meja Kerja dan Perangkat IT', 'Melakukan penataan ulang posisi meja kerja milik Mas Malik dan Mas Adi sesuai kebutuhan operasional.', 'Dilakukan pengaturan ulang posisi meja kerja yang berdampak pada penyesuaian tata letak perangkat IT, meliputi komputer, printer, serta jaringan. Proses pekerjaan mencakup pemindahan switch hub, pengaturan ulang jalur kabel LAN, dan penyesuaian koneksi jaringan agar tetap rapi dan berfungsi dengan baik. Setelah dilakukan penyesuaian, seluruh perangkat dapat beroperasi dengan normal.', '2026-02-14 14:30:00', 'Selesai'),
(158, 12, '2026-02-23 11:17:00', 'Penanganan Kendala Jaringan WiFi di Ruangan Dokter', 'Menindaklanjuti laporan dari dr. Diana terkait jaringan WiFi pada komputer yang tidak dapat digunakan.', 'Dilakukan pengecekan koneksi jaringan WiFi pada komputer dr. Diana. Selanjutnya dilakukan proses disconnect dan reconnect jaringan, sehingga koneksi dapat digunakan kembali dengan normal. Selain itu, dilakukan penyesuaian batas pemakaian (limit) jaringan oleh Mas Hadi. Untuk solusi jangka panjang, direncanakan penarikan kabel LAN langsung dari server ke ruangan dr. Diana guna meningkatkan kestabilan koneksi.', '2026-02-23 11:57:00', 'Dalam Proses / Berjalan'),
(159, 12, '2026-02-24 09:07:00', 'Penataan Ulang Posisi Meja Kerja dan Perangkat IT', 'Melakukan penataan ulang posisi meja kerja milik Counter Lantai 3 sesuai kebutuhan operasional.', 'Dilakukan pengaturan ulang posisi meja kerja yang berdampak pada penyesuaian tata letak perangkat IT, meliputi komputer, printer, serta jaringan. Proses pekerjaan mencakup pemindahan switch hub, pengaturan ulang jalur kabel LAN, dan penyesuaian koneksi jaringan agar tetap rapi dan berfungsi dengan baik. Setelah dilakukan penyesuaian, seluruh perangkat dapat beroperasi dengan normal.', '2026-02-24 12:58:00', 'Selesai'),
(160, 12, '2026-02-24 09:00:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di R. komdik 1 pada hari Selasa, 19 Februari 2026', 'Rincian Kegiatan : 1. Rapat mingguan , 2. Rapat Penentuan On Call Radiologi, 3. Rapat Pembentukan Tim Code Blue,', '2026-02-24 14:00:00', 'Selesai'),
(161, 12, '2026-02-27 08:30:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di R. komdik 1 pada hari Jumat, 27Februari 2026', 'Rincian Kegiatan : 1. Rapat koordinasi radiologi', '2026-02-27 08:50:00', 'Selesai'),
(162, 12, '2026-02-26 08:53:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di R. komdik 1 pada hari Kamis, 26 Februari 2026', 'Rincian Kegiatan : 1. jam 09.00- selesai, R. Komdik 1, Rapat monev pendaftaran px ODC ,\r\n2. jam 14.00 - selesai, R.Komdik 1, Zoom pertemuan rs mitra KARS,', '2026-02-26 13:36:00', 'Selesai'),
(163, 12, '2026-02-26 09:00:00', 'Penambahan Fitur Cuti pada Website IT-RSPI', 'Melakukan pengembangan dan penambahan fitur Cuti khusus untuk karyawan IT pada website IT-RSPI guna mendukung pengelolaan data cuti pegawai IT secara terstruktur.', 'Dilakukan perancangan dan pembuatan fitur cuti yang mencakup pembuatan struktur tabel database serta pengaturan alur proses pengajuan dan pengelolaan data cuti. Fitur berhasil ditambahkan dan siap untuk dilakukan pengujian serta pengembangan lanjutan sesuai kebutuhan.', '2026-02-26 12:02:00', 'Selesai'),
(164, 12, '2026-02-27 08:57:00', 'Troubleshooting Printer – Output  Clip Paper dalam printer(Unit TU)', 'Menerima laporan dari unit TU terkait permasalahan printer dengan gejala hasil cetak selalu menyangkut, setelah di cek ternyata terdapat Clip paper menyangkut di rol keluar nya kertas', 'Dilakukan pengambilkan Clip kertas dengan cara printer di buka dan rol di naikan sedikit guna mengeluarkan clip nya setelah berhasul selanjutnya dilakukan uji cetak ulang dan hasil cetak kembali normal.', '2026-02-27 09:25:00', 'Selesai'),
(165, 12, '2026-02-27 09:25:00', 'Troubleshooting Printer – Output Tidak bisa memprint (Unit Manajemen)', 'Menerima laporan dari unit manajemen yaitu diana terkait permasalahan printer dengan kendala tidak dapat memprint', 'Dilakukan pengecekan kondisi printermemang mati setelah di nyalakan dan dilakukan uji cetak ulang dan hasil cetak kembali normal.', '2026-02-27 09:30:00', 'Selesai'),
(166, 12, '2026-02-26 09:00:00', 'Penanganan Aplikasi Fingerprint (Absensi) Mengalami Hang', 'Menerima laporan dari Elya(Kepegawaian) bahwa aplikasi fingerprint (absensi) mengalami hang dan tidak dapat menarik data dari mesin fingerprint. Tindakan yang Dilakukan : 1.Membuka menu Run (Windows + R), 2. Mengetik dan membuka services.msc, 3. Mencari service mysql_FINAP, 4. Melakukan restart service mysql_FINAP, 5. Membuka kembali aplikasi fingerprint 6. Mencoba ulang proses penarikan data dari mesin fingerprint', 'Setelah service MySQL di-restart, aplikasi fingerprint kembali normal dan berhasil menarik data absensi dari mesin fingerprint, Selain itu juga saya membuatkan Otomatis Stop dan Start service mysql_FINAP di destop semacam shortcut/file.bat gitu jadi ketika di buka dengan Run As Adminintrator service akan membuka cmd dan otomatis stop dan start guna meringankan hang ', '2026-02-26 10:15:00', 'Selesai'),
(167, 12, '2026-02-13 08:25:00', 'Penataan Ulang Posisi Meja Kerja dan Perangkat IT di Unit Manajemen', 'Melakukan penataan ulang posisi meja kerja milik Bu nia, Elya dan Mas Akbar sesuai kebutuhan operasional.', 'Dilakukan pengaturan ulang posisi meja kerja yang berdampak pada penyesuaian tata letak perangkat IT, meliputi komputer, printer, serta jaringan. Proses pekerjaan mencakup pemindahan switch hub, pengaturan ulang jalur kabel LAN, dan penyesuaian koneksi jaringan agar tetap rapi dan berfungsi dengan baik. Setelah dilakukan penyesuaian, seluruh perangkat dapat beroperasi dengan normal.', '2026-02-13 12:15:00', 'Selesai'),
(168, 12, '2026-02-27 14:55:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses pengesahan E-ICV untuk 2 pasien UMUM yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-02-27 15:08:00', 'Selesai'),
(169, 12, '2026-03-02 13:08:00', 'Troubleshooting Alat Finger – Output Tidak bisa melakukan sidik jari(Unit HD)', 'Menerima laporan dari unit HD yaitu terkait Finger yang biasa di gunakan untuk sidik jari pasien di Aplikasi BPJS dengan kendala tidak dapat merespon', 'Dilakukan pengecekan kondisi alat finger memang merespon saat melakukan sidik jari lalu di lakukan restart komputer dan setelah di nyalakan kembali dan dilakukan ujicoba dan finger sudah merespon yang artinya kembali normal.', '2026-03-02 13:58:00', 'Selesai'),
(170, 12, '2026-03-02 08:29:00', 'Rekap dan Pencocokan Data Stok Dokumen dan Vaksin Haji dan Umroh', 'Melakukan rekap data secara keseluruhan serta mencocokkan stok dokumen dan vaksin di farmasi sebagai bahan penyusunan laporan vaksin.', 'Data dokumen dan stok vaksin sudah dicocokkan dengan data yang tersedia untuk memastikan kesesuaian dan keakuratan laporan. dan untuk laporan sudah selesai untuk bulan Februari lalu file nya diserahkan ke Adi ahmad Manajemen', '2026-03-02 09:56:00', 'Selesai'),
(171, 12, '2026-03-02 10:00:00', 'Menambahkan Fitur Uplod bukti struk/Kwintansi', 'Melakukan penambahan fitur di website IT-RSPI yaitu mengupload bukti struk/kwintansi di data perbaikan jika tindakan memilih Service Luar', 'Selesai dan sudah Deploy ke server 192.168.1.108', '2026-03-02 11:35:00', 'Selesai'),
(172, 12, '2026-03-02 10:33:00', 'Penanganan Gangguan Koneksi Display Antrian FO Rawat Jalan', 'Menindaklanjuti laporan gangguan pada display antrian panggil di FO Rawat Jalan, di mana tampilannya muncul tulisan-tulisan atau pesan-pesan dan suara panggilan nomor antrian tidak ada informasi meskipun komputer dalam kondisi menyala', 'Dilakukan pengecekan yaitu komputer tidak mendapatkan jaringan internet dan dilakukan lepas pasang kabel lan di komputer dan sehingga display antrian dapat berfungsi kembali.', '2026-03-02 11:00:00', 'Selesai'),
(173, 12, '2026-03-02 14:08:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses pengesahan E-ICV untuk 10 pasien UMUM dan pancar yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-03-02 15:48:00', 'Selesai'),
(174, 12, '2026-03-02 15:20:00', 'Penanganan Gangguan Network PC Unit TU (Liza)', 'Menindaklanjuti laporan dari Liza TU terkait Network PC tidak dapat dibuka/diakses pada komputernya.', 'tolong sederhanakan kata-kata ini dong\r\nSetelah dilakukan pengecekan, diketahui bahwa IP Address PC berubah menjadi 192.168.1.49 dan belum menggunakan IP statis, sehingga menyebabkan gangguan akses network. Tindakan yang dilakukan yaitu melakukan pengaturan IP statis melalui Winbox Mikrotik agar alamat IP tetap dan stabil. Selain itu, dilakukan pin menu Network ke Start Menu pada PC untuk mempermudah akses Network ke depannya. Setelah penyesuaian dilakukan, koneksi network kembali normal dan dapat digunakan dengan baik.', '2026-03-02 15:39:00', 'Selesai'),
(175, 12, '2026-03-02 12:15:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di R. komdik 1 pada hari Senin, 02 Maret 2026', 'Rincian Kegiatan : 1. Rapat pertemuan dengan Pondok Al Jauhar Martapura', '2026-03-02 12:25:00', 'Selesai'),
(176, 12, '2026-03-03 08:30:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di R. komdik 1 dan 2 pada hari Selasa, 03 Maret 2026', 'Rincian Kegiatan : 1 jam 09.30- selesai, R. Komdik 1,  Rapat Mingguan (23 orang),\r\n2. ⁠jam 10.00 - selesai, R.Komdik 1, Rapat CAC ,\r\n3. ⁠jam 10.00 - selesai, R. Komdik 2, Zoom Dinkes Prov (3 orang),', '2026-03-03 10:00:00', 'Selesai'),
(177, 12, '2026-03-03 09:35:00', 'Penanganan Login Telegram FO Ralan', 'Menangani kendala login Telegram FO Ralan yang memerlukan verifikasi dua langkah (2FA).', 'Dilakukan koordinasi dengan unit RM untuk pertukaran kode verifikasi. Akun Telegram berhasil login di handphone FO Ralan, dan Telegram Web diaktifkan pada perangkat unit RM.', '2026-03-03 10:34:00', 'Selesai'),
(178, 12, '2026-03-03 10:30:00', 'Penataan Ulang Posisi Meja Kerja dan Perangkat IT di unit poli Anak', 'Melakukan penataan ulang posisi meja kerja dokter poli anak dan perawat sesuai kebutuhan operasional di ruangan baru', 'Dilakukan pengaturan ulang posisi meja kerja yang berdampak pada penyesuaian tata letak perangkat IT, meliputi komputer, printer, serta jaringan. Proses pekerjaan mencakup pengaturan ulang jalur kabel LAN, dan penyesuaian koneksi jaringan agar tetap rapi dan berfungsi dengan baik. Setelah dilakukan penyesuaian, seluruh perangkat dapat beroperasi dengan normal.', '2026-03-03 12:55:00', 'Selesai'),
(179, 12, '2026-03-03 15:30:00', 'Penarikan Data Absensi Kepegawaian', 'Menindaklanjuti permintaan dari Elya Kepegawaian untuk penarikan data absensi.', 'Data absensi periode tanggal 1–3 berhasil ditarik dan diekspor ke format Excel sesuai permintaan.', '2026-03-03 16:28:00', 'Selesai'),
(180, 12, '2026-03-03 14:04:00', 'Update Patch SIMRS Khanza Terkait IDRG di Unit Manajemen', 'Melakukan update patch SIMRS Khanza terkait IDRG pada unit Manajemen (Elita) guna memastikan sistem berjalan sesuai dengan ketentuan dan kebutuhan operasional.', 'Proses update patch berjalan dengan lancar dan sistem dapat digunakan tanpa kendala.', '2026-03-03 02:34:00', 'Selesai'),
(181, 12, '2026-03-03 15:44:00', 'Upgrade RAM Komputer Kepegawaian', 'Melakukan penambahan RAM pada komputer Elya Kepegawaian.', 'RAM ditambahkan sebesar 8 GB sehingga total kapasitas RAM menjadi 12 GB dan komputer berjalan normal.', '2026-03-03 16:02:00', 'Selesai'),
(182, 12, '2026-03-04 13:35:00', 'Persiapan Google Meet Rapat Teknis IPAL', 'Menindaklanjuti permintaan rapat teknis terkait pelepasan dan pengiriman sparepart alat IPAL.', 'Menyiapkan perangkat dan membantu pembuatan serta pembagian link Google Meet untuk pelaksanaan rapat.', '2026-03-04 13:56:00', 'Selesai'),
(183, 12, '2026-03-04 09:03:00', 'Pendataan Barang Elektronik IT', 'Melakukan pencatatan barang elektronik IT pada sistem IT-RSPI.', 'Input data perangkat seperti RAM dan printer ke website IT-RSPI.', '2026-03-04 09:10:00', 'Selesai'),
(184, 12, '2026-03-04 11:19:00', 'Pembaruan Tampilan dan Data Sistem IT-RSPI', 'Melakukan pembaruan dan penyesuaian fitur pada modul cuti dan data barang di website IT-RSPI.', 'Menambahkan teks tebusan pada cetak cuti, menambahkan kolom S/N pada data barang, serta memindahkan tombol Pindah dan Perbaikan ke bawah nama barang.', '2026-03-04 11:58:00', 'Selesai'),
(185, 12, '2026-03-04 14:07:00', 'Edukasi Copy Data Excel', 'Menindaklanjuti laporan dari Elita Manajemen terkait data Excel dengan angka nol (No Rm) di depan yang hilang saat copy–paste.', 'Memberikan edukasi kepada user mengenai cara copy–paste data di Excel agar angka nol di depan tetap terbaca.', '2026-03-04 14:28:00', 'Selesai'),
(186, 12, '2026-03-04 15:05:00', 'Perbaikan Export Excel NIK di website aptd_rspi', 'Mengatasi permasalahan export Excel pada website aptd_rspi bagian Diare atau khusus pasien diagnosa GEA dimana kolom NIK berubah menjadi format notasi ilmiah.', 'Menambahkan penyesuaian koding pada fitur export Excel agar data NIK terbaca lengkap dan tidak terpotong.', '2026-03-04 16:10:00', 'Selesai'),
(187, 12, '2026-03-04 15:23:00', 'Rekaman Rapat Teknis IPAL (Google Meet)', 'Melakukan perekaman rapat teknis terkait pelepasan dan pengiriman sparepart alat IPAL melalui Google Meet.', 'Hasil rekaman disimpan, diunggah ke Google Drive, dan tautannya dibagikan kepada pihak terkait.', '2026-03-04 16:37:00', 'Selesai'),
(188, 12, '2026-03-05 13:15:00', 'Edukasi rumus PROPER Data Excel ', 'Menindaklanjuti laporan dari mba dian karu Counter Lt 3 terkait data Excel dengan tulisan huruf besar semua menjadi depan nya saya yang besar', 'Memberikan edukasi kepada user mengenai cara menggunakan rumus di Excel yaitu PROPER  agar tulisan nya hanya hurup depan saja yang besar dengan cara rekam layar', '2026-03-05 13:33:00', 'Selesai'),
(189, 12, '2026-03-05 10:05:00', 'Pemasangan Monitor Display CCTV di IGD', 'Melakukan pemasangan monitor Samsung 22 inci di ruang IGD untuk keperluan display CCTV bersama Mas Hadi.', 'Monitor berhasil terpasang dan digunakan untuk menampilkan tampilan CCTV.', '2026-03-05 11:31:00', 'Selesai'),
(190, 12, '2026-03-06 10:44:00', 'Troubleshooting Printer – Output Hitam Pudar (Unit Yakut C)', 'Menerima laporan dari unit Kecubung terkait permasalahan printer dengan gejala hasil cetak warna hitam pudar dan terputus-putus (broken print).', 'Dilakukan pengecekan kondisi printer dan uji cetak. Proses head cleaning dilakukan melalui utility printer namun hasil cetak belum normal. Selanjutnya dilakukan reset manual menggunakan tombol pada printer dan dilakukan jeda waktu (idle) beberapa saat. Setelah proses reset selesai, dilakukan uji cetak ulang dan hasil cetak kembali normal.', '2026-03-06 10:55:00', 'Selesai'),
(191, 12, '2026-03-06 10:09:00', 'Edukasi Network Sharing PC di Unit Yakut C', 'Memberikan edukasi kepada Bu Atul Yakut C terkait penggunaan network sharing antar PC.', 'User telah meminta cara praktis untuk membuka dan mengakses network sharing pada PC masing-masing dengan cara saya pint start jadi tinggal klik saja', '2026-03-06 10:24:00', 'Selesai'),
(192, 12, '2026-03-06 09:23:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses pengesahan E-ICV untuk 1 pasien UMUM yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-03-06 09:44:00', 'Selesai'),
(193, 12, '2026-03-06 11:10:00', 'Pemindahan Monitor Display informasi poli anak', 'Melakukan pemindahan monitor serta alat STB di ruang poli anak lama ke poli anak baru', 'Monitor berhasil terpasang dan digunakan untuk menampilkan tampilan informasi antrian poli anak. akan tetapi masih belum dapat supply listrik', '2026-03-06 12:09:00', 'Selesai'),
(194, 12, '2026-03-07 10:02:00', 'Perbaikan Aktivasi Microsoft Office', 'Menerima laporan dari Mas Malik terkait aplikasi Microsoft Office yang tidak dapat digunakan.', 'Setelah dilakukan pengecekan, diketahui masa aktivasi Office telah habis. Dilakukan aktivasi ulang sehingga aplikasi dapat digunakan kembali.', '2026-03-07 10:45:00', 'Selesai'),
(195, 12, '2026-03-09 08:50:00', 'Perbaikan Aktivasi Microsoft Office', 'Menerima laporan dari Liza TU terkait aplikasi Microsoft Office yang tidak dapat digunakan', 'Setelah dilakukan pengecekan, diketahui masa aktivasi Office telah habis. Dilakukan aktivasi ulang sehingga aplikasi dapat digunakan kembali.', '2026-03-09 09:04:00', 'Selesai'),
(196, 12, '2026-03-09 09:06:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses pengesahan E-ICV untuk 3 pasien UMUM yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-03-09 09:29:00', 'Selesai'),
(197, 12, '2026-03-09 09:33:00', 'Penanganan Printer Epson L120 Counter Lt. 2', 'Menindaklanjuti laporan dari counter lantai 2 terkait printer Epson L120 yang tidak dapat digunakan untuk mencetak.', 'Setelah dilakukan pengecekan, indikator lampu printer berkedip bergantian yang menandakan waste ink pad counter penuh. Dilakukan proses reset menggunakan software resetter Epson L120 sehingga printer dapat digunakan kembali dengan normal.', '2026-03-09 10:23:00', 'Selesai'),
(198, 12, '2026-03-09 11:00:00', 'Penanganan Kendala Print di Komputer Mas Sam', 'Menerima laporan dari Liza TU terkait komputer Mas Sam yang tidak dapat melakukan proses print meskipun tombol pada switch hub printer sudah ditekan.', 'Setelah dilakukan pengecekan, diketahui penyebabnya karena salah memilih printer pada pengaturan print. Dilakukan penggantian ke printer yang sesuai sehingga proses print dapat berjalan normal kembali.', '2026-03-09 11:20:00', 'Selesai'),
(199, 12, '2026-03-09 14:42:00', 'Penyesuaian Gambar TTD dan Edukasi User', 'Membantu Mbak Cindy dalam menghilangkan background pada gambar tanda tangan (TTD).', 'Dilakukan pengeditan gambar agar background TTD transparan serta memberikan edukasi kepada user mengenai cara menghilangkan background pada gambar tanda tangan.', '2026-03-09 15:04:00', 'Selesai'),
(200, 12, '2026-03-09 11:52:00', 'Distribusi dan Setup Handphone Baru NICU', 'Menindaklanjuti permintaan Kepala Ruangan IT untuk mendistribusikan handphone baru ke unit NICU.', 'Melakukan distribusi perangkat seperti wa, telegram, memindahkan nomor, aktivasi email, serta pengaturan awal pada handphone lama ke yang baru agar dapat digunakan oleh unit NICU.', '2026-03-09 14:42:00', 'Selesai'),
(201, 12, '2026-03-11 15:00:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses pengesahan E-ICV untuk 7 pasien UMUM yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-03-11 16:04:00', 'Selesai'),
(202, 12, '2026-03-11 12:11:00', 'Pengecekan Double Registrasi Pasien SIMRS', 'Melakukan pengecekan laporan double registrasi pasien pada SIMRS.', 'Hasil pengecekan hardware tidak ditemukan masalah. Kemungkinan penyebab berasal dari perbedaan versi/part aplikasi SIMRS Khanza karena pada komputer B registrasi berjalan normal sedangkan pada komputer A terjadi double. Dilakukan penyalinan (copy) aplikasi SIMRS dari komputer B ke komputer A untuk pengujian dan pemantauan selanjutnya.', '2026-03-11 13:00:00', 'Selesai'),
(203, 12, '2026-03-11 10:24:00', 'Pengaktifan Display Antrian Farmasi Rawat Jalan', 'Menyalakan dan membuka display antrian Farmasi Rawat Jalan untuk operasional pelayanan.', 'Ditemukan kendala pada remote TV yang tidak dapat digunakan untuk navigasi menu. Solusi sementara dilakukan dengan menekan tombol Input pada remote untuk memilih HDMI 1, kemudian masuk ke STB dan membuka browser yang menampilkan website display antrian Farmasi Rawat Jalan. Sistem display kembali berjalan normal.', '2026-03-11 10:49:00', 'Dalam Proses / Berjalan'),
(204, 12, '2026-03-11 12:56:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang R. komdik 2 pada hari Rabu, 11 Maret 2026', 'Rincian Kegiatan : 1. ⁠jam 13.00 - selesai, R. Komdik 2, Undangan Rapat cac,', '2026-03-11 13:15:00', 'Selesai'),
(205, 12, '2026-03-10 09:00:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang R. AULA Rspi pada hari Selasa, 10 Maret 2026', 'Rincian Kegiatan : 1. ⁠jam 09.00 - selesai, R. Aula, Undangan Rapat Mingguan, 1. ⁠jam 12.30 - selesai, R. Aula, Undangan Rapat Bulanan,', '2026-03-10 09:30:00', 'Selesai'),
(206, 12, '2026-03-12 09:00:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang R. komdik 1 dan R. Pelayanan Medis, Keperawatan & MPP pada hari Rabu, 12 Maret 2026', 'Rincian Kegiatan : 1. ⁠jam 09.00 - selesai, R. Pelayanan Medis, Keperawatan & MPP,  Ujian dan ada Zoom juga, 2. ⁠jam 11.00 - Selesai, R. Komdik 1, Rapat penyampaian laporan tahunan dan RAB tahun 2026', '2026-03-12 10:30:00', 'Selesai'),
(207, 12, '2026-03-13 08:49:00', 'Gangguan Jaringan Komputer Farmasi Rawat Inap', 'Menerima laporan dari unit Farmasi Rawat Inap bahwa aplikasi SIMRS Khanza tidak dapat dibuka. Setelah dilakukan pengecekan, diketahui komputer tidak terhubung ke jaringan.', 'Dilakukan pengecekan dan cabut-pasang kabel LAN pada komputer. Setelah jaringan normal kembali, SIMRS dapat dibuka dan digunakan seperti biasa.', '2026-03-13 09:15:00', 'Selesai'),
(208, 12, '2026-03-13 09:18:00', 'Pengisian Tinta Printer', 'Melakukan pengisian tinta printer untuk mendukung operasional unit.', 'Pengisian tinta dilakukan pada printer di Farmasi Rawat Inap dan ruangan kepala counter lantai 3 sehingga printer dapat digunakan kembali dengan normal.', '2026-03-13 09:35:00', 'Selesai');
INSERT INTO `tb_logbook` (`id_log`, `id_user`, `tanggal_log`, `judul_log`, `deskripsi_log`, `catatan_log`, `tanggal_selesai`, `status_log`) VALUES
(209, 12, '2026-03-16 11:01:00', 'Penanganan Gangguan Internet Unit Kepegawaian', 'Menindaklanjuti laporan dari Elya dan Mbak Nia (Kepegawaian) terkait komputer yang tidak dapat terhubung ke internet.', 'Setelah dilakukan pengecekan, ditemukan adaptor listrik pada switch hub tidak terpasang dengan benar sehingga perangkat mati. Dilakukan pemasangan ulang adaptor ke sumber listrik dan jaringan internet kembali normal.', '2026-03-16 11:10:00', 'Selesai'),
(210, 12, '2026-03-16 11:21:00', 'Pergantian dan Penataan Printer Counter Lt. 2', 'Melakukan penggantian printer di Counter Lantai 2 setelah printer utama selesai diperbaiki dari proses service.', 'Printer Epson L3210 (Printer & Scanner) yang sebelumnya diservice telah dikembalikan dan dipasang kembali di Counter Lt. 2. Printer Epson L121 yang sebelumnya digunakan sebagai backup dipindahkan ke ICU untuk digunakan sebagai printer cadangan sementara hingga pengajuan printer baru untuk ICU terealisasi.', '2026-03-16 12:17:00', 'Selesai'),
(211, 12, '2026-03-16 12:22:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang R. komdik 1, pada hari Senin, 16 Maret 2026', 'Rincian Kegiatan : 1. ⁠jam 13.00 - selesai, R. Komdik 1, Pembahasan Rapat KPI', '2026-03-16 12:40:00', 'Selesai'),
(212, 12, '2026-03-17 08:42:00', 'Penanganan Printer FO IGD', 'Menindaklanjuti laporan dari FO IGD terkait printer yang tidak dapat digunakan untuk mencetak.', 'Setelah dilakukan pengecekan, ditemukan bahwa bantalan tinta (waste ink pad counter) printer Epson L3210 penuh. Dilakukan proses reset menggunakan software resetter Epson L3210, sehingga printer dapat digunakan kembali dengan normal.', '2026-03-17 09:31:00', 'Selesai'),
(213, 12, '2026-03-17 08:30:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang R. komdik 1 dan Komdil 2, pada hari Selasa, 17 Maret 2026', 'Rincian Kegiatan : 1. ⁠jam 08.30 - selesai, R. Komdik 1, Pembahasan Rapat Mingguan. 2. jam 09.00 - selesai, R. Komdik 2, Zoom tentang Pembahasan SOSIALISASI pelayanan kesehatan bagi Peserta JKN selama Masa Libur dan Cuti bersama Hari Suci Nyepi dan Hari Raya Idul Fitri Tahun 2026', '2026-03-17 09:20:00', 'Selesai'),
(214, 12, '2026-03-17 11:24:00', 'Penyesuaian Akses Akun SIMRS Rawat Jalan', 'Menindaklanjuti permintaan perubahan akses akun SIMRS dari rawat inap ke rawat jalan.', 'Dilakukan pengecekan data akun pengguna serta koordinasi terkait kebutuhan akses. Permintaan penyesuaian akses disetujui karena user menggunakan SIMRS untuk sekedar melihat saja', '2026-03-17 11:42:00', 'Selesai'),
(215, 12, '2026-03-24 08:56:00', 'Instal Ulang Windows 10 pada PC dan All In One', 'Melakukan instal ulang sistem operasi Windows 10 pada 1 unit PC dan 1 unit komputer All In One untuk mengatasi kendala performa dan memastikan sistem berjalan optimal.', 'Proses instalasi berjalan lancar, dilanjutkan dengan instalasi driver dan aplikasi pendukung. Perangkat dapat digunakan kembali dengan normal.', '2026-03-24 13:39:00', 'Selesai'),
(216, 12, '2026-03-25 15:10:00', 'Perawatan Komputer (Penggantian Thermal Paste)', 'Melakukan pemberian/penggantian thermal paste pada komputer pengguna untuk menjaga suhu prosesor tetap stabil dan meningkatkan performa.', 'Setelah dilakukan penggantian thermal paste, suhu komputer menjadi lebih stabil dan perangkat dapat digunakan dengan normal.', '2026-03-25 15:43:00', 'Selesai'),
(217, 12, '2026-03-25 12:33:00', 'Penggantian Power Supply Komputer', 'Melakukan penggantian power supply pada komputer pengguna dikarenakan kipas power supply lama tidak berfungsi saat dinyalakan sehingga menyebabkan komputer sering mati sendiri (overheat).', 'Power supply berhasil diganti dan komputer kembali berjalan normal tanpa kendala mati mendadak.', '2026-03-25 13:46:00', 'Selesai'),
(218, 12, '2026-03-25 08:50:00', 'Penambahan Fitur Dropdown dan Integrasi Media Sosial Website', 'Melakukan pengembangan pada website rspelitainsani.com dengan menambahkan submenu (dropdown) pada menu Layanan → Rawat Jalan, serta menambahkan ikon media sosial yang terhubung langsung ke akun terkait.', 'Fitur dropdown berhasil ditambahkan sesuai kebutuhan dan ikon media sosial (YouTube, Instagram, dll) telah terintegrasi dengan link aktif. Tampilan website menjadi lebih interaktif dan informatif.', '2026-03-25 11:54:00', 'Selesai'),
(219, 12, '2026-03-24 09:00:00', 'Perbaikan Error Tambah Data Dokter pada Website', 'Melakukan perbaikan bug pada fitur tambah data dokter di website rspelitainsani.com yang sebelumnya mengalami error saat form disimpan tanpa upload foto.', 'Dilakukan penyesuaian pada controller dan model agar upload foto bersifat opsional serta penanganan file lebih aman. Setelah perbaikan, proses tambah data dokter berjalan normal baik dengan maupun tanpa foto.', '2026-03-24 10:40:00', 'Selesai'),
(220, 12, '2026-03-25 10:04:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses pengesahan E-ICV untuk 7 pasien UMUM yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-03-25 10:34:00', 'Selesai'),
(221, 13, '2026-03-27 10:09:00', 'Laporan harian', 'APM tidak dapat mencetak no. antrian', 'tombol power printer dikondisi mati', '2026-03-27 10:20:00', 'Selesai'),
(222, 13, '2026-03-27 12:15:00', 'Laporan harian', 'reffile tinta hitam lab', '-', '2026-03-27 12:17:00', 'Selesai'),
(223, 13, '2026-03-28 10:30:00', 'Laporan harian', '- geser posisi cctv deoan poli tht dan kandungan\r\n- sd card cctv depan ruang direktur tidak terbaca, lepas pasang auto normal', 'done!!', '2026-03-28 10:42:00', 'Selesai'),
(224, 12, '2026-03-28 10:36:00', 'Reset Printer Waste Ink Pad', 'Melakukan reset printer menggunakan software resetter karena indikator bantalan pembuangan penuh.', 'Printer dapat digunakan kembali sementara setelah dilakukan reset.', '2026-03-28 11:36:00', 'Selesai'),
(225, 12, '2026-03-28 09:28:00', 'Pembaharuan Tampilan Website IT-RSPI', 'Melakukan pembaruan pada halaman beranda tiap user dan detail data pada website IT-RSPI.', 'Tampilan berhasil diperbaiki sehingga lebih rapi dan user-friendly.', '2026-03-28 11:38:00', 'Selesai'),
(226, 12, '2026-03-28 11:35:00', 'Penanganan Kertas Menyangkut pada Printer Liza (TU)', 'Mengatasi permasalahan kertas menyangkut (paper jam) pada printer.', 'Kertas berhasil dikeluarkan dan printer kembali normal.', '2026-03-28 11:52:00', 'Selesai'),
(227, 12, '2026-03-30 08:36:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang R. komdik 1 dan Komdil 2, pada hari Senin, 30 Maret 2026', 'Rincian Kegiatan : 1. ⁠jam 08.30 - selesai, R. Komdik 2, Pembahasan Rapat Koordinasi Persiapan Perpanjangan Izin Vaksin Internasional (ICV)', '2026-03-30 08:55:00', 'Selesai'),
(228, 13, '2026-03-30 08:52:00', 'Laporan Harian', '- CPU FO UGD kipas prosessor rusak \r\n- Jika dipasang LAN komputer error mati', '- ganti kipas prosessor\r\n- LAN dipasang Switch Hub', '2026-03-30 10:53:00', 'Selesai'),
(229, 12, '2026-03-30 08:52:00', 'Penggantian CPU Fan pada Komputer FO IGD', 'Melakukan troubleshooting pada komputer FO IGD yang mengalami overheat akibat CPU fan tidak berfungsi (tidak berputar), kemudian dilakukan penggantian unit CPU fan.', 'Hasil pengecekan menunjukkan fan prosesor dalam kondisi mati sehingga menyebabkan peningkatan suhu CPU. Dilakukan penggantian CPU fan dengan unit baru, pemasangan ulang heatsink, serta pengujian suhu dan performa sistem. Setelah tindakan, suhu CPU kembali normal dan sistem berjalan stabil.', '2026-03-30 09:21:00', 'Selesai'),
(230, 13, '2026-03-30 13:56:00', 'Laporan Harian', '- hp dr. Hafis tidak bs akses internet via wifi OK', '- ganti IP address', '2026-03-30 14:20:00', 'Selesai'),
(231, 13, '2026-03-30 15:29:00', 'Laporan Harian', '- Cek printer LX-310 Lab', '- belum dapat diagnosa kerusakan', '2026-03-30 15:41:00', 'Dalam Proses / Berjalan'),
(232, 12, '2026-03-30 13:07:00', 'Pengesahan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan pengesahan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Pengesahan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses pengesahan E-ICV untuk 3 pasien UMUM yang akan melakukan vaksinasi Influenza, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-03-30 14:16:00', 'Selesai'),
(233, 12, '2026-03-28 10:28:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses pengesahan E-ICV untuk 2 pasien UMUM yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-03-28 11:17:00', 'Selesai'),
(234, 12, '2026-03-31 08:25:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang R. komdik 1 dan Komdil 2, pada hari Selasa, 31 Maret 2026', 'Rincian Kegiatan : 1. jam 09.00 - selesai, R. Komdik 2, Zoom FKP LPAFK (3 orang), 2. jam 09.30 - selesai, R. Komdik 1, Rapat Mingguan', '2026-03-31 08:44:00', 'Selesai'),
(235, 13, '2026-04-06 08:41:00', 'Laporan harian', 'merapikan kabel LAN di komdik 2', 'done!', '2026-04-06 09:41:00', 'Selesai'),
(236, 13, '2026-04-06 15:45:00', 'Laporan harian', 'Error Pri nter Kecubung tidak bisa ngeprint', 'reset printer', '2026-04-06 15:50:00', 'Selesai'),
(237, 13, '2026-04-14 13:18:00', 'Laporan harian', 'Penggantian cctv baru depan dan pintu masuk parkir motor', 'kendala waktu :\r\ntangga dipinjam dan alat pemasangan kurang memadai sehingga dibantu teknisi', '2026-04-15 13:19:00', 'Selesai'),
(238, 13, '2026-04-15 12:20:00', 'Laporan harian', 'ganti kertas printer label NICU', 'kertas habis', '2026-04-15 12:25:00', 'Selesai'),
(239, 13, '2026-04-24 14:23:00', 'Laporan harian', 'hasil print kurang presisi pada printer pramusaji ', 'calibrasi ulang', '2026-04-24 14:26:00', 'Selesai'),
(240, 13, '2026-04-24 15:30:00', 'Laporan harian', 'monitor poli paru blur tampilan berbayang tidak enak dilihat', 'kabel power monitor kurang bagus', '2026-04-24 15:35:00', 'Selesai'),
(241, 13, '2026-04-24 09:48:00', 'Laporan harian', 'isi tinta printer yakut c', '-', '2026-04-24 10:04:00', 'Selesai'),
(242, 13, '2026-05-04 08:25:00', 'Laporan harian', 'isi ulang tinta counter Lantai 3\r\ncleaning 2x', 'habis total', '2026-05-04 08:32:00', 'Selesai'),
(243, 12, '2026-05-02 09:55:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta menjadi operator untuk beberapa agenda kegiatan yang dilaksanakan di ruang R. komdik 1 dan R. Aula, pada hari Sabtu, 02 Mei 2026', 'Rincian Kegiatan :\r\n1 jam 09.00 - selesai, R. Komdik 1, Koordinasi Pelayanan Asuransi (11 orang), \r\n2 jam 09.00 - selesai, R. Aula, Koordinasi Progres Pokja Akreditasi (39 orang)', '2026-05-02 10:55:00', 'Selesai'),
(244, 12, '2026-05-02 11:00:00', 'Penanganan Printer Ruang Karu Lt.3', 'Menindaklanjuti laporan dari mba dia selaku karu counter perawat lt.3 terkait printer yang tidak dapat digunakan untuk mencetak.', 'Setelah dilakukan pengecekan, ditemukan bahwa terdapat kertas macet saat print dikarenakan kapasitas kertas melebihi batas , jadi dilakukan pengurangan sedikit serta di restart printer di tombol power akhir nya normal kembali', '2026-05-02 11:28:00', 'Selesai'),
(245, 12, '2026-05-02 11:25:00', 'Pengesahan Sertifikat Elektronik E-ICV Vaksinasi Haji', 'Melakukan penginputan lanjutan dan pengesahan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Pengesahan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses pengesahan E-ICV untuk 6 pasien UMUM dan 1 pasien Umum yang akan melakukan vaksinasi MENINGITIS MENINGOCOCCUS dan POLIO, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem Sinkarkes.', '2026-05-02 12:58:00', 'Selesai'),
(246, 12, '2026-05-04 08:00:00', 'Penanganan Gangguan Koneksi Display Antrian FO Rawat Jalan', 'Menindaklanjuti laporan gangguan pada display antrian panggil di FO Rawat Jalan, di mana suara panggilan nomor antrian tidak ada informasi meskipun komputer dalam kondisi menyala', 'Dilakukan pengecekan awal yaitu dengan cara restart kmputer dan masuk kek website nya kembali sehingga display antrian dapat berfungsi kembali.', '2026-05-04 08:20:00', 'Selesai'),
(247, 12, '2026-05-04 10:24:00', 'Penarikan Data SIMRS Berdasarkan Wilayah kecamatan Banjarbaru dan Periode', 'Melakukan penarikan data dari database SIMRS menggunakan SQLyog berdasarkan permintaan user untuk kebutuhan laporan.', 'Dilakukan query data pasien rawat inap dan rawat jalan dengan filter wilayah Banjarbaru (per kecamatan) pada periode tahun 2025 hingga April 2026, dengan pengelompokan data per bulan. Data berhasil ditarik sesuai kriteria dan siap digunakan untuk kebutuhan laporan.', '2026-05-04 11:51:00', 'Selesai'),
(248, 12, '2026-05-04 08:25:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang Komdik 1 dan R. Aula pada hari Senin, 04 Mei 2026.', 'Rincian Kegiatan :\r\n1. Jam 09.00 - selesai, R. Komdik 1, Rapat Awal HUT (12 orang),\r\n2. Jam 09.00 - selesai, R. Komdik 1, Rapat Persiapan HUT RSPI,\r\n3. Jam 14.00 - selesai, R. Komdik 1, Presentasi Sucofindo (8 orang + sucofindo),', '2026-05-04 08:40:00', 'Selesai'),
(249, 12, '2026-05-04 12:58:00', 'Implementasi Query Tarikan Data pada Website APTD RSPI', 'Melakukan integrasi query penarikan data ke dalam website aplikasi tarikan data untuk menampilkan laporan kunjungan pasien berdasarkan wilayah dan kecamatan.', 'Query berhasil diimplementasikan pada sistem dan data dapat ditampilkan secara dinamis sesuai filter (wilayah, kecamatan, dan periode). Output sudah sesuai dengan kebutuhan laporan (Umum, Asuransi, BPJS, dan total).', '2026-05-04 14:55:00', 'Selesai'),
(250, 12, '2026-05-05 08:24:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang Komdik 1 pada hari Selasa, 05 Mei 2026.', 'Rincian Kegiatan : 1. Jam 09.00 - selesai, R. Komdik 1, Rapat Mingguan', '2026-05-05 08:40:00', 'Selesai'),
(251, 12, '2026-05-05 09:16:00', 'Download Dokumen Akreditasi Pokja Prognas 3 (HIV)', 'Melakukan pengunduhan dokumen akreditasi Pokja Prognas 3 (HIV) dari website SIDOKAR.', 'Seluruh dokumen berhasil diunduh dan dikirim ke user digunakan sesuai permintaan user.', '2026-05-05 10:34:00', 'Selesai'),
(252, 12, '2026-05-05 14:18:00', 'Setting Keyboard Bluetooth Poli Anak', 'Melakukan konfigurasi dan pairing keyboard Bluetooth ke perangkat tablet di Poli Anak.', 'Keyboard berhasil terhubung dan dapat digunakan dengan normal.', '2026-05-05 16:18:00', 'Selesai'),
(253, 12, '2026-05-05 15:20:00', 'Penanganan Hasil Cetak Printer Bermasalah di Liza TU', 'Mengatasi hasil cetak printer yang kotor/berbayang pada dokumen.', 'Ditemukan sisa sobekan kertas pada bagian roller printer. Dilakukan pembersihan, setelah itu hasil cetak kembali normal.', '2026-05-05 15:47:00', 'Selesai'),
(254, 12, '2026-05-06 08:49:00', 'Penanganan File Share Excel Tidak Dapat Dibuka', 'Mengatasi permasalahan file Excel bulan April pada folder sharing yang tidak dapat diakses oleh user.', 'Ditemukan kemungkinan file telah dipindahkan atau diubah oleh komputer sumber (PC sharing). Dilakukan pengecekan dan penyesuaian agar file dapat diakses kembali.', '2026-05-06 09:12:00', 'Selesai'),
(255, 12, '2026-05-06 08:16:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang Komdik 1 dan Aula dan Komdik 2 pada hari Rabu, 06 Mei 2026.', 'Rincian Kegiatan : \r\n1. Jam 08.30 - selesai, R. Aula, Rapat Bulanan,\r\n2. Jam 10.00 - selesai, R.Komdik 1, Zoom BPJS Pemahaman HFIS (7 orang) ,\r\n3. Jam 14.00 - selesai, R.Komdik 2, Rapat KPI,\r\n4. Jam 15.00 - selesai, R.Komdik 1, Rapat Pokja PAP (7 orang),', '2026-05-06 14:05:00', 'Selesai'),
(256, 12, '2026-05-06 14:19:00', 'Pembuatan Linktree Komite PPI', 'Membuat Linktree untuk mengintegrasikan seluruh Google Form Komite PPI dalam satu akses.', 'Semua form berhasil dihimpun dalam satu link sehingga memudahkan akses pengguna.', '2026-05-06 15:50:00', 'Selesai'),
(257, 12, '2026-05-07 13:04:00', 'Pendaftaran User pada Mesin Fingerprint', 'Membantu proses pendaftaran user baru pada mesin absensi fingerprint kepegawaian.', 'Dilakukan penambahan 2 data user dan pengujian absensi agar perangkat dapat digunakan dengan normal.', '2026-05-07 13:30:00', 'Selesai'),
(258, 12, '2026-05-07 10:14:00', 'Pemasangan Kaki Monitor FO 5', 'Melakukan pemasangan kaki monitor pada unit FO 5 Ralan.', 'Monitor berhasil dipasang dan dapat digunakan dengan normal.', '2026-05-07 11:15:00', 'Selesai'),
(259, 12, '2026-05-07 15:40:00', 'Penambahan Fitur 10 Penyakit Infeksi Rawat Inap', 'Melakukan pengembangan fitur laporan 10 penyakit infeksi rawat inap pada website APTD_RSPI.', 'Fitur berhasil ditambahkan dan dapat menampilkan data sesuai kebutuhan laporan.', '2026-05-07 05:20:00', 'Selesai'),
(260, 12, '2026-05-07 08:20:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang Komdik 1 dan Aula dan Komdik 2 pada hari Kamis, 07 Mei 2026.', 'Rincian Kegiatan :\r\n1. jam 08.30 - selesai, R. Komdik 1, Rakor Pokja AKP (9 orang),\r\n2. jam 13.30 - selesai, R. Komdik 1, Rakor Jadwal Akre dan Progres PROGNAS (17 orang),', '2026-05-07 13:02:00', 'Selesai'),
(261, 12, '2026-05-07 08:44:00', 'Penanganan Konflik IP Address Komputer Kepegawaian', 'Menindaklanjuti laporan dari Elya Kepegawaian terkait konflik IP Address pada komputer Elya kepegawaian', 'Setelah dilakukan pengecekan, ditemukan perbedaan MAC Address antara perangkat komputer dan konfigurasi di Winbox Mikrotik. Dilakukan penyesuaian konfigurasi agar IP Address kembali normal dan tidak terjadi konflik jaringan.', '2026-05-07 09:30:00', 'Selesai'),
(262, 12, '2026-05-08 10:50:00', 'Perawatan Printer Kecubung (Power Cleaning)', 'Melakukan perawatan printer di unit Kecubung karena hasil cetak tinta hitam tidak keluar.', 'Dilakukan proses power cleaning pada printer untuk membersihkan jalur tinta. Setelah dilakukan pembersihan, hasil cetak kembali normal.', '2026-05-08 11:25:00', 'Selesai'),
(263, 12, '2026-05-08 13:23:00', 'Pemasangan Kaki Monitor FO 3 dan 4', 'Melakukan pemasangan kaki monitor pada unit FO 3 dan 4 di Ralan.', 'Monitor berhasil dipasang dan dapat digunakan dengan normal.', '2026-05-08 14:00:00', 'Selesai'),
(264, 12, '2026-05-09 11:03:00', 'Aktivasi dan Konfigurasi Microsoft Office', 'Melakukan aktivasi dan penyesuaian Microsoft Office pada laptop poli agar aplikasi dapat digunakan dengan normal.', 'Proses aktivasi dan pengecekan aplikasi Office berhasil dilakukan, serta fitur Office dapat digunakan kembali sesuai kebutuhan operasional.', '2026-05-09 11:17:00', 'Selesai'),
(265, 12, '2026-05-12 08:36:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang Komdik 1 dan Aula dan Komdik 2 pada hari Selasa, 12 Mei 2026.', 'Rincian Kegiatan ;\r\n1. Jam 09.30 - selesai, R. Komdik 1, Rapat Mingguan,\r\n2. ⁠Jam 10.00 - selesai, R.Komdik 1, Monev Kepatuhan BPJS  (9 orang) + orang BPJS masih dikonfirmasi,\r\n3. ⁠Jam 13.30 - selesai, R.Komdik 1, presentasi hasil pelatihan SIMKES Khanza (13 orang),', '2026-05-12 13:38:00', 'Selesai'),
(266, 12, '2026-05-12 08:50:00', 'Penanganan Printer Epson l3210 FO Ralan Loket 5', 'Menindaklanjuti laporan dari FO Ralan terkait printer Epson l3210 yang tidak dapat digunakan untuk mencetak. Setelah dilakukan pengecekan, indikator lampu printer berkedip bergantian', 'Dilakukan proses reset menggunakan tombol reset  pada printer dan tombol power secara bersamaan sehingga printer dapat digunakan kembali dengan normal.', '2026-05-12 09:16:00', 'Selesai'),
(267, 12, '2026-05-12 09:17:00', 'Penanganan Gangguan Mesin APM', 'Menindaklanjuti laporan dari petugas security terkait mesin APM yang tidak dapat digunakan.', 'Dilakukan pengecekan pada sistem dan restart PC APM. Setelah proses restart selesai, mesin APM kembali berfungsi normal dan dapat digunakan kembali.', '2026-05-12 09:40:00', 'Selesai'),
(268, 13, '2026-05-12 15:26:00', 'laporan harian', 'aktivasi ms. office  di loket 4 FO', 'done!', '2026-05-12 15:35:00', 'Selesai'),
(269, 13, '2026-05-12 15:36:00', 'laporan harian', 'monitor komputer poli paru buram, kabel power kurang bagus', 'done!', '2026-05-12 15:38:00', 'Selesai'),
(270, 13, '2026-05-12 08:41:00', 'laporan harian', 'standby monitor antrian farmasi, bed UGD, dan antrian poli', 'done!', '2026-05-12 09:50:00', 'Selesai'),
(271, 12, '2026-05-18 08:10:00', 'Persiapan dan Dukungan Teknis Kegiatan di Komdik 1, Komdik 2, dan Aula RSPI', 'Melakukan persiapan serta dukungan teknis untuk beberapa agenda kegiatan yang dilaksanakan di ruang Komdik 1 dan Komdik 2 dan Komdik 2 pada hari Senin, 18Mei 2026.', 'Rincian Kegiatan :\r\n1. Jam 08.30 - selesai, R. Komdik 1, Pembahasan Kuota Pasien Katarak (6 orang),\r\n2. ⁠jam 09.00 - 10.00, R. SDM, Pertemuan Bank Taspen (7orang),\r\n3. ⁠jam 10.00 - 11.00, R. SDM, Pertemuan Bank Muamalat (8orang),\r\n4. ⁠jam 14.00 - selesai, R. Komdik 1, Rapat Pokja MFK (9 orang),\r\n5. Jam 15.00 - Selesai, R.Komdik 2, Rapat Pondok', '2026-05-18 15:00:00', 'Selesai'),
(272, 12, '2026-05-18 09:00:00', 'Penanganan Gangguan Printer FO 5', 'Menindaklanjuti laporan dari FO 5 terkait printer yang tidak dapat digunakan untuk mencetak.', 'Ditemukan kertas menyangkut (paper jam) dan antrean print menumpuk pada printer. Dilakukan penghapusan seluruh antrean print, penarikan kertas yang tersangkut secara perlahan, serta restart printer. Setelah tindakan dilakukan, printer kembali normal dan dapat digunakan.', '2026-05-18 09:20:00', 'Selesai'),
(273, 12, '2026-05-18 08:00:00', 'Instalasi Aplikasi Pendukung Komputer Radiologi', 'Melakukan pemasangan aplikasi pendukung pada komputer Radiologi setelah proses instal ulang sistem operasi.', 'Dilakukan instalasi aplikasi seperti SIMRS, Microsoft Office, dan aplikasi pendukung lainnya agar komputer dapat kembali digunakan untuk operasional pelayanan.', '2026-05-18 10:04:00', 'Selesai'),
(274, 12, '2026-05-16 08:03:00', 'Upgrade ke SSD dan Instalasi Ulang Komputer Radiologi', 'Melakukan penggantian media penyimpanan dari harddisk ke SSD pada komputer Radiologi karena perangkat sering mengalami hang dan penurunan performa.', 'SSD baru dipasang dan dilakukan instalasi Windows 10, serta pembersihan komponen bagian dalam komputer. Setelah tindakan dilakukan, performa komputer menjadi lebih stabil dan dapat digunakan kembali dengan normal.', '2026-05-16 11:21:00', 'Selesai'),
(275, 12, '2026-05-18 09:32:00', 'Penanganan Printer Label TSC FO IGD', 'Menindaklanjuti laporan dari FO IGD terkait printer label TSC yang sering error saat proses cetak dalam jumlah banyak.', 'Setelah dilakukan pengecekan, ditemukan sobekan kertas label pada roll yang menyebabkan proses penarikan label tidak normal. Dilakukan perapihan/pembersihan pada roll label dan pengujian ulang hingga printer dapat digunakan kembali dengan normal.', '2026-05-18 10:25:00', 'Selesai'),
(276, 12, '2026-05-18 10:29:00', 'Edukasi Pemilihan Jaringan WiFi di mba nanda', 'Memberikan bantuan dan edukasi kepada user terkait pemilihan jaringan WiFi yang stabil untuk digunakan.', 'User diarahkan untuk menggunakan jaringan WiFi fh_a25618 karena memiliki koneksi yang lebih stabil dan kuat di lokasi tersebut.', '2026-05-18 10:36:00', 'Selesai'),
(277, 12, '2026-05-18 12:26:00', 'Pembuatan Account mLITE', 'Melakukan pembuatan account mLITE SIMRS untuk user baru atas nama Siti Fathiyya, NIP: 674.070226 dan hak aksesnya di samakan dengan user  aminah.', 'Account berhasil dibuat dan dilakukan pengaturan hak akses sesuai kebutuhan pengguna.', '2026-05-18 12:57:00', 'Selesai'),
(278, 12, '2026-05-18 13:40:00', 'Deploy Update mLITE Status Skrining', 'Melakukan deploy/update pada sistem mLITE terkait permintaan perubahan status skrining dari user (Mba Shobbah).', 'Perubahan berhasil diterapkan pada sistem mLITE sesuai kebutuhan yang disampaikan oleh mba Shobbah dan dilakukan pengecekan fungsi setelah deploy.', '2026-05-18 13:57:00', 'Selesai'),
(279, 13, '2026-06-09 11:01:00', 'laporan harian', 'reset printer fo ugd', 'lampu led printer berkedip semua', '2026-06-09 11:09:00', 'Selesai'),
(280, 13, '2026-06-09 10:12:00', 'laporan harian', 'printer farmasi ralan godex error printer hasil print tidak keluar dan begin run tv antrian farmasi ralan', '-', '2026-06-09 10:25:00', 'Selesai'),
(281, 13, '2026-06-12 09:18:00', 'laporan harian', 'cek jaringan dan testing limitasi ', 'jalur kabel LAN dari HD ke Poli agak rusak', '2026-06-12 14:18:00', 'Belum');

-- --------------------------------------------------------

--
-- Table structure for table `tb_lokasi`
--

CREATE TABLE `tb_lokasi` (
  `lokasi_id` int(11) NOT NULL,
  `kode_lokasi` varchar(15) DEFAULT NULL,
  `nama_lokasi` varchar(100) NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_lokasi`
--

INSERT INTO `tb_lokasi` (`lokasi_id`, `kode_lokasi`, `nama_lokasi`, `keterangan`) VALUES
(1, 'IT', 'IT', 'Unit IT dan Komputer'),
(2, 'KEU', 'Keuangan', 'Unit Keuangan'),
(3, 'FO', 'FO Ralan', 'Front Office atau Pendaftaran Rawat Jalan'),
(4, 'FO', 'FO Ranap (IGD)', 'Front Office atau Pendaftaran Rawat Inap (IGD)'),
(5, 'KECU', 'Kecubung', 'Counter Kecubung'),
(6, 'YKT C', 'Yakut C', 'Counter Yakut C'),
(7, 'CO LT3', 'Counter Lt.3', 'Counter Rawat Inap Lt.3'),
(8, 'CO LT2', 'Counter Lt.2', 'Counter Rawat Inap Lt.2'),
(9, 'MNJM', 'Manajemen', 'Unit Manajemen'),
(10, 'RAD', 'Radiologi', 'Ruang Radiologi'),
(11, 'LAB', 'Lab', 'Ruang Laboratorium'),
(12, 'PL ANK', 'PL Anak', 'Poliklinik Anak'),
(13, 'PL KDG', 'PL Kandungan', 'Poliklinik Kandungan'),
(14, 'PL PD', 'PL Penyakit Dalam', 'Poliklinik Penyakit Dalam'),
(15, 'FARM', 'Farmasi Ralan', 'Farmasai Rawat Jalan'),
(16, 'FARM', 'Farmasi Ranap', 'Farmasai Rawat Inap'),
(17, NULL, 'Pelayanan Medis, Keperawatan & MPP', 'Ruang Pelayanan Medis, Keperawatan & MPP'),
(18, 'GIZI', 'Gizi', 'Ruang Gizi'),
(19, 'FARM', 'Farmasi', 'Farmasi Tengah/ditempat Karu Farmasi'),
(20, 'PN', 'PONEK', 'PONEK Lt.1'),
(21, 'KASIR', 'Kasir', 'Kasir Lt.1'),
(22, 'IGD', 'IGD', 'Ini tempat nya bisa di dokter IGD atau Perawat IGD'),
(23, 'RM', 'Rekam Medik (RM)', '-'),
(24, 'PL MT', 'PL Mata', 'Poli Mata'),
(25, 'PL PR', 'PL Paru', 'Poli Paru'),
(26, 'PL GG', 'PL Gigi', 'Poli GIGI'),
(27, 'PL RH', 'PL Rehab', 'Poli Rehab Medik'),
(28, 'DIR', 'R.Direktur', 'Ruangan Direktur'),
(29, 'KOMD 1', 'R.Komdik 1', 'Ruangan Rapat Komdik 1'),
(30, 'KOMD 2', 'R.Komdik 2', 'Ruangan Rapat Komdik 2'),
(31, 'AULA', 'R. Aula RSPI', 'Ruangan Rapat Aula RS'),
(32, 'TU', 'TU', 'Tata Usaha'),
(33, NULL, 'PL Syaraf', 'Poli Syaraf Dr. Made'),
(34, 'HD', 'HD', 'Ruang Hemodialisa'),
(35, 'ICU', 'ICU', 'Unit Perawatan Intensif'),
(36, 'NICU', 'NICU', 'Neonatal Intensive Care Unit'),
(37, 'KBG PGW', 'KABAG KEPEGAWAIAN', 'Kapala bagian kepegawaian'),
(38, 'SEKRE', 'Sekretariat', 'Komdik 2'),
(39, 'DPR LT4', 'Dapur Lt.4', 'Dapur pengolahan di Lantai 4'),
(40, 'DPR LT1', 'Dapur Lt.1', 'Dapur pengolahan di Lantai 1'),
(41, 'OK', 'Kamar Operasi', 'Kamar Operasi (Tempat Mas Fadil, samping ICU)');

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
(3, 10, 1, 3, '2025-09-04', 12, 'Dipasang FO 5 di rawat jalan dikarenakan yang  di FO 5 rusak sudah tidak respon lagi fingernya'),
(4, 17, 1, 4, '2025-09-15', 12, 'Dikarenakan ada penambahan fitur di simrs yaitu di bagian persetujuan rawat inap yang memerlukan kamera untuk  mengambil gambar pasien'),
(5, 21, 1, 20, '2025-09-17', 12, 'Pemintaan dari mba arien karna bekas di ponek besarnya cuman 14\" aja'),
(6, 45, 1, 23, '2025-10-13', 12, 'Asal mula dari komputer mas hadi IT diserahkan ke RM Untuk kebutuhan Karyawan Baru'),
(7, 84, 1, 24, '2025-12-02', 12, 'Infonya sendat-sendat'),
(8, 87, 1, 20, '2025-12-24', 12, 'Permintaan dari mba ayu sendat2 dan lekas habis baterainya ingin diganti pakai kabel saja (Diganti punya riyan)'),
(9, 75, 1, 24, '2025-08-20', 12, 'mengganti karna yang dulu rusak'),
(10, 98, 33, 20, '2026-01-15', 12, 'Mengganti Spesifikasi yang lebih tinggi di ponek dan penambahan RAM jadi total 16 GB disana'),
(12, 80, 1, 34, '2025-12-24', 12, 'Dapat Laporan finger lama tidak berfungsi , jadi finger dari IT yang keperluannya untuk mentes APM diserahkan ke HD'),
(14, 106, 1, 34, '2025-09-26', 12, 'Dipinjam Untuk Pelatihan Orang Karyawan HD'),
(15, 106, 34, 1, '2026-02-02', 12, 'Dikembalikan Ke IT karna Pelatihan Orang HD sudah selesai'),
(16, 28, 1, 8, '2025-12-24', 12, 'Diserahkan ke Counter Lt2 karna printer mereka di tarik ke IT untuk di service ke luar merek : Epson L3210'),
(17, 109, 20, 1, '2025-12-16', 12, 'karna di ponek sekarang pakai pc bekas dokter syaraf jadi yang ada ini di naikan ke IT untuk Cadangan'),
(18, 109, 1, 25, '2026-02-25', 12, 'dipindah karna komputer yang dulu di Poli paru motherboardnya rusak'),
(19, 110, 1, 9, '2026-03-03', 12, 'Di pasang ke PC elya kepegawaian guna menambah perfoma komputer'),
(20, 111, 34, 1, '2026-03-04', 12, 'Sudah tidak diperlukan lagi karna sudah pakai sistem'),
(21, 28, 8, 35, '2026-03-16', 12, 'Untuk sementara sambil barang pengajuan printer baru datang'),
(22, 28, 35, 1, '2026-03-30', 13, 'dikembalikan ke IT untuk stok di karenakan mereka ingin pakai printer pribadi saja'),
(23, 117, 37, 1, '2026-03-25', 12, 'Di pindah ke IT karena mba nanda memakai PC All in one punya mas lefi pribadi'),
(24, 118, 32, 1, '2026-02-04', 12, 'Diserahkan ke IT buat cadangan dikarenakan liza mendapatkan PC Baru'),
(25, 137, 3, 38, '2026-06-20', 12, 'Untuk Keperluan Akreditasi');

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
(4, 12, 'Solution Digital Persona U are U 4500 Free SDK', 'Unit IT', 0, 1200000.00, 'untuk mentes absensi SIMRS dan untuk cadangan mengganti finger bpjs di fo ralan', 'selesai', '2025-07-28', '2025-07-29'),
(7, 12, 'Fingerprint Sidik Jari Fingerspot U are U 4500 USB PC Based', 'Unit IT', 0, 1500000.00, 'untuk Keperluan untuk mentes website APM BPJS dan nantinya akan di pakai buat APM BPJS Juga di bawah, jika tidak buat  cadangan di FO karna di FO ralan juga banyak mulai bermasalah', 'selesai', '2025-09-12', '2025-09-12'),
(8, 12, 'ADAPTOR LCD/LED MONITOR LG', 'Unit IT', 0, 60000.00, 'Mengganti punya rizky', 'selesai', '2025-06-09', '2025-06-20'),
(9, 12, 'RAM 8 GB DDR 4 Merek KingSton dan V-Gen', 'Unit IT', 0, 250000.00, 'Upgrade PC Desktop (Unit CPU / PC Rakitan) punya riyan it', 'selesai', '2025-06-17', '2025-06-20'),
(10, 12, 'V-GeN SSD 128 GB', 'Unit IT', 0, 160000.00, 'Upgrade PC Desktop (Unit CPU / PC Rakitan) punya riyan it', 'selesai', '2025-06-17', '2025-06-20'),
(11, 12, 'Webcame Logitech C270 HD 720p', 'Unit IT', 0, 320000.00, 'Untuk penunjang Akreditasi dan kegiatan rapat-rapat ketika Zoom', 'selesai', '2025-02-18', '2025-02-24'),
(13, 12, 'Uninterruptible Power Systems (UPS)', 'Unit IT', 3, 1.00, 'Untuk semua komputer unit staff IT', 'disetujui', '2025-04-16', '2025-04-28'),
(15, 12, 'Kabel Duct TC5  Protector Pelindung Kabel', 'Unit IT', 8, 25000.00, 'Untuk merapikan kabel lan di counter lt 3 dan aula', 'disetujui', '2025-09-23', '2025-09-24'),
(16, 12, 'SanDisk 32 GB', 'Unit IT', 0, 68000.00, 'Untuk Instal ulang dan copy file unit it', 'selesai', '2025-10-07', '2025-10-07'),
(17, 12, 'Charger atau Adaptor atau  Casan Mini PC merek HP', 'Unit IT', 1, 200000.00, '-', 'disetujui', '2025-10-16', '2025-10-16'),
(21, 12, 'UGREEN USB Type-C To Lan RJ45 Ethernet Adapter 100Mbps - 1000Mbps For Windows Mac Os Set 50922', 'Unit IT', 1, 155000.00, 'Link Pembelian di Shoppe : https://shopee.co.id/product/1537643280/44053707336?gads_t_sig=VTJGc2RHVmtYMTlxTFVSVVRrdENkVHQ3ZkZSUTMrR3pBWmZZNzdrcnRBMThFcVgvMHJsbTNCQndRS0RHVUo2WDMvUHBLRjJuUTR3cXBxV2dML0VVSGhOcFUvNGY3V0ZwQWlybGR6WGE4aHgrOEhoOCsrTWhWTG4yU2U2S1Zaa2lLOEMwV2hvS3ZvSHBram1Odm00NnNnPT0&gad_source=1&gad_campaignid=22313024608&gbraid=0AAAAADPpU834zgVVtw05HuS7pwMgN_1rG&gclid=Cj0KCQjwmYzIBhC6ARIsAHA3IkRDm5THQd1Gs8MuHz09WCbPBZ2nMkY8BGPiw4W1y23jqn4FjEzoZA0aAlYbEALw_wcB', 'ditolak', '2025-10-31', '2025-11-03'),
(22, 12, 'UGREEN Kabel USB Type C Fast Charging 3A 1m 2m 3m For Samsung Oppo Vivo Xiaomi Realmi', 'Unit IT', 0, 30000.00, 'https://shopee.co.id/UGREEN-Kabel-USB-Type-C-Fast-Charging-3A-1m-2m-3m-For-Samsung-Oppo-Vivo-Xiaomi-Realmi-i.293199663.26517785112?extraParams=%7B%22display_model_id%22%3A108512538250%2C%22model_selection_logic%22%3A2%7D&sp_atk=2257fbb4-4d32-4d1c-a5ee-7cf1f3ba869e&xptdk=2257fbb4-4d32-4d1c-a5ee-7cf1f3ba869e', 'disetujui', '2025-10-31', '2025-11-03'),
(23, 13, 'Kabel USB Printer (15 Meter)', 'Unit IT', 1, 150000.00, 'Untuk printer keuangan', 'disetujui', '2026-04-06', '2026-04-06'),
(24, 12, 'HDMI Video Capture Type C USB 3.0 /Video Capture HDMI Card Video USB3.0 USB Type C', 'Unit IT', 0, 100000.00, 'Contoh Barang : https://shopee.co.id/HDMI-Video-Capture-Type-C-USB-3.0-Video-Capture-HDMI-Card-Video-USB3.0-USB-Type-C-with-Loop-Video-Capture-Card-USB-to-HDMI-Game-Record-Streaming-Live-Video-Capture-Card-HDMI-HD-4K-60Hz-Mac-Windows-Android-i.949812562.44318394126?extraParams=%7B%22display_model_id%22%3A29941591609%2C%22model_selection_logic%22%3A3%7D&sp_atk=d2ddd3e3-2220-48ca-93b8-307a2bb77199&xptdk=d2ddd3e3-2220-48ca-93b8-307a2bb77199', 'selesai', '2026-04-07', '2026-04-07'),
(25, 12, 'UGREEN Kabel Data USB 2.0 A Male To Mini USB 5 Pin 10355 Male Cable Camera Original Ori Kamera', 'Unit IT', 1, 35000.00, '', 'disetujui', '2026-04-07', '2026-04-07'),
(26, 12, 'Kabel data Vention Mini USB 2.0 to Mini-USB', 'Unit IT', 0, 50000.00, 'Contoh Barang : https://shopee.co.id/product/34451969/10938365018?d_id=6bac3&uls_trackid=55bb1pvd00qi&utm_content=3jdvQMp3MerW5EurtW3YFVBTzSb1', 'selesai', '2026-04-07', '2026-04-07'),
(27, 12, 'Tinta Pigment Printer Epson isi 1 Liter PREMIUM INK ( Made in Korea ) - Black', 'Unit IT', 0, 15000000.00, 'Stok tinta, karna untuk persedian tinta printer yang hitam sudah habis di IT', 'selesai', '2026-04-16', '2026-04-17'),
(28, 12, 'VENTION KABEL HDMI TO HDMI 10M GOLD PLATE HIGH QUALITY AAC', 'Unit IT', 0, 290000.00, 'Untuk Kebutuhan di aula karna di aula rusak bekas acara HD WKD', 'selesai', '2026-04-27', '2026-05-06'),
(29, 13, 'TP-LINK Archer AX12 AX1500 Dual-Band Wi-Fi 6 Router', 'Unit IT', 1, 499.00, 'penggantian router wifi untuk pasein ranap VIP/VVIP Lantai 2', 'disetujui', '2026-05-11', '2026-05-18'),
(30, 12, 'Uninterruptible Power Systems (UPS)', 'Unit IT', 2, 700000.00, 'Untuk keperluan atau backup Komputer Riyan dan Hadi IT karna UPS nya sudah tidak menyimpan lagi baterainya', 'disetujui', '2026-05-12', '2026-05-18'),
(31, 13, 'UPS poli mata', 'Unit IT', 1, 700.00, 'sudah tidak dapat digunakan lagi, karena tiap kali digunakan komputer tidak bisa dinyalakan menggunakan ups kurang daya', 'diajukan', '2026-06-10', NULL),
(32, 13, 'Baterai Laptop Poli Kandungan (Asus seri X515EA)', 'Unit IT', 1, 1.00, 'Baterai drop dan tidak mengisi daya sama sekali, jika mati lampu maka laptop mati layanan tertunda perawat tidak dapat menggunakan laptop', 'diajukan', '2026-06-15', NULL);

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
-- Dumping data for table `tb_penyerahan`
--

INSERT INTO `tb_penyerahan` (`penyerahan_id`, `barang_id`, `lokasi_id`, `kondisi`, `keterangan`) VALUES
(1, 10, 3, 'baru', 'mengganti finger bpjs di fo ralan 4'),
(2, 11, 1, 'baru', 'Untuk penunjang akreditasi dan untuk acara agenda2 rapat kedepannya'),
(3, 12, 1, 'baru', 'Untuk komputer Hadi IT'),
(4, 13, 1, 'baru', 'Untuk Monitor Rizky IT karna punya dia di pasang di mba shobah Gizi'),
(5, 14, 1, 'baru', 'Upgrade PC Desktop (Unit CPU / PC Rakitan) punya riyan it'),
(6, 14, 1, 'baru', 'Upgrade PC Desktop (Unit CPU / PC Rakitan) punya riyan it'),
(7, 15, 1, 'baru', 'Mengganti SSD karna rusak bekas di pakai buat AKRE PC Desktop (Unit CPU / PC Rakitan) punya riyan it'),
(8, 16, 15, 'baru', 'Untuk Mengganti komputer farmasi di ralan yang PSU nya konslet'),
(9, 17, 4, 'baru', 'Untuk penunjang Akreditasi dan kegiatan rapat menggunakan zoom dan lainnya'),
(10, 21, 1, 'bekas', 'Punya Mas Hadi IT'),
(11, 22, 1, 'baru', 'Untuk Komputer Riyan IT'),
(12, 23, 10, 'baru', 'Penempatan di Radiologi dalam samping Lab'),
(13, 24, 10, 'baru', 'Penempatan di Radiologi dalam samping Lab'),
(14, 25, 1, 'baru', 'Penempatan di Ruang IT (PC UTAMA ALI)'),
(15, 26, 1, 'baru', 'Untuk Komputer ALI IT'),
(16, 27, 16, 'baru', 'Diterima langsung oleh karu farmasi (Rollah) pada tanggal 2025-09-20 dengan kondisi =BARU , dan printer Epson L121 di tarik ke Unit IT'),
(17, 28, 16, 'bekas', '-'),
(18, 29, 5, 'baru', 'Sudah di pasang dan di terima oleh karu kecubung, di pasang untuk kegunaan backup komputer & printer'),
(19, 30, 5, 'bekas', 'Ini komputer Mas Malik dan diserahkan ke kecubung karna permintaan dari karu kecubung ingin penambahan 1 pc'),
(20, 31, 6, 'baru', 'Untuk membackup komputer dan printer dan diterima langsung oleh karu yakut c (bu atul) saat pemasangan'),
(21, 32, 22, 'baru', 'di pasang di komputer dokter IGD'),
(22, 33, 15, 'baru', 'Sudah diserahkan oleh petugas farmasi ralan'),
(23, 34, 22, 'baru', 'di pasang di komputer Dokter IGD karna yang dulu cuman 4 GB saja RAM nya'),
(24, 35, 15, 'baru', 'langsung di diganti karna yang lama rusak (info : mas hadi)'),
(25, 36, 1, 'bekas', 'SSD Bekas punya Dito IT  dipasang di komputer dokter IGD karna permasalahan yang sebelumnya sering mati sendiri dan bluscreen'),
(26, 37, 1, 'baru', 'Untuk keperluan Bakcup data unit IT'),
(27, 38, 1, 'baru', 'Untuk keperluan Bakcup data unit IT'),
(28, 39, 23, 'baru', 'Upgrade PC Lama (Untuk Karyawan RM Baru)'),
(29, 40, 23, 'baru', 'Upgrade PC Lama (Untuk Karyawan RM Baru)'),
(30, 41, 23, 'baru', 'Upgrade PC Lama (Untuk Karyawan RM Baru)'),
(31, 42, 20, 'baru', 'yang dulu pakai hardisk kondisinya yaitu hardisknya sering tidak terbaca saat menyalakan komputer dan atau saat berhasil masuk windows kadang kembali mati , Indekasi Hardisk sudah melemah/Rusak'),
(32, 43, 20, 'rusak', 'INI BEKAS PC PONEK, KONDISI HARDISK LEMAH KARNA SERING TIDAK TERBACA SAAT NYALAKAN PC, DAN KADANG SERING MATI (BLUESCREEN)'),
(33, 44, 23, 'baru', 'untuk keperluan komputer yang di pasang karyawan RM Baru'),
(34, 45, 23, 'bekas', 'Monitor Untuk Komputer Mas HADI'),
(35, 46, 1, 'baru', 'Stok TINTA  IT'),
(36, 68, 21, 'baru', 'Upgrade RAM + Memperbaiki karna yang dulu error'),
(37, 69, 21, 'baru', 'Pengajuan Printer baru dari kasir , yang dulu di kembalikan ke unit IT'),
(38, 70, 21, 'baru', 'Pengajuan dari kasir karna epson LX yang dulu sudah tidak layak pakai dan sering nyangkut kertasnya'),
(39, 71, 1, 'bekas', 'Printer ini bekas di kasir ditarik ke IT dulu karna di kasir sudah ada yang baru yaitu Epson L121, Kondisi Printer : Tinta banjir dan ???'),
(40, 72, 1, 'bekas', 'Ini bekas di kasir lalu diserahkan dahulu ke unit IT karna di kasir dapat printer Epson LX-310 baru di tanggal 21/10/25'),
(41, 73, 1, 'bekas', 'RAM bekas komputer Dito/Riyan , Kondisi masih perkiraan 60%'),
(42, 74, 1, 'baru', 'Untuk Acara Bu Haji + Backup IT kalo ada acara-acara'),
(43, 75, 1, 'baru', 'dipakai mas ali pas di bulan desember'),
(44, 76, 3, 'baru', 'Untuk Upgrade Komputer FO Ralan 2 dikarenakan keluhan nya sering takang dan setelah di cek memang benar ada kendala di haridisk'),
(45, 77, 24, 'baru', 'Untuk penyambung LAN ke Laptop Poli , yang dulu infonya rusak'),
(46, 78, 3, 'baru', 'Untuk mengganti Kipas proccesor komputer asuransi (FO 2) karna indikasi kipas lama sudah tidak berputar lagi mengakibatkan pc panas dan overhead lalu menyebabkan mati-mati dan lag'),
(47, 79, 3, 'baru', 'Untuk Upgrade performa komputer asuransi (FO 2), jadi sekarang kapasitas RAM PC 10 GB (8+2)'),
(48, 80, 34, 'baru', 'Untuk menunjang keperluan APM Baru dan nanti nya akan di pakai jika app APM baru sudah selesai'),
(49, 81, 1, 'bekas', 'Ram Bekas FO Ralan (2) yang biasa di pakai buat asuransi, Kondisi Perkiraan 60% (Masih bisa di pakai)'),
(50, 82, 3, 'rusak', 'Ini SSD dari komputer Ralan 4 Rusaknya karna SSD tidak terbaca lagi saat menyalakan komputer dan di tes menggunakan external juga tidak terbaca'),
(51, 83, 4, 'baru', 'Untuk kamera persetujuan ranap'),
(52, 94, 9, 'baru', 'Untuk Komputer Mas Adi ahmad'),
(53, 93, 3, 'baru', 'untuk minitor FO ralan 2 (Asuransi)'),
(54, 92, 24, 'rusak', 'Info tidak bisa terhubung lagi jadi ketika di colok ke kabel lan dia tidak terbaca walaupun indikator lannya nyala'),
(55, 91, 1, 'baru', 'Untuk Cadangan dan diperentukan utama untuk agenda rapat online'),
(56, 90, 1, 'baru', 'dI peruntukan di pasien Lt 2 karna yang dulu  rusak Info : mas Hadi'),
(57, 89, 1, 'baru', 'Buat Cadangan Stok'),
(58, 88, 1, 'baru', 'Buat Cadangan Stok'),
(59, 87, 1, 'baru', 'Untuk Ika IT Keyboardnya dan mousenya untuk riyan'),
(60, 86, 1, 'bekas', 'Bekas Poli Mata'),
(61, 84, 24, 'baru', 'Ini di awalnya ampun rizky di bawa dan diserahkan ke Poi Mata'),
(62, 85, 1, 'bekas', 'Bekas Poli Mata (Sepaket dengan Mouse)'),
(63, 95, 33, 'baru', 'Permintaan dokter made yang ini di ganti dan sudah diserahkan pada tanggan 14-01-2026'),
(64, 96, 11, 'baru', 'Untuk komputer mba ema lab tanggal penyerahan : 14-01-2026'),
(65, 97, 11, 'baru', 'untuk komputer di depan mba ema lab, tanggal penyerahan sama  14-01-2026'),
(66, 98, 33, 'bekas', 'Diserahkan ke unit ponek , dan komputer ini bekas poli syaraf'),
(67, 100, 20, 'baru', 'Menambah Kapasitas untuk unit PONEK'),
(68, 101, 1, 'baru', 'UNTUK RIYAN IT KARNA YANG DULU PUNYA NYA RUSAK KARNA KONSLET LALU DI GANTI DENGAN YANG RAKITAN MAS HADI DENGAN KONDISI KEMUNGKINAN BAIK NYA 50%'),
(70, 103, 1, 'baru', '10/02/2026 Ram ini awalnya untuk Liza Untuk PC bekas Ponek dulu ternyata di tarik lagi ke IT karna PC liza dulu cuman diganti Power Supplay nya saja, dan Sekarang untuk Server IT yang E-Klaim (192.168.1.75)'),
(71, 104, 32, 'baru', '11/02/2026-pengajuan sudah lama untuk PC Baru buat Liza'),
(72, 105, 32, 'baru', 'Untuk mempermudah kinerja TU untuk sharing printer'),
(73, 106, 1, 'baru', 'Untuk Acara Akreditasi'),
(74, 107, 8, 'baru', '-'),
(75, 108, 7, 'baru', 'Mengganti keyboard yang lama karna kerusakan yang lama spasi nya tidak bisa'),
(76, 109, 20, 'bekas', 'Ini Komputer Bekas Ponek dahulu'),
(77, 110, 1, 'baru', 'ini awalnya mau di pasang di mas adiahmad akan tetapi komputernya di sama karna DDR 3 hal hasil tidak jadi dan simpan ke IT saja buat cadangan'),
(78, 111, 34, 'baru', 'untuk keperluan HD cetak diagnosa dan lain2nya'),
(79, 112, 22, 'baru', 'Untuk Keperluan DIsplay CCTV'),
(80, 114, 35, 'baru', 'tinta warna untuk printer Cannon pixma E410'),
(81, 113, 35, 'baru', 'tinta hitam untuk printer Cannon pixma E410'),
(82, 115, 36, 'baru', 'Untuk Keperluan Komunikasi antar dokter dan unit lain nya'),
(84, 117, 37, 'bekas', 'PC Mba Nanda humai'),
(85, 118, 32, 'baru', 'PC Nurhaliza TU'),
(86, 119, 1, 'baru', 'Di peruntukan untuk IKA Aprillia IT'),
(87, 120, 35, 'baru', 'Diperuntukan untuk ICU dan diserahkan dengan karu yaitu Bas dan karywan'),
(88, 121, 1, 'baru', 'Untuk Keperluan Halal Bihallal dan seterusnya buat acara'),
(89, 122, 1, 'baru', 'Diperuntukan untuk Halal Bihallal dan selanjutnya digunakan untuk aset IT'),
(90, 123, 1, 'baru', 'Untuk dipasang di area parkir depan atau parkir motor pasien'),
(91, 124, 1, 'baru', '1 di pasang di poli kandungan'),
(92, 124, 1, 'baru', '1 di pasang parkiran dekat sanitasi'),
(93, 125, 34, 'baru', 'Mengganti/Upgrade HP Ruangan'),
(94, 126, 1, 'baru', 'Untuk Acara halal bihallal, setelah nya digunakan untuk acara2 yang di pakai kamera'),
(95, 127, 12, 'baru', 'Diperuntukan untuk Tab untuk operasional penginputan mLITE'),
(96, 128, 1, 'bekas', 'Ini laptop dari mas malik di berikan ke unit IT Untuk Stok yang akan di gunakan acara2 seperti rapat dan acara lainnya'),
(97, 129, 1, 'baru', 'Diperuntukan untuk Wifi ruang VVIP'),
(98, 130, 1, 'baru', 'Untuk Stok Cadangan tinta printer hitam untuk rumah sakit'),
(99, 130, 1, 'baru', 'untuk stok IT'),
(100, 130, 1, 'baru', 'Stok IT'),
(101, 130, 1, 'baru', 'Stok IT'),
(102, 130, 1, 'baru', 'Stok IT'),
(103, 131, 1, 'baru', 'Untuk Keperluan di LCD proyektor di Aula'),
(104, 132, 1, 'baru', 'Dipergunakan untuk Access Point di VVIP Lt.2'),
(105, 133, 10, 'baru', 'Upgrade performance Komputer Merek Dell di Radiologi yang awal nya pakai Hardisk sekarang pakai SSD dikarenakan selalu mengeluh mengelag/takang terus komputer nya'),
(106, 134, 1, 'baru', 'Komputer Baru untuk Rizky Ilha'),
(107, 135, 1, 'baru', 'untuk komputer cadangan di IT karna ram nya di pakai ke unit lain'),
(108, 136, 3, 'baru', 'Printer Baru tanggal : 12/06/2026 di serahkan dan di terima oleh Sofa sebagai karu FO'),
(109, 137, 3, 'baru', 'Data Barang Lama'),
(110, 138, 15, 'baru', 'Ganti Vendor'),
(111, 139, 16, 'baru', 'Ganti Vendor, jadi yang lama di tarik');

-- --------------------------------------------------------

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
  `bukti_struk` varchar(250) NOT NULL,
  `unit_melapor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_perbaikan_barang`
--

INSERT INTO `tb_perbaikan_barang` (`perbaikan_id`, `barang_id`, `tanggal_lapor`, `penyerahan_id`, `deskripsi_kerusakan`, `tindakan_perbaikan`, `status`, `tanggal_selesai`, `teknisi`, `keterangan`, `bukti_struk`, `unit_melapor`) VALUES
(1, 27, '2025-11-25 10:58:00', 16, 'Printer Epson L3210 mengalami gangguan pada bagian fleksibel scanner dan roller (rol) sehingga fungsi scanner dan penarikan kertas tidak berjalan normal.', 'Service luar', 'selesai', '2026-12-29 12:02:00', NULL, 'Unit printer dibawa ke service center Twincom untuk dilakukan perbaikan karena masih dalam masa garansi.', '1_1772758575.jpeg', 16),
(2, 92, '2026-02-17 19:59:00', 0, 'Tidak dapat terdeteksi lagi jaringan internet', 'Service sendiri', 'tidak_dapat_diperbaiki', NULL, NULL, 'Rusak', '', 24),
(3, 82, '2026-02-17 20:11:00', 0, 'Hardisk nya tidak terbaca lagi di bios dan di coba di pasang pakai alat ssd exsternal juga tidak bisa', 'Service sendiri', 'tidak_dapat_diperbaiki', NULL, NULL, 'rusak total', '', 3),
(4, 43, '2026-02-17 20:12:00', 0, 'KONDISI HARDISK LEMAH KARNA SERING TIDAK TERBACA SAAT NYALAKAN PC, DAN KADANG SERING MATI (BLUESCREEN)', 'Service sendiri', 'tidak_dapat_diperbaiki', NULL, NULL, 'Definisi kerusakan sekitar 90%', '', 20),
(5, 28, '2025-12-03 09:20:00', 0, 'Ketika memprint selalu nyangkut setelah di cek ada partisi yang rusak dan ada juga yang patah akibat kemungkinan saat kertas nyangkut menarik kertas terlalu paksa', 'Service luar', 'selesai', '2025-12-18 10:00:00', NULL, 'service di twincom', '5_1772758616.jpeg', 1),
(6, 107, '2026-02-20 15:47:00', 0, 'Rol Patah dan ketika di print juga tidak merespon apa-apa', 'Service luar', 'selesai', '2026-03-16 12:00:00', NULL, '-', '6_1772758169.jpeg', 8),
(7, 107, '2026-04-14 14:15:00', 0, 'Printer mengalami kondisi error yang ditandai dengan munculnya indikator peringatan serta adanya penggunaan jumper pada salah satu bagian rangkaian. Pada saat proses pencetakan, printer masih dapat berfungsi normal pada 1–2 kali cetak awal, namun setelah itu perangkat tidak dapat melanjutkan proses printing dan kembali ke kondisi error.\r\n\r\nGejala ini mengindikasikan adanya gangguan pada sistem kontrol, yang kemungkinan disebabkan oleh kerusakan pada sensor (seperti sensor kertas atau sensor penutup), power supply yang tidak stabil, atau kerusakan pada mainboard. Penggunaan jumper juga menunjukkan adanya indikasi bypass pada jalur tertentu, yang biasanya dilakukan untuk mengatasi sementara kerusakan sensor atau rangkaian.', 'Service luar', 'selesai', '2026-04-27 11:02:00', NULL, 'Akibat dari kondisi tersebut, printer tidak mampu bekerja secara konsisten dan hanya dapat digunakan secara terbatas sebelum kembali mengalami error.', '7_1776310161.jpeg', 8),
(8, 129, '2026-05-11 11:14:00', 0, 'Router tidak menyala lagi, sudak di ganti adapter karna dikira kabel power nya rusak sekalinya pakai adapter baru tetap tidak menyala router nya', 'Service sendiri', 'tidak_dapat_diperbaiki', NULL, 'Abdul Hadi, S.Kom', 'Rusak harus di lakukan pengajuan barang baru', '', 1);

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
(8, '1051896797/192.168.1.157', 'unitit2025', 'LALA PC'),
(9, '1830046703', 'unitit2025', 'Ltp Dell Hitam'),
(10, '1094780395', 'unitit2025', 'ALI IT'),
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
(29, '1009268831/192.168.2.81', 'unitit2025', 'Komputer Dr Made'),
(30, '1834047286/192.168.1.238', 'unitit2025', 'MALIK PC'),
(31, '1560071979/192.168.1.45', 'unitit2025', 'IKA IT'),
(32, '1830046703/192.168.1.84', 'unitit2025', 'Laptop Dell Inspiron (Hit'),
(33, '1550125969', 'unitit2025', 'PC Purwanto');

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
(1, '234.030221', 'ali', '$2y$10$AKkVgsZmhsXun4wHTaxoyewmYuWJJtgPAQ12y1CJJPSY7K7a3I71y', 'ALI IWANSYAH', 'Banjarmasin', '1991-03-02', 'Staff IT', 'SMA', 'Jl. Dahlia Kebun Sayur RT 15 RW 01 No. 45 Gg. Melati 2, Banjarmasin', 'unititrspi@gmail.com', '081253534891', 'Staff', 'user_1_699e90ca70218.jpeg', 'aktif', '2026-01-10 03:24:23', '2026-03-16 03:27:42'),
(2, '662.140725', 'ika', '$2y$10$nhtEVX3lQCQGqHpu0B120O6kN/5j.XfGxFY/GQxecTRdAh4NK9jpW', 'Ika Aprillia, S.Kom', 'Kediri', '2003-04-01', 'Staff IT', 'S1 Komputer', 'Komp Wira Pratama III Jl. Patin No.17 Rt 25 Rw 04,', 'unititrspi@gmail.com', '087753560464', 'Staff', 'user_2_699471dbcd340.png', 'aktif', '2026-01-10 03:21:34', '2026-02-26 06:42:58'),
(3, '22', 'da', '12345', 'dss', '', '2026-02-25', '', '', '', 'ss', '22', 'Staff', '1758164121_depositphotos_316270922-stock-illustration-salesman-employee-providing-a-customer.jpg', 'nonaktif', '2025-09-13 04:29:58', '2026-02-24 01:03:54'),
(4, '629.271224', 'rizky', '$2y$10$mHLm.yWek5Im8BfFzuZF8uauO7xTBicHtPWlLmoEGftd.tqyirf0a', 'Muhammad Rizky Ilham Pratama', 'Martapura', '2002-02-16', 'Staff IT', 'D3 Teknik Komputer', 'Jl. Abdul Muthalib 2 Rt 06 Rw 01 Desa Padang', 'rizkyilhamp16@gmail.com', '08998039978', 'Staff', '629.271224.jpg', 'aktif', '2025-09-18 03:01:01', '2026-04-27 02:01:30'),
(10, '097.011113', 'admin', '$2y$10$SzwDz8O2teScUtfw2pzMau.jdreAWywzeJi9j0UQFQFYC27VqUO1O', 'Qhusnul Arinda, Amd. Far', 'Banjarbaru', '1993-11-28', 'Kepala IT', 'D3 Farmasi', 'Bincau Martapura', 'qhusnl.arienda@gmail.com', '085751094503', 'Kepala Ruangan', 'user_10_69c73f67b7a76.jpg', 'aktif', '2026-01-10 00:50:27', '2026-03-28 02:39:35'),
(12, '635.090125', 'riyan', '$2y$10$akXHkBMhThGUY2bD0hAfSuVkQimRQu0p8DriEx/jaeCfK/Fjnxeju', 'Riyan Aditya Pradana, S.Kom', 'Banjarbaru', '1996-08-21', 'Staff IT', 'S1 Teknik Informatika', 'Komp. Mustika Raya Permai I Blok C 6', 'riyanadityapradanaa@gmail.com', '082130304411', 'Staff', '635.090125.png', 'aktif', '2026-01-10 01:05:21', '2026-02-27 01:37:42'),
(13, '527.010623', 'hadi', '$2y$10$bgOA8.B/1SphzBuXSVGMbe86U9Ibfn0wFK0.4Qmp.eR6bLifj6aUC', 'Abdul Hadi, S.Kom', 'Hantakan', '1985-11-29', 'Staff IT', 'S1 Teknik Informatika', 'Jl. Brigjen Hasan Basri Hantakan Pasar 68 RT.001', 'unititrspi@gmail.com', '085822823436', 'Staff', '527.010623.jpg', 'aktif', '2026-01-10 03:18:58', '2026-03-27 07:17:33');

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
  MODIFY `barang_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- AUTO_INCREMENT for table `tb_calon`
--
ALTER TABLE `tb_calon`
  MODIFY `id_calon` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_cuti`
--
ALTER TABLE `tb_cuti`
  MODIFY `id_cuti` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tb_keahlian`
--
ALTER TABLE `tb_keahlian`
  MODIFY `id_keahlian` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_kegiatan_lembur`
--
ALTER TABLE `tb_kegiatan_lembur`
  MODIFY `id_kegiatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `tb_lembur`
--
ALTER TABLE `tb_lembur`
  MODIFY `id_lembur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `tb_logbook`
--
ALTER TABLE `tb_logbook`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=282;

--
-- AUTO_INCREMENT for table `tb_lokasi`
--
ALTER TABLE `tb_lokasi`
  MODIFY `lokasi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `tb_mutasi_barang`
--
ALTER TABLE `tb_mutasi_barang`
  MODIFY `mutasi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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
  MODIFY `pengajuan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `tb_pengalaman`
--
ALTER TABLE `tb_pengalaman`
  MODIFY `id_pengalaman` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_penyerahan`
--
ALTER TABLE `tb_penyerahan`
  MODIFY `penyerahan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT for table `tb_perbaikan_barang`
--
ALTER TABLE `tb_perbaikan_barang`
  MODIFY `perbaikan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tb_remote`
--
ALTER TABLE `tb_remote`
  MODIFY `id_remote` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

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
