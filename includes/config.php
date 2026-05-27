<?php
// includes/config.php
// Travel Without Limits Global Configurations

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone to Sydney (default for TWL Australia)
date_default_timezone_set('Australia/Sydney');

// Secure Session Start
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    
    // Enable secure cookies if HTTPS is on
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', 1);
    }
    
    session_start();
}

// Database Credentials
require_once dirname(__DIR__) . '/config/database.php';

// Dynamic Base URL Calculation (for local subdirectories or production domains)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$dir = dirname($scriptName);
$dir = str_replace('\\', '/', $dir);
// Strip subdirectories like /admin or /api if script is executed directly
$dir = preg_replace('/\\/(admin|api)$/', '', $dir);
if ($dir === '/' || $dir === '\\') {
    $dir = '';
}
define('BASE_URL', $protocol . $host . $dir);

// System Paths
define('PATH_ROOT', dirname(__DIR__));
define('PATH_UPLOADS', PATH_ROOT . '/uploads');
define('PATH_CACHE', PATH_ROOT . '/cache');

// Create Cache & Uploads directory if not exists
if (!is_dir(PATH_UPLOADS)) {
    mkdir(PATH_UPLOADS, 0755, true);
}
if (!is_dir(PATH_CACHE)) {
    mkdir(PATH_CACHE, 0755, true);
}
