<?php
require_once("../config/koneksi.php");
require_once(__DIR__ . "/barang_helpers.php");
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$jenis_barang_options = barang_get_jenis_options();
$kondisi_barang_options = barang_get_kondisi_options();

$lokasi_q = mysqli_query($config, "SELECT lokasi_id, nama_lokasi FROM tb_lokasi ORDER BY nama_lokasi ASC");
$lokasi_list = [];
while ($row = mysqli_fetch_assoc($lokasi_q)) {
  $lokasi_list[] = $row;
}

$jenis_filter = barang_normalize_jenis($_GET['jenis'] ?? '', '');
$kondisi_filter = barang_normalize_kondisi($_GET['kondisi'] ?? '', '');

?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>MASTER DATA BARANG</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="dashboard_staff.php?unit=beranda">Home</a></li>
          <li class="breadcrumb-item active">Barang</li>
        </ol>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
		<div class="card">
		    <div class="card-header">
		    	<div class="card-tools" style="float: left; text-align: left;">
            <a href="?unit=create_barang" class="btn btn-tool btn-sm" style="background:rgba(0, 123, 255, 1)">
              <i class="fas fa-plus-square" style="color: white;"> Tambah Data</i>
            </a>
          </div>
			    <div class="card-tools" style="float: right; text-align: right;">
            <form method="get" id="filterForm" style="display:inline-block; margin-right:10px;">
              <input type="hidden" name="unit" value="barang">
              <select id="filterJenisBarang" name="jenis" class="form-control form-control-sm" style="display: inline-block; width: auto;" onchange="document.getElementById('filterForm').submit();">
                <option value="">Semua Jenis Barang</option>
                <?php foreach ($jenis_barang_options as $jenis_option): ?>
                  <option value="<?= htmlspecialchars($jenis_option) ?>" <?php if (isset($_GET['jenis']) && $_GET['jenis'] === $jenis_option) echo 'selected'; ?>><?= htmlspecialchars($jenis_option) ?></option>
                <?php endforeach; ?>
              </select>
            </form>
            <form method="get" id="filterFormKondisi" style="display:inline-block;">
              <input type="hidden" name="unit" value="barang">
              <select id="filterStatusBarang" name="kondisi" class="form-control form-control-sm" style="display: inline-block; width: auto; margin-right: 10px;" onchange="document.getElementById('filterFormKondisi').submit();">
                <option value="">Semua Status</option>
                <?php foreach ($kondisi_barang_options as $kondisi_option): ?>
                  <option value="<?= htmlspecialchars($kondisi_option) ?>" <?php if (isset($_GET['kondisi']) && $_GET['kondisi'] === $kondisi_option) echo 'selected'; ?>><?= htmlspecialchars($kondisi_option) ?></option>
                <?php endforeach; ?>
              </select>
            </form>
            <a href="#" class="btn btn-tool btn-sm" data-card-widget="collapse" style="background:rgba(69, 77, 85, 1)">
              <i class="fas fa-bars"></i>
            </a>
            <button type="button" class="btn btn-tool btn-sm" style="background:rgba(40, 167, 69, 1); margin-left: 8px;" data-toggle="modal" data-target="#modalPrint">
              <i class="fas fa-print" style="color: white;"> Print</i>
            </button>
          </div>
			</div>
            <div class="card-body">
                <table id="example2" class="table table-bordered table-striped">
                <thead style="background:rgb(129, 2, 0, 1); color:white;">
                    <tr>
                    <th style="width: 30px; text-align: center;" responsive>No</th>
                    <th style="width: 280px;" responsive>Nama Barang</th>
                    <th style="width: 130px ;" responsive>Jenis Barang</th>
                    <th style="width: 50px; text-align: center;" responsive>Lokasi Awal</th>
                    <th style="width: 70px; text-align: center;" responsive>Lokasi Saat Ini</th>
                    <th style="width: 35px; text-align: center;" responsive>Status Penyerahan</th>
                    <th style="width: 30px; text-align: center;" responsive>Kondisi</th>
                    <th style="width: 280px; text-align: center;" responsive>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    // Build filters from GET parameters
                    $where = array();
                    if ($jenis_filter !== '') {
                      $jenis = mysqli_real_escape_string($config, $jenis_filter);
                      $where[] = "b.jenis_barang = '{$jenis}'";
                    }
                    if ($kondisi_filter !== '') {
                      $kondisi = mysqli_real_escape_string($config, $kondisi_filter);
                      $where[] = "LOWER(b.kondisi) = LOWER('{$kondisi}')";
                    }
                    $sql = "SELECT b.barang_id, b.nama_barang, b.kode_inventaris, b.jenis_barang, b.nomor_seri, b.ip_address, b.jumlah, b.spesifikasi, b.kondisi, b.tanggal_terima, b.foto,
                        (SELECT nama_lokasi FROM tb_lokasi WHERE lokasi_id = b.lokasi_id) AS lokasi_saat_ini,
                        b.lokasi_id,
                        (SELECT lokasi_id FROM tb_penyerahan WHERE barang_id = b.barang_id ORDER BY penyerahan_id DESC LIMIT 1) AS last_penyerahan_lokasi_id,
                         (SELECT GROUP_CONCAT(CONCAT(l.nama_lokasi, ' (', p.kondisi, ')') SEPARATOR ', ') FROM tb_penyerahan p LEFT JOIN tb_lokasi l ON p.lokasi_id = l.lokasi_id WHERE p.barang_id = b.barang_id) AS nama_lokasi_gabung,
                       (SELECT COUNT(*) FROM tb_penyerahan WHERE barang_id = b.barang_id) AS jumlah_penyerahan
                    FROM tb_barang b";
                    if (count($where) > 0) {
                      $sql .= ' WHERE ' . implode(' AND ', $where);
                    }
                    $sql .= ' ORDER BY b.barang_id DESC';
                    $q = mysqli_query($config, $sql);
                    while ($row = mysqli_fetch_assoc($q)) : ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['nama_barang']); ?>
                          <br><small style="color: #2266e4;">Kode Inventaris :<b><?= htmlspecialchars($row['kode_inventaris']); ?></b></small>
                          <br><small style="color: #2266e4;">S/N : <b><?= htmlspecialchars($row['nomor_seri']); ?></b></small>
                          <br><button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalPindah" onclick="setPindahData('<?= $row['barang_id'] ?>', '<?= htmlspecialchars($row['kode_inventaris']) ?>', '<?= htmlspecialchars($row['nama_barang']) ?>', '<?= $row['last_penyerahan_lokasi_id'] ?>')" responsive>
                              <i class="fa fa-exchange-alt"></i> Pindah
                            </button>
                            <button type="button" class="btn btn-warning btn-sm" onclick="setPerbaikanData('<?= $row['barang_id'] ?>', '<?= htmlspecialchars($row['nama_barang']) ?>', '<?= $row['lokasi_id'] ?>')" data-toggle="modal" data-target="#modalPerbaikan" responsive>
                              <i class="fa fa-wrench"></i> Perbaikan
                            </button>
                        </td>
                        <td><?= htmlspecialchars($row['jenis_barang']); ?></td>
                        <td>
                          <?php
                          // Tampilkan penyerahan (lokasi) sebagai badge berwarna biru (lokasi awal)
                          if (!empty($row['nama_lokasi_gabung']) && $row['nama_lokasi_gabung'] != '-') {
                            $parts = explode(', ', $row['nama_lokasi_gabung']);
                            foreach ($parts as $pitem) {
                              // pitem expected format: "NamaLokasi (kondisi)"
                              $safe = htmlspecialchars(trim($pitem));
                              echo '<span class="badge badge-primary">' . $safe . '</span> ';
                            }
                          } else {
                            echo '-';
                          }
                          ?>
                        </td>
                        <td class="text-center">
                          <?php if (!empty($row['lokasi_saat_ini'])): ?>
                            <span class="badge badge-success"><?= htmlspecialchars($row['lokasi_saat_ini']) ?></span>
                          <?php else: ?>
                            -
                          <?php endif; ?>
                        </td>
                        <td class="text-center">
                          <?php if ($row['jumlah_penyerahan'] >= $row['jumlah']): ?>
                            <span class="badge badge-success">Completed</span>
                          <?php elseif ($row['jumlah_penyerahan'] > 0): ?>
                            <span class="badge badge-primary">In Progress (<?= $row['jumlah_penyerahan'] ?>/<?= $row['jumlah'] ?>)</span>
                          <?php else: ?>
                            <span class="badge badge-secondary">Belum Diserahkan</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php
                            $badge = barang_get_badge_class(isset($row['kondisi']) ? $row['kondisi'] : '');
                          ?>
                          <span class="badge badge-<?= $badge ?>"><?= htmlspecialchars($row['kondisi']) ?></span>
                        </td>
                        <td>
                          <!-- Button Detail Data -->
                          <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalDetailBarang<?= $row['barang_id'] ?>" responsive>
                            <i class="fa fa-eye"></i> Detail
                          </button>
                          <?php if ($row['jumlah_penyerahan'] < $row['jumlah']): ?>
                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalUpdateLokasi" onclick="setUpdateLokasiData('<?= $row['barang_id'] ?>', '<?= htmlspecialchars($row['nama_barang']) ?>', '', '', '', '<?= $row['jumlah'] ?>')" responsive>
                              <i class="fa fa-handshake"></i> Penyerahan (<?= $row['jumlah_penyerahan'] ?>/<?= $row['jumlah'] ?>)
                            </button>
                            <a href="dashboard_staff.php?unit=delete_barang&id=<?= urlencode($row['barang_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus barang ini?')" responsive><i class="fa fa-trash"></i> Hapus</a>
                          <?php else: ?>
                            <a href="dashboard_staff.php?unit=update_barang&id=<?= urlencode($row['barang_id']); ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit" responsive></i> Edit</a>
                            <a href="dashboard_staff.php?unit=delete_barang&id=<?= urlencode($row['barang_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus barang ini?')" responsive><i class="fa fa-trash"></i> Hapus</a>
                          <?php endif; ?>
                        </td>
                    <!-- Modal Update Lokasi/Kondisi/Keterangan -->
                    <div class="modal fade" id="modalUpdateLokasi" tabindex="-1" role="dialog" aria-labelledby="modalUpdateLokasiLabel" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="modalUpdateLokasiLabel">Penyerahan Barang</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <form id="formUpdateLokasi" method="POST" action="unit/barang/aksi_penyerahan.php">
                            <div class="modal-body">
                              <input type="hidden" name="barang_id" id="updateBarangId">
                              <input type="hidden" name="unit_index" id="unitIndex">
                              <div class="form-group">
                                <label>Nama Barang:</label>
                                <input type="text" class="form-control" id="updateNamaBarang" readonly>
                              </div>
                              <div class="form-group">
                                <label id="unitLabel">Lokasi:</label>
                                <select  name="lokasi_id[]" class="form-control select2bs4" required>
                                  <option value="">-- Pilih Lokasi --</option>
                                  <?php foreach ($lokasi_list as $lokasi): ?>
                                    <option value="<?= $lokasi['lokasi_id'] ?>"><?= htmlspecialchars($lokasi['nama_lokasi']) ?></option>
                                  <?php endforeach; ?>
                                </select>
                              </div>
                              <div class="form-group">
                                <label>Kondisi:</label>
                                <select class="form-control select2bs4" name="kondisi[]" required>
                                  <option value="baru">Baru</option>
                                  <option value="bekas">Bekas</option>
                                </select>
                              </div>
                              <div class="form-group">
                                <label>Keterangan:</label>
                                <textarea class="form-control" name="keterangan_unit[]" rows="2"></textarea>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                              <button type="submit" class="btn btn-success">Update</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                    <script>
                      function setUpdateLokasiData(barangId, namaBarang, kondisi, keterangan, lokasiId, jumlah) {
                        document.getElementById('updateBarangId').value = barangId;
                        document.getElementById('updateNamaBarang').value = namaBarang;
                        var unit_index = 0;
                        var urlParams = new URLSearchParams(window.location.search);
                        if (urlParams.get('continue_barang_id') == barangId) {
                          unit_index = parseInt(urlParams.get('next_unit')) || 0;
                        }
                        document.getElementById('unitIndex').value = unit_index;
                        document.getElementById('unitLabel').textContent = 'Unit ' + (unit_index + 1) + ' dari ' + jumlah + ' - Lokasi:';
                        if (unit_index == 0) {
                          $('select[name="lokasi_id[]"]').val(lokasiId).trigger('change');
                          $('select[name="kondisi[]"]').val(kondisi).trigger('change');
                          document.querySelector('textarea[name="keterangan_unit[]"]').value = keterangan;
                        }
                        // Jika continue, auto show modal
                        if (unit_index > 0) {
                          $('#modalUpdateLokasi').modal('show');
                        }
                      }
                    </script>
                </tr>
                <?php endwhile; ?>

            <!-- Modal Perbaikan Barang -->
            <div class="modal fade" id="modalPerbaikan" tabindex="-1" role="dialog" aria-labelledby="modalPerbaikanLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="modalPerbaikanLabel">Form Perbaikan Barang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <form method="post" action="unit/barang/aksi_perbaikan.php">
                                        <div class="modal-body">
                      <input type="hidden" name="barang_id" id="perbaikanBarangId">
                      <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text" id="perbaikanNamaBarang" class="form-control" readonly>
                      </div>
                      <div class="form-group">
                        <label>Tanggal Lapor</label>
                        <input type="datetime-local" name="tanggal_lapor" id="perbaikanTanggalLapor" class="form-control" value="<?php echo date('Y-m-d\\TH:i'); ?>">
                      </div>
                      <div class="form-group">
                        <label>Unit Pelapor</label>
                        <select id="perbaikanUnitMelapor" class="form-control select2bs4" disabled>
                          <option value="">-- Lokasi barang (otomatis) --</option>
                          <?php foreach ($lokasi_list as $lok): ?>
                            <option value="<?= $lok['lokasi_id'] ?>"><?= htmlspecialchars($lok['nama_lokasi']) ?></option>
                          <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="unit_melapor" id="perbaikanUnitMelaporHidden" value="">
                      </div>
                      <div class="form-group">
                        <label>Deskripsi Kerusakan</label>
                        <textarea name="deskripsi_kerusakan" class="form-control" rows="3" required></textarea>
                      </div>
                      <div class="form-group">
                        <label>Tindakan Perbaikan</label>
                        <select name="tindakan_perbaikan" id="perbaikanTindakan" class="form-control select2bs4" required>
                          <option value="service_luar">Service luar</option>
                          <option value="service_sendiri">Service sendiri</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label>Status Perbaikan</label>
                        <select name="status_perbaikan" class="form-control select2bs4" required>
                          <option value="diajukan">Diajukan</option>
                          <option value="proses">Proses</option>
                          <option value="tidak_dapat_diperbaiki">Tidak Dapat Diperbaiki</option>
                        </select>
                      </div>
                      <div class="form-group" id="perbaikanTeknisiGroup" style="display:none;">
                        <label>Teknisi</label>
                        <input type="text" name="teknisi" id="perbaikanTeknisi" class="form-control" readonly>
                      </div>
                      <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan_perbaikan" class="form-control" rows="2"></textarea>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                      <button type="submit" class="btn btn-primary">Simpan Perbaikan</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <script>
              var currentPerbaikanUser = "<?= isset($_SESSION['nama_lengkap']) ? addslashes($_SESSION['nama_lengkap']) : (isset($_SESSION['username']) ? addslashes($_SESSION['username']) : '') ?>";
              function setPerbaikanData(barangId, namaBarang, lokasiId) {
                document.getElementById('perbaikanBarangId').value = barangId;
                document.getElementById('perbaikanNamaBarang').value = namaBarang;
                // set current datetime-local if the field is empty
                var dtField = document.getElementById('perbaikanTanggalLapor');
                if (dtField && !dtField.value) {
                  var now = new Date();
                  var year = now.getFullYear();
                  var month = ('0' + (now.getMonth()+1)).slice(-2);
                  var day = ('0' + now.getDate()).slice(-2);
                  var hours = ('0' + now.getHours()).slice(-2);
                  var minutes = ('0' + now.getMinutes()).slice(-2);
                  dtField.value = year + '-' + month + '-' + day + 'T' + hours + ':' + minutes;
                }
                // default unit_melapor to barang lokasi if available (set both display select and hidden input)
                if (lokasiId) {
                  var sel = document.getElementById('perbaikanUnitMelapor');
                  var hid = document.getElementById('perbaikanUnitMelaporHidden');
                  if (sel) {
                    sel.value = lokasiId;
                    if (window.jQuery) try { window.jQuery(sel).val(lokasiId).trigger('change'); } catch(e){}
                  }
                  if (hid) hid.value = lokasiId;
                } else {
                  var hid = document.getElementById('perbaikanUnitMelaporHidden');
                  if (hid) hid.value = '';
                }
                // reset teknisi field visibility
                var tindakan = document.getElementById('perbaikanTindakan');
                toggleTeknisiField(tindakan ? tindakan.value : '');
                $('#modalPerbaikan').modal('show');
              }

              function toggleTeknisiField(value) {
                var grp = document.getElementById('perbaikanTeknisiGroup');
                var input = document.getElementById('perbaikanTeknisi');
                if (!grp || !input) return;
                if (value === 'service_sendiri') {
                  grp.style.display = '';
                  input.value = currentPerbaikanUser || '';
                } else {
                  grp.style.display = 'none';
                  input.value = '';
                }
              }

              document.addEventListener('DOMContentLoaded', function() {
                var tindakanSel = document.getElementById('perbaikanTindakan');
                if (tindakanSel) {
                  tindakanSel.addEventListener('change', function() {
                    toggleTeknisiField(this.value);
                  });
                }
              });
            </script>
            <!-- Modal Pindah Barang -->
            <div class="modal fade" id="modalPindah" tabindex="-1" role="dialog" aria-labelledby="modalPindahLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="modalPindahLabel">Pemindahan Barang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <form method="post" action="unit/barang/aksi_pindah.php">
                                        <div class="modal-body">
                      <input type="hidden" name="barang_id" id="pindahBarangId">
                      <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text" id="pindahNamaBarang" class="form-control" readonly>
                      </div>
                      <div class="form-group">
                        <label>Kode Inventaris</label>
                        <input type="text" id="pindahKodeInventaris" class="form-control" readonly>
                      </div>
                      <div class="form-group">
                        <label>Lokasi Asal</label>
                        <!-- read-only display select (disabled) -->
                        <select id="pindahLokasiAsalDisplay" class="form-control select2bs4" disabled>
                          <option value="">-- Lokasi Asal (otomatis) --</option>
                          <?php foreach ($lokasi_list as $lok): ?>
                            <option value="<?= $lok['lokasi_id'] ?>"><?= htmlspecialchars($lok['nama_lokasi']) ?></option>
                          <?php endforeach; ?>
                        </select>
                        <!-- hidden input to actually submit lokasi_asal value -->
                        <input type="hidden" name="lokasi_asal" id="pindahLokasiAsalHidden" value="">
                      </div>
                      <div class="form-group">
                        <label>Lokasi Tujuan</label>
                        <select name="lokasi_tujuan" class="form-control select2bs4" required>
                          <option value="">-- Pilih Lokasi Tujuan --</option>
                          <?php foreach ($lokasi_list as $lok2): ?>
                            <option value="<?= $lok2['lokasi_id'] ?>"><?= htmlspecialchars($lok2['nama_lokasi']) ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                      <div class="form-group">
                        <label>Tanggal Mutasi</label>
                        <input type="date" name="tanggal_mutasi" class="form-control" value="<?= date('Y-m-d') ?>" required>
                      </div>
                      <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3"></textarea>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                      <button type="submit" class="btn btn-primary">Simpan Pemindahan</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <script>
              function setPindahData(barangId, kodeInventaris, namaBarang, lastPenyerahanLokasiId) {
                document.getElementById('pindahBarangId').value = barangId;
                document.getElementById('pindahKodeInventaris').value = kodeInventaris;
                document.getElementById('pindahNamaBarang').value = namaBarang;
                // set lokasi_asal select to last penyerahan lokasi if available, otherwise leave blank
                try {
                  var sel = document.getElementById('pindahLokasiAsalDisplay');
                  var hid = document.getElementById('pindahLokasiAsalHidden');
                  if (sel && lastPenyerahanLokasiId) {
                    sel.value = lastPenyerahanLokasiId;
                    if (window.jQuery) try { window.jQuery(sel).val(lastPenyerahanLokasiId).trigger('change'); } catch(e){}
                  }
                  if (hid) {
                    hid.value = lastPenyerahanLokasiId ? lastPenyerahanLokasiId : '';
                  }
                } catch (e) { console.warn(e); }
                $('#modalPindah').modal('show');
              }
            </script>
            <!-- Modal Detail Barang -->
            <?php foreach ($q as $detailRow): ?>
            <div class="modal fade" id="modalDetailBarang<?= $detailRow['barang_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalDetailBarangLabel<?= $detailRow['barang_id'] ?>" aria-hidden="true">
              <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                  <div class="modal-header border-bottom">
                    <div>
                      <h5 class="modal-title" id="modalDetailBarangLabel<?= $detailRow['barang_id'] ?>" style="font-size: 18px; font-weight: 600;">Detail Data Barang</h5>
                      <small class="text-muted" style="font-size: 13px;">Informasi lengkap data barang dan lokasi</small>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body" style="padding: 20px;">
                    <!-- Informasi Barang Section -->
                    <div style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #e0e0e0;">
                      <h6 style="font-weight: 600; margin-bottom: 15px; font-size: 16px;">Informasi Barang</h6>
                      
                      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                        <div>
                          <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Nama Barang</p>
                          <div style="display: flex; align-items: center; justify-content: space-between;">
                            <strong style="font-size: 15px;"><?= htmlspecialchars($detailRow['nama_barang']) ?></strong>
                            <button type="button" onclick="copyToClipboard('detailBarangNama<?= $detailRow['barang_id'] ?>')" title="Salin Data" style="background: none; border: none; cursor: pointer; padding: 0; color: #666;">
                              <i class="fas fa-copy" style="font-size: 14px;"></i>
                            </button>
                          </div>
                          <span id="detailBarangNama<?= $detailRow['barang_id'] ?>" style="display:none;"><?= htmlspecialchars($detailRow['nama_barang']) ?></span>
                        </div>
                        <div>
                          <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Foto Barang</p>
                          <?php if (!empty($detailRow['foto'])): ?>
                            <img src="unit/barang/foto-barang/<?= htmlspecialchars($detailRow['foto']) ?>" alt="Foto Barang" style="max-width:160px; max-height:160px; border:1px solid #d8d8d8; border-radius:4px;">
                          <?php else: ?>
                            <div style="width:160px; height:160px; border: 1px dashed #bbb; display:flex; align-items:center; justify-content:center; color:#777; font-size:13px;">No Image</div>
                          <?php endif; ?>
                        </div>
                      </div>
                      
                      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                        <div>
                          <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Kode Inventaris</p>
                          <div style="display: flex; align-items: center; justify-content: space-between;">
                            <strong style="font-size: 15px;"><?= htmlspecialchars($detailRow['kode_inventaris']) ?></strong>
                            <button type="button" onclick="copyToClipboard('detailBarangKode<?= $detailRow['barang_id'] ?>')" title="Salin Data" style="background: none; border: none; cursor: pointer; padding: 0; color: #666;">
                              <i class="fas fa-copy" style="font-size: 14px;"></i>
                            </button>
                          </div>
                          <span id="detailBarangKode<?= $detailRow['barang_id'] ?>" style="display:none;"><?= htmlspecialchars($detailRow['kode_inventaris']) ?></span>
                        </div>
                      </div>

                      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                        <div>
                          <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Jenis Barang</p>
                          <div style="display: flex; align-items: center; justify-content: space-between;">
                            <strong style="font-size: 15px;"><?= htmlspecialchars($detailRow['jenis_barang']) ?></strong>
                            <button type="button" onclick="copyToClipboard('detailBarangJenis<?= $detailRow['barang_id'] ?>')" title="Salin Data" style="background: none; border: none; cursor: pointer; padding: 0; color: #666;">
                              <i class="fas fa-copy" style="font-size: 14px;"></i>
                            </button>
                          </div>
                          <span id="detailBarangJenis<?= $detailRow['barang_id'] ?>" style="display:none;"><?= htmlspecialchars($detailRow['jenis_barang']) ?></span>
                        </div>
                        <div>
                          <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Nomor Seri</p>
                          <div style="display: flex; align-items: center; justify-content: space-between;">
                            <strong style="font-size: 15px;"><?= htmlspecialchars($detailRow['nomor_seri']) ?></strong>
                            <button type="button" onclick="copyToClipboard('detailBarangSeri<?= $detailRow['barang_id'] ?>')" title="Salin Data" style="background: none; border: none; cursor: pointer; padding: 0; color: #666;">
                              <i class="fas fa-copy" style="font-size: 14px;"></i>
                            </button>
                          </div>
                          <span id="detailBarangSeri<?= $detailRow['barang_id'] ?>" style="display:none;"><?= htmlspecialchars($detailRow['nomor_seri']) ?></span>
                        </div>
                      </div>

                      <?php if ($detailRow['jenis_barang'] == 'Komputer & Laptop'): ?>
                      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                        <div>
                          <p style="font-size: 14px; color: #999; margin-bottom: 5px;">IP Address</p>
                          <div style="display: flex; align-items: center; justify-content: space-between;">
                            <strong style="font-size: 15px;"><?= htmlspecialchars($detailRow['ip_address']) ?></strong>
                            <button type="button" onclick="copyToClipboard('detailBarangIP<?= $detailRow['barang_id'] ?>')" title="Salin Data" style="background: none; border: none; cursor: pointer; padding: 0; color: #666;">
                              <i class="fas fa-copy" style="font-size: 14px;"></i>
                            </button>
                          </div>
                          <span id="detailBarangIP<?= $detailRow['barang_id'] ?>" style="display:none;"><?= htmlspecialchars($detailRow['ip_address']) ?></span>
                        </div>
                        <div>
                          <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Tanggal Terima</p>
                          <div style="display: flex; align-items: center; justify-content: space-between;">
                            <strong style="font-size: 15px;"><?= !empty($detailRow['tanggal_terima']) ? date('d-m-Y', strtotime($detailRow['tanggal_terima'])) : '-' ?></strong>
                            <button type="button" onclick="copyToClipboard('detailBarangTgl<?= $detailRow['barang_id'] ?>')" title="Salin Data" style="background: none; border: none; cursor: pointer; padding: 0; color: #666;">
                              <i class="fas fa-copy" style="font-size: 14px;"></i>
                            </button>
                          </div>
                          <span id="detailBarangTgl<?= $detailRow['barang_id'] ?>" style="display:none;"><?= !empty($detailRow['tanggal_terima']) ? date('d-m-Y', strtotime($detailRow['tanggal_terima'])) : '-' ?></span>
                        </div>
                      </div>
                      <?php else: ?>
                      <div style="margin-bottom: 15px;">
                        <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Tanggal Terima</p>
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                          <strong style="font-size: 15px;"><?= !empty($detailRow['tanggal_terima']) ? date('d-m-Y', strtotime($detailRow['tanggal_terima'])) : '-' ?></strong>
                          <button type="button" onclick="copyToClipboard('detailBarangTgl<?= $detailRow['barang_id'] ?>')" title="Salin Data" style="background: none; border: none; cursor: pointer; padding: 0; color: #666;">
                            <i class="fas fa-copy" style="font-size: 14px;"></i>
                          </button>
                        </div>
                        <span id="detailBarangTgl<?= $detailRow['barang_id'] ?>" style="display:none;"><?= !empty($detailRow['tanggal_terima']) ? date('d-m-Y', strtotime($detailRow['tanggal_terima'])) : '-' ?></span>
                      </div>
                      <?php endif; ?>

                      <div style="margin-bottom: 15px;">
                        <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Jumlah</p>
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                          <strong style="font-size: 15px;"><?= htmlspecialchars($detailRow['jumlah']) ?></strong>
                          <button type="button" onclick="copyToClipboard('detailBarangJumlah<?= $detailRow['barang_id'] ?>')" title="Salin Data" style="background: none; border: none; cursor: pointer; padding: 0; color: #666;">
                            <i class="fas fa-copy" style="font-size: 14px;"></i>
                          </button>
                        </div>
                        <span id="detailBarangJumlah<?= $detailRow['barang_id'] ?>" style="display:none;"><?= htmlspecialchars($detailRow['jumlah']) ?></span>
                      </div>
                    </div>

                    <!-- Informasi Lokasi Section -->
                    <div style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #e0e0e0;">
                      <h6 style="font-weight: 600; margin-bottom: 15px; font-size: 16px;">Informasi Lokasi</h6>
                      
                      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                        <div>
                          <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Lokasi Awal</p>
                          <div style="display: flex; align-items: center; justify-content: space-between;">
                            <strong style="font-size: 15px;">
                              <?php
                              $q_penyerahan_awal = mysqli_query($config, "SELECT p.*, l.nama_lokasi FROM tb_penyerahan p LEFT JOIN tb_lokasi l ON p.lokasi_id = l.lokasi_id WHERE p.barang_id='{$detailRow['barang_id']}' ORDER BY p.penyerahan_id ASC LIMIT 1");
                              if (mysqli_num_rows($q_penyerahan_awal) > 0) {
                                $p_awal = mysqli_fetch_assoc($q_penyerahan_awal);
                                echo htmlspecialchars($p_awal['nama_lokasi']);
                              } else {
                                echo '-';
                              }
                              ?>
                            </strong>
                            <button type="button" onclick="copyToClipboard('detailLokasiAwal<?= $detailRow['barang_id'] ?>')" title="Salin Data" style="background: none; border: none; cursor: pointer; padding: 0; color: #666;">
                              <i class="fas fa-copy" style="font-size: 14px;"></i>
                            </button>
                          </div>
                          <span id="detailLokasiAwal<?= $detailRow['barang_id'] ?>" style="display:none;">
                            <?php
                            $q_penyerahan_awal = mysqli_query($config, "SELECT p.*, l.nama_lokasi FROM tb_penyerahan p LEFT JOIN tb_lokasi l ON p.lokasi_id = l.lokasi_id WHERE p.barang_id='{$detailRow['barang_id']}' ORDER BY p.penyerahan_id ASC LIMIT 1");
                            if (mysqli_num_rows($q_penyerahan_awal) > 0) {
                              $p_awal = mysqli_fetch_assoc($q_penyerahan_awal);
                              echo htmlspecialchars($p_awal['nama_lokasi']);
                            }
                            ?>
                          </span>
                        </div>
                        <div>
                          <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Lokasi Terakhir</p>
                          <div style="display: flex; align-items: center; justify-content: space-between;">
                            <strong style="font-size: 15px;">
                              <?php
                              if (!empty($detailRow['lokasi_saat_ini'])) {
                                echo htmlspecialchars($detailRow['lokasi_saat_ini']);
                              } else {
                                echo '-';
                              }
                              ?>
                            </strong>
                            <button type="button" onclick="copyToClipboard('detailLokasiTerakhir<?= $detailRow['barang_id'] ?>')" title="Salin Data" style="background: none; border: none; cursor: pointer; padding: 0; color: #666;">
                              <i class="fas fa-copy" style="font-size: 14px;"></i>
                            </button>
                          </div>
                          <span id="detailLokasiTerakhir<?= $detailRow['barang_id'] ?>" style="display:none;">
                            <?php echo !empty($detailRow['lokasi_saat_ini']) ? htmlspecialchars($detailRow['lokasi_saat_ini']) : '-'; ?>
                          </span>
                        </div>
                      </div>

                      <div style="margin-bottom: 15px;">
                        <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Semua Lokasi Penyerahan</p>
                        <div style="display: flex; gap: 10px;">
                          <div style="flex: 1;">
                            <?php
                            $q_penyerahan_all = mysqli_query($config, "SELECT p.*, l.nama_lokasi FROM tb_penyerahan p LEFT JOIN tb_lokasi l ON p.lokasi_id = l.lokasi_id WHERE p.barang_id='{$detailRow['barang_id']}'");
                            if (mysqli_num_rows($q_penyerahan_all) > 0) {
                              while ($p = mysqli_fetch_assoc($q_penyerahan_all)) {
                                $badge_class = $p['kondisi'] == 'baru' ? 'success' : ($p['kondisi'] == 'bekas' ? 'secondary' : ($p['kondisi'] == 'rusak' ? 'danger' : 'warning'));
                                echo '<span class="badge badge-' . $badge_class . '">' . htmlspecialchars($p['nama_lokasi']) . ' (' . htmlspecialchars($p['kondisi']) . ')</span> ';
                              }
                            } else {
                              echo '<p style="margin: 0; font-size: 14px;">-</p>';
                            }
                            ?>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Informasi Status Section -->
                    <div>
                      <h6 style="font-weight: 600; margin-bottom: 15px; font-size: 16px;">Informasi Status</h6>
                      
                      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                          <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Status Barang</p>
                          <div style="display: flex; align-items: center; justify-content: space-between;">
                            <?php
                            $kval = isset($detailRow['kondisi']) ? strtolower(trim($detailRow['kondisi'])) : '';
                            switch ($kval) {
                              case 'baik':
                                $badge = 'success';
                                break;
                              case 'baru':
                                $badge = 'success';
                                break;
                              case 'bekas':
                                $badge = 'secondary';
                                break;
                              case 'rusak':
                                $badge = 'danger';
                                break;
                              default:
                                if (strpos($kval, 'perbaikan') !== false) {
                                  $badge = 'warning';
                                } else {
                                  $badge = 'secondary';
                                }
                            }
                            ?>
                            <span class="badge badge-<?= $badge ?>" style="display: inline-block; padding: 4px 8px; background: #303030; border-radius: 4px; font-size: 13px; color: white;"><?= htmlspecialchars($detailRow['kondisi']) ?></span>
                            <button type="button" onclick="copyToClipboard('detailStatusBarang<?= $detailRow['barang_id'] ?>')" title="Salin Data" style="background: none; border: none; cursor: pointer; padding: 0; color: #666;">
                              <i class="fas fa-copy" style="font-size: 14px;"></i>
                            </button>
                          </div>
                          <span id="detailStatusBarang<?= $detailRow['barang_id'] ?>" style="display:none;"><?= htmlspecialchars($detailRow['kondisi']) ?></span>
                        </div>
                        <div>
                          <p style="font-size: 14px; color: #999; margin-bottom: 5px;">Status Penyerahan</p>
                          <div style="display: flex; align-items: center; justify-content: space-between;">
                            <?php
                            if ($detailRow['jumlah_penyerahan'] >= $detailRow['jumlah']) {
                              $status_penyerahan = 'Completed';
                              $badge_status = 'badge-success';
                            } elseif ($detailRow['jumlah_penyerahan'] > 0) {
                              $status_penyerahan = 'In Progress (' . $detailRow['jumlah_penyerahan'] . '/' . $detailRow['jumlah'] . ')';
                              $badge_status = 'badge-primary';
                            } else {
                              $status_penyerahan = 'Belum Diserahkan';
                              $badge_status = 'badge-secondary';
                            }
                            ?>
                            <span class="<?= $badge_status ?>" style="display: inline-block; padding: 4px 8px; background: #303030; border-radius: 4px; font-size: 13px; color: white;"><?= $status_penyerahan ?></span>
                            <button type="button" onclick="copyToClipboard('detailStatusPenyerahan<?= $detailRow['barang_id'] ?>')" title="Salin Data" style="background: none; border: none; cursor: pointer; padding: 0; color: #666;">
                              <i class="fas fa-copy" style="font-size: 14px;"></i>
                            </button>
                          </div>
                          <span id="detailStatusPenyerahan<?= $detailRow['barang_id'] ?>" style="display:none;"><?= $status_penyerahan ?></span>
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
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
    </div>
</div>
</section>

<!-- full-size image modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="background:transparent; border:none;">
      <div class="modal-body text-center p-0">
        <img src="" id="modalImage" style="max-width:100%; height:auto;" />
      </div>
    </div>
  </div>
</div>

<!-- Modal Print -->

<div class="modal fade" id="modalPrint" tabindex="-1" role="dialog" aria-labelledby="modalPrintLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formPrint" method="get" target="_blank" action="unit/barang/print_barang.php">
      <div class="modal-content">
        <div class="modal-header" style="background: #1976d2; color: white;">
          <h5 class="modal-title" id="modalPrintLabel"><i class="fas fa-print"></i> Cetak Laporan Data Barang</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Pilihan Kondisi Barang</label>
            <select class="form-control" name="kondisi" required>
              <option value="Baru">Baru</option>
              <option value="Bekas">Bekas</option>
              <option value="Rusak">Rusak</option>
              <option value="Dalam Perbaikan">Dalam Perbaikan</option>
            </select>
          </div>
          <div class="form-group">
            <label>Pilihan Lokasi</label>
            <select class="form-control" name="lokasi_filter" required>
              <option value="unit_it">Unit IT Saja</option>
              <option value="all">Semua Unit</option>
            </select>
          </div>
          <div class="form-group">
            <label>Tahun</label>
            <select class="form-control" name="tahun" required>
              <?php for($y=date('Y')-5;$y<=date('Y');$y++): ?>
                <option value="<?= $y ?>"><?= $y ?></option>
              <?php endfor; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer" style="background: #e3f2fd;">
          <button type="submit" class="btn btn-success"><i class="fas fa-print"></i> Cetak</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
// Fungsi untuk copy text ke clipboard
function copyToClipboard(elementId) {
	const text = document.getElementById(elementId).textContent;
	navigator.clipboard.writeText(text).then(function() {
		alert('Data berhasil disalin: ' + text);
	}).catch(function(err) {
		alert('Gagal menyalin data');
	});
}

// Filter berdasarkan jenis barang dan status barang
document.addEventListener('DOMContentLoaded', function() {
  const filterJenisSelect = document.getElementById('filterJenisBarang');
  const filterStatusSelect = document.getElementById('filterStatusBarang');
  if (!filterJenisSelect || !filterStatusSelect) return;
  const table = document.getElementById('example1') || document.getElementById('example2');
  if (!table) return;
  const tbody = table.querySelector('tbody');
  if (!tbody) return;
  const rows = tbody.querySelectorAll('tr');

    function filterTable() {
        const selectedJenis = filterJenisSelect.value;
        const selectedStatus = filterStatusSelect.value;
        
        rows.forEach(function(row) {
            const jenisBarangCell = row.querySelector('td:nth-child(3)'); // Kolom ke-3 adalah Jenis Barang
            const statusBarangCell = row.querySelector('td:nth-child(5)'); // Kolom ke-5 adalah Status Barang
            
            if (jenisBarangCell && statusBarangCell) {
                const jenisBarang = jenisBarangCell.textContent.trim();
                const statusBarang = statusBarangCell.textContent.trim();
                
                // Filter berdasarkan jenis barang
                const jenisMatch = selectedJenis === '' || jenisBarang === selectedJenis;
                
                // Filter berdasarkan status barang
                const statusMatch = selectedStatus === '' || statusBarang === selectedStatus;
                
                // Tampilkan baris jika kedua filter cocok
                if (jenisMatch && statusMatch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    }
    // Event listener untuk filter jenis barang
    filterJenisSelect.addEventListener('change', filterTable);
    
    // Event listener untuk filter status barang
    filterStatusSelect.addEventListener('change', filterTable);
});
</script>