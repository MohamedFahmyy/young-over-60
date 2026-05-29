<?php
// pages/accessibility.php
// Accessibility Policies and Dynamic Interactive Controls Page

$seoPageType = 'accessibility';

require_once PATH_ROOT . '/includes/header.php';
require_once PATH_ROOT . '/includes/navbar.php';
?>

<main class="container">
    <!-- Breadcrumbs -->
    <?php renderBreadcrumbs(['Accessibility Policy' => '']); ?>

    <section class="accessibility-container">
        <div class="mb-12" data-scroll-reveal>
            <span class="section-label">Our Commitment</span>
            <h1 class="serif-title">Accessibility at <?php echo e($settings['siteName'] ?? 'Young Over 60'); ?></h1>
            <p style="font-size: 1.15rem; font-weight: 300; opacity: 0.85;">
                We are dedicated to ensuring that our digital resources and editorial stories are accessible to travellers with diverse requirements. We design our structures around W3C WCAG 2.1 compliance criteria and welcome feedback on any barriers encountered.
            </p>
        </div>

        <!-- Custom Widget Toggles Page Integration -->
        <div class="accessibility-card-box" data-scroll-reveal>
            <h2 class="serif-title" style="margin-bottom: 1.5rem;">Visual & Display Settings</h2>
            <p style="font-size: 0.95rem; margin-bottom: 2rem; font-weight: 300; opacity: 0.85;">
                Customize the layout and text rendering of the platform using these toggles. Your settings are instantly applied and saved to your device.
            </p>
            
            <div class="widget-toggles">
                <button class="toggle-card" data-toggle="readableFont" aria-label="Toggle Arial readable font font-face">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span class="toggle-card-label">Readable Font</span>
                </button>
                <button class="toggle-card" data-toggle="highContrast" aria-label="Toggle high contrast black and blue visual theme">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    <span class="toggle-card-label">Contrast Mode</span>
                </button>
                <button class="toggle-card" data-toggle="underlineLinks" aria-label="Toggle underlines under links">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                    <span class="toggle-card-label">Underline Links</span>
                </button>
                <button class="toggle-card" data-toggle="highlightLinks" aria-label="Toggle highlighter backgrounds under links">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    <span class="toggle-card-label">Highlight Links</span>
                </button>
            </div>
            
            <div class="accessibility-actions">
                <button class="btn-primary" data-open-accessibility>Open Size Controls Panel</button>
                <button class="btn-reset" data-accessibility-action="reset">Reset to Default Theme</button>
            </div>
        </div>

        <!-- Compliance Text Details -->
        <div class="post-prose" data-scroll-reveal>
            <h2>Design Principles</h2>
            <p>
                To provide a high-contrast and readable design, we adhere to the following principles:
            </p>
            <ul>
                <li><strong>Typography scale</strong>: Flexible text size parameters with five preconfigured steps.</li>
                <li><strong>Keyboard navigation</strong>: Complete index order tab-stops across all dropdown lists, overlay modals, and inputs.</li>
                <li><strong>ARIA Labels</strong>: Logical roles and descriptive helper fields are loaded for screen reader compatibility.</li>
                <li><strong>Dynamic overlays</strong>: Search overlays and drawer overlays trigger focus captures for stability.</li>
            </ul>

            <h2>Get in Touch</h2>
            <p>
                If you encounter any issues accessing any part of this platform, please reach out to us at <a href="mailto:hello@youngover60.com">hello@youngover60.com</a> or use our coordinates on the <a href="<?php echo BASE_URL; ?>/contact">Contact Page</a>. We actively review all submissions.
            </p>
        </div>
    </section>
</main>

<?php 
require_once PATH_ROOT . '/includes/footer.php'; 
?>
