<?php
// router.php
// Routing script for PHP built-in web server to handle clean URLs and static files.

$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// If the file exists, serve it directly (handles styles, scripts, images)
if ($uri !== '/' && is_file(__DIR__ . $uri)) {
    return false;
}

// Otherwise, parse the route parameter and include index.php
$_GET['route'] = ltrim($uri, '/');
include_once __DIR__ . '/index.php';
