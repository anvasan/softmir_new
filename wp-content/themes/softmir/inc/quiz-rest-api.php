<?php
/**
 * SoftMir — Quiz REST API & Scout Integration
 */

if (!defined('ABSPATH')) {
    exit;
}

// ==========================================
// 1. REST API Endpoint: POST /softmir/v1/quiz-submit
// ==========================================
add_action('rest_api_init', function () {
    register_rest_route('softmir/v1', '/quiz-submit', [
        'methods' => 'POST',
        'callback' => 'softmir_rest_quiz_submit',
        'permission_callback' => '__return_true', // Публичный эндпоинт
    ]);

    register_rest_route('softmir/v1', '/quiz-classify', [
        'methods' => 'POST',
        'callback' => 'softmir_rest_quiz_classify',
        'permission_callback' => '__return_true',
    ]);
});

// ==========================================
// 1.1 REST API: Classify User Intent
// ==========================================
function softmir_rest_quiz_classify(WP_REST_Request $request)
{
    $params = $request->get_json_params() ?: $request->get_body_params();
    $intent = sanitize_textarea_field($params['intent'] ?? '');
    $lang_name = sanitize_text_field($params['lang_name'] ?? 'Russian');

    if (empty($intent)) {
        return new WP_Error('missing_intent', 'User intent is required', ['status' => 400]);
    }

    if (!function_exists('softmir_get_gemini_key')) {
        return new WP_Error('no_gemini_functions', 'Gemini functions missing');
    }

    $api_key = softmir_get_gemini_key();
    if (empty($api_key)) {
        return new WP_Error('no_api_key', 'Gemini API key is not configured');
    }

    // Получаем все категории ПО
    $terms = get_terms([
        'taxonomy' => 'software_category',
        'hide_empty' => false,
    ]);

    if (is_wp_error($terms) || empty($terms)) {
        return new WP_Error('no_categories', 'No software categories found');
    }

    $categories_list = "";
    foreach ($terms as $term) {
        $categories_list .= "- ID: {$term->term_id}, Name: {$term->name}\n";
    }

    $prompt = "Ты классификатор намерений пользователя для B2B платформы выбора ПО.\n"
        . "Пользователь описал задачу:\n\"{$intent}\"\n\n"
        . "Язык интерфейса пользователя: {$lang_name}. Не забудь учесть этот язык при анализе.\n\n"
        . "Вот точный список категорий (ID : Название):\n"
        . "{$categories_list}\n"
        . "Инструкция: выбери наиболее подходящий ID из списка выше. Если точного совпадения нет (например 'Зарплата' -> 'Бухгалтерский учет' или 'HR'), выбери самую близкую широкую категорию. Если вообще ничего не подходит, верни 0.\n"
        . "Верни ТОЛЬКО валидный JSON в формате:\n"
        . "{\n"
        . "  \"reason\": \"почему ты выбрал эту категорию, рассуждение\",\n"
        . "  \"category_id\": ID (число)\n"
        . "}";

    $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $api_key;
    $body = [
        'contents' => [
            ['parts' => [['text' => $prompt]]]
        ],
        'generationConfig' => [
            'temperature' => 0.1,
            'responseMimeType' => 'application/json',
        ]
    ];

    $response = wp_remote_post($endpoint, [
        'headers' => ['Content-Type' => 'application/json'],
        'body' => wp_json_encode($body),
        'timeout' => 15,
    ]);

    if (is_wp_error($response)) {
        return rest_ensure_response(['status' => 'success', 'category_id' => 0, 'questions' => []]);
    }

    $response_body = wp_remote_retrieve_body($response);
    $data = json_decode($response_body, true);
    $generated_text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
    $json_res = json_decode($generated_text, true);

    $cat_id = isset($json_res['category_id']) ? intval($json_res['category_id']) : 0;

    $questions = [];
    if ($cat_id > 0) {
        // Получаем вопросы из выбранной категории
        $category_questions = softmir_get_category_quiz_questions($cat_id);
        if (!empty($category_questions) && is_array($category_questions)) {
            $questions = $category_questions;

            // Проверяем, есть ли вопросы непосредственно у текущей языковой категории
            $current_cat_json = get_field('quiz_questions', 'software_category_' . $cat_id);

            // Если поле пустое, значит softmir_get_category_quiz_questions() взяла их из русской версии (fallback).
            // В этом случае переводим их на лету (запасной вариант, если крон еще не отработал)
            if (empty($current_cat_json) && $lang_name !== 'Russian' && !empty($lang_name)) {
                $questions = softmir_translate_quiz_questions($questions, $lang_name, $cat_id);
            }
        }
    }

    return rest_ensure_response([
        'status' => 'success',
        'category_id' => $cat_id,
        'questions' => $questions
    ]);
}

/**
 * Обработка сабмита квиза
 */
function softmir_rest_quiz_submit(WP_REST_Request $request)
{
    $params = $request->get_json_params() ?: $request->get_body_params();

    $category_id = intval($params['category_id'] ?? 0);
    $region = sanitize_text_field($params['region'] ?? '');
    $user_text = sanitize_textarea_field($params['user_text'] ?? '');
    $answers = rest_sanitize_array($params['answers'] ?? []);
    $lang_name = sanitize_text_field($params['lang_name'] ?? 'Russian');

    // Session ID (базово)
    $session_id = wp_generate_uuid4();

    // 1. Логируем изначальное намерение (пока без брифа)
    $log_id = softmir_log_intent([
        'session_id' => $session_id,
        'user_intent' => $user_text,
        'offered_partners' => '',
        'selected_external_id' => null,
        'is_expert_mode' => false,
        'generated_brief' => '',
    ]);

    // Категория может быть 0, если ИИ не смог классифицировать на 1-м шаге. 
    // В этом случае мы все равно идем в Скаут.

    // 2. Проверяем локальную базу
    $args = [
        'post_type' => 'software',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'tax_query' => [
            [
                'taxonomy' => 'software_category',
                'field' => 'term_id',
                'terms' => $category_id,
                'include_children' => true,
            ]
        ],
    ];

    $meta_query = ['relation' => 'AND'];

    // Учитываем регион (target_markets - ACF Checkbox, хранится сериализовано)
    if (!empty($region)) {
        $meta_query[] = [
            'key' => 'target_markets',
            'value' => '"' . $region . '"',
            'compare' => 'LIKE'
        ];
    }

    // Скрываем заблокированный софт, если не запрошена 'Россия' или 'СНГ' (Бизнес логика: геополитический маркер)
    if ($region !== 'Россия' && $region !== 'СНГ') {
        $meta_query[] = [
            'relation' => 'OR',
            ['key' => 'origin', 'compare' => 'NOT EXISTS'],
            ['key' => 'origin', 'value' => ['RU_BLOCKED', 'BY_BLOCKED'], 'compare' => 'NOT IN']
        ];
    }

    if (count($meta_query) > 1) {
        $args['meta_query'] = $meta_query;
    }

    $query = new WP_Query($args);
    $found_count = $query->found_posts;

    // 2. Формируем URL для редиректа на каталог
    // Базовый URL архива ПО (учитывая мультиязычность)
    $archive_url = get_post_type_archive_link('software') ?: home_url('/software/');

    // Добавляем параметры фильтра
    $query_params = [];
    if ($category_id) {
        $query_params['sw_cat'] = $category_id; // Предполагаемый ключ фильтра категорий
    }
    if ($region) {
        $query_params['sw_region'] = urlencode($region); // Предполагаемый ключ фильтра регионов
    }

    $redirect_url = add_query_arg($query_params, $archive_url);

    // 4. Каскадное выполнение Scout, если локально ничего не найдено
    $scout_triggered = false;
    $scouted_items_json = ''; // Для брифа

    if ($found_count === 0) {
        // Получаем названия существующего ПО в этой категории (до 50 шт), чтобы ИИ не создавал дубли
        $existing_names = [];
        if ($category_id > 0) {
            $existing_posts = get_posts([
                'post_type' => 'software',
                'posts_per_page' => 50,
                'tax_query' => [
                    [
                        'taxonomy' => 'software_category',
                        'field' => 'term_id',
                        'terms' => $category_id,
                    ]
                ],
                'post_status' => 'any',
                'fields' => 'ids'
            ]);
            foreach ($existing_posts as $p_id) {
                $existing_names[] = get_the_title($p_id);
            }
        }

        // Локально нет -> Запускаем Скаут
        $scout_result = softmir_run_scout($category_id, $region, $answers, $user_text, $lang_name, $existing_names);
        if (!is_wp_error($scout_result) && is_array($scout_result)) {
            $scout_triggered = true;
            $scouted_items_json = wp_json_encode($scout_result);
        }
    }

    // 5. Генерируем Бриф для вендора в фоновом режиме (пока пользователь ждет редирект)
    if ($log_id) {
        $term = $category_id ? get_term($category_id, 'software_category') : null;
        $category_name = ($term && !is_wp_error($term)) ? $term->name : 'Не определена';

        $brief = softmir_generate_vendor_brief($user_text, $category_name, $answers, $scouted_items_json, $lang_name);
        if (!is_wp_error($brief) && !empty($brief)) {
            global $wpdb;
            $table = $wpdb->prefix . 'softzor_intent_logs';
            $wpdb->update($table, ['generated_brief' => $brief], ['id' => $log_id]);
        }
    }

    return rest_ensure_response([
        'status' => 'success',
        'found_local' => $found_count,
        'scout_triggered' => $scout_triggered,
        'redirect_url' => $redirect_url,
    ]);
}

// ==========================================
// 2. AI Scout Engine
// ==========================================

/**
 * Запуск ИИ Скаута для поиска ПО в интернете и добавления карточек
 * 
 * @param int $category_id ID категории
 * @param string $region Целевой регион
 * @param array $answers Ответы из квиза
 * @param string $user_text Свободный пользовательский запрос (intent)
 * @param string $lang_name Язык генерации
 * @param array  $existing_software Массив названий уже существующего ПО для исключения дублей
 * @return array|WP_Error Массив добавленных элементов или Error
 */
function softmir_run_scout($category_id, $region, $answers, $user_text = '', $lang_name = 'Russian', $existing_software = [])
{
    if (!function_exists('softmir_get_gemini_key')) {
        return new WP_Error('no_gemini_functions', 'Gemini functions missing');
    }

    $api_key = softmir_get_gemini_key();
    if (empty($api_key)) {
        return new WP_Error('no_api_key', 'Gemini API key is not configured');
    }

    $term = $category_id ? get_term($category_id, 'software_category') : null;
    $category_name = ($term && !is_wp_error($term)) ? $term->name : 'Общее ПО';

    // Собираем контекст для запроса
    $context = "Category: {$category_name}\n";
    $context .= "Target Region: " . ($region ?: 'Global') . "\n";
    $context .= "Target Language: {$lang_name}\n";
    if (!empty($user_text)) {
        $context .= "User Core Task: {$user_text}\n";
    }
    if (!empty($answers)) {
        $context .= "Clarifying Answers:\n";
        foreach ($answers as $q => $a) {
            $context .= "- {$q}: {$a}\n";
        }
    }

    $exclude_text = "";
    if (!empty($existing_software)) {
        $exclude_text = "6. ИСКЛЮЧИ ИЗ ПОИСКА следующие программы (они уже есть в нашей базе): \n   " . implode(", ", $existing_software) . "\n";
    }

    // Формируем промпт для Gemini
    $prompt = "Ты B2B-эксперт по подбору программного обеспечения. "
        . "Найди 3 реально существующих, популярных и актуальных программных продукта, "
        . "которые идеально подходят под следующие требования пользователя:\n\n{$context}\n\n"
        . "Важно (СТРОГИЕ ПРАВИЛА ДЛЯ РЫНКА УКРАИНЫ):\n"
        . "1. Ищи софт ТОЛЬКО для рынка Украины. Это может быть локальный (украинский) софт или международный зарубежный продукт (США, ЕС), который официально легально работает в UA.\n"
        . "2. БЕСПОЩАДНО ИСКЛЮЧАЙ любые программы российского (RU) и белорусского (BY) происхождения. Они под санкциями и заблокированы. Если софт из РФ или РБ - не предлагай его ни при каких условиях.\n"
        . "3. Исключай софт, который не принимает оплаты картами украинских банков.\n"
        . "4. Цены (price_summary) выводи ИСКЛЮЧИТЕЛЬНО в долларах (USD), евро (EUR) или гривнах (UAH).\n"
        . "5. ВНИМАНИЕ: ВЕСЬ СГЕНЕРИРОВАННЫЙ ТЕКСТ (включая названия, описания вердикты и массивы) ДОЛЖЕН БЫТЬ СТРОГО НА ЯЗЫКЕ: {$lang_name}!\n"
        . $exclude_text
        . "Верни ТОЛЬКО валидный JSON-массив из 3 элементов. Формат каждого элемента:\n"
        . "{\n"
        . "  \"title\": \"Название ПО\",\n"
        . "  \"short_description\": \"Краткое описание (1-2 предложения)\",\n"
        . "  \"website_url\": \"Официальный сайт продукта\",\n"
        . "  \"logo_url\": \"Прямая ссылка на изображение логотипа (если уверен, иначе пустая строка)\",\n"
        . "  \"verdict\": \"Вердикт выгоды: Почему это точно подходит пользователю (1-2 предложения)\",\n"
        . "  \"price_summary\": \"Примерная цена (например, 'От $10/мес' или 'Бесплатно')\",\n"
        . "  \"origin\": \"Полное название страны происхождения на русском, например: Украина, США, Великобритания, Польша, Эстония и т.д.\",\n"
        . "  \"tech_specs\": \"Технические характеристики. Например: Платформы: Web, iOS. Языки: RU, EN. Особенности: ...\",\n"
        . "  \"scenarios\": [{\"title\": \"Заголовок\", \"desc\": \"Описание сценария (1 пред.)\", \"icon\": \"Название иконки Google Material (например chat, inventory_2)\"}],\n"
        . "  \"features\": [\"Строка 1\", \"Строка 2\"], // 3-4 ключевые функции (массив строк)\n"
        . "  \"advantages\": [\"Строка 1\"], // 3 главных преимущества (почему это ТОП)\n"
        . "  \"disadvantages\": [\"Строка 1\"], // 2-3 нюанса и риска (минусы)\n"
        . "  \"best_for\": [\"Строка 1\"], // 2-3 критерия 'Вам подойдет, если:'\n"
        . "  \"bad_for\": [\"Строка 1\"]\n // 2-3 критерия 'Лучше не брать, если:'"
        . "}\n"
        . "Твой ответ не должен содержать ничего, кроме JSON массива.";

    $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $api_key;
    $body = [
        'contents' => [
            ['parts' => [['text' => $prompt]]]
        ],
        'generationConfig' => [
            'temperature' => 0.2, // Меньше галлюцинаций
            'responseMimeType' => 'application/json',
        ]
    ];

    $response = wp_remote_post($endpoint, [
        'headers' => ['Content-Type' => 'application/json'],
        'body' => wp_json_encode($body),
        'timeout' => 60, // Увеличенный таймаут для большого объема генерации
    ]);

    if (is_wp_error($response)) {
        return $response;
    }

    $status_code = wp_remote_retrieve_response_code($response);
    if ($status_code !== 200) {
        return new WP_Error('gemini_api_error', 'Gemini API returned ' . $status_code);
    }

    $response_body = wp_remote_retrieve_body($response);
    $data = json_decode($response_body, true);
    $generated_text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

    if (empty($generated_text)) {
        return new WP_Error('empty_gemini_response', 'Gemini returned empty response');
    }

    // Очистка от маркдауна, если есть
    $generated_text = trim($generated_text);
    $generated_text = preg_replace('/^```json\s*/i', '', $generated_text);
    $generated_text = preg_replace('/\s*```$/', '', $generated_text);

    $scouted_items = json_decode($generated_text, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($scouted_items)) {
        return new WP_Error('invalid_json', 'Failed to parse Scout JSON');
    }

    $added_items = [];

    // Вспомогательная функция для загрузки логотипа
    if (!function_exists('softmir_sideload_logo')) {
        function softmir_sideload_logo($url, $post_id)
        {
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            if (empty($url)) {
                // Пытаемся взять лого через Clearbit как фоллбэк
                $domain = parse_url(get_field('website_url', $post_id), PHP_URL_HOST);
                if ($domain) {
                    $url = "https://logo.clearbit.com/" . $domain;
                }
            }

            if (!empty($url)) {
                $attach_id = media_sideload_image($url, $post_id, null, 'id');
                if (!is_wp_error($attach_id)) {
                    update_field('company_logo', $attach_id, $post_id);
                }
            }
        }
    }

    foreach ($scouted_items as $item) {
        if (empty($item['title']))
            continue;

        $added_items[] = $item['title']; // Сохраняем названия для брифа

        // Избегаем дубликатов по заголовку
        $existing = get_page_by_title($item['title'], OBJECT, 'software');
        if ($existing)
            continue;

        // Создаем пост
        $post_id = wp_insert_post([
            'post_type' => 'software',
            'post_title' => sanitize_text_field($item['title']),
            'post_status' => 'publish',
            'post_author' => 1,
        ]);

        if (is_wp_error($post_id) || !$post_id)
            continue;

        // Явно привязываем пост к языку, на котором был запущен квиз
        if (function_exists('pll_set_post_language') && function_exists('pll_current_language')) {
            $curr_lang = pll_current_language() ?: pll_default_language();
            pll_set_post_language($post_id, $curr_lang);
        }

        // Привязываем категорию
        if ($category_id > 0) {
            wp_set_post_terms($post_id, [$category_id], 'software_category');
            update_field('primary_category', $category_id, $post_id);
        }

        // Устанавливаем ACF поля (Старые и новые)
        update_post_meta($post_id, 'software_status', 'external_scout');
        update_field('short_description', sanitize_textarea_field($item['short_description'] ?? ''), $post_id);
        update_field('website_url', esc_url_raw($item['website_url'] ?? ''), $post_id);
        update_field('verdict', sanitize_textarea_field($item['verdict'] ?? ''), $post_id);
        update_field('price_summary', sanitize_text_field($item['price_summary'] ?? ''), $post_id);
        update_field('origin', sanitize_text_field($item['origin'] ?? ''), $post_id);

        // Новые глубокие текстовые поля (ACF Text/Textarea)
        if (!empty($item['scenarios']) && is_array($item['scenarios'])) {
            $i = 1;
            foreach ($item['scenarios'] as $sc) {
                if ($i > 3)
                    break;
                update_field("scenario_{$i}_title", sanitize_text_field($sc['title'] ?? ''), $post_id);
                update_field("scenario_{$i}_desc", sanitize_textarea_field($sc['desc'] ?? ''), $post_id);
                update_field("scenario_{$i}_icon", sanitize_text_field($sc['icon'] ?? ''), $post_id);
                $i++;
            }
        }

        if (!empty($item['features']) && is_array($item['features'])) {
            $features_html = '<ul>';
            foreach ($item['features'] as $f) {
                $features_html .= '<li>' . esc_html($f) . '</li>';
            }
            $features_html .= '</ul>';
            update_field('field_sw_features', wp_kses_post($features_html), $post_id);
        }

        if (!empty($item['top_reasons']) && is_array($item['top_reasons'])) {
            update_field('top_reasons', implode("\n", array_map('sanitize_text_field', $item['top_reasons'])), $post_id);
        } elseif (!empty($item['advantages']) && is_array($item['advantages'])) {
            update_field('top_reasons', implode("\n", array_map('sanitize_text_field', $item['advantages'])), $post_id);
        }

        if (!empty($item['disadvantages']) && is_array($item['disadvantages'])) {
            update_field('disadvantages', implode("\n", array_map('sanitize_text_field', $item['disadvantages'])), $post_id);
        }

        if (!empty($item['best_for']) && is_array($item['best_for'])) {
            update_field('best_for', implode("\n", array_map('sanitize_text_field', $item['best_for'])), $post_id);
        }

        if (!empty($item['bad_for']) && is_array($item['bad_for'])) {
            update_field('bad_for', implode("\n", array_map('sanitize_text_field', $item['bad_for'])), $post_id);
        }

        update_field('tech_specs', sanitize_textarea_field($item['tech_specs'] ?? ''), $post_id);

        // Парсим логотип
        softmir_sideload_logo($item['logo_url'] ?? '', $post_id);

        // Рынок
        if (!empty($region)) {
            update_field('target_markets', [$region], $post_id);
        }

        // Запускаем асинхронный перевод карточки (через 5 секунд, чтобы не тормозить квиз)
        wp_schedule_single_event(time() + 5, 'softmir_async_translate_scout_cards', [$post_id]);
    }

    return $added_items;
}

// ==========================================
// 3. Vendor Brief Generation
// ==========================================

/**
 * Генерирует бриф для вендора с помощью Gemini
 */
function softmir_generate_vendor_brief($user_text, $category_name, $answers, $scouted_items_json, $lang_name = 'Russian')
{
    if (!function_exists('softmir_get_gemini_key')) {
        return false;
    }

    $api_key = softmir_get_gemini_key();
    if (empty($api_key))
        return false;

    // Подготовка данных
    $answers_text = "";
    if (!empty($answers)) {
        foreach ($answers as $q => $a) {
            $answers_text .= "- {$q}: {$a}\n";
        }
    } else {
        $answers_text = "Дополнительных вопросов/ответов не было.\n";
    }

    $scout_text = "Были локальные результаты, Скаут не использовался.";
    if (!empty($scouted_items_json)) {
        $scout_text = "Скаут подобрал следующие аналоги: " . $scouted_items_json;
    }

    $prompt = "Сгенерируй бизнес-бриф для вендора программного обеспечения по следующей структуре в формате Markdown:\n\n"
        . "1. Профиль клиента и контекст (Client Intent)\n"
        . "- Суть запроса: Краткое резюме проблемы пользователя (переведенное на деловой язык).\n"
        . "- Сегмент бизнеса: (На основе категории: {$category_name}).\n"
        . "- Режим подбора: Business или Deep Tech (определи по запросу).\n\n"
        . "2. Технические и функциональные требования (Requirements)\n"
        . "- Критические функции: Что было указано как важное.\n"
        . "- Техпаспорт ожидания: Специфика (лимиты, методы и т.д.).\n"
        . "- Геополитический фильтр: Безопасное ПО (не из РФ/РБ).\n\n"
        . "3. Анализ готовности и окружения (AI Sandbox)\n"
        . "- Сценарии использования: 2-3 бизнес-процесса.\n"
        . "- Модель интеграций: С чем вероятно нужно связать софт.\n"
        . "- Потребность во внедрении: Нужен ли интегратор.\n\n"
        . "4. Конкурентный контекст (Market Context)\n"
        . "- Рассматриваемые аналоги: Аналоги, которыми интересовался пользователь.\n"
        . "- Сравнение: Примечания о выборе.\n\n"
        . "Исходные данные от клиента:\n"
        . "Свободный запрос: {$user_text}\n"
        . "Ответы в квизе:\n{$answers_text}\n"
        . "Данные Скаута: {$scout_text}\n\n"
        . "ВАЖНО: Итоговый текст брифа должен быть написан исключительно на языке: {$lang_name}.\n\n"
        . "Верни только сгенерированный бриф с Markdown-разметкой.";

    $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-pro:generateContent?key=' . $api_key;
    $body = [
        'contents' => [
            ['parts' => [['text' => $prompt]]]
        ],
        'generationConfig' => [
            'temperature' => 0.4,
        ]
    ];

    $response = wp_remote_post($endpoint, [
        'headers' => ['Content-Type' => 'application/json'],
        'body' => wp_json_encode($body),
        'timeout' => 30, // Генерация длинного текста
    ]);

    if (is_wp_error($response)) {
        return false;
    }

    $status_code = wp_remote_retrieve_response_code($response);
    if ($status_code !== 200) {
        return false;
    }

    $response_body = wp_remote_retrieve_body($response);
    $data = json_decode($response_body, true);
    $generated_text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

    return trim($generated_text);
}
