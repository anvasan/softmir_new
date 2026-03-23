<?php
/**
 * Fix Polylang: Assign all unassigned software posts and terms to Russian language.
 * Run once: http://wp_test_anti.test/fix-polylang-posts.php
 * Then DELETE this file.
 */
require_once __DIR__ . '/wp-load.php';

if (!is_user_logged_in() || !current_user_can('manage_options')) {
    wp_die('Admin access required');
}

// Check Polylang is active
if (!function_exists('pll_default_language') || !function_exists('pll_set_post_language')) {
    wp_die('Polylang is not active');
}

$default_lang = pll_default_language(); // should be 'ru'
echo "<h2>Polylang Fix — привязка записей к языку: <strong>$default_lang</strong></h2>";

// 1. Assign all software posts to default language
$posts = get_posts([
    'post_type' => ['software', 'integrator', 'sw_attribute', 'post', 'page'],
    'numberposts' => -1,
    'post_status' => 'any',
]);

$assigned = 0;
$skipped = 0;
foreach ($posts as $post) {
    $lang = pll_get_post_language($post->ID);
    if (empty($lang)) {
        pll_set_post_language($post->ID, $default_lang);
        $assigned++;
        echo "✅ Assigned <strong>{$post->post_type}</strong>: {$post->post_title} (ID: {$post->ID}) → $default_lang<br>";
    }
    else {
        $skipped++;
    }
}

echo "<br><strong>Posts: $assigned assigned, $skipped already had language.</strong><br><hr>";

// 2. Assign all taxonomy terms to default language
$taxonomies = ['software_category', 'category', 'post_tag'];
$term_assigned = 0;
$term_skipped = 0;

foreach ($taxonomies as $tax) {
    $terms = get_terms([
        'taxonomy' => $tax,
        'hide_empty' => false,
    ]);

    if (is_wp_error($terms))
        continue;

    foreach ($terms as $term) {
        $lang = pll_get_term_language($term->term_id);
        if (empty($lang)) {
            pll_set_term_language($term->term_id, $default_lang);
            $term_assigned++;
            echo "✅ Term <strong>$tax</strong>: {$term->name} (ID: {$term->term_id}) → $default_lang<br>";
        }
        else {
            $term_skipped++;
        }
    }
}

echo "<br><strong>Terms: $term_assigned assigned, $term_skipped already had language.</strong><br>";

// 3. Show Polylang settings for post types
echo "<hr><h3>Polylang Post Types config:</h3>";
$pll_options = get_option('polylang');
if ($pll_options && isset($pll_options['post_types'])) {
    echo "Translatable post types: <strong>" . implode(', ', $pll_options['post_types']) . "</strong><br>";
}
else {
    echo "⚠️ No custom post types configured in Polylang!<br>";
    echo "Go to <strong>Languages → Settings → Custom post types and Taxonomies</strong> and enable <strong>software</strong> and <strong>software_category</strong>.<br>";
}

if ($pll_options && isset($pll_options['taxonomies'])) {
    echo "Translatable taxonomies: <strong>" . implode(', ', $pll_options['taxonomies']) . "</strong><br>";
}

echo "<hr><p style='color:red;font-weight:bold;'>⚠️ DELETE this file after running: fix-polylang-posts.php</p>";
