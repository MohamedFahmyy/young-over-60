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

// 7. Renders the luxury Post Card component (reusable card)
function renderPostCard($post) {
    $url = BASE_URL . '/posts/' . e($post['slug']);
    $categoryUrl = BASE_URL . '/category/' . e($post['categoryId']);
    $cover = !empty($post['coverImage']) ? e($post['coverImage']) : '/images/hero-bg.png';
    $published = formatDate($post['publishedAt']);
    $author = e($post['authorName'] ?? 'Site Admin');
    
    ?>
    <article class="post-card group" data-scroll-reveal>
        <a href="<?php echo $url; ?>" class="post-card-link" aria-label="Read <?php echo e($post['title']); ?>">
            <div class="post-card-media">
                <img src="<?php echo $cover; ?>" alt="<?php echo e($post['title']); ?>" loading="lazy" class="post-card-img" />
                <div class="post-card-badge">
                    <span class="badge-text"><?php echo e($post['categoryName'] ?? 'Category'); ?></span>
                </div>
            </div>
            
            <div class="post-card-body">
                <div class="post-card-meta">
                    <time datetime="<?php echo e($post['publishedAt']); ?>"><?php echo $published; ?></time>
                    <span class="meta-dot" aria-hidden="true"></span>
                    <span><?php echo $author; ?></span>
                </div>
                
                <h3 class="post-card-title"><?php echo e($post['title']); ?></h3>
                
                <p class="post-card-excerpt">
                    <?php echo e($post['excerpt'] ?? ''); ?>
                </p>
                
                <div class="post-card-cta">
                    <span class="cta-text">Read Story <span class="cta-arrow" aria-hidden="true">→</span></span>
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
                <a href="<?php echo BASE_URL; ?>/" class="breadcrumb-link">Home</a>
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
                        <span class="breadcrumb-current" aria-current="page"><?php echo e($title); ?></span>
                    <?php else: ?>
                        <a href="<?php echo e($url); ?>" class="breadcrumb-link"><?php echo e($title); ?></a>
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
    $siteName = $settings['siteName'] ?? 'Young Over 60';

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
?>
