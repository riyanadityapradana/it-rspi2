<?php
require_once __DIR__ . '/access.php';
require_once __DIR__ . '/queries.php';
require_once __DIR__ . '/staff_identity.php';

function pti_analysis_staff($authDb, $pegawaiDb, $userId)
{
    return pti_ticket_staff($authDb, $pegawaiDb, $userId);
}

function pti_analysis_save($db, $authDb, $session, $method, $ticketId, $post, $pegawaiDb = null)
{
    if ($method !== 'POST') throw new DomainException('Gunakan formulir untuk menyimpan analisis.', 405);
    $user = pti_internal_user($authDb, $session);
    if (!$user) throw new DomainException('Akses analisis tiket ditolak.', 403);
    $csrf = $post['csrf'] ?? null;
    if (!is_string($csrf) || !is_string($session['pti_analysis_csrf'] ?? null) ||
        $session['pti_analysis_csrf'] === '' || !hash_equals($session['pti_analysis_csrf'], $csrf)) {
        throw new DomainException('Sesi formulir tidak valid. Muat ulang halaman lalu coba lagi.', 403);
    }
    if (($post['action'] ?? null) !== 'save_analysis') throw new DomainException('Aksi analisis tidak valid.', 422);
    $id = pt_positive_id($ticketId, 'tiket');
    $priority = $post['prioritas'] ?? null;
    if (!in_array($priority, ['Tinggi', 'Sedang', 'Rendah'], true)) throw new DomainException('Pilih satu prioritas: Tinggi, Sedang, atau Rendah.', 422);
    $analysis = pt_text($post['analisis_unit_it'] ?? null, 'Analisis Unit IT', 10000);
    $decision = pt_text($post['keputusan'] ?? null, 'Keputusan', 10000);
    $staff = pti_ticket_staff_record($authDb, $pegawaiDb ?? pt_connection(true), $user['id_user']);

    $db->begin_transaction();
    try {
        // Lock baris tiket menyerialkan submit bersamaan dari staf/tab berbeda.
        $ticket = pt_rows($db, 'SELECT no_tiket,status_tiket FROM tiket_pengajuan WHERE id_tiket=? FOR UPDATE', [$id])[0] ?? null;
        if (!$ticket) throw new DomainException('Tiket tidak ditemukan.', 404);
        if ($ticket['status_tiket'] !== 'Menunggu Analisis') {
            throw new DomainException('Analisis tidak dapat disimpan karena tiket sudah diproses. Muat ulang untuk melihat status terbaru.', 409);
        }
        pt_query($db, 'INSERT INTO tiket_analisis_it(id_tiket,id_staf_it,tgl_analisis,nama_staf_it,prioritas,analisis_unit_it,keputusan) VALUES (?,?,?,?,?,?,?)',
            [$id, $staff['id'], date('Y-m-d H:i:s'), $staff['nama'], $priority, $analysis, $decision]);
        $changed = pt_query($db, "UPDATE tiket_pengajuan SET status_tiket='Dikerjakan' WHERE id_tiket=? AND status_tiket='Menunggu Analisis'", [$id]);
        if ($changed->affected_rows !== 1) throw new RuntimeException('Status tiket tidak berhasil diperbarui.');
        $db->commit();
        return 'Analisis tiket ' . $ticket['no_tiket'] . ' berhasil disimpan. Status tiket menjadi Dikerjakan.';
    } catch (Throwable $e) {
        $db->rollback();
        throw $e;
    }
}
