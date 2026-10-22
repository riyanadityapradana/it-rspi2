<?php
if (!defined('PORTAL_TIKET_INTERNAL') || !isset($ptiTicket, $ptiDb, $ptiUser)) { http_response_code(404); exit; }
// Riwayat tidak mengambil token; tautan hanya dibaca oleh kartu UAT internal terpisah.
$ptiHandovers = pt_rows($ptiDb, 'SELECT tgl_serah_terima,deskripsi_penyelesaian,status_pengujian_uat,catatan_pengujian FROM tiket_serah_terima WHERE id_tiket=? ORDER BY tgl_serah_terima,id', [$ptiTicket['id_tiket']]);
?>
<?php foreach ($ptiHandovers as $ptiHandover): ?>
  <div class="card card-outline card-info">
    <div class="card-header"><h2 class="card-title">Serah Terima Penyelesaian</h2></div>
    <div class="card-body">
      <p>Tanggal Serah Terima: <strong><?= pti_e(date('d-m-Y H:i T', strtotime($ptiHandover['tgl_serah_terima']))) ?></strong></p>
      <h3 class="h6 font-weight-bold">Deskripsi Penyelesaian</h3><p class="pti-details"><?= pti_e($ptiHandover['deskripsi_penyelesaian']) ?></p>
      <p class="mb-0">Status Pengujian (UAT): <strong><?= pti_e($ptiHandover['status_pengujian_uat'] ?? 'Menunggu pengujian pemohon') ?></strong></p>
      <?php if (trim((string) $ptiHandover['catatan_pengujian']) !== ''): ?><h3 class="h6 font-weight-bold mt-3">Catatan Pengujian</h3><p class="pti-details mb-0"><?= pti_e($ptiHandover['catatan_pengujian']) ?></p><?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>
<?php if ($ptiTicket['status_tiket'] === 'Dikerjakan'): ?>
  <div class="card card-outline card-info">
    <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
      <p class="mb-2 mr-3">Catat hasil penyelesaian untuk diserahkan dan diuji oleh pemohon.</p>
      <button class="btn btn-info mb-2 text-wrap" type="button" data-toggle="modal" data-target="#pti-handover-modal"><i class="fas fa-clipboard-check mr-1" aria-hidden="true"></i>Buat Serah Terima Penyelesaian</button>
    </div>
  </div>
  <div class="modal fade" id="pti-handover-modal" tabindex="-1" role="dialog" aria-labelledby="pti-handover-title" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
      <form class="modal-content" method="post" action="<?= pti_e($ptiUrl(['ticket' => (int) $ptiTicket['id_tiket']])) ?>">
        <div class="modal-header"><h2 class="modal-title h5" id="pti-handover-title">Serah Terima Penyelesaian &mdash; <?= pti_e($ptiTicket['no_tiket']) ?></h2>
          <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button></div>
        <div class="modal-body">
          <?php if ($ptiHandoverError): ?><div class="alert alert-danger" role="alert"><?= pti_e($ptiHandoverError) ?></div><?php endif; ?>
          <input type="hidden" name="csrf" value="<?= pti_e($_SESSION['pti_handover_csrf']) ?>">
          <input type="hidden" name="action" value="save_handover">
          <p>Diserahkan oleh: <strong><?= pti_e($ptiUser['nama_lengkap']) ?></strong><br><span class="text-muted">Tanggal serah terima dicatat otomatis saat disimpan.</span></p>
          <div class="form-group"><label for="pti-handover-description">Deskripsi Penyelesaian <span class="text-danger">*</span></label>
            <textarea class="form-control" id="pti-handover-description" name="deskripsi_penyelesaian" rows="7" maxlength="10000" required><?= pti_e($ptiHandoverDescription) ?></textarea></div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Batal</button>
          <button class="btn btn-info" type="submit"><i class="fas fa-save mr-1" aria-hidden="true"></i>Simpan Serah Terima</button>
        </div>
      </form>
    </div>
  </div>
  <?php if ($ptiHandoverError): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (window.jQuery) window.jQuery('#pti-handover-modal').modal('show');
    });
  </script>
  <?php endif; ?>
<?php endif; ?>
