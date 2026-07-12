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
    $lang = $_GET['lang'] ?? 'en';
    if (!array_key_exists($lang, SUPPORTED_LANGUAGES)) {
        $lang = 'en';
    }

    // Fetch published posts
    $result = $pm->getPosts([
        'categoryId' => $categoryId,
        'search' => $search,
        'limit' => $limit,
        'page' => $page,
        'status' => 'PUBLISHED'
    ]);

    // Format response to map active language fields to generic keys
    $formattedPosts = [];
    if (isset($result['data']) && is_array($result['data'])) {
        foreach ($result['data'] as $post) {
            $title = !empty($post['title_' . $lang]) ? $post['title_' . $lang] : $post['title_en'];
            $slug = !empty($post['slug_' . $lang]) ? $post['slug_' . $lang] : $post['slug_en'];
            $excerpt = !empty($post['excerpt_' . $lang]) ? $post['excerpt_' . $lang] : $post['excerpt_en'];
            $categoryName = !empty($post['categoryName_' . $lang]) ? $post['categoryName_' . $lang] : $post['categoryName_en'];
            
            $formattedPosts[] = [
                'id' => $post['id'],
                'title' => $title,
                'slug' => $slug,
                'excerpt' => $excerpt,
                'coverImage' => $post['coverImage'],
                'publishedAt' => $post['publishedAt'],
                'categoryName' => $categoryName,
                'authorName' => $post['authorName']
            ];
        }
    }
    
    $result['data'] = $formattedPosts;

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
