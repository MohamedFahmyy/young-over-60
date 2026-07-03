<?php
// thumbnail.php
// Secure Dynamic Image Resizer and Caching service with WebP conversion

// Disable error reporting for clean header/output delivery
error_reporting(0);
ini_set('display_errors', 0);

if (!defined('PATH_ROOT')) {
    define('PATH_ROOT', __DIR__);
}
if (!defined('PATH_CACHE')) {
    define('PATH_CACHE', PATH_ROOT . '/cache');
}

// 1. Inputs and Parameters Sanitization
$src = $_GET['src'] ?? '';
$w = isset($_GET['w']) ? (int)$_GET['w'] : 0;
$h = isset($_GET['h']) ? (int)$_GET['h'] : 0;

if (empty($src)) {
    http_response_code(400);
    exit("Source image parameter is required.");
}

// 2. Resolve target file path and check for Directory Traversal
$srcPath = parse_url($src, PHP_URL_PATH);
// Normalize relative path
$relativePath = ltrim($srcPath, '/');

// Absolute path to the requested file
$requestedFile = PATH_ROOT . '/' . $relativePath;

// Secure base directory for uploads
$baseDir = realpath(PATH_ROOT . '/uploads');
$realFile = realpath($requestedFile);

if ($realFile === false || !str_starts_with($realFile, $baseDir)) {
    http_response_code(403);
    exit("Access Denied.");
}

if (!file_exists($realFile)) {
    http_response_code(404);
    exit("File not found.");
}

// 3. Whitelist allowed file extensions
$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
$ext = strtolower(pathinfo($realFile, PATHINFO_EXTENSION));

if (!in_array($ext, $allowedExtensions, true)) {
    http_response_code(403);
    exit("Invalid file type.");
}

// 4. Fallback to original if dimensions are not specified
if ($w <= 0 && $h <= 0) {
    serveOriginal($realFile);
}

// 5. Enforce Whitelisted target widths to prevent DDoS
$allowedWidths = [400, 800, 1200];
if (!in_array($w, $allowedWidths, true)) {
    $w = 400; // Default to smallest size
}

// 6. Detect WebP capability
$browserSupportsWebp = (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false);
$gdSupportsWebp = function_exists('imagewebp') && function_exists('imagecreatefromwebp');
$outputWebp = ($browserSupportsWebp && $gdSupportsWebp);

// 7. Calculate cache keys and check cache hit
$fileMtime = filemtime($realFile);
$format = $outputWebp ? 'webp' : $ext;
$cacheKey = md5($realFile . '_' . $w . '_' . $h . '_' . $fileMtime . '_' . ($outputWebp ? 'webp' : 'orig'));
$cacheFile = PATH_CACHE . '/thumb_' . $cacheKey . '.' . ($format === 'jpg' ? 'jpeg' : $format);

$mime = 'image/jpeg';
if ($outputWebp) {
    $mime = 'image/webp';
} elseif ($ext === 'png') {
    $mime = 'image/png';
} elseif ($ext === 'webp') {
    $mime = 'image/webp';
}

if (file_exists($cacheFile) && filemtime($cacheFile) >= $fileMtime) {
    serveFile($cacheFile, $mime);
}

// 8. Load GD library and verify
if (!extension_loaded('gd')) {
    serveOriginal($realFile);
}

$info = getimagesize($realFile);
if (!$info) {
    serveOriginal($realFile);
}

$origW = $info[0];
$origH = $info[1];
$sourceMime = $info['mime'];

// Calculate new dimensions keeping aspect ratio
if ($w > 0 && $h <= 0) {
    $newW = $w;
    $newH = (int)round(($origH / $origW) * $newW);
} elseif ($h > 0 && $w <= 0) {
    $newH = $h;
    $newW = (int)round(($origW / $origH) * $newH);
} else {
    $newW = $w;
    $newH = $h;
}

// Don't upscale
if ($newW >= $origW) {
    serveOriginal($realFile);
}

// Create canvas
$canvas = imagecreatetruecolor($newW, $newH);

// Retain transparency for PNG/WebP
if ($sourceMime === 'image/png' || $sourceMime === 'image/webp') {
    imagealphablending($canvas, false);
    imagesavealpha($canvas, true);
    $transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
    imagefilledrectangle($canvas, 0, 0, $newW, $newH, $transparent);
}

// Load source image
switch ($sourceMime) {
    case 'image/jpeg':
    case 'image/jpg':
        $source = imagecreatefromjpeg($realFile);
        break;
    case 'image/png':
        $source = imagecreatefrompng($realFile);
        break;
    case 'image/webp':
        $source = imagecreatefromwebp($realFile);
        break;
    default:
        serveOriginal($realFile);
}

if (!$source) {
    serveOriginal($realFile);
}

// Resize image
imagecopyresampled($canvas, $source, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

// Save compressed version to cache
$success = false;
if ($outputWebp) {
    $success = imagewebp($canvas, $cacheFile, 82);
} else {
    switch ($ext) {
        case 'png':
            $success = imagepng($canvas, $cacheFile, 6);
            break;
        case 'webp':
            $success = imagewebp($canvas, $cacheFile, 82);
            break;
        default:
            $success = imagejpeg($canvas, $cacheFile, 82);
    }
}

imagedestroy($canvas);
imagedestroy($source);

if ($success) {
    serveFile($cacheFile, $mime);
} else {
    serveOriginal($realFile);
}

function serveOriginal($path) {
    $info = getimagesize($path);
    $mime = $info['mime'] ?? 'image/jpeg';
    serveFile($path, $mime);
}

function serveFile($path, $mime) {
    $mtime = filemtime($path);
    $etag = md5($path . '_' . $mtime . '_' . filesize($path));

    header('Content-Type: ' . $mime);
    header('Content-Length: ' . filesize($path));
    header('Cache-Control: public, max-age=31536000, immutable');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $mtime) . ' GMT');
    header('ETag: "' . $etag . '"');

    // Handle ETag cache hit
    if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === '"' . $etag . '"') {
        http_response_code(304);
        exit;
    }

    // Handle Last-Modified cache hit
    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
        $ifModifiedSince = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
        if ($ifModifiedSince >= $mtime) {
            http_response_code(304);
            exit;
        }
    }

    readfile($path);
    exit;
}
