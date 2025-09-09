
<?php
require_once("../config/koneksi.php");
$today = date('Y-m-d');
// Proses ACC/Tolak
if (isset($_GET['aksi']) && isset($_GET['id'])) {
  $id_pengajuan = intval($_GET['id']);
  if ($_GET['aksi'] == 'acc') {
    $q = mysqli_query($config, "UPDATE tb_pengajuan SET status='disetujui', tanggal_acc=CURDATE() WHERE pengajuan_id='$id_pengajuan' AND status='diajukan'");
    if ($q && mysqli_affected_rows($config) > 0) {
      header('Location: dashboard_admin.php?unit=pengajuan&msg=Pengajuan barang berhasil di-ACC!');
      exit;
    } else {
      header('Location: dashboard_admin.php?unit=pengajuan&err=Gagal ACC pengajuan!');
      exit;
    }
  } elseif ($_GET['aksi'] == 'tolak') {
    $q = mysqli_query($config, "UPDATE tb_pengajuan SET status='ditolak', tanggal_acc=CURDATE() WHERE pengajuan_id='$id_pengajuan' AND status='diajukan'" );
    if ($q && mysqli_affected_rows($config) > 0) {
      header('Location: dashboard_admin.php?unit=pengajuan&msg=Pengajuan barang berhasil ditolak!');
      exit;
    } else {
      header('Location: dashboard_admin.php?unit=pengajuan&err=Gagal menolak pengajuan!');
      exit;
    }
  }
}
// ...existing code...
// Ambil semua pengajuan barang
// Ambil semua pengajuan barang dari tabel baru
$q = mysqli_query($config, "SELECT p.*, u.nama_lengkap FROM tb_pengajuan p LEFT JOIN tb_user u ON p.id_user = u.id_user ORDER BY p.tanggal_pengajuan DESC");
?>
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>DATA PENGAJUAN BARANG</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="main_staff.php?unit=beranda">Home</a></li>
          <li class="breadcrumb-item active">Barang</li>
        </ol>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <form method="get" class="form-inline" style="display:inline-block; margin-right:10px;">
                  <a href="unit/pengajuan/lap_pemintaan_brg.php?dari=<?= $today ?>&sampai=<?= $today ?>" target="_blank" class="btn btn-sm btn-info mx-1"><i class="fa fa-print"></i> Print Hari Ini</a>
                </form>
              </div>
              <div>
                <a href="#" class="btn btn-tool btn-sm" data-card-widget="collapse" style="background:rgba(69, 77, 85, 1)">
                  <i class="fas fa-bars"></i>
                </a>
              </div>
            </div>
          </div>
          <div class="card-body">
            <table id="example1" class="table table-bordered table-striped">
              <thead style="background:rgb(129, 2, 0, 1); color:white;">
                <tr>
                  <th>No</th>
                  <th>Nama Staff</th>
                  <th>Nama Barang</th>
                  <th>Unit</th>
                  <th>Jumlah</th>
                  <th>Perkiraan Harga</th>
                  <th>Keterangan</th>
                  <th>Status</th>
                  <th>Tanggal Pengajuan</th>
                  <th>Tanggal ACC</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php $no=1; while($row = mysqli_fetch_assoc($q)): ?>
                <tr>
                  <td><?= $no++; ?></td>
                  <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                  <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                  <td><?= htmlspecialchars($row['unit']); ?></td>
                  <td><?= htmlspecialchars($row['jumlah']); ?></td>
                  <td><?= htmlspecialchars(number_format($row['perkiraan_harga'],0,',','.')); ?></td>
                  <td><?= htmlspecialchars($row['keterangan']); ?></td>
                  <td>
                    <?php
                    $status = $row['status'];
                    if ($status == 'diajukan') {
                        echo '<span class="badge badge-warning" style="font-size:1em;">Diajukan</span>';
                    } elseif ($status == 'disetujui') {
                        echo '<span class="badge badge-success" style="font-size:1em;">Disetujui</span>';
                    } elseif ($status == 'ditolak') {
                        echo '<span class="badge badge-danger" style="font-size:1em;">Ditolak</span>';
                    } elseif ($status == 'selesai') {
                        echo '<span class="badge badge-primary" style="font-size:1em;">Selesai</span>';
                    } else {
                        echo '<span class="badge badge-secondary" style="font-size:1em;">' . htmlspecialchars($status) . '</span>';
                    }
                    ?>
                  </td>
                  <td><?= htmlspecialchars($row['tanggal_pengajuan']); ?></td>
                  <td><?= htmlspecialchars($row['tanggal_acc']); ?></td>
                  <td>
                    <?php if ($row['status'] == 'diajukan'): ?>
                      <a href="dashboard_admin.php?unit=pengajuan&aksi=acc&id=<?= $row['pengajuan_id'] ?>" class="btn btn-success btn-sm">ACC</a>
                      <a href="dashboard_admin.php?unit=pengajuan&aksi=tolak&id=<?= $row['pengajuan_id'] ?>" class="btn btn-danger btn-sm">Tolak</a>
                    <?php elseif ($row['status'] == 'disetujui'): ?>
                      <a href="unit/pengajuan/lap_pemintaan_brg.php?id=<?= $row['pengajuan_id'] ?>" class="btn btn-info btn-sm" target="_blank"><i class="fa fa-print"></i> Cetak</a>
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