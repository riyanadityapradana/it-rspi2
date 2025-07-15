<?php 
$unit = $_GET['unit'] ?? '';
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

?>