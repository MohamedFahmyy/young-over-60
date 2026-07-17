<?php
// pages/custom-page.php
// Premium Dynamic Custom CMS Page Frontend Template

if (!defined('PATH_ROOT')) {
    define('PATH_ROOT', dirname(__DIR__));
}

require_once PATH_ROOT . '/config/database.php';
require_once PATH_ROOT . '/classes/Database.php';
require_once PATH_ROOT . '/classes/PostManager.php';
require_once PATH_ROOT . '/classes/PageManager.php';

// $customPageSlug is set by index.php router before including this file
$slug = $customPageSlug ?? ($_GET['slug'] ?? '');
if (empty($slug)) {
    header("HTTP/1.0 404 Not Found");
    require_once PATH_ROOT . '/pages/404.php';
    exit();
}

$pageMgr = new PageManager();

// Check for redirect
$redirectTo = $pageMgr->findRedirect($slug);
if ($redirectTo) {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: " . url('pages/' . $redirectTo));
    exit();
}

$page = $pageMgr->getPageBySlug($slug);
if (!$page) {
    header("HTTP/1.0 404 Not Found");
    require_once PATH_ROOT . '/pages/404.php';
    exit();
}

$lang = defined('CURRENT_LANG') ? CURRENT_LANG : 'en';

// Translation Integrity Validation
$isFallback = false;
$titleTranslation = $page['title_' . $lang] ?? '';
$contentTranslation = $page['content_' . $lang] ?? '';

if ($lang !== 'en' && (empty($titleTranslation) || empty($contentTranslation))) {
    $isFallback = true;
    error_log(sprintf('[Translation Missing] Page ID: %s has no translation for Lang: %s. Falling back to English.', $page['id'], $lang));
}

// Check for localized slug redirect (clean URL enforcement with Redirect Loop Protection)
$expectedSlug = $isFallback ? $page['slug_en'] : ($page['slug_' . $lang] ?? '');
if (!empty($expectedSlug) && $slug !== $expectedSlug) {
    $targetUrl = BASE_URL;
    if ($lang !== DEFAULT_LANG) {
        $targetUrl .= '/' . $lang;
    }
    $targetUrl .= '/pages/' . rawurlencode($expectedSlug);
    
    safeRedirect($targetUrl, $_GET, 301, 'Locale Page Redirect');
}

// Define localized URLs for the language switcher
$localizedUrls = [
    'en' => BASE_URL . '/pages/' . (!empty($page['slug_en']) ? $page['slug_en'] : $page['slug_en']),
    'ar' => BASE_URL . '/ar/pages/' . (!empty($page['slug_ar']) ? $page['slug_ar'] : $page['slug_en']),
    'nl' => BASE_URL . '/nl/pages/' . (!empty($page['slug_nl']) ? $page['slug_nl'] : $page['slug_en'])
];

if (!headers_sent()) {
    header('Vary: Accept-Language, Cookie');
}

// Get translated fields
$pageTitle      = $pageMgr->getField($page, 'title');
$pageContent    = $pageMgr->getField($page, 'content');
$pageExcerpt    = $pageMgr->getField($page, 'excerpt');
$heroTitle      = $pageMgr->getField($page, 'hero_title') ?: $pageTitle;
$heroSubtitle   = $pageMgr->getField($page, 'hero_subtitle');
$metaTitle      = $pageMgr->getField($page, 'meta_title') ?: ($pageTitle . ' | ' . ($settings['siteName'] ?? 'Young Over 60'));
$metaDesc       = $pageMgr->getField($page, 'meta_description') ?: $pageExcerpt;

$templateType   = $page['template_type'] ?? 'default';
$heroImage      = $page['hero_image'] ?? ($page['featured_image'] ?? '');
$featuredImage  = $page['featured_image'] ?? '';

// SEO configuration for header
$seoPageType = 'custom_page';
$seoPageData = $page;

$heroImageToPreload = !empty($heroImage) ? $heroImage : (!empty($featuredImage) ? $featuredImage : null);
if (!empty($heroImageToPreload) && !str_starts_with($heroImageToPreload, 'http') && !str_starts_with($heroImageToPreload, '//')) {
    $heroImageToPreload = BASE_URL . $heroImageToPreload;
}

require_once PATH_ROOT . '/includes/header.php';
require_once PATH_ROOT . '/includes/navbar.php';

// Replace placeholders in page content
$logoHtml = '';
if (!empty($settings['logoUrl'])) {
    $logoHtml = '<img src="' . BASE_URL . $settings['logoUrl'] . '" alt="' . htmlspecialchars(t($settings, 'siteName')) . '" class="about-logo" />';
}
$pageContent = str_replace('{logo}', $logoHtml, $pageContent);
?>

<?php // ====== HERO SECTION (if hero_image or hero_title set) ====== ?>
<?php if (!empty($heroImage) || !empty($heroTitle)): ?>
<section class="custom-page-hero" style="<?php if ($heroImage): ?>background-image: url('<?php echo e(BASE_URL . $heroImage); ?>');<?php endif; ?>" role="banner">
    <div class="custom-page-hero-overlay"></div>
    <div class="container custom-page-hero-content">
        <?php if ($heroTitle): ?>
            <h1 class="custom-page-hero-title" data-scroll-reveal><?php echo e($heroTitle); ?></h1>
        <?php endif; ?>
        <?php if ($heroSubtitle): ?>
            <p class="custom-page-hero-subtitle" data-scroll-reveal><?php echo e($heroSubtitle); ?></p>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<main class="<?php echo in_array($templateType, ['full-width', 'about']) ? '' : 'container'; ?> custom-page-main">

    <?php // Breadcrumbs (not for landing/full-width) ?>
    <?php if (!in_array($templateType, ['landing', 'full-width', 'about'])): ?>
    <?php
    $bcTitle = $pageMgr->getField($page, 'menu_title') ?: $pageTitle;
    renderBreadcrumbs([$bcTitle => '']);
    ?>
    <?php endif; ?>

    <?php // ====== DEFAULT / FULL WIDTH LAYOUT ====== ?>
    <?php if (in_array($templateType, ['default', 'full-width', ''])): ?>
    <article class="custom-page-content post-prose" data-scroll-reveal>
        <?php if (empty($heroImage) && empty($heroTitle)): ?>
            <h1 class="custom-page-standalone-title"><?php echo e($pageTitle); ?></h1>
        <?php endif; ?>
        <?php if ($featuredImage && $templateType !== 'full-width'): ?>
            <img src="<?php echo e(BASE_URL . $featuredImage); ?>" alt="<?php echo e($pageTitle); ?>" class="custom-page-featured-img" />
        <?php endif; ?>
        <div class="custom-page-prose"><?php echo $pageContent; // Trusted HTML from CMS admin ?></div>
    </article>

    <?php // ====== ABOUT US LAYOUT ====== ?>
    <?php elseif ($templateType === 'about'): ?>
    <article class="custom-page-about" data-scroll-reveal style="width: 100%; max-width: 100%; margin: 0; padding: 0;">
        <?php if ($featuredImage): ?>
        <div class="about-hero-img-wrap">
            <img src="<?php echo e(BASE_URL . $featuredImage); ?>" alt="<?php echo e($pageTitle); ?>" class="about-featured-img" loading="lazy" />
        </div>
        <?php endif; ?>
        <div class="about-prose post-prose" style="width: 100%; max-width: 100%; padding: 0;">
            <?php echo $pageContent; ?>
        </div>
    </article>

    <?php // ====== LANDING PAGE LAYOUT ====== ?>
    <?php elseif ($templateType === 'landing'): ?>
    <article class="custom-page-landing">
        <?php if ($featuredImage): ?>
        <div class="landing-cover">
            <img src="<?php echo e(BASE_URL . $featuredImage); ?>" alt="<?php echo e($pageTitle); ?>" loading="lazy" />
        </div>
        <?php endif; ?>
        <div class="container landing-prose post-prose" data-scroll-reveal>
            <?php echo $pageContent; ?>
        </div>
    </article>

    <?php // ====== MEDICAL TOURISM LAYOUT ====== ?>
    <?php elseif ($templateType === 'medical-tourism'): ?>
    <article class="custom-page-medical" data-scroll-reveal>
        <div class="medical-intro">
            <?php if ($pageExcerpt): ?>
            <p class="medical-lead"><?php echo e($pageExcerpt); ?></p>
            <?php endif; ?>
        </div>
        <?php if ($featuredImage): ?>
        <div class="medical-image-wrap">
            <img src="<?php echo e(BASE_URL . $featuredImage); ?>" alt="<?php echo e($pageTitle); ?>" class="medical-img" loading="lazy" />
        </div>
        <?php endif; ?>
        <div class="medical-content post-prose">
            <?php echo $pageContent; ?>
        </div>
    </article>

    <?php // ====== FAQ LAYOUT ====== ?>
    <?php elseif ($templateType === 'faq'): ?>
    <article class="custom-page-faq" data-scroll-reveal>
        <div class="faq-intro post-prose">
            <?php if ($pageExcerpt): ?>
            <p class="faq-lead"><?php echo e($pageExcerpt); ?></p>
            <?php endif; ?>
        </div>
        <div class="faq-content post-prose">
            <?php echo $pageContent; ?>
        </div>
    </article>

    <?php endif; ?>

</main>

<?php require_once PATH_ROOT . '/includes/footer.php'; ?>
