<?php
// Proses verifikasi
if (isset($_GET['verifikasi'])) {
    $id = intval($_GET['verifikasi']);
    $id_pimpinan = $_SESSION['id_user'];
    $update = mysqli_query($config, "UPDATE tb_cuti SET status_lembur='Diterima', id_pimpinan='$id_pimpinan' WHERE id_cuti='$id'");
    if ($update) {
         header('Location: dashboard_admin.php?unit=cuti&msg=Data Cuti staff telah diverifikasi!');
            exit;
    } else {
        header('Location: dashboard_admin.php?unit=cuti&err=Gagal menyimpan data dalam database!');
            exit;
    }
}
// Proses tolak
if (isset($_GET['tolak'])) {
    $id = intval($_GET['tolak']);
    $id_pimpinan = $_SESSION['id_user'];
    $update = mysqli_query($config, "UPDATE tb_cuti SET status_lembur='Ditolak', id_pimpinan='$id_pimpinan' WHERE id_cuti='$id'");
    if ($update) {
         header('Location: dashboard_admin.php?unit=cuti&msg=Data Cuti staff telah diverifikasi!');
            exit;
    } else {
        header('Location: dashboard_admin.php?unit=cuti&err=Gagal menyimpan data dalam database!');
            exit;
    }
}
?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>VERIFIKASI PENGAJUAN CUTI STAFF</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="main_staff.php?unit=beranda">Home</a></li>
          <li class="breadcrumb-item active">Cuti</li>
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
                <div class="card-tools" style="float: right; text-align: right;">
                  <a href="#" class="btn btn-tool btn-sm" data-card-widget="collapse" style="background:rgba(69, 77, 85, 1)">
                        <i class="fas fa-bars"></i>
                  </a>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead style="background:rgb(52, 58, 64, 1); color:white;">
                        <tr>
                            <th style="text-align: center; color: white;">No.</th>
                            <th style="text-align: center; color: white;">Nama</th>
                            <th style="text-align: center; color: white;">Mulai</th>
                            <th style="text-align: center; color: white;">Sampai</th>
                            <th style="text-align: center; color: white;">Hari</th>
                            <th style="text-align: center; color: white;">Jenis Cuti</th>
                            <th style="text-align: center; color: white;">Alasan</th>
                            <th style="text-align: center; color: white;">Status</th>
                            <th style="text-align: center; color: white;">Masuk Tanggal</th>
                            <th style="text-align: center; color: white;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    // Query join tb_cuti dan tb_user
                    $query    = mysqli_query($config,"SELECT a.*, b.nama_lengkap, b.foto FROM tb_cuti a LEFT JOIN tb_user b ON a.id_user = b.id_user ORDER BY a.mulai_tanggal DESC")or die(mysqli_error($config));
                    $n = 1;
                    while ($data=mysqli_fetch_array($query)) {
                        $nn=$n++;
                    ?>
                        <tr>
                            <td align="center"><?= $nn; ?>.</td>
                            <td><?= htmlspecialchars($data['nama_lengkap']) ?></td>
                            <td><?= !empty($data['mulai_tanggal']) ? date('d-m-Y', strtotime($data['mulai_tanggal'])) : '' ?></td>
                            <td><?= !empty($data['sampai_tanggal']) ? date('d-m-Y', strtotime($data['sampai_tanggal'])) : '' ?></td>
                            <td align="center"><?= htmlspecialchars($data['banyak_hari']) ?></td>
                            <td><?= htmlspecialchars(isset($data['jenis_cuti'])?$data['jenis_cuti']:'-') ?></td>
                            <td><?= htmlspecialchars(mb_strimwidth(isset($data['alasan'])?$data['alasan']:'-',0,60,'...')) ?></td>
                            <td align="center">
                                <?php
                                if ($data['status_lembur'] == 'Diterima') {
                                    echo '<span class="badge badge-success">Diterima</span>';
                                } elseif ($data['status_lembur'] == 'Ditolak') {
                                    echo '<span class="badge badge-danger">Ditolak</span>';
                                } elseif ($data['status_lembur'] == 'Menunggu') {
                                    echo '<span class="badge badge-warning" style="color:#212529;">Menunggu</span>';
                                } else {
                                    echo htmlspecialchars($data['status_lembur']);
                                }
                                ?>
                            </td>
                            <td><?= !empty($data['masuk_tanggal']) ? date('d-m-Y', strtotime($data['masuk_tanggal'])) : '' ?></td>
                            <td align="left">
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalDetail<?= $data['id_cuti'] ?>">
                                            <i class="fa fa-eye"></i> Detail
                                    </button>
                                    <?php if ($data['status_lembur'] == 'Menunggu') { ?>
                                            <a href="dashboard_admin.php?unit=cuti&verifikasi=<?= $data['id_cuti'] ?>" class="btn btn-success btn-sm" title="Verifikasi"><i class="fa fa-check"></i> Verifikasi</a>
                                            <a href="dashboard_admin.php?unit=cuti&tolak=<?= $data['id_cuti'] ?>" class="btn btn-danger btn-sm" title="Tolak"><i class="fa fa-times"></i> Tolak</a>
                                    <?php } ?>

                                    <!-- Modal Detail -->
                                    <div class="modal fade" id="modalDetail<?= $data['id_cuti'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel<?= $data['id_cuti'] ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content" style="border-radius:15px;">
                                                <div class="modal-header" style="background:linear-gradient(90deg,#007bff,#17a2b8);color:white;">
                                                    <h5 class="modal-title" id="modalDetailLabel<?= $data['id_cuti'] ?>"><i class="fa fa-info-circle"></i> Detail Cuti</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body" style="background:#f8f9fa;">
                                                    <div class="row">
                                                        <div class="col-md-3 mb-2">
                                                            <?php
                                                                $foto = !empty($data['foto']) ? $data['foto'] : 'default.png';
                                                                $fotoPath = "../assets/img/" . $foto;
                                                            ?>
                                                            <img src="<?= $fotoPath ?>" alt="Foto User" class="img-fluid rounded-circle shadow" style="width:120px;height:120px;object-fit:cover;border:4px solid #007bff;">
                                                            <div class="mt-2"><span style="color:#007bff;font-weight:bold;">Foto</span></div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="row">
                                                                <div class="col-md-6 mb-2">
                                                                    <strong>Nama Staff:</strong><br>
                                                                    <span class="text-primary"><?= htmlspecialchars($data['nama_lengkap']) ?></span>
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <strong>NIP:</strong><br>
                                                                    <span><?= htmlspecialchars($data['nip']) ?></span>
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <strong>Periode Cuti:</strong><br>
                                                                    <span><?= !empty($data['mulai_tanggal']) ? date('d-m-Y', strtotime($data['mulai_tanggal'])) : '' ?> s/d <?= !empty($data['sampai_tanggal']) ? date('d-m-Y', strtotime($data['sampai_tanggal'])) : '' ?></span>
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <strong>Banyak Hari:</strong><br>
                                                                    <span><?= htmlspecialchars($data['banyak_hari']) ?></span>
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <strong>Jenis Cuti:</strong><br>
                                                                    <span><?= htmlspecialchars(isset($data['jenis_cuti'])?$data['jenis_cuti']:'-') ?></span>
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <strong>Alasan:</strong><br>
                                                                    <span><?= nl2br(htmlspecialchars(isset($data['alasan'])?$data['alasan']:'-')) ?></span>
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <strong>Masuk Tanggal:</strong><br>
                                                                    <span><?= !empty($data['masuk_tanggal']) ? date('d-m-Y', strtotime($data['masuk_tanggal'])) : '' ?></span>
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <strong>Status:</strong><br>
                                                                    <?php
                                                                    if ($data['status_lembur'] == 'Diterima') {
                                                                        echo '<span class="badge badge-success">Diterima</span>';
                                                                    } elseif ($data['status_lembur'] == 'Ditolak') {
                                                                        echo '<span class="badge badge-danger">Ditolak</span>';
                                                                    } elseif ($data['status_lembur'] == 'Menunggu') {
                                                                        echo '<span class="badge badge-warning" style="color:#212529;">Menunggu</span>';
                                                                    } else {
                                                                        echo htmlspecialchars($data['status_lembur']);
                                                                    }
                                                                    ?>  
                                                                </div>
                                                            </div>
                                                            <div class="row mt-3">
                                                                <div class="col-12">
                                                                    <strong>Pimpinan:</strong>
                                                                    <div class="card shadow-sm" style="border-radius:10px;">
                                                                        <div class="card-body p-2">
                                                                            <span class="text-muted"><?= htmlspecialchars($data['id_pimpinan']) ?></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer" style="background:#e9ecef;">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </td>
                        </tr>
                    <?php }//end while?>
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
    </div>
  </div>
</section>
