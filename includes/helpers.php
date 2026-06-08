<?php
// includes/helpers.php
// Reusable Helper Functions & UI Builders

// 1. Format date cleanly (e.g., May 25, 2026)
function formatDate($dateString) {
    if (!$dateString) return '';
    $timestamp = strtotime($dateString);
    return date('F d, Y', $timestamp);
}

// 2. Estimate article reading time (words per minute)
function calculateReadTime($content) {
    $cleanContent = strip_tags($content);
    $wordCount = str_word_count($cleanContent);
    $m = ceil($wordCount / 200);
    return $m . ' min read';
}

// 3. Generate a unique URL slug from text
function slugify($text) {
    // Replace non-letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // Transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // Remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    // Trim
    $text = trim($text, '-');
    // Remove duplicate -
    $text = preg_replace('~-+~', '-', $text);
    // Lowercase
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }
    return $text;
}

// 4. Generate/Retrieve secure CSRF Token
function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// 5. Verify posted CSRF Token
function verifyCsrf($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// 6. Escape HTML output to prevent XSS
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// 7. Media Library Metadata Fetcher
function getImageMetadata($url) {
    static $cache = [];
    if (empty($url)) return null;
    
    $relativeUrl = $url;
    if (strpos($url, BASE_URL) === 0) {
        $relativeUrl = substr($url, strlen(BASE_URL));
    }
    
    if (isset($cache[$relativeUrl])) {
        return $cache[$relativeUrl];
    }
    
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT width, height, alt_text_en, alt_text_ar FROM media WHERE url = :url LIMIT 1");
        $stmt->execute([':url' => $relativeUrl]);
        $meta = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($meta) {
            $cache[$relativeUrl] = $meta;
            return $meta;
        }
    } catch (Exception $e) {
        error_log("Failed to fetch media metadata: " . $e->getMessage());
    }
    
    // Fallback: getimagesize for local files
    $filePath = PATH_ROOT . $relativeUrl;
    if (file_exists($filePath)) {
        $info = @getimagesize($filePath);
        if ($info) {
            $meta = [
                'width' => $info[0],
                'height' => $info[1],
                'alt_text_en' => '',
                'alt_text_ar' => ''
            ];
            $cache[$relativeUrl] = $meta;
            return $meta;
        }
    }
    
    return null;
}

// 8. Reusable, Optimized Image Tag Builder
function renderImageTag($url, $defaultAlt = '', $class = '', $lazy = true) {
    if (empty($url)) return;
    
    $imgMeta = getImageMetadata($url);
    
    // Resolve alt text
    $alt = $defaultAlt;
    if ($imgMeta) {
        $metaAlt = t($imgMeta, 'alt_text');
        if (!empty($metaAlt)) {
            $alt = $metaAlt;
        }
    }
    
    $widthAttr = ($imgMeta && !empty($imgMeta['width'])) ? ' width="' . e($imgMeta['width']) . '"' : '';
    $heightAttr = ($imgMeta && !empty($imgMeta['height'])) ? ' height="' . e($imgMeta['height']) . '"' : '';
    $loadingAttr = $lazy ? ' loading="lazy"' : ' loading="eager"';
    $classAttr = !empty($class) ? ' class="' . e($class) . '"' : '';
    
    echo '<img src="' . e($url) . '" alt="' . e($alt) . '"' . $classAttr . $widthAttr . $heightAttr . $loadingAttr . ' />';
}

// 9. Renders the luxury Post Card component (reusable card)
function renderPostCard($post, $ratioType = 'landscape') {
    $url = url('posts/' . e(t($post, 'slug')));
    $categorySlug = t($post, 'categorySlug');
    $categoryUrl = url('category/' . e($categorySlug));
    $cover = !empty($post['coverImage']) ? e($post['coverImage']) : '/images/hero-bg.png';
    $published = formatDate($post['publishedAt']);
    $author = e(t($post, 'authorName') ?: ($post['authorName'] ?? 'Site Admin'));
    
    // Fallback alt
    $defaultAlt = t($post, 'alt_text') ?: t($post, 'title');
    
    $ratioClass = 'ratio-' . $ratioType;
    ?>
    <article class="post-card group ratio-card-<?php echo $ratioType; ?>" data-scroll-reveal>
        <a href="<?php echo $url; ?>" class="post-card-link" aria-label="<?php echo __('btn_read_story'); ?> <?php echo e(t($post, 'title')); ?>">
            <div class="post-card-media <?php echo $ratioClass; ?>">
                <div class="progressive-image-placeholder"></div>
                <?php renderImageTag($cover, $defaultAlt, 'post-card-img', true); ?>
                <div class="post-card-badge">
                    <span class="badge-text"><?php echo e(t($post, 'categoryName')); ?></span>
                </div>
            </div>
            
            <div class="post-card-body">
                <div class="post-card-meta">
                    <time datetime="<?php echo e($post['publishedAt']); ?>"><?php echo $published; ?></time>
                    <span class="meta-dot" aria-hidden="true"></span>
                    <span><?php echo $author; ?></span>
                </div>
                
                <h3 class="post-card-title"><?php echo e(t($post, 'title')); ?></h3>
                
                <p class="post-card-excerpt">
                    <?php echo e(t($post, 'excerpt')); ?>
                </p>
                
                <div class="post-card-cta">
                    <span class="cta-text"><?php echo __('btn_read_story'); ?> <span class="cta-arrow" aria-hidden="true"><?php echo isRTL() ? '←' : '→'; ?></span></span>
                </div>
            </div>
        </a>
    </article>
    <?php
}

// 8. Renders the dynamic Accessibility/WAI-ARIA compliant Breadcrumb Navigation
function renderBreadcrumbs($crumbs) {
    ?>
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <ol class="breadcrumbs-list">
            <li class="breadcrumb-item">
                <a href="<?php echo url('/'); ?>" class="breadcrumb-link"><?php echo __('nav_home'); ?></a>
            </li>
            <?php 
            $total = count($crumbs);
            $i = 0;
            foreach ($crumbs as $title => $url): 
                $i++;
                $isLast = ($i === $total);
                ?>
                <li class="breadcrumb-separator" aria-hidden="true">/</li>
                <li class="breadcrumb-item">
                    <?php if ($isLast): ?>
                        <span class="breadcrumb-current" aria-current="page"><?php echo e(__($title)); ?></span>
                    <?php else: ?>
                        <a href="<?php echo e($url ? url($url) : '#'); ?>" class="breadcrumb-link"><?php echo e(__($title)); ?></a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    </nav>
    <?php
}

// 9. Send plain text email notification to admin using native mail()
function sendAdminNotificationEmail($submission) {
    // Fetch site settings to get admin email
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT email FROM users WHERE role = 'ADMIN' LIMIT 1");
    $stmt->execute();
    $adminEmail = $stmt->fetchColumn();

    $pm = new PostManager();
    $settings = $pm->getSiteSettings();
    $siteName = t($settings, 'siteName') ?: ($settings['siteName'] ?? 'Young Over 60');

    if (!$adminEmail) {
        // Fallback email address
        $adminEmail = 'hello@youngover60.com';
    }

    $subject = "New Inquiry from " . $submission['name'] . " - " . $siteName;
    
    $message = "You have received a new contact submission:\n\n";
    $message .= "Name: " . $submission['name'] . "\n";
    $message .= "Email: " . $submission['email'] . "\n";
    $message .= "Phone: " . $submission['phone'] . "\n\n";
    $message .= "Message:\n" . $submission['message'] . "\n\n";
    $message .= "Submitted At: " . date('Y-m-d H:i:s') . "\n";

    $headers = "From: noreply@youngover60.com\r\n";
    $headers .= "Reply-To: " . $submission['email'] . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Send email using PHP native mail
    try {
        @mail($adminEmail, $subject, $message, $headers);
    } catch (Exception $e) {
        error_log("Failed to send contact notification email: " . $e->getMessage());
    }
}

// 10. Check if current layout is RTL
function isRTL() {
    if (!defined('CURRENT_LANG')) return false;
    $languages = defined('SUPPORTED_LANGUAGES') ? SUPPORTED_LANGUAGES : [];
    return isset($languages[CURRENT_LANG]) && $languages[CURRENT_LANG]['dir'] === 'rtl';
}

// 11. Translate dynamic record field with automatic fallback to English
function t($record, $field, $fallback = true) {
    if (!$record) return '';
    $lang = defined('CURRENT_LANG') ? CURRENT_LANG : 'en';
    
    // Check primary translation column (e.g. title_ar)
    $fieldLang = $field . '_' . $lang;
    if (isset($record[$fieldLang]) && $record[$fieldLang] !== '') {
        return $record[$fieldLang];
    }
    
    // Check default translation column (e.g. title_en)
    $fieldDefault = $field . '_' . DEFAULT_LANG;
    if (isset($record[$fieldDefault]) && $record[$fieldDefault] !== '') {
        return $record[$fieldDefault];
    }
    
    // Fallback to the non-suffixed field if it exists (e.g. title)
    if (isset($record[$field]) && $record[$field] !== '') {
        return $record[$field];
    }
    
    // Fallback to any other language in registry
    if ($fallback && defined('SUPPORTED_LANGUAGES')) {
        foreach (SUPPORTED_LANGUAGES as $lCode => $cfg) {
            $f = $field . '_' . $lCode;
            if (isset($record[$f]) && $record[$f] !== '') {
                return $record[$f];
            }
        }
    }
    
    return '';
}

// 12. Static dictionary translations with local caching
function __($key) {
    static $dictionary = [];
    $lang = defined('CURRENT_LANG') ? CURRENT_LANG : 'en';
    
    if (empty($dictionary[$lang])) {
        $filePath = PATH_ROOT . '/lang/' . $lang . '.php';
        if (file_exists($filePath)) {
            $dictionary[$lang] = include $filePath;
        } else {
            $dictionary[$lang] = [];
        }
    }
    
    return $dictionary[$lang][$key] ?? $key;
}

// 13. Smart URL helper pre-pending language prefix
function url($path = '') {
    $path = ltrim($path, '/');
    
    // Do not alter admin or api routing prefixes
    if (strpos($path, 'admin') === 0 || strpos($path, 'api') === 0) {
        return BASE_URL . '/' . $path;
    }
    
    $lang = defined('CURRENT_LANG') ? CURRENT_LANG : 'en';
    if ($lang !== DEFAULT_LANG) {
        return BASE_URL . '/' . $lang . '/' . $path;
    }
    return BASE_URL . '/' . $path;
}

// 14. Slugify bilingual strings (retaining Arabic characters)
function slugify_bilingual($text) {
    // If it contains Arabic characters, slugify accordingly
    if (preg_match('/[\x{0600}-\x{06FF}]/u', $text)) {
        // Replace spaces and special characters with hyphens
        $text = preg_replace('/[^\p{L}\p{N}]+/u', '-', $text);
        $text = trim($text, '-');
        return $text;
    }
    // ASCII fallback slugify
    return slugify($text);
}
?>
