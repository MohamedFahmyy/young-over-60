<?php
// admin/about-page.php
// About Page Content Editor — YoungOver60 Admin Panel

$error   = null;
$success = null;

// Handle POST — save about page content
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Auth::requireCsrf();

        $db = Database::getInstance()->getConnection();

        // Build SET clauses dynamically from the about_ columns we expect
        $fields = [
            // Hero
            'about_hero_label', 'about_hero_heading_line1', 'about_hero_heading_accent',
            'about_hero_heading_line2', 'about_hero_quote', 'about_hero_desc',
            'about_hero_desc_mobile', 'about_hero_btn1_text', 'about_hero_btn2_text',
            'about_hero_badge_label', 'about_hero_badge_number', 'about_hero_image',
            // Vision
            'about_vision_label', 'about_vision_heading', 'about_vision_heading_accent',
            'about_vision_quote',
            'about_vision_card1_title', 'about_vision_card1_text1', 'about_vision_card1_text2',
            'about_vision_card2_title', 'about_vision_card2_text1', 'about_vision_card2_text2',
            'about_stat1_number', 'about_stat1_desc',
            'about_stat2_number', 'about_stat2_desc',
            'about_stat3_number', 'about_stat3_desc',
            // Philosophy
            'about_phili_label', 'about_phili_heading', 'about_phili_heading_accent',
            'about_phili_quote', 'about_phili_text1', 'about_phili_text2', 'about_phili_text3',
            // Founder / Contact
            'about_founder_name', 'about_founder_role', 'about_founder_linkedin',
            'about_founder_quote', 'about_founder_image',
            'about_contact_heading', 'about_contact_heading_accent', 'about_contact_desc',
        ];

        $setParts = [];
        $binds    = [];
        foreach ($fields as $f) {
            $setParts[] = "`$f` = :$f";
            $binds[":$f"] = isset($_POST[$f]) ? trim($_POST[$f]) : null;
        }

        $sql  = 'UPDATE `site_settings` SET ' . implode(', ', $setParts) . ' WHERE id = 1';
        $stmt = $db->prepare($sql);
        if ($stmt->execute($binds)) {
            $pm->clearCache();
            $success = 'About page content saved successfully!';
        } else {
            $error = 'Failed to save settings — please try again.';
        }
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Load current about content
$settings = $pm->getSiteSettings();

// Fallback defaults if columns don't exist yet (before migration runs)
$abt = array_merge([
    'about_hero_label'              => 'Our Story',
    'about_hero_heading_line1'      => 'Beyond',
    'about_hero_heading_accent'     => ' 60,',
    'about_hero_heading_line2'      => 'Life Begins Again.',
    'about_hero_quote'              => 'Life Begins Again.',
    'about_hero_desc'               => 'The stories, people, and passion behind a community that believes every new chapter can become the greatest adventure yet.',
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
    'about_vision_card1_text1'      => 'We believe that travel is neither a luxury nor a privilege reserved for a specific age group.',
    'about_vision_card1_text2'      => 'Travel opens windows to the world\'s beauty, creates connections between cultures.',
    'about_vision_card2_title'      => 'Our Vision: Travel Without Boundaries',
    'about_vision_card2_text1'      => 'We strive to make travel accessible, comfortable, and enjoyable for everyone over 60.',
    'about_vision_card2_text2'      => 'Our mission is to challenge outdated stereotypes and prove that adventure has no expiration date.',
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
    'about_phili_text1'             => 'We reject the idea of reducing a person to the number on their passport.',
    'about_phili_text2'             => 'We believe that true youth is not measured by age, but by the ability to remain curious.',
    'about_phili_text3'             => 'At Young Over 60, every traveller has a story worth telling.',
    'about_founder_name'            => 'Zakaria Dawoud',
    'about_founder_role'            => 'Founder & Project CEO',
    'about_founder_linkedin'        => 'https://www.linkedin.com/in/zakaria-dawoud-26902b180',
    'about_founder_quote'           => 'Beyond 60, life begins again. This project was built to prove that travel has no age limit.',
    'about_founder_image'           => '/assets/images/founder.jpeg',
    'about_contact_heading'         => 'Let\'s Build Something',
    'about_contact_heading_accent'  => 'Meaningful',
    'about_contact_desc'            => 'Whether you have a question, collaboration idea, or just want to say hello — feel free to reach out.',
], $settings ?? []);

$seoPageType = 'admin';
require_once PATH_ROOT . '/includes/header.php';
?>

<div class="admin-layout">
    <?php require_once PATH_ROOT . '/includes/admin-sidebar.php'; ?>

    <main class="admin-main">
        <div class="admin-header-row" style="border-bottom: 1px solid #e5e7eb; padding-bottom: 1.5rem; margin-bottom: 2rem;">
            <div>
                <h1 class="admin-title">About Page Editor</h1>
                <p style="color: #888; font-size: 0.9rem; margin-top: 0.25rem;">
                    Edit all text, images and content shown on the public About page.
                </p>
            </div>
            <a href="<?php echo BASE_URL; ?>/about" target="_blank"
               style="display:inline-flex; align-items:center; gap:0.4rem; font-size:0.85rem; color:var(--primary-color); text-decoration:none; font-weight:600;">
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Preview About Page
            </a>
        </div>

        <!-- Flash alerts -->
        <?php if ($success): ?>
            <div class="form-status-alert success" style="margin-bottom:2rem;">
                <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span><?php echo e($success); ?></span>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="form-status-alert error" style="margin-bottom:2rem;">
                <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span><?php echo e($error); ?></span>
            </div>
        <?php endif; ?>

        <!-- ── Tab Navigation ─────────────────────────────────── -->
        <div class="admin-tabs-row" style="margin-bottom:2rem;">
            <button class="admin-tab-trigger active" data-target="tab-hero">Hero</button>
            <button class="admin-tab-trigger" data-target="tab-vision">Vision / Story</button>
            <button class="admin-tab-trigger" data-target="tab-philosophy">Philosophy</button>
            <button class="admin-tab-trigger" data-target="tab-founder">Founder &amp; Contact</button>
        </div>

        <form id="aboutForm" action="<?php echo BASE_URL . $lang_prefix_url; ?>/admin/about-page" method="POST">
            <?php echo Auth::csrfInput(); ?>

            <!-- ══════════════════════════════════════════
                 TAB 1: HERO SECTION
            ══════════════════════════════════════════ -->
            <div id="tab-hero" class="admin-tab-content active">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:2rem;">

                    <!-- Left: Text fields -->
                    <div style="display:flex; flex-direction:column; gap:1.5rem;">

                        <div class="admin-card-box" style="margin:0; padding:2rem;">
                            <h3 style="font-size:1rem; font-weight:700; margin-bottom:1.5rem; border-bottom:1px solid #eee; padding-bottom:0.75rem;">
                                Hero Text
                            </h3>

                            <div class="admin-form-group">
                                <label for="ah-label">Section Label <small style="color:#888">(e.g. "Our Story")</small></label>
                                <input type="text" id="ah-label" name="about_hero_label"
                                       value="<?php echo e($abt['about_hero_label']); ?>" class="admin-form-input"/>
                            </div>

                            <div class="admin-form-group" style="margin-top:1rem;">
                                <label>Heading — Line 1 (normal text)</label>
                                <input type="text" name="about_hero_heading_line1"
                                       value="<?php echo e($abt['about_hero_heading_line1']); ?>" class="admin-form-input"/>
                            </div>
                            <div class="admin-form-group" style="margin-top:1rem;">
                                <label>Heading — Accent (gold italic, e.g. " 60,")</label>
                                <input type="text" name="about_hero_heading_accent"
                                       value="<?php echo e($abt['about_hero_heading_accent']); ?>" class="admin-form-input"/>
                            </div>
                            <div class="admin-form-group" style="margin-top:1rem;">
                                <label>Heading — Line 2</label>
                                <input type="text" name="about_hero_heading_line2"
                                       value="<?php echo e($abt['about_hero_heading_line2']); ?>" class="admin-form-input"/>
                            </div>

                            <div class="admin-form-group" style="margin-top:1rem;">
                                <label>Hero Quote (italic sidebar)</label>
                                <input type="text" name="about_hero_quote"
                                       value="<?php echo e($abt['about_hero_quote']); ?>" class="admin-form-input"/>
                            </div>

                            <div class="admin-form-group" style="margin-top:1rem;">
                                <label>Description (desktop)</label>
                                <textarea name="about_hero_desc" rows="4" class="admin-form-textarea"><?php echo e($abt['about_hero_desc']); ?></textarea>
                            </div>
                            <div class="admin-form-group" style="margin-top:1rem;">
                                <label>Description (mobile, shorter)</label>
                                <textarea name="about_hero_desc_mobile" rows="3" class="admin-form-textarea"><?php echo e($abt['about_hero_desc_mobile']); ?></textarea>
                            </div>
                        </div>

                        <div class="admin-card-box" style="margin:0; padding:2rem;">
                            <h3 style="font-size:1rem; font-weight:700; margin-bottom:1.5rem; border-bottom:1px solid #eee; padding-bottom:0.75rem;">
                                Buttons &amp; Badge
                            </h3>
                            <div class="admin-form-group">
                                <label>Primary Button Text</label>
                                <input type="text" name="about_hero_btn1_text"
                                       value="<?php echo e($abt['about_hero_btn1_text']); ?>" class="admin-form-input"/>
                            </div>
                            <div class="admin-form-group" style="margin-top:1rem;">
                                <label>Secondary Button Text</label>
                                <input type="text" name="about_hero_btn2_text"
                                       value="<?php echo e($abt['about_hero_btn2_text']); ?>" class="admin-form-input"/>
                            </div>
                            <div class="admin-form-group" style="margin-top:1rem;">
                                <label>Badge Label (small text)</label>
                                <input type="text" name="about_hero_badge_label"
                                       value="<?php echo e($abt['about_hero_badge_label']); ?>" class="admin-form-input"/>
                            </div>
                            <div class="admin-form-group" style="margin-top:1rem;">
                                <label>Badge Number (large text)</label>
                                <input type="text" name="about_hero_badge_number"
                                       value="<?php echo e($abt['about_hero_badge_number']); ?>" class="admin-form-input"/>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Hero image -->
                    <div>
                        <div class="admin-card-box" style="margin:0; padding:2rem;">
                            <h3 style="font-size:1rem; font-weight:700; margin-bottom:0.5rem; border-bottom:1px solid #eee; padding-bottom:0.75rem;">
                                Hero Image
                            </h3>
                            <p style="font-size:0.75rem; color:#888; margin-bottom:1.25rem;">
                                Shown on the right side of the hero section. Recommended: 800×700px.
                            </p>

                            <div class="admin-form-group">
                                <label>Image URL / Path</label>
                                <input type="text" id="about_hero_image" name="about_hero_image"
                                       value="<?php echo e($abt['about_hero_image']); ?>" class="admin-form-input"
                                       placeholder="/assets/images/about-header.jpeg"/>
                            </div>

                            <!-- Upload dropzone -->
                            <div id="heroImgDropzone" class="image-upload-preview-box"
                                 style="margin-top:1rem; border:1px dashed var(--accent-color); padding:1.5rem 1rem; border-radius:8px; text-align:center; cursor:pointer; transition:var(--transition-smooth);">
                                <p style="font-size:0.7rem; color:#777; margin:0;">Click or drag to upload a new hero image</p>
                                <span style="font-size:0.55rem; color:#999; display:block; margin-top:0.25rem;">JPG, PNG, WebP (Max 5MB)</span>
                                <input type="file" id="heroImgFileSelector" accept="image/*" style="display:none;"/>
                                <div id="heroImgStatus" style="font-size:0.65rem; color:var(--primary-color); margin-top:0.5rem; display:none;">Uploading...</div>
                                <img id="heroImgPreview"
                                     src="<?php echo !empty($abt['about_hero_image']) ? BASE_URL . $abt['about_hero_image'] : ''; ?>"
                                     style="display:<?php echo !empty($abt['about_hero_image']) ? 'inline-block' : 'none'; ?>; max-height:220px; max-width:100%; object-fit:cover; margin-top:1rem; border-radius:8px;"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════
                 TAB 2: VISION / OUR STORY SECTION
            ══════════════════════════════════════════ -->
            <div id="tab-vision" class="admin-tab-content">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:2rem;">

                    <!-- Section header -->
                    <div class="admin-card-box" style="margin:0; padding:2rem; grid-column:span 2;">
                        <h3 style="font-size:1rem; font-weight:700; margin-bottom:1.5rem; border-bottom:1px solid #eee; padding-bottom:0.75rem;">
                            Section Header
                        </h3>
                        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem;">
                            <div class="admin-form-group">
                                <label>Section Label</label>
                                <input type="text" name="about_vision_label"
                                       value="<?php echo e($abt['about_vision_label']); ?>" class="admin-form-input"/>
                            </div>
                            <div class="admin-form-group">
                                <label>Heading (normal)</label>
                                <input type="text" name="about_vision_heading"
                                       value="<?php echo e($abt['about_vision_heading']); ?>" class="admin-form-input"/>
                            </div>
                            <div class="admin-form-group">
                                <label>Heading (gold accent)</label>
                                <input type="text" name="about_vision_heading_accent"
                                       value="<?php echo e($abt['about_vision_heading_accent']); ?>" class="admin-form-input"/>
                            </div>
                            <div class="admin-form-group" style="grid-column:span 3;">
                                <label>Section Quote</label>
                                <input type="text" name="about_vision_quote"
                                       value="<?php echo e($abt['about_vision_quote']); ?>" class="admin-form-input"/>
                            </div>
                        </div>
                    </div>

                    <!-- Card 1 (light) -->
                    <div class="admin-card-box" style="margin:0; padding:2rem;">
                        <h3 style="font-size:1rem; font-weight:700; margin-bottom:1.5rem; border-bottom:1px solid #eee; padding-bottom:0.75rem;">
                            Card 1 — Light Background
                        </h3>
                        <div class="admin-form-group">
                            <label>Title</label>
                            <input type="text" name="about_vision_card1_title"
                                   value="<?php echo e($abt['about_vision_card1_title']); ?>" class="admin-form-input"/>
                        </div>
                        <div class="admin-form-group" style="margin-top:1rem;">
                            <label>Text Paragraph 1</label>
                            <textarea name="about_vision_card1_text1" rows="4" class="admin-form-textarea"><?php echo e($abt['about_vision_card1_text1']); ?></textarea>
                        </div>
                        <div class="admin-form-group" style="margin-top:1rem;">
                            <label>Text Paragraph 2</label>
                            <textarea name="about_vision_card1_text2" rows="4" class="admin-form-textarea"><?php echo e($abt['about_vision_card1_text2']); ?></textarea>
                        </div>
                    </div>

                    <!-- Card 2 (dark) -->
                    <div class="admin-card-box" style="margin:0; padding:2rem;">
                        <h3 style="font-size:1rem; font-weight:700; margin-bottom:1.5rem; border-bottom:1px solid #eee; padding-bottom:0.75rem;">
                            Card 2 — Dark (Navy) Background
                        </h3>
                        <div class="admin-form-group">
                            <label>Title</label>
                            <input type="text" name="about_vision_card2_title"
                                   value="<?php echo e($abt['about_vision_card2_title']); ?>" class="admin-form-input"/>
                        </div>
                        <div class="admin-form-group" style="margin-top:1rem;">
                            <label>Text Paragraph 1</label>
                            <textarea name="about_vision_card2_text1" rows="4" class="admin-form-textarea"><?php echo e($abt['about_vision_card2_text1']); ?></textarea>
                        </div>
                        <div class="admin-form-group" style="margin-top:1rem;">
                            <label>Text Paragraph 2</label>
                            <textarea name="about_vision_card2_text2" rows="4" class="admin-form-textarea"><?php echo e($abt['about_vision_card2_text2']); ?></textarea>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="admin-card-box" style="margin:0; padding:2rem; grid-column:span 2;">
                        <h3 style="font-size:1rem; font-weight:700; margin-bottom:1.5rem; border-bottom:1px solid #eee; padding-bottom:0.75rem;">
                            Statistics Cards (3 items)
                        </h3>
                        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1.5rem;">
                            <?php for ($i = 1; $i <= 3; $i++): ?>
                            <div>
                                <p style="font-size:0.8rem; font-weight:700; color:#888; margin-bottom:0.75rem;">Stat <?php echo $i; ?></p>
                                <div class="admin-form-group">
                                    <label>Number (e.g. "500+")</label>
                                    <input type="text" name="about_stat<?php echo $i; ?>_number"
                                           value="<?php echo e($abt["about_stat{$i}_number"]); ?>" class="admin-form-input"/>
                                </div>
                                <div class="admin-form-group" style="margin-top:0.75rem;">
                                    <label>Description</label>
                                    <input type="text" name="about_stat<?php echo $i; ?>_desc"
                                           value="<?php echo e($abt["about_stat{$i}_desc"]); ?>" class="admin-form-input"/>
                                </div>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════
                 TAB 3: PHILOSOPHY SECTION
            ══════════════════════════════════════════ -->
            <div id="tab-philosophy" class="admin-tab-content">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:2rem;">

                    <div class="admin-card-box" style="margin:0; padding:2rem;">
                        <h3 style="font-size:1rem; font-weight:700; margin-bottom:1.5rem; border-bottom:1px solid #eee; padding-bottom:0.75rem;">
                            Left Panel
                        </h3>
                        <div class="admin-form-group">
                            <label>Section Label</label>
                            <input type="text" name="about_phili_label"
                                   value="<?php echo e($abt['about_phili_label']); ?>" class="admin-form-input"/>
                        </div>
                        <div class="admin-form-group" style="margin-top:1rem;">
                            <label>Heading (normal)</label>
                            <input type="text" name="about_phili_heading"
                                   value="<?php echo e($abt['about_phili_heading']); ?>" class="admin-form-input"/>
                        </div>
                        <div class="admin-form-group" style="margin-top:1rem;">
                            <label>Heading (gold accent)</label>
                            <input type="text" name="about_phili_heading_accent"
                                   value="<?php echo e($abt['about_phili_heading_accent']); ?>" class="admin-form-input"/>
                        </div>
                        <div class="admin-form-group" style="margin-top:1rem;">
                            <label>Large Quote</label>
                            <textarea name="about_phili_quote" rows="3" class="admin-form-textarea"><?php echo e($abt['about_phili_quote']); ?></textarea>
                        </div>
                    </div>

                    <div class="admin-card-box" style="margin:0; padding:2rem;">
                        <h3 style="font-size:1rem; font-weight:700; margin-bottom:1.5rem; border-bottom:1px solid #eee; padding-bottom:0.75rem;">
                            Right Panel — Text Paragraphs
                        </h3>
                        <div class="admin-form-group">
                            <label>Paragraph 1</label>
                            <textarea name="about_phili_text1" rows="4" class="admin-form-textarea"><?php echo e($abt['about_phili_text1']); ?></textarea>
                        </div>
                        <div class="admin-form-group" style="margin-top:1rem;">
                            <label>Paragraph 2</label>
                            <textarea name="about_phili_text2" rows="4" class="admin-form-textarea"><?php echo e($abt['about_phili_text2']); ?></textarea>
                        </div>
                        <div class="admin-form-group" style="margin-top:1rem;">
                            <label>Paragraph 3</label>
                            <textarea name="about_phili_text3" rows="4" class="admin-form-textarea"><?php echo e($abt['about_phili_text3']); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════
                 TAB 4: FOUNDER & CONTACT SECTION
            ══════════════════════════════════════════ -->
            <div id="tab-founder" class="admin-tab-content">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:2rem;">

                    <!-- Contact section header -->
                    <div class="admin-card-box" style="margin:0; padding:2rem; grid-column:span 2;">
                        <h3 style="font-size:1rem; font-weight:700; margin-bottom:1.5rem; border-bottom:1px solid #eee; padding-bottom:0.75rem;">
                            Section Header
                        </h3>
                        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem;">
                            <div class="admin-form-group">
                                <label>Heading (normal)</label>
                                <input type="text" name="about_contact_heading"
                                       value="<?php echo e($abt['about_contact_heading']); ?>" class="admin-form-input"/>
                            </div>
                            <div class="admin-form-group">
                                <label>Heading (gold accent)</label>
                                <input type="text" name="about_contact_heading_accent"
                                       value="<?php echo e($abt['about_contact_heading_accent']); ?>" class="admin-form-input"/>
                            </div>
                            <div class="admin-form-group" style="grid-column:span 3;">
                                <label>Description Text</label>
                                <textarea name="about_contact_desc" rows="3" class="admin-form-textarea"><?php echo e($abt['about_contact_desc']); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Founder info -->
                    <div class="admin-card-box" style="margin:0; padding:2rem;">
                        <h3 style="font-size:1rem; font-weight:700; margin-bottom:1.5rem; border-bottom:1px solid #eee; padding-bottom:0.75rem;">
                            Founder Card
                        </h3>
                        <div class="admin-form-group">
                            <label>Name</label>
                            <input type="text" name="about_founder_name"
                                   value="<?php echo e($abt['about_founder_name']); ?>" class="admin-form-input"/>
                        </div>
                        <div class="admin-form-group" style="margin-top:1rem;">
                            <label>Role / Title</label>
                            <input type="text" name="about_founder_role"
                                   value="<?php echo e($abt['about_founder_role']); ?>" class="admin-form-input"/>
                        </div>
                        <div class="admin-form-group" style="margin-top:1rem;">
                            <label>LinkedIn URL</label>
                            <input type="url" name="about_founder_linkedin"
                                   value="<?php echo e($abt['about_founder_linkedin']); ?>" class="admin-form-input"
                                   placeholder="https://www.linkedin.com/..."/>
                        </div>
                        <div class="admin-form-group" style="margin-top:1rem;">
                            <label>Quote / Bio</label>
                            <textarea name="about_founder_quote" rows="4" class="admin-form-textarea"><?php echo e($abt['about_founder_quote']); ?></textarea>
                        </div>
                    </div>

                    <!-- Founder photo -->
                    <div class="admin-card-box" style="margin:0; padding:2rem;">
                        <h3 style="font-size:1rem; font-weight:700; margin-bottom:0.5rem; border-bottom:1px solid #eee; padding-bottom:0.75rem;">
                            Founder Photo
                        </h3>
                        <p style="font-size:0.75rem; color:#888; margin-bottom:1.25rem;">
                            Portrait image shown on the founder card. Recommended: 400×400px.
                        </p>
                        <div class="admin-form-group">
                            <label>Image URL / Path</label>
                            <input type="text" id="about_founder_image" name="about_founder_image"
                                   value="<?php echo e($abt['about_founder_image']); ?>" class="admin-form-input"
                                   placeholder="/assets/images/founder.jpeg"/>
                        </div>

                        <div id="founderImgDropzone" class="image-upload-preview-box"
                             style="margin-top:1rem; border:1px dashed var(--accent-color); padding:1.5rem 1rem; border-radius:8px; text-align:center; cursor:pointer; transition:var(--transition-smooth);">
                            <p style="font-size:0.7rem; color:#777; margin:0;">Click or drag to upload a founder photo</p>
                            <span style="font-size:0.55rem; color:#999; display:block; margin-top:0.25rem;">JPG, PNG, WebP (Max 3MB)</span>
                            <input type="file" id="founderImgFileSelector" accept="image/*" style="display:none;"/>
                            <div id="founderImgStatus" style="font-size:0.65rem; color:var(--primary-color); margin-top:0.5rem; display:none;">Uploading...</div>
                            <img id="founderImgPreview"
                                 src="<?php echo !empty($abt['about_founder_image']) ? BASE_URL . $abt['about_founder_image'] : ''; ?>"
                                 style="display:<?php echo !empty($abt['about_founder_image']) ? 'inline-block' : 'none'; ?>; max-height:180px; max-width:100%; object-fit:cover; margin-top:1rem; border-radius:8px;"/>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Global Save Button -->
            <div style="border-top:1px solid #e5e7eb; padding-top:1.5rem; margin-top:2rem; display:flex; justify-content:flex-end; gap:1rem;">
                <a href="<?php echo BASE_URL; ?>/about" target="_blank"
                   style="padding:0.85rem 2rem; border-radius:8px; border:1px solid #ddd; text-decoration:none; color:#374151; font-weight:500; display:inline-flex; align-items:center; gap:0.5rem;">
                    Preview →
                </a>
                <button type="submit" class="btn-primary" style="padding:1rem 3rem; border-radius:8px;">
                    💾 Save About Page
                </button>
            </div>

        </form><!-- /aboutForm -->
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ── Tab switching ────────────────────────────────
    const triggers = document.querySelectorAll('.admin-tab-trigger');
    triggers.forEach(function(btn) {
        btn.addEventListener('click', function() {
            triggers.forEach(function(t) { t.classList.remove('active'); });
            document.querySelectorAll('.admin-tab-content').forEach(function(c) { c.classList.remove('active'); });
            this.classList.add('active');
            const panel = document.getElementById(this.getAttribute('data-target'));
            if (panel) panel.classList.add('active');
        });
    });

    // ── Generic image uploader helper ────────────────
    function setupImageUpload(dropzoneId, fileSelectorId, statusId, previewId, urlInputId) {
        const dropzone    = document.getElementById(dropzoneId);
        const fileInput   = document.getElementById(fileSelectorId);
        const statusEl    = document.getElementById(statusId);
        const previewEl   = document.getElementById(previewId);
        const urlInput    = document.getElementById(urlInputId);

        if (!dropzone || !fileInput) return;

        dropzone.addEventListener('click', function() { fileInput.click(); });
        dropzone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropzone.style.background = 'rgba(0,0,0,0.04)';
        });
        dropzone.addEventListener('dragleave', function() {
            dropzone.style.background = '';
        });
        dropzone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropzone.style.background = '';
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                uploadFile(fileInput.files[0]);
            }
        });
        fileInput.addEventListener('change', function() {
            if (fileInput.files.length > 0) uploadFile(fileInput.files[0]);
        });

        function uploadFile(file) {
            if (!file) return;
            statusEl.style.display = 'block';
            statusEl.textContent   = 'Uploading...';

            var fd = new FormData();
            fd.append('file', file);
            fd.append('context', 'about-page');
            fd.append('csrf_token', '<?php echo csrfToken(); ?>');

            fetch('<?php echo BASE_URL; ?>/api/upload', { method: 'POST', body: fd })
                .then(function(r) { return r.json(); })
                .then(function(res) {
                    if (res.success && res.url) {
                        urlInput.value = res.url;
                        previewEl.src  = '<?php echo BASE_URL; ?>' + res.url;
                        previewEl.style.display = 'inline-block';
                        statusEl.textContent = '✓ Uploaded!';
                        setTimeout(function() { statusEl.style.display = 'none'; }, 2500);
                    } else {
                        statusEl.textContent = '✗ Upload failed: ' + (res.error || 'Unknown error');
                    }
                })
                .catch(function() {
                    statusEl.textContent = '✗ Network error — try again.';
                });
        }
    }

    // Setup Hero image
    setupImageUpload('heroImgDropzone', 'heroImgFileSelector', 'heroImgStatus', 'heroImgPreview', 'about_hero_image');
    // Setup Founder photo
    setupImageUpload('founderImgDropzone', 'founderImgFileSelector', 'founderImgStatus', 'founderImgPreview', 'about_founder_image');
});
</script>

<?php
require_once PATH_ROOT . '/includes/footer.php';
?>
