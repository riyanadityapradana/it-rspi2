<?php
include '../../../config/koneksi.php';

// Ambil filter dari GET
$printType   = isset($_GET['printType']) ? $_GET['printType'] : 'all';
$jenisBarang = isset($_GET['jenis_barang']) ? $_GET['jenis_barang'] : '';
$bulan       = isset($_GET['bulan']) ? $_GET['bulan'] : date('n');
$tahun       = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Query dasar
$where = "WHERE MONTH(m.tanggal_mutasi) = '" . mysqli_real_escape_string($config, $bulan) . "' AND YEAR(m.tanggal_mutasi) = '" . mysqli_real_escape_string($config, $tahun) . "'";
if ($printType === 'jenis' && $jenisBarang) {
    $where .= " AND b.jenis_barang = '" . mysqli_real_escape_string($config, $jenisBarang) . "'";
}

$query = "
SELECT m.*, b.nama_barang, b.jenis_barang, l1.nama_lokasi AS lokasi_asal_nama, l2.nama_lokasi AS lokasi_tujuan_nama, u.nama_lengkap as nama_staff
FROM tb_mutasi_barang m
LEFT JOIN tb_barang b ON m.barang_id = b.barang_id
LEFT JOIN tb_lokasi l1 ON m.lokasi_asal = l1.lokasi_id
LEFT JOIN tb_lokasi l2 ON m.lokasi_tujuan = l2.lokasi_id
LEFT JOIN tb_user u ON m.id_user = u.id_user
$where
ORDER BY m.tanggal_mutasi DESC
";

$q = mysqli_query($config, $query);
$data = array();
while ($row = mysqli_fetch_assoc($q)) {
    $data[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pemindahan Barang</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            font-size: 13px;
        }
        .container {
            max-width: 1100px;
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
        .header h1 { margin: 0; font-size: 22px; letter-spacing: 1px; }
        .header p { margin: 6px 0; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; background: #fafafa; border-radius: 8px; overflow: hidden; }
        th, td { border: 1px solid #90caf9; padding: 10px 7px; text-align: left; font-size: 12px; }
        th { background: linear-gradient(90deg, #1976d2 0%, #64b5f6 100%); color: #fff; font-weight: bold; border-bottom: 2px solid #1976d2; }
        tr:nth-child(even) { background: #e3f2fd; }
        .footer { margin-top: 24px; text-align: right; color: #1976d2; font-size: 13px; }
        .no-print { margin-top: 24px; text-align: center; }
        .btn { background: linear-gradient(90deg, #1976d2 0%, #64b5f6 100%); color: #fff; border: none; border-radius: 6px; padding: 8px 18px; font-size: 13px; cursor: pointer; margin: 0 6px; }
        .btn:hover { opacity: 0.95; }
        @media print { body { margin: 0; background: #000000ff; } .container { box-shadow: none; padding: 0; } .no-print { display: none; } }
    </style>
</head>
<body>
    <script>
        window.onload = function() {
            setTimeout(function(){ window.print(); }, 700);
        };
    </script>
    <div class="container">
        <div class="header">
            <h1 style="color: #000000ff;"><i class="fa fa-print"></i> LAPORAN PEMINDAHAN BARANG</h1>
            <p style="color: #000000ff;">Periode: <b><?= htmlspecialchars(date('F', mktime(0,0,0,$bulan,10)) . ' ' . $tahun) ?></b></p>
            <p style="color: #000000ff;">Filter: <span style="background:#fff; color:#1976d2; padding:2px 8px; border-radius:6px; font-weight:bold;">Jenis: <?= $printType === 'jenis' && $jenisBarang ? htmlspecialchars($jenisBarang) : 'Semua' ?></span></p>
        </div>
        <table style="color: #000000ff;">
            <thead>
                <tr>
                    <th style="color: #000000ff;">No</th>
                    <th style="color: #000000ff;">Nama Barang</th>
                    <th style="color: #000000ff;">Jenis Barang</th>
                    <th style="color: #000000ff;">Lokasi Asal</th>
                    <th style="color: #000000ff;">Lokasi Tujuan</th>
                    <th style="color: #000000ff;">Tanggal Mutasi</th>
                    <th style="color: #000000ff;">Keterangan</th>
                    <th style="color: #000000ff;">Staff</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($data)): ?>
                    <tr><td colspan="8" class="text-center text-muted">Tidak ada data</td></tr>
                <?php else: foreach($data as $i => $row): ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                    <td><?= htmlspecialchars($row['jenis_barang']) ?></td>
                    <td><?= htmlspecialchars($row['lokasi_asal_nama']) ?></td>
                    <td><?= htmlspecialchars($row['lokasi_tujuan_nama']) ?></td>
                    <td><?= !empty($row['tanggal_mutasi']) ? date('d/m/Y', strtotime($row['tanggal_mutasi'])) : '-' ?></td>
                    <td><?= htmlspecialchars($row['keterangan']) ?></td>
                    <td><?= htmlspecialchars($row['nama_staff']) ?></td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
        <div class="footer">
            <p style="color: #000000ff;">Dicetak pada: <?= date('d/m/Y H:i:s') ?></p>
            <p style="color: #000000ff;">Total Data: <?= count($data) ?> item</p>
        </div>
        <div class="no-print">
            <button class="btn" onclick="window.print()"><i class="fa fa-print"></i> Cetak</button>
            <button class="btn" onclick="window.close()"><i class="fa fa-times"></i> Tutup</button>
        </div>
    </div>
</body>
</html>