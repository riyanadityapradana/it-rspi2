<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>CATATAN KEGIATAN LEMBUR STAFF IT</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="dashboard_staff.php?unit=beranda">Home</a></li>
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
		    	<div class="card-tools" style="float: left; text-align: left;">
                  <a href="?unit=create_lembur" class="btn btn-tool btn-sm" style="background:rgba(0, 123, 255, 1)">
                    <i class="fas fa-plus-square" style="color: white;"> Tambah Data</i>
                  </a>
              	</div>
			    <div class="card-tools" style="float: right; text-align: right;">
                  <a href="#" class="btn btn-tool btn-sm" data-card-widget="collapse" style="background:rgba(69, 77, 85, 1)">
                        <i class="fas fa-bars"></i>
                  </a>
              	</div>
			</div>
		    <!-- /.card-header -->
		    <div class="card-body">
		        <table id="example" class="table table-bordered table-striped">
		          	<thead style="background:rgb(129, 2, 0, 1)">
						<tr>
							<th style="text-align: center; color: white;">No.</th>
							<th style="text-align: center; color: white;">Nama</th>
							<th style="text-align: center; color: white;">Jabatan</th>
							<th style="text-align: center; color: white;">Tanggal</th>
							<th style="text-align: center; color: white;">Status</th>
							<th style="text-align: center; color: white;">Aksi</th>
						</tr>
		          	</thead>
		          	<tbody>
		          	<?php
					$id_staff = $_SESSION['id_user'];
		          	$query    = mysqli_query($config,"SELECT * FROM tb_lembur a, tb_user b WHERE a.id_staff = b.id_user AND id_staff = '$id_staff' ORDER BY tanggal_lembur DESC")or die(mysqli_error($config));
		          	$n        = 1;
						while ($data=mysqli_fetch_array($query)) {
						$nn=$n++;
		          	?>
						<tr>
							<td align="center"><?= $nn; ?>.</td>
							<td><?= $data['nama_lengkap'] ?></td>
							<td><?= $data['role'] ?></td>
							<td><?= date('d-m-Y', strtotime($data['tanggal_lembur'])) ?></td>
							<td><?= $data['status_lembur'] ?></td>
							<td align="center">
								<?php if ($data['status_lembur'] == 'Menunggu') : ?>
									<a href="?unit=detail_lembur&id=<?= $data['id_lembur'] ?>" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> Detail</a>
									<a href="?unit=delete_lembur&id=<?= $data['id_lembur'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')"><i class="fa fa-trash"></i> Hapus</a>
									<button type="button" class="btn btn-success btn-sm ml-2" style="margin-left:10px;" data-toggle="modal" data-target="#modalWa" 
										onclick="setLemburData('<?= $data['id_lembur'] ?>', '<?= $data['nama_lengkap'] ?>', '<?= $data['role'] ?>', '<?= date('d-m-Y', strtotime($data['tanggal_lembur'])) ?>')">
										<i class="fab fa-whatsapp"></i> Kirim Via WA
									</button>
								<?php elseif ($data['status_lembur'] == 'Ditolak') : ?>
									<a href="?unit=detail_lembur&id=<?= $data['id_lembur'] ?>" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> Detail</a>
								<?php elseif ($data['status_lembur'] == 'Diterima') : ?>
									<a href="unit/lembur/cetak_lembur.php?id=<?= $data['id_lembur'] ?>" target="_blank" class="btn btn-success btn-sm"><i class="fa fa-print"></i> Cetak</a>
								<?php else : ?>
									<span class="text-muted">Status tidak diketahui</span>
								<?php endif; ?>
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
