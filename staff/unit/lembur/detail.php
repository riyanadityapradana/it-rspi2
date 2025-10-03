<?php
	if (!isset($_GET['id'])) {
		echo "<script>alert('ID lembur tidak ditemukan!'); window.history.back();</script>";
		exit;
	}

	$id_lembur = $_GET['id'];

	// Ambil data lembur
	$queryLembur = mysqli_query($config, "SELECT l.*, s.nama_lengkap, s.nip, s.role FROM tb_lembur l 
		LEFT JOIN tb_user s ON l.id_staff = s.id_user
		WHERE l.id_lembur = '$id_lembur'");
	$dataLembur = mysqli_fetch_assoc($queryLembur);

	// Ambil data kegiatan
	$queryKegiatan = mysqli_query($config, "SELECT * FROM tb_kegiatan_lembur WHERE id_lembur = '$id_lembur'");
	
	// Jika sudah dikonfirmasi, ambil info pimpinan
	$dataPimpinan = null;
	if (!empty($dataLembur['id_pimpinan'])) {
		$queryPimpinan = mysqli_query($config, "SELECT nama_lengkap, role FROM tb_user WHERE id_user = '{$dataLembur['id_pimpinan']}'");
		$dataPimpinan = mysqli_fetch_assoc($queryPimpinan);
	}
?>
	<link rel="stylesheet" href="../assets/dist/css/bootstrap.min.css">
	<section class="content-header">
		<!-- [Header sama seperti punyamu] -->
	</section>
	<section class="content">
		<div class="container-fluid">
			<div class="card card-default">
				<div class="card-header">
					<h3 class="card-title">Berikut Detail Data lembur Karyawan</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<!-- TABEL KIRI -->
						<div class="col-md-6">
							<table class="table table-bordered">
								<tr>
									<th width="150">ID Lembur</th>
									<th width="10">:</th>
									<td><?= $dataLembur['id_lembur']; ?></td>
								</tr>
								<tr>
									<th>Nama Staff</th>
									<th>:</th>
									<td><?= $dataLembur['nama_lengkap']; ?></td>
								</tr>
								<tr>
									<th>NIP</th>
									<th>:</th>
									<td><?= $dataLembur['nip']; ?></td>
								</tr>
								<tr>
									<th>Jabatan</th>
									<th>:</th>
									<td><?= $dataLembur['role']; ?></td>
								</tr>
								<tr>
									<th>Tanggal</th>
									<th>:</th>
									<td><?= date('d-m-Y', strtotime($dataLembur['tanggal_lembur'])); ?></td>
								</tr>
								<tr>
									<th>Jam Mulai</th>
									<th>:</th>
									<td><?= date('H:i', strtotime($dataLembur['jam_mulai'])); ?></td>
								</tr>
								<tr>
									<th>Jam Selesai</th>
									<th>:</th>
									<td><?= date('H:i', strtotime($dataLembur['jam_selesai'])); ?></td>
								</tr>
								<tr>
									<th>Status</th>
									<th>:</th>
									<td>
									<?php
										$status = $dataLembur['status_lembur'];
										if ($status == 'Menunggu') {
											echo '<span class="badge badge-warning" style="font-size:1em;">' . $status . '</span>';
										} elseif ($status == 'Diterima') {
											echo '<span class="badge badge-success" style="font-size:1em;">' . $status . '</span>';
										} elseif ($status == 'Ditolak') {
											echo '<span class="badge badge-danger" style="font-size:1em;">' . $status . '</span>';
										} else {
											echo '<span class="badge badge-secondary" style="font-size:1em;">' . $status . '</span>';
										}
									?>
									</td>
								</tr>
								<?php if ($dataPimpinan): ?>
								<tr>
									<th>Diproses Oleh</th><td>:</td><td><?= $dataPimpinan['nama_lengkap']; ?> (<?= $dataPimpinan['role']; ?>)</td>
								</tr>
								<?php endif; ?>
							</table>
						</div>
						<!-- TABEL KANAN -->
						<div class="col-md-6">
							<h5 class="mt-4">Daftar Kegiatan Lembur:</h5>
							<ul class="list-group">
								<?php while ($k = mysqli_fetch_assoc($queryKegiatan)) : ?>
									<li class="list-group-item">- <?= htmlspecialchars($k['kegiatan']); ?></li>
								<?php endwhile; ?>
							</ul>
							<a href="dashboard_staff.php?unit=lembur" class="btn btn-warning mt-4">Kembali</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
