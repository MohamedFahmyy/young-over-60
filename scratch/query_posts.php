<?php
define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/includes/config.php';
require_once PATH_ROOT . '/classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "=== SLUGS OF POSTS ===\n";
    $stmt = $db->query("SELECT id, slug_en, slug_ar, slug_nl, status FROM posts");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($posts as $post) {
        echo "ID: {$post['id']}\n";
        echo "  Slug EN: " . ($post['slug_en'] ?? 'NULL') . "\n";
        echo "  Slug AR: " . ($post['slug_ar'] ?? 'NULL') . "\n";
        echo "  Slug NL: " . ($post['slug_nl'] ?? 'NULL') . "\n";
        echo "  Status: " . $post['status'] . "\n";
        echo "----------------------------------------\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
