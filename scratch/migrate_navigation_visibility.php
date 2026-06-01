<?php
// scratch/migrate_navigation_visibility.php
// Safe, idempotent migration to add navigation visibility toggles to site_settings table.
// Includes database backup and rollback support.

// 1. Windows CLI Connection Override
if (php_sapi_name() === 'cli' && !defined('DB_HOST')) {
    define('DB_HOST', '127.0.0.1');
}

define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/includes/config.php';
require_once PATH_ROOT . '/classes/Database.php';

// Check if rollback is requested
$isRollback = isset($argv[1]) && $argv[1] === '--rollback';

try {
    $db = Database::getInstance()->getConnection();
    echo "Connected to the database successfully.\n";

    // Define navigation visibility columns to add
    $columns = [
        'nav_show_podcasts'      => "TINYINT(1) NOT NULL DEFAULT 1",
        'nav_show_stories'       => "TINYINT(1) NOT NULL DEFAULT 1",
        'nav_show_news'          => "TINYINT(1) NOT NULL DEFAULT 1",
        'nav_show_accessibility' => "TINYINT(1) NOT NULL DEFAULT 1",
        'nav_show_contact'       => "TINYINT(1) NOT NULL DEFAULT 1"
    ];

    if ($isRollback) {
        echo "Starting rollback operation...\n";
        
        // Check existing columns
        $stmt = $db->query("DESCRIBE `site_settings`");
        $existing = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach (array_keys($columns) as $name) {
            if (in_array($name, $existing)) {
                $db->exec("ALTER TABLE `site_settings` DROP COLUMN `{$name}`");
                echo "Dropped column `{$name}` from `site_settings`.\n";
            } else {
                echo "Column `{$name}` does not exist in `site_settings`.\n";
            }
        }
        echo "Rollback completed successfully!\n";
        exit(0);
    }

    // --- 2. Database Backup Phase ---
    echo "Generating database backup...\n";
    $timestamp = date('Ymd_His');
    $backupFile = PATH_ROOT . "/scratch/backup_u402417573_travelfinal_{$timestamp}.sql";
    
    $sqlDump = "-- Travel Without Limits Database Backup\n";
    $sqlDump .= "-- Generated on " . date('Y-m-d H:i:s') . "\n\n";
    $sqlDump .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

    // Fetch all tables
    $tablesQuery = $db->query("SHOW TABLES");
    $tables = $tablesQuery->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tables as $table) {
        $sqlDump .= "-- -----------------------------------------------------\n";
        $sqlDump .= "-- Table structure for table `{$table}`\n";
        $sqlDump .= "-- -----------------------------------------------------\n";
        $sqlDump .= "DROP TABLE IF EXISTS `{$table}`;\n";
        
        // Get create table statement
        $createStmt = $db->query("SHOW CREATE TABLE `{$table}`");
        $createRow = $createStmt->fetch(PDO::FETCH_ASSOC);
        $sqlDump .= $createRow['Create Table'] . ";\n\n";
        
        // Get data from table
        $sqlDump .= "-- Dumping data for table `{$table}`\n";
        $dataStmt = $db->query("SELECT * FROM `{$table}`");
        $rows = $dataStmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($rows)) {
            $insertPrefix = "INSERT INTO `{$table}` (";
            $columnsList = array_keys($rows[0]);
            $insertPrefix .= implode(', ', array_map(function($c) { return "`{$c}`"; }, $columnsList));
            $insertPrefix .= ") VALUES ";
            
            $valuesList = [];
            foreach ($rows as $row) {
                $rowValues = [];
                foreach ($columnsList as $col) {
                    $val = $row[$col];
                    if ($val === null) {
                        $rowValues[] = 'NULL';
                    } else {
                        $rowValues[] = $db->quote($val);
                    }
                }
                $valuesList[] = "(" . implode(', ', $rowValues) . ")";
            }
            
            $sqlDump .= $insertPrefix . "\n" . implode(",\n", $valuesList) . ";\n";
        }
        $sqlDump .= "\n";
    }

    $sqlDump .= "SET FOREIGN_KEY_CHECKS = 1;\n";
    
    file_put_contents($backupFile, $sqlDump);
    echo "Backup file written to: " . basename($backupFile) . " (" . number_format(filesize($backupFile)) . " bytes)\n";

    // --- 3. Run Migration (idempotently add columns) ---
    echo "Running migration script...\n";
    
    // Check existing columns
    $stmt = $db->query("DESCRIBE `site_settings`");
    $existing = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($columns as $name => $definition) {
        if (!in_array($name, $existing)) {
            $db->exec("ALTER TABLE `site_settings` ADD COLUMN `{$name}` {$definition}");
            echo "Added column `{$name}` to `site_settings`.\n";
        } else {
            echo "Column `{$name}` already exists in `site_settings`.\n";
        }
    }

    echo "Migration completed successfully!\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
