<?php
define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/includes/config.php';
require_once PATH_ROOT . '/classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "=== ALL WOMEN STORIES ===\n";
    $stmt = $db->query("SELECT id, title_en, title_ar, title_nl, excerpt_en, excerpt_ar, excerpt_nl, cover_image FROM women_stories");
    $stories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($stories as $story) {
        echo "ID: {$story['id']}\n";
        echo "  Title EN: " . ($story['title_en'] ?? 'NULL') . "\n";
        echo "  Title AR: " . ($story['title_ar'] ?? 'NULL') . "\n";
        echo "  Title NL: " . ($story['title_nl'] ?? 'NULL') . "\n";
        echo "  Excerpt EN: " . ($story['excerpt_en'] ?? 'NULL') . "\n";
        echo "  Excerpt AR: " . ($story['excerpt_ar'] ?? 'NULL') . "\n";
        echo "  Excerpt NL: " . ($story['excerpt_nl'] ?? 'NULL') . "\n";
        echo "  Cover Image: " . ($story['cover_image'] ?? 'NULL') . "\n";
        echo "----------------------------------------\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
