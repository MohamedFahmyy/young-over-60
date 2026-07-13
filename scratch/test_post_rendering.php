<?php
define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/includes/config.php';
require_once PATH_ROOT . '/classes/Auth.php';
require_once PATH_ROOT . '/includes/helpers.php';
require_once PATH_ROOT . '/classes/Database.php';
require_once PATH_ROOT . '/classes/PostManager.php';
require_once PATH_ROOT . '/classes/SeoManager.php';

try {
    $pm = new PostManager();
    $slug = 'laughs-for-all-melbourne-international-comedy-festival-2026';
    
    // Clear cache first
    $pm->clearCache();
    echo "Cache cleared.\n";
    
    // 1. Simulate English Request
    echo "--- Simulating English Request ---\n";
    $_GET['slug'] = $slug;
    $_SERVER['REQUEST_URI'] = '/posts/' . $slug;
    if (!defined('CURRENT_LANG')) {
        define('CURRENT_LANG', 'en');
    }
    
    ob_start();
    require PATH_ROOT . '/pages/post.php';
    $htmlEn = ob_get_clean();
    echo "English Render length: " . strlen($htmlEn) . "\n";
    
    // Check if cache file exists
    $files = glob(PATH_CACHE . '/post_html_*_en_*.html');
    echo "English Cache files generated: " . count($files) . "\n";
    foreach ($files as $f) {
        echo "  - File: " . basename($f) . " (Size: " . filesize($f) . " bytes)\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
