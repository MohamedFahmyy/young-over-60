<?php
define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/config/database.php';
require_once PATH_ROOT . '/classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "=== SLUGS OF CUSTOM PAGES ===\n";
    $stmt = $db->query("SELECT id, slug_en, slug_ar, slug_nl, is_published FROM custom_pages");
    $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($pages as $p) {
        echo "ID: {$p['id']}\n";
        echo "  Slug EN: " . json_encode($p['slug_en']) . "\n";
        echo "  Slug AR: " . json_encode($p['slug_ar']) . "\n";
        echo "  Slug NL: " . json_encode($p['slug_nl']) . "\n";
        echo "  Published: " . $p['is_published'] . "\n";
        echo "----------------------------------------\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
