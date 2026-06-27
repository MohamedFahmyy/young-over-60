<?php
define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/includes/config.php';
require_once PATH_ROOT . '/classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "=== ALL POSTS ===\n";
    $stmt = $db->query("SELECT id, title_en, title_ar, title_nl, excerpt_en, excerpt_ar, excerpt_nl, coverImage FROM posts");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($posts as $post) {
        echo "ID: {$post['id']}\n";
        echo "  Title EN: " . ($post['title_en'] ?? 'NULL') . "\n";
        echo "  Title AR: " . ($post['title_ar'] ?? 'NULL') . "\n";
        echo "  Title NL: " . ($post['title_nl'] ?? 'NULL') . "\n";
        echo "  Excerpt EN: " . ($post['excerpt_en'] ?? 'NULL') . "\n";
        echo "  Excerpt AR: " . ($post['excerpt_ar'] ?? 'NULL') . "\n";
        echo "  Excerpt NL: " . ($post['excerpt_nl'] ?? 'NULL') . "\n";
        echo "  Cover Image: " . ($post['coverImage'] ?? 'NULL') . "\n";
        echo "----------------------------------------\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
