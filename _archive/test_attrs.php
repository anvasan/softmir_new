<?php
require_once __DIR__ . '/wp-load.php';

$attrs = get_posts(['post_type' => 'sw_attribute', 'posts_per_page' => -1]);
$key_functions = [];
foreach ($attrs as $a) {
    $pos = get_post_meta($a->ID, '_attr_card_position', true);
    $type = get_post_meta($a->ID, '_attr_type', true);
    if ($pos === 'middle' && $type === 'checkbox') {
        $key_functions[$a->ID] = $a->post_title;
    }
}
echo "Key Functions Attributes:\n";
print_r($key_functions);
