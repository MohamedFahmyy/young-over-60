<?php
// assets/css/styles-min.php
// Dynamic CSS Minification Compiler with Caching

// Set error reporting to avoid printing PHP warnings in CSS output
error_reporting(0);
ini_set('display_errors', 0);

if (!defined('PATH_ROOT')) {
    define('PATH_ROOT', dirname(dirname(__DIR__)));
}
require_once PATH_ROOT . '/includes/config.php';

$sourceFile = PATH_ROOT . '/assets/css/styles.css';
$cacheFile = PATH_CACHE . '/styles.min.css';

// Check if source CSS file exists
if (!file_exists($sourceFile)) {
    header("HTTP/1.0 404 Not Found");
    exit("Source stylesheet not found.");
}

$sourceTime = filemtime($sourceFile);
$cacheTime = file_exists($cacheFile) ? filemtime($cacheFile) : 0;

// Recompile if cache doesn't exist or is stale
if ($cacheTime < $sourceTime) {
    $css = file_get_contents($sourceFile);
    
    // Perform minification
    // Remove comments
    $css = preg_replace('!/\*[^*]*\*+(?:[^/*][^*]*\*+)*/!', '', $css);
    // Remove spaces, newlines, tabs
    $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
    $css = preg_replace('/\s*([\{\}:;,])\s*/', '$1', $css);
    // Remove unnecessary trailing semicolons
    $css = str_replace(';}', '}', $css);
    
    // Save to cache
    file_put_contents($cacheFile, $css);
    $cacheTime = time();
}

// Set Headers for caching and performance
header("Content-Type: text/css; charset=UTF-8");
header("Last-Modified: " . gmdate("D, d M Y H:i:s", $cacheTime) . " GMT");
header("Cache-Control: public, max-age=31536000"); // 1 year cache

// Check If-Modified-Since header
if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
    $ifModifiedSince = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
    if ($ifModifiedSince >= $sourceTime) {
        header("HTTP/1.1 304 Not Modified");
        exit();
    }
}

// Serve cached file
readfile($cacheFile);
