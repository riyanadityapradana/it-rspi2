<?php
// Cek jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $barang_id            = $_POST['barang_id'];
  $penyerahan_id        = $_POST['penyerahan_id'];
  $tanggal_lapor        = $_POST['tanggal_lapor'];
  $deskripsi_kerusakan  = $_POST['deskripsi_kerusakan'];
  $tindakan_perbaikan   = $_POST['tindakan_perbaikan'];
  $teknisi              = isset($_POST['teknisi']) ? $_POST['teknisi'] : NULL;
  $unit_pelapor         = isset($_POST['unit_pelapor']) ? $_POST['unit_pelapor'] : NULL;
  $keterangan           = $_POST['keterangan'];
  // Validasi barang_id dan penyerahan_id
  if (empty($barang_id) || empty($penyerahan_id)) {
    echo '<div class="alert alert-danger">Barang dan penyerahan harus dipilih!</div>';
  } else {
    // Cek barang_id valid
    $cek_barang = mysqli_query($config, "SELECT barang_id FROM tb_barang WHERE barang_id='$barang_id'");
    if (mysqli_num_rows($cek_barang) == 0) {
      echo '<div class="alert alert-danger">Barang tidak ditemukan di database!</div>';
    } else {
      // Cek penyerahan_id dan ambil lokasi_id sebagai unit_melapor
      $cek_penyerahan = mysqli_query($config, "SELECT lokasi_id FROM tb_penyerahan WHERE penyerahan_id='$penyerahan_id'");
      if (mysqli_num_rows($cek_penyerahan) == 0) {
        echo '<div class="alert alert-danger">Penyerahan tidak ditemukan di database!</div>';
      } else {
        $lokasi = mysqli_fetch_assoc($cek_penyerahan);
        // lokasi_id dari penyerahan sebagai unit default
        $unit_melapor = $lokasi['lokasi_id'];
        // gunakan nilai unit_pelapor dari form jika tersedia (otomatis diisi saat pilih barang)
        if (empty($unit_pelapor)) {
          $unit_pelapor = $unit_melapor;
        }

        // Logika insert sesuai pilihan tindakan_perbaikan
        if ($tindakan_perbaikan == 'Service luar') {
          $teknisi = NULL;
          $query = "INSERT INTO tb_perbaikan_barang (barang_id, penyerahan_id, tanggal_lapor, deskripsi_kerusakan, tindakan_perbaikan, teknisi, status, keterangan, unit_melapor) VALUES ('$barang_id', '$penyerahan_id', '$tanggal_lapor', '$deskripsi_kerusakan', '$tindakan_perbaikan', NULL, 'diajukan', '$keterangan', " . ($unit_pelapor ? "'$unit_pelapor'" : "NULL") . ")";
        } else if ($tindakan_perbaikan == 'Service sendiri') {
          $query = "INSERT INTO tb_perbaikan_barang (barang_id, penyerahan_id, tanggal_lapor, deskripsi_kerusakan, tindakan_perbaikan, teknisi, status, keterangan, unit_melapor) VALUES ('$barang_id', '$penyerahan_id', '$tanggal_lapor', '$deskripsi_kerusakan', '$tindakan_perbaikan', " . ($teknisi ? "'$teknisi'" : "NULL") . ", 'diajukan', '$keterangan', " . ($unit_pelapor ? "'$unit_pelapor'" : "NULL") . ")";
        } else {
          // fallback jika tidak dipilih
          $query = "INSERT INTO tb_perbaikan_barang (barang_id, penyerahan_id, tanggal_lapor, deskripsi_kerusakan, tindakan_perbaikan, status, keterangan, unit_melapor) VALUES ('$barang_id', '$penyerahan_id', '$tanggal_lapor', '$deskripsi_kerusakan', '', 'diajukan', '$keterangan', " . ($unit_pelapor ? "'$unit_pelapor'" : "NULL") . ")";
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
}
// Ambil daftar barang dengan penyerahan untuk modal
$barang_penyerahan_list = mysqli_query($config, "SELECT b.barang_id, b.nama_barang, b.jenis_barang, p.penyerahan_id, p.lokasi_id, l.nama_lokasi, p.kondisi FROM tb_barang b JOIN tb_penyerahan p ON b.barang_id = p.barang_id LEFT JOIN tb_lokasi l ON p.lokasi_id = l.lokasi_id WHERE p.kondisi = 'rusak' ORDER BY b.nama_barang ASC");

// Ambil daftar lokasi untuk unit terkait
$lokasi_list = mysqli_query($config, "SELECT lokasi_id, nama_lokasi FROM tb_lokasi ORDER BY nama_lokasi ASC");
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
            <label>Nama Barang</label>
            <div class="input-group">
              <input type="text" class="form-control" id="nama_barang" readonly required placeholder="Pilih Barang">
              <div class="input-group-append">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalPilihBarang">
                  <i class="fas fa-search"></i> Pilih
                </button>
              </div>
            </div>
            <input type="hidden" name="barang_id" id="barang_id" required>
            <input type="hidden" name="penyerahan_id" id="penyerahan_id" required>
            <div class="form-group">
              <label>Unit Melapor</label>
              <input type="text" class="form-control" id="unit_melapor_display" readonly placeholder="Unit melapor">
              <input type="hidden" name="unit_pelapor" id="unit_pelapor">
            </div>
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
          <div class="form-group" id="field_teknisi" style="display:none;">
            <label>Nama Teknisi</label>
            <input type="text" class="form-control" name="teknisi" value="<?= isset($_SESSION['nama_lengkap']) ? htmlspecialchars($_SESSION['nama_lengkap']) : '' ?>" readonly>
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
      <!-- Modal Pilih Barang -->
      <div class="modal fade" id="modalPilihBarang" tabindex="-1" role="dialog" aria-labelledby="modalPilihBarangLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalPilihBarangLabel">Pilih Barang</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Jenis</th>
                    <th>Lokasi</th>
                    <th>Kondisi</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $no = 1; while ($bp = mysqli_fetch_assoc($barang_penyerahan_list)): ?>
                  <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($bp['nama_barang']) ?></td>
                    <td><?= htmlspecialchars($bp['jenis_barang']) ?></td>
                    <td><?= htmlspecialchars($bp['nama_lokasi'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($bp['kondisi'] ?? '-') ?></td>
                    <td>
                      <button type="button" class="btn btn-success btn-sm" onclick="pilihBarang('<?= addslashes($bp['barang_id']) ?>', '<?= addslashes($bp['penyerahan_id']) ?>', '<?= addslashes($bp['nama_barang']) ?>', '<?= addslashes($bp['lokasi_id'] ?? '') ?>', '<?= addslashes($bp['nama_lokasi'] ?? '-') ?>')">Pilih</button>
                    </td>
                  </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
          </div>
        </div>
      </div>
      <script>
      function togglePerbaikanFields() {
        var tindakan = document.getElementById('tindakan_perbaikan').value;
        document.getElementById('field_teknisi').style.display = (tindakan === 'Service sendiri') ? 'block' : 'none';
      }
      function pilihBarang(barang_id, penyerahan_id, nama_barang, lokasi_id, nama_lokasi) {
        document.getElementById('barang_id').value = barang_id;
        document.getElementById('penyerahan_id').value = penyerahan_id;
        document.getElementById('nama_barang').value = nama_barang;
        // isi unit pelapor (hidden) dan tampilan nama lokasi
        var unitHidden = document.getElementById('unit_pelapor');
        if (unitHidden) unitHidden.value = lokasi_id;
        var unitDisplay = document.getElementById('unit_melapor_display');
        if (unitDisplay) unitDisplay.value = nama_lokasi;
        $('#modalPilihBarang').modal('hide');
      }
      </script>
    </div>
  </div>
</section>
