  <?php
require_once("../config/koneksi.php");
if (!isset($_GET['id'])) {
  header('Location: dashboard_staff.php?unit=barang&err=Barang tidak ditemukan!');
  exit;
}
$barang_id = $_GET['id'];
// Ambil data barang
$q = mysqli_query($config, "SELECT * FROM tb_barang WHERE barang_id='".mysqli_real_escape_string($config, $barang_id)."'");
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
// Pilihan lokasi
$lokasi_q = mysqli_query($config, "SELECT lokasi_id, nama_lokasi FROM tb_lokasi ORDER BY nama_lokasi ASC");
$lokasi_list = [];
while ($row = mysqli_fetch_assoc($lokasi_q)) {
  $lokasi_list[] = $row;
}
// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama_barang   = trim($_POST['nama_barang']);
  $jenis_barang  = trim($_POST['jenis_barang']);
  $nomor_seri    = trim($_POST['nomor_seri']);
  $ip_address    = trim($_POST['ip_address']);
  $jumlah        = intval($_POST['jumlah']);
  $harga         = floatval($_POST['harga']);
  $spesifikasi   = trim($_POST['spesifikasi']);
  $tanggal_terima= trim($_POST['tanggal_terima']);
  $kondisi       = trim($_POST['kondisi']);
  $lokasi_id     = intval($_POST['lokasi_id']);
  $keterangan    = trim($_POST['keterangan']);
  $q = mysqli_query($config, "UPDATE tb_barang SET nama_barang='$nama_barang', jenis_barang='$jenis_barang', nomor_seri='$nomor_seri', ip_address='$ip_address', jumlah=$jumlah, harga=$harga, spesifikasi='$spesifikasi', tanggal_terima='$tanggal_terima', kondisi='$kondisi', lokasi_id=$lokasi_id, keterangan='$keterangan' WHERE barang_id='$barang_id'");
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
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Nama Barang</label>
                <input type="text" name="nama_barang" class="form-control" value="<?= htmlspecialchars($data['nama_barang']) ?>" required maxlength="150">
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
                <label>Nomor Seri</label>
                <input type="text" name="nomor_seri" class="form-control" value="<?= htmlspecialchars($data['nomor_seri']) ?>" maxlength="150">
              </div>
              <div class="form-group">
                <label>IP Address</label>
                <input type="text" name="ip_address" class="form-control" value="<?= htmlspecialchars($data['ip_address']) ?>" maxlength="50">
              </div>
              <div class="form-group">
                <label>Jumlah</label>
                <input type="number" name="jumlah" class="form-control" min="1" value="<?= htmlspecialchars($data['jumlah']) ?>" required>
              </div>
              <div class="form-group">
                <label>Harga</label>
                <input type="number" name="harga" class="form-control" min="0" step="0.01" value="<?= htmlspecialchars($data['harga']) ?>" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Spesifikasi</label>
                <textarea name="spesifikasi" class="form-control" rows="2"><?= htmlspecialchars($data['spesifikasi']) ?></textarea>
              </div>
              <div class="form-group">
                <label>Tanggal Terima</label>
                <input type="date" name="tanggal_terima" class="form-control" value="<?= htmlspecialchars($data['tanggal_terima']) ?>" required>
              </div>
              <div class="form-group">
                <label>Kondisi</label>
                <select name="kondisi" class="form-control" required>
                  <option value="baru" <?= $data['kondisi']=='baru'?'selected':'' ?>>Baru</option>
                  <option value="bekas" <?= $data['kondisi']=='bekas'?'selected':'' ?>>Bekas</option>
                  <option value="rusak" <?= $data['kondisi']=='rusak'?'selected':'' ?>>Rusak</option>
                  <option value="dalam perbaikan" <?= $data['kondisi']=='dalam perbaikan'?'selected':'' ?>>Dalam Perbaikan</option>
                </select>
              </div>
              <div class="form-group">
                <label>Lokasi</label>
                <select name="lokasi_id" class="form-control select2" required>
                  <option value="">-- Pilih Lokasi --</option>
                  <?php foreach ($lokasi_list as $lokasi): ?>
                    <option value="<?= $lokasi['lokasi_id'] ?>" <?= $data['lokasi_id']==$lokasi['lokasi_id']?'selected':'' ?>><?= htmlspecialchars($lokasi['nama_lokasi']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3"><?= htmlspecialchars($data['keterangan']) ?></textarea>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Update</button>
          <a href="dashboard_staff.php?unit=barang" class="btn btn-secondary">Kembali</a>
        </form>
      </div>
    </div>
  </div>
</div>
