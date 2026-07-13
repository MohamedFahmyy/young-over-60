<?php
define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/includes/config.php';
require_once PATH_ROOT . '/classes/Database.php';
require_once PATH_ROOT . '/classes/PostManager.php';

$pm = new PostManager();
$res = $pm->getPosts(['limit' => 1]);
if (!empty($res['data'])) {
    echo "Keys:\n";
    print_r(array_keys($res['data'][0]));
    echo "\nSample post slug values:\n";
    echo "  slug_en: " . $res['data'][0]['slug_en'] . "\n";
    echo "  slug_nl: " . $res['data'][0]['slug_nl'] . "\n";
    echo "  slug_ar: " . $res['data'][0]['slug_ar'] . "\n";
} else {
    echo "No posts found.\n";
}
