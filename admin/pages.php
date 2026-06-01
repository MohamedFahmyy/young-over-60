<?php
// admin/pages.php
// Custom Pages CMS Admin - Pages Index & Manager

require_once PATH_ROOT . '/classes/PageManager.php';

$pageMgr = new PageManager();
$pm      = new PostManager();
$settings = $pm->getSiteSettings();
$error = null;
$success = null;

$lang_prefix_url = CURRENT_LANG === 'ar' ? '/ar' : '/en';

// Handle deletions
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $pageId = trim($_GET['id'] ?? '');
    $token  = $_GET['csrf_token'] ?? '';
    if (verifyCsrf($token) && !empty($pageId)) {
        if ($pageMgr->deletePage($pageId)) {
            $success = __('pages_deleted');
        } else {
            $error = __('admin_error');
        }
    } else {
        $error = __('dash_security_failed');
    }
}

// Handle quick publish/unpublish toggle
if (isset($_GET['action']) && in_array($_GET['action'], ['publish', 'unpublish'])) {
    $pageId  = trim($_GET['id'] ?? '');
    $token   = $_GET['csrf_token'] ?? '';
    $newPub  = ($_GET['action'] === 'publish') ? 1 : 0;
    if (verifyCsrf($token) && !empty($pageId)) {
        $pageMgr->updatePage($pageId, ['is_published' => $newPub]);
        $success = $newPub ? __('pages_published') : __('pages_unpublished');
    } else {
        $error = __('dash_security_failed');
    }
}

// Fetch pages list
$search    = $_GET['search'] ?? '';
$pages = $pageMgr->getPages(['search' => $search]);

$seoPageType = 'admin';
require_once PATH_ROOT . '/includes/header.php';
?>

<div class="admin-layout">
    <!-- Sidebar -->
    <?php require_once PATH_ROOT . '/includes/admin-sidebar.php'; ?>

    <!-- Main Workspace -->
    <main class="admin-main">
        <div class="admin-header-row" style="margin-bottom: 2.5rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
            <div>
                <h1 class="admin-title"><?php echo __('admin_custom_pages'); ?></h1>
                <p style="color:#888; font-size:0.9rem; margin-top:0.25rem;"><?php echo __('pages_admin_desc'); ?></p>
            </div>
            <a href="<?php echo BASE_URL . $lang_prefix_url; ?>/admin/add-page" class="btn-primary" style="border-radius:8px; display:inline-flex; align-items:center; gap:0.5rem; white-space:nowrap;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <?php echo __('pages_add_new'); ?>
            </a>
        </div>

        <!-- Flash alerts -->
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

        <!-- Search bar -->
        <div class="admin-card-box" style="margin-bottom: 1.5rem; padding: 1.25rem 1.5rem;">
            <form method="GET" action="<?php echo BASE_URL . $lang_prefix_url; ?>/admin/pages" style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
                <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="<?php echo __('pages_search_placeholder'); ?>" class="admin-form-input" style="flex: 1; min-width: 200px;" />
                <button type="submit" class="btn-primary" style="border-radius: 8px; padding: 0.6rem 1.25rem; white-space: nowrap;"><?php echo __('admin_search'); ?></button>
                <?php if ($search): ?>
                    <a href="<?php echo BASE_URL . $lang_prefix_url; ?>/admin/pages" class="btn-reset" style="padding: 0.6rem 1rem; border-radius: 8px; white-space: nowrap;"><?php echo __('btn_close'); ?></a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Pages Table -->
        <div class="admin-card-box" style="padding: 0;">
            <div style="padding: 1.5rem 1.5rem 0; display: flex; align-items: center; justify-content: space-between;">
                <h2 style="font-size: 1rem; font-weight: 600;"><?php echo __('pages_all_pages'); ?> <span style="color: #888; font-size: 0.85rem;">(<?php echo count($pages); ?>)</span></h2>
            </div>
            <div class="admin-table-wrapper" style="padding: 1rem 1.5rem 1.5rem;">
                <?php if (empty($pages)): ?>
                    <div style="text-align: center; padding: 3rem; color: #888;">
                        <svg style="width:3rem; height:3rem; margin: 0 auto 1rem; display:block; opacity:0.3;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <p><?php echo __('pages_no_pages'); ?></p>
                        <a href="<?php echo BASE_URL . $lang_prefix_url; ?>/admin/add-page" class="btn-primary" style="margin-top: 1rem; border-radius: 8px; display:inline-block;"><?php echo __('pages_add_new'); ?></a>
                    </div>
                <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width:40%"><?php echo __('pages_col_title'); ?></th>
                            <th><?php echo __('pages_col_slug'); ?></th>
                            <th><?php echo __('pages_col_template'); ?></th>
                            <th><?php echo __('pages_col_status'); ?></th>
                            <th><?php echo __('pages_col_menu'); ?></th>
                            <th><?php echo __('admin_actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pages as $pg):
                            $editUrl   = BASE_URL . $lang_prefix_url . '/admin/edit-page/' . $pg['id'];
                            $deleteUrl = BASE_URL . $lang_prefix_url . '/admin/pages?action=delete&id=' . $pg['id'] . '&csrf_token=' . csrfToken();
                            $pubUrl    = BASE_URL . $lang_prefix_url . '/admin/pages?action=' . ($pg['is_published'] ? 'unpublish' : 'publish') . '&id=' . $pg['id'] . '&csrf_token=' . csrfToken();
                            $viewUrl   = url('pages/' . $pg['slug_en']);
                            $titleEn   = $pg['title_en'] ?: '(Untitled)';
                            $titleAr   = $pg['title_ar'] ?: '';
                        ?>
                        <tr>
                            <td>
                                <strong style="display:block;"><?php echo e($titleEn); ?></strong>
                                <?php if ($titleAr): ?>
                                    <small style="color:#888; direction:rtl; display:block;"><?php echo e($titleAr); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <code style="font-size:0.8rem;"><?php echo e($pg['slug_en']); ?></code>
                            </td>
                            <td>
                                <span class="badge-text" style="background: var(--surface-color); color: var(--text-color); padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.75rem; text-transform: capitalize;">
                                    <?php echo e($pg['template_type'] ?? 'default'); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($pg['is_published']): ?>
                                    <span style="color: #10b981; font-weight: 600; font-size: 0.8rem;">● <?php echo __('pages_status_published'); ?></span>
                                <?php else: ?>
                                    <span style="color: #f59e0b; font-weight: 600; font-size: 0.8rem;">● <?php echo __('pages_status_draft'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($pg['show_in_menu']): ?>
                                    <span style="color: #10b981; font-size: 0.8rem;">✓ <?php echo __('pages_in_menu'); ?></span>
                                <?php else: ?>
                                    <span style="color: #888; font-size: 0.8rem;">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.4rem; flex-wrap: wrap; align-items: center;">
                                    <a href="<?php echo $editUrl; ?>" class="btn-sm-action edit"><?php echo __('admin_edit'); ?></a>
                                    <?php if ($pg['is_published']): ?>
                                        <a href="<?php echo $viewUrl; ?>" target="_blank" class="btn-sm-action" style="background: rgba(99,102,241,0.12); color: #818cf8;"><?php echo __('admin_view'); ?></a>
                                        <a href="<?php echo $pubUrl; ?>" class="btn-sm-action" style="background:rgba(245,158,11,0.12); color:#f59e0b;" onclick="return confirm('<?php echo addslashes(__('pages_unpublish_confirm')); ?>')"><?php echo __('pages_unpublish'); ?></a>
                                    <?php else: ?>
                                        <a href="<?php echo $pubUrl; ?>" class="btn-sm-action" style="background:rgba(16,185,129,0.12); color:#10b981;" onclick="return confirm('<?php echo addslashes(__('pages_publish_confirm')); ?>')"><?php echo __('pages_publish'); ?></a>
                                    <?php endif; ?>
                                    <a href="<?php echo $deleteUrl; ?>" class="btn-sm-action delete" onclick="return confirm('<?php echo addslashes(__('pages_delete_confirm')); ?>')"><?php echo __('admin_delete'); ?></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

    </main>
</div>
</body>
</html>
