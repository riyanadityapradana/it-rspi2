<?php
$query = mysqli_query($config, "SELECT * FROM tb_perbaikan_barang WHERE perbaikan_id = '$_GET[id]'");
$r = mysqli_fetch_array($query);
// Ambil daftar barang rusak
$barang_list = mysqli_query($config, "SELECT barang_id, nama_barang FROM tb_barang WHERE kondisi = 'rusak' ORDER BY nama_barang ASC");

// Ambil nama barang saat ini dan nama unit melapor
$current_barang = mysqli_fetch_assoc(mysqli_query($config, "SELECT nama_barang FROM tb_barang WHERE barang_id='" . $r['barang_id'] . "'"));
$current_unit = null;
if (!empty($r['unit_melapor'])) {
  $unit_q = mysqli_query($config, "SELECT nama_lokasi FROM tb_lokasi WHERE lokasi_id='" . $r['unit_melapor'] . "'");
  if ($unit_q && mysqli_num_rows($unit_q) > 0) {
    $unit_row = mysqli_fetch_assoc($unit_q);
    $current_unit = $unit_row['nama_lokasi'];
  }
}

// Cek jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id                  = $_POST['id'];
  $barang_id           = $_POST['barang_id'];
  $tanggal_lapor       = $_POST['tanggal_lapor'];
  $deskripsi_kerusakan = $_POST['deskripsi_kerusakan'];
  $tindakan_perbaikan  = $_POST['tindakan_perbaikan'];
  $tanggal_selesai     = isset($_POST['tanggal_selesai']) ? $_POST['tanggal_selesai'] : NULL;
  $teknisi             = isset($_POST['teknisi']) ? $_POST['teknisi'] : NULL;
  $keterangan          = $_POST['keterangan'];

  // Validasi barang_id
  if (empty($barang_id)) {
    echo '<div class="alert alert-danger">Barang harus dipilih!</div>';
  } else {
    $cek_barang = mysqli_query($config, "SELECT barang_id FROM tb_barang WHERE barang_id='$barang_id'");
    if (mysqli_num_rows($cek_barang) == 0) {
      echo '<div class="alert alert-danger">Barang tidak ditemukan di database!</div>';
    } else {
      // Logika update sesuai tindakan_perbaikan
      if ($tindakan_perbaikan == 'Service luar') {
        $teknisi = NULL;
        $edit = "UPDATE tb_perbaikan_barang SET barang_id='$barang_id', tanggal_lapor='$tanggal_lapor', deskripsi_kerusakan='$deskripsi_kerusakan', tindakan_perbaikan='$tindakan_perbaikan', tanggal_selesai=" . ($tanggal_selesai ? "'$tanggal_selesai'" : "NULL") . ", teknisi=NULL, status='diajukan', keterangan='$keterangan' WHERE perbaikan_id='$id'";
      } else if ($tindakan_perbaikan == 'Service sendiri') {
        $edit = "UPDATE tb_perbaikan_barang SET barang_id='$barang_id', tanggal_lapor='$tanggal_lapor', deskripsi_kerusakan='$deskripsi_kerusakan', tindakan_perbaikan='$tindakan_perbaikan', tanggal_selesai=" . ($tanggal_selesai ? "'$tanggal_selesai'" : "NULL") . ", teknisi=" . ($teknisi ? "'$teknisi'" : "NULL") . ", status='diajukan', keterangan='$keterangan' WHERE perbaikan_id='$id'";
      } else {
        $edit = "UPDATE tb_perbaikan_barang SET barang_id='$barang_id', tanggal_lapor='$tanggal_lapor', deskripsi_kerusakan='$deskripsi_kerusakan', tindakan_perbaikan='', tanggal_selesai=" . ($tanggal_selesai ? "'$tanggal_selesai'" : "NULL") . ", status='diajukan', keterangan='$keterangan' WHERE perbaikan_id='$id'";
      }
      $query = mysqli_query($config, $edit);
      if ($query) {
        // Update kondisi penyerahan menjadi 'dalam perbaikan'
        $update_penyerahan = "UPDATE tb_penyerahan SET kondisi = 'dalam perbaikan' WHERE penyerahan_id = '" . $r['penyerahan_id'] . "'";
        mysqli_query($config, $update_penyerahan);
        header('Location: dashboard_staff.php?unit=perbaikan&msg=Data perbaikan berhasil diupdate!');
        exit;
      } else {
        header('Location: dashboard_staff.php?unit=perbaikan&err=Gagal menyimpan data dalam database!');
        exit;
      }
    }
  }
}
?>
<!-- HTML Form -->
<section class="content-header">
  <!-- [Header sama seperti punyamu] -->
</section>
<section class="content">
  <div class="container-fluid">
    <div class="card card-default">
      <div class="card-header">
        <h3 class="card-title">Silahkan Ubah Data Perbaikan Barang</h3>
      </div>
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $r['perbaikan_id'] ?>">
        <div class="card-body">
          <div class="form-group">
            <label>Nama Barang </label>
            <div class="input-group">
              <input type="text" class="form-control" id="nama_barang" readonly value="<?= htmlspecialchars($current_barang['nama_barang'] ?? '-') ?>">
            </div>
            <input type="hidden" name="barang_id" id="barang_id" value="<?= htmlspecialchars($r['barang_id']) ?>" required>
            <input type="hidden" name="penyerahan_id" id="penyerahan_id" value="<?= htmlspecialchars($r['penyerahan_id'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Tanggal Lapor</label>
            <input type="datetime-local" class="form-control" name="tanggal_lapor" value="<?php echo str_replace(' ', 'T', $r['tanggal_lapor']); ?>" required>
          </div>
          <div class="form-group">
            <label>Deskripsi Kerusakan</label>
            <textarea class="form-control" rows="3" name="deskripsi_kerusakan" required><?php echo $r['deskripsi_kerusakan'] ?></textarea>
          </div>
          <div class="form-group">
            <label>Tindakan Perbaikan</label>
            <select class="form-control" name="tindakan_perbaikan" id="tindakan_perbaikan" required onchange="togglePerbaikanFields()">
              <option value="">-- Pilih Tindakan --</option>
              <option value="Service luar" <?php if($r['tindakan_perbaikan']=='Service luar') echo 'selected'; ?>>Service luar</option>
              <option value="Service sendiri" <?php if($r['tindakan_perbaikan']=='Service sendiri') echo 'selected'; ?>>Service sendiri</option>
            </select>
          </div>
          <div class="form-group">
            <label>Tanggal Selesai</label>
            <input type="datetime-local" class="form-control" name="tanggal_selesai" value="<?php echo str_replace(' ', 'T', $r['tanggal_selesai']); ?>">
          </div>
          <div class="form-group" id="field_teknisi" style="display:<?= ($r['tindakan_perbaikan']=='Service sendiri') ? 'block' : 'none' ?>;">
            <label>Nama Teknisi</label>
            <input type="text" class="form-control" name="teknisi" value="<?php echo $r['teknisi'] ?>" readonly>
          </div>
          <div class="form-group">
            <label>Unit Melapor</label>
            <input type="text" class="form-control" id="unit_melapor_display" readonly value="<?= htmlspecialchars($current_unit ?? $r['unit_melapor'] ?? '-') ?>">
            <input type="hidden" name="unit_melapor" id="unit_melapor" value="<?= htmlspecialchars($r['unit_melapor'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Keterangan</label>
            <textarea class="form-control" rows="2" name="keterangan"><?php echo $r['keterangan'] ?></textarea>
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
        document.getElementById('field_teknisi').style.display = (tindakan === 'Service sendiri') ? 'block' : 'none';
      }
      </script>
    </div>
  </div>
</section>
