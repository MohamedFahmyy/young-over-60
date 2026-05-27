<?php
// api/posts.php
// Public posts JSON API Endpoint (Handles AJAX Grid tabs and AJAX Search)

try {
    $pm = new PostManager();

    // Parse incoming parameters
    $categoryId = $_GET['categoryId'] ?? null;
    $search = $_GET['search'] ?? null;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    // Fetch published posts
    $result = $pm->getPosts([
        'categoryId' => $categoryId,
        'search' => $search,
        'limit' => $limit,
        'page' => $page,
        'status' => 'PUBLISHED'
    ]);

    // Format response
    echo json_encode([
        'success' => true,
        'data' => $result
    ]);
} catch (Exception $e) {
    header("HTTP/1.0 500 Internal Server Error");
    echo json_encode([
        'success' => false,
        'error' => 'API Query failed: ' . $e->getMessage()
    ]);
}
exit();
