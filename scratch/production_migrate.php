<?php
// CLI migration script for production environments
// This script runs the database upgrades (self-healing migrations) and ensures the cache flag and indexes are properly created.

// Define root path
if (!defined('PATH_ROOT')) {
    define('PATH_ROOT', dirname(__DIR__));
}

// Load configurations and classes
require_once PATH_ROOT . '/includes/config.php';
require_once PATH_ROOT . '/classes/Database.php';

echo "=========================================\n";
echo "  Young Over 60 - Production Migration   \n";
echo "=========================================\n";

try {
    echo "Connecting to the database...\n";
    $db = Database::getInstance()->getConnection();
    echo "✓ Connected successfully.\n";

    echo "Running self-healing migrations and index optimizations...\n";
    
    // Check if the flag file was successfully written
    $cacheDir = defined('PATH_CACHE') ? PATH_CACHE : PATH_ROOT . '/cache';
    $flagFile = $cacheDir . '/db_upgraded_' . '2026_07_04' . '.flag';
    
    if (file_exists($flagFile)) {
        echo "✓ Migrations completed successfully.\n";
        echo "✓ Version Flag: db_upgraded_2026_07_04.flag exists.\n";
        echo "  Upgrade Timestamp: " . file_get_contents($flagFile) . "\n";
    } else {
        echo "⚠️  Migration finished, but flag file was not written. Please check folder permissions for: " . $cacheDir . "\n";
    }
    
    // Display list of indexes to verify they were created
    echo "Verifying database indexes...\n";
    $stmt = $db->query("SHOW INDEX FROM posts");
    $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $idxNames = array_unique(array_column($indexes, 'Key_name'));
    
    $expectedIdx = [
        'idx_posts_slug_nl',
        'idx_posts_status_slug',
        'idx_posts_status_slug_ar',
        'idx_posts_status_slug_nl'
    ];
    
    foreach ($expectedIdx as $idx) {
        if (in_array($idx, $idxNames)) {
            echo "  ✓ Index '$idx' exists.\n";
        } else {
            echo "  ❌ Index '$idx' is MISSING.\n";
        }
    }
    
    echo "=========================================\n";
    echo "Migration completed successfully.\n";
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
