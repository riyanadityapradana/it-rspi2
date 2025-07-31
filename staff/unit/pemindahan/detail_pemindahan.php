<?php
require_once("../config/koneksi.php");
if (!isset($_GET['id'])) {
    echo "<script>alert('ID pemindahan tidak ditemukan!'); window.history.back();</script>";
    exit;
}
$id_pemindahan = intval($_GET['id']);
// Ambil data pemindahan
$query = mysqli_query($config, "SELECT pm.*, b.nama_barang, b.jenis_barang, b.penyerahan, b.stts_brg, u.nama_lengkap as nama_staff FROM tb_pemindahan_barang pm LEFT JOIN tb_barang b ON pm.kode_barang = b.kode_barang LEFT JOIN tb_user u ON pm.id_user = u.id_user WHERE pm.id_pemindahan = '$id_pemindahan'");
$data = mysqli_fetch_assoc($query);
?>
<link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.min.css">
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Detail Pemindahan Barang</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="dashboard_staff.php?unit=beranda">Home</a></li>
          <li class="breadcrumb-item"><a href="dashboard_staff.php?unit=pemindahan">Pemindahan Barang</a></li>
          <li class="breadcrumb-item active">Detail</li>
        </ol>
      </div>
    </div>
  </div>
</section>
<section class="content">
  <div class="container-fluid">
    <div class="card card-default">
      <div class="card-header">
        <h3 class="card-title">Berikut Detail Data Pemindahan Barang</h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-8">
            <table class="table table-bordered">
              <tr><th width="200">ID Pemindahan</th><th width="10">:</th><td><?= htmlspecialchars($data['id_pemindahan']); ?></td></tr>
              <tr><th>Kode Barang</th><th>:</th><td><?= htmlspecialchars($data['kode_barang']); ?></td></tr>
              <tr><th>Nama Barang</th><th>:</th><td><?= htmlspecialchars($data['nama_barang']); ?></td></tr>
              <tr><th>Jenis Barang</th><th>:</th><td><?= htmlspecialchars($data['jenis_barang']); ?></td></tr>
              <tr><th>Sebelumnya dari unit</th><th>:</th><td><?= htmlspecialchars($data['penyerahan']); ?></td></tr>
              <tr><th>Tanggal Pemindahan</th><th>:</th><td><?= date('d-m-Y', strtotime($data['tanggal_pemindahan'])); ?></td></tr>
              <tr><th>Pemindahan ke Unit</th><th>:</th><td><?= htmlspecialchars($data['ke_unit']); ?></td></tr>
              <tr><th>Alasan Pemindahan</th><th>:</th><td><?= htmlspecialchars($data['alasan_pemindahan']); ?></td></tr>
              <tr><th>Status Barang</th><th>:</th><td><?php if (!empty($data['stts_brg'])): ?>
                                <span class="badge badge-<?= $data['stts_brg'] == 'Baik' ? 'success' : 'danger' ?>">
                                    <?= htmlspecialchars($data['stts_brg']); ?>
                                </span>
                            <?php else: ?>
                                <span class="badge badge-secondary">-</span>
                            <?php endif; ?></td></tr>
              <tr><th>Staff yang Memindahkan</th><th>:</th><td><?= htmlspecialchars($data['nama_staff']); ?></td></tr>
            </table>
            <a href="dashboard_staff.php?unit=pemindahan" class="btn btn-warning mt-3">Kembali</a>
          </div>
          <div class="col-md-4 d-flex align-items-center justify-content-center">
            <img src="../assets/img/pemindahan.jpg" alt="Gambar Pemindahan" class="img-fluid" style="max-height:300px; border:2px solid #ccc; padding:8px; background:#fff;">
          </div>
        </div>
      </div>
    </div>
  </div>
</section> 