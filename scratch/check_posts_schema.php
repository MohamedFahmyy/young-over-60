<?php
define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/config/database.php';
require_once PATH_ROOT . '/classes/Database.php';

$db = Database::getInstance()->getConnection();
$stmt = $db->query("DESCRIBE posts");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "=== posts columns ===\n";
foreach ($cols as $c) {
    echo "  " . $c['Field'] . " (" . $c['Type'] . ")\n";
}
