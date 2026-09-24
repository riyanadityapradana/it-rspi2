<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (!ob_get_level()) ob_start();
require_once __DIR__ . '/../../../config/koneksi.php';
require_once __DIR__ . '/helpers.php';
$mnBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'], 4)), '/');
// Dashboard includes the module from one directory below the application root.
if (strpos($_SERVER['SCRIPT_NAME'], '/unit/monev/') === false) {
    $mnBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'], 2)), '/');
}
if (empty($_SESSION['id_user'])) {
    header('Location: '.$mnBase.'/main_login/form_login.php'); exit;
}
$mnUser = mn_one('SELECT id_user,nama_lengkap,role,status FROM tb_user WHERE id_user=?', [$_SESSION['id_user']]);
if (!$mnUser || $mnUser['status'] !== 'aktif' || !in_array($mnUser['role'], ['Staff','Kepala Ruangan'], true)) {
    http_response_code(403); exit('Akses ditolak.');
}
if (empty($_SESSION['monev_csrf'])) $_SESSION['monev_csrf'] = bin2hex(random_bytes(32));
$mnUrl = $mnBase.($mnUser['role'] === 'Staff' ? '/staff/dashboard_staff.php' : '/admin/dashboard_admin.php').'?unit=monev';
$mnEndpoint = $mnBase.'/staff/unit/monev';
