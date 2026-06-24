<?php
define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/config/database.php';
require_once PATH_ROOT . '/classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "--- TABLES ---\n";
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $t) {
        echo "Table: {$t}\n";
    }

    echo "--- DESCRIBE women_stories ---\n";
    $stmt = $db->query("DESCRIBE `women_stories`");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $col) {
        echo " - {$col['Field']}: {$col['Type']}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
