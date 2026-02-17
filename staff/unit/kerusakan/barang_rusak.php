<?php
require_once("../config/koneksi.php");

// Ambil filter dari POST
$unit = $_POST['unit'] ?? '';
$tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
$tanggal_selesai = $_POST['tanggal_selesai'] ?? '';

// Ambil daftar unit (lokasi) untuk pilihan filter
$units = [];
$q_units = mysqli_query($config, "SELECT lokasi_id, nama_lokasi FROM tb_lokasi ORDER BY nama_lokasi ASC");
if ($q_units) {
    while ($u = mysqli_fetch_assoc($q_units)) {
        $units[] = $u;
    }
}

// Build query untuk data kerusakan: kondisi barang = 'rusak' dan perbaikan.status = 'tidak_dapat_diperbaiki'
$where = [];
$where[] = "b.kondisi = 'rusak'";
$where[] = "p.status = 'tidak_dapat_diperbaiki'";

if (!empty($unit)) {
    $unit_esc = mysqli_real_escape_string($config, $unit);
    $where[] = "p.unit_melapor = '{$unit_esc}'";
}

if (!empty($tanggal_mulai)) {
    $tm = mysqli_real_escape_string($config, $tanggal_mulai);
    $where[] = "DATE(p.tanggal_lapor) >= '{$tm}'";
}

if (!empty($tanggal_selesai)) {
    $ts = mysqli_real_escape_string($config, $tanggal_selesai);
    $where[] = "DATE(p.tanggal_lapor) <= '{$ts}'";
}

$where_sql = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "SELECT b.barang_id, b.kode_inventaris, b.nama_barang, b.jenis_barang, b.nomor_seri, b.kondisi, b.lokasi_id, l.nama_lokasi AS lokasi_barang,
               p.perbaikan_id, p.tanggal_lapor, p.unit_melapor, u.nama_lokasi AS unit_melapor_nama, p.deskripsi_kerusakan
        FROM tb_barang b
        JOIN tb_perbaikan_barang p ON p.barang_id = b.barang_id
        LEFT JOIN tb_lokasi l ON b.lokasi_id = l.lokasi_id
        LEFT JOIN tb_lokasi u ON p.unit_melapor = u.lokasi_id
        {$where_sql}
        ORDER BY p.tanggal_lapor DESC";

$res = mysqli_query($config, $sql);
$rows = [];
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        $rows[] = $r;
    }
}

?>
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>DATA BARANG RUSAK</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="dashboard_staff.php?unit=beranda">Home</a></li>
          <li class="breadcrumb-item active">Barang Rusak</li>
        </ol>
      </div>
    </div>
  </div>
</section>

<section class="content">
  <div class="container-fluid">
    <div class="card card-default">
      <div class="card-header">
        <div class="card-tools" style="float: right;">
          <button type="button" class="btn btn-tool btn-sm" style="background:rgba(40, 167, 69, 1); margin-left: 8px;" data-toggle="modal" data-target="#modalPrint">
            <i class="fas fa-print" style="color: white;"> Print</i>
          </button>
        </div>
      </div>
      <div class="card-body">
        <form method="post" class="form-inline mb-3">
          <div class="form-group mr-2">
            <label class="mr-2">Unit</label>
            <select name="unit" class="form-control">
              <option value="">Semua Unit</option>
              <?php foreach ($units as $u): ?>
                <option value="<?= htmlspecialchars($u['lokasi_id']) ?>" <?= ($unit == $u['lokasi_id']) ? 'selected' : '' ?>><?= htmlspecialchars($u['nama_lokasi']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group mr-2">
            <label class="mr-2">Dari</label>
            <input type="date" name="tanggal_mulai" class="form-control" value="<?= htmlspecialchars($tanggal_mulai) ?>">
          </div>
          <div class="form-group mr-2">
            <label class="mr-2">Sampai</label>
            <input type="date" name="tanggal_selesai" class="form-control" value="<?= htmlspecialchars($tanggal_selesai) ?>">
          </div>
          <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <table class="table table-bordered table-striped">
          <thead style="background:rgb(129, 2, 0, 1)">
            <tr>
              <th style="font-size: 14px; color: white;" width="40" responsive>No</th>
              <th style="font-size: 14px; color: white;" width="40" responsive>Tanggal Lapor</th>
              <th style="font-size: 14px; color: white;">Unit Melapor</th>
              <th style="font-size: 14px; color: white;">Nama Barang</th>
              <th style="font-size: 14px; color: white;">Jenis</th>
              <th style="font-size: 14px; color: white;">No. Seri</th>
              <th style="font-size: 14px; color: white;">Deskripsi</th>
            </tr>
          </thead>
          <tbody>
            <?php $n=1; foreach ($rows as $row): ?>
              <tr>
                <td><strong><?= $n++ ?></strong></td>
                <td><?= !empty($row['tanggal_lapor']) ? date('d-m-Y H:i', strtotime($row['tanggal_lapor'])) : '-' ?></td>
                <td><?= htmlspecialchars($row['unit_melapor_nama'] ?? $row['unit_melapor']) ?></td>
                <td><?= htmlspecialchars($row['nama_barang']) ?><br><small style="color:#666;">Kode: <?= htmlspecialchars($row['kode_inventaris'] ?? '-') ?></small></td>
                <td><?= htmlspecialchars($row['jenis_barang']) ?></td>
                <td><?= htmlspecialchars($row['nomor_seri']) ?></td>
                <td><?= htmlspecialchars($row['deskripsi_kerusakan']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<!-- Modal Print -->
<div class="modal fade" id="modalPrint" tabindex="-1" role="dialog" aria-labelledby="modalPrintLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formPrint" method="post" target="_blank" action="unit/kerusakan/print_barang_rusak.php">
      <div class="modal-content">
        <div class="modal-header" style="background: #1976d2; color: white;">
          <h5 class="modal-title" id="modalPrintLabel"><i class="fas fa-print"></i> Cetak Barang Rusak</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Unit</label>
            <select name="unit" class="form-control">
              <option value="">Semua Unit</option>
              <?php foreach ($units as $u): ?>
                <option value="<?= htmlspecialchars($u['lokasi_id']) ?>"><?= htmlspecialchars($u['nama_lokasi']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Tanggal Mulai</label>
            <input type="date" name="tanggal_mulai" class="form-control">
          </div>
          <div class="form-group">
            <label>Tanggal Selesai</label>
            <input type="date" name="tanggal_selesai" class="form-control">
          </div>
        </div>
        <div class="modal-footer" style="background: #e3f2fd;">
          <button type="submit" class="btn btn-success"><i class="fas fa-print"></i> Cetak</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php
// akhir file
?>
