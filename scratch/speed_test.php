<?php
// scratch/speed_test.php
// Diagnostic tool to check performance bottlenecks

define('DB_HOST', '127.0.0.1');

$start = microtime(true);

// 1. Measure Config Load
require_once dirname(__DIR__) . '/includes/config.php';
$configLoadTime = microtime(true) - $start;

// 2. Measure Database connection time & migrations (upgrades)
$dbStart = microtime(true);
require_once dirname(__DIR__) . '/classes/Database.php';
$db = Database::getInstance();
$conn = $db->getConnection();
$dbConnectTime = microtime(true) - $dbStart;

// 3. Measure Post retrieval time (with and without cache)
require_once dirname(__DIR__) . '/classes/PostManager.php';
$pm = new PostManager($conn);

$queryStart = microtime(true);
$post = $pm->getPostBySlug('on-turtle-time-at-mon-repos-turtle-centre');
$queryTime = microtime(true) - $queryStart;

$totalTime = microtime(true) - $start;

echo "--- Speed Test Diagnostics ---\n";
echo "Config Load Time: " . round($configLoadTime * 1000, 2) . " ms\n";
echo "DB Connect & Self-Healing Migration Time: " . round($dbConnectTime * 1000, 2) . " ms\n";
echo "Post Query/Cache Fetch Time: " . round($queryTime * 1000, 2) . " ms\n";
echo "Total Execution Time: " . round($totalTime * 1000, 2) . " ms\n";
