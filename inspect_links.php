<?php
require 'wp-load.php';

echo "=== PAGES ===\n";
$pages = get_pages();
foreach ($pages as $p) {
    echo "ID: {$p->ID} | Title: {$p->post_title} | Slug: {$p->post_name}\n";
}

echo "\n=== CATEGORIES (software_category) ===\n";
$terms = get_terms(['taxonomy' => 'software_category', 'hide_empty' => false]);
if (!is_wp_error($terms)) {
    foreach ($terms as $t) {
        echo "ID: {$t->term_id} | Name: {$t->name} | Slug: {$t->slug}\n";
    }
}
else {
    echo "Error getting terms: " . $terms->get_error_message() . "\n";
}
