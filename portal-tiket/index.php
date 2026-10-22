<?php
require_once __DIR__ . '/layout.php';
portal_header('Portal Tiket IT', 'Pilih layanan untuk kebutuhan perubahan sistem, fitur baru, atau pelacakan tiket IT.', true);
?>
      <p class="portal-stage" role="note">Ajukan kebutuhan layanan IT dan pantau perkembangan pengajuan melalui Daftar Tiket.</p>
      <nav class="portal-menus" aria-label="Menu utama Portal Tiket IT">
        <a class="portal-menu" href="change-request.php" aria-labelledby="menu-change-request">
          <span class="menu-icon"><i class="fas fa-exchange-alt" aria-hidden="true"></i></span>
          <h2 id="menu-change-request" data-portal-menu>Form Change Request</h2>
          <p>Ajukan modifikasi atau perubahan pada sistem yang sudah digunakan.</p>
          <span class="menu-action">Buka Halaman <i class="fas fa-arrow-right" aria-hidden="true"></i></span>
        </a>
        <a class="portal-menu" href="fitur-baru.php" aria-labelledby="menu-fitur-baru">
          <span class="menu-icon"><i class="fas fa-plus" aria-hidden="true"></i></span>
          <h2 id="menu-fitur-baru" data-portal-menu>Form Fitur Baru</h2>
          <p>Ajukan kebutuhan modul, fitur, atau aplikasi baru untuk mendukung pekerjaan unit Anda.</p>
          <span class="menu-action">Buka Halaman <i class="fas fa-arrow-right" aria-hidden="true"></i></span>
        </a>
        <a class="portal-menu" href="daftar-tiket.php" aria-labelledby="menu-daftar-tiket">
          <span class="menu-icon"><i class="fas fa-ticket-alt" aria-hidden="true"></i></span>
          <h2 id="menu-daftar-tiket" data-portal-menu>Daftar Tiket</h2>
          <p>Cari tiket, lacak status pengajuan, dan lihat hasil penyelesaian dari tim IT.</p>
          <span class="menu-action">Buka Halaman <i class="fas fa-arrow-right" aria-hidden="true"></i></span>
        </a>
      </nav>
<?php portal_footer(); ?>
