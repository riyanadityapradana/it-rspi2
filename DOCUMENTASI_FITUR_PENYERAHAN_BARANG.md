# Dokumentasi Fitur Penyerahan Barang - IT-RSPI

## Deskripsi
Fitur ini memungkinkan staff untuk mencatat penyerahan barang ke unit tertentu setelah pengajuan barang disetujui oleh admin. Saat penyerahan disimpan, status barang otomatis diubah menjadi 'Baik'.

## Fitur yang Ditambahkan

### 1. Field Database Baru
- **Tabel `tb_barang`:**
  - `penyerahan` (VARCHAR 100) - Menyimpan unit tujuan penyerahan
  - `stts_brg` (ENUM: 'Baik', 'Rusak') - Status kondisi barang

- **Tabel `tb_pengajuan_barang`:**
  - Status enum diperluas dengan 'Selesai'

### 2. Halaman Data Barang (Staff)
**File:** `staff/unit/barang/barang.php`

**Perubahan:**
- Menambahkan kolom "Penyerahan" dan "Status Barang"
- Menambahkan tombol "Input Penyerahan" untuk pengajuan yang sudah disetujui
- Modal input penyerahan dengan input text unit

**Cara Kerja:**
1. Staff melihat daftar barang
2. Jika ada pengajuan yang sudah disetujui, muncul tombol "Input Penyerahan"
3. Klik tombol untuk membuka modal
4. Masukkan unit tujuan penyerahan
5. Simpan data

### 3. Proses Penyerahan
**File:** `staff/unit/barang/proses_penyerahan.php`

**Fitur:**
- Validasi input
- Transaksi database untuk konsistensi data
- Update field `penyerahan` dan `stts_brg` di tabel barang
- Update status pengajuan menjadi 'Selesai'
- Notifikasi sukses/error menggunakan toastr

### 4. Unit Tujuan Penyerahan
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

## Alur Kerja

1. **Staff mengajukan barang** → Status: Menunggu
2. **Admin menyetujui pengajuan** → Status: Disetujui
3. **Staff input penyerahan** → Status: Selesai
   - Field `penyerahan` terisi dengan unit tujuan
   - Field `stts_brg` otomatis menjadi 'Baik'

## Instalasi

### 1. Update Database
Jalankan file `update_database.php` di browser:
```
http://localhost/it-rspi/update_database.php
```

Atau jalankan query SQL manual di phpMyAdmin:
```sql
ALTER TABLE `tb_barang` ADD `penyerahan` VARCHAR(100) NOT NULL DEFAULT '' AFTER `stok`;
ALTER TABLE `tb_barang` ADD `stts_brg` ENUM('Baik', 'Rusak') NOT NULL DEFAULT 'Baik' AFTER `penyerahan`;
ALTER TABLE `tb_pengajuan_barang` MODIFY `status` ENUM('Menunggu','Disetujui','Ditolak','Selesai') DEFAULT 'Menunggu';
```

### 2. File yang Dimodifikasi
- `staff/unit/barang/barang.php` - Tampilan data barang
- `staff/content.php` - Routing unit baru
- `staff/unit/barang/proses_penyerahan.php` - Proses penyimpanan

### 3. File Baru
- `update_database.php` - Script update database
- `Database/update_tb_barang.sql` - Query SQL manual

## Keamanan
- Validasi input di sisi server
- Prepared statements untuk mencegah SQL injection
- Transaksi database untuk konsistensi data
- Session validation untuk akses staff

## Notifikasi
Menggunakan toastr untuk feedback user:
- Sukses: "Penyerahan barang berhasil disimpan!"
- Error: Pesan error spesifik

## Testing
1. Buat pengajuan barang sebagai staff
2. Setujui pengajuan sebagai admin
3. Login sebagai staff, buka Data Barang
4. Klik tombol "Input Penyerahan"
5. Masukkan unit dan simpan
6. Verifikasi data tersimpan dengan benar

## Troubleshooting
- Jika field database belum ada, jalankan `update_database.php`
- Jika tombol tidak muncul, pastikan ada pengajuan dengan status 'Disetujui'
- Jika error, cek log error PHP dan database 