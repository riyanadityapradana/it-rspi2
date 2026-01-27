-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 27, 2026 at 02:58 AM
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
  `jenis_barang` enum('Komputer & Laptop','Komponen Komputer & Laptop','Printer & Scanner','Komponen Printer & Scanner','Komponen Network','Kamera & Aksesoris') NOT NULL,
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
(10, 4, 'Solution Digital Persona U are U 4500 Free SDK', 'Komponen Komputer & Laptop', 'SG20E17377', '', 1, 0.00, '- PC based\r\n- Need Komputer\r\n- Interface : USB Kabel\r\n- Free SDK : VB6, C++,C#, Java dan Linux', '2025-09-12', 'baru', 3, 'untuk mentes absensi SIMRS dan untuk cadangan mengganti finger bpjs di fo ralan', 'barang_695ded3303663.jpg'),
(11, NULL, 'Laptop lenovo thinkpad', 'Komputer & Laptop', '-', '192.168.1.104', 1, 0.00, 'Intel® Core™ i5-8350U CPU 1.70GHz\r\nRAM 16 GB, Stroge 238 GB, Stystem 64-bit, Sistem Operasi WIndows 11', '2025-06-11', 'baru', 1, 'Untuk penunjang akreditasi dan untuk acara agenda2 rapat kedepannya', ''),
(12, 3, 'POWER SUPLLY EZMAX 600W RGB 80 PLUS', 'Komponen Komputer & Laptop', 'T2521531', '', 1, 280000.00, 'Power Supply Varro Ezmax 600W RGB 80+ 8pin • Silent Fan 120mm • Optimum power output • Ultra High Efficiency • Built-in 120mm with RGB fan • 1 x 20 + 4 pin Motherboard • 1 x P4 + 4 pin CPU +12V • 1 x P6 + 2 pin PCI-E • 4 x Sata • 2 x Molex Garansi Resmi : 2 Tahun Spesifikasi: -Brand : Varro Prime -Capacity : 600 Watt -Version : Standard ATX -80+ -Input Current : 5A -Voltage : 230V -Frekuensi : 50-60Hz -Case Colour : Black -Fan Size : 12cm -Fan Colour : Rainbow RGB LED -CPU Cable : 8-Pin (452mm) Isi Paket: 1 X PSU 1 X Kabel Power', '2025-07-16', 'baru', 1, 'Untuk komputer Hadi IT', 'barang_68ca3e5015b59'),
(13, 8, 'ADAPTOR LCD/LED MONITOR LG', 'Komponen Komputer & Laptop', '-', '', 1, 70000.00, 'Adaptor LCD/LED Monitor LG 19V - 0,84A Original', '2025-06-21', 'baru', 1, 'Untuk Monitor Rizky IT karna punya dia di pasang di mba shobah Gizi', ''),
(14, 9, 'RAM 8 GB DDR 4 Merek KingSton dan V-Gen', 'Komponen Komputer & Laptop', '-', '', 2, 0.00, '-', '2025-07-03', 'baru', 1, 'Upgrade PC Desktop (Unit CPU / PC Rakitan) punya riyan it', ''),
(15, 10, 'V-GeN SSD 128 GB', 'Komponen Komputer & Laptop', '-', '', 1, 0.00, 'SSD 128GB V-GeN\r\n\r\nCapacity : 128GB\r\nDimensi : 100 x 70 x 6 mm\r\nSpeed : Read up to 510 MB/s\r\nWrite up to 410 MB/s\r\nInterface : SATA 3 - 6 GB/s\r\nForm Factor : 2.5 inch\r\nWarranty : 3 years one to one replacement\r\nType : Internal Storage\r\nSupported : UDMA Mode 6\r\nTRIM Support : Yes (Requires OS Support)\r\nGarbage Collection : Yes\r\nS.M.A.R.T : Yes\r\nWrite Cache : Yes\r\nHost Protect Area : Yes\r\nAPM : Yes\r\nNCQ : Yes\r\n48-Bit : Yes\r\nSecurity : AES 256-Bit Full Disk Encryption (FDE)\r\nTCG/Opal V2.0 , Encryption Drive (IEEE1667)\r\nVolume : +/- 20 gr', '2025-07-03', 'baru', 1, 'Mengganti SSD karna rusak bekas di pakai buat AKRE PC Desktop (Unit CPU / PC Rakitan) punya riyan it', ''),
(16, NULL, 'POWER SUPLLY EZMAX 600W RGB 80 PLUS', 'Komponen Komputer & Laptop', 'T2513680', '', 1, 0.00, 'Power Supply Varro Ezmax 600W RGB 80+ 8pin • Silent Fan 120mm • Optimum power output • Ultra High Efficiency • Built-in 120mm with RGB fan • 1 x 20 + 4 pin Motherboard • 1 x P4 + 4 pin CPU +12V • 1 x P6 + 2 pin PCI-E • 4 x Sata • 2 x Molex Garansi Resmi : 2 Tahun Spesifikasi: -Brand : Varro Prime -Capacity : 600 Watt -Version : Standard ATX -80+ -Input Current : 5A -Voltage : 230V -Frekuensi : 50-60Hz -Case Colour : Black -Fan Size : 12cm -Fan Colour : Rainbow RGB LED -CPU Cable : 8-Pin (452mm) Isi Paket: 1 X PSU 1 X Kabel Power', '2025-07-16', 'baru', 15, 'Untuk Mengganti komputer farmasi di ralan yang PSU nya konslet', 'barang_68ca3e3937268'),
(17, 11, 'Webcame Logitech C270 HD 720p', 'Kamera & Aksesoris', '-', '', 1, 320000.00, 'Menggunakan webcam C270 anda akan mendapatkan panggilan video HD 720p dan foto 3 Mega-pixel. Built-in mikrofonnya menggunakan teknologi RightSound yang menghasilkan percakapan yang jernih tanpa noise latar belakang yang mengganggu. Dalam cahaya remang-remang C270 secara otomatis akan menyesuaikan gambar menjadi lebih baik berkat RightLight teknologi. Mendukung aplikasi Skype, Google Hangouts, Yahoo Messenger dan aplikasi pesan instan popular lainnya. Sistem operasi : Windows XP (SP2 atau lebih baru), Windows vista, Windows 7 (32 bit atau 64 bit), Windows 8 dan Windows10. Spesifikasi Teknik : Panggilan video HD (1280 x 720 piksel) dengan sistem yang direkomendasikan. Perekaman video: Hingga 1280 x 720 piksel. Foto: Hingga 3,0 megapiksel (ditingkatkan menggunakan software). Mikrofon bawaan dengan teknologi Logitech RightSound. Bersertifikat Hi-Speed USB 2.0 (direkomendasikan). Klip universal cocok dengan berbagai laptop, monitor LCD atau CRT. Dimensi kemasan : Tinggi x Lebar x Tebal (cm) : 21 x 16 x 9. Isi Kemasan : - Webcam dengan kabel sepanjang 150 cm. - Dokumentasi pengguna.', '2025-03-03', 'baru', 4, 'Untuk penunjang Akreditasi dan kegiatan rapat menggunakan zoom dan lainnya', 'barang_68c7d219032d0'),
(21, NULL, 'Monitor Dell 20\"', 'Komponen Komputer & Laptop', '-', '', 1, 0.00, '-', '2025-09-17', 'bekas', 20, 'Punya Mas Hadi IT', 'barang_68ca2bbf44e8e'),
(22, 13, 'Uninterruptible Power Systems (UPS)', 'Komponen Komputer & Laptop', '-', '', 1, 1.00, '-', '2025-05-07', 'baru', 1, 'Untuk Komputer Riyan IT', 'barang_68ca535099e9b.jpeg'),
(23, NULL, 'PC Desktop (Unit CPU / PC Rakitan)', 'Komputer & Laptop', '-', '192.168.1.222', 1, 1.00, 'Intel® Core™ i5-14400F (16CPUs),~2.5 GHz\r\nRAM 16 GB, Stystem 64-bit, Sistem Operasi WIndows 11 Pro Education', '2023-01-01', 'baru', 10, 'Penempatan di Radiologi dalam samping Lab', 'barang_695ded7d30a7d.jpg'),
(24, NULL, 'PC DELL Desktop (Unit CPU / PC Rakitan)', 'Komputer & Laptop', '-', '192.168.1.234', 1, 1.00, 'Merek : DELL\r\nIntel® Core™ i5-9500 CPU @ 3.00 GHz(6CPUs),~3.0 GHz\r\nRAM 8 GB, Stystem 64-bit, Sistem Operasi WIndows 11 Pro', '2023-01-01', 'baru', 10, 'Penempatan di Radiologi dalam samping Lab', 'barang_695ded92611c7.jpg'),
(25, NULL, 'PC Desktop (Unit CPU / PC Rakitan)', 'Komputer & Laptop', '-', '192.168.1.134', 1, 1.00, '12th Gen Intel® core™ i5-12400 (12 CPUs), ~2.5GHz\nRAM 16 GB, Stystem 64-bit, Sistem Operasi WIndows 11 Home', '2025-03-03', 'baru', 1, 'Penempatan di Ruang IT (PC UTAMA ALI)', 'barang_68cba80f33b7b.jpeg'),
(26, 13, 'Uninterruptible Power Systems (UPS)', 'Komponen Komputer & Laptop', '-', '', 1, 1.00, '-', '2025-05-07', 'baru', 1, 'Untuk Komputer ALI IT', 'barang_695dedae3ad12.jpg'),
(27, NULL, 'Printer Epson L3210', 'Printer & Scanner', 'XAGKF68723', '', 1, 0.00, 'Epson L3210 adalah printer inkjet dengan teknologi EcoTank yang memungkinkan penggunaan tinta dalam jumlah besar. Resolusinya mencapai 5760 x 1440 dpi, jadi hasil cetaknya tajam, baik untuk dokumen hitam-putih maupun foto berwarna. Kecepatan cetaknya juga cukup oke, yaitu 10 ipm (halaman per menit) untuk hitam-putih dan 5 ipm untuk warna. Printer ini mendukung ukuran kertas A4, legal, hingga foto 4R, plus kompatibel dengan Windows dan Mac OS.\r\n\r\nBobotnya hanya 3,9 kg dengan dimensi 375 x 347 x 179 mm, jadi tidak makan tempat di meja kerja. Sayangnya, konektivitasnya masih mengandalkan USB 2.0, belum ada Wi-Fi seperti seri yang lebih tinggi.\r\n\r\nMeski begitu, untuk penggunaan sederhana, spesifikasi ini sudah cukup memadai. Dengan garansi hingga 2 tahun atau 30.000 cetakan, kamu juga bisa lebih tenang soal perawatan.', '2025-09-20', 'baru', 16, 'Diterima langsung oleh karu farmasi (Rollah), dan printer Epson L121 di tarik ke Unit IT', 'barang_68ce1c5fad7ca.jpeg'),
(28, NULL, 'Printer Epson L121', 'Komponen Printer & Scanner', 'X9LU382924', '', 1, 1.00, '-', '2025-09-20', 'rusak', 1, 'Bekas dari Farmasi rawat inap, karna di farmasi ranap sudah ada yang baru', 'barang_68ce1d9813869.jpeg'),
(29, NULL, 'Uninterruptible Power Systems (UPS)', 'Komponen Komputer & Laptop', '1B1QP2400866', '', 1, 0.00, 'Type : CE600D\r\nTegangan Input : 220 Vac\r\nFrekuensi Input : 50 Hz', '2025-09-09', 'baru', 5, 'Sudah di pasang dan di terima oleh karu kecubung, di pasang untuk kegunaan backup komputer & printer', 'barang_68db3563ed635.jpeg'),
(30, NULL, 'PC Desktop (Unit CPU / PC Rakitan)', 'Komputer & Laptop', '-', '192.168.30.167', 1, 0.00, 'Intel® core™ i3-4170 CPU @ 3.70GHz (4 CPUs), ~3.7GHz\r\nRAM 16 GB, Stystem 64-bit, Sistem Operasi WIndows 11 Home', '2025-09-09', 'bekas', 5, 'Ini komputer Mas Malik dan diserahkan ke kecubung karna permintaan dari karu kecubung ingin penambahan 1 pc', 'barang_68db375e0d9e8.jpeg'),
(31, NULL, 'Uninterruptible Power Systems (UPS)', 'Komponen Komputer & Laptop', '1B1QP2401638', '', 1, 0.00, 'Type : CE600D\nTegangan Input : 220 Vac\nFrekuensi Input : 50 Hz', '2025-09-09', 'baru', 6, 'Untuk membackup komputer dan printer dan diterima langsung oleh karu yakut c (bu atul) saat pemasangan', 'barang_68db46df7fcc5.jpeg'),
(32, NULL, 'Uninterruptible Power Systems (UPS)', 'Komponen Komputer & Laptop', '1B1QP2401663', '', 1, 0.00, 'Type : CE600D\r\nTegangan Input : 220 Vac\r\nFrekuensi Input : 50 Hz', '2025-09-09', 'baru', 22, 'di pasang di komputer dokter IGD', 'barang_68dcdded1528a.jpeg'),
(33, NULL, 'Uninterruptible Power Systems (UPS)', 'Komponen Komputer & Laptop', '1B1QP2400837', '', 1, 0.00, 'Type : CE600D\r\nTegangan Input : 220 Vac\r\nFrekuensi Input : 50 Hz', '2025-09-09', 'baru', 15, 'Sudah diserahkan oleh petugas farmasi ralan', 'barang_68dcde9fcd83d.jpeg'),
(34, NULL, 'RAM 8 GB DDR 3 Merek VENOMRX', 'Komponen Komputer & Laptop', 'T2301835', '', 1, 0.00, 'Product Name 8GB PC 12800\nKategori Memory RAM Komputer\nBrand VenomRX\nTahun Rilis 2012\nHardware\nMemory Module DIMM\nCompatible Device Desktop\nMulti-Channel Dual Channel Channel\nMemory\nKapasitas Memori 8 GB\nFrekuensi 1600 MHz\nMemory Type DDR3', '2025-09-10', 'baru', 22, 'di pasang di komputer Dokter IGD karna yang dulu cuman 4 GB saja RAM nya', 'barang_68dce0d0ebe54.jpeg'),
(35, NULL, 'Keyboard Logitech K120', 'Komponen Komputer & Laptop', '2320MR11CC48', '', 1, 1.00, 'https://www.logitech.com/en-hk/products/keyboards/k120-usb-standard-computer.920-002584.html', '2025-10-03', 'baru', 15, 'langsung di diganti karna yang lama rusak (info : mas hadi)', 'barang_68df263cd1fb7.jpeg'),
(36, NULL, 'SSD ADATA SU650 128GB', 'Komponen Komputer & Laptop', '-', '', 1, 1.00, '-', '2025-10-01', 'bekas', 22, 'SSD Bekas punya Dito IT  dipasang di komputer dokter IGD karna permasalahan yang sebelumnya sering mati sendiri dan bluscreen', 'barang_68df29dd1bb2d.jpg'),
(37, 16, 'SanDisk 32 GB', 'Komponen Komputer & Laptop', '-', '', 1, 68000.00, '-', '2025-10-07', 'baru', 1, 'Untuk Instal Ulang windows', 'barang_1759826299_8183.jpeg'),
(38, 16, 'SanDisk 32 GB', 'Komponen Komputer & Laptop', '-', '', 1, 68000.00, '-', '2025-10-07', 'baru', 1, 'Untuk keperluan Bakcup data unit IT', 'barang_1759826327_7021.jpeg'),
(39, NULL, 'POWER SUPLLY EZMAX 600W RGB 80 PLUS', 'Komponen Komputer & Laptop', 'T2527590', '', 1, 280000.00, 'Power Supply Varro Ezmax 600W RGB 80+ 8pin • Silent Fan 120mm • Optimum power output • Ultra High Efficiency • Built-in 120mm with RGB fan • 1 x 20 + 4 pin Motherboard • 1 x P4 + 4 pin CPU +12V • 1 x P6 + 2 pin PCI-E • 4 x Sata • 2 x Molex Garansi Resmi : 2 Tahun Spesifikasi: -Brand : Varro Prime -Capacity : 600 Watt -Version : Standard ATX -80+ -Input Current : 5A -Voltage : 230V -Frekuensi : 50-60Hz -Case Colour : Black -Fan Size : 12cm -Fan Colour : Rainbow RGB LED -CPU Cable : 8-Pin (452mm) Isi Paket: 1 X PSU 1 X Kabel Power', '2025-10-10', 'baru', 23, 'Upgrade PC Lama (Untuk Karyawan RM Baru)', 'barang_68e8817d59e07.jpeg'),
(40, NULL, 'SSD ADATA SU650 256GB', 'Komponen Komputer & Laptop', '404021078462', '', 1, 0.00, '-', '2025-10-10', 'baru', 23, 'Upgrade PC Lama (Untuk Karyawan RM Baru)', 'barang_68e881dbbc12f.jpeg'),
(41, NULL, 'RAM 8 GB DDR 3 Merek VENOMRX', 'Komponen Komputer & Laptop', 'T2301841', '', 1, 0.00, 'Product Name 8GB PC 12800\r\nKategori Memory RAM Komputer\r\nBrand VenomRX\r\nTahun Rilis 2012\r\nHardware\r\nMemory Module DIMM\r\nCompatible Device Desktop\r\nMulti-Channel Dual Channel Channel\r\nMemory\r\nKapasitas Memori 8 GB\r\nFrekuensi 1600 MHz\r\nMemory Type DDR3', '2025-10-10', 'baru', 23, 'Upgrade PC Lama (Untuk Karyawan RM Baru)', 'barang_68e882893ba13.jpeg'),
(42, NULL, 'SSD ADATA SU650 512GB', 'Komponen Komputer & Laptop', 'SN4P1121306844', '', 1, 482000.00, '-', '2025-10-10', 'baru', 20, 'yang dulu pakai hardisk kondisinya yaitu hardisknya sering tidak terbaca saat menyalakan komputer dan atau saat berhasil masuk windows kadang kembali mati , Indekasi Hardisk sudah melemah/Rusak', 'barang_68e8aade575b1.jpeg'),
(43, NULL, 'Hardisk SEGATE 500GB', 'Komponen Komputer & Laptop', 'SN5W6DT65JY', '', 1, 0.00, '-', '2021-11-11', 'rusak', 20, 'INI BEKAS PC PONEK, KONDISI HARDISK LEMAH KARNA SERING TIDAK TERBACA SAAT NYALAKAN PC, DAN KADANG SERING MATI (BLUESCREEN)', 'barang_68e9a907b0139.jpeg'),
(44, NULL, 'Logitech MK120 Plug and Play USB Combo', 'Komponen Komputer & Laptop', 'SN2506LOA0E579', '', 1, 188000.00, 'Desain anti tumpahan cairan. Jangan benamkan keyboard ke dalam cairan.\r\n\r\nTinggi keyboard yang dapat disesuaikan\r\n\r\nNumber pad 10 tombol\r\n\r\nLampu indikator caps lock\r\n\r\nLampu indikator num lock\r\n\r\nMaksimal 10 juta keystroke (tidak termasuk tombol number lock)\r\n\r\nJenis tombol: Deep profile\r\n\r\nMouse\r\n\r\nTeknologi sensor: Penelusuran optik\r\n\r\nJumlah tombol: 3 (Klik Kiri/Kanan, Klik Tengah)\r\n\r\nScrolling: line-by-line\r\n\r\nScroll Wheel: Ya, optik\r\n\r\nKeberlanjutan\r\n\r\nPlastik mouse: 72% bahan Post Consumer Recycled (PCR)\r\n\r\nPlastik keyboard: 54% bahan Post Consumer Recycled (PCR)', '2025-10-13', 'baru', 23, 'untuk keperluan komputer yang di pasang karyawan RM Baru', 'barang_68ec60649c3b2.jpeg'),
(45, NULL, 'Monitor PC DELL', 'Komponen Komputer & Laptop', '-', '', 1, 0.00, '-', '2025-07-09', 'bekas', 23, 'Monitor Untuk Komputer Mas HADI', 'barang_68ec62f6c0351.jpeg'),
(46, NULL, 'PREMIUM INK (TINTA EPSON HITAM)', 'Komponen Printer & Scanner', '-', '', 4, 0.00, '-', '2025-10-15', 'baru', 1, 'Stok IT', 'barang_68f048ada465a.jpeg'),
(68, NULL, 'RAM 4 GB DDR 3 Merek KingSton', 'Komponen Komputer & Laptop', 'T2322042', '', 1, 1.00, '-', '2025-10-18', 'baru', 21, 'Upgrade RAM + Memperbaiki karna yang dulu error', 'barang_68f2f71bc57a9.jpeg'),
(69, NULL, 'Printer Epson L121', 'Printer & Scanner', 'X9LU666594', '', 1, 0.00, 'https://www.bhinneka.com/epson-ecotank-l121-sku3337608496#attr=369083,369084', '2025-10-21', 'baru', 21, 'Pengajuan Printer baru dari kasir , yang dulu di kembalikan ke unit IT', 'barang_68f741102637a.jpeg'),
(70, NULL, 'Printer Epson LX-310', 'Printer & Scanner', 'Q7FYJ98905', '', 1, 1.00, '-', '2025-10-21', 'baru', 21, 'Pengajuan dari kasir karna epson LX yang dulu sudah tidak layak pakai dan sering nyangkut kertasnya', 'barang_68f742415e972.jpeg'),
(71, NULL, 'Printer Epson L220', 'Printer & Scanner', 'WN5P170005', '', 1, 1.00, '-', '2025-10-21', 'bekas', 1, 'Printer ini bekas di kasir ditarik ke IT dulu karna di kasir sudah ada yang baru yaitu Epson L121, Kondisi Printer : Tinta banjir dan ???', 'barang_68f7433887191.jpeg'),
(72, NULL, 'Printer Epson LX-310', 'Printer & Scanner', 'Q7FY072147', '', 1, 0.00, '-', '2025-10-21', 'bekas', 1, 'Ini bekas di kasir lalu diserahkan dahulu ke unit IT karna di kasir dapat printer Epson LX-310 baru di tanggal 21/10/25', 'barang_68f744a101700.jpeg'),
(73, NULL, 'RAM 4 GB DDR 4 Merek VENOMRX', 'Komponen Komputer & Laptop', 'T2304775 DAN T2304772', '', 2, 0.00, '-', '2025-07-03', 'bekas', 1, 'RAM bekas komputer Dito/Riyan , Kondisi masih perkiraan 60%', 'barang_68f8802a6fccf.jpeg'),
(74, 22, 'UGREEN Kabel USB Type C Fast Charging 3A 1m 2m 3m For Samsung Oppo Vivo Xiaomi Realmi', 'Komponen Komputer & Laptop', 'KUTC2', '', 1, 10000.00, 'Ganti Merek yaitu PROFFTECH', '2025-11-03', 'baru', 1, 'Untuk Acara Bu Haji + Backup IT kalo ada acara-acara', 'barang_1762130424_5394.jpeg'),
(75, NULL, 'Ugreen Adapter Ethernet USB 3.0 to LAN RJ45', 'Komponen Komputer & Laptop', '-', '', 1, 0.00, 'Deskripsi :\r\n- Merek : UGREEN\r\n- Model : 50922\r\n- Material : Aluminium Case\r\n\r\n- Interface Type:USB to EthernetLan\r\n- Transmission Speed:600 Mbps\r\n- Transmission Rate:1000M\r\n- Transmission Rate:10/100/1000Mbps\r\n- Type:Gigabit Ethernet\r\n- Adapter Socket:RJ45', '2025-11-03', 'baru', 1, 'dipakai mas ali pas di bulan desember', 'barang_69085547279c2.jpeg'),
(76, NULL, 'SSD ADATA SU650 512GB', 'Komponen Komputer & Laptop', '4P1121306832', '', 1, 1.00, '-', '2025-11-04', 'baru', 3, 'Untuk Upgrade Komputer FO Ralan 2 dikarenakan keluhan nya sering takang dan setelah di cek memang benar ada kendala di haridisk', 'barang_690975b5a581f.jpeg'),
(77, NULL, 'USB 3.0 to Gigabit Ethernet Adapter', 'Komponen Network', 'Model : CM209 | P/N:50922', '', 1, 200000.00, 'Connector : USB-A 3.0, RJ45 (mOMbps)\r\nInput : 5.OV 0.9A Max\r\nCompatible Systems : macOS/Windows/Linux/Android/iOS\r\nNote : Driver-free for Windows 8/10/11, mac OS systems. However, driver\r\ninstallation is needed for Windows XP and Windows 7.', '2025-11-06', 'baru', 24, 'Untuk penyambung LAN ke Laptop Poli , yang dulu infonya rusak', 'barang_690d646ddef39.jpeg'),
(78, NULL, 'DEEPCOOL CK-11509', 'Komponen Komputer & Laptop', '107369', '', 1, 56000.00, '-', '2025-11-11', 'baru', 3, 'Untuk mengganti Kipas proccesor komputer asuransi (FO 2) karna indikasi kipas lama sudah tidak berputar lagi mengakibatkan pc panas dan overhead lalu menyebabkan mati-mati dan lag', 'barang_6913e6094fcec.jpeg'),
(79, NULL, 'RAM 8 GB DDR 3 KVR16N11/8 Merek KingSton', 'Komponen Komputer & Laptop', 'T2530438', '', 1, 221000.00, '-', '2025-11-11', 'baru', 3, 'Untuk Upgrade performa komputer asuransi (FO 2), jadi sekarang kapasitas RAM PC 10 GB (8+2)', 'barang_6913e6fad90c7.jpeg'),
(80, 7, 'Fingerprint Sidik Jari Fingerspot U are U 4500 USB PC Based', 'Komponen Komputer & Laptop', 'TH20E12502', '', 1, 0.00, '-', '2025-10-23', 'baru', 1, 'Untuk menunjang keperluan APM Baru dan nanti nya akan di pakai jika app APM baru sudah selesai', 'barang_1762912215_6676.jpeg'),
(81, NULL, 'RAM 8 GB DDR 3 KVR16N11/8 Merek KingSton', 'Komponen Komputer & Laptop', '-', '', 1, 0.00, '-', '2025-11-11', 'bekas', 1, 'Ram Bekas FO Ralan (2) yang biasa di pakai buat asuransi, Kondisi Perkiraan 60% (Masih bisa di pakai)', 'barang_6916a38c49ce4.jpeg'),
(82, NULL, 'SSD Varro Evolution 512GB', 'Komponen Komputer & Laptop', 'T2225097', '', 1, 0.00, '-', '2025-11-12', 'rusak', 3, 'Ini SSD dari komputer Ralan 4 Rusaknya karna SSD tidak terbaca lagi saat menyalakan komputer dan di tes menggunakan external juga tidak terbaca', 'barang_6916a660553e6.jpeg'),
(83, NULL, 'Tripod Kamera/Webcame Merek NeePho', 'Kamera & Aksesoris', '-', '', 1, 150000.00, '-', '2025-11-14', 'baru', 4, 'Untuk kamera persetujuan ranap', 'barang_6916aa46b8ca7.jpeg'),
(84, NULL, 'Logitech MK120 Keyboard dan Mouse + Play USB Combo', 'Komponen Komputer & Laptop', '-', '', 1, 0.00, '-', '2025-04-01', 'baru', 24, 'Ini di awalnya ampun rizky di bawa dan diserahkan ke Poi Mata', 'barang_692f8dfee52e1.jpeg'),
(85, NULL, 'Keyboard REXUX RX-KM8 Wireless', 'Komponen Komputer & Laptop', '-', '', 1, 0.00, 'Teknologi: Nano USB\r\n\r\nJumlah tombol: 106 tombol\r\n\r\nTegangan: DC 1,9V 3.3V\r\n\r\nArus: < 10MAh\r\n\r\nDaya tahan tombol: 10 juta klik\r\n\r\nBerat: 480 gram\r\n\r\nDimensi: 442x137x31mm', '2025-12-02', 'bekas', 1, 'Bekas Poli Mata (Sepaket dengan Mouse)', 'barang_692f8f212017e.jpeg'),
(86, NULL, 'Mouse REXUX RX-KM8 Wireless', 'Komponen Komputer & Laptop', '-', '', 1, 0.00, 'Material: Plastik ABS\r\n\r\nSensitivitas: 1600DPI\r\n\r\nJumlah tombol: 104 tombol\r\n\r\nMode kerja: Nirkabel 2,4 GHz\r\n\r\nDaya tahan tombol: 1 juta kali\r\n\r\nSensor: Optik\r\n\r\nChipset: PIXART 3065\r\n\r\nTegangan: DC 1,9V 3,3V\r\n\r\nArus:< 10MAh\r\n\r\nDimensi: 108x60x26 mm\r\n\r\nBerat: 50 gram\r\n\r\nUSB: Nano USB\r\n\r\nTegangan USB: DC 4.77V 5,25V', '2025-12-02', 'bekas', 1, 'Bekas Poli Mata', 'barang_692f8fa0b6b69.jpeg'),
(87, NULL, 'Logitech MK120 Keyboard dan Mouse + Play USB Combo', 'Komponen Komputer & Laptop', '2448LOK0A059', '', 1, 0.00, 'Keyboard\r\n\r\nDesain anti tumpahan cairan. Jangan benamkan keyboard ke dalam cairan.\r\n\r\nTinggi keyboard yang dapat disesuaikan\r\n\r\nNumber pad 10 tombol\r\n\r\nLampu indikator caps lock\r\n\r\nLampu indikator num lock\r\n\r\nMaksimal 10 juta keystroke (tidak termasuk tombol number lock)\r\n\r\nJenis tombol: Deep profile\r\n\r\nMouse\r\n\r\nTeknologi sensor: Penelusuran optik\r\n\r\nJumlah tombol: 3 (Klik Kiri/Kanan, Klik Tengah)\r\n\r\nScrolling: line-by-line\r\n\r\nScroll Wheel: Ya, optik\r\n\r\nKeberlanjutan\r\n\r\nPlastik mouse: 72% bahan Post Consumer Recycled (PCR)\r\n\r\nPlastik keyboard: 54% bahan Post Consumer Recycled (PCR)', '2025-12-03', 'baru', 20, 'Untuk Ika IT Keyboardnya dan mousenya untuk riyan', 'barang_692f95b64df4d.jpeg'),
(88, NULL, 'SWITCH HUB TP-LINK 5 PORT GIGABIT', 'Komponen Network', '22472M6011722', '', 1, 135000.00, 'Port: 5 x 10/100/1000Mbps RJ45 (Auto Negotiation, Auto MDI/MDIX).\r\nTipe: Unmanaged Switch (Plug and Play, tanpa perlu konfigurasi).\r\nFitur Utama:\r\nGreen Technology: Menghemat daya hingga 85%.\r\nIEEE 802.3X Flow Control: Mencegah paket hilang saat buffer penuh.\r\nMDI/MDIX Otomatis: Tidak perlu kabel crossover.\r\nKinerja: Mendukung transfer file besar dengan kecepatan tinggi.\r\nDesain: Fanless (tanpa kipas) untuk operasi hening, bisa dipasang di dinding (wall-mount).\r\nBodi: Seringkali berbahan metal (besi) untuk daya tahan dan disipasi panas lebih baik.\r\nIndikator LED: Menunjukkan status koneksi dan aktivitas port', '2025-12-26', 'baru', 1, 'Cadangan Buat Stok', 'barang_695c8a5770cef.jpeg'),
(89, NULL, 'SWITCH HUB TP-LINK 5 PORT GIGABIT', 'Komponen Network', '2253629008719', '', 1, 135000.00, 'Port: 5 x 10/100/1000Mbps RJ45 (Auto Negotiation, Auto MDI/MDIX).\r\nTipe: Unmanaged Switch (Plug and Play, tanpa perlu konfigurasi).\r\nFitur Utama:\r\nGreen Technology: Menghemat daya hingga 85%.\r\nIEEE 802.3X Flow Control: Mencegah paket hilang saat buffer penuh.\r\nMDI/MDIX Otomatis: Tidak perlu kabel crossover.\r\nKinerja: Mendukung transfer file besar dengan kecepatan tinggi.\r\nDesain: Fanless (tanpa kipas) untuk operasi hening, bisa dipasang di dinding (wall-mount).\r\nBodi: Seringkali berbahan metal (besi) untuk daya tahan dan disipasi panas lebih baik.\r\nIndikator LED: Menunjukkan status koneksi dan aktivitas port', '2025-12-26', 'baru', 1, 'Buat Cadangan Stok', 'barang_695c8ad62d4f5.jpeg'),
(90, NULL, 'MikroTik ROUTERBOARD RB450GX4', 'Komponen Network', 'T2533984', '', 1, 1885000.00, 'Routerboard RB450Gx4 (716MHz Quad Core CPU, 1 GB DDR RAM, 512MB NAND Storage) dengan RouterOS (Level 5) 5 (lima) buah port gigabit 10/100/1000 slot mikro-SD. Tidak bisa dipasangkan wireless card. Sudah termasuk 1 buah adaptor 24 Volt.', '2026-01-02', 'baru', 1, 'dI peruntukan di pasien Lt 2 karna yang dulu  rusak Info : mas Hadi', 'barang_695c8cf910832.jpeg'),
(91, NULL, 'Webcame Logitech C270 HD 720p', 'Komponen Komputer & Laptop', '2510APN9N729', '', 1, 0.00, '', '2025-12-14', 'baru', 1, 'Untuk Cadangan dan diperentukan utama untuk agenda rapat online', 'barang_695c8e66b3422.jpeg'),
(92, NULL, 'Ugreen Adapter Ethernet USB 3.0 to LAN RJ45', 'Komponen Network', '-', '', 1, 0.00, 'Deskripsi :\r\n- Merek : UGREEN\r\n- Model : 50922\r\n- Material : Aluminium Case\r\n\r\n- Interface Type:USB to EthernetLan\r\n- Transmission Speed:600 Mbps\r\n- Transmission Rate:1000M\r\n- Transmission Rate:10/100/1000Mbps\r\n- Type:Gigabit Ethernet\r\n- Adapter Socket:RJ45', '2025-01-22', 'rusak', 24, 'Info tidak bisa terhubung lagi jadi ketika di colok ke kabel lan dia tidak terbaca walaupun indikator lannya nyala', 'barang_695c920840ef3.jpg'),
(93, NULL, 'kabel vga 1.5 m / Kabel vga male male 1.5 m / Kabel vga jantan jantan 1.5 m / Cable VGA 1.5 m', 'Komponen Komputer & Laptop', '-', '', 1, 0.00, '- Panjang 1M\r\n\r\n- Warna Hitam\r\n\r\n- Untuk menghubungkan komputer dengan monitor, proyektor, infocus atau\r\n\r\nlayar LCD', '2026-01-02', 'baru', 3, 'untuk minitor FO ralan 2 (Asuransi)', 'barang_695defa626cec.jpg'),
(94, NULL, 'Profftech VGA Cable Male to Male / Kabel VGA / 1.5m', 'Komponen Komputer & Laptop', '-', '', 1, 0.00, '- Connector Type : Standard VGA Male-Male (VGA 3+4)\r\n- Connector : Gold plated\r\n- Terminal Pin : 15 Pin 24k Gold Plated\r\n- Conductor : 30AWG, Pure Copper\r\n- Shielding : Aluminum Foil + Anti-Jamming Materials\r\n- Jacket shell : PVC Environmental Material.', '2026-01-05', 'baru', 9, 'Untuk Komputer Mas Adi ahmad', 'barang_695df0730bfc9.png'),
(95, NULL, 'PC Desktop (Unit CPU / PC Rakitan)', 'Komputer & Laptop', '192.168.2.81', '192.168.2.81', 1, NULL, 'Sistem Operasi\r\n\r\nWindows 11 Pro 64-bit\r\n\r\nVersi: 10.0 (Build 25200)\r\n\r\nBahasa Sistem\r\n\r\nEnglish (Regional Setting: English)\r\n\r\nMotherboard / Sistem\r\n\r\nSystem Manufacturer: To Be Filled By O.E.M.\r\n\r\nSystem Model: B550M-HDV\r\n\r\nBIOS\r\n\r\nVersi BIOS: P3.30\r\n\r\nProsesor\r\n\r\nAMD Ryzen 3 3200G with Radeon Vega Graphics\r\n\r\n4 Core (4 CPUs)\r\n\r\nKecepatan ± 3.60 GHz\r\n\r\nMemori (RAM)\r\n\r\n8192 MB (8 GB RAM)\r\n\r\nGrafis\r\n\r\nRadeon™ Vega Graphics (terintegrasi)\r\n\r\nDirectX\r\n\r\nDirectX Version: 12\r\n\r\nPage File (Virtual Memory)\r\n\r\n5132 MB digunakan\r\n\r\n4558 MB tersedia', '2026-01-14', '-', NULL, NULL, 'barang_696747a603806.jpeg'),
(96, NULL, 'Uninterruptible Power Systems (UPS)', 'Komponen Komputer & Laptop', '1B1QP2500032', '', 1, NULL, 'Type : CE600D\r\nTegangan Input : 220 Vac\r\nFrekuensi Input : 50 Hz', '2026-01-14', '-', NULL, NULL, 'barang_696749cb5ef76.jpeg'),
(97, NULL, 'Uninterruptible Power Supply (UPS)', 'Komponen Komputer & Laptop', '1B1QP2401972', '', 1, NULL, 'Type : CE600D\r\nTegangan Input : 220 Vac\r\nFrekuensi Input : 50 Hz', '2026-01-14', '-', NULL, NULL, 'barang_69674a4e25557.jpeg'),
(98, NULL, 'PC Desktop (Unit CPU / PC Rakitan)', 'Komputer & Laptop', '192.168.1.90', '192.168.1.90', 1, NULL, 'VGA: Intel(R) HD Graphics\r\nOS: Windows 10 32-bit\r\nProcessor: i3-3210CPU @ 3.20GHZ (4 CPUs), ~3.2GHz\r\nRAM : 8 GB + 8 GB (Baru) = 16 GB\r\nDriver sekarang: 10.18.10.3496\r\nModel driver: WDDM 1.3', '2013-01-09', '-', 20, NULL, 'barang_69688df63e5b1.jpeg'),
(100, NULL, 'RAM 8 GB DDR 3 Merek KingSton', 'Komponen Komputer & Laptop', 'T2625004', '', 1, NULL, 'kapasitas 8GB, tipe DDR3, kecepatan 1600MHz (PC12800), tersedia dalam bentuk DIMM (untuk PC Desktop) dan SODIMM (untuk Laptop), serta biasanya menggunakan voltase 1.35V (untuk versi PC3L/low voltage) atau 1.5V', '2026-01-15', '-', NULL, NULL, 'barang_696af19988d30.jpeg'),
(101, NULL, 'POWER SUPLLY EZMAX 600W RGB 80 PLUS', 'Komponen Komputer & Laptop', 'T2527599', '', 1, NULL, 'Power Supply Varro Ezmax 600W RGB 80+ 8pin • Silent Fan 120mm • Optimum power output • Ultra High Efficiency • Built-in 120mm with RGB fan • 1 x 20 + 4 pin Motherboard • 1 x P4 + 4 pin CPU +12V • 1 x P6 + 2 pin PCI-E • 4 x Sata • 2 x Molex Garansi Resmi : 2 Tahun Spesifikasi: -Brand : Varro Prime -Capacity : 600 Watt -Version : Standard ATX -80+ -Input Current : 5A -Voltage : 230V -Frekuensi : 50-60Hz -Case Colour : Black -Fan Size : 12cm -Fan Colour : Rainbow RGB LED -CPU Cable : 8-Pin (452mm) Isi Paket: 1 X PSU 1 X Kabel Power', '2026-01-15', '-', NULL, NULL, 'barang_696af2c617b8b.jpeg'),
(102, NULL, 'PC Desktop (Unit CPU / PC Rakitan)', 'Komputer & Laptop', '1', '1', 1, NULL, 'AMD A4-4000 APU with Radeon HD Graphics\r\nCPU: AMD A4-4000 APU @ 3.0 GHz\r\nRAM: 8 GB\r\nGrafis: Radeon HD (lebih bagus dari Intel HD lama)\r\nMotherboard: Biostar Hi-Fi A55S3\r\nPlatform: Lebih baru dari Core 2 Duo', '2026-01-17', '-', 32, NULL, 'barang_696af44a56e03.jpeg'),
(103, NULL, 'RAM 8 GB DDR 3 Merek KingSton', 'Komponen Komputer & Laptop', 'T2530407', '', 1, NULL, 'kapasitas 8GB, tipe DDR3, kecepatan 1600MHz (PC12800), tersedia dalam bentuk DIMM (untuk PC Desktop) dan SODIMM (untuk Laptop), serta biasanya menggunakan voltase 1.35V (untuk versi PC3L/low voltage) atau 1.5V', '2026-01-17', '-', NULL, NULL, 'barang_696af55b1749e.jpeg');

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
  `status_log` varchar(20) DEFAULT 'Belum'
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
(95, 12, '2026-01-27 08:30:00', 'Persiapan dan Operasional Agenda Rapat Pelatihan BHD Tahap 2', 'Melakukan persiapan agenda acara di aula untuk kegiatan Pelatihan IHT BHD, meliputi penyiapan perangkat dan sarana pendukung acara.', 'Selain persiapan, bertugas sebagai operator selama pelaksanaan kegiatan Pelatihan BHD untuk memastikan acara berjalan dengan lancar.', '2026-01-27 14:28:00', 'Selesai'),
(96, 12, '2026-01-24 09:05:00', 'Kegiatan Akreditasi – Pokja PMKP', 'Melaksanakan tugas akreditasi pada Pokja PMKP. Tindakan yang Dilakukan :\r\n1. Mencari dan mengumpulkan dokumen PPK (Panduan Praktik Klinis).\r\n2. Mencari dan mengumpulkan dokumen CP (Clinical Pathway) sesuai kebutuhan unit.\r\n3.Melakukan pengeditan dan penyesuaian dokumen agar sesuai dengan standar akreditasi yang berlaku.', 'Dokumen PPK dan CP berhasil dikumpulkan dan dilakukan pengeditan awal sebagai persiapan kebutuhan akreditasi PMKP lalu diserahkan ke dr taufiq untuk di periksa di tanggal 26', '2026-01-24 13:09:00', 'Pending'),
(97, 12, '2026-01-24 10:15:00', 'Menindaklanjuti kendala pada sound system di aula, di mana suara lagu atau musik tidak keluar ke speaker saat diputar.', '1. Melakukan pengecekan jalur audio dari perangkat pemutar ke speaker.\r\n2. Melakukan penyesuaian dan pengaturan ulang pada mixer (level volume, input, dan output).\r\n3. Memastikan koneksi kabel audio dan speaker terpasang dengan benar.', 'Sound system aula kembali berfungsi normal dan suara musik dapat keluar melalui speaker dengan baik.', '2026-01-24 11:56:00', 'Selesai'),
(98, 12, '2026-01-24 08:33:00', 'Penerbitan Sertifikat Elektronik E-ICV Vaksinasi Haj', 'Melakukan penginputan lanjutan dan penerbitan sertifikat elektronik E-ICV pada sistem SINKARKES untuk pasien umum yang telah menjalani vaksinasi haji oleh dokter dan perawat.', 'Penerbitan E-ICV dilakukan berdasarkan data vaksinasi yang telah diinput oleh perawat dan dokter. Pada hari ini, dilakukan proses pengesahan E-ICV untuk 1 pasien UMUM yang akan melakukan keberangkatan haji, meliputi verifikasi data pasien dan memastikan sertifikat elektronik berhasil diterbitkan di sistem.', '2026-01-24 08:57:00', 'Selesai');

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
(33, 'PL Syaraf', 'Poli Syaraf Dr. Made');

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
(11, 102, 20, 32, '2026-01-17', 12, 'Ini bekas Ponek di pindah ke liza TU karna mengalami mati-mati terus komputer nya sekalian upgrade karna komputer nya dulu sudah sangat tua dengan spek : Intel Core 2 Duo E8400,\r\nCPU: Intel Core 2 Duo E8400 @ 3.0 GHz (generasi lama banget),\r\nRAM: 8 GB,\r\nGrafis: kemungkinan Intel HD lawas,\r\nPlatform: Intel jadul (sekitar 2008–2009),');

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
(4, 12, 'Solution Digital Persona U are U 4500 Free SDK', 'Unit IT', 0, 1200000.00, 'untuk mentes absensi SIMRS dan untuk cadangan mengganti finger bpjs di fo ralan', 'disetujui', '2025-07-28', '2025-07-29'),
(5, 12, 'SSD M2 128 GB', 'Unit IT', 1, 200000.00, 'Upgrade Mini PC untuk Keperluan APM BPJS', 'disetujui', '2025-09-12', '2025-09-12'),
(6, 12, 'RAM 8 GB (Untuk Mini PC)', 'Unit IT', 1, 150000.00, 'Upgrade Mini PC untuk Keperluan APM BPJS', 'disetujui', '2025-09-12', '2025-09-12'),
(7, 12, 'Fingerprint Sidik Jari Fingerspot U are U 4500 USB PC Based', 'Unit IT', 0, 1500000.00, 'untuk Keperluan untuk mentes website APM BPJS dan nantinya akan di pakai buat APM BPJS Juga di bawah, jika tidak buat  cadangan di FO karna di FO ralan juga banyak mulai bermasalah', 'disetujui', '2025-09-12', '2025-09-12'),
(8, 12, 'ADAPTOR LCD/LED MONITOR LG', 'Unit IT', 0, 60000.00, 'Mengganti punya rizky', 'disetujui', '2025-06-09', '2025-06-20'),
(9, 12, 'RAM 8 GB DDR 4 Merek KingSton dan V-Gen', 'Unit IT', 0, 250000.00, 'Upgrade PC Desktop (Unit CPU / PC Rakitan) punya riyan it', 'disetujui', '2025-06-17', '2025-06-20'),
(10, 12, 'V-GeN SSD 128 GB', 'Unit IT', 0, 160000.00, 'Upgrade PC Desktop (Unit CPU / PC Rakitan) punya riyan it', 'disetujui', '2025-06-17', '2025-06-20'),
(11, 12, 'Webcame Logitech C270 HD 720p', 'Unit IT', 0, 320000.00, 'Untuk penunjang Akreditasi dan kegiatan rapat-rapat ketika Zoom', 'disetujui', '2025-02-18', '2025-02-24'),
(13, 12, 'Uninterruptible Power Systems (UPS)', 'Unit IT', 3, 1.00, 'Untuk semua komputer unit staff IT', 'disetujui', '2025-04-16', '2025-04-28'),
(15, 12, 'Kabel Duct TC5  Protector Pelindung Kabel', 'Unit IT', 8, 25000.00, 'Untuk merapikan kabel lan di counter lt 3 dan aula', 'disetujui', '2025-09-23', '2025-09-24'),
(16, 12, 'SanDisk 32 GB', 'Unit IT', 0, 68000.00, 'Untuk Instal ulang dan copy file unit it', 'disetujui', '2025-10-07', '2025-10-07'),
(17, 12, 'Charger atau Adaptor atau  Casan Mini PC merek HP', 'Unit IT', 1, 200000.00, '-', 'disetujui', '2025-10-16', '2025-10-16'),
(21, 12, 'UGREEN USB Type-C To Lan RJ45 Ethernet Adapter 100Mbps - 1000Mbps For Windows Mac Os Set 50922', 'Unit IT', 1, 155000.00, 'Link Pembelian di Shoppe : https://shopee.co.id/product/1537643280/44053707336?gads_t_sig=VTJGc2RHVmtYMTlxTFVSVVRrdENkVHQ3ZkZSUTMrR3pBWmZZNzdrcnRBMThFcVgvMHJsbTNCQndRS0RHVUo2WDMvUHBLRjJuUTR3cXBxV2dML0VVSGhOcFUvNGY3V0ZwQWlybGR6WGE4aHgrOEhoOCsrTWhWTG4yU2U2S1Zaa2lLOEMwV2hvS3ZvSHBram1Odm00NnNnPT0&gad_source=1&gad_campaignid=22313024608&gbraid=0AAAAADPpU834zgVVtw05HuS7pwMgN_1rG&gclid=Cj0KCQjwmYzIBhC6ARIsAHA3IkRDm5THQd1Gs8MuHz09WCbPBZ2nMkY8BGPiw4W1y23jqn4FjEzoZA0aAlYbEALw_wcB', 'ditolak', '2025-10-31', '2025-11-03'),
(22, 12, 'UGREEN Kabel USB Type C Fast Charging 3A 1m 2m 3m For Samsung Oppo Vivo Xiaomi Realmi', 'Unit IT', 0, 30000.00, 'https://shopee.co.id/UGREEN-Kabel-USB-Type-C-Fast-Charging-3A-1m-2m-3m-For-Samsung-Oppo-Vivo-Xiaomi-Realmi-i.293199663.26517785112?extraParams=%7B%22display_model_id%22%3A108512538250%2C%22model_selection_logic%22%3A2%7D&sp_atk=2257fbb4-4d32-4d1c-a5ee-7cf1f3ba869e&xptdk=2257fbb4-4d32-4d1c-a5ee-7cf1f3ba869e', 'disetujui', '2025-10-31', '2025-11-03');

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
(16, 27, 16, 'bekas', 'Diterima langsung oleh karu farmasi (Rollah) pada tanggal 2025-09-20 dengan kondisi =BARU , dan printer Epson L121 di tarik ke Unit IT'),
(17, 28, 16, 'rusak', '-'),
(18, 29, 5, 'baru', 'Sudah di pasang dan di terima oleh karu kecubung, di pasang untuk kegunaan backup komputer & printer'),
(19, 30, 5, 'bekas', 'Ini komputer Mas Malik dan diserahkan ke kecubung karna permintaan dari karu kecubung ingin penambahan 1 pc'),
(20, 31, 6, 'baru', 'Untuk membackup komputer dan printer dan diterima langsung oleh karu yakut c (bu atul) saat pemasangan'),
(21, 32, 22, 'baru', 'di pasang di komputer dokter IGD'),
(22, 33, 15, 'baru', 'Sudah diserahkan oleh petugas farmasi ralan'),
(23, 34, 22, 'baru', 'di pasang di komputer Dokter IGD karna yang dulu cuman 4 GB saja RAM nya'),
(24, 35, 15, 'baru', 'langsung di diganti karna yang lama rusak (info : mas hadi)'),
(25, 36, 22, 'bekas', 'SSD Bekas punya Dito IT  dipasang di komputer dokter IGD karna permasalahan yang sebelumnya sering mati sendiri dan bluscreen'),
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
(48, 80, 1, 'baru', 'Untuk menunjang keperluan APM Baru dan nanti nya akan di pakai jika app APM baru sudah selesai'),
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
(66, 98, 20, 'bekas', 'Diserahkan ke unit ponek , dan komputer ini bekas poli syaraf'),
(67, 100, 20, 'baru', 'Menambah Kapasitas untuk unit PONEK'),
(68, 101, 1, 'baru', 'UNTUK RIYAN IT KARNA YANG DULU PUNYA NYA RUSAK KARNA KONSLET LALU DI GANTI DENGAN YANG RAKITAN MAS HADI DENGAN KONDISI KEMUNGKINAN BAIK NYA 50%'),
(69, 102, 32, 'bekas', '?'),
(70, 103, 32, 'baru', 'Untuk Menambahkan Kapasitas RAM Punya LIZA karna PC nya kan bekas PONEK dan kapasitas nya hanyar 2 GB saja jadi Untuk sekarang Total Ram 10GB');

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
  `unit_melapor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_perbaikan_barang`
--

INSERT INTO `tb_perbaikan_barang` (`perbaikan_id`, `barang_id`, `tanggal_lapor`, `penyerahan_id`, `deskripsi_kerusakan`, `tindakan_perbaikan`, `status`, `tanggal_selesai`, `teknisi`, `keterangan`, `unit_melapor`) VALUES
(1, 27, '2025-11-25 10:58:00', 16, 'Printer Epson L3210 mengalami gangguan pada bagian fleksibel scanner dan roller (rol) sehingga fungsi scanner dan penarikan kertas tidak berjalan normal.', 'Service luar', 'selesai', '2026-12-29 12:02:00', NULL, 'Unit printer dibawa ke service center Twincom untuk dilakukan perbaikan karena masih dalam masa garansi.', 16);

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
(10, '1094780395', 'unitit2025', 'ALI PC'),
(11, '446013800', 'unitit2025', 'Shobbah Gizi'),
(12, '429134846', 'unitit2025', 'FO UGD'),
(13, '882946671', 'unitit2025', 'Akbar PC'),
(14, '1347725702', 'unitit2025', 'TAB IGD'),
(15, '1 598 186 144', 'unitit2025', 'Liza PC'),
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
(1, '234.030221', 'ali', '$2y$10$AKkVgsZmhsXun4wHTaxoyewmYuWJJtgPAQ12y1CJJPSY7K7a3I71y', 'ALI IWANSYAH', 'unititrspi@gmail.com', '081253534891', 'Staff', '../assets/upload/1768015463_depositphotos_531920820-stock-illustration-photo-available-vector-icon-default.jpg', 'aktif', '2026-01-10 03:24:23', '2026-01-10 03:24:32'),
(2, '662.140725', 'ika', '$2y$10$nhtEVX3lQCQGqHpu0B120O6kN/5j.XfGxFY/GQxecTRdAh4NK9jpW', 'Ika Aprillia, S.Kom', 'unititrspi@gmail.com', '087753560464', 'Staff', '../assets/upload/1768015294_depositphotos_531920820-stock-illustration-photo-available-vector-icon-default.jpg', 'aktif', '2026-01-10 03:21:34', '2026-01-10 03:25:15'),
(3, '22', 'da', '12345', 'dss', 'ss', '22', 'Staff', '1758164121_depositphotos_316270922-stock-illustration-salesman-employee-providing-a-customer.jpg', 'aktif', '2025-09-13 04:29:58', '2026-01-10 03:18:53'),
(4, '629.271224', 'rizki', '12345', 'Muhammad Rizki Ilham Pratama', 'unititrspi@gmail.com', '', 'Staff', '1758164461_depositphotos_316270922-stock-illustration-salesman-employee-providing-a-customer.jpg', 'aktif', '2025-09-18 03:01:01', '2025-09-18 03:01:43'),
(10, '097.011113', 'admin', '$2y$10$SzwDz8O2teScUtfw2pzMau.jdreAWywzeJi9j0UQFQFYC27VqUO1O', 'Qhusnul Arinda, Amd. Far', 'qhusnl.arienda@gmail.com', '085751094503', 'Kepala Ruangan', '../assets/upload/1768006227_depositphotos_531920820-stock-illustration-photo-available-vector-icon-default.jpg', 'aktif', '2026-01-10 00:50:27', '2026-01-10 00:50:27'),
(12, '635.090125', 'riyan', '$2y$10$akXHkBMhThGUY2bD0hAfSuVkQimRQu0p8DriEx/jaeCfK/Fjnxeju', 'Riyan Aditya Pradana, S.Kom', 'riyanadityapradanaa@gmail.com', '082130304411', 'Staff', '635.090125.png', 'aktif', '2026-01-10 01:05:21', '2026-01-10 01:31:03'),
(13, '527.010623', 'hadi', '$2y$10$bgOA8.B/1SphzBuXSVGMbe86U9Ibfn0wFK0.4Qmp.eR6bLifj6aUC', 'Abdul Hadi, S.Kom', 'unititrspi@gmail.com', '085822823436', 'Staff', '../assets/upload/1768015138_depositphotos_531920820-stock-illustration-photo-available-vector-icon-default.jpg', 'aktif', '2026-01-10 03:18:58', '2026-01-10 03:18:58');

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
  MODIFY `barang_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

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
  MODIFY `id_kegiatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `tb_lembur`
--
ALTER TABLE `tb_lembur`
  MODIFY `id_lembur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tb_logbook`
--
ALTER TABLE `tb_logbook`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `tb_lokasi`
--
ALTER TABLE `tb_lokasi`
  MODIFY `lokasi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `tb_mutasi_barang`
--
ALTER TABLE `tb_mutasi_barang`
  MODIFY `mutasi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
  MODIFY `penyerahan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `tb_perbaikan_barang`
--
ALTER TABLE `tb_perbaikan_barang`
  MODIFY `perbaikan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
