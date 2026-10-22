<?php
// Konfigurasi terpisah: tidak membuka koneksi atau memerlukan sesi login.
foreach (is_file(__DIR__ . '/../.env') ? file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES) : [] as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) continue;
    list($key, $value) = explode('=', $line, 2);
    $key = trim($key);
    if (getenv($key) === false) putenv($key . '=' . trim(trim($value), "\"'"));
}
date_default_timezone_set(getenv('TIMEZONE') ?: 'Asia/Makassar');

function pt_connection($pegawai = false)
{
    static $connections = [];
    $key = $pegawai ? 'pegawai' : 'portal';
    if (isset($connections[$key])) return $connections[$key];
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $prefix = $pegawai ? 'PORTAL_PEGAWAI_' : 'PORTAL_DB_';
    $host = getenv($prefix . 'HOST') ?: ($pegawai ? '192.168.1.4' : (getenv('DB_SERVER') ?: '192.168.1.108'));
    $database = getenv($prefix . 'DATABASE') ?: ($pegawai ? 'sik9' : (getenv('DB_DATABASE') ?: 'it-rspi'));
    $password = getenv($prefix . 'PASSWORD');
    $db = mysqli_init();
    $db->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
    $db->real_connect($host, getenv($prefix . 'USERNAME') ?: (getenv('DB_USERNAME') ?: 'root'),
        $password !== false ? $password : (getenv('DB_PASSWORD') ?: ''),
        $database, (int) (getenv($prefix . 'PORT') ?: 3306));
    $db->set_charset('utf8mb4');
    if (!$pegawai) $db->query("SET SESSION sql_mode = CONCAT_WS(',', @@SESSION.sql_mode, 'STRICT_TRANS_TABLES')");
    $connections[$key] = $db;
    return $db;
}
