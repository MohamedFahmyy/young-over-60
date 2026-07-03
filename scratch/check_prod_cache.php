<?php
// scratch/check_prod_cache.php
// Diagnostic tool to check cache writing permissions and migration status on production

define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/includes/config.php';

// Secure access - requires secret token in URL query
$secret = $_GET['secret'] ?? '';
if ($secret !== 'gemini_diag_secret_998877') {
    header("HTTP/1.1 403 Forbidden");
    exit("Access Denied.");
}

echo "<h2>Production Cache & Migration Diagnostics</h2>";

$cacheDir = defined('PATH_CACHE') ? PATH_CACHE : PATH_ROOT . '/cache';
echo "<ul>";
echo "<li><strong>Cache Directory:</strong> " . htmlspecialchars($cacheDir) . "</li>";
echo "<li><strong>Directory Exists:</strong> " . (is_dir($cacheDir) ? "YES" : "NO") . "</li>";
echo "<li><strong>Directory Writable:</strong> " . (is_writable($cacheDir) ? "YES" : "NO") . "</li>";

$flagFile = $cacheDir . '/db_upgraded_2026_07_04.flag';
echo "<li><strong>Flag File Path:</strong> " . htmlspecialchars($flagFile) . "</li>";
echo "<li><strong>Flag File Exists:</strong> " . (file_exists($flagFile) ? "YES" : "NO") . "</li>";

if (file_exists($flagFile)) {
    echo "<li><strong>Flag File Content (Timestamp):</strong> " . htmlspecialchars(file_get_contents($flagFile)) . "</li>";
} else {
    // Try to write it now
    $written = @file_put_contents($flagFile, date('Y-m-d H:i:s'));
    if ($written !== false) {
        echo "<li style='color:green;'><strong>Attempt to write flag file:</strong> SUCCESS</li>";
    } else {
        $lastError = error_get_last();
        echo "<li style='color:red;'><strong>Attempt to write flag file:</strong> FAILED. Error: " . htmlspecialchars($lastError['message'] ?? 'Unknown error') . "</li>";
    }
}

// Let's check post caching as well
$testCacheFile = $cacheDir . '/test_write.txt';
$testWritten = @file_put_contents($testCacheFile, 'test');
if ($testWritten !== false) {
    echo "<li style='color:green;'><strong>Test file write:</strong> SUCCESS</li>";
    @unlink($testCacheFile);
} else {
    echo "<li style='color:red;'><strong>Test file write:</strong> FAILED</li>";
}

// Run database indexes diagnostics
try {
    require_once PATH_ROOT . '/classes/Database.php';
    $db = Database::getInstance()->getConnection();
    echo "<li><strong>Database connection:</strong> SUCCESS</li>";
    
    // Check if the indexes exist
    $stmt = $db->query("SHOW INDEX FROM posts");
    $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $indexNames = array_column($indexes, 'Key_name');
    
    $requiredIndexes = [
        'idx_posts_slug_nl',
        'idx_posts_status_slug',
        'idx_posts_status_slug_ar',
        'idx_posts_status_slug_nl'
    ];
    
    foreach ($requiredIndexes as $idx) {
        $exists = in_array($idx, $indexNames);
        $color = $exists ? 'green' : 'red';
        $status = $exists ? 'EXISTS' : 'MISSING';
        echo "<li style='color:$color;'><strong>Index '$idx':</strong> $status</li>";
    }
} catch (Exception $e) {
    echo "<li style='color:red;'><strong>Database connection:</strong> FAILED. Error: " . htmlspecialchars($e->getMessage()) . "</li>";
}

echo "</ul>";
