<?php
require_once '../../../config/koneksi.php';

if (isset($_GET['id'])) {
    $id_user = intval($_GET['id']);
    // Ambil nama file foto user
    $foto = null;
    $stmt_foto = $config->prepare('SELECT foto FROM tb_user WHERE id_user = ?');
    $stmt_foto->bind_param('i', $id_user);
    $stmt_foto->execute();
    $stmt_foto->bind_result($foto);
    $stmt_foto->fetch();
    $stmt_foto->close();

    // Hapus file foto jika ada dan bukan kosong/null
    if ($foto && $foto !== '' && file_exists('../../../assets/img/' . $foto)) {
        @unlink('../../../assets/img/' . $foto);
    }

    // Hapus data user
    $stmt = $config->prepare('DELETE FROM tb_user WHERE id_user = ?');
    $stmt->bind_param('i', $id_user);
    if ($stmt->execute()) {
        header('Location: ../../dashboard_admin.php?unit=user&msg=User berhasil dihapus!');
        exit;
    } else {
        header('Location: ../../dashboard_admin.php?unit=user&err=Gagal menghapus user!');
        exit;
    }
    $stmt->close();
} else {
    header('Location: ../../dashboard_admin.php?unit=user&err=Permintaan tidak valid!');
    exit;
}
