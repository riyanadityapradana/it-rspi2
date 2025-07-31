<?php
include '../../../config/koneksi.php';

// Ambil filter dari GET
$printType   = isset($_GET['printType']) ? $_GET['printType'] : 'all';
$jenisBarang = isset($_GET['jenis_barang']) ? $_GET['jenis_barang'] : '';
$bulan       = isset($_GET['bulan']) ? $_GET['bulan'] : date('n');
$tahun       = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Query dasar
$where = "WHERE MONTH(pm.tanggal_pemindahan) = '" . mysqli_real_escape_string($config, $bulan) . "' AND YEAR(pm.tanggal_pemindahan) = '" . mysqli_real_escape_string($config, $tahun) . "'";
if ($printType === 'jenis' && $jenisBarang) {
    $where .= " AND b.jenis_barang = '" . mysqli_real_escape_string($config, $jenisBarang) . "'";
}

$query = "
SELECT pm.*, b.nama_barang, b.jenis_barang, u.nama_lengkap as nama_staff
FROM tb_pemindahan_barang pm
LEFT JOIN tb_barang b ON pm.kode_barang = b.kode_barang
LEFT JOIN tb_user u ON pm.id_user = u.id_user
$where
ORDER BY pm.tanggal_pemindahan DESC
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
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Tanggal Pemindahan</th>
                <th>Ke Unit</th>
                <th>Alasan Pemindahan</th>
                <th>Staff</th>
            </tr>
        </thead>
        <tbody>
        <?php if(empty($data)): ?>
            <tr><td colspan="7" class="text-center text-muted">Tidak ada data</td></tr>
        <?php else: foreach($data as $i => $row): ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td><?= htmlspecialchars($row['kode_barang']) ?></td>
                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                <td><?= date('d/m/Y', strtotime($row['tanggal_pemindahan'])) ?></td>
                <td><?= htmlspecialchars($row['ke_unit']) ?></td>
                <td><?= htmlspecialchars($row['alasan_pemindahan']) ?></td>
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