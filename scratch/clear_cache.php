<?php
// scratch/clear_cache.php
// Production-ready cache clearing utility (supports CLI and secure Web access)

if (php_sapi_name() === 'cli') {
    if (!defined('DB_HOST')) define('DB_HOST', '127.0.0.1');
}

define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/includes/config.php';

// Secure Web Access: require admin session
if (php_sapi_name() !== 'cli') {
    require_once PATH_ROOT . '/classes/Database.php';
    require_once PATH_ROOT . '/classes/Auth.php';
    
    if (!Auth::check()) {
        header("HTTP/1.1 403 Forbidden");
        echo json_encode(["status" => "error", "message" => "Access Denied: Admin login required."]);
        exit();
    }
}

$cacheDir = defined('PATH_CACHE') ? PATH_CACHE : PATH_ROOT . '/cache';

if (!is_dir($cacheDir)) {
    if (php_sapi_name() === 'cli') {
        echo "Cache directory does not exist: $cacheDir\n";
    } else {
        header("Content-Type: application/json");
        echo json_encode(["status" => "error", "message" => "Cache directory does not exist."]);
    }
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

// Optionally clear DB flags if requested
$argv = $_SERVER['argv'] ?? [];
$clearAll = in_array('--all', $argv) || (isset($_GET['all']) && $_GET['all'] === '1');
if ($clearAll) {
    $patterns[] = 'db_upgraded_*.flag';
    $patterns[] = 'db_upgrade.lock';
}

$deletedFiles = [];
$deletedCount = 0;
foreach ($patterns as $pattern) {
    $files = glob($cacheDir . '/' . $pattern);
    if ($files) {
        foreach ($files as $file) {
            if (file_exists($file) && is_file($file)) {
                if (@unlink($file)) {
                    $deletedFiles[] = basename($file);
                    $deletedCount++;
                }
            }
        }
    }
}

if (php_sapi_name() === 'cli') {
    echo "Cleared files:\n" . implode("\n", $deletedFiles) . "\n";
    echo "Total cache files cleared: $deletedCount\n";
} else {
    header("Content-Type: application/json");
    echo json_encode([
        "status" => "success",
        "message" => "Cache cleared successfully.",
        "cleared_count" => $deletedCount,
        "cleared_files" => $deletedFiles
    ]);
}
