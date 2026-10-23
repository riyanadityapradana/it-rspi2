<?php
require_once __DIR__ . '/access_helpers.php';
pt_public_require();
require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/public_helpers.php';
ptp_readonly();
$filters = ptp_filters($_GET); $detail = null; $error = null;
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 2147483647]]);
if (!$id) { http_response_code(404); $error = 'Tiket tidak ditemukan.'; }
else {
    try {
        $detail = ptp_detail(pt_connection(), $id);
        if (!$detail) { http_response_code(404); $error = 'Tiket tidak ditemukan.'; }
    } catch (Throwable $e) {
        error_log('Portal public detail: ' . $e->getMessage());
        http_response_code(503); $error = 'Detail tiket belum dapat dimuat. Silakan coba lagi beberapa saat.';
    }
}
portal_header('Detail Tiket', 'Rincian pengajuan dan perkembangan penanganan tiket layanan IT.');
?>
      <nav class="portal-public-nav" aria-label="Navigasi detail tiket">
        <a class="login-link" href="<?= portal_escape(ptp_list_url($filters)) ?>"><i class="fas fa-arrow-left" aria-hidden="true"></i>Kembali ke Daftar Tiket</a>
        <a class="login-link" href="./">Ke Portal Tiket</a>
      </nav>
      <?php if ($error): ?><div class="portal-notice portal-error" role="alert"><?= portal_escape($error) ?></div><?php endif; ?>
      <?php if ($detail !== null): $ticket = $detail['ticket']; ?>
      <section class="portal-panel portal-ticket-section" aria-labelledby="ticket-heading">
        <div class="portal-ticket-heading"><h2 id="ticket-heading"><?= portal_escape($ticket['no_tiket']) ?></h2>
          <span class="portal-status portal-status-<?= ptp_status_class($ticket['status_tiket']) ?>"><?= portal_escape($ticket['status_tiket']) ?></span></div>
        <dl class="portal-ticket-meta">
          <div><dt>Tanggal Pengajuan</dt><dd><?= portal_escape(ptp_date($ticket['tgl_pengajuan'])) ?></dd></div>
          <div><dt>Jenis Pengajuan</dt><dd><?= portal_escape($ticket['jenis_tiket']) ?></dd></div>
          <div><dt>Sistem Terkait</dt><dd><?= portal_escape($ticket['nama_sistem'] ?? '-') ?></dd></div>
        </dl>
      </section>
      <?php foreach ($detail['details'] as $index => $request):
        $fields = pt_request_definition($ticket['jenis_tiket'])['fields']; ?>
      <section class="portal-panel portal-ticket-section" aria-labelledby="request-heading-<?= $index ?>">
        <h2 id="request-heading-<?= $index ?>">Rincian <?= $ticket['jenis_tiket'] === 'Fitur Baru' ? 'Fitur Baru' : 'Change Request' ?><?= count($detail['details']) > 1 ? ' ' . ($index + 1) : '' ?></h2>
        <?php foreach ($fields as $key => $field): ?><h3><?= portal_escape($field['label']) ?></h3><p class="portal-ticket-text"><?= portal_escape($request[$key] ?? '-') ?></p><?php endforeach; ?>
      </section>
      <?php endforeach; ?>
      <?php if (!$detail['details']): ?><div class="portal-notice">Rincian pengajuan belum tersedia.</div><?php endif; ?>
      <section class="portal-panel portal-ticket-section" aria-labelledby="analysis-heading">
        <h2 id="analysis-heading">Hasil Analisis IT</h2>
        <?php foreach ($detail['analyses'] as $analysis): ?>
        <div class="portal-ticket-record">
          <p>Prioritas: <strong><?= portal_escape($analysis['prioritas']) ?></strong></p>
          <h3>Analisis Unit IT</h3><p class="portal-ticket-text"><?= portal_escape($analysis['analisis_unit_it']) ?></p>
          <h3>Keputusan</h3><p class="portal-ticket-text"><?= portal_escape($analysis['keputusan']) ?></p>
        </div>
        <?php endforeach; ?>
        <?php if (!$detail['analyses']): ?><p>Hasil analisis IT belum tersedia.</p><?php endif; ?>
      </section>
      <section class="portal-panel portal-ticket-section" aria-labelledby="completion-heading">
        <h2 id="completion-heading">Hasil Penyelesaian</h2>
        <?php foreach ($detail['handovers'] as $handover): ?>
        <div class="portal-ticket-record">
          <h3>Tanggal Serah Terima</h3><p><?= portal_escape(ptp_date($handover['tgl_serah_terima'])) ?></p>
          <h3>Deskripsi Penyelesaian</h3><p class="portal-ticket-text"><?= portal_escape($handover['deskripsi_penyelesaian']) ?></p>
          <h3>Hasil Pengujian UAT</h3><p><?= portal_escape($handover['status_pengujian_uat'] ?? 'Menunggu pengujian pemohon') ?></p>
          <?php if (trim((string) $handover['catatan_pengujian']) !== ''): ?><h3>Catatan Pengujian</h3><p class="portal-ticket-text"><?= portal_escape($handover['catatan_pengujian']) ?></p><?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php if (!$detail['handovers']): ?><p>Hasil penyelesaian belum tersedia.</p><?php endif; ?>
      </section>
      <?php endif; ?>
<?php portal_footer(); ?>
