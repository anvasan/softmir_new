<?php
// Define ABSPATH if not already defined to avoid notices in wp-load
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}

require_once 'wp-load.php';

echo "=== WordPress Diagnostic ===\n";

// 1. Check Homepage Settings
$front_page_id = get_option('page_on_front');
$show_on_front = get_option('show_on_front');

echo "Front Page Option: " . $show_on_front . "\n";
echo "Front Page ID: " . $front_page_id . "\n";

if ($front_page_id) {
    $template = get_page_template_slug($front_page_id);
    echo "Homepage Template: " . ($template ? $template : 'Default') . "\n";
}
else {
    echo "No static homepage set.\n";
}

// 2. Check ACF
if (function_exists('acf_get_field_groups')) {
    echo "\n=== ACF Field Groups ===\n";
    $groups = acf_get_field_groups();
    if ($groups) {
        foreach ($groups as $group) {
            echo "- [" . $group['key'] . "] " . $group['title'] . "\n";
            echo "  Location Rules:\n";
            if (!empty($group['location'])) {
                foreach ($group['location'] as $rule_group) {
                    foreach ($rule_group as $rule) {
                        echo "    " . $rule['param'] . " " . $rule['operator'] . " " . $rule['value'] . "\n";
                    }
                }
            }
        }
    }
    else {
        echo "No field groups found.\n";
    }
}
else {
    echo "\nACF functions not available.\n";
}
