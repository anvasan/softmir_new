<?php
require 'wp-load.php';

$out = "";
$selected = get_post_meta(457, '_selected_key_functions', true);
$out .= "=== SELECTED FUNCTIONS FOR POST 457 ===\n";
$out .= print_r($selected, true);

$primary_cat_id = get_post_meta(457, 'primary_category', true);
if (!$primary_cat_id) {
    $terms = get_the_terms(457, 'software_category');
    if ($terms && !is_wp_error($terms)) {
        $primary_cat_id = $terms[0]->term_id;
    }
}

$available = softmir_get_all_key_functions_for_category($primary_cat_id);
$out .= "\n=== AVAILABLE FUNCTIONS FOR CAT $primary_cat_id ===\n";
$out .= print_r($available, true);

foreach ($available as $func) {
    if (is_array($selected) && in_array($func, $selected)) {
        $out .= "\nMATCH: $func";
    } else {
        $out .= "\nNO MATCH: $func";
    }
}
file_put_contents('match_debug.txt', $out);
