<?php
session_start();
require_once '../config/koneksi.php';

// Log aktivitas sebelum logout
if (isset($_SESSION['login_type'])) {
    if ($_SESSION['login_type'] == 'admin') {
        // Log untuk admin/staff
        if (isset($_SESSION['user_id'])) {
            $aktivitas = "Logout dari sistem";
            $log_stmt = $config->prepare('INSERT INTO log_aktivitas (kode_user, aktivitas) VALUES (?, ?)');
            $log_stmt->bind_param('ss', $_SESSION['user_id'], $aktivitas);
            $log_stmt->execute();
        }
    } elseif ($_SESSION['login_type'] == 'calon') {
        // Log untuk calon karyawan
        if (isset($_SESSION['calon_id'])) {
            $aktivitas = "Logout sebagai calon karyawan";
            $log_stmt = $config->prepare('INSERT INTO tb_log_tes (id_calon, aktivitas, ip_address) VALUES (?, ?, ?)');
            $ip = $_SERVER['REMOTE_ADDR'];
            $log_stmt->bind_param('sss', $_SESSION['calon_id'], $aktivitas, $ip);
            $log_stmt->execute();
        }
    }
}

// Hapus semua session
session_destroy();

// Redirect ke halaman login
header('Location: form_login.php?message=Anda telah berhasil logout');
exit;
?> 