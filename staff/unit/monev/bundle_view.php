<?php
require_once __DIR__.'/criteria_rules.php';
function mn_canvas($name,$label) {
    echo '<details open class="mn-signature" data-signature-pad><summary>'.mn_e($label).'</summary><canvas width="520" height="180" aria-label="'.mn_e($label).'"></canvas><input type="hidden" name="'.mn_e($name).'"><button type="button" class="btn btn-outline-secondary btn-sm" data-signature-clear>Hapus tanda tangan</button></details>';
}
function mn_render_bundle($b,$rows,$units,$editable,$isNew=false,$h=null) {
    global $mnEndpoint,$mnUser;
    $prefix=$isNew ? 'new_bundle' : 'bundle['.$b['id'].']';
    $signed=$isNew ? null : mn_signed($b['inspeksi_id'],'PIC/Kepala Unit',$b['id']);
    $finished=$signed && ($signed['path_tanda_tangan'] || ($h && $h['status_inspeksi']==='Selesai'));
    $summary=$isNew ? 'Bundle unit/area baru' : $b['nama_lokasi'].' - '.$b['nama_pic'].' - Status: '.($finished?'Selesai':'Draft');
    echo '<details class="mn-card mn-bundle" '.($isNew || !$b['disimpan_pada']?'open':'').'><summary>'.mn_e($summary).'</summary><div class="mn-grid">';
    if ($editable) {
        echo '<label class="mn-field">Unit/Area<select name="'.mn_e($prefix.'[unit_area_id]').'" data-unit-search required style="width:100%"><option value="">Pilih unit/area</option>';
        foreach ($units as $id=>$name) echo '<option value="'.(int)$id.'" '.(!$isNew && (int)$b['unit_area_id']===(int)$id?'selected':'').'>'.mn_e($name).'</option>';
        echo '</select></label>';
        mn_employee_select($prefix.'[pic_area_nik]','PIC Area / Kepala Unit',$b['pic_area_nik'] ?? '',($b['pic_area_nik'] ?? '').' - '.($b['nama_pic'] ?? ''),$isNew || !empty($b['pic_area_nik']));
        if (!$isNew && !$b['pic_area_nik']) echo '<p>PIC akun lama: '.mn_e($b['nama_pic']).'. Pilih pegawai hanya jika perlu mengganti PIC.</p>';
    } else echo '<p>Unit/Area: '.mn_e($b['nama_lokasi']).'</p><p>PIC: '.mn_e($b['nama_pic']).'</p>';
    echo '</div><p>S = Sesuai · TS = Tidak Sesuai · TA = Tidak Berlaku · BV = Belum Diverifikasi</p>';
    $totals=array_fill_keys(['S','TS','TA','BV'],0);
    foreach ($rows as $n=>$d) {
        $key=$isNew ? 'new_bundle[detail]['.$d['id'].']' : 'detail['.$d['id'].']';
        $result=$isNew ? '' : $d['hasil']; if (isset($totals[$result])) $totals[$result]++;
        $category=$isNew?$d['kategori']:$d['kategori_snapshot'];
        $server=strcasecmp(trim($category),'Ruang Server')===0;
        $hidden=!$isNew && mn_server_not_applicable($b['nama_lokasi'],$category);
        echo '<article class="mn-card"'.($server?' data-server-criterion':'').($hidden?' hidden':'').'><h3>'.($n+1).'. '.mn_e($category).'</h3><p>'.mn_e($isNew?$d['deskripsi']:$d['deskripsi_snapshot']).'</p>';
        if ($editable) {
            echo '<fieldset class="mn-results"><legend class="sr-only">Hasil butir '.($n+1).'</legend>';
            foreach (['S'=>'Sesuai','TS'=>'Tidak Sesuai','TA'=>'Tidak Berlaku','BV'=>'Belum Diverifikasi'] as $code=>$label) echo '<label class="mn-result '.$code.'"><input type="radio" name="'.mn_e($key.'[hasil]').'" value="'.$code.'" '.($result===$code?'checked':'').' required><span>'.$code.'<small>'.$label.'</small></span></label>';
            echo '</fieldset><p class="mn-ts-hint" '.($result==='TS'?'':'hidden').'>Temuan otomatis tersedia setelah checklist disimpan.</p>';
            mn_input($key.'[lokasi]','Lokasi / ID perangkat',$isNew?'':$d['lokasi_id_perangkat']);
            mn_area($key.'[catatan]','Catatan / bukti',$isNew?'':$d['catatan_bukti']);
            mn_photo(($isNew?'foto_new_':'foto_').$d['id'],'Foto bukti / kamera');
        } else echo '<p>'.mn_e($result.' · '.$d['lokasi_id_perangkat']).'</p><p class="mn-pre">'.mn_e($d['catatan_bukti']).'</p>';
        if (!$isNew && $d['path_foto_bukti']) echo '<a target="_blank" rel="noopener" href="'.mn_e($mnEndpoint.'/file.php?id='.$d['id']).'">Lihat foto bukti tersimpan</a>';
        echo '</article>';
    }
    echo '<h3>Cakupan dan rekapitulasi bundle</h3><p data-bundle-counts>';
    foreach ($totals as $code=>$count) echo '<span class="mn-count" data-count="'.$code.'">'.$code.': '.$count.'</span>';
    echo '</p>';
    foreach (['ruang_diperiksa'=>'Ruang diperiksa','komputer_diperiksa'=>'Komputer diperiksa','dok_printer_diperiksa'=>'Dokumen / printer diperiksa','objek_belum_diperiksa_alasan'=>'Objek belum diperiksa dan alasannya','alasan_ta_bv'=>'Alasan TA / BV'] as $key=>$label) {
        if ($editable) mn_area($prefix.'['.$key.']',$label,$isNew?'':$b[$key]); else echo '<p><strong>'.mn_e($label).'</strong><br>'.nl2br(mn_e($b[$key])).'</p>';
    }
    echo '<h3>Pengesahan PIC bundle</h3>';
    if ($finished) echo '<p>Tanda tangan PIC tersimpan: '.mn_e($signed['nama_penanda'] ?: $b['nama_pic']).' · '.mn_e($signed['tgl_pengesahan']).'</p>';
    if ($editable) {
        echo '<p><small>Tanda tangan PIC wajib untuk mengesahkan bundle. Perubahan isian membatalkan tanda tangan bundle ini; isi ulang sebelum pengesahan.</small></p>';
        mn_canvas($isNew?'signature_pic_new':'signature_pic_'.$b['id'],'Tanda tangan PIC Area');
    }
    elseif (!$finished && $h && $h['status_inspeksi']!=='Selesai' && ((int)$mnUser['id_user']===(int)$h['pemeriksa_id'] || (int)$mnUser['id_user']===(int)$b['pic_area_id'])) {
        mn_form_start('sign_bundle',$h); echo '<input type="hidden" name="bundle_id" value="'.(int)$b['id'].'">';
        mn_canvas('signature_data','Tanda tangan PIC Area');
        echo '<button class="btn btn-success">Sahkan bundle ini</button></form>';
    }
    echo '</details>';
}
