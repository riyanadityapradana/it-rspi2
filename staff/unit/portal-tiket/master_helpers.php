<?php
require_once __DIR__ . '/access.php';
require_once __DIR__ . '/queries.php';

function pti_master_status($value)
{
    if (!in_array($value, ['0', '1'], true)) throw new DomainException('Status aktif tidak valid.');
    return (int) $value;
}

// Seluruh perubahan master melalui POST, sesi akun internal aktif, dan token CSRF.
function pti_master_save($db, $authDb, $session, $method, $post)
{
    if ($method !== 'POST') throw new DomainException('Gunakan formulir untuk mengubah sistem.', 405);
    if (!pti_internal_user($authDb, $session)) throw new DomainException('Akses Master Sistem ditolak.', 403);
    $csrf = $post['csrf'] ?? null;
    if (!is_string($csrf) || !is_string($session['pti_master_csrf'] ?? null) ||
        $session['pti_master_csrf'] === '' || !hash_equals($session['pti_master_csrf'], $csrf)) {
        throw new DomainException('Sesi formulir tidak valid. Muat ulang halaman lalu coba lagi.', 403);
    }
    $action = $post['action'] ?? '';
    if (!in_array($action, ['create', 'update', 'set_active'], true)) throw new DomainException('Aksi tidak valid.');
    $active = pti_master_status($post['is_active'] ?? null);
    $id = $action === 'create' ? null : pt_positive_id($post['id_sistem'] ?? null, 'sistem');
    if ($action !== 'set_active') {
        $name = pt_text($post['nama_sistem'] ?? null, 'Nama Sistem', 100);
        $description = pt_text($post['deskripsi'] ?? null, 'Deskripsi', 10000);
    }
    $db->begin_transaction();
    try {
        if ($id !== null && !pt_rows($db, 'SELECT id_sistem FROM master_sistem WHERE id_sistem=? FOR UPDATE', [$id])) {
            throw new DomainException('Sistem tidak ditemukan.', 404);
        }
        if ($action === 'create') {
            pt_query($db, 'INSERT INTO master_sistem(nama_sistem,deskripsi,is_active) VALUES (?,?,?)', [$name, $description, $active]);
            $id = $db->insert_id;
        } elseif ($action === 'update') {
            pt_query($db, 'UPDATE master_sistem SET nama_sistem=?,deskripsi=?,is_active=? WHERE id_sistem=?', [$name, $description, $active, $id]);
        } else {
            pt_query($db, 'UPDATE master_sistem SET is_active=? WHERE id_sistem=?', [$active, $id]);
        }
        $db->commit();
        return ['id' => (int) $id, 'message' => $action === 'create' ? 'Sistem berhasil ditambahkan.' :
            ($action === 'update' ? 'Data sistem berhasil diperbarui.' : ($active ? 'Sistem berhasil diaktifkan.' : 'Sistem berhasil dinonaktifkan. Riwayat tiket tetap tersimpan.'))];
    } catch (Throwable $e) {
        $db->rollback();
        throw $e;
    }
}
