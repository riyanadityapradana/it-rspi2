<?php
// Koneksi ke database (hardcode, sesuaikan jika perlu)
$host = '192.168.1.4';
$user = 'root';
$pass = '';
$db   = 'sik9';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die('Koneksi gagal: ' . $conn->connect_error);
$conn->set_charset('utf8');

// Daftar kamar sesuai urutan
$daftar_kamar = [
    'BERLIAN', 'SAFIR', 'RUBY A', 'RUBY B',
    'ZAMRUD A', 'ZAMRUD B', 'ZAMRUD C',
    'ISOLASI',
    'KECUBUNG A', 'KECUBUNG B1', 'KECUBUNG B2', 'KECUBUNG B3', 'KECUBUNG B4',
    'YAKUT A', 'YAKUT B', 'YAKUT C'
];

// Mapping jenis bayar
$jenis_bayar = [
    'A09' => 'UMUM',
    'BPJ' => 'BPJS',
    'A92' => 'ASURANSI',
];

// Array minggu manual (samakan dengan rawat jalan)
$minggu = [
    ['2025-07-01', '2025-07-07'],
    ['2025-07-08', '2025-07-14'],
    ['2025-07-15', '2025-07-21'],
    ['2025-07-22', '2025-07-28'],
    ['2025-07-29', '2025-07-31'],
];

// Siapkan array rekap
$rekap = [];
foreach ($daftar_kamar as $kamar) {
    foreach ($minggu as $i => $range) {
        foreach ($jenis_bayar as $kd_pj => $label) {
            $rekap[$kamar][$i][$kd_pj] = 0;
        }
        $rekap[$kamar][$i]['JUMLAH'] = 0;
    }
}

// Query data per minggu, per kamar, per jenis bayar
foreach ($daftar_kamar as $kamar) {
    foreach ($minggu as $i => $range) {
        $start = $range[0];
        $end   = $range[1];
        foreach ($jenis_bayar as $kd_pj => $label) {
            $sql = "SELECT COUNT(*) as jml
                    FROM kamar_inap ki
                    JOIN reg_periksa rp ON ki.no_rawat = rp.no_rawat
                    WHERE ki.kd_kamar = '$kamar'
                    AND rp.kd_pj = '$kd_pj'
                    AND ki.tgl_masuk BETWEEN '$start' AND '$end'";
            $res = $conn->query($sql);
            $row = $res->fetch_assoc();
            $rekap[$kamar][$i][$kd_pj] = (int)$row['jml'];
            $rekap[$kamar][$i]['JUMLAH'] += (int)$row['jml'];
        }
    }
}

// Hitung total per jenis bayar dan total per minggu
$total_per_jenis = [];
$total_per_minggu = [];
foreach ($minggu as $i => $range) {
    foreach ($jenis_bayar as $kd_pj => $label) {
        $total_per_jenis[$i][$kd_pj] = 0;
    }
    $total_per_minggu[$i] = 0;
    foreach ($daftar_kamar as $kamar) {
        foreach ($jenis_bayar as $kd_pj => $label) {
            $total_per_jenis[$i][$kd_pj] += $rekap[$kamar][$i][$kd_pj];
        }
        $total_per_minggu[$i] += $rekap[$kamar][$i]['JUMLAH'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Kunjungan Pasien Rawat Inap</title>
    <style>
        table { border-collapse: collapse; width: 100%; font-size: 12px; }
        th, td { border: 1px solid #888; padding: 4px 8px; text-align: center; }
        th { background: #ff9800; color: #111; font-weight: bold; }
        .header-minggu { background: #ffe0b2; color: #111; font-weight: bold; }
        .header-jenis { background: #bbdefb; color: #111; font-weight: bold; }
        .jumlah { background: #ffe082; color: #111; font-weight: bold; }
        .total { background: #ff5722; color: #fff; font-weight: bold; }
        .kamar { text-align: left; }
    </style>
</head>
<body>
<h2>REKAP KUNJUNGAN PASIEN RAWAT INAP</h2>
<table>
    <tr>
        <th rowspan="2">KAMAR RAWAT</th>
        <?php foreach ($minggu as $i => $range): ?>
            <th colspan="4" class="header-minggu">
                <?= date('j', strtotime($range[0])) . ' - ' . date('j F Y', strtotime($range[1])) ?>
            </th>
        <?php endforeach; ?>
    </tr>
    <tr>
        <?php foreach ($minggu as $i => $range): ?>
            <th class="header-jenis">UMUM</th>
            <th class="header-jenis">BPJS</th>
            <th class="header-jenis">ASURANSI</th>
            <th class="jumlah">JLH</th>
        <?php endforeach; ?>
    </tr>
    <?php foreach ($daftar_kamar as $kamar): ?>
        <tr>
            <td class="kamar"><?= htmlspecialchars($kamar) ?></td>
            <?php foreach ($minggu as $i => $range): ?>
                <td><?= $rekap[$kamar][$i]['A09'] ?></td>
                <td><?= $rekap[$kamar][$i]['BPJ'] ?></td>
                <td><?= $rekap[$kamar][$i]['A92'] ?></td>
                <td class="jumlah"><?= $rekap[$kamar][$i]['JUMLAH'] ?></td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
    <tr class="total">
        <td>JUMLAH PER JENIS BAYAR</td>
        <?php foreach ($minggu as $i => $range): ?>
            <td><?= $total_per_jenis[$i]['A09'] ?></td>
            <td><?= $total_per_jenis[$i]['BPJ'] ?></td>
            <td><?= $total_per_jenis[$i]['A92'] ?></td>
            <td></td>
        <?php endforeach; ?>
    </tr>
    <tr class="total">
        <td>JUMLAH PX PER MINGGU</td>
        <?php foreach ($minggu as $i => $range): ?>
            <td colspan="4"><?= $total_per_minggu[$i] ?></td>
        <?php endforeach; ?>
    </tr>
</table>
</body>
</html>
