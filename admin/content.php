<?php 
//Dashboard
if ($_GET['unit'] == "beranda"){
  require_once("unit/beranda.php");
}
// User
else if ($_GET['unit'] == "user"){
  require_once("unit/user/user.php");
}
else if ($_GET['unit'] == "edit_user"){
  require_once("unit/user/update.php");
}
else if ($_GET['unit'] == "delete_user"){
  require_once("unit/user/delete.php");
}
// Data Pengajuan Barang
else if ($_GET['unit'] == "pengajuan"){
  require_once("unit/pengajuan/pengajuan.php");
}
else if ($_GET['unit'] == "cetak_pengajuan"){
  require_once("unit/pengajuan/lap_pemintaan_brg.php");
}
// Data Barang
else if ($_GET['unit'] == "barang"){
  require_once("unit/barang/barang.php");
}

// Data Perbaikan Barang
else if ($_GET['unit'] == "perbaikan"){
  require_once("unit/perbaikan/perbaikan.php");
}
else if ($unit == "detail_perbaikan"){
  require_once("unit/perbaikan/detail_perbaikan.php");
}
else if ($unit == "export_perbaikan"){
  require_once("unit/perbaikan/export_perbaikan.php");
}

// Pemindahan Barang
else if ($unit == "pemindahan"){
  require_once("unit/pemindahan/pemindahan.php");
}
else if ($unit == "detail_pemindahan"){
  require_once("unit/pemindahan/detail_pemindahan.php");
}

// KerusakanBarang
else if ($unit == "barang_rusak"){
  require_once("unit/kerusakan/barang_rusak.php");
}
else if ($unit == "grafik_barang"){
  require_once("unit/kerusakan/grafik_barang.php");
}
// Data Verifikasi Lembur staff
else if ($_GET['unit'] == "lembur"){
  require_once("unit/lembur/lembur.php");
}
// Data Logbook
else if ($_GET['unit'] == "logbook"){
  require_once("unit/logbook/logbook.php");
}
// Picare
else if ($unit == "daftar"){
  require_once("unit/pi-care/pi-care_daftar.php");
}
else if ($unit == 'lap_pi-care_daftar'){
  require_once("unit/pi-care/lap_pi-care_daftar.php");
}
else if ($unit == "batal"){
  require_once("unit/pi-care/pi-care_batal.php");
}
else if ($unit == "alasan"){
  require_once("unit/pi-care/pi-care_alasan.php");
}
else if ($unit == "lap_pi-care_batal"){
  require_once("unit/pi-care/lap_pi-care_batal.php");
}
else if ($unit == "rekap"){
  require_once("unit/pi-care/pi-care_rekap.php");
}
else if ($unit == "lap_pi-care_rekap"){
  require_once("unit/pi-care/lap_pi-care_rekap.php");
}
?> 