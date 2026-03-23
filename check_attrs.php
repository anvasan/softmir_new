<?php
require 'wp-load.php';
$attrs = get_posts(['post_type' => 'sw_attribute', 'posts_per_page' => -1, 'post_status' => 'any']);
echo "Total sw_attribute posts: " . count($attrs) . "\n";
foreach ($attrs as $a) {
    if (in_array(get_post_meta($a->ID, '_attr_card_position', true), ['middle', 'none'])) {
        echo $a->ID . " - " . $a->post_title . " | POS: " . get_post_meta($a->ID, '_attr_card_position', true) . " | TYPE: " . get_post_meta($a->ID, '_attr_type', true) . "\n";
    }
}
