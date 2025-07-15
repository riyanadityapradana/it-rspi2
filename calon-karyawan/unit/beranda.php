<?php
require_once '../config/koneksi.php';
if (!isset($_SESSION['id_calon'])) {
    header('Location: ../../main_login/form_login.php');
    exit;
}
$id_calon = $_SESSION['id_calon'];
// Data utama
$q = $config->query("SELECT * FROM tb_calon WHERE id_calon='$id_calon'");
$calon = $q->fetch_assoc();
// Pendidikan
$pendidikan = $config->query("SELECT * FROM tb_pendidikan WHERE id_calon='$id_calon' ORDER BY tahun_lulus DESC");
// Pengalaman
$pengalaman = $config->query("SELECT * FROM tb_pengalaman WHERE id_calon='$id_calon' ORDER BY tahun_keluar DESC");
// Sertifikasi
$sertifikasi = $config->query("SELECT * FROM tb_sertifikasi WHERE id_calon='$id_calon' ORDER BY tahun DESC");
// Keahlian
$keahlian = $config->query("SELECT * FROM tb_keahlian WHERE id_calon='$id_calon'");
// Bahasa
$bahasa = $config->query("SELECT * FROM tb_bahasa WHERE id_calon='$id_calon'");
// Organisasi
$organisasi = $config->query("SELECT * FROM tb_organisasi WHERE id_calon='$id_calon' ORDER BY tahun_selesai DESC");
// Foto
$foto = $calon['foto'] ? '../../assets/img/' . $calon['foto'] : '../../assets/img/default.png';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV Digital | <?= htmlspecialchars($calon['nama_lengkap']) ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:700,400&display=swap">
    <style>
        body { font-family: 'Montserrat', Arial, sans-serif; background: #f6f8fa; margin:0; }
        .cv-container { max-width: 900px; margin: 32px auto; background: #fff; box-shadow:0 4px 24px #0001; border-radius:18px; display:flex; overflow:hidden; }
        .cv-left { background: #eaf6fb; width: 320px; padding: 32px 24px; display:flex; flex-direction:column; align-items:center; }
        .cv-photo { width: 120px; height: 120px; border-radius: 50%; object-fit:cover; border:4px solid #fff; box-shadow:0 2px 8px #0002; margin-bottom:18px; }
        .cv-name { font-size: 2.1rem; font-weight:700; color:#222; margin-bottom:2px; text-align:center; }
        .cv-title { font-size:1.1rem; color:#0077b6; font-weight:600; margin-bottom:18px; text-align:center; letter-spacing:1px; }
        .cv-section-title { color:#0077b6; font-size:1.1rem; font-weight:700; margin:24px 0 8px 0; letter-spacing:1px; }
        .cv-contact, .cv-list { font-size:0.98rem; color:#222; margin-bottom:8px; }
        .cv-list { list-style:none; padding:0; margin:0; }
        .cv-list li { margin-bottom:6px; }
        .cv-main { flex:1; padding:32px 36px 32px 36px; }
        .cv-summary { font-size:1.05rem; color:#444; margin-bottom:18px; }
        .cv-block { margin-bottom:28px; }
        .cv-block-title { color:#0077b6; font-size:1.15rem; font-weight:700; margin-bottom:8px; letter-spacing:1px; border-bottom:2px solid #eaf6fb; padding-bottom:2px; }
        .cv-table { width:100%; border-collapse:collapse; }
        .cv-table td { padding:2px 0; vertical-align:top; }
        .cv-pendidikan, .cv-pengalaman, .cv-sertifikasi, .cv-organisasi { margin-bottom:12px; }
        .cv-pendidikan-title, .cv-pengalaman-title, .cv-sertifikasi-title, .cv-organisasi-title { font-weight:600; color:#222; }
        .cv-pendidikan-meta, .cv-pengalaman-meta, .cv-sertifikasi-meta, .cv-organisasi-meta { color:#0077b6; font-size:0.97em; }
        .cv-pengalaman-desc, .cv-organisasi-desc { color:#444; font-size:0.97em; margin-bottom:4px; }
        .cv-btn-pdf { position:absolute; top:32px; right:36px; background:#0077b6; color:#fff; border:none; border-radius:6px; padding:8px 18px; font-size:1em; font-weight:600; cursor:pointer; box-shadow:0 2px 8px #0077b633; transition:background 0.2s; }
        .cv-btn-pdf:hover { background:#023e8a; }
        @media (max-width: 900px) {
            .cv-container { flex-direction:column; max-width:98vw; }
            .cv-left, .cv-main { width:100%; border-radius:0; }
            .cv-left { flex-direction:row; justify-content:center; align-items:flex-start; padding:24px 8px; }
            .cv-photo { margin-bottom:0; margin-right:18px; }
        }
        @media (max-width: 600px) {
            .cv-main { padding:18px 6vw; }
            .cv-left { padding:18px 6vw; }
            .cv-btn-pdf { right:12px; top:12px; }
        }
    </style>
</head>
<body>
<div class="cv-container">
    <div class="cv-left">
        <img src="<?= htmlspecialchars($foto) ?>" class="cv-photo" alt="Foto Profil">
        <div class="cv-name"><?= htmlspecialchars($calon['nama_lengkap']) ?></div>
        <div class="cv-title">INDUSTRIAL ENGINEERING</div>
        <div class="cv-section-title">INFORMASI KONTAK</div>
        <div class="cv-contact"><b>📞</b> <?= htmlspecialchars($calon['no_hp']) ?></div>
        <div class="cv-contact"><b>✉️</b> <?= htmlspecialchars($calon['email']) ?></div>
        <div class="cv-contact"><b>🏠</b> <?= htmlspecialchars($calon['alamat']) ?></div>
        <div class="cv-contact"><b>LinkedIn:</b> <?= htmlspecialchars($calon['linkedin'] ?? '-') ?></div>
        <div class="cv-section-title">LISENSI DAN SERTIFIKASI</div>
        <ul class="cv-list">
        <?php while($row = $sertifikasi->fetch_assoc()): ?>
            <li><b><?= htmlspecialchars($row['nama_sertifikasi']) ?></b> (<?= htmlspecialchars($row['tahun']) ?>)<br><span style="font-size:0.95em; color:#555;"><?= htmlspecialchars($row['penyelenggara']) ?></span></li>
        <?php endwhile; if($sertifikasi->num_rows==0) echo '<li>-</li>'; ?>
        </ul>
        <div class="cv-section-title">KEAHLIAN</div>
        <ul class="cv-list">
        <?php while($row = $keahlian->fetch_assoc()): ?>
            <li><?= htmlspecialchars($row['nama_keahlian']) ?></li>
        <?php endwhile; if($keahlian->num_rows==0) echo '<li>-</li>'; ?>
        </ul>
        <div class="cv-section-title">KEMAMPUAN BAHASA</div>
        <ul class="cv-list">
        <?php while($row = $bahasa->fetch_assoc()): ?>
            <li><?= htmlspecialchars($row['nama_bahasa']) ?> (<?= htmlspecialchars($row['tingkat']) ?>)</li>
        <?php endwhile; if($bahasa->num_rows==0) echo '<li>-</li>'; ?>
        </ul>
    </div>
    <div class="cv-main">
        <button class="cv-btn-pdf" onclick="window.print()">Cetak PDF</button>
        <div class="cv-name" style="font-size:2.2rem; margin-bottom:0;"><?= htmlspecialchars($calon['nama_lengkap']) ?></div>
        <div class="cv-title" style="margin-bottom:18px;">INDUSTRIAL ENGINEERING</div>
        <div class="cv-summary"> <?= nl2br(htmlspecialchars($calon['deskripsi'] ?? '')) ?> </div>
        <div class="cv-block">
            <div class="cv-block-title">RIWAYAT PENDIDIKAN</div>
            <?php while($row = $pendidikan->fetch_assoc()): ?>
                <div class="cv-pendidikan">
                    <div class="cv-pendidikan-title"><?= htmlspecialchars($row['jenjang']) ?> - <?= htmlspecialchars($row['nama_sekolah']) ?></div>
                    <div class="cv-pendidikan-meta"><?= htmlspecialchars($row['tahun_masuk']) ?> - <?= htmlspecialchars($row['tahun_lulus']) ?><?= $row['jurusan'] ? ' | ' . htmlspecialchars($row['jurusan']) : '' ?></div>
                </div>
            <?php endwhile; if($pendidikan->num_rows==0) echo '<div class="cv-pendidikan">-</div>'; ?>
        </div>
        <div class="cv-block">
            <div class="cv-block-title">PENGALAMAN</div>
            <?php while($row = $pengalaman->fetch_assoc()): ?>
                <div class="cv-pengalaman">
                    <div class="cv-pengalaman-title"><?= htmlspecialchars($row['posisi']) ?> - <?= htmlspecialchars($row['nama_perusahaan']) ?></div>
                    <div class="cv-pengalaman-meta"><?= htmlspecialchars($row['tahun_masuk']) ?> - <?= htmlspecialchars($row['tahun_keluar']) ?></div>
                    <div class="cv-pengalaman-desc"><?= nl2br(htmlspecialchars($row['deskripsi'])) ?></div>
                </div>
            <?php endwhile; if($pengalaman->num_rows==0) echo '<div class="cv-pengalaman">-</div>'; ?>
        </div>
        <div class="cv-block">
            <div class="cv-block-title">ORGANISASI</div>
            <?php while($row = $organisasi->fetch_assoc()): ?>
                <div class="cv-organisasi">
                    <div class="cv-organisasi-title"><?= htmlspecialchars($row['nama_organisasi']) ?></div>
                    <div class="cv-organisasi-meta"><?= htmlspecialchars($row['tahun_mulai']) ?> - <?= htmlspecialchars($row['tahun_selesai']) ?></div>
                    <div class="cv-organisasi-desc"><?= nl2br(htmlspecialchars($row['deskripsi'])) ?></div>
                </div>
            <?php endwhile; if($organisasi->num_rows==0) echo '<div class="cv-organisasi">-</div>'; ?>
        </div>
    </div>
</div>
</body>
</html> 