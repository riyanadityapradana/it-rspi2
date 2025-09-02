<!-- Content Header (Page header) -->
<?php
// Koneksi ke database (hardcode, sesuaikan jika perlu)
$host = '192.168.1.4';
$user = 'root';
$pass = '';
$db   = 'sik9';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die('Koneksi gagal: ' . $conn->connect_error);
$conn->set_charset('utf8');
?>
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>UPDATE DATA RESEP OBAT</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="dashboard_staff.php?unit=beranda">Home</a></li>
          <li class="breadcrumb-item active">Update Resep Obat</li>
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
		    	<!-- <div class="card-tools" style="float: left; text-align: left;"> 
                  <a href="?unit=create_lembur" class="btn btn-tool btn-sm" style="background:rgba(0, 123, 255, 1)">
                    <i class="fas fa-plus-square" style="color: white;"> Tambah Data</i>
                  </a>
              	</div>-->
			    <div class="card-tools" style="float: right; text-align: right;">
                  <a href="#" class="btn btn-tool btn-sm" data-card-widget="collapse" style="background:rgba(69, 77, 85, 1)">
                        <i class="fas fa-bars"></i>
                  </a>
              	</div>
			</div>
		    <!-- /.card-header -->
		    <div class="card-body">
				<!-- Tabel Resep Obat -->
				<h3 class="mt-4 mb-2">Tabel Resep Obat Hari Ini</h3>
				<table id="example1" class="table table-bordered table-striped">
					<thead style="background:rgb(2, 129, 0, 1)">
						<tr>
							<th style="text-align: center; color: white;">No.</th>
							<th style="text-align: center; color: white;">No. Resep</th>
							<th style="text-align: center; color: white;">Tgl Perawatan</th>
							<th style="text-align: center; color: white;">Jam</th>
							<th style="text-align: center; color: white;">No. Rawat</th>
							<th style="text-align: center; color: white;">Kode Dokter</th>
							<th style="text-align: center; color: white;">Tgl Peresepan</th>
							<th style="text-align: center; color: white;">Jam Peresepan</th>
							<th style="text-align: center; color: white;">Status</th>
							<th style="text-align: center; color: white;">Tgl Penyerahan</th>
							<th style="text-align: center; color: white;">Jam Penyerahan</th>
							<!-- <th style="text-align: center; color: white;">Aksi</th> -->
						</tr>
					</thead>
					<tbody>
					<?php
					$tanggal = str_replace('-', '', date('Y-m-d'));
					$sql_resep = "SELECT * FROM resep_obat WHERE no_resep LIKE '%$tanggal%' ORDER BY no_resep DESC";
					$query_resep = $conn->query($sql_resep);
					if(!$query_resep) die('Query error: ' . $conn->error);
					$no = 1;
					while ($resep = mysqli_fetch_array($query_resep)) {
					?>
						<tr>
							<td align="center"><?= $no++; ?>.</td>
							<td class="editable" data-id="<?= $resep['no_resep'] ?>" ondblclick="editNoResep(this)"><?= htmlspecialchars($resep['no_resep']) ?></td>
							<td><?= htmlspecialchars($resep['tgl_perawatan']) ?></td>
							<td><?= htmlspecialchars($resep['jam']) ?></td>
							<td><?= htmlspecialchars($resep['no_rawat']) ?></td>
							<td><?= htmlspecialchars($resep['kd_dokter']) ?></td>
							<td><?= htmlspecialchars($resep['tgl_peresepan']) ?></td>
							<td><?= htmlspecialchars($resep['jam_peresepan']) ?></td>
							<td><?= htmlspecialchars($resep['status']) ?></td>
							<td><?= htmlspecialchars($resep['tgl_penyerahan']) ?></td>
							<td><?= htmlspecialchars($resep['jam_penyerahan']) ?></td>
						</tr>
					<?php } ?>
									</tbody>
									</table>
									<script>
									function editNoResep(cell) {
										var oldValue = cell.textContent;
										var id = cell.getAttribute('data-id');
										cell.innerHTML = '<input type="text" value="' + oldValue + '" style="width:100px;" id="editNoResepInput"> <button onclick="saveNoResep(this, \'' + id + '\')" class="btn btn-success btn-sm">Simpan</button>';
										document.getElementById('editNoResepInput').focus();
									}

									function saveNoResep(btn, oldId) {
										var input = btn.parentNode.querySelector('input');
										var newValue = input.value.trim();
										if(newValue === '') { alert('No. Resep tidak boleh kosong!'); return; }
										// AJAX request
										var xhr = new XMLHttpRequest();
										xhr.open('POST', '/it-rspi/staff/unit/rsp-obat-update/edit_no_resep.php', true);
										xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
										xhr.onreadystatechange = function() {
											if(xhr.readyState === 4) {
												if(xhr.status === 200) {
													if(xhr.responseText === 'OK') {
														btn.parentNode.innerHTML = newValue;
													} else {
														alert('Gagal update: ' + xhr.responseText);
														btn.parentNode.innerHTML = oldId;
													}
												} else {
													alert('Error koneksi!');
													btn.parentNode.innerHTML = oldId;
												}
											}
										};
										xhr.send('old_id=' + encodeURIComponent(oldId) + '&new_id=' + encodeURIComponent(newValue));
									}
									</script>
							<script>
							function editNoResep(cell) {
								var oldValue = cell.textContent;
								var id = cell.getAttribute('data-id');
								cell.innerHTML = '<input type="text" value="' + oldValue + '" style="width:100px;" id="editNoResepInput"> <button onclick="saveNoResep(this, \'' + id + '\')" class="btn btn-success btn-sm">Simpan</button>';
								document.getElementById('editNoResepInput').focus();
							}

							function saveNoResep(btn, oldId) {
								var input = btn.parentNode.querySelector('input');
								var newValue = input.value.trim();
								if(newValue === '') { alert('No. Resep tidak boleh kosong!'); return; }
								// AJAX request
								var xhr = new XMLHttpRequest();
								xhr.open('POST', 'edit_no_resep.php', true);
								xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
								xhr.onreadystatechange = function() {
									if(xhr.readyState === 4) {
										if(xhr.status === 200) {
											if(xhr.responseText === 'OK') {
												btn.parentNode.innerHTML = newValue;
											} else {
												alert('Gagal update: ' + xhr.responseText);
												btn.parentNode.innerHTML = oldId;
											}
										} else {
											alert('Error koneksi!');
											btn.parentNode.innerHTML = oldId;
										}
									}
								};
								xhr.send('old_id=' + encodeURIComponent(oldId) + '&new_id=' + encodeURIComponent(newValue));
							}
							</script>
		    </div>
		    <!-- /.card-body -->
		</div>
		<!-- /.card -->
	</div>
  </div>
</div>
<!-- Modal Kirim WA -->
<div class="modal fade" id="modalWa" tabindex="-1" role="dialog" aria-labelledby="modalWaLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
		<h5 class="modal-title" id="modalWaLabel">Kirim Notifikasi Lemburan via WhatsApp</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		</div>
		<div class="modal-body">
		<form id="formWa">
			<div class="form-group">
			<label>Nomor WhatsApp Admin (format: 08xxxxxxxxxx)</label>
			<input type="text" class="form-control" id="waNumber" placeholder="Masukkan nomor WhatsApp admin" required>
			</div>
			<div class="form-group">
			<label>Detail Lemburan:</label>
			<div class="alert alert-info">
				<strong>ID Lemburan:</strong> <span id="lemburId"></span><br>
				<strong>Nama:</strong> <span id="lemburNama"></span><br>
				<strong>Jabatan:</strong> <span id="lemburJabatan"></span><br>
				<strong>Tanggal:</strong> <span id="lemburTanggal"></span>
			</div>
			</div>
			<button type="submit" class="btn btn-success">Kirim Pesan Verifikasi</button>
		</form>
		</div>
	</div>
	</div>
</div>
<script>
// Variabel global untuk menyimpan data lemburan
var currentLemburData = {};

// Fungsi untuk mengatur data lemburan saat tombol diklik
function setLemburData(id, nama, jabatan, tanggal) {
	currentLemburData = {
		id: id,
		nama: nama,
		jabatan: jabatan,
		tanggal: tanggal
	};
	
	// Update tampilan detail di modal
	document.getElementById('lemburId').textContent = id;
	document.getElementById('lemburNama').textContent = nama;
	document.getElementById('lemburJabatan').textContent = jabatan;
	document.getElementById('lemburTanggal').textContent = tanggal;
}

document.addEventListener('DOMContentLoaded', function() {
	document.getElementById('formWa').onsubmit = function(e) {
		e.preventDefault();
		
		var no = document.getElementById('waNumber').value.trim();
		if(no === '' || !/^08\d{8,13}$/.test(no)) {
			alert('Nomor WA harus diawali 08 dan hanya angka!');
			return false;
		}
		
		// Validasi data lemburan
		if(!currentLemburData.id) {
			alert('Data lemburan tidak ditemukan!');
			return false;
		}
		
		var no62 = '62' + no.substring(1);
		var pesan = encodeURIComponent(
			'Halo Admin, ada pengajuan lemburan baru yang memerlukan verifikasi:\n\n' +
			'📋 *Detail Lemburan:*\n' +
			'• ID: ' + currentLemburData.id + '\n' +
			'• Nama: ' + currentLemburData.nama + '\n' +
			'• Jabatan: ' + currentLemburData.jabatan + '\n' +
			'• Tanggal: ' + currentLemburData.tanggal + '\n\n' +
			'Mohon segera verifikasi pengajuan lemburan ini di aplikasi ITUTL-RSPI.\n\n' +
			'Terima kasih.'
		);
		
		var url = 'https://wa.me/' + no62 + '?text=' + pesan;
		window.open(url, '_blank');
		$('#modalWa').modal('hide');
		
		// Reset form
		document.getElementById('waNumber').value = '';
		return false;
	};
});
</script>
</section>
