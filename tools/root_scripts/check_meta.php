<?php
require 'wp-load.php';
$m = get_post_meta(457, '_selected_key_functions', true);
var_dump($m);
