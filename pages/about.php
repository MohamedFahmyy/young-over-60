<?php
// pages/about.php
// About Us Page — YoungOver60

$seoPageType = 'about';

// Load about-page content from site_settings (populated by admin/about-page.php)
// Merge with defaults so the page works before the migration has run.
$_rawSettings = $pm->getSiteSettings();
$abt = array_merge([
    'about_hero_label'              => 'Our Story',
    'about_hero_heading_line1'      => 'Beyond',
    'about_hero_heading_accent'     => ' 60,',
    'about_hero_heading_line2'      => 'Life Begins Again.',
    'about_hero_quote'              => 'Life Begins Again.',
    'about_hero_desc'               => 'The stories, people, and passion behind a community that believes every new chapter can become the greatest adventure yet. We celebrate curiosity, connection, and meaningful journeys at every stage of life.',
    'about_hero_desc_mobile'        => 'A community celebrating curiosity, connection, and meaningful journeys at every stage of life.',
    'about_hero_btn1_text'          => 'Discover Our Story →',
    'about_hero_btn2_text'          => 'Meet The Team',
    'about_hero_badge_label'        => 'Stories Shared',
    'about_hero_badge_number'       => '500+',
    'about_hero_image'              => '/assets/images/about-header.jpeg',
    'about_vision_label'            => 'About Young Over 60',
    'about_vision_heading'          => 'Travel Has',
    'about_vision_heading_accent'   => 'No Age Limit',
    'about_vision_quote'            => 'Beyond 60, life begins again.',
    'about_vision_card1_title'      => 'Young Over 60',
    'about_vision_card1_text1'      => 'We believe that travel is neither a luxury nor a privilege reserved for a specific age group. It is a fundamental right for everyone, regardless of age or physical ability.',
    'about_vision_card1_text2'      => 'Travel opens windows to the world\'s beauty, creates connections between cultures, and contributes to both mental and physical wellbeing.',
    'about_vision_card2_title'      => 'Our Vision: Travel Without Boundaries',
    'about_vision_card2_text1'      => 'We strive to make travel accessible, comfortable, and enjoyable for everyone over 60 and for individuals with special needs.',
    'about_vision_card2_text2'      => 'Our mission is to challenge outdated stereotypes and prove that adventure, curiosity, and exploration have no expiration date.',
    'about_stat1_number'            => '60+',
    'about_stat1_desc'              => 'Celebrating life beyond traditional limits.',
    'about_stat2_number'            => '∞',
    'about_stat2_desc'              => 'Endless opportunities to discover the world.',
    'about_stat3_number'            => '100%',
    'about_stat3_desc'              => 'Dedicated to accessible and inclusive travel.',
    'about_phili_label'             => 'Our Philosophy',
    'about_phili_heading'           => 'Life Begins',
    'about_phili_heading_accent'    => 'After 60',
    'about_phili_quote'             => 'True youth lives in a curious mind and an adventurous spirit.',
    'about_phili_text1'             => 'We reject the idea of reducing a person to the number on their passport. Sixty is not the end of the road—it is a new beginning, filled with opportunities for discovery, growth, and unforgettable experiences.',
    'about_phili_text2'             => 'We believe that true youth is not measured by age, but by the ability to remain curious, open-minded, and eager to explore the world.',
    'about_phili_text3'             => 'At Young Over 60, every traveller has a story worth telling and every journey is a chance to rediscover life. The world is far too beautiful to be viewed only from a window, and your right to travel never expires.',
    'about_founder_name'            => 'Zakaria Dawoud',
    'about_founder_role'            => 'Founder & Project CEO',
    'about_founder_linkedin'        => 'https://www.linkedin.com/in/zakaria-dawoud-26902b180?utm_source=share_via&utm_content=profile&utm_medium=member_android',
    'about_founder_quote'           => 'Beyond 60, life begins again. This project was built to prove that travel has no age limit.',
    'about_founder_image'           => '/assets/images/founder.jpeg',
    'about_contact_heading'         => "Let's Build Something",
    'about_contact_heading_accent'  => 'Meaningful',
    'about_contact_desc'            => 'Whether you have a question, collaboration idea, or just want to say hello — feel free to reach out.',
], $_rawSettings ?? []);

$teamMgr = new TeamManager();
$activeTeamMembers = $teamMgr->getTeamMembers(false);

require_once PATH_ROOT . '/includes/header.php';
require_once PATH_ROOT . '/includes/navbar.php';
?>

<!-- ═══════════════════════════════════════════
     SECTION 1 — HERO
══════════════════════════════════════════════ -->
<section class="about-hero-section">
    <!-- Decorative blobs -->
    <div class="about-blob about-blob--gold-tl"></div>
    <div class="about-blob about-blob--navy-br"></div>
    <div class="about-blob about-blob--gold-circle-tl"></div>
    <div class="about-blob about-blob--ring-tr"></div>
    <div class="about-blob about-blob--circle-bl"></div>
    <div class="about-blob about-blob--ring-br"></div>
    <div class="about-blob about-blob--ring-mid"></div>

    <div class="about-hero-inner">
        <!-- Left: Text -->
        <div class="about-hero-text">
            <span class="about-label">
                <?php echo e($abt['about_hero_label']); ?>
                <span class="about-label-line"></span>
            </span>

            <h1 class="about-hero-heading">
                <?php echo e($abt['about_hero_heading_line1']); ?>
                <span class="about-heading-accent italic"><?php echo e($abt['about_hero_heading_accent']); ?></span><br>
                <?php echo e($abt['about_hero_heading_line2']); ?>
            </h1>

            <div class="about-hero-quote">
                <?php echo e($abt['about_hero_quote']); ?>
            </div>

            <p class="about-hero-desc desktop-only">
                <?php echo e($abt['about_hero_desc']); ?>
            </p>
            <p class="about-hero-desc mobile-only">
                <?php echo e($abt['about_hero_desc_mobile']); ?>
            </p>

            <div class="about-hero-cta-row">
                <a href="#our-story" class="about-btn-primary">
                    <?php echo e($abt['about_hero_btn1_text']); ?>
                </a>
                <a href="#team" class="about-btn-outline">
                    <?php echo e($abt['about_hero_btn2_text']); ?>
                </a>
            </div>
        </div>

        <!-- Right: Image -->
        <div class="about-hero-image-wrap">
            <!-- Spinning dashed ring SVG -->
            <svg class="about-hero-spin-ring" viewBox="0 0 600 600" fill="none" aria-hidden="true">
                <path
                    d="M140 50C260 -10 490 40 540 210C590 380 500 560 300 560C100 560 20 390 40 240C60 120 90 80 140 50Z"
                    stroke="currentColor"
                    stroke-width="4"
                    stroke-dasharray="10 10"
                />
            </svg>

            <div class="about-hero-photo-frame">
                <img
                    src="<?php echo BASE_URL . e($abt['about_hero_image']); ?>"
                    alt="About Young Over 60"
                    class="about-hero-photo"
                    loading="eager"
                />
            </div>

            <!-- Floating badge -->
            <div class="about-hero-badge desktop-only">
                <p class="about-hero-badge-label"><?php echo e($abt['about_hero_badge_label']); ?></p>
                <h3 class="about-hero-badge-number"><?php echo e($abt['about_hero_badge_number']); ?></h3>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     SECTION 2 — VISION / OUR STORY
══════════════════════════════════════════════ -->
<section id="our-story" class="about-vision-section">
    <!-- Decorative -->
    <div class="about-blob about-blob--vision-tl"></div>
    <div class="about-blob about-blob--vision-br"></div>

    <div class="about-vision-inner">
        <!-- Header -->
        <div class="about-vision-header">
            <span class="about-label">
                <?php echo e($abt['about_vision_label']); ?>
                <span class="about-label-line"></span>
            </span>
            <h2 class="about-section-heading">
                <?php echo e($abt['about_vision_heading']); ?>
                <span class="about-heading-accent italic"> <?php echo e($abt['about_vision_heading_accent']); ?></span>
            </h2>
            <p class="about-vision-quote">
                <?php echo e($abt['about_vision_quote']); ?>
            </p>
        </div>

        <!-- Content Grid -->
        <div class="about-vision-grid">
            <!-- Left: Cards -->
            <div class="about-vision-cards">
                <div class="about-vision-card about-vision-card--light">
                    <h3 class="about-vision-card-title"><?php echo e($abt['about_vision_card1_title']); ?></h3>
                    <p class="about-vision-card-text">
                        <?php echo e($abt['about_vision_card1_text1']); ?>
                    </p>
                    <p class="about-vision-card-text" style="margin-top:1.25rem;">
                        <?php echo e($abt['about_vision_card1_text2']); ?>
                    </p>
                </div>

                <div class="about-vision-card about-vision-card--dark">
                    <div class="about-vision-card-ring"></div>
                    <h3 class="about-vision-card-title"><?php echo e($abt['about_vision_card2_title']); ?></h3>
                    <p class="about-vision-card-subtext">
                        <?php echo e($abt['about_vision_card2_text1']); ?>
                    </p>
                    <p class="about-vision-card-subtext" style="margin-top:1.25rem;">
                        <?php echo e($abt['about_vision_card2_text2']); ?>
                    </p>
                </div>
            </div>

            <!-- Right: Stats -->
            <div class="about-stats-col">
                <div class="about-stat-card">
                    <h4 class="about-stat-number about-stat-number--gold"><?php echo e($abt['about_stat1_number']); ?></h4>
                    <p class="about-stat-desc"><?php echo e($abt['about_stat1_desc']); ?></p>
                </div>
                <div class="about-stat-card">
                    <h4 class="about-stat-number about-stat-number--navy"><?php echo e($abt['about_stat2_number']); ?></h4>
                    <p class="about-stat-desc"><?php echo e($abt['about_stat2_desc']); ?></p>
                </div>
                <div class="about-stat-card">
                    <h4 class="about-stat-number about-stat-number--gold"><?php echo e($abt['about_stat3_number']); ?></h4>
                    <p class="about-stat-desc"><?php echo e($abt['about_stat3_desc']); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     SECTION 3 — WHAT WE OFFER
══════════════════════════════════════════════ -->
<section class="about-offer-section">
    <!-- Decorative -->
    <div class="about-blob about-blob--offer-tl"></div>
    <div class="about-blob about-blob--offer-tr"></div>

    <div class="about-offer-inner">
        <!-- Header -->
        <div class="about-offer-header">
            <span class="about-label about-label--gold">What We Offer</span>
            <h2 class="about-section-heading about-section-heading--white">
                Everything You Need To
                <span class="about-heading-accent italic"> Travel Confidently</span>
            </h2>
            <p class="about-offer-desc desktop-only">
                Carefully curated resources, inspiring stories, and practical guidance designed to help
                travellers over 60 and individuals with special needs explore the world comfortably and confidently.
            </p>
            <p class="about-offer-desc mobile-only">
                We provide resources, stories, and guidance for comfortable and confident travel.
            </p>
        </div>

        <!-- Cards Grid (Desktop) -->
        <div class="about-offer-grid desktop-only">
            <div class="about-offer-card">
                <div class="about-offer-icon">
                    <i class="fa-solid fa-newspaper"></i>
                </div>
                <h3 class="about-offer-card-title">Specialized News &amp; Insights</h3>
                <p class="about-offer-card-text">Stay updated with the latest developments in senior and accessible travel.</p>
            </div>
            <div class="about-offer-card">
                <div class="about-offer-icon">
                    <i class="fa-regular fa-map"></i>
                </div>
                <h3 class="about-offer-card-title">In-Depth Travel Reports</h3>
                <p class="about-offer-card-text">Explore destinations evaluated for accessibility, comfort, and healthcare availability.</p>
            </div>
            <div class="about-offer-card">
                <div class="about-offer-icon">
                    <i class="fa-regular fa-lightbulb"></i>
                </div>
                <h3 class="about-offer-card-title">Stories &amp; Inspiration</h3>
                <p class="about-offer-card-text">Inspiring journeys from travellers over 60 and individuals with special needs.</p>
            </div>
            <div class="about-offer-card">
                <div class="about-offer-icon">
                    <i class="fa-solid fa-book-open"></i>
                </div>
                <h3 class="about-offer-card-title">Practical Guides &amp; Tips</h3>
                <p class="about-offer-card-text">Travel planning, insurance, transport, and accommodation guidance tailored to your needs.</p>
            </div>
        </div>

        <!-- Cards Scroll (Mobile) -->
        <div class="about-offer-scroll mobile-only" dir="ltr">
            <div class="about-offer-scroll-inner">
                <?php
                $offerCards = [
                    ['icon' => 'fa-solid fa-newspaper',    'title' => 'Specialized News &amp; Insights',  'text' => 'Stay updated with the latest developments in senior and accessible travel.'],
                    ['icon' => 'fa-regular fa-map',         'title' => 'In-Depth Travel Reports',          'text' => 'Explore destinations evaluated for accessibility, comfort, and healthcare availability.'],
                    ['icon' => 'fa-regular fa-lightbulb',   'title' => 'Stories &amp; Inspiration',        'text' => 'Inspiring journeys from travellers over 60 and individuals with special needs.'],
                    ['icon' => 'fa-solid fa-book-open',     'title' => 'Practical Guides &amp; Tips',      'text' => 'Travel planning, insurance, transport, and accommodation guidance tailored to your needs.'],
                    // Duplicate for seamless loop
                    ['icon' => 'fa-solid fa-newspaper',    'title' => 'Specialized News &amp; Insights',  'text' => 'Stay updated with the latest developments in senior and accessible travel.'],
                ];
                foreach ($offerCards as $card): ?>
                <div class="about-offer-card about-offer-card--mobile">
                    <div class="about-offer-icon">
                        <i class="<?php echo $card['icon']; ?>"></i>
                    </div>
                    <h3 class="about-offer-card-title" style="font-size:1.25rem;"><?php echo $card['title']; ?></h3>
                    <p class="about-offer-card-text"><?php echo $card['text']; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     SECTION 4 — PHILOSOPHY
══════════════════════════════════════════════ -->
<section class="about-phili-section">
    <!-- Decorative watermark number -->
    <div class="about-phili-watermark">60</div>

    <div class="about-phili-inner">
        <!-- Left -->
        <div class="about-phili-left">
            <span class="about-label"><?php echo e($abt['about_phili_label']); ?></span>
            <h2 class="about-section-heading">
                <?php echo e($abt['about_phili_heading']); ?>
                <span class="about-heading-accent italic"> <?php echo e($abt['about_phili_heading_accent']); ?></span>
            </h2>
            <div class="about-phili-divider"></div>
            <blockquote class="about-phili-quote">
                <?php echo e($abt['about_phili_quote']); ?>
            </blockquote>
        </div>

        <!-- Right -->
        <div class="about-phili-right">
            <?php if (!empty($abt['about_phili_text1'])): ?>
            <p class="about-phili-text">
                <?php echo e($abt['about_phili_text1']); ?>
            </p>
            <?php endif; ?>

            <?php if (!empty($abt['about_phili_text2'])): ?>
            <p class="about-phili-text">
                <?php echo e($abt['about_phili_text2']); ?>
            </p>
            <?php endif; ?>

            <?php if (!empty($abt['about_phili_text3'])): ?>
            <p class="about-phili-text">
                <?php echo e($abt['about_phili_text3']); ?>
            </p>
            <?php endif; ?>

            <div class="about-phili-cta">
                <span class="about-phili-cta-line"></span>
                <span class="about-phili-cta-text">Keep Exploring</span>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     SECTION 4.5 — OUR TEAM
══════════════════════════════════════════════ -->
<section class="about-team-section" id="team">
    <!-- Decorative -->
    <div class="about-blob about-blob--gold-tl"></div>
    <div class="about-blob about-blob--ring-tr"></div>

    <div class="about-team-inner" style="max-width: 80rem; margin: 0 auto; padding: 4rem 1.5rem;">
        <!-- Header -->
        <div class="about-team-header" style="text-align: center; margin-bottom: 4rem;">
            <span class="about-label"><?php echo CURRENT_LANG === 'ar' ? 'فريقنا' : 'Our Team'; ?></span>
            <h2 class="about-section-heading">
                <?php echo CURRENT_LANG === 'ar' ? 'تعرف على' : 'Meet The'; ?>
                <span class="about-heading-accent italic"> <?php echo CURRENT_LANG === 'ar' ? 'فريق العمل' : 'Team'; ?></span>
            </h2>
            <p class="about-team-desc" style="margin-top: 1.25rem; color: #4b5563; max-width: 42rem; margin-left: auto; margin-right: auto; line-height: 1.75;">
                <?php echo CURRENT_LANG === 'ar' ? 'مجموعة من المغامرين والمستكشفين الملتزمين بجعل السفر متاحاً وسهلاً للجميع.' : 'A passionate team of explorers, accessibility advocates, and writers making travel possible for everyone.'; ?>
            </p>
        </div>

        <!-- Team Grid -->
        <div class="about-team-grid">
            <!-- 1. Founder Card (visually featured, styled using about-founder-card) -->
            <a
                href="<?php echo e($abt['about_founder_linkedin']); ?>"
                target="_blank"
                rel="noopener noreferrer"
                class="about-founder-card"
            >
                <div class="about-founder-card-top">
                    <div class="about-founder-img-wrap">
                        <img
                            src="<?php echo BASE_URL . e($abt['about_founder_image']); ?>"
                            alt="<?php echo e($abt['about_founder_name']); ?> - Founder"
                            class="about-founder-img"
                            width="96"
                            height="96"
                            loading="lazy"
                        />
                    </div>
                    <div class="about-founder-info">
                        <h3 class="about-founder-name"><?php echo e($abt['about_founder_name']); ?></h3>
                        <p class="about-founder-role"><?php echo e($abt['about_founder_role']); ?></p>
                        <?php if (!empty($abt['about_founder_linkedin'])): ?>
                            <div class="about-founder-cta">
                                <i class="fa-brands fa-linkedin" style="font-size:1.1rem; color:#0077b5;"></i>
                                <span><?php echo CURRENT_LANG === 'ar' ? 'عرض على LinkedIn' : 'LinkedIn'; ?></span>
                                <span class="about-founder-arrow">→</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <p class="about-founder-quote">
                    <?php echo e($abt['about_founder_quote']); ?>
                </p>
            </a>

            <!-- 2. Dynamic Team Members -->
            <?php foreach ($activeTeamMembers as $member): 
                $mImage = !empty($member['image']) ? BASE_URL . $member['image'] : 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23cbd5e1" width="100%" height="100%"><rect width="100%" height="100%" fill="%23f1f5f9"/><circle cx="12" cy="8.5" r="4"/><path d="M12 14c-4.42 0-8 2.24-8 5v1h16v-1c0-2.76-3.58-5-8-5z"/></svg>';
                $mName = t($member, 'name');
                $mRole = t($member, 'role');
                $mBio = t($member, 'bio');
                $mLinkedin = $member['linkedin_url'] ?? '';
                ?>
                <div class="about-founder-card">
                    <div class="about-founder-card-top">
                        <div class="about-founder-img-wrap">
                            <img
                                src="<?php echo e($mImage); ?>"
                                alt="<?php echo e($mName); ?>"
                                class="about-founder-img"
                                width="96"
                                height="96"
                                loading="lazy"
                            />
                        </div>
                        <div class="about-founder-info">
                            <h3 class="about-founder-name"><?php echo e($mName); ?></h3>
                            <p class="about-founder-role"><?php echo e($mRole); ?></p>
                            <?php if (!empty($mLinkedin)): ?>
                                <a href="<?php echo e($mLinkedin); ?>" target="_blank" rel="noopener noreferrer" class="about-founder-cta" style="text-decoration:none;">
                                    <i class="fa-brands fa-linkedin" style="font-size:1.1rem; color:#0077b5;"></i>
                                    <span><?php echo CURRENT_LANG === 'ar' ? 'عرض على LinkedIn' : 'LinkedIn'; ?></span>
                                    <span class="about-founder-arrow">→</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (!empty($mBio)): ?>
                        <p class="about-founder-quote">
                            <?php echo e($mBio); ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     SECTION 5 — CONTACT / GET IN TOUCH
══════════════════════════════════════════════ -->
<section class="about-contact-section" id="contact">
    <!-- Decorative -->
    <div class="about-blob about-blob--contact-tl"></div>
    <div class="about-blob about-blob--contact-br"></div>

    <div class="about-contact-inner">
        <!-- Header -->
        <div class="about-contact-header">
            <span class="about-label">Get In Touch</span>
            <h2 class="about-section-heading">
                <?php echo e($abt['about_contact_heading']); ?>
                <span class="about-heading-accent italic"> <?php echo e($abt['about_contact_heading_accent']); ?></span>
            </h2>
            <p class="about-contact-desc">
                <?php echo e($abt['about_contact_desc']); ?>
            </p>
        </div>

        <!-- Centered Contact Form -->
        <div class="about-contact-grid">
            <!-- Contact Form -->
            <div class="about-contact-form-wrap">
                <h3 class="about-contact-form-title">Send a Message</h3>
                <div id="aboutContactStatus" class="form-status-alert" style="display:none;"></div>
                <form id="aboutContactForm" action="<?php echo BASE_URL; ?>/api/contact" method="POST" class="about-contact-form">
                    <?php echo Auth::csrfInput(); ?>
                    <input
                        type="text"
                        name="name"
                        id="about-name"
                        class="about-form-input"
                        placeholder="Your Name"
                        required
                    />
                    <input
                        type="email"
                        name="email"
                        id="about-email"
                        class="about-form-input"
                        placeholder="Your Email"
                        required
                    />
                    <textarea
                        name="message"
                        id="about-message"
                        rows="5"
                        class="about-form-input"
                        placeholder="Your Message"
                        required
                    ></textarea>
                    <button type="submit" class="about-form-submit">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
/* ═══════════════════════════════════════════════════════
   ABOUT PAGE — SCOPED STYLES
   All classes are prefixed "about-" to avoid collisions.
═══════════════════════════════════════════════════════ */

/* ── Helpers ─────────────────────────────────── */
.desktop-only { display: block; }
.mobile-only  { display: none; }
@media (max-width: 768px) {
    .desktop-only { display: none; }
    .mobile-only  { display: block; }
}

/* ── Shared tokens ───────────────────────────── */
:root {
    --about-navy:  #0B1F3B;
    --about-gold:  #d89d2e;
    --about-stone: #f8f7f4;
    --about-white: #ffffff;
}

/* ── Label ───────────────────────────────────── */
.about-label {
    display: inline-flex;
    align-items: center;
    gap: .75rem;
    text-transform: uppercase;
    letter-spacing: .25em;
    font-size: .8rem;
    font-weight: 600;
    color: var(--about-gold);
}
.about-label--gold { color: var(--about-gold); }
.about-label-line {
    display: inline-block;
    width: 2.5rem;
    height: 2px;
    background: var(--about-gold);
}

/* ── Headings ────────────────────────────────── */
.about-section-heading {
    margin-top: 1.5rem;
    font-size: clamp(2.2rem, 5vw, 3.75rem);
    font-weight: 800;
    line-height: 1.15;
    color: var(--about-navy);
}
.about-section-heading--white { color: #fff; }
.about-heading-accent { color: var(--about-gold); }
.italic { font-style: italic; }

/* ── Decorative blobs ────────────────────────── */
.about-blob { position: absolute; border-radius: 9999px; pointer-events: none; }
.about-blob--gold-tl  { top:0; left:0; width:18rem; height:18rem; background: rgba(216,157,46,.10); filter:blur(64px); }
.about-blob--navy-br  { bottom:0; right:0; width:24rem; height:24rem; background: rgba(11,31,59,.05); filter:blur(64px); }
.about-blob--gold-circle-tl { top:-2.5rem; left:-.75rem; width:16rem; height:16rem; background:rgba(216,157,46,.10); z-index:0; }
.about-blob--ring-tr  { top:8rem; right:2.5rem; width:20rem; height:20rem; border:1px solid rgba(216,157,46,.80); display:none; }
@media(min-width:768px){ .about-blob--ring-tr{display:block;} }
.about-blob--circle-bl{ bottom:-3.75rem; left:33%; width:8rem; height:8rem; background:rgba(11,31,59,.20); }
.about-blob--ring-br  { bottom:-5rem; right:0; width:28rem; height:28rem; border:1px solid rgba(11,31,59,.10); }
.about-blob--ring-mid { top:50%; left:22.5rem; transform:translateY(-50%); width:11rem; height:11rem; border:2px solid rgba(216,157,46,.20); }
.about-blob--vision-tl{ top:-5rem; left:-5rem; width:18rem; height:18rem; background:rgba(216,157,46,.05); }
.about-blob--vision-br{ bottom:0; right:0; width:28rem; height:28rem; border:1px solid rgba(11,31,59,.10); }
.about-blob--offer-tl { top:0; left:0; width:24rem; height:24rem; background:rgba(255,255,255,.05); filter:blur(64px); }
.about-blob--offer-tr { top:2.5rem; right:0; width:31rem; height:31rem; border:1px solid rgba(255,255,255,.10); }
.about-blob--contact-tl{ top:0; left:0; width:24rem; height:24rem; background:rgba(11,31,59,.05); filter:blur(64px); }
.about-blob--contact-br{ bottom:0; right:0; width:31rem; height:31rem; background:rgba(216,157,46,.10); filter:blur(64px); }

/* ══════════════════════════════════════════════
   § 1  HERO
══════════════════════════════════════════════ */
.about-hero-section {
    position: relative;
    overflow: hidden;
    padding: 5rem 1.5rem;
    background: var(--about-stone);
}
.about-hero-inner {
    max-width: 80rem;
    margin: 0 auto;
    display: grid;
    gap: 4rem;
    align-items: center;
    position: relative;
    z-index: 1;
}
@media(min-width:1024px){
    .about-hero-inner { grid-template-columns: 1fr 1fr; }
}

/* Text side */
.about-hero-heading {
    margin-top: 1.5rem;
    font-size: clamp(2.8rem, 7vw, 4.5rem);
    font-weight: 800;
    line-height: 1.1;
    color: var(--about-navy);
}
.about-hero-quote {
    margin-top: 1.5rem;
    border-left: 4px solid var(--about-gold);
    padding-left: 1.25rem;
    font-size: 1.125rem;
    font-style: italic;
    color: #6b7280;
}
.about-hero-desc {
    margin-top: 2rem;
    font-size: 1.125rem;
    color: #4b5563;
    line-height: 1.75;
    max-width: 36rem;
}
.about-hero-cta-row {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-top: 2.5rem;
}
.about-btn-primary {
    padding: .85rem 2rem;
    border-radius: 9999px;
    background: var(--about-navy);
    color: #fff;
    font-weight: 600;
    font-size: .95rem;
    text-decoration: none;
    transition: background .3s, box-shadow .3s;
}
.about-btn-primary:hover {
    background: #122e58;
    box-shadow: 0 8px 24px rgba(11,31,59,.25);
}
.about-btn-outline {
    padding: .85rem 2rem;
    border-radius: 9999px;
    border: 1px solid #d1d5db;
    color: #374151;
    font-weight: 500;
    font-size: .95rem;
    text-decoration: none;
    transition: background .3s, box-shadow .3s;
}
.about-btn-outline:hover {
    background: #fff;
    box-shadow: 0 8px 24px rgba(0,0,0,.08);
}

/* Image side */
.about-hero-image-wrap {
    position: relative;
    display: flex;
    justify-content: center;
}
.about-hero-spin-ring {
    position: absolute;
    width: 115%;
    height: 115%;
    z-index: -1;
    color: var(--about-gold);
    opacity: .5;
    animation: about-spin 40s linear infinite;
}
@keyframes about-spin { to { transform: rotate(360deg); } }
.about-hero-photo-frame {
    position: relative;
    overflow: hidden;
    border-radius: 2.5rem;
    box-shadow: 0 25px 60px rgba(0,0,0,.18);
    max-width: 32rem;
    width: 100%;
}
.about-hero-photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 2s ease;
}
.about-hero-image-wrap:hover .about-hero-photo { transform: scale(1.06); }
.about-hero-badge {
    position: absolute;
    bottom: -1.5rem;
    left: -1.5rem;
    background: #fff;
    border-radius: 1.5rem;
    padding: 1.5rem;
    box-shadow: 0 12px 32px rgba(0,0,0,.12);
    transition: transform .5s, box-shadow .5s;
}
.about-hero-image-wrap:hover .about-hero-badge {
    transform: translateY(-.75rem);
    box-shadow: 0 20px 50px rgba(0,0,0,.18);
}
.about-hero-badge-label { font-size: .875rem; color: #6b7280; margin: 0; }
.about-hero-badge-number { font-size: 1.875rem; font-weight: 800; color: var(--about-navy); margin: .25rem 0 0; }

/* ══════════════════════════════════════════════
   § 2  VISION
══════════════════════════════════════════════ */
.about-vision-section {
    position: relative;
    overflow: hidden;
    padding: 6rem 1.5rem;
    background: #fff;
}
.about-vision-inner { max-width: 80rem; margin: 0 auto; }
.about-vision-header { max-width: 56rem; margin-bottom: 5rem; }
.about-vision-quote {
    margin-top: 1.5rem;
    font-size: 1.25rem;
    font-style: italic;
    color: #6b7280;
    border-left: 4px solid var(--about-gold);
    padding-left: 1.5rem;
}
.about-vision-grid {
    display: grid;
    gap: 3rem;
}
@media(min-width:1024px){
    .about-vision-grid { grid-template-columns: 1.3fr .7fr; }
}
.about-vision-cards { display: flex; flex-direction: column; gap: 2rem; }
.about-vision-card {
    border-radius: 2rem;
    padding: 2.5rem;
}
.about-vision-card--light {
    background: var(--about-stone);
    border: 1px solid #e7e5e4;
}
.about-vision-card--dark {
    background: var(--about-navy);
    color: #fff;
    position: relative;
    overflow: hidden;
}
.about-vision-card-ring {
    position: absolute;
    top: 0; right: 0;
    width: 10rem; height: 10rem;
    border-radius: 50%;
    background: rgba(255,255,255,.05);
}
.about-vision-card-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: var(--about-navy);
}
.about-vision-card--dark .about-vision-card-title { color: #fff; }
.about-vision-card-text { color: #4b5563; line-height: 1.75; }
.about-vision-card-subtext { color: rgba(255,255,255,.80); line-height: 1.75; }

/* Stats */
.about-stats-col { display: flex; flex-direction: column; gap: 1.5rem; }
.about-stat-card {
    border-radius: 1.75rem;
    border: 1px solid #e7e5e4;
    padding: 2rem;
    transition: transform .25s;
}
.about-stat-card:hover { transform: translateY(-4px); }
.about-stat-number {
    font-size: 3rem;
    font-weight: 800;
    line-height: 1;
}
.about-stat-number--gold  { color: var(--about-gold); }
.about-stat-number--navy  { color: var(--about-navy); }
.about-stat-desc { margin-top: .75rem; color: #4b5563; }

/* ══════════════════════════════════════════════
   § 3  OFFER
══════════════════════════════════════════════ */
.about-offer-section {
    position: relative;
    overflow: hidden;
    padding: 4rem 1rem 5rem;
    background: var(--about-navy);
    min-height: auto;
}
@media(min-width:768px){ .about-offer-section { min-height: 56rem; } }
.about-offer-inner {
    max-width: 80rem;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}
.about-offer-header {
    text-align: center;
    max-width: 56rem;
    margin: 0 auto 5rem;
}
.about-offer-desc {
    margin-top: 1.5rem;
    font-size: 1.125rem;
    color: rgba(255,255,255,.70);
    line-height: 1.75;
}
.about-offer-grid {
    display: grid;
    grid-template-columns: repeat(4,1fr);
    gap: 2rem;
}
@media(max-width:900px){ .about-offer-grid { grid-template-columns: repeat(2,1fr); } }
.about-offer-card {
    background: var(--about-stone);
    border-radius: 2rem;
    padding: 2rem;
    display: flex;
    flex-direction: column;
    min-height: 20rem;
    transition: transform .4s, box-shadow .4s;
}
.about-offer-card:hover {
    transform: translateY(-12px);
    box-shadow: 0 20px 60px rgba(0,0,0,.25);
}
.about-offer-card--mobile { min-height: unset; width: 20rem; }
.about-offer-icon {
    width: 4rem; height: 4rem;
    border-radius: 1rem;
    background: rgba(216,157,46,.10);
    color: var(--about-navy);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.75rem;
    margin-bottom: 1.5rem;
    transition: transform .4s;
}
.about-offer-card:hover .about-offer-icon { transform: scale(1.1) rotate(3deg); }
.about-offer-card-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--about-navy);
    margin-bottom: 1rem;
    min-height: 4rem;
}
.about-offer-card-text { color: #4b5563; line-height: 1.6; flex: 1; }

/* Mobile scrolling strip */
.about-offer-scroll { overflow: hidden; padding: 1rem 0; }
.about-offer-scroll-inner {
    display: flex;
    width: max-content;
    gap: 2rem;
    animation: about-scroll 18s linear infinite;
}
.about-offer-scroll-inner:hover { animation-play-state: paused; }
@keyframes about-scroll {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

/* ══════════════════════════════════════════════
   § 4  PHILOSOPHY
══════════════════════════════════════════════ */
.about-phili-section {
    position: relative;
    overflow: hidden;
    padding: 4rem 1.5rem;
    background: var(--about-stone);
}
.about-phili-watermark {
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    font-size: clamp(10rem, 22vw, 26rem);
    font-weight: 900;
    color: rgba(11,31,59,.03);
    user-select: none;
    pointer-events: none;
    line-height: 1;
}
.about-phili-inner {
    max-width: 80rem;
    margin: 0 auto;
    display: grid;
    gap: 4rem;
    align-items: center;
}
@media(min-width:1024px){ .about-phili-inner { grid-template-columns: 1fr 1fr; } }
.about-phili-left {
    background: rgba(0,0,0,.05);
    padding: 1.5rem;
    border-radius: 1rem;
    border: 1px solid transparent;
    transition: border .5s, transform .5s, box-shadow .5s;
}
.about-phili-left:hover {
    border-color: rgba(216,157,46,.20);
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0,0,0,.08);
}
.about-phili-divider {
    width: 6rem;
    height: 3px;
    background: var(--about-gold);
    margin-top: 2rem;
}
.about-phili-quote {
    margin-top: 2rem;
    font-size: clamp(1.4rem, 3vw, 1.875rem);
    font-weight: 300;
    font-style: italic;
    color: var(--about-navy);
    line-height: 1.5;
}
.about-phili-right {
    background: #fff;
    border-radius: 2.25rem;
    padding: 2.5rem;
    box-shadow: 0 12px 40px rgba(0,0,0,.08);
    border: 1px solid #e7e5e4;
    transition: transform .5s, box-shadow .5s, border-color .5s;
}
.about-phili-right:hover {
    transform: translateY(-8px);
    box-shadow: 0 25px 60px rgba(0,0,0,.12);
    border-color: rgba(216,157,46,.20);
}
.about-phili-text {
    color: #4b5563;
    font-size: 1.125rem;
    line-height: 1.75;
    margin-bottom: 1.5rem;
    transition: color .3s;
}
.about-phili-right:hover .about-phili-text { color: #1f2937; }
.about-phili-cta {
    display: inline-flex;
    align-items: center;
    gap: 1rem;
    color: var(--about-gold);
    font-weight: 600;
    cursor: pointer;
    margin-top: 2rem;
}
.about-phili-cta-line {
    display: inline-block;
    width: 2.5rem;
    height: 2px;
    background: var(--about-gold);
    transition: width .5s;
}
.about-phili-cta:hover .about-phili-cta-line { width: 4rem; }
.about-phili-cta-text { position: relative; }
.about-phili-cta-text::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: -2px;
    width: 0;
    height: 1px;
    background: var(--about-gold);
    transition: width .5s;
}
.about-phili-cta:hover .about-phili-cta-text::after { width: 100%; }

/* ══════════════════════════════════════════════
   § 4.5  TEAM MEMBERS
══════════════════════════════════════════════ */
.about-team-section {
    position: relative;
    overflow: hidden;
    padding: 6rem 1.5rem;
    background: #fff;
}
.about-team-grid {
    display: grid;
    gap: 2.5rem;
    grid-template-columns: 1fr;
    margin-top: 2rem;
}
@media(min-width: 768px) {
    .about-team-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media(min-width: 1024px) {
    .about-team-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* Feature the Founder Card (the first child in the grid) */
@media(min-width: 768px) {
    .about-team-grid > .about-founder-card:first-child {
        grid-column: span 2;
    }
}
@media(min-width: 1024px) {
    .about-team-grid > .about-founder-card:first-child {
        grid-column: span 3;
    }
}

/* Featured card visual styling enhancements */
.about-team-grid > .about-founder-card:first-child {
    background: linear-gradient(145deg, #ffffff, #fcfbf9);
    border: 1px solid rgba(216, 157, 46, 0.2);
    box-shadow: 0 20px 50px rgba(11, 31, 59, 0.08);
    position: relative;
}
.about-team-grid > .about-founder-card:first-child::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 6px;
    background: linear-gradient(90deg, var(--about-gold), var(--about-navy));
    border-radius: 2rem 2rem 0 0;
}
.about-team-grid > .about-founder-card:first-child .about-founder-quote {
    font-size: 1.125rem;
    font-style: italic;
    border-left: 3px solid var(--about-gold);
    padding-left: 1rem;
}
[dir="rtl"] .about-team-grid > .about-founder-card:first-child .about-founder-quote {
    border-left: none;
    padding-left: 0;
    border-right: 3px solid var(--about-gold);
    padding-right: 1rem;
}

/* ══════════════════════════════════════════════
   § 5  CONTACT
══════════════════════════════════════════════ */
.about-contact-section {
    position: relative;
    overflow: hidden;
    padding: 4rem 1rem;
    background: var(--about-stone);
}
.about-contact-inner {
    max-width: 80rem;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}
.about-contact-header {
    text-align: center;
    margin-bottom: 4rem;
}
.about-contact-desc {
    margin-top: 1.25rem;
    color: #4b5563;
    max-width: 42rem;
    margin-left: auto;
    margin-right: auto;
}
.about-contact-grid {
    max-width: 45rem;
    margin: 0 auto;
}

/* Founder card */
.about-founder-card {
    display: block;
    background: #fff;
    border-radius: 2rem;
    box-shadow: 0 12px 40px rgba(0,0,0,.08);
    padding: 2rem;
    text-decoration: none;
    transition: transform .5s, box-shadow .5s;
}
.about-founder-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 25px 60px rgba(0,0,0,.14);
}
.about-founder-card-top {
    display: flex;
    align-items: center;
    gap: 1.25rem;
}
.about-founder-img-wrap { position: relative; }
.about-founder-img {
    width: 6rem;
    height: 6rem;
    border-radius: 1rem;
    object-fit: cover;
    object-position: top;
    display: block;
}
.about-founder-img-wrap::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 1rem;
    box-shadow: inset 0 0 0 2px rgba(216,157,46,.30);
    transition: box-shadow .3s;
}
.about-founder-card:hover .about-founder-img-wrap::after {
    box-shadow: inset 0 0 0 2px var(--about-gold);
}
.about-founder-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--about-navy);
    transition: color .3s;
}
.about-founder-card:hover .about-founder-name { color: var(--about-gold); }
.about-founder-role { font-size: .875rem; color: #6b7280; margin-top: .25rem; }
.about-founder-cta {
    display: flex;
    align-items: center;
    gap: .5rem;
    color: var(--about-navy);
    font-size: .875rem;
    font-weight: 600;
    margin-top: .5rem;
}
.about-founder-arrow { transition: transform .3s; }
.about-founder-card:hover .about-founder-arrow { transform: translateX(4px); }
.about-founder-quote {
    margin-top: 1.5rem;
    color: #4b5563;
    line-height: 1.75;
}

/* Contact form */
.about-contact-form-wrap {
    background: #fff;
    border-radius: 2rem;
    box-shadow: 0 12px 40px rgba(0,0,0,.08);
    padding: 2rem 2.5rem;
    width: 100%;
}
.about-contact-form-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--about-navy);
    margin-bottom: 1.5rem;
}
.about-contact-form { display: flex; flex-direction: column; gap: 1.25rem; }
.about-form-input {
    width: 100%;
    padding: 1rem 1.25rem;
    border-radius: .75rem;
    border: 1px solid #e5e7eb;
    font-size: 1rem;
    font-family: inherit;
    outline: none;
    transition: border-color .3s;
    background: #fff;
    color: #1f2937;
    resize: vertical;
}
.about-form-input:focus { border-color: var(--about-gold); }
.about-form-submit {
    width: 100%;
    padding: 1rem;
    border-radius: .75rem;
    background: var(--about-navy);
    color: #fff;
    font-size: 1rem;
    font-weight: 600;
    font-family: inherit;
    border: none;
    cursor: pointer;
    transition: transform .4s, box-shadow .4s;
}
.about-form-submit:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 40px rgba(11,31,59,.25);
}
</style>

<script>
/* About page contact form AJAX */
(function () {
    var form   = document.getElementById('aboutContactForm');
    var status = document.getElementById('aboutContactStatus');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var btn = form.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.textContent = 'Sending…';

        var data = new FormData(form);

        fetch(form.action, { method: 'POST', body: data })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                status.style.display = 'block';
                if (res.success) {
                    status.className = 'form-status-alert success';
                    status.textContent = res.message || 'Your message has been sent!';
                    form.reset();
                } else {
                    status.className = 'form-status-alert error';
                    status.textContent = res.error || 'Something went wrong. Please try again.';
                }
            })
            .catch(function () {
                status.style.display = 'block';
                status.className = 'form-status-alert error';
                status.textContent = 'Network error. Please try again.';
            })
            .finally(function () {
                btn.disabled = false;
                btn.textContent = 'Send Message';
                setTimeout(function () { status.style.display = 'none'; }, 6000);
            });
    });
})();
</script>

<?php
require_once PATH_ROOT . '/includes/footer.php';
?>
