<?php
require_once("../config/koneksi.php");

// Get current user data
$id_user = $_SESSION['id_user'];
$query = mysqli_query($config, "SELECT * FROM tb_user WHERE id_user = '$id_user'");
$user = mysqli_fetch_assoc($query);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nip = mysqli_real_escape_string($config, $_POST['nip']);
    $nama_lengkap = mysqli_real_escape_string($config, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($config, $_POST['username']);
    $email = mysqli_real_escape_string($config, $_POST['email']);
    $no_hp = mysqli_real_escape_string($config, $_POST['no_hp']);
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $tmp_lahir = mysqli_real_escape_string($config, $_POST['tmp_lahir'] ?? '');
    $tgl_lahir = mysqli_real_escape_string($config, $_POST['tgl_lahir'] ?? '');
    $jbtn = mysqli_real_escape_string($config, $_POST['jbtn'] ?? '');
    $pendidikan = mysqli_real_escape_string($config, $_POST['pendidikan'] ?? '');
    $alamat = mysqli_real_escape_string($config, $_POST['alamat'] ?? '');
    
    $error = '';
    
    // Validasi NIP jika berubah
    if ($nip !== $user['nip']) {
        $cek_nip = mysqli_query($config, "SELECT * FROM tb_user WHERE nip='$nip' AND id_user != '$id_user'");
        if (mysqli_num_rows($cek_nip) > 0) {
            $error = 'NIP sudah digunakan oleh user lain!';
        }
    }
    
    // Validasi username jika berubah
    if (!$error && $username !== $user['username']) {
        $cek_username = mysqli_query($config, "SELECT * FROM tb_user WHERE username='$username' AND id_user != '$id_user'");
        if (mysqli_num_rows($cek_username) > 0) {
            $error = 'Username sudah digunakan oleh user lain!';
        }
    }
    
    // Validasi email jika berubah
    if (!$error && $email !== $user['email']) {
        $cek_email = mysqli_query($config, "SELECT * FROM tb_user WHERE email='$email' AND id_user != '$id_user'");
        if (mysqli_num_rows($cek_email) > 0) {
            $error = 'Email sudah digunakan oleh user lain!';
        }
    }
    
    if (!$error) {
        // Handle image upload
        $update_foto = '';
        if (!empty($_FILES['foto']['name'])) {
            $upload_dir = "../assets/img/";
            $file_name = $_FILES['foto']['name'];
            $file_tmp = $_FILES['foto']['tmp_name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            // Validasi tipe file
            $allowed_types = array("jpg", "jpeg", "png", "gif");
            if (in_array($file_ext, $allowed_types)) {
                // Nama file dengan NIP
                $new_filename = $user['nip'] . '.' . $file_ext;
                $target_file = $upload_dir . $new_filename;
                
                // Hapus file lama jika ada
                if (!empty($user['foto'])) {
                    $old_file = $upload_dir . $user['foto'];
                    if (file_exists($old_file)) {
                        unlink($old_file);
                    }
                }
                
                // Upload file baru
                if (move_uploaded_file($file_tmp, $target_file)) {
                    $update_foto = ", foto='$new_filename'";
                } else {
                    $error = 'Gagal mengupload foto!';
                }
            } else {
                $error = 'Tipe file foto tidak valid (hanya JPG, JPEG, PNG, GIF)!';
            }
        }
        
        if (!$error) {
            // Handle password
            $update_password = '';
            if (!empty($password)) {
                // Hash password sebelum disimpan
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $update_password = ", password='$hashed_password'";
            }
            
            // Update database
            $sql = "UPDATE tb_user SET nip='$nip', nama_lengkap='$nama_lengkap', username='$username', email='$email', no_hp='$no_hp', tmp_lahir='$tmp_lahir', tgl_lahir=" . ($tgl_lahir !== '' ? "'".$tgl_lahir."'" : "NULL") . ", jbtn='$jbtn', pendidikan='$pendidikan', alamat='$alamat' $update_password $update_foto WHERE id_user='$id_user'";
            
            if (mysqli_query($config, $sql)) {
                // Update session
                $_SESSION['nip'] = $nip;
                $_SESSION['nama_lengkap'] = $nama_lengkap;
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;
                $_SESSION['no_hp'] = $no_hp;
                if ($update_foto) {
                    $_SESSION['foto'] = $new_filename;
                }
                
                // Redirect ke dashboard dengan pesan sukses
                header('Location: dashboard_staff.php?unit=beranda&msg=Profil berhasil diperbarui!');
                exit;
            } else {
                $error = 'Gagal memperbarui profil: ' . mysqli_error($config);
            }
        }
    }
}
?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">Edit Data Profil</h3>
                    </div>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> <?= htmlspecialchars($error) ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="card-body">
                            <!-- Foto Profil - Full Width -->
                            <div class="form-group">
                                <label>Foto Profil</label>
                                <div class="mb-3">
                                    <?php 
                                        $foto_path = "../assets/img/" . ($user['foto'] ?? '');
                                        if (!empty($user['foto']) && file_exists($foto_path)):
                                    ?>
                                        <div class="text-center mb-3">
                                            <img src="<?= htmlspecialchars($foto_path) ?>" alt="Foto Profil" style="max-width: 200px; max-height: 200px; border-radius: 5px; object-fit: cover;">
                                            <p class="text-muted small mt-2">Foto saat ini | Abaikan jika tidak ingin diubah</p>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            <i class="fas fa-image"></i> Belum ada gambar profil
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="foto" name="foto" accept="image/*">
                                    <label class="custom-file-label" for="foto">Pilih gambar...</label>
                                </div>
                                <small class="text-muted d-block mt-2">Format: JPG, JPEG, PNG, GIF | File akan disimpan dengan nama NIP Anda</small>
                            </div>
                            
                            <!-- Form Fields - 2 Columns -->
                            <div class="row">
                                <!-- Kolom Kiri -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nip">NIP</label>
                                        <input type="text" class="form-control" id="nip" name="nip" value="<?= htmlspecialchars($user['nip']) ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="nama_lengkap">Nama Lengkap</label>
                                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="username">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="tmp_lahir">Tempat Lahir</label>
                                        <input type="text" class="form-control" id="tmp_lahir" name="tmp_lahir" value="<?= htmlspecialchars($user['tmp_lahir'] ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="tgl_lahir">Tanggal Lahir</label>
                                        <input type="date" class="form-control" id="tgl_lahir" name="tgl_lahir" value="<?= htmlspecialchars($user['tgl_lahir'] ?? '') ?>">
                                    </div>
                                </div>
                                
                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="no_hp">Nomor HP</label>
                                        <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?= htmlspecialchars($user['no_hp'] ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="jbtn">Jabatan</label>
                                        <input type="text" class="form-control" id="jbtn" name="jbtn" value="<?= htmlspecialchars($user['jbtn'] ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="pendidikan">Pendidikan</label>
                                        <input type="text" class="form-control" id="pendidikan" name="pendidikan" value="<?= htmlspecialchars($user['pendidikan'] ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="alamat">Alamat</label>
                                        <textarea class="form-control" id="alamat" name="alamat" rows="3"><?= htmlspecialchars($user['alamat'] ?? '') ?></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="password">Password Baru <small class="text-muted">(Kosongkan jika tidak ingin mengubah)</small></label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password baru">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="dashboard_staff.php?unit=beranda" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                    <br>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Show selected filename
document.getElementById('foto').addEventListener('change', function(e) {
    let fileName = e.target.files[0]?.name || 'Pilih gambar...';
    document.querySelector('.custom-file-label').textContent = fileName;
});
</script>
