<?php
require_once("../config/koneksi.php");
?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>MASTER DATA BARANG</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="dashboard_staff.php?unit=beranda">Home</a></li>
          <li class="breadcrumb-item active">Barang</li>
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
            <a href="?unit=create_barang" class="btn btn-tool btn-sm" style="background:rgba(0, 123, 255, 1)">
              <i class="fas fa-plus-square" style="color: white;"> Tambah Data</i>
            </a>
          </div>
			    <div class="card-tools" style="float: right; text-align: right;">
            <select id="filterJenisBarang" class="form-control form-control-sm" style="display: inline-block; width: auto; margin-right: 10px;">
              <option value="">Semua Jenis Barang</option>
              <option value="Komputer & Laptop">Komputer & Laptop</option>
              <option value="Komponen Komputer & Laptop">Komponen Komputer & Laptop</option>
              <option value="Printer & Scanner">Printer & Scanner</option>
              <option value="Komponen Printer & Scanner">Komponen Printer & Scanner</option>
              <option value="Komponen Network">Komponen Network</option>
            </select>
            <select id="filterStatusBarang" class="form-control form-control-sm" style="display: inline-block; width: auto; margin-right: 10px;">
              <option value="">Semua Status</option>
              <option value="Baik">Baik</option>
              <option value="Rusak">Rusak</option>
            </select>
            <a href="#" class="btn btn-tool btn-sm" data-card-widget="collapse" style="background:rgba(69, 77, 85, 1)">
              <i class="fas fa-bars"></i>
            </a>
            <button type="button" class="btn btn-tool btn-sm" style="background:rgba(40, 167, 69, 1); margin-left: 8px;" data-toggle="modal" data-target="#modalPrint">
              <i class="fas fa-print" style="color: white;"> Print</i>
            </button>
          </div>
			</div>
            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                <thead style="background:rgb(129, 2, 0, 1); color:white;">
                    <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Jenis Barang</th>
                    <th>Penyerahan</th>
                    <th>Status Barang</th>
                    <th>Status Kerusakan</th>
                    <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $q = mysqli_query($config, "SELECT b.*, 
                        CASE 
                            WHEN p.status = 'Disetujui' THEN 'Disetujui'
                            ELSE 'Tidak Ada Pengajuan'
                        END as status_pengajuan,
                        p.id_pengajuan
                        FROM tb_barang b 
                        LEFT JOIN (
                            SELECT pb1.kode_barang, pb1.status, pb1.id_pengajuan 
                            FROM tb_pengajuan_barang pb1
                            INNER JOIN (
                                SELECT kode_barang, MAX(id_pengajuan) as max_id
                                FROM tb_pengajuan_barang
                                GROUP BY kode_barang
                            ) pb2 ON pb1.kode_barang = pb2.kode_barang AND pb1.id_pengajuan = pb2.max_id
                            WHERE pb1.status = 'Disetujui' AND pb1.status != 'Selesai'
                        ) p ON b.kode_barang = p.kode_barang 
                        ORDER BY b.kode_barang ASC");
                    while ($row = mysqli_fetch_assoc($q)) : ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                        <td><?= htmlspecialchars($row['jenis_barang']); ?></td>
                        <td><?= htmlspecialchars($row['penyerahan']); ?></td>
                        <td class="text-center">
                            <?php if (!empty($row['stts_brg'])): ?>
                                <span class="badge badge-<?= $row['stts_brg'] == 'Baik' ? 'success' : 'danger' ?>">
                                    <?= htmlspecialchars($row['stts_brg']); ?>
                                </span>
                            <?php else: ?>
                                <span class="badge badge-secondary">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if (!empty($row['status_perbaikan'])): ?>
                                <?php if ($row['status_perbaikan'] == 'Belum Ada Perbaikan'): ?>
                                    <span class="badge badge-secondary">Belum Ada Perbaikan</span>
                                <?php else: ?>
                                    <span class="badge badge-<?= $row['status_perbaikan'] == 'Dapat Diperbaiki' ? 'warning' : 'danger' ?>">
                                        <?= htmlspecialchars($row['status_perbaikan']); ?>
                                    </span>
                                <?php endif; ?>
                                <?php if (!empty($row['keterangan_rusak'])): ?>
                                    <br><small class="text-muted"><?= htmlspecialchars($row['keterangan_rusak']); ?></small>
                                <?php endif; ?>
                            <?php else: ?>
                                Ket :<span class="badge badge-secondary">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                        <a href="dashboard_staff.php?unit=update_barang&id=<?= urlencode($row['kode_barang']); ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Edit</a>
                        <a href="dashboard_staff.php?unit=delete_barang&id=<?= urlencode($row['kode_barang']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus barang ini?')"><i class="fa fa-trash"></i> Hapus</a>
                        
                        <?php if ($row['status_pengajuan'] == 'Disetujui' && empty($row['penyerahan'])): ?>
                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalPenyerahan" 
                                    onclick="setPenyerahanData('<?= htmlspecialchars($row['kode_barang']); ?>', '<?= htmlspecialchars($row['nama_barang']); ?>', '<?= $row['id_pengajuan']; ?>')">
                                <i class="fa fa-handshake"></i> Input Penyerahan
                            </button>
                        <?php endif; ?>
                        
                        <?php if (isset($row['stts_brg']) && $row['stts_brg'] == 'Baik'): ?>
                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalRusak"
                                onclick="setRusakData('<?= htmlspecialchars($row['kode_barang']); ?>', '<?= htmlspecialchars($row['nama_barang']); ?>')">
                                <i class="fa fa-exclamation-triangle"></i> Input Kerusakan
                            </button>
                        <?php endif; ?>
                        <?php if (isset($row['status_perbaikan']) && $row['status_perbaikan'] == 'Tidak Dapat Diperbaiki'): ?>
                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalBaik" 
                                    onclick="setBaikData('<?= htmlspecialchars($row['kode_barang']); ?>', '<?= htmlspecialchars($row['nama_barang']); ?>')">
                                <i class="fa fa-check"></i> Set Baik
                            </button>
                        <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Modal Input Penyerahan -->
<div class="modal fade" id="modalPenyerahan" tabindex="-1" role="dialog" aria-labelledby="modalPenyerahanLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalPenyerahanLabel">Input Penyerahan Barang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="formPenyerahan" method="POST" action="dashboard_staff.php?unit=proses_penyerahan">
        <div class="modal-body">
          <div class="form-group">
            <label>Kode Barang:</label>
            <input type="text" class="form-control" id="kodeBarang" name="kode_barang" readonly>
          </div>
          <div class="form-group">
            <label>Nama Barang:</label>
            <input type="text" class="form-control" id="namaBarang" readonly>
          </div>
          <div class="form-group">
            <label>Penyerahan ke Unit:</label>
            <input type="text" class="form-control" name="penyerahan" placeholder="Contoh: Manajemen, Keuangan, SDM, dll." required>
          </div>
          <div class="form-group">
            <label>Tanggal Penyerahan:</label>
            <input type="date" class="form-control" id="tglPenyerahan" name="tgl_penyerahan" readonly>
          </div>
          <input type="hidden" name="id_pengajuan" id="idPengajuan">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Simpan Penyerahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Set Baik -->
<div class="modal fade" id="modalBaik" tabindex="-1" role="dialog" aria-labelledby="modalBaikLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalBaikLabel">Set Status Barang Baik</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="formBaik" method="POST" action="dashboard_staff.php?unit=proses_baik">
        <div class="modal-body">
          <div class="form-group">
            <label>Kode Barang:</label>
            <input type="text" class="form-control" id="kodeBarangBaik" name="kode_barang" readonly>
          </div>
          <div class="form-group">
            <label>Nama Barang:</label>
            <input type="text" class="form-control" id="namaBarangBaik" readonly>
          </div>
          <div class="form-group">
            <label>Keterangan Perbaikan:</label>
            <textarea class="form-control" name="keterangan_perbaikan" rows="3" placeholder="Jelaskan perbaikan yang telah dilakukan..." required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Set Baik</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Set Rusak -->
<div class="modal fade" id="modalRusak" tabindex="-1" role="dialog" aria-labelledby="modalRusakLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalRusakLabel">Input Data Kerusakan Barang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="formRusak" method="POST" action="dashboard_staff.php?unit=proses_rusak">
        <div class="modal-body">
          <div class="form-group">
            <label>Kode Barang:</label>
            <input type="text" class="form-control" id="kodeBarangRusak" name="kode_barang" readonly>
          </div>
          <div class="form-group">
            <label>Nama Barang:</label>
            <input type="text" class="form-control" id="namaBarangRusak" readonly>
          </div>
          <div class="form-group">
            <label>Keterangan Kerusakan:</label>
            <textarea class="form-control" name="keterangan_rusak" rows="3" placeholder="Jelaskan kerusakan yang terjadi..." required></textarea>
          </div>
          <div class="form-group">
            <label>Status Perbaikan:</label>
            <select class="form-control" name="status_perbaikan" required>
              <option value="">Pilih Status</option>
              <option value="Dapat Diperbaiki">Dapat Diperbaiki</option>
              <option value="Tidak Dapat Diperbaiki">Tidak Dapat Diperbaiki</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Simpan Data</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Print -->
<div class="modal fade" id="modalPrint" tabindex="-1" role="dialog" aria-labelledby="modalPrintLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
         <form id="formPrint" method="get" target="_blank" action="unit/barang/print_barang.php">
      <div class="modal-content">
        <div class="modal-header">
                     <h5 class="modal-title" id="modalPrintLabel">Cetak Laporan Data Barang</h5>
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
               <option value="status">Cetak Berdasarkan Status Barang</option>
               <option value="jenis_status">Cetak Berdasarkan Jenis & Status Barang</option>
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
           <div class="form-group" id="statusBarangGroup" style="display:none;">
             <label>Status Barang</label>
             <select class="form-control" name="status_barang" id="status_barang">
               <option value="">-- Pilih Status Barang --</option>
               <option value="Baik">Baik</option>
               <option value="Rusak">Rusak</option>
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

<script>
function setPenyerahanData(kodeBarang, namaBarang, idPengajuan) {
    document.getElementById('kodeBarang').value = kodeBarang;
    document.getElementById('namaBarang').value = namaBarang;
    document.getElementById('idPengajuan').value = idPengajuan;
    document.getElementById('tglPenyerahan').value = new Date().toISOString().slice(0, 10); // Set tanggal hari ini
}

function setRusakData(kodeBarang, namaBarang) {
    document.getElementById('kodeBarangRusak').value = kodeBarang;
    document.getElementById('namaBarangRusak').value = namaBarang;
}

function setBaikData(kodeBarang, namaBarang) {
    document.getElementById('kodeBarangBaik').value = kodeBarang;
    document.getElementById('namaBarangBaik').value = namaBarang;
}

document.getElementById('printType').addEventListener('change', function() {
  const jenisGroup = document.getElementById('jenisBarangGroup');
  const statusGroup = document.getElementById('statusBarangGroup');
  
  // Sembunyikan semua group terlebih dahulu
  jenisGroup.style.display = 'none';
  statusGroup.style.display = 'none';
  
  // Tampilkan group sesuai pilihan
  if (this.value === 'jenis' || this.value === 'jenis_status') {
    jenisGroup.style.display = '';
  }
  if (this.value === 'status' || this.value === 'jenis_status') {
    statusGroup.style.display = '';
  }
});

// Filter berdasarkan jenis barang dan status barang
document.addEventListener('DOMContentLoaded', function() {
    const filterJenisSelect = document.getElementById('filterJenisBarang');
    const filterStatusSelect = document.getElementById('filterStatusBarang');
    const table = document.getElementById('example1');
    const tbody = table.querySelector('tbody');
    const rows = tbody.querySelectorAll('tr');

    function filterTable() {
        const selectedJenis = filterJenisSelect.value;
        const selectedStatus = filterStatusSelect.value;
        
        rows.forEach(function(row) {
            const jenisBarangCell = row.querySelector('td:nth-child(3)'); // Kolom ke-3 adalah Jenis Barang
            const statusBarangCell = row.querySelector('td:nth-child(5)'); // Kolom ke-5 adalah Status Barang
            
            if (jenisBarangCell && statusBarangCell) {
                const jenisBarang = jenisBarangCell.textContent.trim();
                const statusBarang = statusBarangCell.textContent.trim();
                
                // Filter berdasarkan jenis barang
                const jenisMatch = selectedJenis === '' || jenisBarang === selectedJenis;
                
                // Filter berdasarkan status barang
                const statusMatch = selectedStatus === '' || statusBarang === selectedStatus;
                
                // Tampilkan baris jika kedua filter cocok
                if (jenisMatch && statusMatch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    }

    // Event listener untuk filter jenis barang
    filterJenisSelect.addEventListener('change', filterTable);
    
    // Event listener untuk filter status barang
    filterStatusSelect.addEventListener('change', filterTable);
});
</script>
