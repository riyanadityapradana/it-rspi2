<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
putenv('PORTAL_TELEGRAM_ENABLED=0'); // Fixture tidak boleh mengirim pesan Telegram asli.
require_once __DIR__ . '/../staff/unit/portal-tiket/master_helpers.php';
require_once __DIR__ . '/portal_test_helpers.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$schema = 'codex_portal_master_' . bin2hex(random_bytes(5));
$db = new mysqli('localhost', getenv('DB_USERNAME') ?: 'root', getenv('DB_PASSWORD') ?: '');
$db->query("CREATE DATABASE `$schema` CHARACTER SET utf8mb4"); $db->select_db($schema); $db->set_charset('utf8mb4');
putenv('PORTAL_PEGAWAI_HOST=localhost'); putenv('PORTAL_PEGAWAI_DATABASE=' . $schema);
$checks = 0;
function ms_check($condition, $label) { global $checks; if (!$condition) throw new RuntimeException($label); ++$checks; }
function ms_reject($callback, $label) { try { $callback(); } catch (DomainException $e) { ms_check(true, $label); return; } throw new RuntimeException('Not rejected: ' . $label); }
try {
    cr_sql_file($db, __DIR__ . '/../Database/2026-10-20_portal_tiket_layanan_it.sql', $schema);
    $db->query('CREATE TABLE tb_user(id_user INT PRIMARY KEY,nama_lengkap VARCHAR(100),role VARCHAR(30),status VARCHAR(20))');
    $db->query("INSERT INTO tb_user VALUES (1,'Staff Uji','Staff','aktif'),(2,'Admin Uji','Kepala Ruangan','aktif'),(3,'Nonaktif Uji','Staff','nonaktif')");
    $db->query('CREATE TABLE tb_lokasi(lokasi_id INT PRIMARY KEY,nama_lokasi VARCHAR(150))');
    $db->query("INSERT INTO tb_lokasi VALUES (1,'Unit Uji')");
    $db->query('CREATE TABLE pegawai(id INT PRIMARY KEY,nama VARCHAR(100),stts_aktif VARCHAR(20))');
    $db->query("INSERT INTO pegawai VALUES (1,'Pengaju Uji','AKTIF')");
    $staff = ['id_user'=>1,'role'=>'Staff','pti_master_csrf'=>str_repeat('a',64)];
    $admin = ['id_user'=>2,'role'=>'Kepala Ruangan','pti_master_csrf'=>$staff['pti_master_csrf']];
    $post = ['csrf'=>$staff['pti_master_csrf'],'action'=>'create','nama_sistem'=>'  Sistem Uji  ','deskripsi'=>'Deskripsi sistem','is_active'=>'1'];
    $save = function ($data, $session = null, $method = 'POST') use ($db,$staff) { return pti_master_save($db,$db,$session ?? $staff,$method,$data); };
    $id = $save($post)['id'];
    $row = pt_rows($db, 'SELECT * FROM master_sistem WHERE id_sistem=?', [$id])[0];
    ms_check($row['nama_sistem'] === 'Sistem Uji' && $row['is_active'] === 1, 'Create and trim');
    ms_check($save(array_replace($post,['is_active'=>'0']),$admin)['id'] !== $id, 'Admin can create inactive system');
    foreach (['GET','PUT','DELETE'] as $method) ms_reject(function () use ($save,$post,$method) { $save($post,null,$method); }, 'Method blocked ' . $method);
    foreach ([[], ['id_user'=>3,'role'=>'Staff'], ['id_user'=>1,'role'=>'Kepala Ruangan'], ['id_user'=>999,'role'=>'Staff']] as $session) {
        ms_reject(function () use ($save,$post,$session) { $save($post,$session); }, 'Invalid identity blocked');
    }
    foreach ([['csrf','bad'],['csrf',[]],['action','delete'],['action',[]],['nama_sistem',' '],['nama_sistem',str_repeat('x',101)],['nama_sistem',[]],['deskripsi',''],['deskripsi',str_repeat('x',10001)],['is_active','2'],['is_active',[]]] as [$key,$value]) {
        ms_reject(function () use ($save,$post,$key,$value) { $save(array_replace($post,[$key=>$value])); }, 'Invalid input ' . $key);
    }
    ms_check(count(pt_rows($db,'SELECT id_sistem FROM master_sistem')) === 2, 'Rejected writes leave database unchanged');
    $edit = array_replace($post,['action'=>'update','id_sistem'=>(string)$id,'nama_sistem'=>'Sistem Diperbarui','deskripsi'=>'<script>window.masterXss=1</script>']);
    $save($edit,$admin);
    ms_check(pt_rows($db,'SELECT deskripsi FROM master_sistem WHERE id_sistem=?',[$id])[0]['deskripsi'] === $edit['deskripsi'], 'Update stores literal text');
    $toggle = ['csrf'=>$post['csrf'],'action'=>'set_active','id_sistem'=>(string)$id,'is_active'=>'0'];
    foreach (['999','1 OR 1=1',[], '0'] as $badId) ms_reject(function () use ($save,$toggle,$badId) { $save(array_replace($toggle,['id_sistem'=>$badId])); }, 'Unknown/invalid ID blocked');
    $_SESSION = ['pt_csrf'=>str_repeat('b',64)];
    $valid = ['id_sistem'=>(string)$id,'unit_id'=>'1','pegawai_id'=>'1','kontak_pengaju'=>'081234567890', 'nama_perubahan_fitur'=>'Perubahan Uji','kondisi_masalah'=>'Kondisi','perubahan_diharapkan'=>'Harapan','dampak_modul'=>'Dampak','ttd_pengaju'=>cr_image()];
    $draft = pt_reserve($db);
    $ticketId = pt_save_change_request($db,$draft,$valid,$_SESSION['pt_csrf']);
    $save($toggle);
    ms_check(!pt_rows($db,'SELECT id_sistem FROM master_sistem WHERE is_active=1 AND id_sistem=?',[$id]), 'Inactive excluded from public systems');
    ms_check((int)pt_rows($db,'SELECT id_sistem FROM tiket_pengajuan WHERE id_tiket=?',[$ticketId])[0]['id_sistem'] === $id, 'Ticket FK/history retained');
    ms_check(pti_detail($db,$ticketId)['ticket']['nama_sistem'] === 'Sistem Diperbarui', 'Internal ticket detail remains readable');
    $newDraft = pt_reserve($db);
    ms_reject(function () use ($db,$newDraft,$valid) { pt_save_change_request($db,$newDraft,$valid,$_SESSION['pt_csrf']); }, 'Submit to deactivated system rejected');
    $feature = pt_reserve($db,'Fitur Baru');
    $featurePost = $valid + ['latar_belakang'=>'Kebutuhan','deskripsi_alur_kerja'=>'Alur','target_pengguna'=>'Petugas','output_diharapkan'=>'Hasil'];
    ms_reject(function () use ($db,$feature,$featurePost) { pt_save_new_feature($db,$feature,$featurePost,$_SESSION['pt_csrf']); }, 'Feature submit to inactive system rejected');
    $save(array_replace($toggle,['is_active'=>'1']));
    ms_check(count(pt_rows($db,'SELECT id_sistem FROM master_sistem WHERE is_active=1 AND id_sistem=?',[$id])) === 1, 'Reactivated available publicly');
    $before = pt_rows($db,'SELECT * FROM master_sistem WHERE id_sistem=?',[$id])[0];
    $db->query("CREATE TRIGGER ms_fail BEFORE UPDATE ON master_sistem FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Forced update failure'");
    try { $save($toggle); throw new RuntimeException('Failure not raised'); }
    catch (mysqli_sql_exception $e) { ms_check(pt_rows($db,'SELECT * FROM master_sistem WHERE id_sistem=?',[$id])[0] === $before, 'Database error rolls back'); }
    echo 'PASS ' . $checks . ' isolated master-system checks.' . PHP_EOL;
} finally {
    if (preg_match('/^codex_portal_master_[a-f0-9]{10}$/D', $schema)) $db->query("DROP DATABASE `$schema`");
}
