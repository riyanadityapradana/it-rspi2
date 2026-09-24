<?php
require_once __DIR__.'/bootstrap.php';
require_once __DIR__.'/bundle_view.php';
header('Cache-Control: no-store');
function mn_select($name,$label,$options,$selected='',$required=true) {
    echo '<label class="mn-field">'.mn_e($label).'<select name="'.mn_e($name).'" '.($required?'required':'').'>';
    echo '<option value="">Pilih...</option>';
    foreach ($options as $key=>$text) echo '<option value="'.mn_e($key).'" '.((string)$key===(string)$selected?'selected':'').'>'.mn_e($text).'</option>';
    echo '</select></label>';
}
function mn_input($name,$label,$value='',$type='text',$required=false) {
    echo '<label class="mn-field">'.mn_e($label).'<input type="'.mn_e($type).'" name="'.mn_e($name).'" value="'.mn_e($value).'" '.($required?'required':'').'></label>';
}
function mn_employee_select($name, $label, $selected='', $selectedText='', $required=true) {
    global $mnEndpoint;
    echo '<label class="mn-field">'.mn_e($label).'<select name="'.mn_e($name).'" data-employee-search="'.mn_e($mnEndpoint.'/pegawai.php').'" '.($required?'required':'').' style="width:100%"><option value=""></option>';
    if ($selected!=='') echo '<option selected value="'.mn_e($selected).'">'.mn_e($selectedText).'</option>';
    echo '</select><small>Ketik minimal 3 karakter NIK atau nama pegawai.</small></label>';
}
function mn_area($name,$label,$value='') { echo '<label class="mn-field">'.mn_e($label).'<textarea name="'.mn_e($name).'" rows="3" maxlength="10000">'.mn_e($value).'</textarea></label>'; }
function mn_form_start($action,$h=null,$extra='') {
    global $mnEndpoint;
    echo '<form class="mn-form" action="'.mn_e($mnEndpoint.'/action.php').'" method="post" enctype="multipart/form-data" '.$extra.'>'.mn_csrf().'<input type="hidden" name="action" value="'.mn_e($action).'">';
    if ($h) echo '<input type="hidden" name="id" value="'.(int)$h['id'].'"><input type="hidden" name="version" value="'.(int)$h['version'].'">';
}
function mn_photo($name,$label) { echo '<label class="mn-field">'.mn_e($label).'<input type="file" name="'.mn_e($name).'" accept="image/jpeg,image/png,image/webp" capture="environment"><small>JPG/PNG/WebP, maksimal 5 MB. Foto dikompres sebelum dikirim.</small></label>'; }
function mn_signature_pad($label) {
    echo '<details class="mn-signature" data-signature-pad><summary>Tanda Tangan Langsung (opsional)</summary><p><small>'.mn_e($label).' dapat menandatangani langsung di layar perangkat bila sedang mendampingi inspeksi.</small></p><canvas width="520" height="180" aria-label="Area tanda tangan langsung"></canvas><input type="hidden" name="signature_data"><p><button type="button" class="btn btn-outline-secondary btn-sm" data-signature-clear>Hapus tanda tangan</button></p></details>';
}
?>
<link rel="stylesheet" href="<?= mn_e($mnEndpoint) ?>/monev.css">
<section class="content mn" data-user="<?= (int)$mnUser['id_user'] ?>">
<div class="mn-heading"><div><h1>Monev Keamanan Fisik dan Kerahasiaan Data</h1><p>Inspeksi Keamanan IT · Checklist, tindak lanjut, dan pengesahan</p></div><a class="btn btn-outline-secondary" href="<?= mn_e($mnUrl) ?>">Daftar inspeksi</a></div>
<div id="mn-message" role="alert" tabindex="-1" hidden></div>
<?php try {
    $ready=mn_one("SELECT COUNT(*) n FROM information_schema.tables WHERE table_schema=DATABASE() AND table_name='inspeksi_header'");
    if (!$ready['n']) { echo '<div class="alert alert-warning">Modul belum diinisialisasi. Jalankan migrasi Database/2026-09-22_monev_keamanan.sql pada database aplikasi.</div>'; }
    else {
        $people=($_GET['view'] ?? '') === 'new' ? [] : mn_all("SELECT id_user,nama_lengkap,role FROM tb_user WHERE status='aktif' ORDER BY nama_lengkap");
        $users=[]; foreach ($people as $p) $users[$p['id_user']]=$p['nama_lengkap'].' ('.$p['role'].')';
        $allNames=[]; if (($_GET['view'] ?? '') !== 'new') foreach (mn_all('SELECT id_user,nama_lengkap FROM tb_user') as $p) $allNames[$p['id_user']]=$p['nama_lengkap'];
        $units=[]; foreach (mn_all('SELECT lokasi_id,nama_lokasi FROM tb_lokasi ORDER BY nama_lokasi') as $u) $units[$u['lokasi_id']]=$u['nama_lokasi'];
        $view=$_GET['view'] ?? ''; $id=(int)($_GET['id'] ?? 0);
        if ($view === 'new') {
            mn_need($mnUser['role']==='Staff','Pembuatan inspeksi dilakukan oleh Staff pemeriksa.');
            echo '<div class="mn-card"><h2>Inspeksi baru</h2><p>Pemeriksa: '.mn_e($mnUser['nama_lengkap']).'. Pilih PIC area dan verifikator dari pegawai aktif yang berbeda.</p>';
            mn_form_start('create');
            echo '<div class="mn-grid">';
            echo '<label class="mn-field">Nomor formulir<input type="text" name="no_formulir" value="'.mn_e(mn_form_number()).'" readonly><small>Nomor final ditetapkan otomatis saat draft disimpan.</small></label>';
            mn_input('revisi','Revisi'); mn_input('tgl_berlaku','Tanggal berlaku','','date');
            mn_input('periode','Periode',date('Y-m')); mn_input('tgl_waktu_inspeksi','Tanggal dan waktu inspeksi',date('Y-m-d\TH:i'),'datetime-local',true);
            mn_select('unit_area_id','Unit/area',$units);
            mn_select('jenis_inspeksi','Jenis inspeksi',array_combine(['Rutin','Sewaktu-Waktu','Verifikasi Perbaikan','Setelah Insiden'],['Rutin','Sewaktu-Waktu','Verifikasi Perbaikan','Setelah Insiden']),'Rutin');
            mn_employee_select('pic_area_nik','PIC area / Kepala Unit'); mn_employee_select('verifikator_nik','Verifikator IT');
            echo '</div><button class="btn btn-primary">Buat draft checklist</button></form></div>';
        } elseif ($view === 'master') {
            mn_need($mnUser['role']==='Kepala Ruangan','Akses master hanya untuk Kepala Ruangan.');
            echo '<div class="mn-card"><h2>Pemetaan pegawai ke akun pengesahan</h2><p>Hubungkan pegawai ke akun IT-RSPI miliknya agar dapat melihat penugasan dan mengesahkan laporan. Satu akun untuk satu pegawai.</p>';
            mn_form_start('employee_map',null,'data-confirm="Simpan pemetaan pegawai ke akun ini? Pemetaan menentukan hak akses dan pengesahan laporan."');
            echo '<div class="mn-grid">'; mn_employee_select('pegawai_nik','Pegawai aktif'); mn_select('user_id','Akun IT-RSPI',$users); echo '</div><button class="btn btn-primary">Simpan pemetaan</button></form>';
            foreach (mn_all('SELECT m.nik,m.nama_pegawai,u.nama_lengkap FROM monev_pegawai_akun m JOIN tb_user u ON u.id_user=m.user_id ORDER BY m.nama_pegawai') as $mapping) echo '<p>'.mn_e($mapping['nik'].' - '.$mapping['nama_pegawai'].' → '.$mapping['nama_lengkap']).'</p>';
            echo '</div>';
            echo '<h2>Master kriteria inspeksi</h2><p>Kriteria awal merupakan usulan dan perlu disesuaikan dengan formulir resmi. Perubahan hanya berlaku pada inspeksi baru; laporan lama menyimpan salinan kriterianya.</p>';
            $criteria=mn_all('SELECT * FROM master_kriteria_inspeksi ORDER BY urutan,id');
            $criteria[]=['id'=>0,'kategori'=>'','deskripsi'=>'','urutan'=>count($criteria)+1,'is_active'=>1];
            foreach ($criteria as $k) {
                echo '<details class="mn-card"><summary>'.($k['id']?mn_e($k['kategori'].' — '.$k['deskripsi']).($k['is_active']?'':' (nonaktif)'):'Tambah kriteria').'</summary>';
                mn_form_start('master'); echo '<input type="hidden" name="id" value="'.(int)$k['id'].'">';
                mn_input('kategori','Kategori',$k['kategori'],'text',true); mn_area('deskripsi','Deskripsi',$k['deskripsi']); mn_input('urutan','Urutan',$k['urutan'],'number');
                echo '<label><input type="checkbox" name="is_active" value="1" '.($k['is_active']?'checked':'').'> Aktif</label><p><button class="btn btn-primary">Simpan kriteria</button></p></form></details>';
            }
        } elseif ($id) {
            $h=mn_header($id); $editable=mn_editor($h); $bundles=mn_bundles($id);
            $drows=mn_all('SELECT * FROM inspeksi_detail_checklist WHERE inspeksi_id=? ORDER BY id',[$id]);
            $c=mn_one('SELECT * FROM inspeksi_cakupan WHERE inspeksi_id=?',[$id]);
            $findings=mn_all('SELECT t.*,d.deskripsi_snapshot,d.lokasi_id_perangkat,l.nama_lokasi FROM inspeksi_temuan_perbaikan t JOIN inspeksi_detail_checklist d ON d.id=t.detail_checklist_id JOIN inspeksi_bundle b ON b.id=d.bundle_id JOIN tb_lokasi l ON l.lokasi_id=b.unit_area_id WHERE t.inspeksi_id=? ORDER BY t.id',[$id]);
            echo '<div class="mn-card"><div class="mn-heading"><h2>Inspeksi #'.$id.' · '.mn_e($h['status_inspeksi']).'</h2><a class="btn btn-outline-primary" target="_blank" rel="noopener" href="'.mn_e($mnEndpoint.'/pdf.php?id='.$id).'">Cetak PDF</a></div>';
            echo '<p>'.mn_e(implode(', ',array_unique(array_column($bundles,'nama_lokasi')))).' · '.mn_e($h['periode']).' · '.mn_e($h['tgl_waktu_inspeksi']).' · '.mn_e($h['jenis_inspeksi']).'</p>';
            echo '<p>Formulir: '.mn_e($h['no_formulir']).' | Revisi: '.mn_e($h['revisi']).' | Berlaku: '.mn_e($h['tgl_berlaku']).'</p>';
            echo '<p>Pemeriksa: '.mn_e($allNames[$h['pemeriksa_id']] ?? '').'<br>Jumlah bundle: '.count($bundles).'<br>Verifikator: '.mn_e(mn_assignee_label($h,'verifikator',$allNames)).'</p>';
            if ((!empty($h['pic_area_nik']) && !$h['pic_area_id']) || (!empty($h['verifikator_nik']) && !$h['verifikator_id'])) echo '<p class="alert alert-info">Pengesahan melalui akun memerlukan pemetaan pegawai oleh Kepala Ruangan. Tanda tangan langsung tetap dapat direkam di perangkat pemeriksa.</p>';
            if ($h['catatan_validasi']) echo '<div class="alert alert-warning">Dikembalikan untuk perbaikan: '.mn_e($h['catatan_validasi']).'</div>';
            echo '<nav class="mn-links"><a href="#mn-checklist">Checklist</a><a href="#mn-cakupan">Cakupan</a><a href="#mn-temuan">Temuan dan Perbaikan ('.count($findings).')</a><a href="#mn-approval">Pengesahan</a></nav></div>';
            if ($editable) {
                echo '<div class="mn-card"><label><input type="checkbox" id="mn-local"> Simpan cadangan isian teks di perangkat ini</label><p><small>Opsional untuk koneksi terputus. Foto tidak dicadangkan. Gunakan perangkat pribadi; cadangan dihapus setelah berhasil disimpan.</small></p><button type="button" id="mn-restore" class="btn btn-outline-secondary">Pulihkan cadangan</button> <button type="button" id="mn-clear" class="btn btn-outline-secondary">Hapus cadangan</button><span id="mn-local-status" role="status"></span></div>';
                mn_form_start('save',$h,'id="mn-checklist-form" data-inspection="'.$id.'" data-version="'.$h['version'].'"');
            }
            echo '<h2 id="mn-checklist">Bundle checklist inspeksi</h2><span id="mn-cakupan"></span>';
            $bundles=mn_bundles($id);
            foreach ($bundles as $bundle) {
                $bundleRows=array_values(array_filter($drows,function($d) use ($bundle) { return (int)$d['bundle_id']===(int)$bundle['id']; }));
                mn_render_bundle($bundle,$bundleRows,$units,$editable,false,$h);
            }
            if ($editable) {
                $criteria=mn_all('SELECT * FROM master_kriteria_inspeksi WHERE is_active=1 ORDER BY urutan,id');
                echo '<button type="button" class="btn btn-outline-primary" id="mn-add-bundle">+ Tambah Checklist</button>';
                echo '<fieldset id="mn-new-bundle" disabled hidden><input type="hidden" name="add_bundle" value="1">';
                mn_render_bundle([], $criteria, $units, true, true);
                echo '<button type="button" class="btn btn-outline-secondary" id="mn-cancel-bundle">Batalkan bundle baru</button></fieldset>';
                echo '<input type="hidden" name="bundle_payload_complete" value="1"><div class="mn-save"><button class="btn btn-primary btn-lg">Simpan semua checklist & cakupan</button><span class="mn-dirty" hidden> Ada perubahan yang belum disimpan.</span></div></form>';
            }
            echo '<h2 id="mn-temuan">Temuan dan Perbaikan</h2><p>Temuan mengikuti hasil TS. Lokasi dan butir terhubung otomatis. Pemeriksa menetapkan PIC; verifikasi dilakukan oleh verifikator yang ditugaskan.</p>';
            if (!$findings) echo '<div class="mn-card">Belum ada temuan TS yang tersimpan.</div>';
            foreach ($findings as $t) {
                $canFix=$h['status_inspeksi']!=='Selesai' && $t['status_temuan']!=='Selesai terverifikasi' && in_array((int)$mnUser['id_user'],[(int)$h['pemeriksa_id'],(int)$t['pic_perbaikan_id']],true);
                echo '<article class="mn-card"><h3>'.mn_e($t['no_temuan']).' · '.mn_e($t['status_temuan']).'</h3><p>'.mn_e($t['deskripsi_snapshot']).'<br>Unit/Area: '.mn_e($t['nama_lokasi']).'<br>Lokasi / ID: '.mn_e($t['lokasi_id_perangkat']).' · Ditemukan: '.mn_e($t['waktu_ditemukan']).'</p>';
                if ($canFix) {
                    mn_form_start('finding',$h); echo '<input type="hidden" name="temuan_id" value="'.(int)$t['id'].'">';
                    mn_area('kondisi_risiko','Kondisi / risiko',$t['kondisi_risiko']); mn_area('tindakan_segera','Tindakan segera',$t['tindakan_segera']); mn_area('pencegahan','Pencegahan',$t['pencegahan']);
                    echo '<div class="mn-grid">'; $repairUsers=$users; unset($repairUsers[$h['verifikator_id']]);
                    if ((int)$mnUser['id_user'] !== (int)$h['pemeriksa_id']) $repairUsers=array_intersect_key($repairUsers,[$t['pic_perbaikan_id']=>true]);
                    mn_select('pic_perbaikan_id','PIC perbaikan',$repairUsers,$t['pic_perbaikan_id']);
                    mn_input('target_selesai','Target selesai',$t['target_selesai'],'date',true); mn_input('tgl_pelaksanaan','Tanggal pelaksanaan',$t['tgl_pelaksanaan'],'date');
                    mn_select('status_temuan','Status tindak lanjut',['Terbuka'=>'Terbuka','Dalam proses'=>'Dalam proses'],$t['status_temuan']); mn_input('no_laporan_insiden','Nomor laporan insiden / tiket (referensi)',$t['no_laporan_insiden']); echo '</div>';
                    mn_photo('bukti_perbaikan','Foto bukti perbaikan'); echo '<button class="btn btn-primary">Simpan tindak lanjut</button></form>';
                } else {
                    foreach (['kondisi_risiko'=>'Kondisi / risiko','tindakan_segera'=>'Tindakan segera','pencegahan'=>'Pencegahan','target_selesai'=>'Target selesai','tgl_pelaksanaan'=>'Tanggal pelaksanaan','no_laporan_insiden'=>'Nomor laporan insiden / tiket'] as $key=>$label) echo '<p><strong>'.$label.':</strong> <span class="mn-pre">'.mn_e($t[$key]).'</span></p>';
                    echo '<p>PIC perbaikan: '.mn_e($allNames[$t['pic_perbaikan_id']] ?? 'Belum ditentukan').'</p>';
                }
                if ($t['path_bukti_perbaikan']) echo '<p><a target="_blank" rel="noopener" href="'.mn_e($mnEndpoint.'/file.php?kind=perbaikan&id='.$t['id']).'">Lihat bukti perbaikan</a></p>';
                if ($t['tgl_verifikasi']) echo '<div class="alert alert-success">Diverifikasi '.mn_e($t['tgl_verifikasi']).' oleh '.mn_e($allNames[$t['verifikator_id']] ?? '').'<br>'.mn_e($t['hasil_pemeriksaan_ulang']).'</div>';
                elseif ((int)$mnUser['id_user']===(int)$h['verifikator_id'] && $h['status_inspeksi']==='Menunggu Pengesahan') {
                    mn_form_start('verify',$h); echo '<input type="hidden" name="temuan_id" value="'.(int)$t['id'].'">'; mn_area('hasil_pemeriksaan_ulang','Hasil pemeriksaan ulang'); echo '<button class="btn btn-success">Verifikasi perbaikan selesai</button></form>';
                }
                echo '</article>';
            }
            echo '<div class="mn-card"><h2 id="mn-approval">Pengesahan</h2><p>Pemeriksa dan Verifikator menandatangani satu kali untuk seluruh dokumen. Setiap PIC menandatangani bundlenya sendiri. Dokumen Selesai setelah dua tanda tangan global dan seluruh tanda tangan PIC lengkap.</p>';
            $signatures=mn_all('SELECT p.*,COALESCE(p.nama_penanda,u.nama_lengkap) nama_lengkap FROM inspeksi_pengesahan p JOIN tb_user u ON u.id_user=p.user_id WHERE p.inspeksi_id=? ORDER BY p.id',[$id]);
            if ((int)$mnUser['id_user']===(int)$h['pemeriksa_id'] && $h['status_inspeksi']!=='Selesai') {
                mn_form_start('onsite',$h,'data-confirm="Simpan tanda tangan pihak yang hadir? Dokumen selesai setelah tanda tangan global dan seluruh PIC bundle lengkap."');
                echo '<div class="row">';
                foreach (['pemeriksa'=>['Pemeriksa',$allNames[$h['pemeriksa_id']] ?? ''], 'verifikator'=>['Verifikator',mn_assignee_label($h,'verifikator',$allNames)]] as $key=>[$role,$name]) {
                    echo '<div class="col-md-6"><h3>'.mn_e($role).'</h3><p>'.mn_e($name).'</p>';
                    $signed=null; foreach ($signatures as $s) if ($s['peran']===$role && !$s['dibatalkan_pada'] && $s['path_tanda_tangan']) $signed=$s;
                    if ($signed) echo '<p>Tanda tangan tersimpan: '.mn_e($signed['tgl_pengesahan']).'</p>';
                    else echo '<details open class="mn-signature" data-signature-pad><summary>Tanda Tangan Langsung</summary><canvas width="520" height="180" aria-label="Tanda tangan '.mn_e($role).'"></canvas><input type="hidden" name="signature_'.$key.'"><button type="button" class="btn btn-outline-secondary btn-sm" data-signature-clear>Hapus tanda tangan</button></details>';
                    echo '</div>';
                }
                echo '</div><p><button class="btn btn-success">Simpan tanda tangan</button></p></form>';
            }
            foreach ($signatures as $s) if ($s['bundle_id']===null) echo '<p>'.mn_e($s['peran']).' · '.mn_e($s['nama_lengkap']).' · '.mn_e($s['tgl_pengesahan']).($s['path_tanda_tangan']?' · tanda tangan langsung tersimpan':'').($s['dibatalkan_pada']?' (dibatalkan '.mn_e($s['dibatalkan_pada']).')':' — sah').'</p>';
            if ($editable) { mn_form_start('submit',$h,'data-confirm="Ajukan laporan dan catat pengesahan Anda sebagai pemeriksa?"'); echo '<button class="btn btn-success">Submit dan ajukan pengesahan</button></form>'; }
            if ($h['status_inspeksi']==='Menunggu Pengesahan' && (mn_bundle_pic_access($id,(int)$mnUser['id_user']) || (int)$mnUser['id_user']===(int)$h['verifikator_id'])) {
                $role='Verifikator';
                if ((int)$mnUser['id_user']===(int)$h['verifikator_id'] && !mn_signed($id,$role)) { mn_form_start('approve',$h,'data-confirm="Sahkan laporan ini dengan akun Anda?"'); mn_signature_pad($role); echo '<button class="btn btn-success">Sahkan sebagai '.mn_e($role).'</button></form>'; }
                echo '<details><summary>Kembalikan kepada pemeriksa</summary>'; mn_form_start('return',$h); mn_area('catatan_validasi','Alasan pengembalian'); echo '<button class="btn btn-warning">Kembalikan ke Draft</button></form></details>';
            }
            echo '</div>';
        } else {
            [$scope,$params]=mn_scope(); $where=$scope;
            $status=$_GET['status'] ?? ''; $unitFilter=(int)($_GET['area'] ?? 0); $from=mn_date($_GET['from'] ?? ''); $to=mn_date($_GET['to'] ?? ''); $pending=($_GET['pending'] ?? '')==='1';
            if (in_array($status,['Draft','Menunggu Pengesahan','Selesai'],true)) { $where.=' AND h.status_inspeksi=?'; $params[]=$status; }
            if ($unitFilter) { $where.=' AND EXISTS(SELECT 1 FROM inspeksi_bundle fb WHERE fb.inspeksi_id=h.id AND fb.unit_area_id=?)'; $params[]=$unitFilter; }
            if ($from) { $where.=' AND h.tgl_waktu_inspeksi>=?'; $params[]=$from.' 00:00:00'; }
            if ($to) { $where.=' AND h.tgl_waktu_inspeksi<=?'; $params[]=$to.' 23:59:59'; }
            if ($pending) { $where.=' AND ('.mn_pending_sql().')'; $params[]=$mnUser['id_user']; $params[]=$mnUser['id_user']; }
            echo '<div class="mn-links">';
            if ($mnUser['role']==='Staff') echo '<a class="btn btn-primary" href="'.mn_e($mnUrl.'&view=new').'">Buat inspeksi</a>';
            if ($mnUser['role']==='Kepala Ruangan') echo '<a class="btn btn-outline-primary" href="'.mn_e($mnUrl.'&view=master').'">Master kriteria</a>';
            echo '<a class="btn btn-outline-warning" href="'.mn_e($mnUrl.'&pending=1').'">Menunggu pengesahan saya</a></div>';
            echo '<form method="get" class="mn-card"><input type="hidden" name="unit" value="monev"><div class="mn-grid">';
            mn_select('status','Status',['Draft'=>'Draft','Menunggu Pengesahan'=>'Menunggu Pengesahan','Selesai'=>'Selesai'],$status,false); mn_select('area','Unit/area',$units,$unitFilter,false);
            mn_input('from','Dari tanggal',$from,'date'); mn_input('to','Sampai tanggal',$to,'date'); echo '</div><label><input type="checkbox" name="pending" value="1" '.($pending?'checked':'').'> Menunggu pengesahan saya</label> <button class="btn btn-primary">Terapkan</button> <a href="'.mn_e($mnUrl).'">Reset</a><p><small>Ringkasan dan grafik mengikuti filter tanggal inspeksi, unit, status, dan hak akses.</small></p></form>';
            $stats=mn_one("SELECT COUNT(*) total,COALESCE(SUM(h.status_inspeksi='Draft'),0) draft,COALESCE(SUM(h.status_inspeksi='Menunggu Pengesahan'),0) pending,COALESCE(SUM(h.status_inspeksi='Selesai'),0) done FROM inspeksi_header h WHERE $where",$params);
            $findingStats=mn_one("SELECT COUNT(*) total,COALESCE(SUM(t.status_temuan<>'Selesai terverifikasi'),0) opened,COALESCE(SUM(t.status_temuan<>'Selesai terverifikasi' AND t.target_selesai<CURDATE()),0) overdue FROM inspeksi_temuan_perbaikan t JOIN inspeksi_header h ON h.id=t.inspeksi_id WHERE $where",$params);
            echo '<div class="mn-grid mn-stats">'; foreach (['Inspeksi'=>$stats['total'],'Draft'=>$stats['draft'],'Menunggu pengesahan'=>$stats['pending'],'Selesai'=>$stats['done'],'Temuan belum selesai'=>$findingStats['opened'],'Lewat target'=>$findingStats['overdue']] as $label=>$value) echo '<div class="mn-card"><strong>'.(int)$value.'</strong><span>'.$label.'</span></div>'; echo '</div>';
            $chart=mn_all("SELECT l.nama_lokasi,COUNT(*) n FROM inspeksi_temuan_perbaikan t JOIN inspeksi_header h ON h.id=t.inspeksi_id JOIN inspeksi_detail_checklist cd ON cd.id=t.detail_checklist_id JOIN inspeksi_bundle cb ON cb.id=cd.bundle_id JOIN tb_lokasi l ON l.lokasi_id=cb.unit_area_id WHERE $where GROUP BY l.lokasi_id,l.nama_lokasi ORDER BY n DESC,l.nama_lokasi LIMIT 10",$params);
            echo '<div class="mn-grid"><div class="mn-card"><h2>Temuan TS per unit</h2>'; $max=$chart?max(array_column($chart,'n')):1;
            foreach ($chart as $bar) echo '<div class="mn-bar"><span>'.mn_e($bar['nama_lokasi']).' ('.(int)$bar['n'].')</span><div style="width:'.round(100*$bar['n']/$max).'%"></div></div>';
            if (!$chart) echo '<p>Belum ada temuan.</p>'; echo '</div><div class="mn-card"><h2>Butir paling sering TS</h2>';
            foreach (mn_all("SELECT d.deskripsi_snapshot,COUNT(*) n FROM inspeksi_detail_checklist d JOIN inspeksi_header h ON h.id=d.inspeksi_id WHERE $where AND d.hasil='TS' GROUP BY d.deskripsi_snapshot ORDER BY n DESC LIMIT 10",$params) as $r) echo '<p><strong>'.(int)$r['n'].'×</strong> '.mn_e($r['deskripsi_snapshot']).'</p>';
            echo '</div></div>';
            $page=max(1,(int)($_GET['page'] ?? 1)); $pages=max(1,(int)ceil($stats['total']/20)); $page=min($page,$pages); $offset=($page-1)*20;
            $rows=mn_all("SELECT h.*,(SELECT GROUP_CONCAT(DISTINCT bl.nama_lokasi ORDER BY bl.nama_lokasi SEPARATOR ', ') FROM inspeksi_bundle lb JOIN tb_lokasi bl ON bl.lokasi_id=lb.unit_area_id WHERE lb.inspeksi_id=h.id) nama_lokasi,u.nama_lengkap,(SELECT COUNT(*) FROM inspeksi_temuan_perbaikan t WHERE t.inspeksi_id=h.id AND t.status_temuan<>'Selesai terverifikasi') open_count FROM inspeksi_header h JOIN tb_lokasi l ON l.lokasi_id=h.unit_area_id JOIN tb_user u ON u.id_user=h.pemeriksa_id WHERE $where ORDER BY h.tgl_waktu_inspeksi DESC,h.id DESC LIMIT 20 OFFSET $offset",$params);
            echo '<div class="mn-card table-responsive"><table class="table"><thead><tr><th>Inspeksi</th><th>Waktu / periode</th><th>Unit / pemeriksa</th><th>Status</th><th>Temuan terbuka</th><th>Aksi</th></tr></thead><tbody>';
            foreach ($rows as $r) echo '<tr><td><a href="'.mn_e($mnUrl.'&id='.$r['id']).'">#'.(int)$r['id'].' · '.mn_e($r['jenis_inspeksi']).'</a></td><td>'.mn_e($r['tgl_waktu_inspeksi']).'<br>'.mn_e($r['periode']).'</td><td>'.mn_e($r['nama_lokasi']).'<br>'.mn_e($r['nama_lengkap']).'</td><td>'.mn_e($r['status_inspeksi']).'</td><td>'.(int)$r['open_count'].'</td><td><a class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener" href="'.mn_e($mnEndpoint.'/pdf.php?id='.$r['id']).'">Cetak PDF</a></td></tr>';
            if (!$rows) echo '<tr><td colspan="6">Tidak ada inspeksi pada filter ini.</td></tr>'; echo '</tbody></table></div><nav class="mn-links">';
            $query=['unit'=>'monev','status'=>$status,'area'=>$unitFilter,'from'=>$from,'to'=>$to,'pending'=>$pending?'1':''];
            if ($page>1) echo '<a href="?'.mn_e(http_build_query(array_merge($query,['page'=>$page-1]))).'">Sebelumnya</a>';
            echo '<span>Halaman '.$page.' / '.$pages.'</span>';
            if ($page<$pages) echo '<a href="?'.mn_e(http_build_query(array_merge($query,['page'=>$page+1]))).'">Berikutnya</a>'; echo '</nav>';
        }
    }
} catch (Throwable $e) {
    if (!($e instanceof DomainException)) error_log('Monev page: '.$e->getMessage());
    echo '<div class="alert alert-danger">'.mn_e($e instanceof DomainException?$e->getMessage():'Modul tidak dapat dimuat. Periksa migrasi dan koneksi database.').'</div>';
} ?>
</section>
<script src="<?= mn_e($mnEndpoint) ?>/monev.js?v=<?= filemtime(__DIR__.'/monev.js') ?>" defer></script>
