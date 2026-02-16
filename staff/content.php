<?php 
$unit = isset($_GET['unit']) ? $_GET['unit'] : '';
//Dashboard
if ($unit == "beranda"){
  require_once("unit/beranda.php");
}
// Barang
else if ($unit == "barang"){
  require_once("unit/barang/barang.php");
}
else if ($unit == "create_barang"){
  require_once("unit/barang/create.php");
}
else if ($unit == "update_barang"){
  require_once("unit/barang/update.php");
}
else if ($unit == "delete_barang"){
  require_once("unit/barang/delete.php");
}
else if ($unit == "proses_penyerahan"){
  require_once("unit/barang/proses_penyerahan.php");
}
else if ($unit == "proses_rusak"){
  require_once("unit/barang/proses_rusak.php");
}
else if ($unit == "proses_baik"){
  require_once("unit/barang/proses_baik.php");
}
// Pemindahan Barang
else if ($unit == "pemindahan"){
  require_once("unit/pemindahan/pemindahan.php");
}
else if ($unit == "create_pemindahan"){
  require_once("unit/pemindahan/create.php");
}
else if ($unit == "update_pemindahan"){
  require_once("unit/pemindahan/update.php");
}
else if ($unit == "delete_pemindahan"){
  require_once("unit/pemindahan/delete.php");
}
else if ($unit == "detail_pemindahan"){
  require_once("unit/pemindahan/detail_pemindahan.php");
}
// Perbaikan Barang
else if ($unit == "perbaikan"){
  require_once("unit/perbaikan/perbaikan.php");
}
else if ($unit == "create_perbaikan"){
  require_once("unit/perbaikan/create.php");
}
else if ($unit == "update_perbaikan"){
  require_once("unit/perbaikan/update.php");
}
else if ($unit == "delete_perbaikan"){
  require_once("unit/perbaikan/delete.php");
}
else if ($unit == "detail_perbaikan"){
  require_once("unit/perbaikan/detail_perbaikan.php");
}
else if ($unit == "export_perbaikan"){
  require_once("unit/perbaikan/export_perbaikan.php");
}
// PengajuanBarang
else if ($unit == "pengajuan"){
  require_once("unit/pengajuan/pengajuan.php");
}
else if ($unit == "create_pengajuan"){
  require_once("unit/pengajuan/create.php");
}
else if ($unit == "update_pengajuan"){
  require_once("unit/pengajuan/update.php");
}
else if ($unit == "delete_pengajuan"){
  require_once("unit/pengajuan/delete.php");
}
// User
else if ($unit == "user"){
  require_once("unit/user/user.php");
}
else if ($unit == "update_user"){
  require_once("unit/user/update.php");
}
// Logbook kegiatan harian
else if ($unit == "logbook"){
  require_once("unit/logbook/logbook.php");
}
else if ($unit == "create_logbook"){
  require_once("unit/logbook/create.php");
}
else if ($unit == "update_logbook"){
  require_once("unit/logbook/update.php");
}
else if ($unit == "delete_logbook"){
  require_once("unit/logbook/delete.php");
}
// Lembur
else if ($unit == "lembur"){
  require_once("unit/lembur/lembur.php");
}
else if ($unit == "create_lembur"){
  require_once("unit/lembur/create.php");
}
else if ($unit == "detail_lembur"){
  require_once("unit/lembur/detail.php");
}
else if ($unit== "delete_lembur"){
  require_once("unit/lembur/delete.php");
}
// Remote Desktop
else if ($unit == "remote"){
  require_once("unit/remote_destop/remote.php");
}
else if ($unit == "create_remote"){
  require_once("unit/remote_destop/create.php");
}
else if ($unit == "update_remote"){
  require_once("unit/remote_destop/update.php");
}
else if ($unit == "delete_remote"){
  require_once("unit/remote_destop/delete.php");
}
// Update Profile
else if ($unit == "update_profile"){
  require_once("unit/user/update_profile.php");
}
?>