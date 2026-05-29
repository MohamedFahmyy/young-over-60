<?php
// pages/contact.php
// Premium Split Layout Contact Form Page

$seoPageType = 'contact';

require_once PATH_ROOT . '/includes/header.php';
require_once PATH_ROOT . '/includes/navbar.php';
?>

<main class="container">
    <!-- Breadcrumbs -->
    <?php renderBreadcrumbs(['Contact Us' => '']); ?>

    <div class="contact-grid animate-in fade-in" data-scroll-reveal>
        
        <!-- Left Column: Branding & Contact Info -->
        <div class="contact-info-panel">
            <div>
                <h1 class="contact-info-title">Get In Touch</h1>
                <p class="contact-info-desc">
                    Have an inquiry, feedback about local accessibility features, or want to contribute your travel log? Fill out the coordinates and we will reach out soon.
                </p>

                <div class="contact-meta-list">
                    <!-- Email Item -->
                    <div class="contact-meta-item">
                        <div class="contact-icon-box">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="contact-meta-label">Email Us</p>
                            <p class="contact-meta-value">hello@youngover60.com</p>
                        </div>
                    </div>

                    <!-- WhatsApp Item -->
                    <div class="contact-meta-item">
                        <div class="contact-icon-box">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                        </div>
                        <div>
                            <p class="contact-meta-label">WhatsApp Number</p>
                            <p class="contact-meta-value">+1 (555) 000-Travel</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="contact-brand-footer">
                Travel without boundaries
            </div>
        </div>

        <!-- Right Column: Interactive Form Panel -->
        <div class="contact-form-panel">
            <div id="contactStatus" class="form-status-alert" style="display: none;"></div>
            
            <form id="contactForm" action="<?php echo BASE_URL; ?>/api/contact" method="POST" class="newsletter-form">
                <?php echo Auth::csrfInput(); ?>
                
                <div class="form-group">
                    <label for="form-name" class="form-label">Full Name</label>
                    <input type="text" id="form-name" name="name" class="form-input" placeholder="e.g. Jane Doe" required />
                </div>

                <div class="form-group">
                    <label for="form-email" class="form-label">Email Address</label>
                    <input type="email" id="form-email" name="email" class="form-input" placeholder="e.g. jane@example.com" required />
                </div>

                <div class="form-group">
                    <label for="form-phone" class="form-label">Phone Number</label>
                    <input type="text" id="form-phone" name="phone" class="form-input" placeholder="e.g. +61 400 000 000" required />
                </div>

                <div class="form-group">
                    <label for="form-message" class="form-label">Your Message</label>
                    <textarea id="form-message" name="message" class="form-input" rows="5" placeholder="Write your message here..." style="resize: vertical; font-family: inherit;" required></textarea>
                </div>

                <button type="submit" class="btn-primary">
                    Send Message
                </button>
            </form>
        </div>

    </div>
</main>

<?php 
require_once PATH_ROOT . '/includes/footer.php'; 
?>
