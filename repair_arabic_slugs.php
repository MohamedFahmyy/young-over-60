<?php
/**
 * repair_arabic_slugs.php
 * 
 * Production Database Migration Script to repair 'n-a' Arabic Slugs
 * Run via CLI: php repair_arabic_slugs.php [--dry-run] [--prod]
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

$logFile = !$isDryRun ? $logsDir . '/repair_arabic_slugs.log' : null;
$lockPath = $locksDir . '/repair_arabic_slugs.lock';

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
        if ($age < 3600) {
            logMsg("Repair lock file exists and is recent ($age seconds old). Aborting execution to prevent concurrent runs.", true);
            exit(1);
        } else {
            logMsg("Stale lock file found ($age seconds old). Overwriting lock.", false);
        }
    }
    file_put_contents($lockPath, getmypid());
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
    
    $sql = "-- Database Backup before Arabic Slug Repair\n";
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

// Check if slug exists in DB
function slugExistsInDb($db, $slug, $excludeId = null) {
    $sql = "SELECT COUNT(*) FROM women_stories WHERE (slug_en = :slug_en OR slug_ar = :slug_ar OR slug_nl = :slug_nl)";
    $params = [
        'slug_en' => $slug,
        'slug_ar' => $slug,
        'slug_nl' => $slug
    ];
    if ($excludeId !== null) {
        $sql .= " AND id != :excludeId";
        $params['excludeId'] = $excludeId;
    }
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn() > 0;
}

// Pre-define production credentials if running with --prod CLI argument
if ($isProdCli) {
    if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
    if (!defined('DB_PORT')) define('DB_PORT', '3306');
    if (!defined('DB_NAME')) define('DB_NAME', 'u402417573_travelfinal');
    if (!defined('DB_USER')) define('DB_USER', 'u402417573_traveluser');
    if (!defined('DB_PASS')) define('DB_PASS', 'ProdPassword123!');
}

// Load config and connection
require_once PATH_ROOT . '/includes/config.php';
require_once PATH_ROOT . '/includes/helpers.php';
require_once PATH_ROOT . '/classes/Database.php';
require_once PATH_ROOT . '/classes/PageManager.php';
require_once PATH_ROOT . '/classes/PostManager.php';

logMsg("=================================");
logMsg("Arabic Slug Repair Migration");
if ($isDryRun) {
    logMsg("MODE: DRY RUN (No modifications will be made)");
} else {
    logMsg("MODE: LIVE");
}
logMsg("=================================");

try {
    $db = Database::getInstance()->getConnection();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Force UTF-8 Safety
    $db->exec("SET NAMES utf8mb4;");
    $db->exec("SET CHARACTER SET utf8mb4;");

    // -------------------------------------------------------------
    // STEP 1: Database Backup
    // -------------------------------------------------------------
    if (!$isDryRun) {
        try {
            $timestamp = date('Ymd_His');
            $backupPath = $backupsDir . "/pre_arabic_slug_repair_{$timestamp}.sql";
            logMsg("Creating database backup at $backupPath...");
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
            $backups = glob($backupsDir . '/pre_arabic_slug_repair_*.sql');
            if (count($backups) > 10) {
                usort($backups, function($a, $b) {
                    return filemtime($a) - filemtime($b);
                });
                $toDeleteCount = count($backups) - 10;
                for ($i = 0; $i < $toDeleteCount; $i++) {
                    if (@unlink($backups[$i])) {
                        logMsg("Deleted old repair backup due to retention policy: " . basename($backups[$i]));
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
    // STEP 2: Repair Records
    // -------------------------------------------------------------
    logMsg("Scanning women_stories table for 'n-a' Arabic slugs...");
    
    $recordsScanned = 0;
    $recordsRepaired = 0;
    $recordsSkipped = 0;
    $repairErrors = 0;

    $stmt = $db->query("SELECT id, title_ar, slug_ar FROM women_stories");
    $stories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($stories as $story) {
        $recordsScanned++;
        $id = $story['id'];
        $title_ar = $story['title_ar'];
        $slug_ar = $story['slug_ar'];

        if ($slug_ar === 'n-a') {
            try {
                // Generate a new slug using the updated slugify logic
                $newSlug = slugify(empty($title_ar) ? '' : $title_ar);
                
                // If it is still empty or 'n-a' (meaning title_ar was empty or non-transliteratable)
                if (empty($newSlug) || $newSlug === 'n-a') {
                    $newSlug = 'story-' . time();
                }

                // Verify uniqueness (excluding the current row)
                $originalSlug = $newSlug;
                $counter = 1;
                while (slugExistsInDb($db, $newSlug, $id)) {
                    $newSlug = $originalSlug . '-' . $counter++;
                }

                logMsg("Found broken Arabic slug for ID '$id'. Title (AR): '$title_ar'. Original Slug: '$slug_ar'. New Slug: '$newSlug'.");

                if ($isDryRun) {
                    logMsg("[DRY-RUN] Would update ID '$id' to slug_ar = '$newSlug'.");
                } else {
                    $stmtUpdate = $db->prepare("UPDATE women_stories SET slug_ar = :slug_ar WHERE id = :id");
                    $stmtUpdate->execute(['slug_ar' => $newSlug, 'id' => $id]);
                    logMsg("Repaired ID '$id' with new slug_ar: '$newSlug'.");
                }
                $recordsRepaired++;
            } catch (Exception $e) {
                logMsg("Error repairing story ID '$id': " . $e->getMessage(), true);
                $repairErrors++;
            }
        } else {
            $recordsSkipped++;
        }
    }

    // -------------------------------------------------------------
    // STEP 3: Clear Caches
    // -------------------------------------------------------------
    if ($repairErrors === 0 && $recordsRepaired > 0) {
        if (!$isDryRun) {
            logMsg("Clearing server caches to reflect repaired slugs...");
            try {
                $pageMgr = new PageManager();
                $pageMgr->clearCache();

                $postMgr = new PostManager();
                $postMgr->clearCache();
                logMsg("Caches cleared successfully.");
            } catch (Exception $e) {
                logMsg("Error clearing caches: " . $e->getMessage(), true);
                $repairErrors++;
            }
        } else {
            logMsg("[DRY-RUN] Would clear server caches.");
        }
    }

    // -------------------------------------------------------------
    // STEP 4: Metrics & Final Report
    // -------------------------------------------------------------
    $endTime = microtime(true);
    $endDateTime = date('Y-m-d H:i:s');
    $executionTime = round($endTime - $startTime, 4);

    $peakMemory = round(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB';
    $currentMemory = round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB';

    logMsg("=================================");
    logMsg("Arabic Slug Repair Report");
    logMsg("=================================");
    logMsg("Start Time: " . $startDateTime);
    logMsg("End Time: " . $endDateTime);
    logMsg("Total Execution Time: " . $executionTime . " seconds");
    logMsg("Peak Memory Usage: " . $peakMemory);
    logMsg("Current Memory Usage: " . $currentMemory);
    logMsg("---------------------------------");
    logMsg("Stories Scanned: " . $recordsScanned);
    logMsg("Stories Repaired: " . $recordsRepaired);
    logMsg("Stories Skipped: " . $recordsSkipped);
    logMsg("Errors Encountered: " . $repairErrors);
    logMsg("=================================");

} catch (Exception $e) {
    logMsg("Fatal Error in repair script: " . $e->getMessage(), true);
    exit(1);
}
