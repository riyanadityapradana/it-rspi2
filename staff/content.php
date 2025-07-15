<?php 
//Dashboard
if ($_GET['unit'] == "beranda"){
  require_once("unit/beranda.php");
}
// Barang
else if ($_GET['unit'] == "barang"){
  require_once("unit/barang/barang.php");
}
else if ($_GET['unit'] == "create_barang"){
  require_once("unit/barang/create.php");
}
else if ($_GET['unit'] == "update_barang"){
  require_once("unit/barang/update.php");
}
else if ($_GET['unit'] == "delete_barang"){
  require_once("unit/barang/delete.php");
}
// PengajuanBarang
else if ($_GET['unit'] == "pengajuan"){
  require_once("unit/pengajuan/pengajuan.php");
}
else if ($_GET['unit'] == "create_pengajuan"){
  require_once("unit/pengajuan/create.php");
}
else if ($_GET['unit'] == "update_pengajuan"){
  require_once("unit/pengajuan/update.php");
}
else if ($_GET['unit'] == "delete_pengajuan"){
  require_once("unit/pengajuan/delete.php");
}
// Logbook kegiatan harian
else if ($_GET['unit'] == "logbook"){
  require_once("unit/logbook/logbook.php");
}
else if ($_GET['unit'] == "create_logbook"){
  require_once("unit/logbook/create.php");
}
else if ($_GET['unit'] == "update_logbook"){
  require_once("unit/logbook/update.php");
}
else if ($_GET['unit'] == "delete_logbook"){
  require_once("unit/logbook/delete.php");
}
// Lembur
else if ($_GET['unit'] == "lembur_staff"){
  require_once("unit/lembur/lembur_staff.php");
}
else if ($_GET['unit'] == "create_lembur_staff"){
  require_once("unit/lembur/create_staff.php");
}
else if ($_GET['unit'] == "update_lembur_staff"){
  require_once("unit/lembur/update_staff.php");
}
else if ($_GET['unit'] == "delete_lembur_staff"){
  require_once("unit/lembur/delete_staff.php");
}
else if ($_GET['unit'] == "lembur"){
  require_once("unit/lembur/lembur.php");
}
else if ($_GET['unit'] == "create_lembur"){
  require_once("unit/lembur/create.php");
}
else if ($_GET['unit'] == "detail_lembur"){
  require_once("unit/lembur/detail.php");
}
else if ($_GET['unit'] == "delete_lembur"){
  require_once("unit/lembur/delete.php");
}
// Remote Desktop
else if ($_GET['unit'] == "remote_desktop"){
  require_once("unit/remote_destop/remote_desktop.php");
}
else if ($_GET['unit'] == "create_remote_desktop"){
  require_once("unit/remote_destop/create_desktop.php");
}
else if ($_GET['unit'] == "update_remote_desktop"){
  require_once("unit/remote_destop/update_desktop.php");
}
else if ($_GET['unit'] == "delete_remote_desktop"){
  require_once("unit/remote_destop/delete_desktop.php");
}
else if ($_GET['unit'] == "remote"){
  require_once("unit/remote_destop/remote.php");
}
else if ($_GET['unit'] == "create_remote"){
  require_once("unit/remote_destop/create.php");
}
else if ($_GET['unit'] == "update_remote"){
  require_once("unit/remote_destop/update.php");
}
else if ($_GET['unit'] == "delete_remote"){
  require_once("unit/remote_destop/delete.php");
}
// Picare
else if ($_GET['unit'] == "daftar"){
  require_once("unit/pi-care/pi-care_daftar.php");
}
else if ($_GET['unit'] == "batal"){
  require_once("unit/pi-care/pi-care_batal.php");
}
else if ($_GET['unit'] == "alasan"){
  require_once("unit/pi-care/pi-care_alasan.php");
}
?>