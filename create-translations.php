<?php
/**
 * Create translation stubs for all software posts and categories.
 * Creates UK and EN copies (drafts) linked via Polylang.
 * 
 * Run once: http://wp_test_anti.test/create-translations.php
 * Then DELETE this file.
 */
require_once __DIR__ . '/wp-load.php';

if (!is_user_logged_in() || !current_user_can('manage_options')) {
    wp_die('Admin access required');
}

if (!function_exists('pll_set_post_language') || !function_exists('pll_save_post_translations')) {
    wp_die('Polylang not active');
}

$default_lang = pll_default_language();
$languages = pll_languages_list();
$other_langs = array_filter($languages, fn($l) => $l !== $default_lang);

echo "<h1>Creating Translation Stubs</h1>";
echo "<p>Default language: <strong>$default_lang</strong></p>";
echo "<p>Other languages: <strong>" . implode(', ', $other_langs) . "</strong></p>";
echo "<hr>";

// ===== 1. POSTS =====
echo "<h2>📝 Software Posts</h2>";
$posts = get_posts([
    'post_type' => 'software',
    'numberposts' => -1,
    'post_status' => 'any',
    'lang' => $default_lang,
]);

$created = 0;
$skipped = 0;

foreach ($posts as $post) {
    // Get existing translations for this post
    $translations = pll_get_post_translations($post->ID);

    echo "<div style='margin:5px 0;padding:8px;background:#f5f5f5;border-radius:4px;'>";
    echo "📦 <strong>{$post->post_title}</strong> (ID: {$post->ID})<br>";

    foreach ($other_langs as $lang) {
        // Skip if translation already exists
        if (isset($translations[$lang]) && $translations[$lang] != $post->ID) {
            echo "&nbsp;&nbsp;🔗 $lang: already exists (ID: {$translations[$lang]})<br>";
            $skipped++;
            continue;
        }

        // Create a draft copy
        $new_id = wp_insert_post([
            'post_type' => $post->post_type,
            'post_status' => 'draft',
            'post_title' => $post->post_title . " [$lang]",
            'post_content' => $post->post_content,
            'post_excerpt' => $post->post_excerpt,
        ]);

        if (is_wp_error($new_id)) {
            echo "&nbsp;&nbsp;❌ $lang: ERROR - " . $new_id->get_error_message() . "<br>";
            continue;
        }

        // Set language
        pll_set_post_language($new_id, $lang);

        // Copy thumbnail
        $thumb_id = get_post_thumbnail_id($post->ID);
        if ($thumb_id) {
            set_post_thumbnail($new_id, $thumb_id);
        }

        // Copy all post meta
        $meta = get_post_meta($post->ID);
        foreach ($meta as $key => $values) {
            if (strpos($key, '_edit_') === 0 || $key === '_wp_old_slug')
                continue;
            foreach ($values as $val) {
                update_post_meta($new_id, $key, maybe_unserialize($val));
            }
        }

        // Copy taxonomy terms
        $taxonomies = get_object_taxonomies($post->post_type);
        foreach ($taxonomies as $tax) {
            if ($tax === 'language' || $tax === 'post_translations')
                continue;
            $terms = wp_get_object_terms($post->ID, $tax, ['fields' => 'ids']);
            if (!empty($terms) && !is_wp_error($terms)) {
                wp_set_object_terms($new_id, $terms, $tax);
            }
        }

        // Link translations
        $translations[$lang] = $new_id;
        $translations[$default_lang] = $post->ID;
        pll_save_post_translations($translations);

        echo "&nbsp;&nbsp;✅ $lang: created as draft (ID: $new_id)<br>";
        $created++;
    }
    echo "</div>";
}

echo "<p><strong>Posts: $created created, $skipped skipped</strong></p><hr>";

// ===== 2. TAXONOMY TERMS =====
echo "<h2>📂 Software Categories</h2>";
$terms = get_terms([
    'taxonomy' => 'software_category',
    'hide_empty' => false,
    'lang' => $default_lang,
]);

$t_created = 0;
$t_skipped = 0;

if ($terms && !is_wp_error($terms)) {
    foreach ($terms as $term) {
        $translations = pll_get_term_translations($term->term_id);

        echo "<div style='margin:5px 0;padding:8px;background:#eef;border-radius:4px;'>";
        echo "📁 <strong>{$term->name}</strong> (ID: {$term->term_id})<br>";

        foreach ($other_langs as $lang) {
            if (isset($translations[$lang]) && $translations[$lang] != $term->term_id) {
                echo "&nbsp;&nbsp;🔗 $lang: already exists (ID: {$translations[$lang]})<br>";
                $t_skipped++;
                continue;
            }

            // Create term copy
            $result = wp_insert_term(
                $term->name . " [$lang]",
                'software_category',
            [
                'description' => $term->description,
                'slug' => $term->slug . '-' . $lang,
                'parent' => $term->parent, // Will need fixing for child terms
            ]
            );

            if (is_wp_error($result)) {
                echo "&nbsp;&nbsp;❌ $lang: ERROR - " . $result->get_error_message() . "<br>";
                continue;
            }

            $new_term_id = $result['term_id'];
            pll_set_term_language($new_term_id, $lang);

            // Link translations
            $translations[$lang] = $new_term_id;
            $translations[$default_lang] = $term->term_id;
            pll_save_term_translations($translations);

            echo "&nbsp;&nbsp;✅ $lang: created (ID: $new_term_id)<br>";
            $t_created++;
        }
        echo "</div>";
    }
}

echo "<p><strong>Terms: $t_created created, $t_skipped skipped</strong></p>";

echo "<hr>";
echo "<h2>✅ Done!</h2>";
echo "<p>Now go to <strong>wp-admin → Каталог ПО</strong> and translate each draft post.</p>";
echo "<p>For categories, go to <strong>wp-admin → Каталог ПО → Категории</strong>.</p>";
echo "<p style='color:red;font-weight:bold;'>⚠️ DELETE this file after use: create-translations.php</p>";
