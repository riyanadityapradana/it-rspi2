<?php
require_once("../../../config/koneksi.php");

$unit = $_POST['unit'] ?? '';
$tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
$tanggal_selesai = $_POST['tanggal_selesai'] ?? '';

$where = [];
$where[] = "b.kondisi = 'rusak'";
$where[] = "p.status = 'tidak_dapat_diperbaiki'";

if (!empty($unit)) {
    $unit_esc = mysqli_real_escape_string($config, $unit);
    $where[] = "p.unit_melapor = '{$unit_esc}'";
    // ambil nama unit untuk tampilan di header print
    $qname = mysqli_query($config, "SELECT nama_lokasi FROM tb_lokasi WHERE lokasi_id = '{$unit_esc}' LIMIT 1");
    $unit_name = '';
    if ($qname) {
        $rn = mysqli_fetch_assoc($qname);
        if ($rn) $unit_name = $rn['nama_lokasi'];
    }
}

if (!empty($tanggal_mulai)) {
    $tm = mysqli_real_escape_string($config, $tanggal_mulai);
    $where[] = "DATE(p.tanggal_lapor) >= '{$tm}'";
}

if (!empty($tanggal_selesai)) {
    $ts = mysqli_real_escape_string($config, $tanggal_selesai);
    $where[] = "DATE(p.tanggal_lapor) <= '{$ts}'";
}

$where_sql = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$query = "SELECT b.kode_inventaris, b.nama_barang, b.jenis_barang, b.nomor_seri, l.nama_lokasi AS lokasi_barang,
                 p.perbaikan_id, p.deskripsi_kerusakan, p.tanggal_lapor, p.unit_melapor, u.nama_lokasi AS unit_melapor_nama
          FROM tb_barang b
          JOIN tb_perbaikan_barang p ON p.barang_id = b.barang_id
          LEFT JOIN tb_lokasi l ON b.lokasi_id = l.lokasi_id
          LEFT JOIN tb_lokasi u ON p.unit_melapor = u.lokasi_id
          {$where_sql}
          ORDER BY p.tanggal_lapor DESC";

$result = mysqli_query($config, $query);
if (!$result) {
    die('Query Error: '.mysqli_error($config));
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Print Barang Rusak</title>
    <style>
        body{font-family: Arial, sans-serif; font-size:13px}
        table{width:100%;border-collapse:collapse}
        th,td{border:1px solid #ccc;padding:6px;text-align:left}
        th{background:#f0f0f0}
        .filter{margin-bottom:12px}
    </style>
</head>
<body>
    <h3>Daftar Barang Rusak</h3>
    <div class="filter">
        <?php if (!empty($unit)): ?>
            <strong>Unit:</strong> <?= htmlspecialchars($unit_name ?: $unit) ?>
        <?php endif; ?>
        <?php if (!empty($tanggal_mulai)): ?>
            &nbsp; <strong>Dari:</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($tanggal_mulai))) ?>
        <?php endif; ?>
        <?php if (!empty($tanggal_selesai)): ?>
            &nbsp; <strong>Sampai:</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($tanggal_selesai))) ?>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Lapor</th>
                <th>Unit Melapor</th>
                <th>Nama Barang</th>
                <th>Jenis</th>
                <th>No. Seri</th>
                <th>Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no=1; while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= !empty($row['tanggal_lapor']) ? date('d/m/Y H:i', strtotime($row['tanggal_lapor'])) : '-' ?></td>
                <td><?= htmlspecialchars($row['unit_melapor_nama'] ?? $row['unit_melapor']) ?></td>
                <td><?= htmlspecialchars($row['nama_barang']) ?><br><small>Kode: <?= htmlspecialchars($row['kode_inventaris'] ?? '-') ?></small></td>
                <td><?= htmlspecialchars($row['jenis_barang']) ?></td>
                <td><?= htmlspecialchars($row['nomor_seri']) ?></td>
                <td><?= htmlspecialchars($row['deskripsi_kerusakan']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <script>
        window.onload = function(){ setTimeout(function(){ window.print(); }, 500); }
    </script>
</body>
</html>
