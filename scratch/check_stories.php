<?php
define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/includes/config.php';
require_once PATH_ROOT . '/classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT id, title_ar, slug_ar FROM women_stories");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Current stories in database:\n";
    foreach ($rows as $row) {
        echo "ID: {$row['id']} | Title (AR): {$row['title_ar']} | Slug (AR): {$row['slug_ar']}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
