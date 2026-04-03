<?php

if (!function_exists('telegram_send_channel_message')) {
    function telegram_send_channel_message($message)
    {
        $botToken = getenv('TELEGRAM_BOT_TOKEN') ?: ''; // Silahkan liat di file .env
        $chatId = getenv('TELEGRAM_CHANNEL_ID') ?: (getenv('TELEGRAM_CHAT_ID') ?: ''); // Silahkan liat di file .env
        $threadId = getenv('TELEGRAM_MESSAGE_THREAD_ID') ?: ''; // Silahkan liat di file .env

        if ($botToken === '' || $chatId === '') {
            error_log('Telegram not configured: missing TELEGRAM_BOT_TOKEN or TELEGRAM_CHANNEL_ID');
            return [
                'ok' => false,
                'reason' => 'not_configured',
            ];
        }

        $payload = [
            'chat_id' => $chatId,
            'text' => $message,
        ];

        if ($threadId !== '') {
            $payload['message_thread_id'] = $threadId;
        }

        $endpoint = 'https://api.telegram.org/bot' . $botToken . '/sendMessage';

        if (function_exists('curl_init')) {
            $ch = curl_init($endpoint);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($ch);
            $curlError = curl_error($ch);
            $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $result = [
                'ok' => ($response !== false && $httpCode >= 200 && $httpCode < 300),
                'response' => $response,
                'http_code' => $httpCode,
                'error' => $curlError,
            ];

            if (!$result['ok']) {
                error_log('Telegram send failed: ' . json_encode($result));
            }

            return $result;
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded`r`n",
                'content' => http_build_query($payload),
                'timeout' => 10,
            ],
        ]);

        $response = @file_get_contents($endpoint, false, $context);
        $result = [
            'ok' => ($response !== false),
            'response' => $response,
        ];

        if (!$result['ok']) {
            error_log('Telegram send failed via file_get_contents');
        }

        return $result;
    }
}
