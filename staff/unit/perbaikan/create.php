<?php
// Cek jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $barang_id            = $_POST['barang_id'];
  $tanggal_lapor        = $_POST['tanggal_lapor'];
  $deskripsi_kerusakan  = $_POST['deskripsi_kerusakan'];
  $tindakan_perbaikan   = $_POST['tindakan_perbaikan'];
  $tanggal_selesai      = isset($_POST['tanggal_selesai']) ? $_POST['tanggal_selesai'] : NULL;
  $biaya_perbaikan      = isset($_POST['biaya_perbaikan']) ? $_POST['biaya_perbaikan'] : NULL;
  $teknisi              = isset($_POST['teknisi']) ? $_POST['teknisi'] : NULL;
  $keterangan           = $_POST['keterangan'];
  // Validasi barang_id
  if (empty($barang_id)) {
    echo '<div class="alert alert-danger">Barang harus dipilih!</div>';
  } else {
    // Cek barang_id valid
    $cek_barang = mysqli_query($config, "SELECT barang_id FROM tb_barang WHERE barang_id='$barang_id'");
    if (mysqli_num_rows($cek_barang) == 0) {
      echo '<div class="alert alert-danger">Barang tidak ditemukan di database!</div>';
    } else {
      // Logika insert sesuai pilihan tindakan_perbaikan
      if ($tindakan_perbaikan == 'Service luar') {
        $teknisi = NULL;
        $query = "INSERT INTO tb_perbaikan_barang (barang_id, tanggal_lapor, deskripsi_kerusakan, tindakan_perbaikan, tanggal_selesai, biaya_perbaikan, teknisi, status, keterangan) VALUES ('$barang_id', '$tanggal_lapor', '$deskripsi_kerusakan', '$tindakan_perbaikan', " . ($tanggal_selesai ? "'$tanggal_selesai'" : "NULL") . ", " . ($biaya_perbaikan ? "'$biaya_perbaikan'" : "NULL") . ", NULL, 'diajukan', '$keterangan')";
      } else if ($tindakan_perbaikan == 'Service sendiri') {
        $tanggal_selesai = NULL;
        $biaya_perbaikan = NULL;
        $query = "INSERT INTO tb_perbaikan_barang (barang_id, tanggal_lapor, deskripsi_kerusakan, tindakan_perbaikan, tanggal_selesai, biaya_perbaikan, teknisi, status, keterangan) VALUES ('$barang_id', '$tanggal_lapor', '$deskripsi_kerusakan', '$tindakan_perbaikan', NULL, NULL, " . ($teknisi ? "'$teknisi'" : "NULL") . ", 'diajukan', '$keterangan')";
      } else {
        // fallback jika tidak dipilih
        $query = "INSERT INTO tb_perbaikan_barang (barang_id, tanggal_lapor, deskripsi_kerusakan, tindakan_perbaikan, status, keterangan) VALUES ('$barang_id', '$tanggal_lapor', '$deskripsi_kerusakan', '', 'diajukan', '$keterangan')";
      }
      $input = mysqli_query($config, $query);
      if ($input) {
        header('Location: dashboard_staff.php?unit=perbaikan&msg=Data perbaikan berhasil ditambahkan!');
        exit;
      } else {
        header('Location: dashboard_staff.php?unit=perbaikan&err=Gagal menambah data perbaikan!');
        exit;
      }
    }
  }
}
// Ambil daftar barang
$barang_list = mysqli_query($config, "SELECT barang_id, nama_barang FROM tb_barang WHERE kondisi = 'rusak' ORDER BY nama_barang ASC");
?>
<!-- HTML Form -->
<section class="content-header">
  <!-- [Header sama seperti punyamu] -->
</section>
<section class="content">
  <div class="container-fluid">
    <div class="card card-default">
      <div class="card-header">
        <h3 class="card-title">Silahkan Input Catatan Kegiatan Harian</h3>
      </div>
      <form method="post" enctype="multipart/form-data">
        <div class="card-body">
          <div class="form-group">
            <label>Nama Barang </label>
            <select name="barang_id" class="form-control select2" required>
              <option value="">-- Pilih Barang --</option>
              <?php while ($barang = mysqli_fetch_assoc($barang_list)): ?>
                <option value="<?= $barang['barang_id'] ?>"> <?= htmlspecialchars($barang['nama_barang']) ?> </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Tanggal Lapor</label>
            <input type="date" class="form-control" name="tanggal_lapor" value="<?= date('Y-m-d'); ?>" required>
          </div>
          <div class="form-group">
            <label>Deskripsi Kerusakan</label>
            <textarea class="form-control" rows="3" name="deskripsi_kerusakan" required></textarea>
          </div>
          <div class="form-group">
            <label>Tindakan Perbaikan</label>
            <select class="form-control" name="tindakan_perbaikan" id="tindakan_perbaikan" required onchange="togglePerbaikanFields()">
              <option value="">-- Pilih Tindakan --</option>
              <option value="Service luar">Service luar</option>
              <option value="Service sendiri">Service sendiri</option>
            </select>
          </div>
          <div class="form-group" id="field_tanggal_selesai" style="display:none;">
            <label>Tanggal Selesai</label>
            <input type="date" class="form-control" name="tanggal_selesai">
          </div>
          <div class="form-group" id="field_biaya_perbaikan" style="display:none;">
            <label>Biaya Perbaikan</label>
            <input type="number" step="0.01" class="form-control" name="biaya_perbaikan" value="0">
          </div>
          <div class="form-group" id="field_teknisi" style="display:none;">
            <label>Nama Teknisi</label>
            <input type="text" class="form-control" name="teknisi">
          </div>
          <div class="form-group">
            <label>Keterangan</label>
            <textarea class="form-control" rows="2" name="keterangan"></textarea>
          </div>
        </div>
        <div class="card-footer">
          <a class="btn btn-app bg-warning float-left" href="dashboard_staff.php?unit=perbaikan">
            <i class="fas fa-reply"></i> Back
          </a>
          <button class="btn btn-app bg-success float-right" type="submit">
            <i class="fas fa-save"></i> SAVE
          </button>
        </div>
      </form>
      <script>
      function togglePerbaikanFields() {
        var tindakan = document.getElementById('tindakan_perbaikan').value;
        document.getElementById('field_tanggal_selesai').style.display = (tindakan === 'Service luar') ? 'block' : 'none';
        document.getElementById('field_biaya_perbaikan').style.display = (tindakan === 'Service luar') ? 'block' : 'none';
        document.getElementById('field_teknisi').style.display = (tindakan === 'Service sendiri') ? 'block' : 'none';
      }
      </script>
    </div>
  </div>
</section>
