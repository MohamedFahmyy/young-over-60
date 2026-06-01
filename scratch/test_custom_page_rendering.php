<?php
// scratch/test_custom_page_rendering.php
define('CURRENT_LANG', 'en');

define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/includes/config.php';
require_once PATH_ROOT . '/includes/helpers.php';
require_once PATH_ROOT . '/classes/Database.php';
require_once PATH_ROOT . '/classes/Auth.php';
require_once PATH_ROOT . '/classes/PostManager.php';
require_once PATH_ROOT . '/classes/PageManager.php';
require_once PATH_ROOT . '/classes/SeoManager.php';

$customPageSlug = 'about-us';

$pageMgr = new PageManager();
$page = $pageMgr->getPageBySlug($customPageSlug);

if (!$page) {
    echo "ERROR: Page 'about-us' not found in database!\n";
    exit(1);
}

echo "Page Title: " . $page['title_en'] . "\n";
echo "Template: " . $page['template_type'] . "\n";
echo "Content length: " . strlen($page['content_en']) . "\n";

$pageTitle      = $pageMgr->getField($page, 'title');
$pageContent    = $pageMgr->getField($page, 'content');
echo "PageTitle (translated): " . $pageTitle . "\n";
echo "PageContent (translated) length: " . strlen($pageContent) . "\n";

ob_start();
include PATH_ROOT . '/pages/custom-page.php';
$html = ob_get_clean();

echo "HTML length: " . strlen($html) . "\n";
if (strpos($html, 'Who We Are') !== false) {
    echo "✓ HTML contains 'Who We Are'!\n";
} else {
    echo "✗ HTML does not contain 'Who We Are'!\n";
}
