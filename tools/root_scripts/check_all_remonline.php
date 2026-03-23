<?php
require 'wp-load.php';

global $wpdb;
$posts = $wpdb->get_results("SELECT ID, post_title, post_status FROM {$wpdb->posts} WHERE post_type='software' AND post_title LIKE '%RemOnline%'");

echo "Total software posts matching RemOnline: " . count($posts) . "\n";
foreach ($posts as $p) {
    if (function_exists('pll_get_post_language')) {
        $lang = pll_get_post_language($p->ID);
    } else {
        $lang = 'none';
    }
    $kf = get_post_meta($p->ID, '_selected_key_functions', true);
    $c = is_array($kf) ? count($kf) : 0;

    echo "ID: $p->ID | Lang: $lang | Status: $p->post_status | Keys Count: $c | Title: $p->post_title\n";
    if ($c > 0) {
        echo "   -> " . implode(', ', $kf) . "\n";
    }
}
