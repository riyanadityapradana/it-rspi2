<?php
require_once __DIR__ . '/request_helpers.php';

function pt_uat_expiry(DateTimeImmutable $generatedAt)
{
    // Satu bulan kalender, dibatasi hari terakhir bulan tujuan (31 Jan -> 28/29 Feb).
    $next = $generatedAt->modify('first day of next month');
    return $next->setDate((int) $next->format('Y'), (int) $next->format('n'), min((int) $generatedAt->format('j'), (int) $next->format('t')));
}

function pt_uat_deadline($value)
{
    if (!is_string($value)) return null;
    $date = DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', $value);
    return $date && $date->format('Y-m-d H:i:s') === $value ? $date : null;
}

function pt_uat_token_valid($value)
{
    return is_string($value) && preg_match('/^[a-f0-9]{64}$/D', $value) === 1;
}

function pt_uat_review($db, $token, $now = null, $pegawaiDb = null)
{
    if (!pt_uat_token_valid($token)) throw new DomainException('Tautan UAT tidak valid atau tidak tersedia.', 404);
    $ticket = pt_rows($db, 'SELECT p.id_tiket,p.no_tiket,p.jenis_tiket,p.status_tiket,p.token_expired_at,s.nama_sistem
        FROM tiket_pengajuan p LEFT JOIN master_sistem s ON s.id_sistem=p.id_sistem WHERE p.token_uat=? LIMIT 1', [$token])[0] ?? null;
    if (!$ticket) throw new DomainException('Tautan UAT tidak valid atau tidak tersedia.', 404);
    $deadline = pt_uat_deadline($ticket['token_expired_at']);
    if (!$deadline || ($now ?? new DateTimeImmutable('now')) >= $deadline) throw new DomainException('Tautan telah kedaluwarsa. Hubungi tim IT untuk tindak lanjut.', 410);
    if (!in_array($ticket['status_tiket'], ['Menunggu UAT', 'Selesai'], true)) throw new DomainException('Tautan UAT tidak aktif. Hubungi tim IT untuk tindak lanjut.', 410);
    $handovers = pt_rows($db, 'SELECT id,id_staf_penyerah,nama_staf_penyerah,tgl_serah_terima,deskripsi_penyelesaian,status_pengujian_uat,catatan_pengujian,nama_penerima,ttd_penerima FROM tiket_serah_terima WHERE id_tiket=? ORDER BY id', [$ticket['id_tiket']]);
    $handover = $handovers ? $handovers[count($handovers)-1] : null;
    $completed = $ticket['status_tiket'] === 'Selesai';
    // Tautan yang sudah disetujui tetap dapat dibaca sampai batas waktu aslinya.
    if ($completed && (!$handover || !in_array($handover['status_pengujian_uat'], ['Berhasil', 'Diterima dengan catatan'], true)
        || trim($handover['nama_penerima'] ?? '') === '' || trim($handover['ttd_penerima'] ?? '') === '')) {
        throw new DomainException('Hasil verifikasi serah terima belum tersedia. Hubungi tim IT untuk tindak lanjut.', 410);
    }
    // Semua rincian, identitas, dan gambar hanya dibaca setelah gate token/status/expiry.
    $ticket += pt_rows($db, 'SELECT tgl_pengajuan,unit_pemohon,nama_pengaju,kontak_pengaju,ttd_pengaju FROM tiket_pengajuan WHERE id_tiket=?', [$ticket['id_tiket']])[0];
    try { $ticket['ttd_pengaju'] = pt_signature($ticket['ttd_pengaju']); }
    catch (DomainException $e) { $ticket['ttd_pengaju'] = null; }
    $details = pt_rows($db, 'SELECT nama_perubahan_fitur,kondisi_masalah,perubahan_diharapkan,dampak_modul,latar_belakang,deskripsi_alur_kerja,target_pengguna,output_diharapkan FROM tiket_detail_request WHERE id_tiket=? ORDER BY id', [$ticket['id_tiket']]);
    $analyses = pt_rows($db, 'SELECT id,id_staf_it,tgl_analisis,nama_staf_it,prioritas,analisis_unit_it,keputusan FROM tiket_analisis_it WHERE id_tiket=? ORDER BY id', [$ticket['id_tiket']]);
    $names = [];
    $resolveName = function ($snapshot, $staffId) use (&$names, &$pegawaiDb) {
        if (is_string($snapshot) && trim($snapshot) !== '') return $snapshot;
        if (!array_key_exists($staffId, $names)) {
            try {
                $pegawaiDb = $pegawaiDb ?? pt_connection(true);
                $names[$staffId] = pt_rows($pegawaiDb, 'SELECT nama FROM pegawai WHERE id=?', [$staffId])[0]['nama'] ?? null;
            } catch (Throwable $e) { $names[$staffId] = null; }
        }
        return $names[$staffId];
    };
    foreach ($analyses as &$row) $row['nama_staf_it'] = $resolveName($row['nama_staf_it'], $row['id_staf_it']); unset($row);
    foreach ($handovers as &$row) $row['nama_staf_penyerah'] = $resolveName($row['nama_staf_penyerah'], $row['id_staf_penyerah']); unset($row);
    foreach ($handovers as &$row) {
        if ($row['ttd_penerima'] !== null) {
            try { $row['ttd_penerima'] = pt_signature($row['ttd_penerima']); }
            catch (DomainException $e) { $row['ttd_penerima'] = null; }
        }
    } unset($row);
    return ['ticket'=>$ticket,'details'=>$details,'analyses'=>$analyses,'handovers'=>$handovers,'handover'=>$handovers ? $handovers[count($handovers)-1] : null,'completed'=>$completed];
}

function pt_uat_receiver($id, $pegawaiDb = null)
{
    $id = pt_positive_id($id, 'Nama Penerima');
    try {
        $row = pt_rows($pegawaiDb ?? pt_connection(true), "SELECT id,nama FROM pegawai WHERE id=? AND stts_aktif='AKTIF' LIMIT 1", [$id])[0] ?? null;
    } catch (Throwable $e) { throw new DomainException('Master pegawai belum dapat diakses. Silakan coba lagi.', 503); }
    if (!$row) throw new DomainException('Pegawai penerima tidak ditemukan atau sudah tidak aktif. Pilih ulang Nama Penerima.', 422);
    $row['nama'] = pt_text($row['nama'], 'Nama pegawai penerima', 150);
    return $row;
}

function pt_uat_default_receiver($name, $pegawaiDb = null)
{
    try {
        $rows = pt_rows($pegawaiDb ?? pt_connection(true), "SELECT id,nama FROM pegawai WHERE nama=? AND stts_aktif='AKTIF' ORDER BY id LIMIT 2", [$name]);
    } catch (Throwable $e) { throw new DomainException('Nama pengaju belum dapat dicocokkan ke master pegawai. Silakan cari Nama Penerima kembali.', 503); }
    // Header tiket lama hanya menyimpan nama; jangan menebak ID jika nama ambigu.
    return count($rows) === 1 ? $rows[0] : null;
}

function pt_uat_search_access($db, $token, $now = null)
{
    if (!pt_uat_token_valid($token)) throw new DomainException('Tautan UAT tidak valid atau tidak tersedia.', 404);
    $ticket = pt_rows($db, 'SELECT status_tiket,token_expired_at FROM tiket_pengajuan WHERE token_uat=? LIMIT 1', [$token])[0] ?? null;
    if (!$ticket) throw new DomainException('Tautan UAT tidak valid atau tidak tersedia.', 404);
    $deadline = pt_uat_deadline($ticket['token_expired_at']);
    if (!$deadline || ($now ?? new DateTimeImmutable()) >= $deadline || $ticket['status_tiket'] !== 'Menunggu UAT') {
        throw new DomainException('Tautan UAT telah kedaluwarsa atau tidak lagi menunggu verifikasi.', 410);
    }
}

function pt_uat_confirm($db, $token, $method, $input, $session, $now = null, $pegawaiDb = null)
{
    if ($method !== 'POST') throw new DomainException('Metode permintaan tidak didukung.', 405);
    if (!pt_uat_token_valid($token)) throw new DomainException('Tautan UAT tidak valid atau tidak tersedia.', 404);
    if (!is_string($session['pt_csrf'] ?? null) || $session['pt_csrf'] === '' || !is_string($input['csrf'] ?? null)
        || !hash_equals($session['pt_csrf'], $input['csrf'])) throw new DomainException('Sesi formulir tidak valid. Muat ulang halaman dan coba lagi.', 403);
    $status = $input['status_pengujian_uat'] ?? null;
    if (!in_array($status, ['Berhasil', 'Diterima dengan catatan'], true)) throw new DomainException('Pilih satu status pengujian UAT.', 422);
    $receiverId = (string) pt_positive_id($input['id_pegawai_penerima'] ?? null, 'Nama Penerima');
    $notes = $input['catatan_pengujian'] ?? '';
    if (!is_string($notes) || !mb_check_encoding($notes, 'UTF-8') || mb_strlen($notes) > 10000) throw new DomainException('Catatan pengujian tidak valid, maksimal 10000 karakter.', 422);
    $notes = trim($notes);
    if ($status === 'Diterima dengan catatan' && $notes === '') throw new DomainException('Catatan pengujian wajib diisi untuk status Diterima dengan catatan.', 422);
    $signature = pt_signature($input['ttd_penerima'] ?? null);
    $db->begin_transaction();
    try {
        $ticket = pt_rows($db, 'SELECT id_tiket,status_tiket,token_expired_at FROM tiket_pengajuan WHERE token_uat=? LIMIT 1 FOR UPDATE', [$token])[0] ?? null;
        if (!$ticket) throw new DomainException('Tautan UAT tidak valid atau tidak tersedia.', 404);
        $deadline = pt_uat_deadline($ticket['token_expired_at']);
        if (!$deadline || ($now ?? new DateTimeImmutable('now')) >= $deadline) throw new DomainException('Tautan telah kedaluwarsa. Hubungi tim IT untuk tindak lanjut.', 410);
        if ($ticket['status_tiket'] !== 'Menunggu UAT') throw new DomainException('Tiket sudah diverifikasi atau tidak lagi menunggu UAT. Muat ulang halaman untuk melihat hasil terbaru.', 409);
        $receiver = pt_uat_receiver($receiverId, $pegawaiDb);
        $name = $receiver['nama'];
        $handover = pt_rows($db, 'SELECT id,status_pengujian_uat FROM tiket_serah_terima WHERE id_tiket=? ORDER BY id DESC LIMIT 1 FOR UPDATE', [$ticket['id_tiket']])[0] ?? null;
        if (!$handover || $handover['status_pengujian_uat'] !== null) throw new DomainException('Serah terima tidak tersedia atau sudah diverifikasi. Muat ulang halaman.', 409);
        $updated = pt_query($db, 'UPDATE tiket_serah_terima SET status_pengujian_uat=?,catatan_pengujian=?,nama_penerima=?,ttd_penerima=? WHERE id=? AND id_tiket=? AND status_pengujian_uat IS NULL',
            [$status, $notes === '' ? null : $notes, $name, $signature, $handover['id'], $ticket['id_tiket']]);
        if ($updated->affected_rows !== 1) throw new DomainException('Serah terima telah berubah. Muat ulang halaman.', 409);
        $updated = pt_query($db, "UPDATE tiket_pengajuan SET status_tiket='Selesai' WHERE id_tiket=? AND status_tiket='Menunggu UAT'", [$ticket['id_tiket']]);
        if ($updated->affected_rows !== 1) throw new DomainException('Status tiket telah berubah. Muat ulang halaman.', 409);
        $db->commit();
    } catch (Throwable $e) { $db->rollback(); throw $e; }
}
