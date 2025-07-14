<?php
$unit = isset($_GET['unit']) ? basename($_GET['unit']) : 'beranda';
$unit_file = __DIR__ . "/unit/{$unit}.php";
if (file_exists($unit_file)) {
    include_once $unit_file;
} else {
    echo '<div class="alert alert-danger">Halaman tidak ditemukan.</div>';
} 