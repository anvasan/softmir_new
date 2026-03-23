<?php
require_once 'wp-load.php';

$post_id = get_option('page_on_front');
if (!$post_id)
    $post_id = 95; // Hardcoded fallback

echo "Inspecting Post ID: $post_id\n";

$meta = get_post_meta($post_id, 'home_modules', true);

if (empty($meta)) {
    echo "Meta 'home_modules' is empty.\n";
}
else {
    echo "Meta 'home_modules' found. Count: " . count($meta) . "\n";
    print_r($meta);
}

// Check raw database rows for this post meta to see if there are other keys
global $wpdb;
$results = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = $post_id AND meta_key LIKE 'home_modules%'");

echo "\nRaw Meta Rows:\n";
foreach ($results as $row) {
    echo $row->meta_key . ": " . substr($row->meta_value, 0, 100) . "...\n";
}
