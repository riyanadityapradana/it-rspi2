<?php
require_once("../config/koneksi.php");
$id_staff = $_SESSION['id_user'];
// Ambil data barang untuk dropdown
$barang = [];
$q = mysqli_query($config, "SELECT kode_barang, nama_barang FROM tb_barang ORDER BY nama_barang ASC");
while ($row = mysqli_fetch_assoc($q)) {
    $barang[] = $row;
}
// Proses simpan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_barang = trim($_POST['kode_barang']);
    $satuan      = trim($_POST['satuan']);
    $jumlah      = intval($_POST['jumlah']);
    $keterangan  = trim($_POST['keterangan']);
    $bidang      = isset($_POST['bidang_pengajuan']) ? trim($_POST['bidang_pengajuan']) : '';
    if (!$kode_barang || !$satuan || !$jumlah) {
        header('Location: dashboard_staff.php?unit=pengajuan&err=Data tidak lengkap!');
        exit;
    }
    $q = mysqli_query($config, "INSERT INTO tb_pengajuan_barang (id_staff, kode_barang, satuan, jumlah, keterangan, bidang_pengajuan) VALUES ('$id_staff', '$kode_barang', '$satuan', $jumlah, '$keterangan', '$bidang')");
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
            <select name="kode_barang" class="form-control select2" required>
              <option value="">-- Pilih Barang --</option>
              <?php foreach ($barang as $b): ?>
                <option value="<?= htmlspecialchars($b['kode_barang']) ?>"><?= htmlspecialchars($b['nama_barang']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Satuan</label>
            <input type="text" name="satuan" class="form-control" required maxlength="15">
          </div>
          <div class="form-group">
            <label>Jumlah</label>
            <input type="number" name="jumlah" class="form-control" min="1" required>
          </div>
          <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="2"></textarea>
          </div>
          <div class="form-group">
            <label>Bidang Pengajuan</label>
            <input type="text" name="bidang_pengajuan" class="form-control" value="Divis Unit IT" readonly required>
          </div>
          <button type="submit" class="btn btn-primary">Simpan</button>
          <a href="dashboard_staff.php?unit=pengajuan" class="btn btn-secondary">Kembali</a>
        </form>
      </div>
    </div>
  </div>
</div>
