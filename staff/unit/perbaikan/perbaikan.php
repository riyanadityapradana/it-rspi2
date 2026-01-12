<?php
// Handle Set Selesai form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'set_selesai') {
  $perbaikan_id = isset($_POST['perbaikan_id']) ? $_POST['perbaikan_id'] : '';
  $tanggal_selesai = isset($_POST['tanggal_selesai']) ? $_POST['tanggal_selesai'] : '';
  
  if (!empty($perbaikan_id) && !empty($tanggal_selesai)) {
    // Format datetime dari datetime-local input
    $datetime_selesai = str_replace('T', ' ', $tanggal_selesai);
    
    // Get penyerahan_id dari perbaikan record
    $perbaikan_q = mysqli_query($config, "SELECT penyerahan_id FROM tb_perbaikan_barang WHERE perbaikan_id = '$perbaikan_id'");
    $perbaikan_r = mysqli_fetch_assoc($perbaikan_q);
    $penyerahan_id = $perbaikan_r['penyerahan_id'];
    
    // Update tanggal_selesai dengan status selesai
    $update_query = "UPDATE tb_perbaikan_barang SET tanggal_selesai = '$datetime_selesai', status = 'selesai' WHERE perbaikan_id = '$perbaikan_id'";
    $update_result = mysqli_query($config, $update_query);
    
    if ($update_result) {
      // Update kondisi penyerahan menjadi 'bekas'
      $update_penyerahan = "UPDATE tb_penyerahan SET kondisi = 'bekas' WHERE penyerahan_id = '$penyerahan_id'";
      mysqli_query($config, $update_penyerahan);
      
      header('Location: dashboard_staff.php?unit=perbaikan&msg=Tanggal selesai berhasil disimpan!');
      exit;
    } else {
      header('Location: dashboard_staff.php?unit=perbaikan&err=Gagal menyimpan tanggal selesai!');
      exit;
    }
  }
}

 $result = mysqli_query($config, "SELECT 
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
JOIN tb_penyerahan pen ON p.penyerahan_id = pen.penyerahan_id
LEFT JOIN tb_lokasi l ON b.lokasi_id = l.lokasi_id
LEFT JOIN tb_lokasi u ON p.unit_melapor = u.lokasi_id
ORDER BY b.barang_id, p.tanggal_lapor DESC");
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
                         <a href="?unit=create_perbaikan" class="btn btn-tool btn-sm" style="background:rgba(0, 123, 255, 1)">
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
				<table id="example" class="table table-bordered table-striped">
					<thead style="background:rgb(129, 2, 0, 1)">
						<tr>
							<th style="text-align: center; color: white;">No</th>
                                   <th style="text-align: center; color: white;">Tanggal Lapor</th>
                                   <th style="text-align: center; color: white;">Unit Melapor</th>
							<th style="text-align: center; color: white;">Nama Barang</th>
                                   <th style="text-align: center; color: white;">No.Seri/SN</th>
							<th style="text-align: center; color: white;">Status</th>
                                   <th style="text-align: center; color: white;">Tindakan</th>
							<th style="text-align: center; color: white;">Tanggal Selesai</th>
							<th style="text-align: center; color: white;">Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php while($row = mysqli_fetch_assoc($result)): ?>
						<tr>
							<td><?= $n++; ?></td>
							<td><?= htmlspecialchars($row['tanggal_lapor']); ?></td>
                                   <td><?= htmlspecialchars($row['unit_melapor_nama'] ?? $row['unit_melapor']); ?></td>
                                   <td><?= htmlspecialchars($row['nama_barang']); ?></td>
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
								<?php if (empty($row['tanggal_selesai'])): ?>
									<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalSetSelesai<?= $row['perbaikan_id'] ?>">
										<i class="fa fa-clock"></i> Set Selesai
									</button>
								<?php else: ?>
									<span style="background: #28a745; color: #fff; padding: 4px 12px; border-radius: 10px; font-weight: bold;">
										<?= htmlspecialchars(date('d-m-Y H:i', strtotime($row['tanggal_selesai']))); ?>
									</span>
								<?php endif; ?>
							</td>
							<td>
								<a href="dashboard_staff.php?unit=update_perbaikan&id=<?= urlencode($row['perbaikan_id']); ?>" class="btn btn-warning btn-sm">Edit</a>
								<a href="dashboard_staff.php?unit=delete_perbaikan&id=<?= urlencode($row['perbaikan_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus data?')">Hapus</a>
								<!-- Button Detail Data -->
                                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalDetailBarang<?= $row['perbaikan_id'] ?>">
                                             <i class="fa fa-eye"></i> Detail
                                        </button>
							</td>
						</tr>
					<?php endwhile; ?>
                         <!-- Modal Detail Barang -->
                         <?php foreach ($result as $detailRow): ?>
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
                                                  <div class="p-2" style="background:#fff; border-radius:6px; border:1px solid #90caf9;"> <?= htmlspecialchars($detailRow['nama_lokasi']) ?> </div>
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
                         <!-- Modal Set Selesai -->
                         <div class="modal fade" id="modalSetSelesai<?= $detailRow['perbaikan_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalSetSelesaiLabel<?= $detailRow['perbaikan_id'] ?>" aria-hidden="true">
                         <div class="modal-dialog" role="document">
                              <div class="modal-content">
                              <div class="modal-header" style="background: #28a745; color: white;">
                                   <h5 class="modal-title" id="modalSetSelesaiLabel<?= $detailRow['perbaikan_id'] ?>"><i class="fa fa-check-circle"></i> Set Tanggal Selesai Perbaikan</h5>
                                   <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                                   <span aria-hidden="true">&times;</span>
                                   </button>
                              </div>
                              <form method="post" action="">
                                   <div class="modal-body">
                                        <input type="hidden" name="perbaikan_id" value="<?= $detailRow['perbaikan_id'] ?>">
                                        <input type="hidden" name="action" value="set_selesai">
                                        <div class="form-group">
                                             <label>Tanggal Selesai</label>
                                             <input type="date" class="form-control" name="tanggal_selesai" value="<?= !empty($detailRow['tanggal_selesai']) ? date('Y-m-d', strtotime($detailRow['tanggal_selesai'])) : date('Y-m-d') ?>" required>
                                        </div>
                                        <div class="form-group">
                                             <label>Jam Selesai</label>
                                             <input type="time" class="form-control" name="jam_selesai" value="<?= !empty($detailRow['tanggal_selesai']) ? date('H:i', strtotime($detailRow['tanggal_selesai'])) : '12:00' ?>" required>
                                        </div>
                                   </div>
                                   <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-success">Simpan Tanggal Selesai</button>
                                   </div>
                              </form>
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