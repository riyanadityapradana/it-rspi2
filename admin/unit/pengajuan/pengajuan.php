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

$q = mysqli_query($config, "SELECT p.*, u.nama_lengkap, u.nip, u.email, u.no_hp, COALESCE(SUM(b.jumlah), 0) AS jumlah_realisasi, (p.jumlah + COALESCE(SUM(b.jumlah), 0)) AS jumlah_diminta FROM tb_pengajuan p LEFT JOIN tb_user u ON p.id_user = u.id_user LEFT JOIN tb_barang b ON b.pengajuan_id = p.pengajuan_id GROUP BY p.pengajuan_id ORDER BY p.tanggal_pengajuan DESC");
$pengajuanRows = [];
while ($pengajuanRow = mysqli_fetch_assoc($q)) {
  $pengajuanRows[] = $pengajuanRow;
}
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
                  <!-- <th>Keterangan</th> -->
                  <th>Status</th>
                  <th>Tanggal Pengajuan</th>
                  <th>Tanggal ACC</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php $no=1; foreach($pengajuanRows as $row): ?>
                <tr>
                  <td><?= $no++; ?></td>
                  <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                  <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                  <td><?= htmlspecialchars($row['unit']); ?></td>
                  <td><?= htmlspecialchars($row['jumlah_diminta']); ?></td>
                  <td><?= htmlspecialchars(number_format($row['perkiraan_harga'],0,',','.')); ?></td>
                  <!-- <td><?= htmlspecialchars($row['keterangan']); ?></td> -->
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
                    <button type="button" class="btn btn-info btn-sm mb-1" data-toggle="modal" data-target="#modalDetailPengajuan<?= $row['pengajuan_id'] ?>">
                      <i class="fa fa-eye"></i> Detail
                    </button>
                    <?php if ($row['status'] == 'diajukan'): ?>
                      <a href="dashboard_admin.php?unit=pengajuan&aksi=acc&id=<?= $row['pengajuan_id'] ?>" class="btn btn-success btn-sm mb-1">ACC</a>
                      <a href="dashboard_admin.php?unit=pengajuan&aksi=tolak&id=<?= $row['pengajuan_id'] ?>" class="btn btn-danger btn-sm mb-1">Tolak</a>
                    <?php elseif ($row['status'] == 'disetujui' || $row['status'] == 'selesai'): ?>
                      <a href="unit/pengajuan/lap_pemintaan_brg.php?id=<?= $row['pengajuan_id'] ?>" class="btn btn-primary btn-sm mb-1" target="_blank"><i class="fa fa-print"></i> Cetak</a>
                    <?php else: ?>
                      <span class="text-muted d-block">-</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php foreach ($pengajuanRows as $detailRow): ?>
<div class="modal fade" id="modalDetailPengajuan<?= $detailRow['pengajuan_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalDetailPengajuanLabel<?= $detailRow['pengajuan_id'] ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header border-bottom">
        <div>
          <h5 class="modal-title" id="modalDetailPengajuanLabel<?= $detailRow['pengajuan_id'] ?>" style="font-size: 18px; font-weight: 600;">Detail Data Pengajuan</h5>
          <small class="text-muted">ID Pengajuan: <?= htmlspecialchars($detailRow['pengajuan_id']) ?></small>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="padding: 20px;">
        <div style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #e0e0e0;">
          <h6 style="font-weight: 600; margin-bottom: 15px; font-size: 16px;">Informasi Pengaju</h6>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
              <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Nama Staff</p>
              <strong style="font-size: 15px;"><?= !empty($detailRow['nama_lengkap']) ? htmlspecialchars($detailRow['nama_lengkap']) : '-' ?></strong>
            </div>
            <div>
              <p style="font-size: 14px; color: #999; margin-bottom: 5px;">NIP</p>
              <strong style="font-size: 15px;"><?= !empty($detailRow['nip']) ? htmlspecialchars($detailRow['nip']) : '-' ?></strong>
            </div>
            <div>
              <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Email</p>
              <strong style="font-size: 15px;"><?= !empty($detailRow['email']) ? htmlspecialchars($detailRow['email']) : '-' ?></strong>
            </div>
            <div>
              <p style="font-size: 14px; color: #999; margin-bottom: 5px;">No. HP</p>
              <strong style="font-size: 15px;"><?= !empty($detailRow['no_hp']) ? htmlspecialchars($detailRow['no_hp']) : '-' ?></strong>
            </div>
          </div>
        </div>

        <div style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #e0e0e0;">
          <h6 style="font-weight: 600; margin-bottom: 15px; font-size: 16px;">Informasi Pengajuan</h6>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
            <div>
              <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Nama Barang</p>
              <strong style="font-size: 15px;"><?= htmlspecialchars($detailRow['nama_barang']) ?></strong>
            </div>
            <div>
              <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Unit</p>
              <strong style="font-size: 15px;"><?= htmlspecialchars($detailRow['unit']) ?></strong>
            </div>
            <div>
              <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Jumlah Diminta</p>
              <strong style="font-size: 15px;"><?= htmlspecialchars($detailRow['jumlah_diminta']) ?></strong>
            </div>
            <div>
              <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Jumlah Direalisasi</p>
              <strong style="font-size: 15px;"><?= htmlspecialchars($detailRow['jumlah_realisasi']) ?></strong>
            </div>
            <div>
              <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Perkiraan Harga</p>
              <strong style="font-size: 15px;">Rp <?= htmlspecialchars(number_format($detailRow['perkiraan_harga'], 0, ',', '.')) ?></strong>
            </div>
            <div>
              <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Tanggal Pengajuan</p>
              <strong style="font-size: 15px;"><?= !empty($detailRow['tanggal_pengajuan']) ? htmlspecialchars(date('d-m-Y', strtotime($detailRow['tanggal_pengajuan']))) : '-' ?></strong>
            </div>
          </div>
          <div>
            <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Keterangan</p>
            <div style="background: #f8f9fa; border-radius: 8px; padding: 12px 14px; min-height: 52px; max-height: 180px; overflow-y: auto; font-size: 14px; line-height: 1.6; white-space: pre-wrap; overflow-wrap: anywhere; word-break: break-word;">
              <?= !empty($detailRow['keterangan']) ? nl2br(htmlspecialchars($detailRow['keterangan'])) : '-' ?>
            </div>
          </div>
        </div>

        <div>
          <h6 style="font-weight: 600; margin-bottom: 15px; font-size: 16px;">Status Verifikasi</h6>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
              <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Status Saat Ini</p>
              <?php
              $detailStatus = $detailRow['status'];
              if ($detailStatus == 'diajukan') {
                $detailBadgeClass = 'warning';
                $detailStatusLabel = 'Diajukan';
              } elseif ($detailStatus == 'disetujui') {
                $detailBadgeClass = 'success';
                $detailStatusLabel = 'Disetujui';
              } elseif ($detailStatus == 'ditolak') {
                $detailBadgeClass = 'danger';
                $detailStatusLabel = 'Ditolak';
              } elseif ($detailStatus == 'selesai') {
                $detailBadgeClass = 'primary';
                $detailStatusLabel = 'Selesai';
              } else {
                $detailBadgeClass = 'secondary';
                $detailStatusLabel = $detailStatus;
              }
              ?>
              <span class="badge badge-<?= $detailBadgeClass ?>" style="font-size: 13px; padding: 6px 10px;"><?= htmlspecialchars($detailStatusLabel) ?></span>
            </div>
            <div>
              <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Tanggal ACC / Keputusan</p>
              <strong style="font-size: 15px;"><?= !empty($detailRow['tanggal_acc']) ? htmlspecialchars(date('d-m-Y', strtotime($detailRow['tanggal_acc']))) : '-' ?></strong>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
<?php endforeach; ?>
