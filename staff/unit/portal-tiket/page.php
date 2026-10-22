<?php
// Hanya dimuat melalui router dashboard yang sudah memeriksa sesi login.
if (!defined('PORTAL_TIKET_INTERNAL')) { http_response_code(404); exit; }
if (session_status() !== PHP_SESSION_ACTIVE || !isset($config) || !($config instanceof mysqli)) {
    http_response_code(403); echo '<div class="alert alert-danger">Akses Portal Tiket IT ditolak.</div>'; return;
}
require_once __DIR__ . '/access.php';
try {
    $ptiUser = pti_internal_user($config, $_SESSION);
    if (!$ptiUser) { http_response_code(403); echo '<div class="alert alert-danger">Akses Portal Tiket IT ditolak.</div>'; return; }
} catch (Throwable $e) {
    error_log('Portal internal authentication: ' . $e->getMessage());
    http_response_code(503); echo '<div class="alert alert-danger">Akses belum dapat diverifikasi.</div>'; return;
}
require_once __DIR__ . '/queries.php';
function pti_e($text) { return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8'); }
function pti_status_class($status) {
    return ['Menunggu Analisis' => 'warning', 'Dikerjakan' => 'primary', 'Ditolak' => 'danger', 'Menunggu UAT' => 'info', 'Selesai' => 'success'][$status] ?? 'secondary';
}
$ptiDashboard = $ptiUser['role'] === 'Staff' ? 'dashboard_staff.php' : 'dashboard_admin.php';
$ptiMasterUrl = $ptiDashboard . '?unit=portal_tiket&view=master_sistem';
if (($_GET['view'] ?? '') === 'master_sistem') {
    require __DIR__ . '/master_page.php';
    return;
}
$ptiFilters = pti_filters($_GET);
$ptiUrl = function ($extra = []) use ($ptiDashboard, $ptiFilters) {
    return $ptiDashboard . '?' . http_build_query(array_merge(['unit' => 'portal_tiket'], $ptiFilters, $extra));
};
$ptiStatuses = ['Menunggu Analisis', 'Dikerjakan', 'Ditolak', 'Menunggu UAT', 'Selesai'];
$ptiDetailView = array_key_exists('ticket', $_GET);
header('Cache-Control: no-store');
if (empty($_SESSION['pti_analysis_csrf'])) $_SESSION['pti_analysis_csrf'] = bin2hex(random_bytes(32));
$ptiAnalysisError = null;
$ptiAnalysisValues = ['prioritas' => '', 'analisis_unit_it' => '', 'keputusan' => ''];
$ptiAnalysisFlash = $_SESSION['pti_analysis_flash'] ?? null;
unset($_SESSION['pti_analysis_flash']);
if (empty($_SESSION['pti_handover_csrf'])) $_SESSION['pti_handover_csrf'] = bin2hex(random_bytes(32));
$ptiHandoverError = null;
$ptiHandoverDescription = '';
$ptiHandoverFlash = $_SESSION['pti_handover_flash'] ?? null;
unset($_SESSION['pti_handover_flash']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ptiHandoverRequest = ($_POST['action'] ?? null) === 'save_handover';
    try {
        if ($ptiHandoverRequest) {
            require_once __DIR__ . '/handover_helpers.php';
            $_SESSION['pti_handover_flash'] = pti_handover_save(pt_connection(), $config, $_SESSION, 'POST', $_GET['ticket'] ?? null, $_POST);
            $ptiRedirect = $ptiUrl(['ticket' => (int) $_GET['ticket']]);
        } else {
            require_once __DIR__ . '/analysis_helpers.php';
            $_SESSION['pti_analysis_flash'] = pti_analysis_save(pt_connection(), $config, $_SESSION, 'POST', $_GET['ticket'] ?? null, $_POST);
            $ptiRedirect = $ptiUrl();
        }
        header('Location: ' . $ptiRedirect, true, 303);
        exit;
    } catch (DomainException $e) {
        http_response_code(in_array($e->getCode(), [403, 404, 405, 409, 422], true) ? $e->getCode() : 422);
        if ($ptiHandoverRequest) $ptiHandoverError = $e->getMessage();
        else $ptiAnalysisError = $e->getMessage();
    } catch (Throwable $e) {
        error_log('Portal ' . ($ptiHandoverRequest ? 'handover' : 'analysis') . ' save: ' . $e->getMessage());
        http_response_code(503);
        if ($ptiHandoverRequest) $ptiHandoverError = 'Serah terima belum dapat disimpan. Silakan coba lagi. Jika berulang, hubungi administrator.';
        else $ptiAnalysisError = 'Analisis belum dapat disimpan. Silakan coba lagi. Jika berulang, hubungi administrator.';
    }
    foreach ($ptiAnalysisValues as $key => $unused) $ptiAnalysisValues[$key] = is_string($_POST[$key] ?? null) ? $_POST[$key] : '';
    $ptiHandoverDescription = is_string($_POST['deskripsi_penyelesaian'] ?? null) ? $_POST['deskripsi_penyelesaian'] : '';
}
?>
<style>
  .pti-queue { padding-bottom: 55px; }
  .pti-queue td { vertical-align: middle; overflow-wrap: anywhere; }
  .pti-queue .pti-details { white-space: pre-wrap; overflow-wrap: anywhere; }
  .pti-queue .pti-heading { font-size: 1.8rem; margin: 0; }
</style>
<section class="pti-queue" aria-labelledby="pti-heading">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div><h1 id="pti-heading" class="pti-heading"><i class="fas fa-ticket-alt mr-2" aria-hidden="true"></i>Portal Tiket IT</h1>
    <p class="text-muted mb-0">Antrean pengajuan layanan IT dari portal publik.</p></div>
    <div class="mt-2 ml-sm-auto">
      <?php if ($ptiDetailView): ?><a class="btn btn-outline-secondary mr-2" href="<?= pti_e($ptiUrl()) ?>"><i class="fas fa-arrow-left mr-1" aria-hidden="true"></i>Kembali ke Antrean</a><?php endif; ?>
      <a class="btn btn-primary" href="<?= pti_e($ptiMasterUrl) ?>"><i class="fas fa-cog mr-1" aria-hidden="true"></i>Master Sistem</a>
    </div>
  </div>
  <?php if ($ptiAnalysisFlash): ?><div class="alert alert-success" role="status"><?= pti_e($ptiAnalysisFlash) ?></div><?php endif; ?>
  <?php if ($ptiAnalysisError): ?><div class="alert alert-danger" role="alert"><?= pti_e($ptiAnalysisError) ?></div><?php endif; ?>
  <?php if ($ptiHandoverFlash): ?><div class="alert alert-success" role="status"><?= pti_e($ptiHandoverFlash) ?></div><?php endif; ?>
  <?php if ($ptiHandoverError): ?><div class="alert alert-danger" role="alert"><?= pti_e($ptiHandoverError) ?></div><?php endif; ?>
<?php
try {
    $ptiDb = pt_connection();
    if ($ptiDetailView) {
        $ptiTicketId = filter_var($_GET['ticket'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 2147483647]]);
        $ptiDetail = $ptiTicketId ? pti_detail($ptiDb, $ptiTicketId) : null;
        if (!$ptiDetail) { http_response_code(404); echo '<div class="alert alert-warning">Tiket tidak ditemukan.</div></section>'; return; }
        $ptiTicket = $ptiDetail['ticket'];
?>
  <div class="card card-outline card-primary">
    <div class="card-header d-flex flex-wrap align-items-center"><h2 class="card-title mr-auto"><?= pti_e($ptiTicket['no_tiket']) ?></h2><span class="badge badge-<?= pti_status_class($ptiTicket['status_tiket']) ?>"><?= pti_e($ptiTicket['status_tiket']) ?></span></div>
    <div class="card-body">
      <dl class="row mb-0">
        <?php foreach (['jenis_tiket' => 'Jenis Tiket', 'nama_sistem' => 'Sistem Terkait', 'tgl_pengajuan' => 'Tanggal Pengajuan', 'unit_pemohon' => 'Unit Pemohon', 'nama_pengaju' => 'Nama Pengaju', 'kontak_pengaju' => 'WhatsApp'] as $ptiKey => $ptiLabel): ?>
        <dt class="col-md-3"><?= pti_e($ptiLabel) ?></dt><dd class="col-md-9 pti-details"><?= pti_e($ptiKey === 'tgl_pengajuan' ? date('d-m-Y H:i', strtotime($ptiTicket[$ptiKey])) : ($ptiTicket[$ptiKey] ?? '-')) ?></dd>
        <?php endforeach; ?>
      </dl>
    </div>
  </div>
  <?php foreach ($ptiDetail['details'] as $ptiIndex => $ptiRequest): ?>
  <div class="card">
    <div class="card-header"><h2 class="card-title">Rincian Pengajuan <?= count($ptiDetail['details']) > 1 ? $ptiIndex + 1 : '' ?></h2></div>
    <div class="card-body">
      <?php
      $ptiFields = ['nama_perubahan_fitur' => 'Nama Perubahan/Fitur'];
      $ptiFields += $ptiTicket['jenis_tiket'] === 'Change Request'
          ? ['kondisi_masalah' => 'Kondisi/masalah Saat Ini', 'perubahan_diharapkan' => 'Perubahan yang Diharapkan', 'dampak_modul' => 'Dampak Terhadap Unit/Modul/Integrasi']
          : ['latar_belakang' => 'Latar Belakang', 'deskripsi_alur_kerja' => 'Deskripsi Alur Kerja', 'target_pengguna' => 'Target Pengguna', 'output_diharapkan' => 'Output yang Diharapkan'];
      foreach ($ptiFields as $ptiKey => $ptiLabel): ?>
      <h3 class="h6 font-weight-bold"><?= pti_e($ptiLabel) ?></h3><p class="pti-details"><?= pti_e($ptiRequest[$ptiKey] ?? '-') ?></p>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; ?>
  <?php if (!$ptiDetail['details']): ?><div class="alert alert-info">Rincian pengajuan belum tersedia.</div><?php endif; ?>
  <?php require __DIR__ . '/analysis_detail.php'; ?>
  <?php require __DIR__ . '/handover_detail.php'; ?>
  <?php require __DIR__ . '/uat_link.php'; ?>
<?php
    } else {
        $ptiQueue = pti_queue($ptiDb, $ptiFilters);
        $ptiFilters['page'] = $ptiQueue['page'];
        $ptiSystems = pt_rows($ptiDb, 'SELECT id_sistem,nama_sistem FROM master_sistem ORDER BY nama_sistem');
        $ptiCounts = array_fill_keys($ptiStatuses, 0);
        foreach (pt_rows($ptiDb, 'SELECT status_tiket,COUNT(*) total FROM tiket_pengajuan GROUP BY status_tiket') as $ptiCount) $ptiCounts[$ptiCount['status_tiket']] = (int) $ptiCount['total'];
?>
  <link rel="stylesheet" href="../assets/dist/css/portal-tiket-summary.css">
  <div class="row pti-summary" aria-label="Ringkasan seluruh tiket">
    <?php
    $ptiStats = [
        ['label' => 'Total Tiket', 'count' => array_sum($ptiCounts), 'theme' => 'total', 'icon' => 'fa-ticket-alt', 'caption' => 'Seluruh pengajuan'],
        ['label' => 'Menunggu Analisis', 'count' => $ptiCounts['Menunggu Analisis'], 'theme' => 'waiting', 'icon' => 'fa-search', 'caption' => 'Perlu ditinjau'],
        ['label' => 'Dikerjakan', 'count' => $ptiCounts['Dikerjakan'], 'theme' => 'working', 'icon' => 'fa-laptop-code', 'caption' => 'Sedang diproses'],
        ['label' => 'Menunggu UAT', 'count' => $ptiCounts['Menunggu UAT'], 'theme' => 'uat', 'icon' => 'fa-clipboard-check', 'caption' => 'Menunggu pengujian']
    ];
    foreach ($ptiStats as $ptiStatIndex => $ptiStat): ?>
    <div class="col-6 col-lg-3">
      <article class="pti-stat pti-stat--<?= $ptiStat['theme'] ?>" style="--stat-delay:<?= $ptiStatIndex * 80 ?>ms" aria-labelledby="pti-stat-<?= $ptiStat['theme'] ?>">
        <span class="pti-stat-icon" aria-hidden="true"><i class="fas <?= $ptiStat['icon'] ?>"></i></span>
        <h2 id="pti-stat-<?= $ptiStat['theme'] ?>" class="pti-stat-label"><?= pti_e($ptiStat['label']) ?></h2>
        <div class="pti-stat-value"><?= $ptiStat['count'] ?></div>
        <div class="pti-stat-caption"><span class="pti-stat-dot" aria-hidden="true"></span><?= pti_e($ptiStat['caption']) ?></div>
      </article>
    </div>
    <?php endforeach; ?>
  </div>
  <div class="card card-outline card-primary">
    <div class="card-header"><h2 class="card-title">Daftar Antrean Tiket IT</h2></div>
    <div class="card-body">
      <form method="get" action="<?= pti_e($ptiDashboard) ?>" class="mb-3">
        <input type="hidden" name="unit" value="portal_tiket">
        <div class="form-row align-items-end">
          <div class="form-group col-lg-4"><label for="pti-q">Cari tiket</label><input id="pti-q" name="q" class="form-control" maxlength="100" value="<?= pti_e($ptiFilters['q']) ?>" placeholder="No tiket, pengaju, unit, atau perubahan"></div>
          <div class="form-group col-sm-6 col-lg-3"><label for="pti-status">Status</label><select class="form-control" id="pti-status" name="status"><option value="">Semua status</option><?php foreach ($ptiStatuses as $ptiStatus): ?><option value="<?= pti_e($ptiStatus) ?>" <?= $ptiFilters['status'] === $ptiStatus ? 'selected' : '' ?>><?= pti_e($ptiStatus) ?></option><?php endforeach; ?></select></div>
          <div class="form-group col-sm-6 col-lg-3"><label for="pti-sistem">Sistem</label><select class="form-control" id="pti-sistem" name="sistem"><option value="">Semua sistem</option><?php foreach ($ptiSystems as $ptiSystem): ?><option value="<?= (int) $ptiSystem['id_sistem'] ?>" <?= $ptiFilters['sistem'] === (int) $ptiSystem['id_sistem'] ? 'selected' : '' ?>><?= pti_e($ptiSystem['nama_sistem']) ?></option><?php endforeach; ?></select></div>
          <div class="form-group col-lg-2"><button class="btn btn-primary" type="submit">Tampilkan</button> <a class="btn btn-outline-secondary" href="<?= pti_e($ptiDashboard . '?unit=portal_tiket') ?>">Reset</a></div>
        </div>
      </form>
      <p class="text-muted small">Ditemukan <?= $ptiQueue['total'] ?> tiket. Antrean aktif ditampilkan lebih dahulu, dari pengajuan paling awal.</p>
      <div class="table-responsive">
        <table class="table table-bordered table-hover" aria-label="Daftar antrean tiket IT">
          <thead><tr><th scope="col">No Tiket / Tanggal</th><th scope="col">Pemohon / Unit</th><th scope="col">Jenis / Sistem</th><th scope="col">Perubahan/Fitur</th><th scope="col">Status</th><th scope="col">Aksi</th></tr></thead>
          <tbody>
            <?php foreach ($ptiQueue['rows'] as $ptiRow): ?>
            <tr>
              <td><strong><?= pti_e($ptiRow['no_tiket']) ?></strong><br><small class="text-muted"><?= pti_e(date('d-m-Y H:i', strtotime($ptiRow['tgl_pengajuan']))) ?></small></td>
              <td><?= pti_e($ptiRow['nama_pengaju']) ?><br><small class="text-muted"><?= pti_e($ptiRow['unit_pemohon']) ?></small></td>
              <td><?= pti_e($ptiRow['jenis_tiket']) ?><br><small class="text-muted"><?= pti_e($ptiRow['nama_sistem'] ?? '-') ?></small></td>
              <td><?= pti_e($ptiRow['nama_perubahan_fitur'] ?? '-') ?></td>
              <td><span class="badge badge-<?= pti_status_class($ptiRow['status_tiket']) ?>"><?= pti_e($ptiRow['status_tiket']) ?></span></td>
              <td><div class="d-flex align-items-center">
                <a class="btn btn-sm btn-outline-primary mr-1" href="<?= pti_e($ptiUrl(['ticket' => (int) $ptiRow['id_tiket'], 'page' => $ptiQueue['page']])) ?>" title="Lihat Detail" aria-label="<?= pti_e('Lihat detail tiket '.$ptiRow['no_tiket']) ?>"><i class="fas fa-search" aria-hidden="true"></i><span class="sr-only">Detail</span></a>
                <?php if ($ptiRow['status_tiket'] === 'Selesai'): ?><a class="btn btn-sm btn-outline-success" href="../staff/unit/portal-tiket/pdf.php?id=<?= (int) $ptiRow['id_tiket'] ?>" target="_blank" rel="noopener" title="Cetak PDF" aria-label="<?= pti_e('Cetak PDF tiket '.$ptiRow['no_tiket']) ?>"><i class="fas fa-print" aria-hidden="true"></i><span class="sr-only">Cetak PDF</span></a><?php endif; ?>
              </div></td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$ptiQueue['rows']): ?><tr><td colspan="6" class="text-center text-muted py-4">Belum ada tiket yang sesuai dengan pencarian atau filter.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
      <div class="d-flex flex-wrap justify-content-between align-items-center">
        <span class="text-muted small">Halaman <?= $ptiQueue['page'] ?> dari <?= $ptiQueue['pages'] ?></span>
        <nav aria-label="Navigasi halaman antrean"><ul class="pagination pagination-sm mb-0">
          <li class="page-item<?= $ptiQueue['page'] <= 1 ? ' disabled' : '' ?>"><?php if ($ptiQueue['page'] > 1): ?><a class="page-link" href="<?= pti_e($ptiUrl(['page' => $ptiQueue['page'] - 1])) ?>">Sebelumnya</a><?php else: ?><span class="page-link" aria-disabled="true">Sebelumnya</span><?php endif; ?></li>
          <li class="page-item<?= $ptiQueue['page'] >= $ptiQueue['pages'] ? ' disabled' : '' ?>"><?php if ($ptiQueue['page'] < $ptiQueue['pages']): ?><a class="page-link" href="<?= pti_e($ptiUrl(['page' => $ptiQueue['page'] + 1])) ?>">Berikutnya</a><?php else: ?><span class="page-link" aria-disabled="true">Berikutnya</span><?php endif; ?></li>
        </ul></nav>
      </div>
    </div>
  </div>
<?php
    }
} catch (Throwable $e) {
    error_log('Portal internal queue: ' . $e->getMessage());
    http_response_code(503);
    echo '<div class="alert alert-danger" role="alert">Antrean tiket belum dapat dimuat. Silakan coba lagi atau hubungi administrator.</div>';
}
?>
</section>
