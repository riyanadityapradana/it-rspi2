<?php
require_once("../config/koneksi.php");

if (!isset($_GET['id'])) {
    header('Location: dashboard_staff.php?unit=pemindahan&err=Data pemindahan tidak ditemukan!');
    exit;
}

$id_pemindahan = $_GET['id'];

// Ambil data pemindahan
$q = mysqli_query($config, "SELECT * FROM tb_pemindahan_barang WHERE id_pemindahan='".mysqli_real_escape_string($config, $id_pemindahan)."'");
$data = mysqli_fetch_assoc($q);

if (!$data) {
    header('Location: dashboard_staff.php?unit=pemindahan&err=Data pemindahan tidak ditemukan!');
    exit;
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_barang = trim($_POST['kode_barang']);
    $tanggal_pemindahan = trim($_POST['tanggal_pemindahan']);
    $ke_unit = trim($_POST['ke_unit']);
    $alasan_pemindahan = trim($_POST['alasan_pemindahan']);
    
    // Validasi
    if (empty($kode_barang) || empty($tanggal_pemindahan) || empty($ke_unit) || empty($alasan_pemindahan)) {
        header('Location: dashboard_staff.php?unit=pemindahan&err=Semua field harus diisi!');
        exit;
    }
    
    // Cek apakah barang ada
    $cek_barang = mysqli_query($config, "SELECT 1 FROM tb_barang WHERE kode_barang='".mysqli_real_escape_string($config, $kode_barang)."'");
    if (mysqli_num_rows($cek_barang) == 0) {
        header('Location: dashboard_staff.php?unit=pemindahan&err=Kode barang tidak ditemukan!');
        exit;
    }
    
    // Update data pemindahan
    $q = mysqli_query($config, "UPDATE tb_pemindahan_barang SET 
                               kode_barang='".mysqli_real_escape_string($config, $kode_barang)."', 
                               tanggal_pemindahan='".mysqli_real_escape_string($config, $tanggal_pemindahan)."', 
                               ke_unit='".mysqli_real_escape_string($config, $ke_unit)."', 
                               alasan_pemindahan='".mysqli_real_escape_string($config, $alasan_pemindahan)."' 
                               WHERE id_pemindahan='".mysqli_real_escape_string($config, $id_pemindahan)."'");
    if ($q) {
        header('Location: dashboard_staff.php?unit=pemindahan&msg=Data pemindahan berhasil diupdate!');
        exit;
    } else {
        header('Location: dashboard_staff.php?unit=pemindahan&err=Gagal update data pemindahan!');
        exit;
    }
}

// Ambil daftar barang untuk dropdown
$kode_barang_saat_ini = $data['kode_barang'];
$barang_list = mysqli_query($config, "SELECT kode_barang, nama_barang FROM tb_barang WHERE stts_brg = 'Baik' OR kode_barang = '" . mysqli_real_escape_string($config, $kode_barang_saat_ini) . "' ORDER BY nama_barang ASC");

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
            <label>Kode Barang <span class="text-danger">*</span></label>
            <select name="kode_barang" class="form-control select2" required>
              <option value="">-- Pilih Barang --</option>
              <?php 
              mysqli_data_seek($barang_list, 0);
              while ($barang = mysqli_fetch_assoc($barang_list)): ?>
                <option value="<?= htmlspecialchars($barang['kode_barang']) ?>" <?= $data['kode_barang'] == $barang['kode_barang'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($barang['kode_barang']) ?> - <?= htmlspecialchars($barang['nama_barang']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Tanggal Pemindahan <span class="text-danger">*</span></label>
            <input type="date" name="tanggal_pemindahan" class="form-control" value="<?= htmlspecialchars($data['tanggal_pemindahan']) ?>" required>
          </div>
          <div class="form-group">
            <label>Ke Unit <span class="text-danger">*</span></label>
            <select name="ke_unit" class="form-control select2" required>
              <option value="">-- Pilih Unit --</option>
              <?php foreach ($unit_list as $unit): ?>
                <option value="<?= htmlspecialchars($unit) ?>" <?= $data['ke_unit'] == $unit ? 'selected' : '' ?>><?= htmlspecialchars($unit) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Alasan Pemindahan <span class="text-danger">*</span></label>
            <textarea name="alasan_pemindahan" class="form-control" rows="3" placeholder="Jelaskan alasan pemindahan barang..." required><?= htmlspecialchars($data['alasan_pemindahan']) ?></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Update</button>
          <a href="dashboard_staff.php?unit=pemindahan" class="btn btn-secondary">Kembali</a>
        </form>
      </div>
    </div>
  </div>
</div>
