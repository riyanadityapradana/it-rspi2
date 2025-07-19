<?php
require_once("../config/koneksi.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode_barang = $_POST['kode_barang'];
    $keterangan_rusak = $_POST['keterangan_rusak'];
    $status_perbaikan = $_POST['status_perbaikan'];
    
    // Validasi input
    if (empty($kode_barang) || empty($keterangan_rusak) || empty($status_perbaikan)) {
        header('Location: dashboard_staff.php?unit=barang&msg=Data tidak lengkap!');
        exit;
    }
    
    // Mulai transaksi
    mysqli_begin_transaction($config);
    
    try {
        // Update tabel tb_barang dengan status berdasarkan status perbaikan
        $status_barang = ($status_perbaikan == 'Dapat Diperbaiki') ? 'Baik' : 'Rusak';
        
        $query_update_barang = "UPDATE tb_barang SET 
                                stts_brg = ?,
                                keterangan_rusak = ?,
                                status_perbaikan = ?
                                WHERE kode_barang = ?";
        
        $stmt_barang = mysqli_prepare($config, $query_update_barang);
        mysqli_stmt_bind_param($stmt_barang, "ssss", $status_barang, $keterangan_rusak, $status_perbaikan, $kode_barang);
        
        if (!mysqli_stmt_execute($stmt_barang)) {
            throw new Exception("Gagal mengupdate status barang");
        }
        
        // Commit transaksi
        mysqli_commit($config);
        
        // Redirect dengan pesan sukses
        $pesan = ($status_barang == 'Baik') ? 
            'Status barang tetap Baik (dapat diperbaiki)!' : 
            'Status barang diubah menjadi Rusak (tidak dapat diperbaiki)!';
        header('Location: dashboard_staff.php?unit=barang&msg=' . urlencode($pesan));
        exit;
        
    } catch (Exception $e) {
        // Rollback jika terjadi error
        mysqli_rollback($config);
        header('Location: dashboard_staff.php?unit=barang&msg=Gagal mengubah status: ' . $e->getMessage());
        exit;
    }
} else {
    // Jika bukan POST request, redirect ke halaman barang
    header('Location: dashboard_staff.php?unit=barang');
    exit;
}
?> 