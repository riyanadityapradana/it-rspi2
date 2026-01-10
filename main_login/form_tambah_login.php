<?php
require_once("../config/koneksi.php");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah User Baru</title>
</head>
<body>
    <h2>Form Tambah User</h2>

    <form action="proses_tambah_user.php" method="POST" enctype="multipart/form-data">
        <label>NIP</label><br>
        <input type="text" name="nip" required><br><br>

        <label>Nama Lengkap</label><br>
        <input type="text" name="nama_lengkap" required><br><br>

        <label>Username</label><br>
        <input type="text" name="username" required><br><br>

        <label>Password</label><br>
        <input type="password" name="password" required><br><br>

        <label>Email</label><br>
        <input type="email" name="email"><br><br>

        <label>No HP</label><br>
        <input type="text" name="no_hp"><br><br>

        <label>Role</label><br>
        <select name="role" required>
            <option value="">-- Pilih Role --</option>
            <option value="Kepala Ruangan">Kepala Ruangan</option>
            <option value="Staff">Staff</option>
        </select><br><br>

        <label>Foto</label><br>
        <input type="file" name="foto" accept="image/*"><br><br>

        <button type="submit" name="simpan">Simpan</button>
    </form>

</body>
</html>
