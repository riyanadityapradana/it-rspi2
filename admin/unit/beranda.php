<?php
// Halaman beranda staff
$bulan = date('m');
$tahun = date('Y');

$totalLembur = 0;
$sqlLembur = "SELECT COUNT(*) as total FROM tb_lembur WHERE status_lembur='Menunggu' AND MONTH(tanggal_lembur)='$bulan' AND YEAR(tanggal_lembur)='$tahun'";
$resultLembur = mysqli_query($config, $sqlLembur);
if ($resultLembur) {
  $rowLembur = mysqli_fetch_assoc($resultLembur);
  $totalLembur = $rowLembur['total'];
}

$totalPengajuan = 0;
$sqlPengajuan = "SELECT COUNT(*) as total FROM tb_pengajuan WHERE status='disetujui' AND YEAR(tanggal_pengajuan)='$tahun'";
$resultPengajuan = mysqli_query($config, $sqlPengajuan);
if ($resultPengajuan) {
  $rowPengajuan = mysqli_fetch_assoc($resultPengajuan);
  $totalPengajuan = $rowPengajuan['total'];
}

$totalBrgRusak = 0;
$sqlBrgRusak = "SELECT COUNT(*) as total FROM tb_barang WHERE kondisi='rusak' AND YEAR(tanggal_terima)='$tahun'";
$resultBrgRusak = mysqli_query($config, $sqlBrgRusak);
if ($resultBrgRusak) {
  $rowBrgRusak = mysqli_fetch_assoc($resultBrgRusak);
  $totalBrgRusak = $rowBrgRusak['total'];
}

$totalBarang = 0;
$sqlBarang = "SELECT COUNT(*) as total FROM tb_barang";
$resultBarang = mysqli_query($config, $sqlBarang);
if ($resultBarang) {
  $rowBarang = mysqli_fetch_assoc($resultBarang);
  $totalBarang = $rowBarang['total'];
}

$totalCuti = 0;
$sqlCuti = "SELECT COUNT(*) as total FROM tb_cuti WHERE status_lembur='Menunggu' AND MONTH(mulai_tanggal)='$bulan' AND YEAR(mulai_tanggal)='$tahun'";
$resultCuti = mysqli_query($config, $sqlCuti);
if ($resultCuti) {
  $rowCuti = mysqli_fetch_assoc($resultCuti);
  $totalCuti = $rowCuti['total'];
}

$totalServiceLuar = 0;
$sqlServiceLuar = "SELECT COUNT(*) as total FROM tb_perbaikan_barang WHERE tindakan_perbaikan='Service luar' AND MONTH(tanggal_lapor)='$bulan' AND YEAR(tanggal_lapor)='$tahun'";
$resultServiceLuar = mysqli_query($config, $sqlServiceLuar);
if ($resultServiceLuar) {
  $rowServiceLuar = mysqli_fetch_assoc($resultServiceLuar);
  $totalServiceLuar = $rowServiceLuar['total'];
}

$pendingActions = [
  [
    'label' => 'Pengajuan Barang',
    'icon' => 'fas fa-file-signature',
    'color' => 'warning',
    'link' => 'dashboard_admin.php?unit=pengajuan',
    'total' => 0,
  ],
  [
    'label' => 'Verifikasi Lembur',
    'icon' => 'fas fa-user-clock',
    'color' => 'info',
    'link' => 'dashboard_admin.php?unit=lembur',
    'total' => 0,
  ],
  [
    'label' => 'Verifikasi Cuti',
    'icon' => 'fas fa-calendar-check',
    'color' => 'primary',
    'link' => 'dashboard_admin.php?unit=cuti',
    'total' => 0,
  ],
  [
    'label' => 'Perbaikan Diajukan',
    'icon' => 'fas fa-tools',
    'color' => 'secondary',
    'link' => 'dashboard_admin.php?unit=perbaikan',
    'total' => 0,
  ],
];

$resultPendingPengajuan = mysqli_query($config, "SELECT COUNT(*) as total FROM tb_pengajuan WHERE status='diajukan'");
if ($resultPendingPengajuan) {
  $pendingActions[0]['total'] = (int) mysqli_fetch_assoc($resultPendingPengajuan)['total'];
}

$resultPendingLembur = mysqli_query($config, "SELECT COUNT(*) as total FROM tb_lembur WHERE status_lembur='Menunggu'");
if ($resultPendingLembur) {
  $pendingActions[1]['total'] = (int) mysqli_fetch_assoc($resultPendingLembur)['total'];
}

$resultPendingCuti = mysqli_query($config, "SELECT COUNT(*) as total FROM tb_cuti WHERE status_lembur='Menunggu'");
if ($resultPendingCuti) {
  $pendingActions[2]['total'] = (int) mysqli_fetch_assoc($resultPendingCuti)['total'];
}

$resultPendingPerbaikan = mysqli_query($config, "SELECT COUNT(*) as total FROM tb_perbaikan_barang WHERE status='diajukan'");
if ($resultPendingPerbaikan) {
  $pendingActions[3]['total'] = (int) mysqli_fetch_assoc($resultPendingPerbaikan)['total'];
}

$namaBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
$trenPengajuan = array_fill(0, 12, 0);
$trenLembur = array_fill(0, 12, 0);
$trenCuti = array_fill(0, 12, 0);

$sqlTrenPengajuan = "SELECT MONTH(tanggal_pengajuan) as bulan, COUNT(*) as total FROM tb_pengajuan WHERE YEAR(tanggal_pengajuan)='$tahun' GROUP BY MONTH(tanggal_pengajuan)";
$resultTrenPengajuan = mysqli_query($config, $sqlTrenPengajuan);
if ($resultTrenPengajuan) {
  while ($row = mysqli_fetch_assoc($resultTrenPengajuan)) {
    $index = (int)$row['bulan'] - 1;
    if ($index >= 0 && $index < 12) {
      $trenPengajuan[$index] = (int)$row['total'];
    }
  }
}

$sqlTrenLembur = "SELECT MONTH(tanggal_lembur) as bulan, COUNT(*) as total FROM tb_lembur WHERE YEAR(tanggal_lembur)='$tahun' GROUP BY MONTH(tanggal_lembur)";
$resultTrenLembur = mysqli_query($config, $sqlTrenLembur);
if ($resultTrenLembur) {
  while ($row = mysqli_fetch_assoc($resultTrenLembur)) {
    $index = (int)$row['bulan'] - 1;
    if ($index >= 0 && $index < 12) {
      $trenLembur[$index] = (int)$row['total'];
    }
  }
}

$sqlTrenCuti = "SELECT MONTH(mulai_tanggal) as bulan, COUNT(*) as total FROM tb_cuti WHERE YEAR(mulai_tanggal)='$tahun' GROUP BY MONTH(mulai_tanggal)";
$resultTrenCuti = mysqli_query($config, $sqlTrenCuti);
if ($resultTrenCuti) {
  while ($row = mysqli_fetch_assoc($resultTrenCuti)) {
    $index = (int)$row['bulan'] - 1;
    if ($index >= 0 && $index < 12) {
      $trenCuti[$index] = (int)$row['total'];
    }
  }
}

$kondisiLabels = [];
$kondisiData = [];
$sqlKondisiBarang = "SELECT kondisi, COUNT(*) as total FROM tb_barang GROUP BY kondisi ORDER BY kondisi ASC";
$resultKondisiBarang = mysqli_query($config, $sqlKondisiBarang);
if ($resultKondisiBarang) {
  while ($row = mysqli_fetch_assoc($resultKondisiBarang)) {
    $kondisiLabels[] = ucfirst($row['kondisi'] ? $row['kondisi'] : 'Tidak diketahui');
    $kondisiData[] = (int)$row['total'];
  }
}
?>
<style>
  .fc .fc-day-today {
    background: #4caf50 !important;
    color: #fff !important;
    border-radius: 6px;
  }

  .dashboard-stats-row {
    margin-bottom: 1.5rem;
  }

  .dashboard-box-link {
    display: block;
    color: inherit;
    text-decoration: none;
  }

  .dashboard-box-link:hover {
    color: inherit;
    text-decoration: none;
  }

  .dashboard-box-link .small-box {
    position: relative;
    overflow: hidden;
    min-height: 132px;
    margin-bottom: 0;
    border: 0;
    border-radius: 24px;
    padding: 1.15rem 1.15rem 1rem;
    box-shadow: 0 18px 35px rgba(15, 23, 42, 0.15);
    transition: transform 0.28s ease, box-shadow 0.28s ease, filter 0.28s ease;
    isolation: isolate;
    cursor: pointer;
    animation: statsCardEnter 0.65s ease both;
  }

  .dashboard-stats-row > div:nth-child(1) .small-box { animation-delay: 0.03s; }
  .dashboard-stats-row > div:nth-child(2) .small-box { animation-delay: 0.08s; }
  .dashboard-stats-row > div:nth-child(3) .small-box { animation-delay: 0.13s; }
  .dashboard-stats-row > div:nth-child(4) .small-box { animation-delay: 0.18s; }
  .dashboard-stats-row > div:nth-child(5) .small-box { animation-delay: 0.23s; }
  .dashboard-stats-row > div:nth-child(6) .small-box { animation-delay: 0.28s; }

  .dashboard-box-link .small-box::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.22), rgba(255,255,255,0.02) 55%);
    z-index: -1;
  }

  .dashboard-box-link .small-box::after {
    content: '';
    position: absolute;
    top: -35%;
    right: -18%;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.14);
    filter: blur(2px);
    transition: transform 0.35s ease;
  }

  .dashboard-box-link:hover .small-box {
    transform: translateY(-8px) scale(1.01);
    box-shadow: 0 24px 45px rgba(15, 23, 42, 0.22);
    filter: saturate(1.05);
  }

  .dashboard-box-link:hover .small-box::after {
    transform: scale(1.18) translate(-8px, 12px);
  }

  .dashboard-box-link .small-box .inner {
    position: relative;
    z-index: 2;
  }

  .dashboard-box-link .small-box .inner h3 {
    margin: 0 0 0.45rem;
    font-size: 2rem;
    line-height: 1;
    font-weight: 800;
    letter-spacing: -0.04em;
    color: #ffffff;
  }

  .dashboard-box-link .small-box .inner small {
    display: block;
    max-width: 75%;
    font-size: 0.82rem;
    line-height: 1.45;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.9);
  }

  .dashboard-box-link .small-box .icon {
    position: absolute;
    right: 14px;
    top: 14px;
    width: 58px;
    height: 58px;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.14);
    border: 1px solid rgba(255, 255, 255, 0.18);
    z-index: 2;
    transition: transform 0.28s ease, background 0.28s ease;
  }

  .dashboard-box-link .small-box .icon i {
    position: static;
    font-size: 1.6rem;
    color: rgba(255, 255, 255, 0.92);
  }

  .dashboard-box-link:hover .small-box .icon {
    transform: rotate(-6deg) scale(1.06);
    background: rgba(255, 255, 255, 0.2);
  }

  .dashboard-box-link .bg-info {
    background: linear-gradient(135deg, #0891b2, #0f766e) !important;
  }

  .dashboard-box-link .bg-success {
    background: linear-gradient(135deg, #16a34a, #15803d) !important;
  }

  .dashboard-box-link .bg-danger {
    background: linear-gradient(135deg, #ef4444, #be123c) !important;
  }

  .dashboard-box-link .bg-warning {
    background: linear-gradient(135deg, #f59e0b, #d97706) !important;
  }

  .dashboard-box-link .bg-warning .inner h3,
  .dashboard-box-link .bg-warning .inner small,
  .dashboard-box-link .bg-warning .icon i {
    color: #1f2937;
  }

  .dashboard-box-link .bg-warning .icon {
    background: rgba(255, 255, 255, 0.28);
    border-color: rgba(255, 255, 255, 0.22);
  }

  .dashboard-box-link .bg-primary {
    background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
  }

  .dashboard-box-link .bg-secondary {
    background: linear-gradient(135deg, #6b7280, #374151) !important;
  }

  .equal-card {
    height: 100%;
  }

  .equal-card .card-body {
    min-height: 310px;
  }

  .chart-card-body {
    position: relative;
  }

  .chart-card-body canvas {
    width: 100% !important;
    height: 240px !important;
  }

  .action-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 14px;
    border-radius: 10px;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    text-decoration: none;
    color: #212529;
    transition: all 0.2s ease;
    height: 100%;
  }

  .action-item:hover {
    background: #eef4ff;
    border-color: #b8d0ff;
    color: #212529;
    text-decoration: none;
  }

  .action-left {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .action-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
  }

  .action-badge {
    font-size: 1rem;
    min-width: 36px;
  }

  @keyframes statsCardEnter {
    from {
      opacity: 0;
      transform: translateY(18px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @media (max-width: 767.98px) {
    .dashboard-box-link .small-box {
      min-height: 118px;
      border-radius: 20px;
    }

    .dashboard-box-link .small-box .inner h3 {
      font-size: 1.7rem;
    }

    .dashboard-box-link .small-box .inner small {
      max-width: 68%;
      font-size: 0.76rem;
    }

    .dashboard-box-link .small-box .icon {
      width: 50px;
      height: 50px;
      border-radius: 16px;
    }
  }
</style>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-tachometer-alt me-2"></i> Dashboard</h2>
    <div class="text-muted">
        <i class="fas fa-calendar me-1"></i>
        <?php echo date('d F Y'); ?>
    </div>
</div>
<div class="row dashboard-stats-row">
  <div class="col-lg-2 col-md-4 col-6">
    <a href="dashboard_admin.php?unit=lembur" class="dashboard-box-link">
      <div class="small-box bg-info">
        <div class="inner">
          <h3><?= $totalLembur ?></h3>
          <small>Lembur Menunggu Bulan Ini</small>
        </div>
        <div class="icon">
          <i class="fas fa-clock"></i>
        </div>
      </div>
    </a>
  </div>
  <div class="col-lg-2 col-md-4 col-6">
    <a href="dashboard_admin.php?unit=pengajuan" class="dashboard-box-link">
      <div class="small-box bg-success">
        <div class="inner">
          <h3><?= $totalPengajuan ?></h3>
          <small>Pengajuan Disetujui Tahun Ini</small>
        </div>
        <div class="icon">
          <i class="fas fa-file-alt fa-2x"></i>
        </div>
      </div>
    </a>
  </div>
  <div class="col-lg-2 col-md-4 col-6">
    <a href="dashboard_admin.php?unit=barang_rusak" class="dashboard-box-link">
      <div class="small-box bg-danger">
        <div class="inner">
          <h3><?= $totalBrgRusak ?></h3>
          <small>Barang Rusak Tahun Ini</small>
        </div>
        <div class="icon">
          <i class="fa fa-trash"></i>
        </div>
      </div>
    </a>
  </div>
  <div class="col-lg-2 col-md-4 col-6">
    <a href="dashboard_admin.php?unit=barang" class="dashboard-box-link">
      <div class="small-box bg-warning">
        <div class="inner">
          <h3><?= $totalBarang ?></h3>
          <small>Total Data Barang</small>
        </div>
        <div class="icon">
          <i class="fas fa-boxes"></i>
        </div>
      </div>
    </a>
  </div>
  <div class="col-lg-2 col-md-4 col-6">
    <a href="dashboard_admin.php?unit=cuti" class="dashboard-box-link">
      <div class="small-box bg-primary">
        <div class="inner">
          <h3><?= $totalCuti ?></h3>
          <small>Cuti Menunggu Bulan Ini</small>
        </div>
        <div class="icon">
          <i class="fas fa-calendar-check"></i>
        </div>
      </div>
    </a>
  </div>
  <div class="col-lg-2 col-md-4 col-6">
    <a href="dashboard_admin.php?unit=perbaikan" class="dashboard-box-link">
      <div class="small-box bg-secondary">
        <div class="inner">
          <h3><?= $totalServiceLuar ?></h3>
          <small>Service Luar Bulan Ini</small>
        </div>
        <div class="icon">
          <i class="fas fa-tools"></i>
        </div>
      </div>
    </a>
  </div>
</div>
<hr>
<div class="row mb-3">
  <div class="col-lg-8 d-flex">
    <div class="card equal-card w-100">
      <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Tren Aktivitas Tahun <?= $tahun ?></h5>
      </div>
      <div class="card-body chart-card-body">
        <canvas id="activityTrendChart"></canvas>
      </div>
    </div>
  </div>
  <div class="col-lg-4 d-flex">
    <div class="card equal-card w-100">
      <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Kondisi Barang</h5>
      </div>
      <div class="card-body chart-card-body">
        <canvas id="barangConditionChart"></canvas>
      </div>
    </div>
  </div>
</div>
<div class="row mb-3">
  <div class="col-lg-12">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-bell me-2"></i>Butuh Tindakan</h5>
      </div>
      <div class="card-body">
        <div class="row dashboard-stats-row">
          <?php foreach ($pendingActions as $item): ?>
            <?php $badgeClass = ((int) $item['total'] > 0) ? 'badge-danger' : 'badge-secondary'; ?>
            <div class="col-lg-3 col-md-6 mb-3 d-flex">
              <a href="<?= htmlspecialchars($item['link']) ?>" class="action-item w-100">
                <div class="action-left">
                  <div class="action-icon bg-<?= htmlspecialchars($item['color']) ?>">
                    <i class="<?= htmlspecialchars($item['icon']) ?>"></i>
                  </div>
                  <div>
                    <div class="font-weight-bold"><?= htmlspecialchars($item['label']) ?></div>
                    <small class="text-muted">Data yang masih perlu diproses</small>
                  </div>
                </div>
                <span class="badge <?= $badgeClass ?> action-badge"><?= (int) $item['total'] ?></span>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row dashboard-stats-row">
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
const bulanLabels = <?php echo json_encode($namaBulan); ?>;
const trenPengajuan = <?php echo json_encode($trenPengajuan); ?>;
const trenLembur = <?php echo json_encode($trenLembur); ?>;
const trenCuti = <?php echo json_encode($trenCuti); ?>;
const kondisiLabels = <?php echo json_encode($kondisiLabels); ?>;
const kondisiData = <?php echo json_encode($kondisiData); ?>;

const activityTrendCtx = document.getElementById('activityTrendChart');
if (activityTrendCtx) {
  new Chart(activityTrendCtx, {
    type: 'line',
    data: {
      labels: bulanLabels,
      datasets: [
        {
          label: 'Pengajuan',
          data: trenPengajuan,
          borderColor: '#28a745',
          backgroundColor: 'rgba(40, 167, 69, 0.12)',
          tension: 0.35,
          fill: true,
          borderWidth: 3
        },
        {
          label: 'Lembur',
          data: trenLembur,
          borderColor: '#17a2b8',
          backgroundColor: 'rgba(23, 162, 184, 0.08)',
          tension: 0.35,
          fill: true,
          borderWidth: 3
        },
        {
          label: 'Cuti',
          data: trenCuti,
          borderColor: '#ffc107',
          backgroundColor: 'rgba(255, 193, 7, 0.08)',
          tension: 0.35,
          fill: true,
          borderWidth: 3
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top'
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            precision: 0
          }
        }
      }
    }
  });
}

const barangConditionCtx = document.getElementById('barangConditionChart');
if (barangConditionCtx) {
  new Chart(barangConditionCtx, {
    type: 'doughnut',
    data: {
      labels: kondisiLabels,
      datasets: [{
        data: kondisiData,
        backgroundColor: ['#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6f42c1', '#fd7e14'],
        borderWidth: 2,
        borderColor: '#ffffff'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom'
        }
      }
    }
  });
}
</script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var calendarEl = document.getElementById('calendar');
  if (!calendarEl) {
    return;
  }

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

