<?php
require_once 'wp-load.php';

$page_title = 'Сравнение программ';
$page_name = 'compare';
$page_check = get_page_by_path($page_name);

if (!isset($page_check->ID)) {
    $new_page_id = wp_insert_post([
        'post_title' => $page_title,
        'post_name' => $page_name,
        'post_status' => 'publish',
        'post_type' => 'page',
        'meta_input' => [
            '_wp_page_template' => 'page-compare.php'
        ]
    ]);
    if($new_page_id) { 
        echo 'Page Created: ' . $new_page_id; 
    } else {
        echo 'Failed to create page';
    }
} else {
    update_post_meta($page_check->ID, '_wp_page_template', 'page-compare.php');
    echo 'Page Exists, updated template.';
}
echo "\nDONE";
?>
