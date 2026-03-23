<?php
require_once 'wp-load.php';
$active_plugins = get_option('active_plugins');
echo "Active Plugins:\n";
foreach ($active_plugins as $plugin) {
    echo "- " . $plugin . "\n";
}

if (function_exists('acf_add_local_field_group')) {
    echo "\nACF is active and functions are available.";
}
else {
    echo "\nACF function 'acf_add_local_field_group' NOT found.";
}
