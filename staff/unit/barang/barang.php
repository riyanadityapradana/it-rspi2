<?php
require_once("../config/koneksi.php");
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// Proses simpan perbaikan barang (action=perbaikan)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'perbaikan') {
  $barang_id = isset($_POST['barang_id']) ? intval($_POST['barang_id']) : 0;
  // penyerahan_id removed; we use unit_melapor field instead
  $tanggal_lapor = isset($_POST['tanggal_lapor']) && $_POST['tanggal_lapor'] !== '' ? mysqli_real_escape_string($config, $_POST['tanggal_lapor']) : date('Y-m-d H:i:s');
  $deskripsi_kerusakan = isset($_POST['deskripsi_kerusakan']) ? mysqli_real_escape_string($config, trim($_POST['deskripsi_kerusakan'])) : '';
  $tindakan_perbaikan = isset($_POST['tindakan_perbaikan']) ? mysqli_real_escape_string($config, $_POST['tindakan_perbaikan']) : '';
  $status_perbaikan = isset($_POST['status_perbaikan']) ? mysqli_real_escape_string($config, $_POST['status_perbaikan']) : 'diajukan';
  // tanggal_selesai selalu NULL sesuai permintaan
  $tanggal_selesai = null;
  $keterangan_perbaikan = isset($_POST['keterangan_perbaikan']) ? mysqli_real_escape_string($config, trim($_POST['keterangan_perbaikan'])) : '';
  // unit_melapor diambil dari form (berdasarkan lokasi barang default)
  $unit_melapor = isset($_POST['unit_melapor']) && $_POST['unit_melapor'] !== '' ? intval($_POST['unit_melapor']) : null;

  // Jika tindakan_perbaikan tidak dipilih oleh user, beri default 'service_luar'
  if ($tindakan_perbaikan === '') {
    $tindakan_perbaikan = 'service_luar';
  }

  if ($barang_id <= 0) {
    header('Location: dashboard_staff.php?unit=barang&err=Data perbaikan tidak lengkap');
    exit;
  }

  // Build insert query
  // tanggal_selesai selalu NULL
  $tanggal_selesai_sql = "NULL";
  $unit_sql = $unit_melapor !== null ? "'{$unit_melapor}'" : "NULL";

  // teknisi: jika tindakan = service_sendiri -> isi dengan nama user yang login; jika service_luar -> NULL
  if ($tindakan_perbaikan === 'service_sendiri') {
    $teknisi_val = isset($_SESSION['nama_lengkap']) ? mysqli_real_escape_string($config, $_SESSION['nama_lengkap']) : (isset($_SESSION['username']) ? mysqli_real_escape_string($config, $_SESSION['username']) : '');
    $teknisi_sql = $teknisi_val !== '' ? "'{$teknisi_val}'" : "NULL";
  } else {
    $teknisi_sql = "NULL";
  }

  $ins_sql = "INSERT INTO tb_perbaikan_barang (barang_id, tanggal_lapor, deskripsi_kerusakan, tindakan_perbaikan, status, tanggal_selesai, teknisi, keterangan, unit_melapor) VALUES ('{$barang_id}', '{$tanggal_lapor}', '{$deskripsi_kerusakan}', '{$tindakan_perbaikan}', '{$status_perbaikan}', {$tanggal_selesai_sql}, {$teknisi_sql}, '{$keterangan_perbaikan}', {$unit_sql})";

  mysqli_begin_transaction($config);
  $ins = mysqli_query($config, $ins_sql);
  if ($ins) {
    // Update kondisi di tb_barang sesuai status perbaikan
    if ($status_perbaikan === 'tidak_dapat_diperbaiki') {
      $upd = mysqli_query($config, "UPDATE tb_barang SET kondisi='rusak' WHERE barang_id='{$barang_id}'");
    } elseif ($status_perbaikan === 'selesai') {
      $upd = mysqli_query($config, "UPDATE tb_barang SET kondisi='baik' WHERE barang_id='{$barang_id}'");
    } elseif ($status_perbaikan === 'proses' || $status_perbaikan === 'diajukan') {
      // saat sedang diajukan atau dalam proses, set kondisi menjadi Dalam Perbaikan
      $upd = mysqli_query($config, "UPDATE tb_barang SET kondisi='Dalam Perbaikan' WHERE barang_id='{$barang_id}'");
    } else {
      $upd = true;
    }

    if ($upd) {
      mysqli_commit($config);
      header('Location: dashboard_staff.php?unit=barang&msg=Perbaikan berhasil disimpan');
      exit;
    } else {
      mysqli_rollback($config);
      header('Location: dashboard_staff.php?unit=barang&err=Gagal update kondisi barang: ' . mysqli_error($config));
      exit;
    }
  } else {
    mysqli_rollback($config);
    header('Location: dashboard_staff.php?unit=barang&err=Gagal menyimpan perbaikan: ' . mysqli_error($config));
    exit;
  }
}
// Proses simpan pemindahan barang (action=pindah)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'pindah') {
  $barang_id = isset($_POST['barang_id']) ? intval($_POST['barang_id']) : 0;
  $lokasi_asal = isset($_POST['lokasi_asal']) ? intval($_POST['lokasi_asal']) : 0;
  $lokasi_tujuan = isset($_POST['lokasi_tujuan']) ? intval($_POST['lokasi_tujuan']) : 0;
  $tanggal_mutasi = isset($_POST['tanggal_mutasi']) ? mysqli_real_escape_string($config, $_POST['tanggal_mutasi']) : date('Y-m-d');
  $keterangan = isset($_POST['keterangan']) ? mysqli_real_escape_string($config, trim($_POST['keterangan'])) : '';
  $id_user = isset($_SESSION['id_user']) ? intval($_SESSION['id_user']) : 0;

  if ($barang_id <= 0 || $lokasi_tujuan <= 0) {
    header('Location: dashboard_staff.php?unit=barang&err=Data pemindahan tidak lengkap');
    exit;
  }

  // if user id not available, insert NULL to avoid FK constraint error
  $id_user_sql = $id_user > 0 ? "'{$id_user}'" : "NULL";

  // gunakan transaksi agar insert mutasi dan update lokasi barang berjalan atomik
  mysqli_begin_transaction($config);
  $ins = mysqli_query($config, "INSERT INTO tb_mutasi_barang (barang_id, lokasi_asal, lokasi_tujuan, tanggal_mutasi, id_user, keterangan) VALUES ('{$barang_id}', '{$lokasi_asal}', '{$lokasi_tujuan}', '{$tanggal_mutasi}', {$id_user_sql}, '{$keterangan}')");
  if ($ins) {
    $upd = mysqli_query($config, "UPDATE tb_barang SET lokasi_id='{$lokasi_tujuan}' WHERE barang_id='{$barang_id}'");
    if ($upd) {
      // Setelah lokasi diupdate, set kondisi menjadi 'Bekas' karena barang dipindahkan
      $upd_kond = mysqli_query($config, "UPDATE tb_barang SET kondisi='Bekas' WHERE barang_id='{$barang_id}'");
      if ($upd_kond) {
        mysqli_commit($config);
        header('Location: dashboard_staff.php?unit=barang&msg=Pemindahan barang berhasil disimpan');
        exit;
      } else {
        mysqli_rollback($config);
        header('Location: dashboard_staff.php?unit=barang&err=Gagal update kondisi barang: ' . mysqli_error($config));
        exit;
      }
    } else {
      mysqli_rollback($config);
      header('Location: dashboard_staff.php?unit=barang&err=Gagal update lokasi barang: ' . mysqli_error($config));
      exit;
    }
  } else {
    mysqli_rollback($config);
    header('Location: dashboard_staff.php?unit=barang&err=Gagal menyimpan pemindahan: ' . mysqli_error($config));
    exit;
  }
}
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
    // Setelah berhasil insert/update penyerahan, update kondisi utama di tb_barang
    $kondisi_for_db = '';
    $k_lower = strtolower($kondisi);
    if ($k_lower === 'baru' || $k_lower === 'Baru' || $k_lower === 'BARU') {
      $kondisi_for_db = 'Baru';
    } elseif ($k_lower === 'bekas') {
      $kondisi_for_db = 'Bekas';
    } elseif ($k_lower === 'rusak') {
      $kondisi_for_db = 'Rusak';
    } else {
      // default: use submitted value (sanitized)
      $kondisi_for_db = mysqli_real_escape_string($config, $kondisi);
    }
    if ($kondisi_for_db !== '') {
      mysqli_query($config, "UPDATE tb_barang SET kondisi='" . mysqli_real_escape_string($config, $kondisi_for_db) . "' WHERE barang_id='$barang_id'");
    }
    // Update lokasi_id di tb_barang sesuai lokasi penyerahan
    $lokasi_id_int = intval($lokasi_id);
    if ($lokasi_id_int > 0) {
      mysqli_query($config, "UPDATE tb_barang SET lokasi_id='{$lokasi_id_int}' WHERE barang_id='{$barang_id}'");
    }
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
                        <td><?= htmlspecialchars($row['nama_barang']); ?>
                          <br><small style="color: #2266e4;">Kode Inventaris :<b><?= htmlspecialchars($row['kode_inventaris']); ?></b></small>
                          <br><small style="color: #2266e4;">S/N : <b><?= htmlspecialchars($row['nomor_seri']); ?></b></small>
                          <br><button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalPindah" onclick="setPindahData('<?= $row['barang_id'] ?>', '<?= htmlspecialchars($row['kode_inventaris']) ?>', '<?= htmlspecialchars($row['nama_barang']) ?>', '<?= $row['lokasi_id'] ?>')" responsive>
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
                  <form method="post" action="">
                    <input type="hidden" name="action" value="pindah">
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
                        <select id="pindahLokasiAsalDisplay" class="form-control" disabled>
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
                        <select name="lokasi_tujuan" class="form-control" required>
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
                              <img src="unit/barang/foto-barang/<?= htmlspecialchars($detailRow['foto']) ?>" alt="Foto Barang" style="max-width:180px;max-height:180px;" class="zoomable" data-full="unit/barang/foto-barang/<?= htmlspecialchars($detailRow['foto']) ?>">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.zoomable').forEach(function(img) {
        img.style.cursor = 'pointer';
        img.addEventListener('click', function() {
            var src = img.getAttribute('data-full') || img.src;
            var modalImg = document.getElementById('modalImage');
            if (modalImg) {
                modalImg.src = src;
                $('#imageModal').modal('show');
            } else {
                window.open(src, '_blank');
            }
        });
    });
});
</script>

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