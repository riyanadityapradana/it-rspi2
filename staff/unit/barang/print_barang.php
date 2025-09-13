
<?php
require_once("../../../config/koneksi.php");

// Ambil parameter dari form
$kondisi = $_GET['kondisi'] ?? 'baik';
$lokasi_filter = $_GET['lokasi_filter'] ?? 'unit_it';
$tahun = $_GET['tahun'] ?? date('Y');

$kondisi_db = strtolower($kondisi) == 'baik' ? 'baru' : (strtolower($kondisi) == 'rusak' ? 'rusak' : $kondisi);
$where = "WHERE YEAR(b.tanggal_terima) = '$tahun' AND b.kondisi = '$kondisi_db'";
if ($lokasi_filter == 'unit_it') {
    $lokasiIT = [];
    $lokasiQ = mysqli_query($config, "SELECT lokasi_id FROM tb_lokasi WHERE LOWER(nama_lokasi) LIKE '%it%'");
    while ($rowLokasi = mysqli_fetch_assoc($lokasiQ)) {
        $lokasiIT[] = $rowLokasi['lokasi_id'];
    }
    if (!empty($lokasiIT)) {
        $where .= " AND b.lokasi_id IN (" . implode(',', $lokasiIT) . ")";
    }
}
$query = "SELECT b.*, l.nama_lokasi FROM tb_barang b LEFT JOIN tb_lokasi l ON b.lokasi_id = l.lokasi_id $where ORDER BY b.barang_id ASC";
$result = mysqli_query($config, $query);

// Hapus pengambilan data ke array agar tidak menghabiskan hasil query

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Barang</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            font-size: 13px;
        }
        .container {
            max-width: 1100px;
            margin: 30px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            padding: 32px 28px 24px 28px;
        }
        .header {
            text-align: center;
            margin-bottom: 24px;
            background: linear-gradient(90deg, #1976d2 0%, #64b5f6 100%);
            color: #fff;
            border-radius: 12px 12px 0 0;
            padding: 18px 0 10px 0;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            letter-spacing: 1px;
        }
        .header p {
            margin: 6px 0;
            font-size: 13px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
            background: #fafafa;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            border: 1px solid #90caf9;
            padding: 10px 7px;
            text-align: left;
            font-size: 12px;
        }
        th {
            background: linear-gradient(90deg, #1976d2 0%, #64b5f6 100%);
            color: #fff;
            font-weight: bold;
            border-bottom: 2px solid #1976d2;
        }
        tr:nth-child(even) {
            background: #e3f2fd;
        }
        .status-baik {
            color: #388e3c;
            font-weight: bold;
        }
        .status-rusak {
            color: #d32f2f;
            font-weight: bold;
        }
        .footer {
            margin-top: 32px;
            text-align: right;
            color: #1976d2;
            font-size: 13px;
        }
        .no-print {
            margin-top: 24px;
            text-align: center;
        }
        .btn {
            background: linear-gradient(90deg, #1976d2 0%, #64b5f6 100%);
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 8px 18px;
            font-size: 13px;
            cursor: pointer;
            margin: 0 6px;
            box-shadow: 0 2px 6px rgba(25,118,210,0.08);
            transition: background 0.2s;
        }
        .btn:hover {
            background: linear-gradient(90deg, #1565c0 0%, #42a5f5 100%);
        }
        @media print {
            body { margin: 0; background: #000000ff; }
            .container { box-shadow: none; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <script>
        // Auto print saat halaman dimuat
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 1000); // Delay 1 detik agar halaman dan chart selesai dimuat
        };
    </script>
    <div class="container">
        <div class="header">
            <h1 style="color: #000000ff;"><i class="fa fa-print"></i> LAPORAN DATA BARANG</h1>
            <p style="color: #000000ff;">Periode: <b><?= htmlspecialchars($tahun) ?></b></p>
            <p style="color: #000000ff;">Filter: <span style="background:#fff; color:#1976d2; padding:2px 8px; border-radius:6px; font-weight:bold;">Kondisi: <?= htmlspecialchars(ucwords($kondisi)) ?></span>,
            <span style="background:#fff; color:#1976d2; padding:2px 8px; border-radius:6px; font-weight:bold;">Lokasi: <?= $lokasi_filter == 'unit_it' ? 'Unit IT Saja' : 'Semua Unit' ?></span></p>
        </div>
        <table style="color: #000000ff;">
            <thead>
                <tr>
                    <th style="color: #000000ff;">No</th>
                    <th style="color: #000000ff;">Nama Barang</th>
                    <th style="color: #000000ff;">Jenis Barang</th>
                    <th style="color: #000000ff;">Nomor Seri</th>
                    <th style="color: #000000ff;">Jumlah</th>
                    <th style="color: #000000ff;">Harga</th>
                    <th style="color: #000000ff;">Spesifikasi</th>
                    <th style="color: #000000ff;">Tanggal Terima</th>
                    <th style="color: #000000ff;">Kondisi</th>
                    <th style="color: #000000ff;">Lokasi</th>
                    <th style="color: #000000ff;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)): 
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                    <td><?= htmlspecialchars($row['jenis_barang']) ?></td>
                    <td><?= htmlspecialchars($row['nomor_seri']) ?></td>
                    <td><?= htmlspecialchars($row['jumlah']) ?></td>
                    <td>Rp. <?= number_format($row['harga'],0,',','.') ?></td>
                    <td><?= htmlspecialchars($row['spesifikasi']) ?></td>
                    <td><?= !empty($row['tanggal_terima']) ? date('d/m/Y', strtotime($row['tanggal_terima'])) : '-' ?></td>
                    <td class="<?= $row['kondisi'] == 'baru' ? 'status-baik' : ($row['kondisi'] == 'rusak' ? 'status-rusak' : '') ?>">
                        <?= htmlspecialchars(ucwords($row['kondisi'])) ?>
                    </td>
                    <td><?= htmlspecialchars($row['nama_lokasi']) ?></td>
                    <td><?= htmlspecialchars($row['keterangan']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="footer">
            <p style="color: #000000ff;">Dicetak pada: <?= date('d/m/Y H:i:s') ?></p>
            <p style="color: #000000ff;">Total Data: <?= $no-1 ?> barang</p>
        </div>
        <div class="no-print">
            <button class="btn" onclick="window.print()"><i class="fa fa-print"></i> Cetak</button>
            <button class="btn" onclick="window.close()"><i class="fa fa-times"></i> Tutup</button>
        </div>
    </div>
</body>
</html> 