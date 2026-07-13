<?php
// scratch/query_debug.php
// Diagnostic tool to analyze database query execution plan on production

if (php_sapi_name() === 'cli') {
    if (!defined('DB_HOST')) define('DB_HOST', '127.0.0.1');
}

define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/includes/config.php';

// Secure Web Access: require admin session
if (php_sapi_name() !== 'cli') {
    require_once PATH_ROOT . '/classes/Database.php';
    require_once PATH_ROOT . '/classes/Auth.php';
    
    if (!Auth::check()) {
        header("HTTP/1.1 403 Forbidden");
        exit("Access Denied: Admin login required.");
    }
}

try {
    $db = Database::getInstance()->getConnection();
    
    echo "<h2>Query Execution Plan Analysis</h2>";
    
    $slug = 'european-heatwave-proves-a-treasure-trove-for-egypts-tourism-sector';
    
    // 1. Explain getPostBySlug
    $sql = "EXPLAIN SELECT p.id, p.slug_en, p.slug_ar, p.slug_nl, p.status
            FROM posts p 
            INNER JOIN categories c ON p.categoryId = c.id 
            LEFT JOIN users u ON p.authorId = u.id 
            WHERE (p.slug_en = :slug_en OR p.slug_ar = :slug_ar OR p.slug_nl = :slug_nl) 
              AND p.deleted_at IS NULL 
              AND p.status = 'PUBLISHED'";
              
    $stmt = $db->prepare($sql);
    $stmt->execute(['slug_en' => $slug, 'slug_ar' => $slug, 'slug_nl' => $slug]);
    $explain = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>1. getPostBySlug EXPLAIN Result:</h3>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    if (!empty($explain)) {
        echo "<tr>" . implode("", array_map(fn($k) => "<th>$k</th>", array_keys($explain[0]))) . "</tr>";
        foreach ($explain as $row) {
            echo "<tr>" . implode("", array_map(fn($v) => "<td>" . htmlspecialchars($v ?? 'NULL') . "</td>", $row)) . "</tr>";
        }
    }
    echo "</table>";

    // 2. Show Indexes on posts table
    $stmt = $db->query("SHOW INDEX FROM posts");
    $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>2. Indexes on `posts` table:</h3>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    if (!empty($indexes)) {
        echo "<tr>" . implode("", array_map(fn($k) => "<th>$k</th>", array_keys($indexes[0]))) . "</tr>";
        foreach ($indexes as $row) {
            echo "<tr>" . implode("", array_map(fn($v) => "<td>" . htmlspecialchars($v ?? 'NULL') . "</td>", $row)) . "</tr>";
        }
    }
    echo "</table>";

    // 3. Show Indexes on categories table
    $stmt = $db->query("SHOW INDEX FROM categories");
    $indexesCat = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>3. Indexes on `categories` table:</h3>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    if (!empty($indexesCat)) {
        echo "<tr>" . implode("", array_map(fn($k) => "<th>$k</th>", array_keys($indexesCat[0]))) . "</tr>";
        foreach ($indexesCat as $row) {
            echo "<tr>" . implode("", array_map(fn($v) => "<td>" . htmlspecialchars($v ?? 'NULL') . "</td>", $row)) . "</tr>";
        }
    }
    echo "</table>";

    // 4. Check table sizes
    $stmt = $db->query("SELECT COUNT(*) FROM posts");
    $postsCount = $stmt->fetchColumn();
    $stmt = $db->query("SELECT COUNT(*) FROM categories");
    $catsCount = $stmt->fetchColumn();
    echo "<h3>4. Table row counts:</h3>";
    echo "<ul>";
    echo "<li>Posts: $postsCount</li>";
    echo "<li>Categories: $catsCount</li>";
    echo "</ul>";

} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
