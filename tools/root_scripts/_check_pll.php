<?php
/**
 * List all software categories and translate CRM/ERP ones to Ukrainian
 */
$_SERVER['HTTP_HOST'] = 'softmir.loc';
$_SERVER['REQUEST_URI'] = '/wp-admin/';
define('WP_ADMIN', true);
define('ABSPATH', 'd:/laragon/www/WP_Test_anti/');
require_once ABSPATH . 'wp-load.php';

// Step 1: List all top-level categories
echo "=== All Software Categories (top-level) ===" . PHP_EOL;
$all_cats = get_terms([
    'taxonomy' => 'software_category',
    'hide_empty' => false,
    'parent' => 0,
    'number' => 50,
]);

if (is_wp_error($all_cats)) {
    echo "ERROR: " . $all_cats->get_error_message() . PHP_EOL;
    exit(1);
}

foreach ($all_cats as $cat) {
    $lang = pll_get_term_language($cat->term_id);
    $children = get_terms([
        'taxonomy' => 'software_category',
        'hide_empty' => false,
        'parent' => $cat->term_id,
    ]);
    $child_count = is_array($children) ? count($children) : 0;
    echo "[{$cat->term_id}] {$cat->name} (slug: {$cat->slug}, lang: " . ($lang ?: 'none') . ", children: {$child_count})" . PHP_EOL;
}

// Step 2: Search specifically for CRM and ERP in all categories (not just top-level)
echo PHP_EOL . "=== Searching for CRM/ERP in all categories ===" . PHP_EOL;
$all = get_terms([
    'taxonomy' => 'software_category',
    'hide_empty' => false,
    'number' => 200,
]);

if (!is_wp_error($all)) {
    foreach ($all as $cat) {
        if (stripos($cat->name, 'CRM') !== false || stripos($cat->name, 'ERP') !== false) {
            $parent_name = '';
            if ($cat->parent > 0) {
                $parent = get_term($cat->parent, 'software_category');
                $parent_name = $parent ? " (parent: {$parent->name})" : '';
            }
            echo "[{$cat->term_id}] {$cat->name}{$parent_name}" . PHP_EOL;
        }
    }
}

echo PHP_EOL . "Total categories: " . count($all) . PHP_EOL;
