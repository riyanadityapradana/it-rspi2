<?php
if (!defined('PORTAL_TIKET_INTERNAL') || !isset($ptiTicket, $ptiDb, $ptiUser)) { http_response_code(404); exit; }
$ptiAnalyses = pt_rows($ptiDb, 'SELECT prioritas,analisis_unit_it,keputusan FROM tiket_analisis_it WHERE id_tiket=? ORDER BY id', [$ptiTicket['id_tiket']]);
?>
<?php foreach ($ptiAnalyses as $ptiAnalysis): ?>
  <div class="card card-outline card-primary">
    <div class="card-header"><h2 class="card-title">Analisis IT</h2></div>
    <div class="card-body">
      <p>Prioritas: <strong><?= pti_e($ptiAnalysis['prioritas']) ?></strong></p>
      <h3 class="h6 font-weight-bold">Analisis Unit IT</h3><p class="pti-details"><?= pti_e($ptiAnalysis['analisis_unit_it']) ?></p>
      <h3 class="h6 font-weight-bold">Keputusan</h3><p class="pti-details mb-0"><?= pti_e($ptiAnalysis['keputusan']) ?></p>
    </div>
  </div>
<?php endforeach; ?>
<?php if ($ptiTicket['status_tiket'] === 'Menunggu Analisis'): ?>
  <div class="card card-outline card-warning">
    <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
      <p class="mb-2 mr-3">Tiket menunggu analisis dari Unit IT.</p>
      <button class="btn btn-primary mb-2" type="button" data-toggle="modal" data-target="#pti-analysis-modal"><i class="fas fa-search mr-1" aria-hidden="true"></i>Analisis</button>
    </div>
  </div>
  <div class="modal fade" id="pti-analysis-modal" tabindex="-1" role="dialog" aria-labelledby="pti-analysis-title" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <form class="modal-content" method="post" action="<?= pti_e($ptiUrl(['ticket' => (int) $ptiTicket['id_tiket']])) ?>">
          <div class="modal-header"><h2 class="modal-title h5" id="pti-analysis-title">Analisis IT — <?= pti_e($ptiTicket['no_tiket']) ?></h2>
            <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button></div>
          <div class="modal-body">
            <?php if ($ptiAnalysisError): ?><div class="alert alert-danger" role="alert"><?= pti_e($ptiAnalysisError) ?></div><?php endif; ?>
            <input type="hidden" name="csrf" value="<?= pti_e($_SESSION['pti_analysis_csrf']) ?>">
            <input type="hidden" name="action" value="save_analysis">
            <fieldset class="form-group">
              <legend class="h6 font-weight-bold">Prioritas <span class="text-danger">*</span></legend>
              <?php foreach (['Tinggi','Sedang','Rendah'] as $ptiPriority): ?>
              <div class="custom-control custom-radio custom-control-inline">
                <input class="custom-control-input" type="radio" id="pti-prioritas-<?= strtolower($ptiPriority) ?>" name="prioritas" value="<?= $ptiPriority ?>" required <?= $ptiAnalysisValues['prioritas'] === $ptiPriority ? 'checked' : '' ?>>
                <label class="custom-control-label" for="pti-prioritas-<?= strtolower($ptiPriority) ?>"><?= $ptiPriority ?></label>
              </div>
              <?php endforeach; ?>
            </fieldset>
            <div class="form-group"><label for="pti-analisis">Analisis Unit IT <span class="text-danger">*</span></label>
              <textarea class="form-control" id="pti-analisis" name="analisis_unit_it" rows="5" maxlength="10000" required><?= pti_e($ptiAnalysisValues['analisis_unit_it']) ?></textarea></div>
            <div class="form-group"><label for="pti-keputusan">Keputusan <span class="text-danger">*</span></label>
              <textarea class="form-control" id="pti-keputusan" name="keputusan" rows="4" maxlength="10000" required><?= pti_e($ptiAnalysisValues['keputusan']) ?></textarea></div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Batal</button>
            <button class="btn btn-primary" type="submit"><i class="fas fa-save mr-1" aria-hidden="true"></i>Simpan Analisis</button>
          </div>
        </form>
    </div>
  </div>
  <?php if ($ptiAnalysisError): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (window.jQuery) window.jQuery('#pti-analysis-modal').modal('show');
    });
  </script>
  <?php endif; ?>
<?php endif; ?>
