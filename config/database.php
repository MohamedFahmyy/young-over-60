<?php
// config/database.php
// Production & Local Database Configuration Credentials

$hostName = $_SERVER['HTTP_HOST'] ?? 'localhost';
$isProd = ($hostName === 'youngover60.com' || $hostName === 'www.youngover60.com');

if ($isProd) {
    // Production (Hostinger) credentials
    if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
    if (!defined('DB_PORT')) define('DB_PORT', '3306');
    if (!defined('DB_NAME')) define('DB_NAME', 'u402417573_travelfinal');
    if (!defined('DB_USER')) define('DB_USER', 'u402417573_traveluser');
    if (!defined('DB_PASS')) define('DB_PASS', 'ProdPassword123!');
} else {
    // Local / Development credentials
    if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
    if (!defined('DB_PORT')) define('DB_PORT', '3306');
    if (!defined('DB_NAME')) define('DB_NAME', 'u402417573_travelfinal');
    if (!defined('DB_USER')) define('DB_USER', 'root');
    if (!defined('DB_PASS')) define('DB_PASS', '');
}
