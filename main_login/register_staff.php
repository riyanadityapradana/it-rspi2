<?php
require_once '../config/koneksi.php';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nip = isset($_POST['nip']) ? mysqli_real_escape_string($config, trim($_POST['nip'])) : '';
    $email = isset($_POST['email']) ? mysqli_real_escape_string($config, trim($_POST['email'])) : '';
    $nama_lengkap = isset($_POST['nama_lengkap']) ? mysqli_real_escape_string($config, trim($_POST['nama_lengkap'])) : '';
    $username = isset($_POST['username']) ? mysqli_real_escape_string($config, trim($_POST['username'])) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $no_hp = isset($_POST['no_hp']) ? mysqli_real_escape_string($config, trim($_POST['no_hp'])) : '';
    $tmp_lahir = isset($_POST['tmp_lahir']) ? mysqli_real_escape_string($config, trim($_POST['tmp_lahir'])) : '';
    $tgl_lahir = isset($_POST['tgl_lahir']) ? mysqli_real_escape_string($config, trim($_POST['tgl_lahir'])) : '';
    $jbtn = isset($_POST['jbtn']) ? mysqli_real_escape_string($config, trim($_POST['jbtn'])) : '';
    $pendidikan = isset($_POST['pendidikan']) ? mysqli_real_escape_string($config, trim($_POST['pendidikan'])) : '';
    $alamat = isset($_POST['alamat']) ? mysqli_real_escape_string($config, trim($_POST['alamat'])) : '';
    $role = 'Staff';

    // Validasi field wajib
    if ($nip === '' || $email === '' || $nama_lengkap === '' || $username === '' || $password === '') {
        $error = 'Semua field wajib diisi!';
    } else {
        // Cek NIP sudah ada atau belum
        $cek_nip = mysqli_query($config, "SELECT * FROM tb_user WHERE nip='$nip'");
        if (mysqli_num_rows($cek_nip) > 0) {
            $error = 'NIP sudah digunakan, silakan pilih NIP lain!';
        } else {
            // Cek username sudah ada atau belum
            $cek_username = mysqli_query($config, "SELECT * FROM tb_user WHERE username='$username'");
            if (mysqli_num_rows($cek_username) > 0) {
                $error = 'Username sudah digunakan, silakan pilih username lain!';
            } else {
                // Cek email sudah ada atau belum
                $cek_email = mysqli_query($config, "SELECT * FROM tb_user WHERE email='$email'");
                if (mysqli_num_rows($cek_email) > 0) {
                    $error = 'Email sudah digunakan, silakan pilih email lain!';
                } else {
                    // Handle upload foto
                    $foto = null;
                    if (!empty($_FILES['foto']['name'])) {
                        $upload_dir = "../assets/upload/";
                        $file_name = basename($_FILES['foto']['name']);
                        $target_file = $upload_dir . time() . "_" . $file_name;
                        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                        
                        // Validasi tipe file
                        $allowed_types = array("jpg", "jpeg", "png", "gif");
                        if (in_array($imageFileType, $allowed_types)) {
                            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
                                $foto = $target_file;
                            } else {
                                $error = 'Gagal mengupload foto!';
                            }
                        } else {
                            $error = 'Tipe file foto tidak valid (hanya JPG, JPEG, PNG, GIF)!';
                        }
                    }
                    
                    if ($error === '') {
                        // Hash password sebelum disimpan
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        
                        // Simpan ke database
                        $query = "INSERT INTO tb_user (nip, nama_lengkap, username, password, email, no_hp, role, tmp_lahir, tgl_lahir, jbtn, pendidikan, alamat, foto, status) 
                                  VALUES ('$nip', '$nama_lengkap', '$username', '$hashed_password', '$email', '$no_hp', '$role', '$tmp_lahir', " . ($tgl_lahir !== '' ? "'".$tgl_lahir."'" : "NULL") . ", '$jbtn', '$pendidikan', '$alamat', '$foto', 'nonaktif')";
                        
                        if (mysqli_query($config, $query)) {
                            $success = 'Akun berhasil dibuat! Silakan tunggu konfirmasi admin.';
                        } else {
                            $error = 'Gagal mendaftar. Silakan coba lagi.';
                        }
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ITUTL-3 | Register</title>
  <link rel="icon" href="../assets/img/icon.png">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
  <style>
    body {
      min-height: 100vh;
      background: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1500&q=80') no-repeat center center fixed;
      background-size: cover;
      position: relative;
    }
    body::before {
      content: "";
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(30, 0, 60, 0.5);
      backdrop-filter: blur(4px);
      z-index: 0;
    }
    .login-box {
      position: relative;
      z-index: 1;
    }
    .card {
      background: rgba(255,255,255,0.15);
      border-radius: 20px;
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
      backdrop-filter: blur(8px);
      border: 1px solid rgba(255,255,255,0.18);
    }

    /* Widen the login/register box while keeping it responsive */
    .login-box {
      max-width: 760px;
      width: 92%;
      margin: 6vh auto;
    }

    .card {
      padding: 1.25rem;
    }

    @media (max-width: 768px) {
      .login-box {
        width: 96%;
        margin: 3vh auto;
      }
      .card {
        padding: 1rem;
      }
    }
    .card-header .h1 {
      color: #fff;
      text-shadow: 0 2px 8px #7f2092;
      font-weight: bold;
      letter-spacing: 2px;
    }
    .form-control {
      background: rgba(255,255,255,0.2);
      border: none;
      border-radius: 10px;
      color: #fff;
      box-shadow: 0 2px 8px rgba(127,32,146,0.1);
      transition: box-shadow 0.3s;
    }
    .form-control:focus {
      box-shadow: 0 0 0 2px #7f2092;
      background: rgba(255,255,255,0.3);
      color: #fff;
    }
    .btn-primary {
      background: linear-gradient(90deg, #7f2092 0%, #4e54c8 100%);
      border: none;
      border-radius: 10px;
      font-weight: bold;
      box-shadow: 0 2px 8px #7f2092;
      transition: background 0.3s;
    }
    .btn-primary:hover {
      background: linear-gradient(90deg, #4e54c8 0%, #7f2092 100%);
    }
    .login-box-msg, .icheck-primary label, .input-group-text span {
      color: #fff;
    }
    .input-group-text {
      background: transparent;
      border: none;
    }
    .fas.fa-user, .fas.fa-lock, .fas.fa-id-card {
      cursor: default;
      color: #fff;
      transition: color 0.3s;
    }
    a {
      color: #fff;
      text-decoration: underline;
    }
    a:hover {
      color: #7f2092;
    }
    .input-group-prepend .input-group-text {
      border-top-right-radius: 0;
      border-bottom-right-radius: 0;
      border-top-left-radius: 10px;
      border-bottom-left-radius: 10px;
      margin-right: 0;
      padding-right: 0.75rem;
      padding-left: 0.75rem;
      height: 44px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .form-control {
      border-top-left-radius: 0 !important;
      border-bottom-left-radius: 0 !important;
      height: 44px;
      padding-left: 0.75rem;
    }
    .input-group {
      align-items: center;
    }
  </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="#" class="h1"><b>IT -</b> RSPI</a>
    </div>
    <div class="card-body">
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger" style="font-size:1.1em;"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <?php if (!empty($success)): ?>
        <!-- Modal Success -->
        <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius:18px;">
              <div class="modal-header bg-success" style="border-top-left-radius:18px;border-top-right-radius:18px;">
                <h5 class="modal-title text-white" id="successModalLabel"><i class="fas fa-check-circle"></i> Berhasil!</h5>
              </div>
              <div class="modal-body text-center" style="font-size:1.2em;">
                <?= htmlspecialchars($success) ?><br><br>
                <span style="font-size:0.9em;color:#888;">Anda akan diarahkan ke halaman login...</span>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>
      <p class="login-box-msg">Register akun baru</p>
      <form action="#" method="post" enctype="multipart/form-data">
        <div class="row">
          <div class="col-md-6">
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <span class="fas fa-id-badge"></span>
                </div>
              </div>
              <input type="text" class="form-control" placeholder="NIP" name="nip" required>
            </div>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <span class="fas fa-envelope"></span>
                </div>
              </div>
              <input type="email" class="form-control" placeholder="Email" name="email" required>
            </div>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <span class="fas fa-id-card"></span>
                </div>
              </div>
              <input type="text" class="form-control" placeholder="Nama Karyawan" name="nama_lengkap" required>
            </div>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <span class="fas fa-user"></span>
                </div>
              </div>
              <input type="text" class="form-control" placeholder="Username" name="username" required>
            </div>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
              <input type="password" class="form-control" placeholder="Password" name="password" required>
            </div>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <span class="fas fa-phone"></span>
                </div>
              </div>
              <input type="text" class="form-control" placeholder="No. HP" name="no_hp">
            </div>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <span class="fas fa-image"></span>
                </div>
              </div>
              <input type="file" class="form-control" name="foto" accept="image/*">
            </div>
          </div>
          <div class="col-md-6">
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <span class="fas fa-map-marker-alt"></span>
                </div>
              </div>
              <input type="text" class="form-control" placeholder="Tempat Lahir" name="tmp_lahir">
            </div>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <span class="fas fa-calendar"></span>
                </div>
              </div>
              <input type="date" class="form-control" name="tgl_lahir">
            </div>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <span class="fas fa-briefcase"></span>
                </div>
              </div>
              <input type="text" class="form-control" placeholder="Jabatan" name="jbtn">
            </div>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <span class="fas fa-graduation-cap"></span>
                </div>
              </div>
              <input type="text" class="form-control" placeholder="Pendidikan" name="pendidikan">
            </div>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <span class="fas fa-home"></span>
                </div>
              </div>
              <textarea class="form-control" placeholder="Alamat" name="alamat" rows="4"></textarea>
            </div>
          </div>
        </div>
        <input type="hidden" name="role" value="Staff">
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Register</button>
          </div>
        </div>
      </form>
      <div class="row mt-3">
        <div class="col-12 text-center">
          <span style="color:#fff">Sudah punya akun?</span> <a href="form_login.php">Login di sini</a>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/dist/js/adminlte.min.js"></script>
<?php if (!empty($success)): ?>
<script>
  // Fallback jika jQuery belum termuat
  if (typeof $ === 'undefined') {
    window.location.href = 'form_login.php';
  } else {
    $(document).ready(function(){
      $('#successModal').modal('show');
      setTimeout(function(){
        window.location.href = 'form_login.php';
      }, 5000);
    });
  }
</script>
<?php endif; ?>
</body>
</html> 