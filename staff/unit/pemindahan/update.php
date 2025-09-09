  <?php
require_once("../config/koneksi.php");

if (!isset($_GET['id'])) {
  header('Location: dashboard_staff.php?unit=pemindahan&err=Data mutasi tidak ditemukan!');
  exit;
}

$mutasi_id = $_GET['id'];

// Ambil data mutasi
$q = mysqli_query($config, "SELECT * FROM tb_mutasi_barang WHERE mutasi_id='".mysqli_real_escape_string($config, $mutasi_id)."'");
$data = mysqli_fetch_assoc($q);

if (!$data) {
  header('Location: dashboard_staff.php?unit=pemindahan&err=Data mutasi tidak ditemukan!');
  exit;
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $barang_id = intval($_POST['barang_id']);
  $lokasi_asal = intval($_POST['lokasi_asal']);
  $lokasi_tujuan = intval($_POST['lokasi_tujuan']);
  $tanggal_mutasi = trim($_POST['tanggal_mutasi']);
  $keterangan = trim($_POST['keterangan']);
  // Validasi
  if (empty($barang_id) || empty($lokasi_asal) || empty($lokasi_tujuan) || empty($tanggal_mutasi)) {
    header('Location: dashboard_staff.php?unit=pemindahan&err=Semua field harus diisi!');
    exit;
  }
  // Update data mutasi
  $q = mysqli_query($config, "UPDATE tb_mutasi_barang SET barang_id='$barang_id', lokasi_asal='$lokasi_asal', lokasi_tujuan='$lokasi_tujuan', tanggal_mutasi='$tanggal_mutasi', keterangan='".mysqli_real_escape_string($config, $keterangan)."' WHERE mutasi_id='$mutasi_id'");
  if ($q) {
    // Update lokasi barang jika lokasi tujuan berubah
    mysqli_query($config, "UPDATE tb_barang SET lokasi_id='$lokasi_tujuan' WHERE barang_id='$barang_id'");
    header('Location: dashboard_staff.php?unit=pemindahan&msg=Data mutasi berhasil diupdate!');
    exit;
  } else {
    header('Location: dashboard_staff.php?unit=pemindahan&err=Gagal update data mutasi!');
    exit;
  }
}

// Ambil daftar barang untuk dropdown
$barang_list = mysqli_query($config, "SELECT barang_id, nama_barang FROM tb_barang ORDER BY nama_barang ASC");
// Ambil daftar lokasi untuk dropdown
$lokasi_list = mysqli_query($config, "SELECT lokasi_id, nama_lokasi FROM tb_lokasi ORDER BY nama_lokasi ASC");
// ...existing code...
?>
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Edit Pemindahan Barang</h1>
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
            <label>Barang <span class="text-danger">*</span></label>
            <select name="barang_id" class="form-control select2" required>
              <option value="">-- Pilih Barang --</option>
              <?php 
              mysqli_data_seek($barang_list, 0);
              while ($barang = mysqli_fetch_assoc($barang_list)): ?>
                <option value="<?= $barang['barang_id'] ?>" <?= $data['barang_id'] == $barang['barang_id'] ? 'selected' : '' ?>><?= htmlspecialchars($barang['nama_barang']) ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Lokasi Asal <span class="text-danger">*</span></label>
            <select name="lokasi_asal" class="form-control select2" required>
              <option value="">-- Pilih Lokasi Asal --</option>
              <?php 
              mysqli_data_seek($lokasi_list, 0);
              while ($lokasi = mysqli_fetch_assoc($lokasi_list)): ?>
                <option value="<?= $lokasi['lokasi_id'] ?>" <?= $data['lokasi_asal'] == $lokasi['lokasi_id'] ? 'selected' : '' ?>><?= htmlspecialchars($lokasi['nama_lokasi']) ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Lokasi Tujuan <span class="text-danger">*</span></label>
            <select name="lokasi_tujuan" class="form-control select2" required>
              <option value="">-- Pilih Lokasi Tujuan --</option>
              <?php 
              mysqli_data_seek($lokasi_list, 0);
              while ($lokasi = mysqli_fetch_assoc($lokasi_list)): ?>
                <option value="<?= $lokasi['lokasi_id'] ?>" <?= $data['lokasi_tujuan'] == $lokasi['lokasi_id'] ? 'selected' : '' ?>><?= htmlspecialchars($lokasi['nama_lokasi']) ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Tanggal Mutasi <span class="text-danger">*</span></label>
            <input type="date" name="tanggal_mutasi" class="form-control" value="<?= htmlspecialchars($data['tanggal_mutasi']) ?>" required>
          </div>
          <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="2" placeholder="Keterangan tambahan"><?= htmlspecialchars($data['keterangan']) ?></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Update</button>
          <a href="dashboard_staff.php?unit=pemindahan" class="btn btn-secondary">Kembali</a>
        </form>
      </div>
    </div>
  </div>
</div>
