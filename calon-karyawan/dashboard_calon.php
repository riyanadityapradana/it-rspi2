<?php
session_start();
require_once '../config/koneksi.php'; // Tambahkan baris ini!

if (!isset($_SESSION['id_calon']) || !isset($_SESSION['login_type']) || $_SESSION['login_type'] !== 'calon') {
    header('Location: ../main_login/form_login.php?error=Akses ditolak!');
    exit;
}
if (isset($_GET['unit'])){ $unit = $_GET['unit']; }
ob_start();
$id 	= $_SESSION['id_calon'];
$query 	= "SELECT * FROM tb_calon WHERE id_calon = '$id'";
$admin 	= mysqli_fetch_array(mysqli_query($config, $query));
$nama 	= $admin['nama_lengkap'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Calon Karyawan | IT-RSPI</title>
    <link rel="icon" href="../assets/img/icon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white" style="background:#800000">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></li>
            <li class="nav-item d-none d-sm-inline-block"><a href="#" class="nav-link">Home</a></li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#"><i class="far fa-user"></i></a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <a href="../main_login/form_login.php" class="dropdown-item"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
                </div>
            </li>
        </ul>
    </nav>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="#" class="brand-link" style="background:#800000">
            <img src="../assets/img/icon.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">IT - RSPI</span>
        </a>
        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="../assets/img/icon.png" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info ml-2">
                    <a href="#" class="d-block"><?php echo htmlspecialchars($nama); ?></a>
                    <span class="text-muted small">Calon Karyawan</span>
                </div>
            </div>
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item menu-open">
                        <a href="#" class="nav-link active">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="dashboard_calon.php?unit=cv_user" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Lengkapi Data</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
              <?php require_once ("content.php");?>
            </div>
        </div>
    </div>
</div>
<!-- Modal Lengkapi Data -->
<div class="modal fade" id="modalLengkapiData" tabindex="-1" role="dialog" aria-labelledby="modalLengkapiDataLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLengkapiDataLabel">Lengkapi Data User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="modalLengkapiDataBody">
        <!-- Form akan dimuat di sini -->
      </div>
    </div>
  </div>
</div>
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/dist/js/adminlte.min.js"></script>
<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('#example1').DataTable({
        "lengthChange": false,
        "paging":true,
        "pagingType":"numbers",
        "scrollCollapse": true,
        "ordering":true,
        "info":true,
        "language":{
            "decimal":       "",
            "sEmptyTable":   "Tidak ada data yang tersedia pada tabel ini",
            "sProcessing":   "Sedang memproses...",
            "sLengthMenu":   "Tampilkan _MENU_ entri",
            "sZeroRecords":  "Tidak ditemukan data yang sesuai",
            "sInfo":         "Showing _START_ to _END_ of _TOTAL_ entri",
            "sInfoEmpty":    "Showing 0 to 0 of 0 entries",
            "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
            "sInfoPostFix":  "",
            "sSearch":       "",
            "searchPlaceholder": "Cari Data..",
            "sUrl":          "",
            "oPaginate": {
                "sFirst":    "Pertama",
                "sPrevious": "Sebelumnya",
                "sNext":     "Selanjutnya",
                "sLast":     "Terakhir"
            }
        }
    });
});
</script>
<script>
$(document).ready(function(){
  $(document).on('click', 'a[href*="unit=cv_user"]', function(e){
    e.preventDefault();
    $('#modalLengkapiDataBody').html('<div class="text-center p-4"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');
    $('#modalLengkapiData').modal('show');
    $.get('unit/cv_user.php', function(data){
      $('#modalLengkapiDataBody').html(data);
    });
  });
});
</script>
</body>
</html> 