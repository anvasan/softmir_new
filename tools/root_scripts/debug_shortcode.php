<?php
require 'wp-load.php';

$slug = 'crm';
if (isset($argv[1])) {
    $slug = $argv[1];
}

echo "Testing Query for category slug: '$slug'\n";

// 1. Check if term exists
$term = get_term_by('slug', $slug, 'software_category');
if ($term) {
    echo "Term Found: ID={$term->term_id}, Name={$term->name}, Count={$term->count}\n";
}
else {
    echo "Term '$slug' NOT FOUND in 'software_category'.\n";

    // List available terms to be helpful
    echo "Available terms:\n";
    $terms = get_terms(['taxonomy' => 'software_category', 'hide_empty' => false]);
    foreach ($terms as $t) {
        echo "- {$t->name} (slug: {$t->slug}, count: {$t->count})\n";
    }
}

// 2. Run Query
$args = [
    'post_type' => 'software',
    'posts_per_page' => 6,
    'tax_query' => [
        [
            'taxonomy' => 'software_category',
            'field' => 'slug',
            'terms' => $slug
        ]
    ]
];

$q = new WP_Query($args);
echo "Query Found: " . $q->found_posts . " posts.\n";

if ($q->have_posts()) {
    while ($q->have_posts()) {
        $q->the_post();
        echo "- [" . get_the_ID() . "] " . get_the_title() . "\n";
        $meta = get_post_meta(get_the_ID());
        // Filter out implementation details if needed, but for debug dump all
        // echo "All Meta keys: " . implode(", ", array_keys($meta)) . "\n";
        foreach ($meta as $k => $v) {
            echo "  Meta Key: '$k' => " . print_r($v[0], true) . "\n";
        }
        break; // Only first post needed
    }
}
else {
    echo "No posts found.\n";
}
if (function_exists('softmir_stars')) {
    echo "Function 'softmir_stars' EXISTS.\n";
}
else {
    echo "Function 'softmir_stars' DOES NOT EXIST.\n";
}
