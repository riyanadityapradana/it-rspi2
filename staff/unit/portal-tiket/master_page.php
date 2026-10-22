<?php
if (!defined('PORTAL_TIKET_INTERNAL') || !isset($ptiUser, $ptiDashboard, $ptiMasterUrl)) { http_response_code(404); exit; }
require_once __DIR__ . '/master_helpers.php';
header('Cache-Control: no-store');
if (empty($_SESSION['pti_master_csrf'])) $_SESSION['pti_master_csrf'] = bin2hex(random_bytes(32));
$masterError = null;
$masterFlash = $_SESSION['pti_master_flash'] ?? null;
unset($_SESSION['pti_master_flash']);
$masterForm = null;
$masterRows = [];
$masterAvailable = false;
try {
    $masterDb = pt_connection();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $result = pti_master_save($masterDb, $config, $_SESSION, 'POST', $_POST);
            $_SESSION['pti_master_flash'] = $result['message'];
            header('Location: ' . $ptiMasterUrl, true, 303);
            exit;
        } catch (DomainException $e) {
            http_response_code(in_array($e->getCode(), [403, 404, 405], true) ? $e->getCode() : 422);
            $masterError = $e->getMessage();
            if (in_array($_POST['action'] ?? null, ['create', 'update'], true)) {
                $masterForm = [];
                foreach (['id_sistem', 'nama_sistem', 'deskripsi', 'is_active'] as $key) {
                    $masterForm[$key] = is_string($_POST[$key] ?? null) ? $_POST[$key] : '';
                }
                $masterForm['editing'] = $_POST['action'] === 'update';
            }
        }
    } elseif ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header('Allow: GET, POST');
        throw new DomainException('Metode permintaan tidak didukung.', 405);
    } elseif (array_key_exists('edit', $_GET)) {
        $id = pt_positive_id($_GET['edit'], 'sistem');
        $masterForm = pt_rows($masterDb, 'SELECT id_sistem,nama_sistem,deskripsi,is_active FROM master_sistem WHERE id_sistem=?', [$id])[0] ?? null;
        if (!$masterForm) throw new DomainException('Sistem tidak ditemukan.', 404);
        $masterForm['editing'] = true;
    } elseif (($_GET['form'] ?? '') === 'create') {
        $masterForm = ['id_sistem' => '', 'nama_sistem' => '', 'deskripsi' => '', 'is_active' => '1', 'editing' => false];
    }
    $masterRows = pt_rows($masterDb, 'SELECT id_sistem,nama_sistem,deskripsi,is_active FROM master_sistem ORDER BY nama_sistem,id_sistem');
    $masterAvailable = true;
} catch (DomainException $e) {
    http_response_code(in_array($e->getCode(), [404, 405], true) ? $e->getCode() : 422);
    $masterError = $e->getMessage();
} catch (Throwable $e) {
    error_log('Portal master sistem: ' . $e->getMessage());
    http_response_code(503);
    $masterError = 'Data sistem belum dapat diproses. Silakan coba lagi.';
}
?>
<style>
  .pti-master { padding-bottom: 55px; }
  .pti-master .pti-heading { font-size: 1.8rem; margin: 0; }
  .pti-master-table { min-width: 650px; }
  .pti-master-table td { vertical-align: middle; overflow-wrap: anywhere; }
  .pti-master-description { white-space: pre-wrap; min-width: 200px; max-width: 560px; }
  .pti-master-actions { display: flex; flex-wrap: wrap; gap: .4rem; }
  .pti-master-actions form { margin: 0; }
</style>
<section class="pti-master" aria-labelledby="pti-master-heading">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div><h1 id="pti-master-heading" class="pti-heading"><i class="fas fa-cog mr-2" aria-hidden="true"></i>Master Sistem</h1>
      <p class="text-muted mb-0">Kelola sistem yang tersedia pada formulir pengajuan tiket publik.</p></div>
    <a class="btn btn-outline-secondary mt-2" href="<?= pti_e($ptiDashboard . '?unit=portal_tiket') ?>"><i class="fas fa-arrow-left mr-1" aria-hidden="true"></i>Kembali ke Antrean</a>
  </div>
  <?php if ($masterFlash): ?><div class="alert alert-success" role="status"><?= pti_e($masterFlash) ?></div><?php endif; ?>
  <?php if ($masterError): ?><div class="alert alert-danger" role="alert"><?= pti_e($masterError) ?></div><?php endif; ?>
  <?php if ($masterAvailable): ?>
  <?php if ($masterForm !== null): ?>
  <div class="card card-outline card-primary" id="form-sistem">
    <div class="card-header"><h2 class="card-title"><?= $masterForm['editing'] ? 'Edit Sistem' : 'Tambah Sistem' ?></h2></div>
    <form method="post" action="<?= pti_e($ptiMasterUrl) ?>">
      <div class="card-body">
        <input type="hidden" name="csrf" value="<?= pti_e($_SESSION['pti_master_csrf']) ?>">
        <input type="hidden" name="action" value="<?= $masterForm['editing'] ? 'update' : 'create' ?>">
        <?php if ($masterForm['editing']): ?><input type="hidden" name="id_sistem" value="<?= pti_e($masterForm['id_sistem']) ?>"><?php endif; ?>
        <div class="form-group"><label for="master-nama">Nama Sistem <span class="text-danger">*</span></label>
          <input class="form-control" id="master-nama" name="nama_sistem" maxlength="100" required value="<?= pti_e($masterForm['nama_sistem']) ?>" placeholder="Contoh: SIMRS"></div>
        <div class="form-group"><label for="master-deskripsi">Deskripsi <span class="text-danger">*</span></label>
          <textarea class="form-control" id="master-deskripsi" name="deskripsi" rows="4" maxlength="10000" required><?= pti_e($masterForm['deskripsi']) ?></textarea></div>
        <input type="hidden" name="is_active" value="0">
        <div class="custom-control custom-switch"><input class="custom-control-input" type="checkbox" id="master-aktif" name="is_active" value="1" <?= (string) $masterForm['is_active'] === '1' ? 'checked' : '' ?>>
          <label class="custom-control-label" for="master-aktif">Aktif (tampilkan pada form publik)</label></div>
      </div>
      <div class="card-footer"><button class="btn btn-primary" type="submit"><i class="fas fa-save mr-1" aria-hidden="true"></i>Simpan</button>
        <a class="btn btn-outline-secondary ml-1" href="<?= pti_e($ptiMasterUrl) ?>">Batal</a></div>
    </form>
  </div>
  <?php endif; ?>
  <div class="card card-outline card-primary">
    <div class="card-header d-flex flex-wrap align-items-center"><h2 class="card-title mr-auto">Data Master Sistem</h2>
      <a class="btn btn-primary btn-sm" href="<?= pti_e($ptiMasterUrl . '&form=create#form-sistem') ?>"><i class="fas fa-plus mr-1" aria-hidden="true"></i>Tambah Sistem</a></div>
    <div class="card-body">
      <p class="text-muted small">Sistem nonaktif tidak muncul di form publik. Riwayat tiket yang menggunakannya tetap tersimpan.</p>
      <div class="table-responsive">
        <table class="table table-bordered table-hover pti-master-table" aria-label="Data master sistem">
          <thead><tr><th scope="col">Nama Sistem</th><th scope="col">Deskripsi</th><th scope="col">Status Aktif</th><th scope="col">Aksi</th></tr></thead>
          <tbody>
          <?php foreach ($masterRows as $system): ?>
          <tr>
            <td><strong><?= pti_e($system['nama_sistem']) ?></strong></td>
            <td class="pti-master-description"><?= pti_e($system['deskripsi']) ?></td>
            <td><span class="badge badge-<?= $system['is_active'] ? 'success' : 'secondary' ?>"><?= $system['is_active'] ? 'Ya' : 'Tidak' ?></span></td>
            <td><div class="pti-master-actions">
              <a class="btn btn-sm btn-outline-primary" aria-label="<?= pti_e('Edit ' . $system['nama_sistem']) ?>" href="<?= pti_e($ptiMasterUrl . '&edit=' . (int) $system['id_sistem'] . '#form-sistem') ?>"><i class="fas fa-edit mr-1" aria-hidden="true"></i>Edit</a>
              <form method="post" action="<?= pti_e($ptiMasterUrl) ?>">
                <input type="hidden" name="csrf" value="<?= pti_e($_SESSION['pti_master_csrf']) ?>">
                <input type="hidden" name="action" value="set_active">
                <input type="hidden" name="id_sistem" value="<?= (int) $system['id_sistem'] ?>">
                <input type="hidden" name="is_active" value="<?= $system['is_active'] ? '0' : '1' ?>">
                <button class="btn btn-sm btn-outline-<?= $system['is_active'] ? 'warning' : 'success' ?>" type="submit" aria-label="<?= pti_e(($system['is_active'] ? 'Nonaktifkan ' : 'Aktifkan ') . $system['nama_sistem']) ?>"><?= $system['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?></button>
              </form>
            </div></td>
          </tr>
          <?php endforeach; ?>
          <?php if (!$masterRows): ?><tr><td colspan="4" class="text-center text-muted py-4">Belum ada sistem. Klik Tambah Sistem untuk menambahkan data.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <?php endif; ?>
</section>
