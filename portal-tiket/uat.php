<?php
require_once __DIR__ . '/access_helpers.php';
pt_public_require();
require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/uat_helpers.php';
require_once __DIR__ . '/signature_helpers.php';
header('Cache-Control: no-store'); header('Referrer-Policy: no-referrer');
header('X-Content-Type-Options: nosniff'); header('X-Robots-Tag: noindex, nofollow');
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET','HEAD','POST'], true)) { header('Allow: GET, HEAD, POST'); http_response_code(405); exit('Metode permintaan tidak didukung.'); }
$review = null; $error = null; $formError = null;
$uatValues = ['status_pengujian_uat'=>'', 'id_pegawai_penerima'=>'', 'catatan_pengujian'=>''];
$uatReceiver = null; $uatReceiverError = null;
try {
    $token = $_GET['token'] ?? null;
    if (!pt_uat_token_valid($token)) throw new DomainException('Tautan UAT tidak valid atau tidak tersedia.', 404);
    $review = pt_uat_review(pt_connection(), $token);
    if (!$review['completed'] || $_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($_SERVER['REQUEST_METHOD'] !== 'HEAD') pt_session();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        foreach ($uatValues as $key=>$value) if (is_string($_POST[$key] ?? null)) $uatValues[$key] = $_POST[$key];
        try {
            pt_uat_confirm(pt_connection(), $token, 'POST', $_POST, $_SESSION);
            header('Referrer-Policy: no-referrer');
            header('Location: uat.php?token='.rawurlencode($token), true, 303);
            exit;
        } catch (DomainException $e) {
            if (in_array($e->getCode(), [404,410], true)) throw $e;
            http_response_code(in_array($e->getCode(), [403,409,422], true) ? $e->getCode() : 422);
            $formError = $e->getMessage();
        } catch (Throwable $e) {
            http_response_code(503); error_log('Portal UAT confirmation unavailable.');
            $formError = 'Verifikasi belum dapat disimpan. Silakan coba lagi beberapa saat.';
        }
    }
    pt_prepare_signatures($review,$token,$_SERVER);
}
catch (DomainException $e) { $review=null; http_response_code(in_array($e->getCode(),[404,410],true)?$e->getCode():503); $error = $e->getMessage(); }
catch (Throwable $e) { $review=null; error_log('Portal UAT review unavailable.'); http_response_code(503); $error = 'Hasil serah terima belum dapat dimuat. Silakan coba lagi beberapa saat.'; }
header('Referrer-Policy: no-referrer');
$canVerify = $review && !$review['completed'] && $review['handover'] && $review['handover']['status_pengujian_uat'] === null;
if ($canVerify) {
    try {
        $uatReceiver = $_SERVER['REQUEST_METHOD'] === 'POST'
            ? pt_uat_receiver($uatValues['id_pegawai_penerima'])
            : pt_uat_default_receiver($review['ticket']['nama_pengaju']);
        if (!$uatReceiver) $uatReceiverError = 'Nama pengaju tidak ditemukan secara unik dalam daftar pegawai aktif. Cari dan pilih Nama Penerima.';
    } catch (DomainException $e) { $uatReceiverError = $e->getMessage(); }
}
portal_header('Serah Terima & UAT', 'Tinjau hasil penyelesaian layanan IT yang diserahkan kepada unit pemohon.');
?>
      <?php if ($error): ?><div class="portal-notice portal-error" role="alert"><?= portal_escape($error) ?></div><?php endif; ?>
      <?php if ($review): $ticket = $review['ticket']; ?>
      <section class="portal-panel portal-ticket-section" aria-labelledby="uat-ticket">
        <div class="portal-ticket-heading"><h2 id="uat-ticket"><?= portal_escape($ticket['no_tiket']) ?></h2><span class="portal-status <?= $review['completed'] ? 'portal-status-done' : 'portal-status-uat' ?>"><?= $review['completed'] ? 'Selesai' : 'Menunggu UAT' ?></span></div>
        <dl class="portal-ticket-meta">
          <?php foreach(['jenis_tiket'=>'Jenis Pengajuan','nama_sistem'=>'Sistem Terkait','unit_pemohon'=>'Unit Pemohon','nama_pengaju'=>'Nama Pengaju','kontak_pengaju'=>'Kontak / WhatsApp'] as $key=>$label): ?><div><dt><?= portal_escape($label) ?></dt><dd><?= portal_escape($ticket[$key] ?? '-') ?></dd></div><?php endforeach; ?>
          <div><dt>Tanggal Pengajuan</dt><dd><?= portal_escape(date('d-m-Y H:i:s T',strtotime($ticket['tgl_pengajuan']))) ?></dd></div><div><dt>Batas Waktu UAT</dt><dd><?= portal_escape(pt_uat_deadline($ticket['token_expired_at'])->format('d-m-Y H:i:s T')) ?></dd></div>
        </dl>
      </section>
      <?php if ($review['completed']): ?><div class="portal-notice portal-success" role="status"><strong>Serah terima berhasil diverifikasi.</strong> Tiket telah selesai. Hasil penerimaan ditampilkan di bagian bawah halaman.</div><?php endif; ?>
      <?php if ($formError && !$canVerify): ?><div class="portal-notice portal-error" role="alert"><?= portal_escape($formError) ?></div><?php endif; ?>
      <section class="portal-panel portal-ticket-section" aria-labelledby="uat-request">
        <h2 id="uat-request">Rincian Pengajuan</h2>
        <?php foreach($review['details'] as $index=>$request): ?><div class="portal-ticket-record">
        <?php if(count($review['details'])>1): ?><h3>Pengajuan <?= $index+1 ?></h3><?php endif; ?>
        <?php foreach(pt_request_definition($ticket['jenis_tiket'])['fields'] as $key=>$field): ?><h3><?= portal_escape($field['label']) ?></h3><p class="portal-ticket-text"><?= portal_escape($request[$key] ?? '-') ?></p><?php endforeach; ?>
        </div><?php endforeach; ?>
        <?php if(!$review['details']): ?><p>Rincian pengajuan belum tersedia.</p><?php endif; ?>
        <div class="portal-requester-approval"><h3>Diajukan oleh Pemohon</h3><p><strong><?= portal_escape($ticket['nama_pengaju']) ?></strong></p>
        <?php if($ticket['ttd_pengaju']): ?><img class="portal-requester-signature" src="<?= portal_escape($ticket['ttd_pengaju']) ?>" alt="Tanda tangan pengaju"><?php else: ?><p>Tanda tangan pengaju belum tersedia.</p><?php endif; ?>
        </div>
      </section>
      <section class="portal-panel portal-ticket-section" aria-labelledby="uat-analysis"><h2 id="uat-analysis">Analisis IT</h2>
        <?php foreach($review['analyses'] as $record): ?><div class="portal-ticket-record"><p>Prioritas: <strong><?= portal_escape($record['prioritas']) ?></strong></p><h3>Analisis Unit IT</h3><p class="portal-ticket-text"><?= portal_escape($record['analisis_unit_it']) ?></p><h3>Keputusan</h3><p class="portal-ticket-text"><?= portal_escape($record['keputusan']) ?></p>
          <?php $ptSignatureRecord=$record;$ptSignatureKind='analisis';require __DIR__.'/signature_block.php'; ?>
        </div><?php endforeach; ?>
        <?php if(!$review['analyses']): ?><p>Hasil analisis IT belum tersedia.</p><?php endif; ?>
      </section>
      <section class="portal-panel portal-ticket-section" aria-labelledby="uat-completion">
        <div class="portal-completion-heading"><h2 id="uat-completion">Hasil Penyelesaian IT</h2>
          <?php if ($canVerify): ?><button class="portal-button portal-button-verify" type="button" id="open-uat-verification" data-uat-open aria-haspopup="dialog" aria-controls="uat-verification"><i class="fas fa-check-circle" aria-hidden="true"></i>Verifikasi Serah Terima</button><?php endif; ?>
        </div>
        <?php foreach($review['handovers'] as $record): ?><div class="portal-ticket-record"><h3>Deskripsi Penyelesaian</h3><p class="portal-ticket-text"><?= portal_escape($record['deskripsi_penyelesaian']) ?></p>
          <?php $ptSignatureRecord=$record;$ptSignatureKind='serah_terima';require __DIR__.'/signature_block.php'; ?>
        </div><?php endforeach; ?>
        <?php if(!$review['handovers']): ?><p>Hasil serah terima belum tersedia. Hubungi tim IT untuk tindak lanjut.</p><?php endif; ?>
      </section>
      <?php if($review['completed']): $accepted = $review['handover']; ?>
      <section class="portal-panel portal-ticket-section" aria-labelledby="uat-receiver-result">
        <h2 id="uat-receiver-result">Hasil Serah Terima Pemohon</h2>
        <dl class="portal-ticket-meta"><div><dt>Status Pengujian (UAT)</dt><dd><?= portal_escape($accepted['status_pengujian_uat']) ?></dd></div><div><dt>Nama Penerima</dt><dd><?= portal_escape($accepted['nama_penerima']) ?></dd></div></dl>
        <?php if($accepted['catatan_pengujian'] !== null): ?><h3>Catatan Pengujian</h3><p class="portal-ticket-text"><?= portal_escape($accepted['catatan_pengujian']) ?></p><?php endif; ?>
        <div class="portal-requester-approval"><h3>Tanda Tangan Penerima</h3>
          <?php if($accepted['ttd_penerima']): ?><img class="portal-requester-signature" src="<?= portal_escape($accepted['ttd_penerima']) ?>" alt="Tanda tangan penerima"><?php else: ?><p>Gambar tanda tangan penerima belum dapat ditampilkan.</p><?php endif; ?>
        </div>
      </section>
      <?php endif; ?>
      <?php if($canVerify): require __DIR__.'/uat_modal.php'; ?>
      <link rel="stylesheet" href="../assets/dist/plugins/select2/css/select2.min.css">
      <script src="../assets/dist/plugins/jquery/jquery.min.js"></script>
      <script src="../assets/dist/plugins/select2/js/select2.full.min.js"></script>
      <script src="../assets/dist/js/portal-uat-verification.js" defer></script><?php endif; ?>
      <?php endif; ?>
      <nav class="portal-public-nav" aria-label="Navigasi UAT"><a class="login-link" href="./">Ke Portal Tiket</a></nav>
<?php portal_footer(); ?>
