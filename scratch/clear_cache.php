<?php
// scratch/clear_cache.php
// Script to clear application cache

if (php_sapi_name() === 'cli' && !defined('DB_HOST')) {
    define('DB_HOST', '127.0.0.1');
}

define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/includes/config.php';

$cacheDir = defined('PATH_CACHE') ? PATH_CACHE : PATH_ROOT . '/cache';

if (!is_dir($cacheDir)) {
    echo "Cache directory does not exist: $cacheDir\n";
    exit(1);
}

// Patterns of files to delete
$patterns = [
    'page_*.json',
    'twl_*.json',
    'post_html_*.html',
    'story_html_*.html',
    'thumb_*',
    'styles.min.css'
];

$deletedCount = 0;
foreach ($patterns as $pattern) {
    $files = glob($cacheDir . '/' . $pattern);
    if ($files) {
        foreach ($files as $file) {
            if (file_exists($file) && is_file($file)) {
                if (@unlink($file)) {
                    echo "Cleared: " . basename($file) . "\n";
                    $deletedCount++;
                } else {
                    echo "Failed to clear: " . basename($file) . "\n";
                }
            }
        }
    }
}

echo "\nTotal cache files cleared: $deletedCount\n";
