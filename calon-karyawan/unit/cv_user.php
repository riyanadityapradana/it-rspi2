<?php
session_start();
require_once '../../config/koneksi.php';
if (!isset($_SESSION['id_calon'])) exit('Akses ditolak');
$id_calon = $_SESSION['id_calon'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Proses simpan/update data utama
    $nama_lengkap = $_POST['nama_lengkap'] ?? '';
    $email = $_POST['email'] ?? '';
    $no_hp = $_POST['no_hp'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $linkedin = $_POST['linkedin'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    $config->query("UPDATE tb_calon SET nama_lengkap='$nama_lengkap', email='$email', no_hp='$no_hp', alamat='$alamat', linkedin='$linkedin', deskripsi='$deskripsi' WHERE id_calon='$id_calon'");
    // (Pendidikan, pengalaman, dll bisa ditambah di sini)
    echo json_encode(['status'=>'ok','msg'=>'Data berhasil disimpan']);
    exit;
}
// Ambil data utama
$calon = $config->query("SELECT * FROM tb_calon WHERE id_calon='$id_calon'")->fetch_assoc();
?>
<form id="formLengkapiData">
  <div class="form-group">
    <label>Nama Lengkap</label>
    <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($calon['nama_lengkap']) ?>" required>
  </div>
  <div class="form-group">
    <label>Email</label>
    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($calon['email']) ?>">
  </div>
  <div class="form-group">
    <label>No HP</label>
    <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($calon['no_hp']) ?>">
  </div>
  <div class="form-group">
    <label>Alamat</label>
    <textarea name="alamat" class="form-control"><?= htmlspecialchars($calon['alamat']) ?></textarea>
  </div>
  <div class="form-group">
    <label>LinkedIn</label>
    <input type="text" name="linkedin" class="form-control" value="<?= htmlspecialchars($calon['linkedin'] ?? '') ?>">
  </div>
  <div class="form-group">
    <label>Deskripsi Diri (Ringkasan Profil)</label>
    <textarea name="deskripsi" class="form-control"><?= htmlspecialchars($calon['deskripsi'] ?? '') ?></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Simpan</button>
</form>
<script>
$('#formLengkapiData').on('submit', function(e){
  e.preventDefault();
  var form = $(this);
  $.post('unit/cv_user.php', form.serialize(), function(res){
    try { var data = JSON.parse(res); } catch(e) { data = {status:'fail',msg:'Gagal parsing response'}; }
    if(data.status==='ok'){
      alert('Data berhasil disimpan!');
      $('#modalLengkapiData').modal('hide');
      location.reload();
    } else {
      alert(data.msg||'Gagal menyimpan data!');
    }
  });
});
</script> 