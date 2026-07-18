<?php
// config/database.php
// Production & Local Database Configuration Credentials

$hostName = $_SERVER['HTTP_HOST'] ?? '';
$argv = $_SERVER['argv'] ?? [];
$isProd = ($hostName === 'youngover60.com' || 
           $hostName === 'www.youngover60.com' || 
           in_array('--prod', $argv) || 
           (php_sapi_name() === 'cli' && strpos(__DIR__, 'laragon') === false && PHP_OS_FAMILY !== 'Windows'));

if ($isProd) {
    // Production (Hostinger) credentials
    if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
    if (!defined('DB_PORT')) define('DB_PORT', '3306');
    if (!defined('DB_NAME')) define('DB_NAME', 'u402417573_travelfinal2');
    if (!defined('DB_USER')) define('DB_USER', 'u402417573_traveluser2');
    if (!defined('DB_PASS')) define('DB_PASS', 'Jukx0kRPf>b8');
} else {
    // Local / Development credentials
    $devHost = (PHP_OS_FAMILY === 'Windows') ? '127.0.0.1' : 'localhost';
    if (!defined('DB_HOST')) define('DB_HOST', $devHost);
    if (!defined('DB_PORT')) define('DB_PORT', '3306');
    if (!defined('DB_NAME')) define('DB_NAME', 'u402417573_travelfinal');
    if (!defined('DB_USER')) define('DB_USER', 'root');
    if (!defined('DB_PASS')) define('DB_PASS', '');
}
