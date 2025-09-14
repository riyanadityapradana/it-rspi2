<?php
// Proses verifikasi
if (isset($_GET['verifikasi'])) {
	$id = intval($_GET['verifikasi']);
	$id_pimpinan = $_SESSION['id_user'];
	$update = mysqli_query($config, "UPDATE tb_lembur SET status_lembur='Diterima', id_pimpinan='$id_pimpinan' WHERE id_lembur='$id'");
	if ($update) {
		 header('Location: dashboard_admin.php?unit=lembur&msg=Data Lembur staff telah diverifikasi!');
    		exit;
 	} else {
        header('Location: dashboard_admin.php?unit=lembur&err=Gagal menyimpan data dalam database!');
    		exit;
        // echo "Error: " . mysqli_error($config); // Debugging error
    }
}
// Proses tolak
if (isset($_GET['tolak'])) {
	$id = intval($_GET['tolak']);
	$id_pimpinan = $_SESSION['id_user'];
	$update = mysqli_query($config, "UPDATE tb_lembur SET status_lembur='Ditolak', id_pimpinan='$id_pimpinan' WHERE id_lembur='$id'");
	if ($update) {
		 header('Location: dashboard_admin.php?unit=lembur&msg=Data Lembur staff telah diverifikasi!');
    		exit;
 	} else {
        header('Location: dashboard_admin.php?unit=lembur&err=Gagal menyimpan data dalam database!');
    		exit;
        // echo "Error: " . mysqli_error($config); // Debugging error
    }
}
?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>CATATAN KEGIATAN LEMBUR STAFF IT</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="main_staff.php?unit=beranda">Home</a></li>
          <li class="breadcrumb-item active">Lembur</li>
        </ol>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
		<div class="card">
		    <div class="card-header">
			    <div class="card-tools" style="float: right; text-align: right;">
                  <a href="#" class="btn btn-tool btn-sm" data-card-widget="collapse" style="background:rgba(69, 77, 85, 1)">
                        <i class="fas fa-bars"></i>
                  </a>
              	</div>
			</div>
		    <!-- /.card-header -->
		    <div class="card-body">
		        <table id="example1" class="table table-bordered table-striped">
		          	<thead style="background:rgb(52, 58, 64, 1); color:white;">
						<tr>
							<th style="text-align: center; color: white;">No.</th>
							<th style="text-align: center; color: white;">Nama</th>
							<th style="text-align: center; color: white;">Tanggal Lembur</th>
							<th style="text-align: center; color: white;">Status Lembur</th>
							<th style="text-align: center; color: white;">Waktu Input</th>
							<th style="text-align: center; color: white;">Aksi</th>
						</tr>
		          	</thead>
		          	<tbody>
		          	<?php
					$id_staff = $_SESSION['id_user'];
					// Query join tb_lembur dan tb_user untuk ambil nama staff dan foto
					$query    = mysqli_query($config,"SELECT a.*, b.nama_lengkap, b.foto FROM tb_lembur a LEFT JOIN tb_user b ON a.id_staff = b.id_user ORDER BY a.tanggal_lembur DESC")or die(mysqli_error($config));
		          	$n        = 1;
						while ($data=mysqli_fetch_array($query)) {
						$nn=$n++;
		          	?>
						<tr>
							<td align="center"><?= $nn; ?>.</td>
							<td><?= $data['nama_lengkap'] ?></td>
							<td><?= date('d-m-Y', strtotime($data['tanggal_lembur'])) ?></td>
							<td align="center">
								<?php
								if ($data['status_lembur'] == 'Diterima') {
									echo '<span class="badge badge-success">Diterima</span>';
								} elseif ($data['status_lembur'] == 'Ditolak') {
									echo '<span class="badge badge-danger">Ditolak</span>';
								} elseif ($data['status_lembur'] == 'Menunggu') {
									echo '<span class="badge badge-warning" style="color:#212529;">Menunggu</span>';
								} else {
									echo $data['status_lembur'];
								}
								?>
							</td>
							<td><?= $data['waktu_input'] ?></td>
							<td align="center">
									<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalDetail<?= $data['id_lembur'] ?>">
											<i class="fa fa-eye"></i> Detail
									</button>
									<?php if ($data['status_lembur'] == 'Menunggu') { ?>
											<a href="dashboard_admin.php?unit=lembur&verifikasi=<?= $data['id_lembur'] ?>" class="btn btn-success btn-sm" title="Verifikasi"><i class="fa fa-check"></i> Verifikasi</a>
											<a href="dashboard_admin.php?unit=lembur&tolak=<?= $data['id_lembur'] ?>" class="btn btn-danger btn-sm" title="Tolak"><i class="fa fa-times"></i> Tolak</a>
									<?php } ?>


										<!-- Modal Detail -->
										<div class="modal fade" id="modalDetail<?= $data['id_lembur'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel<?= $data['id_lembur'] ?>" aria-hidden="true">
											<div class="modal-dialog modal-lg" role="document">
												<div class="modal-content" style="border-radius:15px;">
													<div class="modal-header" style="background:linear-gradient(90deg,#007bff,#17a2b8);color:white;">
														<h5 class="modal-title" id="modalDetailLabel<?= $data['id_lembur'] ?>"><i class="fa fa-info-circle"></i> Detail Lembur</h5>
														<button type="button" class="close" data-dismiss="modal" aria-label="Close">
															<span aria-hidden="true">&times;</span>
														</button>
													</div>
													<div class="modal-body" style="background:#f8f9fa;">
														<div class="row">
															<div class="col-md-3 mb-2">
																<?php
																	$foto = !empty($data['foto']) ? $data['foto'] : 'default.png';
																	$fotoPath = "../assets/img/" . $foto;
																?>
																<img src="<?= $fotoPath ?>" alt="Foto User" class="img-fluid rounded-circle shadow" style="width:120px;height:120px;object-fit:cover;border:4px solid #007bff;">
																<div class="mt-2"><span style="color:#007bff;font-weight:bold;">Foto</span></div>
															</div>
															<div class="col-md-9">
																<div class="row">
																	<div class="col-md-6 mb-2">
																		<strong>Nama Staff:</strong><br>
																		<span class="text-primary"><?= $data['nama_lengkap'] ?></span>
																	</div>
																	<div class="col-md-6 mb-2">
																		<strong>Tanggal Lembur:</strong><br>
																		<span><?= date('d-m-Y', strtotime($data['tanggal_lembur'])) ?></span>
																	</div>
																	<div class="col-md-6 mb-2">
																		<strong>Status Lembur:</strong><br>
																		<?php
																		if ($data['status_lembur'] == 'Diterima') {
																			echo '<span class="badge badge-success">Diterima</span>';
																		} elseif ($data['status_lembur'] == 'Ditolak') {
																			echo '<span class="badge badge-danger">Ditolak</span>';
																		} elseif ($data['status_lembur'] == 'Menunggu') {
																			echo '<span class="badge badge-warning" style="color:#212529;">Menunggu</span>';
																		} else {
																			echo $data['status_lembur'];
																		}
																		?>	
																	</div>
																	<div class="col-md-6 mb-2">
																		<strong>Pimpinan:</strong><br>
																		<span><?= $data['id_pimpinan'] ?></span>
																	</div>
																	<div class="col-md-6 mb-2">
																		<strong>Waktu Input:</strong><br>
																		<span><?= $data['waktu_input'] ?></span>
																	</div>
																</div>
																<div class="row mt-3">
																	<div class="col-12">
																		<strong>Kegiatan Lembur:</strong>
																		<div class="card shadow-sm" style="border-radius:10px;">
																			<div class="card-body p-2">
																				<?php
																				$qKegiatan = mysqli_query($config, "SELECT kegiatan FROM tb_kegiatan_lembur WHERE id_lembur='".$data['id_lembur']."'");
																				if (mysqli_num_rows($qKegiatan) > 0) {
																					echo '<ul class="list-group list-group-flush">';
																					while ($keg = mysqli_fetch_assoc($qKegiatan)) {
																						echo '<li class="list-group-item">'.htmlspecialchars($keg['kegiatan']).'</li>';
																					}
																					echo '</ul>';
																				} else {
																					echo '<span class="text-muted">Tidak ada kegiatan lembur.</span>';
																				}
																				?>
																			</div>
																		</div>
																	</div>
																</div>
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
			        <?php }//end while?>
		        	</tbody>
		        </table>
		    </div>
		    <!-- /.card-body -->
		</div>
		<!-- /.card -->
	</div>
  </div>
</div>
</section>

