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
                    <li><a href="<?php echo url('about'); ?>" class="footer-link"><?php echo ($activeLang === 'ar' ? 'عن الموقع' : 'About'); ?></a></li>
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

    <?php
    $whatsappRaw = $settings['contact_whatsapp'] ?? '';
    $whatsappUrl = '';
    if (!empty($whatsappRaw)) {
        if (strpos($whatsappRaw, 'http://') === 0 || strpos($whatsappRaw, 'https://') === 0) {
            $whatsappUrl = $whatsappRaw;
        } else {
            $digitsOnly = preg_replace('/[^0-9]/', '', $whatsappRaw);
            if (!empty($digitsOnly)) {
                $whatsappUrl = 'https://wa.me/' . $digitsOnly;
            }
        }
    }

    $phoneRaw = $settings['contact_phone'] ?? '';
    $phoneUrl = '';
    if (!empty($phoneRaw)) {
        $cleanPhone = preg_replace('/[^0-9+]/', '', $phoneRaw);
        if (!empty($cleanPhone)) {
            $phoneUrl = 'tel:' . $cleanPhone;
        }
    }

    if (!empty($phoneUrl) || !empty($whatsappUrl)):
    ?>
    <div class="floating-contact-widget" role="complementary" aria-label="<?php echo ($activeLang === 'ar' ? 'أزرار الاتصال السريع' : 'Quick Contact Actions'); ?>">
        <?php if (!empty($phoneUrl)): ?>
            <a href="<?php echo e($phoneUrl); ?>" class="contact-floating-btn phone-btn" aria-label="<?php echo ($activeLang === 'ar' ? 'اتصل بنا عبر الهاتف' : 'Call us by phone'); ?>" tabindex="0">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
            </a>
        <?php endif; ?>
        
        <?php if (!empty($whatsappUrl)): ?>
            <a href="<?php echo e($whatsappUrl); ?>" class="contact-floating-btn whatsapp-btn" target="_blank" rel="noopener noreferrer" aria-label="<?php echo ($activeLang === 'ar' ? 'تواصل معنا عبر واتساب' : 'Chat with us on WhatsApp'); ?>" tabindex="0">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor" aria-hidden="true">
                    <path d="M12.012 2C6.48 2 2 6.48 2 12.012a9.9 9.9 0 001.374 5.03L2 22l5.124-1.344a9.92 9.92 0 004.888 1.356c5.532 0 10.012-4.48 10.012-10.012C22.024 6.48 17.544 2 12.012 2zm0 16.518a6.5 6.5 0 01-3.306-.9l-.234-.144-3.048.8.816-2.97-.156-.252a6.49 6.49 0 01-1-3.438c0-3.588 2.922-6.51 6.516-6.51h.006c3.588 0 6.51 2.922 6.51 6.516 0 3.588-2.922 6.51-6.51 6.51h-.004zm3.564-4.86c-.198-.102-1.17-.576-1.35-.642-.18-.066-.312-.102-.444.096-.132.198-.51.642-.624.774-.114.132-.228.15-.426.048-.198-.102-.84-.306-1.596-.984-.588-.528-.984-1.176-1.104-1.374-.114-.198-.012-.306.084-.402.09-.09.198-.228.3-.342.096-.114.132-.198.198-.33.066-.132.03-.252-.018-.354-.048-.102-.444-1.068-.606-1.464-.162-.384-.324-.33-.444-.336-.114-.006-.246-.006-.378-.006-.132 0-.348.048-.528.246-.18.198-.69.678-.69 1.656 0 .978.708 1.92.81 2.058.096.138 1.392 2.13 3.378 2.988.474.204.84.324 1.128.42.48.15.912.132 1.26-.018.384-.168 1.17-.48 1.338-.948.168-.468.168-.87.12-.948-.048-.078-.18-.126-.378-.228z"/>
                </svg>
            </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Global Application JS Scripts -->
    <script src="<?php echo BASE_URL; ?>/assets/js/app.js?v=<?php echo time(); ?>" defer></script>
</body>
</html>
