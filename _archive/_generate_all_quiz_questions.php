<?php
require_once __DIR__ . '/wp-load.php';

echo "=== Mass Generation of Quiz Questions ===\n\n";

if (!function_exists('softmir_get_gemini_key') || !function_exists('pll_default_language')) {
    echo "ERROR: SoftMir Gemini functions or Polylang not found.\n";
    exit;
}

$api_key = softmir_get_gemini_key();
if (empty($api_key)) {
    echo "ERROR: Gemini API key is missing in settings.\n";
    exit;
}

$default_lang = pll_default_language();

// Get all software categories in default language
$terms = get_terms([
    'taxonomy' => 'software_category',
    'hide_empty' => false,
    'lang' => $default_lang,
]);

if (is_wp_error($terms)) {
    echo "Error fetching terms: " . $terms->get_error_message() . "\n";
    exit;
}

echo "Found " . count($terms) . " categories in default language ('{$default_lang}').\n\n";

$endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $api_key;

foreach ($terms as $index => $term) {
    echo "[" . ($index + 1) . "/" . count($terms) . "] Processing: {$term->name} (ID: {$term->term_id})\n";

    // Check if already filled
    $existing = get_field('quiz_questions', 'software_category_' . $term->term_id);
    if (!empty($existing)) {
        echo "   -> Skipped (already has quiz_questions)\n";
        continue;
    }

    $prompt = "Ты AI-ассистент, помогающий выбирать B2B софт. "
        . "Составь 2-3 коротких уточняющих вопроса (с вариантами ответов), "
        . "чтобы помочь пользователю точнее подобрать программу в категории «{$term->name}». "
        . "Обязательно верни только валидный JSON-массив, без markdown (без ```json), по такому формату:\n"
        . "[\n  {\"q\": \"Вопрос 1?\", \"options\": [\"Вариант 1\", \"Вариант 2\"]},\n"
        . "  {\"q\": \"Вопрос 2?\", \"options\": [\"Вариант 1\", \"Вариант 2\"]}\n]\n\n"
        . "Никаких других пояснений, только JSON.";

    $body = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt],
                ],
            ],
        ],
        'generationConfig' => [
            'temperature' => 0.4,
            'responseMimeType' => 'application/json',
        ],
    ];

    $response = wp_remote_post($endpoint, [
        'headers' => ['Content-Type' => 'application/json'],
        'body' => wp_json_encode($body),
        'timeout' => 60,
    ]);

    if (is_wp_error($response)) {
        echo "   -> Error calling Gemini: " . $response->get_error_message() . "\n";
        continue;
    }

    $status_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);

    if ($status_code !== 200) {
        echo "   -> Gemini API returned {$status_code}. Rate limit? Waiting 30s...\n";
        if ($status_code == 429) {
            sleep(30);
        }
        continue;
    }

    $data = json_decode($response_body, true);
    $generated_text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

    if (empty($generated_text)) {
        echo "   -> Empty response from Gemini.\n";
        continue;
    }

    // Clean markdown
    $generated_text = trim($generated_text);
    $generated_text = preg_replace('/^```json\s*/i', '', $generated_text);
    $generated_text = preg_replace('/\s*```$/', '', $generated_text);

    // Validate JSON
    $decoded = json_decode($generated_text, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        // Save to ACF
        update_field('quiz_questions', $generated_text, 'software_category_' . $term->term_id);
        echo "   -> Saved successfully!\n";

        // Trigger auto-translation queue for this term
        if (function_exists('softmir_auto_translate_category')) {
            // Need to pass the term properly to WP hook handler
            if (!wp_next_scheduled('softmir_do_translate_term', [$term->term_id, 'software_category'])) {
                wp_schedule_single_event(time() + 2, 'softmir_do_translate_term', [$term->term_id, 'software_category']);
                echo "   -> Scheduled translation to other languages.\n";
            }
        }
    } else {
        echo "   -> Invalid JSON returned by Gemini: " . json_last_error_msg() . "\n";
    }

    // Delay to avoid hitting rates (15 RPM for free tier usually, let's wait 4s)
    sleep(4);
}

echo "\nDone!\n";
