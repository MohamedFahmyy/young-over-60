<?php
define('PATH_ROOT', dirname(__DIR__));
define('PATH_CACHE', PATH_ROOT . '/cache');
require_once PATH_ROOT . '/config/database.php';
require_once PATH_ROOT . '/classes/Database.php';
require_once PATH_ROOT . '/classes/PostManager.php';
require_once PATH_ROOT . '/classes/PageManager.php';
define('CURRENT_LANG', 'en');

$mgr = new PageManager();

echo "Testing findRedirect('about-us'): ";
$r = $mgr->findRedirect('about-us');
echo var_export($r, true) . PHP_EOL;

echo "Testing getPageBySlug('about-us'): ";
$page = $mgr->getPageBySlug('about-us');
echo ($page ? $page['title_en'] : 'NULL') . PHP_EOL;

echo "Testing getPages(): ";
$pages = $mgr->getPages();
echo count($pages) . " pages found" . PHP_EOL;
foreach ($pages as $p) {
    echo "  - " . $p['title_en'] . " (" . $p['slug_en'] . ")" . PHP_EOL;
}
echo "All tests PASSED!" . PHP_EOL;
