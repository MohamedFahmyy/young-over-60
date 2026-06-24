<?php
define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/config/database.php';
require_once PATH_ROOT . '/classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // 1. Get all tables
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $t) {
        $stmt = $db->query("DESCRIBE `$t`");
        $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Find all columns that end with _en
        foreach ($cols as $col) {
            $field = $col['Field'];
            $type = $col['Type'];
            $null = $col['Null'] === 'YES' ? 'NULL' : 'NOT NULL';
            $default = $col['Default'] !== null ? "DEFAULT " . $db->quote($col['Default']) : '';
            
            if (str_ends_with($field, '_en')) {
                $baseName = substr($field, 0, -3);
                $nlField = $baseName . '_nl';
                
                // Check if the _nl field already exists
                $exists = false;
                foreach ($cols as $c) {
                    if ($c['Field'] === $nlField) {
                        $exists = true;
                        break;
                    }
                }
                
                if (!$exists) {
                    $alterSql = "ALTER TABLE `$t` ADD `$nlField` $type $null $default AFTER `$field`";
                    echo "Executing: $alterSql\n";
                    $db->exec($alterSql);
                } else {
                    echo "Column `$nlField` already exists in `$t`.\n";
                }
            }
        }
    }
    echo "Database migration complete!\n";
} catch (Exception $e) {
    echo "Error during migration: " . $e->getMessage() . "\n";
}
