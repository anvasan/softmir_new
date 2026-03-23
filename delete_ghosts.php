<?php
require 'wp-load.php';

global $wpdb;
// Get all sw_attribute IDs
$attrs = $wpdb->get_results("SELECT ID, post_title, post_status FROM {$wpdb->posts} WHERE post_type='sw_attribute'");
$count = 0;

foreach ($attrs as $a) {
    $pos = get_post_meta($a->ID, '_attr_card_position', true);
    $type = get_post_meta($a->ID, '_attr_type', true);

    // According to user instruction, attributes located in the middle section (checkboxes) 
    // are replaced by the new Key Functions system and must be deleted.
    if ($pos === 'middle') {
        echo "Deleting ghost attribute: $a->ID | $a->post_title | Type: $type\n";
        wp_delete_post($a->ID, true);
        $count++;
    }
}
echo "Deleted $count ghost attributes.\n";
