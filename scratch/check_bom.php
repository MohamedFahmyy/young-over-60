<?php
// scratch/check_bom.php
// Scans all PHP files in the project for UTF-8 BOM or leading whitespace.

define('PATH_ROOT', dirname(__DIR__));

function scan_dir($dir) {
    $files = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }
    return $files;
}

$files = scan_dir(PATH_ROOT);
$issues = 0;

foreach ($files as $file) {
    // Skip vendor or cache or temp if any
    if (strpos($file, 'vendor') !== false || strpos($file, 'cache') !== false) {
        continue;
    }
    
    $content = file_get_contents($file);
    if ($content === false) continue;
    
    // Check for UTF-8 BOM
    if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
        echo "BOM detected in: " . str_replace(PATH_ROOT, '', $file) . "\n";
        $issues++;
    }
    
    // Check for leading whitespace before <?php
    if (preg_match('/^\s+<\?php/i', $content)) {
        echo "Leading whitespace detected in: " . str_replace(PATH_ROOT, '', $file) . "\n";
        $issues++;
    }
}

if ($issues === 0) {
    echo "No BOM or leading whitespace issues found in PHP files!\n";
} else {
    echo "Found {$issues} file(s) with issues.\n";
}
