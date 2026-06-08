<?php
// index.php
// Pure PHP Front-Controller Router & Entry Point

// 1. Load Configurations & Helpers
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

// 2. Load Core Classes
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Auth.php';
require_once __DIR__ . '/classes/PostManager.php';
require_once __DIR__ . '/classes/UploadManager.php';
require_once __DIR__ . '/classes/SeoManager.php';
require_once __DIR__ . '/classes/PodcastManager.php';
require_once __DIR__ . '/classes/StoryManager.php';
require_once __DIR__ . '/classes/SliderManager.php';
require_once __DIR__ . '/classes/PageManager.php';

// 3. Parse Route Query (From .htaccess rewrite rules)
$route = $_GET['route'] ?? '';
$route = trim($route, '/');

$parts = explode('/', $route);

// First check if the route starts with a language prefix
$lang_prefix = null;
if (isset($parts[0]) && array_key_exists($parts[0], SUPPORTED_LANGUAGES)) {
    $lang_prefix = $parts[0];
}

// Determine if this is an admin route
$isAdminRoute = false;
if ($lang_prefix !== null) {
    $isAdminRoute = (isset($parts[1]) && $parts[1] === 'admin');
} else {
    $isAdminRoute = (isset($parts[0]) && $parts[0] === 'admin');
}

$lang = null;

if ($isAdminRoute) {
    // Admin language logic (fully independent of frontend language)
    if (isset($_GET['lang']) && array_key_exists($_GET['lang'], SUPPORTED_LANGUAGES)) {
        $lang = $_GET['lang'];
    } elseif ($lang_prefix !== null) {
        $lang = $lang_prefix;
    } elseif (isset($_COOKIE['admin_lang']) && array_key_exists($_COOKIE['admin_lang'], SUPPORTED_LANGUAGES)) {
        $lang = $_COOKIE['admin_lang'];
    } else {
        $lang = DEFAULT_LANG;
    }

    if (!headers_sent()) {
        setcookie('admin_lang', $lang, time() + 30 * 24 * 60 * 60, '/');
    }
    
    if ($lang_prefix !== null) {
        array_shift($parts); // Remove language prefix
        $route = implode('/', $parts);
    }
} else {
    // Frontend language logic
    if (isset($_GET['lang']) && array_key_exists($_GET['lang'], SUPPORTED_LANGUAGES)) {
        $lang = $_GET['lang'];
    } elseif ($lang_prefix !== null) {
        $lang = $lang_prefix;
    } elseif (isset($_COOKIE['lang']) && array_key_exists($_COOKIE['lang'], SUPPORTED_LANGUAGES)) {
        $lang = $_COOKIE['lang'];
    } else {
        $lang = DEFAULT_LANG;
    }

    if (!headers_sent()) {
        setcookie('lang', $lang, time() + 30 * 24 * 60 * 60, '/');
    }
    
    if ($lang_prefix !== null) {
        array_shift($parts); // Remove language prefix
        $route = implode('/', $parts);
    }
}

define('CURRENT_LANG', $lang);
$route_clean = $route;
$base = $parts[0] ?? '';

// Initialize PostManager and PageManager globally for all page templates
$pm = new PostManager();
$pageMgrGlobal = new PageManager();

// 4. Perform MVC-style page routing
if ($base === '' || $base === 'home') {
    // 4.1 Home Page
    require_once __DIR__ . '/pages/home.php';
} elseif ($base === 'destinations') {
    // 4.2 Destinations Page
    require_once __DIR__ . '/pages/destinations.php';
} elseif ($base === 'experiences') {
    // 4.3 Experiences Page
    require_once __DIR__ . '/pages/experiences.php';
} elseif ($base === 'news') {
    // 4.4 News Landing / Search Page
    require_once __DIR__ . '/pages/news.php';
} elseif ($base === 'accessibility') {
    // 4.5 Accessibility Compliance Page
    require_once __DIR__ . '/pages/accessibility.php';
} elseif ($base === 'contact') {
    // 4.6 Contact Page
    require_once __DIR__ . '/pages/contact.php';
} elseif ($base === 'podcasts') {
    // Podcasts Page
    require_once __DIR__ . '/pages/podcasts.php';
} elseif ($base === 'women-stories') {
    // Women Stories Page
    require_once __DIR__ . '/pages/women-stories.php';
} elseif ($base === 'category') {
    // 4.7 Category Archive
    $slug = $parts[1] ?? '';
    $_GET['slug'] = $slug;
    require_once __DIR__ . '/pages/category.php';
} elseif ($base === 'posts') {
    // 4.8 Single Article Page
    $slug = $parts[1] ?? '';
    $_GET['slug'] = $slug;
    require_once __DIR__ . '/pages/post.php';
} elseif ($base === 'pages') {
    // 4.9 Custom CMS Pages (frontend)
    $customPageSlug = $parts[1] ?? '';
    if (empty($customPageSlug)) {
        header("HTTP/1.0 404 Not Found");
        require_once __DIR__ . '/pages/404.php';
    } else {
        // Check slug redirect first
        $redirectSlug = $pageMgrGlobal->findRedirect($customPageSlug);
        if ($redirectSlug) {
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: " . BASE_URL . '/' . ($lang !== 'en' ? $lang . '/' : '') . 'pages/' . $redirectSlug);
            exit();
        }
        require_once __DIR__ . '/pages/custom-page.php';
    }
} elseif ($base === 'admin') {
    // 4.10 Admin Panel Subsections
    $action = $parts[1] ?? 'dashboard';
    
    if ($action === 'login') {
        require_once __DIR__ . '/admin/login.php';
    } elseif ($action === 'dashboard') {
        Auth::requireAdmin();
        require_once __DIR__ . '/admin/dashboard.php';
    } elseif ($action === 'add-post') {
        Auth::requireAdmin();
        require_once __DIR__ . '/admin/add-post.php';
    } elseif ($action === 'edit-post') {
        Auth::requireAdmin();
        $_GET['id'] = $parts[2] ?? '';
        require_once __DIR__ . '/admin/edit-post.php';
    } elseif ($action === 'manage-content') {
        Auth::requireAdmin();
        require_once __DIR__ . '/admin/manage-content.php';
    } elseif ($action === 'podcasts') {
        Auth::requireAdmin();
        require_once __DIR__ . '/admin/podcasts.php';
    } elseif ($action === 'women-stories') {
        Auth::requireAdmin();
        require_once __DIR__ . '/admin/women-stories.php';
    } elseif ($action === 'hero-slider') {
        Auth::requireAdmin();
        require_once __DIR__ . '/admin/hero-slider.php';
    } elseif ($action === 'media') {
        Auth::requireAdmin();
        require_once __DIR__ . '/admin/media.php';
    } elseif ($action === 'settings') {
        Auth::requireAdmin();
        require_once __DIR__ . '/admin/settings.php';
    } elseif ($action === 'pages') {
        Auth::requireAdmin();
        require_once __DIR__ . '/admin/pages.php';
    } elseif ($action === 'add-page') {
        Auth::requireAdmin();
        require_once __DIR__ . '/admin/add-page.php';
    } elseif ($action === 'edit-page') {
        Auth::requireAdmin();
        $_GET['id'] = $parts[2] ?? '';
        require_once __DIR__ . '/admin/edit-page.php';
    } else {
        header("HTTP/1.0 404 Not Found");
        require_once __DIR__ . '/pages/404.php';
    }
} elseif ($base === 'api') {
    // 4.10 AJAX JSON APIs
    $endpoint = $parts[1] ?? '';
    
    // Set headers
    header("Content-Type: application/json; charset=UTF-8");
    
    // Route API request
    if ($endpoint === 'categories') {
        require_once __DIR__ . '/api/categories.php';
    } elseif ($endpoint === 'posts') {
        require_once __DIR__ . '/api/posts.php';
    } elseif ($endpoint === 'contact') {
        require_once __DIR__ . '/api/contact.php';
    } elseif ($endpoint === 'newsletter') {
        require_once __DIR__ . '/api/newsletter.php';
    } elseif ($endpoint === 'upload') {
        // CSRF and Admin checks applied inside the endpoint files
        require_once __DIR__ . '/api/upload.php';
    } else {
        header("HTTP/1.0 404 Not Found");
        echo json_encode(['success' => false, 'error' => 'API endpoint not found']);
    }
} elseif ($route === 'sitemap.xml' || $route === 'sitemap-index.xml') {
    // Dynamic sitemap index XML
    require_once __DIR__ . '/sitemaps/index.php';
} elseif ($route === 'sitemap-pages.xml') {
    require_once __DIR__ . '/sitemaps/pages.php';
} elseif ($route === 'sitemap-posts.xml') {
    require_once __DIR__ . '/sitemaps/posts.php';
} elseif ($route === 'sitemap-podcasts.xml') {
    require_once __DIR__ . '/sitemaps/podcasts.php';
} elseif ($route === 'sitemap-stories.xml') {
    require_once __DIR__ . '/sitemaps/stories.php';
} else {
    // Before giving up with a 404, check if the clean route matches a root-level custom CMS page slug
    $checkPage = $pageMgrGlobal->getPageBySlug($base);
    if ($checkPage) {
        $customPageSlug = $base;
        require_once __DIR__ . '/pages/custom-page.php';
    } else {
        // 4.12 Not Found Fallback
        header("HTTP/1.0 404 Not Found");
        require_once __DIR__ . '/pages/404.php';
    }
}
