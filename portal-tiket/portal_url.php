<?php
require_once __DIR__ . '/../config/portal_tiket.php';

function pt_portal_base_url($server)
{
    $configured = getenv('PORTAL_PUBLIC_URL');
    if ($configured !== false && trim($configured) !== '') {
        $base = rtrim(trim($configured), '/');
    } else {
        $host = $server['HTTP_HOST'] ?? ($server['SERVER_NAME'] ?? '');
        if (!is_string($host) || preg_match('/[\s\/\\\\?#@]/', $host)) throw new DomainException('Alamat portal tidak valid.');
        $https = !empty($server['HTTPS']) && !in_array(strtolower((string) $server['HTTPS']), ['off', '0'], true);
        $script = str_replace('\\', '/', $server['SCRIPT_NAME'] ?? '');
        if (!str_starts_with($script, '/') || preg_match('/[\s?#]/', $script)) throw new DomainException('Lokasi portal tidak valid.');
        $root = rtrim(str_replace('\\', '/', dirname(dirname($script))), '/');
        $base = ($https ? 'https://' : 'http://') . $host . $root . '/portal-tiket';
    }
    $parts = parse_url($base); $host = $parts['host'] ?? '';
    if (!$parts || !in_array($parts['scheme'] ?? '', ['http','https'], true) ||
        (!filter_var(trim($host, '[]'), FILTER_VALIDATE_IP) && !filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) ||
        isset($parts['user']) || isset($parts['pass']) || isset($parts['query']) || isset($parts['fragment']) ||
        preg_match('/[\s<>"\x00-\x1f]/', $base)) throw new DomainException('Alamat portal tidak valid.');
    return $base;
}
