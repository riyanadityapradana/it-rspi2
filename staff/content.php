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
// Rekap kunjungan
else if ($unit == "rekap_pasien_poli"){
  require_once("unit/rekap-kunjungan-pasien/rawat-jalan/rekap_pasien_poli.php");
}
else if ($unit == "rekap_pasien_ranap"){
  require_once("unit/rekap-kunjungan-pasien/rawat-inap/rekap_pasien_ranap.php");
}
  else if ($unit == "rekap_px_usia_ranap"){
    require_once("unit/rekap-kunjungan-pasien/rawat-inap/rekap_px_usia_ranap.php");
  }
else if ($unit == "rekap_px_usia_ralan"){
  require_once("unit/rekap-kunjungan-pasien/rawat-jalan/rekap_px_usia_ralan.php");
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
// Resep Obat Update
else if ($unit == "rsp-obat-update"){
  require_once("unit/rsp-obat-update/rsp-obat-update.php");
}
?>