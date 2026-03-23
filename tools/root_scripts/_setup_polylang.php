<?php
/**
 * Configure Polylang languages and translate CRM/ERP categories
 * Run: php _setup_polylang.php
 */

// Simulate admin context for Polylang
$_SERVER['HTTP_HOST'] = 'softmir.loc';
$_SERVER['REQUEST_URI'] = '/wp-admin/';
define('WP_ADMIN', true);

define('ABSPATH', 'd:/laragon/www/WP_Test_anti/');
require_once ABSPATH . 'wp-load.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';

echo "=== Polylang Setup ===" . PHP_EOL;

// Check if Polylang is active
if (!class_exists('PLL_Model')) {
    echo "ERROR: Polylang not loaded." . PHP_EOL;
    exit(1);
}

// Step 1: Check if languages already exist
$existing_langs = get_terms(['taxonomy' => 'language', 'hide_empty' => false]);
$lang_slugs = [];
if (!is_wp_error($existing_langs)) {
    foreach ($existing_langs as $lang) {
        $lang_slugs[] = $lang->slug;
    }
}
echo "Existing languages: " . (empty($lang_slugs) ? 'none' : implode(', ', $lang_slugs)) . PHP_EOL;

// Step 2: Add languages if they don't exist
global $polylang;

if (!isset($polylang) || !isset($polylang->model)) {
    echo "Polylang model not available, trying to bootstrap..." . PHP_EOL;

    // Try to access via global
    if (function_exists('PLL')) {
        $pll = PLL();
        echo "PLL() function available." . PHP_EOL;
    }
}

// Use the Polylang internal method to add languages
// Languages are stored in the 'language' taxonomy with specific term_meta
function add_pll_language($slug, $name, $locale, $flag, $term_group, $is_default = false)
{
    global $polylang;

    // Check if language already exists
    $existing = get_term_by('slug', $slug, 'language');
    if ($existing) {
        echo "  Language '{$slug}' already exists (ID: {$existing->term_id})" . PHP_EOL;
        return $existing->term_id;
    }

    // Insert language term
    $result = wp_insert_term($name, 'language', [
        'slug' => $slug,
        'description' => $locale,
    ]);

    if (is_wp_error($result)) {
        echo "  ERROR adding '{$slug}': " . $result->get_error_message() . PHP_EOL;
        return false;
    }

    $term_id = $result['term_id'];
    $tt_id = $result['term_taxonomy_id'];

    // Set the required term meta
    update_term_meta($term_id, '_pll_order', $term_group);

    // Set the locale in term description (Polylang uses mo_id, locale etc.)
    // Polylang stores locale and other data in the 'language' taxonomy description
    // Format: a:X:{s:6:"locale";s:5:"ru_RU"; ...}
    $lang_data = [
        'locale' => $locale,
        'rtl' => 0,
        'flag_code' => $flag,
        'term_group' => $term_group,
    ];

    // Polylang stores some data as description (serialized)
    // But the main data is in the pll_language post type
    // Let's create a pll_language post
    $post_id = wp_insert_post([
        'post_title' => $name,
        'post_name' => $locale,
        'post_type' => 'pll_language',
        'post_status' => 'publish',
        'post_content' => '',
    ]);

    if ($post_id && !is_wp_error($post_id)) {
        update_post_meta($post_id, '_pll_locale', $locale);
        update_post_meta($post_id, '_pll_flag', $flag);
        update_post_meta($post_id, '_pll_active', 1);

        // Link the term to the post
        wp_set_object_terms($post_id, $term_id, 'language');
    }

    echo "  Language '{$slug}' added (term_id: {$term_id}, post_id: {$post_id})" . PHP_EOL;

    if ($is_default) {
        update_option('pll_default_lang', $slug);
        echo "  Set as default language." . PHP_EOL;
    }

    return $term_id;
}

echo PHP_EOL . "--- Adding languages ---" . PHP_EOL;
add_pll_language('ru', 'Русский', 'ru_RU', 'ru', 0, true);
add_pll_language('uk', 'Українська', 'uk', 'ua', 1, false);

// Re-check
echo PHP_EOL . "--- Verifying languages ---" . PHP_EOL;
$langs = get_terms(['taxonomy' => 'language', 'hide_empty' => false]);
if (!is_wp_error($langs)) {
    foreach ($langs as $l) {
        echo "  " . $l->slug . " (" . $l->name . ") - ID: " . $l->term_id . PHP_EOL;
    }
}

// Clear Polylang cache
delete_transient('pll_languages_list');

// Now configure Polylang options
$pll_options = get_option('polylang', []);
if (empty($pll_options)) {
    $pll_options = [];
}

// Set post types and taxonomies to translate
$pll_options['post_types'] = ['software'];
$pll_options['taxonomies'] = ['software_category', 'software_attribute'];
$pll_options['media_support'] = 0;
$pll_options['default_lang'] = 'ru';
$pll_options['hide_default'] = 1;
$pll_options['force_lang'] = 1;
$pll_options['rewrite'] = 1;

update_option('polylang', $pll_options);
echo PHP_EOL . "Polylang options updated." . PHP_EOL;

echo PHP_EOL . "=== Done ===" . PHP_EOL;
echo "Please open wp-admin > Languages to verify and complete setup wizard if needed." . PHP_EOL;
