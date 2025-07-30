<?php
require_once '../config/koneksi.php';

// Ambil data user berdasarkan id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<div class="alert alert-danger">ID user tidak valid.</div>';
    exit;
}
$id_user = intval($_GET['id']);
$stmt = $config->prepare('SELECT * FROM tb_user WHERE id_user = ?');
$stmt->bind_param('i', $id_user);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
if (!$user) {
    echo '<div class="alert alert-danger">User tidak ditemukan.</div>';
    exit;
}

// Proses update jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nip = trim($_POST['nip'] ?? '');
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $status = trim($_POST['status'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $foto = $user['foto'];
    $updated_at = date('Y-m-d H:i:s');
    $upload_dir = '../assets/img/';
    $foto_baru = false;

    // Handle upload foto baru
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        $filename = $_FILES['foto']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $newname = time() . '_' . $filename;
            $destination = $upload_dir . $newname;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $destination)) {
                // Hapus foto lama jika ada
                if ($foto && file_exists($upload_dir . $foto)) {
                    @unlink($upload_dir . $foto);
                }
                $foto = $newname;
                $foto_baru = true;
            }
        }
    }

    // Update data
    if ($password !== '') {
        $stmt = $config->prepare('UPDATE tb_user SET nip=?, nama_lengkap=?, username=?, email=?, no_hp=?, role=?, status=?, foto=?, password=?, updated_at=? WHERE id_user=?');
        $stmt->bind_param('ssssssssssi', $nip, $nama_lengkap, $username, $email, $no_hp, $role, $status, $foto, $password, $updated_at, $id_user);
    } else {
        $stmt = $config->prepare('UPDATE tb_user SET nip=?, nama_lengkap=?, username=?, email=?, no_hp=?, role=?, status=?, foto=?, updated_at=? WHERE id_user=?');
        $stmt->bind_param('sssssssssi', $nip, $nama_lengkap, $username, $email, $no_hp, $role, $status, $foto, $updated_at, $id_user);
    }
    $success = $stmt->execute();
    $stmt->close();
    if ($success) {
        header('Location:dashboard_admin.php?unit=user&msg=User berhasil diupdate');
        exit;
    } else {
        header('Location:dashboard_admin.php?unit=user&error=Gagal update user');
        exit;
    }
}
?>
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Edit Data User/Staff</h1>
      </div>
    </div>
  </div>
</section>
<section class="content">
  <div class="container-fluid">
    <div class="card card-default">
      <div class="card-header">
        <h3 class="card-title">Form Edit User/Staff</h3>
      </div>
      <form method="post" enctype="multipart/form-data">
        <div class="card-body">
          <div class="form-group">
            <label>NIP</label>
            <input type="text" class="form-control" name="nip" value="<?= htmlspecialchars($user['nip']) ?>" required>
          </div>
          <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" class="form-control" name="nama_lengkap" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
          </div>
          <div class="form-group">
            <label>Username</label>
            <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
          </div>
          <div class="form-group">
            <label>No HP</label>
            <input type="text" class="form-control" name="no_hp" value="<?= htmlspecialchars($user['no_hp']) ?>">
          </div>
          <div class="form-group">
            <label>Role</label>
            <select class="form-control select2bs4" name="role" style="width: 100%;" required>
              <option value="Kepala Ruangan" <?= $user['role'] == 'Kepala Ruangan' ? 'selected' : '' ?>>Kepala Ruangan</option>
              <option value="Staff" <?= $user['role'] == 'Staff' ? 'selected' : '' ?>>Staff</option>
            </select>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select class="form-control select2bs4" name="status" style="width: 100%;" required>
              <option value="aktif" <?= $user['status'] == 'aktif' ? 'selected' : '' ?>>Aktif</option>
              <option value="nonaktif" <?= $user['status'] == 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
            </select>
          </div>
          <div class="form-group">
            <label>Foto</label><br>
            <?php if ($user['foto']): ?>
              <img src="../../../assets/img/<?= htmlspecialchars($user['foto']) ?>" alt="Foto User" style="max-width:100px;max-height:100px;margin-bottom:10px;"><br>
            <?php endif; ?>
            <input type="file" class="form-control" name="foto" accept="image/*">
            <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti foto.</small>
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" class="form-control" name="password" placeholder="Kosongkan jika tidak ingin mengganti password">
          </div>
        </div>
        <div class="card-footer">
          <a class="btn btn-app bg-warning float-left" href="../../dashboard_admin.php?unit=user">
            <i class="fas fa-reply"></i> Back
          </a>
          <button class="btn btn-app bg-success float-right" type="submit">
            <i class="fas fa-save"></i> SAVE
          </button>
        </div>
        <div style="height:70px;"></div>
      </form>
    </div>
  </div>
</section>

