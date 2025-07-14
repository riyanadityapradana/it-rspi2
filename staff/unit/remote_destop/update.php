<?php
require_once("../config/koneksi.php"); // Sesuaikan path ke koneksi

  $query = mysqli_query($config,"SELECT * FROM tb_remote WHERE id_remote = '$_GET[id]'");
  $r = mysqli_fetch_array($query);

// Cek jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id=$_POST['id'];
    $ip_add       = $_POST['ip_add'];
    $password     = $_POST['password'];
    $nama_desktop = $_POST['nama_desktop'];

    // Update data ke database
    $edit = "UPDATE `tb_remote` 
    SET 
    `ip_add`         = '$ip_add',
    `password`       = '$password',
    `nama_desktop`   = '$nama_desktop' WHERE `id_remote` = '$id'";
    
    $query = mysqli_query ($config,$edit);

    if ($query) {
        echo "<script>alert('Data Telah Berhasil Terubah'); window.location = 'dashboard_staff.php?unit=remote&action=datagrid'</script>";
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
        <h3 class="card-title">Silahkan Ubah IP ADDRESS UNTUK REMOTE DESTOP</h3>
      </div>
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $r['id_remote'] ?>">
        <div class="card-body">
          <div class="form-group">
            <label>IP ADDRESS</label>
            <input type="text" class="form-control" name="ip_add" value="<?php echo $r['ip_add'] ?>" required>
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="text" class="form-control" name="password" value="<?php echo $r['password'] ?>" required>
          </div>
          <div class="form-group">
            <label>Nama Pemilik Destop</label>
            <input type="text" class="form-control" name="nama_desktop" value="<?php echo $r['nama_desktop'] ?>" required>
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
