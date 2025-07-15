<?php
require_once("../config/koneksi.php");
if (!isset($_GET['id'])) {
    header('Location: dashboard_staff.php?unit=pengajuan&err=Pengajuan tidak ditemukan!');
    exit;
}
$id_pengajuan = $_GET['id'];
$id_staff = $_SESSION['id_user'];
// Ambil data pengajuan
$q = mysqli_query($config, "SELECT * FROM tb_pengajuan_barang WHERE id_pengajuan='".intval($id_pengajuan)."' AND id_staff='$id_staff'");
$data = mysqli_fetch_assoc($q);
if (!$data) {
    header('Location: dashboard_staff.php?unit=pengajuan&err=Pengajuan tidak ditemukan!');
    exit;
}
if ($data['status'] !== 'Menunggu') {
    header('Location: dashboard_staff.php?unit=pengajuan&err=Pengajuan tidak bisa diedit!');
    exit;
}
// Ambil data barang untuk dropdown
$barang = [];
$qbarang = mysqli_query($config, "SELECT kode_barang, nama_barang FROM tb_barang ORDER BY nama_barang ASC");
while ($row = mysqli_fetch_assoc($qbarang)) {
    $barang[] = $row;
}
// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $satuan      = trim($_POST['satuan']);
    $jumlah      = intval($_POST['jumlah']);
    $keterangan  = trim($_POST['keterangan']);
    $bidang      = isset($_POST['bidang_pengajuan']) ? trim($_POST['bidang_pengajuan']) : '';
    if (!$satuan || !$jumlah) {
        header('Location: dashboard_staff.php?unit=pengajuan&err=Data tidak lengkap!');
        exit;
    }
    $q = mysqli_query($config, "UPDATE tb_pengajuan_barang SET satuan='$satuan', jumlah=$jumlah, keterangan='$keterangan', bidang_pengajuan='$bidang' WHERE id_pengajuan='$id_pengajuan' AND id_staff='$id_staff'");
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
            <select name="kode_barang" class="form-control select2" disabled readonly>
              <?php foreach ($barang as $b): ?>
                <option value="<?= htmlspecialchars($b['kode_barang']) ?>" <?= $data['kode_barang'] == $b['kode_barang'] ? 'selected' : '' ?>><?= htmlspecialchars($b['nama_barang']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Satuan</label>
            <input type="text" name="satuan" class="form-control" value="<?= htmlspecialchars($data['satuan']) ?>" required maxlength="15">
          </div>
          <div class="form-group">
            <label>Jumlah</label>
            <input type="number" name="jumlah" class="form-control" min="1" value="<?= htmlspecialchars($data['jumlah']) ?>" required>
          </div>
          <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="2"><?= htmlspecialchars($data['keterangan']) ?></textarea>
          </div>
          <div class="form-group">
            <label>Bidang Pengajuan</label>
            <input type="text" name="bidang_pengajuan" class="form-control" value="<?= htmlspecialchars($data['bidang_pengajuan']) ?>" readonly required>
          </div>
          <button type="submit" class="btn btn-primary">Update</button>
          <a href="dashboard_staff.php?unit=pengajuan" class="btn btn-secondary">Kembali</a>
        </form>
      </div>
    </div>
  </div>
</div>
