<?php
require_once("../../../configuration/koneksi.php"); // Sesuaikan path ke koneksi

  $query = mysqli_query($config,"SELECT * FROM tb_perbaikan_barang WHERE perbaikan_id = '$_GET[id]'");
  $r = mysqli_fetch_array($query);

// Cek jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id                  = $_POST['id'];
  $barang_id           = $_POST['barang_id'];
  $tanggal_lapor       = $_POST['tanggal_lapor'];
  $deskripsi_kerusakan = $_POST['deskripsi_kerusakan'];
  $tindakan_perbaikan  = $_POST['tindakan_perbaikan'];
  $status              = $_POST['status'];
  $tanggal_selesai     = $_POST['tanggal_selesai'] ? $_POST['tanggal_selesai'] : NULL;
  $biaya_perbaikan     = $_POST['biaya_perbaikan'] ? $_POST['biaya_perbaikan'] : 0;
  $teknisi             = $_POST['teknisi'];
  $keterangan          = $_POST['keterangan'];

  $edit = "UPDATE tb_perbaikan_barang SET barang_id='$barang_id', tanggal_lapor='$tanggal_lapor', deskripsi_kerusakan='$deskripsi_kerusakan', tindakan_perbaikan='$tindakan_perbaikan', status='$status', tanggal_selesai=" . ($tanggal_selesai ? "'$tanggal_selesai'" : "NULL") . ", biaya_perbaikan='$biaya_perbaikan', teknisi='$teknisi', keterangan='$keterangan' WHERE perbaikan_id='$id'";
  $query = mysqli_query($config, $edit);

  if ($query) {
    header('Location: perbaikan.php?msg=Data perbaikan berhasil diupdate!');
    exit;
  } else {
    header('Location: perbaikan.php?err=Gagal menyimpan data dalam database!');
    exit;
  }
}
?>
<!-- HTML Form -->
<section class="content-header">
  <!-- [Header sama seperti punyamu] -->
</section>
<section class="content">
  <div class="container-fluid">
    <div class="card card-default">
      <div class="card-header">
        <h3 class="card-title">Silahkan Ubah Catatan Kegiatan Harian</h3>
      </div>
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $r['perbaikan_id'] ?>">
        <div class="card-body">
          <div class="form-group">
            <label>ID Barang</label>
            <input type="number" class="form-control" name="barang_id" value="<?php echo $r['barang_id'] ?>" required>
          </div>
          <div class="form-group">
            <label>Tanggal Lapor</label>
            <input type="date" class="form-control" name="tanggal_lapor" value="<?php echo $r['tanggal_lapor'] ?>" required>
          </div>
          <div class="form-group">
            <label>Deskripsi Kerusakan</label>
            <textarea class="form-control" rows="3" name="deskripsi_kerusakan" required><?php echo $r['deskripsi_kerusakan'] ?></textarea>
          </div>
          <div class="form-group">
            <label>Tindakan Perbaikan</label>
            <textarea class="form-control" rows="3" name="tindakan_perbaikan"><?php echo $r['tindakan_perbaikan'] ?></textarea>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select class="form-control" name="status" required>
              <option value="diajukan" <?php if($r['status']=='diajukan') echo 'selected'; ?>>Diajukan</option>
              <option value="proses" <?php if($r['status']=='proses') echo 'selected'; ?>>Proses</option>
              <option value="selesai" <?php if($r['status']=='selesai') echo 'selected'; ?>>Selesai</option>
              <option value="tidak_dapat_diperbaiki" <?php if($r['status']=='tidak_dapat_diperbaiki') echo 'selected'; ?>>Tidak Dapat Diperbaiki</option>
            </select>
          </div>
          <div class="form-group">
            <label>Tanggal Selesai</label>
            <input type="date" class="form-control" name="tanggal_selesai" value="<?php echo $r['tanggal_selesai'] ?>">
          </div>
          <div class="form-group">
            <label>Biaya Perbaikan</label>
            <input type="number" step="0.01" class="form-control" name="biaya_perbaikan" value="<?php echo $r['biaya_perbaikan'] ?>">
          </div>
          <div class="form-group">
            <label>Teknisi</label>
            <input type="text" class="form-control" name="teknisi" value="<?php echo $r['teknisi'] ?>">
          </div>
          <div class="form-group">
            <label>Keterangan</label>
            <textarea class="form-control" rows="2" name="keterangan"><?php echo $r['keterangan'] ?></textarea>
          </div>
        </div>
        <div class="card-footer">
          <a class="btn btn-app bg-warning float-left" href="dashboard_staff.php?unit=logbook&action=datagrid">
            <i class="fas fa-reply"></i> Back
          </a>
          <button class="btn btn-app bg-success float-right" type="submit">
            <i class="fas fa-save"></i> SAVE
          </button>
        </div>
      </form>
    </div>
  </div>
</section>
