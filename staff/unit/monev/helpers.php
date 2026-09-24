<?php
require_once __DIR__.'/employees.php';
require_once __DIR__.'/bundles.php';
function mn_query($sql, $params = []) {
    global $config;
    $stmt = $config->prepare($sql);
    if ($params) { $types = str_repeat('s', count($params)); $stmt->bind_param($types, ...$params); }
    $stmt->execute(); return $stmt;
}
function mn_all($sql, $params = []) { return mn_query($sql, $params)->get_result()->fetch_all(MYSQLI_ASSOC); }
function mn_one($sql, $params = []) { return mn_query($sql, $params)->get_result()->fetch_assoc(); }
function mn_e($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function mn_need($condition, $message) { if (!$condition) throw new DomainException($message); }
function mn_text($value, $max = 10000) {
    mn_need(is_scalar($value) || $value === null, 'Format isian tidak valid.');
    $value = trim((string)$value); mn_need(mb_strlen($value) <= $max, 'Isian terlalu panjang (maksimum '.$max.' karakter).'); return $value;
}
function mn_date($value, $time = false, $required = false) {
    $value = mn_text($value, 30); if (!$required && $value === '') return null;
    $value = str_replace('T', ' ', $value);
    if ($time && strlen($value) === 16) $value .= ':00';
    $fmt = $time ? 'Y-m-d H:i:s' : 'Y-m-d'; $d = DateTime::createFromFormat('!'.$fmt, $value);
    mn_need($d && $d->format($fmt) === $value, 'Tanggal tidak valid.'); return $value;
}
function mn_account($id) {
    $r = mn_one("SELECT id_user,role FROM tb_user WHERE id_user=? AND status='aktif'", [$id]);
    mn_need($r, 'Pilih pegawai aktif.'); return (int)$r['id_user'];
}
function mn_form_number($date = null, $reserve = false) {
    // Reserve only inside the same transaction as creation of the inspection.
    $date = mn_date($date ?? date('Y-m-d'), false, true);
    $prefix = 'INSP-'.str_replace('-', '', $date).'-';
    if ($reserve) mn_query('INSERT INTO inspeksi_nomor_harian (tanggal,urutan) VALUES (?,0) ON DUPLICATE KEY UPDATE tanggal=VALUES(tanggal)',[$date]);
    $counter = mn_one('SELECT urutan FROM inspeksi_nomor_harian WHERE tanggal=?'.($reserve ? ' FOR UPDATE' : ''),[$date]);
    $existing = mn_one('SELECT MAX(CAST(RIGHT(no_formulir,3) AS UNSIGNED)) n FROM inspeksi_header WHERE no_formulir REGEXP ?', ['^'.$prefix.'[0-9]{3}$']);
    $next = max((int)($counter['urutan'] ?? 0), (int)($existing['n'] ?? 0)) + 1;
    mn_need($next <= 999, 'Batas 999 nomor formulir hari ini telah tercapai. Buat inspeksi pada hari berikutnya.');
    if ($reserve) mn_query('UPDATE inspeksi_nomor_harian SET urutan=? WHERE tanggal=?',[$next,$date]);
    return $prefix.str_pad((string)$next,3,'0',STR_PAD_LEFT);
}
function mn_scope($alias = 'h') {
    global $mnUser;
    if ($mnUser['role'] === 'Kepala Ruangan') return ['1=1', []];
    $u = $mnUser['id_user'];
    $pic = mn_assignee_sql('pic_area','sb'); $ver = mn_assignee_sql('verifikator',$alias);
    return ["($alias.pemeriksa_id=? OR EXISTS(SELECT 1 FROM inspeksi_bundle sb WHERE sb.inspeksi_id=$alias.id AND ($pic)=?) OR ($ver)=? OR EXISTS (SELECT 1 FROM inspeksi_temuan_perbaikan st WHERE st.inspeksi_id=$alias.id AND st.pic_perbaikan_id=?))", [$u,$u,$u,$u]];
}
function mn_header($id, $lock = false) {
    [$scope,$params] = mn_scope();
    $pic = mn_assignee_sql('pic_area'); $ver = mn_assignee_sql('verifikator');
    $h = mn_one("SELECT h.*,($pic) AS pic_area_id,($ver) AS verifikator_id FROM inspeksi_header h WHERE h.id=? AND $scope".($lock ? ' FOR UPDATE' : ''), array_merge([$id], $params));
    mn_need($h, 'Inspeksi tidak ditemukan atau akses ditolak.'); return $h;
}
function mn_editor($h) { global $mnUser; return (int)$h['pemeriksa_id'] === (int)$mnUser['id_user'] && $h['status_inspeksi'] === 'Draft'; }
function mn_csrf() { return '<input type="hidden" name="csrf" value="'.mn_e($_SESSION['monev_csrf']).'">'; }
function mn_upload($key, &$created) {
    if (!isset($_FILES[$key]) || $_FILES[$key]['error'] === UPLOAD_ERR_NO_FILE) return null;
    $f = $_FILES[$key];
    mn_need($f['error'] === UPLOAD_ERR_OK && $f['size'] > 0 && $f['size'] <= 5*1024*1024, 'Foto gagal diunggah atau melebihi 5 MB.');
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    $ext = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$mime] ?? null;
    $size = @getimagesize($f['tmp_name']);
    mn_need($ext && $size && $size[0]*$size[1] <= 40000000, 'Gunakan gambar JPG, PNG, atau WebP yang valid (maksimum 40 megapiksel).');
    $name = bin2hex(random_bytes(24)).'.'.$ext;
    $path = __DIR__.'/../../../assets/upload/monev/'.$name;
    mn_need(move_uploaded_file($f['tmp_name'], $path), 'Penyimpanan foto gagal.');
    $created[] = $path; return $name;
}
function mn_signature_upload($key, &$created) {
    mn_need(is_string($_POST[$key] ?? ''),'Format tanda tangan langsung tidak valid.');
    $data = trim($_POST[$key] ?? '');
    if ($data === '') return null;
    mn_need(preg_match('/^data:image\/png;base64,([A-Za-z0-9+\/=]+)$/', $data, $m), 'Format tanda tangan langsung tidak valid.');
    $binary = base64_decode($m[1], true);
    mn_need($binary !== false && strlen($binary) >= 100 && strlen($binary) <= 350000, 'Tanda tangan langsung tidak valid atau terlalu besar.');
    mn_need(substr($binary, 0, 8) === "\x89PNG\x0d\x0a\x1a\x0a", 'Tanda tangan langsung harus berupa PNG.');
    $size = @getimagesizefromstring($binary);
    mn_need($size && $size[0] >= 20 && $size[1] >= 20 && $size[0] <= 1600 && $size[1] <= 800, 'Ukuran tanda tangan langsung tidak valid.');
    $name = bin2hex(random_bytes(24)).'.png';
    $path = __DIR__.'/../../../assets/upload/monev/'.$name;
    mn_need(file_put_contents($path, $binary, LOCK_EX) !== false, 'Penyimpanan tanda tangan langsung gagal.');
    $created[] = $path; return $name;
}
function mn_signed($id, $role, $bundleId=null) {
    if ($role==='PIC/Kepala Unit' && !$bundleId) $bundleId=mn_one('SELECT id FROM inspeksi_bundle WHERE inspeksi_id=? ORDER BY id LIMIT 1',[$id])['id'] ?? null;
    return mn_one('SELECT * FROM inspeksi_pengesahan WHERE inspeksi_id=? AND peran=? AND bundle_id <=> ? AND dibatalkan_pada IS NULL', [$id,$role,$bundleId]);
}
function mn_sign($id, $role, $uid, $signaturePath = null) {
    mn_need($role!=='PIC/Kepala Unit','Pengesahan PIC harus dilakukan per bundle.');
    mn_need(!mn_signed($id,$role), 'Peran ini sudah mengesahkan.');
    mn_query('INSERT INTO inspeksi_pengesahan (inspeksi_id,peran,user_id,path_tanda_tangan) VALUES (?,?,?,?)', [$id,$role,$uid,$signaturePath]);
}
function mn_finish($id) {
    $global=mn_one("SELECT COUNT(DISTINCT peran) n FROM inspeksi_pengesahan WHERE inspeksi_id=? AND bundle_id IS NULL AND peran IN ('Pemeriksa','Verifikator') AND dibatalkan_pada IS NULL AND path_tanda_tangan IS NOT NULL",[$id]);
    $bundles=mn_one("SELECT COUNT(*) total,SUM(NOT EXISTS(SELECT 1 FROM inspeksi_pengesahan p WHERE p.inspeksi_id=b.inspeksi_id AND p.bundle_id=b.id AND p.peran='PIC/Kepala Unit' AND p.dibatalkan_pada IS NULL AND p.path_tanda_tangan IS NOT NULL)) pending FROM inspeksi_bundle b WHERE b.inspeksi_id=?",[$id]);
    if ((int)$global['n']===2 && (int)$bundles['total']>0 && (int)$bundles['pending']===0) mn_query("UPDATE inspeksi_header SET status_inspeksi='Selesai' WHERE id=? AND status_inspeksi='Menunggu Pengesahan'",[$id]);
}
function mn_sign_onsite($h,$uid,&$created) {
    mn_need($uid===(int)$h['pemeriksa_id'],'Hanya pemeriksa dapat merekam tanda tangan langsung.');
    $exam=mn_one('SELECT nama_lengkap,nip FROM tb_user WHERE id_user=?',[$uid]);
    $signers=['pemeriksa'=>['Pemeriksa',$exam['nama_lengkap'],$exam['nip']],
        'verifikator'=>['Verifikator',$h['verifikator_nama'] ?? null,$h['verifikator_nik'] ?? null]];
    foreach (['verifikator'=>'verifikator'] as $key=>$field) {
        if (!$signers[$key][1]) { $u=mn_one('SELECT nama_lengkap,nip FROM tb_user WHERE id_user=?',[$h[$field.'_id']]); $signers[$key][1]=$u['nama_lengkap']; $signers[$key][2]=$u['nip']; }
    }
    $saved=0;
    foreach ($signers as $key=>[$role,$name,$nik]) {
        $path=mn_signature_upload('signature_'.$key,$created);
        if (!$path) continue;
        mn_need(!mn_one('SELECT id FROM inspeksi_pengesahan WHERE inspeksi_id=? AND peran=? AND dibatalkan_pada IS NULL AND path_tanda_tangan IS NOT NULL',[$h['id'],$role]),'Tanda tangan '.$role.' sudah tersimpan.');
        mn_query('UPDATE inspeksi_pengesahan SET dibatalkan_pada=NOW() WHERE inspeksi_id=? AND peran=? AND dibatalkan_pada IS NULL',[$h['id'],$role]);
        mn_query("INSERT INTO inspeksi_pengesahan (inspeksi_id,peran,user_id,path_tanda_tangan,nama_penanda,nik_penanda,metode) VALUES (?,?,?,?,?,?,'langsung')",[$h['id'],$role,$uid,$path,$name,$nik]);
        $saved++;
    }
    mn_need($saved>0,'Isi minimal satu tanda tangan sebelum menyimpan.');
    mn_finish($h['id']);
}
