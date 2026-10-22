<?php
if (!defined('PORTAL_TIKET_INTERNAL') || !isset($ptiTicket, $ptiDb, $ptiUser)) { http_response_code(404); exit; }
if ($ptiTicket['status_tiket'] !== 'Menunggu UAT') return;
require_once __DIR__ . '/uat_helpers.php';
header('Referrer-Policy: no-referrer');
try {
    $ptiLink = pti_uat_link_state($ptiDb, $ptiTicket['id_tiket'], $_SERVER);
} catch (DomainException $e) {
    error_log('Portal UAT link: ' . $e->getMessage());
    $ptiLink = ['url'=>'','deadline'=>null,'now'=>new DateTimeImmutable('now'),'active'=>false,'expired'=>false];
}
?>
  <div class="card card-outline card-info" data-uat-card data-uat-expires="<?= $ptiLink['deadline'] ? $ptiLink['deadline']->getTimestamp() * 1000 : 0 ?>" data-uat-server-now="<?= (int) ((float) $ptiLink['now']->format('U.u') * 1000) ?>">
    <div class="card-header"><h2 class="card-title"><i class="fas fa-link mr-1" aria-hidden="true"></i>Tautan UAT Pemohon</h2></div>
    <div class="card-body">
      <label for="pti-uat-url">Magic Link UAT</label>
      <input id="pti-uat-url" class="form-control" type="text" readonly value="<?= pti_e($ptiLink['url']) ?>" placeholder="Tautan UAT belum tersedia" aria-describedby="pti-uat-expiry pti-uat-copy-status" autocomplete="off" spellcheck="false">
      <p class="mt-2 mb-3 text-muted" id="pti-uat-expiry">
        <span data-uat-expiry-label class="<?= $ptiLink['expired'] ? 'text-danger' : '' ?>"><?php if ($ptiLink['expired']): ?>Tautan telah kedaluwarsa.<?php elseif ($ptiLink['active']): ?>Tautan aktif.<?php else: ?>Tautan UAT belum tersedia. Hubungi administrator untuk memeriksa data tiket.<?php endif; ?></span>
        <?php if ($ptiLink['deadline']): ?>Batas waktu: <?= pti_e($ptiLink['deadline']->format('d-m-Y H:i:s T')) ?>.<?php endif; ?>
      </p>
      <button class="btn <?= $ptiLink['active'] ? 'btn-primary' : 'btn-secondary' ?>" type="button" data-uat-copy <?= $ptiLink['active'] ? '' : 'disabled' ?>><i class="fas fa-copy mr-1" aria-hidden="true"></i>Salin Tautan</button>
      <span id="pti-uat-copy-status" class="d-block mt-2 small" role="status" aria-live="polite" data-uat-copy-status></span>
    </div>
  </div>
  <script src="../assets/dist/js/portal-uat-copy.js" defer></script>
