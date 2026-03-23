<?php
require_once('wp-load.php');

// Get a few software posts
$posts = get_posts([
    'post_type' => 'software',
    'numberposts' => 5,
]);

echo "Checking ratings for software posts...\n";

foreach ($posts as $p) {
    echo "Post: " . $p->post_title . " (ID: " . $p->ID . ")\n";

    if (function_exists('glsr_get_ratings')) {
        // Try method 1: post_id
        $r1 = glsr_get_ratings(['post_id' => $p->ID]);
        echo "  Method 1 (post_id): Av=" . $r1->average . ", Count=" . $r1->reviews . "\n";

        // Try method 2: assigned_posts
        $r2 = glsr_get_ratings(['assigned_posts' => $p->ID]);
        echo "  Method 2 (assigned_posts): Av=" . $r2->average . ", Count=" . $r2->reviews . "\n";
    }
    else {
        echo "  glsr_get_ratings function not found.\n";
    }
    echo "-------------------\n";
}
