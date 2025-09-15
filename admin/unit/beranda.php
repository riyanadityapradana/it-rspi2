<?php
// Halaman beranda staff
?>
<style>
  .fc .fc-day-today {
    background: #4caf50 !important;
    color: #fff !important;
    border-radius: 6px;
  }
</style>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-tachometer-alt me-2"></i> Dashboard</h2>
    <div class="text-muted">
        <i class="fas fa-calendar me-1"></i>
        <?php echo date('d F Y'); ?>
    </div>
</div>
<div class="row">
  <div class="col-lg-3 col-6">
    <div class="small-box bg-info">
      <div class="inner">
        <?php
        // Hitung jumlah lembur disetujui per bulan
        $bulan = date('m');
        $tahun = date('Y');
        $sqlLembur = "SELECT COUNT(*) as total FROM tb_lembur WHERE status_lembur='Diterima' AND MONTH(tanggal_lembur)='$bulan' AND YEAR(tanggal_lembur)='$tahun'";
        $resultLembur = mysqli_query($config, $sqlLembur);
        $totalLembur = 0;
        if ($resultLembur) {
          $rowLembur = mysqli_fetch_assoc($resultLembur);
          $totalLembur = $rowLembur['total'];
        }
        ?>
        <h3><?= $totalLembur ?></h3>
        <small>Lembur Disetujui Bulan Ini</small>
      </div>
      <div class="icon">
        <i class="fas fa-clock"></i>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="small-box bg-success">
      <div class="inner">
        <?php
        // Hitung jumlah pengajuan disetujui per tahun
          $tahun = date('Y');
          $sqlPengajuan = "SELECT COUNT(*) as total FROM tb_pengajuan WHERE status='disetujui' AND YEAR(tanggal_pengajuan)='$tahun'";
          $resultPengajuan = mysqli_query($config, $sqlPengajuan);
          $totalPengajuan = 0;
          if ($resultPengajuan) {
            $rowPengajuan = mysqli_fetch_assoc($resultPengajuan);
            $totalPengajuan = $rowPengajuan['total'];
          }
        ?>
        <h3><?= $totalPengajuan ?></h3>
        <small>Pengajuan Disetujui Tahun Ini</small>
      </div>
      <div class="icon">
        <i class="fas fa-file-alt fa-2x"></i>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="small-box bg-danger">
      <div class="inner">
        <?php
        // Hitung jumlah  Barang Rusak per tahun
          $tahun = date('Y');
          $sqlBrgRusak = "SELECT COUNT(*) as total FROM tb_barang WHERE kondisi='rusak' AND YEAR(tanggal_terima)='$tahun'";
          $resultBrgRusak = mysqli_query($config, $sqlBrgRusak);
          $totalBrgRusak = 0;
          if ($resultBrgRusak) {
            $rowBrgRusak = mysqli_fetch_assoc($resultBrgRusak);
            $totalBrgRusak = $rowBrgRusak['total'];
          }
        ?>
        <h3><?= $totalBrgRusak ?></h3>
        <small>Barang Rusak Tahun Ini</small>
      </div>
      <div class="icon">
        <i class="fa fa-trash"></i>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="small-box bg-warning">
      <div class="inner">
        <h3>2,000</h3>
        <p>New Members</p>
      </div>
      <div class="icon">
        <i class="fas fa-child"></i>
      </div>
    </div>
  </div>
</div>
<!-- <div class="card"> 
  <div class="card-body">
    <h3>Beranda Admin</h3>
    <p>Ini adalah halamansssSSS beranda admin.</p>
  </div>
</div>-->
<hr>
<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
      <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Aktivitas Pengajuan Baru baru ini</h5>
    </div>
    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
      <div class="list-group list-group-flush">
        <?php
        $sql = "SELECT p.nama_barang, p.status, p.tanggal_pengajuan, u.nama_lengkap as staff_name 
                      FROM tb_pengajuan p 
                      JOIN tb_user u ON p.id_user = u.id_user 
                      ORDER BY p.tanggal_pengajuan DESC LIMIT 10";
        $result = mysqli_query($config, $sql);
        if ($result && mysqli_num_rows($result) > 0):
          while ($row = mysqli_fetch_assoc($result)):
        ?>
        <div class="d-flex justify-content-between align-items-center py-2">
          <div>
            <small class="text-muted"><?php echo htmlspecialchars($row['staff_name']); ?></small><br>
            <strong><?php echo htmlspecialchars($row['nama_barang']); ?></strong>
          </div>
          <span class="badge bg-primary"><?php echo htmlspecialchars(ucwords($row['status'])); ?></span>
        </div>
        <?php 
          endwhile;
        else:
        ?>
        <p class="text-muted">Belum ada aktivitas</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
  </div>
  <div class="col-md-6">
    <div class="card">
      <div class="card-header"><h5 class="card-title">Perbaikan Barang (Service Luar)</h5></div>
      <div class="card-body" style="max-height:400px;overflow-y:auto;">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>Nama Barang</th>
              <th>Tanggal Mulai</th>
              <th>Tanggal Selesai</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $sqlPerbaikan = "SELECT p.*, b.nama_barang FROM tb_perbaikan_barang p JOIN tb_barang b ON p.barang_id = b.barang_id WHERE p.tindakan_perbaikan = 'Service luar' AND p.status = 'diajukan' ORDER BY p.tanggal_lapor DESC LIMIT 10";
            $resultPerbaikan = mysqli_query($config, $sqlPerbaikan);
            if ($resultPerbaikan && mysqli_num_rows($resultPerbaikan) > 0):
              while ($row = mysqli_fetch_assoc($resultPerbaikan)):
            ?>
            <tr>
              <td><?= htmlspecialchars($row['nama_barang']) ?></td>
              <td><?= htmlspecialchars(date('d-m-Y', strtotime($row['tanggal_lapor']))) ?></td>
              <td><?= $row['tanggal_selesai'] ? htmlspecialchars(date('d-m-Y', strtotime($row['tanggal_selesai']))) : '<span class="badge bg-warning">Belum selesai</span>' ?></td>
              <td><span class="badge bg-info"><?= htmlspecialchars(ucwords($row['status'])) ?></span></td>
            </tr>
            <?php
              endwhile;
            else:
            ?>
            <tr><td colspan="4" class="text-center text-muted">Tidak ada data perbaikan barang dengan service luar.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('myChart').getContext('2d');
const myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
        datasets: [{
            label: 'Data Dummy',
            data: [12, 19, 3, 5, 2, 3],
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var calendarEl = document.getElementById('calendar');
  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'id',
    height: 400,
    headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
    selectable: false,
    dateClick: function(info) {
      // Bisa tambahkan aksi jika tanggal diklik
    }
  });
  calendar.render();
});
</script> 