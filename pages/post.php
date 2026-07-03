<?php
// pages/post.php
// Individual Article Reader View

$startTime = microtime(true);

// 1. Fetch post by slug
$slug = $_GET['slug'] ?? '';
$post = $pm->getPostBySlug($slug, Auth::check());

if (!$post) {
    require_once PATH_ROOT . '/pages/404.php';
    exit();
}

$isAdmin = Auth::check();
$lang = defined('CURRENT_LANG') ? CURRENT_LANG : 'en';
$postVersion = strtotime($post['updatedAt'] ?? $post['createdAt'] ?? 'now');
$cacheFile = PATH_CACHE . '/post_html_' . $post['id'] . '_' . $lang . '_' . $postVersion . '.html';

if (!$isAdmin && file_exists($cacheFile)) {
    header('Cache-Control: public, max-age=300');
    header('X-Cache: HIT');
    readfile($cacheFile);
    error_log(sprintf('[Post Cache HIT] ID:%s LANG:%s TIME: %.2f ms', $post['id'], $lang, (microtime(true) - $startTime) * 1000));
    exit();
}

if (!$isAdmin) {
    header('X-Cache: MISS');
}

// Start buffering the output
ob_start();

// Pre-compute translated fields to optimize rendering speed
$postTitle = t($post, 'title');
$postContent = lazyLoadContentImages(t($post, 'content'));
$categorySlug = t($post, 'categorySlug');
$categoryName = t($post, 'categoryName');

// Set SEO Page Context
$seoPageType = 'post';
$seoPageData = $post;
$heroImageToPreload = !empty($post['coverImage']) ? $post['coverImage'] : '/images/hero-bg.png';
if (!empty($heroImageToPreload) && !str_starts_with($heroImageToPreload, 'http') && !str_starts_with($heroImageToPreload, '//')) {
    $heroImageToPreload = BASE_URL . $heroImageToPreload;
}

// 2. Fetch related posts (same category, limit 4, exclude current)
$relatedResult = $pm->getPosts([
    'categoryId' => $post['categoryId'],
    'status' => 'PUBLISHED',
    'limit' => 4
]);
$relatedPosts = array_filter($relatedResult['data'], function($p) use ($post) {
    return $p['id'] !== $post['id'];
});
$relatedPosts = array_slice($relatedPosts, 0, 3); // Take top 3

// 3. Inject IDs into headings to match TOC
function injectHeadingIds($content) {
    if (empty($content)) return '';
    $index = 0;
    return preg_replace_callback('/<(h[23])>(.*?)<\/\1>/i', function($matches) use (&$index) {
        $tag = $matches[1];
        $text = $matches[2];
        $id = strtolower(preg_replace('/[^\w\s-]/', '', $text));
        $id = preg_replace('/\s+/', '-', $id) . '-' . $index++;
        return "<{$tag} id=\"{$id}\">{$text}</{$tag}>";
    }, $content);
}

$processedContent = injectHeadingIds($postContent);

// Extract headings for Table of Contents sidebar
preg_match_all('/<(h[23])>(.*?)<\/\1>/i', $postContent, $headingsMatches, PREG_SET_ORDER);

require_once PATH_ROOT . '/includes/header.php';
require_once PATH_ROOT . '/includes/navbar.php';
?>



<!-- Article Hero -->
<section class="post-hero">
    <img src="<?php echo e(!empty($post['coverImage']) ? $post['coverImage'] : '/images/hero-bg.png'); ?>" alt="<?php echo e($postTitle); ?>" />
    <div class="post-hero-overlay"></div>
    
    <div class="container post-hero-content" data-scroll-reveal>
        <!-- Breadcrumbs inside Hero -->
        <nav aria-label="Breadcrumb" class="post-hero-breadcrumb">
            <ol class="breadcrumbs-list" style="color: rgba(255,255,255,0.7);">
                <li><a href="<?php echo BASE_URL; ?>/">Home</a></li>
                <li class="breadcrumb-separator">/</li>
                <li><a href="<?php echo BASE_URL; ?>/category/<?php echo e($categorySlug); ?>"><?php echo e($categoryName); ?></a></li>
            </ol>
        </nav>
        
        <h1 class="post-hero-title"><?php echo e($postTitle); ?></h1>
        
        <div class="post-meta-strip">
            <div class="post-meta-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span>By <?php echo e($post['authorName'] ?? 'Site Admin'); ?></span>
            </div>
            <div class="post-meta-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                <span><?php echo formatDate($post['publishedAt']); ?></span>
            </div>
            <div class="post-meta-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span><?php echo calculateReadTime($postContent); ?></span>
            </div>
        </div>
    </div>
</section>

<!-- Content Layout -->
<main class="container">
    <div class="post-layout-grid">
        
        <!-- Sidebar TOC -->
        <aside class="toc-sidebar">
            <h4 class="toc-title">Table of Contents</h4>
            <?php if (!empty($headingsMatches)): ?>
                <ul class="toc-list">
                    <?php 
                    $headingIdx = 0;
                    foreach ($headingsMatches as $h):
                        $text = $h[2];
                        $id = strtolower(preg_replace('/[^\w\s-]/', '', $text));
                        $id = preg_replace('/\s+/', '-', $id) . '-' . $headingIdx++;
                        ?>
                        <li>
                            <a href="#<?php echo $id; ?>" class="toc-item-link">
                                <?php echo e($text); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p style="font-size: 0.8rem; color:#999; font-style:italic;">No sections to outline.</p>
            <?php endif; ?>
        </aside>

        <!-- Main Body -->
        <div class="post-content-column">
            <!-- Content -->
            <article class="post-prose animate-in fade-in" data-scroll-reveal>
                <?php echo $processedContent; ?>
            </article>

            <!-- Social Share Bar -->
            <div class="social-share-box" data-scroll-reveal>
                <span class="share-label">Share Story</span>
                <div class="share-links">
                    <?php 
                    $pageUrl = urlencode(BASE_URL . $_SERVER['REQUEST_URI']); 
                    $pageTitle = urlencode($postTitle);
                    ?>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo $pageUrl; ?>&text=<?php echo $pageTitle; ?>" target="_blank" rel="noopener" class="share-btn">Twitter</a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $pageUrl; ?>" target="_blank" rel="noopener" class="share-btn">Facebook</a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $pageUrl; ?>&title=<?php echo $pageTitle; ?>" target="_blank" rel="noopener" class="share-btn">LinkedIn</a>
                    <a href="https://api.whatsapp.com/send?text=<?php echo $pageTitle . '%20' . $pageUrl; ?>" target="_blank" rel="noopener" class="share-btn">WhatsApp</a>
                </div>
            </div>

            <!-- Author Bio Box -->
            <div class="author-bio-box" data-scroll-reveal>
                <img src="<?php echo e(!empty($post['authorAvatar']) ? $post['authorAvatar'] : '/uploads/admin-avatar.png'); ?>" alt="<?php echo e($post['authorName']); ?>" class="author-avatar" />
                <div class="author-details">
                    <h4 class="author-name-title">About <?php echo e($post['authorName'] ?? 'Site Admin'); ?></h4>
                    <p class="author-bio-text">
                        <?php echo e(!empty($post['authorBio']) ? $post['authorBio'] : 'A passionate travel writer dedicated to making the world accessible for everyone.'); ?>
                    </p>
                    <div class="author-socials">
                        <?php if(!empty($post['authorWebsite'])): ?>
                            <a href="<?php echo e($post['authorWebsite']); ?>" target="_blank" rel="noopener">Website</a>
                        <?php endif; ?>
                        <?php if(!empty($post['authorTwitter'])): ?>
                            <a href="<?php echo e($post['authorTwitter']); ?>" target="_blank" rel="noopener">Twitter</a>
                        <?php endif; ?>
                        <?php if(!empty($post['authorFacebook'])): ?>
                            <a href="<?php echo e($post['authorFacebook']); ?>" target="_blank" rel="noopener">Facebook</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Related / Recommended Posts -->
    <?php if (!empty($relatedPosts)): ?>
        <section class="related-posts-section">
            <div class="related-posts-header-row" data-scroll-reveal>
                <h3 class="serif-title" style="margin: 0;">Recommended Reading</h3>
                <a href="<?php echo BASE_URL; ?>/category/<?php echo e($categorySlug); ?>" class="btn-underline">
                    Explore more in <?php echo e($categoryName); ?>
                </a>
            </div>
            
            <div class="story-grid">
                <?php foreach ($relatedPosts as $relPost): ?>
                    <?php renderPostCard($relPost, 'landscape'); ?>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</main>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        // TOC Tracking Scroll Script
        const links = document.querySelectorAll('.toc-item-link');
        const sections = Array.from(links).map(link => document.querySelector(link.getAttribute('href')));
        
        window.addEventListener('scroll', () => {
            let activeIdx = 0;
            const scrollPos = window.scrollY + 200;
            
            sections.forEach((sec, idx) => {
                if (sec && sec.offsetTop <= scrollPos) {
                    activeIdx = idx;
                }
            });
            
            links.forEach((link, idx) => {
                if (idx === activeIdx && sections[idx]) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        });


    });
</script>

<?php 
require_once PATH_ROOT . '/includes/footer.php'; 

$htmlOutput = ob_get_clean();

if (!$isAdmin && !empty($htmlOutput)) {
    $lockFile = $cacheFile . '.lock';
    $lockFp = fopen($lockFile, 'c');
    if ($lockFp) {
        if (flock($lockFp, LOCK_EX)) {
            if (!file_exists($cacheFile)) {
                file_put_contents($cacheFile, $htmlOutput);
                error_log(sprintf('[Post Cache WRITE] ID:%s LANG:%s', $post['id'], $lang));
            }
            flock($lockFp, LOCK_UN);
        }
        fclose($lockFp);
        @unlink($lockFile);
    }
}

echo $htmlOutput;

// Performance profiling log with details (MISS case)
error_log(sprintf(
    '[Post Cache MISS] ID:%s LANG:%s TIME: %.2f ms',
    $post['id'] ?? 'unknown',
    $lang,
    (microtime(true) - $startTime) * 1000
));
?>
