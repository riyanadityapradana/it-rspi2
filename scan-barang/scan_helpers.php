<?php

if (ob_get_level() === 0) {
    ob_start();
}

require_once(__DIR__ . '/../config/koneksi.php');
require_once(__DIR__ . '/../staff/unit/barang/barang_helpers.php');

if (!function_exists('scan_json_response')) {
    function scan_json_response($payload, $status = 200)
    {
        if (ob_get_level() > 0 && ob_get_length() > 0) {
            ob_clean();
        }
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit;
    }
}

if (!function_exists('scan_required_pin')) {
    function scan_required_pin()
    {
        return trim((string) getenv('SCAN_BARANG_PIN'));
    }
}

if (!function_exists('scan_validate_pin')) {
    function scan_validate_pin($pin)
    {
        $required = scan_required_pin();
        if ($required === '') {
            return true;
        }
        return hash_equals($required, (string) $pin);
    }
}

if (!function_exists('scan_require_pin')) {
    function scan_require_pin()
    {
        $pin = isset($_POST['pin']) ? $_POST['pin'] : (isset($_GET['pin']) ? $_GET['pin'] : '');
        if (!scan_validate_pin($pin)) {
            scan_json_response(array(
                'success' => false,
                'message' => 'PIN tidak valid'
            ), 403);
        }
    }
}

if (!function_exists('scan_escape')) {
    function scan_escape($config, $value)
    {
        return mysqli_real_escape_string($config, trim((string) $value));
    }
}

if (!function_exists('scan_normalize_datetime')) {
    function scan_normalize_datetime($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return date('Y-m-d H:i:s');
        }
        $value = str_replace('T', ' ', $value);
        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return date('Y-m-d H:i:s');
        }
        return date('Y-m-d H:i:s', $timestamp);
    }
}

if (!function_exists('scan_count_barang_by_kode')) {
    function scan_count_barang_by_kode($config, $kode)
    {
        $kode_sql = scan_escape($config, $kode);
        $result = mysqli_query($config, "SELECT COUNT(*) AS total FROM tb_barang WHERE kode_inventaris = '{$kode_sql}'");
        if (!$result) {
            return 0;
        }
        $row = mysqli_fetch_assoc($result);
        return intval($row['total']);
    }
}

if (!function_exists('scan_get_barang_by_kode')) {
    function scan_get_barang_by_kode($config, $kode)
    {
        $kode_sql = scan_escape($config, $kode);
        $sql = "SELECT b.barang_id, b.kode_inventaris, b.nama_barang, b.jenis_barang, b.nomor_seri, b.ip_address, b.jumlah, b.spesifikasi, b.kondisi, b.tanggal_terima, b.lokasi_id,
            l.nama_lokasi AS lokasi_saat_ini,
            p.penyerahan_id AS last_penyerahan_id,
            p.lokasi_id AS last_penyerahan_lokasi_id,
            lp.nama_lokasi AS last_penyerahan_lokasi
            FROM tb_barang b
            LEFT JOIN tb_lokasi l ON l.lokasi_id = b.lokasi_id
            LEFT JOIN tb_penyerahan p ON p.penyerahan_id = (
                SELECT p2.penyerahan_id FROM tb_penyerahan p2
                WHERE p2.barang_id = b.barang_id
                ORDER BY p2.penyerahan_id DESC
                LIMIT 1
            )
            LEFT JOIN tb_lokasi lp ON lp.lokasi_id = p.lokasi_id
            WHERE b.kode_inventaris = '{$kode_sql}'
            LIMIT 1";

        $result = mysqli_query($config, $sql);
        if (!$result || mysqli_num_rows($result) === 0) {
            return null;
        }
        return mysqli_fetch_assoc($result);
    }
}

if (!function_exists('scan_get_barang_by_id')) {
    function scan_get_barang_by_id($config, $barang_id)
    {
        $barang_id = intval($barang_id);
        if ($barang_id <= 0) {
            return null;
        }
        $sql = "SELECT b.barang_id, b.kode_inventaris, b.nama_barang, b.jenis_barang, b.nomor_seri, b.kondisi, b.lokasi_id,
            l.nama_lokasi AS lokasi_saat_ini,
            p.penyerahan_id AS last_penyerahan_id,
            p.lokasi_id AS last_penyerahan_lokasi_id,
            lp.nama_lokasi AS last_penyerahan_lokasi
            FROM tb_barang b
            LEFT JOIN tb_lokasi l ON l.lokasi_id = b.lokasi_id
            LEFT JOIN tb_penyerahan p ON p.penyerahan_id = (
                SELECT p2.penyerahan_id FROM tb_penyerahan p2
                WHERE p2.barang_id = b.barang_id
                ORDER BY p2.penyerahan_id DESC
                LIMIT 1
            )
            LEFT JOIN tb_lokasi lp ON lp.lokasi_id = p.lokasi_id
            WHERE b.barang_id = '{$barang_id}'
            LIMIT 1";

        $result = mysqli_query($config, $sql);
        if (!$result || mysqli_num_rows($result) === 0) {
            return null;
        }
        return mysqli_fetch_assoc($result);
    }
}

if (!function_exists('scan_get_barang_by_identifier')) {
    function scan_get_barang_by_identifier($config, $identifier)
    {
        $identifier = trim((string) $identifier);
        if ($identifier === '') {
            return array('barang' => null, 'error' => 'Kode / ID barang kosong');
        }

        if (preg_match('/^\d+$/', $identifier)) {
            $barang = scan_get_barang_by_id($config, intval($identifier));
            if ($barang) {
                return array('barang' => $barang, 'lookup' => 'barang_id');
            }
        }

        $total = scan_count_barang_by_kode($config, $identifier);
        if ($total > 1) {
            return array(
                'barang' => null,
                'error' => 'Kode barang ditemukan lebih dari satu. Gunakan ID barang atau perbaiki kode inventaris terlebih dahulu.',
                'status' => 409
            );
        }

        $barang = scan_get_barang_by_kode($config, $identifier);
        if ($barang) {
            return array('barang' => $barang, 'lookup' => 'kode_inventaris');
        }

        return array('barang' => null, 'error' => 'Barang tidak ditemukan', 'status' => 404);
    }
}

if (!function_exists('scan_public_note')) {
    function scan_public_note($petugas, $keterangan)
    {
        $parts = array();
        $petugas = trim((string) $petugas);
        $keterangan = trim((string) $keterangan);
        if ($petugas !== '') {
            $parts[] = 'Petugas: ' . $petugas;
        }
        if ($keterangan !== '') {
            $parts[] = $keterangan;
        }
        return '[Scan Publik] ' . implode(' - ', $parts);
    }
}
