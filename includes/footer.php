<?php
// includes/footer.php
// Global Footer Component Layout Include

// Fetch categories for footer columns (with caching)
$db = Database::getInstance()->getConnection();
$activeLang = defined('CURRENT_LANG') ? CURRENT_LANG : 'en';

$pm = new PostManager();
$cacheKey = "footer_categories_lang_" . $activeLang;
$footerCats = $pm->cacheGet($cacheKey);

if (!$footerCats) {
    // Select categories that have posts under them or just some main regions
    $footerCatsQuery = $db->query("SELECT * FROM categories WHERE slug_en IN ('australia', 'europe', 'asia-pacific', 'americas') LIMIT 4");
    $footerCats = $footerCatsQuery->fetchAll();
    $pm->cacheSet($cacheKey, $footerCats);
}
?>
<?php
// Make sure site name and logo variables are present in footer scope
$siteTitle = t($settings, 'siteName') ?: ($settings['siteName'] ?? 'Young Over 60');
$logoUrl = $settings['logoUrl'] ?? null;
$logoDarkUrl = $settings['logoDarkUrl'] ?? null;
$titleParts = explode(' ', $siteTitle);
$firstWord = $titleParts[0] ?? '';
$remainingWords = implode(' ', array_slice($titleParts, 1));
?>
    <!-- 7. Newsletter Section -->
    <section class="newsletter-section" data-scroll-reveal>
        <div class="container newsletter-grid">
            <div class="intro-content">
                <span class="section-label"><?php echo __('news_label'); ?></span>
                <h3 class="serif-title"><?php echo __('news_title'); ?> <br><span class="italic"><?php echo __('news_title_italic'); ?></span></h3>
                <div class="intro-divider"></div>
                <p class="footer-desc" style="max-width: 100%; color: var(--text-color); opacity: 0.8;">
                    <?php echo __('news_desc'); ?>
                </p>
            </div>
            
            <div class="newsletter-form-container">
                <div id="newsletterStatus" class="form-status-alert" style="display: none;"></div>
                <form id="newsletterForm" action="<?php echo BASE_URL; ?>/api/newsletter" method="POST" class="newsletter-form">
                    <?php echo Auth::csrfInput(); ?>
                    <div class="form-group">
                        <input type="email" name="email" class="form-input" placeholder="<?php echo __('news_placeholder'); ?>" aria-label="<?php echo __('news_placeholder'); ?>" required />
                    </div>
                    <label class="form-checkbox-label">
                        <input type="checkbox" required />
                        <span><?php echo __('news_consent'); ?></span>
                    </label>
                    <button type="submit" class="btn-primary"><?php echo __('btn_subscribe'); ?></button>
                </form>
            </div>
        </div>
    </section>

    <!-- Global Footer -->
    <footer class="footer-section">
        <div class="container footer-grid">
            <!-- Brand Column -->
            <div class="footer-brand">
                <a href="<?php echo url('/'); ?>" class="logo-link logo-footer" aria-label="<?php echo __('nav_home'); ?>">
                    <?php if ($logoUrl || $logoDarkUrl): ?>
                        <div class="logo-container" style="display: flex; align-items: center; height: 3.5rem; width: 12rem; max-width: 100%;">
                            <?php if ($logoUrl): ?>
                                <img src="<?php echo e($logoUrl); ?>" alt="<?php echo e($siteTitle); ?>" class="logo-light" style="height:100%; width:100%; object-fit: contain; object-position: left;" />
                            <?php endif; ?>
                            <?php if ($logoDarkUrl): ?>
                                <img src="<?php echo e($logoDarkUrl); ?>" alt="<?php echo e($siteTitle); ?>" class="logo-dark" style="height:100%; width:100%; object-fit: contain; object-position: left; <?php echo $logoUrl ? 'display: none;' : ''; ?>" />
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <span class="logo-primary"><?php echo e($firstWord); ?></span>
                        <span class="logo-sub"><?php echo e($remainingWords); ?></span>
                    <?php endif; ?>
                </a>
                <p class="footer-desc">
                    <?php echo __('footer_about_desc'); ?>
                </p>
            </div>

            <!-- Destinations Columns -->
            <div>
                <h4 class="footer-title"><?php echo __('footer_dest_title'); ?></h4>
                <ul class="footer-links">
                    <?php foreach ($footerCats as $fCat): ?>
                        <li>
                            <a href="<?php echo url('category/' . e(t($fCat, 'slug'))); ?>" class="footer-link">
                                <?php echo e(t($fCat, 'name')); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Categories Columns -->
            <div>
                <h4 class="footer-title"><?php echo __('footer_explore_title'); ?></h4>
                <ul class="footer-links">
                    <li><a href="<?php echo url('news'); ?>" class="footer-link"><?php echo __('nav_news'); ?></a></li>
                    <li><a href="<?php echo url('accessibility'); ?>" class="footer-link"><?php echo __('nav_accessibility'); ?></a></li>
                    <li><a href="<?php echo url('contact'); ?>" class="footer-link"><?php echo __('nav_contact'); ?></a></li>
                    <li><a href="<?php echo url('admin/login'); ?>" class="footer-link"><?php echo __('btn_staff_login'); ?></a></li>
                </ul>
            </div>

            <!-- About Information -->
            <div>
                <h4 class="footer-title"><?php echo __('footer_access_title'); ?></h4>
                <p class="footer-desc" style="font-size: 0.8rem; line-height: 1.6;">
                    <?php echo __('footer_access_desc'); ?>
                </p>
            </div>
        </div>

        <div class="container footer-bottom">
            <p><?php echo sprintf(__('footer_copy'), date('Y'), e($siteTitle)); ?></p>
            <p><?php echo __('footer_tagline'); ?></p>
        </div>
    </footer>

    <!-- Global Application JS Scripts -->
    <script src="<?php echo BASE_URL; ?>/assets/js/app.js?v=<?php echo time(); ?>" defer></script>
</body>
</html>
