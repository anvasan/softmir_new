<?php
require_once 'wp-load.php';

$term_id = 90; // Из URL скриншота

echo "Testing get_field('quiz_questions', 'software_category_90'):\n";
$val1 = get_field('quiz_questions', 'software_category_' . $term_id);
var_dump($val1);

echo "\nTesting get_field('quiz_questions', 'term_90'):\n";
$val2 = get_field('quiz_questions', 'term_' . $term_id);
var_dump($val2);

echo "\nRaw Term Meta for quiz_questions:\n";
$raw = get_term_meta($term_id, 'quiz_questions', true);
var_dump($raw);

if (!empty($val1)) {
    echo "\nJSON Decode test:\n";
    $decoded = json_decode($val1, true);
    var_dump($decoded);
    echo "JSON Error: " . json_last_error_msg() . "\n";
}
