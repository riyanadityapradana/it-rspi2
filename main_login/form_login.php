<?php
session_start();
require_once __DIR__ . '/login_security.php';
itrspi_login_no_cache();

$loginError = isset($_SESSION['itrspi_login_error']) ? $_SESSION['itrspi_login_error'] : '';
$oldUsername = isset($_SESSION['itrspi_login_old_username']) ? $_SESSION['itrspi_login_old_username'] : '';
unset($_SESSION['itrspi_login_error'], $_SESSION['itrspi_login_old_username']);
// Pertahankan pesan dari halaman registrasi dan penjagaan akses dashboard.
if ($loginError === '' && isset($_GET['error']) && is_string($_GET['error'])) {
    $loginError = $_GET['error'];
}
$loginMessage = isset($_GET['message']) && is_string($_GET['message']) ? $_GET['message'] : '';
$loginWait = 0;
$captchaId = '';
$securityAvailable = true;
$csrfToken = itrspi_login_csrf_token();
try {
    $loginWait = itrspi_login_wait();
    $captchaId = itrspi_login_create_captcha();
} catch (Throwable $e) {
    error_log('IT-RSPI login security: ' . $e->getMessage());
    $securityAvailable = false;
    $loginError = 'Pengamanan login sedang tidak tersedia. Silakan hubungi administrator.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ITUTL-3 | Log in (v2)</title>
  <link rel="icon" href="../assets/img/icon.png">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../assets/dist/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="../assets/dist/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
  <style>
    body {
      min-height: 100vh;
      height: auto !important;
      padding: 24px 0;
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
      max-width: calc(100% - 32px);
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
    .fas.fa-eye {
      cursor: pointer;
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
    /* Tambahan agar icon di kiri lebih rapi */
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
    .captcha-field label, .captcha-help {
      color: #fff;
    }
    .captcha-field .form-control {
      border-radius: 10px !important;
    }
    .captcha-image {
      display: block;
      max-width: 100%;
      height: auto;
      border-radius: 10px;
      margin-bottom: 8px;
    }
    .captcha-refresh {
      display: inline-block;
      margin-bottom: 10px;
      font-size: 14px;
    }
    .captcha-help {
      display: block;
      margin-top: 8px;
      font-size: 13px;
      line-height: 1.5;
    }
    #loginButton:disabled {
      cursor: not-allowed;
      opacity: 0.6;
      box-shadow: none;
    }
  </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <!-- /.login-logo -->
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="#" class="h1"><b>IT -</b> RSPI</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <?php if ($loginError !== ''): ?>
        <div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>
      
      <?php if ($loginMessage !== ''): ?>
        <div class="alert alert-success" role="status"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($loginMessage, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>

      <form action="form_login_action.php" method="post" id="loginForm">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="captcha_id" value="<?= htmlspecialchars($captchaId, ENT_QUOTES, 'UTF-8') ?>">
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
          <input type="text" class="form-control" id="username" placeholder="Masukkan Username atau Email" name="username" value="<?= htmlspecialchars($oldUsername, ENT_QUOTES, 'UTF-8') ?>" aria-label="Username" autocomplete="username" autofocus required>
        </div>
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <div class="input-group-text">
              <span class="fas fa-eye" onclick="show()"></span>
            </div>
          </div>
          <input class="form-control" id="pswrd" placeholder="Masukkan Password" name="password" type="password" value="" aria-label="Password" autocomplete="current-password" required>
        </div>
        <?php if ($securityAvailable): ?>
        <div class="form-group captcha-field">
          <label for="captcha">Kode CAPTCHA</label>
          <img class="captcha-image" src="captcha.php?id=<?= rawurlencode($captchaId) ?>" width="220" height="70" alt="Gambar kode CAPTCHA. Ketik lima karakter yang terlihat di gambar.">
          <a class="captcha-refresh" href="form_login.php" id="refreshCaptcha">Ganti kode CAPTCHA</a>
          <input class="form-control" id="captcha" name="captcha" type="text" placeholder="Masukkan 5 karakter pada gambar" maxlength="5" minlength="5" autocomplete="off" autocapitalize="characters" spellcheck="false" aria-describedby="captchaHelp" required>
          <small class="captcha-help" id="captchaHelp">Huruf besar/kecil sama. Kode berlaku 5 menit dan hanya dapat digunakan sekali.</small>
        </div>
        <?php endif; ?>
        <p class="alert alert-warning" id="loginCooldown" role="status" aria-live="polite" <?= $loginWait > 0 ? '' : 'hidden' ?>>
          Silakan tunggu <strong id="loginSeconds"><?= (int) $loginWait ?></strong> detik sebelum mencoba lagi.
        </p>
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox">
              <label for="remember">
                Remember Me
              </label>
            </div>
          </div>
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block" id="loginButton" <?= (!$securityAvailable || $loginWait > 0) ? 'disabled' : '' ?>>Login</button>
          </div>
        </div>
        <small class="captcha-help">Setiap percobaan login yang gagal dikenai jeda 30 detik.</small>
        <noscript><p class="captcha-help">Setelah 30 detik, muat ulang halaman untuk mencoba login kembali.</p></noscript>
        <div class="row mt-3">
          <div class="col-12 text-center">
            <span style="color:#fff">Belum punya akun?</span> <a href="#" id="openRegisterModal">Register di sini</a>
          </div>
        </div>
      </form>
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- Register Modal -->
<div id="registerModal" class="modal-custom">
  <div class="modal-content-custom">
    <span class="close-modal" id="closeRegisterModal">&times;</span>
    <div class="register-options">
      <div class="register-box left-box" onclick="window.location.href='register_calon_pegawai.php'">
        <h3>Register Calon Pegawai</h3>
        <p>Daftar sebagai calon pegawai baru RSPI</p>
      </div>
      <div class="register-box right-box" onclick="window.location.href='register_staff.php'">
        <h3>Register Staff Pegawai</h3>
        <p>Daftar sebagai staff pegawai RSPI</p>
      </div>
    </div>
  </div>
</div>

<style>
.modal-custom {
  display: none;
  position: fixed;
  z-index: 9999;
  left: 0;
  top: 0;
  width: 100vw;
  height: 100vh;
  overflow: auto;
  background: rgba(30, 0, 60, 0.6);
  backdrop-filter: blur(4px);
}
.modal-content-custom {
  background: rgba(255,255,255,0.15);
  border-radius: 20px;
  margin: 5% auto;
  padding: 40px 30px 30px 30px;
  border: 1px solid rgba(255,255,255,0.18);
  width: 90%;
  max-width: 600px;
  box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
  position: relative;
}
.close-modal {
  color: #fff;
  position: absolute;
  top: 0px;
  right: 28px;
  font-size: 32px;
  font-weight: bold;
  cursor: pointer;
  z-index: 10;
  transition: color 0.3s;
}
.close-modal:hover {
  color: #7f2092;
}
.register-options {
  display: flex;
  gap: 24px;
  justify-content: center;
  align-items: stretch;
}
.register-box {
  flex: 1;
  background: rgba(255,255,255,0.25);
  border-radius: 16px;
  box-shadow: 0 2px 8px rgba(127,32,146,0.1);
  padding: 32px 16px;
  margin: 0 8px;
  text-align: center;
  color: #fff;
  cursor: pointer;
  transition: background 0.3s, box-shadow 0.3s, transform 0.2s;
  border: 1px solid rgba(255,255,255,0.18);
  backdrop-filter: blur(4px);
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.register-box:hover {
  background: linear-gradient(90deg, #7f2092 0%, #4e54c8 100%);
  box-shadow: 0 4px 16px #7f2092;
  transform: translateY(-4px) scale(1.03);
}
.register-box h3 {
  margin-bottom: 12px;
  font-weight: bold;
  font-size: 1.3rem;
  color: #fff;
  text-shadow: 0 2px 8px #7f2092;
}
.register-box p {
  color: #fff;
  opacity: 0.9;
}
@media (max-width: 600px) {
  .register-options {
    flex-direction: column;
    gap: 16px;
  }
  .register-box {
    margin: 0;
  }
}
</style>

<script>
(function () {
  var form = document.getElementById('loginForm');
  var button = document.getElementById('loginButton');
  var notice = document.getElementById('loginCooldown');
  var seconds = document.getElementById('loginSeconds');
  var remaining = <?= (int) $loginWait ?>;
  var available = <?= $securityAvailable ? 'true' : 'false' ?>;
  var deadline = Date.now() + remaining * 1000;
  if (remaining > 0) {
    var timer = setInterval(function () {
      remaining = Math.max(0, Math.ceil((deadline - Date.now()) / 1000));
      seconds.textContent = remaining;
      if (remaining === 0) {
        clearInterval(timer);
        notice.hidden = true;
        button.disabled = !available;
      }
    }, 250);
  }
  form.addEventListener('submit', function (event) {
    if (!available || remaining > 0) {
      event.preventDefault();
      return;
    }
    button.disabled = true;
  });
  var refresh = document.getElementById('refreshCaptcha');
  if (refresh) {
    refresh.addEventListener('click', function (event) {
      event.preventDefault();
      refresh.textContent = 'Memuat kode...';
      fetch('form_login.php', { cache: 'no-store', credentials: 'same-origin' })
        .then(function (response) {
          if (!response.ok) { throw new Error('CAPTCHA unavailable'); }
          return response.text();
        })
        .then(function (html) {
          var page = new DOMParser().parseFromString(html, 'text/html');
          var nextId = page.querySelector('input[name="captcha_id"]');
          var nextImage = page.querySelector('.captcha-image');
          if (!nextId || !nextImage) { throw new Error('CAPTCHA unavailable'); }
          form.querySelector('input[name="captcha_id"]').value = nextId.value;
          form.querySelector('.captcha-image').src = nextImage.getAttribute('src');
          form.querySelector('input[name="csrf_token"]').value = page.querySelector('input[name="csrf_token"]').value;
          document.getElementById('captcha').value = '';
          document.getElementById('captcha').focus();
        })
        .catch(function () { window.location.href = 'form_login.php'; })
        .finally(function () { refresh.textContent = 'Ganti kode CAPTCHA'; });
    });
  }
  window.addEventListener('pageshow', function (event) {
    if (event.persisted) { window.location.reload(); }
  });
})();

     function show(){
          var pswrd = document.getElementById('pswrd');
          var icon  = document.querySelector('.fa-eye');
          if (pswrd.type === "password"){
               pswrd.type = "text";
               icon.style.color ="#7f2092";
          }else{
               pswrd.type = "password";
               icon.style.color = "grey";
          }
     }
// Modal logic
const openModalBtn = document.getElementById('openRegisterModal');
const modal = document.getElementById('registerModal');
const closeModalBtn = document.getElementById('closeRegisterModal');

openModalBtn.addEventListener('click', function(e) {
  e.preventDefault();
  modal.style.display = 'block';
});
closeModalBtn.addEventListener('click', function() {
  modal.style.display = 'none';
});
window.onclick = function(event) {
  if (event.target === modal) {
    modal.style.display = 'none';
  }
};
</script>
</body>
</html>
