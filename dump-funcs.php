<?php
require 'wp-load.php';
$p = get_page_by_title('KeyCRM', OBJECT, 'software');
var_dump(get_post_meta($p->ID, '_selected_key_functions', true));
