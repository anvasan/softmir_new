<?php
// Load WordPress environment
require_once('wp-load.php');

// Ensure functions.php is loaded (it should be via wp-load, but themes might be late?)
// Actually wp-load loads everything.

// Manually include the definition just in case
require_once get_stylesheet_directory() . '/inc/acf-home.php';
if (function_exists('softmir_register_home_flexible')) {
    softmir_register_home_flexible();
}

$field_key = 'field_home_modules';
$field_obj = acf_get_field($field_key);

echo "Field Object Check:\n";
if (!$field_obj) {
    echo "ERROR: Field '$field_key' NOT FOUND.\n";
    exit;
}
else {
    echo "SUCCESS: Field '$field_key' found. Type: " . $field_obj['type'] . "\n";
}

// Create a test page
$post_id = wp_insert_post([
    'post_title' => 'Test ACF Save',
    'post_type' => 'page',
    'post_status' => 'publish'
]);

echo "Created Test Post ID: $post_id\n";

// Test Data - Minimal
$data = [
    [
        'acf_fc_layout' => 'hero',
        'field_hero_title' => 'Test Title', // Using KEY
    ]
];

// Try Update
if (function_exists('update_field')) {
    $result = update_field($field_key, $data, $post_id);
    echo "update_field result: " . ($result ? 'TRUE' : 'FALSE') . "\n";
}
else {
    echo "ACF update_field function missing.\n";
}

// Check Meta
$meta = get_post_meta($post_id);
echo "Meta dump:\n";
foreach ($meta as $k => $v) {
    if (strpos($k, 'home_modules') !== false) {
        echo "[$k] => " . print_r($v[0], true) . "\n";
    }
}

// Check specifically for expansion
if (isset($meta['home_modules_0_field_hero_title'])) {
// Wait, if expanded, it should be mapped to name? 
// No, standard ACF stores: 'home_modules_0_title' usually?
// Let's see what it stored.
}

// Clean up
wp_delete_post($post_id, true);
