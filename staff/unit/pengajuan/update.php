
<?php
require_once("../config/koneksi.php");
if (!isset($_GET['id'])) {
    header('Location: dashboard_staff.php?unit=pengajuan&err=Pengajuan tidak ditemukan!');
    exit;
}
$pengajuan_id = intval($_GET['id']);
$id_user = $_SESSION['id_user'];
// Ambil data pengajuan
$q = mysqli_query($config, "SELECT * FROM tb_pengajuan WHERE pengajuan_id='$pengajuan_id' AND id_user='$id_user'");
$data = mysqli_fetch_assoc($q);
if (!$data) {
    header('Location: dashboard_staff.php?unit=pengajuan&err=Pengajuan tidak ditemukan!');
    exit;
}
if ($data['status'] !== 'diajukan') {
    header('Location: dashboard_staff.php?unit=pengajuan&err=Pengajuan tidak bisa diedit!');
    exit;
}
// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_barang = trim($_POST['nama_barang']);
    $unit = trim($_POST['unit']);
    $jumlah = intval($_POST['jumlah']);
    $perkiraan_harga = floatval($_POST['perkiraan_harga']);
    $keterangan = trim($_POST['keterangan']);
    if (!$nama_barang || !$unit || !$jumlah || !$perkiraan_harga) {
        header('Location: dashboard_staff.php?unit=pengajuan&err=Data tidak lengkap!');
        exit;
    }
    $q = mysqli_query($config, "UPDATE tb_pengajuan SET nama_barang='$nama_barang', unit='$unit', jumlah=$jumlah, perkiraan_harga=$perkiraan_harga, keterangan='$keterangan' WHERE pengajuan_id='$pengajuan_id' AND id_user='$id_user' AND status='diajukan'");
    if ($q) {
        header('Location: dashboard_staff.php?unit=pengajuan&msg=Pengajuan barang berhasil diupdate!');
        exit;
    } else {
        header('Location: dashboard_staff.php?unit=pengajuan&err=Gagal update pengajuan!');
        exit;
    }
}
?>
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Edit Pengajuan Barang</h1>
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
            <input type="text" name="nama_barang" class="form-control" value="<?= htmlspecialchars($data['nama_barang']) ?>" required maxlength="150">
          </div>
          <div class="form-group">
            <label>Unit</label>
            <input type="text" name="unit" class="form-control" value="<?= htmlspecialchars($data['unit']) ?>" readonly maxlength="50">
          </div>
          <div class="form-group">
            <label>Jumlah</label>
            <input type="number" name="jumlah" class="form-control" min="1" value="<?= htmlspecialchars($data['jumlah']) ?>" required>
          </div>
          <div class="form-group">
            <label>Perkiraan Harga</label>
            <input type="number" name="perkiraan_harga" class="form-control" min="0" step="0.01" value="<?= htmlspecialchars($data['perkiraan_harga']) ?>" required>
          </div>
          <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="2"><?= htmlspecialchars($data['keterangan']) ?></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Update</button>
          <a href="dashboard_staff.php?unit=pengajuan" class="btn btn-secondary">Kembali</a>
        </form>
      </div>
    </div>
  </div>
</div>
