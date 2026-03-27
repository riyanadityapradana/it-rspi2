<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$bulanSekarang = date('m');
$tahunSekarang = date('Y');
$userId = isset($_SESSION['id_user']) ? mysqli_real_escape_string($config, (string) $_SESSION['id_user']) : '';
$namaUser = isset($_SESSION['nama_lengkap']) && $_SESSION['nama_lengkap'] !== ''
    ? $_SESSION['nama_lengkap']
    : (isset($_SESSION['username']) ? $_SESSION['username'] : 'Staff IT');

$totalLembur = 0;
$totalPengajuan = 0;
$totalBrgRusak = 0;
$totalLogs = 0;
$myLembur = [
    'Menunggu' => 0,
    'Diterima' => 0,
    'Ditolak' => 0,
];
$myCuti = [
    'Menunggu' => 0,
    'Diterima' => 0,
    'Ditolak' => 0,
];
$recentPengajuan = [];
$serviceLuarList = [];
$barangChart = [
    'Baru' => 0,
    'Bekas' => 0,
    'Rusak' => 0,
    'Dalam Perbaikan' => 0,
];

$sqlLembur = "SELECT COUNT(*) AS total FROM tb_lembur WHERE status_lembur='Diterima' AND MONTH(tanggal_lembur)='$bulanSekarang' AND YEAR(tanggal_lembur)='$tahunSekarang'";
$resultLembur = mysqli_query($config, $sqlLembur);
if ($resultLembur) {
    $rowLembur = mysqli_fetch_assoc($resultLembur);
    $totalLembur = isset($rowLembur['total']) ? (int) $rowLembur['total'] : 0;
}

$sqlPengajuan = "SELECT COUNT(*) AS total FROM tb_pengajuan WHERE status='disetujui' AND YEAR(tanggal_pengajuan)='$tahunSekarang'";
$resultPengajuan = mysqli_query($config, $sqlPengajuan);
if ($resultPengajuan) {
    $rowPengajuan = mysqli_fetch_assoc($resultPengajuan);
    $totalPengajuan = isset($rowPengajuan['total']) ? (int) $rowPengajuan['total'] : 0;
}

$sqlBrgRusak = "SELECT COUNT(*) AS total FROM tb_barang WHERE LOWER(kondisi)='rusak' AND YEAR(tanggal_terima)='$tahunSekarang'";
$resultBrgRusak = mysqli_query($config, $sqlBrgRusak);
if ($resultBrgRusak) {
    $rowBrgRusak = mysqli_fetch_assoc($resultBrgRusak);
    $totalBrgRusak = isset($rowBrgRusak['total']) ? (int) $rowBrgRusak['total'] : 0;
}

if ($userId !== '') {
    $sqlLogs = "SELECT COUNT(*) AS total FROM tb_logbook WHERE id_user='$userId' AND MONTH(tanggal_log)='$bulanSekarang' AND YEAR(tanggal_log)='$tahunSekarang'";
    $resLogs = mysqli_query($config, $sqlLogs);
    if ($resLogs) {
        $rowLogs = mysqli_fetch_assoc($resLogs);
        $totalLogs = isset($rowLogs['total']) ? (int) $rowLogs['total'] : 0;
    }

    $sqlMyLembur = "SELECT status_lembur, COUNT(*) AS total FROM tb_lembur WHERE id_staff='$userId' AND YEAR(tanggal_lembur)='$tahunSekarang' GROUP BY status_lembur";
    $resMyLembur = mysqli_query($config, $sqlMyLembur);
    if ($resMyLembur) {
        while ($row = mysqli_fetch_assoc($resMyLembur)) {
            $status = isset($row['status_lembur']) ? trim($row['status_lembur']) : '';
            if (array_key_exists($status, $myLembur)) {
                $myLembur[$status] = (int) $row['total'];
            }
        }
    }

    $sqlMyCuti = "SELECT status_lembur, COUNT(*) AS total FROM tb_cuti WHERE id_user='$userId' AND YEAR(mulai_tanggal)='$tahunSekarang' GROUP BY status_lembur";
    $resMyCuti = mysqli_query($config, $sqlMyCuti);
    if ($resMyCuti) {
        while ($row = mysqli_fetch_assoc($resMyCuti)) {
            $status = isset($row['status_lembur']) ? trim($row['status_lembur']) : '';
            if (array_key_exists($status, $myCuti)) {
                $myCuti[$status] = (int) $row['total'];
            }
        }
    }
}

$sqlPengajuanTerbaru = "SELECT p.nama_barang, p.status, p.tanggal_pengajuan, u.nama_lengkap AS staff_name
    FROM tb_pengajuan p
    JOIN tb_user u ON p.id_user = u.id_user
    ORDER BY p.tanggal_pengajuan DESC LIMIT 6";
$resultPengajuanTerbaru = mysqli_query($config, $sqlPengajuanTerbaru);
if ($resultPengajuanTerbaru) {
    while ($row = mysqli_fetch_assoc($resultPengajuanTerbaru)) {
        $recentPengajuan[] = $row;
    }
}

$sqlPerbaikan = "SELECT p.*, b.nama_barang FROM tb_perbaikan_barang p
    JOIN tb_barang b ON p.barang_id = b.barang_id
    WHERE p.tindakan_perbaikan = 'Service luar' AND p.status = 'proses'
    ORDER BY p.tanggal_lapor DESC LIMIT 6";
$resultPerbaikan = mysqli_query($config, $sqlPerbaikan);
if ($resultPerbaikan) {
    while ($row = mysqli_fetch_assoc($resultPerbaikan)) {
        $serviceLuarList[] = $row;
    }
}

$sqlBarangChart = "SELECT kondisi, COUNT(*) AS total FROM tb_barang WHERE YEAR(tanggal_terima)='$tahunSekarang' GROUP BY kondisi";
$resultBarangChart = mysqli_query($config, $sqlBarangChart);
if ($resultBarangChart) {
    while ($row = mysqli_fetch_assoc($resultBarangChart)) {
        $kondisi = isset($row['kondisi']) ? strtolower(trim($row['kondisi'])) : '';
        if ($kondisi === 'baru') {
            $barangChart['Baru'] = (int) $row['total'];
        } elseif ($kondisi === 'bekas') {
            $barangChart['Bekas'] = (int) $row['total'];
        } elseif ($kondisi === 'rusak') {
            $barangChart['Rusak'] = (int) $row['total'];
        } elseif ($kondisi === 'dalam perbaikan') {
            $barangChart['Dalam Perbaikan'] = (int) $row['total'];
        }
    }
}

$totalPengajuanTerbaru = count($recentPengajuan);
$totalServiceLuar = count($serviceLuarList);
$persenLogbook = min(100, $totalLogs * 10);
?>
<style>
  .beranda-shell {
    --brand: #7d1212;
    --brand-dark: #581010;
    --accent: #d8a14a;
    --ink: #1f2937;
    --muted: #6b7280;
    --card-border: rgba(125, 18, 18, 0.08);
  }

  .beranda-shell .dashboard-hero {
    background: linear-gradient(135deg, #7d1212 0%, #9e2a2b 55%, #d8a14a 100%);
    border-radius: 22px;
    padding: 26px 28px;
    color: #fff;
    box-shadow: 0 18px 40px rgba(125, 18, 18, 0.18);
    margin-bottom: 20px;
  }

  .beranda-shell .hero-title {
    font-size: 28px;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 8px;
  }

  .beranda-shell .hero-text {
    max-width: 680px;
    color: rgba(255, 255, 255, 0.88);
    margin-bottom: 0;
  }

  .beranda-shell .hero-pill-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: flex-end;
  }

  .beranda-shell .hero-pill {
    min-width: 150px;
    background: rgba(255, 255, 255, 0.14);
    backdrop-filter: blur(2px);
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 16px;
    padding: 12px 14px;
  }

  .beranda-shell .hero-pill strong {
    display: block;
    font-size: 18px;
    line-height: 1.1;
  }

  .beranda-shell .hero-pill span {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.78);
    text-transform: uppercase;
    letter-spacing: 0.04em;
  }

  .beranda-shell .metric-card,
  .beranda-shell .status-card,
  .beranda-shell .quick-card,
  .beranda-shell .dashboard-card {
    border: 1px solid var(--card-border);
    border-radius: 18px;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.05);
    height: 100%;
  }

  .beranda-shell .metric-card {
    overflow: hidden;
  }

  .beranda-shell .metric-card .card-body {
    padding: 18px;
  }

  .beranda-shell .metric-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
  }

  .beranda-shell .metric-label {
    color: var(--muted);
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 8px;
  }

  .beranda-shell .metric-value {
    color: var(--ink);
    font-size: 30px;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 8px;
  }

  .beranda-shell .metric-meta {
    font-size: 13px;
    color: var(--muted);
    margin-bottom: 0;
  }

  .beranda-shell .metric-icon {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: #fff;
  }

  .beranda-shell .metric-info { background: linear-gradient(135deg, #2d8cf0, #4ea8ff); }
  .beranda-shell .metric-success { background: linear-gradient(135deg, #1f9d73, #34c38f); }
  .beranda-shell .metric-danger { background: linear-gradient(135deg, #ca3f58, #ef6b81); }
  .beranda-shell .metric-warning { background: linear-gradient(135deg, #d48a14, #f5b94c); }

  .beranda-shell .section-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--ink);
    margin-bottom: 4px;
  }

  .beranda-shell .section-subtitle {
    color: var(--muted);
    font-size: 13px;
    margin-bottom: 0;
  }

  .beranda-shell .dashboard-card .card-header,
  .beranda-shell .status-card .card-header,
  .beranda-shell .quick-card .card-header {
    background: #fff;
    border-bottom: 1px solid rgba(15, 23, 42, 0.06);
    border-top-left-radius: 18px;
    border-top-right-radius: 18px;
    padding: 18px 20px 14px;
  }

  .beranda-shell .dashboard-card .card-body,
  .beranda-shell .status-card .card-body,
  .beranda-shell .quick-card .card-body {
    padding: 20px;
  }

  .beranda-shell .status-summary {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
    margin-bottom: 16px;
  }

  .beranda-shell .status-chip {
    border-radius: 14px;
    padding: 12px 10px;
    text-align: center;
    background: #f8fafc;
  }

  .beranda-shell .status-chip strong {
    display: block;
    font-size: 22px;
    line-height: 1;
    margin-bottom: 4px;
    color: var(--ink);
  }

  .beranda-shell .status-chip span {
    font-size: 12px;
    color: var(--muted);
  }

  .beranda-shell .status-chip.pending {
    background: rgba(245, 185, 76, 0.16);
  }

  .beranda-shell .status-chip.accepted {
    background: rgba(52, 195, 143, 0.16);
  }

  .beranda-shell .status-chip.rejected {
    background: rgba(239, 107, 129, 0.14);
  }

  .beranda-shell .status-note {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    border-radius: 14px;
    background: #fff8ec;
    padding: 12px 14px;
    color: #8a5a0a;
    font-size: 13px;
  }

  .beranda-shell .quick-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
  }

  .beranda-shell .quick-link {
    display: flex;
    align-items: center;
    gap: 12px;
    border-radius: 16px;
    padding: 14px;
    border: 1px solid rgba(125, 18, 18, 0.08);
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    color: var(--ink);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .beranda-shell .quick-link:hover {
    color: var(--brand);
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 10px 22px rgba(15, 23, 42, 0.08);
  }

  .beranda-shell .quick-link i {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    background: linear-gradient(135deg, var(--brand), #b63a3a);
  }

  .beranda-shell .quick-link span {
    display: block;
    font-weight: 600;
  }

  .beranda-shell .quick-link small {
    display: block;
    color: var(--muted);
    font-size: 12px;
  }

  .beranda-shell .list-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    border-bottom: 1px dashed rgba(15, 23, 42, 0.08);
    padding: 12px 0;
  }

  .beranda-shell .list-item:last-child {
    border-bottom: 0;
    padding-bottom: 0;
  }

  .beranda-shell .list-item:first-child {
    padding-top: 0;
  }

  .beranda-shell .list-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--ink);
    margin-bottom: 3px;
  }

  .beranda-shell .list-meta {
    color: var(--muted);
    font-size: 12px;
    margin-bottom: 0;
  }

  .beranda-shell .soft-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    white-space: nowrap;
  }

  .beranda-shell .soft-badge.pending {
    background: rgba(245, 185, 76, 0.18);
    color: #9a6700;
  }

  .beranda-shell .soft-badge.accepted {
    background: rgba(52, 195, 143, 0.18);
    color: #117a54;
  }

  .beranda-shell .soft-badge.rejected {
    background: rgba(239, 107, 129, 0.18);
    color: #a52742;
  }

  .beranda-shell .soft-badge.info {
    background: rgba(45, 140, 240, 0.14);
    color: #1663b7;
  }

  .beranda-shell .chart-wrap {
    position: relative;
    height: 310px;
  }

  .beranda-shell .service-table th {
    font-size: 12px;
    color: var(--muted);
    border-top: 0;
    border-bottom: 1px solid rgba(15, 23, 42, 0.08);
  }

  .beranda-shell .service-table td {
    font-size: 13px;
    vertical-align: middle;
    border-top: 1px solid rgba(15, 23, 42, 0.06);
  }

  .beranda-shell .progress {
    height: 10px;
    border-radius: 999px;
    background: #f1f5f9;
  }

  .beranda-shell .progress-bar {
    background: linear-gradient(90deg, var(--brand), #d8a14a);
  }

  @media (max-width: 991.98px) {
    .beranda-shell .hero-pill-wrap {
      justify-content: flex-start;
      margin-top: 16px;
    }
  }

  @media (max-width: 767.98px) {
    .beranda-shell .dashboard-hero {
      padding: 22px 20px;
    }

    .beranda-shell .hero-title {
      font-size: 23px;
    }

    .beranda-shell .status-summary,
    .beranda-shell .quick-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="beranda-shell">
  <div class="dashboard-hero">
    <div class="row align-items-center">
      <div class="col-lg-8">
        <div class="hero-title">Halo, <?= htmlspecialchars($namaUser) ?>.</div>
        <p class="hero-text">Beranda staff kini menampilkan ringkasan kerja pribadi, shortcut aksi cepat, dan kondisi barang tahun <?= htmlspecialchars($tahunSekarang) ?> supaya aktivitas harian lebih mudah dipantau dalam satu layar.</p>
      </div>
      <div class="col-lg-4">
        <div class="hero-pill-wrap">
          <div class="hero-pill">
            <span>Tanggal</span>
            <strong><?= htmlspecialchars(date('d M Y')) ?></strong>
          </div>
          <div class="hero-pill">
            <span>Logbook Bulan Ini</span>
            <strong><?= $totalLogs ?> Aktivitas</strong>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-3 col-sm-6 mb-3">
      <div class="card metric-card">
        <div class="card-body">
          <div class="metric-top">
            <div>
              <div class="metric-label">Lembur Disetujui</div>
              <div class="metric-value"><?= $totalLembur ?></div>
              <p class="metric-meta">Akumulasi lembur diterima pada bulan ini.</p>
            </div>
            <div class="metric-icon metric-info"><i class="fas fa-business-time"></i></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-3">
      <div class="card metric-card">
        <div class="card-body">
          <div class="metric-top">
            <div>
              <div class="metric-label">Pengajuan Disetujui</div>
              <div class="metric-value"><?= $totalPengajuan ?></div>
              <p class="metric-meta">Total pengajuan barang disetujui tahun ini.</p>
            </div>
            <div class="metric-icon metric-success"><i class="fas fa-file-signature"></i></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-3">
      <div class="card metric-card">
        <div class="card-body">
          <div class="metric-top">
            <div>
              <div class="metric-label">Barang Rusak</div>
              <div class="metric-value"><?= $totalBrgRusak ?></div>
              <p class="metric-meta">Barang berstatus rusak yang tahun penerimaannya tercatat pada <?= htmlspecialchars($tahunSekarang) ?>.</p>
            </div>
            <div class="metric-icon metric-danger"><i class="fas fa-tools"></i></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-3">
      <div class="card metric-card">
        <div class="card-body">
          <div class="metric-top">
            <div>
              <div class="metric-label">Aktivitas Saya</div>
              <div class="metric-value"><?= $totalLogs ?></div>
              <p class="metric-meta">Catatan logbook yang sudah kamu isi bulan ini.</p>
            </div>
            <div class="metric-icon metric-warning"><i class="fas fa-clipboard-list"></i></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-4 mb-3">
      <div class="card status-card">
        <div class="card-header">
          <div class="section-title">Status Lembur Saya</div>
          <p class="section-subtitle">Ringkasan pengajuan lembur pribadi tahun <?= htmlspecialchars($tahunSekarang) ?>.</p>
        </div>
        <div class="card-body">
          <div class="status-summary">
            <div class="status-chip pending">
              <strong><?= $myLembur['Menunggu'] ?></strong>
              <span>Menunggu</span>
            </div>
            <div class="status-chip accepted">
              <strong><?= $myLembur['Diterima'] ?></strong>
              <span>Diterima</span>
            </div>
            <div class="status-chip rejected">
              <strong><?= $myLembur['Ditolak'] ?></strong>
              <span>Ditolak</span>
            </div>
          </div>
          <div class="status-note">
            <span><i class="fas fa-info-circle mr-1"></i> Pantau pengajuan sebelum cetak dokumen lembur.</span>
            <a href="dashboard_staff.php?unit=lembur" class="btn btn-sm btn-outline-warning">Buka</a>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 mb-3">
      <div class="card status-card">
        <div class="card-header">
          <div class="section-title">Status Cuti Saya</div>
          <p class="section-subtitle">Monitor persetujuan cuti pribadi tanpa perlu buka tabel utama.</p>
        </div>
        <div class="card-body">
          <div class="status-summary">
            <div class="status-chip pending">
              <strong><?= $myCuti['Menunggu'] ?></strong>
              <span>Menunggu</span>
            </div>
            <div class="status-chip accepted">
              <strong><?= $myCuti['Diterima'] ?></strong>
              <span>Diterima</span>
            </div>
            <div class="status-chip rejected">
              <strong><?= $myCuti['Ditolak'] ?></strong>
              <span>Ditolak</span>
            </div>
          </div>
          <div class="status-note">
            <span><i class="fas fa-plane-departure mr-1"></i> Cek kembali tanggal mulai, kembali, dan status approval.</span>
            <a href="dashboard_staff.php?unit=cuti" class="btn btn-sm btn-outline-warning">Buka</a>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 mb-3">
      <div class="card quick-card">
        <div class="card-header">
          <div class="section-title">Shortcut Aksi Cepat</div>
          <p class="section-subtitle">Akses langsung ke menu yang paling sering dipakai staff.</p>
        </div>
        <div class="card-body">
          <div class="quick-grid">
            <a href="dashboard_staff.php?unit=create_logbook" class="quick-link">
              <i class="fas fa-book-medical"></i>
              <div>
                <span>Input Logbook</span>
                <small>Catat aktivitas harian</small>
              </div>
            </a>
            <a href="dashboard_staff.php?unit=create_lembur" class="quick-link">
              <i class="fas fa-user-clock"></i>
              <div>
                <span>Ajukan Lembur</span>
                <small>Buat lembur baru</small>
              </div>
            </a>
            <a href="dashboard_staff.php?unit=create_cuti" class="quick-link">
              <i class="fas fa-calendar-plus"></i>
              <div>
                <span>Ajukan Cuti</span>
                <small>Isi form cuti</small>
              </div>
            </a>
            <a href="dashboard_staff.php?unit=create_pengajuan" class="quick-link">
              <i class="fas fa-box-open"></i>
              <div>
                <span>Pengajuan Barang</span>
                <small>Buat permintaan baru</small>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-8 mb-3">
      <div class="card dashboard-card">
        <div class="card-header d-flex justify-content-between align-items-start">
          <div>
            <div class="section-title">Grafik Kondisi Barang Per Tahun</div>
            <p class="section-subtitle">Distribusi kondisi barang saat ini untuk barang yang diterima pada tahun <?= htmlspecialchars($tahunSekarang) ?>.</p>
          </div>
          <span class="soft-badge info">Tahun <?= htmlspecialchars($tahunSekarang) ?></span>
        </div>
        <div class="card-body">
          <div class="chart-wrap">
            <canvas id="barangConditionChart"></canvas>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 mb-3">
      <div class="card dashboard-card">
        <div class="card-header">
          <div class="section-title">Performa Logbook</div>
          <p class="section-subtitle">Target sederhana supaya aktivitas bulanan tetap terisi konsisten.</p>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <strong style="font-size:32px;color:#1f2937;"><?= $persenLogbook ?>%</strong>
            <span class="soft-badge info"><?= $totalLogs ?> entri</span>
          </div>
          <div class="progress mb-3">
            <div class="progress-bar" role="progressbar" style="width: <?= $persenLogbook ?>%" aria-valuenow="<?= $persenLogbook ?>" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
          <p class="section-subtitle mb-3">Perhitungan ini memakai target ringan 10 aktivitas per bulan agar progres cepat terbaca dari dashboard.</p>
          <div class="list-item">
            <div>
              <div class="list-title">Pengajuan terbaru</div>
              <p class="list-meta">Item yang masuk ke dashboard hari ini.</p>
            </div>
            <span class="soft-badge info"><?= $totalPengajuanTerbaru ?> item</span>
          </div>
          <div class="list-item">
            <div>
              <div class="list-title">Service luar aktif</div>
              <p class="list-meta">Perbaikan yang masih berjalan.</p>
            </div>
            <span class="soft-badge pending"><?= $totalServiceLuar ?> proses</span>
          </div>
          <div class="list-item">
            <div>
              <div class="list-title">Barang rusak tahun ini</div>
              <p class="list-meta">Mengacu pada barang rusak dengan tahun penerimaan <?= htmlspecialchars($tahunSekarang) ?>.</p>
            </div>
            <span class="soft-badge rejected"><?= $totalBrgRusak ?> unit</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-5 mb-3">
      <div class="card dashboard-card">
        <div class="card-header">
          <div class="section-title">Aktivitas Pengajuan Terbaru</div>
          <p class="section-subtitle">Enam pengajuan terakhir yang masuk dari staff.</p>
        </div>
        <div class="card-body">
          <?php if (!empty($recentPengajuan)): ?>
            <?php foreach ($recentPengajuan as $item): ?>
              <?php
              $statusPengajuan = strtolower(isset($item['status']) ? $item['status'] : '');
              $badgeClass = 'info';
              if ($statusPengajuan === 'disetujui') {
                  $badgeClass = 'accepted';
              } elseif ($statusPengajuan === 'ditolak') {
                  $badgeClass = 'rejected';
              } elseif ($statusPengajuan === 'pending' || $statusPengajuan === 'menunggu') {
                  $badgeClass = 'pending';
              }
              ?>
              <div class="list-item">
                <div>
                  <div class="list-title"><?= htmlspecialchars($item['nama_barang']) ?></div>
                  <p class="list-meta"><?= htmlspecialchars($item['staff_name']) ?> � <?= htmlspecialchars(date('d-m-Y', strtotime($item['tanggal_pengajuan']))) ?></p>
                </div>
                <span class="soft-badge <?= $badgeClass ?>"><?= htmlspecialchars(ucwords($item['status'])) ?></span>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="section-subtitle mb-0">Belum ada aktivitas pengajuan yang bisa ditampilkan.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div class="col-lg-7 mb-3">
      <div class="card dashboard-card">
        <div class="card-header d-flex justify-content-between align-items-start">
          <div>
            <div class="section-title">Perbaikan Barang Service Luar</div>
            <p class="section-subtitle">Daftar pekerjaan service luar yang masih berstatus proses.</p>
          </div>
          <a href="dashboard_staff.php?unit=perbaikan" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
        </div>
        <div class="card-body table-responsive p-0">
          <table class="table service-table mb-0">
            <thead>
              <tr>
                <th class="pl-3">Nama Barang</th>
                <th>Tanggal Lapor</th>
                <th>Tanggal Selesai</th>
                <th class="text-center pr-3">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($serviceLuarList)): ?>
                <?php foreach ($serviceLuarList as $item): ?>
                  <tr>
                    <td class="pl-3"><?= htmlspecialchars($item['nama_barang']) ?></td>
                    <td><?= htmlspecialchars(date('d-m-Y', strtotime($item['tanggal_lapor']))) ?></td>
                    <td>
                      <?php if (!empty($item['tanggal_selesai'])): ?>
                        <?= htmlspecialchars(date('d-m-Y', strtotime($item['tanggal_selesai']))) ?>
                      <?php else: ?>
                        <span class="soft-badge pending">Belum selesai</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-center pr-3"><span class="soft-badge info"><?= htmlspecialchars(ucwords($item['status'])) ?></span></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4" class="text-center text-muted py-4">Tidak ada data service luar yang sedang diproses.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<br><br>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const barangChartLabels = <?= json_encode(array_keys($barangChart)) ?>;
  const barangChartData = <?= json_encode(array_values($barangChart)) ?>;
  const barangCtx = document.getElementById('barangConditionChart');

  if (barangCtx) {
    new Chart(barangCtx, {
      type: 'bar',
      data: {
        labels: barangChartLabels,
        datasets: [{
          label: 'Jumlah Barang',
          data: barangChartData,
          backgroundColor: [
            'rgba(29, 78, 216, 0.82)',
            'rgba(234, 88, 12, 0.82)',
            'rgba(220, 38, 38, 0.82)',
            'rgba(5, 150, 105, 0.82)'
          ],
          borderRadius: 10,
          maxBarThickness: 54
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: '#111827',
            padding: 12,
            displayColors: false
          }
        },
        scales: {
          x: {
            grid: {
              display: false
            },
            ticks: {
              color: '#475569',
              font: {
                size: 12,
                weight: '600'
              }
            }
          },
          y: {
            beginAtZero: true,
            ticks: {
              precision: 0,
              color: '#64748b'
            },
            grid: {
              color: 'rgba(148, 163, 184, 0.18)'
            }
          }
        }
      }
    });
  }
</script>

