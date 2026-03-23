<?php
require 'wp-load.php';
$p1 = get_post(457);
$p2 = get_post(449);
echo "457 status: {$p1->post_status}, date: {$p1->post_date}\n";
echo "449 status: {$p2->post_status}, date: {$p2->post_date}\n";
