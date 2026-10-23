<?php
require_once __DIR__ . '/access_helpers.php';
pt_public_require();
require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/public_helpers.php';
ptp_readonly();
$filters = ptp_filters($_GET); $listing = null; $error = null;
try {
    $listing = ptp_list(pt_connection(), $filters);
    $filters['page'] = $listing['page'];
} catch (Throwable $e) {
    error_log('Portal public list: ' . $e->getMessage());
    http_response_code(503); $error = 'Daftar tiket belum dapat dimuat. Silakan coba lagi beberapa saat.';
}
portal_header('Daftar Tiket', 'Cari pengajuan Change Request dan Fitur Baru, pantau status, serta lihat hasil penanganan dari tim IT.');
?>
      <nav class="portal-public-nav" aria-label="Navigasi daftar tiket"><a class="login-link" href="./"><i class="fas fa-arrow-left" aria-hidden="true"></i>Kembali ke Portal Tiket</a></nav>
      <?php if ($error): ?><div class="portal-notice portal-error" role="alert"><?= portal_escape($error) ?></div><?php endif; ?>
      <section class="portal-panel portal-ticket-list" aria-labelledby="daftar-tiket-title">
        <h2 id="daftar-tiket-title">Daftar Pengajuan Tiket IT</h2>
        <form class="portal-ticket-search" method="get" action="daftar-tiket.php" role="search">
          <label class="portal-field" for="ticket-search">Cari tiket
            <input id="ticket-search" type="search" name="q" maxlength="100" value="<?= portal_escape($filters['q']) ?>" placeholder="No tiket, sistem, atau nama perubahan/fitur"></label>
          <label class="portal-field" for="ticket-status">Status
            <select id="ticket-status" name="status"><option value="">Semua status</option>
              <?php foreach (['Menunggu Analisis','Dikerjakan','Menunggu UAT','Selesai','Ditolak'] as $status): ?><option value="<?= portal_escape($status) ?>" <?= $filters['status'] === $status ? 'selected' : '' ?>><?= portal_escape($status) ?></option><?php endforeach; ?>
            </select></label>
          <label class="portal-field" for="ticket-type">Jenis Pengajuan
            <select id="ticket-type" name="jenis"><option value="">Semua jenis</option>
              <?php foreach (['Change Request','Fitur Baru'] as $type): ?><option value="<?= portal_escape($type) ?>" <?= $filters['jenis'] === $type ? 'selected' : '' ?>><?= portal_escape($type) ?></option><?php endforeach; ?>
            </select></label>
          <div class="portal-ticket-search-actions"><button class="portal-button" type="submit"><i class="fas fa-search" aria-hidden="true"></i>Cari</button><a class="login-link" href="daftar-tiket.php">Reset</a></div>
        </form>
        <?php if ($listing !== null): ?>
        <p class="portal-ticket-summary" role="status"><?= $listing['total'] ? 'Menampilkan ' . ($listing['offset'] + 1) . '–' . ($listing['offset'] + count($listing['rows'])) . ' dari ' . $listing['total'] . ' tiket. Pengajuan terbaru ditampilkan lebih dahulu.' : 'Tidak ada tiket yang sesuai dengan pencarian atau filter.' ?></p>
        <p class="portal-table-hint">Geser tabel ke samping untuk melihat seluruh kolom.</p>
        <div class="portal-table-scroll" role="region" aria-label="Tabel daftar tiket, dapat digeser ke samping" tabindex="0">
          <table class="portal-ticket-table">
            <caption class="portal-visually-hidden">Daftar seluruh tiket layanan IT</caption>
            <thead><tr><th scope="col">No Tiket</th><th scope="col">Tanggal Pengajuan</th><th scope="col">Jenis Pengajuan</th><th scope="col">Sistem Terkait</th><th scope="col">Nama Perubahan/Fitur</th><th scope="col">Status</th><th scope="col">Aksi</th></tr></thead>
            <tbody>
              <?php foreach ($listing['rows'] as $row): ?>
              <tr>
                <th scope="row"><?= portal_escape($row['no_tiket']) ?></th>
                <td><?= portal_escape(ptp_date($row['tgl_pengajuan'])) ?></td>
                <td><?= portal_escape($row['jenis_tiket']) ?></td>
                <td><?= portal_escape($row['nama_sistem'] ?? '-') ?></td>
                <td><?= portal_escape($row['nama_perubahan_fitur'] ?? '-') ?></td>
                <td><span class="portal-status portal-status-<?= ptp_status_class($row['status_tiket']) ?>"><?= portal_escape($row['status_tiket']) ?></span></td>
                <td><a class="portal-ticket-detail-link" href="<?= portal_escape('detail-tiket.php?' . http_build_query(array_merge($filters, ['id' => (int) $row['id_tiket']]))) ?>" aria-label="<?= portal_escape('Detail tiket ' . $row['no_tiket']) ?>">Detail<i class="fas fa-arrow-right" aria-hidden="true"></i></a></td>
              </tr>
              <?php endforeach; ?>
              <?php if (!$listing['rows']): ?><tr><td colspan="7" class="portal-ticket-empty">Belum ada tiket yang sesuai. Coba kata kunci atau filter lain.</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
        <div class="portal-pagination-row">
          <p>Halaman <?= $listing['page'] ?> dari <?= $listing['pages'] ?></p>
          <nav class="portal-pagination" aria-label="Navigasi halaman daftar tiket">
            <?php if ($listing['page'] > 1): ?><a href="<?= portal_escape(ptp_list_url($filters, ['page' => $listing['page'] - 1])) ?>" rel="prev">Sebelumnya</a><?php else: ?><span aria-disabled="true">Sebelumnya</span><?php endif; ?>
            <?php
            $numbers = array_unique(array_merge([1], range(max(1, $listing['page'] - 2), min($listing['pages'], $listing['page'] + 2)), [$listing['pages']]));
            sort($numbers); $previous = 0;
            foreach ($numbers as $number):
              if ($previous && $number > $previous + 1): ?><span class="portal-pagination-gap" aria-hidden="true">…</span><?php endif;
              $previous = $number;
              if ($number === $listing['page']): ?><span aria-current="page" aria-label="<?= 'Halaman ' . $number ?>"><?= $number ?></span><?php else: ?><a href="<?= portal_escape(ptp_list_url($filters, ['page' => $number])) ?>" aria-label="<?= 'Halaman ' . $number ?>"><?= $number ?></a><?php endif; ?>
            <?php endforeach; ?>
            <?php if ($listing['page'] < $listing['pages']): ?><a href="<?= portal_escape(ptp_list_url($filters, ['page' => $listing['page'] + 1])) ?>" rel="next">Berikutnya</a><?php else: ?><span aria-disabled="true">Berikutnya</span><?php endif; ?>
          </nav>
        </div>
        <?php endif; ?>
      </section>
<?php portal_footer(); ?>
