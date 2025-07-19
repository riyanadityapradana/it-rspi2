# Fitur Penyerahan Barang - IT-RSPI

## 🎯 Tujuan
Menambahkan fitur untuk mencatat penyerahan barang ke unit tertentu setelah pengajuan barang disetujui oleh admin.

## 📋 Langkah Instalasi

### 1. Update Database
Jalankan salah satu cara berikut:

**Cara A: Otomatis (Direkomendasikan)**
1. Buka browser
2. Akses: `http://localhost/it-rspi/update_database.php`
3. Tunggu hingga muncul pesan "Database berhasil diupdate!"

**Cara B: Manual di phpMyAdmin**
1. Buka phpMyAdmin
2. Pilih database `it-rspi`
3. Jalankan query berikut:
```sql
ALTER TABLE `tb_barang` ADD `penyerahan` VARCHAR(100) NOT NULL DEFAULT '' AFTER `stok`;
ALTER TABLE `tb_barang` ADD `stts_brg` ENUM('Baik', 'Rusak') NOT NULL DEFAULT 'Baik' AFTER `penyerahan`;
ALTER TABLE `tb_pengajuan_barang` MODIFY `status` ENUM('Menunggu','Disetujui','Ditolak','Selesai') DEFAULT 'Menunggu';
```

### 2. Test Fitur

#### Langkah 1: Buat Pengajuan Barang
1. Login sebagai Staff
2. Buka menu "Master Barang" → "Pengajuan Barang"
3. Klik "Tambah Pengajuan"
4. Isi form pengajuan barang
5. Simpan pengajuan

#### Langkah 2: Setujui Pengajuan (Admin)
1. Login sebagai Admin
2. Buka menu pengajuan barang
3. Klik tombol "ACC" pada pengajuan yang baru dibuat
4. Status berubah menjadi "Disetujui"

#### Langkah 3: Input Penyerahan (Staff)
1. Login sebagai Staff
2. Buka menu "Master Barang" → "Data Barang"
3. Cari barang yang sudah disetujui
4. Klik tombol "Input Penyerahan" (hijau)
5. Masukkan unit tujuan penyerahan
6. Klik "Simpan Penyerahan"

#### Langkah 4: Verifikasi
1. Data penyerahan akan muncul di kolom "Penyerahan"
2. Status barang otomatis menjadi "Baik"
3. Tombol "Input Penyerahan" hilang (sudah diserahkan)

## 🔧 Fitur yang Ditambahkan

### Halaman Data Barang
- **Kolom Baru:**
  - Penyerahan: Menampilkan unit tujuan penyerahan
  - Status Barang: Menampilkan kondisi barang (Baik/Rusak)

- **Tombol Baru:**
  - "Input Penyerahan": Muncul hanya untuk pengajuan yang sudah disetujui

### Modal Input Penyerahan
- Input text untuk unit tujuan penyerahan
- Placeholder dengan contoh: "Contoh: Manajemen, Keuangan, SDM, dll."
- Validasi input
- Feedback sukses/error menggunakan toastr

### Proses Otomatis
- Status barang otomatis menjadi "Baik" saat penyerahan disimpan
- Status pengajuan berubah menjadi "Selesai"
- Transaksi database untuk konsistensi data

## 🎨 Unit Tujuan Penyerahan
Staff dapat mengetik unit tujuan secara bebas. Contoh unit yang umum:
- Manajemen
- Keuangan
- SDM
- Pelayanan
- Rawat Inap
- Rawat Jalan
- IGD
- Farmasi
- Laboratorium
- Radiologi
- Gizi
- CSSD
- Laundry
- Housekeeping
- Security
- IT
- Lainnya

## ⚠️ Troubleshooting

### Tombol "Input Penyerahan" Tidak Muncul
- Pastikan ada pengajuan dengan status "Disetujui"
- Pastikan field database sudah ditambahkan
- Cek log error di browser console

### Error Saat Menyimpan
- Pastikan semua field terisi
- Cek koneksi database
- Pastikan user memiliki hak akses

### Field Database Belum Ada
- Jalankan `update_database.php`
- Atau tambahkan manual di phpMyAdmin

## 📁 File yang Dimodifikasi
- `staff/unit/barang/barang.php` - Tampilan utama
- `staff/content.php` - Routing
- `staff/unit/barang/proses_penyerahan.php` - Proses penyimpanan

## 📁 File Baru
- `update_database.php` - Script update database
- `Database/update_tb_barang.sql` - Query SQL
- `DOCUMENTASI_FITUR_PENYERAHAN_BARANG.md` - Dokumentasi lengkap

## 🔒 Keamanan
- Validasi input server-side
- Prepared statements anti SQL injection
- Session validation
- Transaksi database

## 📞 Support
Jika ada masalah, periksa:
1. Log error PHP
2. Log error database
3. Console browser
4. Dokumentasi lengkap di `DOCUMENTASI_FITUR_PENYERAHAN_BARANG.md` 