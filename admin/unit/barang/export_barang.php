<?php
ob_start();
require_once('../../../config/koneksi.php');
require_once(__DIR__ . '/../export_excel_helpers.php');

$jenis_filter = trim((string)($_GET['jenis'] ?? ''));
$kondisi_filter = trim((string)($_GET['kondisi'] ?? ''));

$where = [];
if ($jenis_filter !== '') {
    $jenis = mysqli_real_escape_string($config, $jenis_filter);
    $where[] = "b.jenis_barang = '{$jenis}'";
}
if ($kondisi_filter !== '') {
    $kondisi = mysqli_real_escape_string($config, $kondisi_filter);
    $where[] = "LOWER(b.kondisi) = LOWER('{$kondisi}')";
}

$where_sql = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';
$sql = "SELECT b.barang_id, b.kode_inventaris, b.nama_barang, b.jenis_barang, b.nomor_seri,
               b.ip_address, b.jumlah, b.spesifikasi, b.tanggal_terima, b.kondisi, b.keterangan,
               ls.nama_lokasi AS lokasi_saat_ini,
               lp.nama_lokasi AS lokasi_penyerahan,
               p.kondisi AS kondisi_penyerahan,
               p.keterangan AS keterangan_penyerahan
        FROM tb_barang b
        LEFT JOIN tb_lokasi ls ON b.lokasi_id = ls.lokasi_id
        LEFT JOIN tb_penyerahan p ON p.penyerahan_id = (
            SELECT p2.penyerahan_id
            FROM tb_penyerahan p2
            WHERE p2.barang_id = b.barang_id
            ORDER BY p2.penyerahan_id DESC
            LIMIT 1
        )
        LEFT JOIN tb_lokasi lp ON p.lokasi_id = lp.lokasi_id
        {$where_sql}
        ORDER BY b.barang_id DESC";

$result = mysqli_query($config, $sql);

excel_start('data_barang_admin_' . date('Ymd_His') . '.xls', 'Data Barang');
?>
<tr>
    <th>No</th>
    <th>ID Barang</th>
    <th>Kode Inventaris</th>
    <th>Nama Barang</th>
    <th>Jenis Barang</th>
    <th>No. Seri / SN</th>
    <th>IP Address</th>
    <th>Jumlah</th>
    <th>Spesifikasi</th>
    <th>Tanggal Terima</th>
    <th>Kondisi</th>
    <th>Lokasi Saat Ini</th>
    <th>Lokasi Penyerahan Terakhir</th>
    <th>Kondisi Penyerahan</th>
    <th>Keterangan</th>
</tr>
<?php
$no = 1;
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $keterangan = $row['keterangan_penyerahan'] ?: $row['keterangan'];
        echo '<tr>';
        echo '<td>' . $no++ . '</td>';
        echo '<td class="text">' . excel_text($row['barang_id']) . '</td>';
        echo '<td class="text">' . excel_text($row['kode_inventaris']) . '</td>';
        echo '<td>' . excel_text($row['nama_barang']) . '</td>';
        echo '<td>' . excel_text($row['jenis_barang']) . '</td>';
        echo '<td class="text">' . excel_text($row['nomor_seri']) . '</td>';
        echo '<td class="text">' . excel_text($row['ip_address']) . '</td>';
        echo '<td>' . excel_text($row['jumlah']) . '</td>';
        echo '<td>' . excel_text($row['spesifikasi']) . '</td>';
        echo '<td>' . excel_text(excel_date($row['tanggal_terima'], 'd-m-Y')) . '</td>';
        echo '<td>' . excel_text($row['kondisi']) . '</td>';
        echo '<td>' . excel_text($row['lokasi_saat_ini']) . '</td>';
        echo '<td>' . excel_text($row['lokasi_penyerahan']) . '</td>';
        echo '<td>' . excel_text($row['kondisi_penyerahan']) . '</td>';
        echo '<td>' . excel_text($keterangan) . '</td>';
        echo '</tr>';
    }
}
excel_end();
