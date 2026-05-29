<?php
// includes/admin-sidebar.php
// Reusable Admin Layout Sidebar Navigation Include

$current_page = basename($_SERVER['SCRIPT_NAME']);
$route = $_GET['route'] ?? '';

$switch_params = $_GET;
unset($switch_params['route']);
$query_string = !empty($switch_params) ? '?' . http_build_query($switch_params) : '';

$route_clean = $route_clean ?? 'admin/dashboard';
$lang_prefix_url = CURRENT_LANG === 'ar' ? '/ar' : '/en';
?>
<aside class="admin-sidebar">
    <div class="admin-sidebar-header" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
        <div>
            <span class="admin-logo-text" style="font-size: 0.95rem; font-weight: bold; color: var(--primary-color);"><?php echo e($settings['siteName'] ?? 'Young Over 60'); ?> Admin</span>
            <p style="font-size: 0.6rem; color:#666666; text-transform:uppercase; letter-spacing:0.15em; margin-top:0.25rem;">
                <?php echo __('admin_dashboard_area'); ?>
            </p>
        </div>
        <!-- Hamburger Menu Button -->
        <button class="admin-menu-toggle" aria-label="Toggle Menu" style="display: none; background: none; border: none; color: #ffffff; cursor: pointer; padding: 0.5rem; outline: none;">
            <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path class="menu-icon-open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                <path class="menu-icon-close" style="display: none;" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <div class="admin-lang-switcher" style="padding: 0.75rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.08); display: flex; gap: 0.75rem; align-items: center; font-size: 0.8rem; background: rgba(0,0,0,0.15);">
        <span style="color: rgba(255,255,255,0.4);"><?php echo __('admin_lang'); ?>:</span>
        <a href="<?php echo BASE_URL . '/en/' . ltrim($route_clean, '/') . $query_string; ?>" class="lang-switch-btn <?php echo CURRENT_LANG === 'en' ? 'active' : ''; ?>" style="text-decoration: none; color: <?php echo CURRENT_LANG === 'en' ? '#ffffff' : 'rgba(255,255,255,0.5)'; ?>; font-weight: <?php echo CURRENT_LANG === 'en' ? '600' : '400'; ?>; transition: color 0.2s;"><?php echo CURRENT_LANG === 'ar' ? 'الإنجليزية' : 'English'; ?></a>
        <span style="color: rgba(255,255,255,0.2);">|</span>
        <a href="<?php echo BASE_URL . '/ar/' . ltrim($route_clean, '/') . $query_string; ?>" class="lang-switch-btn <?php echo CURRENT_LANG === 'ar' ? 'active' : ''; ?>" style="text-decoration: none; color: <?php echo CURRENT_LANG === 'ar' ? '#ffffff' : 'rgba(255,255,255,0.5)'; ?>; font-weight: <?php echo CURRENT_LANG === 'ar' ? '600' : '400'; ?>; transition: color 0.2s;">العربية</a>
    </div>

    <ul class="admin-nav-list">
        <li>
            <a href="<?php echo BASE_URL . $lang_prefix_url; ?>/admin/dashboard" class="admin-nav-link <?php echo ($route === 'admin' || $route === 'admin/dashboard' || str_contains($route, 'admin/dashboard')) ? 'active' : ''; ?>">
                <?php echo __('admin_dashboard'); ?>
            </a>
        </li>
        <li>
            <a href="<?php echo BASE_URL . $lang_prefix_url; ?>/admin/add-post" class="admin-nav-link <?php echo str_contains($route, 'admin/add-post') ? 'active' : ''; ?>">
                <?php echo __('admin_add_post'); ?>
            </a>
        </li>
        <li>
            <a href="<?php echo BASE_URL . $lang_prefix_url; ?>/admin/manage-content" class="admin-nav-link <?php echo str_contains($route, 'admin/manage-content') ? 'active' : ''; ?>">
                <?php echo __('admin_manage_content'); ?>
            </a>
        </li>
        <li>
            <a href="<?php echo BASE_URL . $lang_prefix_url; ?>/admin/podcasts" class="admin-nav-link <?php echo str_contains($route, 'admin/podcasts') ? 'active' : ''; ?>">
                <?php echo __('admin_podcasts'); ?>
            </a>
        </li>
        <li>
            <a href="<?php echo BASE_URL . $lang_prefix_url; ?>/admin/women-stories" class="admin-nav-link <?php echo str_contains($route, 'admin/women-stories') ? 'active' : ''; ?>">
                <?php echo __('admin_women_stories'); ?>
            </a>
        </li>
        <li>
            <a href="<?php echo BASE_URL . $lang_prefix_url; ?>/admin/hero-slider" class="admin-nav-link <?php echo str_contains($route, 'admin/hero-slider') ? 'active' : ''; ?>">
                <?php echo __('admin_hero_slider'); ?>
            </a>
        </li>
        <li>
            <a href="<?php echo BASE_URL . $lang_prefix_url; ?>/admin/media" class="admin-nav-link <?php echo str_contains($route, 'admin/media') ? 'active' : ''; ?>">
                <?php echo __('admin_media_manager'); ?>
            </a>
        </li>
        <li>
            <a href="<?php echo BASE_URL . $lang_prefix_url; ?>/admin/settings" class="admin-nav-link <?php echo str_contains($route, 'admin/settings') ? 'active' : ''; ?>">
                <?php echo __('admin_settings'); ?>
            </a>
        </li>
        <li>
            <a href="<?php echo BASE_URL . $lang_prefix_url; ?>/admin/reset-db" class="admin-nav-link <?php echo str_contains($route, 'admin/reset-db') ? 'active' : ''; ?>" style="color: #ef4444; opacity: 0.85;">
                <?php echo __('admin_reset_db'); ?>
            </a>
        </li>
        <li>
            <a href="<?php echo BASE_URL; ?>/" target="_blank" class="admin-nav-link">
                <?php echo __('admin_view_website'); ?>
            </a>
        </li>
        <li style="margin-top: 4rem;" class="admin-logout-item">
            <a href="<?php echo BASE_URL . $lang_prefix_url; ?>/admin/dashboard?logout=true" class="admin-nav-link" style="color: #ef4444;">
                <?php echo __('admin_logout'); ?>
            </a>
        </li>
    </ul>

    <div class="admin-sidebar-footer" style="margin-top: auto; font-size: 0.7rem; color: #555555;">
        <?php echo __('admin_logged_in_as'); ?><br>
        <span style="color: #888888; font-weight: bold;"><?php echo e($_SESSION['admin_name'] ?? 'Admin'); ?></span>
    </div>
</aside>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const toggleBtn = document.querySelector('.admin-menu-toggle');
    const navList = document.querySelector('.admin-nav-list');
    const sidebarFooter = document.querySelector('.admin-sidebar-footer');
    const openIcon = document.querySelector('.menu-icon-open');
    const closeIcon = document.querySelector('.menu-icon-close');
    const sidebar = document.querySelector('.admin-sidebar');

    if (toggleBtn && navList) {
        toggleBtn.addEventListener('click', function() {
            const isOpen = navList.classList.toggle('show');
            if (sidebarFooter) sidebarFooter.classList.toggle('show');
            if (sidebar) sidebar.classList.toggle('expanded');
            
            if (isOpen) {
                openIcon.style.display = 'none';
                closeIcon.style.display = 'block';
            } else {
                openIcon.style.display = 'block';
                closeIcon.style.display = 'none';
            }
        });
    }
});
</script>
