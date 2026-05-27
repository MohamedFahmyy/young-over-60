<?php
// includes/footer.php
// Global Footer Component Layout Include

// Fetch categories for footer columns
$db = Database::getInstance()->getConnection();
$footerCatsQuery = $db->query("SELECT * FROM categories ORDER BY name ASC LIMIT 8");
$footerCats = $footerCatsQuery->fetchAll();
?>
    <!-- 7. Newsletter Section -->
    <section class="newsletter-section" data-scroll-reveal>
        <div class="container newsletter-grid">
            <div class="intro-content">
                <span class="section-label">Newsletter</span>
                <h3 class="serif-title">Stay Connected to <br><span class="italic">The Wonder</span></h3>
                <div class="intro-divider"></div>
                <p class="footer-desc" style="max-width: 100%; color: #666666;">
                    Subscribe to receive seasonal itineraries, accessibility updates, and guides curated for travellers with diverse requirements.
                </p>
            </div>
            
            <div class="newsletter-form-container">
                <div id="newsletterStatus" class="form-status-alert" style="display: none;"></div>
                <form id="newsletterForm" action="<?php echo BASE_URL; ?>/api/newsletter" method="POST" class="newsletter-form">
                    <?php echo Auth::csrfInput(); ?>
                    <div class="form-group">
                        <input type="email" name="email" class="form-input" placeholder="Your email address" aria-label="Newsletter email address" required />
                    </div>
                    <label class="form-checkbox-label">
                        <input type="checkbox" required />
                        <span>I consent to receiving emails and agree to the travel privacy policy.</span>
                    </label>
                    <button type="submit" class="btn-primary">Subscribe</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Global Footer -->
    <footer class="footer-section">
        <div class="container footer-grid">
            <!-- Brand Column -->
            <div class="footer-brand">
                <a href="<?php echo BASE_URL; ?>/" class="logo-link logo-footer" aria-label="Go to homepage">
                    <span class="logo-primary"><?php echo e($firstWord); ?></span>
                    <span class="logo-sub"><?php echo e($remainingWords); ?></span>
                </a>
                <p class="footer-desc">
                    Unlocking the world's most beautiful destinations for everyone, regardless of physical or sensory requirements.
                </p>
            </div>

            <!-- Destinations Columns -->
            <div>
                <h4 class="footer-title">Destinations</h4>
                <ul class="footer-links">
                    <li><a href="<?php echo BASE_URL; ?>/category/australia" class="footer-link">Australia</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/category/europe" class="footer-link">Europe</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/category/asia-pacific" class="footer-link">Asia Pacific</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/category/americas" class="footer-link">Americas</a></li>
                </ul>
            </div>

            <!-- Categories Columns -->
            <div>
                <h4 class="footer-title">Explore</h4>
                <ul class="footer-links">
                    <li><a href="<?php echo BASE_URL; ?>/news" class="footer-link">Latest Stories</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/accessibility" class="footer-link">Accessibility</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/contact" class="footer-link">Get In Touch</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/admin/login" class="footer-link">Staff Login</a></li>
                </ul>
            </div>

            <!-- About Information -->
            <div>
                <h4 class="footer-title">Access Policy</h4>
                <p class="footer-desc" style="font-size: 0.8rem; line-height: 1.6;">
                    Our content is mapped to dynamic accessibility standards. If you find any barriers or want to submit feedback, please use our contact coordinates.
                </p>
            </div>
        </div>

        <div class="container footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php echo e($siteTitle); ?>. All Rights Reserved. Rebuilt in Pure PHP.</p>
            <p>Designed with accessibility & visual excellence.</p>
        </div>
    </footer>

    <!-- Global Application JS Scripts -->
    <script src="<?php echo BASE_URL; ?>/assets/js/app.js?v=<?php echo time(); ?>" defer></script>
</body>
</html>
