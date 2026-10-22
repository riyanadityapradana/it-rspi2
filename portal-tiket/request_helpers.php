<?php
require_once __DIR__ . '/../config/portal_tiket.php';
require_once __DIR__ . '/notification_helpers.php';

function pt_session()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        ini_set('session.use_strict_mode', '1');
        session_name('portal_tiket');
        session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'path' => rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/') . '/']);
        session_start();
    }
    if (empty($_SESSION['pt_csrf'])) $_SESSION['pt_csrf'] = bin2hex(random_bytes(32));
    header('Cache-Control: no-store');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: same-origin');
}

function pt_query($db, $sql, $params = [])
{
    $stmt = $db->prepare($sql);
    if ($params) $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    $stmt->execute();
    return $stmt;
}

function pt_rows($db, $sql, $params = [])
{
    return pt_query($db, $sql, $params)->get_result()->fetch_all(MYSQLI_ASSOC);
}

function pt_text($value, $label, $max)
{
    if (!is_string($value) || !mb_check_encoding($value, 'UTF-8')) throw new DomainException($label . ' tidak valid.');
    $value = trim($value);
    if ($value === '' || mb_strlen($value) > $max) throw new DomainException($label . ' wajib diisi, maksimal ' . $max . ' karakter.');
    return $value;
}

function pt_positive_id($value, $label)
{
    if (!is_string($value) || !ctype_digit($value) || (int) $value < 1 || (int) $value > 2147483647) {
        throw new DomainException('Pilih ' . $label . ' yang valid.');
    }
    return (int) $value;
}

function pt_request_definition($type)
{
    $title = ['label' => 'Nama Perubahan', 'max' => 255, 'id' => 'nama-perubahan', 'input' => true];
    if ($type === 'Change Request') return [
        'file' => 'change-request.php', 'form_id' => 'change-request-form', 'prefix' => 'REQ-',
        'title' => 'Form Change Request', 'intro' => 'Lengkapi pengajuan perubahan pada sistem yang sudah digunakan.',
        'section' => 'Rincian Perubahan', 'fields' => [
            'nama_perubahan_fitur' => $title,
            'kondisi_masalah' => ['label' => 'Kondisi/masalah Saat Ini', 'max' => 10000, 'id' => 'kondisi-masalah'],
            'perubahan_diharapkan' => ['label' => 'Perubahan yang Diharapkan', 'max' => 10000, 'id' => 'perubahan-diharapkan'],
            'dampak_modul' => ['label' => 'Dampak Terhadap Unit/Modul/Integrasi', 'max' => 10000, 'id' => 'dampak-modul']
        ]
    ];
    if ($type === 'Fitur Baru') return [
        'file' => 'fitur-baru.php', 'form_id' => 'new-feature-form', 'prefix' => 'NEWFEAT-',
        'title' => 'Form Fitur Baru', 'intro' => 'Lengkapi permintaan fitur, modul, atau aplikasi baru untuk mendukung kebutuhan unit Anda.',
        'section' => 'Rincian Fitur Baru', 'fields' => [
            'nama_perubahan_fitur' => ['label' => 'Nama Fitur Baru', 'max' => 255, 'id' => 'nama-fitur-baru', 'input' => true],
            'latar_belakang' => ['label' => 'Latar Belakang & Kebutuhan', 'max' => 10000, 'id' => 'latar-belakang'],
            'deskripsi_alur_kerja' => ['label' => 'Deskripsi & Alur Kerja', 'max' => 10000, 'id' => 'deskripsi-alur-kerja'],
            'target_pengguna' => ['label' => 'Target Pengguna', 'max' => 255, 'id' => 'target-pengguna', 'input' => true],
            'output_diharapkan' => ['label' => 'Output yang Diharapkan', 'max' => 10000, 'id' => 'output-diharapkan']
        ]
    ];
    throw new DomainException('Jenis formulir tidak valid.');
}

function pt_assert_draft_type($draft, $type)
{
    $definition = pt_request_definition($type);
    // Reservasi Change Request sebelum penambahan tipe tetap dapat digunakan.
    if (($draft['jenis_tiket'] ?? 'Change Request') !== $type ||
        !preg_match('/^' . preg_quote($definition['prefix'], '/') . '\d{4}-\d{3,}$/D', $draft['no_tiket'] ?? '')) {
        throw new DomainException('Reservasi nomor tidak sesuai dengan jenis formulir. Buka Form Baru untuk melanjutkan.');
    }
}

function pt_reserve($db, $type = 'Change Request')
{
    $definition = pt_request_definition($type);
    $date = date('Y-m-d H:i:s');
    $period = date('Ym', strtotime($date));
    $db->begin_transaction();
    try {
        pt_query($db, 'INSERT INTO tiket_nomor_bulanan (periode,urutan_terakhir) VALUES (?,1) ON DUPLICATE KEY UPDATE urutan_terakhir=urutan_terakhir+1', [$period]);
        $number = pt_rows($db, 'SELECT urutan_terakhir FROM tiket_nomor_bulanan WHERE periode=?', [$period])[0]['urutan_terakhir'];
        $db->commit();
    } catch (Throwable $e) { $db->rollback(); throw $e; }
    return ['no_tiket' => $definition['prefix'] . date('ym', strtotime($date)) . '-' . str_pad($number, 3, '0', STR_PAD_LEFT),
        'jenis_tiket' => $type, 'tgl_pengajuan' => $date, 'created_at' => time(), 'saved_id' => null];
}

function pt_employee($id)
{
    $rows = pt_rows(pt_connection(true), "SELECT id,nama FROM pegawai WHERE id=? AND stts_aktif='AKTIF' LIMIT 1", [$id]);
    if (!$rows) throw new DomainException('Pegawai tidak ditemukan atau sudah tidak aktif. Pilih ulang Nama Pengaju.');
    return $rows[0];
}

function pt_employee_search($term, $page)
{
    if (!is_string($term) || !mb_check_encoding($term, 'UTF-8') || mb_strlen($term) > 80) return ['results' => [], 'pagination' => ['more' => false]];
    $term = trim($term);
    // Bukan sekadar tiga karakter: angka dan tanda baca tidak dihitung sebagai huruf.
    if (preg_match_all('/\p{L}/u', $term) < 3) return ['results' => [], 'pagination' => ['more' => false]];
    $page = max(1, min(50, (int) $page));
    $offset = ($page - 1) * 20;
    $like = '%' . strtr($term, ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
    $rows = pt_rows(pt_connection(true), "SELECT id,nama FROM pegawai WHERE stts_aktif='AKTIF' AND nama LIKE ? ESCAPE '!' ORDER BY nama,id LIMIT 21 OFFSET $offset", [$like]);
    return ['results' => array_map(function ($row) { return ['id' => (string) $row['id'], 'text' => $row['nama']]; }, array_slice($rows, 0, 20)),
        'pagination' => ['more' => count($rows) > 20]];
}

function pt_signature($data)
{
    if (!is_string($data) || strlen($data) > 350000 || strpos($data, 'data:image/png;base64,') !== 0) throw new DomainException('Tanda tangan wajib diisi dalam format gambar PNG.');
    $raw = base64_decode(substr($data, 22), true);
    $size = $raw !== false ? @getimagesizefromstring($raw) : false;
    if (!$size || $size[2] !== IMAGETYPE_PNG || $size[0] < 50 || $size[1] < 30 || $size[0] > 1200 || $size[1] > 480) throw new DomainException('Gambar tanda tangan tidak valid atau ukurannya terlalu besar.');
    $image = @imagecreatefromstring($raw);
    if (!$image) throw new DomainException('Gambar tanda tangan tidak dapat dibaca.');
    $ink = 0; $minX = $size[0]; $maxX = 0; $minY = $size[1]; $maxY = 0;
    for ($y = 0; $y < $size[1]; $y += 2) {
        for ($x = 0; $x < $size[0]; $x += 2) {
            $c = imagecolorsforindex($image, imagecolorat($image, $x, $y));
            if ($c['alpha'] < 100 && max($c['red'], $c['green'], $c['blue']) < 220) {
                ++$ink; $minX = min($minX, $x); $maxX = max($maxX, $x); $minY = min($minY, $y); $maxY = max($maxY, $y);
            }
        }
    }
    if ($ink < 20 || $maxX - $minX < 10 || $maxY - $minY < 5) { imagedestroy($image); throw new DomainException('Tanda tangan masih kosong atau belum cukup jelas.'); }
    ob_start(); imagepng($image); $clean = ob_get_clean(); imagedestroy($image);
    return 'data:image/png;base64,' . base64_encode($clean);
}

function pt_save_change_request($db, $draft, $input, $csrf)
{
    return pt_save_request($db, $draft, $input, $csrf, 'Change Request');
}

function pt_save_new_feature($db, $draft, $input, $csrf)
{
    return pt_save_request($db, $draft, $input, $csrf, 'Fitur Baru');
}

function pt_save_request($db, $draft, $input, $csrf, $type)
{
    if (!is_string($csrf) || !hash_equals($_SESSION['pt_csrf'], $csrf)) throw new DomainException('Sesi formulir tidak valid. Muat ulang halaman dan coba lagi.');
    pt_assert_draft_type($draft, $type);
    $definition = pt_request_definition($type);
    // Identitas dan nilai otomatis hanya berasal dari data tepercaya di server.
    $existing = pt_rows($db, 'SELECT id_tiket FROM tiket_pengajuan WHERE no_tiket=? AND jenis_tiket=?', [$draft['no_tiket'], $type]);
    if ($existing) return (int) $existing[0]['id_tiket'];
    $systemId = pt_positive_id($input['id_sistem'] ?? null, 'Sistem Terkait');
    $unitId = pt_positive_id($input['unit_id'] ?? null, 'Unit Pemohon');
    $employee = pt_employee(pt_positive_id($input['pegawai_id'] ?? null, 'Nama Pengaju'));
    $details = [];
    foreach ($definition['fields'] as $column => $field) $details[$column] = pt_text($input[$column] ?? null, $field['label'], $field['max']);
    $phone = pt_text($input['kontak_pengaju'] ?? null, 'No. WhatsApp', 25);
    if (!preg_match('/^\+?[0-9\s()\-]+$/D', $phone)) throw new DomainException('Nomor WhatsApp hanya boleh berisi angka, awalan +, spasi, tanda kurung, atau tanda hubung.');
    $phone = preg_replace('/\D/', '', $phone);
    if (strpos($phone, '0') === 0) $phone = '62' . substr($phone, 1);
    if (strlen($phone) < 9 || strlen($phone) > 15) throw new DomainException('Nomor WhatsApp harus terdiri dari 9 sampai 15 digit.');
    $signature = pt_signature($input['ttd_pengaju'] ?? null);
    $db->begin_transaction();
    try {
        $system = pt_rows($db, 'SELECT id_sistem,nama_sistem FROM master_sistem WHERE id_sistem=? AND is_active=1 FOR UPDATE', [$systemId]);
        if (!$system) throw new DomainException('Sistem yang dipilih tidak tersedia atau sudah tidak aktif.');
        $unit = pt_rows($db, 'SELECT nama_lokasi FROM tb_lokasi WHERE lokasi_id=? FOR UPDATE', [$unitId]);
        if (!$unit) throw new DomainException('Unit pemohon tidak ditemukan. Pilih ulang unit.');
        $unitName = pt_text($unit[0]['nama_lokasi'], 'Nama unit', 150);
        $employeeName = pt_text($employee['nama'], 'Nama pegawai', 150);
        pt_query($db, 'SET @portal_tiket_no_reservasi=?, @portal_tiket_tgl_reservasi=?', [$draft['no_tiket'], $draft['tgl_pengajuan']]);
        pt_query($db, "INSERT INTO tiket_pengajuan (no_tiket,jenis_tiket,id_sistem,kontak_pengaju,tgl_pengajuan,unit_pemohon,nama_pengaju,ttd_pengaju,status_tiket) VALUES (?,?,?,?,?,?,?,?,'Menunggu Analisis')",
            [$draft['no_tiket'], $type, $systemId, $phone, $draft['tgl_pengajuan'], $unitName, $employeeName, $signature]);
        $id = $db->insert_id;
        // Nama kolom hanya berasal dari definisi server, tidak dari payload pengguna.
        $columns = implode(',', array_keys($details));
        $placeholders = implode(',', array_fill(0, count($details) + 1, '?'));
        pt_query($db, 'INSERT INTO tiket_detail_request (id_tiket,' . $columns . ') VALUES (' . $placeholders . ')', array_merge([$id], array_values($details)));
        $db->commit();
    } catch (Throwable $e) { $db->rollback(); throw $e; }
    finally { $db->query('SET @portal_tiket_no_reservasi=NULL, @portal_tiket_tgl_reservasi=NULL'); }
    // Hanya INSERT baru yang sudah dikomit; retry di atas tidak mengirim ulang.
    pt_notify_new_request(['id_tiket' => $id, 'no_tiket' => $draft['no_tiket'], 'jenis_tiket' => $type,
        'nama_sistem' => $system[0]['nama_sistem'], 'nama_pengaju' => $employeeName,
        'unit_pemohon' => $unitName, 'nama_perubahan_fitur' => $details['nama_perubahan_fitur']]);
    return $id;
}
