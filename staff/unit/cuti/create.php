<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
// asumsi: $config (koneksi mysqli) tersedia di scope pemanggil
require_once("../config/telegram.php");
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
	$alasan = mysqli_real_escape_string($config, isset($_POST['alasan']) ? $_POST['alasan'] : '');
	$jenis_cuti = mysqli_real_escape_string($config, isset($_POST['jenis_cuti']) ? $_POST['jenis_cuti'] : '');
	$status = 'Menunggu';

	// Hitung banyak_hari secara server-side (inklusif)
	$diff_seconds = strtotime($sampai_tanggal) - strtotime($mulai_tanggal);
	$banyak_hari = ($diff_seconds !== false) ? intval(floor($diff_seconds / 86400) + 1) : 0;

	// Basic validation
	if (empty($mulai_tanggal) || empty($sampai_tanggal) || empty($masuk_tanggal) || $banyak_hari <= 0) {
		$error = 'Lengkapi semua field dengan benar.';
	} else {
		$sql = "INSERT INTO tb_cuti (id_user, nip, banyak_hari, mulai_tanggal, sampai_tanggal, masuk_tanggal, alasan, jenis_cuti, status_lembur) VALUES ('{$id_user}','{$nip}','{$banyak_hari}','{$mulai_tanggal}','{$sampai_tanggal}','{$masuk_tanggal}','{$alasan}','{$jenis_cuti}','{$status}');";

		$insert = mysqli_query($config, $sql) or die(mysqli_error($config));
		if ($insert) {
			$id_cuti = mysqli_insert_id($config);
			$nama_staff = isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : 'Staff IT';
			$pesan = "PENGAJUAN CUTI BARU\n";
			$pesan .= "ID Cuti: " . $id_cuti . "\n";
			$pesan .= "Nama: " . $nama_staff . "\n";
			$pesan .= "NIP: " . ($nip !== '' ? $nip : '-') . "\n";
			$pesan .= "Jenis Cuti: " . ($jenis_cuti !== '' ? $jenis_cuti : '-') . "\n";
			$pesan .= "Mulai: " . date('d-m-Y', strtotime($mulai_tanggal)) . "\n";
			$pesan .= "Sampai: " . date('d-m-Y', strtotime($sampai_tanggal)) . "\n";
			$pesan .= "Masuk: " . date('d-m-Y', strtotime($masuk_tanggal)) . "\n";
			$pesan .= "Banyak Hari: " . $banyak_hari . "\n";
			$pesan .= "Alasan: " . ($alasan !== '' ? $alasan : '-') . "\n";
			$pesan .= "Status: Menunggu";
			telegram_send_channel_message($pesan);
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
</section>

<!-- Main content -->
<section class="content">
	<div class="container-fluid">
		<?php
		$today = date('Y-m-d');
		$mulaiTanggalValue = isset($_POST['mulai_tanggal']) ? htmlspecialchars($_POST['mulai_tanggal']) : $today;
		$sampaiTanggalValue = isset($_POST['sampai_tanggal']) ? htmlspecialchars($_POST['sampai_tanggal']) : $today;
		$masukTanggalValue = isset($_POST['masuk_tanggal']) ? htmlspecialchars($_POST['masuk_tanggal']) : $today;
		?>
		<div class="card card-default">
			<div class="card-header">
				<h3 class="card-title">Silahkan Isi Form Pengajuan Cuti</h3>
			</div>
			<form method="post" enctype="multipart/form-data">
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
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label>Mulai Tanggal</label>
								<input type="date" name="mulai_tanggal" class="form-control" value="<?php echo $mulaiTanggalValue; ?>" required>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Sampai Tanggal</label>
								<input type="date" name="sampai_tanggal" class="form-control" value="<?php echo $sampaiTanggalValue; ?>" required>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Masuk Tanggal</label>
								<input type="date" name="masuk_tanggal" class="form-control" value="<?php echo $masukTanggalValue; ?>" required>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label>Jenis Cuti</label>
						<select name="jenis_cuti" id="jenis_cuti" class="form-control select2" required>
							<option value="">-- Pilih Jenis Cuti --</option>
							<option value="Izin Cuti Melahirkan" <?php echo (isset($_POST['jenis_cuti']) && $_POST['jenis_cuti']=='Izin Cuti Melahirkan')?'selected':''; ?>>Izin Cuti Melahirkan</option>
							<option value="Izin Cuti Tahunan" <?php echo (isset($_POST['jenis_cuti']) && $_POST['jenis_cuti']=='Izin Cuti Tahunan')?'selected':''; ?>>Izin Cuti Tahunan</option>
							<option value="Izin Cuti Menikah" <?php echo (isset($_POST['jenis_cuti']) && $_POST['jenis_cuti']=='Izin Cuti Menikah')?'selected':''; ?>>Izin Cuti Menikah</option>
						</select>
					</div>
					<div class="form-group">
						<label>Alasan</label>
						<textarea name="alasan" class="form-control" rows="3"><?php echo isset($_POST['alasan'])?htmlspecialchars($_POST['alasan']):''; ?></textarea>
					</div>
				</div>
				<div class="card-footer">
					<a class="btn btn-app bg-warning float-left" href="?unit=cuti">
						<i class="fas fa-reply"></i> Kembali
					</a>
					<button class="btn btn-app bg-success float-right" type="submit" name="save">
						<i class="fas fa-save"></i> SIMPAN
					</button>
				</div>
			</form>
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
	} else {
		banyak.value = 0;
	}
}
var startInput = document.querySelector('input[name="mulai_tanggal"]');
var endInput = document.querySelector('input[name="sampai_tanggal"]');
if (startInput && endInput){
	startInput.addEventListener('change', calcDaysCreate);
	endInput.addEventListener('change', calcDaysCreate);
	window.addEventListener('load', calcDaysCreate);
}
$(document).ready(function() {
	$('#jenis_cuti').select2({
		placeholder: '-- Pilih Jenis Cuti --',
		allowClear: true
	});
});
</script>
