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
    <title>Laporan Pemindahan Barang</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <style>
        @media print {
            .no-print { display: none; }
        }
        th, td { font-size: 12px; }
        .kop-surat img { height: 100px; }
        .kop-surat .kanan { float: right; height: 40px; margin-left: 10px; }
        .kop-surat .kiri { float: left; height: 80px; margin-right: 10px; }
        .kop-surat { text-align: center; position: relative; }
    </style>
</head>
<body>
<div class="container mt-4">
    <div class="kop-surat mb-2">
        <img src="../../../assets/img/logo.jpg" alt="Logo" class="kiri">
        <img src="../../../assets/img/bintang.png" alt="Bintang" class="kanan">
        <span style="font-size:20px; font-weight:bold;">PT. PELITA INSANI MULIA</span><br>
        <span style="font-size:16px;">RUMAH SAKIT PELITA INSANI MARTAPURA</span><br>
        <span style="font-size:14px;">Terakreditasi KARS Versi SNARS Edisi 1 Tingkat Madya</span><br>
        <span style="font-size:12px;">Jl. Sekumpul No. 66 Martapura - Telp. (0511) 4722210, 4722220, Kalimantan Selatan</span><br>
        <span style="font-size:12px; color:red;">Emergency Call (0511) 4722222</span> |
        <span style="font-size:12px;">Fax. (0511) 4722230 | Email: <span style="color:blue;">rs.pelitainsani@gmail.com</span></span><br>
        <span style="font-size:12px;">Website: www.pelitainsani.com</span>
        <div style="clear:both;"></div>
    </div>
    <hr style="border:1px solid #000; margin-top:10px; margin-bottom:20px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Laporan Pemindahan Barang</h4>
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
                <th>Nama Barang</th>
                <th>Jenis Barang</th>
                <th>Lokasi Asal</th>
                <th>Lokasi Tujuan</th>
                <th>Tanggal Mutasi</th>
                <th>Keterangan</th>
                <th>Staff</th>
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
                <td><?= date('d/m/Y', strtotime($row['tanggal_mutasi'])) ?></td>
                <td><?= htmlspecialchars($row['keterangan']) ?></td>
                <td><?= htmlspecialchars($row['nama_staff']) ?></td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<!-- FontAwesome for print icon -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>