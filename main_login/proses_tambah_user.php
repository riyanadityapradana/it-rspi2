<?php
session_start();
require_once("../config/koneksi.php");

if (isset($_POST['simpan'])) {
    $nip          = mysqli_real_escape_string($config, $_POST['nip']);
    $nama_lengkap = mysqli_real_escape_string($config, $_POST['nama_lengkap']);
    $username     = mysqli_real_escape_string($config, $_POST['username']);
    $password     = mysqli_real_escape_string($config, $_POST['password']);
    $email        = mysqli_real_escape_string($config, $_POST['email']);
    $no_hp        = mysqli_real_escape_string($config, $_POST['no_hp']);
    $role         = mysqli_real_escape_string($config, $_POST['role']);

    // Handle upload foto
    $foto = null;
    if (!empty($_FILES['foto']['name'])) {
        $upload_dir = "../assets/upload/"; // Pastikan folder ada dan writable
        $file_name = basename($_FILES['foto']['name']);
        $target_file = $upload_dir . time() . "_" . $file_name; // Tambah timestamp untuk unik
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Validasi tipe file
        $allowed_types = array("jpg", "jpeg", "png", "gif");
        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
                $foto = $target_file;
            } else {
                echo "<script>
                        alert('Gagal mengupload foto!');
                        window.location = 'main_admin.php?unit=create_pokja';
                      </script>";
                exit;
            }
        } else {
            echo "<script>
                    alert('Tipe file foto tidak valid (hanya JPG, JPEG, PNG, GIF)!');
                    window.location = 'main_admin.php?unit=create_pokja';
                  </script>";
            exit;
        }
    }

    // Hash password sebelum disimpan
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Cek apakah nip sudah ada
    $cek_nip = mysqli_query($config, "SELECT * FROM tb_user WHERE nip='$nip'");
    if (mysqli_num_rows($cek_nip) > 0) {
        echo "<script>
                alert('NIP sudah digunakan, silakan pilih yang lain!');
                window.location = 'main_admin.php?unit=create_pokja';
              </script>";
        exit;
    }

    // Cek apakah username sudah ada
    $cek_username = mysqli_query($config, "SELECT * FROM tb_user WHERE username='$username'");
    if (mysqli_num_rows($cek_username) > 0) {
        echo "<script>
                alert('Username sudah digunakan, silakan pilih yang lain!');
                window.location = 'main_admin.php?unit=create_pokja';
              </script>";
        exit;
    }

    // Simpan ke database
    $query = "INSERT INTO tb_user (nip, nama_lengkap, username, password, email, no_hp, role, foto)
              VALUES ('$nip', '$nama_lengkap', '$username', '$hashed_password', '$email', '$no_hp', '$role', '$foto')";

    if (mysqli_query($config, $query)) {
        echo "<script>
                alert('User baru berhasil ditambahkan!');
                window.location = 'main_admin.php?unit=create_pokja';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menyimpan data: " . mysqli_error($config) . "');
                window.location = 'main_admin.php?unit=create_pokja';
              </script>";
    }
}
?>
