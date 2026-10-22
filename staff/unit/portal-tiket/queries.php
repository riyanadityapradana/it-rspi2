<?php
require_once __DIR__ . '/../../../portal-tiket/request_helpers.php';

function pti_filters($input)
{
    $statuses = ['Menunggu Analisis', 'Dikerjakan', 'Ditolak', 'Menunggu UAT', 'Selesai'];
    $status = is_string($input['status'] ?? null) && in_array($input['status'], $statuses, true) ? $input['status'] : '';
    $q = is_string($input['q'] ?? null) && mb_check_encoding($input['q'], 'UTF-8') ? mb_substr(trim($input['q']), 0, 100) : '';
    $system = filter_var($input['sistem'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 2147483647]]) ?: 0;
    $page = filter_var($input['page'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 1000000]]) ?: 1;
    return ['q' => $q, 'status' => $status, 'sistem' => $system, 'page' => $page];
}

function pti_queue($db, $filters)
{
    $where = []; $params = [];
    if ($filters['status'] !== '') { $where[] = 'p.status_tiket=?'; $params[] = $filters['status']; }
    if ($filters['sistem']) { $where[] = 'p.id_sistem=?'; $params[] = $filters['sistem']; }
    if ($filters['q'] !== '') {
        $like = '%' . strtr($filters['q'], ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
        $where[] = "(p.no_tiket LIKE ? ESCAPE '!' OR p.nama_pengaju LIKE ? ESCAPE '!' OR p.unit_pemohon LIKE ? ESCAPE '!' OR EXISTS (SELECT 1 FROM tiket_detail_request d WHERE d.id_tiket=p.id_tiket AND d.nama_perubahan_fitur LIKE ? ESCAPE '!'))";
        array_push($params, $like, $like, $like, $like);
    }
    $condition = $where ? ' WHERE ' . implode(' AND ', $where) : '';
    $total = (int) pt_rows($db, 'SELECT COUNT(*) total FROM tiket_pengajuan p' . $condition, $params)[0]['total'];
    $pages = max(1, (int) ceil($total / 25));
    $page = min($filters['page'], $pages); $offset = ($page - 1) * 25;
    $rows = pt_rows($db, 'SELECT p.id_tiket,p.no_tiket,p.jenis_tiket,p.tgl_pengajuan,p.nama_pengaju,p.unit_pemohon,p.status_tiket,s.nama_sistem,
        (SELECT d.nama_perubahan_fitur FROM tiket_detail_request d WHERE d.id_tiket=p.id_tiket ORDER BY d.id LIMIT 1) nama_perubahan_fitur
        FROM tiket_pengajuan p LEFT JOIN master_sistem s ON s.id_sistem=p.id_sistem' . $condition . "
        ORDER BY FIELD(p.status_tiket,'Menunggu Analisis','Dikerjakan','Menunggu UAT','Ditolak','Selesai'),p.tgl_pengajuan,p.id_tiket LIMIT 25 OFFSET $offset", $params);
    return ['rows' => $rows, 'total' => $total, 'pages' => $pages, 'page' => $page];
}

function pti_detail($db, $id)
{
    $rows = pt_rows($db, 'SELECT p.id_tiket,p.no_tiket,p.jenis_tiket,p.tgl_pengajuan,p.nama_pengaju,p.unit_pemohon,p.kontak_pengaju,p.status_tiket,s.nama_sistem
        FROM tiket_pengajuan p LEFT JOIN master_sistem s ON s.id_sistem=p.id_sistem WHERE p.id_tiket=?', [$id]);
    if (!$rows) return null;
    return ['ticket' => $rows[0], 'details' => pt_rows($db, 'SELECT nama_perubahan_fitur,kondisi_masalah,perubahan_diharapkan,dampak_modul,latar_belakang,deskripsi_alur_kerja,target_pengguna,output_diharapkan FROM tiket_detail_request WHERE id_tiket=? ORDER BY id', [$id])];
}
