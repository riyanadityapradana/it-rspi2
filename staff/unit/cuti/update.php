
<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (!isset($_SESSION['id_user'])) {
		echo '<script>window.location="?unit=login";</script>';
		exit;
}

if (!isset($_GET['id'])) {
		echo '<script>window.location="?unit=cuti";</script>';
		exit;
}

$id = intval($_GET['id']);

$query = mysqli_query($config, "SELECT c.*, u.nip, u.nama_lengkap FROM tb_cuti c JOIN tb_user u ON c.id_user=u.id_user WHERE c.id_cuti='$id'") or die(mysqli_error($config));
$data = mysqli_fetch_array($query);
if (!$data) {
		echo '<script>window.location="?unit=cuti&msg=notfound";</script>';
		exit;
}

$id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : '';
$allowEdit = ($data['id_user'] == $id_user) || (isset($_SESSION['nip']) && $_SESSION['nip'] === '662.140725' && strtolower($_SESSION['role']) === 'staff');
if (!$allowEdit) {
		echo '<script>alert("Anda tidak berwenang mengubah data ini.");window.location="?unit=cuti";</script>';
		exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
	$mulai_tanggal = mysqli_real_escape_string($config, $_POST['mulai_tanggal']);
	$sampai_tanggal = mysqli_real_escape_string($config, $_POST['sampai_tanggal']);
	$masuk_tanggal = mysqli_real_escape_string($config, $_POST['masuk_tanggal']);
	$alasan = mysqli_real_escape_string($config, isset($_POST['alasan'])?$_POST['alasan']:'');

	// Hitung banyak_hari server-side (inklusif)
	$diff_seconds = strtotime($sampai_tanggal) - strtotime($mulai_tanggal);
	$banyak_hari = ($diff_seconds !== false) ? intval(floor($diff_seconds / 86400) + 1) : 0;

	if ($banyak_hari <= 0 || empty($mulai_tanggal) || empty($sampai_tanggal) || empty($masuk_tanggal)) {
		$error = 'Lengkapi semua field dengan benar.';
	} else {
		$sql = "UPDATE tb_cuti SET banyak_hari='{$banyak_hari}', mulai_tanggal='{$mulai_tanggal}', sampai_tanggal='{$sampai_tanggal}', masuk_tanggal='{$masuk_tanggal}', alasan='{$alasan}' WHERE id_cuti='{$id}'";
		$update = mysqli_query($config, $sql) or die(mysqli_error($config));
		if ($update) {
			echo '<script>window.location.href="?unit=cuti&msg=updated";</script>';
			exit;
		} else {
			$error = 'Gagal memperbarui data.';
		}
	}
}
?>

<!-- Content Header -->
<section class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"><h1>Ubah Pengajuan Cuti</h1></div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="?unit=cuti">Cuti</a></li>
					<li class="breadcrumb-item active">Ubah</li>
				</ol>
			</div>
		</div>
	</div>
</section>

<section class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="card card-primary">
					<div class="card-header"><h3 class="card-title">Form Ubah</h3></div>
					<form method="post">
						<div class="card-body">
							<?php if (isset($error)): ?>
								<div class="alert alert-danger"><?php echo $error; ?></div>
							<?php endif; ?>
							<div class="form-group">
								<label>NIP</label>
								<input type="text" class="form-control" value="<?php echo htmlspecialchars($data['nip']); ?>" readonly>
							</div>
							<div class="form-group">
								<label>Banyak Hari</label>
								<input type="number" id="banyak_hari" name="banyak_hari" class="form-control" min="0" value="<?php echo htmlspecialchars($data['banyak_hari']); ?>" readonly required>
							</div>
							<div class="form-group">
								<label>Mulai Tanggal</label>
								<input type="date" name="mulai_tanggal" class="form-control" value="<?php echo htmlspecialchars($data['mulai_tanggal']); ?>" required>
							</div>
							<div class="form-group">
								<label>Sampai Tanggal</label>
								<input type="date" name="sampai_tanggal" class="form-control" value="<?php echo htmlspecialchars($data['sampai_tanggal']); ?>" required>
							</div>
							<div class="form-group">
								<label>Masuk Tanggal</label>
								<input type="date" name="masuk_tanggal" class="form-control" value="<?php echo htmlspecialchars($data['masuk_tanggal']); ?>" required>
							</div>
							<div class="form-group">
								<label>Alasan</label>
								<textarea name="alasan" class="form-control" rows="3"><?php echo htmlspecialchars(isset($data['alasan'])?$data['alasan']:''); ?></textarea>
							</div>
						</div>
						<div class="card-footer">
							<button type="submit" name="save" class="btn btn-primary">Simpan Perubahan</button>
							<a href="?unit=cuti" class="btn btn-secondary">Batal</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>
<script>
function calcDaysUpdate(){
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
var sIn = document.querySelector('input[name="mulai_tanggal"]');
var eIn = document.querySelector('input[name="sampai_tanggal"]');
if (sIn && eIn){
	sIn.addEventListener('change', calcDaysUpdate);
	eIn.addEventListener('change', calcDaysUpdate);
	window.addEventListener('load', calcDaysUpdate);
}
</script>
