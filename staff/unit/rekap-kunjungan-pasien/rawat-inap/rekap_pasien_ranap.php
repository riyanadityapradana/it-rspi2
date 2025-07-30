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
$mapping_kamar = [
    'RUBY' => ['RUBY A', 'RUBY B'],
    'ZAMRUD' => ['ZAMRUD A', 'ZAMRUD B', 'ZAMRUD C'],
    'YAKUT' => ['YAKUT A', 'YAKUT B', 'YAKUT C'],
    'KECUBUNG' => ['KECUBUNG A', 'KECUBUNG B1', 'KECUBUNG B2', 'KECUBUNG B3', 'KECUBUNG B4'],
    'BERLIAN' => ['BERLIAN'],
    'SAFIR' => ['SAFIR'],
    'ISOLASI' => ['ISOLASI']
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
foreach ($mapping_kamar as $group => $sub_kamar_list) {
    foreach ($minggu as $i => $range) {
        foreach ($jenis_bayar as $kd_pj => $label) {
            $rekap[$group][$i][$kd_pj] = 0;
        }
        $rekap[$group][$i]['JUMLAH'] = 0;
    }

    foreach ($minggu as $i => $range) {
        $start = $range[0];
        $end = $range[1];
        foreach ($jenis_bayar as $kd_pj => $label) {
            $total = 0;
            foreach ($sub_kamar_list as $sub_kamar) {
                $sql = "SELECT COUNT(*) as jml
                        FROM kamar_inap ki
                        JOIN reg_periksa rp ON ki.no_rawat = rp.no_rawat
                        JOIN kamar k ON ki.kd_kamar = k.kd_kamar
                        JOIN bangsal b ON k.kd_bangsal = b.kd_bangsal
                        WHERE b.nm_bangsal = '$sub_kamar'
                        AND rp.kd_pj = '$kd_pj'
                        AND ki.tgl_masuk BETWEEN '$start' AND '$end'";
                $res = $conn->query($sql);
                $row = $res->fetch_assoc();
                $total += (int)$row['jml'];
            }
            $rekap[$group][$i][$kd_pj] = $total;
            $rekap[$group][$i]['JUMLAH'] += $total;
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
    foreach (array_keys($mapping_kamar) as $group) {
        foreach ($jenis_bayar as $kd_pj => $label) {
            $total_per_jenis[$i][$kd_pj] += $rekap[$group][$i][$kd_pj];
        }
        $total_per_minggu[$i] += $rekap[$group][$i]['JUMLAH'];
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
<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th rowspan="2">KAMAR RAWAT</th>
            <?php foreach ($minggu as $i => $range): ?>
                <th colspan="4"><?= date('j', strtotime($range[0])) . ' - ' . date('j F Y', strtotime($range[1])) ?></th>
            <?php endforeach; ?>
        </tr>
        <tr>
            <?php foreach ($minggu as $i => $range): ?>
                <th>UMUM</th>
                <th>BPJS</th>
                <th>ASURANSI</th>
                <th>JML</th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($daftar_kamar as $kamar): ?>
            <tr>
                <td><?= $kamar ?></td>
                <?php foreach ($minggu as $i => $range): ?>
                    <td align="center"><?= $rekap[$kamar][$i]['A09'] ?></td>
                    <td align="center"><?= $rekap[$kamar][$i]['BPJ'] ?? 0 ?></td>
                    <td align="center"><?= $rekap[$kamar][$i]['A92'] ?></td>
                    <td align="center"><strong><?= $rekap[$kamar][$i]['JUMLAH'] ?></strong></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr style="background-color: orange; font-weight: bold;">
            <td>JUMLAH PER JENIS BAYAR</td>
            <?php foreach ($minggu as $i => $range): ?>
                <td align="center"><?= $total_per_jenis[$i]['A09'] ?></td>
                <td align="center"><?= $total_per_jenis[$i]['BPJ'] ?? 0 ?></td>
                <td align="center"><?= $total_per_jenis[$i]['A92'] ?></td>
                <td align="center"><?= $total_per_minggu[$i] ?></td>
            <?php endforeach; ?>
        </tr>
        <tr style="background-color: orangered; font-weight: bold;">
            <td>JUMLAH PX PER MINGGU</td>
            <?php foreach ($minggu as $i => $range): ?>
                <td colspan="4" align="center"><?= $total_per_minggu[$i] ?></td>
            <?php endforeach; ?>
        </tr>
    </tfoot>
</table>

</body>
</html>
