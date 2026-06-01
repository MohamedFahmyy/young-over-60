<?php
define('DB_HOST', '127.0.0.1');
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/classes/Database.php';

$db = Database::getInstance()->getConnection();
$stmt = $db->query("DESCRIBE site_settings");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
