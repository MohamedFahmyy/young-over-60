<?php
/**
 * deploy_dutch_localization.php
 * 
 * Production Deployment & Migration Script for Dutch Localization
 * Run via CLI: php deploy_dutch_localization.php [--dry-run] [--prod]
 * 
 * ====================================================================
 * PRODUCTION DEPLOYMENT PROCESS
 * ====================================================================
 * 1. Run in Dry-Run mode to review planned changes:
 *    php deploy_dutch_localization.php --dry-run
 * 
 * 2. Execute the migration (creates automatic backup & applies changes):
 *    php deploy_dutch_localization.php
 * 
 * 3. Review the printed deployment report and logs/deploy_dutch_localization.log.
 * 
 * 4. Verify Dutch frontend and admin pages.
 * 
 * ====================================================================
 * ROLLBACK PROCEDURE (If anything goes wrong)
 * ====================================================================
 * 1. Restore the generated SQL backup located in:
 *    /backups/pre_dutch_localization_YYYYMMDD_HHMMSS.sql
 *    Example CLI command:
 *    mysql -u [user] -p[pass] [dbname] < backups/pre_dutch_localization_YYYYMMDD_HHMMSS.sql
 * 
 * 2. Revert code changes in git:
 *    git checkout HEAD -- deploy_dutch_localization.php
 * 
 * 3. Clear caches (using Admin Dashboard or manually deleting files in /cache/).
 * 
 * 4. Verify localization and site integrity.
 */

define('PATH_ROOT', __DIR__);

$startTime = microtime(true);
$startDateTime = date('Y-m-d H:i:s');

$isCli = (php_sapi_name() === 'cli');
$lineBreak = $isCli ? "\n" : "<br>";
$argv = $_SERVER['argv'] ?? [];
$isDryRun = in_array('--dry-run', $argv);
$isProdCli = in_array('--prod', $argv);

$logsDir = PATH_ROOT . '/logs';
$backupsDir = PATH_ROOT . '/backups';
$locksDir = PATH_ROOT . '/storage/locks';

if (!$isDryRun) {
    if (!is_dir($logsDir)) {
        @mkdir($logsDir, 0755, true);
    }
    if (!is_dir($backupsDir)) {
        @mkdir($backupsDir, 0755, true);
    }
    if (!is_dir($locksDir)) {
        @mkdir($locksDir, 0755, true);
    }
}

$logFile = !$isDryRun ? $logsDir . '/deploy_dutch_localization.log' : null;
$lockPath = $locksDir . '/deploy_dutch_localization.lock';

// Helper function for logging
function logMsg($message, $isError = false) {
    global $isCli, $logFile, $isDryRun;
    $timestamp = date('Y-m-d H:i:s');
    $prefix = $isDryRun ? '[DRY-RUN] ' : '';
    $formatted = "[$timestamp] " . ($isError ? '[ERROR] ' : '') . $prefix . $message;
    
    // Print to screen
    if ($isCli) {
        echo $formatted . "\n";
    } else {
        echo htmlspecialchars($formatted) . "<br>\n";
    }
    
    // Write to log file
    if ($logFile) {
        @file_put_contents($logFile, $formatted . "\n", FILE_APPEND);
    }
}

// Implement Migration Lock Protection
if (!$isDryRun) {
    if (file_exists($lockPath)) {
        $lockTime = filemtime($lockPath);
        $age = time() - $lockTime;
        if ($age < 3600) { // Lock is considered recent if less than 1 hour old
            logMsg("Migration lock file exists and is recent ($age seconds old). Aborting execution to prevent concurrent runs.", true);
            exit(1);
        } else {
            logMsg("Stale lock file found ($age seconds old). Overwriting lock.", false);
        }
    }
    file_put_contents($lockPath, getmypid());
    // Ensure the lock is removed on shutdown/termination
    register_shutdown_function(function() use ($lockPath) {
        if (file_exists($lockPath)) {
            @unlink($lockPath);
        }
    });
}

// Pure PHP Database Backup Function
function createDatabaseBackup($db, $backupFilePath) {
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $sql = "-- Database Backup\n";
    $sql .= "-- Generated on " . date('Y-m-d H:i:s') . "\n\n";
    $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
    
    foreach ($tables as $table) {
        // Get create table statement
        $stmtTable = $db->query("SHOW CREATE TABLE `$table`");
        $rowTable = $stmtTable->fetch(PDO::FETCH_NUM);
        $sql .= "DROP TABLE IF EXISTS `$table`;\n";
        $sql .= $rowTable[1] . ";\n\n";
        
        // Get data
        $stmtData = $db->query("SELECT * FROM `$table`");
        while ($row = $stmtData->fetch(PDO::FETCH_ASSOC)) {
            $keys = array_keys($row);
            $values = array_values($row);
            
            $escapedValues = array_map(function($val) use ($db) {
                if ($val === null) {
                    return 'NULL';
                }
                return $db->quote($val);
            }, $values);
            
            $sql .= "INSERT INTO `$table` (`" . implode("`, `", $keys) . "`) VALUES (" . implode(", ", $escapedValues) . ");\n";
        }
        $sql .= "\n";
    }
    
    $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";
    
    if (file_put_contents($backupFilePath, $sql) === false) {
        throw new Exception("Unable to write backup file to: " . $backupFilePath);
    }
}

// Helper function to safely create an index
function createIndexSafely($db, $table, $column, $indexName, $isDryRun, &$indexesCreated, &$indexesSkipped, &$errors) {
    try {
        // Verify table exists
        $stmt = $db->query("SHOW TABLES LIKE " . $db->quote($table));
        if ($stmt->rowCount() === 0) {
            logMsg("Cannot create index '$indexName': Table '$table' does not exist.", true);
            $errors++;
            return;
        }

        // Verify column exists
        $stmtCol = $db->query("SHOW COLUMNS FROM `$table` LIKE " . $db->quote($column));
        if ($stmtCol->rowCount() === 0) {
            logMsg("Skipped index '$indexName' on '$table': Column '$column' does not exist.");
            $indexesSkipped++;
            return;
        }

        // Verify index does not already exist
        $stmtIdx = $db->query("SHOW INDEX FROM `$table` WHERE Key_name = " . $db->quote($indexName));
        if ($stmtIdx->fetch()) {
            logMsg("Skipped index '$indexName' on '$table' (already exists).");
            $indexesSkipped++;
            return;
        }

        // Create index
        if ($isDryRun) {
            logMsg("[DRY-RUN] Would create index '$indexName' on '$table' ($column).");
        } else {
            $db->exec("CREATE INDEX `$indexName` ON `$table` (`$column`)");
            logMsg("Created index '$indexName' on '$table' ($column).");
        }
        $indexesCreated++;
    } catch (Exception $e) {
        logMsg("Error creating index '$indexName' on '$table': " . $e->getMessage(), true);
        $errors++;
    }
}

// Helper function to safely seed/update translation records
function seedRecord($db, $table, $id, $data, $isDryRun, &$recordsSeeded, &$recordsSkipped, &$errors) {
    try {
        // Check if the record with this ID exists
        $stmt = $db->prepare("SELECT * FROM `$table` WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $existing = $stmt->fetch();
        
        if (!$existing) {
            logMsg("Skipped seeding for `$table` ID '$id': Record does not exist.");
            $recordsSkipped++;
            return;
        }

        // Check if it already has the same translation
        $needsUpdate = false;
        foreach ($data as $col => $val) {
            if (!array_key_exists($col, $existing) || $existing[$col] !== $val) {
                $needsUpdate = true;
                break;
            }
        }

        if (!$needsUpdate) {
            logMsg("Skipped seeding for `$table` ID '$id' (already matches).");
            $recordsSkipped++;
            return;
        }

        // Prepare the UPDATE statement
        $setClauses = [];
        $params = ['id' => $id];
        foreach ($data as $col => $val) {
            $setClauses[] = "`$col` = :$col";
            $params[$col] = $val;
        }
        
        $sql = "UPDATE `$table` SET " . implode(", ", $setClauses) . " WHERE id = :id";
        
        if ($isDryRun) {
            logMsg("[DRY-RUN] Would update `$table` ID '$id' with Dutch translation.");
        } else {
            $stmtUpdate = $db->prepare($sql);
            $stmtUpdate->execute($params);
            logMsg("Updated `$table` ID '$id' with Dutch translation.");
        }
        $recordsSeeded++;
    } catch (Exception $e) {
        logMsg("Error seeding `$table` ID '$id': " . $e->getMessage(), true);
        $errors++;
    }
}

// Pre-define production credentials if running with --prod CLI argument
if ($isProdCli) {
    if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
    if (!defined('DB_PORT')) define('DB_PORT', '3306');
    if (!defined('DB_NAME')) define('DB_NAME', 'u402417573_travelfinal2');
    if (!defined('DB_USER')) define('DB_USER', 'u402417573_traveluser2');
    if (!defined('DB_PASS')) define('DB_PASS', 'Jukx0kRPf>b8');
}

// Load config and connection
require_once PATH_ROOT . '/includes/config.php';
require_once PATH_ROOT . '/classes/Database.php';
require_once PATH_ROOT . '/classes/PageManager.php';
require_once PATH_ROOT . '/classes/PostManager.php';

logMsg("=================================");
logMsg("Dutch Localization Deployment Hardened Utility");
if ($isDryRun) {
    logMsg("MODE: DRY RUN (No modifications will be made)");
} else {
    logMsg("MODE: LIVE");
}
logMsg("=================================");

try {
    $db = Database::getInstance()->getConnection();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Force UTF-8 Safety for proper Dutch special character support
    $db->exec("SET NAMES utf8mb4;");
    $db->exec("SET CHARACTER SET utf8mb4;");

    // -------------------------------------------------------------
    // STEP 1: Database Backup
    // -------------------------------------------------------------
    if (!$isDryRun) {
        try {
            $timestamp = date('Ymd_His');
            $backupPath = $backupsDir . "/pre_dutch_localization_{$timestamp}.sql";
            logMsg("Creating pre-migration database backup at $backupPath...");
            createDatabaseBackup($db, $backupPath);
            
            // Backup Integrity Verification
            if (!file_exists($backupPath)) {
                throw new Exception("Backup file does not exist after creation.");
            }
            $fileSize = filesize($backupPath);
            if ($fileSize <= 0) {
                throw new Exception("Backup file is empty (0 bytes).");
            }
            $formattedSize = round($fileSize / 1024, 2) . ' KB';
            logMsg("Backup created successfully. File size: $formattedSize.");

            // Backup Retention Policy (retain only latest 10 files)
            $backups = glob($backupsDir . '/pre_dutch_localization_*.sql');
            if (count($backups) > 10) {
                usort($backups, function($a, $b) {
                    return filemtime($a) - filemtime($b);
                });
                $toDeleteCount = count($backups) - 10;
                for ($i = 0; $i < $toDeleteCount; $i++) {
                    if (@unlink($backups[$i])) {
                        logMsg("Deleted old backup due to retention policy: " . basename($backups[$i]));
                    } else {
                        logMsg("Warning: Failed to delete old backup: " . basename($backups[$i]), true);
                    }
                }
            }
        } catch (Exception $e) {
            logMsg("Failed to create database backup: " . $e->getMessage(), true);
            logMsg("Aborting migration to prevent data loss risk.", true);
            exit(1);
        }
    } else {
        logMsg("[DRY-RUN] Skipped database backup creation.");
    }

    // -------------------------------------------------------------
    // STEP 2: Schema Migration (Add _nl columns)
    // -------------------------------------------------------------
    logMsg("Starting schema migration...");
    
    $columnsAdded = 0;
    $columnsSkipped = 0;
    $migrationErrors = 0;

    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $t) {
        try {
            $stmtCol = $db->query("DESCRIBE `$t`");
            $cols = $stmtCol->fetchAll(PDO::FETCH_ASSOC);
            
            // Find all columns ending in _en
            foreach ($cols as $col) {
                $field = $col['Field'];
                $type = $col['Type'];
                $null = 'NULL'; // Always make _nl columns nullable to prevent insert failures on existing code
                $default = $col['Default'] !== null ? "DEFAULT " . $db->quote($col['Default']) : 'DEFAULT NULL';
                
                if (str_ends_with($field, '_en')) {
                    $baseName = substr($field, 0, -3);
                    $nlField = $baseName . '_nl';
                    
                    // Check if _nl column already exists
                    $exists = false;
                    foreach ($cols as $c) {
                        if ($c['Field'] === $nlField) {
                            $exists = true;
                            break;
                        }
                    }
                    
                    if (!$exists) {
                        $alterSql = "ALTER TABLE `$t` ADD `$nlField` $type $null $default AFTER `$field`";
                        if ($isDryRun) {
                            logMsg("[DRY-RUN] Would execute: $alterSql");
                        } else {
                            $db->exec($alterSql);
                            logMsg("Added column `$nlField` to table `$t`.");
                        }
                        $columnsAdded++;
                    } else {
                        logMsg("Skipped column `$nlField` in table `$t` (already exists).");
                        $columnsSkipped++;
                    }
                }
            }
        } catch (Exception $e) {
            logMsg("Error migrating table `$t`: " . $e->getMessage(), true);
            $migrationErrors++;
        }
    }

    // -------------------------------------------------------------
    // STEP 3: Create Indexes
    // -------------------------------------------------------------
    logMsg("Configuring database indexes...");
    
    $indexesCreated = 0;
    $indexesSkipped = 0;
    $indexErrors = 0;

    $indexes = [
        ['table' => 'custom_pages', 'column' => 'slug_nl', 'name' => 'idx_custom_pages_slug_nl'],
        ['table' => 'women_stories', 'column' => 'slug_nl', 'name' => 'idx_stories_slug_nl'],
        ['table' => 'podcasts', 'column' => 'slug_nl', 'name' => 'idx_podcasts_slug_nl']
    ];

    foreach ($indexes as $idx) {
        createIndexSafely($db, $idx['table'], $idx['column'], $idx['name'], $isDryRun, $indexesCreated, $indexesSkipped, $indexErrors);
    }

    // -------------------------------------------------------------
    // STEP 4: Run Idempotent Seeding
    // -------------------------------------------------------------
    logMsg("Seeding Dutch localization content...");
    
    $recordsSeeded = 0;
    $seedingSkipped = 0;
    $seedingErrors = 0;

    // Seeding Menus
    $menus = [
        'menu-destinations' => ['title_nl' => 'BESTEMMINGEN'],
        'menu-experiences' => ['title_nl' => 'ERVARINGEN'],
        'menu-plan' => ['title_nl' => 'PLAN UW REIS']
    ];
    foreach ($menus as $id => $data) {
        seedRecord($db, 'menus', $id, $data, $isDryRun, $recordsSeeded, $seedingSkipped, $seedingErrors);
    }

    // Seeding Menu Sections
    $sections = [
        'sec-americas' => ['title_nl' => 'AMERIKA\'S'],
        'sec-australia' => ['title_nl' => 'AUSTRALIË'],
        'sec-experiences' => ['title_nl' => 'ALLE ERVARINGEN'],
        'sec-plan' => ['title_nl' => 'HULP BIJ HET PLAN']
    ];
    foreach ($sections as $id => $data) {
        seedRecord($db, 'menu_sections', $id, $data, $isDryRun, $recordsSeeded, $seedingSkipped, $seedingErrors);
    }

    // Seeding Menu Links
    $links = [
        'link-accom' => ['title_nl' => 'ACCOMMODATIE'],
        'link-act' => ['title_nl' => 'ACT'],
        'link-beach' => ['title_nl' => 'STRANDUITSTAPJES'],
        'link-cali' => ['title_nl' => 'CALIFORNIË'],
        'link-colo' => ['title_nl' => 'COLORADO'],
        'link-cruises' => ['title_nl' => 'CRUISES'],
        'link-encounters' => ['title_nl' => 'ONTMOETINGEN MET DIEREN'],
        'link-family' => ['title_nl' => 'FAMILIEREIZEN'],
        'link-gear' => ['title_nl' => 'UITRUSTING & ONDERSTEUNING'],
        'link-nsw' => ['title_nl' => 'NEW SOUTH WALES'],
        'link-nt' => ['title_nl' => 'NOORDELIJK TERRITORIUM'],
        'link-qld' => ['title_nl' => 'QUEENSLAND'],
        'link-texas' => ['title_nl' => 'TEXAS'],
        'link-tips' => ['title_nl' => 'TIPS & TRICKS'],
        'link-transport' => ['title_nl' => 'VERVOER']
    ];
    foreach ($links as $id => $data) {
        seedRecord($db, 'menu_links', $id, $data, $isDryRun, $recordsSeeded, $seedingSkipped, $seedingErrors);
    }

    // Seeding Categories
    $categories = [
        'cat-accommodation' => ['name_nl' => 'Accommodatie', 'slug_nl' => 'accommodation'],
        'cat-act' => ['name_nl' => 'ACT', 'slug_nl' => 'australian-capital-territory'],
        'cat-africa' => ['name_nl' => 'Afrika', 'slug_nl' => 'africa'],
        'cat-americas' => ['name_nl' => 'Amerika\'s', 'slug_nl' => 'americas'],
        'cat-animal-encounters' => ['name_nl' => 'Ontmoetingen met dieren', 'slug_nl' => 'animal-encounters'],
        'cat-animals' => ['name_nl' => 'Assistentiedieren', 'slug_nl' => 'assistance-animals'],
        'cat-asiapacific' => ['name_nl' => 'Azië-Pacific', 'slug_nl' => 'asia-pacific'],
        'cat-australia' => ['name_nl' => 'Australië', 'slug_nl' => 'australia'],
        'cat-cruises' => ['name_nl' => 'Cruises', 'slug_nl' => 'cruises'],
        'cat-deaf' => ['name_nl' => 'Doof / Slechthorend', 'slug_nl' => 'deaf-hard-of-hearing'],
        'cat-europe' => ['name_nl' => 'Europa', 'slug_nl' => 'europe'],
        'cat-events' => ['name_nl' => 'Evenementen & Feestdagen', 'slug_nl' => 'events-holidays'],
        'cat-family' => ['name_nl' => 'Familiereizen', 'slug_nl' => 'family-travel'],
        'cat-food' => ['name_nl' => 'Eten & Drinken', 'slug_nl' => 'food-drink'],
        'cat-gear' => ['name_nl' => 'Uitrusting & Ondersteuning', 'slug_nl' => 'gear'],
        'cat-hidden' => ['name_nl' => 'Onzichtbare Beperkingen', 'slug_nl' => 'hidden-disabilities'],
        'cat-inspiration' => ['name_nl' => 'Inspiratie', 'slug_nl' => 'inspiration'],
        'cat-mobility' => ['name_nl' => 'Fysiek / Mobiliteit', 'slug_nl' => 'physical-mobility'],
        'cat-neurodiversity' => ['name_nl' => 'Neurodiversiteit', 'slug_nl' => 'neurodiversity'],
        'cat-nsw' => ['name_nl' => 'New South Wales', 'slug_nl' => 'new-south-wales'],
        'cat-nt' => ['name_nl' => 'Noordelijk Territorium', 'slug_nl' => 'northern-territory'],
        'cat-queensland' => ['name_nl' => 'Queensland', 'slug_nl' => 'queensland'],
        'cat-reviews' => ['name_nl' => 'Bronnen & Beoordelingen', 'slug_nl' => 'resources-reviews'],
        'cat-sa' => ['name_nl' => 'Zuid-Australië', 'slug_nl' => 'south-australia'],
        'cat-sensory' => ['name_nl' => 'Sensorische Behoeften', 'slug_nl' => 'sensory-needs'],
        'cat-tasmania' => ['name_nl' => 'Tasmanië', 'slug_nl' => 'tasmania'],
        'cat-tips' => ['name_nl' => 'Tips & Trucs', 'slug_nl' => 'tips-tricks'],
        'cat-transport' => ['name_nl' => 'Vervoer', 'slug_nl' => 'transport'],
        'cat-victoria' => ['name_nl' => 'Victoria', 'slug_nl' => 'victoria'],
        'cat-vision' => ['name_nl' => 'Blind / Slechtziend', 'slug_nl' => 'blind-low-vision'],
        'cat-wa' => ['name_nl' => 'West-Australië', 'slug_nl' => 'western-australia']
    ];
    foreach ($categories as $id => $data) {
        seedRecord($db, 'categories', $id, $data, $isDryRun, $recordsSeeded, $seedingSkipped, $seedingErrors);
    }

    // -------------------------------------------------------------
    // STEP 5: Clear Caches (Only if everything succeeded)
    // -------------------------------------------------------------
    $totalErrors = $migrationErrors + $indexErrors + $seedingErrors;
    if ($totalErrors === 0) {
        if (!$isDryRun) {
            logMsg("Clearing server caches...");
            try {
                $pageMgr = new PageManager();
                $pageMgr->clearCache();

                $postMgr = new PostManager();
                $postMgr->clearCache();
                logMsg("Caches cleared successfully.");
            } catch (Exception $e) {
                logMsg("Error clearing caches: " . $e->getMessage(), true);
                $totalErrors++;
            }
        } else {
            logMsg("[DRY-RUN] Would clear server caches.");
        }
    } else {
        logMsg("WARNING: Cache clearing bypassed because migration or seeding encountered errors.", true);
    }

    // -------------------------------------------------------------
    // STEP 6: Hardened Execution Metrics & Report
    // -------------------------------------------------------------
    $endTime = microtime(true);
    $endDateTime = date('Y-m-d H:i:s');
    $executionTime = round($endTime - $startTime, 4);

    $peakMemory = round(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB';
    $currentMemory = round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB';

    logMsg("=================================");
    logMsg("Dutch Localization Deployment Report");
    logMsg("=================================");
    logMsg("Start Time: " . $startDateTime);
    logMsg("End Time: " . $endDateTime);
    logMsg("Total Execution Time: " . $executionTime . " seconds");
    logMsg("Peak Memory Usage: " . $peakMemory);
    logMsg("Current Memory Usage: " . $currentMemory);
    logMsg("---------------------------------");
    logMsg("Columns Added: " . $columnsAdded);
    logMsg("Columns Skipped: " . $columnsSkipped);
    logMsg("Indexes Created: " . $indexesCreated);
    logMsg("Indexes Skipped: " . $indexesSkipped);
    logMsg("Records Seeded: " . $recordsSeeded);
    logMsg("Errors Encountered: " . $totalErrors);
    logMsg("=================================");

} catch (Exception $e) {
    logMsg("Fatal Error in deployment script: " . $e->getMessage(), true);
    exit(1);
}
