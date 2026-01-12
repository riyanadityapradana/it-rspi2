  <?php
require_once("../config/koneksi.php");

if (!isset($_GET['id'])) {
  header('Location: dashboard_staff.php?unit=pemindahan&err=Data mutasi tidak ditemukan!');
  exit;
}

$mutasi_id = $_GET['id'];

// Ambil data mutasi dengan nama barang, lokasi, dan penyerahan_id terakhir
$q = mysqli_query($config, "SELECT m.*, b.nama_barang, l_asal.nama_lokasi as nama_lokasi_asal, (SELECT penyerahan_id FROM tb_penyerahan WHERE barang_id = m.barang_id ORDER BY penyerahan_id DESC LIMIT 1) as penyerahan_id FROM tb_mutasi_barang m LEFT JOIN tb_barang b ON m.barang_id = b.barang_id LEFT JOIN tb_lokasi l_asal ON m.lokasi_asal = l_asal.lokasi_id WHERE m.mutasi_id='".mysqli_real_escape_string($config, $mutasi_id)."'");
$data = mysqli_fetch_assoc($q);

if (!$data) {
  header('Location: dashboard_staff.php?unit=pemindahan&err=Data mutasi tidak ditemukan!');
  exit;
}

$penyerahan_id = $data['penyerahan_id'] ?? '';

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $barang_id = intval($_POST['barang_id']);
  $penyerahan_id = trim($_POST['penyerahan_id'] ?? $penyerahan_id);
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
    // Update lokasi barang di tb_barang
    mysqli_query($config, "UPDATE tb_barang SET lokasi_id='$lokasi_tujuan' WHERE barang_id='$barang_id'");
    
    // Update lokasi barang di tb_penyerahan dengan penyerahan_id yang spesifik
    if (!empty($penyerahan_id)) {
      mysqli_query($config, "UPDATE tb_penyerahan SET lokasi_id='$lokasi_tujuan' WHERE penyerahan_id='".mysqli_real_escape_string($config, $penyerahan_id)."'");
    }
    
    header('Location: dashboard_staff.php?unit=pemindahan&msg=Data mutasi berhasil diupdate!');
    exit;
  } else {
    header('Location: dashboard_staff.php?unit=pemindahan&err=Gagal update data mutasi!');
    exit;
  }
}

// Ambil daftar barang
$barang_list = mysqli_query($config, "SELECT b.barang_id, b.nama_barang FROM tb_barang b INNER JOIN tb_lokasi l ON b.lokasi_id = l.lokasi_id WHERE l.nama_lokasi = 'IT' AND (b.kondisi = 'baru' OR b.kondisi = 'bekas') ORDER BY b.nama_barang ASC");
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
            <input type="text" class="form-control" value="<?= htmlspecialchars($data['nama_barang'] ?? '-') ?>" readonly>
            <input type="hidden" name="barang_id" value="<?= $data['barang_id'] ?>">
          </div>
          <div class="form-group">
            <label>Lokasi Asal <span class="text-danger">*</span></label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($data['nama_lokasi_asal'] ?? '-') ?>" readonly>
            <input type="hidden" name="lokasi_asal" value="<?= $data['lokasi_asal'] ?>">
            <input type="hidden" name="penyerahan_id" value="<?= htmlspecialchars($penyerahan_id) ?>">
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
