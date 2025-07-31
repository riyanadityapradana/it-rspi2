<?php
require_once("../config/koneksi.php");
?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>DATA PEMINDAHAN BARANG</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="dashboard_staff.php?unit=beranda">Home</a></li>
          <li class="breadcrumb-item active">Pemindahan Barang</li>
        </ol>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
      <div class="card">
          <div class="card-header">
          <div class="card-tools" style="float: left; text-align: left;">
  <a href="?unit=create_pemindahan" class="btn btn-tool btn-sm" style="background:rgba(0, 123, 255, 1)">
    <i class="fas fa-plus-square" style="color: white;"> Tambah Pemindahan</i>
  </a>
  <button type="button" class="btn btn-tool btn-sm" style="background:rgba(40, 167, 69, 1); margin-left: 8px;" data-toggle="modal" data-target="#modalPrint">
  <i class="fas fa-print" style="color: white;"> Print</i>
</button>
</div>
            <div class="card-tools" style="float: right; text-align: right;">
              <a href="#" class="btn btn-tool btn-sm" data-card-widget="collapse" style="background:rgba(69, 77, 85, 1)">
                    <i class="fas fa-bars"></i>
              </a>
            </div>
          </div>
            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                <thead style="background:rgb(129, 2, 0, 1); color:white;">
                    <tr>
                    <th>No</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Tanggal Pemindahan</th>
                    <th>Ke Unit</th>
                    <th>Alasan Pemindahan</th>
                    <th>Staff</th>
                    <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $q = mysqli_query($config, "SELECT pm.*, b.nama_barang, b.jenis_barang, b.penyerahan, u.nama_lengkap as nama_staff 
                        FROM tb_pemindahan_barang pm 
                        LEFT JOIN tb_barang b ON pm.kode_barang = b.kode_barang 
                        LEFT JOIN tb_user u ON pm.id_user = u.id_user 
                        ORDER BY pm.tanggal_pemindahan DESC");
                    while ($row = mysqli_fetch_assoc($q)) : ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['kode_barang']); ?></td>
                        <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                        <td><?= date('d/m/Y', strtotime($row['tanggal_pemindahan'])); ?></td>
                        <td><?= htmlspecialchars($row['ke_unit']); ?></td>
                        <td><?= htmlspecialchars($row['alasan_pemindahan']); ?></td>
                        <td><?= htmlspecialchars($row['nama_staff']); ?></td>
                        <td>
                        <a href="dashboard_staff.php?unit=update_pemindahan&id=<?= urlencode($row['id_pemindahan']); ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Edit</a>
                        <a href="dashboard_staff.php?unit=delete_pemindahan&id=<?= urlencode($row['id_pemindahan']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data pemindahan ini?')"><i class="fa fa-trash"></i> Hapus</a>
                        <a href="?unit=detail_pemindahan&id=<?= $row['id_pemindahan'] ?>" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> Detail</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<!-- Modal Print -->
<div class="modal fade" id="modalPrint" tabindex="-1" role="dialog" aria-labelledby="modalPrintLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formPrint" method="get" target="_blank" action="unit/pemindahan/print_pemindahan.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalPrintLabel">Cetak Laporan Pemindahan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Pilihan Cetak</label>
            <select class="form-control" id="printType" name="printType" required>
              <option value="all">Cetak Semua</option>
              <option value="jenis">Cetak Berdasarkan Jenis Barang</option>
            </select>
          </div>
          <div class="form-group" id="jenisBarangGroup" style="display:none;">
            <label>Jenis Barang</label>
            <select class="form-control" name="jenis_barang" id="jenis_barang">
              <option value="">-- Pilih Jenis Barang --</option>
              <!-- Isi dengan data dari database -->
              <option value="Komputer & Laptop">Komputer & Laptop</option>
              <option value="Komponen Komputer & Laptop">Komponen Komputer & Laptop</option>
              <option value="Printer & Scanner">Printer & Scanner</option>
              <option value="Komponen Printer & Scanner">Komponen Printer & Scanner</option>
              <option value="Komponen Network">Komponen Network</option>
            </select>
          </div>
          <div class="form-group">
            <label>Bulan</label>
            <select class="form-control" name="bulan" required>
              <?php for($i=1;$i<=12;$i++): ?>
                <option value="<?= $i ?>"><?= date('F', mktime(0,0,0,$i,10)) ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Tahun</label>
            <select class="form-control" name="tahun" required>
              <?php for($y=date('Y')-5;$y<=date('Y');$y++): ?>
                <option value="<?= $y ?>"><?= $y ?></option>
              <?php endfor; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success"><i class="fas fa-print"></i> Cetak</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- FontAwesome for print icon -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
<!-- jQuery CDN agar $ terdefinisi -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.getElementById('printType').addEventListener('change', function() {
  document.getElementById('jenisBarangGroup').style.display = (this.value === 'jenis') ? '' : 'none';
});
</script>
