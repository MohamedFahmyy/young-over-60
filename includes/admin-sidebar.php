<?php
// includes/admin-sidebar.php
// Reusable Admin Layout Sidebar Navigation Include

$current_page = basename($_SERVER['SCRIPT_NAME']);
$route = $_GET['route'] ?? '';
?>
<aside class="admin-sidebar">
    <div class="admin-sidebar-header" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
        <div>
            <span class="admin-logo-text" style="font-size: 0.95rem; font-weight: bold; color: var(--primary-color);"><?php echo e($settings['siteName'] ?? 'Young Over 60'); ?> Admin</span>
            <p style="font-size: 0.6rem; color:#666666; text-transform:uppercase; letter-spacing:0.15em; margin-top:0.25rem;">
                Dashboard Area
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

    <ul class="admin-nav-list">
        <li>
            <a href="<?php echo BASE_URL; ?>/admin/dashboard" class="admin-nav-link <?php echo ($route === 'admin' || $route === 'admin/dashboard' || str_contains($route, 'admin/dashboard')) ? 'active' : ''; ?>">
                Dashboard
            </a>
        </li>
        <li>
            <a href="<?php echo BASE_URL; ?>/admin/add-post" class="admin-nav-link <?php echo str_contains($route, 'admin/add-post') ? 'active' : ''; ?>">
                Add Post
            </a>
        </li>
        <li>
            <a href="<?php echo BASE_URL; ?>/admin/manage-content" class="admin-nav-link <?php echo str_contains($route, 'admin/manage-content') ? 'active' : ''; ?>">
                Manage Content
            </a>
        </li>
        <li>
            <a href="<?php echo BASE_URL; ?>/admin/podcasts" class="admin-nav-link <?php echo str_contains($route, 'admin/podcasts') ? 'active' : ''; ?>">
                Podcasts
            </a>
        </li>
        <li>
            <a href="<?php echo BASE_URL; ?>/admin/women-stories" class="admin-nav-link <?php echo str_contains($route, 'admin/women-stories') ? 'active' : ''; ?>">
                Women Stories
            </a>
        </li>
        <li>
            <a href="<?php echo BASE_URL; ?>/admin/hero-slider" class="admin-nav-link <?php echo str_contains($route, 'admin/hero-slider') ? 'active' : ''; ?>">
                Hero Slider
            </a>
        </li>
        <li>
            <a href="<?php echo BASE_URL; ?>/admin/media" class="admin-nav-link <?php echo str_contains($route, 'admin/media') ? 'active' : ''; ?>">
                Media Manager
            </a>
        </li>
        <li>
            <a href="<?php echo BASE_URL; ?>/admin/settings" class="admin-nav-link <?php echo str_contains($route, 'admin/settings') ? 'active' : ''; ?>">
                Settings
            </a>
        </li>
        <li>
            <a href="<?php echo BASE_URL; ?>/admin/reset-db" class="admin-nav-link <?php echo str_contains($route, 'admin/reset-db') ? 'active' : ''; ?>" style="color: #ef4444; opacity: 0.85;">
                Reset DB
            </a>
        </li>
        <li>
            <a href="<?php echo BASE_URL; ?>/" target="_blank" class="admin-nav-link">
                View Website
            </a>
        </li>
        <li style="margin-top: 4rem;" class="admin-logout-item">
            <a href="<?php echo BASE_URL; ?>/admin/dashboard?logout=true" class="admin-nav-link" style="color: #ef4444;">
                Log Out
            </a>
        </li>
    </ul>

    <div class="admin-sidebar-footer" style="margin-top: auto; font-size: 0.7rem; color: #555555;">
        Logged in as:<br>
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
