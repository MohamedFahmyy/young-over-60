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

// Handle Post Soft Deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $postId = $_GET['id'] ?? '';
    $token = $_GET['csrf_token'] ?? '';
    
    if (verifyCsrf($token)) {
        $pm->deletePost($postId, true); // True triggers soft delete
        $_SESSION['admin_flash_success'] = "Story successfully moved to trash.";
    } else {
        $_SESSION['admin_flash_error'] = "Security check failed. Unable to delete.";
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
                <h1 class="admin-title">Dashboard</h1>
                <p style="color: #888; font-size: 0.9rem; margin-top: 0.25rem;">Overview of site metrics and recent content logs.</p>
            </div>
            <a href="<?php echo BASE_URL; ?>/admin/add-post" class="btn-primary" style="padding: 0.75rem 1.5rem; border-radius: 8px; font-size: 0.75rem; text-decoration: none;">
                Create New Story
            </a>
        </div>

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
                    <span class="stat-label">Total Articles</span>
                    <h3 class="stat-value"><?php echo $totalPosts; ?></h3>
                </div>
            </div>

            <!-- Categories Stat -->
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background-color: #faf5ff; color: #a855f7;">
                    <svg style="width:1.5rem; height:1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                </div>
                <div>
                    <span class="stat-label">Categories</span>
                    <h3 class="stat-value"><?php echo $totalCategories; ?></h3>
                </div>
            </div>

            <!-- Subscribers Stat -->
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background-color: #ecfdf5; color: #10b981;">
                    <svg style="width:1.5rem; height:1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <span class="stat-label">Subscribers</span>
                    <h3 class="stat-value"><?php echo $totalSubscribers; ?></h3>
                </div>
            </div>
        </section>

        <!-- Recent Posts Workspace Table -->
        <section class="admin-card-box">
            <h2 class="admin-card-title">Recent Stories</h2>
            
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Cover</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Published</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentPosts)): ?>
                            <?php foreach ($recentPosts as $post): 
                                $editUrl = BASE_URL . '/admin/edit-post/' . $post['id'];
                                $deleteUrl = BASE_URL . '/admin/dashboard?action=delete&id=' . $post['id'] . '&csrf_token=' . csrfToken();
                                $cover = !empty($post['coverImage']) ? $post['coverImage'] : '/images/hero-bg.png';
                                ?>
                                <tr>
                                    <td style="width: 80px;">
                                        <img src="<?php echo e($cover); ?>" alt="" style="width: 60px; height: 38px; object-fit: cover; border-radius: 4px;" />
                                    </td>
                                    <td>
                                        <strong style="font-weight: 600;"><?php echo e($post['title']); ?></strong>
                                    </td>
                                    <td><?php echo e($post['categoryName']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $post['status'] === 'PUBLISHED' ? 'published' : 'draft'; ?>">
                                            <?php echo e($post['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo $post['publishedAt'] ? formatDate($post['publishedAt']) : '<span style="color:#aaa; font-style:italic;">Not Published</span>'; ?>
                                    </td>
                                    <td>
                                        <div class="btn-actions">
                                            <a href="<?php echo $editUrl; ?>" class="btn-sm-action">Edit</a>
                                            <a href="<?php echo $deleteUrl; ?>" onclick="return confirm('Are you sure you want to delete this story?');" class="btn-sm-action delete">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: #999; padding: 2rem 0; font-style: italic;">
                                    No stories written yet.
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
