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
// Data Pengajuan Barang
else if ($_GET['unit'] == "barang"){
  require_once("unit/barang/barang.php");
}
// Data Verifikasi Lembur staff
else if ($_GET['unit'] == "lembur"){
  require_once("unit/lembur/lembur.php");
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