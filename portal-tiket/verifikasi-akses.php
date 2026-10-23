<?php
require_once __DIR__ . '/access_helpers.php';
require_once __DIR__ . '/layout.php';
try { pt_public_session(); } catch (Throwable $e) { pt_access_unavailable(); }
header('X-Robots-Tag: noindex, nofollow');
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET','HEAD','POST'], true)) { header('Allow: GET, HEAD, POST'); http_response_code(405); exit; }
$next = is_string($_GET['lanjut'] ?? null) ? $_GET['lanjut'] : '';
$location = 'verifikasi-akses.php' . ($next !== '' && preg_match('/^[a-f0-9]{32}$/D', $next) ? '?lanjut=' . $next : '');
if (pt_access_active(null, false)) { header('Location: ' . pt_access_next($next), true, 303); exit; }
if (empty($_SESSION['pt_access_csrf'])) $_SESSION['pt_access_csrf'] = bin2hex(random_bytes(32));
$error = $_SESSION['pt_access_error'] ?? ''; unset($_SESSION['pt_access_error']);
$wait = 0; $available = true; $captchaId = '';
try {
    $storage = pt_access_storage() . '/attempts.sqlite';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrf = itrspi_login_post_string('csrf_token');
        if (!hash_equals($_SESSION['pt_access_csrf'], $csrf)) {
            $_SESSION['pt_access_error'] = 'Sesi formulir tidak valid. Silakan coba lagi.';
        } else {
            $attempt = itrspi_login_reserve_attempt($storage);
            if ($attempt['wait'] > 0) {
                header('Retry-After: ' . $attempt['wait']);
                $_SESSION['pt_access_error'] = 'Percobaan dibatasi. Tunggu hingga hitung mundur selesai.';
            } elseif (!itrspi_login_check_captcha(itrspi_login_post_string('captcha_id'), itrspi_login_post_string('captcha'), 'pt_access_captchas')) {
                $_SESSION['pt_access_error'] = 'Kode CAPTCHA salah atau kedaluwarsa. Silakan tunggu 30 detik sebelum mencoba lagi.';
            } else {
                itrspi_login_clear_attempt($attempt['lease'], $storage);
                if (!session_regenerate_id(true)) throw new RuntimeException('Cannot rotate session');
                $_SESSION['pt_public_access'] = ['last_activity'=>time()];
                unset($_SESSION['pt_access_captchas'], $_SESSION['pt_access_csrf'], $_SESSION['pt_access_error']);
                header('Location: ' . pt_access_next($next), true, 303); exit;
            }
        }
        header('Location: ' . $location, true, 303); exit;
    }
    $wait = itrspi_login_wait($storage);
    $captchaId = itrspi_login_create_captcha('pt_access_captchas');
} catch (Throwable $e) {
    error_log('Portal CAPTCHA unavailable.'); http_response_code(503);
    $available = false; $error = 'Pengamanan portal sedang tidak tersedia. Silakan coba lagi atau hubungi Unit IT.';
}
portal_header('Verifikasi Akses Portal', 'Masukkan kode CAPTCHA berikut untuk melanjutkan ke layanan tiket IT RSPI.');
?>
      <section class="portal-panel portal-access-panel" aria-labelledby="access-title">
        <span class="portal-access-icon" aria-hidden="true"><i class="fas fa-shield-alt"></i></span>
        <h2 id="access-title">Verifikasi sebelum melanjutkan</h2>
        <p>Cukup sekali verifikasi. Anda dapat berpindah menu tanpa mengisi ulang selama sesi belum melewati 2 jam tanpa aktivitas.</p>
        <?php if ($error): ?><div class="portal-notice portal-error" role="alert"><?= portal_escape($error) ?></div><?php endif; ?>
        <form method="post" action="<?= portal_escape($location) ?>" id="portal-access-form" data-wait="<?= (int) $wait ?>" data-available="<?= $available ? '1' : '0' ?>">
          <input type="hidden" name="csrf_token" value="<?= portal_escape($_SESSION['pt_access_csrf']) ?>">
          <input type="hidden" name="captcha_id" value="<?= portal_escape($captchaId) ?>">
          <?php if ($available): ?>
          <div class="portal-access-challenge"><img class="captcha-image" src="captcha.php?id=<?= portal_escape($captchaId) ?>" width="220" height="70" alt="Gambar kode CAPTCHA, lima karakter">
            <button class="portal-button portal-button-secondary" type="button" id="portal-refresh-captcha"><i class="fas fa-sync-alt" aria-hidden="true"></i>Ganti kode</button></div>
          <?php endif; ?>
          <label class="portal-field" for="portal-captcha-answer">Kode CAPTCHA
            <input id="portal-captcha-answer" name="captcha" type="text" required minlength="5" maxlength="5" pattern="[A-Za-z0-9]{5}" autocomplete="off" autocapitalize="characters" spellcheck="false" aria-describedby="portal-captcha-help" <?= !$available ? 'disabled' : '' ?>></label>
          <p id="portal-captcha-help" class="portal-access-help">Ketik 5 karakter pada gambar. Huruf besar atau kecil diterima. Kode berlaku selama 5 menit.</p>
          <p id="portal-access-feedback" role="status" aria-live="polite"><?= $wait > 0 ? 'Coba lagi dalam ' . (int) $wait . ' detik.' : '' ?></p>
          <button class="portal-button portal-access-submit" type="submit" id="portal-access-submit" <?= !$available || $wait > 0 ? 'disabled' : '' ?>><i class="fas fa-unlock-alt" aria-hidden="true"></i>Verifikasi &amp; Masuk Portal</button>
        </form>
        <noscript><p>Setelah menunggu 30 detik, muat ulang halaman jika tombol masih terkunci. <a class="login-link" href="<?= portal_escape($location) ?>">Muat ulang CAPTCHA</a></p></noscript>
      </section>
      <script src="../assets/dist/js/portal-access.js" defer></script>
<?php portal_footer(); ?>
