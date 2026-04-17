
<?php
require_once("../../../config/koneksi.php");

// Ambil parameter dari form
$kondisi = $_GET['kondisi'] ?? 'Baru';
$lokasi_filter = $_GET['lokasi_filter'] ?? 'unit_it';
$tahun = $_GET['tahun'] ?? date('Y');

$allowed_kondisi = ['baru', 'bekas', 'rusak', 'dalam perbaikan'];
$kondisi_db = strtolower(trim($kondisi));
if (!in_array($kondisi_db, $allowed_kondisi, true)) {
    $kondisi_db = 'baru';
}

// Build WHERE clause
$where_conditions = ["YEAR(b.tanggal_terima) = '$tahun'"];

// Filter kondisi dari tb_penyerahan
$where_conditions[] = "p.kondisi = '$kondisi_db'";

// Filter lokasi
if ($lokasi_filter == 'unit_it') {
    $lokasiIT = [];
    $lokasiQ = mysqli_query($config, "SELECT lokasi_id FROM tb_lokasi WHERE LOWER(nama_lokasi) LIKE '%it%'");
    while ($rowLokasi = mysqli_fetch_assoc($lokasiQ)) {
        $lokasiIT[] = $rowLokasi['lokasi_id'];
    }
    if (!empty($lokasiIT)) {
        $where_conditions[] = "p.lokasi_id IN (" . implode(',', $lokasiIT) . ")";
    }
}

$where = "WHERE " . implode(" AND ", $where_conditions);

$query = "SELECT DISTINCT b.barang_id, b.kode_inventaris, b.nama_barang, b.jenis_barang, b.nomor_seri, b.jumlah, b.spesifikasi, b.tanggal_terima, p.kondisi, l.nama_lokasi, p.keterangan 
FROM tb_barang b 
LEFT JOIN tb_penyerahan p ON b.barang_id = p.barang_id 
LEFT JOIN tb_lokasi l ON p.lokasi_id = l.lokasi_id 
$where 
ORDER BY b.barang_id ASC";
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
            background: #ffffff;
            font-size: 13px;
            color: #000;
        }
        .container {
            max-width: 1100px;
            margin: 30px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            padding: 32px 28px 24px 28px;
        }
        .kop-surat {
            display: table;
            width: 100%;
            border-bottom: 3px solid #000;
            padding-bottom: 12px;
            margin-bottom: 22px;
        }
        .kop-logo,
        .kop-text {
            display: table-cell;
            vertical-align: top;
        }
        .kop-logo {
            width: 170px;
        }
        .kop-logo img {
            width: 140px;
            height: auto;
        }
        .kop-text {
            text-align: center;
            padding-right: 60px;
        }
        .kop-text h1,
        .kop-text h2,
        .kop-text p {
            margin: 0;
        }
        .kop-text h1 {
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .kop-text h2 {
            font-size: 26px;
            font-weight: 700;
            margin-top: 2px;
        }
        .kop-text .sub {
            font-size: 13px;
            margin-top: 4px;
        }
        .kop-text .addr {
            font-size: 12px;
            margin-top: 3px;
            line-height: 1.45;
        }
        .report-title {
            text-align: center;
            margin-bottom: 18px;
        }
        .report-title h3 {
            margin: 0;
            font-size: 20px;
            letter-spacing: 0.8px;
        }
        .report-title p {
            margin: 6px 0 0;
            font-size: 12px;
        }
        .filter-info {
            background: #f5f5f5;
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 12px;
            font-size: 12px;
            border: 1px solid #d0d7de;
        }
        .filter-badge {
            display: inline-block;
            background: #fff;
            color: #1976d2;
            padding: 2px 8px;
            border-radius: 6px;
            font-weight: bold;
            margin-right: 8px;
            border: 1px solid #c7d8ef;
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
        .status-bekas {
            color: #616161;
            font-weight: bold;
        }
        .status-rusak {
            color: #d32f2f;
            font-weight: bold;
        }
        .status-perbaikan {
            color: #f57c00;
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
            body { margin: 0; background: #ffffff; }
            .container { box-shadow: none; padding: 0; margin: 0; max-width: none; }
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
        <div class="kop-surat">
            <div class="kop-logo">
                <img src="../../../assets/img/logo.jpg" alt="Logo RSPI">
            </div>
            <div class="kop-text">
                <h1>PT. PELITA INSANI MULIA</h1>
                <h2>RUMAH SAKIT PELITA INSANI MARTAPURA</h2>
                <p class="sub">Terakreditasi KARS Versi SNARS Edisi 1 Tingkat Madya</p>
                <p class="addr">Jl. Sekumpul No. 66 Martapura - Telp. (0511) 4722210, 4722220, Kalimantan Selatan</p>
                <p class="addr">Fax. (0511) 4722230, Emergency Call (0511) 4722222, Email: rs.pelitainsani@gmail.com</p>
                <p class="addr">Website: www.pelitainsani.com</p>
            </div>
        </div>

        <div class="report-title">
            <h3>LAPORAN DATA BARANG</h3>
            <p>Periode Tahun <?= htmlspecialchars($tahun) ?></p>
        </div>

        <div class="filter-info">
            <span class="filter-badge">Kondisi: <?= htmlspecialchars(ucwords($kondisi)) ?></span>
            <span class="filter-badge">Lokasi: <?= $lokasi_filter == 'unit_it' ? 'Unit IT Saja' : 'Semua Unit' ?></span>
        </div>
        <table style="color: #000000ff;">
            <thead>
                <tr>
                    <th style="color: #000000ff;">No</th>
                    <th style="color: #000000ff;">Kode Inventaris</th>
                    <th style="color: #000000ff;">Nama Barang</th>
                    <th style="color: #000000ff;">Jenis Barang</th>
                    <th style="color: #000000ff;">Nomor Seri</th>
                    <th style="color: #000000ff;">Jumlah</th>
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
                    <td><?= htmlspecialchars($row['kode_inventaris']) ?></td>
                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                    <td><?= htmlspecialchars($row['jenis_barang']) ?></td>
                    <td><?= htmlspecialchars($row['nomor_seri']) ?></td>
                    <td><?= htmlspecialchars($row['jumlah']) ?></td>
                    <td><?= htmlspecialchars($row['spesifikasi']) ?></td>
                    <td><?= !empty($row['tanggal_terima']) ? date('d/m/Y', strtotime($row['tanggal_terima'])) : '-' ?></td>
                    <td class="<?php
                        $kondisi_row = strtolower(trim($row['kondisi']));
                        if ($kondisi_row === 'baru') {
                            echo 'status-baik';
                        } elseif ($kondisi_row === 'bekas') {
                            echo 'status-bekas';
                        } elseif ($kondisi_row === 'rusak') {
                            echo 'status-rusak';
                        } elseif ($kondisi_row === 'dalam perbaikan') {
                            echo 'status-perbaikan';
                        }
                    ?>">
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
