# IT-RSPI

Aplikasi PHP untuk layanan dan pekerjaan tim IT RSPI, dijalankan melalui Apache/XAMPP.

- `main_login/`: login dan pendaftaran.
- `staff/`, `admin/`, `calon-karyawan/`: halaman sesuai peran pengguna.
- `portal-tiket/`: pengajuan, daftar tiket, dan UAT publik.
- `scan-barang/`: pemindaian QR barang.
- `assets/dist/css/`, `assets/dist/js/`: CSS dan JavaScript aplikasi.
- `assets/dist/plugins/`: library frontend beserta font/gambar pendukungnya.
- `assets/img/`, `assets/upload/`, dan folder unggahan modul: gambar dan data pengguna.
- `library/`: library PHP, termasuk TCPDF untuk PDF/QR.
- `config/`, `.env`: konfigurasi koneksi; jangan membagikan kredensial.
- `Database/`: instalasi, migrasi, dan snapshot SQL. Jangan mengimpor seluruh snapshot ke database aktif tanpa pemeriksaan.
- `docs/`: panduan modul dan pengujian regresi.

Portal publik: `/it-rspi2/portal-tiket/`. Login: `/it-rspi2/main_login/form_login.php`.

Login menggunakan CAPTCHA lokal dan jeda 30 detik setelah percobaan gagal. Lihat [panduan pengamanan login](docs/LOGIN_SECURITY.md) untuk perilaku, kebutuhan ekstensi/penyimpanan, dan pengujian.

Panduan: [Portal Tiket](docs/PORTAL_TIKET_LAYANAN_IT.md), [Monev](docs/MONEV_KEAMANAN.md), [catatan perapian dan pemeriksaan aset](docs/PEMBERSIHAN_PROYEK.md).

Periksa referensi aset setelah mengubah/memindahkan file:

```powershell
node docs/check_assets.cjs
node docs/check_assets.cjs --http=http://localhost/it-rspi2/
```
