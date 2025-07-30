<?php
require_once("../config/koneksi.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = $_POST['id_user'];
    $nip = mysqli_real_escape_string($config, $_POST['nip']);
    $nama_lengkap = mysqli_real_escape_string($config, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($config, $_POST['username']);
    $email = mysqli_real_escape_string($config, $_POST['email']);
    $no_hp = mysqli_real_escape_string($config, $_POST['no_hp']);
    $password = $_POST['password'];
    $foto = '';
    $update_foto = '';

    // Handle upload foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto = 'user_' . $id_user . '_' . time() . '.' . $ext;
        $upload_path = '../../assets/img/' . $foto;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_path)) {
            $update_foto = ", foto='$foto'";
        }
    }

    // Handle password
    $update_password = '';
    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $update_password = ", password='$password_hash'";
    }

    $sql = "UPDATE tb_user SET nip='$nip', nama_lengkap='$nama_lengkap', username='$username', email='$email', no_hp='$no_hp' $update_password $update_foto WHERE id_user='$id_user'";
    $result = mysqli_query($config, $sql);

    if ($result) {
        // Update session
        $_SESSION['nip'] = $nip;
        $_SESSION['nama_lengkap'] = $nama_lengkap;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['no_hp'] = $no_hp;
        if ($update_foto) {
            $_SESSION['foto'] = $foto;
        }
        // Jika password diubah, bisa tambahkan logika logout paksa jika perlu
        $_SESSION['success_msg'] = 'Data berhasil diperbarui!';
    } else {
        $_SESSION['error_msg'] = 'Gagal memperbarui data!';
    }
    header('Location: user.php');
    exit;
} else {
    header('Location: user.php');
    exit;
}

