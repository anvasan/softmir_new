<?php
/**
 * Check and configure Polylang languages
 */

// Load WordPress
define('WP_USE_THEMES', false);
require_once __DIR__ . '/wp-load.php';

echo "=== Polylang Status Check ===\n\n";

// Check active plugins
$plugins = get_option('active_plugins');
$polylang_active = false;
foreach ($plugins as $p) {
    if (strpos($p, 'polylang') !== false) {
        $polylang_active = true;
        echo "Polylang plugin: ACTIVE ($p)\n";
    }
}
if (!$polylang_active) {
    echo "Polylang plugin: NOT ACTIVE\n";

    // Try to activate it
    $all_plugins = array_keys(get_plugins());
    foreach ($all_plugins as $p) {
        if (strpos($p, 'polylang') !== false) {
            echo "Found Polylang at: $p\n";
            echo "Attempting to activate...\n";
            $result = activate_plugin($p);
            if (is_wp_error($result)) {
                echo "ERROR: " . $result->get_error_message() . "\n";
            }
            else {
                echo "Polylang ACTIVATED successfully!\n";
                $polylang_active = true;
            }
        }
    }
}

echo "\n";

// Check configured languages
if (function_exists('pll_languages_list')) {
    $langs = pll_languages_list(['fields' => 'slug']);
    echo "Configured languages: " . implode(', ', $langs) . "\n";
    echo "Count: " . count($langs) . "\n";
}
else {
    // Check via taxonomy directly
    $terms = get_terms(['taxonomy' => 'language', 'hide_empty' => false]);
    if (!is_wp_error($terms) && !empty($terms)) {
        echo "Languages (from taxonomy):\n";
        foreach ($terms as $t) {
            echo "  - {$t->name} ({$t->slug})\n";
        }
    }
    else {
        echo "No languages configured yet.\n";
    }
}

// Check WordPress locale
echo "\nWordPress locale: " . get_locale() . "\n";
echo "Site language: " . get_option('WPLANG', '(not set)') . "\n";
