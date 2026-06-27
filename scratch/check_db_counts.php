<?php
define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/includes/config.php';
require_once PATH_ROOT . '/classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $t) {
        $cnt = $db->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
        echo "Table: $t ($cnt rows)\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
