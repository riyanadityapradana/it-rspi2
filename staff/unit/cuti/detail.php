<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (!isset($_GET['id'])) {
    echo '<script>window.location="?unit=cuti";</script>';
    exit;
}
$id = intval($_GET['id']);
$query = mysqli_query($config, "SELECT c.*, u.nama_lengkap, u.nip FROM tb_cuti c JOIN tb_user u ON c.id_user=u.id_user WHERE c.id_cuti='$id'") or die(mysqli_error($config));
$data = mysqli_fetch_array($query);
if (!$data) {
    echo '<script>window.location="?unit=cuti&msg=notfound";</script>';
    exit;
}

$status = isset($data['status_lembur']) ? $data['status_lembur'] : 'Menunggu';
if ($status == 'Diterima') {
    $badge = 'badge-success';
} elseif ($status == 'Ditolak') {
    $badge = 'badge-danger';
} else {
    $badge = 'badge-warning';
}
?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6"><h1>Detail Pengajuan Cuti</h1></div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="?unit=cuti">Cuti</a></li>
          <li class="breadcrumb-item active">Detail</li>
        </ol>
      </div>
    </div>
  </div>
</section>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-8">
        <div class="card">
          <div class="card-body">
            <table class="table table-striped">
              <tr><th>NIP</th><td><?php echo htmlspecialchars($data['nip']); ?></td></tr>
              <tr><th>Nama</th><td><?php echo htmlspecialchars($data['nama_lengkap']); ?></td></tr>
              <tr><th>Banyak Hari</th><td><?php echo htmlspecialchars($data['banyak_hari']); ?></td></tr>
              <tr><th>Mulai Tanggal</th><td><?php echo htmlspecialchars($data['mulai_tanggal']); ?></td></tr>
              <tr><th>Sampai Tanggal</th><td><?php echo htmlspecialchars($data['sampai_tanggal']); ?></td></tr>
              <tr><th>Masuk Tanggal</th><td><?php echo htmlspecialchars($data['masuk_tanggal']); ?></td></tr>
              <tr><th>Status</th><td><span class="badge <?php echo $badge;?>"><?php echo $status;?></span></td></tr>
            </table>
          </div>
          <div class="card-footer">
            <a href="?unit=cuti" class="btn btn-secondary">Kembali</a>
            <?php if ((isset($_SESSION['id_user']) && $_SESSION['id_user']==$data['id_user']) || (isset($_SESSION['nip']) && $_SESSION['nip']==='662.140725')): ?>
              <a href="?unit=update_cuti&id=<?php echo $data['id_cuti']; ?>" class="btn btn-success">Ubah</a>
              <a onclick="return confirm('Yakin hapus pengajuan cuti ini?')" href="?unit=delete_cuti&id=<?php echo $data['id_cuti']; ?>" class="btn btn-danger">Hapus</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
