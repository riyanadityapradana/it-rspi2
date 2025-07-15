<?php
session_start();
require_once '../config/koneksi.php';

// Ambil data dari form
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if ($username === '' || $password === '') {
    header('Location: form_login.php?error=Username dan Password wajib diisi');
    exit();
}

// Cek ke tb_user (username/email dan password)
$stmt = $config->prepare("SELECT * FROM tb_user WHERE (username = ? OR email = ?) AND password = ? AND status = 'aktif' LIMIT 1");
$stmt->bind_param('sss', $username, $username, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Login user internal
    $_SESSION['id_user'] = $row['id_user'];
    $_SESSION['nip'] = $row['nip'];
    $_SESSION['username'] = $row['username'];
    $_SESSION['nama_lengkap'] = $row['nama_lengkap'];
    $_SESSION['role'] = $row['role'];
    $_SESSION['login_type'] = 'user';

    if ($row['role'] === 'Kepala Ruangan') {
        header('Location: ../admin/dashboard_admin.php');
        exit();
    } elseif ($row['role'] === 'Staff') {
        header('Location: ../staff/dashboard_staff.php?unit=beranda');
        exit();
    } else {
        header('Location: form_login.php?error=Role user tidak dikenali');
        exit();
    }
}
$stmt->close();

// Cek ke tb_calon (username/email dan password)
$stmt2 = $config->prepare("SELECT * FROM tb_calon WHERE (username = ? OR email = ?) LIMIT 1");
$stmt2->bind_param('ss', $username, $username);
$stmt2->execute();
$result2 = $stmt2->get_result();

if ($row2 = $result2->fetch_assoc()) {
    // Verifikasi password hash
    if (password_verify($password, $row2['password'])) {
        // Login calon karyawan
        $_SESSION['id_calon'] = $row2['id_calon'];
        $_SESSION['username'] = $row2['username'];
        $_SESSION['nama_lengkap'] = $row2['nama_lengkap'];
        $_SESSION['login_type'] = 'calon';
        header('Location: ../calon-karyawan/dashboard_calon.php?unit=beranda');
        exit();
    }
}
$stmt2->close();

// Jika tidak ditemukan di kedua tabel
header('Location: form_login.php?error=Username/email atau password salah');
exit();

