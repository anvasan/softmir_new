<?php
require 'wp-load.php';
$q = new WP_Query(['post_type' => 'software', 's' => 'RemOnline']);
foreach ($q->posts as $p) {
    if (function_exists('pll_get_post_language')) {
        $lang = pll_get_post_language($p->ID);
    } else {
        $lang = 'none';
    }
    $kf = get_post_meta($p->ID, '_selected_key_functions', true);
    $c = is_array($kf) ? count($kf) : 0;
    echo "ID: $p->ID | Lang: $lang | Meta: $c\n";
    if ($c > 0) {
        var_dump($kf);
    }
}
