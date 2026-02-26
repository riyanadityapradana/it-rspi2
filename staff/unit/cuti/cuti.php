	<!-- Main content -->
<!-- Main content -->
<section class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="card">
						<div class="card-header">
								<div class="card-tools" style="float: left; text-align: left;">
									<a href="?unit=create_cuti" class="btn btn-tool btn-sm" style="background:rgba(0, 123, 255, 1)">
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
														<?php
														for ($m=1;$m<=12;$m++){
																$nama = date('F', mktime(0,0,0,$m,1));
																$sel = (isset($_GET['bulan']) && $_GET['bulan']==$m)?'selected':'';
																echo "<option value=\"$m\" $sel>$nama</option>";
														}
														?>
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
												<th style="text-align: center; color: white; font-size: 14px;" responsive>No</th>
												<th style="text-align: center; color: white; font-size: 14px;" responsive>NIP</th>
												<th style="text-align: center; color: white; font-size: 14px;" responsive>Banyak Hari</th>											
												<th style="text-align: center; color: white; font-size: 14px;" responsive>Jenis Cuti</th>												
												<th style="text-align: center; color: white; font-size: 14px;" responsive>Mulai Tanggal</th>
												<th style="text-align: center; color: white; font-size: 14px;" responsive>Sampai Tanggal</th>
												<th style="text-align: center; color: white; font-size: 14px;" responsive>Masuk Tanggal</th>
												<th style="text-align: center; color: white; font-size: 14px;" responsive>Status</th>
												<th style="text-align: center; color: white; font-size: 14px;" responsive>Action</th>
											</tr>
										</thead>
										<tbody>
										<?php
										$bulan = isset($_GET['bulan']) && $_GET['bulan'] != '' ? $_GET['bulan'] : date('m');
										$tahun = isset($_GET['tahun']) && $_GET['tahun'] != '' ? $_GET['tahun'] : date('Y');

										if (session_status() !== PHP_SESSION_ACTIVE) session_start();
										$nip = isset($_SESSION['nip']) ? $_SESSION['nip'] : '';
										$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
										$id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : '';
										$showUserDropdown = ($nip === '662.140725' && strtolower($role) === 'staff');
										$selectedUser = $showUserDropdown ? (isset($_GET['user']) ? $_GET['user'] : $id_user) : $id_user;
										$userFilter = $selectedUser ? " AND c.id_user = '".mysqli_real_escape_string($config, $selectedUser)."'" : "";

										$query = mysqli_query($config, "SELECT c.*, u.nama_lengkap, u.nip FROM tb_cuti c JOIN tb_user u ON c.id_user = u.id_user WHERE MONTH(mulai_tanggal) = '$bulan' AND YEAR(mulai_tanggal) = '$tahun' $userFilter ORDER BY mulai_tanggal DESC") or die(mysqli_error($config));
										$n=1;
										while ($data=mysqli_fetch_array($query)) {
												$nn = $n++;
												$status = isset($data['status_lembur']) ? $data['status_lembur'] : 'Menunggu';
												if ($status == 'Diterima') {
														$badge = 'badge-success';
												} elseif ($status == 'Ditolak') {
														$badge = 'badge-danger';
												} else {
														$badge = 'badge-warning';
												}
										?>
											<tr>
												<td><?php echo $nn ?></td>
												<td><?php echo $data['nip'] ?></td>
												<td style="text-align: center;"><?php echo $data['banyak_hari'] ?> Hari</td>										
												<td><?php echo isset($data['jenis_cuti'])?htmlspecialchars($data['jenis_cuti']):'-' ?></td>												
												<td><?php echo $data['mulai_tanggal'] ?></td>
												<td><?php echo $data['sampai_tanggal'] ?></td>
												<td><?php echo $data['masuk_tanggal'] ?></td>
												<td><span class="badge <?php echo $badge; ?>"><?php echo $status; ?></span></td>
												<td style="text-align: center;">
													<?php
													if (!$showUserDropdown || $selectedUser == $id_user) {
														if ($status == 'Menunggu') {
															?>
															<a href="?unit=update_cuti&id=<?=$data['id_cuti']?>" class="btn btn-success"><i class="fa fa-pencil"></i> Edit</a>
															<a onclick="return confirm('Yakin hapus pengajuan cuti ini?')" href="?unit=delete_cuti&id=<?=$data['id_cuti']?>" class="btn btn-danger"><i class="fa fa-trash"></i> Hapus</a>
															<?php
														} elseif ($status == 'Diterima') {
															?>
															<a href="unit/cuti/cetak_cuti.php?id=<?= $data['id_cuti'] ?>" target="_blank" class="btn btn-primary"><i class="fa fa-print"></i> Print</a>
															<a onclick="return confirm('Yakin hapus pengajuan cuti ini?')" href="?unit=delete_cuti&id=<?=$data['id_cuti']?>" class="btn btn-danger"><i class="fa fa-trash"></i> Hapus</a>
															<?php
														} else {
															?>
															<a onclick="return confirm('Yakin hapus pengajuan cuti ini?')" href="?unit=delete_cuti&id=<?=$data['id_cuti']?>" class="btn btn-danger"><i class="fa fa-trash"></i> Hapus</a>
															<?php
														}
													}
													?>
												</td>
											</tr>
										<?php } ?>
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
		var url = '?unit=cuti&bulan=' + bulan + '&tahun=' + tahun;
		if (user !== '') {
				url += '&user=' + user;
		}
		window.location.href = url;
});
</script>
