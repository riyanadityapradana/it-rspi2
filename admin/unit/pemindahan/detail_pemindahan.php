<?php
require_once("../config/koneksi.php");
if (!isset($_GET['id'])) {
    echo "<script>alert('ID pemindahan tidak ditemukan!'); window.history.back();</script>";
    exit;
}
$mutasi_id = intval($_GET['id']);
// Ambil data mutasi
$query = mysqli_query($config, "SELECT m.*, b.nama_barang, b.jenis_barang, l1.nama_lokasi AS lokasi_asal_nama, l2.nama_lokasi AS lokasi_tujuan_nama, u.nama_lengkap as nama_staff FROM tb_mutasi_barang m LEFT JOIN tb_barang b ON m.barang_id = b.barang_id LEFT JOIN tb_lokasi l1 ON m.lokasi_asal = l1.lokasi_id LEFT JOIN tb_lokasi l2 ON m.lokasi_tujuan = l2.lokasi_id LEFT JOIN tb_user u ON m.id_user = u.id_user WHERE m.mutasi_id = '$mutasi_id'");
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
              <tr><th width="200">ID Mutasi</th><th width="10">:</th><td><?= htmlspecialchars($data['mutasi_id']); ?></td></tr>
              <tr><th>Nama Barang</th><th>:</th><td><?= htmlspecialchars($data['nama_barang']); ?></td></tr>
              <tr><th>Jenis Barang</th><th>:</th><td><?= htmlspecialchars($data['jenis_barang']); ?></td></tr>
              <tr><th>Lokasi Asal</th><th>:</th><td><?= htmlspecialchars($data['lokasi_asal_nama']); ?></td></tr>
              <tr><th>Lokasi Tujuan</th><th>:</th><td><?= htmlspecialchars($data['lokasi_tujuan_nama']); ?></td></tr>
              <tr><th>Tanggal Mutasi</th><th>:</th><td><?= date('d-m-Y', strtotime($data['tanggal_mutasi'])); ?></td></tr>
              <tr><th>Keterangan</th><th>:</th><td><?= htmlspecialchars($data['keterangan']); ?></td></tr>
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