<?php
require_once 'wp-load.php';

$api_key = softmir_get_gemini_key();
$endpoint = 'https://generativelanguage.googleapis.com/v1beta/models?key=' . $api_key;

$response = wp_remote_get($endpoint);
if (is_wp_error($response)) {
    echo "WP Error: " . $response->get_error_message();
    exit;
}

$response_body = wp_remote_retrieve_body($response);
$data = json_decode($response_body, true);

foreach ($data['models'] as $model) {
    if (strpos($model['name'], 'generateContent') !== false || isset($model['supportedGenerationMethods'])) {
        echo $model['name'] . "\n";
    }
}
