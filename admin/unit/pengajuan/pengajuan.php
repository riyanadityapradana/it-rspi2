<?php
require_once("../config/koneksi.php");

// Proses ACC/Tolak
if (isset($_GET['aksi']) && isset($_GET['id'])) {
    $id_pengajuan = intval($_GET['id']);
    $id_kepala = $_SESSION['id_user'];
    if ($_GET['aksi'] == 'acc') {
        $q = mysqli_query($config, "UPDATE tb_pengajuan_barang SET status='Disetujui', id_kepala='$id_kepala', waktu_acc=NOW() WHERE id_pengajuan='$id_pengajuan' AND status='Menunggu'");
        if ($q && mysqli_affected_rows($config) > 0) {
            $_SESSION['toastr'] = ['type' => 'success', 'msg' => 'Pengajuan berhasil di-ACC!'];
        } else {
            $_SESSION['toastr'] = ['type' => 'error', 'msg' => 'Gagal ACC pengajuan!'];
        }
        header('Location: dashboard_admin.php?unit=pengajuan');
        exit;
    } elseif ($_GET['aksi'] == 'tolak') {
        $q = mysqli_query($config, "UPDATE tb_pengajuan_barang SET status='Ditolak', id_kepala='$id_kepala', waktu_acc=NOW() WHERE id_pengajuan='$id_pengajuan' AND status='Menunggu'");
        if ($q && mysqli_affected_rows($config) > 0) {
            $_SESSION['toastr'] = ['type' => 'success', 'msg' => 'Pengajuan berhasil ditolak!'];
        } else {
            $_SESSION['toastr'] = ['type' => 'error', 'msg' => 'Gagal menolak pengajuan!'];
        }
        header('Location: dashboard_admin.php?unit=pengajuan');
        exit;
    }
}
// Ambil semua pengajuan barang
$q = mysqli_query($config, "SELECT p.*, b.nama_barang, u.nama_lengkap FROM tb_pengajuan_barang p LEFT JOIN tb_barang b ON p.kode_barang = b.kode_barang LEFT JOIN tb_user u ON p.id_staff = u.id_user ORDER BY p.tgl_pengajuan DESC");
?>
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>APPROVAL PENGAJUAN BARANG</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="dashboard_admin.php?unit=beranda">Home</a></li>
          <li class="breadcrumb-item active">Approval Pengajuan Barang</li>
        </ol>
      </div>
    </div>
  </div>
</section>
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <table id="example1" class="table table-bordered table-striped">
              <thead style="background:rgb(129, 2, 0, 1); color:white;">
                <tr>
                  <th>No</th>
                  <th>Nama Staff</th>
                  <th>Kode Barang</th>
                  <th>Nama Barang</th>
                  <th>Satuan</th>
                  <th>Jumlah</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php $no=1; while($row = mysqli_fetch_assoc($q)): ?>
                <tr>
                  <td><?= $no++; ?></td>
                  <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                  <td><?= htmlspecialchars($row['kode_barang']); ?></td>
                  <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                  <td><?= htmlspecialchars($row['satuan']); ?></td>
                  <td><?= htmlspecialchars($row['jumlah']); ?></td>
                  <td>
                    <?php
                    $status = $row['status'];
                    if ($status == 'Menunggu') {
                        echo '<span class="badge badge-warning" style="font-size:1em;">' . $status . '</span>';
                    } elseif ($status == 'Disetujui') {
                        echo '<span class="badge badge-success" style="font-size:1em;">' . $status . '</span>';
                    } elseif ($status == 'Ditolak') {
                        echo '<span class="badge badge-danger" style="font-size:1em;">' . $status . '</span>';
                    } else {
                        echo '<span class="badge badge-secondary" style="font-size:1em;">' . $status . '</span>';
                    }
                    ?>
                  </td>
                  <td>
                    <?php if ($row['status'] == 'Menunggu'): ?>
                      <a href="dashboard_admin.php?unit=pengajuan&aksi=acc&id=<?= $row['id_pengajuan'] ?>" class="btn btn-success btn-sm">ACC</a>
                      <a href="dashboard_admin.php?unit=pengajuan&aksi=tolak&id=<?= $row['id_pengajuan'] ?>" class="btn btn-danger btn-sm">Tolak</a>
                    <?php elseif ($row['status'] == 'Disetujui'): ?>
                      <a href="dashboard_admin.php?unit=cetak_pengajuan&id=<?= $row['id_pengajuan'] ?>" class="btn btn-info btn-sm" target="_blank"><i class="fa fa-print"></i> Cetak</a>
                    <?php else: ?>
                      <span class="text-muted">-</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php if (isset($_SESSION['toastr'])): ?>
<script>
toastr.options = {"positionClass": "toast-top-right", "timeOut": "3000"};
toastr.<?= $_SESSION['toastr']['type'] ?>("<?= addslashes($_SESSION['toastr']['msg']) ?>");
</script>
<?php unset($_SESSION['toastr']); endif; ?> 