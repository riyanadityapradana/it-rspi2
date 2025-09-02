<?php
// Koneksi ke database
$host = '192.168.1.4';
$user = 'root';
$pass = '';
$db   = 'sik9';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    die('Koneksi gagal: ' . $conn->connect_error);
}
$conn->set_charset('utf8');

// Validasi input
if (isset($_POST['old_id']) && isset($_POST['new_id'])) {
    $old_id = trim($_POST['old_id']);
    $new_id = trim($_POST['new_id']);
    if ($old_id === '' || $new_id === '') {
        echo 'ERROR: Data tidak boleh kosong';
        exit;
    }
    // Cek apakah new_id sudah ada
    $cek = $conn->prepare("SELECT no_resep FROM resep_obat WHERE no_resep = ?");
    $cek->bind_param('s', $new_id);
    $cek->execute();
    $cek->store_result();
    if ($cek->num_rows > 0) {
        echo 'ERROR: No. Resep sudah ada!';
        exit;
    }
    $cek->close();
    // Update no_resep
    $stmt = $conn->prepare("UPDATE resep_obat SET no_resep = ? WHERE no_resep = ?");
    $stmt->bind_param('ss', $new_id, $old_id);
    if ($stmt->execute()) {
        echo 'OK';
    } else {
        echo 'ERROR: ' . $stmt->error;
    }
    $stmt->close();
} else {
    echo 'ERROR: Data tidak lengkap';
}
