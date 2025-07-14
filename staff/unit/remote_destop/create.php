<?php
require_once("../config/koneksi.php"); // Sesuaikan path ke koneksi

// Cek jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ip_add       = $_POST['ip_add'];
    $password     = $_POST['password'];
    $nama_desktop = $_POST['nama_desktop'];

    // Simpan data ke database
    $query = "INSERT INTO tb_remote (ip_add, password, nama_desktop) 
              VALUES ('$ip_add', '$password', '$nama_desktop')";
    
    $input = mysqli_query($config, $query);

    if ($input) {
        echo "<script>alert('Data Telah Berhasil Tersimpan'); window.location = 'dashboard_staff.php?unit=remote&action=datagrid'</script>";
        exit();
    } else {
        echo "<script>alert('Gagal menyimpan data dalam database');</script>";
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
        <h3 class="card-title">Silahkan Input IP ADDRESS UNTUK REMOTE DESTOP</h3>
      </div>
      <form method="post" enctype="multipart/form-data">
        <div class="card-body">
          <div class="form-group">
            <label>IP ADDRESS</label>
            <input type="text" class="form-control" name="ip_add" required>
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="text" class="form-control" name="password" required>
          </div>
          <div class="form-group">
            <label>Nama Pemilik Destop</label>
            <input type="text" class="form-control" name="nama_desktop" required>
          </div>
        </div>
        <div class="card-footer">
          <a class="btn btn-app bg-warning float-left" href="dashboard_staff.php?unit=remote&action=datagrid">
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
