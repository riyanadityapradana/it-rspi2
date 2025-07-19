<?php
require_once("../config/koneksi.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode_barang = $_POST['kode_barang'];
    $keterangan_perbaikan = $_POST['keterangan_perbaikan'];
    
    // Validasi input
    if (empty($kode_barang) || empty($keterangan_perbaikan)) {
        header('Location: dashboard_staff.php?unit=barang&msg=Data tidak lengkap!');
        exit;
    }
    
    // Mulai transaksi
    mysqli_begin_transaction($config);
    
    try {
        // Update tabel tb_barang dengan status baik dan hapus data kerusakan
        $query_update_barang = "UPDATE tb_barang SET 
                                stts_brg = 'Baik',
                                keterangan_rusak = NULL,
                                status_perbaikan = NULL,
                                keterangan_perbaikan = ?
                                WHERE kode_barang = ?";
        
        $stmt_barang = mysqli_prepare($config, $query_update_barang);
        mysqli_stmt_bind_param($stmt_barang, "ss", $keterangan_perbaikan, $kode_barang);
        
        if (!mysqli_stmt_execute($stmt_barang)) {
            throw new Exception("Gagal mengupdate status barang");
        }
        
        // Commit transaksi
        mysqli_commit($config);
        
        // Redirect dengan pesan sukses
        header('Location: dashboard_staff.php?unit=barang&msg=Status barang berhasil diubah menjadi baik!');
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