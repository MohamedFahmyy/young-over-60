<?php
define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/config/database.php';
require_once PATH_ROOT . '/classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "=== CATEGORIES ===\n";
    $stmt = $db->query("SELECT id, name_en, name_nl, name_ar, slug_en, slug_nl, slug_ar FROM categories");
    $cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cats as $c) {
        echo "ID: {$c['id']}\n";
        echo "  Name EN: " . json_encode($c['name_en']) . "\n";
        echo "  Name NL: " . json_encode($c['name_nl']) . "\n";
        echo "  Name AR: " . json_encode($c['name_ar']) . "\n";
        echo "  Slug EN: " . json_encode($c['slug_en']) . "\n";
        echo "  Slug NL: " . json_encode($c['slug_nl']) . "\n";
        echo "  Slug AR: " . json_encode($c['slug_ar']) . "\n";
        echo "----------------------------------------\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
