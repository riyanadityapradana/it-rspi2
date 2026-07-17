<?php
ob_start();
require_once('../../../config/koneksi.php');
require_once(__DIR__ . '/../export_excel_helpers.php');

$printType = $_GET['printType'] ?? 'all';
$jenisBarang = $_GET['jenis_barang'] ?? '';
$bulan = $_GET['bulan'] ?? '';
$tahun = $_GET['tahun'] ?? '';

$where = [];
if ($bulan !== '') {
    $bulan_esc = mysqli_real_escape_string($config, $bulan);
    $where[] = "MONTH(m.tanggal_mutasi) = '{$bulan_esc}'";
}
if ($tahun !== '') {
    $tahun_esc = mysqli_real_escape_string($config, $tahun);
    $where[] = "YEAR(m.tanggal_mutasi) = '{$tahun_esc}'";
}
if ($printType === 'jenis' && $jenisBarang !== '') {
    $jenis_esc = mysqli_real_escape_string($config, $jenisBarang);
    $where[] = "b.jenis_barang = '{$jenis_esc}'";
}

$where_sql = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';
$sql = "SELECT m.mutasi_id, m.barang_id, m.tanggal_mutasi, m.keterangan,
               b.kode_inventaris, b.nama_barang, b.jenis_barang, b.nomor_seri,
               b.kondisi AS status_barang,
               l1.nama_lokasi AS lokasi_asal_nama,
               l2.nama_lokasi AS lokasi_tujuan_nama,
               u.nama_lengkap AS nama_staff
        FROM tb_mutasi_barang m
        LEFT JOIN tb_barang b ON m.barang_id = b.barang_id
        LEFT JOIN tb_lokasi l1 ON m.lokasi_asal = l1.lokasi_id
        LEFT JOIN tb_lokasi l2 ON m.lokasi_tujuan = l2.lokasi_id
        LEFT JOIN tb_user u ON m.id_user = u.id_user
        {$where_sql}
        ORDER BY m.tanggal_mutasi DESC";

$result = mysqli_query($config, $sql);

excel_start('data_pemindahan_admin_' . date('Ymd_His') . '.xls', 'Data Pemindahan Barang');
?>
<tr>
    <th>No</th>
    <th>ID Mutasi</th>
    <th>ID Barang</th>
    <th>Kode Inventaris</th>
    <th>Nama Barang</th>
    <th>Jenis Barang</th>
    <th>No. Seri / SN</th>
    <th>Status Barang</th>
    <th>Lokasi Asal</th>
    <th>Lokasi Tujuan</th>
    <th>Tanggal Mutasi</th>
    <th>Staff</th>
    <th>Keterangan</th>
</tr>
<?php
$no = 1;
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>' . $no++ . '</td>';
        echo '<td class="text">' . excel_text($row['mutasi_id']) . '</td>';
        echo '<td class="text">' . excel_text($row['barang_id']) . '</td>';
        echo '<td class="text">' . excel_text($row['kode_inventaris']) . '</td>';
        echo '<td>' . excel_text($row['nama_barang']) . '</td>';
        echo '<td>' . excel_text($row['jenis_barang']) . '</td>';
        echo '<td class="text">' . excel_text($row['nomor_seri']) . '</td>';
        echo '<td>' . excel_text($row['status_barang']) . '</td>';
        echo '<td>' . excel_text($row['lokasi_asal_nama']) . '</td>';
        echo '<td>' . excel_text($row['lokasi_tujuan_nama']) . '</td>';
        echo '<td>' . excel_text(excel_date($row['tanggal_mutasi'], 'd-m-Y')) . '</td>';
        echo '<td>' . excel_text($row['nama_staff']) . '</td>';
        echo '<td>' . excel_text($row['keterangan']) . '</td>';
        echo '</tr>';
    }
}
excel_end();
