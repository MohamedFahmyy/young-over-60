<?php
if (php_sapi_name() === 'cli' && !defined('DB_HOST')) {
    define('DB_HOST', '127.0.0.1');
}

define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/config/database.php';
require_once PATH_ROOT . '/classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "--- DESCRIBE custom_pages ---\n";
    $stmt = $db->query("DESCRIBE `custom_pages`");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $col) {
        echo " - {$col['Field']}: {$col['Type']} (Null: {$col['Null']})\n";
    }

    echo "\n--- DESCRIBE page_revisions ---\n";
    $stmt = $db->query("DESCRIBE `page_revisions`");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $col) {
        echo " - {$col['Field']}: {$col['Type']} (Null: {$col['Null']})\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
