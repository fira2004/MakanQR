<?php
// Minimal wrapper around Stripe's REST API using cURL directly.
// This avoids needing Composer/the stripe-php SDK on a plain XAMPP setup.
function stripe_request($endpoint, $params = [], $method = 'POST') {
    $url = "https://api.stripe.com/v1/" . $endpoint;

    $ch = curl_init();

    if ($method === 'GET') {
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
    } else {
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, STRIPE_SECRET_KEY . ':');
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        return ['error' => ['message' => 'cURL error: ' . $curlError]];
    }

    $decoded = json_decode($response, true);
    return $decoded ?? ['error' => ['message' => 'Invalid response from Stripe']];
}
