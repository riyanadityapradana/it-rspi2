<?php
// Proses ACC perbaikan
if (isset($_GET['acc'])) {
		$id = intval($_GET['acc']);
		$update = mysqli_query($config, "UPDATE tb_perbaikan_barang SET status='proses' WHERE perbaikan_id='$id'");
		if ($update) {
				header('Location: dashboard_admin.php?unit=perbaikan&msg=Data perbaikan telah di-ACC!');
				exit;
		} else {
				header('Location: dashboard_admin.php?unit=perbaikan&err=Gagal update status!');
				exit;
		}
}
?>
<section class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1>DATA PERBAIKAN BARANG</h1>
			</div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="dashboard_admin.php?unit=beranda">Home</a></li>
					<li class="breadcrumb-item active">Perbaikan</li>
				</ol>
			</div>
		</div>
	</div>
</section>
<section class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<div class="card-tools" style="float: right; text-align: right;">
							<a href="#" class="btn btn-tool btn-sm" data-card-widget="collapse" style="background:rgba(69, 77, 85, 1)"><i class="fas fa-bars"></i></a>
						</div>
					</div>
					<div class="card-body">
						<table id="example1" class="table table-bordered table-striped">
							<thead style="background:rgb(52, 58, 64, 1); color:white;">
								<tr>
									<th style="text-align:center; color:white;">No.</th>
									<th style="text-align:center; color:white;">Nama Barang</th>
									<th style="text-align:center; color:white;">Tanggal Lapor</th>
									<th style="text-align:center; color:white;">Tindakan Perbaikan</th>
									<th style="text-align:center; color:white;">Status</th>
									<th style="text-align:center; color:white;">Aksi</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$query = mysqli_query($config, "SELECT p.*, b.nama_barang FROM tb_perbaikan_barang p LEFT JOIN tb_barang b ON p.barang_id = b.barang_id ORDER BY p.tanggal_lapor DESC");
								$n = 1;
								while ($data = mysqli_fetch_array($query)) {
									$nn = $n++;
								?>
								<tr>
									<td align="center"><?= $nn; ?>.</td>
									<td><?= htmlspecialchars($data['nama_barang']) ?></td>
									<td><?= date('d-m-Y', strtotime($data['tanggal_lapor'])) ?></td>
									<td><?= htmlspecialchars($data['tindakan_perbaikan']) ?></td>
									<td align="center">
										<?php
										if ($data['status'] == 'proses') {
											echo '<span class="badge badge-info">Proses</span>';
										} elseif ($data['status'] == 'selesai') {
											echo '<span class="badge badge-success">Selesai</span>';
										} elseif ($data['status'] == 'diajukan') {
											echo '<span class="badge badge-warning" style="color:#212529;">Diajukan</span>';
										} else {
											echo $data['status'];
										}
										?>
									</td>
									<td align="center">
										<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalDetail<?= $data['perbaikan_id'] ?>">
											<i class="fa fa-eye"></i> Detail
										</button>
										<?php if ($data['status'] == 'diajukan') { ?>
											<a href="dashboard_admin.php?unit=perbaikan&acc=<?= $data['perbaikan_id'] ?>" class="btn btn-success btn-sm" title="ACC"><i class="fa fa-check"></i> ACC</a>
										<?php } ?>
										<!-- Modal Detail -->
										<div class="modal fade" id="modalDetail<?= $data['perbaikan_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel<?= $data['perbaikan_id'] ?>" aria-hidden="true">
											<div class="modal-dialog modal-lg" role="document">
												<div class="modal-content" style="border-radius:15px;">
													<div class="modal-header" style="background:linear-gradient(90deg,#007bff,#17a2b8);color:white;">
														<h5 class="modal-title" id="modalDetailLabel<?= $data['perbaikan_id'] ?>"><i class="fa fa-info-circle"></i> Detail Perbaikan</h5>
														<button type="button" class="close" data-dismiss="modal" aria-label="Close">
															<span aria-hidden="true">&times;</span>
														</button>
													</div>
													<div class="modal-body" style="background:#f8f9fa;">
														<div class="row">
															<div class="col-md-6 mb-2">
																<strong>Nama Barang:</strong><br>
																<span class="text-primary"><?= htmlspecialchars($data['nama_barang']) ?></span>
															</div>
															<div class="col-md-6 mb-2">
																<strong>Tanggal Lapor:</strong><br>
																<span><?= date('d-m-Y', strtotime($data['tanggal_lapor'])) ?></span>
															</div>
															<div class="col-md-6 mb-2">
																<strong>Tindakan Perbaikan:</strong><br>
																<span><?= htmlspecialchars($data['tindakan_perbaikan']) ?></span>
															</div>
															<div class="col-md-6 mb-2">
																<strong>Status:</strong><br>
																<span><?= htmlspecialchars(ucwords($data['status'])) ?></span>
															</div>
															<div class="col-md-6 mb-2">
																<strong>Tanggal Selesai:</strong><br>
																<span><?= $data['tanggal_selesai'] ? date('d-m-Y', strtotime($data['tanggal_selesai'])) : '<span class="badge badge-warning">Belum selesai</span>' ?></span>
															</div>
															<div class="col-md-6 mb-2">
																<strong>Biaya Perbaikan:</strong><br>
																<span><?= $data['biaya_perbaikan'] ? 'Rp '.number_format($data['biaya_perbaikan'],0,',','.') : '-' ?></span>
															</div>
															<div class="col-md-6 mb-2">
																<strong>Teknisi:</strong><br>
																<span><?= htmlspecialchars($data['teknisi']) ?></span>
															</div>
															<div class="col-md-12 mb-2">
																<strong>Keterangan:</strong><br>
																<span><?= htmlspecialchars($data['keterangan']) ?></span>
															</div>
														</div>
													</div>
													<div class="modal-footer" style="background:#e9ecef;">
														<button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
													</div>
												</div>
											</div>
										</div>
									</td>
								</tr>
								<?php } // end while ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
