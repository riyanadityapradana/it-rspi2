<?php
function pti_internal_user($authDb, $session)
{
    $id = filter_var($session['id_user'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $role = $session['role'] ?? '';
    if (!$id || !in_array($role, ['Staff', 'Kepala Ruangan'], true)) return null;
    $stmt = $authDb->prepare('SELECT id_user,nama_lengkap,role,status FROM tb_user WHERE id_user=?');
    $stmt->bind_param('i', $id); $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc(); $stmt->close();
    return $user && $user['status'] === 'aktif' && $user['role'] === $role ? $user : null;
}
