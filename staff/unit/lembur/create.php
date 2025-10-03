<?php
	require_once("../config/koneksi.php"); // Sesuaikan path ke koneksi

	// Proses Simpan
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$tanggal_lembur = $_POST['tanggal_lembur'];
		$jam_mulai      = $_POST['jam_mulai'];
		$jam_selesai    = $_POST['jam_selesai'];
		$id_staff       = $_SESSION['id_user'];
		$kegiatan_array = $_POST['kegiatan'];

		// Simpan ke tb_lembur
		$insertLembur = mysqli_query($config, "INSERT INTO tb_lembur (id_staff, tanggal_lembur, jam_mulai, jam_selesai) VALUES ('$id_staff', '$tanggal_lembur', '$jam_mulai', '$jam_selesai')");

		if ($insertLembur) {
			$id_lembur = mysqli_insert_id($config); // Dapatkan id lembur yang baru dibuat
			// Simpan semua kegiatan
			foreach ($kegiatan_array as $kegiatan) {
				if (!empty(trim($kegiatan))) {
					mysqli_query($config, "INSERT INTO tb_kegiatan_lembur (id_lembur, kegiatan) VALUES ('$id_lembur', '$kegiatan')");
				}
			}
			header('Location: dashboard_staff.php?unit=lembur&msg=Data Lembur berhasil Diserahkan!');
			exit;
		} else {
			header('Location: dashboard_staff.php?unit=lembur&err=Gagal menyerahkan lembur!');
			exit;
		}
	}
?>
	<link rel="stylesheet" href="../assets/dist/css/bootstrap.min.css">
    <script src="../assets/dist/js/jquery.min.js"></script>
	<!-- HTML Form -->
	<section class="content-header">
	  <!-- [Header sama seperti punyamu] -->
	</section>
	<section class="content">
		<div class="container-fluid">
			<div class="card card-default">
				<div class="card-header">
					<h3 class="card-title">Silahkan Input Kerja Lembur Staff IT</h3>
				</div>
				<form method="post" enctype="multipart/form-data">
					<div class="card-body">
						<div class="form-group">
							<label>Tanggal Lembur</label>
							<input type="date" class="form-control" name="tanggal_lembur" value="<?= date('Y-m-d'); ?>">
						</div>
						<div class="form-row">
							<div class="form-group col-md-6">
								<label>Jam Mulai</label>
								<input type="time" class="form-control" name="jam_mulai" required>
							</div>
							<div class="form-group col-md-6">
								<label>Jam Selesai</label>
								<input type="time" class="form-control" name="jam_selesai" required>
							</div>
						</div>

						<div class="form-group">
							<label>Kegiatan Lembur</label>
							<div id="kegiatan-container">
								<div class="input-group mb-2 kegiatan-row">
									<input type="text" name="kegiatan[]" class="form-control" placeholder="Masukkan kegiatan..." required>
									<div class="input-group-append">
										<button type="button" class="btn btn-danger btn-remove">Hapus</button>
									</div>
								</div>
							</div>
							<button type="button" class="btn btn-sm btn-primary" id="btn-tambah-kegiatan">Tambah Kegiatan</button>
						</div>
					</div>
					<div class="card-footer">
						<a class="btn btn-app bg-warning float-left" href="dashboard_staff.php?unit=logbook&action=datagrid"><i class="fas fa-reply"></i> Back</a>
						<button class="btn btn-app bg-success float-right" type="submit"><i class="fas fa-save"></i> SAVE</button>
					</div>
				</form>
			</div>
		</div>
	</section>
	<script>
		$(document).ready(function () {
			// Tambah baris kegiatan
			$('#btn-tambah-kegiatan').on('click', function () {
				let html = `
				<div class="input-group mb-2 kegiatan-row">
					<input type="text" name="kegiatan[]" class="form-control" placeholder="Masukkan kegiatan..." required>
					<div class="input-group-append">
						<button type="button" class="btn btn-danger btn-remove">Hapus</button>
					</div>
				</div>`;
				$('#kegiatan-container').append(html);
			});

			// Hapus baris kegiatan
			$(document).on('click', '.btn-remove', function () {
				$(this).closest('.kegiatan-row').remove();
			});
		});
	</script>
