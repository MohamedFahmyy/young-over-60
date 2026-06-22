<?php
// admin/settings.php
// Administrator Site Settings & Branding Control Panel

$error = null;
$success = null;

// Handle Settings Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Auth::requireCsrf();
        
        $data = [
            'siteName' => trim($_POST['siteName'] ?? 'Young Over 60'),
            'logoUrl' => trim($_POST['logoUrl'] ?? ''),
            'logoDarkUrl' => trim($_POST['logoDarkUrl'] ?? ''),
            'faviconUrl' => trim($_POST['faviconUrl'] ?? ''),
            'heroBackgroundUrl' => trim($_POST['heroBackgroundUrl'] ?? '/images/hero-bg.png'),
            'metaTitle' => trim($_POST['metaTitle'] ?? ''),
            'metaDescription' => trim($_POST['metaDescription'] ?? ''),
            'ogImage' => trim($_POST['ogImage'] ?? ''),
            'primaryColor' => trim($_POST['primaryColor'] ?? '#0F4C81'),
            'secondaryColor' => trim($_POST['secondaryColor'] ?? '#1e3c5a'),
            'accentColor' => trim($_POST['accentColor'] ?? '#D4A75C'),
            'backgroundColor' => trim($_POST['backgroundColor'] ?? '#F8F6F2'),
            'textColor' => trim($_POST['textColor'] ?? '#1F1F1F'),
            'surfaceColor' => trim($_POST['surfaceColor'] ?? '#ffffff'),
            'borderColor' => trim($_POST['borderColor'] ?? 'rgba(0,0,0,0.05)'),
            'themeMode' => trim($_POST['themeMode'] ?? 'light'),
            'fontFamily' => trim($_POST['fontFamily'] ?? 'Outfit'),
            'fontFamilyBody' => trim($_POST['fontFamilyBody'] ?? 'Inter'),
            'fontFamilyAr' => trim($_POST['fontFamilyAr'] ?? 'Cairo'),
            'fontFamilyArBody' => trim($_POST['fontFamilyArBody'] ?? 'Cairo'),
            'fontSize' => trim($_POST['fontSize'] ?? '16px'),
            'headingWeight' => trim($_POST['headingWeight'] ?? '700'),
            'bodyWeight' => trim($_POST['bodyWeight'] ?? '400'),
            'letterSpacing' => trim($_POST['letterSpacing'] ?? 'normal'),
            'lineHeight'    => trim($_POST['lineHeight'] ?? '1.8'),
            // Navigation Visibility Toggles
            'nav_show_podcasts'       => isset($_POST['nav_show_podcasts']) ? '1' : '0',
            'nav_show_stories'        => isset($_POST['nav_show_stories']) ? '1' : '0',
            'nav_show_news'           => isset($_POST['nav_show_news']) ? '1' : '0',
            'nav_show_accessibility'  => isset($_POST['nav_show_accessibility']) ? '1' : '0',
            'nav_show_contact'        => isset($_POST['nav_show_contact']) ? '1' : '0',
            'contact_whatsapp'        => trim($_POST['contact_whatsapp'] ?? ''),
            'contact_phone'           => trim($_POST['contact_phone'] ?? ''),
        ];

        if (empty($data['siteName'])) {
            $error = __('settings_sitename_required');
        } else {
            $dbConnection = Database::getInstance()->getConnection();
            $dbConnection->beginTransaction();
            
            $settingsUpdated = $pm->updateSiteSettings($data);
            
            // Save dynamic menu visibility
            $menuStmt = $dbConnection->query("SELECT id FROM menus");
            $dbMenus = $menuStmt->fetchAll();
            foreach ($dbMenus as $dbMenu) {
                $isActive = isset($_POST['menu_show_' . $dbMenu['id']]) ? 1 : 0;
                $updateStmt = $dbConnection->prepare("UPDATE menus SET isActive = :isActive WHERE id = :id");
                $updateStmt->execute(['isActive' => $isActive, 'id' => $dbMenu['id']]);
            }
            
            if ($settingsUpdated) {
                $dbConnection->commit();
                $pm->clearCache(); // Force immediate cache invalidation
                $success = __('settings_success_save');
                // Refresh settings array
                $settings = $pm->getSiteSettings();
            } else {
                $dbConnection->rollBack();
                $error = __('settings_save_error');
            }
        }
    } catch (Exception $e) {
        $error = __('admin_error') . ": " . $e->getMessage();
    }
}

// Set SEO Meta & Header
$seoPageType = 'admin';
require_once PATH_ROOT . '/includes/header.php';
?>

<div class="admin-layout">
    <!-- Sidebar -->
    <?php require_once PATH_ROOT . '/includes/admin-sidebar.php'; ?>

    <!-- Main Workspace -->
    <main class="admin-main">
        <div class="admin-header-row" style="border-bottom: 1px solid #e5e7eb; padding-bottom: 1.5rem; margin-bottom: 2rem;">
            <div>
                <h1 class="admin-title"><?php echo __('admin_settings'); ?></h1>
                <p style="color: #888; font-size: 0.9rem; margin-top: 0.25rem;"><?php echo __('settings_manage_desc'); ?></p>
            </div>
        </div>

        <!-- Alert messages -->
        <?php if ($success): ?>
            <div class="form-status-alert success" style="margin-bottom: 2rem;">
                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span><?php echo e($success); ?></span>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="form-status-alert error" style="margin-bottom: 2rem;">
                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span><?php echo e($error); ?></span>
            </div>
        <?php endif; ?>

        <form id="settingsForm" action="<?php echo BASE_URL . $lang_prefix_url; ?>/admin/settings" method="POST" style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
            <?php echo Auth::csrfInput(); ?>

            <div class="admin-grid-layout admin-split-12-1">
                
                <!-- Left Column: Settings Fields -->
                <div style="display: flex; flex-direction: column; gap: 2rem;">
                    
                    <!-- General Settings -->
                    <div class="admin-card-box" style="margin: 0; padding: 2rem; display: flex; flex-direction: column; gap: 1.25rem;">
                        <h3 style="font-size: 1.05rem; font-weight: 700; border-bottom: 1px solid #eee; padding-bottom: 0.75rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <?php echo __('settings_general_identity'); ?>
                        </h3>
                        
                        <div class="admin-form-group">
                            <label for="set-sitename"><?php echo __('settings_site_name'); ?></label>
                            <input type="text" id="set-sitename" name="siteName" value="<?php echo e($settings['siteName'] ?? 'Young Over 60'); ?>" class="admin-form-input" required />
                        </div>
 
                        <div class="admin-form-group">
                            <label for="set-herobg"><?php echo __('settings_hero_bg'); ?></label>
                            <input type="text" id="set-herobg" name="heroBackgroundUrl" value="<?php echo e($settings['heroBackgroundUrl'] ?? '/images/hero-bg.png'); ?>" class="admin-form-input" />
                        </div>

                        <div class="admin-form-group">
                            <label for="set-contact-whatsapp"><?php echo __('settings_contact_whatsapp'); ?></label>
                            <input type="text" id="set-contact-whatsapp" name="contact_whatsapp" value="<?php echo e($settings['contact_whatsapp'] ?? ''); ?>" class="admin-form-input" placeholder="e.g. +61 400 000 000 or https://wa.me/..." />
                        </div>

                        <div class="admin-form-group">
                            <label for="set-contact-phone"><?php echo __('settings_contact_phone'); ?></label>
                            <input type="text" id="set-contact-phone" name="contact_phone" value="<?php echo e($settings['contact_phone'] ?? ''); ?>" class="admin-form-input" placeholder="e.g. +61 400 000 000" />
                        </div>
                    </div>
 
                    <!-- SEO Meta Configuration -->
                    <div class="admin-card-box" style="margin: 0; padding: 2rem; display: flex; flex-direction: column; gap: 1.25rem;">
                        <h3 style="font-size: 1.05rem; font-weight: 700; border-bottom: 1px solid #eee; padding-bottom: 0.75rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <?php echo __('settings_seo_config'); ?>
                        </h3>
                        
                        <div class="admin-form-group">
                            <label for="set-metatitle"><?php echo __('settings_meta_title'); ?></label>
                            <input type="text" id="set-metatitle" name="metaTitle" value="<?php echo e($settings['metaTitle'] ?? ''); ?>" class="admin-form-input" placeholder="e.g. Young Over 60 | Active Senior Inspiration" />
                            <span style="font-size: 0.65rem; color: #888; margin-top: 0.25rem;"><?php echo __('settings_meta_title_hint'); ?></span>
                        </div>
 
                        <div class="admin-form-group">
                            <label for="set-metadesc"><?php echo __('settings_meta_desc'); ?></label>
                            <textarea id="set-metadesc" name="metaDescription" rows="4" class="admin-form-textarea" placeholder="..."><?php echo e($settings['metaDescription'] ?? ''); ?></textarea>
                            <span style="font-size: 0.65rem; color: #888; margin-top: 0.25rem;"><?php echo __('settings_meta_desc_hint'); ?></span>
                        </div>
                    </div>
 
                    <!-- Brand Color Palette & Theme Manager -->
                    <div class="admin-card-box" style="margin: 0; padding: 2rem; display: flex; flex-direction: column; gap: 1.25rem;">
                        <h3 style="font-size: 1.05rem; font-weight: 700; border-bottom: 1px solid #eee; padding-bottom: 0.75rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                            <?php echo __('settings_design_palette'); ?>
                        </h3>
                        
                        <div class="admin-grid-fields-2">
                            
                            <!-- Theme Mode -->
                            <div class="admin-form-group" style="grid-column: span 2;">
                                <label for="set-theme-mode"><?php echo __('settings_theme_mode_default'); ?></label>
                                <select id="set-theme-mode" name="themeMode" class="admin-form-input" onchange="updateLivePreview();">
                                    <option value="light" <?php echo ($settings['themeMode'] ?? 'light') === 'light' ? 'selected' : ''; ?>><?php echo __('settings_mode_light'); ?></option>
                                    <option value="dark" <?php echo ($settings['themeMode'] ?? 'light') === 'dark' ? 'selected' : ''; ?>><?php echo __('settings_mode_dark'); ?></option>
                                </select>
                            </div>

                            <!-- Primary Color -->
                            <div class="admin-form-group">
                                <label for="color-primary"><?php echo __('settings_primary_color_label'); ?></label>
                                <div style="display: flex; gap: 0.5rem;">
                                    <input type="color" value="<?php echo e($settings['primaryColor'] ?? '#0F4C81'); ?>" oninput="document.getElementById('color-primary-text').value = this.value; updateLivePreview();" style="width: 42px; height: 42px; border: 1px solid #ddd; border-radius: 8px; cursor: pointer; padding: 0;" />
                                    <input type="text" id="color-primary-text" name="primaryColor" value="<?php echo e($settings['primaryColor'] ?? '#0F4C81'); ?>" class="admin-form-input" style="flex-grow:1;" oninput="updateLivePreview();" />
                                </div>
                            </div>

                            <!-- Secondary Color -->
                            <div class="admin-form-group">
                                <label for="color-secondary"><?php echo __('settings_secondary_color_label'); ?></label>
                                <div style="display: flex; gap: 0.5rem;">
                                    <input type="color" value="<?php echo e($settings['secondaryColor'] ?? '#1e3c5a'); ?>" oninput="document.getElementById('color-secondary-text').value = this.value; updateLivePreview();" style="width: 42px; height: 42px; border: 1px solid #ddd; border-radius: 8px; cursor: pointer; padding: 0;" />
                                    <input type="text" id="color-secondary-text" name="secondaryColor" value="<?php echo e($settings['secondaryColor'] ?? '#1e3c5a'); ?>" class="admin-form-input" style="flex-grow:1;" oninput="updateLivePreview();" />
                                </div>
                            </div>

                            <!-- Accent Color -->
                            <div class="admin-form-group">
                                <label for="color-accent"><?php echo __('settings_accent_color_label'); ?></label>
                                <div style="display: flex; gap: 0.5rem;">
                                    <input type="color" value="<?php echo e($settings['accentColor'] ?? '#D4A75C'); ?>" oninput="document.getElementById('color-accent-text').value = this.value; updateLivePreview();" style="width: 42px; height: 42px; border: 1px solid #ddd; border-radius: 8px; cursor: pointer; padding: 0;" />
                                    <input type="text" id="color-accent-text" name="accentColor" value="<?php echo e($settings['accentColor'] ?? '#D4A75C'); ?>" class="admin-form-input" style="flex-grow:1;" oninput="updateLivePreview();" />
                                </div>
                            </div>

                            <!-- Background Color -->
                            <div class="admin-form-group">
                                <label for="color-bg"><?php echo __('settings_bg_color_label'); ?></label>
                                <div style="display: flex; gap: 0.5rem;">
                                    <input type="color" value="<?php echo e($settings['backgroundColor'] ?? '#F8F6F2'); ?>" oninput="document.getElementById('color-bg-text').value = this.value; updateLivePreview();" style="width: 42px; height: 42px; border: 1px solid #ddd; border-radius: 8px; cursor: pointer; padding: 0;" />
                                    <input type="text" id="color-bg-text" name="backgroundColor" value="<?php echo e($settings['backgroundColor'] ?? '#F8F6F2'); ?>" class="admin-form-input" style="flex-grow:1;" oninput="updateLivePreview();" />
                                </div>
                            </div>

                            <!-- Surface Color -->
                            <div class="admin-form-group">
                                <label for="color-surface"><?php echo __('settings_surface_color_label'); ?></label>
                                <div style="display: flex; gap: 0.5rem;">
                                    <input type="color" value="<?php echo e($settings['surfaceColor'] ?? '#ffffff'); ?>" oninput="document.getElementById('color-surface-text').value = this.value; updateLivePreview();" style="width: 42px; height: 42px; border: 1px solid #ddd; border-radius: 8px; cursor: pointer; padding: 0;" />
                                    <input type="text" id="color-surface-text" name="surfaceColor" value="<?php echo e($settings['surfaceColor'] ?? '#ffffff'); ?>" class="admin-form-input" style="flex-grow:1;" oninput="updateLivePreview();" />
                                </div>
                            </div>

                            <!-- Border Color -->
                            <div class="admin-form-group">
                                <label for="color-border"><?php echo __('settings_border_color_label'); ?></label>
                                <div style="display: flex; gap: 0.5rem;">
                                    <input type="color" value="<?php echo e(str_starts_with($settings['borderColor'] ?? 'rgba(0,0,0,0.05)', '#') ? $settings['borderColor'] : '#e5e7eb'); ?>" oninput="document.getElementById('color-border-text').value = this.value; updateLivePreview();" style="width: 42px; height: 42px; border: 1px solid #ddd; border-radius: 8px; cursor: pointer; padding: 0;" />
                                    <input type="text" id="color-border-text" name="borderColor" value="<?php echo e($settings['borderColor'] ?? 'rgba(0,0,0,0.05)'); ?>" class="admin-form-input" style="flex-grow:1;" oninput="updateLivePreview();" />
                                </div>
                            </div>

                            <!-- Text Color -->
                            <div class="admin-form-group" style="grid-column: span 2;">
                                <label for="color-text"><?php echo __('settings_text_color_label'); ?></label>
                                <div style="display: flex; gap: 0.5rem;">
                                    <input type="color" value="<?php echo e($settings['textColor'] ?? '#1F1F1F'); ?>" oninput="document.getElementById('color-text-text').value = this.value; updateLivePreview();" style="width: 42px; height: 42px; border: 1px solid #ddd; border-radius: 8px; cursor: pointer; padding: 0;" />
                                    <input type="text" id="color-text-text" name="textColor" value="<?php echo e($settings['textColor'] ?? '#1F1F1F'); ?>" class="admin-form-input" style="flex-grow:1;" oninput="updateLivePreview();" />
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Advanced Typography Controls -->
                    <div class="admin-card-box" style="margin: 0; padding: 2rem; display: flex; flex-direction: column; gap: 1.25rem;">
                        <h3 style="font-size: 1.05rem; font-weight: 700; border-bottom: 1px solid #eee; padding-bottom: 0.75rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            <?php echo __('settings_section_weights'); ?>
                        </h3>
                        
                        <div class="admin-grid-fields-2">
                            
                            <!-- Heading Font Family -->
                            <div class="admin-form-group">
                                <label for="set-font-family"><?php echo __('settings_typography_heading_en'); ?></label>
                                <select id="set-font-family" name="fontFamily" class="admin-form-input" onchange="updateLivePreview();">
                                    <?php 
                                    $enHeadingFonts = ['Outfit', 'Inter', 'Roboto', 'Open Sans', 'Montserrat', 'Playfair Display', 'Lora', 'Merriweather'];
                                    foreach ($enHeadingFonts as $f): 
                                        $selected = ($settings['fontFamily'] ?? 'Outfit') === $f ? 'selected' : '';
                                        echo "<option value=\"$f\" $selected>$f</option>";
                                    endforeach;
                                    ?>
                                </select>
                            </div>

                            <!-- Body Font Family -->
                            <div class="admin-form-group">
                                <label for="set-font-family-body"><?php echo __('settings_typography_body_en'); ?></label>
                                <select id="set-font-family-body" name="fontFamilyBody" class="admin-form-input" onchange="updateLivePreview();">
                                    <?php 
                                    $enBodyFonts = ['Inter', 'Outfit', 'Roboto', 'Open Sans', 'Montserrat', 'Lora', 'Merriweather'];
                                    foreach ($enBodyFonts as $f): 
                                        $selected = ($settings['fontFamilyBody'] ?? 'Inter') === $f ? 'selected' : '';
                                        echo "<option value=\"$f\" $selected>$f</option>";
                                    endforeach;
                                    ?>
                                </select>
                            </div>

                            <!-- Arabic Heading Font -->
                            <div class="admin-form-group">
                                <label for="set-font-family-ar"><?php echo __('settings_typography_heading_ar'); ?></label>
                                <select id="set-font-family-ar" name="fontFamilyAr" class="admin-form-input" onchange="updateLivePreview();">
                                    <?php 
                                    $arHeadingFonts = ['Cairo', 'Tajawal', 'Almarai', 'Amiri'];
                                    foreach ($arHeadingFonts as $f): 
                                        $selected = ($settings['fontFamilyAr'] ?? 'Cairo') === $f ? 'selected' : '';
                                        echo "<option value=\"$f\" $selected>$f</option>";
                                    endforeach;
                                    ?>
                                </select>
                            </div>

                            <!-- Arabic Body Font -->
                            <div class="admin-form-group">
                                <label for="set-font-family-ar-body"><?php echo __('settings_typography_body_ar'); ?></label>
                                <select id="set-font-family-ar-body" name="fontFamilyArBody" class="admin-form-input" onchange="updateLivePreview();">
                                    <?php 
                                    $arBodyFonts = ['Cairo', 'Tajawal', 'Almarai', 'Amiri'];
                                    foreach ($arBodyFonts as $f): 
                                        $selected = ($settings['fontFamilyArBody'] ?? 'Cairo') === $f ? 'selected' : '';
                                        echo "<option value=\"$f\" $selected>$f</option>";
                                    endforeach;
                                    ?>
                                </select>
                            </div>

                            <!-- Base Font Size -->
                            <div class="admin-form-group">
                                <label for="set-font-size"><?php echo __('settings_typography_size'); ?></label>
                                <select id="set-font-size" name="fontSize" class="admin-form-input" onchange="updateLivePreview();">
                                    <?php 
                                    $fontSizes = ['14px', '15px', '16px', '17px', '18px', '20px', '22px', '24px'];
                                    foreach ($fontSizes as $size): 
                                        $selected = ($settings['fontSize'] ?? '16px') === $size ? 'selected' : '';
                                        echo "<option value=\"$size\" $selected>$size</option>";
                                    endforeach;
                                    ?>
                                </select>
                            </div>

                            <!-- Heading Weight -->
                            <div class="admin-form-group">
                                <label for="set-heading-weight"><?php echo __('settings_typography_heading_weight'); ?></label>
                                <select id="set-heading-weight" name="headingWeight" class="admin-form-input" onchange="updateLivePreview();">
                                    <?php 
                                    $weights = ['300', '400', '500', '700', '900'];
                                    foreach ($weights as $w): 
                                        $selected = ($settings['headingWeight'] ?? '700') === $w ? 'selected' : '';
                                        echo "<option value=\"$w\" $selected>$w</option>";
                                    endforeach;
                                    ?>
                                </select>
                            </div>

                            <!-- Body Weight -->
                            <div class="admin-form-group">
                                <label for="set-body-weight"><?php echo __('settings_typography_body_weight'); ?></label>
                                <select id="set-body-weight" name="bodyWeight" class="admin-form-input" onchange="updateLivePreview();">
                                    <?php 
                                    $weightsBody = ['300', '400', '500'];
                                    foreach ($weightsBody as $w): 
                                        $selected = ($settings['bodyWeight'] ?? '400') === $w ? 'selected' : '';
                                        echo "<option value=\"$w\" $selected>$w</option>";
                                    endforeach;
                                    ?>
                                </select>
                            </div>

                            <!-- Letter Spacing -->
                            <div class="admin-form-group">
                                <label for="set-letter-spacing"><?php echo __('settings_typography_spacing'); ?></label>
                                <select id="set-letter-spacing" name="letterSpacing" class="admin-form-input" onchange="updateLivePreview();">
                                    <?php 
                                    $spacings = ['normal', '-0.02em', '-0.01em', '0.05em', '0.1em', '0.15em', '0.2em'];
                                    foreach ($spacings as $s): 
                                        $selected = ($settings['letterSpacing'] ?? 'normal') === $s ? 'selected' : '';
                                        echo "<option value=\"$s\" $selected>$s</option>";
                                    endforeach;
                                    ?>
                                </select>
                            </div>

                            <!-- Line Height -->
                            <div class="admin-form-group" style="grid-column: span 2;">
                                <label for="set-line-height"><?php echo __('settings_typography_line_height'); ?></label>
                                <select id="set-line-height" name="lineHeight" class="admin-form-input" onchange="updateLivePreview();">
                                    <?php 
                                    $heights = ['1.4', '1.5', '1.6', '1.7', '1.8', '2.0'];
                                    foreach ($heights as $h): 
                                        $selected = ($settings['lineHeight'] ?? '1.8') === $h ? 'selected' : '';
                                        echo "<option value=\"$h\" $selected>$h</option>";
                                    endforeach;
                                    ?>
                                </select>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Right Column: Logos & Favicon Upload Cards -->
                <div style="display: flex; flex-direction: column; gap: 2rem;">
                    
                    <!-- Light Theme Logo -->
                    <div class="admin-card-box" style="margin: 0; padding: 2rem;">
                        <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                            <?php echo __('settings_light_logo'); ?>
                        </h3>
                        <p style="font-size: 0.65rem; color:#888; margin-bottom: 1.25rem;"><?php echo __('settings_light_logo_desc'); ?></p>
                        
                        <div class="admin-form-group">
                            <label for="set-logo"><?php echo __('admin_brand_logo_url'); ?></label>
                            <input type="text" id="set-logo" name="logoUrl" value="<?php echo e($settings['logoUrl'] ?? ''); ?>" class="admin-form-input" placeholder="/uploads/..." />
                        </div>
                        
                        <div id="logoDropzone" class="image-upload-preview-box" style="margin-top: 1rem; border: 1px dashed var(--accent-color); padding: 1.5rem 1rem; border-radius: 8px; text-align: center; cursor: pointer; transition: var(--transition-smooth);">
                            <p style="font-size: 0.7rem; color: #777; margin: 0;"><?php echo __('settings_click_drop'); ?></p>
                            <span style="font-size: 0.55rem; color:#999; display:block; margin-top:0.25rem;">PNG, JPG, WebP, SVG (Max 2MB)</span>
                            <input type="file" id="logoFileSelector" accept="image/*" style="display:none;" />
                            <div id="logoStatusText" style="font-size:0.65rem; color: var(--primary-color); margin-top:0.5rem; display:none;">Uploading...</div>
                            <img id="logoPreview" src="<?php echo $settings['logoUrl'] ? BASE_URL . $settings['logoUrl'] : ''; ?>" style="display: <?php echo $settings['logoUrl'] ? 'inline-block' : 'none'; ?>; max-height: 50px; max-width: 100%; object-fit: contain; margin-top: 1rem; border-radius: 4px; background: #f9f9f9; padding: 0.25rem;" />
                        </div>
                    </div>

                    <!-- Dark Theme Logo -->
                    <div class="admin-card-box" style="margin: 0; padding: 2rem;">
                        <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                            <?php echo __('settings_dark_logo'); ?>
                        </h3>
                        <p style="font-size: 0.65rem; color:#888; margin-bottom: 1.25rem;"><?php echo __('settings_dark_logo_desc'); ?></p>
                        
                        <div class="admin-form-group">
                            <label for="set-logo-dark">Dark Logo Image URL</label>
                            <input type="text" id="set-logo-dark" name="logoDarkUrl" value="<?php echo e($settings['logoDarkUrl'] ?? ''); ?>" class="admin-form-input" placeholder="/uploads/..." />
                        </div>
                        
                        <div id="logoDarkDropzone" class="image-upload-preview-box" style="margin-top: 1rem; border: 1px dashed var(--accent-color); padding: 1.5rem 1rem; border-radius: 8px; text-align: center; cursor: pointer; transition: var(--transition-smooth);">
                            <p style="font-size: 0.7rem; color: #777; margin: 0;"><?php echo __('settings_click_drop'); ?></p>
                            <span style="font-size: 0.55rem; color:#999; display:block; margin-top:0.25rem;">PNG, JPG, WebP, SVG (Max 2MB)</span>
                            <input type="file" id="logoDarkFileSelector" accept="image/*" style="display:none;" />
                            <div id="logoDarkStatusText" style="font-size:0.65rem; color: var(--primary-color); margin-top:0.5rem; display:none;">Uploading...</div>
                            <img id="logoDarkPreview" src="<?php echo !empty($settings['logoDarkUrl']) ? BASE_URL . $settings['logoDarkUrl'] : ''; ?>" style="display: <?php echo !empty($settings['logoDarkUrl']) ? 'inline-block' : 'none'; ?>; max-height: 50px; max-width: 100%; object-fit: contain; margin-top: 1rem; border-radius: 4px; background: #222; padding: 0.25rem;" />
                        </div>
                    </div>

                    <!-- Favicon Upload Card -->
                    <div class="admin-card-box" style="margin: 0; padding: 2rem;">
                        <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                            <?php echo __('settings_browser_favicon'); ?>
                        </h3>
                        <p style="font-size: 0.65rem; color:#888; margin-bottom: 1.25rem;"><?php echo __('settings_favicon_desc'); ?></p>
                        
                        <div class="admin-form-group">
                            <label for="set-favicon"><?php echo __('admin_favicon_url'); ?></label>
                            <input type="text" id="set-favicon" name="faviconUrl" value="<?php echo e($settings['faviconUrl'] ?? ''); ?>" class="admin-form-input" placeholder="/favicon.ico or /uploads/..." />
                        </div>
                        
                        <div id="faviconDropzone" class="image-upload-preview-box" style="margin-top: 1rem; border: 1px dashed var(--accent-color); padding: 1.5rem 1rem; border-radius: 8px; text-align: center; cursor: pointer; transition: var(--transition-smooth);">
                            <p style="font-size: 0.7rem; color: #777; margin: 0;"><?php echo __('settings_click_drop_favicon'); ?></p>
                            <span style="font-size: 0.55rem; color:#999; display:block; margin-top:0.25rem;">ICO, PNG, SVG, GIF (Max 2MB)</span>
                            <input type="file" id="faviconFileSelector" accept="image/x-icon,image/vnd.microsoft.icon,image/png,image/svg+xml,image/gif" style="display:none;" />
                            <div id="faviconStatusText" style="font-size:0.65rem; color: var(--primary-color); margin-top:0.5rem; display:none;">Uploading...</div>
                            <img id="faviconPreview" src="<?php echo $settings['faviconUrl'] ? BASE_URL . $settings['faviconUrl'] : ''; ?>" style="display: <?php echo $settings['faviconUrl'] ? 'inline-block' : 'none'; ?>; max-height: 32px; max-width: 32px; object-fit: contain; margin-top: 1rem; border-radius: 4px; padding: 0.15rem; background: #fff;" />
                        </div>
                    </div>

                    <!-- SEO Open Graph Image Card -->
                    <div class="admin-card-box" style="margin: 0; padding: 2rem;">
                        <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                            <?php echo __('settings_social_share'); ?>
                        </h3>
                        <p style="font-size: 0.65rem; color:#888; margin-bottom: 1.25rem;"><?php echo __('settings_social_share_desc'); ?></p>
                        
                        <div class="admin-form-group">
                            <label for="set-ogimage"><?php echo __('admin_seo_og_url'); ?></label>
                            <input type="text" id="set-ogimage" name="ogImage" value="<?php echo e($settings['ogImage'] ?? ''); ?>" class="admin-form-input" placeholder="/uploads/..." />
                        </div>
                        
                        <div id="ogDropzone" class="image-upload-preview-box" style="margin-top: 1rem; border: 1px dashed var(--accent-color); padding: 1.5rem 1rem; border-radius: 8px; text-align: center; cursor: pointer; transition: var(--transition-smooth);">
                            <p style="font-size: 0.7rem; color: #777; margin: 0;"><?php echo __('settings_click_drop'); ?></p>
                            <span style="font-size: 0.55rem; color:#999; display:block; margin-top:0.25rem;">PNG, JPG, WebP (Max 2MB)</span>
                            <input type="file" id="ogFileSelector" accept="image/*" style="display:none;" />
                            <div id="ogStatusText" style="font-size:0.65rem; color: var(--primary-color); margin-top:0.5rem; display:none;">Uploading...</div>
                            <img id="ogPreview" src="<?php echo $settings['ogImage'] ? BASE_URL . $settings['ogImage'] : ''; ?>" style="display: <?php echo $settings['ogImage'] ? 'inline-block' : 'none'; ?>; max-height: 80px; max-width: 100%; object-fit: cover; margin-top: 1rem; border-radius: 4px;" />
                        </div>
                    </div>

                    <!-- Live Theme & Style Preview Card -->
                    <div class="admin-card-box theme-preview-container" style="margin: 0; padding: 2rem; position: sticky; top: 2rem; z-index: 5;">
                        <h3 style="font-size: 1.05rem; font-weight: 700; border-bottom: 1px solid #eee; padding-bottom: 0.75rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                            <?php echo __('settings_live_preview'); ?>
                        </h3>
                        
                        <div id="theme-preview-card" style="border-radius: 12px; border: 1px solid #ddd; overflow: hidden; padding: 1.5rem; transition: all 0.3s ease;">
                            <!-- Hero Preview -->
                            <div id="prev-hero" style="position: relative; height: 140px; background-size: cover; background-position: center; border-radius: 8px; display: flex; align-items: center; justify-content: center; overflow: hidden; margin-bottom: 1.5rem; background-image: url('<?php echo BASE_URL; ?>/images/hero-bg.png');">
                                <div id="prev-hero-overlay" style="position: absolute; inset: 0; background: rgba(0,0,0,0.5);"></div>
                                <div style="position: relative; text-align: center; color: #fff; padding: 1rem; z-index: 2;">
                                    <h4 id="prev-hero-title" style="font-size: 1.25rem; margin: 0; font-weight: 700; line-height: 1.2;"><?php echo __('settings_sim_hero_title'); ?></h4>
                                    <p id="prev-hero-sub" style="font-size: 0.65rem; margin: 0.25rem 0 0 0; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.1em;"><?php echo __('settings_sim_hero_sub'); ?></p>
                                </div>
                            </div>
                            
                            <!-- Typography Preview -->
                            <div style="margin-bottom: 1.5rem;">
                                <h4 id="prev-heading" style="margin-bottom: 0.5rem; font-size: 1.25rem; line-height: 1.2;"><?php echo __('settings_sim_heading'); ?></h4>
                                <p id="prev-body" style="font-size: 0.85rem; margin: 0;">
                                    <?php echo __('settings_sim_body'); ?>
                                </p>
                            </div>
                            
                            <!-- Buttons Preview -->
                            <div style="display: flex; gap: 0.75rem; margin-bottom: 1.5rem;">
                                <button type="button" id="prev-btn-primary" style="flex-grow: 1; padding: 0.6rem 1rem; font-size: 0.8rem; border-radius: 30px; border: none; cursor: default;"><?php echo __('settings_sim_primary_btn'); ?></button>
                                <button type="button" id="prev-btn-secondary" style="flex-grow: 1; padding: 0.6rem 1rem; font-size: 0.8rem; border-radius: 30px; border: 1px solid #ddd; background: transparent; cursor: default;"><?php echo __('settings_sim_secondary_btn'); ?></button>
                            </div>
                            
                            <!-- Form Preview -->
                            <div style="margin-bottom: 1.5rem;">
                                <label id="prev-form-label" style="font-size: 0.65rem; text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 0.25rem;"><?php echo __('settings_sim_input_label'); ?></label>
                                <input type="text" id="prev-form-input" value="<?php echo addslashes(__('settings_sim_input_val')); ?>" style="width: 100%; padding: 0.5rem; font-size: 0.8rem; border-radius: 6px; border: 1px solid #ddd; background: transparent;" readonly />
                            </div>
                            
                            <!-- Card Preview -->
                            <div id="prev-card" style="border-radius: 8px; border: 1px solid #eee; overflow: hidden; padding: 1rem; display: flex; gap: 1rem; align-items: center;">
                                <div style="width: 50px; height: 50px; background: #eee; border-radius: 4px; flex-shrink: 0; background-image: url('<?php echo BASE_URL; ?>/images/australia.png'); background-size: cover;"></div>
                                <div>
                                    <h5 id="prev-card-title" style="font-size: 0.85rem; margin: 0; font-weight: 700; line-height: 1.2;"><?php echo __('settings_sim_card_title'); ?></h5>
                                    <p id="prev-card-desc" style="font-size: 0.7rem; margin: 0.2rem 0 0 0; opacity: 0.7; line-height: 1.3;"><?php echo __('settings_sim_card_desc'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ===== Navigation Visibility ===== -->
            <div class="admin-card-box" style="margin: 0; padding: 2rem;">
                <h3 style="font-size: 1.05rem; font-weight: 700; border-bottom: 1px solid #eee; padding-bottom: 0.75rem; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    <?php echo __('settings_nav_visibility'); ?>
                </h3>
                <p style="font-size: 0.8rem; color: #888; margin-bottom: 1.5rem;"><?php echo __('settings_nav_visibility_desc'); ?></p>

                <!-- Section 1: Static Website Sections -->
                <h4 style="font-size: 0.9rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--primary-color);">Static Website Sections</h4>
                <div class="admin-grid-fields-2" style="gap: 0.75rem; margin-bottom: 2rem;">
                    <?php
                    $navItems = [
                        'nav_show_podcasts'      => __('nav_podcasts'),
                        'nav_show_stories'       => __('nav_stories'),
                        'nav_show_news'          => __('nav_news'),
                        'nav_show_accessibility' => __('nav_accessibility'),
                        'nav_show_contact'       => __('nav_contact'),
                    ];
                    foreach ($navItems as $key => $label):
                        $isOn = (int)($settings[$key] ?? 1) === 1;
                    ?>
                    <label style="display: flex; align-items: center; gap: 0.75rem; padding: 0.85rem 1rem; border: 1px solid var(--border-color); border-radius: 8px; cursor: pointer; transition: background 0.2s; background: var(--surface-color);" onmouseover="this.style.background='rgba(15,76,129,0.05)'" onmouseout="this.style.background='var(--surface-color)'">
                        <input type="checkbox" name="<?php echo $key; ?>" value="1" <?php echo $isOn ? 'checked' : ''; ?> style="width: 1rem; height: 1rem; accent-color: var(--primary-color); cursor: pointer;" />
                        <span style="font-size: 0.9rem; font-weight: 500;"><?php echo e($label); ?></span>
                        <span style="margin-inline-start: auto; font-size: 0.7rem; color: <?php echo $isOn ? '#10b981' : '#f59e0b'; ?>; font-weight: 600;">
                            <?php echo $isOn ? __('pages_status_published') : __('pages_status_draft'); ?>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>

                <!-- Section 2: Dynamic Category Menus -->
                <h4 style="font-size: 0.9rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--primary-color);">Dynamic Navigation Menus</h4>
                <div class="admin-grid-fields-2" style="gap: 0.75rem;">
                    <?php
                    $db = Database::getInstance()->getConnection();
                    $dynamicMenusQuery = $db->query("SELECT * FROM menus ORDER BY `order` ASC");
                    $dynamicMenus = $dynamicMenusQuery->fetchAll();
                    foreach ($dynamicMenus as $menu):
                        $menuIsOn = (int)$menu['isActive'] === 1;
                    ?>
                    <label style="display: flex; align-items: center; gap: 0.75rem; padding: 0.85rem 1rem; border: 1px solid var(--border-color); border-radius: 8px; cursor: pointer; transition: background 0.2s; background: var(--surface-color);" onmouseover="this.style.background='rgba(15,76,129,0.05)'" onmouseout="this.style.background='var(--surface-color)'">
                        <input type="checkbox" name="menu_show_<?php echo $menu['id']; ?>" value="1" <?php echo $menuIsOn ? 'checked' : ''; ?> style="width: 1rem; height: 1rem; accent-color: var(--primary-color); cursor: pointer;" />
                        <span style="font-size: 0.9rem; font-weight: 500;"><?php echo e(t($menu, 'title')); ?></span>
                        <span style="margin-inline-start: auto; font-size: 0.7rem; color: <?php echo $menuIsOn ? '#10b981' : '#f59e0b'; ?>; font-weight: 600;">
                            <?php echo $menuIsOn ? __('pages_status_published') : __('pages_status_draft'); ?>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>

                <p style="font-size: 0.75rem; color: #888; margin-top: 1.5rem;">
                    <?php echo __('settings_nav_custom_pages_note'); ?>
                </p>
            </div>

            <!-- Form Actions -->
            <div style="border-top: 1px solid #e5e7eb; padding-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
                <button type="submit" class="btn-primary" style="padding: 1rem 3.5rem; border-radius: 8px;"><?php echo __('admin_save'); ?></button>
            </div>

        </form>

        <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Drag and drop helper binds
            setupUploader('logoDropzone', 'logoFileSelector', 'set-logo', 'logoPreview', 'logoStatusText');
            setupUploader('logoDarkDropzone', 'logoDarkFileSelector', 'set-logo-dark', 'logoDarkPreview', 'logoDarkStatusText');
            setupUploader('faviconDropzone', 'faviconFileSelector', 'set-favicon', 'faviconPreview', 'faviconStatusText');
            setupUploader('ogDropzone', 'ogFileSelector', 'set-ogimage', 'ogPreview', 'ogStatusText');

            function setupUploader(dropzoneId, selectorId, inputId, previewId, statusTextId) {
                const dropzone = document.getElementById(dropzoneId);
                const selector = document.getElementById(selectorId);
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);
                const statusBox = document.getElementById(statusTextId);

                if (!dropzone || !selector) return;

                dropzone.addEventListener('click', () => selector.click());
                selector.addEventListener('change', function() {
                    if (this.files.length > 0) uploadFile(this.files[0], input, preview, statusBox);
                });

                dropzone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropzone.style.borderColor = 'var(--primary-color)';
                    dropzone.style.backgroundColor = 'rgba(45, 90, 136, 0.03)';
                });

                dropzone.addEventListener('dragleave', () => {
                    dropzone.style.borderColor = '';
                    dropzone.style.backgroundColor = '';
                });

                dropzone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropzone.style.borderColor = '';
                    dropzone.style.backgroundColor = '';
                    if (e.dataTransfer.files.length > 0) {
                        uploadFile(e.dataTransfer.files[0], input, preview, statusBox);
                    }
                });
            }

            function uploadFile(file, input, preview, statusBox) {
                const formData = new FormData();
                formData.append('file', file);
                formData.append('csrf_token', '<?php echo csrfToken(); ?>');

                statusBox.style.display = 'block';
                statusBox.textContent = 'Uploading...';
                statusBox.style.color = 'var(--primary-color)';

                const url = '<?php echo BASE_URL; ?>/api/upload';

                fetch(url, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(result => {
                    if (result.success) {
                        statusBox.textContent = 'Upload complete!';
                        statusBox.style.color = '#10b981';
                        
                        // Set value in text input
                        input.value = result.relativeUrl;
                        
                        // Display visual preview
                        preview.src = result.url;
                        preview.style.display = 'inline-block';
                    } else {
                        statusBox.textContent = 'Error: ' + (result.error || 'Upload failed');
                        statusBox.style.color = '#ef4444';
                    }
                })
                .catch(err => {
                    statusBox.textContent = 'Upload failed. Network error.';
                    statusBox.style.color = '#ef4444';
                    console.error("AJAX Upload failed", err);
                });
            }

            // Live Preview JS implementation
            window.loadPreviewFonts = function() {
                const fonts = [
                    document.getElementById('set-font-family').value,
                    document.getElementById('set-font-family-body').value,
                    document.getElementById('set-font-family-ar').value,
                    document.getElementById('set-font-family-ar-body').value
                ];
                
                const uniqueFonts = [...new Set(fonts)].filter(f => f);
                
                uniqueFonts.forEach(font => {
                    const linkId = 'font-link-' + font.replace(/\s+/g, '-').toLowerCase();
                    if (!document.getElementById(linkId)) {
                        const link = document.createElement('link');
                        link.id = linkId;
                        link.rel = 'stylesheet';
                        link.href = 'https://fonts.googleapis.com/css2?family=' + encodeURIComponent(font) + ':wght@300;400;500;700;900&display=swap';
                        document.head.appendChild(link);
                    }
                });
            };

            window.updateLivePreview = function() {
                const themeMode = document.getElementById('set-theme-mode').value;
                const primaryColor = document.getElementById('color-primary-text').value;
                const secondaryColor = document.getElementById('color-secondary-text').value;
                const accentColor = document.getElementById('color-accent-text').value;
                const backgroundColor = document.getElementById('color-bg-text').value;
                const surfaceColor = document.getElementById('color-surface-text').value;
                const textColor = document.getElementById('color-text-text').value;
                const borderColor = document.getElementById('color-border-text').value;
                
                const fontFamily = document.getElementById('set-font-family').value;
                const fontFamilyBody = document.getElementById('set-font-family-body').value;
                const fontSize = document.getElementById('set-font-size').value;
                const headingWeight = document.getElementById('set-heading-weight').value;
                const bodyWeight = document.getElementById('set-body-weight').value;
                const letterSpacing = document.getElementById('set-letter-spacing').value;
                const lineHeight = document.getElementById('set-line-height').value;
                
                loadPreviewFonts();
                
                const previewCard = document.getElementById('theme-preview-card');
                if (!previewCard) return;

                if (themeMode === 'dark') {
                    previewCard.style.backgroundColor = '#121214';
                    previewCard.style.color = '#f3f4f6';
                } else {
                    previewCard.style.backgroundColor = backgroundColor;
                    previewCard.style.color = textColor;
                }
                previewCard.style.borderColor = borderColor;
                
                const prevHeroTitle = document.getElementById('prev-hero-title');
                if (prevHeroTitle) {
                    prevHeroTitle.style.fontFamily = `'${fontFamily}', sans-serif`;
                    prevHeroTitle.style.fontWeight = headingWeight;
                    prevHeroTitle.style.letterSpacing = letterSpacing;
                }
                
                const prevHeading = document.getElementById('prev-heading');
                if (prevHeading) {
                    prevHeading.style.fontFamily = `'${fontFamily}', sans-serif`;
                    prevHeading.style.fontWeight = headingWeight;
                    prevHeading.style.letterSpacing = letterSpacing;
                    prevHeading.style.color = themeMode === 'dark' ? '#ffffff' : textColor;
                }
                
                const prevBody = document.getElementById('prev-body');
                if (prevBody) {
                    prevBody.style.fontFamily = `'${fontFamilyBody}', sans-serif`;
                    prevBody.style.fontWeight = bodyWeight;
                    prevBody.style.lineHeight = lineHeight;
                    prevBody.style.fontSize = fontSize;
                    prevBody.style.color = themeMode === 'dark' ? '#cbd5e1' : textColor;
                }
                
                const prevBtnPrimary = document.getElementById('prev-btn-primary');
                if (prevBtnPrimary) {
                    prevBtnPrimary.style.backgroundColor = primaryColor;
                    prevBtnPrimary.style.color = '#ffffff';
                    prevBtnPrimary.style.fontFamily = `'${fontFamilyBody}', sans-serif`;
                    prevBtnPrimary.style.fontWeight = bodyWeight;
                }
                
                const prevBtnSecondary = document.getElementById('prev-btn-secondary');
                if (prevBtnSecondary) {
                    prevBtnSecondary.style.borderColor = accentColor;
                    prevBtnSecondary.style.color = themeMode === 'dark' ? '#ffffff' : primaryColor;
                    prevBtnSecondary.style.fontFamily = `'${fontFamilyBody}', sans-serif`;
                }
                
                const prevFormLabel = document.getElementById('prev-form-label');
                if (prevFormLabel) {
                    prevFormLabel.style.color = themeMode === 'dark' ? '#9ca3af' : '#666';
                    prevFormLabel.style.fontFamily = `'${fontFamilyBody}', sans-serif`;
                }
                
                const prevFormInput = document.getElementById('prev-form-input');
                if (prevFormInput) {
                    prevFormInput.style.borderColor = borderColor;
                    prevFormInput.style.color = themeMode === 'dark' ? '#f3f4f6' : textColor;
                    prevFormInput.style.fontFamily = `'${fontFamilyBody}', sans-serif`;
                }
                
                const prevCard = document.getElementById('prev-card');
                if (prevCard) {
                    prevCard.style.backgroundColor = themeMode === 'dark' ? '#1e1e24' : surfaceColor;
                    prevCard.style.borderColor = borderColor;
                }
                
                const prevCardTitle = document.getElementById('prev-card-title');
                if (prevCardTitle) {
                    prevCardTitle.style.fontFamily = `'${fontFamily}', sans-serif`;
                    prevCardTitle.style.fontWeight = headingWeight;
                }
                
                const prevCardDesc = document.getElementById('prev-card-desc');
                if (prevCardDesc) {
                    prevCardDesc.style.fontFamily = `'${fontFamilyBody}', sans-serif`;
                    prevCardDesc.style.color = themeMode === 'dark' ? '#9ca3af' : '#777';
                }
            };
            
            // Initial call to render
            updateLivePreview();
            
            // Add listeners to input inputs and selectors
            const previewInputs = [
                'set-theme-mode', 'color-primary-text', 'color-secondary-text', 'color-accent-text',
                'color-bg-text', 'color-surface-text', 'color-border-text', 'color-text-text',
                'set-font-family', 'set-font-family-body', 'set-font-family-ar', 'set-font-family-ar-body',
                'set-font-size', 'set-heading-weight', 'set-body-weight', 'set-letter-spacing', 'set-line-height'
            ];
            previewInputs.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('input', updateLivePreview);
                    el.addEventListener('change', updateLivePreview);
                }
            });
        });
        </script>
    </main>
</div>

</body>
</html>
