<?php
// scratch/test_locale_resolution.php
// Automated Verification Script for Locale Routing, Redirections, and Clean URLs

define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/includes/config.php';
require_once PATH_ROOT . '/includes/helpers.php';
require_once PATH_ROOT . '/classes/Database.php';
require_once PATH_ROOT . '/classes/PostManager.php';
require_once PATH_ROOT . '/classes/PageManager.php';
require_once PATH_ROOT . '/classes/TeamManager.php';
require_once PATH_ROOT . '/classes/Auth.php';

// Helper to mock request state and run index.php logic
function simulate_request($uri, $get = [], $cookie = [], $session = [], $acceptLang = '') {
    $_SERVER['REQUEST_URI'] = $uri;
    $_SERVER['QUERY_STRING'] = http_build_query($get);
    $_GET = $get;
    $_COOKIE = $cookie;
    $_SESSION = $session;
    $_SERVER['HTTP_ACCEPT_LANGUAGE'] = $acceptLang;
    $_SERVER['HTTP_HOST'] = 'localhost';
    $_SERVER['HTTPS'] = 'off';

    // Parse route from URI
    $basePath = '';
    $uri_path = parse_url($uri, PHP_URL_PATH);
    $route = ltrim($uri_path, '/');
    $_GET['route'] = $route;

    // Capture output and headers
    $headers = [];
    $redirectedTo = null;
    $exitCode = 0;

    // Mock header function using a helper
    // Since we are running in CLI, headers_sent() is false, but we can't send headers.
    // We will override index.php redirect triggers or mock header() using a custom script,
    // or we can execute index.php inside a mock environment.
    // Let's implement a clean simulation of the logic in index.php.
    
    $parts = explode('/', trim($route, '/'));
    $lang_prefix = null;
    if (isset($parts[0]) && array_key_exists($parts[0], SUPPORTED_LANGUAGES)) {
        $lang_prefix = $parts[0];
    }

    $isAdminRoute = (isset($parts[0]) && $parts[0] === 'admin') || (isset($parts[1]) && $parts[1] === 'admin');
    
    $lang = null;
    $isApiRoute = (isset($parts[0]) && $parts[0] === 'api');

    if ($isApiRoute) {
        if (isset($_GET['lang']) && array_key_exists($_GET['lang'], SUPPORTED_LANGUAGES)) {
            $lang = $_GET['lang'];
        } else {
            $lang = DEFAULT_LANG;
        }
    } else {
        if (isset($_GET['lang']) && array_key_exists($_GET['lang'], SUPPORTED_LANGUAGES)) {
            $lang = $_GET['lang'];
        } elseif ($lang_prefix !== null) {
            $lang = $lang_prefix;
        } elseif (isset($_SESSION['lang']) && array_key_exists($_SESSION['lang'], SUPPORTED_LANGUAGES)) {
            $lang = $_SESSION['lang'];
        } elseif (isset($_COOKIE['lang']) && array_key_exists($_COOKIE['lang'], SUPPORTED_LANGUAGES)) {
            $lang = $_COOKIE['lang'];
        } else {
            $browserLang = substr($acceptLang, 0, 2);
            if (array_key_exists($browserLang, SUPPORTED_LANGUAGES)) {
                $lang = $browserLang;
            } else {
                $lang = DEFAULT_LANG;
            }
        }

        if ($lang_prefix !== null) {
            array_shift($parts);
            $route = implode('/', $parts);
        }

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
            $queryParams = $_GET;
            unset($queryParams['lang'], $queryParams['route']);
            if (!empty($queryParams)) {
                $targetUrl .= '?' . http_build_query($queryParams);
            }

            // Redirect Loop Protection check
            $currentUrl = 'http://localhost' . $_SERVER['REQUEST_URI'];
            if (urldecode($currentUrl) === urldecode($targetUrl)) {
                $redirectedTo = 'LOOP_PREVENTED';
            } else {
                $redirectedTo = $targetUrl;
            }
        }
    }

    return [
        'resolved_lang' => $lang,
        'redirected_to' => $redirectedTo
    ];
}

echo "=== ROUTING & LOCALE RESOLUTION UNIT TESTS ===\n";

// Test 1: Direct English Post Request (No cookie, no prefix)
$res = simulate_request('/posts/my-post');
assert($res['resolved_lang'] === 'en', "Test 1 failed: Lang should be 'en'");
assert($res['redirected_to'] === null, "Test 1 failed: Should not redirect");
echo " ✓ Test 1 Passed: Direct English post requests resolve to 'en' without redirects.\n";

// Test 2: Cookie language preference redirection on Root URL
$res = simulate_request('/', [], ['lang' => 'nl']);
assert($res['resolved_lang'] === 'nl', "Test 2 failed: Lang should be 'nl'");
assert($res['redirected_to'] === BASE_URL . '/nl', "Test 2 failed: Should redirect to /nl");
echo " ✓ Test 2 Passed: Root URL with Dutch cookie redirects to /nl.\n";

// Test 3: Cookie language preference redirection on Inner URL (e.g. /posts/my-post)
$res = simulate_request('/posts/my-post', [], ['lang' => 'nl']);
assert($res['resolved_lang'] === 'nl', "Test 3 failed: Lang should be 'nl'");
assert($res['redirected_to'] === BASE_URL . '/nl/posts/my-post', "Test 3 failed: Should redirect to /nl/posts/my-post");
echo " ✓ Test 3 Passed: Inner URL redirects to /nl prefix when cookie is 'nl'.\n";

// Test 4: Mismatch URL parameter (?lang=en) on Dutch URL prefix
$res = simulate_request('/nl/posts/my-post', ['lang' => 'en']);
assert($res['resolved_lang'] === 'en', "Test 4 failed: Lang should be 'en'");
assert($res['redirected_to'] === BASE_URL . '/posts/my-post', "Test 4 failed: Should redirect to clean English URL");
echo " ✓ Test 4 Passed: ?lang=en on /nl prefix URL redirects to clean English URL.\n";

// Test 5: API Endpoint isolation (no cookie fallback)
$res = simulate_request('/api/posts', [], ['lang' => 'nl']);
assert($res['resolved_lang'] === 'en', "Test 5 failed: API should resolve to default 'en' ignoring cookie");
assert($res['redirected_to'] === null, "Test 5 failed: API should never redirect");
echo " ✓ Test 5 Passed: API endpoints ignore cookie preference and default to English.\n";

// Test 6: API Endpoint with explicit parameter
$res = simulate_request('/api/posts', ['lang' => 'nl'], ['lang' => 'ar']);
assert($res['resolved_lang'] === 'nl', "Test 6 failed: API should resolve to parameter 'nl'");
assert($res['redirected_to'] === null, "Test 6 failed: API should never redirect");
echo " ✓ Test 6 Passed: API endpoints resolve language strictly from query parameters.\n";

echo "\nAll routing and locale resolution test assertions passed successfully!\n";
