<?php
require_once("../config/koneksi.php");

// Proses simpan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $barang_id = intval($_POST['barang_id']);
  $penyerahan_id = trim($_POST['penyerahan_id'] ?? '');
  $nama_barang = trim($_POST['nama_barang'] ?? '');
  // unit_pelapor (hidden) berisi lokasi_id dari penyerahan; gunakan itu jika ada
  $unit_melapor = trim($_POST['unit_pelapor'] ?? '');
  $lokasi_tujuan = intval($_POST['lokasi_tujuan']);
  $tanggal_mutasi = trim($_POST['tanggal_mutasi']);
  $keterangan = trim($_POST['keterangan']);
  $id_user = $_SESSION['id_user'] ?? 1;

  // Validasi
  if (empty($barang_id) || empty($nama_barang) || empty($lokasi_tujuan) || empty($tanggal_mutasi)) {
    header('Location: dashboard_staff.php?unit=pemindahan&err=Semua field bertanda * harus diisi!');
    exit;
  }

  // Ambil lokasi asal dari penyerahan (karena penyerahan sudah dipilih)
  if (!empty($penyerahan_id)) {
    $cek_penyerahan = mysqli_query($config, "SELECT lokasi_id FROM tb_penyerahan WHERE penyerahan_id='" . mysqli_real_escape_string($config, $penyerahan_id) . "'");
    if ($cek_penyerahan && mysqli_num_rows($cek_penyerahan) > 0) {
      $lok = mysqli_fetch_assoc($cek_penyerahan);
      $lokasi_asal = $lok['lokasi_id'];
      
      // Jika unit_melapor kosong, gunakan lokasi asal
      if (empty($unit_melapor)) {
        $unit_melapor = $lokasi_asal;
      }
    } else {
      header('Location: dashboard_staff.php?unit=pemindahan&err=Penyerahan tidak ditemukan!');
      exit;
    }
  } else {
    // Jika tidak ada penyerahan_id, ambil dari tb_barang
    $barang_q = mysqli_query($config, "SELECT lokasi_id FROM tb_barang WHERE barang_id='$barang_id'");
    $barang_row = mysqli_fetch_assoc($barang_q);
    if ($barang_row && !empty($barang_row['lokasi_id'])) {
      $lokasi_asal = $barang_row['lokasi_id'];
      if (empty($unit_melapor)) {
        $unit_melapor = $lokasi_asal;
      }
    } else {
      header('Location: dashboard_staff.php?unit=pemindahan&err=Lokasi asal barang tidak ditemukan!');
      exit;
    }
  }

  // Insert ke tb_mutasi_barang
  $keterangan_escaped = mysqli_real_escape_string($config, $keterangan);

  $q = mysqli_query($config, "INSERT INTO tb_mutasi_barang (barang_id, lokasi_asal, lokasi_tujuan, tanggal_mutasi, id_user, keterangan) VALUES (
    '$barang_id', '$lokasi_asal', '$lokasi_tujuan', '$tanggal_mutasi', '$id_user', '$keterangan_escaped')");

  if ($q) {
    // Update lokasi barang di tb_barang
    mysqli_query($config, "UPDATE tb_barang SET lokasi_id='$lokasi_tujuan' WHERE barang_id='$barang_id'");
    
    // Update lokasi barang di tb_penyerahan (ubah lokasi_id ke lokasi_tujuan dengan penyerahan_id yang spesifik)
    if (!empty($penyerahan_id)) {
      mysqli_query($config, "UPDATE tb_penyerahan SET lokasi_id='$lokasi_tujuan' WHERE penyerahan_id='" . mysqli_real_escape_string($config, $penyerahan_id) . "'");
    }
    
    header('Location: dashboard_staff.php?unit=pemindahan&msg=Data mutasi berhasil ditambahkan!');
    exit;
  } else {
    header('Location: dashboard_staff.php?unit=pemindahan&err=Gagal menambah data mutasi!');
    exit;
  }
}

// Ambil daftar barang untuk modal dengan LEFT JOIN ke penyerahan + lokasi (dengan pagination)
$items_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $items_per_page;

// Hitung total barang (yang punya penyerahan atau tidak)
$total_query = mysqli_query($config, "SELECT COUNT(DISTINCT b.barang_id) as total FROM tb_barang b LEFT JOIN tb_penyerahan p ON b.barang_id = p.barang_id");
$total_result = mysqli_fetch_assoc($total_query);
$total_items = $total_result['total'];
$total_pages = ceil($total_items / $items_per_page);

// Ambil daftar barang dengan penyerahan untuk modal
$barang_penyerahan_list = mysqli_query($config, "SELECT b.barang_id, b.nama_barang, b.jenis_barang, p.penyerahan_id, p.lokasi_id, l.nama_lokasi, p.kondisi FROM tb_barang b JOIN tb_penyerahan p ON b.barang_id = p.barang_id LEFT JOIN tb_lokasi l ON p.lokasi_id = l.lokasi_id ORDER BY b.nama_barang ASC LIMIT $offset, $items_per_page");

// Ambil daftar lokasi untuk dropdown tujuan
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
        <h1 class="m-0">Silahkan Input Pemindahan Barang</h1>
      </div>
    </div>
  </div>
</div>
<div class="content">
  <div class="container-fluid">
    <div class="card">
      <div class="card-body">
        <form method="post" id="formPemindahan">
          <div class="form-group">
            <label>Nama Barang <span class="text-danger">*</span></label>
            <div class="input-group">
              <input type="text" name="nama_barang" id="nama_barang" class="form-control" placeholder="Pilih Barang" readonly required>
              <input type="hidden" name="barang_id" id="barang_id" required>
              <div class="input-group-append">
                <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#modalPilihBarang">
                  <i class="fas fa-search"></i> Pilih
                </button>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>Unit Melapor <span class="text-danger">*</span></label>
            <input type="text" id="unit_melapor_display" class="form-control" placeholder="Unit melapor" readonly required>
            <input type="hidden" name="unit_pelapor" id="unit_pelapor">
            <input type="hidden" name="penyerahan_id" id="penyerahan_id">
          </div>

          <div class="form-group">
            <label>Tanggal Mutasi <span class="text-danger">*</span></label>
            <input type="date" name="tanggal_mutasi" class="form-control" value="<?= date('Y-m-d') ?>" required>
          </div>

          <div class="form-group">
            <label>Lokasi Tujuan <span class="text-danger">*</span></label>
            <select name="lokasi_tujuan" class="form-control select2" style="width: 100%;" required>
              <option value="">-- Pilih Lokasi --</option>
              <?php 
                $lokasi_list = mysqli_query($config, "SELECT lokasi_id, nama_lokasi FROM tb_lokasi ORDER BY nama_lokasi ASC");
                while ($lokasi = mysqli_fetch_assoc($lokasi_list)): ?>
                <option value="<?= $lokasi['lokasi_id'] ?>"><?= htmlspecialchars($lokasi['nama_lokasi']) ?></option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="3" placeholder="Keterangan tambahan"></textarea>
          </div>

          <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Simpan
          </button>
          <a href="dashboard_staff.php?unit=pemindahan" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
          </a>
        </form>
      </div>
    </div>
  </div>
</div>

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
        <div class="form-group">
          <input type="text" id="searchBarang" class="form-control" placeholder="Cari barang..." onkeyup="filterTable()">
        </div>
        <table class="table table-bordered" id="tabelBarang">
          <thead style="background:rgb(129, 2, 0, 1)">
            <tr>
              <th style="color: white;">No</th>
              <th style="color: white;">Nama Barang</th>
              <th style="color: white;">Jenis</th>
              <th style="color: white;">Lokasi</th>
              <th style="color: white;">Kondisi</th>
              <th style="color: white;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $no = 1; if ($barang_penyerahan_list && mysqli_num_rows($barang_penyerahan_list) > 0): while ($bp = mysqli_fetch_assoc($barang_penyerahan_list)): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($bp['nama_barang']) ?></td>
              <td><?= htmlspecialchars($bp['jenis_barang'] ?? '-') ?></td>
              <td><?= htmlspecialchars($bp['nama_lokasi'] ?? '-') ?></td>
              <td><?= htmlspecialchars($bp['kondisi'] ?? '-') ?></td>
              <td>
                <button type="button" class="btn btn-success btn-sm" onclick="pilihBarang('<?= addslashes($bp['barang_id']) ?>', '<?= addslashes($bp['penyerahan_id'] ?? '') ?>', '<?= addslashes($bp['nama_barang']) ?>', '<?= addslashes($bp['lokasi_id'] ?? '') ?>', '<?= addslashes($bp['nama_lokasi'] ?? '-') ?>')">Pilih</button>
              </td>
            </tr>
            <?php endwhile; else: ?>
            <tr><td colspan="6" class="text-center">Tidak ada data barang</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
        <div class="modal-footer">
          <div class="mr-auto">
            <?php 
              $start = ($page - 1) * $items_per_page + 1;
              $end = min($page * $items_per_page, $total_items);
              echo "Showing " . $start . " to " . $end . " of " . $total_items . " entries";
            ?>
          </div>
          <div>
            <?php if ($page > 1): ?>
              <button type="button" class="btn btn-secondary" onclick="goToPage(<?= $page - 1 ?>)">Previous</button>
            <?php endif; ?>
            <?php if ($page < $total_pages): ?>
              <button type="button" class="btn btn-secondary" onclick="goToPage(<?= $page + 1 ?>)">Next</button>
            <?php endif; ?>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  function pilihBarang(barang_id, penyerahan_id, nama_barang, lokasi_id, nama_lokasi) {
    document.getElementById('barang_id').value = barang_id;
    document.getElementById('nama_barang').value = nama_barang;
    // set penyerahan id and unit (lokasi) hidden/display
    var penBox = document.getElementById('penyerahan_id');
    if (penBox) penBox.value = penyerahan_id;
    var unitHidden = document.getElementById('unit_pelapor');
    if (unitHidden) unitHidden.value = lokasi_id;
    var unitDisplay = document.getElementById('unit_melapor_display');
    if (unitDisplay) unitDisplay.value = nama_lokasi;
    $('#modalPilihBarang').modal('hide');
  }

  function filterTable() {
    var searchValue = document.getElementById('searchBarang').value.toLowerCase();
    var table = document.getElementById('tabelBarang');
    var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (var i = 0; i < rows.length; i++) {
      var row = rows[i];
      var text = row.textContent.toLowerCase();
      row.style.display = text.includes(searchValue) ? '' : 'none';
    }
  }
  function goToPage(pageNum) {
    var url = new URL(window.location);
    url.searchParams.set('page', pageNum);
    window.location = url.toString();
  }
</script>
