<?php
function mn_employee_db() {
    global $mnPegawaiConnection;
    if (isset($mnPegawaiConnection)) return $mnPegawaiConnection;
    $password = getenv('MONEV_PEGAWAI_PASSWORD');
    $connection = mysqli_init();
    $connection->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
    $connection->real_connect(
        getenv('MONEV_PEGAWAI_HOST') ?: '192.168.1.4',
        getenv('MONEV_PEGAWAI_USER') ?: (getenv('DB_USERNAME') ?: 'root'),
        $password !== false ? $password : (getenv('DB_PASSWORD') ?: ''),
        getenv('MONEV_PEGAWAI_DB') ?: 'sik9',
        (int)(getenv('MONEV_PEGAWAI_PORT') ?: 3306)
    );
    $connection->set_charset('utf8mb4');
    $mnPegawaiConnection = $connection;
    return $mnPegawaiConnection;
}
function mn_employee_rows($sql, $params) {
    try {
        $stmt = mn_employee_db()->prepare($sql);
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } catch (mysqli_sql_exception $e) {
        error_log('Monev employee database: '.$e->getMessage());
        throw new DomainException('Master pegawai belum dapat diakses. Hubungi administrator untuk memeriksa koneksi sik9.');
    }
}
function mn_employee($nik) {
    $nik = mn_text($nik, 30);
    mn_need($nik !== '', 'Pilih pegawai dari hasil pencarian.');
    $rows = mn_employee_rows("SELECT nik,nama FROM pegawai WHERE nik=? AND stts_aktif='AKTIF' LIMIT 2", [$nik]);
    mn_need(count($rows) === 1, 'Pegawai tidak ditemukan, tidak aktif, atau NIK tidak unik. Pilih ulang pegawai.');
    return $rows[0];
}
function mn_employee_search($term, $page) {
    $term = mn_text($term, 100);
    if (mb_strlen($term) < 3) return ['results'=>[], 'pagination'=>['more'=>false]];
    $page = max(1, min(1000, (int)$page));
    $offset = ($page - 1) * 20;
    $like = '%'.strtr($term, ['!'=>'!!', '%'=>'!%', '_'=>'!_']).'%';
    $rows = mn_employee_rows("SELECT nik,nama FROM pegawai WHERE stts_aktif='AKTIF' AND (nik LIKE ? ESCAPE '!' OR nama LIKE ? ESCAPE '!') ORDER BY nama,nik LIMIT 21 OFFSET $offset", [$like,$like]);
    $more = count($rows) > 20;
    return ['results'=>array_map(function ($row) { return ['id'=>(string)$row['nik'], 'text'=>$row['nik'].' - '.$row['nama']]; }, array_slice($rows,0,20)), 'pagination'=>['more'=>$more]];
}
// Mapping is managed by Kepala Ruangan; a self-edited profile NIP cannot grant approval rights.
function mn_assignee_sql($field, $alias = 'h') {
    return "CASE WHEN $alias.{$field}_nik IS NULL THEN $alias.{$field}_id ELSE (SELECT mu.id_user FROM monev_pegawai_akun ma JOIN tb_user mu ON mu.id_user=ma.user_id WHERE ma.nik=$alias.{$field}_nik AND mu.status='aktif') END";
}
function mn_pending_sql() {
    $pic = mn_assignee_sql('pic_area','sb'); $ver = mn_assignee_sql('verifikator');
    return "h.status_inspeksi='Menunggu Pengesahan' AND (EXISTS(SELECT 1 FROM inspeksi_bundle sb WHERE sb.inspeksi_id=h.id AND ($pic)=? AND NOT EXISTS(SELECT 1 FROM inspeksi_pengesahan p WHERE p.bundle_id=sb.id AND p.inspeksi_id=h.id AND p.peran='PIC/Kepala Unit' AND p.dibatalkan_pada IS NULL AND p.path_tanda_tangan IS NOT NULL)) OR ($ver=? AND NOT EXISTS(SELECT 1 FROM inspeksi_pengesahan p WHERE p.inspeksi_id=h.id AND p.bundle_id IS NULL AND p.peran='Verifikator' AND p.dibatalkan_pada IS NULL AND p.path_tanda_tangan IS NOT NULL)))";
}
function mn_validate_assignees($h) {
    foreach (['verifikator'] as $field) {
        if (!empty($h[$field.'_nik'])) mn_employee($h[$field.'_nik']);
        else mn_account($h[$field.'_id']);
    }
    $ids = array_filter([(int)$h['pemeriksa_id'],(int)$h['verifikator_id']]);
    mn_need(count($ids) === count(array_unique($ids)), 'Pemeriksa, PIC area, dan verifikator harus berbeda. Periksa pemetaan akun pegawai.');
    if ($h['verifikator_id']) mn_need(!mn_one('SELECT id FROM inspeksi_temuan_perbaikan WHERE inspeksi_id=? AND pic_perbaikan_id=? LIMIT 1',[$h['id'],$h['verifikator_id']]), 'PIC perbaikan tidak boleh merangkap verifikator. Periksa pemetaan akun pegawai.');
}
function mn_assignee_label($h, $field, $names) {
    if (!empty($h[$field.'_nik'])) return $h[$field.'_nik'].' - '.$h[$field.'_nama'];
    return $names[$h[$field.'_id']] ?? '-';
}
