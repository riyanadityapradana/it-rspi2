<?php
require_once("../config/koneksi.php");
// Toastr
if (!function_exists('toastr_script')) {
function toastr_script($msg, $type = 'success') {
    echo "<script>toastr.$type('$msg');</script>";
}}
// Generate kode otomatis
$q = mysqli_query($config, "SELECT kode_barang FROM tb_barang WHERE kode_barang LIKE 'BRG/RSPI-%' ORDER BY kode_barang DESC LIMIT 1");
$last = mysqli_fetch_assoc($q);
if ($last && preg_match('/(\\d+)$/', $last['kode_barang'], $m)) {
    $next = intval($m[1]) + 1;
} else {
    $next = 1;
}
$kode_baru = 'BRG/RSPI-' . str_pad($next, 3, '0', STR_PAD_LEFT);
// Proses simpan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_barang   = trim($_POST['kode_barang']);
    $nama_barang   = trim($_POST['nama_barang']);
    $spesifikasi   = trim($_POST['spesifikasi']);
    $jenis_barang  = trim($_POST['jenis_barang']);
    $stok          = intval($_POST['stok']);
    $cek = mysqli_query($config, "SELECT 1 FROM tb_barang WHERE kode_barang='$kode_barang'");
    if (mysqli_num_rows($cek) > 0) {
        header('Location: dashboard_staff.php?unit=barang&err=Kode barang sudah terdaftar!');
        exit;
    } else {
        $q = mysqli_query($config, "INSERT INTO tb_barang (kode_barang, nama_barang, spesifikasi, jenis_barang, stok) VALUES ('$kode_barang', '$nama_barang', '$spesifikasi', '$jenis_barang', $stok)");
        if ($q) {
            header('Location: dashboard_staff.php?unit=barang&msg=Barang berhasil ditambahkan!');
            exit;
        } else {
            header('Location: dashboard_staff.php?unit=barang&err=Gagal menambah barang!');
            exit;
        }
    }
}
// Pilihan jenis barang
$jenis_list = [
    'Komputer & Laptop',
    'Komponen Komputer & Laptop',
    'Printer & Scanner',
    'Komponen Printer & Scanner',
    'Komponen Network'
];
?>
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Tambah Barang</h1>
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
            <input type="text" name="kode_barang" class="form-control" value="<?= $kode_baru ?>" readonly required maxlength="15">
          </div>
          <div class="form-group">
            <label>Nama Barang</label>
            <input type="text" name="nama_barang" class="form-control" required maxlength="100">
          </div>
          <div class="form-group">
            <label>Jenis Barang</label>
            <select name="jenis_barang" class="form-control select2" required>
              <option value="">-- Pilih Jenis --</option>
              <?php foreach ($jenis_list as $jenis): ?>
                <option value="<?= htmlspecialchars($jenis) ?>"><?= htmlspecialchars($jenis) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Spesifikasi</label>
            <textarea name="spesifikasi" class="form-control" rows="2"></textarea>
          </div>
          <div class="form-group">
            <label>Stok</label>
            <input type="number" name="stok" class="form-control" min="0" required>
          </div>
          <button type="submit" class="btn btn-primary">Simpan</button>
          <a href="dashboard_staff.php?unit=barang" class="btn btn-secondary">Kembali</a>
        </form>
      </div>
    </div>
  </div>
</div>
