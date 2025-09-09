<?php
require_once("../config/koneksi.php");
$id_staff = $_SESSION['id_user'];
?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>PENGAJUAN BARANG</h1>
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
          <th>Unit</th>
          <th>Jumlah</th>
          <th>Perkiraan Harga</th>
          <th>Keterangan</th>
          <th>Status</th>
          <th>Tanggal Pengajuan</th>
          <th>Aksi</th>
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
            <td><?= htmlspecialchars($row['unit']); ?></td>
            <td><?= htmlspecialchars($row['jumlah']); ?></td>
            <td><?= htmlspecialchars(number_format($row['perkiraan_harga'],0,',','.')); ?></td>
            <td><?= htmlspecialchars($row['keterangan']); ?></td>
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
            <td><?= htmlspecialchars($row['tanggal_pengajuan']); ?></td>
            <td>
            <?php if ($row['status'] == 'diajukan'): ?>
              <a href="dashboard_staff.php?unit=update_pengajuan&id=<?= urlencode($row['pengajuan_id']); ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Edit</a>
              <a href="dashboard_staff.php?unit=delete_pengajuan&id=<?= urlencode($row['pengajuan_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus pengajuan ini?')"><i class="fa fa-trash"></i> Hapus</a>
              <button type="button" class="btn btn-success btn-sm ml-2" style="margin-left:10px;" data-toggle="modal" data-target="#modalWa"
                onclick="setPengajuanData('<?= $row['pengajuan_id'] ?>', '<?= htmlspecialchars($row['nama_barang']) ?>', '<?= htmlspecialchars($row['keterangan']) ?>', '<?= htmlspecialchars($row['jumlah']) ?>')">
                <i class="fab fa-whatsapp"></i> Kirim Via WA
              </button>
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
