<?php
require_once 'wp-load.php';

function check_acf_pro()
{
    if (function_exists('acf_is_pro') && acf_is_pro()) {
        echo "ACF PRO: Installed and Active\n";
    }
    else {
        echo "ACF PRO: NOT Installed or Inactive\n";
    }

    if (function_exists('acf_get_field_type')) {
        $flexible_content = acf_get_field_type('flexible_content');
        if ($flexible_content) {
            echo "Flexible Content Field: Available\n";
        }
        else {
            echo "Flexible Content Field: NOT Available\n";
        }
    }
    else {
        echo "ACF Functions not found.\n";
    }
}

check_acf_pro();
