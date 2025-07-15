<?php 
if (isset($_GET['unit']) && $_GET['unit'] == "user") {
    require_once("unit/user/user.php");
} else if (isset($_GET['unit']) && $_GET['unit'] == "edit_user") {
    require_once("unit/user/update.php");
} else if (isset($_GET['unit']) && $_GET['unit'] == "delete_user") {
    require_once("unit/user/delete_user.php");
    // Data Pengajuan Barang
} else if (isset($_GET['unit']) && $_GET['unit'] == "pengajuan") {
    require_once("unit/pengajuan/pengajuan.php");
    // Beranda
} else if (!isset($_GET['unit']) || $_GET['unit'] == "beranda") {
    require_once("unit/beranda.php");
} else {
    echo '<div class="alert alert-danger">Halaman tidak ditemukan.</div>';
}
?> 