<?php
require_once '../config/koneksi.php';

// Tampilkan pesan sukses/gagal jika ada
// (Dihilangkan, sekarang pakai toastr di dashboard_admin.php)

// Proses verifikasi/ACC user
if (isset($_GET['aksi']) && $_GET['aksi'] == 'acc' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // Pastikan hanya user nonaktif yang bisa di-ACC
    $cek = $config->prepare('SELECT status FROM tb_user WHERE id_user = ?');
    $cek->bind_param('i', $id);
    $cek->execute();
    $cek->bind_result($status);
    $cek->fetch();
    $cek->close();
    if ($status === 'nonaktif') {
        $stmt = $config->prepare('UPDATE tb_user SET status = "aktif" WHERE id_user = ?');
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            header('Location: dashboard_admin.php?unit=user&msg=User berhasil diverifikasi!');
            exit;
        } else {
            header('Location: dashboard_admin.php?unit=user&err=Gagal verifikasi user!');
            exit;
        }
        $stmt->close();
    } else {
        header('Location: dashboard_admin.php?unit=user&err=User sudah aktif atau tidak ditemukan!');
        exit;
    }
}

// Proses hapus user
if (isset($_GET['hapus']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $config->prepare('DELETE FROM tb_user WHERE id_user = ?');
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        header('Location: ?msg=User berhasil dihapus!');
        exit;
    } else {
        header('Location: ?err=Gagal menghapus user!');
        exit;
    }
    $stmt->close();
}

// Ambil data user
$result = $config->query('SELECT * FROM tb_user ORDER BY id_user DESC');
?>
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>DATA USER</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="main_staff.php?unit=beranda">Home</a></li>
          <li class="breadcrumb-item active">User</li>
        </ol>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
              <div class="card-title">Data User/Staff</div> 
              <div>
                <a href="#" class="btn btn-tool btn-sm" data-card-widget="collapse" style="background:rgba(69, 77, 85, 1)">
                  <i class="fas fa-bars"></i>
                </a>
              </div>
            </div>
          </div>
          <div class="card">
          <div class="card-body">
            <table id="example1" class="table table-bordered table-striped">
              <thead style="background:rgb(52, 58, 64, 1); color:white;">
                <tr>
                  <th>No</th>
                  <th>NIP</th>
                  <th>Nama Lengkap</th>
                  <th>Username</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php $no=1; while($row = $result->fetch_assoc()): ?>
                  <?php $idd = $row['id_user']; ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td><?= htmlspecialchars($row['nip']) ?></td>
                  <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                  <td><?= htmlspecialchars($row['username']) ?></td>
                  <td><?= htmlspecialchars($row['email']) ?></td>
                  <td><?= htmlspecialchars($row['role']) ?></td>
                  <td>
                    <?php if($row['status'] == 'aktif'): ?>
                      <span class="badge badge-success">Aktif</span>
                    <?php else: ?>
                      <span class="badge badge-warning">Nonaktif</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <a href="dashboard_admin.php?unit=edit_user&id=<?=$idd;?>" class="btn btn-sm btn-info">Edit</a>
                    <a href="dashboard_admin.php?unit=delete_user&id=<?=$idd;?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</a>
                    <?php if($row['status'] == 'nonaktif'): ?>
                      <a href="dashboard_admin.php?unit=user&id=<?= $row['id_user'] ?>&aksi=acc" class="btn btn-sm btn-success">ACC</a>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
          </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
// Proses edit user (form edit akan dibuat terpisah di edit_user.php)
if (isset($_GET['edit']) && isset($_GET['id'])) {
    include 'edit_user.php';
}
?> 