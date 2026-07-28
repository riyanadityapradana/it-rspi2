<?php
ob_start();
require_once('../../../config/koneksi.php');
require_once(__DIR__ . '/../export_excel_helpers.php');

$conditions = [];
if (!empty($_GET['tindakan'])) {
    $t_norm = strtolower(str_replace(' ', '_', $_GET['tindakan']));
    $t_norm = mysqli_real_escape_string($config, $t_norm);
    $conditions[] = "LOWER(REPLACE(p.tindakan_perbaikan, ' ', '_')) = '{$t_norm}'";
}
if (!empty($_GET['status'])) {
    $status = mysqli_real_escape_string($config, $_GET['status']);
    $conditions[] = "p.status = '{$status}'";
}
if (!empty($_GET['tanggal_mulai'])) {
    $tanggal_mulai = mysqli_real_escape_string($config, $_GET['tanggal_mulai']);
    $conditions[] = "DATE(p.tanggal_lapor) >= '{$tanggal_mulai}'";
}
if (!empty($_GET['tanggal_selesai'])) {
    $tanggal_selesai = mysqli_real_escape_string($config, $_GET['tanggal_selesai']);
    $conditions[] = "DATE(p.tanggal_lapor) <= '{$tanggal_selesai}'";
}

$where_sql = count($conditions) ? ' WHERE ' . implode(' AND ', $conditions) : '';
$sql = "SELECT p.perbaikan_id, p.barang_id, p.tanggal_lapor, p.deskripsi_kerusakan,
               p.tindakan_perbaikan, p.status, p.tanggal_selesai, p.teknisi,
               p.keterangan, p.bukti_struk, p.unit_melapor,
               b.kode_inventaris, b.nama_barang, b.jenis_barang, b.nomor_seri,
               l.nama_lokasi AS lokasi_barang,
               u.nama_lokasi AS unit_melapor_nama
        FROM tb_perbaikan_barang p
        JOIN tb_barang b ON p.barang_id = b.barang_id
        LEFT JOIN tb_lokasi l ON b.lokasi_id = l.lokasi_id
        LEFT JOIN tb_lokasi u ON p.unit_melapor = u.lokasi_id
        {$where_sql}
        ORDER BY b.barang_id, p.tanggal_lapor DESC";

$result = mysqli_query($config, $sql);

excel_start('data_perbaikan_admin_' . date('Ymd_His') . '.xls', 'Data Perbaikan Barang');
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
    <th>Tindakan</th>
    <th>Status</th>
    <th>Teknisi</th>
    <th>Tanggal Selesai</th>
    <th>Deskripsi Kerusakan</th>
    <th>Keterangan</th>
    <th>Bukti Struk</th>
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
        echo '<td>' . excel_text($row['tindakan_perbaikan']) . '</td>';
        echo '<td>' . excel_text($row['status']) . '</td>';
        echo '<td>' . excel_text($row['teknisi']) . '</td>';
        echo '<td>' . excel_text(excel_date($row['tanggal_selesai'])) . '</td>';
        echo '<td>' . excel_text($row['deskripsi_kerusakan']) . '</td>';
        echo '<td>' . excel_text($row['keterangan']) . '</td>';
        echo '<td>' . excel_text($row['bukti_struk']) . '</td>';
        echo '</tr>';
    }
}
excel_end();
