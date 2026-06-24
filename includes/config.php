<?php
// includes/config.php
// Travel Without Limits Global Configurations

// Environment Detection (Production vs Development)
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$isProd = ($host === 'youngover60.com' || $host === 'www.youngover60.com');

if ($isProd) {
    // Production settings
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', dirname(__DIR__) . '/error.log');
} else {
    // Development/Local settings
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Set timezone to Sydney (default for TWL Australia)
date_default_timezone_set('Australia/Sydney');

// Central Multilingual Registry
define('SUPPORTED_LANGUAGES', [
    'en' => ['dir' => 'ltr', 'name' => 'English', 'locale' => 'en_US', 'font' => 'Outfit'],
    'ar' => ['dir' => 'rtl', 'name' => 'العربية', 'locale' => 'ar_EG', 'font' => 'Cairo'],
    'nl' => ['dir' => 'ltr', 'name' => 'Nederlands', 'locale' => 'nl_NL', 'font' => 'Outfit']
]);
define('DEFAULT_LANG', 'en');

// Secure Session Start
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Lax');
    
    // Enable secure cookies if HTTPS is on
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', 1);
    }
    
    session_start();
}

// Database Credentials
require_once dirname(__DIR__) . '/config/database.php';

// Dynamic Base URL Calculation (supporting subdirectories, virtual hosts, and dev servers)
if ($isProd) {
    define('BASE_URL', 'https://youngover60.com');
} else {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    $docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? '');
    $pathRoot = str_replace('\\', '/', dirname(__DIR__));
    $subDir = '';

    if (!empty($docRoot) && strpos($pathRoot, $docRoot) === 0) {
        $subDir = substr($pathRoot, strlen($docRoot));
    }
    $subDir = str_replace('\\', '/', $subDir);
    $subDir = rtrim($subDir, '/');

    define('BASE_URL', $protocol . $host . $subDir);
}

// System Paths
if (!defined('PATH_ROOT')) {
    define('PATH_ROOT', dirname(__DIR__));
}
define('PATH_UPLOADS', PATH_ROOT . '/uploads');
define('PATH_CACHE', PATH_ROOT . '/cache');

// Create Cache & Uploads directory if not exists
if (!is_dir(PATH_UPLOADS)) {
    mkdir(PATH_UPLOADS, 0755, true);
}
if (!is_dir(PATH_CACHE)) {
    mkdir(PATH_CACHE, 0755, true);
}
