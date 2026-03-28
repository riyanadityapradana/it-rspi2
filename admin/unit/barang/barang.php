<?php
require_once("../config/koneksi.php");
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
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
          <li class="breadcrumb-item"><a href="dashboard_admin.php?unit=beranda">Home</a></li>
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
			    <div class="card-tools" style="float: right; text-align: right;">
            <form method="get" id="filterForm" style="display:inline-block; margin-right:10px;">
              <input type="hidden" name="unit" value="barang">
              <select id="filterJenisBarang" name="jenis" class="form-control form-control-sm" style="display: inline-block; width: auto;" onchange="document.getElementById('filterForm').submit();">
                <option value="">Semua Jenis Barang</option>
                <option value="Komputer & Laptop" <?php if (isset($_GET['jenis']) && $_GET['jenis'] === 'Komputer & Laptop') echo 'selected'; ?>>Komputer & Laptop</option>
              <option value="Komponen Komputer & Laptop">Komponen Komputer & Laptop</option>
              <option value="Printer & Scanner">Printer & Scanner</option>
              <option value="Komponen Printer & Scanner">Komponen Printer & Scanner</option>
              <option value="Kamera & Aksesoris">Kamera & Aksesoris</option>
              <option value="Komponen Network">Komponen Network</option>
              </select>
            </form>
            <form method="get" id="filterFormKondisi" style="display:inline-block;">
              <input type="hidden" name="unit" value="barang">
              <select id="filterStatusBarang" name="kondisi" class="form-control form-control-sm" style="display: inline-block; width: auto; margin-right: 10px;" onchange="document.getElementById('filterFormKondisi').submit();">
                <option value="">Semua Status</option>
                <option value="Baru" <?php if (isset($_GET['kondisi']) && $_GET['kondisi'] === 'Baru') echo 'selected'; ?>>Baru</option>
                <option value="Bekas" <?php if (isset($_GET['kondisi']) && $_GET['kondisi'] === 'Bekas') echo 'selected'; ?>>Bekas</option>
                <option value="Rusak" <?php if (isset($_GET['kondisi']) && $_GET['kondisi'] === 'Rusak') echo 'selected'; ?>>Rusak</option>
                <option value="Dalam Perbaikan" <?php if (isset($_GET['kondisi']) && $_GET['kondisi'] === 'Dalam Perbaikan') echo 'selected'; ?>>Dalam Perbaikan</option>
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
                <table id="example1" class="table table-bordered table-striped">
                <thead style="background:rgb(52, 58, 64, 1)">
                    <tr>
                    <th style="width: 50px; text-align: center; color: white;">No</th>
                    <th style="width: 200px; color: white;">Nama Barang</th>
                    <th style="width: 130px ; color: white;">Jenis Barang</th>
                    <th style="width: 70px; text-align: center; color: white;">Lokasi Awal</th>
                    <th style="width: 70px; text-align: center; color: white;">Lokasi Saat Ini</th>
                    <th style="width: 50px; text-align: center; color: white;">Status Penyerahan</th>
                    <th style="width: 50px; text-align: center; color: white;">Kondisi</th>
                    <th style="width: 100px; text-align: center; color: white;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    // Ambil lokasi
                    $lokasi_q = mysqli_query($config, "SELECT lokasi_id, nama_lokasi FROM tb_lokasi ORDER BY nama_lokasi ASC");
                    $lokasi_list = [];
                    while ($row = mysqli_fetch_assoc($lokasi_q)) {
                      $lokasi_list[] = $row;
                    }
                    // Build filters from GET parameters
                    $where = array();
                    if (!empty($_GET['jenis'])) {
                      $jenis = mysqli_real_escape_string($config, $_GET['jenis']);
                      $where[] = "b.jenis_barang = '{$jenis}'";
                    }
                    if (!empty($_GET['kondisi'])) {
                      $kondisi = mysqli_real_escape_string($config, $_GET['kondisi']);
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
                    $barangRows = [];
                    while ($row = mysqli_fetch_assoc($q)) {
                      $barangRows[] = $row;
                    }
                    foreach ($barangRows as $row) : ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['nama_barang']); ?><br><small style="color: #666;">Kode Inventaris :<b><?= htmlspecialchars($row['kode_inventaris']); ?></b></small></td>
                        <td><?= htmlspecialchars($row['jenis_barang']); ?></td>
                        <td class="text-center">
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
                        <td class="text-center">
                          <?php
                            $kval = isset($row['kondisi']) ? strtolower(trim($row['kondisi'])) : '';
                            switch ($kval) {
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
                          <span class="badge badge-<?= $badge ?>"><?= htmlspecialchars($row['kondisi']) ?></span>
                        </td>
                        <td class="text-center">
                          <!-- Button Detail Data -->
                          <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalDetailBarang<?= $row['barang_id'] ?>">
                            <i class="fa fa-eye"></i> Detail
                          </button>
                        </td>
                </tr>
                <?php endforeach; ?>

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
                  <form method="post" action="">
                    <input type="hidden" name="action" value="perbaikan">
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
                        <select id="perbaikanUnitMelapor" class="form-control" disabled>
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
                        <select name="tindakan_perbaikan" id="perbaikanTindakan" class="form-control" required>
                          <option value="Service luar">Service luar</option>
                          <option value="Service sendiri">Service sendiri</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label>Status Perbaikan</label>
                        <select name="status_perbaikan" class="form-control" required>
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
                  if (sel) sel.value = lokasiId;
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

            <!-- Modal Detail Barang -->
            <?php foreach ($barangRows as $detailRow): ?>
            <div class="modal fade" id="modalDetailBarang<?= $detailRow['barang_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalDetailBarangLabel<?= $detailRow['barang_id'] ?>" aria-hidden="true">
              <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                  <div class="modal-header border-bottom">
                    <div>
                      <h5 class="modal-title" id="modalDetailBarangLabel<?= $detailRow['barang_id'] ?>" style="font-size: 18px; font-weight: 600;">Detail Data Barang</h5>
                      <small class="text-muted" style="font-size: 13px;">Informasi lengkap data barang</small>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body" style="padding: 20px;">
                    <div style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #e0e0e0;">
                      <h6 style="font-weight: 600; margin-bottom: 15px; font-size: 16px;">Informasi Barang</h6>

                      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                        <div>
                          <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Foto Barang</p>                          <?php if (!empty($detailRow['foto'])): ?>
                            <div class="detail-image-viewer" data-image-viewer>
                              <div class="detail-image-frame" data-image-frame>
                                <div class="detail-image-stage">
                                  <img src="/it-rspi2/staff/unit/barang/foto-barang/<?= htmlspecialchars($detailRow['foto']) ?>" alt="Foto Barang" class="detail-image-element" data-image-element>
                                </div>
                              </div>
                              <div class="detail-image-controls">
                                <small class="text-muted">Double click pada foto untuk zoom cepat.</small>
                                <div class="detail-image-actions">
                                  <button type="button" class="btn btn-outline-secondary btn-sm" data-action="zoom-out" title="Zoom Out">
                                    <i class="fas fa-search-minus"></i>
                                  </button>
                                  <button type="button" class="btn btn-outline-secondary btn-sm" data-action="reset" title="Reset Zoom">
                                    <i class="fas fa-sync-alt"></i>
                                  </button>
                                  <button type="button" class="btn btn-outline-secondary btn-sm" data-action="zoom-in" title="Zoom In">
                                    <i class="fas fa-search-plus"></i>
                                  </button>
                                  <span class="badge badge-light detail-image-zoom-level" data-image-zoom-level>100%</span>
                                </div>
                              </div>
                            </div>
                          <?php else: ?>
                            <div style="background: #f5f5f5; border: 1px dashed #ccc; border-radius: 6px; padding: 20px; text-align: center; color: #999;">
                              -
                            </div>
                          <?php endif; ?>
                        </div>

                        <div>
                          <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Spesifikasi</p>
                          <div style="background: #fff; border: 1px solid #e0e0e0; border-radius: 6px; padding: 12px; min-height: 110px;">
                            <?= !empty($detailRow['spesifikasi']) ? nl2br(htmlspecialchars($detailRow['spesifikasi'])) : '-' ?>
                          </div>
                        </div>
                      </div>

                      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                        <div>
                          <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Nama Barang</p>
                          <strong style="font-size: 15px;"><?= htmlspecialchars($detailRow['nama_barang']) ?></strong>
                        </div>
                        <div>
                          <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Kode Inventaris</p>
                          <strong style="font-size: 15px;"><?= htmlspecialchars($detailRow['kode_inventaris']) ?></strong>
                        </div>
                      </div>

                      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                        <div>
                          <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Jenis Barang</p>
                          <strong style="font-size: 15px;"><?= htmlspecialchars($detailRow['jenis_barang']) ?></strong>
                        </div>
                          <?php if ($detailRow['jenis_barang'] == 'Komputer & Laptop'): ?>
                          <div>
                            <p style="font-size: 13px; color: #999; margin-bottom: 5px;">IP Address</p>
                            <strong style="font-size: 15px;"><?= !empty($detailRow['ip_address']) ? htmlspecialchars($detailRow['ip_address']) : '-' ?></strong>
                          </div>
                          <?php else: ?>
                          <div>
                            <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Nomor Seri</p>
                            <strong style="font-size: 15px;"><?= !empty($detailRow['nomor_seri']) ? htmlspecialchars($detailRow['nomor_seri']) : '-' ?></strong>
                          </div>
                          <?php endif; ?>
                        </div>

                      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                          <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Nomor Seri</p>
                          <strong style="font-size: 15px;"><?= !empty($detailRow['nomor_seri']) ? htmlspecialchars($detailRow['nomor_seri']) : '-' ?></strong>
                        </div>
                        <div>
                          <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Jumlah</p>
                          <strong style="font-size: 15px;"><?= htmlspecialchars($detailRow['jumlah']) ?></strong>
                        </div>
                      </div>
                    </div>

                    <div style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #e0e0e0;">
                      <h6 style="font-weight: 600; margin-bottom: 15px; font-size: 16px;">Informasi Lokasi</h6>

                      <div style="margin-bottom: 15px;">
                        <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Riwayat Penyerahan / Lokasi</p>
                        <div style="background: #fff; border: 1px solid #e0e0e0; border-radius: 6px; padding: 12px;">
                            <?php
                            $q_penyerahan = mysqli_query($config, "SELECT p.*, l.nama_lokasi FROM tb_penyerahan p LEFT JOIN tb_lokasi l ON p.lokasi_id = l.lokasi_id WHERE p.barang_id='{$detailRow['barang_id']}'");
                            if (mysqli_num_rows($q_penyerahan) > 0) {
                              while ($p = mysqli_fetch_assoc($q_penyerahan)) {
                                $badge_class = $p['kondisi'] == 'baru' ? 'success' : ($p['kondisi'] == 'bekas' ? 'secondary' : ($p['kondisi'] == 'rusak' ? 'danger' : 'warning'));
                                echo '<span class="badge badge-' . $badge_class . '">' . htmlspecialchars($p['nama_lokasi']) . ' (' . htmlspecialchars($p['kondisi']) . ')</span> ';
                              }
                            } else {
                              echo 'Belum diserahkan';
                            }
                            ?>
                        </div>
                      </div>

                      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                          <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Lokasi Saat Ini</p>
                          <strong style="font-size: 15px;"><?= !empty($detailRow['lokasi_saat_ini']) ? htmlspecialchars($detailRow['lokasi_saat_ini']) : '-' ?></strong>
                        </div>
                        <div>
                          <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Tanggal Terima</p>
                          <strong style="font-size: 15px;"><?= !empty($detailRow['tanggal_terima']) ? date('d-m-Y', strtotime($detailRow['tanggal_terima'])) : '-' ?></strong>
                        </div>
                      </div>
                    </div>

                    <div>
                      <h6 style="font-weight: 600; margin-bottom: 15px; font-size: 16px;">Informasi Tambahan</h6>

                      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                        <div>
                          <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Kondisi</p>
                          <?php
                            $detailKondisi = isset($detailRow['kondisi']) ? strtolower(trim($detailRow['kondisi'])) : '';
                            switch ($detailKondisi) {
                              case 'baru':
                                $detailBadge = 'success';
                                break;
                              case 'bekas':
                                $detailBadge = 'secondary';
                                break;
                              case 'rusak':
                                $detailBadge = 'danger';
                                break;
                              default:
                                if (strpos($detailKondisi, 'perbaikan') !== false) {
                                  $detailBadge = 'warning';
                                } else {
                                  $detailBadge = 'secondary';
                                }
                            }
                          ?>
                          <span class="badge badge-<?= $detailBadge ?>" style="font-size: 13px; padding: 6px 10px;"><?= htmlspecialchars($detailRow['kondisi']) ?></span>
                        </div>
                        <div>
                          <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Status Penyerahan</p>
                          <?php if ($detailRow['jumlah_penyerahan'] >= $detailRow['jumlah']): ?>
                            <span class="badge badge-success" style="font-size: 13px; padding: 6px 10px;">Completed</span>
                          <?php elseif ($detailRow['jumlah_penyerahan'] > 0): ?>
                            <span class="badge badge-primary" style="font-size: 13px; padding: 6px 10px;">In Progress (<?= $detailRow['jumlah_penyerahan'] ?>/<?= $detailRow['jumlah'] ?>)</span>
                          <?php else: ?>
                            <span class="badge badge-secondary" style="font-size: 13px; padding: 6px 10px;">Belum Diserahkan</span>
                          <?php endif; ?>
                        </div>
                      </div>

                      <div style="margin-bottom: 15px;">
                        <p style="font-size: 13px; color: #999; margin-bottom: 5px;">Keterangan Penyerahan</p>
                        <div style="background: #fff; border: 1px solid #e0e0e0; border-radius: 6px; padding: 12px;">
                            <?php
                            if (mysqli_num_rows($q_penyerahan) > 0) {
                              mysqli_data_seek($q_penyerahan, 0); // Reset pointer
                              while ($p = mysqli_fetch_assoc($q_penyerahan)) {
                                echo '<strong>' . htmlspecialchars($p['nama_lokasi']) . ':</strong> ' . htmlspecialchars($p['keterangan']) . '<br>';
                              }
                            } else {
                              echo '-';
                            }
                            ?>
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

<style>
  .detail-image-viewer {
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 14px;
  }

  .detail-image-frame {
    position: relative;
    overflow: auto;
    min-height: 220px;
    max-height: 320px;
    background: linear-gradient(135deg, #fdfdfd 0%, #f1f3f5 100%);
    border: 1px solid #dee2e6;
    border-radius: 8px;
  }

  .detail-image-frame.is-zoomed {
    cursor: grab;
  }

  .detail-image-frame.is-dragging {
    cursor: grabbing;
  }

  .detail-image-stage {
    display: inline-flex;
    min-width: 100%;
    min-height: 100%;
    align-items: center;
    justify-content: center;
  }

  .detail-image-element {
    display: block;
    width: 100%;
    height: auto;
    max-width: 100%;
    transition: width 0.2s ease;
    cursor: zoom-in;
    user-select: none;
    -webkit-user-drag: none;
    flex-shrink: 0;
    border-radius: 4px;
  }

  .detail-image-frame.is-zoomed .detail-image-element {
    cursor: grab;
  }

  .detail-image-frame.is-dragging .detail-image-element {
    cursor: grabbing;
  }

  .detail-image-controls {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 12px;
  }

  .detail-image-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
  }

  .detail-image-actions button {
    min-width: 42px;
  }

  .detail-image-zoom-level {
    display: inline-flex;
    align-items: center;
    padding: 0 12px;
  }
</style>
<script>
function initDetailImageViewer(viewer) {
  var frame = viewer.querySelector('[data-image-frame]');
  var img = viewer.querySelector('[data-image-element]');
  var indicator = viewer.querySelector('[data-image-zoom-level]');
  var state = {
    zoom: 1,
    isDragging: false,
    startX: 0,
    startY: 0,
    scrollLeft: 0,
    scrollTop: 0
  };

  if (!frame || !img || !indicator) {
    return;
  }

  function stopDrag() {
    state.isDragging = false;
    frame.classList.remove('is-dragging');
  }

  function centerFrame() {
    frame.scrollLeft = Math.max(0, (frame.scrollWidth - frame.clientWidth) / 2);
    frame.scrollTop = Math.max(0, (frame.scrollHeight - frame.clientHeight) / 2);
  }

  function setZoom(value) {
    state.zoom = Math.min(3, Math.max(0.5, value));
    img.style.width = (state.zoom * 100) + '%';
    img.style.maxWidth = state.zoom > 1 ? 'none' : '100%';
    indicator.textContent = Math.round(state.zoom * 100) + '%';
    frame.classList.toggle('is-zoomed', state.zoom > 1);

    if (state.zoom <= 1) {
      stopDrag();
      frame.scrollLeft = 0;
      frame.scrollTop = 0;
    } else {
      centerFrame();
    }
  }

  viewer.querySelector('[data-action="zoom-in"]').addEventListener('click', function() {
    setZoom(state.zoom + 0.25);
  });

  viewer.querySelector('[data-action="zoom-out"]').addEventListener('click', function() {
    setZoom(state.zoom - 0.25);
  });

  viewer.querySelector('[data-action="reset"]').addEventListener('click', function() {
    setZoom(1);
  });

  img.addEventListener('dblclick', function() {
    if (state.zoom > 1) {
      setZoom(1);
    } else {
      setZoom(2);
    }
  });

  img.addEventListener('dragstart', function(event) {
    event.preventDefault();
  });

  frame.addEventListener('mousedown', function(event) {
    if (state.zoom <= 1) {
      return;
    }

    state.isDragging = true;
    state.startX = event.clientX;
    state.startY = event.clientY;
    state.scrollLeft = frame.scrollLeft;
    state.scrollTop = frame.scrollTop;
    frame.classList.add('is-dragging');
    event.preventDefault();
  });

  frame.addEventListener('mousemove', function(event) {
    if (!state.isDragging || state.zoom <= 1) {
      return;
    }

    frame.scrollLeft = state.scrollLeft - (event.clientX - state.startX);
    frame.scrollTop = state.scrollTop - (event.clientY - state.startY);
  });

  frame.addEventListener('mouseleave', stopDrag);
  frame.addEventListener('mouseup', stopDrag);
  document.addEventListener('mouseup', stopDrag);

  setZoom(1);
}

document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('[data-image-viewer]').forEach(initDetailImageViewer);

  $('.modal').on('hidden.bs.modal', function() {
    this.querySelectorAll('[data-image-viewer]').forEach(function(viewer) {
      var resetButton = viewer.querySelector('[data-action="reset"]');
      if (resetButton) {
        resetButton.click();
      }
    });
  });
});
</script>
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
              <option value="baik">Baik</option>
              <option value="rusak">Rusak</option>
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
// Filter berdasarkan jenis barang dan status barang
document.addEventListener('DOMContentLoaded', function() {
    const filterJenisSelect = document.getElementById('filterJenisBarang');
    const filterStatusSelect = document.getElementById('filterStatusBarang');
    const table = document.getElementById('example1');
    const tbody = table.querySelector('tbody');
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



