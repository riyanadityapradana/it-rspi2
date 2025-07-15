<?php
// Aktifkan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Koneksi database
require_once '../config/koneksi.php';
if (!$config) {
    die('Koneksi gagal: ' . mysqli_connect_error());
}

// Proses register
$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $no_hp = trim($_POST['no_hp'] ?? '');

    if ($nama_lengkap === '' || $username === '' || $password === '' || $no_hp === '') {
        $error = 'Semua field wajib diisi!';
    } else {
        $cek = mysqli_query($config, "SELECT id_calon FROM tb_calon WHERE username='$username'");
        if (!$cek) {
            $error = 'Query error: ' . mysqli_error($config);
        } elseif (mysqli_num_rows($cek) > 0) {
            $error = 'Username sudah terdaftar!';
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO tb_calon (nama_lengkap, username, password, no_hp, status, created_at, updated_at) VALUES (?,?,?,?, 'belum tes', NOW(), NOW())";
            $stmt = mysqli_prepare($config, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'ssss', $nama_lengkap, $username, $password_hash, $no_hp);
                if (mysqli_stmt_execute($stmt)) {
                    $success = 'Registrasi berhasil! Silakan login.';
                } else {
                    $error = 'Gagal menyimpan data: ' . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_close($stmt);
            } else {
                $error = 'Gagal menyiapkan query: ' . mysqli_error($config);
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
  <title>ITUTL-3 | Register Calon Karyawan IT</title>
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
      width: 100%;
      max-width: 800px;
    }
    .card {
      background: rgba(255,255,255,0.15);
      border-radius: 20px;
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
      backdrop-filter: blur(8px);
      border: 1px solid rgba(255,255,255,0.18);
      padding: 32px 32px 24px 32px;
      min-width: 420px;
      max-width: 800px;
    }
    .card-header .h1 {
      color: #fff;
      text-shadow: 0 2px 8px #7f2092;
      font-weight: bold;
      letter-spacing: 2px;
      font-size: 2.3rem;
    }
    .form-control {
      background: rgba(255,255,255,0.2);
      border: none;
      border-radius: 10px;
      color: #fff;
      box-shadow: 0 2px 8px rgba(127,32,146,0.1);
      transition: box-shadow 0.3s;
      font-size: 1.15rem;
      height: 52px;
      padding-left: 1.1rem;
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
      font-size: 1.2rem;
      height: 50px;
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
      font-size: 1.3rem;
      min-width: 48px;
      justify-content: center;
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
      height: 52px;
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
    select.form-control {
      color: #fff !important;
    }
    select.form-control option {
      color: #222 !important;
      background: #fff !important;
    }
    textarea.form-control {
      height: auto;
      min-height: 80px;
      resize: vertical;
    }
    .form-section {
      background: rgba(255,255,255,0.1);
      border-radius: 15px;
      padding: 20px;
      margin-bottom: 20px;
      border: 1px solid rgba(255,255,255,0.2);
    }
    .section-title {
      color: #fff;
      font-size: 1.3rem;
      font-weight: bold;
      margin-bottom: 15px;
      text-shadow: 0 2px 4px rgba(127,32,146,0.5);
    }
    @media (min-width: 768px) {
      .login-box { max-width: 900px; }
      .card { min-width: 800px; max-width: 900px; }
      .card .row { margin-left: -8px; margin-right: -8px; }
      .pr-md-1 { padding-right: 8px !important; }
      .pl-md-1 { padding-left: 8px !important; }
    }
    @media (max-width: 767.98px) {
      .login-box, .card { min-width: unset; max-width: 98vw; padding: 16px; }
      .pr-md-1, .pl-md-1 { padding-right: 0 !important; padding-left: 0 !important; }
      .form-control, .input-group-prepend .input-group-text { height: 44px; font-size: 1rem; }
      .btn-primary { height: 44px; font-size: 1rem; }
    }
  </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="card card-outline card-primary" style="max-width:320px; margin:auto;">
    <div class="card-header text-center position-relative">
      <a href="#" class="h1"><b>IT -</b> RSPI</a>
      <a href="form_login.php" style="position:absolute; top:5px; right:18px; color:#000000; font-size:2rem; text-decoration:none; z-index:2;" title="Tutup"><span aria-hidden="true">&times;</span></a>
    </div>
    <div class="card-body">
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger" style="font-size:1.1em;"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <?php if (!empty($success)): ?>
        <div class="alert alert-success" style="font-size:1.1em;"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
      <p class="login-box-msg">Register Calon Karyawan IT - Rumah Sakit Pelita Insani</p>
      <form action="#" method="post">
        <div class="form-section">
          <div class="section-title"><i class="fas fa-user"></i> Data Calon Karyawan</div>
          <div class="row">
            <div class="col-12 mb-2">
              <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                  <div class="input-group-text"><span class="fas fa-user"></span></div>
                </div>
                <input type="text" class="form-control" placeholder="Nama Lengkap" name="nama_lengkap" maxlength="100" required style="height:34px; font-size:1em;">
              </div>
            </div>
            <div class="col-12 mb-2">
              <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                  <div class="input-group-text"><span class="fas fa-user-circle"></span></div>
                </div>
                <input type="text" class="form-control" placeholder="Username" name="username" maxlength="50" required style="height:34px; font-size:1em;">
              </div>
            </div>
            <div class="col-12 mb-2">
              <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                  <div class="input-group-text"><span class="fas fa-lock"></span></div>
                </div>
                <input type="password" class="form-control" placeholder="Password" name="password" maxlength="255" required style="height:34px; font-size:1em;">
              </div>
            </div>
            <div class="col-12 mb-3">
              <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                  <div class="input-group-text"><span class="fas fa-phone"></span></div>
                </div>
                <input type="text" class="form-control" placeholder="No. Telpon" name="no_hp" maxlength="20" required style="height:34px; font-size:1em;">
              </div>
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-primary btn-block" style="height:36px; font-size:1em; border-radius:7px;">Daftar</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/dist/js/adminlte.min.js"></script>
<?php if (!empty($success)): ?>
<script>
  $(document).ready(function(){
    $('#successModal').modal('show');
    setTimeout(function(){
      window.location.href = 'form_login.php';
    }, 4000);
  });
</script>
<?php endif; ?>
</body>
</html> 