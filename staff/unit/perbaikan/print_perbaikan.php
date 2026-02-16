<?php
require_once("../../../config/koneksi.php");

// Ambil parameter dari form
$tindakan = $_GET['tindakan'] ?? '';
$status = $_GET['status'] ?? '';

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

$where = count($where_conditions) > 0 ? "WHERE " . implode(" AND ", $where_conditions) : "";

$query = "SELECT 
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
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            font-size: 13px;
        }
        .container {
            max-width: 1200px;
            margin: 30px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            padding: 32px 28px 24px 28px;
        }
        .header {
            text-align: center;
            margin-bottom: 24px;
            background: linear-gradient(90deg, #1976d2 0%, #64b5f6 100%);
            color: #fff;
            border-radius: 12px 12px 0 0;
            padding: 18px 0 10px 0;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            letter-spacing: 1px;
        }
        .header p {
            margin: 6px 0;
            font-size: 13px;
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
            margin-top: 24px;
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
        }
        .filter-badge {
            display: inline-block;
            background: #fff;
            color: #1976d2;
            padding: 2px 8px;
            border-radius: 6px;
            font-weight: bold;
            margin-right: 8px;
        }
        @media print {
            body { margin: 0; background: #ffffff; }
            .container { box-shadow: none; padding: 0; }
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
        <div class="header">
            <h1 style="color: #000000ff;"><i class="fa fa-print"></i> LAPORAN DATA PERBAIKAN BARANG</h1>
            <p style="color: #000000ff;">IT-RSPI <?= date('d/m/Y H:i:s') ?></p>
        </div>

        <?php if (!empty($tindakan) || !empty($status)): ?>
        <div class="filter-info">
            Filter Aktif:
            <?php if (!empty($tindakan)): ?>
                <span class="filter-badge">Tindakan: <?= htmlspecialchars(ucwords(str_replace('_', ' ', $tindakan))) ?></span>
            <?php endif; ?>
            <?php if (!empty($status)): ?>
                <span class="filter-badge">Status: <?= htmlspecialchars(ucwords($status)) ?></span>
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
                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                    <td><?= htmlspecialchars($row['jenis_barang']) ?></td>
                    <td><?= htmlspecialchars($row['nomor_seri']) ?></td>
                    <td><?= htmlspecialchars(substr($row['deskripsi_kerusakan'], 0, 50)) . (strlen($row['deskripsi_kerusakan']) > 50 ? '...' : '') ?></td>
                    <td class="<?= $tindakan_class ?>"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $row['tindakan_perbaikan']))) ?></td>
                    <td class="<?= $status_class ?>"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $row['status']))) ?></td>
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
