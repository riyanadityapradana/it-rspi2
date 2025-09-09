<?php
require_once("../config/koneksi.php");

// Proses simpan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $barang_id = intval($_POST['barang_id']);
  $lokasi_tujuan = intval($_POST['lokasi_tujuan']);
  $tanggal_mutasi = trim($_POST['tanggal_mutasi']);
  $keterangan = trim($_POST['keterangan']);
  $id_user = $_SESSION['id_user'] ?? 1;
  // Validasi
  if (empty($barang_id) || empty($lokasi_tujuan) || empty($tanggal_mutasi)) {
    header('Location: dashboard_staff.php?unit=pemindahan&err=Semua field harus diisi!');
    exit;
  }
  // Ambil lokasi asal dari barang
  $barang_q = mysqli_query($config, "SELECT lokasi_id FROM tb_barang WHERE barang_id='$barang_id'");
  $barang_row = mysqli_fetch_assoc($barang_q);
  if (!$barang_row) {
    header('Location: dashboard_staff.php?unit=pemindahan&err=Barang tidak ditemukan!');
    exit;
  }
  $lokasi_asal = $barang_row['lokasi_id'];
  // Insert ke tb_mutasi_barang
  $q = mysqli_query($config, "INSERT INTO tb_mutasi_barang (barang_id, lokasi_asal, lokasi_tujuan, tanggal_mutasi, id_user, keterangan) VALUES (
    '$barang_id', '$lokasi_asal', '$lokasi_tujuan', '$tanggal_mutasi', '$id_user', '".mysqli_real_escape_string($config, $keterangan)."')");
  if ($q) {
    // Update lokasi barang
    mysqli_query($config, "UPDATE tb_barang SET lokasi_id='$lokasi_tujuan' WHERE barang_id='$barang_id'");
    header('Location: dashboard_staff.php?unit=pemindahan&msg=Data mutasi berhasil ditambahkan!');
    exit;
  } else {
    header('Location: dashboard_staff.php?unit=pemindahan&err=Gagal menambah data mutasi!');
    exit;
  }
}

// Ambil daftar barang untuk dropdown
// Ambil daftar barang
$barang_list = mysqli_query($config, "SELECT barang_id, nama_barang FROM tb_barang ORDER BY nama_barang ASC");
// Ambil daftar lokasi
$lokasi_list = mysqli_query($config, "SELECT lokasi_id, nama_lokasi FROM tb_lokasi ORDER BY nama_lokasi ASC");

// Daftar unit yang umum
$unit_list = [
    'Manajemen',
    'Keuangan', 
    'SDM',
    'Pelayanan',
    'Rawat Inap',
    'Rawat Jalan',
    'IGD',
    'Farmasi',
    'Laboratorium',
    'Radiologi',
    'Gizi',
    'CSSD',
    'Laundry',
    'Housekeeping',
    'Security',
    'IT',
    'Lainnya'
];
?>
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Tambah Pemindahan Barang</h1>
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
              <?php while ($barang = mysqli_fetch_assoc($barang_list)): ?>
                <option value="<?= $barang['barang_id'] ?>"> <?= htmlspecialchars($barang['nama_barang']) ?> </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Lokasi Tujuan <span class="text-danger">*</span></label>
            <select name="lokasi_tujuan" class="form-control select2" required>
              <option value="">-- Pilih Lokasi --</option>
              <?php while ($lokasi = mysqli_fetch_assoc($lokasi_list)): ?>
                <option value="<?= $lokasi['lokasi_id'] ?>"> <?= htmlspecialchars($lokasi['nama_lokasi']) ?> </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Tanggal Mutasi <span class="text-danger">*</span></label>
            <input type="date" name="tanggal_mutasi" class="form-control" value="<?= date('Y-m-d') ?>" required>
          </div>
          <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="2" placeholder="Keterangan tambahan"></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Simpan</button>
          <a href="dashboard_staff.php?unit=pemindahan" class="btn btn-secondary">Kembali</a>
        </form>
      </div>
    </div>
  </div>
</div>
