<?php
if (php_sapi_name() === 'cli' && !defined('DB_HOST')) {
    define('DB_HOST', '127.0.0.1');
}

define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/includes/config.php';
require_once PATH_ROOT . '/includes/helpers.php';
require_once PATH_ROOT . '/config/database.php';
require_once PATH_ROOT . '/classes/Database.php';
require_once PATH_ROOT . '/classes/Auth.php';
require_once PATH_ROOT . '/classes/PageManager.php';
require_once PATH_ROOT . '/classes/PostManager.php';
require_once PATH_ROOT . '/classes/SeoManager.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SERVER['REQUEST_URI'] = '/pages/about-us';

echo "=== RENDER TEST FOR /pages/about-us ===\n";

try {
    // 1. Mock Request state for English About Us Page
    $_GET['slug'] = 'about-us';
    $customPageSlug = 'about-us';
    
    if (!defined('CURRENT_LANG')) {
        define('CURRENT_LANG', 'en');
    }

    // Capture output of custom-page.php
    ob_start();
    require PATH_ROOT . '/pages/custom-page.php';
    $html = ob_get_clean();

    // Check if the custom styles exist in the page
    if (strpos($html, '.about-container') !== false) {
        echo " ✓ Scoped stylesheet found in rendered HTML.\n";
    } else {
        throw new Exception("FAIL: Scoped stylesheet not found in rendered HTML.");
    }

    // Check if the slogan is present
    if (strpos($html, 'Beyond 60, life begins again.') !== false) {
        echo " ✓ Slogan 'Beyond 60, life begins again.' found.\n";
    } else {
        throw new Exception("FAIL: Slogan not found.");
    }

    // Check if the titles are present
    if (strpos($html, '<h1>Young over 60</h1>') !== false) {
        echo " ✓ Main title '<h1>Young over 60</h1>' found.\n";
    } else {
        throw new Exception("FAIL: Main title not found.");
    }

    // Check if the hero section is rendered with correct background image and titles
    if (strpos($html, 'class="custom-page-hero"') !== false) {
        echo " ✓ Hero section rendered successfully.\n";
        if (strpos($html, 'australia.png') !== false) {
            echo " ✓ Hero background image (australia.png) found.\n";
        } else {
            throw new Exception("FAIL: Hero background image not found in HTML.");
        }
    } else {
        throw new Exception("FAIL: Hero section not rendered.");
    }

    // Check if the SEO meta tags work
    if (strpos($html, '<title>About Us | ') !== false || strpos($html, '<title>About Our Mission and Philosophy') !== false) {
        echo " ✓ SEO Page Title found in <head>.\n";
    } else {
        echo " ⚠️ WARNING: SEO Page Title not found in <head>.\n";
    }

    echo "\n--- FIRST 50 LINES OF RENDERED HTML ---\n";
    $lines = explode("\n", $html);
    for ($i = 0; $i < 50 && $i < count($lines); $i++) {
        echo $lines[$i] . "\n";
    }
    echo "---------------------------------------\n";

    echo " ✓ Frontend Render test passed successfully!\n";

} catch (Exception $e) {
    echo "❌ RENDER TEST FAILED: " . $e->getMessage() . "\n";
    ob_get_clean(); // clean up buffer
    exit(1);
}
