# Perapian proyek — 21 Oktober 2026

Audit mencakup 2.966 file awal, di luar `.git`. Penghapusan dibatasi pada paket demo/template dan uji coba lama yang tidak dipanggil oleh kode aplikasi. Modul yang bisa digunakan melalui URL langsung tetap dipertahankan meskipun tidak memiliki tautan menu.

## Hasil

- Menghapus 395 file (sekitar 36,6 MiB): dokumentasi/demo AdminLTE dalam `assets/docs`, halaman contoh `assets/pages`, sumber/tool build `assets/build`, metadata paket template, `assets/.github`, contoh chart `assets/chart`, script dashboard demo, dan enam file `test_chart.html`, `test_pdf.php`, `test_print.php` PI-Care pada folder admin/staf.
- Memindahkan 1.993 file. Library frontend dipindahkan sebagai satu kesatuan dari `assets/plugins` ke `assets/dist/plugins` agar struktur font, gambar, dan dependensi relatif tetap utuh. Lisensi tetap disimpan.
- Memperbarui referensi pada 35 file, termasuk dashboard admin/staf/calon, login/registrasi, form publik, scanner, laporan, dan pengujian Monev.
- Memperbaiki referensi Bootstrap yang sebelumnya menunjuk `assets/vendor` yang tidak ada, serta gambar pengganti bukti struk yang sebelumnya menunjuk `/images/placeholder.png` yang tidak ada.
- Memindahkan snapshot SQL pada root ke `Database/it-rspi-root-snapshot.sql`. Isinya berbeda dari `Database/it-rspi (1).sql`; keduanya dipertahankan.

| Aset | Lokasi sekarang |
| --- | --- |
| Tema portal publik | `assets/dist/css/portal-tiket.css` |
| Form, pencarian pegawai, tanda tangan | `assets/dist/js/portal-request-form.js` |
| Kartu statistik tiket | `assets/dist/css/portal-tiket-summary.css` |
| Salin tautan UAT | `assets/dist/js/portal-uat-copy.js` |
| Monev | `assets/dist/css/monev.css`, `assets/dist/js/monev.js` |
| Decoder QR scanner | `assets/dist/js/html5-qrcode.min.js` |
| Chart 4 yang sebelumnya berada di folder CSS | `assets/dist/js/chart4.js` |
| Library frontend | `assets/dist/plugins/` |

`.env`, konfigurasi, isi SQL/migrasi, unggahan pengguna, font TCPDF, dan seluruh modul bisnis dipertahankan. File unggahan tidak dinilai berdasarkan pencarian nama file karena referensinya dapat tersimpan dalam database. Paket library dipertahankan lengkap agar pemuatan dependensi secara dinamis tetap berfungsi. Source CSS/JS yang dipindahkan tidak diubah isinya.

PI-Care, rekap pasien, pembaruan resep, formulir penambahan user lama, dan alias cetak Monev tetap disimpan: akses URL langsung atau pemanggilan dinamis belum cukup dibuktikan tidak terpakai. Tidak ada penghapusan data atau migrasi pada database operasional dalam perapian ini.

## Pemeriksaan

- Sebelum perubahan: 281 PHP lolos lint; ditemukan dua referensi aset tidak tersedia.
- Setelah perubahan: 277 PHP lolos lint; JavaScript aplikasi/decoder lolos pemeriksaan sintaks.
- `node docs/check_assets.cjs`: 150 referensi lokal, 55 aset unik, 15 stylesheet; tidak ada aset hilang dan tidak ada `.css`/`.js` di luar `assets/dist`.
- Mode HTTP pemeriksa aset: 55 permintaan berhasil pada server uji dan 55 permintaan berhasil melalui Apache/XAMPP (`http://localhost/it-rspi2/`). Login utama merespons HTTP 200.
- Sembilan suite Portal Tiket: 506 pemeriksaan backend/HTTP berhasil, menggunakan database sementara.
- Monev: 140 pemeriksaan integrasi, 40 pemeriksaan foto/PDF, serta dua suite frontend berhasil.
- Chrome: 50 pemeriksaan berhasil pada login/registrasi, landing, kedua form publik (Select2, tanda tangan, desktop/mobile), daftar/detail publik, scanner QR, antrean/detail/master staf/admin, pemuatan aset Monev, salin tautan dan halaman UAT. CDNs eksternal diblokir dalam pengujian ini agar pemeriksaan fokus pada aset lokal.
- Hash SHA-256 memastikan 1.993 file yang dipindahkan dan 543 file lainnya tetap identik; perubahan isi hanya pada 35 file yang referensinya diperbarui. `git diff --check` berhasil.

Pemeriksaan sintaks dan aset mencakup seluruh proyek. Pengujian alur bisnis menggunakan suite di atas; operasi tambah/ubah/hapus seluruh modul lama dan integrasi ke semua server eksternal belum diuji satu per satu.

Jalankan ulang pemeriksa ini setelah memindahkan atau mengubah referensi aset:

```powershell
node docs/check_assets.cjs
node docs/check_assets.cjs --http=http://localhost/it-rspi2/
node docs/test_monev_frontend.cjs
node docs/test_monev_bundle_frontend.cjs
```

Pemeriksa aset membaca referensi lokal pada PHP/HTML, menelusuri `url()` stylesheet, dan dapat memeriksa ketersediaan HTTP. Referensi yang dibentuk sepenuhnya secara dinamis tetap memerlukan pengujian browser.

## Salinan pemulihan

Salinan sebelum perubahan berada di `C:\xampp\htdocs\.codex_tmp\it-rspi2-cleanup-20261021\before`, dilengkapi inventaris/hash, rencana pemindahan, daftar perubahan referensi, serta `.htaccess` yang menolak akses Apache. Akses folder backup dan file di dalamnya telah diperiksa dan merespons HTTP 403. `.env` tidak disalin; file aslinya tetap utuh. Simpan backup ini sampai pengujian operasional selesai. Jangan menjalankan PHP built-in server dengan root yang mencakup backup pada antarmuka jaringan publik karena server tersebut tidak membaca `.htaccess`.

Rincian file terhapus dan pemindahan: [cleanup-2026-10-21.json](cleanup-2026-10-21.json). Untuk pemulihan, salin hanya file yang diperlukan dari backup ke lokasi asal sesuai manifest; jangan menimpa unggahan atau konfigurasi yang sudah diperbarui setelah tanggal ini.
