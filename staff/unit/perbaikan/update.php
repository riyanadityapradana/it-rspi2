<?php
$query = mysqli_query($config, "SELECT * FROM tb_perbaikan_barang WHERE perbaikan_id = '$_GET[id]'");
$r = mysqli_fetch_array($query);
// Ambil daftar barang rusak
$barang_list = mysqli_query($config, "SELECT barang_id, nama_barang FROM tb_barang WHERE kondisi = 'rusak' ORDER BY nama_barang ASC");

// Cek jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id                  = $_POST['id'];
  $barang_id           = $_POST['barang_id'];
  $tanggal_lapor       = $_POST['tanggal_lapor'];
  $deskripsi_kerusakan = $_POST['deskripsi_kerusakan'];
  $tindakan_perbaikan  = $_POST['tindakan_perbaikan'];
  $tanggal_selesai     = isset($_POST['tanggal_selesai']) ? $_POST['tanggal_selesai'] : NULL;
  $biaya_perbaikan     = isset($_POST['biaya_perbaikan']) ? $_POST['biaya_perbaikan'] : NULL;
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
        $edit = "UPDATE tb_perbaikan_barang SET barang_id='$barang_id', tanggal_lapor='$tanggal_lapor', deskripsi_kerusakan='$deskripsi_kerusakan', tindakan_perbaikan='$tindakan_perbaikan', tanggal_selesai=" . ($tanggal_selesai ? "'$tanggal_selesai'" : "NULL") . ", biaya_perbaikan=" . ($biaya_perbaikan ? "'$biaya_perbaikan'" : "NULL") . ", teknisi=NULL, status='diajukan', keterangan='$keterangan' WHERE perbaikan_id='$id'";
      } else if ($tindakan_perbaikan == 'Service sendiri') {
        $tanggal_selesai = NULL;
        $biaya_perbaikan = NULL;
        $edit = "UPDATE tb_perbaikan_barang SET barang_id='$barang_id', tanggal_lapor='$tanggal_lapor', deskripsi_kerusakan='$deskripsi_kerusakan', tindakan_perbaikan='$tindakan_perbaikan', tanggal_selesai=NULL, biaya_perbaikan=NULL, teknisi=" . ($teknisi ? "'$teknisi'" : "NULL") . ", status='diajukan', keterangan='$keterangan' WHERE perbaikan_id='$id'";
      } else {
        $edit = "UPDATE tb_perbaikan_barang SET barang_id='$barang_id', tanggal_lapor='$tanggal_lapor', deskripsi_kerusakan='$deskripsi_kerusakan', tindakan_perbaikan='', status='diajukan', keterangan='$keterangan' WHERE perbaikan_id='$id'";
      }
      $query = mysqli_query($config, $edit);
      if ($query) {
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
            <select name="barang_id" class="form-control select2" required>
              <option value="">-- Pilih Barang --</option>
              <?php while ($barang = mysqli_fetch_assoc($barang_list)): ?>
                <option value="<?= $barang['barang_id'] ?>" <?= ($barang['barang_id'] == $r['barang_id']) ? 'selected' : '' ?>> <?= htmlspecialchars($barang['nama_barang']) ?> </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Tanggal Lapor</label>
            <input type="date" class="form-control" name="tanggal_lapor" value="<?php echo $r['tanggal_lapor'] ?>" required>
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
          <div class="form-group" id="field_tanggal_selesai" style="display:<?= ($r['tindakan_perbaikan']=='Service luar') ? 'block' : 'none' ?>;">
            <label>Tanggal Selesai</label>
            <input type="date" class="form-control" name="tanggal_selesai" value="<?php echo $r['tanggal_selesai'] ?>">
          </div>
          <div class="form-group" id="field_biaya_perbaikan" style="display:<?= ($r['tindakan_perbaikan']=='Service luar') ? 'block' : 'none' ?>;">
            <label>Biaya Perbaikan</label>
            <input type="number" step="0.01" class="form-control" name="biaya_perbaikan" value="<?php echo $r['biaya_perbaikan'] ?>">
          </div>
          <div class="form-group" id="field_teknisi" style="display:<?= ($r['tindakan_perbaikan']=='Service sendiri') ? 'block' : 'none' ?>;">
            <label>Nama Teknisi</label>
            <input type="text" class="form-control" name="teknisi" value="<?php echo $r['teknisi'] ?>">
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
        document.getElementById('field_biaya_perbaikan').style.display = (tindakan === 'Service luar') ? 'block' : 'none';
        document.getElementById('field_teknisi').style.display = (tindakan === 'Service sendiri') ? 'block' : 'none';
      }
      </script>
    </div>
  </div>
</section>
