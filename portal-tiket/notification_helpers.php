<?php

function pt_notify_new_request($ticket)
{
    if (getenv('PORTAL_TELEGRAM_ENABLED') === '0') return;
    try {
        require_once __DIR__ . '/../config/telegram.php';
        $escape = function ($value) {
            return htmlspecialchars(preg_replace('/\s+/u', ' ', $value), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        };
        $title = $ticket['jenis_tiket'] === 'Fitur Baru' ? 'Nama Fitur Baru' : 'Judul Perubahan';
        $message = "🚨 <b>TIKET IT!</b> 🚨\n\n"
            . '<b>No. Tiket:</b> ' . $escape($ticket['no_tiket']) . "\n"
            . '<b>Jenis Layanan:</b> ' . $escape($ticket['jenis_tiket']) . "\n"
            . '<b>Sistem:</b> ' . $escape($ticket['nama_sistem']) . "\n"
            . '<b>Pemohon:</b> ' . $escape($ticket['nama_pengaju']) . "\n"
            . '<b>Unit:</b> ' . $escape($ticket['unit_pemohon']) . "\n"
            . '<b>' . $title . ':</b> ' . $escape($ticket['nama_perubahan_fitur']) . "\n\n"
            . 'Silakan cek dashboard IT-RSPI untuk melakukan Analisis.';
        $result = telegram_send_channel_message($message,
            ['parse_mode' => 'HTML', 'connect_timeout' => 2, 'timeout' => 3, 'require_curl' => true]);
        if (!is_array($result) || ($result['ok'] ?? false) !== true) {
            error_log('Portal Telegram notification failed for ticket ID ' . (int) $ticket['id_tiket']);
        }
    } catch (Throwable $e) {
        // Kegagalan notifikasi terpisah dari transaksi dan konfirmasi sukses tiket.
        error_log('Portal Telegram notification exception for ticket ID ' . (int) $ticket['id_tiket']);
    }
}
