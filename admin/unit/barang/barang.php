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
                <table id="example2" class="table table-bordered table-striped">
                <thead style="background:rgb(52, 58, 64, 1)">
                    <tr>
                    <th style="width: 50px; text-align: center;">No</th>
                    <th style="width: 200px;">Nama Barang</th>
                    <th style="width: 130px ;">Jenis Barang</th>
                    <th style="width: 70px; text-align: center;">Lokasi Awal</th>
                    <th style="width: 70px; text-align: center;">Lokasi Saat Ini</th>
                    <th style="width: 50px; text-align: center;">Status Penyerahan</th>
                    <th style="width: 50px; text-align: center;">Kondisi</th>
                    <th style="width: 200px; text-align: center;">Aksi</th>
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
                    while ($row = mysqli_fetch_assoc($q)) : ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['nama_barang']); ?><br><small style="color: #666;">Kode Inventaris :<b><?= htmlspecialchars($row['kode_inventaris']); ?></b></small></td>
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
                        <td>
                          <!-- Button Detail Data -->
                          <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalDetailBarang<?= $row['barang_id'] ?>">
                            <i class="fa fa-eye"></i> Detail
                          </button>
                        </td>
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
            <?php foreach ($q as $detailRow): ?>
            <div class="modal fade" id="modalDetailBarang<?= $detailRow['barang_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalDetailBarangLabel<?= $detailRow['barang_id'] ?>" aria-hidden="true">
              <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-radius: 12px;">
                  <div class="modal-header" style="background: #1976d2; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                    <h5 class="modal-title" id="modalDetailBarangLabel<?= $detailRow['barang_id'] ?>"><i class="fa fa-eye"></i> Detail Barang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group">
                          <label><strong>Foto Barang:</strong></label>
                          <?php if (!empty($detailRow['foto'])): ?>
                            <div class="p-2" style="background:#fff; border-radius:6px; border:1px solid #90caf9; text-align:center;">
                              <img src="unit/barang/foto-barang/<?= htmlspecialchars($detailRow['foto']) ?>" alt="Foto Barang" style="max-width:180px;max-height:180px;">
                            </div>
                          <?php else: ?>
                            <div class="p-2" style="background:#fff; border-radius:6px; border:1px solid #90caf9; text-align:center;">-</div>
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label><strong>Nama Barang:</strong></label>
                          <div class="p-2" style="background:#fff; border-radius:6px; border:1px solid #90caf9;"> <?= htmlspecialchars($detailRow['nama_barang']) ?> </div>
                        </div>
                        <div class="form-group">
                          <label><strong>Kode Inventaris:</strong></label>
                          <div class="p-2" style="background:#fff; border-radius:6px; border:1px solid #90caf9;"> <?= htmlspecialchars($detailRow['kode_inventaris']) ?> </div>
                        </div>
                        <div class="form-group">
                          <label><strong>Jenis Barang:</strong></label>
                          <div class="p-2" style="background:#fff; border-radius:6px; border:1px solid #90caf9;"> <?= htmlspecialchars($detailRow['jenis_barang']) ?> </div>
                        </div>
                        <div class="form-group">
                          <label><strong>Nomor Seri:</strong></label>
                          <div class="p-2" style="background:#fff; border-radius:6px; border:1px solid #90caf9;"> <?= htmlspecialchars($detailRow['nomor_seri']) ?> </div>
                        </div>
                        <div class="form-group">
                          <?php if ($detailRow['jenis_barang'] == 'Komputer & Laptop'): ?>
                          <label><strong>IP Address:</strong></label>
                          <div class="p-2" style="background:#fff; border-radius:6px; border:1px solid #90caf9;"> <?= htmlspecialchars($detailRow['ip_address']) ?> </div>
                          <?php endif; ?>
                        </div>
                         <div class="form-group">
                          <label><strong>Jumlah:</strong></label>
                          <div class="p-2" style="background:#fff; border-radius:6px; border:1px solid #90caf9;"> <?= htmlspecialchars($detailRow['jumlah']) ?> </div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label><strong>Tanggal Terima:</strong></label>
                          <div class="p-2" style="background:#fff; border-radius:6px; border:1px solid #90caf9;"> <?= !empty($detailRow['tanggal_terima']) ? date('d-m-Y', strtotime($detailRow['tanggal_terima'])) : '-' ?> </div>
                        </div>
                        <div class="form-group">
                          <label><strong>Lokasi:</strong></label>
                          <div class="p-2" style="background:#fff; border-radius:6px; border:1px solid #90caf9;">
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
                        <div class="form-group">
                          <label><strong>Keterangan:</strong></label>
                          <div class="p-2" style="background:#fff; border-radius:6px; border:1px solid #90caf9;">
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
                        <div class="form-group">
                          <label><strong>Spesifikasi:</strong></label>
                          <div class="p-2" style="background:#fff; border-radius:6px; border:1px solid #90caf9;"> <?= nl2br(htmlspecialchars($detailRow['spesifikasi'])) ?> </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer" style="background: #e3f2fd; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
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