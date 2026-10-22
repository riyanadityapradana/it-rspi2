<?php
if (!isset($ptSignatureRecord,$ptSignatureKind)) { http_response_code(404); exit; }
$ptSignature=$ptSignatureRecord['signature'];
$ptSignerName=$ptSignatureRecord[$ptSignatureKind==='analisis' ? 'nama_staf_it' : 'nama_staf_penyerah'];
$ptSignerDate=$ptSignatureRecord[$ptSignatureKind==='analisis' ? 'tgl_analisis' : 'tgl_serah_terima'];
?>
        <div class="portal-it-signature">
          <div><p class="portal-signature-role"><?= $ptSignatureKind==='analisis' ? 'Dianalisis oleh (Tim IT)' : 'Diserahkan oleh (Tim IT)' ?></p>
            <p class="portal-signer-name"><?= portal_escape($ptSignerName ?? 'Nama pegawai belum tersedia') ?></p>
            <p><?= $ptSignatureKind==='analisis' ? 'Tanggal Analisis' : 'Tanggal Serah Terima' ?>: <strong><?= $ptSignerDate ? portal_escape(date('d-m-Y H:i:s T',strtotime($ptSignerDate))) : 'Belum tercatat (data lama)' ?></strong></p>
            <?php if (!$ptSignature): ?><p>Verifikasi identitas belum tersedia.</p><?php endif; ?>
          </div>
          <?php if ($ptSignature): ?><img class="portal-it-qr" src="<?= portal_escape($ptSignature['qr']) ?>" width="176" height="176" alt="<?= portal_escape('QR verifikasi '.$ptSignerName) ?>"><?php endif; ?>
        </div>
