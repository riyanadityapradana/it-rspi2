<?php
if (!isset($review, $canVerify) || !$canVerify) { http_response_code(404); exit; }
?>
<dialog class="portal-uat-dialog" id="uat-verification" aria-labelledby="uat-verification-title" data-open-on-error="<?= $formError ? 'true' : 'false' ?>">
  <div class="portal-dialog-heading"><div><p class="portal-eyebrow">KONFIRMASI PEMOHON</p><h2 id="uat-verification-title">Verifikasi Serah Terima</h2></div><button class="portal-dialog-close" type="button" data-uat-close aria-label="Tutup formulir verifikasi">&times;</button></div>
  <p class="field-help">Tinjau hasil penyelesaian sebelum menyetujui serah terima tiket <strong><?= portal_escape($ticket['no_tiket']) ?></strong>.</p>
  <form method="post" action="<?= portal_escape('uat.php?token='.rawurlencode($token)) ?>" id="uat-verification-form">
    <input type="hidden" name="csrf" value="<?= portal_escape($_SESSION['pt_csrf'] ?? '') ?>">
    <input type="hidden" name="ttd_penerima" id="uat-signature-data" value="">
    <?php if($formError): ?><div class="portal-notice portal-error" role="alert" tabindex="-1" data-uat-server-error><?= portal_escape($formError) ?> Silakan bubuhkan kembali tanda tangan penerima.</div><?php endif; ?>
    <div class="portal-notice portal-error" id="uat-client-error" role="alert" tabindex="-1" hidden></div>
    <fieldset class="portal-uat-status"><legend>Status Pengujian (UAT) *</legend>
      <?php foreach(['Berhasil','Diterima dengan catatan'] as $option): ?><label><input type="radio" name="status_pengujian_uat" value="<?= portal_escape($option) ?>" <?= $uatValues['status_pengujian_uat']===$option ? 'checked' : '' ?> required> <?= portal_escape($option) ?></label><?php endforeach; ?>
    </fieldset>
    <label class="portal-field" for="uat-notes">Catatan Pengujian <span id="uat-notes-required"></span>
      <textarea id="uat-notes" name="catatan_pengujian" rows="3" maxlength="10000" aria-describedby="uat-notes-help"><?= portal_escape($uatValues['catatan_pengujian']) ?></textarea><small id="uat-notes-help">Wajib jika memilih Diterima dengan catatan; opsional jika Berhasil.</small>
    </label>
    <div class="portal-field portal-receiver-field"><label for="uat-receiver-name">Nama Penerima *</label>
      <select id="uat-receiver-name" name="id_pegawai_penerima" data-search-url="uat-pegawai.php" data-uat-token="<?= portal_escape($token) ?>" aria-describedby="uat-receiver-help uat-receiver-status" required>
        <option value=""></option>
        <?php if($uatReceiver): ?><option value="<?= (int) $uatReceiver['id'] ?>" selected><?= portal_escape($uatReceiver['nama']) ?></option><?php endif; ?>
      </select>
      <small id="uat-receiver-help">Pengaju awal: <?= portal_escape($ticket['nama_pengaju']) ?>. Hapus pilihan untuk mengganti penerima; ketik minimal 3 huruf dan pilih pegawai dari daftar.</small>
      <small id="uat-receiver-status" role="status" aria-live="polite"><?= portal_escape($uatReceiverError ?? '') ?></small>
    </div>
    <div class="portal-field"><label for="uat-signature">Tanda Tangan Penerima *</label><canvas id="uat-signature" class="signature-canvas" width="900" height="240" tabindex="0" aria-label="Area tanda tangan penerima" aria-describedby="uat-signature-status"></canvas>
      <div class="portal-actions"><button class="login-link clear-signature" type="button" id="uat-clear-signature">Hapus Tanda Tangan</button><span id="uat-signature-status" role="status" aria-live="polite">Tanda tangan belum diisi.</span></div>
    </div>
    <p class="field-help">Dengan menyimpan verifikasi, Anda menyatakan hasil penyelesaian telah diuji dan diterima. Tiket akan ditandai Selesai.</p>
    <div class="portal-actions portal-uat-actions"><button class="portal-button portal-button-verify" type="submit" id="uat-submit">Simpan Verifikasi</button><button class="portal-button portal-button-secondary" type="button" data-uat-close>Batal</button></div>
  </form>
</dialog>
<noscript><p class="portal-notice">Aktifkan JavaScript untuk membuka formulir verifikasi dan membubuhkan tanda tangan.</p></noscript>
