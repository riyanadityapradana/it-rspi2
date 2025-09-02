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
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .profile-container {
            padding: 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .profile-card {
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            padding: 0;
            position: relative;
            max-width: 450px;
            min-height: 650px;
            margin: 0 auto;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .profile-header {
            background: linear-gradient(135deg, #174b7e 0%, #2c5aa0 100%);
            padding: 30px 25px 20px 25px;
            position: relative;
            overflow: hidden;
        }
        
        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .profile-header-bar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            position: relative;
            z-index: 2;
        }
        
        .profile-header-left {
            display: flex;
            align-items: center;
        }
        
        .profile-logo-kemkes {
            width: 50px;
            height: 50px;
            object-fit: contain;
            margin-right: 12px;
            border-radius: 10px;
            background: rgba(255,255,255,0.1);
            padding: 5px;
        }
        
        .profile-kemkes-text {
            font-size: 0.9rem;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.2;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .profile-logo-kanan {
            width: 45px;
            height: 45px;
            object-fit: contain;
            border-radius: 10px;
            background: rgba(255,255,255,0.1);
            padding: 5px;
        }
        
        .profile-role {
            font-size: 1.6rem;
            font-weight: 800;
            margin: 20px 0 15px 0;
            letter-spacing: 3px;
            position: relative;
            z-index: 2;
        }
        
        .profile-role-green {
            color: #4ade80;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .profile-role-dark {
            color: #ffffff;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .profile-box {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            color: #ffffff;
            border-radius: 12px;
            padding: 12px 20px;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 10px;
            width: fit-content;
            min-width: 250px;
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .profile-id-box {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            color: #ffffff;
            border-radius: 12px;
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 0;
            width: fit-content;
            min-width: 250px;
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .profile-main {
            position: relative;
            min-height: 200px;
            margin-bottom: 0;
            padding: 30px 25px;
        }
        
        .profile-name-bg {
            background: linear-gradient(135deg, #174b7e 0%, #2c5aa0 100%);
            border-radius: 25px 0 0 0;
            min-height: 200px;
            width: 75%;
            position: absolute;
            left: 0;
            bottom: 0;
            z-index: 1;
            display: flex;
            align-items: flex-end;
            padding: 30px 0 30px 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .profile-name {
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: 1px;
            color: #ffffff;
            line-height: 1.1;
            z-index: 3;
            position: relative;
            text-shadow: 0 3px 10px rgba(0,0,0,0.3);
        }
        
        .profile-username {
            font-size: 1.2rem;
            color: #b2ebf2;
            margin-bottom: 0;
            z-index: 3;
            position: relative;
            text-shadow: 0 2px 8px rgba(0,0,0,0.2);
            font-weight: 500;
        }
        
        .profile-foto {
            width: 220px;
            height: 280px;
            object-fit: cover;
            border-radius: 25px;
            border: 8px solid #ffffff;
            position: absolute;
            right: -35px;
            bottom: -35px;
            z-index: 2;
            background: #e3eafc;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2), 0 5px 15px rgba(0,0,0,0.1);
            animation: fotoFadeInUp 0.8s cubic-bezier(.39,.575,.565,1) both;
            transition: transform 0.3s ease;
        }
        
        .profile-foto:hover {
            transform: scale(1.05);
        }
        
        @keyframes fotoFadeInUp {
            0% {
                opacity: 0;
                transform: translateY(80px) scale(0.9);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .profile-actions {
            padding: 25px;
            background: #ffffff;
        }
        
        .edit-btn {
            width: 100%;
            padding: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 15px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        
        .edit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
            color: white;
        }
        
        .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }
        
        .modal-header {
            background: linear-gradient(135deg, #174b7e 0%, #2c5aa0 100%);
            color: white;
            border-radius: 20px 20px 0 0;
            border-bottom: none;
        }
        
        .modal-title {
            font-weight: 700;
            font-size: 1.3rem;
        }
        
        .close {
            color: white;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }
        
        .close:hover {
            opacity: 1;
        }
        
        .form-group label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #174b7e;
            box-shadow: 0 0 0 0.2rem rgba(23, 75, 126, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #174b7e 0%, #2c5aa0 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(23, 75, 126, 0.3);
        }
        
        .btn-secondary {
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
        }
        
        .current-photo {
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 2px solid #e9ecef;
        }
        
        @media (max-width: 576px) {
            .profile-card { 
                margin: 10px;
                max-width: 100%;
            }
            .profile-name-bg { 
                width: 100%; 
                min-height: 120px; 
                padding: 20px 0 20px 20px; 
            }
            .profile-foto { 
                width: 140px; 
                height: 180px; 
                right: -15px; 
                bottom: -15px; 
            }
            .profile-name {
                font-size: 1.8rem;
            }
            .profile-username {
                font-size: 1rem;
            }
        }
        
        .floating-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #174b7e 0%, #2c5aa0 100%);
            color: white;
            border: none;
            box-shadow: 0 8px 25px rgba(23, 75, 126, 0.3);
            font-size: 1.5rem;
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
                 .floating-btn:hover {
             transform: scale(1.1);
             box-shadow: 0 12px 35px rgba(23, 75, 126, 0.4);
             color: white;
         }
         
         /* Modern Modal Styles */
         .modern-modal {
             border-radius: 20px;
             border: none;
             box-shadow: 0 25px 80px rgba(0,0,0,0.15);
             overflow: hidden;
         }
         
         .modern-header {
             background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
             border: none;
             padding: 25px 30px;
             position: relative;
         }
         
         .modern-header::before {
             content: '';
             position: absolute;
             top: 0;
             left: 0;
             right: 0;
             bottom: 0;
             background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
             opacity: 0.3;
         }
         
                   .modern-header {
              position: relative;
          }
          
          .header-content {
              display: flex;
              justify-content: space-between;
              align-items: center;
              position: relative;
              z-index: 2;
          }
         
         .logo-section {
             display: flex;
             align-items: center;
             gap: 15px;
         }
         
         .header-logo {
             width: 50px;
             height: 50px;
             border-radius: 12px;
             object-fit: cover;
             border: 2px solid rgba(255,255,255,0.2);
         }
         
         .header-text h4 {
             color: white;
             font-weight: 700;
             margin: 0;
             font-size: 1.2rem;
             text-shadow: 0 2px 4px rgba(0,0,0,0.3);
         }
         
         .header-text p {
             color: rgba(255,255,255,0.9);
             margin: 0;
             font-size: 0.9rem;
             font-weight: 500;
         }
         
                   .modern-close {
              color: white;
              opacity: 0.8;
              font-size: 1.5rem;
              transition: all 0.3s ease;
              background: rgba(255,255,255,0.1);
              border: none;
              border-radius: 50%;
              width: 40px;
              height: 40px;
              display: flex;
              align-items: center;
              justify-content: center;
              position: absolute;
              right: 20px;
              top: 50%;
              transform: translateY(-50%);
          }
         
         .modern-close:hover {
             opacity: 1;
             background: rgba(255,255,255,0.2);
             transform: scale(1.1);
         }
         
         .modern-body {
             padding: 0;
             background: #f8f9fa;
         }
         
         .user-profile-grid {
             padding: 30px;
         }
         
         .profile-card-modern {
             background: white;
             border-radius: 20px;
             padding: 30px;
             box-shadow: 0 10px 40px rgba(0,0,0,0.1);
             position: relative;
             overflow: hidden;
         }
         
         .profile-card-modern::before {
             content: '';
             position: absolute;
             top: 0;
             left: 0;
             right: 0;
             height: 4px;
             background: linear-gradient(90deg, #667eea, #764ba2);
         }
         
         .card-header-modern {
             display: flex;
             justify-content: flex-end;
             margin-bottom: 20px;
         }
         
         .role-badge {
             background: linear-gradient(135deg, #28a745, #20c997);
             color: white;
             padding: 8px 16px;
             border-radius: 20px;
             font-size: 0.9rem;
             font-weight: 600;
             display: flex;
             align-items: center;
             gap: 8px;
             box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
         }
         
         .profile-image-section {
             text-align: center;
             margin-bottom: 25px;
         }
         
         .profile-image-container {
             position: relative;
             display: inline-block;
         }
         
         .profile-image {
             width: 120px;
             height: 120px;
             border-radius: 50%;
             object-fit: cover;
             border: 4px solid white;
             box-shadow: 0 8px 25px rgba(0,0,0,0.15);
             transition: transform 0.3s ease;
         }
         
         .profile-image:hover {
             transform: scale(1.05);
         }
         
         .image-overlay {
             position: absolute;
             bottom: 5px;
             right: 5px;
             background: #667eea;
             color: white;
             width: 35px;
             height: 35px;
             border-radius: 50%;
             display: flex;
             align-items: center;
             justify-content: center;
             font-size: 0.9rem;
             box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
         }
         
         .profile-info {
             text-align: center;
             margin-bottom: 25px;
         }
         
         .user-name {
             font-size: 1.8rem;
             font-weight: 700;
             color: #2c3e50;
             margin-bottom: 5px;
         }
         
         .user-title {
             color: #667eea;
             font-weight: 600;
             font-size: 1rem;
             margin-bottom: 5px;
         }
         
         .user-username {
             color: #95a5a6;
             font-size: 0.9rem;
             margin-bottom: 20px;
         }
         
         .user-details {
             text-align: left;
             background: #f8f9fa;
             border-radius: 15px;
             padding: 20px;
             margin-top: 20px;
         }
         
         .detail-item {
             display: flex;
             align-items: center;
             gap: 12px;
             margin-bottom: 12px;
             padding: 8px 0;
         }
         
         .detail-item:last-child {
             margin-bottom: 0;
         }
         
         .detail-item i {
             color: #667eea;
             width: 20px;
             font-size: 1rem;
         }
         
         .detail-item span {
             color: #2c3e50;
             font-weight: 500;
             font-size: 0.95rem;
         }
         
         .profile-actions-modern {
             display: flex;
             gap: 15px;
             justify-content: center;
         }
         
         .btn-chat {
             background: #28a745;
             color: white;
             border: none;
             border-radius: 10px;
             width: 50px;
             height: 50px;
             display: flex;
             align-items: center;
             justify-content: center;
             font-size: 1.2rem;
             transition: all 0.3s ease;
             box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
         }
         
         .btn-chat:hover {
             transform: translateY(-2px);
             box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
             color: white;
         }
         
         .btn-view-profile {
             background: linear-gradient(135deg, #667eea, #764ba2);
             color: white;
             border: none;
             border-radius: 10px;
             padding: 12px 25px;
             font-weight: 600;
             display: flex;
             align-items: center;
             gap: 8px;
             transition: all 0.3s ease;
             box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
         }
         
         .btn-view-profile:hover {
             transform: translateY(-2px);
             box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
             color: white;
         }
         
         @media (max-width: 768px) {
             .profile-actions-modern {
                 flex-direction: column;
                 align-items: center;
             }
             
             .btn-chat {
                 width: 100%;
                 max-width: 200px;
             }
             
             .user-name {
                 font-size: 1.5rem;
             }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <button class="floating-btn" data-toggle="modal" data-target="#modalProfile">
            <i class="fas fa-user"></i>
        </button>
        
        <!-- Modal Profile -->
        <div class="modal fade" id="modalProfile" tabindex="-1" role="dialog" aria-labelledby="modalProfileLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content modern-modal">
                             <div class="modal-header modern-header">
                 <div class="logo-section">
                   <img src="../assets/img/logo.jpg" alt="Logo" class="header-logo">
                   <div class="header-text">
                     <h4>RUMAH SAKIT PELITA INSANI</h4>
                     <p>Martapura, Kalimantan Selatan</p>
                   </div>
                 </div>
                 <button type="button" class="close modern-close" onclick="window.location.href='dashboard_staff.php?unit=beranda'">
                    <span aria-hidden="true">&times;</span>
                  </button>
               </div>
              
              <div class="modal-body modern-body">
                <div class="user-profile-grid">
                  <!-- Profile Card -->
                  <div class="profile-card-modern">
                    <div class="card-header-modern">
                      <div class="role-badge">
                        <i class="fas fa-user-tie"></i>
                        <span><?php echo htmlspecialchars($role); ?></span>
                      </div>
                    </div>
                    
                    <div class="profile-image-section">
                      <div class="profile-image-container">
                        <img src="../assets/img/<?php echo htmlspecialchars($foto); ?>" alt="Foto User" class="profile-image">
                        <div class="image-overlay">
                          <i class="fas fa-camera"></i>
                        </div>
                      </div>
                    </div>
                    
                    <div class="profile-info">
                      <h3 class="user-name"><?php echo htmlspecialchars($nama); ?></h3>
                      <p class="user-title"><?php echo htmlspecialchars($role); ?></p>
                      <p class="user-username">@<?php echo htmlspecialchars($username); ?></p>
                      
                      <div class="user-details">
                        <div class="detail-item">
                          <i class="fas fa-id-card"></i>
                          <span>NIP: <?php echo htmlspecialchars($nip); ?></span>
                        </div>
                        <?php if (isset($user['email']) && $user['email']): ?>
                        <div class="detail-item">
                          <i class="fas fa-envelope"></i>
                          <span><?php echo htmlspecialchars($user['email']); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($user['no_hp']) && $user['no_hp']): ?>
                        <div class="detail-item">
                          <i class="fas fa-phone"></i>
                          <span><?php echo htmlspecialchars($user['no_hp']); ?></span>
                        </div>
                        <?php endif; ?>
                  </div>
                  </div>
                    
                    <div class="profile-actions-modern">
                      <button class="btn btn-chat" data-toggle="modal" data-target="#modalEditProfile" data-dismiss="modal">
                        <i class="fas fa-comments"></i>
                      </button>
                      <button class="btn btn-view-profile" data-toggle="modal" data-target="#modalEditProfile" data-dismiss="modal">
                        <i class="fas fa-user-edit"></i>
                        Edit Profile
                      </button>
                     </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Modal Edit Profile -->
        <div class="modal fade" id="modalEditProfile" tabindex="-1" role="dialog" aria-labelledby="modalEditProfileLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalEditProfileLabel">
                    <i class="fas fa-user-edit mr-2"></i>Edit Data Profil
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form action="/it-rspi/staff/unit/user/update.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                  <div class="row">
                    <div class="col-md-6">
                  <div class="form-group">
                        <label for="nip"><i class="fas fa-id-card mr-2"></i>NIP</label>
                    <input type="text" class="form-control" id="nip" name="nip" value="<?php echo htmlspecialchars($nip); ?>" required>
                  </div>
                    </div>
                    <div class="col-md-6">
                  <div class="form-group">
                        <label for="nama_lengkap"><i class="fas fa-user mr-2"></i>Nama Lengkap</label>
                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($nama); ?>" required>
                      </div>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-6">
                  <div class="form-group">
                        <label for="username"><i class="fas fa-at mr-2"></i>Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                  </div>
                    </div>
                    <div class="col-md-6">
                  <div class="form-group">
                        <label for="password"><i class="fas fa-lock mr-2"></i>Password</label>
                        <input type="password" class="form-control" id="password" name="password" autocomplete="new-password" placeholder="Kosongkan jika tidak ingin diubah">
                      </div>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-6">
                  <div class="form-group">
                        <label for="email"><i class="fas fa-envelope mr-2"></i>Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($user['email']) ? htmlspecialchars($user['email']) : ''; ?>">
                  </div>
                    </div>
                    <div class="col-md-6">
                  <div class="form-group">
                        <label for="no_hp"><i class="fas fa-phone mr-2"></i>No. HP</label>
                    <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?php echo isset($user['no_hp']) ? htmlspecialchars($user['no_hp']) : ''; ?>">
                      </div>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="foto"><i class="fas fa-camera mr-2"></i>Foto Profil</label>
                    <div class="custom-file">
                      <input type="file" class="custom-file-input" id="foto" name="foto" accept="image/*">
                      <label class="custom-file-label" for="foto">Pilih file...</label>
                    </div>
                    <?php if ($foto && $foto !== 'default-user.png'): ?>
                      <div class="mt-3">
                        <label>Foto Saat Ini:</label>
                        <img src="../assets/img/<?php echo htmlspecialchars($foto); ?>" alt="Foto Saat Ini" class="current-photo" style="width:80px;height:80px;object-fit:cover;">
                      </div>
                    <?php endif; ?>
                  </div>
                  
                  <input type="hidden" name="id_user" value="<?php echo htmlspecialchars($id); ?>">
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="window.location.href='dashboard_staff.php?unit=beranda'">
                      <i class="fas fa-times mr-2"></i>Batal
                  </button>
                  <button type="submit" class="btn btn-primary">
                      <i class="fas fa-save mr-2"></i>Simpan Perubahan
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Update custom file input label
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });
        
        // Auto show modal on page load
        $(document).ready(function() {
            $('#modalProfile').modal('show');
        });
    </script>
</body>
</html>
