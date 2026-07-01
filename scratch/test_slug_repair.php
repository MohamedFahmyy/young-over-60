<?php
define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/includes/config.php';
require_once PATH_ROOT . '/classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    $id = 'test-repair-story-id';
    $stmt = $db->query("SELECT slug_ar FROM women_stories WHERE id = '$id'");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        echo "Verification: Dummy record slug_ar in database is: '{$row['slug_ar']}'\n";
        
        // Clean up the dummy record
        $db->exec("DELETE FROM women_stories WHERE id = '$id'");
        echo "Verification cleanup: Dummy record deleted.\n";
    } else {
        echo "Verification Error: Dummy record not found.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
