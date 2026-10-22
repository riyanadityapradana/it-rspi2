<?php
require_once __DIR__ . '/../../../portal-tiket/uat_helpers.php';
require_once __DIR__ . '/../../../portal-tiket/portal_url.php';

function pti_uat_base_url($server)
{
    return pt_portal_base_url($server);
}

function pti_uat_link_state($db, $ticketId, $server, $now = null)
{
    $now = $now ?? new DateTimeImmutable('now');
    $row = pt_rows($db, "SELECT token_uat,token_expired_at FROM tiket_pengajuan WHERE id_tiket=? AND status_tiket='Menunggu UAT'", [$ticketId])[0] ?? [];
    $deadline = pt_uat_deadline($row['token_expired_at'] ?? null);
    $state = ['url' => '', 'deadline' => $deadline, 'now' => $now, 'active' => false, 'expired' => false];
    if (!pt_uat_token_valid($row['token_uat'] ?? null) || !$deadline) return $state;
    $state['url'] = pti_uat_base_url($server) . '/uat.php?token=' . rawurlencode($row['token_uat']);
    $state['expired'] = $now >= $deadline;
    $state['active'] = !$state['expired'];
    return $state;
}
