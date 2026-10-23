<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
function cr_sql_file($db, $file, $schema) {
    $sql = str_replace('USE `it-rspi`;', "USE `$schema`;", file_get_contents($file));
    $delimiter = ';'; $buffer = '';
    foreach (explode("\n", $sql) as $line) {
        if (preg_match('/^DELIMITER\s+(\S+)\s*$/', trim($line), $m)) { $delimiter = $m[1]; continue; }
        $buffer .= $line . "\n"; $trimmed = rtrim($buffer);
        if (substr($trimmed, -strlen($delimiter)) !== $delimiter) continue;
        $result = $db->query(substr($trimmed, 0, -strlen($delimiter))); $buffer = '';
        if ($result instanceof mysqli_result) $result->free();
        while ($db->more_results() && $db->next_result()) { if ($result = $db->store_result()) $result->free(); }
    }
    if (trim($buffer) !== '') throw new RuntimeException('Incomplete SQL');
}
function cr_image($blank = false) {
    $im = imagecreatetruecolor(300, 120); imagefill($im, 0, 0, imagecolorallocate($im, 255, 255, 255));
    if (!$blank) { $ink = imagecolorallocate($im, 20, 25, 35); imagesetthickness($im, 4); imageline($im, 20, 80, 100, 25, $ink); imageline($im, 100, 25, 180, 90, $ink); }
    ob_start(); imagepng($im); $raw = ob_get_clean(); imagedestroy($im);
    return 'data:image/png;base64,' . base64_encode($raw);
}

// Lolos CAPTCHA melalui endpoint produksi; kode hanya dibaca dari sesi fixture lokal.
// Tidak ada parameter/env bypass CAPTCHA pada aplikasi.
function cr_public_access_cookie($base, $storage) {
    $cookie = '';
    $request = function ($fields = null) use ($base, &$cookie) {
        $ch = curl_init(rtrim($base, '/') . '/verifikasi-akses.php');
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_HEADER=>true, CURLOPT_COOKIE=>$cookie, CURLOPT_TIMEOUT=>10]);
        if ($fields !== null) curl_setopt_array($ch, [CURLOPT_POST=>true, CURLOPT_POSTFIELDS=>http_build_query($fields)]);
        $raw = curl_exec($ch); if ($raw === false) throw new RuntimeException('CAPTCHA fixture HTTP unavailable');
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE); $length = curl_getinfo($ch, CURLINFO_HEADER_SIZE); curl_close($ch);
        if (preg_match('/Set-Cookie: (portal_tiket=[^;\r\n]+)/i', substr($raw,0,$length), $m)) $cookie=$m[1];
        return ['status'=>$status, 'body'=>substr($raw,$length)];
    };
    $get = $request();
    preg_match('/name="captcha_id" value="([a-f0-9]{32})"/', $get['body'], $id);
    preg_match('/name="csrf_token" value="([a-f0-9]{64})"/', $get['body'], $csrf);
    if ($get['status'] !== 200 || !isset($id[1], $csrf[1])) throw new RuntimeException('CAPTCHA fixture form unavailable');
    $code = null;
    foreach (glob($storage . '/sessions/sess_*') ?: [] as $session) {
        if (preg_match('/s:32:"' . $id[1] . '";a:2:\{s:4:"code";s:5:"([A-Z2-9]{5})";/', file_get_contents($session), $m)) { $code=$m[1]; break; }
    }
    if ($code === null) throw new RuntimeException('CAPTCHA fixture session missing');
    if ($request(['csrf_token'=>$csrf[1], 'captcha_id'=>$id[1], 'captcha'=>$code])['status'] !== 303) throw new RuntimeException('CAPTCHA fixture submit failed');
    return $cookie;
}
