<?php
require_once("../config/koneksi.php");
require_once("../config/telegram.php");
$today = date('Y-m-d');

if (isset($_GET['aksi']) && isset($_GET['id'])) {
  $id_pengajuan = (int) $_GET['id'];
  $aksi = $_GET['aksi'];

  $stmt = $config->prepare("SELECT p.*, u.nama_lengkap, u.nip FROM tb_pengajuan p LEFT JOIN tb_user u ON p.id_user = u.id_user WHERE p.pengajuan_id = ? LIMIT 1");
  $stmt->bind_param('i', $id_pengajuan);
  $stmt->execute();
  $pengajuan = $stmt->get_result()->fetch_assoc();
  $stmt->close();

  if ($aksi === 'acc') {
    $stmt = $config->prepare("UPDATE tb_pengajuan SET status = 'disetujui', tanggal_acc = CURDATE() WHERE pengajuan_id = ? AND status = 'diajukan'");
    $stmt->bind_param('i', $id_pengajuan);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();

    if ($affected > 0) {
      if ($pengajuan) {
        $pesan = "VERIFIKASI PENGAJUAN BARANG\n";
        $pesan .= "Aksi: ACC Pengajuan\n";
        $pesan .= "ID Pengajuan: " . $pengajuan['pengajuan_id'] . "\n";
        $pesan .= "Nama Staff: " . ($pengajuan['nama_lengkap'] ?: '-') . "\n";
        $pesan .= "NIP: " . ($pengajuan['nip'] ?: '-') . "\n";
        $pesan .= "Nama Barang: " . $pengajuan['nama_barang'] . "\n";
        $pesan .= "Jumlah: " . $pengajuan['jumlah'] . "\n";
        $pesan .= "Perkiraan Harga: Rp " . number_format($pengajuan['perkiraan_harga'], 0, ',', '.') . "\n";
        $pesan .= "Status Baru: Disetujui\n";
        $pesan .= "Tanggal ACC: " . date('d-m-Y');
        telegram_send_channel_message($pesan);
      }
      header('Location: dashboard_admin.php?unit=pengajuan&msg=Pengajuan barang berhasil di-ACC!');
      exit;
    }

    header('Location: dashboard_admin.php?unit=pengajuan&err=Gagal ACC pengajuan!');
    exit;
  }

  if ($aksi === 'tolak') {
    $stmt = $config->prepare("UPDATE tb_pengajuan SET status = 'ditolak', tanggal_acc = CURDATE() WHERE pengajuan_id = ? AND status = 'diajukan'");
    $stmt->bind_param('i', $id_pengajuan);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();

    if ($affected > 0) {
      if ($pengajuan) {
        $pesan = "VERIFIKASI PENGAJUAN BARANG\n";
        $pesan .= "Aksi: Tolak Pengajuan\n";
        $pesan .= "ID Pengajuan: " . $pengajuan['pengajuan_id'] . "\n";
        $pesan .= "Nama Staff: " . ($pengajuan['nama_lengkap'] ?: '-') . "\n";
        $pesan .= "NIP: " . ($pengajuan['nip'] ?: '-') . "\n";
        $pesan .= "Nama Barang: " . $pengajuan['nama_barang'] . "\n";
        $pesan .= "Jumlah: " . $pengajuan['jumlah'] . "\n";
        $pesan .= "Perkiraan Harga: Rp " . number_format($pengajuan['perkiraan_harga'], 0, ',', '.') . "\n";
        $pesan .= "Status Baru: Ditolak\n";
        $pesan .= "Tanggal Keputusan: " . date('d-m-Y');
        telegram_send_channel_message($pesan);
      }
      header('Location: dashboard_admin.php?unit=pengajuan&msg=Pengajuan barang berhasil ditolak!');
      exit;
    }

    header('Location: dashboard_admin.php?unit=pengajuan&err=Gagal menolak pengajuan!');
    exit;
  }
}

$q = mysqli_query($config, "SELECT p.*, u.nama_lengkap, COALESCE(SUM(b.jumlah), 0) AS jumlah_realisasi, (p.jumlah + COALESCE(SUM(b.jumlah), 0)) AS jumlah_diminta FROM tb_pengajuan p LEFT JOIN tb_user u ON p.id_user = u.id_user LEFT JOIN tb_barang b ON b.pengajuan_id = p.pengajuan_id GROUP BY p.pengajuan_id ORDER BY p.tanggal_pengajuan DESC");
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
              <thead style="background:rgb(52, 58, 64, 1); color:white;">
                <tr>
                  <th>No</th>
                  <th>Nama Staff</th>
                  <th>Nama Barang</th>
                  <th>Unit</th>
                  <th>Jumlah Diminta</th>
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
                  <td><?= htmlspecialchars($row['jumlah_diminta']); ?></td>
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
                    <?php elseif ($row['status'] == 'disetujui' || $row['status'] == 'selesai'): ?>
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
