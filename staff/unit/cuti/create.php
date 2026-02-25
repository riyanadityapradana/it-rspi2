
<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
// asumsi: $config (koneksi mysqli) tersedia di scope pemanggil
if (!isset($_SESSION['id_user'])) {
		echo '<script>window.location="?unit=login";</script>';
		exit;
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
	$id_user = mysqli_real_escape_string($config, $_SESSION['id_user']);
	$nip = mysqli_real_escape_string($config, isset($_SESSION['nip']) ? $_SESSION['nip'] : '');
	$mulai_tanggal = mysqli_real_escape_string($config, $_POST['mulai_tanggal']);
	$sampai_tanggal = mysqli_real_escape_string($config, $_POST['sampai_tanggal']);
	$masuk_tanggal = mysqli_real_escape_string($config, $_POST['masuk_tanggal']);
		$alasan = mysqli_real_escape_string($config, isset($_POST['alasan'])?$_POST['alasan']: '');
	$status = 'Menunggu';

	// Hitung banyak_hari secara server-side (inklusif)
	$diff_seconds = strtotime($sampai_tanggal) - strtotime($mulai_tanggal);
	$banyak_hari = ($diff_seconds !== false) ? intval(floor($diff_seconds / 86400) + 1) : 0;

	// Basic validation
		if (empty($mulai_tanggal) || empty($sampai_tanggal) || empty($masuk_tanggal) || $banyak_hari <= 0) {
		$error = 'Lengkapi semua field dengan benar.';
	} else {
			$sql = "INSERT INTO tb_cuti (id_user, nip, banyak_hari, mulai_tanggal, sampai_tanggal, masuk_tanggal, alasan, status_lembur) VALUES ('{$id_user}','{$nip}','{$banyak_hari}','{$mulai_tanggal}','{$sampai_tanggal}','{$masuk_tanggal}','{$alasan}','{$status}')";
		$insert = mysqli_query($config, $sql) or die(mysqli_error($config));
		if ($insert) {
			echo '<script>window.location.href="?unit=cuti&msg=created";</script>';
			exit;
		} else {
			$error = 'Gagal menyimpan data.';
		}
	}
}
?>

<!-- Content Header -->
<section class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1>Tambah Pengajuan Cuti</h1>
			</div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="dashboard_staff.php?unit=beranda">Home</a></li>
					<li class="breadcrumb-item"><a href="?unit=cuti">Cuti</a></li>
					<li class="breadcrumb-item active">Tambah</li>
				</ol>
			</div>
		</div>
	</div>
</section>

<!-- Main content -->
<section class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="card card-primary">
					<div class="card-header"><h3 class="card-title">Form Pengajuan Cuti</h3></div>
					<form method="post">
						<div class="card-body">
							<?php if (isset($error)): ?>
								<div class="alert alert-danger"><?php echo $error; ?></div>
							<?php endif; ?>
							<div class="form-group">
								<label>NIP</label>
								<input type="text" class="form-control" value="<?php echo isset($_SESSION['nip'])?htmlspecialchars($_SESSION['nip']):''; ?>" readonly>
							</div>
							<div class="form-group">
								<label>Banyak Hari</label>
								<input type="number" id="banyak_hari" name="banyak_hari" class="form-control" min="0" value="<?php echo isset($_POST['banyak_hari'])?htmlspecialchars($_POST['banyak_hari']):'1'; ?>" readonly required>
							</div>
							<div class="form-group">
								<label>Mulai Tanggal</label>
								<input type="date" name="mulai_tanggal" class="form-control" value="<?php echo isset($_POST['mulai_tanggal'])?htmlspecialchars($_POST['mulai_tanggal']):''; ?>" required>
							</div>
							<div class="form-group">
								<label>Sampai Tanggal</label>
								<input type="date" name="sampai_tanggal" class="form-control" value="<?php echo isset($_POST['sampai_tanggal'])?htmlspecialchars($_POST['sampai_tanggal']):''; ?>" required>
							</div>
							<div class="form-group">
								<label>Masuk Tanggal</label>
								<input type="date" name="masuk_tanggal" class="form-control" value="<?php echo isset($_POST['masuk_tanggal'])?htmlspecialchars($_POST['masuk_tanggal']):''; ?>" required>
							</div>
							<div class="form-group">
								<label>Alasan</label>
								<textarea name="alasan" class="form-control" rows="3"><?php echo isset($_POST['alasan'])?htmlspecialchars($_POST['alasan']):''; ?></textarea>
							</div>
						</div>
						<div class="card-footer">
							<button type="submit" name="save" class="btn btn-primary">Simpan</button>
							<a href="?unit=cuti" class="btn btn-secondary">Batal</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>
<script>
function calcDaysCreate(){
	var mulai = document.querySelector('input[name="mulai_tanggal"]').value;
	var sampai = document.querySelector('input[name="sampai_tanggal"]').value;
	var banyak = document.getElementById('banyak_hari');
	if (mulai && sampai){
		var d1 = new Date(mulai);
		var d2 = new Date(sampai);
		var diff = Math.floor((d2 - d1)/(1000*60*60*24)) + 1;
		if (isNaN(diff) || diff < 0) diff = 0;
		banyak.value = diff;
	}
}
var startInput = document.querySelector('input[name="mulai_tanggal"]');
var endInput = document.querySelector('input[name="sampai_tanggal"]');
if (startInput && endInput){
	startInput.addEventListener('change', calcDaysCreate);
	endInput.addEventListener('change', calcDaysCreate);
	window.addEventListener('load', calcDaysCreate);
}
</script>
