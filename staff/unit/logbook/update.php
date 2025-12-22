<?php
require_once("../config/koneksi.php"); // Sesuaikan path ke koneksi

  $query = mysqli_query($config,"SELECT * FROM tb_logbook WHERE id_log = '$_GET[id]'");
  $r = mysqli_fetch_array($query);

// Cek jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id=$_POST['id'];
    $nama_karyawan = $_SESSION['id_user']; // Ambil ID user dari session
    $tanggal       = $_POST['tanggal'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $judul         = $_POST['judul'];
    $deskripsi     = $_POST['deskripsi'];
    $catatan       = $_POST['catatan'];
    $status        = $_POST['status'] ?? 'Belum';

    // Update data ke database
    $edit = "UPDATE `tb_logbook` 
    SET 
    `id_user`           = '$nama_karyawan',
    `tanggal_log`       = '$tanggal',
    `tanggal_selesai`   = '$tanggal_selesai',
    `judul_log`         = '$judul',
    `deskripsi_log`     = '$deskripsi',
    `catatan_log`       = '$catatan',
    `status_log`        = '$status' WHERE `id_log` = '$id'";
    
    $query = mysqli_query ($config,$edit);

    if ($query) {
        header('Location: dashboard_staff.php?unit=logbook&msg=Data Logbook berhasil Diupdate!');
    		exit;
    } else {
        header('Location: dashboard_staff.php?unit=logbook&err=Gagal menyimpan data dalam database!');
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
        <input type="hidden" name="id" value="<?php echo $r['id_log'] ?>">
        <div class="card-body">
          <div class="form-group">
            <label>Nama Karyawan</label>
            <input type="text" class="form-control" value="<?= $_SESSION['nama_lengkap']; ?>" readonly>
            <input type="hidden" name="nm_karyawan" value="<?= $_SESSION['id_user']; ?>">
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Tanggal Lapor</label>
                <input type="datetime-local" class="form-control" name="tanggal" value="<?php echo str_replace(' ', 'T', $r['tanggal_log']); ?>" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Tanggal Selesai</label>
                <input type="datetime-local" class="form-control" name="tanggal_selesai" value="<?php echo str_replace(' ', 'T', $r['tanggal_selesai']); ?>" required>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label>Judul Catatan Log</label>
            <input type="text" class="form-control" name="judul" value="<?php echo $r['judul_log'] ?>" required>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Deskripsi Log</label>
                <textarea class="form-control" rows="4" name="deskripsi" required><?php echo $r['deskripsi_log'] ?></textarea>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Catatan</label>
                <textarea class="form-control" rows="4" name="catatan" required><?php echo $r['catatan_log'] ?></textarea>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select class="form-control" name="status" required>
              <option value="Belum" <?php echo ($r['status_log'] == 'Belum') ? 'selected' : ''; ?>>Belum</option>
              <option value="Pending" <?php echo ($r['status_log'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
              <option value="Selesai" <?php echo ($r['status_log'] == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
            </select>
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
