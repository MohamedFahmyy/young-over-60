<?php
define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/config/database.php';
require_once PATH_ROOT . '/classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    foreach (['posts', 'categories'] as $table) {
        echo "--- DESCRIBE {$table} ---\n";
        $stmt = $db->query("DESCRIBE `{$table}`");
        $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cols as $col) {
            echo " - {$col['Field']}: {$col['Type']} (Null: {$col['Null']}, Default: " . json_encode($col['Default']) . ")\n";
        }
        echo "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
