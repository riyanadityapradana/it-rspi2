<?php
require_once("../config/koneksi.php");
$id_staff = $_SESSION['id_user'];

// Proses insert data barang dari modal dengan POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insert_barang'])) {
  $pengajuan_id   = isset($_POST['pengajuan_id'])   ? mysqli_real_escape_string($config, $_POST['pengajuan_id'])   : '';
  $nama_barang    = isset($_POST['nama_barang'])    ? mysqli_real_escape_string($config, $_POST['nama_barang'])    : '';
  $jenis_barang   = isset($_POST['jenis_barang'])   ? mysqli_real_escape_string($config, $_POST['jenis_barang'])   : '';
  $nomor_seri     = isset($_POST['nomor_seri'])     ? mysqli_real_escape_string($config, $_POST['nomor_seri'])     : '';
  $ip_address     = isset($_POST['ip_address'])     ? mysqli_real_escape_string($config, $_POST['ip_address'])     : '';
  $jumlah         = isset($_POST['jumlah'])         ? mysqli_real_escape_string($config, $_POST['jumlah'])         : '';
  $harga          = isset($_POST['harga'])          ? mysqli_real_escape_string($config, $_POST['harga'])          : '';
  $spesifikasi    = isset($_POST['spesifikasi'])    ? mysqli_real_escape_string($config, $_POST['spesifikasi'])    : '';
  $tanggal_terima = isset($_POST['tanggal_terima']) ? mysqli_real_escape_string($config, $_POST['tanggal_terima']) : '';

  $sql = "INSERT INTO tb_barang (pengajuan_id, nama_barang, jenis_barang, nomor_seri, ip_address, jumlah, harga, spesifikasi, tanggal_terima) VALUES (
    '$pengajuan_id', '$nama_barang', '$jenis_barang', '$nomor_seri', '$ip_address', '$jumlah', '$harga', '$spesifikasi', '$tanggal_terima')";
  if (mysqli_query($config, $sql)) {
    header('Location: dashboard_staff.php?unit=barang&msg=Barang berhasil ditambahkan!');
      exit;
  } else {
    header('Location: dashboard_staff.php?unit=pengajuan&err=Gagal menambah barang!');
      exit;
  }
}
?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>PENGAJUAN BARANG DARI UNIT IT</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="dashboard_staff.php?unit=beranda">Home</a></li>
          <li class="breadcrumb-item active">Pengajuan Barang</li>
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
            <a href="?unit=create_pengajuan" class="btn btn-tool btn-sm" style="background:rgba(0, 123, 255, 1)">
              <i class="fas fa-plus-square" style="color: white;"> Tambah Pengajuan</i>
            </a>
          </div>
			    <div class="card-tools" style="float: right; text-align: right;">
            <a href="#" class="btn btn-tool btn-sm" data-card-widget="collapse" style="background:rgba(69, 77, 85, 1)">
                  <i class="fas fa-bars"></i>
            </a>
          </div>
			</div>
      <div class="card-body">
      <table id="example1" class="table table-bordered table-striped">
        <thead style="background:rgb(129, 2, 0, 1); color:white;">
          <tr>
            <th>No</th>
            <th>Nama Barang</th>
            <th>Status</th>
            <th style="text-align: center;">Tanggal Pengajuan</th>
            <th style="text-align: center;">Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php
          $no = 1;
          $q = mysqli_query($config, "SELECT * FROM tb_pengajuan WHERE id_user='$id_staff' ORDER BY tanggal_pengajuan DESC");
          while ($row = mysqli_fetch_assoc($q)) : ?>
          <tr>
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($row['nama_barang']); ?></td>
            <!-- <td> 
              <?php
                $harga = is_numeric($row['perkiraan_harga']) ? 'Rp. ' . number_format($row['perkiraan_harga'], 0, ',', '.') : '-';
                echo htmlspecialchars($harga);
              ?>
            </td> -->
            <td>
            <?php
            $status = $row['status'];
            if ($status == 'diajukan') {
              echo '<span class="badge badge-warning" style="font-size:1em;">Diajukan</span>';
            } elseif ($status == 'disetujui') {
              echo '<span class="badge badge-success" style="font-size:1em;">Disetujui</span>';
            } elseif ($status == 'ditolak') {
              echo '<span class="badge badge-danger" style="font-size:1em;">Ditolak</span>';
            } elseif ($status == 'selesai') {
              echo '<span class="badge badge-primary" style="font-size:1em;">Selesai</span>';
            } else {
              echo '<span class="badge badge-secondary" style="font-size:1em;">' . htmlspecialchars($status) . '</span>';
            }
            ?>
            </td>
            <td>
              <?php
                $bulanIndo = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                $tgl = '-';
                if (!empty($row['tanggal_pengajuan'])) {
                  $arr = explode('-', $row['tanggal_pengajuan']);
                  if (count($arr) === 3) {
                    $tgl = intval($arr[2]) . ' ' . $bulanIndo[intval($arr[1])-1] . ' ' . $arr[0];
                  } else {
                    $tgl = htmlspecialchars($row['tanggal_pengajuan']);
                  }
                }
                echo $tgl;
              ?>
            </td>
            <td>
            <button type="button" class="btn btn-info btn-sm" onclick="showDetailPengajuan('<?= htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') ?>')" data-toggle="modal" data-target="#modalDetailPengajuan"><i class="fa fa-eye"></i></button>
            <?php if ($row['status'] == 'diajukan'): ?>
              <a href="dashboard_staff.php?unit=update_pengajuan&id=<?= urlencode($row['pengajuan_id']); ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Edit</a>
              <a href="dashboard_staff.php?unit=delete_pengajuan&id=<?= urlencode($row['pengajuan_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus pengajuan ini?')"><i class="fa fa-trash"></i> Hapus</a>
              <button type="button" class="btn btn-success btn-sm ml-2" style="margin-left:10px;" data-toggle="modal" data-target="#modalWa"
                onclick="setPengajuanData('<?= $row['pengajuan_id'] ?>', '<?= htmlspecialchars($row['nama_barang']) ?>', '<?= htmlspecialchars($row['keterangan']) ?>', '<?= htmlspecialchars($row['jumlah']) ?>')">
                <i class="fab fa-whatsapp"></i> Kirim Via WA
              </button>
            <?php elseif ($row['status'] == 'disetujui'): ?>
              <?php
                $cek_barang = mysqli_query($config, "SELECT barang_id FROM tb_barang WHERE pengajuan_id='" . mysqli_real_escape_string($config, $row['pengajuan_id']) . "'");
                if (mysqli_num_rows($cek_barang) == 0) {
                ?>
                  <button type="button" class="btn btn-primary btn-sm" onclick="setBarangData('<?= $row['pengajuan_id'] ?>', '<?= htmlspecialchars($row['nama_barang']) ?>', '<?= htmlspecialchars($row['jumlah']) ?>')" data-toggle="modal" data-target="#modalBarang"><i class="fa fa-plus"></i> Input Data Barang</button>
                <?php
                } else {
                  echo '<span class="badge badge-info">Sudah di input ke data barang</span>';
                }
              ?>

            <!-- Modal Input Data Barang -->
            <div class="modal fade" id="modalBarang" tabindex="-1" role="dialog" aria-labelledby="modalBarangLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="modalBarangLabel">Input Data Barang Inventaris</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
              <form id="formBarang" method="post">
                <div class="modal-body">
                    <input type="hidden" name="insert_barang" value="1">
                    <input type="hidden" class="form-control" name="pengajuan_id" id="barangPengajuanId" readonly>
                    <input type="hidden" class="form-control" name="jumlah" id="barangJumlah" readonly>
                    <div class="form-group">
                      <label>Nama Barang</label>
                      <input type="text" class="form-control" name="nama_barang" id="barangNama" readonly>
                    </div>
                    <div class="form-group">
                      <label>Jenis Barang</label>
                      <select class="form-control" name="jenis_barang" required>
                        <option value="">- Pilih Jenis Barang -</option>
                        <option value="Komputer & Laptop">Komputer & Laptop</option>
                        <option value="Komponen Komputer & Laptop">Komponen Komputer & Laptop</option>
                        <option value="Komponen Network">Komponen Network</option>
                        <option value="Printer & Scanner">Printer & Scanner</option>
                        <option value="Komponen Printer & Scanner">Komponen Printer & Scanner</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label>Nomor Seri</label>
                      <input type="text" class="form-control" name="nomor_seri" placeholder="Contoh: SN123456789">
                    </div>
                    <div class="form-group">
                      <div id="ipAddressGroup" style="display:none;">
                        <label>IP Address</label>
                        <input type="text" class="form-control" name="ip_address" id="ipAddressInput" placeholder="Contoh: 192.168.1.10">
                      </div>
                    </div>
                    <div class="form-group">
                      <label>Harga</label>
                      <input type="number" step="0.01" class="form-control" name="harga">
                    </div>
                    <div class="form-group">
                      <label>Spesifikasi</label>
                      <textarea class="form-control" name="spesifikasi"></textarea>
                    </div>
                    <div class="form-group">
                      <label>Tanggal Terima</label>
                      <input type="date" class="form-control" name="tanggal_terima" value="<?= date('Y-m-d') ?>">
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Data Barang</button>
                  </div>
                  </form>
                </div>
              </div>
            </div>
            <!-- Script untuk Modal Input data pengajuan ke data barang -->
            <script>
              function setBarangData(id, nama, jumlah) {
                document.getElementById('barangPengajuanId').value = id;
                document.getElementById('barangNama').value = nama;
                document.getElementById('barangJumlah').value = jumlah;
                document.getElementById('ipAddressInput').value = '';
                document.getElementById('ipAddressGroup').style.display = 'none';
                document.querySelector('select[name="jenis_barang"]').value = '';
              }

              document.addEventListener('DOMContentLoaded', function() {
                var jenisBarangSelect = document.querySelector('select[name="jenis_barang"]');
                var ipAddressGroup = document.getElementById('ipAddressGroup');
                var ipAddressInput = document.getElementById('ipAddressInput');
                if (jenisBarangSelect) {
                  jenisBarangSelect.addEventListener('change', function() {
                    if (this.value === 'Komputer & Laptop') {
                      ipAddressGroup.style.display = '';
                    } else {
                      ipAddressGroup.style.display = 'none';
                      ipAddressInput.value = '';
                    }
                  });
                }
              });
            </script>
            <!-- END Script untuk Modal Input data pengajuan ke data barang -->
              <a href="dashboard_staff.php?unit=delete_pengajuan&id=<?= urlencode($row['pengajuan_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus pengajuan ini?')"><i class="fa fa-trash"></i> Hapus</a>
            <?php else: ?>
              <span class="text-muted">-</span>
            <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
      </div>
    </div>
  </div>
</section>
<!-- Modal Detail Pengajuan -->
<div class="modal fade" id="modalDetailPengajuan" tabindex="-1" role="dialog" aria-labelledby="modalDetailPengajuanLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDetailPengajuanLabel">Detail Data Pengajuan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="detailPengajuanBody">
        <!-- detail will be filled by JS -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<script>
function showDetailPengajuan(rowJson) {
    var data = JSON.parse(rowJson.replace(/&quot;/g, '"'));
    var html = '<table class="table table-bordered">';
    html += '<tr><th>ID Pengajuan</th><td>' + (data.pengajuan_id || '-') + '</td></tr>';
    html += '<tr><th>Nama Barang</th><td>' + (data.nama_barang || '-') + '</td></tr>';
    html += '<tr><th>Unit</th><td>' + (data.unit || '-') + '</td></tr>';
    html += '<tr><th>Jumlah</th><td>' + (data.jumlah || '-') + '</td></tr>';
    // Format harga
    var harga = data.perkiraan_harga ? 'Rp. ' + Number(data.perkiraan_harga).toLocaleString('id-ID') : '-';
    html += '<tr><th>Perkiraan Harga</th><td>' + harga + '</td></tr>';
    html += '<tr><th>Keterangan</th><td>' + (data.keterangan || '-') + '</td></tr>';
    html += '<tr><th>Status</th><td>' + (data.status || '-') + '</td></tr>';
    // Format tanggal
    var bulanIndo = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    var tgl = '-';
    if (data.tanggal_pengajuan) {
      var t = new Date(data.tanggal_pengajuan);
      if (!isNaN(t.getTime())) {
        tgl = t.getDate() + ' ' + bulanIndo[t.getMonth()] + ' ' + t.getFullYear();
      } else {
        // fallback jika format bukan yyyy-mm-dd
        var arr = String(data.tanggal_pengajuan).split('-');
        if (arr.length === 3) {
          tgl = arr[2] + ' ' + bulanIndo[parseInt(arr[1],10)-1] + ' ' + arr[0];
        } else {
          tgl = data.tanggal_pengajuan;
        }
      }
    }
    html += '<tr><th>Tanggal Pengajuan</th><td>' + tgl + '</td></tr>';
    html += '</table>';
    document.getElementById('detailPengajuanBody').innerHTML = html;
}
</script>
<!-- Modal Kirim WA -->
<div class="modal fade" id="modalWa" tabindex="-1" role="dialog" aria-labelledby="modalWaLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalWaLabel">Kirim Notifikasi Pengajuan via WhatsApp</h5>
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
            <label>Detail Pengajuan:</label>
            <div class="alert alert-info">
              <strong>ID Pengajuan:</strong> <span id="pengajuanId"></span><br>
              <strong>Nama Barang:</strong> <span id="pengajuanNama"></span><br>
              <strong>Keterangan:</strong> <span id="pengajuanKeterangan"></span><br>
              <strong>Jumlah:</strong> <span id="pengajuanJumlah"></span>
            </div>
          </div>
          <button type="submit" class="btn btn-success">Kirim Pesan Verifikasi</button>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
var currentPengajuanData = {};
function setPengajuanData(id, nama, keterangan, jumlah) {
    currentPengajuanData = {
        id: id,
        nama: nama,
        keterangan: keterangan,
        jumlah: jumlah
    };
    document.getElementById('pengajuanId').textContent = id;
    document.getElementById('pengajuanNama').textContent = nama;
    document.getElementById('pengajuanKeterangan').textContent = keterangan;
    document.getElementById('pengajuanJumlah').textContent = jumlah;
}
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('formWa').onsubmit = function(e) {
        e.preventDefault();
        var no = document.getElementById('waNumber').value.trim();
        if(no === '' || !/^08\d{8,13}$/.test(no)) {
            alert('Nomor WA harus diawali 08 dan hanya angka!');
            return false;
        }
        if(!currentPengajuanData.id) {
            alert('Data pengajuan tidak ditemukan!');
            return false;
        }
        var no62 = '62' + no.substring(1);
        var pesan = encodeURIComponent(
            'Halo Qhusnul Arinda Selaku Kepala Ruangan Unit IT, ada pengajuan barang baru yang memerlukan verifikasi:\n\n' +
            '📋 *Detail Pengajuan:*\n' +
            '• ID: ' + currentPengajuanData.id + '\n' +
            '• Nama Barang: ' + currentPengajuanData.nama + '\n' +
            '• Keterangan: ' + currentPengajuanData.keterangan + '\n' +
            '• Jumlah: ' + currentPengajuanData.jumlah + '\n\n' +
            'Mohon segera verifikasi pengajuan barang ini di aplikasi IT-RSPI.\n\n' +
            'Terima kasih.'
        );
        var url = 'https://wa.me/' + no62 + '?text=' + pesan;
        window.open(url, '_blank');
        $('#modalWa').modal('hide');
        document.getElementById('waNumber').value = '';
        return false;
    };
});
</script>
