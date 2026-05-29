<?php
// admin/add-page.php
// Custom CMS Pages - Create New Page

require_once PATH_ROOT . '/classes/PageManager.php';

$pageMgr  = new PageManager();
$pm       = new PostManager();
$settings = $pm->getSiteSettings();
$error    = null;
$success  = null;

$lang_prefix_url = CURRENT_LANG === 'ar' ? '/ar' : '/en';

// Template options
$templateOptions = [
    'default'        => __('page_tpl_default'),
    'about'          => __('page_tpl_about'),
    'landing'        => __('page_tpl_landing'),
    'full-width'     => __('page_tpl_full_width'),
    'medical-tourism'=> __('page_tpl_medical'),
    'faq'            => __('page_tpl_faq'),
];

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Auth::requireCsrf();

        $data = [
            'title_en'            => trim($_POST['title_en'] ?? ''),
            'title_ar'            => trim($_POST['title_ar'] ?? ''),
            'slug_en'             => trim($_POST['slug_en'] ?? ''),
            'slug_ar'             => trim($_POST['slug_ar'] ?? ''),
            'content_en'          => $_POST['content_en'] ?? '',
            'content_ar'          => $_POST['content_ar'] ?? '',
            'excerpt_en'          => trim($_POST['excerpt_en'] ?? ''),
            'excerpt_ar'          => trim($_POST['excerpt_ar'] ?? ''),
            'meta_title_en'       => trim($_POST['meta_title_en'] ?? ''),
            'meta_title_ar'       => trim($_POST['meta_title_ar'] ?? ''),
            'meta_description_en' => trim($_POST['meta_description_en'] ?? ''),
            'meta_description_ar' => trim($_POST['meta_description_ar'] ?? ''),
            'hero_title_en'       => trim($_POST['hero_title_en'] ?? ''),
            'hero_title_ar'       => trim($_POST['hero_title_ar'] ?? ''),
            'hero_subtitle_en'    => trim($_POST['hero_subtitle_en'] ?? ''),
            'hero_subtitle_ar'    => trim($_POST['hero_subtitle_ar'] ?? ''),
            'hero_image'          => trim($_POST['hero_image'] ?? ''),
            'featured_image'      => trim($_POST['featured_image'] ?? ''),
            'template_type'       => $_POST['template_type'] ?? 'default',
            'is_published'        => isset($_POST['is_published']) ? 1 : 0,
            'show_in_menu'        => isset($_POST['show_in_menu']) ? 1 : 0,
            'menu_title_en'       => trim($_POST['menu_title_en'] ?? ''),
            'menu_title_ar'       => trim($_POST['menu_title_ar'] ?? ''),
            'sort_order'          => (int)($_POST['sort_order'] ?? 0),
        ];

        if (empty($data['title_en'])) {
            $error = __('pages_title_required');
        } else {
            $newId = $pageMgr->createPage($data);
            if ($newId) {
                header("Location: " . BASE_URL . $lang_prefix_url . "/admin/edit-page/{$newId}?saved=1");
                exit();
            } else {
                $error = __('admin_error');
            }
        }
    } catch (Exception $e) {
        $error = __('admin_error') . ': ' . $e->getMessage();
    }
}

$seoPageType = 'admin';
require_once PATH_ROOT . '/includes/header.php';
?>

<div class="admin-layout">
    <?php require_once PATH_ROOT . '/includes/admin-sidebar.php'; ?>

    <main class="admin-main">
        <!-- Header Row -->
        <div class="admin-header-row" style="margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
            <div>
                <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:0.5rem;">
                    <a href="<?php echo BASE_URL . $lang_prefix_url; ?>/admin/pages" style="color:#888; text-decoration:none; font-size:0.85rem; display:flex; align-items:center; gap:0.35rem;">
                        ← <?php echo __('admin_custom_pages'); ?>
                    </a>
                </div>
                <h1 class="admin-title"><?php echo __('pages_add_new'); ?></h1>
            </div>
        </div>

        <!-- Alerts -->
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

        <form method="POST" action="<?php echo BASE_URL . $lang_prefix_url; ?>/admin/add-page" id="page-form">
            <?php echo Auth::csrfInput(); ?>

            <div style="display: grid; grid-template-columns: 1fr 320px; gap: 1.5rem; align-items: start;">

                <!-- Main Column -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">

                    <!-- Titles & Slugs -->
                    <div class="admin-card-box" style="padding: 1.75rem;">
                        <h2 style="font-size: 1rem; font-weight: 600; margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-color);">
                            <?php echo __('pages_basic_info'); ?>
                        </h2>

                        <!-- Bilingual Tab Switcher -->
                        <div class="admin-tabs-row" style="margin-bottom: 1.5rem;">
                            <button type="button" class="admin-tab-trigger active" data-target="tab-en-content"><?php echo CURRENT_LANG === 'ar' ? 'English' : 'English'; ?></button>
                            <button type="button" class="admin-tab-trigger" data-target="tab-ar-content">العربية</button>
                        </div>

                        <!-- English Fields -->
                        <div id="tab-en-content" class="admin-tab-content active">
                            <div class="admin-form-group" style="margin-bottom:1rem;">
                                <label for="title_en"><?php echo __('pages_title_en'); ?> <span style="color:#ef4444">*</span></label>
                                <input type="text" id="title_en" name="title_en" class="admin-form-input" placeholder="e.g. About Us" value="<?php echo e($_POST['title_en'] ?? ''); ?>" oninput="autoSlug(this.value, 'slug_en')" required />
                            </div>
                            <div class="admin-form-group" style="margin-bottom:1rem;">
                                <label for="slug_en"><?php echo __('pages_slug_en'); ?></label>
                                <div style="display:flex; gap:0.5rem;">
                                    <span style="color:#888; align-self:center; white-space:nowrap; font-size:0.85rem;"><?php echo BASE_URL; ?>/pages/</span>
                                    <input type="text" id="slug_en" name="slug_en" class="admin-form-input" placeholder="about-us" value="<?php echo e($_POST['slug_en'] ?? ''); ?>" style="flex:1;" />
                                </div>
                                <p style="font-size:0.75rem; color:#888; margin-top:0.35rem;"><?php echo __('pages_slug_hint'); ?></p>
                            </div>
                            <div class="admin-form-group" style="margin-bottom:1rem;">
                                <label for="excerpt_en"><?php echo __('pages_excerpt_en'); ?></label>
                                <textarea id="excerpt_en" name="excerpt_en" rows="2" class="admin-form-textarea" placeholder="Short description..."><?php echo e($_POST['excerpt_en'] ?? ''); ?></textarea>
                            </div>
                        </div>

                        <!-- Arabic Fields -->
                        <div id="tab-ar-content" class="admin-tab-content" style="direction:rtl; text-align:right;">
                            <div class="admin-form-group" style="margin-bottom:1rem;">
                                <label for="title_ar"><?php echo __('pages_title_ar'); ?></label>
                                <input type="text" id="title_ar" name="title_ar" class="admin-form-input" placeholder="مثال: من نحن" value="<?php echo e($_POST['title_ar'] ?? ''); ?>" dir="rtl" />
                            </div>
                            <div class="admin-form-group" style="margin-bottom:1rem;">
                                <label for="slug_ar"><?php echo __('pages_slug_ar'); ?></label>
                                <input type="text" id="slug_ar" name="slug_ar" class="admin-form-input" placeholder="من-نحن" value="<?php echo e($_POST['slug_ar'] ?? ''); ?>" dir="ltr" />
                            </div>
                            <div class="admin-form-group" style="margin-bottom:1rem;">
                                <label for="excerpt_ar"><?php echo __('pages_excerpt_ar'); ?></label>
                                <textarea id="excerpt_ar" name="excerpt_ar" rows="2" class="admin-form-textarea" placeholder="وصف مختصر..." dir="rtl"><?php echo e($_POST['excerpt_ar'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Content Tabs -->
                    <div class="admin-card-box" style="padding: 1.75rem;">
                        <h2 style="font-size: 1rem; font-weight: 600; margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-color);">
                            <?php echo __('pages_content'); ?>
                        </h2>
                        <div class="admin-tabs-row" style="margin-bottom: 1.5rem;">
                            <button type="button" class="admin-tab-trigger active" data-target="tab-content-en">English Content</button>
                            <button type="button" class="admin-tab-trigger" data-target="tab-content-ar">محتوى عربي</button>
                        </div>
                        <div id="tab-content-en" class="admin-tab-content active">
                            <textarea id="content_en" name="content_en" rows="18" class="admin-form-textarea" placeholder="Write your page content in HTML or plain text..."><?php echo htmlspecialchars($_POST['content_en'] ?? ''); ?></textarea>
                        </div>
                        <div id="tab-content-ar" class="admin-tab-content" style="direction:rtl;">
                            <textarea id="content_ar" name="content_ar" rows="18" class="admin-form-textarea" placeholder="اكتب محتوى الصفحة..." dir="rtl"><?php echo htmlspecialchars($_POST['content_ar'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <!-- Hero Section -->
                    <div class="admin-card-box" style="padding: 1.75rem;">
                        <h2 style="font-size: 1rem; font-weight: 600; margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-color);">
                            <?php echo __('pages_hero_section'); ?>
                        </h2>
                        <div class="admin-tabs-row" style="margin-bottom: 1.5rem;">
                            <button type="button" class="admin-tab-trigger active" data-target="tab-hero-en">English Hero</button>
                            <button type="button" class="admin-tab-trigger" data-target="tab-hero-ar">هيرو عربي</button>
                        </div>
                        <div id="tab-hero-en" class="admin-tab-content active">
                            <div class="admin-form-group" style="margin-bottom:1rem;">
                                <label for="hero_title_en"><?php echo __('pages_hero_title_en'); ?></label>
                                <input type="text" id="hero_title_en" name="hero_title_en" class="admin-form-input" placeholder="Big headline..." value="<?php echo e($_POST['hero_title_en'] ?? ''); ?>" />
                            </div>
                            <div class="admin-form-group">
                                <label for="hero_subtitle_en"><?php echo __('pages_hero_subtitle_en'); ?></label>
                                <input type="text" id="hero_subtitle_en" name="hero_subtitle_en" class="admin-form-input" placeholder="Supporting subtitle..." value="<?php echo e($_POST['hero_subtitle_en'] ?? ''); ?>" />
                            </div>
                        </div>
                        <div id="tab-hero-ar" class="admin-tab-content" style="direction:rtl; text-align:right;">
                            <div class="admin-form-group" style="margin-bottom:1rem;">
                                <label for="hero_title_ar"><?php echo __('pages_hero_title_ar'); ?></label>
                                <input type="text" id="hero_title_ar" name="hero_title_ar" class="admin-form-input" placeholder="عنوان رئيسي..." value="<?php echo e($_POST['hero_title_ar'] ?? ''); ?>" dir="rtl" />
                            </div>
                            <div class="admin-form-group">
                                <label for="hero_subtitle_ar"><?php echo __('pages_hero_subtitle_ar'); ?></label>
                                <input type="text" id="hero_subtitle_ar" name="hero_subtitle_ar" class="admin-form-input" placeholder="وصف مساند..." value="<?php echo e($_POST['hero_subtitle_ar'] ?? ''); ?>" dir="rtl" />
                            </div>
                        </div>
                        <div class="admin-form-group" style="margin-top:1.5rem;">
                            <label for="hero_image"><?php echo __('pages_hero_image'); ?></label>
                            <input type="text" id="hero_image" name="hero_image" class="admin-form-input" placeholder="/uploads/..." value="<?php echo e($_POST['hero_image'] ?? ''); ?>" />
                            <p style="font-size:0.75rem; color:#888; margin-top:0.35rem;"><?php echo __('pages_hero_image_hint'); ?></p>
                        </div>
                    </div>

                    <!-- SEO Settings -->
                    <div class="admin-card-box" style="padding: 1.75rem;">
                        <h2 style="font-size: 1rem; font-weight: 600; margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-color);">
                            <?php echo __('pages_seo_settings'); ?>
                        </h2>
                        <div class="admin-tabs-row" style="margin-bottom: 1.5rem;">
                            <button type="button" class="admin-tab-trigger active" data-target="tab-seo-en">English SEO</button>
                            <button type="button" class="admin-tab-trigger" data-target="tab-seo-ar">SEO العربية</button>
                        </div>
                        <div id="tab-seo-en" class="admin-tab-content active">
                            <div class="admin-form-group" style="margin-bottom:1rem;">
                                <label for="meta_title_en"><?php echo __('settings_meta_title'); ?> (EN)</label>
                                <input type="text" id="meta_title_en" name="meta_title_en" class="admin-form-input" placeholder="SEO Page Title..." value="<?php echo e($_POST['meta_title_en'] ?? ''); ?>" maxlength="70" />
                                <p style="font-size:0.75rem; color:#888; margin-top:0.35rem;"><?php echo __('settings_meta_title_hint'); ?></p>
                            </div>
                            <div class="admin-form-group">
                                <label for="meta_description_en"><?php echo __('settings_meta_desc'); ?> (EN)</label>
                                <textarea id="meta_description_en" name="meta_description_en" rows="3" class="admin-form-textarea" placeholder="160 character description..." maxlength="170"><?php echo e($_POST['meta_description_en'] ?? ''); ?></textarea>
                                <p style="font-size:0.75rem; color:#888; margin-top:0.35rem;"><?php echo __('settings_meta_desc_hint'); ?></p>
                            </div>
                        </div>
                        <div id="tab-seo-ar" class="admin-tab-content" style="direction:rtl; text-align:right;">
                            <div class="admin-form-group" style="margin-bottom:1rem;">
                                <label for="meta_title_ar"><?php echo __('settings_meta_title'); ?> (AR)</label>
                                <input type="text" id="meta_title_ar" name="meta_title_ar" class="admin-form-input" placeholder="عنوان الصفحة في محركات البحث..." value="<?php echo e($_POST['meta_title_ar'] ?? ''); ?>" dir="rtl" />
                            </div>
                            <div class="admin-form-group">
                                <label for="meta_description_ar"><?php echo __('settings_meta_desc'); ?> (AR)</label>
                                <textarea id="meta_description_ar" name="meta_description_ar" rows="3" class="admin-form-textarea" placeholder="وصف محرك البحث..." dir="rtl"><?php echo e($_POST['meta_description_ar'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Side Column: Settings Sidebar -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">

                    <!-- Publish Actions -->
                    <div class="admin-card-box" style="padding: 1.5rem;">
                        <h3 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 1.25rem;"><?php echo __('pages_publish_settings'); ?></h3>

                        <div style="display:flex; flex-direction:column; gap:0.75rem; margin-bottom:1.5rem;">
                            <label style="display:flex; align-items:center; gap:0.6rem; cursor:pointer;">
                                <input type="checkbox" name="is_published" value="1" <?php echo (($_POST['is_published'] ?? 0) == 1) ? 'checked' : ''; ?> style="width:1rem; height:1rem;" />
                                <span style="font-size:0.9rem;"><?php echo __('pages_is_published'); ?></span>
                            </label>
                            <label style="display:flex; align-items:center; gap:0.6rem; cursor:pointer;">
                                <input type="checkbox" name="show_in_menu" value="1" <?php echo (($_POST['show_in_menu'] ?? 0) == 1) ? 'checked' : ''; ?> style="width:1rem; height:1rem;" />
                                <span style="font-size:0.9rem;"><?php echo __('pages_show_in_menu'); ?></span>
                            </label>
                        </div>

                        <button type="submit" class="btn-primary" style="width: 100%; border-radius:8px; padding:0.75rem;">
                            <?php echo __('pages_create_page'); ?>
                        </button>
                        <a href="<?php echo BASE_URL . $lang_prefix_url; ?>/admin/pages" style="display:block; text-align:center; margin-top:0.75rem; color:#888; font-size:0.85rem; text-decoration:none;">
                            <?php echo __('admin_cancel'); ?>
                        </a>
                    </div>

                    <!-- Template & Display -->
                    <div class="admin-card-box" style="padding: 1.5rem;">
                        <h3 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 1.25rem;"><?php echo __('pages_template'); ?></h3>
                        <div class="admin-form-group" style="margin-bottom:1rem;">
                            <label for="template_type"><?php echo __('pages_template_type'); ?></label>
                            <select id="template_type" name="template_type" class="admin-form-input">
                                <?php foreach ($templateOptions as $val => $label): ?>
                                    <option value="<?php echo $val; ?>" <?php echo (($_POST['template_type'] ?? 'default') === $val) ? 'selected' : ''; ?>><?php echo e($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="admin-form-group" style="margin-bottom:1rem;">
                            <label for="featured_image"><?php echo __('pages_featured_image'); ?></label>
                            <input type="text" id="featured_image" name="featured_image" class="admin-form-input" placeholder="/uploads/..." value="<?php echo e($_POST['featured_image'] ?? ''); ?>" />
                        </div>
                        <div class="admin-form-group">
                            <label for="sort_order"><?php echo __('pages_sort_order'); ?></label>
                            <input type="number" id="sort_order" name="sort_order" class="admin-form-input" min="0" value="<?php echo e($_POST['sort_order'] ?? '0'); ?>" />
                        </div>
                    </div>

                    <!-- Menu Labels -->
                    <div class="admin-card-box" style="padding: 1.5rem;">
                        <h3 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 1.25rem;"><?php echo __('pages_menu_settings'); ?></h3>
                        <div class="admin-form-group" style="margin-bottom:1rem;">
                            <label for="menu_title_en"><?php echo __('pages_menu_title_en'); ?></label>
                            <input type="text" id="menu_title_en" name="menu_title_en" class="admin-form-input" placeholder="e.g. About Us" value="<?php echo e($_POST['menu_title_en'] ?? ''); ?>" />
                        </div>
                        <div class="admin-form-group">
                            <label for="menu_title_ar"><?php echo __('pages_menu_title_ar'); ?></label>
                            <input type="text" id="menu_title_ar" name="menu_title_ar" class="admin-form-input" placeholder="مثال: من نحن" value="<?php echo e($_POST['menu_title_ar'] ?? ''); ?>" dir="rtl" />
                        </div>
                    </div>

                </div>
            </div>
        </form>

    </main>
</div>

<script>
function autoSlug(value, targetId) {
    const slug = value.toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
    const el = document.getElementById(targetId);
    if (el && el.dataset.manual !== '1') {
        el.value = slug;
    }
}
document.addEventListener('DOMContentLoaded', function() {
    const slugEl = document.getElementById('slug_en');
    if (slugEl) {
        slugEl.addEventListener('input', function() {
            this.dataset.manual = '1';
        });
    }
});
</script>

</body>
</html>
