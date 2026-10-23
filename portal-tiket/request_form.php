<?php
if (!isset($ptRequestType) || !in_array($ptRequestType, ['Change Request', 'Fitur Baru'], true)) { http_response_code(404); exit; }
require_once __DIR__ . '/access_helpers.php';
pt_public_require();
require_once __DIR__ . '/request_helpers.php';
require_once __DIR__ . '/layout.php';
$definition = pt_request_definition($ptRequestType);
$requestFile = $definition['file'];
pt_session();
$error = ''; $draft = null; $systems = []; $units = []; $employee = null; $values = [];
$formId = is_string($_GET['form'] ?? null) ? $_GET['form'] : '';
try {
    $db = pt_connection();
    $systems = pt_rows($db, 'SELECT id_sistem,nama_sistem FROM master_sistem WHERE is_active=1 ORDER BY nama_sistem');
    $units = pt_rows($db, 'SELECT lokasi_id,nama_lokasi FROM tb_lokasi ORDER BY nama_lokasi');
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $formId === '') {
        // Mengulang GET pada URL hasil redirect menggunakan reservasi yang sama.
        foreach ($_SESSION['pt_forms'] ?? [] as $key => $old) {
            if (time() - $old['created_at'] > 86400) unset($_SESSION['pt_forms'][$key]);
        }
        if (count($_SESSION['pt_forms'] ?? []) >= 20) array_shift($_SESSION['pt_forms']);
        $formId = bin2hex(random_bytes(24));
        $_SESSION['pt_forms'][$formId] = pt_reserve($db, $ptRequestType);
        header('Location: ' . $requestFile . '?form=' . $formId, true, 303); exit;
    }
    if (!preg_match('/^[a-f0-9]{48}$/D', $formId) || !isset($_SESSION['pt_forms'][$formId])) {
        http_response_code(410);
        throw new DomainException('Sesi formulir telah berakhir atau tautan formulir tidak valid. Buka Form Baru untuk melanjutkan.');
    }
    pt_assert_draft_type($_SESSION['pt_forms'][$formId], $ptRequestType);
    $draft = $_SESSION['pt_forms'][$formId];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $values = $_POST;
        $id = pt_save_request($db, $draft, $values, $_POST['csrf'] ?? null, $ptRequestType);
        $_SESSION['pt_forms'][$formId]['saved_id'] = $id;
        header('Location: ' . $requestFile . '?form=' . $formId, true, 303); exit;
    }
} catch (DomainException $e) {
    if (http_response_code() !== 410) http_response_code(422);
    $error = $e->getMessage();
} catch (Throwable $e) {
    error_log('Portal ' . $ptRequestType . ': ' . $e->getMessage());
    http_response_code(503);
    $error = 'Form belum dapat diproses. Silakan coba lagi. Jika berulang, hubungi Unit IT.';
}
// Hanya nama terverifikasi yang ditampilkan kembali setelah kegagalan validasi.
if (!empty($values['pegawai_id']) && is_string($values['pegawai_id']) && ctype_digit($values['pegawai_id'])) {
    try { $employee = pt_employee((int) $values['pegawai_id']); } catch (Throwable $e) { $employee = null; }
}
$value = function ($name) use ($values) { return portal_escape(is_string($values[$name] ?? null) ? $values[$name] : ''); };
portal_header($definition['title'], $definition['intro'] . ' Kolom bertanda * wajib diisi.');
?>
<?php if ($error): ?>
      <div class="portal-notice portal-error" role="alert" tabindex="-1"><?= portal_escape($error) ?></div>
<?php endif; ?>
<?php if ($draft && $draft['saved_id']): ?>
      <section class="portal-panel" aria-labelledby="hasil-pengajuan">
        <h2 id="hasil-pengajuan">Pengajuan berhasil disimpan</h2>
        <p>Simpan nomor tiket berikut untuk pelacakan dan komunikasi dengan tim IT.</p>
        <p class="ticket-number"><?= portal_escape($draft['no_tiket']) ?></p>
        <p>Status: <strong>Menunggu Analisis</strong></p>
        <div class="portal-actions portal-success-actions">
          <a class="portal-button" href="<?= portal_escape($requestFile) ?>">Buat Pengajuan Baru</a>
          <a class="portal-button portal-button-secondary" href="./"><i class="fas fa-arrow-left" aria-hidden="true"></i>Kembali ke Portal Tiket</a>
        </div>
      </section>
<?php elseif ($draft): ?>
<?php if (!$systems || !$units): ?>
      <div class="portal-notice" role="alert">Pilihan <?= !$systems ? 'sistem aktif' : 'unit pemohon' ?> belum tersedia. Hubungi Unit IT sebelum mengisi formulir.</div>
<?php endif; ?>
      <form class="portal-panel request-form" method="post" action="<?= portal_escape($requestFile) ?>?form=<?= portal_escape($formId) ?>" id="<?= portal_escape($definition['form_id']) ?>" data-ticket-form>
        <input type="hidden" name="csrf" value="<?= portal_escape($_SESSION['pt_csrf']) ?>">
        <input type="hidden" name="ttd_pengaju" id="signature-data" value="">
        <h2>Data Pengajuan</h2>
        <label class="portal-field" for="no-tiket">No Tiket
          <input id="no-tiket" value="<?= portal_escape($draft['no_tiket']) ?>" readonly aria-readonly="true">
        </label>
        <label class="portal-field" for="jenis-tiket">Jenis Tiket
          <input id="jenis-tiket" value="<?= portal_escape($ptRequestType) ?>" readonly aria-readonly="true">
        </label>
        <label class="portal-field" for="tgl-pengajuan">Tanggal Pengajuan
          <input id="tgl-pengajuan" value="<?= portal_escape(date('d-m-Y H:i T', strtotime($draft['tgl_pengajuan']))) ?>" readonly aria-readonly="true">
        </label>
        <label class="portal-field" for="id-sistem">Sistem Terkait *
          <select id="id-sistem" name="id_sistem" required>
            <option value="">Pilih sistem terkait</option>
            <?php foreach ($systems as $system): ?>
            <option value="<?= (int) $system['id_sistem'] ?>" <?= (string) $system['id_sistem'] === ($values['id_sistem'] ?? '') ? 'selected' : '' ?>><?= portal_escape($system['nama_sistem']) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label class="portal-field" for="unit-id">Unit Pemohon *
          <select id="unit-id" name="unit_id" required>
            <option value="">Pilih unit pemohon</option>
            <?php foreach ($units as $unit): ?>
            <option value="<?= (int) $unit['lokasi_id'] ?>" <?= (string) $unit['lokasi_id'] === ($values['unit_id'] ?? '') ? 'selected' : '' ?>><?= portal_escape($unit['nama_lokasi']) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label class="portal-field" for="pegawai-id">Nama Pengaju *
          <select id="pegawai-id" name="pegawai_id" required aria-describedby="pegawai-help pegawai-status">
            <option value="">Cari nama pegawai</option>
            <?php if ($employee): ?><option value="<?= (int) $employee['id'] ?>" selected><?= portal_escape($employee['nama']) ?></option><?php endif; ?>
          </select>
          <small id="pegawai-help">Ketik minimal 3 huruf untuk mencari nama pegawai.</small>
          <small id="pegawai-status" role="status" aria-live="polite"></small>
        </label>
        <label class="portal-field portal-field-full" for="kontak-pengaju"><?= $ptRequestType === 'Fitur Baru' ? 'Kontak / No. WhatsApp' : 'No. WhatsApp yang bisa dihubungi' ?> *
          <input id="kontak-pengaju" name="kontak_pengaju" type="tel" inputmode="tel" autocomplete="tel" placeholder="Contoh: 081234567890" maxlength="25" value="<?= $value('kontak_pengaju') ?>" required>
        </label>
        <h2><?= portal_escape($definition['section']) ?></h2>
        <?php foreach ($definition['fields'] as $fieldName => $field): ?>
        <label class="portal-field<?= count($definition['fields']) % 2 === 1 && $fieldName === array_key_last($definition['fields']) ? ' portal-field-full' : '' ?>" for="<?= portal_escape($field['id']) ?>"><?= portal_escape($field['label']) ?> *
          <?php if (!empty($field['input'])): ?>
          <input id="<?= portal_escape($field['id']) ?>" name="<?= portal_escape($fieldName) ?>" maxlength="<?= $field['max'] ?>" value="<?= $value($fieldName) ?>" required>
          <?php else: ?>
          <textarea id="<?= portal_escape($field['id']) ?>" name="<?= portal_escape($fieldName) ?>" maxlength="<?= $field['max'] ?>" rows="4" required><?= $value($fieldName) ?></textarea>
          <?php endif; ?>
        </label>
        <?php endforeach; ?>
        <h2>Diajukan oleh</h2>
        <div class="portal-field portal-signature">
          <label for="signature-canvas">Tanda Tangan Pemohon *</label>
          <p class="field-help" id="signature-help">Bubuhkan tanda tangan dengan jari, stylus, atau mouse di kotak putih berikut.</p>
          <canvas id="signature-canvas" class="signature-canvas" width="900" height="240" tabindex="0" aria-label="Area tanda tangan pemohon" aria-describedby="signature-help signature-status"></canvas>
          <div class="portal-actions"><button class="login-link clear-signature" type="button" id="clear-signature">Hapus Tanda Tangan</button><span id="signature-status" role="status" aria-live="polite">Tanda tangan belum diisi.</span></div>
        </div>
        <div class="portal-notice portal-error" id="form-client-error" role="alert" tabindex="-1" hidden></div>
        <div class="portal-actions"><button class="portal-button" type="submit" id="save-request" <?= !$systems || !$units ? 'disabled' : '' ?>>SIMPAN</button><a class="login-link" href="./">Kembali ke Portal</a></div>
        <noscript><p>Aktifkan JavaScript agar pencarian pegawai dan tanda tangan dapat digunakan.</p></noscript>
      </form>
      <script src="../assets/dist/plugins/jquery/jquery.min.js"></script>
      <link rel="stylesheet" href="../assets/dist/plugins/select2/css/select2.min.css">
      <script src="../assets/dist/plugins/select2/js/select2.full.min.js"></script>
      <script src="../assets/dist/js/portal-request-form.js"></script>
<?php else: ?>
      <a class="login-link" href="<?= portal_escape($requestFile) ?>">Buka Form Baru</a>
<?php endif; ?>
<?php portal_footer(); ?>
