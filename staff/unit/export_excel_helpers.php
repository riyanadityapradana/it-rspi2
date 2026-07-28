<?php
if (!function_exists('excel_text')) {
    function excel_text($value)
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('excel_date')) {
    function excel_date($value, $format = 'd-m-Y H:i')
    {
        if (empty($value) || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
            return '-';
        }

        $timestamp = strtotime($value);
        return $timestamp ? date($format, $timestamp) : $value;
    }
}

if (!function_exists('excel_start')) {
    function excel_start($filename, $title)
    {
        if (ob_get_length()) {
            ob_clean();
        }

        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        echo "\xEF\xBB\xBF";
        echo '<html><head><meta charset="UTF-8">';
        echo '<style>';
        echo 'body{font-family:Arial,sans-serif;font-size:12px;}';
        echo 'table{border-collapse:collapse;width:100%;}';
        echo 'th{background:#810200;color:#fff;font-weight:bold;}';
        echo 'th,td{border:1px solid #999;padding:6px;vertical-align:top;}';
        echo '.text{mso-number-format:"\@";}';
        echo '</style></head><body>';
        echo '<h3>' . excel_text($title) . '</h3>';
        echo '<p>Diexport pada: ' . date('d-m-Y H:i') . '</p>';
        echo '<table>';
    }
}

if (!function_exists('excel_end')) {
    function excel_end()
    {
        echo '</table></body></html>';
        exit;
    }
}
