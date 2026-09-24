<?php
require_once __DIR__.'/employees.php';
// Both dashboards have authenticated their account before including this menu.
$mnPendingCount=0;
try {
    $mnMenuUid=(int)($_SESSION['id_user'] ?? 0);
    $mnMenuStmt=$config->prepare('SELECT COUNT(*) n FROM inspeksi_header h WHERE '.mn_pending_sql());
    $mnMenuStmt->bind_param('ii',$mnMenuUid,$mnMenuUid); $mnMenuStmt->execute(); $mnPendingCount=(int)$mnMenuStmt->get_result()->fetch_assoc()['n']; $mnMenuStmt->close();
} catch (mysqli_sql_exception $e) { /* Menu remains available before migration. */ }
$mnMenuDashboard=($_SESSION['role'] ?? '')==='Kepala Ruangan'?'dashboard_admin.php':'dashboard_staff.php';
?>
<li class="nav-item"><a href="<?= $mnMenuDashboard ?>?unit=monev" class="nav-link"><i class="nav-icon fas fa-shield-alt" style="color:#800000"></i><p style="color:black;white-space:normal">Monev Keamanan IT <?php if ($mnPendingCount): ?><span class="badge badge-warning" title="Menunggu pengesahan Anda"><?= $mnPendingCount ?></span><?php endif; ?></p></a></li>
