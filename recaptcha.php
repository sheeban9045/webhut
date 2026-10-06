<?php

function verify_recaptcha($token, $remote_ip = null) {
    $token = trim((string) $token);
    if ($token === '') {
        return false;
    }

    $secret = defined('RECAPTCHA_SECRET_KEY') ? RECAPTCHA_SECRET_KEY : '';
    if ($secret === '') {
        error_log('reCAPTCHA: RECAPTCHA_SECRET_KEY is not configured.');
        return false;
    }

    $params = array(
        'secret' => $secret,
        'response' => $token,
    );
    if ($remote_ip) {
        $params['remoteip'] = $remote_ip;
    }

    $response = recaptcha_http_post('https://www.google.com/recaptcha/api/siteverify', $params);
    if ($response === false) {
        error_log('reCAPTCHA: verification request to Google failed.');
        return false;
    }

    $data = json_decode($response, true);
    if (!is_array($data)) {
        error_log('reCAPTCHA: received an invalid verification response.');
        return false;
    }

    if (empty($data['success'])) {
        $error_codes = isset($data['error-codes']) ? implode(', ', (array) $data['error-codes']) : 'unknown';
        error_log('reCAPTCHA: verification failed (' . $error_codes . ').');
        return false;
    }

    return true;
}

//small POST helper, uses cURL when available and falls back to a stream context otherwise
function recaptcha_http_post($url, array $params) {
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ));
        $response = curl_exec($ch);
        if ($response === false) {
            error_log('reCAPTCHA cURL error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $response;
    }

    $context = stream_context_create(array(
        'http' => array(
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($params),
            'timeout' => 10,
        ),
    ));

    return @file_get_contents($url, false, $context);
}
