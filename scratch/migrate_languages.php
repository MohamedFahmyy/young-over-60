<?php
// scratch/migrate_languages.php
// Automated Multilingual Database Schema Synchronization Script

// Ensure PATH_ROOT is defined
if (!defined('PATH_ROOT')) {
    define('PATH_ROOT', dirname(__DIR__));
}

require_once PATH_ROOT . '/includes/config.php';
require_once PATH_ROOT . '/classes/Database.php';

// Check if running from CLI or Browser
$isCli = (php_sapi_name() === 'cli');
$lineBreak = $isCli ? "\n" : "<br>";

echo "=== Multilingual Schema Migration Started ===" . $lineBreak;

try {
    $db = Database::getInstance()->getConnection();
    
    // 1. Get all tables in the database
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // 2. Determine target languages to migrate (excluding the default language)
    $languages = defined('SUPPORTED_LANGUAGES') ? SUPPORTED_LANGUAGES : [];
    $defaultLang = defined('DEFAULT_LANG') ? DEFAULT_LANG : 'en';
    $targetLangs = array_diff(array_keys($languages), [$defaultLang]);
    
    if (empty($targetLangs)) {
        echo "No additional target languages registered besides default ('$defaultLang')." . $lineBreak;
        exit();
    }
    
    echo "Default Language: $defaultLang" . $lineBreak;
    echo "Target Languages to Synchronize: " . implode(', ', $targetLangs) . "$lineBreak$lineBreak";
    
    $migratedCount = 0;
    
    foreach ($tables as $t) {
        $stmt = $db->query("DESCRIBE `$t`");
        $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Match columns that end with `_en` (default language suffix)
        $suffix = '_' . $defaultLang;
        
        foreach ($cols as $col) {
            $field = $col['Field'];
            $type = $col['Type'];
            $null = $col['Null'] === 'YES' ? 'NULL' : 'NOT NULL';
            $default = $col['Default'] !== null ? "DEFAULT " . $db->quote($col['Default']) : '';
            
            if (str_ends_with($field, $suffix)) {
                // e.g. "title_en" -> baseName is "title"
                $baseName = substr($field, 0, -strlen($suffix));
                
                // Check all target languages
                foreach ($targetLangs as $lang) {
                    $langField = $baseName . '_' . $lang;
                    
                    // See if the language-specific column exists
                    $exists = false;
                    foreach ($cols as $c) {
                        if ($c['Field'] === $langField) {
                            $exists = true;
                            break;
                        }
                    }
                    
                    if (!$exists) {
                        $alterSql = "ALTER TABLE `$t` ADD `$langField` $type $null $default AFTER `$field`";
                        echo "[$t] Adding missing column: $langField ... ";
                        
                        try {
                            $db->exec($alterSql);
                            echo "SUCCESS" . $lineBreak;
                            $migratedCount++;
                        } catch (PDOException $ex) {
                            echo "FAILED (" . $ex->getMessage() . ")" . $lineBreak;
                        }
                    }
                }
            }
        }
    }
    
    echo $lineBreak . "=== Migration finished! $migratedCount columns added. ===" . $lineBreak;
    
} catch (Exception $e) {
    echo "Critical Error: " . $e->getMessage() . $lineBreak;
}
