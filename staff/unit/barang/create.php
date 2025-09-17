<?php
require_once("../config/koneksi.php");
// Toastr
if (!function_exists('toastr_script')) {
function toastr_script($msg, $type = 'success') {
    echo "<script>toastr.$type('$msg');</script>";
}}
// ...existing code...
// Ambil lokasi
$lokasi_q = mysqli_query($config, "SELECT lokasi_id, nama_lokasi FROM tb_lokasi ORDER BY nama_lokasi ASC");
$lokasi_list = [];
while ($row = mysqli_fetch_assoc($lokasi_q)) {
  $lokasi_list[] = $row;
}
// Proses simpan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $pengajuan_id = isset($_POST['pengajuan_id']) ? intval($_POST['pengajuan_id']) : NULL;
  $nama_barang = trim($_POST['nama_barang']);
  $jenis_barang = trim($_POST['jenis_barang']);
  $nomor_seri = trim($_POST['nomor_seri']);
  // Jika jenis barang bukan Komputer & Laptop, ip_address = null
  if ($jenis_barang === 'Komputer & Laptop') {
    $ip_address = trim($_POST['ip_address']);
  } else {
    $ip_address = '';
  }
  $jumlah = intval($_POST['jumlah']);
  $harga = floatval($_POST['harga']);
  $spesifikasi = trim($_POST['spesifikasi']);
  $tanggal_terima = trim($_POST['tanggal_terima']);
  // Proses upload foto
  $foto_nama = NULL;
  if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif','webp'];
    if (in_array($ext, $allowed)) {
      $foto_nama = uniqid('barang_') . '.' . $ext;
      $tujuan = __DIR__ . '/foto-barang/' . $foto_nama;
      move_uploaded_file($_FILES['foto']['tmp_name'], $tujuan);
    }
  }
  $cek = mysqli_query($config, "SELECT 1 FROM tb_barang WHERE barang_id='$barang_id' AND nomor_seri='$nomor_seri'");
  if (mysqli_num_rows($cek) > 0) {
    header('Location: dashboard_staff.php?unit=barang&err=Barang sudah terdaftar!');
    exit;
  } else {
    $q = mysqli_query($config, "INSERT INTO tb_barang (pengajuan_id, nama_barang, jenis_barang, nomor_seri, ip_address, jumlah, harga, spesifikasi, tanggal_terima, foto) VALUES (
      " . ($pengajuan_id ? "'$pengajuan_id'," : "NULL,") . "
      '$nama_barang', '$jenis_barang', '$nomor_seri', '$ip_address', $jumlah, $harga, '$spesifikasi', '$tanggal_terima', " . ($foto_nama ? "'$foto_nama'" : "NULL") . ")");
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
  <form method="post" enctype="multipart/form-data">
          <div class="form-group">
            <label>Tanggal Terima</label>
            <input type="date" name="tanggal_terima" class="form-control" value="<?= date('Y-m-d') ?>" required>
          </div>
          <div class="form-group">
            <label>Nama Barang</label>
            <input type="text" name="nama_barang" class="form-control" required maxlength="150">
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
            <label>Nomor Seri</label>
            <input type="text" name="nomor_seri" class="form-control" maxlength="150"  placeholder="Contoh: SN123456789">
          </div>
          <div class="form-group" id="ipAddressGroup" style="display:none;">
            <label>IP Address</label>
            <input type="text" name="ip_address" class="form-control" maxlength="50" placeholder="Contoh: 192.168.1.10">
          </div>
          <script>
          document.addEventListener('DOMContentLoaded', function() {
            var jenisSelect = document.querySelector('select[name="jenis_barang"]');
            var ipGroup = document.getElementById('ipAddressGroup');
            function toggleIp() {
              if (jenisSelect.value === 'Komputer & Laptop') {
                ipGroup.style.display = '';
              } else {
                ipGroup.style.display = 'none';
                ipGroup.querySelector('input').value = '';
              }
            }
            // Trigger on select2 change too
            $(jenisSelect).on('change', toggleIp);
            // Initial check
            setTimeout(toggleIp, 100);
          });
          </script>
          <div class="form-group">
            <label>Jumlah</label>
            <input type="number" name="jumlah" class="form-control" min="1" required>
          </div>
          <div class="form-group">
            <label>Harga</label>
            <input type="number" name="harga" class="form-control" min="0" step="0.01" required>
          </div>
          <div class="form-group">
            <label>Spesifikasi</label>
            <textarea name="spesifikasi" class="form-control" rows="2"></textarea>
          </div>
          <!-- <div class="form-group"> 
            <label>Kondisi</label>
            <select name="kondisi" class="form-control" required>
              <option value="baru">Baru</option>
              <option value="bekas">Bekas</option>
              <option value="rusak">Rusak</option>
              <option value="dalam perbaikan">Dalam Perbaikan</option>
            </select>
          </div>
          <div class="form-group">
            <label>Lokasi</label>
            <select name="lokasi_id" class="form-control select2" required>
              <option value="">-- Pilih Lokasi --</option>
              <?php foreach ($lokasi_list as $lokasi): ?>
                <option value="<?= $lokasi['lokasi_id'] ?>"><?= htmlspecialchars($lokasi['nama_lokasi']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="2"></textarea>
          </div>-->
          <div class="form-group">
            <label>Foto Barang</label>
            <input type="file" name="foto" class="form-control" accept="image/*">
          </div>
          <button type="submit" class="btn btn-primary">Simpan</button>
          <a href="dashboard_staff.php?unit=barang" class="btn btn-secondary">Kembali</a>
        </form>
      </div>
    </div>
  </div>
</div>
