<?php
// Set up some server variables to simulate a request
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

define('WP_USE_THEMES', true);
require('wp-blog-header.php');
