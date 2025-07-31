<?php
require_once("../config/koneksi.php");

// Ambil parameter dari form
$printType = $_GET['printType'] ?? 'all';
$jenis_barang = $_GET['jenis_barang'] ?? '';
$status_barang = $_GET['status_barang'] ?? '';
$bulan = $_GET['bulan'] ?? date('n');
$tahun = $_GET['tahun'] ?? date('Y');

// Buat query berdasarkan filter
$where_conditions = [];
$params = [];

if ($printType === 'jenis' && !empty($jenis_barang)) {
    $where_conditions[] = "b.jenis_barang = ?";
    $params[] = $jenis_barang;
}

if ($printType === 'status' && !empty($status_barang)) {
    $where_conditions[] = "b.stts_brg = ?";
    $params[] = $status_barang;
}

if ($printType === 'jenis_status') {
    if (!empty($jenis_barang)) {
        $where_conditions[] = "b.jenis_barang = ?";
        $params[] = $jenis_barang;
    }
    if (!empty($status_barang)) {
        $where_conditions[] = "b.stts_brg = ?";
        $params[] = $status_barang;
    }
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = "WHERE " . implode(" AND ", $where_conditions);
}

$query = "SELECT b.*, 
    CASE 
        WHEN p.status = 'Disetujui' THEN 'Disetujui'
        ELSE 'Tidak Ada Pengajuan'
    END as status_pengajuan,
    p.id_pengajuan
    FROM tb_barang b 
    LEFT JOIN (
        SELECT pb1.kode_barang, pb1.status, pb1.id_pengajuan 
        FROM tb_pengajuan_barang pb1
        INNER JOIN (
            SELECT kode_barang, MAX(id_pengajuan) as max_id
            FROM tb_pengajuan_barang
            GROUP BY kode_barang
        ) pb2 ON pb1.kode_barang = pb2.kode_barang AND pb1.id_pengajuan = pb2.max_id
        WHERE pb1.status = 'Disetujui' AND pb1.status != 'Selesai'
    ) p ON b.kode_barang = p.kode_barang 
    $where_clause
    ORDER BY b.kode_barang ASC";

$stmt = mysqli_prepare($config, $query);
if (!empty($params)) {
    $types = str_repeat('s', count($params));
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .status-baik {
            color: green;
            font-weight: bold;
        }
        .status-rusak {
            color: red;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DATA BARANG</h1>
        <p>IT - RSPI</p>
        <p>Periode: <?= date('F Y', mktime(0,0,0,$bulan,1,$tahun)) ?></p>
        <?php if ($printType !== 'all'): ?>
            <p>Filter: 
                <?php 
                if ($printType === 'jenis' && !empty($jenis_barang)) {
                    echo "Jenis Barang: " . htmlspecialchars($jenis_barang);
                } elseif ($printType === 'status' && !empty($status_barang)) {
                    echo "Status Barang: " . htmlspecialchars($status_barang);
                } elseif ($printType === 'jenis_status') {
                    $filters = [];
                    if (!empty($jenis_barang)) $filters[] = "Jenis: " . htmlspecialchars($jenis_barang);
                    if (!empty($status_barang)) $filters[] = "Status: " . htmlspecialchars($status_barang);
                    echo implode(", ", $filters);
                }
                ?>
            </p>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Jenis Barang</th>
                <th>Penyerahan</th>
                <th>Status Barang</th>
                <th>Status Kerusakan</th>
                <th>Tanggal Penyerahan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($row = mysqli_fetch_assoc($result)): 
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['kode_barang']) ?></td>
                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                <td><?= htmlspecialchars($row['jenis_barang']) ?></td>
                <td><?= htmlspecialchars($row['penyerahan'] ?? '-') ?></td>
                <td class="<?= $row['stts_brg'] == 'Baik' ? 'status-baik' : 'status-rusak' ?>">
                    <?= htmlspecialchars($row['stts_brg'] ?? '-') ?>
                </td>
                <td><?= htmlspecialchars($row['status_perbaikan'] ?? '-') ?></td>
                <td><?= !empty($row['tgl_penyerahan']) ? date('d/m/Y', strtotime($row['tgl_penyerahan'])) : '-' ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: <?= date('d/m/Y H:i:s') ?></p>
        <p>Total Data: <?= mysqli_num_rows($result) ?> barang</p>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()">Cetak</button>
        <button onclick="window.close()">Tutup</button>
    </div>
</body>
</html> 