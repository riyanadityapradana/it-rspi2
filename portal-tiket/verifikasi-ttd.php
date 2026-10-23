<?php
require_once __DIR__ . '/access_helpers.php';
pt_public_require();
require_once __DIR__.'/layout.php';
require_once __DIR__.'/uat_helpers.php';
require_once __DIR__.'/signature_helpers.php';
header('Cache-Control: no-store');header('Referrer-Policy: no-referrer');header('X-Content-Type-Options: nosniff');header('X-Robots-Tag: noindex, nofollow');
if(!in_array($_SERVER['REQUEST_METHOD'],['GET','HEAD'],true)){header('Allow: GET, HEAD');http_response_code(405);exit('Metode permintaan tidak didukung.');}
$signature=null;$error=null;$review=null;
try{
    $token=$_GET['token'] ?? null;
    if(!pt_uat_token_valid($token))throw new DomainException('Tautan UAT tidak valid atau tidak tersedia.',404);
    $review=pt_uat_review(pt_connection(),$token);
    $id=filter_var($_GET['id']??null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1,'max_range'=>2147483647]]);
    $signature=pt_verify_signature($review,$_GET['jenis']??null,$id?:0,$_GET['hash']??null);
}catch(DomainException $e){http_response_code($e->getCode());$error=$e->getMessage();}
catch(Throwable $e){http_response_code(503);$error='Verifikasi belum dapat diproses. Silakan coba lagi beberapa saat.';error_log('Portal IT signature verification unavailable.');}
portal_header('Verifikasi QR Tim IT','Pencocokan identitas dan isi catatan dengan data serah terima layanan IT.');
?>
<?php if($error): ?><div class="portal-notice portal-error" role="alert"><?= portal_escape($error) ?></div><?php endif; ?>
<?php if($signature): ?>
<section class="portal-panel portal-ticket-section"><h2>Catatan IT terverifikasi</h2>
  <dl class="portal-ticket-meta"><div><dt>No Tiket</dt><dd><?= portal_escape($review['ticket']['no_tiket']) ?></dd></div><div><dt>Jenis Catatan</dt><dd><?= $signature['kind']==='analisis'?'Analisis IT':'Serah Terima Penyelesaian' ?></dd></div><div><dt>Nama Staf IT</dt><dd><?= portal_escape($signature['nama']) ?></dd></div><div><dt>Tanggal</dt><dd><?= $signature['tanggal']?portal_escape(date('d-m-Y H:i:s T',strtotime($signature['tanggal']))):'Belum tercatat (data lama)' ?></dd></div></dl>
  <p class="portal-signature-hash">SHA-256: <?= portal_escape($signature['hash']) ?></p>
</section>
<nav class="portal-public-nav"><a class="login-link" href="<?= portal_escape('uat.php?token='.rawurlencode($token)) ?>">Kembali ke Serah Terima &amp; UAT</a></nav>
<?php endif; ?>
<?php portal_footer(); ?>
