# Peta Alur Modul Pengajuan

## Alur sederhana
1. Staff membuka menu pengajuan barang.
2. Staff membuat data pengajuan baru ke `tb_pengajuan` dengan status `diajukan`.
3. Admin membuka daftar pengajuan dan memverifikasi pengajuan.
4. Jika admin menolak, status menjadi `ditolak` dan proses berhenti.
5. Jika admin menyetujui, status menjadi `disetujui` dan tanggal ACC terisi.
6. Staff dapat menginput realisasi barang ke `tb_barang` berdasarkan pengajuan yang sudah disetujui.
7. Jumlah pada `tb_pengajuan` akan berkurang sesuai jumlah barang yang sudah direalisasikan.
8. Admin dapat mencetak surat permintaan barang (SPB) dari data pengajuan.

## Tabel yang terlibat
- `tb_pengajuan`: menyimpan permintaan barang.
- `tb_user`: menyimpan data staff pengaju.
- `tb_barang`: menyimpan barang inventaris hasil realisasi pengajuan.

## Status utama
- `diajukan`: baru diajukan staff, masih bisa diedit atau dihapus oleh staff.
- `disetujui`: sudah di-ACC admin, siap direalisasikan menjadi barang.
- `ditolak`: ditolak admin.
- `selesai`: sudah tersedia di schema, tetapi belum dipakai konsisten di alur kode saat ini.
