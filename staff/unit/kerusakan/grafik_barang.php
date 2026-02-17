<?php
// Include database configuration
include '../config/koneksi.php';

// Query untuk mendapatkan 10 unit dengan barang rusak terbanyak
$query = "SELECT 
    l.lokasi_id,
    l.nama_lokasi,
    COUNT(b.barang_id) as jumlah_rusak
FROM tb_barang b
JOIN tb_lokasi l ON b.lokasi_id = l.lokasi_id
WHERE b.kondisi = 'rusak'
GROUP BY l.lokasi_id, l.nama_lokasi
ORDER BY jumlah_rusak DESC
LIMIT 10";

$result = mysqli_query($config, $query);

$labels = [];
$data = [];
$colors = [
    'rgba(255, 99, 132, 0.8)',
    'rgba(54, 162, 235, 0.8)',
    'rgba(255, 206, 86, 0.8)',
    'rgba(75, 192, 192, 0.8)',
    'rgba(153, 102, 255, 0.8)',
    'rgba(255, 159, 64, 0.8)',
    'rgba(199, 199, 199, 0.8)',
    'rgba(83, 102, 255, 0.8)',
    'rgba(255, 99, 255, 0.8)',
    'rgba(99, 255, 132, 0.8)'
];

$borderColors = [
    'rgba(255, 99, 132, 1)',
    'rgba(54, 162, 235, 1)',
    'rgba(255, 206, 86, 1)',
    'rgba(75, 192, 192, 1)',
    'rgba(153, 102, 255, 1)',
    'rgba(255, 159, 64, 1)',
    'rgba(199, 199, 199, 1)',
    'rgba(83, 102, 255, 1)',
    'rgba(255, 99, 255, 1)',
    'rgba(99, 255, 132, 1)'
];

$i = 0;
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $labels[] = $row['nama_lokasi'];
        $data[] = $row['jumlah_rusak'];
        $i++;
    }
}

// Siapkan data untuk JSON
$chartData = [
    'labels' => $labels,
    'datasets' => [
        [
            'label' => 'Jumlah Barang Rusak',
            'data' => $data,
            'backgroundColor' => array_slice($colors, 0, count($data)),
            'borderColor' => array_slice($borderColors, 0, count($data)),
            'borderWidth' => 2
        ]
    ]
];

// Return JSON jika diminta via AJAX
if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    header('Content-Type: application/json');
    echo json_encode($chartData);
    exit;
}
?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>GRAFIK BARANG RUSAK PER UNIT</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard_staff.php?unit=beranda">Home</a></li>
                    <li class="breadcrumb-item active">Grafik Barang Rusak</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">10 Unit dengan Barang Rusak Terbanyak</h3>
                    </div>
                    <div class="card-body">
                        <div style="position: relative; height: 400px;">
                            <canvas id="chartBarangRusak"></canvas>
                        </div>
                    </div>
                    <div class="card-footer">
                        <small class="text-muted">Data menampilkan 10 unit dengan jumlah barang rusak terbanyak</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Detail Data Barang Rusak per Unit</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead style="background: rgb(0, 123, 255);">
                                <tr>
                                    <th style="color: white;">No</th>
                                    <th style="color: white;">Nama Unit</th>
                                    <th style="color: white;">Jumlah Barang Rusak</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = mysqli_query($config, $query);
                                $no = 1;
                                if ($result) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<tr>';
                                        echo '<td>' . $no++ . '</td>';
                                        echo '<td>' . htmlspecialchars($row['nama_lokasi']) . '</td>';
                                        echo '<td style="text-align:center;"><span class="badge badge-danger">' . $row['jumlah_rusak'] . '</span></td>';
                                        echo '</tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data dari PHP
    const chartData = <?php echo json_encode($chartData); ?>;

    // Buat Chart
    const ctx = document.getElementById('chartBarangRusak').getContext('2d');
    const chartBarangRusak = new Chart(ctx, {
        type: 'bar',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    title: {
                        display: true,
                        text: 'Jumlah Barang Rusak'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Unit/Lokasi'
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                title: {
                    display: false
                }
            }
        }
    });
</script>
