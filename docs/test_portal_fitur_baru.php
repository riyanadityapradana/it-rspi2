<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
putenv('PORTAL_TELEGRAM_ENABLED=0'); // Fixture tidak boleh mengirim pesan Telegram asli.
require_once __DIR__ . '/../config/portal_tiket.php';
require_once __DIR__ . '/portal_test_helpers.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$schema = 'codex_portal_feat_' . bin2hex(random_bytes(5));
$db = new mysqli('localhost', getenv('DB_USERNAME') ?: 'root', getenv('DB_PASSWORD') ?: '');
$db->query("CREATE DATABASE `$schema` CHARACTER SET utf8mb4"); $db->select_db($schema);
putenv('PORTAL_DB_HOST=localhost'); putenv('PORTAL_DB_DATABASE=' . $schema);
putenv('PORTAL_PEGAWAI_HOST=localhost'); putenv('PORTAL_PEGAWAI_DATABASE=' . $schema);
require_once __DIR__ . '/../portal-tiket/request_helpers.php';
$checks = 0; $keep = false;
function nf_check($condition, $label) { global $checks; if (!$condition) throw new RuntimeException($label); ++$checks; }
function nf_reject($callback, $label) { try { $callback(); } catch (DomainException $e) { nf_check(true, $label); return; } throw new RuntimeException('Not rejected: ' . $label); }
try {
    cr_sql_file($db, __DIR__ . '/../Database/2026-10-20_portal_tiket_layanan_it.sql', $schema);
    cr_sql_file($db, __DIR__ . '/../Database/2026-10-20_portal_tiket_reservasi_nomor.sql', $schema);
    $db->query('CREATE TABLE tb_lokasi(lokasi_id INT PRIMARY KEY,nama_lokasi VARCHAR(150)) ENGINE=InnoDB');
    $db->query("INSERT INTO tb_lokasi VALUES (1,'Unit Uji')");
    $db->query('CREATE TABLE pegawai(id INT PRIMARY KEY,nama VARCHAR(100),stts_aktif VARCHAR(20)) ENGINE=InnoDB');
    $db->query("INSERT INTO pegawai VALUES (1,'Pengaju Uji Aktif','AKTIF'),(2,'Pengaju Nonaktif','NONAKTIF')");
    $db->query("INSERT INTO master_sistem(nama_sistem,deskripsi,is_active) VALUES ('Sistem Uji','Aktif',1),('Sistem Nonaktif','Nonaktif',0)");
    $_SESSION = ['pt_csrf' => str_repeat('a',64)];
    $crDraft = pt_reserve($db); $draft = pt_reserve($db, 'Fitur Baru');
    nf_check(preg_match('/^NEWFEAT-\d{4}-002$/D', $draft['no_tiket']) === 1, 'New feature prefix and shared monthly counter');
    nf_check($draft['jenis_tiket'] === 'Fitur Baru', 'Typed reservation');
    nf_check((int) $db->query('SELECT COUNT(*) n FROM tiket_pengajuan')->fetch_assoc()['n'] === 0, 'Opening form creates no blank header');
    $oldCr = $crDraft; unset($oldCr['jenis_tiket']); pt_assert_draft_type($oldCr, 'Change Request'); nf_check(true, 'Existing CR reservations remain valid');
    $valid = ['id_sistem'=>'1','unit_id'=>'1','pegawai_id'=>'1','kontak_pengaju'=>'081234567890',
        'nama_perubahan_fitur'=>'Fitur Uji','latar_belakang'=>'Kebutuhan uji','deskripsi_alur_kerja'=>'Alur kerja uji',
        'target_pengguna'=>'Petugas unit','output_diharapkan'=>'Laporan hasil uji','ttd_pengaju'=>cr_image()];
    nf_reject(function () use ($db,$draft,$valid) { pt_save_new_feature($db,$draft,$valid,'wrong'); }, 'Invalid CSRF');
    nf_reject(function () use ($db,$crDraft,$valid) { pt_save_new_feature($db,$crDraft,$valid,$_SESSION['pt_csrf']); }, 'CR reservation rejected on feature form');
    nf_reject(function () use ($db,$draft,$valid) { pt_save_change_request($db,$draft,$valid,$_SESSION['pt_csrf']); }, 'Feature reservation rejected on CR form');
    foreach (array_keys($valid) as $key) {
        $bad = $valid; unset($bad[$key]);
        nf_reject(function () use ($db,$draft,$bad) { pt_save_new_feature($db,$draft,$bad,$_SESSION['pt_csrf']); }, 'Required ' . $key);
    }
    foreach ([['id_sistem','2'],['unit_id','999'],['pegawai_id','2'],['pegawai_id','999'],['target_pengguna',str_repeat('x',256)],['output_diharapkan','   '],['latar_belakang',['array']],['kontak_pengaju','abc'],['ttd_pengaju',cr_image(true)]] as $case) {
        $bad = $valid; $bad[$case[0]] = $case[1];
        nf_reject(function () use ($db,$draft,$bad) { pt_save_new_feature($db,$draft,$bad,$_SESSION['pt_csrf']); }, 'Invalid ' . $case[0]);
    }
    nf_check((int) $db->query('SELECT COUNT(*) n FROM tiket_pengajuan')->fetch_assoc()['n'] === 0, 'Validation writes no headers');
    $db->query("CREATE TRIGGER nf_force_failure BEFORE INSERT ON tiket_detail_request FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Forced feature detail failure'");
    try { pt_save_new_feature($db,$draft,$valid,$_SESSION['pt_csrf']); throw new RuntimeException('Detail failure not raised'); }
    catch (mysqli_sql_exception $e) { nf_check((int) $db->query('SELECT COUNT(*) n FROM tiket_pengajuan')->fetch_assoc()['n'] === 0, 'Relational rollback'); }
    $db->query('DROP TRIGGER nf_force_failure');
    $valid += ['no_tiket'=>'FORGED','jenis_tiket'=>'Change Request','tgl_pengajuan'=>'2000-01-01','status_tiket'=>'Selesai','nama_pengaju'=>'FORGED',
        'kondisi_masalah'=>'DO_NOT_SAVE','perubahan_diharapkan'=>'DO_NOT_SAVE','dampak_modul'=>'DO_NOT_SAVE'];
    $id = pt_save_new_feature($db,$draft,$valid,$_SESSION['pt_csrf']);
    $header = pt_rows($db, 'SELECT * FROM tiket_pengajuan WHERE id_tiket=?', [$id])[0];
    $detail = pt_rows($db, 'SELECT * FROM tiket_detail_request WHERE id_tiket=?', [$id])[0];
    nf_check($header['no_tiket'] === $draft['no_tiket'] && $header['tgl_pengajuan'] === $draft['tgl_pengajuan'], 'Server number/date authoritative');
    nf_check($header['jenis_tiket'] === 'Fitur Baru' && $header['status_tiket'] === 'Menunggu Analisis', 'Server type/status authoritative');
    nf_check($header['nama_pengaju'] === 'Pengaju Uji Aktif' && $header['unit_pemohon'] === 'Unit Uji', 'Authoritative employee/unit');
    foreach (['nama_perubahan_fitur','latar_belakang','deskripsi_alur_kerja','target_pengguna','output_diharapkan'] as $key) nf_check($detail[$key] === $valid[$key], 'Correct detail column ' . $key);
    nf_check($detail['kondisi_masalah'] === null && $detail['perubahan_diharapkan'] === null && $detail['dampak_modul'] === null, 'CR-only fields remain NULL');
    nf_check($header['token_uat'] === null && strlen($header['ttd_pengaju']) > 100, 'Signature saved without issuing UAT token');
    nf_check(pt_save_new_feature($db,$draft,$valid,$_SESSION['pt_csrf']) === $id, 'Retry returns same ID');
    nf_check((int) $db->query('SELECT COUNT(*) n FROM tiket_detail_request')->fetch_assoc()['n'] === 1, 'No duplicate detail');
    nf_check($db->query('SELECT @portal_tiket_no_reservasi n')->fetch_assoc()['n'] === null, 'Connection reservation cleared');
    $db->query('UPDATE tiket_nomor_bulanan SET urutan_terakhir=999');
    $large = pt_reserve($db, 'Fitur Baru'); nf_check(substr($large['no_tiket'], -5) === '-1000', 'Sequence not truncated after 999');
    foreach (['Fitur Baru'=>'NEWFEAT-','Change Request'=>'REQ-'] as $type=>$prefix) {
        pt_query($db, "INSERT INTO tiket_pengajuan(jenis_tiket,id_sistem,kontak_pengaju,unit_pemohon,nama_pengaju,ttd_pengaju) VALUES (?,1,'628123456789','Unit','Pengaju','test')",[$type]);
        $row = pt_rows($db, 'SELECT no_tiket FROM tiket_pengajuan WHERE id_tiket=?', [$db->insert_id])[0];
        nf_check(strpos($row['no_tiket'],$prefix) === 0, 'Trigger prefix ' . $type);
    }
    echo 'PASS ' . $checks . ' isolated new-feature checks.' . PHP_EOL;
    if (in_array('--keep-for-browser', $argv, true)) { $keep = true; echo 'BROWSER_TEST_DB=' . $schema . PHP_EOL; }
} finally {
    if (!$keep && preg_match('/^codex_portal_feat_[a-f0-9]{10}$/D', $schema)) { $db->query("DROP DATABASE `$schema`"); echo 'Isolated database removed.' . PHP_EOL; }
}
