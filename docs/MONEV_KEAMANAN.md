# Monev Keamanan Fisik dan Kerahasiaan Data

Menu **Monev Keamanan IT** tersedia pada dashboard Staff dan Kepala Ruangan. Rute menggunakan `?unit=monev` di kedua dashboard. Implementasi bersama berada di `staff/unit/monev`.

## Kriteria Ruang Server per unit

Untuk unit selain `IT` (tanpa membedakan huruf besar/kecil), kriteria dengan kategori snapshot `Ruang Server` disembunyikan. Saat bundle disimpan, backend tetap menyimpan hasil `TA`, lokasi sesuai nama unit, dan catatan `Ruang server tidak tersedia`, termasuk jika input tersembunyi tidak dikirim. Alasan TA/BV tambahan tidak diperlukan untuk default ini. Data tetap berjumlah 10 butir; PDF mengecualikan kriteria tersebut dari tabel, rekap, dan lampiran foto checklist. Unit IT menampilkan semua kriteria. Mengganti unit tidak menghapus perlindungan riwayat temuan yang sudah ditindaklanjuti atau diverifikasi.

Aturan mengikuti kategori, bukan nomor urut: seed awal proyek memiliki 4 butir Ruang Server, sedangkan butir 5 berkategori Komputer. Kategori pada dokumen menggunakan snapshot saat bundle dibuat agar perubahan master tidak mengubah laporan historis.

## Instalasi

Jalankan pada database yang dikonfigurasi aplikasi:

```powershell
C:\xampp\php\php.exe Database/migrate_monev.php
```

Gunakan script PHP di atas untuk instalasi maupun pembaruan tabel yang sudah ada. Migrasi dapat diulang dan tidak mengubah master akun atau lokasi. Sepuluh kriteria contoh hanya diisi apabila master kriteria kosong. Kriteria tersebut **usulan**, perlu disesuaikan Kepala Ruangan dengan formulir resmi. Revisi dan tanggal berlaku diisi pengguna.

Nomor formulir otomatis berformat `INSP-YYYYMMDD-001` berdasarkan tanggal pembuatan draft menurut zona waktu aplikasi (`TIMEZONE`, default Asia/Makassar), bukan tanggal inspeksi yang diisi. Kolom read-only menampilkan perkiraan nomor berikutnya; nomor final dialokasikan saat penyimpanan menggunakan penguncian penghitung harian dalam transaksi. Nomor dari browser diabaikan. Urutan mulai dari 001 setiap hari, melanjutkan nomor berformat sama yang sudah ada, maksimal 999 per hari. Kegagalan transaksi tidak menghabiskan nomor. Nomor laporan lama tidak diubah.

PIC area dan Verifikator IT dipilih dari **`sik9.pegawai` pada server `192.168.1.4:3306`**, hanya dengan `stts_aktif = 'AKTIF'`. Ketik minimal 3 karakter NIK atau nama; hasil berupa `NIK - Nama`. Pencarian AJAX memiliki jeda 300 ms dan menampilkan 20 hasil per halaman. Data pegawai dibaca saja; tidak ada penulisan ke database SIMRS. Unit/area tetap berasal dari `tb_lokasi`.

Koneksi pegawai terpisah dari koneksi aplikasi, menggunakan kredensial database aplikasi sebagai nilai awal. Jika perlu akun/port berbeda, atur di `.env` lokal: `MONEV_PEGAWAI_HOST`, `MONEV_PEGAWAI_PORT`, `MONEV_PEGAWAI_DB`, `MONEV_PEGAWAI_USER`, dan `MONEV_PEGAWAI_PASSWORD`. Nilai awal host/database adalah `192.168.1.4`/`sik9`. Jangan menyimpan kredensial ke repositori.

Draft menyimpan NIK dan salinan nama pegawai sehingga dapat dibuat meskipun pegawai belum memiliki akun IT-RSPI. Untuk pengesahan, Kepala Ruangan membuka **Master kriteria → Pemetaan pegawai ke akun pengesahan**, memilih pegawai dan akun aktif miliknya, lalu menyimpan. Pemetaan disimpan di `monev_pegawai_akun`; satu pegawai hanya terhubung ke satu akun dan sebaliknya. Perubahan NIP profil sendiri tidak memberikan hak pengesahan. Penugasan laporan lama yang berbasis akun tetap berlaku. Pemeriksa, PIC area, dan verifikator harus berbeda; PIC perbaikan tidak boleh merangkap verifikator inspeksinya.

## Alur dan hak akses

1. Staff membuat Draft dengan satu nomor formulir dan bundle pertama. Unit/area dan PIC pada halaman Inspeksi baru menjadi identitas bundle pertama; Pemeriksa dan Verifikator ditetapkan untuk seluruh dokumen.
2. Setiap bundle berisi 10 kriteria aktif, hasil S/TS/TA/BV, lokasi/ID perangkat, catatan, foto, cakupan, dan rekapnya sendiri. Master harus memiliki tepat 10 kriteria aktif untuk membuat bundle baru. Salinan kriteria menjaga laporan historis saat master berubah.
3. Klik **+ Tambah Checklist** untuk membuka bundle baru lengkap. Pilih Unit/Area dan PIC melalui pencarian. Hasil belum dipilih dan isian masih kosong. Gunakan **Simpan semua checklist & cakupan** untuk menyimpan seluruh bundle sekaligus. Satu bundle baru dapat ditambahkan per penyimpanan; ulangi untuk unit berikutnya. Tidak ada tombol simpan per kriteria. Menambah bundle tidak membuat nomor formulir baru.
4. Bundle tersimpan menjadi accordion tertutup dengan judul **Nama Unit/Area - Nama PIC - Status: Draft/Selesai**. Rekap S/TS/TA/BV dihitung per bundle. Setiap TS menghasilkan temuan yang tetap terikat ke butir dan bundle sumbernya.
5. **Tanda tangan PIC bersifat parsial:** canvas tersedia di dalam setiap bundle. PIC dapat menandatangani langsung di perangkat pemeriksa tanpa berganti login, atau melalui akun yang dipetakan. Satu tanda tangan PIC hanya berlaku untuk satu bundle, sekalipun orang yang sama menjadi PIC beberapa unit. Draft boleh disimpan tanpa tanda tangan, tetapi bundle belum disahkan.
6. **Tanda tangan global:** Pemeriksa dan Verifikator masing-masing menandatangani sekali pada bagian Pengesahan di bawah dokumen. Tanda tangan dapat dicatat bertahap. Dokumen menjadi **Selesai hanya jika kedua tanda tangan global bergambar dan seluruh tanda tangan PIC bundle lengkap**. Persetujuan akun tanpa gambar tidak memenuhi kelengkapan tanda tangan baru.
7. Pengajuan memvalidasi checklist, lokasi, cakupan, alasan TA/BV setiap bundle, serta kondisi/risiko, PIC perbaikan, dan target temuan. Status menjadi Menunggu Pengesahan. Setelah selesai, dokumen terkunci. Penyelesaian dokumen melalui tanda tangan langsung tidak otomatis mengubah status temuan menjadi terverifikasi.
8. Selama Draft, perubahan isi, unit, atau PIC sebuah bundle membatalkan tanda tangan PIC bundle tersebut dan tanda tangan global yang masih aktif. Tanda tangan bundle lain yang tidak berubah tetap berlaku. Riwayat pembatalan tersimpan. Pengembalian dokumen ke Draft oleh PIC yang ditugaskan atau Verifikator membatalkan seluruh pengesahan. Butir temuan terverifikasi tetap tidak dapat diubah.
9. Tindak lanjut tetap dikerjakan Pemeriksa/PIC perbaikan. Tanggal pelaksanaan boleh sama dengan atau setelah tanggal temuan, termasuk setelah hari ini. Verifikasi perbaikan memerlukan tanggal pelaksanaan, bukti, dan hasil pemeriksaan ulang; pengesahan Verifikator melalui akunnya sendiri tetap mensyaratkan seluruh temuan terverifikasi.

Nama dan NIK penanda tangan langsung disimpan dari penugasan, sedangkan akun perekam tetap dicatat terpisah. Perubahan NIP profil tidak memberikan hak pengesahan. Staff yang menjadi PIC salah satu bundle mendapat akses dokumen dan hanya boleh mengesahkan bundle yang ditugaskan kepadanya. Pemeriksa dapat merekam semua tanda tangan pihak yang hadir pada perangkatnya.

PDF menampilkan satu header dokumen, checklist/cakupan/rekap serta pengesahan PIC untuk setiap bundle, daftar temuan, dan dua kolom pengesahan global. Unit/area pada filter daftar mempertimbangkan semua bundle, sedangkan grafik TS menggunakan unit bundle tempat temuan berasal.

Migrasi membungkus checklist dan cakupan laporan lama ke bundle pertama, termasuk tanda tangan PIC historis. Nomor, butir tambahan lama, foto, temuan, tanda tangan, dan status dokumen lama tidak dihapus atau diubah menjadi laporan baru. Migrasi dapat dijalankan ulang tanpa menggandakan bundle.

## Penyimpanan dan keamanan

- Aksi perubahan menggunakan POST, token CSRF, prepared statements, transaksi, pemeriksaan akun aktif dan penugasan. Version/row lock menolak penyimpanan dari halaman yang sudah tertinggal.
- Foto JPG/PNG/WebP dikompres browser menjadi JPEG maksimal dimensi 1600 px. Server memeriksa MIME, ukuran maksimum 5 MB, dan dimensi maksimum 40 megapiksel; nama file acak.
- `assets/upload/monev/.htaccess` menolak akses langsung pada Apache. Foto hanya disajikan melalui `file.php` dengan pemeriksaan akses inspeksi dan respons `no-store`. Jika pindah ke Nginx, terapkan aturan penolakan direktori setara sebelum dipakai.
- Penggantian foto menghapus berkas lama setelah transaksi berhasil. File baru dibersihkan jika transaksi gagal.
- Ringkasan, grafik TS per unit, grafik kriteria, dan daftar mengikuti filter yang sama, termasuk hak akses. Rentang tanggal berdasarkan waktu inspeksi.

## Koneksi terputus dan integrasi

Cadangan teks di perangkat bersifat **opsional**, harus diaktifkan pengguna, terpisah menurut akun/inspeksi, dan dihapus setelah penyimpanan checklist berhasil. Cadangan dengan versi berbeda tidak dipulihkan di atas data baru. Foto dan gambar tanda tangan tidak disimpan dalam cadangan. Halaman yang sudah terbuka tetap dapat diisi ketika koneksi terputus; pengguna menekan Simpan setelah online. Ini belum merupakan PWA dengan pembukaan halaman offline atau sinkronisasi otomatis latar belakang.

Nomor laporan insiden/tiket tersedia sebagai referensi. Checkout ini tidak memiliki modul tiket tugas yang dapat dihubungkan; belum ada pembuatan tiket eksternal otomatis. Dashboard temuan dan penugasan PIC tersedia di modul ini.

## Verifikasi

```powershell
C:\xampp\php\php.exe docs/test_monev.php
C:\xampp\php\php.exe docs/test_monev_http.php
node --check staff/unit/monev/monev.js
node docs/test_monev_frontend.cjs
node docs/test_monev_bundle_frontend.cjs
```

Pengujian integrasi membuat database sementara bernama `it_rspi2_monev_test_<acak>`, menguji migrasi berulang, checklist TS, anti-duplikasi, CSRF, pembatasan akun/penugasan, konflik versi, pengajuan, pengembalian, audit persetujuan, penyelesaian temuan, dan penguncian laporan selesai; database sementara dibuang di blok `finally`. Perlu hak CREATE/DROP DATABASE untuk menjalankan pengujian ini.

Pengujian HTTP menggunakan Apache lokal pada `http://localhost/it-rspi2`, tiga akun Staff aktif dan satu Kepala Ruangan aktif. Script membuat sesi uji sementara (memerlukan akses ke `session.save_path` PHP), satu inspeksi bertanda `TEST HTTP sementara`, dan foto kecil. Semuanya dibersihkan di blok `finally`. Uji mencakup dashboard kedua peran, unggahan gambar valid/palsu, rollback kegagalan, unduhan, export PDF, tombol Cetak PDF pada riwayat/detail, dan penolakan akses akun lain serta direktori berkas.

Uji kamera smartphone, pemulihan cadangan perangkat, dan tata letak cetak pada browser sasaran secara langsung sebelum penggunaan lapangan.
