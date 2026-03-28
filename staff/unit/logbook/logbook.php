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
					<?php
					if (session_status() !== PHP_SESSION_ACTIVE) session_start();
					$nip = isset($_SESSION['nip']) ? $_SESSION['nip'] : '';
					$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
					$id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : '';
					$showUserDropdown = ($nip === '662.140725' && strtolower($role) === 'staff');
					?>
					<?php if ($showUserDropdown): ?>
					<div class="col-md-3">
						<label>User</label>
						<select class="form-control" id="filterUser" name="user">
							<option value="<?= $id_user ?>">-- Pilih User --</option>
							<?php
							$queryUser = mysqli_query($config, "SELECT id_user, nama_lengkap FROM tb_user WHERE status = 'aktif' AND role = 'Staff' ORDER BY nama_lengkap");
							while ($user = mysqli_fetch_array($queryUser)) {
								$selected = (isset($_GET['user']) && $_GET['user'] == $user['id_user']) ? 'selected' : '';
								echo "<option value=\"{$user['id_user']}\" $selected>{$user['nama_lengkap']}</option>";
							}
							?>
						</select>
					</div>
					<?php endif; ?>
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
			            <th style="text-align: center; color: white; width: 180px;">Action</th>
			          </tr>
		          	</thead>
		          	<tbody>
		          	<?php
		          	// Set default ke bulan dan tahun sekarang jika tidak ada filter
		          	$bulan = isset($_GET['bulan']) && $_GET['bulan'] != '' ? $_GET['bulan'] : date('m');
		          	$tahun = isset($_GET['tahun']) && $_GET['tahun'] != '' ? $_GET['tahun'] : date('Y');
		          	
				if (session_status() !== PHP_SESSION_ACTIVE) session_start();
				$nip = isset($_SESSION['nip']) ? $_SESSION['nip'] : '';
				$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
				$id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : '';
				$showUserDropdown = ($nip === '662.140725' && strtolower($role) === 'staff');
				$selectedUser = $showUserDropdown ? (isset($_GET['user']) ? $_GET['user'] : $id_user) : $id_user;
				$userFilter = $selectedUser ? " AND l.id_user = '".mysqli_real_escape_string($config, $selectedUser)."'" : "";
				$query = mysqli_query($config, "SELECT * FROM tb_logbook l JOIN tb_user u ON l.id_user=u.id_user WHERE MONTH(tanggal_log) = '$bulan' AND YEAR(tanggal_log) = '$tahun' $userFilter ORDER BY tanggal_log DESC") or die(mysqli_error($config));
		          	$n=1;
						while ($data=mysqli_fetch_array($query)) {
							// Perhitungan Stok
						$idlog = $data['id_log'];
						
						$nn=$n++;
					
					// Tentukan warna status
					$status = $data['status_log'] ?? 'Belum';
					if ($status == 'Selesai') {
						$badge = 'badge-success';
					} elseif ($status == 'Dalam Proses / Berjalan') {
						$badge = 'badge-info';
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
						<td class="text-center">
						<input type="hidden" id="code">
						<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#detailModal" onclick="showDetail(<?php echo htmlspecialchars(json_encode($data)); ?>)">
							<i class="fa fa-eye"></i> Detail
						</button>
						<?php
						// Tombol edit/hapus hanya muncul jika user yang dipilih adalah user login sendiri
						if (!$showUserDropdown || $selectedUser == $id_user) {
						?>
							<span>
								<a href="?unit=update_logbook&id=<?=$data['id_log']?>" class="btn btn-success btn-sm"><i class="fa fa-pencil"></i> Edit</a>
							</span>
							<span>
								<a onclick="return confirm ('Yakin hapus <?php echo $data['judul_log'];?>')" href="?unit=delete_logbook&id=<?=$data['id_log']?>" class="btn btn-danger btn-sm">
								<i class="fa fa-trash"></i> Hapus</a>
							</span>
						<?php } ?>
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
	var user = document.getElementById('filterUser') ? document.getElementById('filterUser').value : '';
	if (bulan == '' || tahun == '') {
		alert('Silahkan pilih bulan dan tahun');
		return;
	}
	var url = '?unit=logbook&bulan=' + bulan + '&tahun=' + tahun;
	if (user !== '') {
		url += '&user=' + user;
	}
	window.location.href = url;
});

// Fungsi untuk menampilkan detail di modal
function showDetail(data) {
	document.getElementById('detailTanggalLapor').textContent = data.tanggal_log || '-';
	document.getElementById('detailTanggalSelesai').textContent = data.tanggal_selesai || '-';
	document.getElementById('detailJudulLog').textContent = data.judul_log || '-';
	document.getElementById('detailDeskripsiLog').textContent = data.deskripsi_log || '-';
	document.getElementById('detailCatatanLog').textContent = data.catatan_log || '-';
	
	// Menampilkan status dengan styling badge
	const status = data.status_log || 'Belum';
	let badgeClass = 'badge-secondary';
	if (status == 'Selesai') {
		badgeClass = 'badge-success';
	} else if (status == 'Dalam Proses / Berjalan') {
		badgeClass = 'badge-info';
	} else if (status == 'Pending') {
		badgeClass = 'badge-warning';
	}
	const statusElement = document.getElementById('detailStatusLog');
	statusElement.textContent = status;
	statusElement.className = 'badge ' + badgeClass;
	
	document.getElementById('detailNamaUser').textContent = data.nama_lengkap || '-';
}

// Fungsi untuk copy text ke clipboard
function copyToClipboard(elementId) {
	const text = document.getElementById(elementId).textContent;
	navigator.clipboard.writeText(text).then(function() {
		alert('Data berhasil disalin: ' + text);
	}).catch(function(err) {
		alert('Gagal menyalin data');
	});
}
</script>

<!-- Modal Detail Logbook -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header border-bottom">
        <div>
          <h5 class="modal-title" id="detailModalLabel" style="font-size: 18px; font-weight: 600;">Detail Catatan Harian Kegiatan</h5>
          <small class="text-muted" style="font-size: 13px;">Informasi lengkap catatan harian kegiatan</small>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="padding: 20px;">
        <!-- Informasi Logbook Section -->
        <div style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #e0e0e0;">
          <h6 style="font-weight: 600; margin-bottom: 15px; font-size: 16px;">Informasi Logbook</h6>
          
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
            <div>
              <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Tanggal Lapor</p>
              <div style="display: flex; align-items: center; justify-content: space-between;">
                <strong id="detailTanggalLapor" style="font-size: 15px;">-</strong>
                <button type="button" onclick="copyToClipboard('detailTanggalLapor')" title="Salin Data" style="background: none; border: none; cursor: pointer; padding: 0; color: #666;">
                  <i class="fas fa-copy" style="font-size: 14px;"></i>
                </button>
              </div>
            </div>
            <div>
              <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Tanggal Selesai</p>
              <div style="display: flex; align-items: center; justify-content: space-between;">
                <strong id="detailTanggalSelesai" style="font-size: 15px;">-</strong>
                <button type="button" onclick="copyToClipboard('detailTanggalSelesai')" title="Salin Data" style="background: none; border: none; cursor: pointer; padding: 0; color: #666;">
                  <i class="fas fa-copy" style="font-size: 14px;"></i>
                </button>
              </div>
            </div>
          </div>

          <div style="margin-bottom: 15px;">
            <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Judul Log</p>
            <div style="display: flex; align-items: center; justify-content: space-between;">
              <strong id="detailJudulLog" style="font-size: 15px;">-</strong>
              <button type="button" onclick="copyToClipboard('detailJudulLog')" title="Salin Data" style="background: none; border: none; cursor: pointer; padding: 0; color: #666;">
                <i class="fas fa-copy" style="font-size: 14px;"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Informasi Detail Kegiatan Section -->
        <div style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #e0e0e0;">
          <h6 style="font-weight: 600; margin-bottom: 15px; font-size: 16px;">Informasi Detail Kegiatan</h6>
          
          <div style="margin-bottom: 15px;">
            <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Deskripsi Log</p>
            <div style="display: flex; gap: 10px;">
              <div style="flex: 1;">
                <p id="detailDeskripsiLog" style="word-wrap: break-word; white-space: pre-wrap; margin: 0; font-size: 14px;">-</p>
              </div>
              <button type="button" onclick="copyToClipboard('detailDeskripsiLog')" title="Salin Data" style="background: none; border: none; cursor: pointer; padding: 0; color: #666; height: fit-content;">
                <i class="fas fa-copy" style="font-size: 14px;"></i>
              </button>
            </div>
          </div>

          <div style="margin-bottom: 15px;">
            <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Catatan Log</p>
            <div style="display: flex; gap: 10px;">
              <div style="flex: 1;">
                <p id="detailCatatanLog" style="word-wrap: break-word; white-space: pre-wrap; margin: 0; font-size: 14px;">-</p>
              </div>
              <button type="button" onclick="copyToClipboard('detailCatatanLog')" title="Salin Data" style="background: none; border: none; cursor: pointer; padding: 0; color: #666; height: fit-content;">
                <i class="fas fa-copy" style="font-size: 14px;"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Informasi Status Section -->
        <div>
          <h6 style="font-weight: 600; margin-bottom: 15px; font-size: 16px;">Informasi Status</h6>
          
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
              <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Status Log</p>
              <div style="display: flex; align-items: center; justify-content: space-between;">
                <span id="detailStatusLog" style="display: inline-block; padding: 4px 8px; background: #303030; border-radius: 4px; font-size: 13px;">-</span>
                <button type="button" onclick="copyToClipboard('detailStatusLog')" title="Salin Data" style="background: none; border: none; cursor: pointer; padding: 0; color: #666;">
                  <i class="fas fa-copy" style="font-size: 14px;"></i>
                </button>
              </div>
            </div>
            <div>
              <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Nama Staff / Pegawai</p>
              <div style="display: flex; align-items: center; justify-content: space-between;">
                <strong id="detailNamaUser" style="font-size: 15px;">-</strong>
                <button type="button" onclick="copyToClipboard('detailNamaUser')" title="Salin Data" style="background: none; border: none; cursor: pointer; padding: 0; color: #666;">
                  <i class="fas fa-copy" style="font-size: 14px;"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

