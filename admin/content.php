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
// Data Verifikasi Cuti staff
else if ($_GET['unit'] == "cuti"){
  require_once("unit/cuti/cuti.php");
}
// Data Logbook
else if ($_GET['unit'] == "logbook"){
  require_once("unit/logbook/logbook.php");
}
?> 