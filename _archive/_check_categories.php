<?php
require_once __DIR__ . '/wp-load.php';

echo "=== SoftZor Category Structure ===\n\n";

$terms = get_terms([
    'taxonomy' => 'software_category',
    'hide_empty' => false,
    'orderby' => 'name',
]);

if (is_wp_error($terms)) {
    echo "Error: " . $terms->get_error_message() . "\n";
    exit;
}

// Build tree
$parents = [];
$children = [];
foreach ($terms as $term) {
    if ($term->parent === 0) {
        $parents[$term->term_id] = $term;
    } else {
        $children[$term->parent][] = $term;
    }
}

foreach ($parents as $pid => $parent) {
    $quiz = get_field('quiz_questions', 'software_category_' . $pid);
    $count = $parent->count;
    echo "📁 [{$pid}] {$parent->name} (slug: {$parent->slug}, posts: {$count})";
    if ($quiz)
        echo " [HAS QUIZ]";
    echo "\n";

    if (isset($children[$pid])) {
        foreach ($children[$pid] as $child) {
            $cquiz = get_field('quiz_questions', 'software_category_' . $child->term_id);
            $ccount = $child->count;
            echo "   └─ [{$child->term_id}] {$child->name} (slug: {$child->slug}, posts: {$ccount})";
            if ($cquiz)
                echo " [HAS QUIZ]";
            echo "\n";
        }
    }
    echo "\n";
}

echo "Total categories: " . count($terms) . "\n";
echo "Parent categories: " . count($parents) . "\n";
echo "Child categories: " . array_sum(array_map('count', $children)) . "\n";
