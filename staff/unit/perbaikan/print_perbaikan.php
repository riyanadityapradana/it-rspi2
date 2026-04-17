<?php
require_once("../../../config/koneksi.php");

// Ambil parameter dari form
$tindakan = $_GET['tindakan'] ?? '';
$status = $_GET['status'] ?? '';
$tanggal_mulai = $_GET['tanggal_mulai'] ?? '';
$tanggal_selesai = $_GET['tanggal_selesai'] ?? '';

// Build WHERE clause
$where_conditions = [];

if (!empty($tindakan)) {
    // normalize input (allow both 'service_luar' and 'Service luar' etc.)
    $tindakan_search = strtolower(str_replace('_', ' ', $tindakan));
    $tindakan_esc = mysqli_real_escape_string($config, $tindakan_search);
    $where_conditions[] = "LOWER(p.tindakan_perbaikan) LIKE '%{$tindakan_esc}%'";
}

if (!empty($status)) {
    // match status case-insensitively
    $status_esc = mysqli_real_escape_string($config, strtolower($status));
    $where_conditions[] = "LOWER(p.status) = '{$status_esc}'";
}

if (!empty($tanggal_mulai)) {
    $tanggal_mulai_esc = mysqli_real_escape_string($config, $tanggal_mulai);
    $where_conditions[] = "DATE(p.tanggal_lapor) >= '{$tanggal_mulai_esc}'";
}

if (!empty($tanggal_selesai)) {
    $tanggal_selesai_esc = mysqli_real_escape_string($config, $tanggal_selesai);
    $where_conditions[] = "DATE(p.tanggal_lapor) <= '{$tanggal_selesai_esc}'";
}

$where = count($where_conditions) > 0 ? "WHERE " . implode(" AND ", $where_conditions) : "";

$query = "SELECT 
         b.kode_inventaris,
         b.nama_barang,
         b.jenis_barang,
         b.nomor_seri,
         b.ip_address,
         l.nama_lokasi AS lokasi_barang,
         p.perbaikan_id,
         p.deskripsi_kerusakan,
         p.tindakan_perbaikan,
         p.status,
         p.tanggal_lapor,
         p.teknisi,
         p.keterangan,
         p.unit_melapor,
         u.nama_lokasi AS unit_melapor_nama,
         p.tanggal_selesai
FROM tb_perbaikan_barang p
JOIN tb_barang b ON p.barang_id = b.barang_id
LEFT JOIN tb_lokasi l ON b.lokasi_id = l.lokasi_id
LEFT JOIN tb_lokasi u ON p.unit_melapor = u.lokasi_id
$where
ORDER BY b.barang_id, p.tanggal_lapor DESC";

$result = mysqli_query($config, $query);

// Debug: log the query if no results
if (!$result) {
    die("Query Error: " . mysqli_error($config));
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Perbaikan Barang</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            background: #ffffff;
            font-size: 13px;
            color: #000;
        }
        .container {
            max-width: 1200px;
            margin: 30px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            padding: 32px 28px 24px 28px;
        }
        .kop-surat {
            display: table;
            width: 100%;
            border-bottom: 3px solid #000;
            padding-bottom: 12px;
            margin-bottom: 22px;
        }
        .kop-logo,
        .kop-text {
            display: table-cell;
            vertical-align: top;
        }
        .kop-logo {
            width: 170px;
        }
        .kop-logo img {
            width: 140px;
            height: auto;
        }
        .kop-text {
            text-align: center;
            padding-right: 60px;
        }
        .kop-text h1,
        .kop-text h2,
        .kop-text p {
            margin: 0;
        }
        .kop-text h1 {
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .kop-text h2 {
            font-size: 26px;
            font-weight: 700;
            margin-top: 2px;
        }
        .kop-text .sub {
            font-size: 13px;
            margin-top: 4px;
        }
        .kop-text .addr {
            font-size: 12px;
            margin-top: 3px;
            line-height: 1.45;
        }
        .report-title {
            text-align: center;
            margin-bottom: 18px;
        }
        .report-title h3 {
            margin: 0;
            font-size: 20px;
            letter-spacing: 0.8px;
        }
        .report-title p {
            margin: 6px 0 0;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
            background: #fafafa;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            border: 1px solid #90caf9;
            padding: 10px 7px;
            text-align: left;
            font-size: 12px;
        }
        th {
            background: linear-gradient(90deg, #1976d2 0%, #64b5f6 100%);
            color: #fff;
            font-weight: bold;
            border-bottom: 2px solid #1976d2;
        }
        tr:nth-child(even) {
            background: #e3f2fd;
        }
        .status-diajukan {
            color: #f57c00;
            font-weight: bold;
        }
        .status-proses {
            color: #1976d2;
            font-weight: bold;
        }
        .status-selesai {
            color: #388e3c;
            font-weight: bold;
        }
        .status-tidak_dapat_diperbaiki {
            color: #d32f2f;
            font-weight: bold;
        }
        .tindakan-service_luar {
            color: #1976d2;
        }
        .tindakan-service_sendiri {
            color: #388e3c;
        }
        .footer {
            margin-top: 32px;
            text-align: right;
            color: #1976d2;
            font-size: 13px;
        }
        .no-print {
            margin-top: 26px;
            text-align: center;
        }
        .btn {
            background: linear-gradient(90deg, #1976d2 0%, #64b5f6 100%);
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 8px 18px;
            font-size: 13px;
            cursor: pointer;
            margin: 0 6px;
            box-shadow: 0 2px 6px rgba(25,118,210,0.08);
            transition: background 0.2s;
        }
        .btn:hover {
            background: linear-gradient(90deg, #1565c0 0%, #42a5f5 100%);
        }
        .filter-info {
            background: #f5f5f5;
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 12px;
            font-size: 12px;
            border: 1px solid #d0d7de;
        }
        .filter-badge {
            display: inline-block;
            background: #fff;
            color: #1976d2;
            padding: 2px 8px;
            border-radius: 6px;
            font-weight: bold;
            margin-right: 8px;
            border: 1px solid #c7d8ef;
        }
        @media print {
            body { margin: 0; background: #ffffff; }
            .container { box-shadow: none; padding: 0; margin: 0; max-width: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <script>
        // Auto print saat halaman dimuat
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 1000);
        };
    </script>
    <div class="container">
        <div class="kop-surat">
            <div class="kop-logo">
                <img src="../../../assets/img/logo.jpg" alt="Logo RSPI">
            </div>
            <div class="kop-text">
                <h1>PT. PELITA INSANI MULIA</h1>
                <h2>RUMAH SAKIT PELITA INSANI MARTAPURA</h2>
                <p class="sub">Terakreditasi KARS Versi SNARS Edisi 1 Tingkat Madya</p>
                <p class="addr">Jl. Sekumpul No. 66 Martapura - Telp. (0511) 4722210, 4722220, Kalimantan Selatan</p>
                <p class="addr">Fax. (0511) 4722230, Emergency Call (0511) 4722222, Email: rs.pelitainsani@gmail.com</p>
                <p class="addr">Website: www.pelitainsani.com</p>
            </div>
        </div>

        <div class="report-title">
            <h3>LAPORAN DATA PERBAIKAN BARANG</h3>
            <p>Dicetak pada <?= date('d/m/Y H:i:s') ?></p>
        </div>

        <?php if (!empty($tindakan) || !empty($status) || !empty($tanggal_mulai) || !empty($tanggal_selesai)): ?>
        <div class="filter-info">
            Filter Aktif:
            <?php if (!empty($tindakan)): ?>
                <span class="filter-badge">Tindakan: <?= htmlspecialchars(ucwords(str_replace('_', ' ', $tindakan))) ?></span>
            <?php endif; ?>
            <?php if (!empty($status)): ?>
                <span class="filter-badge">Status: <?= htmlspecialchars(ucwords($status)) ?></span>
            <?php endif; ?>
            <?php if (!empty($tanggal_mulai)): ?>
                <span class="filter-badge">Dari: <?= htmlspecialchars(date('d/m/Y', strtotime($tanggal_mulai))) ?></span>
            <?php endif; ?>
            <?php if (!empty($tanggal_selesai)): ?>
                <span class="filter-badge">Sampai: <?= htmlspecialchars(date('d/m/Y', strtotime($tanggal_selesai))) ?></span>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <table style="color: #000000ff;">
            <thead>
                <tr>
                    <th style="color: #000000ff;">No</th>
                    <th style="color: #000000ff;">Tanggal Lapor</th>
                    <th style="color: #000000ff;">Unit Melapor</th>
                    <th style="color: #000000ff;">Nama Barang</th>
                    <th style="color: #000000ff;">Jenis Barang</th>
                    <th style="color: #000000ff;">No.Seri</th>
                    <th style="color: #000000ff;">Deskripsi Kerusakan</th>
                    <th style="color: #000000ff;">Tindakan</th>
                    <th style="color: #000000ff;">Status</th>
                    <th style="color: #000000ff;">Teknisi</th>
                    <th style="color: #000000ff;">Tanggal Selesai</th>
                </tr>
            </thead>
            <tbody>
                <!-- DEBUG: Query = <?php echo htmlspecialchars($query); ?> -->
                <!-- DEBUG: Tindakan = <?php echo htmlspecialchars($tindakan); ?>, Status = <?php echo htmlspecialchars($status); ?> -->
                <?php 
                $no = 1;
                $total_rows = 0;
                while ($row = mysqli_fetch_assoc($result)): 
                    $total_rows++;
                    $status_class = 'status-' . $row['status'];
                    $tindakan_class = 'tindakan-' . $row['tindakan_perbaikan'];
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($row['tanggal_lapor']))) ?></td>
                    <td><?= htmlspecialchars($row['unit_melapor_nama'] ?? $row['unit_melapor']) ?></td>
                    <td><?= htmlspecialchars($row['nama_barang']) ?><br><small style="color:#666;">Kode: <?= htmlspecialchars($row['kode_inventaris'] ?? '-') ?></small></td>
                    <td><?= htmlspecialchars($row['jenis_barang']) ?></td>
                    <td><?= htmlspecialchars($row['nomor_seri']) ?></td>
                    <td><?= htmlspecialchars(substr($row['deskripsi_kerusakan'], 0, 50)) . (strlen($row['deskripsi_kerusakan']) > 50 ? '...' : '') ?></td>
                    <td class="<?= $tindakan_class ?>"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $row['tindakan_perbaikan']))) ?></td>
                    <td class="<?= $status_class ?>">
                        <?php
                            if (strtolower($row['status']) === 'tidak_dapat_diperbaiki') {
                                echo 'RUSAK';
                            } else {
                                echo htmlspecialchars(ucwords(str_replace('_', ' ', $row['status'])));
                            }
                        ?>
                    </td>
                    <td><?= htmlspecialchars($row['teknisi'] ?? '-') ?></td>
                    <td><?= !empty($row['tanggal_selesai']) ? htmlspecialchars(date('d/m/Y H:i', strtotime($row['tanggal_selesai']))) : '-' ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="footer">
            <p style="color: #000000ff;">Dicetak pada: <?= date('d/m/Y H:i:s') ?></p>
            <p style="color: #000000ff;">Total Data: <?= $total_rows ?> perbaikan</p>
        </div>
        <div class="no-print">
            <button class="btn" onclick="window.print()"><i class="fa fa-print"></i> Cetak</button>
            <button class="btn" onclick="window.close()"><i class="fa fa-times"></i> Tutup</button>
        </div>
    </div>
</body>
</html>
