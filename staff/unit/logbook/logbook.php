<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>CATATAN HARIAN KEGIATAN</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="dashboard_staff.php?unit=beranda">Home</a></li>
          <li class="breadcrumb-item active">Logbook</li>
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
                  <a href="?unit=create_logbook" class="btn btn-tool btn-sm" style="background:rgba(0, 123, 255, 1)">
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
		    	<div class="row mb-3">
		    		<div class="col-md-3">
		    			<label>Bulan</label>
		    			<select class="form-control" id="filterBulan" name="bulan">
		    				<option value="">-- Pilih Bulan --</option>
		    				<option value="1" <?php echo (isset($_GET['bulan']) && $_GET['bulan'] == '1') ? 'selected' : ''; ?>>Januari</option>
		    				<option value="2" <?php echo (isset($_GET['bulan']) && $_GET['bulan'] == '2') ? 'selected' : ''; ?>>Februari</option>
		    				<option value="3" <?php echo (isset($_GET['bulan']) && $_GET['bulan'] == '3') ? 'selected' : ''; ?>>Maret</option>
		    				<option value="4" <?php echo (isset($_GET['bulan']) && $_GET['bulan'] == '4') ? 'selected' : ''; ?>>April</option>
		    				<option value="5" <?php echo (isset($_GET['bulan']) && $_GET['bulan'] == '5') ? 'selected' : ''; ?>>Mei</option>
		    				<option value="6" <?php echo (isset($_GET['bulan']) && $_GET['bulan'] == '6') ? 'selected' : ''; ?>>Juni</option>
		    				<option value="7" <?php echo (isset($_GET['bulan']) && $_GET['bulan'] == '7') ? 'selected' : ''; ?>>Juli</option>
		    				<option value="8" <?php echo (isset($_GET['bulan']) && $_GET['bulan'] == '8') ? 'selected' : ''; ?>>Agustus</option>
		    				<option value="9" <?php echo (isset($_GET['bulan']) && $_GET['bulan'] == '9') ? 'selected' : ''; ?>>September</option>
		    				<option value="10" <?php echo (isset($_GET['bulan']) && $_GET['bulan'] == '10') ? 'selected' : ''; ?>>Oktober</option>
		    				<option value="11" <?php echo (isset($_GET['bulan']) && $_GET['bulan'] == '11') ? 'selected' : ''; ?>>November</option>
		    				<option value="12" <?php echo (isset($_GET['bulan']) && $_GET['bulan'] == '12') ? 'selected' : ''; ?>>Desember</option>
		    			</select>
		    		</div>
		    		<div class="col-md-3">
		    			<label>Tahun</label>
		    			<select class="form-control" id="filterTahun" name="tahun">
		    				<option value="">-- Pilih Tahun --</option>
		    				<?php
		    				$tahunSekarang = date('Y');
		    				for ($i = $tahunSekarang; $i >= $tahunSekarang - 10; $i--) {
		    					$selected = (isset($_GET['tahun']) && $_GET['tahun'] == $i) ? 'selected' : '';
		    					echo "<option value=\"$i\" $selected>$i</option>";
		    				}
		    				?>
		    			</select>
		    		</div>
		    		<div class="col-md-1">
		    			<label>&nbsp;</label>
		    			<button type="button" class="btn btn-primary form-control" id="btnFilter">Filter</button>
		    		</div>
		    	</div>
		    	<hr>
		        <table id="example1" class="table table-bordered table-striped">
		          	<thead style="background:rgb(129, 2, 0, 1)">
			          <tr>
			            <th style="text-align: center; color: white;">No</th>
			            <th style="text-align: center; color: white;">Tanggal Lapor</th>
			            <th style="text-align: center; color: white;">Tanggal Selesai</th>
			            <th style="text-align: center; color: white;">Judul Log</th>
			            <th style="text-align: center; color: white;">Deskripsi Log</th>
			            <th style="text-align: center; color: white;">Catatan</th>
			            <th style="text-align: center; color: white;">Status</th>
			            <th style="text-align: center; color: white;">Action</th>
			          </tr>
		          	</thead>
		          	<tbody>
		          	<?php
		          	// Set default ke bulan dan tahun sekarang jika tidak ada filter
		          	$bulan = isset($_GET['bulan']) && $_GET['bulan'] != '' ? $_GET['bulan'] : date('m');
		          	$tahun = isset($_GET['tahun']) && $_GET['tahun'] != '' ? $_GET['tahun'] : date('Y');
		          	
		          	$query = mysqli_query($config,"SELECT * FROM tb_logbook l JOIN tb_user u ON l.id_user=u.id_user WHERE MONTH(tanggal_log) = '$bulan' AND YEAR(tanggal_log) = '$tahun' ORDER BY tanggal_log DESC")or die(mysqli_error($config));
		          	$n=1;
						while ($data=mysqli_fetch_array($query)) {
							// Perhitungan Stok
						$idlog = $data['id_log'];
						
						$nn=$n++;
					
					// Tentukan warna status
					$status = $data['status_log'] ?? 'Belum';
					if ($status == 'Selesai') {
						$badge = 'badge-success';
					} elseif ($status == 'Pending') {
						$badge = 'badge-warning';
					} else {
						$badge = 'badge-secondary';
					}
		          	?>
			          <tr>
			            <td><?php echo $nn ?></td>
			            <td><?php echo $data['tanggal_log'] ?></td>
			            <td><?php echo $data['tanggal_selesai'] ?></td>
			            <td><?php echo $data['judul_log'] ?></td>
			            <td><?php echo $data['deskripsi_log'] ?></td>
			            <td><?php echo $data['catatan_log'] ?></td>
			            <td><span class="badge <?php echo $badge; ?>"><?php echo $status; ?></span></td>
			            <td>
			            	<input type="hidden" id="code">
			            	<span>
			            		<a href="?unit=update_logbook&id=<?=$data['id_log']?>" class="btn btn-success"><i class="fa fa-pencil"></i> Edit</a>
			            	</span>
						<span>
							<a onclick="return confirm ('Yakin hapus <?php echo $data['judul_log'];?>')" href="?unit=delete_logbook&id=<?=$data['id_log']?>" class="btn btn-danger">
							<i class="fa fa-trash"></i> Hapus</a>
						</span>	
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

<script>
document.getElementById('btnFilter').addEventListener('click', function() {
	var bulan = document.getElementById('filterBulan').value;
	var tahun = document.getElementById('filterTahun').value;
	
	if (bulan == '' || tahun == '') {
		alert('Silahkan pilih bulan dan tahun');
		return;
	}
	
	window.location.href = '?unit=logbook&bulan=' + bulan + '&tahun=' + tahun;
});
</script>

