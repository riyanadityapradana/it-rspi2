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
                        <td><?= htmlspecialchars($row['penyerahan'] ?? '-'); ?></td>
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

<script>
function setPenyerahanData(kodeBarang, namaBarang, idPengajuan) {
    document.getElementById('kodeBarang').value = kodeBarang;
    document.getElementById('namaBarang').value = namaBarang;
    document.getElementById('idPengajuan').value = idPengajuan;
}

function setRusakData(kodeBarang, namaBarang) {
    document.getElementById('kodeBarangRusak').value = kodeBarang;
    document.getElementById('namaBarangRusak').value = namaBarang;
}

function setBaikData(kodeBarang, namaBarang) {
    document.getElementById('kodeBarangBaik').value = kodeBarang;
    document.getElementById('namaBarangBaik').value = namaBarang;
}
</script>
