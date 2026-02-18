<?php

// Build dynamic WHERE conditions from filters (tindakan, status, tanggal range)
$conditions = [];
if (!empty($_GET['tindakan'])) {
     // normalize passed tindakan (e.g. service_sendiri) and compare against a normalized
     // version of p.tindakan_perbaikan to avoid mismatches due to spaces/case
     $tindakan_raw = $_GET['tindakan'];
     $tindakan = mysqli_real_escape_string($config, $tindakan_raw);
     // create a normalized value for SQL comparison: lowercase and replace spaces with underscores
     $t_norm = strtolower(str_replace(' ', '_', $tindakan_raw));
     $t_norm = mysqli_real_escape_string($config, $t_norm);
     $conditions[] = "LOWER(REPLACE(p.tindakan_perbaikan, ' ', '_')) = '" . $t_norm . "'";
}
if (!empty($_GET['status'])) {
     $status = mysqli_real_escape_string($config, $_GET['status']);
     $conditions[] = "p.status = '" . $status . "'";
}
if (!empty($_GET['tanggal_mulai'])) {
     $tanggal_mulai = mysqli_real_escape_string($config, $_GET['tanggal_mulai']);
     $conditions[] = "DATE(p.tanggal_lapor) >= '" . $tanggal_mulai . "'";
}
if (!empty($_GET['tanggal_selesai'])) {
     $tanggal_selesai = mysqli_real_escape_string($config, $_GET['tanggal_selesai']);
     $conditions[] = "DATE(p.tanggal_lapor) <= '" . $tanggal_selesai . "'";
}

$where_sql = '';
if (!empty($conditions)) {
     $where_sql = ' WHERE ' . implode(' AND ', $conditions);
}

$query = "SELECT 
             b.kode_inventaris,
             b.nama_barang,
             b.jenis_barang,
             b.nomor_seri,
             b.foto,
             b.ip_address,
             l.nama_lokasi AS lokasi_barang,
             p.perbaikan_id,
             p.deskripsi_kerusakan,
             p.tindakan_perbaikan,
             p.status,
             p.tanggal_lapor,
             p.teknisi,
             p.keterangan,
             p.unit_melapor,
             u.nama_lokasi AS unit_melapor_nama,
             p.tanggal_selesai
FROM tb_perbaikan_barang p
JOIN tb_barang b ON p.barang_id = b.barang_id
LEFT JOIN tb_lokasi l ON b.lokasi_id = l.lokasi_id
LEFT JOIN tb_lokasi u ON p.unit_melapor = u.lokasi_id" . $where_sql . " ORDER BY b.barang_id, p.tanggal_lapor DESC";

$res = mysqli_query($config, $query);

// fetch all rows into array so we can render table and modals safely
$rows = [];
if ($res) {
     while ($r = mysqli_fetch_assoc($res)) {
          $rows[] = $r;
     }
}
$n      = 1;
?>
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>DATA PERBAIKAN BARANG</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="dashboard_staff.php?unit=beranda">Home</a></li>
          <li class="breadcrumb-item active">Perbaikan</li>
        </ol>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
	<div class="container-fluid">
		<div class="card card-default">
			<div class="card-header">
				<div class="card-tools" style="float: left; text-align: left;">
                         <!-- <a href="?unit=create_perbaikan" class="btn btn-tool btn-sm" style="background:rgba(0, 123, 255, 1)">
                              <i class="fas fa-plus-square" style="color: white;"> Tambah Data</i>
                         </a> -->
              	     </div>
                    <div class="card-tools" style="float: right; text-align: right;">
                         <a href="#" class="btn btn-tool btn-sm" data-card-widget="collapse" style="background:rgba(69, 77, 85, 1)">
                              <i class="fas fa-bars"></i>
                         </a>
                         <button type="button" class="btn btn-tool btn-sm" style="background:rgba(40, 167, 69, 1); margin-left: 8px;" data-toggle="modal" data-target="#modalPrint">
                              <i class="fas fa-print" style="color: white;"> Print</i>
                         </button>
                    </div>
			</div>
			<div class="card-body">
                    <div class="mb-3">
                         <form method="get" class="form-inline">
                              <input type="hidden" name="unit" value="perbaikan">
                              <div class="form-group mr-2">
                                   <select name="tindakan" class="form-control">
                                        <option value="">Semua Tindakan</option>
                                        <option value="service_luar" <?php if(!empty($_GET['tindakan']) && $_GET['tindakan']==='service_luar') echo 'selected'; ?>>Service Luar</option>
                                        <option value="service_sendiri" <?php if(!empty($_GET['tindakan']) && $_GET['tindakan']==='service_sendiri') echo 'selected'; ?>>Service Sendiri</option>
                                   </select>
                              </div>
                              <div class="form-group mr-2">
                                   <select name="status" class="form-control">
                                        <option value="">Semua Status</option>
                                        <option value="diajukan" <?php if(!empty($_GET['status']) && $_GET['status']==='diajukan') echo 'selected'; ?>>Diajukan</option>
                                        <option value="proses" <?php if(!empty($_GET['status']) && $_GET['status']==='proses') echo 'selected'; ?>>Proses</option>
                                        <option value="selesai" <?php if(!empty($_GET['status']) && $_GET['status']==='selesai') echo 'selected'; ?>>Selesai</option>
                                        <option value="tidak_dapat_diperbaiki" <?php if(!empty($_GET['status']) && $_GET['status']==='tidak_dapat_diperbaiki') echo 'selected'; ?>>Tidak Dapat Diperbaiki</option>
                                   </select>
                              </div>
                              <div class="form-group mr-2">
                                   <input type="date" name="tanggal_mulai" class="form-control" value="<?php echo !empty($_GET['tanggal_mulai']) ? htmlspecialchars($_GET['tanggal_mulai']) : ''; ?>" placeholder="Tanggal Mulai">
                              </div>
                              <div class="form-group mr-2">
                                   <input type="date" name="tanggal_selesai" class="form-control" value="<?php echo !empty($_GET['tanggal_selesai']) ? htmlspecialchars($_GET['tanggal_selesai']) : ''; ?>" placeholder="Tanggal Selesai">
                              </div>
                              <div class="form-group">
                                   <button type="submit" class="btn btn-primary">Filter</button>
                                   <a href="dashboard_staff.php?unit=perbaikan" class="btn btn-secondary ml-2">Reset</a>
                              </div>
                         </form>
                    </div>
				<table id="example1" class="table table-bordered table-striped">
					<thead style="background:rgb(52, 58, 64, 1)">
						<tr>
							<th style="font-size: 14px; color: white;" width="40" responsive>No</th>
                                   <th style="font-size: 14px; color: white;" width="120" responsive>Tanggal Lapor</th>
                                   <th style="font-size: 14px; color: white;" width="120" responsive>Unit Melapor</th>
							<th style="font-size: 14px; color: white;" width="120" responsive>Nama Barang</th>
                                   <th style="font-size: 14px; color: white;" width="120" responsive>No.Seri/SN</th>
							<th style="font-size: 14px; color: white;" width="120" responsive>Status</th>
                                   <th style="font-size: 14px; color: white;" width="120" responsive>Tindakan</th>
							<th style="font-size: 14px; color: white;" width="120" responsive>Tanggal Selesai</th>
							<th style="font-size: 14px; color: white;" width="200" responsive>Aksi</th>
						</tr>
					</thead>
					<tbody>
                              <?php foreach($rows as $row): ?>
						<tr>
							<td><?= $n++; ?></td>
							<td><?= htmlspecialchars($row['tanggal_lapor']); ?></td>
                                   <td><?= htmlspecialchars($row['unit_melapor_nama'] ?? $row['unit_melapor']); ?></td>
                                   <td><?= htmlspecialchars($row['nama_barang']); ?><br><small style="color:#666;">Kode: <?= htmlspecialchars($row['kode_inventaris'] ?? '-') ?></small></td>
                                   <td><?= htmlspecialchars($row['nomor_seri']); ?></td>
							<td style="text-align:center;">
								<?php
									$status = $row['status'];
									$badge = '';
									switch($status) {
										case 'diajukan':
											$badge = '<span style="background: #ffc107; color: #212529; padding: 4px 12px; border-radius: 10px; font-weight: bold;">Diajukan</span>';
											break;
										case 'proses':
											$badge = '<span style="background: #007bff; color: #fff; padding: 4px 12px; border-radius: 10px; font-weight: bold;">Proses</span>';
											break;
										case 'selesai':
											$badge = '<span style="background: #28a745; color: #fff; padding: 4px 12px; border-radius: 10px; font-weight: bold;">Selesai</span>';
											break;
										case 'tidak_dapat_diperbaiki':
											$badge = '<span style="background: #dc3545; color: #fff; padding: 4px 12px; border-radius: 10px; font-weight: bold;">Tidak Dapat Diperbaiki</span>';
											break;
										default:
											$badge = htmlspecialchars($status);
									}
									echo $badge;
								?>
							</td>
                                   <td><?= htmlspecialchars($row['tindakan_perbaikan']); ?></td>
							<td style="text-align:center;">
								<?php if ($row['status'] == 'tidak_dapat_diperbaiki'): ?>
									<span style="background: #dc3545; color: #fff; padding: 4px 12px; border-radius: 10px; font-weight: bold;">
										RUSAK
									</span>
								<?php elseif (empty($row['tanggal_selesai'])): ?>
									-
								<?php else: ?>
									<span style="background: #28a745; color: #fff; padding: 4px 12px; border-radius: 10px; font-weight: bold;">
										<?= htmlspecialchars(date('d-m-Y H:i', strtotime($row['tanggal_selesai']))); ?>
									</span>
								<?php endif; ?>
							</td>
							<td>
								<!-- Button Detail Data -->
                                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalDetailBarang<?= $row['perbaikan_id'] ?>">
                                             <i class="fa fa-eye"></i> Detail
                                        </button>
							</td>
						</tr>
                         <?php endforeach; ?>
                         <!-- Modal Detail Barang -->
                         <?php foreach ($rows as $detailRow): ?>
                         <div class="modal fade" id="modalDetailBarang<?= $detailRow['perbaikan_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalDetailBarangLabel<?= $detailRow['perbaikan_id'] ?>" aria-hidden="true">
                         <div class="modal-dialog modal-lg" role="document">
                              <div class="modal-content" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-radius: 12px;">
                              <div class="modal-header" style="background: #1976d2; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                                   <h5 class="modal-title" id="modalDetailBarangLabel<?= $detailRow['perbaikan_id'] ?>"><i class="fa fa-eye"></i> Detail Data Perbaikan Barang</h5>
                                   <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                                   <span aria-hidden="true">&times;</span>
                                   </button>
                              </div>
                              <div class="modal-body">
                                   <div class="row">
                                        <div class="col-md-6">
                                             <div class="form-group">
                                                  <label><strong>Foto Barang:</strong></label>
                                                  <?php if (!empty($detailRow['foto'])): ?>
                                                  <div class="p-2" style="background:#fff; border-radius:6px; border:1px solid #90caf9; text-align:center;">
                                                       <img src="unit/barang/foto-barang/<?= htmlspecialchars($detailRow['foto']) ?>" alt="Foto Barang" style="max-width:180px;max-height:180px;">
                                                  </div>
                                                  <?php else: ?>
                                                  <div class="p-2" style="background:#fff; border-radius:6px; border:1px solid #90caf9; text-align:center;">-</div>
                                                  <?php endif; ?>
                                             </div>
                                             <div class="form-group">
                                                  <label><strong>Nama Barang:</strong></label>
                                                  <div class="p-2" style="background:#fff; border-radius:6px; border:1px solid #90caf9;"> <?= htmlspecialchars($detailRow['nama_barang']) ?> </div>
                                             </div>
                                             <div class="form-group">
                                                  <label><strong>Jenis Barang:</strong></label>
                                                  <div class="p-2" style="background:#fff; border-radius:6px; border:1px solid #90caf9;"> <?= htmlspecialchars($detailRow['jenis_barang']) ?> </div>
                                             </div>
                                             <div class="form-group">
                                                  <label><strong>Nomor Seri:</strong></label>
                                                  <div class="p-2" style="background:#fff; border-radius:6px; border:1px solid #90caf9;"> <?= htmlspecialchars($detailRow['nomor_seri']) ?> </div>
                                             </div>
                                             <div class="form-group">
                                                  <?php if ($detailRow['jenis_barang'] == 'Komputer & Laptop'): ?>
                                                  <label><strong>IP Address:</strong></label>
                                                  <div class="p-2" style="background:#fff; border-radius:6px; border:1px solid #90caf9;"> <?= htmlspecialchars($detailRow['ip_address']) ?> </div>
                                                  <?php endif; ?>
                                             </div>
                                             <div class="form-group">
                                                  <label><strong>Lokasi:</strong></label>
                                                      <div class="p-2" style="background:#fff; border-radius:6px; border:1px solid #90caf9;"> <?= htmlspecialchars($detailRow['lokasi_barang']) ?> </div>
                                             </div>
                                        </div>
                                        <div class="col-md-6">
                                             <div class="form-group">
                                                  <label><strong>Tanggal Kerusakan:</strong></label>
                                                  <div class="p-2" style="background:#fff; border-radius:6px; border:1px solid #90caf9;"> <?= !empty($detailRow['tanggal_lapor']) ? date('d-m-Y', strtotime($detailRow['tanggal_lapor'])) : '-' ?> </div>
                                             </div>
                                             <div class="form-group">
                                                  <label><strong>Descripsi Kerusakan:</strong></label>
                                                  <div class="p-2" style="background:#fff; border-radius:6px; border:1px solid #90caf9;"> <?= htmlspecialchars(ucwords($detailRow['deskripsi_kerusakan'])) ?> </div>
                                             </div>
                                             <div class="form-group">
                                                  <label><strong>Tindakan perbaikan:</strong></label>
                                                  <div class="p-2" style="background:#fff; border-radius:6px; border:1px solid #90caf9;"> <?= htmlspecialchars($detailRow['tindakan_perbaikan']) ?> </div>
                                             </div>
                                             <div class="form-group">
                                                  <label><strong>Teknisi:</strong></label><br>
                                                  <label><small>Noted : Jika Barang dapat diperbaiki oleh unit IT</small></label>
                                                  <div class="p-2" style="background:#fff; border-radius:6px; border:1px solid #90caf9;"> <?= htmlspecialchars($detailRow['teknisi']) ?> </div>
                                             </div>
                                             <div class="form-group">
                                                  <label><strong>Keterangan:</strong></label>
                                                  <div class="p-2" style="background:#fff; border-radius:6px; border:1px solid #90caf9;"> <?= htmlspecialchars($detailRow['keterangan']) ?> </div>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                              <div class="modal-footer" style="background: #e3f2fd; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                                   <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                              </div>
                              </div>
                         </div>
                         </div>
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
    <form id="formPrint" method="get" target="_blank" action="unit/perbaikan/print_perbaikan.php">
      <div class="modal-content">
        <div class="modal-header" style="background: #1976d2; color: white;">
          <h5 class="modal-title" id="modalPrintLabel"><i class="fas fa-print"></i> Cetak Laporan Data Perbaikan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Pilihan Tindakan</label>
            <select class="form-control" name="tindakan">
              <option value="">Semua Tindakan</option>
              <option value="service_luar">Service Luar</option>
              <option value="service_sendiri">Service Sendiri</option>
            </select>
          </div>
          <div class="form-group">
            <label>Pilihan Status</label>
            <select class="form-control" name="status">
              <option value="">Semua Status</option>
              <option value="diajukan">Diajukan</option>
              <option value="proses">Proses</option>
              <option value="selesai">Selesai</option>
              <option value="tidak_dapat_diperbaiki">Tidak Dapat Diperbaiki</option>
            </select>
          </div>
          <div class="form-group">
            <label>Tanggal Mulai</label>
            <input type="date" class="form-control" name="tanggal_mulai">
          </div>
          <div class="form-group">
            <label>Tanggal Selesai</label>
            <input type="date" class="form-control" name="tanggal_selesai">
          </div>
        </div>
        <div class="modal-footer" style="background: #e3f2fd;">
          <button type="submit" class="btn btn-success"><i class="fas fa-print"></i> Cetak</button>
        </div>
      </div>
    </form>
  </div>
</div>
