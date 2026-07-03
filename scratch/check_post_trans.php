<?php
// scratch/check_post_trans.php
define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/includes/config.php';
require_once PATH_ROOT . '/classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    $slug = 'european-heatwave-proves-a-treasure-trove-for-egypts-tourism-sector';
    $stmt = $db->prepare('SELECT id, slug_en, slug_ar, slug_nl, title_en, title_ar, title_nl, content_en, content_ar, content_nl FROM posts WHERE slug_en = :slug');
    $stmt->execute(['slug' => $slug]);
    $res = $stmt->fetch();
    print_r($res);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
