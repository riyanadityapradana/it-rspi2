<?php

// Handle Set Selesai form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'set_selesai') {
     $perbaikan_id = isset($_POST['perbaikan_id']) ? $_POST['perbaikan_id'] : '';
     $tanggal_selesai_date = isset($_POST['tanggal_selesai']) ? $_POST['tanggal_selesai'] : '';
     $jam_selesai = isset($_POST['jam_selesai']) ? $_POST['jam_selesai'] : '';

     if (!empty($perbaikan_id) && !empty($tanggal_selesai_date) && !empty($jam_selesai)) {
          // Combine date + time into datetime
          $datetime_selesai = $tanggal_selesai_date . ' ' . $jam_selesai . ':00';

          // Update tanggal_selesai with status selesai
          $update_query = "UPDATE tb_perbaikan_barang SET tanggal_selesai = '$datetime_selesai', status = 'selesai' WHERE perbaikan_id = '$perbaikan_id'";
          $update_result = mysqli_query($config, $update_query);

          if ($update_result) {
               // Setelah selesai, update kondisi di tb_barang menjadi 'bekas'
               $q_barang = mysqli_query($config, "SELECT barang_id FROM tb_perbaikan_barang WHERE perbaikan_id = '$perbaikan_id'");
               if ($q_barang) {
                    $rb = mysqli_fetch_assoc($q_barang);
                    if ($rb && !empty($rb['barang_id'])) {
                         mysqli_query($config, "UPDATE tb_barang SET kondisi = 'bekas' WHERE barang_id = '" . intval($rb['barang_id']) . "'");
                    }
               }

               header('Location: dashboard_staff.php?unit=perbaikan&msg=Tanggal selesai berhasil disimpan!');
               exit;
          } else {
               header('Location: dashboard_staff.php?unit=perbaikan&err=Gagal menyimpan tanggal selesai!');
               exit;
          }
     }
}

// Handle Upload Bukti Struk (gambar) form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'upload_bukti') {
     $perbaikan_id = isset($_POST['perbaikan_id']) ? $_POST['perbaikan_id'] : '';
     if (!empty($perbaikan_id) && isset($_FILES['bukti_struk']) && $_FILES['bukti_struk']['error'] == 0) {
          $file = $_FILES['bukti_struk'];
          $allowed = ['jpg','jpeg','png','gif','bmp','webp'];
          $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
          $maxSize = 2 * 1024 * 1024; // 2MB
          $target_dir = __DIR__ . '/bukti_struk/';
          if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
          if (!in_array($ext, $allowed)) {
               header('Location: dashboard_staff.php?unit=perbaikan&err=Format file tidak diperbolehkan!');
               exit;
          }
          if ($file['size'] > $maxSize) {
               header('Location: dashboard_staff.php?unit=perbaikan&err=File terlalu besar (max 2MB)!');
               exit;
          }
          $newname = $perbaikan_id . '_' . time() . '.' . $ext;
          $target = $target_dir . $newname;
          if (move_uploaded_file($file['tmp_name'], $target)) {
               // simpan nama file ke DB (relatif)
               $safe_name = mysqli_real_escape_string($config, $newname);
               $up = "UPDATE tb_perbaikan_barang SET bukti_struk = '$safe_name' WHERE perbaikan_id = '" . mysqli_real_escape_string($config, $perbaikan_id) . "'";
               mysqli_query($config, $up);
               header('Location: dashboard_staff.php?unit=perbaikan&msg=Upload bukti struk berhasil!');
               exit;
          } else {
               header('Location: dashboard_staff.php?unit=perbaikan&err=Gagal memindahkan file!');
               exit;
          }
     }
}

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
             p.bukti_struk,
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
					<thead style="background:rgb(129, 2, 0, 1)">
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
                                   <td><?= htmlspecialchars($row['tindakan_perbaikan']); ?><br><small style="color:#666;"><?php if (strtolower(str_replace(' ', '_', $row['tindakan_perbaikan'])) === 'service_luar'): ?>
                                             <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalUploadBukti<?= $row['perbaikan_id'] ?>">Upload Bukti</button>
                                          <?php endif; ?></small></td>
							<td style="text-align:center;">
								<?php if ($row['status'] == 'tidak_dapat_diperbaiki'): ?>
									<span style="background: #dc3545; color: #fff; padding: 4px 12px; border-radius: 10px; font-weight: bold;">
										RUSAK
									</span>
								<?php elseif (empty($row['tanggal_selesai'])): ?>
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
                         <?php endforeach; ?>
                                   <!-- Modal Upload Bukti per row -->
                                   <?php foreach ($rows as $uploadRow): ?>
                                   <div class="modal fade" id="modalUploadBukti<?= $uploadRow['perbaikan_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalUploadBuktiLabel<?= $uploadRow['perbaikan_id'] ?>" aria-hidden="true">
                                   <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                        <div class="modal-header" style="background: #007bff; color: white;">
                                             <h5 class="modal-title" id="modalUploadBuktiLabel<?= $uploadRow['perbaikan_id'] ?>">Upload Bukti Struk - ID <?= htmlspecialchars($uploadRow['perbaikan_id']) ?></h5>
                                             <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                                             <span aria-hidden="true">&times;</span>
                                             </button>
                                        </div>
                                        <form method="post" enctype="multipart/form-data" action="">
                                             <div class="modal-body">
                                                  <input type="hidden" name="perbaikan_id" value="<?= htmlspecialchars($uploadRow['perbaikan_id']) ?>">
                                                  <input type="hidden" name="action" value="upload_bukti">
                                                  <div class="form-group">
                                                       <label>Pilih gambar bukti struk (jpg/png/gif/webp, max 2MB)</label>
                                                       <input type="file" name="bukti_struk" accept="image/*" class="form-control" required>
                                                  </div>
                                                  <?php if (!empty($uploadRow['bukti_struk'])): ?>
                                                       <div class="form-group">
                                                            <label>Preview saat ini:</label><br>
                                                            <img src="bukti_struk/<?= htmlspecialchars($uploadRow['bukti_struk']) ?>" alt="Bukti" style="max-width:220px; max-height:220px; border:1px solid #ddd; padding:6px; background:#fff;">
                                                       </div>
                                                  <?php endif; ?>
                                             </div>
                                             <div class="modal-footer">
                                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                  <button type="submit" class="btn btn-primary">Upload</button>
                                             </div>
                                        </form>
                                        </div>
                                   </div>
                                   </div>
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
