<?php
define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/config/database.php';
require_once PATH_ROOT . '/classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $t) {
        $stmt = $db->query("DESCRIBE `$t`");
        $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $en_cols = [];
        $ar_cols = [];
        foreach ($cols as $col) {
            if (str_ends_with($col['Field'], '_en')) {
                $en_cols[] = $col['Field'];
            }
            if (str_ends_with($col['Field'], '_ar')) {
                $ar_cols[] = $col['Field'];
            }
        }
        if (!empty($en_cols)) {
            echo "Table `$t` has localized columns:\n";
            echo "  EN: " . implode(', ', $en_cols) . "\n";
            echo "  AR: " . implode(', ', $ar_cols) . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
