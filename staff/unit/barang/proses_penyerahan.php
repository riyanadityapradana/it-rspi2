<?php
require_once("../config/koneksi.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode_barang = $_POST['kode_barang'];
    $penyerahan = $_POST['penyerahan'];
    $id_pengajuan = $_POST['id_pengajuan'];
    $tgl_penyerahan = $_POST['tgl_penyerahan'];
    
    // Validasi input
    if (empty($kode_barang) || empty($penyerahan)) {
        header('Location: dashboard_staff.php?unit=barang&msg=Data tidak lengkap!');
        exit;
    }
    
    // Mulai transaksi
    mysqli_begin_transaction($config);
    
    try {
        // Update tabel tb_barang dengan penyerahan, status barang, dan status_perbaikan
        $query_update_barang = "UPDATE tb_barang SET 
                                penyerahan = ?, 
                                tgl_penyerahan = ?,
                                stts_brg = 'Baik',
                                status_perbaikan = 'Belum Ada Perbaikan'
                                WHERE kode_barang = ?";
        
        $stmt_barang = mysqli_prepare($config, $query_update_barang);
        mysqli_stmt_bind_param($stmt_barang, "sss", $penyerahan, $tgl_penyerahan, $kode_barang);
        
        if (!mysqli_stmt_execute($stmt_barang)) {
            throw new Exception("Gagal mengupdate data barang");
        }
        
        // Update status pengajuan menjadi 'Selesai' (opsional)
        if (!empty($id_pengajuan)) {
            $query_update_pengajuan = "UPDATE tb_pengajuan_barang SET 
                                      status = 'Selesai' 
                                      WHERE id_pengajuan = ?";
            
            $stmt_pengajuan = mysqli_prepare($config, $query_update_pengajuan);
            mysqli_stmt_bind_param($stmt_pengajuan, "i", $id_pengajuan);
            
            if (!mysqli_stmt_execute($stmt_pengajuan)) {
                throw new Exception("Gagal mengupdate status pengajuan");
            }
        }
        
        // Commit transaksi
        mysqli_commit($config);
        
        // Redirect dengan pesan sukses
        header('Location: dashboard_staff.php?unit=barang&msg=Penyerahan barang berhasil disimpan!');
        exit;
        
    } catch (Exception $e) {
        // Rollback jika terjadi error
        mysqli_rollback($config);
        header('Location: dashboard_staff.php?unit=barang&msg=Gagal menyimpan penyerahan: ' . $e->getMessage());
        exit;
    }
} else {
    // Jika bukan POST request, redirect ke halaman barang
    header('Location: dashboard_staff.php?unit=barang');
    exit;
}
?> 