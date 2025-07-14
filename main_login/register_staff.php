<?php
require_once '../config/koneksi.php';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nip = isset($_POST['nip']) ? trim($_POST['nip']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $nama_lengkap = isset($_POST['nama_lengkap']) ? trim($_POST['nama_lengkap']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $role = 'Staff';
    $status = 'nonaktif';
    $foto = '';
    $no_hp = '';
    $created_at = date('Y-m-d H:i:s');
    $updated_at = $created_at;

    // Validasi field wajib
    if ($nip === '' || $email === '' || $nama_lengkap === '' || $username === '' || $password === '') {
        $error = 'Semua field wajib diisi!';
    } else {
        // Cek username sudah ada atau belum
        $stmt = $config->prepare('SELECT COUNT(*) FROM tb_user WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        if ($count > 0) {
            $error = 'Username sudah digunakan, silakan pilih username lain!';
        } else {
            // Cek email sudah ada atau belum
            $stmt = $config->prepare('SELECT COUNT(*) FROM tb_user WHERE email = ?');
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->bind_result($count_email);
            $stmt->fetch();
            $stmt->close();
            if ($count_email > 0) {
                $error = 'Email sudah digunakan, silakan pilih email lain!';
            } else {
                // Cek NIP sudah ada atau belum
                $stmt = $config->prepare('SELECT COUNT(*) FROM tb_user WHERE nip = ?');
                $stmt->bind_param('s', $nip);
                $stmt->execute();
                $stmt->bind_result($count_nip);
                $stmt->fetch();
                $stmt->close();
                if ($count_nip > 0) {
                    $error = 'NIP sudah digunakan, silakan pilih NIP lain!';
                } else {
                    // Upload foto jika ada
                    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
                        $allowed = array('jpg', 'jpeg', 'png', 'gif');
                        $filename = $_FILES['foto']['name'];
                        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                        if (in_array($ext, $allowed)) {
                            $newname = time() . '_' . $filename;
                            $destination = '../assets/upload/' . $newname;
                            if (move_uploaded_file($_FILES['foto']['tmp_name'], $destination)) {
                                $foto = $newname;
                            }
                        }
                    }
                    // Simpan ke database
                    $stmt = $config->prepare('INSERT INTO tb_user (nip, username, password, nama_lengkap, email, no_hp, role, foto, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                    $stmt->bind_param('sssssssssss', $nip, $username, $password, $nama_lengkap, $email, $no_hp, $role, $foto, $status, $created_at, $updated_at);
                    if ($stmt->execute()) {
                        $success = 'Akun berhasil dibuat! Silakan tunggu konfirmasi admin.';
                    } else {
                        $error = 'Gagal mendaftar. Silakan coba lagi.';
                    }
                    $stmt->close();
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
              <span class="fas fa-image"></span>
            </div>
          </div>
          <input type="file" class="form-control" name="foto" accept="image/*">
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