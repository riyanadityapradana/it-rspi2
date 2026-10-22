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
