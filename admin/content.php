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
?> 