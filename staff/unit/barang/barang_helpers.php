<?php

if (!function_exists('barang_get_jenis_options')) {
    function barang_get_jenis_options()
    {
        return [
            'Komputer & Laptop',
            'Komponen Komputer & Laptop',
            'Printer & Scanner',
            'Komponen Printer & Scanner',
            'Kamera & Aksesoris',
            'Komponen Network',
            'CCTV & Keamanan',
            'Perangkat Mobile',
            'Aksesoris Perangkat Mobile'
        ];
    }
}

if (!function_exists('barang_get_kondisi_options')) {
    function barang_get_kondisi_options()
    {
        return [
            'Baik',
            'Baru',
            'Bekas',
            'Rusak',
            'Dalam Perbaikan'
        ];
    }
}

if (!function_exists('barang_normalize_jenis')) {
    function barang_normalize_jenis($jenis, $default = '')
    {
        $value = trim((string) $jenis);
        if ($value === '') {
            return $default;
        }

        foreach (barang_get_jenis_options() as $option) {
            if (strcasecmp($option, $value) === 0) {
                return $option;
            }
        }

        return $default;
    }
}
if (!function_exists('barang_normalize_kondisi')) {
    function barang_normalize_kondisi($kondisi, $default = '')
    {
        $value = trim((string) $kondisi);
        if ($value === '') {
            return $default;
        }

        $normalized = strtolower($value);
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        switch ($normalized) {
            case 'baik':
                return 'Baik';
            case 'baru':
                return 'Baru';
            case 'bekas':
                return 'Bekas';
            case 'rusak':
                return 'Rusak';
            case 'dalam perbaikan':
                return 'Dalam Perbaikan';
            default:
                return $value;
        }
    }
}

if (!function_exists('barang_get_badge_class')) {
    function barang_get_badge_class($kondisi)
    {
        $normalized = strtolower(trim((string) $kondisi));
        if ($normalized === 'baik' || $normalized === 'baru') {
            return 'success';
        }
        if ($normalized === 'bekas') {
            return 'secondary';
        }
        if ($normalized === 'rusak') {
            return 'danger';
        }
        if (strpos($normalized, 'perbaikan') !== false) {
            return 'warning';
        }
        return 'secondary';
    }
}

if (!function_exists('barang_get_unit_kode_options')) {
    function barang_get_unit_kode_options()
    {
        return [
            'TU' => 'TU',
            'IT' => 'IT',
            'FAR' => 'Farmasi'
        ];
    }
}

if (!function_exists('barang_get_lokasi_kode')) {
    function barang_get_lokasi_kode($nama_lokasi)
    {
        $nama_lokasi = trim((string) $nama_lokasi);
        $normalized = strtolower($nama_lokasi);

        if ($normalized === 'it') {
            return 'IT';
        }
        if ($normalized === 'tu') {
            return 'TU';
        }
        if (strpos($normalized, 'farmasi') !== false) {
            return 'FAR';
        }
        if ($normalized === 'lab') {
            return 'LAB';
        }
        if ($normalized === 'igd') {
            return 'IGD';
        }
        if ($normalized === 'hd') {
            return 'HD';
        }
        if ($normalized === 'ponek') {
            return 'PONEK';
        }

        $clean = preg_replace('/\([^)]*\)/', '', $nama_lokasi);
        $clean = preg_replace('/[^A-Za-z0-9\s]/', ' ', $clean);
        $words = preg_split('/\s+/', trim($clean));
        $kode = '';

        foreach ($words as $word) {
            if ($word === '') {
                continue;
            }
            $kode .= strtoupper(substr($word, 0, 1));
        }

        if ($kode === '') {
            $kode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $nama_lokasi), 0, 5));
        }

        return $kode;
    }
}

if (!function_exists('barang_get_roman_month')) {
    function barang_get_roman_month($month)
    {
        $months = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];

        $month = intval($month);
        return isset($months[$month]) ? $months[$month] : '';
    }
}

if (!function_exists('barang_get_kode_inventaris_reference')) {
    function barang_get_kode_inventaris_reference($config, $unit_kode, $tanggal)
    {
        $unit_kode = strtoupper(trim((string) $unit_kode));
        $timestamp = strtotime($tanggal ?: date('Y-m-d'));
        if ($unit_kode === '' || $timestamp === false) {
            return [
                'last' => '-',
                'next' => '-',
                'prefix' => '',
                'last_number' => 0
            ];
        }

        $month_roman = barang_get_roman_month(date('n', $timestamp));
        $year = date('Y', $timestamp);
        $suffix = '/LOG/' . $unit_kode . '/' . $month_roman . '/' . $year;
        $unit_kode_sql = mysqli_real_escape_string($config, $unit_kode);

        $query = mysqli_query(
            $config,
            "SELECT kode_inventaris FROM tb_barang WHERE kode_inventaris LIKE '%/LOG/{$unit_kode_sql}/%'"
        );

        $last_number = 0;
        $last_code = '-';
        if ($query) {
            while ($row = mysqli_fetch_assoc($query)) {
                $kode = trim((string) $row['kode_inventaris']);
                if (preg_match('/^(\d+)\/LOG\/' . preg_quote($unit_kode, '/') . '\/[IVXLCDM]+\/\d{4}$/i', $kode, $matches)) {
                    $number = intval($matches[1]);
                    if ($number > $last_number) {
                        $last_number = $number;
                        $last_code = $kode;
                    }
                }
            }
        }

        $next_number = $last_number + 1;
        $next_code = str_pad($next_number, 3, '0', STR_PAD_LEFT) . $suffix;

        return [
            'last' => $last_code,
            'next' => $next_code,
            'prefix' => $suffix,
            'last_number' => $last_number
        ];
    }
}

if (!function_exists('barang_sync_snapshot')) {
    function barang_sync_snapshot($config, $barang_id, array $overrides = [])
    {
        $barang_id = intval($barang_id);
        if ($barang_id <= 0) {
            return false;
        }

        $barang_q = mysqli_query($config, "SELECT lokasi_id, kondisi FROM tb_barang WHERE barang_id='{$barang_id}' LIMIT 1");
        if (!$barang_q || mysqli_num_rows($barang_q) === 0) {
            return false;
        }

        $barang = mysqli_fetch_assoc($barang_q);
        $lokasi_id = isset($barang['lokasi_id']) ? intval($barang['lokasi_id']) : 0;
        $kondisi = barang_normalize_kondisi(isset($barang['kondisi']) ? $barang['kondisi'] : '', '');

        $penyerahan_q = mysqli_query(
            $config,
            "SELECT lokasi_id, kondisi FROM tb_penyerahan WHERE barang_id='{$barang_id}' ORDER BY penyerahan_id DESC LIMIT 1"
        );
        if ($penyerahan_q && mysqli_num_rows($penyerahan_q) > 0) {
            $penyerahan = mysqli_fetch_assoc($penyerahan_q);
            if (!empty($penyerahan['lokasi_id'])) {
                $lokasi_id = intval($penyerahan['lokasi_id']);
            }
            $kondisi_penyerahan = barang_normalize_kondisi(isset($penyerahan['kondisi']) ? $penyerahan['kondisi'] : '', '');
            if ($kondisi_penyerahan !== '') {
                $kondisi = $kondisi_penyerahan;
            }
        }

        if (array_key_exists('lokasi_id', $overrides) && $overrides['lokasi_id'] !== null && intval($overrides['lokasi_id']) > 0) {
            $lokasi_id = intval($overrides['lokasi_id']);
        }

        if (array_key_exists('kondisi', $overrides)) {
            $kondisi_override = barang_normalize_kondisi($overrides['kondisi'], '');
            if ($kondisi_override !== '') {
                $kondisi = $kondisi_override;
            }
        }

        $fields = [];
        if ($lokasi_id > 0) {
            $fields[] = "lokasi_id='{$lokasi_id}'";
        }
        if ($kondisi !== '') {
            $kondisi_sql = mysqli_real_escape_string($config, $kondisi);
            $fields[] = "kondisi='{$kondisi_sql}'";
        }

        if (empty($fields)) {
            return true;
        }

        return mysqli_query($config, "UPDATE tb_barang SET " . implode(', ', $fields) . " WHERE barang_id='{$barang_id}'");
    }
}
