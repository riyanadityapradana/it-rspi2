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
