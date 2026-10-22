<?php
require_once __DIR__ . '/access.php';
require_once __DIR__ . '/queries.php';

function pti_ticket_staff($authDb, $pegawaiDb, $userId)
{
    return pti_ticket_staff_record($authDb, $pegawaiDb, $userId)['id'];
}

function pti_ticket_staff_record($authDb, $pegawaiDb, $userId)
{
    // Cocokkan NIP dari akun terverifikasi; jangan percaya identitas pada POST/sesi.
    $account = pt_rows($authDb, 'SELECT nip FROM tb_user WHERE id_user=?', [$userId])[0] ?? null;
    $nip = trim((string) ($account['nip'] ?? ''));
    if ($nip === '') throw new DomainException('NIP akun belum tersedia. Lengkapi profil atau hubungi administrator sebelum melanjutkan proses tiket.', 422);
    $employees = pt_rows($pegawaiDb, "SELECT id,nama FROM pegawai WHERE nik=? AND stts_aktif='AKTIF' LIMIT 2", [$nip]);
    if (count($employees) !== 1) throw new DomainException('NIP akun belum cocok dengan satu pegawai SIMRS aktif. Hubungi administrator untuk memeriksa data akun.', 422);
    return ['id'=>pt_positive_id((string) $employees[0]['id'], 'pegawai IT'), 'nama'=>pt_text($employees[0]['nama'], 'Nama pegawai IT', 150)];
}
