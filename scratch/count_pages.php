<?php
// scratch/count_pages.php
// Counts physical and database pages in the project

if (php_sapi_name() === 'cli' && !defined('DB_HOST')) {
    define('DB_HOST', '127.0.0.1');
}

define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/includes/config.php';
require_once PATH_ROOT . '/classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // 1. Scan pages/ directory
    $physicalPages = array_filter(glob(PATH_ROOT . '/pages/*.php'), 'is_file');
    echo "--- PHYSICAL PAGE TEMPLATES (pages/ directory) ---\n";
    echo "Total: " . count($physicalPages) . " templates\n";
    foreach ($physicalPages as $file) {
        echo " - " . basename($file) . "\n";
    }
    
    // 2. Query custom_pages table in database
    echo "\n--- DATABASE CUSTOM PAGES (custom_pages table) ---\n";
    try {
        $stmt = $db->query("SELECT id, title_en, slug_en, is_published FROM custom_pages");
        $dbPages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Total: " . count($dbPages) . " pages in database\n";
        foreach ($dbPages as $page) {
            $status = $page['is_published'] ? 'PUBLISHED' : 'DRAFT';
            echo " - {$page['title_en']} (Slug: /pages/{$page['slug_en']}) [Status: {$status}]\n";
        }
    } catch (PDOException $e) {
        echo "Error querying custom_pages: " . $e->getMessage() . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
