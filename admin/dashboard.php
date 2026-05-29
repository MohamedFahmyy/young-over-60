<?php
// admin/dashboard.php
// Administrator Dashboard Control Panel View

// Handle Logout
if (isset($_GET['logout'])) {
    Auth::logout();
    header("Location: " . BASE_URL . "/admin/login");
    exit();
}

$pm = new PostManager();

// Handle Git Pull updates
if (isset($_GET['action']) && $_GET['action'] === 'git_pull') {
    $token = $_GET['csrf_token'] ?? '';
    
    if (verifyCsrf($token)) {
        if (!function_exists('exec')) {
            $_SESSION['admin_flash_error'] = __('dash_git_no_exec');
            $git_output = "Error: PHP execution function 'exec()' is disabled in hosting configurations.";
        } else {
            $output = [];
            $return_var = 1;
            @exec("git pull origin master 2>&1", $output, $return_var);
            
            $git_output = implode("\n", $output);
            if ($return_var === 0) {
                $_SESSION['admin_flash_success'] = __('dash_pull_success');
            } else {
                $_SESSION['admin_flash_error'] = __('dash_pull_error') . ' ' . $git_output;
            }
            $pm->clearCache();
        }
    } else {
        $_SESSION['admin_flash_error'] = __('dash_security_failed');
        $git_output = "CSRF Verification Failed.";
    }
    header("Location: " . BASE_URL . "/admin/dashboard?pull_result=" . urlencode($git_output));
    exit();
}

// Handle Post Soft Deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $postId = $_GET['id'] ?? '';
    $token = $_GET['csrf_token'] ?? '';
    
    if (verifyCsrf($token)) {
        $pm->deletePost($postId, true); // True triggers soft delete
        $_SESSION['admin_flash_success'] = __('dash_soft_delete_success');
    } else {
        $_SESSION['admin_flash_error'] = __('dash_security_failed');
    }
    header("Location: " . BASE_URL . "/admin/dashboard");
    exit();
}

// Fetch stats and lists
$totalPostsResult = $pm->getPosts(['limit' => 1]);
$totalPosts = $totalPostsResult['meta']['total'];

$categories = $pm->getCategories();
$totalCategories = count($categories);

$subscribers = $pm->getSubscribers();
$totalSubscribers = count($subscribers);

// Recent Posts list (take 8)
$recentResult = $pm->getPosts(['limit' => 8]);
$recentPosts = $recentResult['data'];

// Set SEO Meta
$seoPageType = 'admin';
require_once PATH_ROOT . '/includes/header.php';
?>

<div class="admin-layout">
    <!-- Sidebar -->
    <?php require_once PATH_ROOT . '/includes/admin-sidebar.php'; ?>

    <!-- Main Workspace -->
    <main class="admin-main">
        <div class="admin-header-row">
            <div>
                <h1 class="admin-title"><?php echo __('admin_dashboard'); ?></h1>
                <p style="color: #888; font-size: 0.9rem; margin-top: 0.25rem;"><?php echo __('dash_overview'); ?></p>
            </div>
            <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                <a href="<?php echo BASE_URL; ?>/admin/dashboard?action=git_pull&csrf_token=<?php echo csrfToken(); ?>" class="btn-primary" style="background-color: #10b981; border-color: #10b981; padding: 0.75rem 1.5rem; border-radius: 8px; font-size: 0.75rem; text-decoration: none; display: flex; align-items: center; gap: 0.4rem;" onclick="this.style.opacity='0.6'; this.textContent='<?php echo addslashes(__('btn_view_guides')); /* or a pulling text if we want, but let's keep it simple or just translate */ ?>';">
                    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <?php echo __('dash_update_site'); ?>
                </a>
                <a href="<?php echo BASE_URL; ?>/admin/add-post" class="btn-primary" style="padding: 0.75rem 1.5rem; border-radius: 8px; font-size: 0.75rem; text-decoration: none;">
                    <?php echo __('dash_create_story'); ?>
                </a>
            </div>
        </div>

        <!-- Git Pull Output -->
        <?php if (isset($_GET['pull_result'])): ?>
            <div class="admin-card-box" style="margin-bottom: 2rem; border-left: 4px solid var(--primary-color);">
                <h3 style="font-size: 0.9rem; font-weight: bold; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem; color: var(--primary-color);">
                    <svg style="width:1.25rem; height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <?php echo __('dash_git_log'); ?>
                </h3>
                <pre style="background: #1e293b; color: #f8fafc; padding: 1.25rem; border-radius: 8px; font-family: monospace; font-size: 0.75rem; white-space: pre-wrap; overflow-x: auto; line-height: 1.5; margin: 0;"><?php echo e($_GET['pull_result']); ?></pre>
            </div>
        <?php endif; ?>

        <!-- Flash messages -->
        <?php if (isset($_SESSION['admin_flash_success'])): ?>
            <div class="form-status-alert success" style="margin-bottom: 2rem;">
                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span><?php echo e($_SESSION['admin_flash_success']); unset($_SESSION['admin_flash_success']); ?></span>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['admin_flash_error'])): ?>
            <div class="form-status-alert error" style="margin-bottom: 2rem;">
                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span><?php echo e($_SESSION['admin_flash_error']); unset($_SESSION['admin_flash_error']); ?></span>
            </div>
        <?php endif; ?>

        <!-- Stat Grid Cards -->
        <section class="admin-stats-grid">
            <!-- Posts Stat -->
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background-color: #eff6ff; color: #3b82f6;">
                    <svg style="width:1.5rem; height:1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <span class="stat-label"><?php echo __('dash_total_articles'); ?></span>
                    <h3 class="stat-value"><?php echo $totalPosts; ?></h3>
                </div>
            </div>

            <!-- Categories Stat -->
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background-color: #faf5ff; color: #a855f7;">
                    <svg style="width:1.5rem; height:1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                </div>
                <div>
                    <span class="stat-label"><?php echo __('admin_manage_content'); /* Categories */ ?></span>
                    <h3 class="stat-value"><?php echo $totalCategories; ?></h3>
                </div>
            </div>

            <!-- Subscribers Stat -->
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background-color: #ecfdf5; color: #10b981;">
                    <svg style="width:1.5rem; height:1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <span class="stat-label"><?php echo __('manage_subscribers'); ?></span>
                    <h3 class="stat-value"><?php echo $totalSubscribers; ?></h3>
                </div>
            </div>
        </section>

        <!-- Recent Posts Workspace Table -->
        <section class="admin-card-box">
            <h2 class="admin-card-title"><?php echo __('dash_recent_stories'); ?></h2>
            
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th><?php echo __('dash_cover'); ?></th>
                            <th><?php echo __('dash_title'); ?></th>
                            <th><?php echo __('dash_category'); ?></th>
                            <th><?php echo __('admin_status'); ?></th>
                            <th><?php echo __('admin_published'); ?></th>
                            <th><?php echo __('admin_actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentPosts)): ?>
                            <?php foreach ($recentPosts as $post): 
                                $editUrl = BASE_URL . $lang_prefix_url . '/admin/edit-post/' . $post['id'];
                                $deleteUrl = BASE_URL . $lang_prefix_url . '/admin/dashboard?action=delete&id=' . $post['id'] . '&csrf_token=' . csrfToken();
                                $cover = !empty($post['coverImage']) ? $post['coverImage'] : '/images/hero-bg.png';
                                ?>
                                <tr>
                                    <td style="width: 80px;">
                                        <img src="<?php echo e($cover); ?>" alt="" style="width: 60px; height: 38px; object-fit: cover; border-radius: 4px;" />
                                    </td>
                                    <td>
                                        <strong style="font-weight: 600;"><?php echo e(t($post, 'title')); ?></strong>
                                    </td>
                                    <td><?php echo e(t($post, 'categoryName')); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $post['status'] === 'PUBLISHED' ? 'published' : 'draft'; ?>">
                                            <?php echo e($post['status'] === 'PUBLISHED' ? __('admin_published') : __('admin_draft')); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo $post['publishedAt'] ? formatDate($post['publishedAt']) : '<span style="color:#aaa; font-style:italic;">' . __('admin_draft') . '</span>'; ?>
                                    </td>
                                    <td>
                                        <div class="btn-actions">
                                            <a href="<?php echo $editUrl; ?>" class="btn-sm-action"><?php echo __('admin_edit'); ?></a>
                                            <a href="<?php echo $deleteUrl; ?>" onclick="return confirm('<?php echo addslashes(__('dash_confirm_delete')); ?>');" class="btn-sm-action delete"><?php echo __('admin_delete'); ?></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: #999; padding: 2rem 0; font-style: italic;">
                                    <?php echo __('dash_no_stories'); ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

</body>
</html>
