<?php
require 'wp-load.php';

global $wpdb;
$attrs = $wpdb->get_results("SELECT ID, post_title, post_status FROM {$wpdb->posts} WHERE post_type='sw_attribute'");
echo "Total raw sw_attribute posts: " . count($attrs) . "\n";
foreach ($attrs as $a) {
    $pos = get_post_meta($a->ID, '_attr_card_position', true);
    if ($pos === 'middle') {
        echo "GHOST ATTR (middle): $a->ID - $a->post_title\n";
    }
}
