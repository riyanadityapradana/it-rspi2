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
             p.bukti_struk,
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
                                   <th style="font-size: 14px; color: white;" width="90" responsive>Tindakan</th>
							<th style="font-size: 14px; color: white;" width="120" responsive>Tanggal Selesai</th>
							<th style="font-size: 14px; color: white;" width="90" responsive>Aksi</th>
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
							<td class="text-center">
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
											$badge = '<span style="background: #dc3545; color: #fff; padding: 2px 3px; border-radius: 5px; font-weight: bold;">Tidak Dapat Diperbaiki</span>';
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
                              <div class="modal-content">
                              <div class="modal-header border-bottom">
                                   <div>
                                        <h5 class="modal-title" id="modalDetailBarangLabel<?= $detailRow['perbaikan_id'] ?>" style="font-size: 18px; font-weight: 600;">Detail Data Perbaikan Barang</h5>
                                        <small class="text-muted" style="font-size: 13px;">Informasi lengkap perbaikan barang</small>
                                   </div>
                                   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                   <span aria-hidden="true">&times;</span>
                                   </button>
                              </div>
                              <div class="modal-body" style="padding: 20px;">
                                   <!-- Informasi Barang Section -->
                                   <div style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #e0e0e0;">
                                        <h6 style="font-weight: 600; margin-bottom: 15px; font-size: 16px;">Informasi Barang</h6>
                                        
                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                                             <div>
                                                  <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Foto Barang</p>
                                                  <?php if (!empty($detailRow['foto'])): ?>
                                                  <div style="background: #f5f5f5; border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px; text-align: center;">
                                                       <img src="../barang/foto-barang/<?= htmlspecialchars($detailRow['foto']) ?>" alt="Foto Barang" style="max-width: 100%; max-height: 200px; border-radius: 4px;">
                                                  </div>
                                                  <?php else: ?>
                                                  <div style="background: #f5f5f5; border: 1px dashed #ccc; border-radius: 6px; padding: 20px; text-align: center; color: #999;">
                                                       -
                                                  </div>
                                                  <?php endif; ?>
                                             </div>
                                             <div>
                                                  <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Foto Bukti Struk</p>
                                                  <?php if (!empty($detailRow['bukti_struk'])): ?>
                                                  <div style="background: #f5f5f5; border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px; text-align: center;">
                                                       <img src="/it-rspi2/staff/unit/perbaikan/bukti_struk/<?= htmlspecialchars($detailRow['bukti_struk']) ?>" alt="Bukti Struk" style="max-width: 100%; max-height: 200px; border-radius: 4px;" onerror="this.src='/images/placeholder.png'; this.onerror=null;">
                                                  </div>
                                                  <?php else: ?>
                                                  <div style="background: #f5f5f5; border: 1px dashed #ccc; border-radius: 6px; padding: 20px; text-align: center; color: #999;">
                                                       -
                                                  </div>
                                                  <?php endif; ?>
                                             </div>
                                        </div>
                                        
                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                                             <div>
                                                  <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Nama Barang</p>
                                                  <strong style="font-size: 15px;"><?= htmlspecialchars($detailRow['nama_barang']) ?></strong>
                                             </div>
                                             <div>
                                                  <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Kode Inventaris</p>
                                                  <strong style="font-size: 15px;"><?= htmlspecialchars($detailRow['kode_inventaris'] ?? '-') ?></strong>
                                             </div>
                                        </div>

                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                                             <div>
                                                  <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Jenis Barang</p>
                                                  <strong style="font-size: 15px;"><?= htmlspecialchars($detailRow['jenis_barang']) ?></strong>
                                             </div>
                                             <div>
                                                  <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Nomor Seri</p>
                                                  <strong style="font-size: 15px;"><?= htmlspecialchars($detailRow['nomor_seri']) ?></strong>
                                             </div>
                                        </div>

                                        <?php if ($detailRow['jenis_barang'] == 'Komputer & Laptop'): ?>
                                        <div style="margin-bottom: 15px;">
                                             <p style="font-size: 13px; color: #999; margin-bottom: 5px;">IP Address</p>
                                             <strong style="font-size: 15px;"><?= htmlspecialchars($detailRow['ip_address']) ?></strong>
                                        </div>
                                        <?php endif; ?>

                                        <div style="margin-bottom: 15px;">
                                             <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Lokasi</p>
                                             <strong style="font-size: 15px;"><?= htmlspecialchars($detailRow['lokasi_barang']) ?></strong>
                                        </div>
                                   </div>

                                   <!-- Informasi Detail Kerusakan Section -->
                                   <div style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #e0e0e0;">
                                        <h6 style="font-weight: 600; margin-bottom: 15px; font-size: 16px;">Informasi Detail Kerusakan</h6>
                                        
                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                                             <div>
                                                  <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Tanggal Kerusakan</p>
                                                  <strong style="font-size: 15px;"><?= !empty($detailRow['tanggal_lapor']) ? date('d-m-Y', strtotime($detailRow['tanggal_lapor'])) : '-' ?></strong>
                                             </div>
                                             <div>
                                                  <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Unit Melapor</p>
                                                  <strong style="font-size: 15px;"><?= htmlspecialchars($detailRow['unit_melapor_nama'] ?? $detailRow['unit_melapor']) ?></strong>
                                             </div>
                                        </div>

                                        <div style="margin-bottom: 15px;">
                                             <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Deskripsi Kerusakan</p>
                                             <p style="word-wrap: break-word; white-space: pre-wrap; margin: 0; font-size: 14px;"><?= htmlspecialchars(ucwords($detailRow['deskripsi_kerusakan'])) ?></p>
                                        </div>

                                        <div style="margin-bottom: 15px;">
                                             <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Tindakan Perbaikan</p>
                                             <strong style="font-size: 15px;"><?= htmlspecialchars($detailRow['tindakan_perbaikan']) ?></strong>
                                        </div>

                                        <div style="margin-bottom: 15px;">
                                             <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Keterangan</p>
                                             <p style="word-wrap: break-word; white-space: pre-wrap; margin: 0; font-size: 14px;"><?= htmlspecialchars($detailRow['keterangan']) ?></p>
                                        </div>
                                   </div>

                                   <!-- Informasi Status Section -->
                                   <div>
                                        <h6 style="font-weight: 600; margin-bottom: 15px; font-size: 16px;">Informasi Status</h6>
                                        
                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                             <div>
                                                  <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Status</p>
                                                  <span style="display: inline-block; padding: 4px 8px; background: #e0e0e0; border-radius: 4px; font-size: 13px;">
                                                       <?php
                                                            $status = $detailRow['status'];
                                                            switch($status) {
                                                                 case 'diajukan':
                                                                      echo '<span style="background: #ffc107; color: #212529; padding: 4px 12px; border-radius: 10px; font-weight: bold;">Diajukan</span>';
                                                                      break;
                                                                 case 'proses':
                                                                      echo '<span style="background: #007bff; color: #fff; padding: 4px 12px; border-radius: 10px; font-weight: bold;">Proses</span>';
                                                                      break;
                                                                 case 'selesai':
                                                                      echo '<span style="background: #28a745; color: #fff; padding: 4px 12px; border-radius: 10px; font-weight: bold;">Selesai</span>';
                                                                      break;
                                                                 case 'tidak_dapat_diperbaiki':
                                                                      echo '<span style="background: #dc3545; color: #fff; padding: 2px 3px; border-radius: 5px; font-weight: bold;">Tidak Dapat Diperbaiki</span>';
                                                                      break;
                                                                 default:
                                                                      echo htmlspecialchars($status);
                                                            }
                                                       ?>
                                                  </span>
                                             </div>
                                             <div>
                                                  <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Tanggal Selesai</p>
                                                  <strong style="font-size: 15px;">
                                                       <?php if ($detailRow['status'] == 'tidak_dapat_diperbaiki'): ?>
                                                            RUSAK
                                                       <?php elseif (empty($detailRow['tanggal_selesai'])): ?>
                                                            -
                                                       <?php else: ?>
                                                            <?= htmlspecialchars(date('d-m-Y H:i', strtotime($detailRow['tanggal_selesai']))); ?>
                                                       <?php endif; ?>
                                                  </strong>
                                             </div>
                                        </div>

                                        <div style="margin-top: 15px;">
                                             <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Teknisi</p>
                                             <small style="display: block; color: #666; margin-bottom: 5px;">Noted: Jika barang dapat diperbaiki oleh unit IT</small>
                                             <strong style="font-size: 15px;"><?= htmlspecialchars($detailRow['teknisi']) ?></strong>
                                        </div>
                                   </div>
                              </div>
                              <div class="modal-footer">
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

<style>
  .detail-image-viewer {
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 14px;
  }

  .detail-image-frame {
    position: relative;
    overflow: auto;
    min-height: 220px;
    max-height: 320px;
    background: linear-gradient(135deg, #fdfdfd 0%, #f1f3f5 100%);
    border: 1px solid #dee2e6;
    border-radius: 8px;
  }

  .detail-image-frame.is-zoomed {
    cursor: grab;
  }

  .detail-image-frame.is-dragging {
    cursor: grabbing;
  }

  .detail-image-stage {
    display: inline-flex;
    min-width: 100%;
    min-height: 100%;
    align-items: center;
    justify-content: center;
  }

  .detail-image-element {
    display: block;
    width: 100%;
    height: auto;
    max-width: 100%;
    transition: width 0.2s ease;
    cursor: zoom-in;
    user-select: none;
    -webkit-user-drag: none;
    flex-shrink: 0;
    border-radius: 4px;
  }

  .detail-image-frame.is-zoomed .detail-image-element {
    cursor: grab;
  }

  .detail-image-frame.is-dragging .detail-image-element {
    cursor: grabbing;
  }

  .detail-image-controls {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 12px;
  }

  .detail-image-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
  }

  .detail-image-actions button {
    min-width: 42px;
  }

  .detail-image-zoom-level {
    display: inline-flex;
    align-items: center;
    padding: 0 12px;
  }
</style>
<script>
function initDetailImageViewer(viewer) {
  var frame = viewer.querySelector('[data-image-frame]');
  var img = viewer.querySelector('[data-image-element]');
  var indicator = viewer.querySelector('[data-image-zoom-level]');
  var state = {
    zoom: 1,
    isDragging: false,
    startX: 0,
    startY: 0,
    scrollLeft: 0,
    scrollTop: 0
  };

  if (!frame || !img || !indicator) {
    return;
  }

  function stopDrag() {
    state.isDragging = false;
    frame.classList.remove('is-dragging');
  }

  function centerFrame() {
    frame.scrollLeft = Math.max(0, (frame.scrollWidth - frame.clientWidth) / 2);
    frame.scrollTop = Math.max(0, (frame.scrollHeight - frame.clientHeight) / 2);
  }

  function setZoom(value) {
    state.zoom = Math.min(3, Math.max(0.5, value));
    img.style.width = (state.zoom * 100) + '%';
    img.style.maxWidth = state.zoom > 1 ? 'none' : '100%';
    indicator.textContent = Math.round(state.zoom * 100) + '%';
    frame.classList.toggle('is-zoomed', state.zoom > 1);

    if (state.zoom <= 1) {
      stopDrag();
      frame.scrollLeft = 0;
      frame.scrollTop = 0;
    } else {
      centerFrame();
    }
  }

  viewer.querySelector('[data-action="zoom-in"]').addEventListener('click', function() {
    setZoom(state.zoom + 0.25);
  });

  viewer.querySelector('[data-action="zoom-out"]').addEventListener('click', function() {
    setZoom(state.zoom - 0.25);
  });

  viewer.querySelector('[data-action="reset"]').addEventListener('click', function() {
    setZoom(1);
  });

  img.addEventListener('dblclick', function() {
    if (state.zoom > 1) {
      setZoom(1);
    } else {
      setZoom(2);
    }
  });

  img.addEventListener('dragstart', function(event) {
    event.preventDefault();
  });

  frame.addEventListener('mousedown', function(event) {
    if (state.zoom <= 1) {
      return;
    }

    state.isDragging = true;
    state.startX = event.clientX;
    state.startY = event.clientY;
    state.scrollLeft = frame.scrollLeft;
    state.scrollTop = frame.scrollTop;
    frame.classList.add('is-dragging');
    event.preventDefault();
  });

  frame.addEventListener('mousemove', function(event) {
    if (!state.isDragging || state.zoom <= 1) {
      return;
    }

    frame.scrollLeft = state.scrollLeft - (event.clientX - state.startX);
    frame.scrollTop = state.scrollTop - (event.clientY - state.startY);
  });

  frame.addEventListener('mouseleave', stopDrag);
  frame.addEventListener('mouseup', stopDrag);
  document.addEventListener('mouseup', stopDrag);

  setZoom(1);
}

document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('[data-image-viewer]').forEach(initDetailImageViewer);

  $('.modal').on('hidden.bs.modal', function() {
    this.querySelectorAll('[data-image-viewer]').forEach(function(viewer) {
      var resetButton = viewer.querySelector('[data-action="reset"]');
      if (resetButton) {
        resetButton.click();
      }
    });
  });
});
</script>
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

