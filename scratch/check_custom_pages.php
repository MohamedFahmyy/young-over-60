<?php
// scratch/check_custom_pages.php

if (php_sapi_name() === 'cli' && !defined('DB_HOST')) {
    define('DB_HOST', '127.0.0.1');
}

define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/includes/config.php';
require_once PATH_ROOT . '/classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "--- custom_pages columns ---\n";
    $stmt = $db->query("DESCRIBE custom_pages");
    print_r(array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'Field'));

    echo "\n--- page_revisions columns ---\n";
    $stmt2 = $db->query("DESCRIBE page_revisions");
    print_r(array_column($stmt2->fetchAll(PDO::FETCH_ASSOC), 'Field'));
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
