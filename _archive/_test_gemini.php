<?php
require_once 'wp-load.php';

$intent = "Бухгалтерский учет для малого бизнеса";

echo "Testing intent: '{$intent}'\n\n";

$api_key = softmir_get_gemini_key();

$terms = get_terms([
    'taxonomy' => 'software_category',
    'hide_empty' => false,
]);

$categories_list = "";
foreach ($terms as $term) {
    $categories_list .= "- ID: {$term->term_id}, Name: {$term->name}\n";
}

$prompt = "Ты классификатор намерений пользователя для B2B платформы выбора ПО.\n"
    . "Пользователь описал задачу, которую должно решать ПО:\n"
    . "\"{$intent}\"\n\n"
    . "Вот список существующих категорий:\n"
    . "{$categories_list}\n"
    . "Определи наиболее подходящую категорию. Верни ТОЛЬКО ОДНО ЧИСЛО — ID этой категории. Если ни одна не подходит, верни 0.";

echo "Prompt:\n{$prompt}\n\n";

$endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=' . $api_key;
$body = [
    'contents' => [
        ['parts' => [['text' => $prompt]]]
    ],
    'generationConfig' => [
        'temperature' => 0.1, // Строгий ответ
        'maxOutputTokens' => 10,
    ]
];

$response = wp_remote_post($endpoint, [
    'headers' => ['Content-Type' => 'application/json'],
    'body' => wp_json_encode($body),
    'timeout' => 15,
]);

if (is_wp_error($response)) {
    echo "WP Error: " . $response->get_error_message();
    exit;
}

$response_body = wp_remote_retrieve_body($response);
$data = json_decode($response_body, true);
echo "Response Body:\n";
print_r($data);

$generated_text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '0';
$cat_id = intval(trim($generated_text));

echo "\n\nExtracted Category ID: {$cat_id}\n";
