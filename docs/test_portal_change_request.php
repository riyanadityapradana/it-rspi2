<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
putenv('PORTAL_TELEGRAM_ENABLED=0'); // Fixture tidak boleh mengirim pesan Telegram asli.
require_once __DIR__ . '/../config/portal_tiket.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$schema = 'codex_portal_cr_' . bin2hex(random_bytes(5));
$admin = new mysqli('localhost', getenv('DB_USERNAME') ?: 'root', getenv('DB_PASSWORD') ?: '');
$admin->query("CREATE DATABASE `$schema` CHARACTER SET utf8mb4");
$admin->select_db($schema);
putenv('PORTAL_DB_HOST=localhost'); putenv('PORTAL_DB_DATABASE=' . $schema);
putenv('PORTAL_PEGAWAI_HOST=localhost'); putenv('PORTAL_PEGAWAI_DATABASE=' . $schema);
require_once __DIR__ . '/../portal-tiket/request_helpers.php';
$checks = 0;
function cr_check($condition, $message) { global $checks; if (!$condition) throw new RuntimeException($message); ++$checks; }
function cr_reject($fn, $message) {
    try { $fn(); } catch (DomainException $e) { cr_check(true, $message); return; }
    throw new RuntimeException('Not rejected: ' . $message);
}
require_once __DIR__ . '/portal_test_helpers.php';
$keep = false;
try {
    cr_sql_file($admin, __DIR__ . '/../Database/2026-10-20_portal_tiket_layanan_it.sql', $schema);
    cr_sql_file($admin, __DIR__ . '/../Database/2026-10-20_portal_tiket_reservasi_nomor.sql', $schema);
    $admin->query('CREATE TABLE tb_lokasi(lokasi_id INT PRIMARY KEY,nama_lokasi VARCHAR(150)) ENGINE=InnoDB');
    $admin->query("INSERT INTO tb_lokasi VALUES (1,'Unit Uji')");
    $admin->query("CREATE TABLE pegawai(id INT PRIMARY KEY,nama VARCHAR(100),stts_aktif VARCHAR(20)) ENGINE=InnoDB");
    $admin->query("INSERT INTO pegawai VALUES (1,'Pengaju Uji Aktif','AKTIF'),(2,'Pengaju Uji Nonaktif','NONAKTIF')");
    $admin->query("INSERT INTO master_sistem(nama_sistem,deskripsi,is_active) VALUES ('Sistem Uji','Fixture aktif',1),('Sistem Nonaktif','Fixture nonaktif',0)");
    $_SESSION = ['pt_csrf' => str_repeat('a', 64)];
    // These searches must not attempt a connection at all.
    putenv('PORTAL_PEGAWAI_HOST=unreachable.invalid');
    foreach (['', 'Pe', 'P12', '12345', '%%_', ['bad']] as $q) cr_check(pt_employee_search($q, 1)['results'] === [], 'Three-letter backend gate');
    putenv('PORTAL_PEGAWAI_HOST=localhost');
    $search = pt_employee_search('Peng', 1);
    cr_check(count($search['results']) === 1 && $search['results'][0]['id'] === '1', 'Only active employee searched');
    cr_check(pt_employee_search("Peng%' OR 1=1 --", 1)['results'] === [], 'SQL and wildcard escaping');
    cr_reject(function () { pt_signature(cr_image(true)); }, 'Blank PNG');
    cr_reject(function () { pt_signature('data:image/png;base64,not-image'); }, 'Invalid PNG');
    cr_reject(function () { pt_signature('data:image/svg+xml;base64,PHN2Zz4='); }, 'Non-PNG signature');
    $db = pt_connection(); $draft = pt_reserve($db); $another = pt_reserve($db);
    cr_check($draft['no_tiket'] !== $another['no_tiket'], 'Reserved numbers distinct');
    $valid = ['id_sistem' => '1', 'unit_id' => '1', 'pegawai_id' => '1', 'kontak_pengaju' => '081234567890',
        'nama_perubahan_fitur' => 'Perubahan Uji', 'kondisi_masalah' => 'Masalah uji',
        'perubahan_diharapkan' => 'Perubahan uji', 'dampak_modul' => 'Dampak uji', 'ttd_pengaju' => cr_image()];
    cr_reject(function () use ($db, $draft, $valid) { pt_save_change_request($db, $draft, $valid, 'wrong'); }, 'Invalid CSRF');
    foreach (array_keys($valid) as $field) {
        $bad = $valid; unset($bad[$field]);
        cr_reject(function () use ($db, $draft, $bad) { pt_save_change_request($db, $draft, $bad, $_SESSION['pt_csrf']); }, 'Missing ' . $field);
    }
    foreach ([['id_sistem','2'],['unit_id','9999'],['pegawai_id','2'],['pegawai_id','9999'],['kontak_pengaju','abc'],['kontak_pengaju','123'],['nama_perubahan_fitur',str_repeat('x',256)],['ttd_pengaju',cr_image(true)]] as $case) {
        $bad = $valid; $bad[$case[0]] = $case[1];
        cr_reject(function () use ($db, $draft, $bad) { pt_save_change_request($db, $draft, $bad, $_SESSION['pt_csrf']); }, 'Invalid ' . $case[0]);
    }
    cr_check((int) $db->query('SELECT COUNT(*) n FROM tiket_pengajuan')->fetch_assoc()['n'] === 0, 'Validation creates no headers');
    $admin->query("CREATE TRIGGER cr_force_failure BEFORE INSERT ON tiket_detail_request FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Test detail failure'");
    try { pt_save_change_request($db, $draft, $valid, $_SESSION['pt_csrf']); throw new RuntimeException('Detail failure not raised'); }
    catch (mysqli_sql_exception $e) { cr_check((int) $db->query('SELECT COUNT(*) n FROM tiket_pengajuan')->fetch_assoc()['n'] === 0, 'Header rollback when detail fails'); }
    $admin->query('DROP TRIGGER cr_force_failure');
    $counter = $db->query('SELECT SUM(urutan_terakhir) n FROM tiket_nomor_bulanan')->fetch_assoc()['n'];
    $valid += ['no_tiket' => 'FORGED', 'jenis_tiket' => 'Fitur Baru', 'tgl_pengajuan' => '2000-01-01 00:00:00', 'status_tiket' => 'Selesai', 'nama_pengaju' => 'FORGED', 'unit_pemohon' => 'FORGED'];
    $id = pt_save_change_request($db, $draft, $valid, $_SESSION['pt_csrf']);
    $row = pt_rows($db, 'SELECT * FROM tiket_pengajuan WHERE id_tiket=?', [$id])[0];
    cr_check($row['no_tiket'] === $draft['no_tiket'] && $row['tgl_pengajuan'] === $draft['tgl_pengajuan'], 'Reserved number/date preserved');
    cr_check($row['jenis_tiket'] === 'Change Request' && $row['status_tiket'] === 'Menunggu Analisis', 'Protected ticket type/status');
    cr_check($row['nama_pengaju'] === 'Pengaju Uji Aktif' && $row['unit_pemohon'] === 'Unit Uji', 'Authoritative employee/unit snapshots');
    cr_check($row['kontak_pengaju'] === '6281234567890', 'WhatsApp normalized');
    cr_check(count(pt_rows($db, 'SELECT * FROM tiket_detail_request WHERE id_tiket=?', [$id])) === 1, 'Related detail saved');
    cr_check($row['token_uat'] === null && $row['token_expired_at'] === null, 'UAT tokens not prematurely issued');
    cr_check(pt_save_change_request($db, $draft, $valid, $_SESSION['pt_csrf']) === $id, 'Submission retry idempotent');
    cr_check((int) $db->query('SELECT COUNT(*) n FROM tiket_pengajuan')->fetch_assoc()['n'] === 1, 'No duplicate on retry');
    cr_check($db->query('SELECT SUM(urutan_terakhir) n FROM tiket_nomor_bulanan')->fetch_assoc()['n'] === $counter, 'Saving reservation does not allocate twice');
    cr_check($db->query('SELECT @portal_tiket_no_reservasi n')->fetch_assoc()['n'] === null, 'Connection reservation cleared');
    try {
        $db->query("INSERT INTO tiket_pengajuan(no_tiket,jenis_tiket,id_sistem,kontak_pengaju,unit_pemohon,nama_pengaju,ttd_pengaju) VALUES ('FORGED','Change Request',1,'628123456789','Unit','Nama','sig')");
        throw new RuntimeException('Manual ticket accepted');
    } catch (mysqli_sql_exception $e) { cr_check(true, 'Unreserved explicit number rejected by trigger'); }
    $db->query("INSERT INTO tiket_pengajuan(jenis_tiket,id_sistem,kontak_pengaju,unit_pemohon,nama_pengaju,ttd_pengaju) VALUES ('Change Request',1,'628123456789','Unit','Nama','sig')");
    cr_check($db->insert_id > 0, 'Legacy automatic numbering still works');
    echo 'PASS ' . $checks . ' isolated backend checks.' . PHP_EOL;
    if (in_array('--keep-for-browser', $argv, true)) { $keep = true; echo 'BROWSER_TEST_DB=' . $schema . PHP_EOL; }
} finally {
    if (!$keep && preg_match('/^codex_portal_cr_[a-f0-9]{10}$/D', $schema)) { $admin->query("DROP DATABASE `$schema`"); echo 'Isolated database removed.' . PHP_EOL; }
}
