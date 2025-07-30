<?php
require_once("../config/koneksi.php");
//session_start();
// Ambil data user dari session dan database
$id = $_SESSION['id_user'];
$query = "SELECT * FROM tb_user WHERE id_user = '$id'";
$user = mysqli_fetch_array(mysqli_query($config, $query));
$nama = $user['nama_lengkap'];
$username = $user['username'];
$nip = $user['nip'];
$foto = isset($user['foto']) && $user['foto'] ? $user['foto'] : 'default-user.png';
$role = isset($user['role']) ? strtoupper($user['role']) : 'USER';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya</title>
    <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .profile-card {
            border-radius: 20px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            padding: 32px 24px 24px 24px;
            background: #fff;
            position: relative;
            max-width: 420px;
            min-height: 600px;
            margin: 40px auto;
        }
        .profile-header-bar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }
        .profile-header-left {
            display: flex;
            align-items: center;
        }
        .profile-logo-kemkes {
            width: 48px;
            height: 48px;
            object-fit: contain;
            margin-right: 8px;
        }
        .profile-kemkes-text {
            font-size: 0.85rem;
            font-weight: bold;
            color: #1a237e;
            line-height: 1.1;
        }
        .profile-logo-kanan {
            width: 44px;
            height: 44px;
            object-fit: contain;
        }
        .profile-role {
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 18px;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }
        .profile-role-green {
            color: #1de9b6;
        }
        .profile-role-dark {
            color: #263238;
        }
        .profile-box {
            background: #174b7e;
            color: #fff;
            border-radius: 6px;
            padding: 8px 18px;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 8px;
            width: fit-content;
            min-width: 220px;
        }
        .profile-id-box {
            background: #174b7e;
            color: #fff;
            border-radius: 6px;
            padding: 8px 18px;
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 18px;
            width: fit-content;
            min-width: 220px;
        }
        .profile-main {
            position: relative;
            min-height: 180px;
            margin-bottom: 0;
        }
        .profile-name-bg {
            background: #174b7e;
            border-radius: 32px 0 0 0;
            min-height: 180px;
            width: 70%;
            position: absolute;
            left: 0;
            bottom: 0;
            z-index: 1;
            display: flex;
            align-items: flex-end;
            padding: 24px 0 24px 24px;
        }
        .profile-name {
            font-size: 2rem;
            font-weight: bold;
            letter-spacing: 2px;
            color: #fff;
            line-height: 1.1;
            z-index: 3;
            position: relative;
            text-shadow: 0 2px 8px rgba(0,0,0,0.25), 0 1px 2px rgba(0,0,0,0.18);
        }
        .profile-username {
            font-size: 1.1rem;
            color: #b2ebf2;
            margin-bottom: 0;
            z-index: 3;
            position: relative;
            text-shadow: 0 2px 8px rgba(0,0,0,0.18);
        }
        .profile-foto {
            width: 200px;
            height: 250px;
            object-fit: cover;
            border-radius: 32px 32px 32px 32px;
            border: 6px solid #fff;
            position: absolute;
            right: -30px;
            bottom: -30px;
            z-index: 2;
            background: #e3eafc;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18), 0 2px 8px rgba(0,0,0,0.10);
            animation: fotoFadeInUp 0.7s cubic-bezier(.39,.575,.565,1) both;
        }
        @keyframes fotoFadeInUp {
            0% {
                opacity: 0;
                transform: translateY(60px) scale(0.95);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        .edit-btn {
            margin-top: 32px;
            width: 100%;
        }
        @media (max-width: 500px) {
            .profile-card { padding: 16px 4px 16px 4px; }
            .profile-name-bg { width: 100%; min-height: 90px; padding: 12px 0 12px 12px; }
            .profile-foto { width: 110px; height: 130px; right: -10px; bottom: -10px; }
        }
    </style>
</head>
<body style="background: #f4f6f9;">
    <div class="container">
        <button class="btn btn-primary mt-5" data-toggle="modal" data-target="#modalProfile">Lihat Profil Saya</button>
        <!-- Modal -->
        <div class="modal fade" id="modalProfile" tabindex="-1" role="dialog" aria-labelledby="modalProfileLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-body">
                <div class="profile-card">
                  <div class="profile-header-bar">
                    <div class="profile-header-left">
                      <img src="../assets/img/logo.jpg" class="profile-logo-kemkes" alt="Logo KEMENKES">
                      <div class="profile-kemkes-text">
                        RS<br>PELITA INSANI<br>MARTAPURA
                      </div>
                    </div>
                    <img src="../assets/img/icon.png" class="profile-logo-kanan" alt="Logo Kanan">
                  </div>
                  <div class="profile-role">
                    <span class="profile-role-green"><?php echo substr(htmlspecialchars($role), 0, 4); ?></span><span class="profile-role-dark"><?php echo substr(htmlspecialchars($role), 4); ?></span>
                  </div>
                  <div class="profile-box">RUMAH SAKIT PELITA INSANI</div>
                  <div class="profile-id-box">ID/NIP : <?php echo htmlspecialchars($nip); ?></div>
                  <div class="profile-main">
                     <div class="profile-name-bg"></div>
                     <img src="../assets/upload/<?php echo htmlspecialchars($foto); ?>" class="profile-foto" alt="Foto User">
                     <div style="position: absolute; left: 32px; bottom: 32px; z-index: 3;">
                       <div class="profile-name"><?php echo htmlspecialchars($nama); ?></div>
                       <div class="profile-username">@<?php echo htmlspecialchars($username); ?></div>
                     </div>
                  </div>
                  <button class="btn btn-success edit-btn" data-toggle="modal" data-target="#modalEditProfile" data-dismiss="modal"><i class="fas fa-edit"></i> Edit Data</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Modal Edit Profile (akan diisi tahap berikutnya) -->
        <div class="modal fade" id="modalEditProfile" tabindex="-1" role="dialog" aria-labelledby="modalEditProfileLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalEditProfileLabel">Edit Data Profil</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form action="/it-rspi/staff/unit/user/update.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                  <div class="form-group">
                    <label for="nip">NIP</label>
                    <input type="text" class="form-control" id="nip" name="nip" value="<?php echo htmlspecialchars($nip); ?>" required>
                  </div>
                  <div class="form-group">
                    <label for="nama_lengkap">Nama Lengkap</label>
                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($nama); ?>" required>
                  </div>
                  <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                  </div>
                  <div class="form-group">
                    <label for="password">Password (Kosongkan jika tidak ingin diubah)</label>
                    <input type="password" class="form-control" id="password" name="password" autocomplete="new-password">
                  </div>
                  <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($user['email']) ? htmlspecialchars($user['email']) : ''; ?>">
                  </div>
                  <div class="form-group">
                    <label for="no_hp">No. HP</label>
                    <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?php echo isset($user['no_hp']) ? htmlspecialchars($user['no_hp']) : ''; ?>">
                  </div>
                  <div class="form-group">
                    <label for="foto">Foto</label><br>
                    <input type="file" id="foto" name="foto" accept="image/*">
                    <?php if ($foto && $foto !== 'default-user.png'): ?>
                      <br><img src="../assets/img/<?php echo htmlspecialchars($foto); ?>" alt="Foto Saat Ini" style="width:60px;height:60px;border-radius:8px;margin-top:8px;">
                    <?php endif; ?>
                  </div>
                  <input type="hidden" name="id_user" value="<?php echo htmlspecialchars($id); ?>">
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                  <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
              </form>
            </div>
          </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
