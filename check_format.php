<?php
require_once('wp-load.php');

$cat_id = 37;
$val = get_field('quiz_questions', 'software_category_' . $cat_id);
echo "Type: " . gettype($val) . "\n";
print_r($val);

$val2 = get_field('quiz_questions', 'software_category_188');
echo "Type 188: " . gettype($val2) . "\n";
print_r($val2);
