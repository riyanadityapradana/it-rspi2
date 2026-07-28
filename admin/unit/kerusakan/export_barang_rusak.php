<?php
ob_start();
require_once('../../../config/koneksi.php');
require_once(__DIR__ . '/../export_excel_helpers.php');

$unit = $_GET['unit'] ?? '';
$tanggal_mulai = $_GET['tanggal_mulai'] ?? '';
$tanggal_selesai = $_GET['tanggal_selesai'] ?? '';

$where = [];
$where[] = "b.kondisi = 'rusak'";
$where[] = "p.status = 'tidak_dapat_diperbaiki'";

if ($unit !== '') {
    $unit_esc = mysqli_real_escape_string($config, $unit);
    $where[] = "p.unit_melapor = '{$unit_esc}'";
}
if ($tanggal_mulai !== '') {
    $tm = mysqli_real_escape_string($config, $tanggal_mulai);
    $where[] = "DATE(p.tanggal_lapor) >= '{$tm}'";
}
if ($tanggal_selesai !== '') {
    $ts = mysqli_real_escape_string($config, $tanggal_selesai);
    $where[] = "DATE(p.tanggal_lapor) <= '{$ts}'";
}

$where_sql = 'WHERE ' . implode(' AND ', $where);
$sql = "SELECT b.barang_id, b.kode_inventaris, b.nama_barang, b.jenis_barang, b.nomor_seri,
               b.kondisi, l.nama_lokasi AS lokasi_barang,
               p.perbaikan_id, p.tanggal_lapor, p.unit_melapor,
               u.nama_lokasi AS unit_melapor_nama, p.deskripsi_kerusakan
        FROM tb_barang b
        JOIN tb_perbaikan_barang p ON p.barang_id = b.barang_id
        LEFT JOIN tb_lokasi l ON b.lokasi_id = l.lokasi_id
        LEFT JOIN tb_lokasi u ON p.unit_melapor = u.lokasi_id
        {$where_sql}
        ORDER BY p.tanggal_lapor DESC";

$result = mysqli_query($config, $sql);

excel_start('data_barang_rusak_admin_' . date('Ymd_His') . '.xls', 'Data Barang Rusak');
?>
<tr>
    <th>No</th>
    <th>ID Perbaikan</th>
    <th>ID Barang</th>
    <th>Kode Inventaris</th>
    <th>Nama Barang</th>
    <th>Jenis Barang</th>
    <th>No. Seri / SN</th>
    <th>Lokasi Barang</th>
    <th>Unit Melapor</th>
    <th>Tanggal Lapor</th>
    <th>Kondisi</th>
    <th>Deskripsi Kerusakan</th>
</tr>
<?php
$no = 1;
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>' . $no++ . '</td>';
        echo '<td class="text">' . excel_text($row['perbaikan_id']) . '</td>';
        echo '<td class="text">' . excel_text($row['barang_id']) . '</td>';
        echo '<td class="text">' . excel_text($row['kode_inventaris']) . '</td>';
        echo '<td>' . excel_text($row['nama_barang']) . '</td>';
        echo '<td>' . excel_text($row['jenis_barang']) . '</td>';
        echo '<td class="text">' . excel_text($row['nomor_seri']) . '</td>';
        echo '<td>' . excel_text($row['lokasi_barang']) . '</td>';
        echo '<td>' . excel_text($row['unit_melapor_nama'] ?: $row['unit_melapor']) . '</td>';
        echo '<td>' . excel_text(excel_date($row['tanggal_lapor'])) . '</td>';
        echo '<td>' . excel_text($row['kondisi']) . '</td>';
        echo '<td>' . excel_text($row['deskripsi_kerusakan']) . '</td>';
        echo '</tr>';
    }
}
excel_end();
