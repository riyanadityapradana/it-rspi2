<?php
if (!isset($config) || session_status() !== PHP_SESSION_ACTIVE) { http_response_code(404); return; }
require_once __DIR__ . '/access.php';
$ptiMenuUser = pti_internal_user($config, $_SESSION);
if ($ptiMenuUser):
    $ptiMenuActive = ($_GET['unit'] ?? '') === 'portal_tiket';
    $ptiMenuStaff = $ptiMenuUser['role'] === 'Staff';
    $ptiMenuDashboard = $ptiMenuStaff ? 'dashboard_staff.php' : 'dashboard_admin.php';
    $ptiMenuText = $ptiMenuActive || !$ptiMenuStaff ? '#fff' : '#000';
    $ptiMenuIcon = $ptiMenuActive ? '#fff' : ($ptiMenuStaff ? '#800000' : 'green');
?>
<li class="nav-item">
  <a href="<?= $ptiMenuDashboard ?>?unit=portal_tiket" class="nav-link<?= $ptiMenuActive ? ' active' : '' ?>"<?= $ptiMenuActive ? ' aria-current="page"' : '' ?>>
    <i class="nav-icon fas fa-ticket-alt" style="color:<?= $ptiMenuIcon ?>" aria-hidden="true"></i>
    <p style="color:<?= $ptiMenuText ?>;white-space:normal">Portal Tiket IT</p>
  </a>
</li>
<?php endif; ?>
