<?php
require_once __DIR__ . '/staff_identity.php';
require_once __DIR__ . '/../../../portal-tiket/uat_helpers.php';

function pti_handover_save($db, $authDb, $session, $method, $ticketId, $post, $pegawaiDb = null)
{
    if ($method !== 'POST') throw new DomainException('Gunakan formulir untuk menyimpan serah terima.', 405);
    $user = pti_internal_user($authDb, $session);
    if (!$user) throw new DomainException('Akses serah terima tiket ditolak.', 403);
    $csrf = $post['csrf'] ?? null;
    if (!is_string($csrf) || !is_string($session['pti_handover_csrf'] ?? null) ||
        $session['pti_handover_csrf'] === '' || !hash_equals($session['pti_handover_csrf'], $csrf)) {
        throw new DomainException('Sesi formulir tidak valid. Muat ulang halaman lalu coba lagi.', 403);
    }
    if (($post['action'] ?? null) !== 'save_handover') throw new DomainException('Aksi serah terima tidak valid.', 422);
    $id = pt_positive_id($ticketId, 'tiket');
    $description = pt_text($post['deskripsi_penyelesaian'] ?? null, 'Deskripsi Penyelesaian', 10000);
    $staff = pti_ticket_staff_record($authDb, $pegawaiDb ?? pt_connection(true), $user['id_user']);

    $db->begin_transaction();
    try {
        // Pemeriksaan status dan lock mencegah serah terima/token ganda saat submit bersamaan.
        $ticket = pt_rows($db, 'SELECT no_tiket,status_tiket FROM tiket_pengajuan WHERE id_tiket=? FOR UPDATE', [$id])[0] ?? null;
        if (!$ticket) throw new DomainException('Tiket tidak ditemukan.', 404);
        if ($ticket['status_tiket'] !== 'Dikerjakan') throw new DomainException('Serah terima hanya dapat dibuat untuk tiket berstatus Dikerjakan. Muat ulang untuk melihat status terbaru.', 409);
        $submittedAt = new DateTimeImmutable('now');
        $date = $submittedAt->format('Y-m-d H:i:s');
        $expires = pt_uat_expiry($submittedAt)->format('Y-m-d H:i:s');
        $token = bin2hex(random_bytes(32));
        pt_query($db, 'INSERT INTO tiket_serah_terima(id_tiket,tgl_serah_terima,deskripsi_penyelesaian,id_staf_penyerah,nama_staf_penyerah,status_pengujian_uat,catatan_pengujian,nama_penerima,ttd_penerima)
            VALUES (?,?,?,?,?,NULL,NULL,NULL,NULL)', [$id, $date, $description, $staff['id'], $staff['nama']]);
        $changed = pt_query($db, "UPDATE tiket_pengajuan SET status_tiket='Menunggu UAT',token_uat=?,token_expired_at=? WHERE id_tiket=? AND status_tiket='Dikerjakan'", [$token, $expires, $id]);
        if ($changed->affected_rows !== 1) throw new RuntimeException('Status serah terima tidak berhasil diperbarui.');
        $db->commit();
        return 'Serah terima tiket ' . $ticket['no_tiket'] . ' berhasil disimpan. Status tiket menjadi Menunggu UAT.';
    } catch (Throwable $e) {
        $db->rollback();
        throw $e;
    }
}
