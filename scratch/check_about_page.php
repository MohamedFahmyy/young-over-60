<?php
if (php_sapi_name() === 'cli' && !defined('DB_HOST')) {
    define('DB_HOST', '127.0.0.1');
}

define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/config/database.php';
require_once PATH_ROOT . '/classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM custom_pages WHERE id = 'page-about'");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        echo "Found About page:\n";
        echo " - id: {$row['id']}\n";
        echo " - slug_en: {$row['slug_en']}\n";
        echo " - template_type: {$row['template_type']}\n";
        echo " - title_en: {$row['title_en']}\n";
        echo " - title_ar: {$row['title_ar']}\n";
        echo " - content_en length: " . strlen($row['content_en'] ?? '') . "\n";
    } else {
        echo "About page NOT found in database!\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
