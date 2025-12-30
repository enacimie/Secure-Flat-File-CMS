<?php
// PHP Built-in Server Router
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// If the file exists physically, serve it.
if (file_exists(__DIR__ . $path) && !is_dir(__DIR__ . $path)) {
    return false;
}

// Otherwise, hand it over to index.php
require 'index.php';
