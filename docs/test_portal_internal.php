<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../config/portal_tiket.php';
require_once __DIR__ . '/../staff/unit/portal-tiket/queries.php';
require_once __DIR__ . '/../staff/unit/portal-tiket/access.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$schema = 'codex_portal_internal_' . bin2hex(random_bytes(5));
$db = new mysqli('localhost', getenv('DB_USERNAME') ?: 'root', getenv('DB_PASSWORD') ?: '');
$db->query("CREATE DATABASE `$schema` CHARACTER SET utf8mb4"); $db->select_db($schema); $db->set_charset('utf8mb4');
$checks = 0; $keep = false;
function pi_check($value, $label) { global $checks; if (!$value) throw new RuntimeException($label); ++$checks; }
try {
    $db->query('CREATE TABLE tb_user(id_user INT PRIMARY KEY,nama_lengkap VARCHAR(100),role VARCHAR(30),status VARCHAR(20))');
    $db->query("INSERT INTO tb_user VALUES (1,'Staff Uji','Staff','aktif'),(2,'Admin Uji','Kepala Ruangan','aktif'),(3,'Nonaktif Uji','Staff','nonaktif')");
    $db->query('CREATE TABLE master_sistem(id_sistem INT PRIMARY KEY AUTO_INCREMENT,nama_sistem VARCHAR(100),deskripsi TEXT NOT NULL,is_active INT)');
    $db->query("INSERT INTO master_sistem VALUES (1,'SIMRS Uji','Sistem aktif untuk pengujian',1),(2,'Sistem Lama','Riwayat sistem nonaktif',0)");
    $db->query('CREATE TABLE tiket_pengajuan(id_tiket INT PRIMARY KEY,no_tiket VARCHAR(32),jenis_tiket VARCHAR(50),id_sistem INT,kontak_pengaju VARCHAR(25),tgl_pengajuan DATETIME,unit_pemohon VARCHAR(150),nama_pengaju VARCHAR(150),status_tiket VARCHAR(40),ttd_pengaju LONGTEXT,token_uat VARCHAR(64),token_expired_at DATETIME)');
    $db->query('CREATE TABLE tiket_detail_request(id INT PRIMARY KEY AUTO_INCREMENT,id_tiket INT,nama_perubahan_fitur VARCHAR(255),kondisi_masalah TEXT,perubahan_diharapkan TEXT,dampak_modul TEXT,latar_belakang TEXT,deskripsi_alur_kerja TEXT,target_pengguna VARCHAR(255),output_diharapkan TEXT)');
    $db->query('CREATE TABLE tiket_analisis_it(id INT PRIMARY KEY AUTO_INCREMENT,id_tiket INT,id_staf_it INT,prioritas VARCHAR(10),analisis_unit_it TEXT,keputusan TEXT)');
    $db->query('CREATE TABLE tiket_serah_terima(id INT PRIMARY KEY AUTO_INCREMENT,id_tiket INT,tgl_serah_terima DATETIME,deskripsi_penyelesaian TEXT,status_pengujian_uat VARCHAR(40),catatan_pengujian TEXT)');
    for ($i = 1; $i <= 35; ++$i) {
        $status = [31 => 'Dikerjakan', 32 => 'Menunggu UAT', 33 => 'Ditolak', 34 => 'Selesai'][$i] ?? 'Menunggu Analisis';
        pt_query($db, "INSERT INTO tiket_pengajuan VALUES (?,?,'Change Request',?,'628123456789','2026-10-20 08:00:00',?,'Pemohon Uji',?,'PRIVATE_SIGNATURE','PRIVATE_TOKEN',NULL)", [$i, 'REQ-UJI-' . str_pad($i, 3, '0', STR_PAD_LEFT), $i === 35 ? 2 : 1, $i === 35 ? 'Unit Lain' : 'Unit Uji', $status]);
        pt_query($db, 'INSERT INTO tiket_detail_request(id_tiket,nama_perubahan_fitur,kondisi_masalah,perubahan_diharapkan,dampak_modul) VALUES (?,?,?,?,?)', [$i, 'Perubahan Uji ' . $i, 'Kondisi <script>window.ptiXss=1</script>', 'Hasil uji', 'Dampak uji']);
    }
    pt_query($db, 'INSERT INTO tiket_detail_request(id_tiket,nama_perubahan_fitur) VALUES (1,?)', ['Extra Needle']);
    foreach ([['id_user'=>1,'role'=>'Staff'], ['id_user'=>2,'role'=>'Kepala Ruangan']] as $session) pi_check(pti_internal_user($db, $session) !== null, 'Active role allowed');
    foreach ([[], ['id_user'=>1,'role'=>'Admin'], ['id_user'=>1,'role'=>'Kepala Ruangan'], ['id_user'=>3,'role'=>'Staff'], ['id_user'=>'1 OR 1=1','role'=>'Staff'], ['id_user'=>999,'role'=>'Staff']] as $session) pi_check(pti_internal_user($db, $session) === null, 'Invalid or inactive identity rejected');
    $filters = pti_filters(['q'=>['bad'], 'status'=>'invalid', 'sistem'=>'1 OR 1=1', 'page'=>'-1']);
    pi_check($filters === ['q'=>'','status'=>'','sistem'=>0,'page'=>1], 'Invalid filters normalized');
    $queue = pti_queue($db, pti_filters([]));
    pi_check($queue['total'] === 35 && count($queue['rows']) === 25 && $queue['pages'] === 2, 'Pagination and total');
    pi_check(count(array_unique(array_column($queue['rows'], 'id_tiket'))) === 25, 'Multiple details do not duplicate headers');
    pi_check($queue['rows'][0]['id_tiket'] === 1, 'FIFO within status');
    pi_check(!array_key_exists('ttd_pengaju', $queue['rows'][0]) && !array_key_exists('token_uat', $queue['rows'][0]), 'Sensitive columns excluded');
    $page = pti_queue($db, pti_filters(['page'=>'100']));
    pi_check($page['page'] === 2 && count($page['rows']) === 10, 'Out of range page clamped');
    pi_check($page['rows'][9]['status_tiket'] === 'Selesai', 'Completed tickets after active queue');
    foreach ([['q'=>'Extra Needle'], ['q'=>'REQ-UJI-035'], ['status'=>'Dikerjakan'], ['sistem'=>'2'], ['status'=>'Menunggu Analisis','sistem'=>'2']] as $params) pi_check(pti_queue($db, pti_filters($params))['total'] === 1, 'Search and filters');
    foreach (['%', '_', "' OR 1=1 --"] as $q) pi_check(pti_queue($db, pti_filters(['q'=>$q]))['total'] === 0, 'LIKE and SQL escaping');
    $detail = pti_detail($db, 1);
    pi_check(count($detail['details']) === 2, 'All request details available');
    pi_check(!array_key_exists('ttd_pengaju', $detail['ticket']) && !array_key_exists('token_uat', $detail['ticket']), 'Detail excludes signature/token');
    pi_check(pti_detail($db, 999) === null, 'Unknown ticket');
    if (in_array('--keep-for-browser', $argv, true)) {
        $folder = __DIR__ . '/../../.codex_tmp/portal-internal-preview/sessions';
        if (!is_dir($folder)) mkdir($folder, 0700, true);
        session_save_path($folder); $sessions = [];
        foreach (['staff'=>[1,'Staff'], 'admin'=>[2,'Kepala Ruangan'], 'inactive'=>[3,'Staff']] as $label => $account) {
            $sid = 'pitest' . bin2hex(random_bytes(16)); session_id($sid); session_start();
            $_SESSION = ['id_user'=>$account[0], 'role'=>$account[1], 'foto'=>'', 'nip'=>'fixture']; session_write_close(); $sessions[$label] = $sid;
        }
        $keep = true;
        echo json_encode(['schema'=>$schema,'sessions'=>$sessions]) . PHP_EOL;
    }
    echo 'PASS ' . $checks . ' isolated internal queue checks.' . PHP_EOL;
} finally {
    if (!$keep && preg_match('/^codex_portal_internal_[a-f0-9]{10}$/D', $schema)) $db->query("DROP DATABASE `$schema`");
}
