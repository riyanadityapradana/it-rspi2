
<?php
require_once("../config/koneksi.php");
$id_user = $_SESSION['id_user'];
// Proses simpan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_barang = trim($_POST['nama_barang']);
  $unit = 'Unit IT';
    $jumlah = intval($_POST['jumlah']);
    $perkiraan_harga = floatval($_POST['perkiraan_harga']);
    $keterangan = trim($_POST['keterangan']);
    if (!$nama_barang || !$unit || !$jumlah || !$perkiraan_harga) {
        header('Location: dashboard_staff.php?unit=pengajuan&err=Data tidak lengkap!');
        exit;
    }
    $q = mysqli_query($config, "INSERT INTO tb_pengajuan (id_user, nama_barang, unit, jumlah, perkiraan_harga, keterangan, status, tanggal_pengajuan) VALUES ('$id_user', '$nama_barang', '$unit', $jumlah, $perkiraan_harga, '$keterangan', 'diajukan', CURDATE())");
    if ($q) {
        header('Location: dashboard_staff.php?unit=pengajuan&msg=Pengajuan barang berhasil ditambahkan!');
        exit;
    } else {
        header('Location: dashboard_staff.php?unit=pengajuan&err=Gagal menambah pengajuan!');
        exit;
    }
}
?>
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Tambah Pengajuan Barang</h1>
      </div>
    </div>
  </div>
</div>
<div class="content">
  <div class="container-fluid">
    <div class="card">
      <div class="card-body">
        <form method="post">
          <div class="form-group">
            <label>Nama Barang</label>
            <input type="text" name="nama_barang" class="form-control" required maxlength="150">
          </div>
          <div class="form-group">
            <label>Unit</label>
            <input type="text" name="unit" class="form-control" value="Unit IT" readonly required maxlength="50">
          </div>
          <div class="form-group">
            <label>Jumlah</label>
            <input type="number" name="jumlah" class="form-control" min="1" required>
          </div>
          <div class="form-group">
            <label>Perkiraan Harga</label>
            <input type="number" name="perkiraan_harga" class="form-control" min="0" step="0.01" required>
          </div>
          <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="2"></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Simpan</button>
          <a href="dashboard_staff.php?unit=pengajuan" class="btn btn-secondary">Kembali</a>
        </form>
      </div>
    </div>
  </div>
</div>
