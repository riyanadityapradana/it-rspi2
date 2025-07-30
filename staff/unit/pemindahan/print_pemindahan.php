<?php
include '../config/koneksi.php';

// Ambil filter dari GET
$printType   = $_GET['printType'] ?? 'all';
$jenisBarang = $_GET['jenis_barang'] ?? '';
$bulan       = $_GET['bulan'] ?? date('n');
$tahun       = $_GET['tahun'] ?? date('Y');

// Query dasar
$where = "WHERE MONTH(tanggal_pemindahan) = ? AND YEAR(tanggal_pemindahan) = ?";
$params = [$bulan, $tahun];

if ($printType === 'jenis' && $jenisBarang) {
    $where .= " AND b.jenis_barang = ?";
    $params[] = $jenisBarang;
}

$query = "
SELECT pb.*, b.nama_barang, b.jenis_barang, b.spesifikasi, u1.nama_lengkap as pengaju, u2.nama_lengkap as approver
FROM tb_pemindahan_barang pb
JOIN tb_barang b ON pb.kode_barang = b.kode_barang
JOIN tb_user u1 ON pb.created_by = u1.id_user
LEFT JOIN tb_user u2 ON pb.disetujui_oleh = u2.id_user
$where
ORDER BY pb.tanggal_pemindahan ASC
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$data = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pemindahan Barang</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <style>
        @media print {
            .no-print { display: none; }
        }
        th, td { font-size: 12px; }
    </style>
</head>
<body>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Laporan Pemindahan Barang</h4>
        <button class="btn btn-success no-print" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
    </div>
    <div class="mb-2">
        <strong>Periode:</strong> <?= date('F', mktime(0,0,0,$bulan,10)) ?> <?= $tahun ?><br>
        <?php if($printType === 'jenis' && $jenisBarang): ?>
            <strong>Jenis Barang:</strong> <?= htmlspecialchars($jenisBarang) ?>
        <?php else: ?>
            <strong>Jenis Barang:</strong> Semua
        <?php endif; ?>
    </div>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Barang</th>
                <th>Jenis</th>
                <th>Unit Asal</th>
                <th>Unit Tujuan</th>
                <th>PIC</th>
                <th>Status</th>
                <th>Pengaju</th>
                <th>Approver</th>
            </tr>
        </thead>
        <tbody>
        <?php if(empty($data)): ?>
            <tr><td colspan="10" class="text-center text-muted">Tidak ada data</td></tr>
        <?php else: foreach($data as $i => $row): ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td><?= date('d/m/Y H:i', strtotime($row['tanggal_pemindahan'])) ?></td>
                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                <td><?= htmlspecialchars($row['jenis_barang']) ?></td>
                <td><?= htmlspecialchars($row['unit_asal']) ?></td>
                <td><?= htmlspecialchars($row['unit_tujuan']) ?></td>
                <td><?= htmlspecialchars($row['pic_pemindahan']) ?></td>
                <td><?= htmlspecialchars($row['status_pemindahan']) ?></td>
                <td><?= htmlspecialchars($row['pengaju']) ?></td>
                <td><?= htmlspecialchars($row['approver'] ?? '-') ?></td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<!-- FontAwesome for print icon -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>