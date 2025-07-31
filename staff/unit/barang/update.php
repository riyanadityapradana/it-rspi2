<?php
require_once("../config/koneksi.php");
if (!isset($_GET['id'])) {
    header('Location: dashboard_staff.php?unit=barang&err=Barang tidak ditemukan!');
    exit;
}
$kode_barang = $_GET['id'];
// Ambil data barang
$q = mysqli_query($config, "SELECT * FROM tb_barang WHERE kode_barang='".mysqli_real_escape_string($config, $kode_barang)."'");
$data = mysqli_fetch_assoc($q);
if (!$data) {
    header('Location: dashboard_staff.php?unit=barang&err=Barang tidak ditemukan!');
    exit;
}
// Pilihan jenis barang
$jenis_list = [
    'Komputer & Laptop',
    'Komponen Komputer & Laptop',
    'Printer & Scanner',
    'Komponen Printer & Scanner',
    'Komponen Network'
];
// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_barang   = trim($_POST['nama_barang']);
    $spesifikasi   = trim($_POST['spesifikasi']);
    $jenis_barang  = trim($_POST['jenis_barang']);
    $q = mysqli_query($config, "UPDATE tb_barang SET nama_barang='$nama_barang', spesifikasi='$spesifikasi', jenis_barang='$jenis_barang' WHERE kode_barang='$kode_barang'");
    if ($q) {
        header('Location: dashboard_staff.php?unit=barang&msg=Barang berhasil diupdate!');
        exit;
    } else {
        header('Location: dashboard_staff.php?unit=barang&err=Gagal update barang!');
        exit;
    }
}
?>
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Edit Barang</h1>
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
            <label>Kode Barang</label>
            <input type="text" name="kode_barang" class="form-control" value="<?= htmlspecialchars($data['kode_barang']) ?>" readonly>
          </div>
          <div class="form-group">
            <label>Nama Barang</label>
            <input type="text" name="nama_barang" class="form-control" value="<?= htmlspecialchars($data['nama_barang']) ?>" required maxlength="100">
          </div>
          <div class="form-group">
            <label>Jenis Barang</label>
            <select name="jenis_barang" class="form-control select2" required>
              <option value="">-- Pilih Jenis --</option>
              <?php foreach ($jenis_list as $jenis): ?>
                <option value="<?= htmlspecialchars($jenis) ?>" <?= $data['jenis_barang'] == $jenis ? 'selected' : '' ?>><?= htmlspecialchars($jenis) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Spesifikasi</label>
            <textarea name="spesifikasi" class="form-control" rows="2"><?= htmlspecialchars($data['spesifikasi']) ?></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Update</button>
          <a href="dashboard_staff.php?unit=barang" class="btn btn-secondary">Kembali</a>
        </form>
      </div>
    </div>
  </div>
</div>
