<?php
require_once("../config/koneksi.php");
if (!isset($_GET['id'])) {
    header('Location: dashboard_staff.php?unit=pengajuan&err=Pengajuan tidak ditemukan!');
    exit;
}

$pengajuan_id = (int) $_GET['id'];
$id_user = $_SESSION['id_user'];

$stmt = $config->prepare("SELECT * FROM tb_pengajuan WHERE pengajuan_id = ? AND id_user = ? LIMIT 1");
$stmt->bind_param('ii', $pengajuan_id, $id_user);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    header('Location: dashboard_staff.php?unit=pengajuan&err=Pengajuan tidak ditemukan!');
    exit;
}
if ($data['status'] !== 'diajukan') {
    header('Location: dashboard_staff.php?unit=pengajuan&err=Pengajuan tidak bisa diedit!');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_barang = trim($_POST['nama_barang']);
    $unit = trim($_POST['unit']);
    $jumlah = isset($_POST['jumlah']) ? (int) $_POST['jumlah'] : 0;
    $perkiraan_harga = isset($_POST['perkiraan_harga']) ? (float) $_POST['perkiraan_harga'] : 0;
    $keterangan = trim($_POST['keterangan']);

    if ($nama_barang === '' || $unit === '' || $jumlah <= 0 || $perkiraan_harga <= 0) {
        header('Location: dashboard_staff.php?unit=pengajuan&err=Data tidak lengkap!');
        exit;
    }

    $stmt = $config->prepare("UPDATE tb_pengajuan SET nama_barang = ?, unit = ?, jumlah = ?, perkiraan_harga = ?, keterangan = ? WHERE pengajuan_id = ? AND id_user = ? AND status = 'diajukan'");
    $stmt->bind_param('ssidsii', $nama_barang, $unit, $jumlah, $perkiraan_harga, $keterangan, $pengajuan_id, $id_user);
    $success = $stmt->execute();
    $stmt->close();

    if ($success) {
        header('Location: dashboard_staff.php?unit=pengajuan&msg=Pengajuan barang berhasil diupdate!');
        exit;
    }

    header('Location: dashboard_staff.php?unit=pengajuan&err=Gagal update pengajuan!');
    exit;
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
