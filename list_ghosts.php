<?php
require 'wp-load.php';

global $wpdb;
$attrs = $wpdb->get_results("SELECT ID, post_title, post_status FROM {$wpdb->posts} WHERE post_type='sw_attribute'");
echo "Total raw sw_attribute posts: " . count($attrs) . "\n";
foreach ($attrs as $a) {
    $pos = get_post_meta($a->ID, '_attr_card_position', true);
    $type = get_post_meta($a->ID, '_attr_type', true);
    if ($pos === 'middle') {
        echo "GHOST: $a->ID | $a->post_title | Type: $type\n";
    }
}
