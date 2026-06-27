<?php
define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/includes/config.php';
require_once PATH_ROOT . '/classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "=== DATABASE POSTS CHECK ===\n";
    $stmt = $db->query("SELECT id, title_en, title_ar, title_nl, excerpt_en, excerpt_ar, excerpt_nl, coverImage FROM posts");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($posts as $post) {
        echo "ID: {$post['id']}\n";
        echo "  Title EN: {$post['title_en']}\n";
        echo "  Title AR: {$post['title_ar']}\n";
        echo "  Title NL: {$post['title_nl']}\n";
        echo "  Excerpt EN: " . substr(trim($post['excerpt_en'] ?? ''), 0, 50) . "...\n";
        echo "  Excerpt AR: " . substr(trim($post['excerpt_ar'] ?? ''), 0, 50) . "...\n";
        echo "  Excerpt NL: " . substr(trim($post['excerpt_nl'] ?? ''), 0, 50) . "...\n";
        echo "  Cover Image: {$post['coverImage']}\n";
        echo "----------------------------------------\n";
    }
    
    echo "\n=== DATABASE WOMEN_STORIES CHECK ===\n";
    $stmt = $db->query("SELECT id, title_en, title_ar, title_nl, excerpt_en, excerpt_ar, excerpt_nl, coverImage FROM women_stories");
    $stories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($stories as $story) {
        echo "ID: {$story['id']}\n";
        echo "  Title EN: {$story['title_en']}\n";
        echo "  Title AR: {$story['title_ar']}\n";
        echo "  Title NL: {$story['title_nl']}\n";
        echo "  Excerpt EN: " . substr(trim($story['excerpt_en'] ?? ''), 0, 50) . "...\n";
        echo "  Excerpt AR: " . substr(trim($story['excerpt_ar'] ?? ''), 0, 50) . "...\n";
        echo "  Excerpt NL: " . substr(trim($story['excerpt_nl'] ?? ''), 0, 50) . "...\n";
        echo "  Cover Image: {$story['coverImage']}\n";
        echo "----------------------------------------\n";
    }
    
    echo "\n=== DATABASE PODCASTS CHECK ===\n";
    $stmt = $db->query("SELECT id, title_en, title_ar, title_nl, coverImage FROM podcasts");
    $podcasts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($podcasts as $podcast) {
        echo "ID: {$podcast['id']}\n";
        echo "  Title EN: {$podcast['title_en']}\n";
        echo "  Title AR: {$podcast['title_ar']}\n";
        echo "  Title NL: {$podcast['title_nl']}\n";
        echo "  Cover Image: {$podcast['coverImage']}\n";
        echo "----------------------------------------\n";
    }

    echo "\n=== DATABASE CUSTOM_PAGES CHECK ===\n";
    $stmt = $db->query("SELECT id, title_en, title_ar, title_nl, slug_en, slug_ar, slug_nl FROM custom_pages");
    $cpages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cpages as $cp) {
        echo "ID: {$cp['id']}\n";
        echo "  Title EN: {$cp['title_en']}\n";
        echo "  Title AR: {$cp['title_ar']}\n";
        echo "  Title NL: {$cp['title_nl']}\n";
        echo "  Slug EN: {$cp['slug_en']}\n";
        echo "  Slug AR: {$cp['slug_ar']}\n";
        echo "  Slug NL: {$cp['slug_nl']}\n";
        echo "----------------------------------------\n";
    }
    
    echo "\n=== DATABASE MENUS CHECK ===\n";
    $stmt = $db->query("SELECT id, title_en, title_ar, title_nl FROM menus");
    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($menus as $menu) {
        echo "ID: {$menu['id']}\n";
        echo "  Title EN: {$menu['title_en']}\n";
        echo "  Title AR: {$menu['title_ar']}\n";
        echo "  Title NL: {$menu['title_nl']}\n";
        echo "----------------------------------------\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
