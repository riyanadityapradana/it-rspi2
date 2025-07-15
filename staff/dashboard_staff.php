<?php
session_start();
require_once("../config/koneksi.php");
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Staff') {
    header('Location: ../main_login/form_login.php?error=Akses ditolak!');
    exit;
}
if (isset($_GET['unit'])){ $unit = $_GET['unit']; }
ob_start();
$id 	= $_SESSION['id_user'];
	$query 	= "SELECT * FROM tb_user WHERE id_user = '$id'";
	$admin 	= mysqli_fetch_array(mysqli_query($config, $query));
	$nama 	= $admin['nama_lengkap'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Staff | IT-RSPI</title>
    <link rel="icon" href="../assets/img/icon.png">
    <!-- Google Font: Source Sans Pro -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<!-- Ionicons -->
	<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="../assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <link rel="stylesheet" href="../assets/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/plugins/toastr/toastr.css">
    <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
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
    <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background:rgb(217, 221, 224, 1)">
        <a href="dashboard_staff.php?unit=beranda" class="brand-link" style="background:rgb(129, 2, 0, 1)">
            <img src="../assets/img/icon.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">IT - RSPI</span>
        </a>
        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <?php
                        $foto = isset($_SESSION['foto']) && $_SESSION['foto'] ? $_SESSION['foto'] : 'default-user.png';
                    ?>
                    <img src="../assets/img/<?= htmlspecialchars($foto) ?>" class="img-circle elevation-2" alt="User Image" style="width: 47px; height: 52px; object-fit: cover;">
                </div>
                <div class="info ml-2">
                    <a href="#" class="d-block" style="color: black;"><?php echo htmlspecialchars($nama); ?></a>
                    <span class="text-muted small">STAFF IT</span>
                </div>
            </div>
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item menu-open">
                        <a href="dashboard_staff.php?unit=beranda" class="nav-link active">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <?php if ($_SESSION['id_user'] == '5' || $_SESSION['id_user'] == '6'): ?> 
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fab fa-whatsapp" style="color: green;"></i><p style="color: black;">Grafik Pi-Care<i class="fas fa-angle-left right" style="color: black;"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="dashboard_staff.php?unit=daftar" class="nav-link">
                                    <i class="nav-icon fas fa-caret-right" style="color: black;"></i><p style="color: black;">PENDAFTARAN</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="dashboard_staff.php?unit=batal" class="nav-link">
                                    <i class="nav-icon fas fa-caret-right" style="color: black;"></i><p style="color: black;">PEMBATALAN</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="dashboard_staff.php?unit=alasan" class="nav-link">
                                    <i class="nav-icon fas fa-caret-right" style="color: black;"></i><p style="color: black;">ALASAN PEMBATALAN</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-folder" style="color: black;"></i>
                            <p style="color: black;">Master Data <i class="right fas fa-angle-left" style="color: black;"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="dashboard_staff.php?unit=logbook" class="nav-link">
                                    <i class="nav-icon fas fa-book" style="color: black;"></i>
                                    <p style="font-size: 14px; color: black;">Data Logbook</p>
                                </a>
                                <a href="dashboard_staff.php?unit=lembur" class="nav-link">
                                    <i class="nav-icon fas fa-hospital-user" style="color: black;"></i>
                                    <p style="font-size: 14px; color: black;">Data Lembur</p>
                                </a>
                                <a href="dashboard_staff.php?unit=remote" class="nav-link">
                                    <i class="nav-icon fas fa-laptop" style="color: black;"></i>
                                    <p style="font-size: 14px; color: black;">Remote Desktop</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-folder" style="color: black;"></i>
                            <p style="color: black;">Master Barang <i class="right fas fa-angle-left" style="color: black;"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="dashboard_staff.php?unit=barang" class="nav-link">
                                    <i class="nav-icon fas fa-book" style="color: black;"></i>
                                    <p style="font-size: 14px; color: black;">Data Barang</p>
                                </a>
                                <a href="dashboard_staff.php?unit=pengajuan" class="nav-link">
                                    <i class="nav-icon fas fa-hospital-user" style="color: black;"></i>
                                    <p style="font-size: 14px; color: black;">Pengajuan Barang</p>
                                </a>
                            </li>
                        </ul>
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
    <!--Modal logout -->
    <div id="modallogout" class="modal fade" role="dialog">
        <div class="modal-dialog" align="center">
            <div class="modal-content">
                <div class="modal-body">
                    <form method="POST" action="logout.php">
                        <strong>Anda yakin ingin Logout Dari Aplikasi ?&nbsp;&nbsp;</strong>
                        <input type="submit" name="logout" class="btn btn-danger" style="width: 60px" value="Ya">
                        <button type="button" class="btn btn-warning" data-dismiss="modal" style="width: 60px">Batal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Akhir Modal logout -->
</div>
<footer class="main-footer" style="position:fixed;bottom:0;width:100%;background:#222;color:#fff;z-index:9999;padding:0;">
  <div style="overflow:hidden;white-space:nowrap;">
    <marquee behavior="scroll" direction="left" scrollamount="6" style="font-size:16px;padding:8px 0;">
      &copy; <?= date('Y') ?> IT-RSPI | Sistem Informasi Teknologi RSPI. Dikembangkan dengan ❤️ oleh Tim IT-RSPI. Seluruh hak cipta dilindungi undang-undang.
    </marquee>
  </div>
</footer>
<!-- Toastr Success Message -->
<?php if (isset($_GET['msg'])): ?>
  <script>
    toastr.success("<?= addslashes($_GET['msg']) ?>", "Sukses", {positionClass: "toast-top-right"});
  </script>
<?php endif; ?>
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="../assets/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Select2 -->
<script src="../assets/plugins/select2/js/select2.full.min.js"></script>
<!-- Bootstrap4 Duallistbox -->
<script src="../assets/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
<!-- InputMask -->
<script src="../assets/plugins/moment/moment.min.js"></script>
<script src="../assets/plugins/inputmask/jquery.inputmask.min.js"></script>

<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../assets/plugins/toastr/toastr.min.js"></script>
<script src="../assets/dist/js/adminlte.min.js"></script>

<script>
    $(function() {
    $('.select2').select2();
    $('.select2bs4').select2({ theme: 'bootstrap4' });
    });
</script>
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
    // Toastr notification
    <?php if(isset($_GET['msg'])): ?>
        toastr.options = {"positionClass": "toast-top-right", "timeOut": "3000"};
        toastr.success("<?= htmlspecialchars($_GET['msg']) ?>");
    <?php endif; ?>
    <?php if(isset($_GET['err'])): ?>
        toastr.options = {"positionClass": "toast-top-right", "timeOut": "3000"};
        toastr.error("<?= htmlspecialchars($_GET['err']) ?>");
    <?php endif; ?>
});
</script>
</body>
</html> 