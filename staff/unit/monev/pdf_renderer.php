<?php
require_once __DIR__.'/criteria_rules.php';
require_once __DIR__.'/../../../library/tcpdf/tcpdf.php';

class MnInspectionPdf extends TCPDF {
    public function Footer() {
        $this->SetY(-12);
        $this->SetFont('dejavusans', '', 7);
        $this->Cell(0, 8, 'Laporan Inspeksi Keamanan Fisik dan Kerahasiaan Data - Halaman '.$this->getAliasNumPage().' dari '.$this->getAliasNbPages(), 0, 0, 'C');
    }
}

function mn_pdf_text($value) {
    return htmlspecialchars(trim((string)$value), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function mn_pdf_date($value, $withTime = false) {
    $value = trim((string)$value);
    if ($value === '' || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') return '-';
    $formats = $withTime ? ['Y-m-d H:i:s', 'Y-m-d H:i'] : ['Y-m-d', 'Y-m-d H:i:s'];
    foreach ($formats as $format) {
        $d = DateTime::createFromFormat($format, str_replace('T', ' ', $value));
        if ($d) return $d->format($withTime ? 'd-m-Y H:i' : 'd-m-Y');
    }
    return $value;
}

function mn_pdf_person($names, $id) {
    return $id && isset($names[$id]) ? $names[$id] : '-';
}

function mn_pdf_field($label, $value) {
    return '<tr><th width="30%">'.mn_pdf_text($label).'</th><td width="70%">'.nl2br(mn_pdf_text($value === '' || $value === null ? '-' : $value)).'</td></tr>';
}

function mn_pdf_photo_path($fileName) {
    $fileName = (string)$fileName;
    if (!preg_match('/^[a-f0-9]{48}\.(jpg|png)$/D', $fileName)) return null;
    $path = __DIR__.'/../../../assets/upload/monev/'.$fileName;
    if (!is_file($path) || !@getimagesize($path)) return null;
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if ($ext === 'png' && function_exists('imagecreatefrompng')) {
        $img = @imagecreatefrompng($path);
        if (!$img) return null;
        imagedestroy($img);
    }
    if ($ext === 'jpg' && function_exists('imagecreatefromjpeg')) {
        $img = @imagecreatefromjpeg($path);
        if (!$img) return null;
        imagedestroy($img);
    }
    return $path;
}

function mn_pdf_render_photo_appendix(TCPDF $pdf, array $photos) {
    if (!$photos) return;
    $margins = $pdf->getMargins();
    $width = $pdf->getPageWidth() - $margins['left'] - $margins['right'];
    $newPage = function () use ($pdf, $width) {
        $pdf->AddPage();
        $pdf->SetFont('dejavusans', 'B', 10);
        $pdf->MultiCell($width, 7, 'Lampiran Foto Bukti', 0, 'L', false, 1);
        $pdf->Ln(3);
        $pdf->SetFont('dejavusans', '', 8);
        return $pdf->GetY();
    };
    $top = $newPage();
    $bottom = $pdf->getPageHeight() - $pdf->getBreakMargin() - 2;
    foreach ($photos as $photo) {
        $size = getimagesize($photo['path']);
        if (!$size || !$size[0] || !$size[1]) continue;
        $captionHeight = max(6, $pdf->getStringHeight($width, $photo['label']));
        // Fit the entire caption/photo block on a fresh page, preserving aspect ratio.
        $scale = min(70 / $size[0], ($bottom - $top - $captionHeight - 7) / $size[1]);
        $imageWidth = $size[0] * $scale;
        $imageHeight = $size[1] * $scale;
        $blockHeight = $captionHeight + 2 + $imageHeight + 5;
        if ($pdf->GetY() + $blockHeight > $bottom) $newPage();
        $y = $pdf->GetY();
        $pdf->MultiCell($width, $captionHeight, $photo['label'], 0, 'L', false, 1, $margins['left'], $y);
        $imageY = $y + $captionHeight + 2;
        $ext = strtoupper(image_type_to_extension($size[2], false));
        $pdf->Image($photo['path'], $margins['left'], $imageY, $imageWidth, $imageHeight, $ext, '', '', false, 150, '', false, false, 1);
        // Image() does not advance the text cursor by the rendered image height.
        $pdf->SetY($imageY + $imageHeight + 5);
    }
}

function mn_pdf_photo_label($fileName) {
    $fileName = (string)$fileName;
    return $fileName !== '' ? basename($fileName) : '-';
}

function mn_pdf_signature_src($fileName) {
    $fileName = (string)$fileName;
    if (!preg_match('/^[a-f0-9]{48}\.png$/D', $fileName)) return '';
    $path = __DIR__.'/../../../assets/upload/monev/'.$fileName;
    if (!is_file($path) || !@getimagesize($path)) return '';
    // Embed trusted local bytes; TCPDF HTML rejects parent-directory paths.
    $binary = file_get_contents($path);
    return $binary === false ? '' : 'data:image/png;base64,'.base64_encode($binary);
}

function mn_pdf_styles() {
    return '<style>
        body { color:#111; font-family:dejavusans; font-size:8.7pt; }
        h1 { font-size:13pt; text-align:center; margin:0 0 6px 0; font-weight:bold; }
        h2 { font-size:10pt; margin:10px 0 5px 0; font-weight:bold; }
        .muted { color:#555; font-size:7.5pt; }
        table { border-collapse:collapse; width:100%; }
        th { font-weight:bold; background-color:#f0f0f0; }
        th, td { border:0.6px solid #333; padding:4px; vertical-align:top; }
        .plain td, .plain th { border:0.6px solid #333; }
        .center { text-align:center; }
        .small { font-size:7.4pt; }
        .signature td { height:78px; text-align:center; vertical-align:bottom; }
        .signature th { text-align:center; }
        .pending-sign { color:#777; font-size:12pt; font-weight:bold; }
    </style>';
}

function mn_build_inspection_pdf(array $report) {
    $h = $report['header'];
    $details = $report['details'];
    $coverage = $report['coverage'] ?: [];
    $findings = $report['findings'];
    $names = $report['names'];
    $unitName = implode(', ',array_unique(array_column($report['bundles'],'nama_lokasi')));
    $activeSigns = $report['active_signatures'];

    $pdf = new MnInspectionPdf('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator('IT-RSPI');
    $pdf->SetAuthor('IT-RSPI');
    $pdf->SetTitle('Laporan Inspeksi Keamanan Fisik dan Kerahasiaan Data #'.(int)$h['id']);
    $pdf->SetSubject('Monev Keamanan Fisik dan Kerahasiaan Data');
    $pdf->SetKeywords('monev, keamanan fisik, kerahasiaan data, inspeksi');
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(true);
    $pdf->SetMargins(12, 12, 12);
    $pdf->SetAutoPageBreak(true, 14);
    $pdf->setFontSubsetting(true);
    $pdf->SetFont('dejavusans', '', 8.7);
    $pdf->AddPage();

    $counts = array_fill_keys(['S', 'TS', 'TA', 'BV'], 0);
    foreach ($details as $d) {
        if (isset($counts[$d['hasil']])) $counts[$d['hasil']]++;
    }

    $html = mn_pdf_styles();
    $html .= '<h1>LAPORAN INSPEKSI KEAMANAN FISIK DAN KERAHASIAAN DATA</h1>';
    $html .= '<table class="plain">
        <tr>
            <th width="18%">No. Formulir</th><td width="32%">'.mn_pdf_text($h['no_formulir'] ?: '-').'</td>
            <th width="18%">Unit/Area</th><td width="32%">'.mn_pdf_text($unitName).'</td>
        </tr>
        <tr>
            <th>Periode</th><td>'.mn_pdf_text($h['periode']).'</td>
            <th>Jenis Inspeksi</th><td>'.mn_pdf_text($h['jenis_inspeksi']).'</td>
        </tr>
        <tr>
            <th>Tanggal/Waktu</th><td>'.mn_pdf_text(mn_pdf_date($h['tgl_waktu_inspeksi'], true)).'</td>
            <th>Status</th><td>'.mn_pdf_text($h['status_inspeksi']).'</td>
        </tr>
        <tr>
            <th>Revisi</th><td>'.mn_pdf_text($h['revisi'] ?: '-').'</td>
            <th>Tanggal Berlaku</th><td>'.mn_pdf_text(mn_pdf_date($h['tgl_berlaku'])).'</td>
        </tr>
        <tr>
            <th>Petugas Pemeriksa</th><td>'.mn_pdf_text(mn_pdf_person($names, $h['pemeriksa_id'])).'</td>
            <th>Jumlah bundle</th><td>'.count($report['bundles']).'</td>
        </tr>
        <tr>
            <th>Verifikator</th><td>'.mn_pdf_text(mn_assignee_label($h, 'verifikator', $names)).'</td>
            <th>ID Inspeksi</th><td>#'.(int)$h['id'].'</td>
        </tr>
    </table>';
    if (!empty($h['catatan_validasi'])) {
        $html .= '<p><strong>Catatan pengembalian:</strong><br>'.nl2br(mn_pdf_text($h['catatan_validasi'])).'</p>';
    }

    foreach ($report['bundles'] as $bundleIndex=>$bundle) {
        $details=mn_visible_bundle_details($bundle); $coverage=$bundle;
        $counts=array_fill_keys(['S','TS','TA','BV'],0);
        foreach ($details as $detail) if (isset($counts[$detail['hasil']])) $counts[$detail['hasil']]++;
        $html.='<h2>Bundle '.($bundleIndex+1).' - '.mn_pdf_text($bundle['nama_lokasi']).'</h2><p>PIC: '.mn_pdf_text($bundle['nama_pic']).'</p>';
    $html .= '<h2>Tabel Checklist</h2><p class="muted">S = Sesuai, TS = Tidak Sesuai, TA = Tidak Berlaku, BV = Belum Diverifikasi.</p>';
    $html .= '<table cellpadding="3">
        <thead><tr>
            <th width="7%" class="center">No.</th>
            <th width="43%">Kriteria Inspeksi</th>
            <th width="12%" class="center">Hasil</th>
            <th width="38%">Lokasi/Bukti</th>
        </tr></thead><tbody>';
    foreach ($details as $i => $d) {
        $proof = trim((string)$d['lokasi_id_perangkat']);
        if (trim((string)$d['catatan_bukti']) !== '') $proof .= ($proof !== '' ? "\n" : '').trim((string)$d['catatan_bukti']);
        if (!empty($d['path_foto_bukti'])) $proof .= ($proof !== '' ? "\n" : '').'Foto bukti: '.mn_pdf_photo_label($d['path_foto_bukti']);
        $html .= '<tr>
            <td width="7%" class="center">'.($i + 1).'</td>
            <td width="43%"><strong>'.mn_pdf_text($d['kategori_snapshot']).'</strong><br>'.nl2br(mn_pdf_text($d['deskripsi_snapshot'])).'</td>
            <td width="12%" class="center"><strong>'.mn_pdf_text($d['hasil']).'</strong></td>
            <td width="38%">'.nl2br(mn_pdf_text($proof ?: '-')).'</td>
        </tr>';
    }
    $html .= '</tbody></table>';

    $html .= '<h2>Cakupan dan Ringkasan Inspeksi</h2>';
    $html .= '<table cellpadding="3">
        <tr><th width="17%" class="center">S</th><th width="17%" class="center">TS</th><th width="17%" class="center">TA</th><th width="17%" class="center">BV</th><th width="32%">Total Butir</th></tr>
        <tr><td class="center">'.(int)$counts['S'].'</td><td class="center">'.(int)$counts['TS'].'</td><td class="center">'.(int)$counts['TA'].'</td><td class="center">'.(int)$counts['BV'].'</td><td>'.count($details).' butir checklist</td></tr>
    </table>';
    $html .= '<table cellpadding="3">';
    $html .= mn_pdf_field('Ruang diperiksa', $coverage['ruang_diperiksa'] ?? '');
    $html .= mn_pdf_field('Komputer diperiksa', $coverage['komputer_diperiksa'] ?? '');
    $html .= mn_pdf_field('Dokumen/printer diperiksa', $coverage['dok_printer_diperiksa'] ?? '');
    $html .= mn_pdf_field('Objek belum diperiksa dan alasan', $coverage['objek_belum_diperiksa_alasan'] ?? '');
    $html .= mn_pdf_field('Alasan TA/BV', $coverage['alasan_ta_bv'] ?? '');
    $html .= '</table>';

        $sign=$bundle['signature'];
        $html.='<table nobr="true" cellpadding="4"><tr><th>Pengesahan PIC Area - '.mn_pdf_text($bundle['nama_lokasi']).'</th></tr><tr><td>';
        if ($sign) {
            $src=mn_pdf_signature_src($sign['path_tanda_tangan'] ?? '');
            if ($src) $html.='<img src="'.$src.'" height="35"><br>';
            $html.='<strong>'.mn_pdf_text($sign['nama_lengkap']).'</strong><br>Tanggal: '.mn_pdf_text(mn_pdf_date($sign['tgl_pengesahan'],true));
        } else $html.='Menunggu Pengesahan';
        $html.='</td></tr></table><br>';
    }
    $details=[];
    foreach ($report['bundles'] as $bundle) {
        foreach (mn_visible_bundle_details($bundle) as $detail) $details[]=$detail;
    }
    $html .= '<h2>Temuan dan Tindakan Perbaikan</h2>';
    if (!$findings) {
        $html .= '<table cellpadding="5"><tr><td>Tidak ada temuan.</td></tr></table>';
    } else {
        foreach ($findings as $t) {
            $html .= '<table cellpadding="3">
                <tr><th width="24%">No. Temuan</th><td width="76%">'.mn_pdf_text($t['no_temuan']).'</td></tr>
                '.mn_pdf_field('Waktu ditemukan', mn_pdf_date($t['waktu_ditemukan'], true)).'
                '.mn_pdf_field('Butir checklist', $t['deskripsi_snapshot']).'
                '.mn_pdf_field('Unit/Area', $t['nama_lokasi'] ?? '').'
                '.mn_pdf_field('Lokasi/ID', $t['lokasi_id_perangkat']).'
                '.mn_pdf_field('Kondisi dan risiko', $t['kondisi_risiko']).'
                '.mn_pdf_field('Tindakan pengamanan/segera', $t['tindakan_segera']).'
                '.mn_pdf_field('Tindakan pencegahan', $t['pencegahan']).'
                '.mn_pdf_field('PIC perbaikan', mn_pdf_person($names, $t['pic_perbaikan_id'])).'
                '.mn_pdf_field('Target selesai', mn_pdf_date($t['target_selesai'])).'
                '.mn_pdf_field('Tanggal pelaksanaan', mn_pdf_date($t['tgl_pelaksanaan'])).'
                '.mn_pdf_field('Bukti perbaikan', mn_pdf_photo_label($t['path_bukti_perbaikan'])).'
                '.mn_pdf_field('Verifikator perbaikan', mn_pdf_person($names, $t['verifikator_id'])).'
                '.mn_pdf_field('Tanggal verifikasi', mn_pdf_date($t['tgl_verifikasi'])).'
                '.mn_pdf_field('Hasil pemeriksaan ulang', $t['hasil_pemeriksaan_ulang']).'
                '.mn_pdf_field('Status perbaikan', $t['status_temuan']).'
                '.mn_pdf_field('No. laporan insiden/tiket', $t['no_laporan_insiden']).'
            </table><br>';
        }
    }

    $roles = [
        'Pemeriksa' => ['Petugas Pemeriksa', $h['pemeriksa_id']],
        'Verifikator' => ['Verifikator Perbaikan', $h['verifikator_id']],
    ];
    $html .= '<h2>Pengesahan Dokumen (Global)</h2><br><table class="signature" nobr="true" cellpadding="4">
        <tr>';
    foreach ($roles as $role => $data) $html .= '<th width="50%">'.mn_pdf_text($data[0]).'</th>';
    $html .= '</tr><tr>';
    foreach ($roles as $role => $data) {
        $signed = $activeSigns[$role] ?? null;
        if ($signed) {
            $src = mn_pdf_signature_src($signed['path_tanda_tangan'] ?? '');
            $image = $src ? '<img src="'.$src.'" height="28"><br>' : '<br><br><br>';
            $method=($signed['metode'] ?? 'akun')==='langsung' ? 'Ditandatangani langsung di perangkat pemeriksa' : 'Disahkan melalui akun aplikasi';
            $html .= '<td width="50%">'.$image.'<strong>'.mn_pdf_text($signed['nama_lengkap'] ?? mn_pdf_person($names, $data[1])).'</strong><br>Tanggal: '.mn_pdf_text(mn_pdf_date($signed['tgl_pengesahan'], true)).'<br><span class="small">'.mn_pdf_text($method).'</span></td>';
        } else {
            $html .= '<td width="50%"><br><br><span class="pending-sign">Menunggu Pengesahan</span><br><br><span class="small">Belum approve di sistem</span></td>';
        }
    }
    $html .= '</tr></table>';
    $html .= '<p class="muted">Dicetak: '.mn_pdf_text(date('d-m-Y H:i:s')).'</p>';

    $pdf->writeHTML($html, true, false, true, false, '');

    $photos = [];
    foreach ($details as $i => $d) {
        $path = mn_pdf_photo_path($d['path_foto_bukti'] ?? '');
        if ($path) $photos[] = ['label' => 'Bukti checklist butir '.($i + 1), 'path' => $path];
    }
    foreach ($findings as $t) {
        $path = mn_pdf_photo_path($t['path_bukti_perbaikan'] ?? '');
        if ($path) $photos[] = ['label' => 'Bukti perbaikan '.$t['no_temuan'], 'path' => $path];
    }
    mn_pdf_render_photo_appendix($pdf, $photos);

    return $pdf->Output('', 'S');
}
