<?php
require_once("../config/koneksi.php"); // Sesuaikan path ke koneksi

// Cek jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_karyawan = $_SESSION['id_user']; // Ambil ID user dari session
    $tanggal       = $_POST['tanggal'];
    $judul         = $_POST['judul'];
    $deskripsi     = $_POST['deskripsi'];
    $catatan       = $_POST['catatan'];

    // Simpan data ke database
    $query = "INSERT INTO tb_logbook (id_user, tanggal_log, judul_log, deskripsi_log, catatan_log) 
              VALUES ('$nama_karyawan', '$tanggal', '$judul', '$deskripsi', '$catatan')";
    
    $input = mysqli_query($config, $query);

    if ($input) {
        header('Location: dashboard_staff.php?unit=logbook&msg=Data Logbook berhasil Dicatat!');
    		exit;
    } else {
        header('Location: dashboard_staff.php?unit=logbook&err=Gagal menyimpan data dalam database!');
    		exit;
        // echo "Error: " . mysqli_error($config); // Debugging error
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
        <h3 class="card-title">Silahkan Input Catatan Kegiatan Harian</h3>
      </div>
      <form method="post" enctype="multipart/form-data">
        <div class="card-body">
          <div class="form-group">
            <label>Nama Karyawan</label>
            <input type="text" class="form-control" value="<?= $_SESSION['nama_lengkap']; ?>" readonly>
            <input type="hidden" name="nm_karyawan" value="<?= $_SESSION['id_user']; ?>">
          </div>
          <div class="form-group">
            <label>Tanggal</label>
            <input type="date" class="form-control" name="tanggal" required>
          </div>
          <div class="form-group">
            <label>Judul Catatan Log</label>
            <input type="text" class="form-control" name="judul" required>
          </div>
          <div class="form-group">
            <label>Deskripsi Log</label>
            <textarea class="form-control" rows="4" name="deskripsi" required></textarea>
          </div>
          <div class="form-group">
            <label>Catatan</label>
            <textarea class="form-control" rows="5" name="catatan" required></textarea>
          </div>
        </div>
        <div class="card-footer">
          <a class="btn btn-app bg-warning float-left" href="dashboard_staff.php?unit=logbook">
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
