<?php
function portal_escape($text)
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

function portal_header($title, $intro, $landing = false)
{
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= portal_escape($title) ?> | IT-RSPI</title>
  <link rel="icon" href="../assets/img/icon.png">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&amp;display=fallback">
  <link rel="stylesheet" href="../assets/dist/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../assets/dist/css/portal-tiket.css">
</head>
<body>
  <div class="portal-container">
    <header class="portal-header">
      <a class="portal-brand" href="./" aria-label="Beranda Portal Tiket IT">
        <img src="../assets/img/icon.png" alt="" width="40" height="40">
        <span>IT &middot; RSPI</span>
      </a>
      <!-- <a class="login-link" href="<?= $landing ? '../main_login/form_login.php' : './' ?>"> 
        <i class="fas fa-arrow-left" aria-hidden="true"></i>
        <?= $landing ? 'Kembali ke Login' : 'Kembali ke Portal' ?>
      </a>-->
    </header>
    <main>
      <p class="portal-eyebrow">LAYANAN IT RSPI</p>
      <h1><?= portal_escape($title) ?></h1>
      <p class="portal-intro"><?= portal_escape($intro) ?></p>
<?php
}

function portal_footer()
{
    ?>
    </main>
    <footer class="portal-footer">Portal publik IT RSPI &middot; Dapat diakses tanpa login.</footer>
  </div>
  <script src="../assets/dist/js/portal-session.js" defer></script>
</body>
</html>
<?php
}

function portal_initial_page($title, $intro, $items)
{
    portal_header($title, $intro);
    ?>
      <div class="portal-notice" role="note">
        <strong>Layanan sedang disiapkan</strong>
        <p>Halaman ini merupakan halaman awal. Pengiriman pengajuan dan pencarian tiket belum diaktifkan.</p>
      </div>
      <section class="portal-panel" aria-labelledby="informasi-layanan">
        <h2 id="informasi-layanan">Informasi layanan</h2>
        <ul>
          <?php foreach ($items as $item): ?>
          <li><?= portal_escape($item) ?></li>
          <?php endforeach; ?>
        </ul>
        <p>Silakan kembali ke portal untuk melihat menu layanan lainnya.</p>
        <a class="login-link" href="./"><i class="fas fa-arrow-left" aria-hidden="true"></i> Kembali ke Portal</a>
      </section>
<?php
    portal_footer();
}
