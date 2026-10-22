<?php
require_once __DIR__ . '/request_helpers.php';

function ptp_filters($input)
{
    $q = is_string($input['q'] ?? null) && mb_check_encoding($input['q'], 'UTF-8') ? mb_substr(trim($input['q']), 0, 100) : '';
    $statuses = ['Menunggu Analisis', 'Dikerjakan', 'Menunggu UAT', 'Selesai', 'Ditolak'];
    $status = is_string($input['status'] ?? null) && in_array($input['status'], $statuses, true) ? $input['status'] : '';
    $type = is_string($input['jenis'] ?? null) && in_array($input['jenis'], ['Change Request', 'Fitur Baru'], true) ? $input['jenis'] : '';
    $page = filter_var($input['page'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 1000000]]) ?: 1;
    return ['q' => $q, 'status' => $status, 'jenis' => $type, 'page' => $page];
}

function ptp_list($db, $filters)
{
    $where = []; $params = [];
    if ($filters['q'] !== '') {
        $like = '%' . strtr($filters['q'], ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
        $where[] = "(p.no_tiket LIKE ? ESCAPE '!' OR s.nama_sistem LIKE ? ESCAPE '!' OR EXISTS
            (SELECT 1 FROM tiket_detail_request d WHERE d.id_tiket=p.id_tiket AND d.nama_perubahan_fitur LIKE ? ESCAPE '!'))";
        array_push($params, $like, $like, $like);
    }
    if ($filters['status'] !== '') { $where[] = 'p.status_tiket=?'; $params[] = $filters['status']; }
    if ($filters['jenis'] !== '') { $where[] = 'p.jenis_tiket=?'; $params[] = $filters['jenis']; }
    $from = ' FROM tiket_pengajuan p LEFT JOIN master_sistem s ON s.id_sistem=p.id_sistem';
    $condition = $where ? ' WHERE ' . implode(' AND ', $where) : '';
    $total = (int) pt_rows($db, 'SELECT COUNT(*) total' . $from . $condition, $params)[0]['total'];
    $pages = max(1, (int) ceil($total / 20)); $page = min($filters['page'], $pages); $offset = ($page - 1) * 20;
    // Proyeksi publik: jangan memakai SELECT *, atau query internal yang memuat kontak.
    $rows = pt_rows($db, 'SELECT p.id_tiket,p.no_tiket,p.tgl_pengajuan,p.jenis_tiket,p.status_tiket,s.nama_sistem,
        (SELECT d.nama_perubahan_fitur FROM tiket_detail_request d WHERE d.id_tiket=p.id_tiket ORDER BY d.id LIMIT 1) nama_perubahan_fitur' .
        $from . $condition . " ORDER BY p.tgl_pengajuan DESC,p.id_tiket DESC LIMIT 20 OFFSET $offset", $params);
    return ['rows' => $rows, 'total' => $total, 'pages' => $pages, 'page' => $page, 'offset' => $offset];
}

function ptp_detail($db, $id)
{
    if (!is_int($id) || $id < 1 || $id > 2147483647) return null;
    $ticket = pt_rows($db, 'SELECT p.id_tiket,p.no_tiket,p.tgl_pengajuan,p.jenis_tiket,p.status_tiket,s.nama_sistem
        FROM tiket_pengajuan p LEFT JOIN master_sistem s ON s.id_sistem=p.id_sistem WHERE p.id_tiket=?', [$id])[0] ?? null;
    if (!$ticket) return null;
    return [
        'ticket' => $ticket,
        'details' => pt_rows($db, 'SELECT nama_perubahan_fitur,kondisi_masalah,perubahan_diharapkan,dampak_modul,
            latar_belakang,deskripsi_alur_kerja,target_pengguna,output_diharapkan FROM tiket_detail_request WHERE id_tiket=? ORDER BY id', [$id]),
        'analyses' => pt_rows($db, 'SELECT prioritas,analisis_unit_it,keputusan FROM tiket_analisis_it WHERE id_tiket=? ORDER BY id', [$id]),
        'handovers' => pt_rows($db, 'SELECT tgl_serah_terima,deskripsi_penyelesaian,status_pengujian_uat,catatan_pengujian
            FROM tiket_serah_terima WHERE id_tiket=? ORDER BY tgl_serah_terima,id', [$id])
    ];
}

function ptp_status_class($status)
{
    return ['Menunggu Analisis' => 'waiting', 'Dikerjakan' => 'working', 'Menunggu UAT' => 'uat', 'Selesai' => 'done', 'Ditolak' => 'rejected'][$status] ?? 'unknown';
}
function ptp_date($value)
{
    $timestamp = is_string($value) ? strtotime($value) : false;
    return $timestamp === false ? '-' : date('d-m-Y H:i T', $timestamp);
}
function ptp_list_url($filters, $extra = [])
{
    return 'daftar-tiket.php?' . http_build_query(array_merge($filters, $extra));
}
function ptp_readonly()
{
    header('Cache-Control: no-store'); header('X-Content-Type-Options: nosniff'); header('Referrer-Policy: same-origin');
    if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'HEAD'], true)) {
        header('Allow: GET, HEAD'); http_response_code(405); exit('Metode permintaan tidak didukung.');
    }
}
