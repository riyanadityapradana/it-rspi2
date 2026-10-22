<?php

// Transport terpisah agar pengujian memakai server lokal tanpa mengirim pesan asli.
if (!function_exists('telegram_http_post')) {
    function telegram_http_post($endpoint, $payload, $timeout, $connectTimeout)
    {
        if (function_exists('curl_init')) {
            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => http_build_query($payload),
                CURLOPT_RETURNTRANSFER => true, CURLOPT_CONNECTTIMEOUT => $connectTimeout,
                CURLOPT_TIMEOUT => $timeout, CURLOPT_FOLLOWLOCATION => false]);
            $response = curl_exec($ch);
            $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $errorCode = curl_errno($ch);
            curl_close($ch);
            return ['response' => $response, 'http_code' => $httpCode, 'error_code' => $errorCode];
        }

        $context = stream_context_create(['http' => ['method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($payload), 'timeout' => $timeout,
            'ignore_errors' => true, 'follow_location' => 0]]);
        $response = @file_get_contents($endpoint, false, $context);
        $httpCode = 0;
        foreach ($http_response_header ?? [] as $header) {
            if (preg_match('~^HTTP/\\S+ (\\d{3})~', $header, $match)) $httpCode = (int) $match[1];
        }
        return ['response' => $response, 'http_code' => $httpCode, 'error_code' => $response === false ? 1 : 0];
    }
}

function telegram_message_result($transport)
{
    $body = $transport['response'];
    $data = is_string($body) ? json_decode($body, true) : null;
    $status = (int) $transport['http_code'];
    return ['ok' => (int) $transport['error_code'] === 0 && $status >= 200 && $status < 300 && is_array($data) && ($data['ok'] ?? false) === true,
        'response' => $body, 'http_code' => $status, 'error_code' => (int) $transport['error_code']];
}

if (!function_exists('telegram_send_channel_message')) {
    // Pemanggil lama tetap mengirim plain text. Opsi hanya ditentukan kode server.
    function telegram_send_channel_message($message, $options = [])
    {
        $botToken = getenv('TELEGRAM_BOT_TOKEN') ?: '';
        $chatId = getenv('TELEGRAM_CHANNEL_ID') ?: (getenv('TELEGRAM_CHAT_ID') ?: '');
        $threadId = getenv('TELEGRAM_MESSAGE_THREAD_ID') ?: '';
        if ($botToken === '' || $chatId === '') {
            error_log('Telegram not configured: missing bot token or chat ID');
            return ['ok' => false, 'reason' => 'not_configured'];
        }
        // Portal memerlukan cURL supaya batas waktu total juga mencakup transfer respons.
        if (!empty($options['require_curl']) && !function_exists('curl_init')) {
            error_log('Telegram send failed: cURL unavailable');
            return ['ok' => false, 'reason' => 'curl_unavailable'];
        }
        $payload = ['chat_id' => $chatId, 'text' => $message];
        if ($threadId !== '') $payload['message_thread_id'] = $threadId;
        if (($options['parse_mode'] ?? null) === 'HTML') $payload['parse_mode'] = 'HTML';
        $timeout = max(1, min(10, (int) ($options['timeout'] ?? 10)));
        $connectTimeout = max(1, min($timeout, (int) ($options['connect_timeout'] ?? 3)));
        try {
            $result = telegram_message_result(telegram_http_post(
                'https://api.telegram.org/bot' . $botToken . '/sendMessage', $payload, $timeout, $connectTimeout));
        } catch (Throwable $e) {
            // Exception dapat berisi URL dengan token; jangan dicatat atau ditampilkan.
            $result = ['ok' => false, 'reason' => 'transport_exception'];
        }
        if (!$result['ok']) {
            error_log('Telegram send failed: HTTP ' . ($result['http_code'] ?? 0)
                . ', transport ' . ($result['error_code'] ?? 0));
        }
        return $result;
    }
}
