<?php
require 'wp-load.php';

$p1 = get_post(457);
$p2 = get_post(449);

echo "POST 457: \n";
echo "Title: " . $p1->post_title . "\n";
echo "Functions: " . count((array) get_post_meta(457, '_selected_key_functions', true)) . "\n\n";

echo "POST 449: \n";
echo "Title: " . $p2->post_title . "\n";
echo "Functions: " . count((array) get_post_meta(449, '_selected_key_functions', true)) . "\n";
