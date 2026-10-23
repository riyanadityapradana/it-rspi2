# Struktur database Portal Tiket Layanan IT

Target: server `192.168.1.108`, database `it-rspi`, MariaDB 10.4.32.
SQL instalasi: [`../Database/2026-10-20_portal_tiket_layanan_it.sql`](../Database/2026-10-20_portal_tiket_layanan_it.sql).

## Akses portal publik

Portal untuk pegawai di luar unit IT diakses langsung melalui URL
`/it-rspi2/portal-tiket/`, tanpa membuka halaman login terlebih dahulu.
Pengunjung terlebih dahulu melewati halaman **Verifikasi Akses Portal** (CAPTCHA)
sebelum menu atau data portal ditampilkan. Setelah benar, sesi akses berlaku
hingga **2 jam tanpa aktivitas** dan diperbarui pada permintaan halaman/AJAX portal.
Contoh lokal: `http://localhost/it-rspi2/portal-tiket/`. Untuk komputer lain,
gunakan hostname/IP server aplikasi sebagai pengganti `localhost`.
Tombol Portal Tiket IT pada halaman login utama telah dihapus sesuai permintaan.

Landing publik berada di [`../portal-tiket/index.php`](../portal-tiket/index.php)
dan menampilkan tiga kartu tautan besar: **Form Change Request**, **Form Fitur Baru**,
dan **Daftar Tiket**. Seluruh kartu dapat diklik dan diakses lewat keyboard.
Masing-masing menuju `change-request.php`, `fitur-baru.php`, dan `daftar-tiket.php`.
Form Change Request dan Form Fitur Baru sudah mendukung penyimpanan ke database.
Daftar Tiket sudah menampilkan pencarian, pagination, status, dan rincian
pengajuan berikut hasil analisis/penyelesaian yang tersedia.

Seluruh halaman tetap dapat dibuka tanpa akun/login, dengan penjagaan CAPTCHA
melalui sesi publik. Landing dan CAPTCHA tidak membutuhkan database bisnis;
daftar dan detail membaca database portal setelah CAPTCHA lolos. Kedua formulir
permintaan memakai sesi publik yang sama untuk CSRF dan reservasi nomor,
terpisah dari sesi autentikasi Staff/Kepala Ruangan.
Logo mengarah ke portal; daftar menyediakan tombol kembali ke portal, sedangkan
detail menyediakan kembali ke daftar dengan filter/halaman yang sama.
Tata letak tiga kolom berlaku mulai lebar 640px (tablet/desktop), dan satu kolom
di bawah 640px (smartphone). Tema menggunakan Source Sans Pro, latar gambar yang
sama dengan login, overlay ungu, panel transparan, sudut membulat, dan aksen amber.

Landing terbaru juga diverifikasi di Chrome pada ukuran 1440x900, 768x1024,
390x844, dan 320x568: tiga kolom pada desktop/tablet, kartu menumpuk pada
smartphone, tanpa overflow horizontal. Menu dapat diakses lewat keyboard.
Navigasi daftar/detail tanpa cookie sesi juga diverifikasi; tidak ada redirect
ke autentikasi.

## CAPTCHA sebelum akses portal publik

`portal-tiket/access_helpers.php` menjaganya sebelum query/render halaman landing,
Change Request, Fitur Baru, daftar/detail, UAT, dan verifikasi QR. Akses langsung
ke URL tersebut juga harus lolos. Halaman perantara berada di
`verifikasi-akses.php`, dengan gambar lokal `captcha.php`, tombol **Ganti kode**,
input 5 karakter, dan tombol **Verifikasi & Masuk Portal**. Tema mengikuti portal
dan mendukung layar ponsel. Tidak memerlukan layanan CAPTCHA eksternal.

Pembuatan/pemeriksaan kode, renderer PNG, serta pembatasan IP memakai fungsi
yang sama dengan login IT (`main_login/login_security.php`). PNG 220×70 memakai
huruf/angka acak tanpa karakter ambigu, noise dan rotasi karakter. Kode berlaku
5 menit, tidak peka huruf besar/kecil, sekali pakai termasuk jawaban salah;
maksimal 5 challenge dipertahankan agar beberapa tab dapat digunakan.
POST wajib CSRF. Jawaban salah/kedaluwarsa memulai jeda 30 detik per IP,
ditampilkan sebagai hitung mundur/tombol terkunci, dan diperiksa ulang server
meskipun POST langsung, ganti browser/sesi, atau memalsukan X-Forwarded-For.

Cookie publik `portal_tiket` memakai HttpOnly, SameSite=Lax, path portal, Secure
pada HTTPS, strict mode, serta rotasi ID setelah CAPTCHA benar. Direktori sesi
khusus dan `session.gc_maxlifetime=7200` mencegah pembersihan sesi login lain
memotong masa akses publik. Status dan waktu aktivitas hanya disimpan server.
Setelah 7200 detik tanpa permintaan portal, CAPTCHA diwajibkan lagi. Halaman
CAPTCHA dan gambar/aset tidak memperpanjang akses yang sudah diberikan.
Sesi dan SQLite limiter berada di `storage/portal-security/`, dilindungi
`.htaccess` dan diabaikan Git. Direktori dibuat otomatis bila belum tersedia.
`PORTAL_SECURITY_PATH` dapat menunjuk direktori writable lain, terutama untuk
fixture terisolasi; default sudah sesuai XAMPP. Sesi/limiter publik terpisah
dari login IT sehingga kesalahan CAPTCHA portal tidak mengunci login Staff.
GD, PDO SQLite, dan direktori writable diperlukan; gangguan pengamanan menolak
akses dengan pesan umum, tanpa melewatkan CAPTCHA atau membuka data portal.

Tujuan kembali diizinkan hanya untuk rute portal. Form ID, filter/pagination,
atau token magic link ditahan di sesi server dengan penanda acak `lanjut`,
bukan menerima URL redirect bebas dari browser. Token UAT tidak diteruskan
sebagai query di halaman perantara; token/expiry UAT tetap diperiksa setelah
CAPTCHA benar. POST yang sesinya kedaluwarsa **tidak dieksekusi atau diputar ulang**;
pengguna kembali ke halaman GET tujuan setelah verifikasi dan perlu mengisi
ulang data yang belum tersimpan. Draft pengajuan dalam sesi tetap dipertahankan.

Endpoint AJAX `pegawai.php` dan `uat-pegawai.php` mengembalikan HTTP 403 JSON
`captcha_required` serta URL perantara jika sesi akses habis. Kedua dropdown
mengikuti URL tersebut untuk verifikasi ulang, kemudian kembali ke form/UAT
semula. Setelah lolos, validasi CSRF, pegawai aktif, token UAT, dan transaksi
bisnis tetap berlaku. Back/forward cache memuat ulang halaman untuk memeriksa
sesi server. Dashboard IT dan akses PDF internal tetap menggunakan login IT.

Verifikasi: `C:\xampp\php\php.exe docs/test_portal_access.php` menjalankan **71**
pemeriksaan HTTP/sesi/database terisolasi, termasuk URL langsung, AJAX/POST,
cookie/rotasi ID, timeout 2 jam, TTL/sekali pakai/antar sesi, limiter, kondisi
penyimpanan rusak, dan tujuan redirect. **20** pemeriksaan Chrome memverifikasi
refresh, countdown, navigasi menu, UAT/AJAX yang kedaluwarsa, serta responsivitas
1440/390/320 piksel. Regresi login IT (28), daftar/detail publik (70), konfirmasi
UAT (115), Telegram (150), antrean internal (26), analisis (54), dan PDF (196)
tetap lolos. Suite HTTP bisnis melewati CAPTCHA produksi dengan membaca jawaban
dari sesi fixture lokal; tidak ada flag/parameter bypass pada aplikasi.

## Daftar dan Detail Tiket publik

Kartu **Daftar Tiket** (termasuk area kuning **Buka Halaman**) menuju
`/it-rspi2/portal-tiket/daftar-tiket.php`. Halaman menampilkan seluruh jenis dan
status tiket, termasuk tiket yang terkait sistem nonaktif. Kolom tabel: No
Tiket, Tanggal Pengajuan, Jenis Pengajuan, Sistem Terkait, Nama Perubahan/Fitur,
Status, dan Aksi **Detail**. Tema mengikuti portal publik; tabel dapat digeser
di dalam panel pada layar kecil tanpa membuat halaman melebar.

Pencarian mencakup nomor tiket, nama sistem, dan nama perubahan/fitur pada
seluruh rincian. Filter status/jenis memakai allowlist. Query diparameterisasi
dan karakter `%`, `_`, `!` pada pencarian diperlakukan literal. Pagination
20 tiket per halaman, terbaru berdasarkan tanggal lalu ID menurun, dengan
nomor halaman dan tombol sebelumnya/berikutnya. Banyak rincian/analisis/serah
terima tidak menggandakan baris maupun jumlah tiket. Halaman di luar jangkauan
dinormalkan ke halaman terakhir; pencarian baru dimulai dari halaman pertama.

Badge status: amber untuk Menunggu Analisis, biru untuk Dikerjakan, teal untuk
Menunggu UAT, hijau untuk Selesai, dan merah muda untuk Ditolak. Data dibaca
ulang pada permintaan halaman dan respons memakai `Cache-Control: no-store`.

`detail-tiket.php?id=<id_tiket>` menampilkan seluruh rincian sesuai jenis
pengajuan, hasil analisis IT/prioritas/keputusan, serta deskripsi penyelesaian,
tanggal serah terima, hasil dan catatan pengujian UAT jika tersedia. Hasil yang
belum tersedia ditandai dengan keterangan. Tiket tidak ditemukan/ID tidak valid
merespons 404; kegagalan database merespons 503 dengan pesan umum. Tombol kembali
mempertahankan pencarian, filter, dan nomor halaman.

Query publik memakai proyeksi kolom terpisah dari modul internal, tanpa
`SELECT *`. Daftar/detail tidak mengambil nomor WhatsApp, nama/unit pengaju,
tanda tangan, token/kedaluwarsa UAT, nama penerima, atau ID pegawai internal.
Pencarian juga tidak memakai kolom privat. Seluruh nilai ditampilkan sebagai
teks dengan escaping HTML; URL kembali dibangun dari filter yang dinormalisasi.
Halaman hanya menerima GET/HEAD, tanpa sesi/login dan tanpa aksi perubahan data.
Tidak diperlukan migrasi database tambahan.

Pengujian: `C:\xampp\php\php.exe docs/test_portal_public.php --http` menjalankan
70 pemeriksaan (43 query/backend dan 27 HTTP), memakai database localhost
sementara dengan 43 tiket, lima status, dua jenis, rincian ganda, sistem
nonaktif, analisis ganda, dan serah terima sebelum/sesudah UAT. Memeriksa
pagination/search/filter, kolom privat, escaping, status terbaru, metode HTTP,
404/503, serta tanpa cookie/redirect login. Tambahan 32 pemeriksaan Chrome
mencakup klik kartu, halaman bernomor, Detail/kembali dengan filter, pencarian,
badge, privasi, dan layout 1440, 768, 390, 320px. Database uji dihapus setelah
verifikasi; tidak ada data tiket bisnis yang ditambahkan.

## Sidebar dan antrean internal

Menu **Portal Tiket IT** tersedia tepat di antara **Monev Keamanan IT** dan
**Dashboard** pada sidebar Staff dan Admin/Kepala Ruangan. Ikon `fa-ticket-alt`
mengikuti kelas `nav-icon`/`nav-link` AdminLTE dan warna sidebar masing-masing.
Ketika antrean atau detail tiket dibuka, Portal Tiket IT menjadi menu aktif;
Dashboard tidak ditandai aktif bersamaan.

- Staff: `/it-rspi2/staff/dashboard_staff.php?unit=portal_tiket`.
- Admin/Kepala Ruangan: `/it-rspi2/admin/dashboard_admin.php?unit=portal_tiket`.
- Implementasi bersama: `staff/unit/portal-tiket/`; kedua router `content.php`
  memuat halaman yang sama dalam layout dashboard masing-masing.

Akses memakai sesi login aplikasi utama dan memeriksa kembali `tb_user` pada
koneksi autentikasi aplikasi: ID harus ada, status `aktif`, role `Staff` atau
`Kepala Ruangan`, dan role sesi harus sesuai dengan role database. Tidak ada
role tambahan Admin atau asumsi unit dari nama/jabatan. Permintaan tanpa login
diarahkan oleh dashboard ke login; akun nonaktif ditolak. Modul dan router
langsung tidak menyediakan jalur akses anonim ke data tiket.

Antrean membaca database portal melalui `config/portal_tiket.php`, sama dengan
form publik; autentikasi tetap mengikuti database aplikasi dashboard. Halaman
memuat ringkasan seluruh tiket, pencarian nomor/pengaju/unit/nama perubahan,
filter status dan sistem, serta paginasi 25 tiket. Sistem nonaktif tetap dapat
dipilih untuk melihat riwayat pengajuan. Status aktif didahulukan, dengan urutan
pengajuan terlama per status. Banyak rincian dalam satu tiket tidak menggandakan
baris atau total antrean.

Empat panel ringkasan memakai gradasi ungu, amber, biru, dan teal, ikon status,
animasi masuk bertahap, efek terangkat/kilau saat hover, serta tata letak dua
kolom pada mobile. Gaya berada di `assets/dist/css/portal-tiket-summary.css`, berlaku
untuk Admin dan Staff. Pengaturan reduced motion menonaktifkan animasi/gerakan.

Tombol **Detail** membuka identitas pengajuan dan seluruh rincian permintaan;
tombol kembali mempertahankan filter dan halaman. Nilai ditampilkan dengan
escaping HTML. Query daftar/detail tidak mengambil token UAT atau gambar tanda
tangan. Detail menyediakan analisis IT dan transisi awal ke Dikerjakan;
serah terima IT menyediakan transisi ke Menunggu UAT. Persetujuan pemohon melalui
magic link UAT melengkapi penerimaan dan mengubah tiket menjadi Selesai.

Verifikasi: `C:\xampp\php\php.exe docs/test_portal_internal.php` menjalankan 26
pemeriksaan akses, filter, SQL/LIKE escaping, urutan, paginasi, dan detail pada
database localhost sementara yang dihapus setelah pengujian. Browser diuji
menggunakan sesi dan 35 tiket fixture terisolasi untuk kedua role, termasuk
urutan sidebar, klik menu, menu aktif, filter, detail, paginasi, penolakan akun
nonaktif/tanpa login, dan sidebar mobile. Tidak ada akun/tiket bisnis yang dibuat
untuk pengujian ini.

## Analisis IT

Pada halaman **Detail**, tombol **Analisis** dan modal hanya dirender saat
status tiket tepat `Menunggu Analisis`. Modal mewajibkan satu prioritas
(radio Tinggi/Sedang/Rendah), Analisis Unit IT, dan Keputusan. Dua textarea
dibatasi 10.000 karakter dan divalidasi kembali oleh server.

Identitas diambil dari akun sesi yang diverifikasi ulang aktif/role-nya.
Backend membaca NIP akun dari `tb_user`, mencocokkannya dengan satu
`sik9.pegawai.nik` aktif melalui koneksi SIMRS `192.168.1.4`, lalu menyimpan
`pegawai.id` ke `tiket_analisis_it.id_staf_it`. ID akun lokal tidak dianggap
sebagai ID pegawai, dan identitas dari POST/NIP sesi tidak dipercaya.
Akun tanpa NIP, tanpa kecocokan pegawai aktif, atau dengan kecocokan ambigu
mendapat pesan validasi; status tiket tidak berubah. Tidak ada FK lintas server.

Penyimpanan memerlukan POST dan token CSRF sesi login. Transaksi mengunci
baris tiket dengan `SELECT ... FOR UPDATE`, memeriksa ulang status, memasukkan
analisis, lalu mengubah status ke `Dikerjakan`. Jika salah satu query gagal,
seluruh perubahan di-rollback. Submit ulang atau submit bersamaan setelah
tiket diproses ditolak dengan HTTP 409 agar tidak membuat analisis ganda.

Sukses memakai redirect 303 kembali ke antrean dengan filter yang sama,
notifikasi dari sesi, dan data ringkasan/tabel yang dimuat ulang. Status
`Dikerjakan` memakai badge biru. Detail memperlihatkan hasil analisis dengan
escaping HTML; tombol/modal tidak lagi dirender. Jika validasi gagal saat
status masih menunggu, modal dibuka ulang dengan nilai yang sudah diisi.
Implementasi bersama berlaku untuk Staff dan Kepala Ruangan, serta kedua
jenis tiket. Nama pegawai dan tanggal analisis kini disimpan sebagai snapshot
melalui migrasi identitas QR (lihat bagian Rincian UAT di bawah).

`C:\xampp\php\php.exe docs/test_portal_analysis.php` menjalankan 54 pemeriksaan
backend terisolasi: akses, CSRF, identitas, input, semua status, rollback kedua
arah, serta dua proses yang menyimpan tiket yang sama. Tambahan verifikasi:
33 pemeriksaan HTTP pada kedua dashboard dan 18 pemeriksaan Chrome untuk
modal, radio, simpan, redirect, notifikasi, badge, dan tombol yang hilang
setelah proses; desktop Staff 1440x1000 dan mobile Kepala Ruangan 390x700.
Regresi antrean (26) serta Master Sistem (33) tetap lolos. Seluruh data/sesi
uji berada di database localhost sementara, tanpa perubahan tiket bisnis.

## Serah Terima Penyelesaian oleh IT

Tombol **Buat Serah Terima Penyelesaian** tersedia paling bawah pada halaman
Detail, setelah blok **Analisis IT**, hanya jika status tepat `Dikerjakan`.
Klik membuka modal Bootstrap pada halaman yang sama. Modal mewajibkan
Deskripsi Penyelesaian (maksimal 10.000 karakter), memperlihatkan nama akun
penyerah, dan menjelaskan tanggal dicatat saat penyimpanan. Teks tombol dapat
membungkus pada layar kecil. Berlaku untuk Change Request/Fitur Baru serta
Staff/Kepala Ruangan yang aktif.

Backend memerlukan POST dan CSRF sesi khusus serah terima, memverifikasi ulang
akun aktif/role, kemudian memakai pemetaan identitas yang sama dengan Analisis:
`tb_user.nip` akun login ke satu `sik9.pegawai.nik` aktif, lalu `pegawai.id`
disimpan sebagai `id_staf_penyerah`. Helper bersama berada di
`staff/unit/portal-tiket/staff_identity.php`. ID/NIP penyerah, tanggal, token,
status tiket, dan bagian penerimaan dari POST tidak dipercaya. Akun yang tidak
memiliki pemetaan pegawai aktif/unik mendapat pesan validasi tanpa perubahan.

Transaksi mengunci header tiket dengan `FOR UPDATE` dan memeriksa ulang status
`Dikerjakan`. Waktu server ditentukan saat penyimpanan; backend membuat token
256-bit dengan `bin2hex(random_bytes(32))` (64 karakter hex) dan kedaluwarsa
satu bulan kalender dari waktu serah terima. Transaksi memasukkan `tiket_serah_terima`
serta memperbarui `status_tiket='Menunggu UAT'`, `token_uat`, dan
`token_expired_at` sekaligus. Kegagalan query membatalkan seluruh perubahan.
Submit ulang/bersamaan setelah tiket diproses mendapat HTTP 409, tanpa membuat
serah terima ganda, mengganti token, atau memperpanjang kedaluwarsa.

Kolom hasil UAT, catatan pengujian, nama penerima, dan tanda tangan penerima
tetap NULL karena belum diisi pemohon. Setelah sukses, redirect 303 kembali ke
Detail mempertahankan filter antrean, menampilkan notifikasi, status Menunggu
UAT, dan riwayat penyelesaian. Tombol/modal serah terima tidak lagi dirender.
Saat validasi gagal dan tiket masih Dikerjakan, modal dibuka ulang dengan
pesan serta teks isian yang di-escape. Deskripsi penyelesaian juga langsung
tersedia di Detail publik, tanpa token UAT atau identitas penyerah.

Token disimpan di database dan ditampilkan sebagai magic link hanya pada
kartu khusus Detail internal saat status Menunggu UAT. Token tidak dimasukkan
ke daftar/detail publik, redirect setelah simpan, atau notifikasi. Tahap ini belum
mengirim WhatsApp. Formulir persetujuan tersedia pada halaman UAT publik. Tidak
ada migrasi tabel tambahan karena semua kolom sudah tersedia pada skema portal.

`C:\xampp\php\php.exe docs/test_portal_handover.php` menjalankan 57 pemeriksaan
backend terisolasi, termasuk identitas, CSRF, semua status terlarang, tanggal,
token/kedaluwarsa, kolom penerimaan NULL, rollback insert/update, submit ulang,
dan dua proses yang mengakses tiket yang sama. Tambahan 34 pemeriksaan HTTP
mencakup kedua role, notifikasi, redirect, token yang tidak bocor, rollback,
serta alur Analisis sampai serah terima. Chrome menjalankan 22 pemeriksaan
untuk posisi tombol, buka/batal modal tanpa navigasi, validasi wajib, simpan,
status/tombol setelah simpan, dan viewport 1440, 390, 320px. Regresi Analisis
(54) dan antrean (26) tetap lolos. Database/sesi uji localhost dihapus;
tidak ada tiket bisnis yang ditambahkan atau diubah selama pengujian.

## Magic Link UAT dan Salin Tautan

Token baru saat menyimpan serah terima berlaku **satu bulan kalender**, pada
jam yang sama. Tanggal dijepit ke hari terakhir bulan tujuan bila diperlukan:
31 Januari menjadi 28/29 Februari, dan 31 Maret menjadi 30 April. Implementasi
berada di `portal-tiket/uat_helpers.php`; tidak menggunakan penambahan 30 hari
atau overflow `+1 month`. Tanggal token yang sudah tersimpan tetap dipakai;
tidak ada perubahan massal atau perpanjangan token lama secara otomatis.

Detail dashboard Staff/Kepala Ruangan yang sudah diverifikasi aktif menampilkan
kartu **Tautan UAT Pemohon** hanya untuk status `Menunggu UAT`. Query token
khusus berada di `staff/unit/portal-tiket/uat_helpers.php`, terpisah dari query
antrean serta query daftar/detail publik. Kartu berisi URL penuh read-only,
batas waktu lengkap dengan zona waktu, tombol **Salin Tautan**, serta area
notifikasi `aria-live`. Token kosong/tanggal tidak valid menonaktifkan salin.

URL menuju `/portal-tiket/uat.php?token=<token>` pada prefix aplikasi saat ini.
Default hostname, protokol, dan port mengikuti URL dashboard. Untuk URL publik
khusus, reverse proxy HTTPS, atau dashboard yang diakses melalui localhost,
atur `PORTAL_PUBLIC_URL` pada `.env` ke URL portal yang dapat dijangkau pemohon,
misalnya `https://it.rumah-sakit.example/it-rspi2/portal-tiket`. Contoh tersedia
di `config/portal_tiket.env.example`. Nilai URL ditolak bila mengandung kredensial,
query, fragment, skema selain HTTP/HTTPS, atau hostname tidak valid. Header
forwarded tidak dipercaya otomatis; gunakan URL konfigurasi untuk proxy.

`assets/dist/js/portal-uat-copy.js` memakai Clipboard API pada secure context, dengan fallback
seleksi seluruh URL dan `execCommand('copy')` untuk browser/intranet HTTP
yang tidak menyediakan API tersebut. Berhasil menampilkan **Tautan disalin!**;
kegagalan menampilkan petunjuk salin manual, tanpa klaim sukses. Status expiry
ditentukan server, lalu dipantau dengan jam server dan timer monotonic di
browser. Pada `now >= token_expired_at`, tombol menjadi disabled/abu-abu dan
label berubah **Tautan telah kedaluwarsa**, termasuk saat halaman dibiarkan
terbuka. Klik, kembali fokus, dan perubahan visibilitas juga memeriksa expiry.
Tanggal kedaluwarsa tetap terlihat.

Halaman tujuan `portal-tiket/uat.php` menyediakan peninjauan hasil penyelesaian
tanpa login berdasarkan token acak 64 hex yang valid dan tanggal yang belum
lewat. Tiket Menunggu UAT dapat diverifikasi; tiket Selesai dengan penerimaan
yang lengkap dapat dibaca sampai batas waktu aslinya. Token tidak dikenal/malformed
merespons 404; expired/status tidak aktif merespons 410. GET/HEAD untuk peninjauan,
POST dengan CSRF sesi portal untuk persetujuan dan tanda tangan penerima.
Halaman tujuan menampilkan rincian pengajuan, identitas pemohon/tanda tangan,
analisis, dan hasil penyelesaian beserta QR verifikasi staf setelah gate token.
Respons token memakai no-store/no-referrer/noindex; kartu internal juga memakai
no-referrer. Token bukan bagian URL daftar/detail publik; URL di dalam QR
membawa token yang sama dan tunduk pada batas waktu yang sama.

Verifikasi `C:\xampp\php\php.exe docs/test_portal_uat_link.php`: 45 pemeriksaan
backend untuk akhir bulan/tahun kabisat, URL, status, deadline tepat di batas,
token tidak valid/dirotasi, dan privasi. Tambahan 31 pemeriksaan HTTP meliputi
otorisasi kartu, URL penuh, tanggal, disabled, tujuan valid/404/410, header,
dan query publik yang tidak membocorkan token. Chrome menjalankan 23 pemeriksaan
Clipboard API nyata dan fallback, notifikasi gagal/berhasil, expiry saat
klik/timer, jam perangkat berbeda, serta layout desktop dan mobile 390/320px.
Regresi serah terima (57) serta antrean (26) tetap lolos. Seluruh verifikasi
memakai database dan sesi localhost sementara yang dibersihkan setelah uji.

## Rincian UAT Lengkap dan QR Verifikasi Tim IT

`portal-tiket/uat.php` memvalidasi format token, kecocokan tiket, status Menunggu
UAT atau Selesai dengan hasil penerimaan, dan batas waktu sebelum merender rincian
pengajuan atau identitas staf.
Setelah lolos, halaman menampilkan seluruh field tipe Change Request/Fitur
Baru, metadata pengajuan (tanggal, unit, nama, kontak), dan tanda tangan PNG
pemohon. PNG divalidasi dan dinormalisasi kembali sebelum ditampilkan; data
gambar tidak valid tidak diteruskan sebagai URL gambar. Informasi pemohon
tersebut tetap tidak diambil oleh daftar/detail publik anonim.

Semua catatan Analisis IT menampilkan prioritas, analisis, keputusan, nama
terang analis, dan tanggal analisis. Semua serah terima menampilkan hasil
penyelesaian, nama penyerah, dan tanggal serah terima. Di bawah masing-masing
catatan terdapat QR lokal 176px, rata tengah di bawah nama/tanggal. Teks hash
dan tombol verifikasi tidak ditampilkan dalam blok UAT; URL verifikasi tetap
tersimpan di QR dan dapat dipindai. Rincian pengajuan dan tanda tangan pemohon
digabung dalam satu kartu; gambar tanda tangan ditampilkan rata tengah 250x96px
dengan `object-fit: contain`.

Upgrade database yang sudah ada memakai
`Database/2026-10-21_portal_tiket_identitas_qr.sql` (aman diulang, tanpa hapus
tabel): `tiket_analisis_it.tgl_analisis`, `tiket_analisis_it.nama_staf_it`, dan
`tiket_serah_terima.nama_staf_penyerah`. Installer pertama juga memuat ketiga
kolom. Migrasi sudah diterapkan pada koneksi portal yang dikonfigurasi;
jumlah baris bisnis sebelum/sesudah tetap sama. Tanggal analisis lama yang
tidak pernah disimpan tetap NULL dan ditampilkan **Belum tercatat (data lama)**,
bukan diisi dengan tanggal pengajuan atau tanggal migrasi.

Saat proses baru disimpan, nama diambil dari pegawai SIMRS terverifikasi dan
disalin bersama ID, serta waktu analisis dicatat server dalam transaksi yang
sama. Nama/tanggal yang dipalsukan dalam POST diabaikan. Snapshot mencegah nama
pada catatan baru ikut berubah bila master pegawai berubah. Untuk nama lama
yang NULL, halaman membaca `sik9.pegawai` berdasarkan ID tersimpan, termasuk
pegawai yang sekarang nonaktif. Jika nama tidak tersedia, tampilan menjelaskan
identitas belum tersedia dan tidak membuat QR seolah sudah terverifikasi.

Hash SHA-256 mengikat jenis catatan, ID tiket/catatan/pegawai, nomor tiket,
nama, waktu, serta isi analisis atau penyelesaian. QR dibuat langsung memakai
library TCPDF yang sudah ada, sebagai SVG dengan quiet zone empat modul;
tidak ada pengiriman token atau informasi staf ke API QR eksternal.
QR memuat URL `verifikasi-ttd.php` dengan token UAT, jenis/ID catatan, dan hash.
Halaman verifikasi memvalidasi ulang token/status/expiry, memastikan catatan
milik tiket tersebut, menghitung ulang fingerprint dari data otoritatif, lalu
membandingkan hash dengan `hash_equals`. QR memverifikasi kecocokan catatan
dan otorisasi staf yang direkam aplikasi.

Hash yang diubah, ID catatan lain, atau QR milik tiket lain ditolak 404 tanpa
mengungkap nama staf. Token kedaluwarsa menolak halaman UAT serta tujuan QR
dengan 410. Catatan yang isinya berubah tidak lagi cocok dengan hash QR lama.
Nama/teks di-escape, dan verifikasi hanya menerima GET/HEAD. Halaman tujuan
verifikasi tetap memakai no-store/no-referrer/noindex.

Verifikasi: `C:\xampp\php\php.exe docs/test_portal_uat_detail.php` menjalankan
33 pemeriksaan backend untuk snapshot, tanggal, field lengkap kedua tipe,
PNG, migrasi berulang, lookup nama lama, hash, perubahan isi, dan gate token.
Tambahan 37 pemeriksaan HTTP mencakup seluruh blok, identitas/QR, validasi
tujuan scan, hash palsu/lintas tiket, expired, serta privasi daftar anonim.
Chrome menjalankan 22 pemeriksaan desktop/mobile (1440/768/390/320px), termasuk
memindai kedua QR dengan decoder lokal html5-qrcode yang sudah ada dan membuka
hasilnya. Regresi Analisis (54), Serah Terima (57), dan magic link (45) tetap
lolos. Pengujian memakai database localhost terisolasi yang dihapus setelah uji.

## Cetak PDF Tiket Selesai

Kolom Aksi pada antrean internal memakai ikon kaca pembesar untuk Detail, dengan
label aksesibilitas dan tooltip. Ikon printer hanya muncul untuk status Selesai,
baik pada dashboard Staff maupun Kepala Ruangan. Klik membuka PDF di tab baru:
`staff/unit/portal-tiket/pdf.php?id=<id_tiket>`.

Endpoint berdiri sendiri agar HTML dashboard tidak masuk ke binary PDF. GET/HEAD
memerlukan sesi login internal dan pengecekan ulang akun aktif/role pada
`tb_user`. Akses anonim diarahkan ke login; role tidak sesuai/nonaktif ditolak 403,
ID tidak ditemukan 404, dan tiket belum selesai 409. Validasi status tetap berjalan
untuk akses URL langsung. Response inline memakai application/pdf, Content-Length,
no-store, dan no-referrer. Token UAT dan nomor WhatsApp tidak diambil oleh query
ekspor. Tidak ada perubahan skema atau penulisan data bisnis.

`pdf_helpers.php` membaca header, seluruh rincian, analisis, dan serah terima dalam
transaksi baca snapshot InnoDB. Sistem nonaktif tetap tercetak untuk riwayat.
Nama staf memakai snapshot tersimpan; data lama yang kosong dapat dicocokkan
berdasarkan ID pegawai, dan tanggal lama yang tidak tercatat tidak ditebak.
Gambar tanda tangan pengaju/penerima hanya diambil dari PNG Base64 yang lolos
validasi aplikasi. Path/URL gambar dan SVG dari input tidak diteruskan ke TCPDF.

`pdf_renderer.php` memakai TCPDF yang sudah tersedia, A4 portrait, font serif,
dan susunan label sesuai blanko:

- Bagian pengajuan: Form Change Request atau Form Permintaan Fitur Baru,
  tanggal/unit, seluruh field khusus jenis tiket, prioritas, analisis dan
  keputusan, lalu tanda tangan pemohon di kiri dan QR analis IT di kanan.
- Setiap lembar Serah Terima dimulai dengan AddPage setelah pengajuan/analisis
  selesai, menampilkan tanggal, nama perubahan/fitur, unit, penyelesaian,
  status/catatan UAT, pernyataan, QR penyerah IT di kiri, dan tanda tangan/nama
  penerima di kanan. Beberapa catatan serah terima historis masing-masing dimulai
  pada halaman baru.
- Teks panjang mengalir dengan MultiCell; tanda tangan/QR ditempatkan dalam
  blok utuh dan kursor dilanjutkan di bawah gambar. Footer kanan bawah setiap
  halaman mencantumkan Halaman X dari Y untuk seluruh dokumen gabungan.

QR arsip memuat JSON lokal berisi nomor tiket, jenis/ID catatan, nama staf,
tanggal, serta SHA-256 yang sama dengan fingerprint catatan pada UAT. QR tidak
mengandung token/link UAT berbatas waktu sehingga tetap terbaca pada arsip lama.
Ini adalah identitas/fingerprint catatan aplikasi, bukan sertifikat tanda tangan
digital dari penyelenggara sertifikasi. Data yang belum tersedia ditandai dengan
keterangan, tanpa membuat tanda tangan atau identitas pengganti.

Verifikasi:

```powershell
C:\xampp\php\php.exe docs/test_portal_pdf.php --http
```

Suite menjalankan 151 pemeriksaan PDF/backend dan 45 pemeriksaan HTTP. Lima PDF
nyata diuji: kedua formulir pendek (2 halaman), kedua formulir dengan pengajuan
panjang (3 halaman; serah terima mulai halaman 3), serta penyelesaian panjang
(5 halaman). Pemeriksaan mencakup media A4, akhir teks di stream PDF, footer
terisi pada setiap halaman, penempatan dua PNG dan dua QR, kesesuaian fingerprint,
snapshot/legacy, gambar tidak sah, autentikasi, semua status yang tidak boleh
dicetak, HEAD/POST, ikon pada dua dashboard, dan binary tanpa HTML/warning.
Chrome menjalankan 17 pemeriksaan ikon/routing/pembukaan PDF, termasuk klik
printer nyata dan membuka PDF kedua jenis serta contoh panjang. Pengujian memakai
database sementara dan tidak mengubah tiket operasional.

## Verifikasi Serah Terima oleh Pemohon

Tombol hijau **Verifikasi Serah Terima** berada di kanan judul Hasil Penyelesaian
IT, hanya untuk tiket Menunggu UAT dengan serah terima terakhir yang belum
diterima. Tombol membuka dialog pada halaman yang sama. Modal berisi radio
Berhasil/Diterima dengan catatan, catatan pengujian, dropdown nama penerima yang
awalnya memilih pengaju dan dapat diganti dengan pegawai lain, serta canvas tanda tangan PNG. Catatan
wajib diisi untuk Diterima dengan catatan, opsional untuk Berhasil. Semua nilai
divalidasi kembali di server; tanda tangan kosong, SVG, dan gambar tidak sah ditolak.

Nama Penerima memakai Select2 dengan `allowClear` dan tanpa tags/nama bebas.
Default dicocokkan dari `tiket_pengajuan.nama_pengaju` ke satu pegawai aktif pada
master SIMRS. Karena header tiket lama hanya menyimpan nama, nama yang tidak
ditemukan atau sama pada lebih dari satu pegawai tidak ditebak ID-nya; pengguna
diminta mencari dan memilih penerima. Gangguan koneksi master tidak menghalangi
peninjauan rincian UAT, tetapi persetujuan tidak dapat disimpan tanpa validasi pegawai.

Pencarian melalui `portal-tiket/uat-pegawai.php` menggunakan koneksi secondary
`sik9.pegawai` di `192.168.1.4`, sesuai IP SIMRS yang telah dikonfirmasi. Frontend
baru mengirim AJAX setelah minimal tiga huruf (delay 350ms); backend menerapkan
batas yang sama sebelum query master, paginasi 20 hasil, dan hanya pegawai aktif.
Endpoint memerlukan token UAT yang masih menunggu verifikasi/belum kedaluwarsa
serta CSRF sesi portal, dengan batas 60 pencarian per menit. Dropdown ditempatkan
di dalam wrapper field pada dialog agar posisi dan fokus tetap benar saat modal
digulir di desktop/ponsel.

Form mengirim `id_pegawai_penerima`, bukan nama bebas. Backend membaca ulang ID
tersebut pada master pegawai aktif dan menyimpan nama otoritatif sebagai snapshot
`tiket_serah_terima.nama_penerima`. Nama dari POST diabaikan. ID palsu, pegawai
nonaktif, atau master tidak tersedia menolak persetujuan. Tidak ada FK lintas
server atau perubahan skema untuk fitur ini; receipt lama tetap memakai snapshot.

`pt_uat_confirm()` memerlukan POST dan CSRF sesi portal. Dalam satu transaksi,
backend mengunci tiket berdasarkan token, memeriksa kembali tanggal kedaluwarsa
setelah memperoleh lock, serta memastikan status masih Menunggu UAT. Hanya
serah terima terakhir milik tiket tersebut yang diisi status, catatan, nama dan
tanda tangan penerima. Status tiket diubah menjadi Selesai. Kedua perubahan
dibatalkan jika salah satunya gagal. ID tiket, identitas/tanggal staf, dan status
tiket dari input pemohon tidak dipercaya. Pengiriman ulang atau bersamaan tidak
dapat menimpa penerimaan pertama (409).

Sukses merespons redirect 303 ke URL UAT yang sama. Halaman selesai menyembunyikan
tombol dan dialog, serta menampilkan kartu Hasil Serah Terima Pemohon (status,
nama, catatan jika ada, tanda tangan). Token dan tanggal kedaluwarsa asli tetap
tersimpan untuk akses baca sampai batas waktunya; token tidak dapat digunakan
untuk persetujuan kedua. Batas waktu tidak diperpanjang. Formulir gagal validasi
dibuka kembali dengan teks isian dipertahankan dan tanda tangan perlu diisi ulang.
Seluruh respons tetap no-store/no-referrer/noindex. Detail publik anonim tidak
membaca nama/gambar penerima.

Frontend memakai `assets/dist/js/portal-uat-verification.js` dan tema
`assets/dist/css/portal-tiket.css`. Dialog mendukung keyboard/Escape, canvas
pointer mouse/sentuh, penghapusan tanda tangan, serta validasi catatan kondisional.
Pada ponsel, tombol aksi menyusun vertikal dan modal dapat digulir.

Verifikasi:

```powershell
C:\xampp\php\php.exe docs/test_portal_uat_confirmation.php --http
node --check assets/dist/js/portal-uat-verification.js
node docs/check_assets.cjs
```

Suite menjalankan 62 pemeriksaan backend, termasuk kedua jenis tiket, isian
palsu/tidak lengkap, CSRF, signature kosong, token expired/tepat batas, penolakan
status, kegagalan update dua arah/rollback, persetujuan bersamaan, replay,
riwayat handover, kecocokan default master, penolakan ID/nama bebas dan pegawai
nonaktif, master tidak tersedia, serta receipt selesai. Tambahan 53 pemeriksaan HTTP memeriksa
guest GET/HEAD, default nama, error/escaping, redirect dan render selesai,
penyembunyian modal, expired/404, QR setelah selesai, AJAX ber-CSRF, minimal
huruf tanpa query master, hasil pegawai aktif dan kegagalan koneksi. Chrome
menjalankan 21 pemeriksaan dropdown desktop/ponsel: pilihan default, clear,
penggantian penerima, tidak ada AJAX sebelum tiga huruf, tanpa tags/nama bebas,
ukuran/posisi field saat digulir, serta simpan dengan tanda tangan mouse/sentuh.
Pengujian layout sebelumnya juga mencakup pemindaian kedua QR pada ukuran 176px,
catatan wajib, dan reload receipt. Semua memakai
database sementara. Server uji memakai folder sesi dan temporary yang dapat
ditulis dalam `.codex_tmp`, tanpa mengubah konfigurasi PHP operasional.

## Master Sistem

Tombol **Master Sistem** berada di kanan atas judul Portal Tiket IT. Halamannya
memakai route dashboard yang sama dengan tambahan `&view=master_sistem`, baik
untuk Staff maupun Kepala Ruangan yang aktif. Sidebar Portal Tiket IT tetap aktif.

Halaman menampilkan nama, deskripsi, status aktif Ya/Tidak, dan aksi. **Tambah
Sistem** dan **Edit** membuka form dengan nama (maksimal 100 karakter), deskripsi
(maksimal 10.000 karakter), serta toggle aktif. **Nonaktifkan** hanya mengubah
`is_active` menjadi 0; **Aktifkan** mengembalikannya ke 1. Tidak ada hapus permanen.
Form publik hanya mengambil sistem aktif dan memvalidasi ulang statusnya saat
penyimpanan, termasuk jika sistem dinonaktifkan setelah formulir dibuka. Relasi
serta riwayat tiket lama tetap tersedia di antrean internal.

Semua perubahan memerlukan POST, token CSRF sesi login, dan verifikasi ulang akun
aktif. Query memakai parameter, output di-escape, perubahan dijalankan dalam
transaksi, dan keberhasilan memakai redirect 303 agar refresh tidak mengulang
penyimpanan. Modul ini memakai tabel yang sudah ada, tanpa migrasi tambahan.

Pengujian terisolasi: `C:\xampp\php\php.exe docs/test_portal_master_sistem.php`.
Mencakup kedua role, validasi input/akses/CSRF, penonaktifan dan aktivasi kembali,
integritas FK tiket, penolakan pengajuan ke sistem nonaktif, serta rollback error.
Hasil verifikasi: 33 pemeriksaan Master Sistem dan 26 pemeriksaan antrean lolos.
Chrome headless menjalankan 18 pemeriksaan tambahan untuk posisi tombol,
routing kedua role, tambah/edit/nonaktifkan/aktifkan, escaping teks, penolakan
akses/CSRF, serta form dan tabel responsif pada desktop 1440px dan mobile 390px.
Seluruh pengujian memakai database serta sesi sementara di localhost.

## Form Change Request

URL: `/it-rspi2/portal-tiket/change-request.php`. Kedua form memakai dua kolom
pada desktop/tablet (mulai 768px) dan satu kolom pada layar lebih kecil. Urutan
pengisian tetap data otomatis, sistem, unit, pengaju, WhatsApp, rincian perubahan,
tanda tangan, lalu **SIMPAN**. Nomor tiket, jenis `Change Request`, dan tanggal
read-only. Tanggal ditampilkan `dd-mm-yyyy HH:mm WITA` pada zona waktu default
Asia/Makassar; konfigurasi `TIMEZONE` dapat menyesuaikannya.

WhatsApp, area tanda tangan, dan tombol aksi menggunakan lebar penuh. Isian
rincian terakhir yang tidak memiliki pasangan juga memenuhi satu baris.
Layout kedua form diverifikasi di Chrome pada lebar 1440, 768, 390, dan 320px,
termasuk lebar Select2/canvas dan tidak adanya overflow horizontal.

Nomor benar-benar dicadangkan melalui transaksi penghitung saat form dibuka.
Redirect ke URL dengan kunci formulir acak mempertahankan nomor/tanggal saat
refresh maupun validasi gagal. Kunci hanya berlaku dalam sesi publik pemiliknya.
Maksimal 20 formulir dipertahankan per sesi; formulir lama dapat perlu dibuka ulang.
Nomor, tanggal, jenis, status, nama pegawai, dan nama unit tidak dipercaya dari POST.
Form yang ditinggalkan atau sesinya berakhir dapat meninggalkan celah nomor urut.
Tidak ada header tiket kosong yang dibuat hanya karena halaman dibuka.

- Sistem: `master_sistem` yang `is_active=1`. SIMRS, APTD, MLITE telah diisi aktif
  pada database target sesuai persetujuan pengguna.
- Unit: master standar aplikasi `tb_lokasi`, disimpan sebagai snapshot `nama_lokasi`.
- Nama: AJAX `pegawai.php`, sumber `192.168.1.4/sik9.pegawai`, hanya pegawai AKTIF.
  Pencarian baru berjalan setelah minimal tiga **huruf**, termasuk validasi backend;
  angka/tanda baca tidak dihitung. Hasil hanya memuat ID dan nama, dipaginasi 20 baris.
  Nama dipilih menggunakan ID dan dibaca ulang dari SIMRS ketika menyimpan.
- WhatsApp: divalidasi dan dinormalisasi ke digit internasional (awalan `0` menjadi
  `62`) agar siap digunakan ketika pengiriman magic link dibuat.
- Signature: canvas mouse/jari/stylus, tombol hapus, PNG Base64; backend memvalidasi
  format, ukuran, dan keberadaan coretan, lalu menulis ulang PNG sebelum disimpan.

Simpan menggunakan prepared statement dan satu transaksi untuk `tiket_pengajuan`
beserta `tiket_detail_request`. Kegagalan detail membatalkan header. Sistem aktif
dan unit diperiksa ulang di dalam transaksi; pegawai diperiksa ulang melalui SIMRS.
CSRF wajib. Kirim ulang formulir yang sama mengembalikan tiket yang sama, kemudian
redirect menuju pesan sukses, nomor tiket, dan status **Menunggu Analisis**.
Token UAT tetap NULL; pengiriman WhatsApp/UAT belum menjadi bagian tahap ini.
Validasi server yang gagal mempertahankan isian teks; tanda tangan perlu diisi ulang.

### Koneksi dan upgrade

Konfigurasi lazy terpisah: `config/portal_tiket.php`. Contoh variabel tersedia di
`config/portal_tiket.env.example`. Kredensial `PORTAL_DB_*` dan `PORTAL_PEGAWAI_*`
dapat diatur terpisah; jika tidak disediakan, username/password mengikuti `DB_*`.
Host utama default `192.168.1.108` (atau `DB_SERVER` jika dikonfigurasi eksplisit),
database `it-rspi`; secondary default `192.168.1.4`, database `sik9`. Aplikasi portal
hanya menjalankan SELECT pada koneksi pegawai. Gunakan akun secondary dengan hak
SELECT saja ketika tersedia. Error koneksi ditampilkan sebagai pesan umum kepada
publik; rincian ditulis ke log server.

Instalasi baru memakai SQL utama yang sudah diperbarui. Untuk instalasi lama,
jalankan **`Database/2026-10-20_portal_tiket_reservasi_nomor.sql`**. Migrasi ini
mengganti trigger penomoran tanpa menghapus atau mengubah data tiket. Migrasi
telah diterapkan pada server target. Trigger tetap mendukung penomoran otomatis
untuk INSERT lama yang mengosongkan `no_tiket`; nomor eksplisit hanya diterima
ketika sesuai variabel koneksi reservasi yang diisi backend dari sesi server.
Backend menghapus variabel koneksi tersebut setelah operasi simpan selesai.

### Verifikasi Change Request

Jalankan `C:\xampp\php\php.exe docs/test_portal_change_request.php` dari root proyek.
Skrip CLI membuat database sementara pada localhost, menguji 44 skenario, lalu
menghapus database uji. Memerlukan hak CREATE/DROP database pada server lokal;
tidak menulis ke database bisnis. Cakupan mencakup validasi, tiga-huruf backend,
pegawai/sistem nonaktif, PNG kosong/rusak, manipulasi nilai otomatis, rollback
detail, retry idempoten, dan kompatibilitas trigger penomoran lama.

Chrome diuji pada 1440x1000, 390x844, dan 320x568: dropdown AJAX tidak memanggil
backend pada dua huruf, aktif mulai tiga huruf, signature touch/mouse/hapus,
validasi kelengkapan, penyimpanan relasional, pesan sukses, dan refresh yang stabil.
Seluruh pengajuan contoh diuji pada database terisolasi. Pemeriksaan pada database
target hanya membuka form, memeriksa master, dan mencari pegawai; tidak menyimpan
tiket contoh.

## Form Fitur Baru

URL publik: `/it-rspi2/portal-tiket/fitur-baru.php`, juga dapat dibuka dari kartu
**Form Fitur Baru** pada landing. UI menggunakan template `request_form.php`
dan JavaScript `assets/dist/js/portal-request-form.js` yang sama dengan Change Request. Jenis formulir
ditetapkan oleh entry point PHP, bukan dari parameter/payload pengguna.

Nomor otomatis menggunakan **`NEWFEAT-YYMM-urutan`**, misalnya `NEWFEAT-2610-002`.
Kedua jenis tiket berbagi penghitung bulanan yang sudah ada, sehingga urutan
tidak dimulai ulang per awalan. Nomor dicadangkan saat membuka form, tetap sama
ketika refresh/validasi gagal, dan tidak dipotong setelah 999. Nomor, jenis
**Fitur Baru**, serta tanggal pengajuan bersifat read-only dan diambil dari server
ketika menyimpan. Reservasi tidak dapat dipakai silang antara kedua jenis form;
reservasi Change Request lama tetap didukung.

Sistem aktif, unit, AJAX pegawai minimal tiga huruf, normalisasi WhatsApp, dan
validasi PNG tanda tangan mengikuti Change Request. Sumber pegawai tetap
**192.168.1.4/sik9.pegawai**, sesuai konfirmasi alamat SIMRS sebelumnya, bukan
alamat `192.198.1.4` pada deskripsi contoh. Nama dibaca ulang berdasarkan ID
pegawai aktif ketika menyimpan; input nama/jenis/status buatan tidak dipercaya.

| Label wajib | Kolom `tiket_detail_request` |
| --- | --- |
| Nama Fitur Baru | `nama_perubahan_fitur` |
| Latar Belakang & Kebutuhan | `latar_belakang` |
| Deskripsi & Alur Kerja | `deskripsi_alur_kerja` |
| Target Pengguna | `target_pengguna` |
| Output yang Diharapkan | `output_diharapkan` |

Backend hanya menulis kolom sesuai definisi jenis formulir; kolom khusus Change
Request tetap NULL untuk Fitur Baru meskipun dikirim dalam payload. Header dan
detail disimpan dalam satu transaksi, status awal **Menunggu Analisis**. Kirim
ulang tidak menambah tiket/detail ganda. Sesudah berhasil, redirect menampilkan
pesan sukses dan nomor tiket. Token UAT belum dibuat pada saat pengajuan.

SQL instalasi dan migrasi `2026-10-20_portal_tiket_reservasi_nomor.sql` telah
diperbarui agar INSERT otomatis tanpa reservasi juga memilih awalan berdasarkan
`jenis_tiket`. Migrasi trigger terbaru telah diterapkan di `192.168.1.108/it-rspi`;
tidak memerlukan tabel atau kolom tambahan.

Verifikasi: `C:\xampp\php\php.exe docs/test_portal_fitur_baru.php` lulus 44
pemeriksaan backend terisolasi (isian wajib, data nonaktif, CSRF, tanda tangan,
penomoran, tipe reservasi, pemetaan kolom, rollback, dan retry idempoten).
Sebanyak 44 pemeriksaan regresi Change Request juga lulus. Chrome memverifikasi
klik dari landing, direct link, AJAX tiga huruf, read-only, canvas mouse/touch,
simpan dan refresh sukses pada desktop/mobile, serta tampilan lebar 320px.
Hasil simpan browser diverifikasi langsung pada database fixture: dua Fitur Baru
dan satu Change Request dengan pemetaan kolom yang sesuai. Database fixture
dihapus; tidak ada tiket contoh yang ditulis ke database bisnis.

## Notifikasi Telegram tiket baru

Penyimpanan bersama pada `portal-tiket/request_helpers.php` memanggil
`notification_helpers.php` setelah transaksi header dan detail berhasil **commit**.
Berlaku untuk Change Request dan Fitur Baru. Membuka formulir, kegagalan validasi,
rollback, dan POST ulang tiket yang sudah tersimpan tidak mengirim notifikasi.
Pesan memuat nomor tiket, jenis layanan, nama sistem, nama pengaju, unit, judul
perubahan/fitur, dan ajakan melakukan Analisis melalui dashboard IT-RSPI.
Nama sistem/unit/pegawai memakai data master yang sudah diverifikasi, bukan nama
yang dikirim browser. Nomor WhatsApp, tanda tangan, dan token UAT tidak dikirim.
Nilai dinamis di-escape, dengan format HTML sesuai
[Telegram Bot API sendMessage](https://core.telegram.org/bots/api#sendmessage).

Pengiriman memakai `config/telegram.php` dan konfigurasi `.env` yang sudah dipakai
modul cuti/lembur/pengajuan: `TELEGRAM_BOT_TOKEN`, `TELEGRAM_CHANNEL_ID` (atau
`TELEGRAM_CHAT_ID` jika CHANNEL_ID kosong), serta `TELEGRAM_MESSAGE_THREAD_ID`
opsional untuk topik. Bot perlu izin mengirim pesan ke tujuan. Tidak ada token
atau chat ID asli di source code, payload browser, maupun log kegagalan.
Contoh pengaturan tersedia di `config/portal_tiket.env.example`.
`PORTAL_TELEGRAM_ENABLED=0` menonaktifkan notifikasi portal saja; default aktif.

Portal memakai cURL dengan batas koneksi 2 detik dan total pengiriman 3 detik.
Jika cURL tidak tersedia, konfigurasi kosong, timeout, respons API gagal/invalid,
atau terjadi exception, tiket tetap tersimpan dan pemohon tetap mendapat
redirect 303 ke konfirmasi sukses. Kegagalan dicatat dengan ID tiket/kode HTTP
tanpa isi pesan, token, atau detail exception. Helper bersama memeriksa status
HTTP **dan** nilai JSON `ok`; pemanggil lama tetap mengirim plain text.

Notifikasi mencoba mengirim satu kali setelah commit, tanpa retry otomatis atau
antrean pengiriman ulang. Kegagalan Telegram tidak dijadikan alasan untuk
mengulang transaksi tiket. Tidak memerlukan migrasi database tambahan.

`C:\xampp\php\php.exe docs/test_portal_telegram.php` menjalankan 150 pemeriksaan
terisolasi: observer database terpisah membuktikan header/detail sudah dikomit
sebelum pengiriman, escaping dan sumber nama, target/topik, rollback, idempotensi,
API gagal/JSON rusak, serta PRG dan konfirmasi sukses pada kedua formulir melalui
HTTP. Transport produksi diuji ke server loopback termasuk timeout cURL nyata
3 detik dan fallback stream untuk pemanggil lama. Pengujian tidak mengirim pesan
Telegram asli dan menghapus database, server, serta fixture sementara.
Regresi Change Request (44), Fitur Baru (44), dan Master Sistem (33) menonaktifkan
Telegram dan tetap lolos. Kredensial serta data tiket bisnis tidak diubah.

## Status penerapan

Terpasang pada server target pada 20 Oktober 2026: lima tabel utama,
`tiket_nomor_bulanan`, empat foreign key lokal, dan trigger penomoran.
Seluruh tabel baru masih kosong setelah instalasi; tidak ada data bisnis
contoh yang dimasukkan ke server target.

Pengujian pada database lokal terisolasi dengan MariaDB 10.4.32 lulus
23 pemeriksaan: relasi, nilai default, nomor bulanan, urutan lebih dari 999,
rollback, konkurensi dua koneksi, token unik dan case sensitive, kelengkapan
UAT, serta penolakan instalasi ulang tanpa mengubah data. Database uji
dihapus setelah pengujian. Metadata enam tabel, empat FK, dan trigger juga
diverifikasi langsung pada server target.

## Kondisi sebelum instalasi

Pemeriksaan metadata pada 20 Oktober 2026 sebelum instalasi: kelima tabel portal
belum tersedia di server target. Master pegawai berada pada server terpisah,
**192.168.1.4**, database **sik9**, tabel **pegawai**, dengan `id INT` signed
sebagai primary key (InnoDB; MariaDB 10.1.25).

Sesuai keputusan integrasi, `id_staf_it` dan `id_staf_penyerah` menyimpan ID
pegawai SIMRS sebagai **referensi logis tanpa FK lintas server**. Validasi
keberadaan pegawai dilakukan backend aplikasi melalui koneksi SIMRS. Database
portal tidak membuat salinan master pegawai atau mengubah `tb_user`.

## Instalasi pertama

1. Hubungkan phpMyAdmin ke **192.168.1.108**, lalu pilih database **it-rspi**.
2. Gunakan akun yang memiliki hak instalasi di bawah.
3. Gunakan tab **Import** dan unggah file SQL lengkap. Alternatif melalui CLI:

   ```text
   C:\xampp\mysql\bin\mysql.exe -h 192.168.1.108 -u NAMA_USER -p --default-character-set=utf8mb4
   SOURCE C:/xampp/htdocs/it-rspi2/Database/2026-10-20_portal_tiket_layanan_it.sql;
   ```

4. Pastikan enam tabel dan trigger `trg_tiket_pengajuan_nomor` terbentuk.
   Isi `master_sistem` dengan sistem resmi yang akan ditampilkan di portal,
   misalnya SIMRS, APTD, dan MLITE. Master tidak diisi otomatis.

Hak yang diperlukan: CREATE, REFERENCES, CREATE ROUTINE, ALTER ROUTINE,
EXECUTE, dan TRIGGER. Installer mengaktifkan mode strict selama instalasi
dan mengembalikan mode sesi sebelumnya setelah sukses. CHECK tambahan
menolak ENUM yang tidak dikenal meskipun koneksi aplikasi tidak strict.
Jangan memakai opsi
`--force` atau melanjutkan import setelah error. DDL MariaDB melakukan
implicit commit sehingga kegagalan setelah pembuatan tabel dimulai dapat
meninggalkan instalasi parsial. Prosedur instalasi dihapus setelah sukses;
jika instalasi ditolak, prosedur dapat tertinggal dan akan diganti saat retry.

File ini adalah instalasi pertama. Pengulangan ditolak jika salah satu tabel
portal sudah tersedia; tidak ada `DROP TABLE` atau perubahan tabel lama.
Untuk database yang sudah memakai portal, periksa `SHOW CREATE TABLE` dan
buat migrasi `ALTER TABLE` terpisah berdasarkan struktur aktual.

## Tabel dan relasi

| Tabel | Isi dan relasi |
| --- | --- |
| `master_sistem` | Master sistem; `is_active` default 1 dan dibatasi 0/1. |
| `tiket_pengajuan` | Header, kontak WhatsApp sebagai teks, tanda tangan Base64, status, token dan kedaluwarsa UAT; FK ke sistem. |
| `tiket_detail_request` | Rincian Change Request/Fitur Baru; FK ke tiket. |
| `tiket_analisis_it` | Prioritas, analisis dan keputusan; FK ke tiket, ID staf merujuk secara logis ke pegawai SIMRS. |
| `tiket_serah_terima` | Penyelesaian IT, petugas penyerah dan penerimaan UAT; FK ke tiket, ID penyerah merujuk secara logis ke pegawai SIMRS. |
| `tiket_nomor_bulanan` | Tabel pendukung untuk penghitung nomor bulanan yang aman saat pengajuan bersamaan. |

Seluruh tabel memakai InnoDB dan utf8mb4. ID memakai INT signed agar konsisten
dengan `sik9.pegawai.id`. Empat FK lokal memakai `ON DELETE RESTRICT` dan
`ON UPDATE RESTRICT` untuk melindungi riwayat tiket dan tanda tangan.
Kolom `id_tiket` pada tabel anak tidak dibuat UNIQUE: satu tiket dapat memiliki
lebih dari satu rincian, analisis, atau serah terima, termasuk pengujian ulang.

## Nomor tiket otomatis

Saat INSERT header di luar alur reservasi formulir, jangan isi `no_tiket` (atau kirim string kosong).
Trigger membuat `REQ-YYMM-urutan` untuk Change Request dan
`NEWFEAT-YYMM-urutan` untuk Fitur Baru berdasarkan `tgl_pengajuan`, misalnya
`REQ-2610-001` dan `NEWFEAT-2610-002` dengan penghitung bulanan bersama.
Urutan dimulai dari 001 setiap bulan, tetap menjadi 1000 setelah 999,
dan tidak menggunakan `MAX()+1`. Update penghitung mengunci satu baris bulan
selama transaksi sehingga dua pengajuan bersamaan memperoleh nomor berbeda.
Pada INSERT otomatis tanpa reservasi, rollback tiket juga me-rollback alokasi
nomor. Reservasi formulir sudah dikomit saat form dibuka sehingga tidak dikembalikan
ketika validasi/simpan gagal atau form ditinggalkan. Bulan historis mengikuti tanggal
pengajuan yang dikirim, sehingga backend harus menentukan tanggal yang sah.
Jangan mengubah nomor/tanggal tiket yang sudah diterbitkan atau mereset penghitung.

Simpan header beserta detail request dalam satu transaksi aplikasi. Jangan
memakai `INSERT IGNORE` untuk penerbitan nomor. Penomoran tidak bergantung
pada ID AUTO_INCREMENT; pembatalan transaksi tetap dapat melewati angka ID.

## UAT dan batas tanggung jawab DDL

Status tiket awal adalah `Menunggu Analisis`. Token dan kedaluwarsanya harus
sama-sama NULL atau sama-sama terisi; token non-NULL harus unik dengan
perbandingan case sensitive. Token tidak dibuat saat tiket baru diajukan.
Saat IT mengirim UAT, backend harus membuat token kriptografis, misalnya
`bin2hex(random_bytes(32))`, lalu mengisi batas waktu satu bulan kalender. Jika menyimpan
hash SHA-256 token, hash kembali token dari tautan sebelum pencarian.

IT dapat menyimpan serah terima sebelum pemohon melakukan UAT. Karena itu,
`status_pengujian_uat`, `nama_penerima`, dan `ttd_penerima` boleh NULL selama
menunggu. Setelah hasil UAT diisi, CHECK mewajibkan nama dan tanda tangan;
`Diterima dengan catatan` juga mewajibkan catatan yang tidak kosong. Untuk
`Berhasil`, catatan bersifat opsional. Seluruh kolom khusus jenis request
tetap nullable sesuai spesifikasi.

DDL ini tidak membuat QR tanda tangan atau pengiriman WhatsApp. Backend memvalidasi rincian
sesuai jenis tiket, sistem aktif, pemetaan akun login ke pegawai IT, gambar
tanda tangan beserta ukurannya, transisi status, serta token UAT yang belum
kedaluwarsa. Penyelesaian UAT dan pencatatan penerimaan dilakukan dalam
satu transaksi. Status Selesai menolak persetujuan ulang; token tetap tersedia
untuk membaca receipt hingga kedaluwarsa aslinya. Identitas staf harus
diambil dari akun login yang telah dipetakan, bukan dipercaya dari input form.
