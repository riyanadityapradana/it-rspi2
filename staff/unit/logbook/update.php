<?php
require_once("../config/koneksi.php"); // Sesuaikan path ke koneksi

  $query = mysqli_query($config,"SELECT * FROM tb_logbook WHERE id_log = '$_GET[id]'");
  $r = mysqli_fetch_array($query);

// Cek jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id=$_POST['id'];
    $nama_karyawan = $_SESSION['id_user']; // Ambil ID user dari session
    $tanggal       = $_POST['tanggal'];
    $judul         = $_POST['judul'];
    $deskripsi     = $_POST['deskripsi'];
    $catatan       = $_POST['catatan'];

    // Update data ke database
    $edit = "UPDATE `tb_logbook` 
    SET 
    `id_user`           = '$nama_karyawan',
    `tanggal_log`       = '$tanggal',
    `judul_log`         = '$judul',
    `deskripsi_log`     = '$deskripsi',
    `catatan_log`       = '$catatan' WHERE `id_log` = '$id'";
    
    $query = mysqli_query ($config,$edit);

    if ($query) {
        echo "<script>alert('Data Telah Berhasil Terubah'); window.location = 'dashboard_staff.php?unit=logbook&action=datagrid'</script>";
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
          <div class="form-group">
            <label>Tanggal</label>
            <input type="date" class="form-control" name="tanggal" value="<?php echo $r['tanggal_log'] ?>" required>
          </div>
          <div class="form-group">
            <label>Judul Catatan Log</label>
            <input type="text" class="form-control" name="judul" value="<?php echo $r['judul_log'] ?>" required>
          </div>
          <div class="form-group">
            <label>Deskripsi Log</label>
            <textarea class="form-control" rows="4" name="deskripsi" required><?php echo $r['deskripsi_log'] ?></textarea>
          </div>
          <div class="form-group">
            <label>Catatan</label>
            <textarea class="form-control" rows="5" name="catatan" required><?php echo $r['catatan_log'] ?></textarea>
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
