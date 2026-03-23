<?php
require 'wp-load.php';
$id = 11640;
$p = get_post($id);
if (!$p)
    die("Post $id not found");
echo "Post Title: " . $p->post_title . "\n";
echo "Post ID: " . $p->ID . "\n";

echo "\n--- SIDEBAR ATTRS ---\n";
echo softmir_render_attrs_block($p->ID, '_attr_page_position', 'sidebar', '');

echo "\n\n--- MIDDLE ATTRS ---\n";
echo softmir_render_attrs_block($p->ID, '_attr_page_position', 'middle', 'bg-white p-6 rounded-xl border border-slate-200 shadow-sm mt-8');
