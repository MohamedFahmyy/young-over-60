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
require_once __DIR__ . '/classes/TeamManager.php';

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
    $lang = null;
    
    // Check if the current route is an API route
    $isApiRoute = (isset($parts[0]) && $parts[0] === 'api');

    if ($isApiRoute) {
        // API routes: resolve strictly by ?lang= query param, no cookies, no redirects
        if (isset($_GET['lang']) && array_key_exists($_GET['lang'], SUPPORTED_LANGUAGES)) {
            $lang = $_GET['lang'];
        } else {
            $lang = DEFAULT_LANG;
        }
    } else {
        // 1. Check query parameter lang (e.g. ?lang=)
        if (isset($_GET['lang']) && array_key_exists($_GET['lang'], SUPPORTED_LANGUAGES)) {
            $lang = $_GET['lang'];
        }
        // 2. Check URL Prefix
        elseif ($lang_prefix !== null) {
            $lang = $lang_prefix;
        }
        // 3. Check Session
        elseif (isset($_SESSION['lang']) && array_key_exists($_SESSION['lang'], SUPPORTED_LANGUAGES)) {
            $lang = $_SESSION['lang'];
        }
        // 4. Check Cookie
        elseif (isset($_COOKIE['lang']) && array_key_exists($_COOKIE['lang'], SUPPORTED_LANGUAGES)) {
            $lang = $_COOKIE['lang'];
        }
        // 5. Fallback to Browser Accept-Language
        else {
            $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '', 0, 2);
            if (array_key_exists($browserLang, SUPPORTED_LANGUAGES)) {
                $lang = $browserLang;
            } else {
                $lang = DEFAULT_LANG;
            }
        }

        // Save active language
        $_SESSION['lang'] = $lang;
        if (!headers_sent()) {
            setcookie('lang', $lang, time() + 30 * 24 * 60 * 60, '/');
            header('Vary: Accept-Language, Cookie');
        }

        // Clean route if prefix is in URL
        if ($lang_prefix !== null) {
            array_shift($parts); // Remove prefix
            $route = implode('/', $parts);
        }

        // Redirect Enforcement for Clean URLs
        $route_clean = $route;
        $expectedPrefix = ($lang === DEFAULT_LANG) ? null : $lang;

        if ($lang_prefix !== $expectedPrefix) {
            $cleanRoute = ltrim($route_clean, '/');
            $targetUrl = BASE_URL;
            if ($lang !== DEFAULT_LANG) {
                $targetUrl .= '/' . $lang;
            }
            if ($cleanRoute !== '') {
                $targetUrl .= '/' . $cleanRoute;
            }

            // Preserve query string (exclude lang)
            $queryParams = $_GET;
            unset($queryParams['lang']);
            if (!empty($queryParams)) {
                $targetUrl .= '?' . http_build_query($queryParams);
            }

            // Redirect Loop Protection
            $currentUrl = (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off' ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            if (urldecode($currentUrl) === urldecode($targetUrl)) {
                error_log("[Redirect Loop Prevented] Attempted loop redirect to identical URL: " . $targetUrl);
            } else {
                error_log(sprintf(
                    '[Locale Redirect] Redirecting from %s to %s | Resolved: %s',
                    $currentUrl,
                    $targetUrl,
                    $lang
                ));
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: " . $targetUrl);
                exit();
            }
        }
    }

    // Diagnostic locale resolving log
    error_log(sprintf(
        '[Locale Resolve] URL: %s | Prefix: %s | Cookie: %s | Session: %s | Resolved: %s',
        $_SERVER['REQUEST_URI'] ?? '',
        $lang_prefix ?? 'none',
        $_COOKIE['lang'] ?? 'none',
        $_SESSION['lang'] ?? 'none',
        $lang
    ));
}

define('CURRENT_LANG', $lang);
$route_clean = $route;
$base = $parts[0] ?? '';

// Initialize PostManager and PageManager globally for all page templates
$pm = new PostManager();
$pageMgrGlobal = new PageManager();
$teamMgr = new TeamManager();

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
} elseif ($base === 'about') {
    // 4.7 About Us Page
    require_once __DIR__ . '/pages/about.php';
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
    } elseif ($action === 'about-page') {
        Auth::requireAdmin();
        require_once __DIR__ . '/admin/about-page.php';
    } elseif ($action === 'reset-db') {
        Auth::requireAdmin();
        require_once __DIR__ . '/admin/reset-db.php';
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
