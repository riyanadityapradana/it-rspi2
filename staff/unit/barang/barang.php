<?php
require_once("../config/koneksi.php");
// Proses update penyerahan barang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['barang_id']) && isset($_POST['lokasi_id'])) {
  $barang_id = intval($_POST['barang_id']);
  $lokasi_id = intval($_POST['lokasi_id'][0]);
  $kondisi = trim($_POST['kondisi'][0]);
  $keterangan = isset($_POST['keterangan_unit'][0]) ? trim($_POST['keterangan_unit'][0]) : '';
  $unit_index = isset($_POST['unit_index']) ? intval($_POST['unit_index']) : 0;

  // Ambil jumlah dari tb_barang
  $q_jumlah = mysqli_query($config, "SELECT jumlah FROM tb_barang WHERE barang_id='$barang_id'");
  $row_jumlah = mysqli_fetch_assoc($q_jumlah);
  $jumlah = $row_jumlah['jumlah'];

  // Cek jumlah penyerahan sudah ada
  $q_penyerahan_count = mysqli_query($config, "SELECT COUNT(*) as count FROM tb_penyerahan WHERE barang_id='$barang_id'");
  $row_count = mysqli_fetch_assoc($q_penyerahan_count);
  $jumlah_penyerahan_current = $row_count['count'];

  if ($jumlah_penyerahan_current >= $jumlah) {
    header('Location: dashboard_staff.php?unit=barang&err=Semua unit sudah diserahkan!');
    exit;
  }

  // Get existing penyerahan ids
  $penyerahan_ids = [];
  $q_ids = mysqli_query($config, "SELECT penyerahan_id FROM tb_penyerahan WHERE barang_id='$barang_id' ORDER BY penyerahan_id ASC");
  while ($r = mysqli_fetch_assoc($q_ids)) {
    $penyerahan_ids[] = $r['penyerahan_id'];
  }
  $target_id = isset($penyerahan_ids[$unit_index]) ? $penyerahan_ids[$unit_index] : null;
  if ($target_id) {
    $query = mysqli_query($config, "UPDATE tb_penyerahan SET lokasi_id='$lokasi_id', kondisi='$kondisi', keterangan='$keterangan' WHERE penyerahan_id='$target_id'");
  } else {
    $query = mysqli_query($config, "INSERT INTO tb_penyerahan (barang_id, lokasi_id, kondisi, keterangan) VALUES ('$barang_id', '$lokasi_id', '$kondisi', '$keterangan')");
  }
  if ($query) {
    $next_unit = $unit_index + 1;
    if ($next_unit < $jumlah) {
      header('Location: dashboard_staff.php?unit=barang&msg=Unit ' . ($unit_index+1) . ' berhasil diserahkan, lanjut unit ' . ($next_unit+1) . '&continue_barang_id=' . $barang_id . '&next_unit=' . $next_unit);
      exit;
    } else {
      header('Location: dashboard_staff.php?unit=barang&msg=Semua unit berhasil diserahkan!');
      exit;
    }
  } else {
    header('Location: dashboard_staff.php?unit=barang&err=Gagal menyerahkan unit ' . ($unit_index+1) . ': ' . mysqli_error($config));
    exit;
  }
}
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
            <select id="filterJenisBarang" class="form-control form-control-sm" style="display: inline-block; width: auto; margin-right: 10px;">
              <option value="">Semua Jenis Barang</option>
              <option value="Komputer & Laptop">Komputer & Laptop</option>
              <option value="Komponen Komputer & Laptop">Komponen Komputer & Laptop</option>
              <option value="Printer & Scanner">Printer & Scanner</option>
              <option value="Komponen Printer & Scanner">Komponen Printer & Scanner</option>
              <option value="Kamera & Aksesoris">Kamera & Aksesoris</option>
              <option value="Komponen Network">Komponen Network</option>
            </select>
            <select id="filterStatusBarang" class="form-control form-control-sm" style="display: inline-block; width: auto; margin-right: 10px;">
              <option value="">Semua Status</option>
              <option value="Baru">Baru</option>
              <option value="Bekas">Bekas</option>
              <option value="Rusak">Rusak</option>
              <option value="Dalam Perbaikan">Dalam Perbaikan</option>
            </select>
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
                    <th style="width: 50px; text-align: center;">No</th>
                    <th style="width: 200px;">Nama Barang</th>
                    <th style="width: 130px ;">Jenis Barang</th>
                    <th style="width: 70px;">Penyerahan</th>
                    <th style="width: 50px; text-align: center;">Status</th>
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
                    $q = mysqli_query($config, "SELECT b.barang_id, b.nama_barang, b.kode_inventaris, b.jenis_barang, b.nomor_seri, b.ip_address, b.jumlah, b.spesifikasi, b.tanggal_terima, b.foto,
                       CASE
                         WHEN b.jumlah >= 1 THEN (
                           SELECT GROUP_CONCAT(CONCAT('<span class=\"badge badge-', IF(p.kondisi='baru','success',IF(p.kondisi='bekas','info',IF(p.kondisi='rusak','danger','warning'))), '\">', REPLACE(REPLACE(l.nama_lokasi, '<', '&lt;'), '>', '&gt;'), ' (', REPLACE(REPLACE(p.kondisi, '<', '&lt;'), '>', '&gt;'), ')</span>') SEPARATOR ', ')
                           FROM tb_penyerahan p
                           LEFT JOIN tb_lokasi l ON p.lokasi_id = l.lokasi_id
                           WHERE p.barang_id = b.barang_id
                         )
                         ELSE '-'
                       END AS nama_lokasi_gabung,
                       (SELECT COUNT(*) FROM tb_penyerahan WHERE barang_id = b.barang_id) AS jumlah_penyerahan
                    FROM tb_barang b ORDER BY b.barang_id ASC");
                    while ($row = mysqli_fetch_assoc($q)) : ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['nama_barang']); ?><br><small style="color: #666;">Kode Inventaris :<b><?= htmlspecialchars($row['kode_inventaris']); ?></b></small></td>
                        <td><?= htmlspecialchars($row['jenis_barang']); ?></td>
                        <td><?php echo $row['nama_lokasi_gabung']; ?></td>
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
                          <!-- Button Detail Data -->
                          <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalDetailBarang<?= $row['barang_id'] ?>">
                            <i class="fa fa-eye"></i> Detail
                          </button>
                          <?php if ($row['jumlah_penyerahan'] < $row['jumlah']): ?>
                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalUpdateLokasi" onclick="setUpdateLokasiData('<?= $row['barang_id'] ?>', '<?= htmlspecialchars($row['nama_barang']) ?>', '', '', '', '<?= $row['jumlah'] ?>')">
                              <i class="fa fa-handshake"></i> Penyerahan (<?= $row['jumlah_penyerahan'] ?>/<?= $row['jumlah'] ?>)
                            </button>
                            <a href="dashboard_staff.php?unit=delete_barang&id=<?= urlencode($row['barang_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus barang ini?')"><i class="fa fa-trash"></i> Hapus</a>
                          <?php else: ?>
                            <a href="dashboard_staff.php?unit=update_barang&id=<?= urlencode($row['barang_id']); ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Edit</a>
                            <a href="dashboard_staff.php?unit=delete_barang&id=<?= urlencode($row['barang_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus barang ini?')"><i class="fa fa-trash"></i> Hapus</a>
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
                      <form id="formUpdateLokasi" method="POST" action="">
                            <div class="modal-body">
                              <input type="hidden" name="barang_id" id="updateBarangId">
                              <input type="hidden" name="unit_index" id="unitIndex">
                              <div class="form-group">
                                <label>Nama Barang:</label>
                                <input type="text" class="form-control" id="updateNamaBarang" readonly>
                              </div>
                              <div class="form-group">
                                <label id="unitLabel">Lokasi:</label>
                                <select class="form-control select2" name="lokasi_id[]" required>
                                  <option value="">-- Pilih Lokasi --</option>
                                  <?php foreach ($lokasi_list as $lokasi): ?>
                                    <option value="<?= $lokasi['lokasi_id'] ?>"><?= htmlspecialchars($lokasi['nama_lokasi']) ?></option>
                                  <?php endforeach; ?>
                                </select>
                              </div>
                              <div class="form-group">
                                <label>Kondisi:</label>
                                <select class="form-control select2" name="kondisi[]" required>
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
                          document.querySelector('select[name="lokasi_id[]"]').value = lokasiId;
                          document.querySelector('select[name="kondisi[]"]').value = kondisi;
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
                                $badge_class = $p['kondisi'] == 'baru' ? 'success' : ($p['kondisi'] == 'bekas' ? 'info' : ($p['kondisi'] == 'rusak' ? 'danger' : 'warning'));
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
