# CAPTCHA dan pembatasan login

Form `main_login/form_login.php` memerlukan CAPTCHA gambar lokal dan token CSRF. Kode terdiri dari 5 karakter, tidak peka huruf besar/kecil, berlaku 5 menit, dan hanya dapat digunakan sekali. Tautan **Ganti kode CAPTCHA** mengambil kode baru tanpa menghapus username/password yang sedang diketik. Lima tab dapat memiliki kode masing-masing.

CAPTCHA, username/password, atau role yang tidak valid memicu jeda **30 detik** berdasarkan IP koneksi langsung. Hitung mundur menonaktifkan tombol login dan mengaktifkannya kembali saat selesai. Server juga menolak POST langsung selama jeda. Refresh, ganti sesi/browser, ganti username, dan ganti CAPTCHA tidak menghapus jeda. Percobaan ketika masih terkunci tidak memperpanjangnya dan tidak menghabiskan CAPTCHA. Pengguna di balik NAT/proxy dengan IP sama berbagi jeda. Header `X-Forwarded-For` tidak dipercaya. Token CSRF tidak valid ditolak sebelum pemeriksaan kredensial.

Login berhasil merotasi ID sesi, membersihkan CAPTCHA dan reservasi percobaan, menyimpan identitas pengguna yang sama seperti sebelumnya, lalu mengarahkan **Staff** ke dashboard staff dan **Kepala Ruangan** ke dashboard admin. Role lain ditolak sebelum sesi pengguna dibuat. Verifikasi password tetap memakai `password_verify`; spasi pada password dipertahankan. Query username memakai parameter terikat. Pesan username/password salah dibuat sama untuk menghindari pengungkapan keberadaan akun.

Input formulir memakai `$loginUsername` dan `$loginPassword`, terpisah dari `$username`/`$password` koneksi database. Pengujian meniru variabel koneksi tersebut agar pemuatan `koneksi.php` tidak menimpa kredensial formulir.

## Kebutuhan lingkungan

Portal publik menggunakan pembuat/pemeriksa kode, renderer PNG, dan algoritma
pembatasan yang sama dari `login_security.php`. Parameter opsional mengizinkan
namespace challenge dan file SQLite terpisah; pemanggilan login lama tetap
memakai nilai bawaan. Cookie/sesi publik serta SQLite portal berada di
`storage/portal-security/`, sehingga kegagalan CAPTCHA portal tidak mengunci
login IT. Rincian alur dan pengujiannya ada di
[dokumentasi portal](PORTAL_TIKET_LAYANAN_IT.md#captcha-sebelum-akses-portal-publik).

- PHP dengan ekstensi **GD** dan **PDO SQLite**.
- Izin tulis akun Apache pada `storage/login-security/`.
- Apache harus menerapkan `.htaccess` pada direktori tersebut (`Require all denied`). Pada server lain, blokir akses HTTP ke direktori ini melalui konfigurasi server.

`attempts.sqlite` dibuat otomatis; isinya hanya hash IP, batas waktu jeda, dan token reservasi. Data pembatasan terpisah dari proyek `aptd_rspi`. Tidak ada perubahan skema atau isi `tb_user`. Penyimpanan/ekstensi pengamanan yang tidak tersedia membuat form menolak login dengan pesan umum; rincian masuk log PHP.

Halaman pendaftaran Staff dan calon pegawai tetap dapat dibuka melalui modal registrasi. Modul ini mengamankan alur login yang sudah ada untuk `tb_user`; tidak menambahkan alur autentikasi calon pegawai.

## Verifikasi

Jalankan dari direktori proyek:

```powershell
C:\xampp\php\php.exe docs/test_login_security.php
```

Pengujian menggunakan salinan sementara, sesi sendiri, SQLite terisolasi, dan koneksi pengguna simulasi; database aplikasi asli tidak diakses. Cakupan meliputi CAPTCHA, CSRF, jeda 30 detik aktual, pergantian sesi, percobaan bersamaan, kedua peran pengguna, password dengan spasi, payload SQL injection, dan penolakan jika penyimpanan tidak tersedia.
