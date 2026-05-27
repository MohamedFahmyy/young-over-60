<?php
// admin/manage-content.php
// Administrator Content, Categories, Subscribers, & Settings Manager

$pm = new PostManager();
$error = null;
$success = null;

// Determine active tab
$activeTab = $_GET['tab'] ?? 'categories';

// 1. Handle POST Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Auth::requireCsrf();
        $action = $_POST['action'] ?? '';

        if ($action === 'create_category') {
            $name = $_POST['name'] ?? '';
            $slug = $_POST['slug'] ?? '';
            $description = $_POST['description'] ?? '';
            $image = $_POST['image'] ?? '';

            if (empty($name)) {
                $error = "Category name is required.";
            } else {
                $slug = slugify(empty($slug) ? $name : $slug);
                
                // Validate duplicate
                if ($pm->getCategoryBySlug($slug)) {
                    $slug = $slug . '-' . time();
                }

                $catData = [
                    'name' => $name,
                    'slug' => $slug,
                    'description' => $description,
                    'image' => $image
                ];

                if ($pm->createCategory($catData)) {
                    $success = "Category successfully created!";
                } else {
                    $error = "Failed to create category in database.";
                }
            }
        } elseif ($action === 'save_settings') {
            if ($pm->updateSiteSettings($_POST)) {
                $success = "Settings updated successfully!";
                // Refresh local settings cache
                $settings = $pm->getSiteSettings();
            } else {
                $error = "Failed to update site configurations.";
            }
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// 2. Handle Deletes
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $token = $_GET['csrf_token'] ?? '';
    
    if (verifyCsrf($token)) {
        if ($action === 'delete_category') {
            $catId = $_GET['id'] ?? '';
            if ($pm->deleteCategory($catId)) {
                $success = "Category deleted successfully.";
            } else {
                $error = "Unable to delete category. Verify no posts are linked to it.";
            }
        }
    } else {
        $error = "Security validation mismatch.";
    }
}

// Fetch categories, settings, subscribers, submissions
$categories = $pm->getCategories();
$settings = $pm->getSiteSettings();
$subscribers = $pm->getSubscribers();
$submissions = $pm->getSubmissions();

// Meta Title Override
$seoPageType = 'admin';
require_once PATH_ROOT . '/includes/header.php';
?>

<div class="admin-layout">
    <!-- Sidebar -->
    <?php require_once PATH_ROOT . '/includes/admin-sidebar.php'; ?>

    <!-- Main Workspace -->
    <main class="admin-main">
        <div class="admin-header-row" style="margin-bottom: 2.5rem;">
            <div>
                <h1 class="admin-title">Content Manager</h1>
                <p style="color:#888; font-size:0.9rem; margin-top:0.25rem;">Adjust visual styles, browse subscriptions, and modify categories.</p>
            </div>
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

        <!-- Tabs Navigation -->
        <div class="admin-tabs-row">
            <button class="admin-tab-trigger <?php echo $activeTab === 'categories' ? 'active' : ''; ?>" data-target="tab-categories">Categories</button>
            <button class="admin-tab-trigger <?php echo $activeTab === 'settings' ? 'active' : ''; ?>" data-target="tab-settings">Theme Settings</button>
            <button class="admin-tab-trigger <?php echo $activeTab === 'subscribers' ? 'active' : ''; ?>" data-target="tab-subscribers">Newsletter</button>
            <button class="admin-tab-trigger <?php echo $activeTab === 'submissions' ? 'active' : ''; ?>" data-target="tab-submissions">Submissions</button>
        </div>

        <!-- ==========================================
             TAB 1: Categories Manager
             ========================================== -->
        <div id="tab-categories" class="admin-tab-content <?php echo $activeTab === 'categories' ? 'active' : ''; ?>">
            <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
                <!-- Add Category Form -->
                <div class="admin-card-box" style="margin: 0; padding: 2rem;">
                    <h3 style="font-size:1.1rem; margin-bottom:1.5rem;">Create New Category</h3>
                    <form action="<?php echo BASE_URL; ?>/admin/manage-content?tab=categories" method="POST" class="newsletter-form">
                        <?php echo Auth::csrfInput(); ?>
                        <input type="hidden" name="action" value="create_category" />
                        
                        <div class="admin-form-group">
                            <label for="cat-name">Category Name</label>
                            <input type="text" id="cat-name" name="name" class="admin-form-input" placeholder="e.g. Wellness" required />
                        </div>
                        <div class="admin-form-group" style="margin-top: 1rem;">
                            <label for="cat-slug">URL Slug</label>
                            <input type="text" id="cat-slug" name="slug" class="admin-form-input" placeholder="e.g. wellness" />
                        </div>
                        <div class="admin-form-group" style="margin-top: 1rem;">
                            <label for="cat-desc">Description</label>
                            <textarea id="cat-desc" name="description" rows="3" class="admin-form-textarea" placeholder="Teaser category description..."></textarea>
                        </div>
                        <div class="admin-form-group" style="margin-top: 1rem;">
                            <label for="cat-image">Cover Image URL</label>
                            <input type="text" id="cat-image" name="image" class="admin-form-input" placeholder="/images/..." />
                        </div>
                        <button type="submit" class="btn-primary" style="margin-top: 1.5rem; border-radius: 8px;">Create Category</button>
                    </form>
                </div>

                <!-- Categories Grid Table -->
                <div class="admin-card-box" style="margin: 0;">
                    <h3 class="admin-card-title" style="padding:2rem 2rem 0 2rem;">All Categories</h3>
                    <div class="admin-table-wrapper" style="padding: 1rem 2rem 2rem 2rem;">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Cover</th>
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $cat): 
                                    $cover = !empty($cat['image']) ? $cat['image'] : '/images/hero-bg.png';
                                    $deleteUrl = BASE_URL . '/admin/manage-content?tab=categories&action=delete_category&id=' . $cat['id'] . '&csrf_token=' . csrfToken();
                                    ?>
                                    <tr>
                                        <td style="width:70px;">
                                            <img src="<?php echo e(BASE_URL . $cover); ?>" alt="" style="width: 50px; height: 35px; object-fit: cover; border-radius: 4px;" />
                                        </td>
                                        <td><strong><?php echo e($cat['name']); ?></strong></td>
                                        <td><code><?php echo e($cat['slug']); ?></code></td>
                                        <td style="max-width:250px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                            <?php echo e($cat['description']); ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo $deleteUrl; ?>" onclick="return confirm('Delete this category? This will fail if posts are linked.');" class="btn-sm-action delete">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             TAB 2: Site & Theme Settings Customizer
             ========================================== -->
        <div id="tab-settings" class="admin-tab-content <?php echo $activeTab === 'settings' ? 'active' : ''; ?>">
            <form action="<?php echo BASE_URL; ?>/admin/manage-content?tab=settings" method="POST" style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
                <?php echo Auth::csrfInput(); ?>
                <input type="hidden" name="action" value="save_settings" />

                <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
                    <!-- Left: Metadata settings -->
                    <div style="display:flex; flex-direction:column; gap:2rem;">
                        <!-- Branding Box -->
                        <div class="admin-card-box" style="margin:0; padding:2rem;">
                            <h3 style="font-size:1.1rem; margin-bottom:1.5rem;">Branding Configurations</h3>
                            
                            <div class="admin-form-group">
                                <label for="set-name">Website Brand Title</label>
                                <input type="text" id="set-name" name="siteName" value="<?php echo e($settings['siteName']); ?>" class="admin-form-input" required />
                            </div>
                            <div class="admin-form-group" style="margin-top:1.25rem;">
                                <label for="set-logo">Brand Logo URL</label>
                                <input type="text" id="set-logo" name="logoUrl" value="<?php echo e($settings['logoUrl'] ?? ''); ?>" class="admin-form-input" />
                            </div>
                            <div class="admin-form-group" style="margin-top:1.25rem;">
                                <label for="set-hero">Homepage Hero Background URL</label>
                                <input type="text" id="set-hero" name="heroBackgroundUrl" value="<?php echo e($settings['heroBackgroundUrl'] ?? ''); ?>" class="admin-form-input" />
                            </div>
                        </div>

                        <!-- SEO Metadata Box -->
                        <div class="admin-card-box" style="margin:0; padding:2rem;">
                            <h3 style="font-size:1.1rem; margin-bottom:1.5rem;">SEO & Social Metadata</h3>
                            
                            <div class="admin-form-group">
                                <label for="set-seo-title">SEO Meta Title Override</label>
                                <input type="text" id="set-seo-title" name="metaTitle" value="<?php echo e($settings['metaTitle'] ?? ''); ?>" class="admin-form-input" />
                            </div>
                            <div class="admin-form-group" style="margin-top:1.25rem;">
                                <label for="set-seo-desc">SEO Meta Description</label>
                                <textarea id="set-seo-desc" name="metaDescription" rows="4" class="admin-form-textarea"><?php echo e($settings['metaDescription'] ?? ''); ?></textarea>
                            </div>
                            <div class="admin-form-group" style="margin-top:1.25rem;">
                                <label for="set-og">Social Share (OG Image) URL</label>
                                <input type="text" id="set-og" name="ogImage" value="<?php echo e($settings['ogImage'] ?? ''); ?>" class="admin-form-input" />
                            </div>
                            <div class="admin-form-group" style="margin-top:1.25rem;">
                                <label for="set-fav">Favicon Icon URL</label>
                                <input type="text" id="set-fav" name="faviconUrl" value="<?php echo e($settings['faviconUrl'] ?? ''); ?>" class="admin-form-input" />
                            </div>
                        </div>
                    </div>

                    <!-- Right: Colors & Theme Previews -->
                    <div style="display:flex; flex-direction:column; gap:2rem;">
                        <!-- Color configurations -->
                        <div class="admin-card-box" style="margin:0; padding:2rem;">
                            <h3 style="font-size:1.1rem; margin-bottom:1.5rem;">UI Theme Color Presets</h3>
                            
                            <div class="color-inputs-grid">
                                <div class="admin-form-group">
                                    <label>Primary Color</label>
                                    <input type="color" name="primaryColor" value="<?php echo e($settings['primaryColor']); ?>" class="admin-form-input" style="height:50px; padding:0; cursor:pointer;" />
                                    <input type="text" name="primaryColor" value="<?php echo e($settings['primaryColor']); ?>" class="admin-form-input" style="font-family:monospace; margin-top:0.25rem; font-size:0.8rem;" />
                                </div>
                                <div class="admin-form-group">
                                    <label>Secondary Color</label>
                                    <input type="color" name="secondaryColor" value="<?php echo e($settings['secondaryColor']); ?>" class="admin-form-input" style="height:50px; padding:0; cursor:pointer;" />
                                    <input type="text" name="secondaryColor" value="<?php echo e($settings['secondaryColor']); ?>" class="admin-form-input" style="font-family:monospace; margin-top:0.25rem; font-size:0.8rem;" />
                                </div>
                                <div class="admin-form-group">
                                    <label>Accent Grid color</label>
                                    <input type="color" name="accentColor" value="<?php echo e($settings['accentColor']); ?>" class="admin-form-input" style="height:50px; padding:0; cursor:pointer;" />
                                    <input type="text" name="accentColor" value="<?php echo e($settings['accentColor']); ?>" class="admin-form-input" style="font-family:monospace; margin-top:0.25rem; font-size:0.8rem;" />
                                </div>
                                <div class="admin-form-group">
                                    <label>Global Text color</label>
                                    <input type="color" name="textColor" value="<?php echo e($settings['textColor']); ?>" class="admin-form-input" style="height:50px; padding:0; cursor:pointer;" />
                                    <input type="text" name="textColor" value="<?php echo e($settings['textColor']); ?>" class="admin-form-input" style="font-family:monospace; margin-top:0.25rem; font-size:0.8rem;" />
                                </div>
                            </div>
                        </div>

                        <div class="admin-card-box" style="margin:0; padding:2rem;">
                            <h3 style="font-size:1.1rem; margin-bottom:1.5rem;">Visual Simulator</h3>
                            <div style="background-color:#111; padding:2rem; border-radius:12px; display:flex; flex-direction:column; gap:1.5rem; color:#fff; text-align:center;">
                                <div style="padding:1rem; background:#fff; color:#000; border-radius:8px; display:flex; justify-content:space-between; align-items:center;">
                                    <span style="font-weight:bold; font-family:var(--font-serif); color:<?php echo $settings['primaryColor']; ?>"><?php echo e(explode(' ', $settings['siteName'])[0]); ?></span>
                                    <div style="width:20px; height:20px; border-radius:50%; background:<?php echo $settings['secondaryColor']; ?>;"></div>
                                </div>
                                <button type="button" class="btn-primary" style="background:<?php echo $settings['primaryColor']; ?>; border-radius:50px; padding:0.75rem;">Simulate Action Button</button>
                                <span style="font-size:0.6rem; color:#666;">Colors refresh on Save</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Box -->
                <div style="border-top:1px solid #e5e7eb; padding-top:2rem; display:flex; justify-content:flex-end;">
                    <button type="submit" class="btn-primary" style="padding:1rem 3rem; border-radius:8px;">Save Configurations</button>
                </div>
            </form>
        </div>

        <!-- ==========================================
             TAB 3: Newsletter Subscribers
             ========================================== -->
        <div id="tab-subscribers" class="admin-tab-content <?php echo $activeTab === 'subscribers' ? 'active' : ''; ?>">
            <div class="admin-card-box" style="margin:0;">
                <div class="admin-card-title" style="padding:2rem 2rem 0 2rem;">
                    <span>Email List (<?php echo count($subscribers); ?>)</span>
                    <!-- Simple Copy list buttons -->
                    <button class="btn-sm-action" onclick="copySubscribersList();">Copy Email List</button>
                </div>
                <div class="admin-table-wrapper" style="padding: 1rem 2rem 2rem 2rem;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Subscriber Email</th>
                                <th>Subscription Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($subscribers)): ?>
                                <?php foreach ($subscribers as $sub): ?>
                                    <tr>
                                        <td><strong><?php echo e($sub['email']); ?></strong></td>
                                        <td><?php echo formatDate($sub['created_at']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" style="text-align: center; padding: 2rem; color:#999; font-style:italic;">No emails subscribed yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ==========================================
             TAB 4: Contact Messages
             ========================================== -->
        <div id="tab-submissions" class="admin-tab-content <?php echo $activeTab === 'submissions' ? 'active' : ''; ?>">
            <div class="admin-card-box" style="margin:0;">
                <h3 class="admin-card-title" style="padding:2rem 2rem 0 2rem;">Messages Received</h3>
                <div class="admin-table-wrapper" style="padding: 1rem 2rem 2rem 2rem;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone / WhatsApp</th>
                                <th style="max-width: 350px;">Message</th>
                                <th>Submitted At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($submissions)): ?>
                                <?php foreach ($submissions as $sub): ?>
                                    <tr>
                                        <td><strong><?php echo e($sub['name']); ?></strong></td>
                                        <td><a href="mailto:<?php echo e($sub['email']); ?>" style="text-decoration:underline;"><?php echo e($sub['email']); ?></a></td>
                                        <td>
                                            <a href="https://wa.me/<?php echo preg_replace('/[^\d]/', '', $sub['phone']); ?>" target="_blank" style="text-decoration:underline; color:#10b981;">
                                                <?php echo e($sub['phone']); ?>
                                            </a>
                                        </td>
                                        <td style="max-width: 350px; font-size: 0.85rem; line-height: 1.4; white-space: normal; word-break: break-word;">
                                            <?php echo nl2br(e($sub['message'])); ?>
                                        </td>
                                        <td><?php echo formatDate($sub['created_at']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 2rem; color:#999; font-style:italic;">No messages received yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Tab switching controller
        const tabTriggers = document.querySelectorAll('.admin-tab-trigger');
        tabTriggers.forEach(trigger => {
            trigger.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                
                // Set triggers
                tabTriggers.forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                // Set content panels
                const contents = document.querySelectorAll('.admin-tab-content');
                contents.forEach(c => c.classList.remove('active'));
                
                const targetPanel = document.getElementById(targetId);
                if (targetPanel) targetPanel.classList.add('active');
                
                // Update URL parameter
                const tabName = targetId.replace('tab-', '');
                history.replaceState(null, '', `?tab=${tabName}`);
            });
        });

        // Slug generation for category creator
        const catName = document.getElementById('cat-name');
        const catSlug = document.getElementById('cat-slug');
        if (catName && catSlug) {
            catName.addEventListener('input', function() {
                catSlug.value = this.value.toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/--+/g, '-');
            });
        }
    });

    // Subscriptions Copy Helper
    function copySubscribersList() {
        const emails = [];
        <?php foreach ($subscribers as $sub): ?>
            emails.push("<?php echo e($sub['email']); ?>");
        <?php endforeach; ?>
        
        if (emails.length === 0) {
            alert("No emails to copy.");
            return;
        }

        const rawText = emails.join("\n");
        navigator.clipboard.writeText(rawText)
            .then(() => alert("Copied subscriber list to clipboard!"))
            .catch(err => alert("Failed to copy list: " + err));
    }
</script>
<?php 
// Clean admin layout close
?>
    </main>
</div>
</body>
</html>
