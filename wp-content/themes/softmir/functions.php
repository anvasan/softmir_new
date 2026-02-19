<?php
/**
 * SoftMir Theme Functions
 */

// ========== Content Width ==========
if (!isset($content_width)) {
    $content_width = 1280;
}

// ========== Theme Setup ==========
function softmir_setup()
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    add_theme_support('custom-logo', ['height' => 60, 'width' => 250, 'flex-height' => true, 'flex-width' => true]);
    add_theme_support('align-wide');

    register_nav_menus([
        'primary' => 'Верхнее меню (Header)',
        'footer' => 'Нижнее меню (Footer)',
    ]);
}
add_action('after_setup_theme', 'softmir_setup');

// ========== Enqueue Styles & Scripts ==========
function softmir_enqueue()
{
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap', [], null);
    wp_enqueue_style('softmir-style', get_stylesheet_uri(), ['google-fonts'], '1.2.0');

    if (is_front_page()) {
        wp_enqueue_script('popular-tabs', get_template_directory_uri() . '/js/popular-tabs.js', [], '1.0.0', true);
    }
    if (is_post_type_archive('software')) {
        wp_enqueue_script('view-switcher', get_template_directory_uri() . '/js/view-switcher.js', [], '1.0.0', true);
    }

    // Auth JS (global)
    wp_enqueue_script('softmir-auth', get_template_directory_uri() . '/js/auth.js', [], '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'softmir_enqueue');

// ========== Register CPT: Software ==========
function softmir_cpt_software()
{
    register_post_type('software', [
        'labels' => [
            'name' => 'Каталог ПО',
            'singular_name' => 'Программа',
            'add_new' => 'Добавить ПО',
            'add_new_item' => 'Добавить новое ПО',
            'edit_item' => 'Редактировать ПО',
            'all_items' => 'Все программы',
            'search_items' => 'Поиск ПО',
            'not_found' => 'Не найдено',
            'menu_name' => 'Каталог ПО',
        ],
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'software', 'with_front' => false],
        'menu_icon' => 'dashicons-grid-view',
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
        'show_in_rest' => true,
    ]);
}
add_action('init', 'softmir_cpt_software');

// ========== Register CPT: Integrator ==========
function softmir_cpt_integrator()
{
    register_post_type('integrator', [
        'labels' => [
            'name' => 'Интеграторы',
            'singular_name' => 'Интегратор',
            'add_new' => 'Добавить интегратора',
            'add_new_item' => 'Добавить нового интегратора',
            'edit_item' => 'Редактировать интегратора',
            'all_items' => 'Все интеграторы',
            'menu_name' => 'Интеграторы',
        ],
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'integrator', 'with_front' => false],
        'menu_icon' => 'dashicons-groups',
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
        'show_in_rest' => true,
    ]);
}
add_action('init', 'softmir_cpt_integrator');

// ========== Register Taxonomy: Software Category ==========
function softmir_taxonomy_software_category()
{
    register_taxonomy('software_category', 'software', [
        'labels' => [
            'name' => 'Категории ПО',
            'singular_name' => 'Категория ПО',
            'search_items' => 'Поиск категорий',
            'all_items' => 'Все категории',
            'parent_item' => 'Родительская категория',
            'edit_item' => 'Редактировать категорию',
            'add_new_item' => 'Добавить категорию',
            'menu_name' => 'Категории',
        ],
        'hierarchical' => true,
        'public' => true,
        'rewrite' => ['slug' => 'software-category'],
        'show_in_rest' => true,
        'show_admin_column' => true,
    ]);
}
add_action('init', 'softmir_taxonomy_software_category');

// ========== Move Category Meta Box to Main Column ==========
function softmir_move_category_metabox()
{
    remove_meta_box('software_categorydiv', 'software', 'side');
    add_meta_box('software_categorydiv', 'Категории ПО', 'post_categories_meta_box', 'software', 'normal', 'high', ['taxonomy' => 'software_category']);
}
add_action('add_meta_boxes', 'softmir_move_category_metabox');

// ========== ACF Field Groups ==========
function softmir_acf_fields()
{
    if (!function_exists('acf_add_local_field_group'))
        return;

    // Software Fields
    acf_add_local_field_group([
        'key' => 'group_software',
        'title' => 'Данные ПО',
        'fields' => [
            // Short Description
            ['key' => 'field_sw_short_desc', 'label' => 'Краткое описание', 'name' => 'short_description', 'type' => 'textarea', 'rows' => 3],
            // Logo
            ['key' => 'field_sw_logo', 'label' => 'Логотип компании', 'name' => 'company_logo', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium', 'instructions' => 'Рекомендуемый размер 300x150'],
            // Website
            ['key' => 'field_sw_website', 'label' => 'Сайт', 'name' => 'website_url', 'type' => 'url'],
            // Video
            ['key' => 'field_sw_video', 'label' => 'Видео (YouTube)', 'name' => 'video_url', 'type' => 'url'],
            // Screenshots (Individual fields for Free ACF)
            ['key' => 'field_sw_screen_1', 'label' => 'Скриншот 1', 'name' => 'screenshot_1', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium'],
            ['key' => 'field_sw_screen_2', 'label' => 'Скриншот 2', 'name' => 'screenshot_2', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium'],
            ['key' => 'field_sw_screen_3', 'label' => 'Скриншот 3', 'name' => 'screenshot_3', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium'],
            ['key' => 'field_sw_screen_4', 'label' => 'Скриншот 4', 'name' => 'screenshot_4', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium'],
            // Pricing (WYSIWYG)
            ['key' => 'field_sw_pricing', 'label' => 'Тарифы', 'name' => 'pricing', 'type' => 'wysiwyg', 'tabs' => 'all', 'toolbar' => 'full', 'media_upload' => 0, 'instructions' => 'Используйте таблицу или список для описания тарифов. Например: Название тарифа — Цена — Примечание.'],
            // Key Features (WYSIWYG)
            ['key' => 'field_sw_features', 'label' => 'Ключевые особенности', 'name' => 'key_features', 'type' => 'wysiwyg', 'tabs' => 'all', 'toolbar' => 'full', 'media_upload' => 0, 'instructions' => 'Перечислите ключевые особенности продукта.'],
            // Advantages (WYSIWYG)
            ['key' => 'field_sw_advantages', 'label' => 'Преимущества', 'name' => 'advantages', 'type' => 'wysiwyg', 'tabs' => 'all', 'toolbar' => 'full', 'media_upload' => 0, 'instructions' => 'Перечислите преимущества продукта.'],
            // Business Areas (WYSIWYG)
            ['key' => 'field_sw_areas', 'label' => 'Сферы бизнеса', 'name' => 'business_areas', 'type' => 'wysiwyg', 'tabs' => 'all', 'toolbar' => 'full', 'media_upload' => 0, 'instructions' => 'Перечислите сферы бизнеса, для которых подходит продукт.'],
            // Pricing Summary
            ['key' => 'field_sw_price_summary', 'label' => 'Цена (отображение в карточке)', 'name' => 'price_summary', 'type' => 'text', 'instructions' => 'Например: От $19/мес'],
            // Target Markets
            ['key' => 'field_sw_markets', 'label' => 'Целевые рынки', 'name' => 'target_markets', 'type' => 'checkbox', 'choices' => ['Россия' => 'Россия', 'СНГ' => 'СНГ', 'Европа' => 'Европа', 'США' => 'США']],
            // Flags
            ['key' => 'field_sw_featured', 'label' => 'Рекомендуемое (TOP)', 'name' => 'is_featured', 'type' => 'true_false', 'ui' => 1],
            ['key' => 'field_sw_pinned', 'label' => 'Закрепить на главной', 'name' => 'is_pinned', 'type' => 'true_false', 'ui' => 1],
        ],
        'location' => [
            [['param' => 'post_type', 'operator' => '==', 'value' => 'software']],
        ],
        'position' => 'normal',
        'style' => 'default',
    ]);

    // Integrator Fields
    acf_add_local_field_group([
        'key' => 'group_integrator',
        'title' => 'Данные интегратора',
        'fields' => [
            ['key' => 'field_int_logo', 'label' => 'Логотип', 'name' => 'integrator_logo', 'type' => 'image', 'return_format' => 'url'],
            ['key' => 'field_int_website', 'label' => 'Сайт', 'name' => 'integrator_website', 'type' => 'url'],
            ['key' => 'field_int_short_desc', 'label' => 'Краткое описание', 'name' => 'integrator_short_desc', 'type' => 'textarea', 'rows' => 3],
        ],
        'location' => [
            [['param' => 'post_type', 'operator' => '==', 'value' => 'integrator']],
        ],
    ]);


}
add_action('acf/init', 'softmir_acf_fields');

// ========== Include Attribute Helpers ==========
// require_once get_template_directory() . '/inc/acf-home.php';
require_once get_template_directory() . '/inc/shortcodes.php';
require_once get_template_directory() . '/inc/shortcodes-home.php';
require_once get_template_directory() . '/inc/acf-home-options.php';
require_once get_template_directory() . '/inc/block-patterns.php';

require_once get_template_directory() . '/inc/attributes.php';

// ========== Auth System ==========
require_once get_template_directory() . '/inc/auth.php';
require_once get_template_directory() . '/inc/smtp.php';
require_once get_template_directory() . '/inc/google-oauth.php';

// ========== Register CPT: sw_attribute ==========
function softmir_cpt_sw_attribute()
{
    register_post_type('sw_attribute', [
        'labels' => [
            'name' => 'Атрибуты ПО',
            'singular_name' => 'Атрибут',
            'add_new' => 'Добавить атрибут',
            'add_new_item' => 'Добавить новый атрибут',
            'edit_item' => 'Редактировать атрибут',
            'all_items' => 'Атрибуты',
            'search_items' => 'Поиск атрибутов',
            'not_found' => 'Атрибуты не найдены',
            'menu_name' => 'Атрибуты ПО',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => 'edit.php?post_type=software',
        'menu_icon' => 'dashicons-tag',
        'supports' => ['title'],
        'has_archive' => false,
        'rewrite' => false,
    ]);
}
add_action('init', 'softmir_cpt_sw_attribute');

// ========== Meta Box: Attribute Settings ==========
function softmir_attr_settings_meta_box()
{
    add_meta_box(
        'softmir_attr_settings',
        'Настройки атрибута',
        'softmir_attr_settings_render',
        'sw_attribute',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'softmir_attr_settings_meta_box');

function softmir_attr_settings_render($post)
{
    wp_nonce_field('softmir_attr_settings', 'softmir_attr_nonce');

    $type = get_post_meta($post->ID, '_attr_type', true) ?: 'text';
    $icon = get_post_meta($post->ID, '_attr_icon', true) ?: '';
    $filterable = get_post_meta($post->ID, '_attr_filterable', true);
    $card_pos = get_post_meta($post->ID, '_attr_card_position', true) ?: 'none';
    $page_pos = get_post_meta($post->ID, '_attr_page_position', true) ?: 'none';
    $options = get_post_meta($post->ID, '_attr_options', true) ?: '';
    $multiple = get_post_meta($post->ID, '_attr_multiple', true);
    $bound_cats = get_post_meta($post->ID, '_attr_categories', true) ?: [];

    $types = [
        'text' => 'Текст',
        'number' => 'Число',
        'url' => 'Ссылка (URL)',
        'select' => 'Выпадающий список',
        'checkbox' => 'Чекбоксы',
    ];
    $card_positions = ['none' => 'Не показывать', 'middle' => 'Средняя секция', 'footer' => 'Подвал'];
    $page_positions = ['none' => 'Не показывать', 'middle' => 'Основная колонка', 'sidebar' => 'Сайдбар'];

    echo '<table class="form-table">';

    // Type
    echo '<tr><th><label>Тип данных</label></th><td><select name="_attr_type" style="min-width:200px">';
    foreach ($types as $k => $v) {
        echo '<option value="' . esc_attr($k) . '"' . selected($type, $k, false) . '>' . esc_html($v) . '</option>';
    }
    echo '</select></td></tr>';

    // Icon
    echo '<tr><th><label>Иконка</label></th><td>';
    echo '<input type="text" name="_attr_icon" value="' . esc_attr($icon) . '" placeholder="Emoji или CSS-класс, напр. 🌐" style="min-width:200px">';
    echo '<p class="description">Emoji (🌐 💻 📊) или CSS-класс иконки</p>';
    echo '</td></tr>';

    // Filterable
    echo '<tr><th><label>Участвует в фильтрации</label></th><td>';
    echo '<label><input type="checkbox" name="_attr_filterable" value="1"' . checked($filterable, '1', false) . '> Показывать в фильтрах каталога</label>';
    echo '</td></tr>';

    // Card position
    echo '<tr><th><label>Расположение в карточке</label></th><td><select name="_attr_card_position" style="min-width:200px">';
    foreach ($card_positions as $k => $v) {
        echo '<option value="' . esc_attr($k) . '"' . selected($card_pos, $k, false) . '>' . esc_html($v) . '</option>';
    }
    echo '</select></td></tr>';

    // Page position
    echo '<tr><th><label>Расположение на странице</label></th><td><select name="_attr_page_position" style="min-width:200px">';
    foreach ($page_positions as $k => $v) {
        echo '<option value="' . esc_attr($k) . '"' . selected($page_pos, $k, false) . '>' . esc_html($v) . '</option>';
    }
    echo '</select></td></tr>';

    // Options (for select/checkbox)
    echo '<tr><th><label>Варианты</label></th><td>';
    echo '<textarea name="_attr_options" rows="3" style="width:100%;max-width:400px" placeholder="Вариант 1, Вариант 2, Вариант 3">' . esc_textarea($options) . '</textarea>';
    echo '<p class="description">Через запятую. Только для типов «Выпадающий список» и «Чекбоксы»</p>';
    echo '</td></tr>';

    // Multiple
    echo '<tr><th><label>Множественный выбор</label></th><td>';
    echo '<label><input type="checkbox" name="_attr_multiple" value="1"' . checked($multiple, '1', false) . '> Разрешить выбор нескольких значений</label>';
    echo '</td></tr>';

    // Category binding
    $all_cats = get_terms(['taxonomy' => 'software_category', 'hide_empty' => false]);
    if ($all_cats && !is_wp_error($all_cats)) {
        echo '<tr><th><label>Привязка к категориям</label></th><td>';
        echo '<fieldset style="max-height:200px;overflow-y:auto;border:1px solid #ddd;padding:8px;border-radius:4px;">';
        foreach ($all_cats as $cat) {
            $chk = is_array($bound_cats) && in_array($cat->term_id, $bound_cats) ? ' checked' : '';
            echo '<label style="display:block;margin-bottom:4px;"><input type="checkbox" name="_attr_categories[]" value="' . esc_attr($cat->term_id) . '"' . $chk . '> ' . esc_html($cat->name) . '</label>';
        }
        echo '</fieldset>';
        echo '<p class="description">Если ничего не выбрано — атрибут для всех категорий</p>';
        echo '</td></tr>';
    }

    echo '</table>';
}

// ========== Save Attribute Settings ==========
function softmir_attr_settings_save($post_id)
{
    if (!isset($_POST['softmir_attr_nonce']) || !wp_verify_nonce($_POST['softmir_attr_nonce'], 'softmir_attr_settings'))
        return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;
    if (get_post_type($post_id) !== 'sw_attribute')
        return;

    $fields = ['_attr_type', '_attr_icon', '_attr_card_position', '_attr_page_position', '_attr_options'];
    foreach ($fields as $f) {
        if (isset($_POST[$f])) {
            update_post_meta($post_id, $f, sanitize_text_field($_POST[$f]));
        }
    }
    // Checkboxes
    update_post_meta($post_id, '_attr_filterable', isset($_POST['_attr_filterable']) ? '1' : '0');
    update_post_meta($post_id, '_attr_multiple', isset($_POST['_attr_multiple']) ? '1' : '0');

    // Categories (array of term IDs)
    $cats = isset($_POST['_attr_categories']) ? array_map('intval', $_POST['_attr_categories']) : [];
    update_post_meta($post_id, '_attr_categories', $cats);
}
add_action('save_post', 'softmir_attr_settings_save');

// ========== Meta Box: Dynamic Attributes on Software Edit ==========
function softmir_software_attrs_meta_box()
{
    add_meta_box(
        'softmir_sw_attributes',
        '📋 Атрибуты ПО',
        'softmir_software_attrs_render',
        'software',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'softmir_software_attrs_meta_box');

function softmir_software_attrs_render($post)
{
    wp_nonce_field('softmir_sw_attrs', 'softmir_sw_attrs_nonce');

    $attrs = softmir_get_attributes();
    if (empty($attrs)) {
        echo '<p style="color:#888;">Атрибуты ещё не созданы. <a href="' . admin_url('edit.php?post_type=sw_attribute') . '">Создайте атрибуты</a></p>';
        return;
    }

    echo '<table class="form-table">';
    foreach ($attrs as $attr) {
        if (!softmir_attr_applies_to_software($attr->ID, $post->ID))
            continue;

        $meta = softmir_get_attr_meta($attr->ID);
        $value = softmir_get_software_attr_value($post->ID, $attr->ID);
        $field_name = '_sw_attr_' . $attr->ID;
        $icon = $meta['icon'] ? $meta['icon'] . ' ' : '';

        echo '<tr><th><label>' . esc_html($icon . $attr->post_title) . '</label></th><td>';

        switch ($meta['type']) {
            case 'checkbox':
                $options = softmir_parse_options($meta['options']);
                if (!empty($options)) {
                    $current = is_array($value) ? $value : [];
                    foreach ($options as $opt) {
                        $chk = in_array($opt, $current) ? ' checked' : '';
                        echo '<label style="display:inline-block;margin-right:12px;margin-bottom:4px;"><input type="checkbox" name="' . esc_attr($field_name) . '[]" value="' . esc_attr($opt) . '"' . $chk . '> ' . esc_html($opt) . '</label>';
                    }
                }
                else {
                    echo '<label><input type="checkbox" name="' . esc_attr($field_name) . '" value="1"' . checked($value, '1', false) . '> Да</label>';
                }
                break;

            case 'select':
                $options = softmir_parse_options($meta['options']);
                if ($meta['multiple']) {
                    $current = is_array($value) ? $value : [];
                    echo '<select name="' . esc_attr($field_name) . '[]" multiple style="min-width:250px;min-height:80px">';
                    foreach ($options as $opt) {
                        $sel = in_array($opt, $current) ? ' selected' : '';
                        echo '<option value="' . esc_attr($opt) . '"' . $sel . '>' . esc_html($opt) . '</option>';
                    }
                    echo '</select>';
                    echo '<p class="description">Зажмите Ctrl для множественного выбора</p>';
                }
                else {
                    echo '<select name="' . esc_attr($field_name) . '" style="min-width:250px">';
                    echo '<option value="">— Выберите —</option>';
                    foreach ($options as $opt) {
                        echo '<option value="' . esc_attr($opt) . '"' . selected($value, $opt, false) . '>' . esc_html($opt) . '</option>';
                    }
                    echo '</select>';
                }
                break;

            case 'url':
                echo '<input type="url" name="' . esc_attr($field_name) . '" value="' . esc_attr($value) . '" style="width:100%;max-width:400px" placeholder="https://">';
                break;

            case 'number':
                echo '<input type="number" name="' . esc_attr($field_name) . '" value="' . esc_attr($value) . '" style="min-width:150px">';
                break;

            case 'text':
            default:
                echo '<input type="text" name="' . esc_attr($field_name) . '" value="' . esc_attr($value) . '" style="width:100%;max-width:400px">';
                break;
        }

        echo '</td></tr>';
    }
    echo '</table>';
}

// ========== Save Software Attribute Values ==========
function softmir_software_attrs_save($post_id)
{
    if (!isset($_POST['softmir_sw_attrs_nonce']) || !wp_verify_nonce($_POST['softmir_sw_attrs_nonce'], 'softmir_sw_attrs'))
        return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;
    if (get_post_type($post_id) !== 'software')
        return;

    $attrs = softmir_get_attributes();
    foreach ($attrs as $attr) {
        $field_name = '_sw_attr_' . $attr->ID;
        $meta = softmir_get_attr_meta($attr->ID);

        if (isset($_POST[$field_name])) {
            $val = $_POST[$field_name];
            if (is_array($val)) {
                $val = array_map('sanitize_text_field', $val);
            }
            else {
                $val = sanitize_text_field($val);
            }
            update_post_meta($post_id, $field_name, $val);
        }
        else {
            // Checkbox unchecked or nothing selected
            if ($meta['type'] === 'checkbox') {
                $options = softmir_parse_options($meta['options']);
                if (!empty($options)) {
                    update_post_meta($post_id, $field_name, []);
                }
                else {
                    update_post_meta($post_id, $field_name, '0');
                }
            }
        }
    }
}
add_action('save_post', 'softmir_software_attrs_save');

// ========== Helper: Stars Rating HTML ==========
function softmir_stars($rating = 0, $max = 5)
{
    $output = '<span class="stars">';
    for ($i = 1; $i <= $max; $i++) {
        $output .= $i <= round($rating) ? '★' : '<span class="empty">★</span>';
    }
    $output .= '</span>';
    return $output;
}

// ========== Helper: Truncate text ==========
function softmir_truncate($text, $length = 120)
{
    if (strlen($text) <= $length)
        return $text;
    return rtrim(substr($text, 0, $length)) . '...';
}

// ========== Post View Counter ==========
function softmir_track_views()
{
    if (!is_singular('software'))
        return;
    if (is_admin())
        return;

    // Don't count bots
    if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|spider|slurp|facebookexternalhit/i', $_SERVER['HTTP_USER_AGENT']))
        return;

    $post_id = get_the_ID();
    $count = (int)get_post_meta($post_id, 'softmir_views', true);
    update_post_meta($post_id, 'softmir_views', $count + 1);
}
add_action('wp_head', 'softmir_track_views');

function softmir_get_views($post_id = null)
{
    if (!$post_id)
        $post_id = get_the_ID();
    return (int)get_post_meta($post_id, 'softmir_views', true);
}

// ========== Flush Rewrite on Activation ==========
function softmir_flush_rewrites()
{
    softmir_cpt_software();
    softmir_cpt_integrator();
    softmir_cpt_sw_attribute();
    softmir_taxonomy_software_category();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'softmir_flush_rewrites');
