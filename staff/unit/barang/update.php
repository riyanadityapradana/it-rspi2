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
// Ambil data penyerahan
$penyerahan_list = [];
$penyerahan_q = mysqli_query($config, "SELECT p.*, l.nama_lokasi FROM tb_penyerahan p LEFT JOIN tb_lokasi l ON p.lokasi_id = l.lokasi_id WHERE p.barang_id='$barang_id' ORDER BY p.penyerahan_id ASC");
while ($row = mysqli_fetch_assoc($penyerahan_q)) {
  $penyerahan_list[] = $row;
}
// Pilihan lokasi
$lokasi_q = mysqli_query($config, "SELECT lokasi_id, nama_lokasi FROM tb_lokasi ORDER BY nama_lokasi ASC");
$lokasi_list = [];
while ($row = mysqli_fetch_assoc($lokasi_q)) {
  $lokasi_list[] = $row;
}
// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['penyerahan_id'])) {
    // Update existing penyerahan
    for ($i = 0; $i < count($_POST['penyerahan_id']); $i++) {
      $penyerahan_id = intval($_POST['penyerahan_id'][$i]);
      $lokasi_id = intval($_POST['lokasi_id'][$i]);
      $kondisi = trim($_POST['kondisi'][$i]);
      $keterangan = trim($_POST['keterangan_unit'][$i]);
      mysqli_query($config, "UPDATE tb_penyerahan SET lokasi_id='$lokasi_id', kondisi='$kondisi', keterangan='$keterangan' WHERE penyerahan_id='$penyerahan_id'");
    }
    header('Location: dashboard_staff.php?unit=barang&msg=Penyerahan berhasil diupdate!');
    exit;
  } else {
    // Insert new penyerahan for unit 1
    $lokasi_id = intval($_POST['lokasi_id'][0]);
    $kondisi = trim($_POST['kondisi'][0]);
    $keterangan = trim($_POST['keterangan_unit'][0]);
    $query = mysqli_query($config, "INSERT INTO tb_penyerahan (barang_id, lokasi_id, kondisi, keterangan) VALUES ('$barang_id', '$lokasi_id', '$kondisi', '$keterangan')");
    if ($query) {
      header('Location: dashboard_staff.php?unit=barang&msg=Unit 1 berhasil diserahkan!');
      exit;
    } else {
      header('Location: dashboard_staff.php?unit=barang&err=Gagal menyerahkan unit 1: ' . mysqli_error($config));
      exit;
    }
  }
}
?>
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Edit Penyerahan Barang</h1>
      </div>
    </div>
  </div>
</div>
<div class="content">
  <div class="container-fluid">
    <div class="card">
      <div class="card-body">
        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="barang_id" value="<?= $barang_id ?>">
          <h4>Edit Penyerahan Barang</h4>
          <?php if (!empty($penyerahan_list)): ?>
            <?php foreach ($penyerahan_list as $index => $p): ?>
              <div class="card mb-3">
                <div class="card-header">
                  <h5 class="card-title">Unit <?= $index + 1 ?> - Saat Ini: <?= htmlspecialchars($p['nama_lokasi']) ?> (<?= htmlspecialchars($p['kondisi']) ?>)</h5>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Lokasi Baru:</label>
                        <select name="lokasi_id[]" class="form-control select2" required>
                          <option value="">-- Pilih Lokasi --</option>
                          <?php foreach ($lokasi_list as $lokasi): ?>
                            <option value="<?= $lokasi['lokasi_id'] ?>" <?= $p['lokasi_id']==$lokasi['lokasi_id']?'selected':'' ?>><?= htmlspecialchars($lokasi['nama_lokasi']) ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                      <div class="form-group">
                        <label>Kondisi:</label>
                        <select name="kondisi[]" class="form-control" required>
                          <option value="baru" <?= $p['kondisi']=='baru'?'selected':'' ?>>Baru</option>
                          <option value="bekas" <?= $p['kondisi']=='bekas'?'selected':'' ?>>Bekas</option>
                          <option value="rusak" <?= $p['kondisi']=='rusak'?'selected':'' ?>>Rusak</option>
                          <option value="dalam perbaikan" <?= $p['kondisi']=='dalam perbaikan'?'selected':'' ?>>Dalam Perbaikan</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Keterangan:</label>
                        <textarea name="keterangan_unit[]" class="form-control" rows="4"><?= htmlspecialchars($p['keterangan']) ?></textarea>
                      </div>
                    </div>
                  </div>
                  <input type="hidden" name="penyerahan_id[]" value="<?= $p['penyerahan_id'] ?>">
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="card mb-3">
              <div class="card-header">
                <h5 class="card-title">Unit 1 - Belum Diserahkan</h5>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Lokasi:</label>
                      <select name="lokasi_id[]" class="form-control select2" required>
                        <option value="">-- Pilih Lokasi --</option>
                        <?php foreach ($lokasi_list as $lokasi): ?>
                          <option value="<?= $lokasi['lokasi_id'] ?>"><?= htmlspecialchars($lokasi['nama_lokasi']) ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label>Kondisi:</label>
                      <select name="kondisi[]" class="form-control" required>
                        <option value="baru">Baru</option>
                        <option value="bekas">Bekas</option>
                        <option value="rusak">Rusak</option>
                        <option value="dalam perbaikan">Dalam Perbaikan</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Keterangan:</label>
                      <textarea name="keterangan_unit[]" class="form-control" rows="4"></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endif; ?>
          <button type="submit" class="btn btn-primary">Update Penyerahan</button>
          <a href="dashboard_staff.php?unit=barang" class="btn btn-secondary">Kembali</a>
        </form>
      </div>
    </div>
  </div>
</div>
