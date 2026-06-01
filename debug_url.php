<?php
// debug_url.php
// Diagnostic utility to verify Base URL and Stylesheet path loading

require_once __DIR__ . '/includes/config.php';

echo "<h2>Young Over 60 - URL Diagnostics</h2>";
echo "<ul>";
echo "<li><strong>REQUEST_URI:</strong> " . htmlspecialchars($_SERVER['REQUEST_URI'] ?? '') . "</li>";
echo "<li><strong>SCRIPT_NAME:</strong> " . htmlspecialchars($_SERVER['SCRIPT_NAME'] ?? '') . "</li>";
echo "<li><strong>HTTP_HOST:</strong> " . htmlspecialchars($_SERVER['HTTP_HOST'] ?? '') . "</li>";
echo "<li><strong>Computed BASE_URL:</strong> " . htmlspecialchars(BASE_URL) . "</li>";

$stylesheetUrl = BASE_URL . "/assets/css/styles.css";
echo "<li><strong>Target Stylesheet URL:</strong> <a href='$stylesheetUrl' target='_blank'>$stylesheetUrl</a></li>";

$physicalPath = __DIR__ . "/assets/css/styles.css";
echo "<li><strong>Physical Stylesheet Path:</strong> $physicalPath</li>";
echo "<li><strong>Physical File Exists:</strong> " . (file_exists($physicalPath) ? "YES" : "NO") . "</li>";

echo "</ul>";

echo "<h3>How to resolve styling issues:</h3>";
echo "<p>If the <strong>Target Stylesheet URL</strong> above does not load or gives a 404, please open your browser's Developer Console (press <strong>F12</strong>) and check for any red error messages under the Console or Network tab. Report the error back to me so I can resolve it!</p>";
