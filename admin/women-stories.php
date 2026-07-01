<?php
// admin/women-stories.php
// Administrator Egypt Women Stories CRUD Panel

$storyMgr = new StoryManager();
$error = null;
$success = null;

$action = $_GET['action'] ?? 'list';
$editId = $_GET['id'] ?? '';
$editItem = null;

// Handle CRUD Operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Auth::requireCsrf();
        
        $title_en = trim($_POST['title_en'] ?? '');
        $title_ar = trim($_POST['title_ar'] ?? '');
        $slug_en = trim($_POST['slug_en'] ?? '');
        $slug_ar = trim($_POST['slug_ar'] ?? '');
        $excerpt_en = trim($_POST['excerpt_en'] ?? '');
        $excerpt_ar = trim($_POST['excerpt_ar'] ?? '');
        $content_en = trim($_POST['content_en'] ?? '');
        $content_ar = trim($_POST['content_ar'] ?? '');
        $category_en = trim($_POST['category_en'] ?? '');
        $category_ar = trim($_POST['category_ar'] ?? '');
        $author_en = trim($_POST['author_en'] ?? '');
        $author_ar = trim($_POST['author_ar'] ?? '');
        $readTime = trim($_POST['read_time'] ?? '');
        $coverImage = trim($_POST['cover_image'] ?? '');
        $alt_text_en = trim($_POST['alt_text_en'] ?? '');
        $alt_text_ar = trim($_POST['alt_text_ar'] ?? '');

        if (empty($title_en) || empty($content_en)) {
            $error = "Title (EN) and Content (EN) are required fields.";
        } else {
            $slug_en = slugify(empty($slug_en) ? $title_en : $slug_en);
            
            // Verify slug uniqueness (loop until unique)
            $originalSlugEn = $slug_en;
            $counter = 1;
            while ($storyMgr->slugExists($slug_en, $action === 'edit' ? $editId : null)) {
                $slug_en = $originalSlugEn . '-' . $counter++;
            }

            if (!empty($title_ar)) {
                $slug_ar = slugify(empty($slug_ar) ? $title_ar : $slug_ar);
                $originalSlugAr = $slug_ar;
                $counter = 1;
                while ($storyMgr->slugExists($slug_ar, $action === 'edit' ? $editId : null)) {
                    $slug_ar = $originalSlugAr . '-' . $counter++;
                }
            } else {
                $slug_ar = null;
            }
            
            $data = [
                'title_en' => $title_en,
                'title_ar' => !empty($title_ar) ? $title_ar : null,
                'slug_en' => $slug_en,
                'slug_ar' => $slug_ar,
                'excerpt_en' => !empty($excerpt_en) ? $excerpt_en : null,
                'excerpt_ar' => !empty($excerpt_ar) ? $excerpt_ar : null,
                'content_en' => $content_en,
                'content_ar' => !empty($content_ar) ? $content_ar : null,
                'category_en' => $category_en ?: 'General',
                'category_ar' => $category_ar ?: 'عام',
                'author_en' => $author_en ?: 'Guest Writer',
                'author_ar' => !empty($author_ar) ? $author_ar : null,
                'read_time' => $readTime ?: '5 min read',
                'cover_image' => $coverImage ?: '/images/hero-bg.png',
                'alt_text_en' => !empty($alt_text_en) ? $alt_text_en : null,
                'alt_text_ar' => !empty($alt_text_ar) ? $alt_text_ar : null
            ];

            if ($action === 'add') {
                if ($storyMgr->createStory($data)) {
                    $_SESSION['admin_flash_success'] = __("wstory_added_success");
                    header("Location: " . BASE_URL . "/admin/women-stories");
                    exit();
                } else {
                    $error = "Failed to save story to the database.";
                }
            } elseif ($action === 'edit' && !empty($editId)) {
                if ($storyMgr->updateStory($editId, $data)) {
                    $_SESSION['admin_flash_success'] = __("wstory_updated_success");
                    header("Location: " . BASE_URL . "/admin/women-stories");
                    exit();
                } else {
                    $error = "Failed to update story in the database.";
                }
            }
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Handle deletion
if ($action === 'delete' && !empty($editId)) {
    $token = $_GET['csrf_token'] ?? '';
    if (verifyCsrf($token)) {
        if ($storyMgr->deleteStory($editId)) {
            $_SESSION['admin_flash_success'] = __("wstory_deleted_success");
        } else {
            $_SESSION['admin_flash_error'] = "Failed to delete the story.";
        }
    } else {
        $_SESSION['admin_flash_error'] = "Security validation failed.";
    }
    header("Location: " . BASE_URL . "/admin/women-stories");
    exit();
}

// Prepare item for edit
if ($action === 'edit' && !empty($editId)) {
    $editItem = $storyMgr->getStoryById($editId);
    if (!$editItem) {
        $_SESSION['admin_flash_error'] = "Story not found.";
        header("Location: " . BASE_URL . "/admin/women-stories");
        exit();
    }
}

// Fetch list of stories
$storiesResult = $storyMgr->getStories(['limit' => 100]);
$stories = $storiesResult['data'];

// Set SEO Meta
$seoPageType = 'admin';
require_once PATH_ROOT . '/includes/header.php';
?>

<!-- Include Quill Stylesheet -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />

<div class="admin-layout">
    <!-- Sidebar -->
    <?php require_once PATH_ROOT . '/includes/admin-sidebar.php'; ?>

    <!-- Main Workspace -->
    <main class="admin-main">
        
        <?php if ($action === 'list'): ?>
            <!-- LIST VIEW -->
            <div class="admin-header-row">
                <div>
                    <h1 class="admin-title"><?php echo __("admin_women_stories"); ?></h1>
                    <p style="color: #888; font-size: 0.9rem; margin-top: 0.25rem;"><?php echo __("wstory_manage_desc"); ?></p>
                </div>
                <a href="<?php echo BASE_URL; ?>/admin/women-stories?action=add" class="btn-primary" style="padding: 0.75rem 1.5rem; border-radius: 8px; font-size: 0.75rem; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">
                    <?php echo __("wstory_add_title"); ?>
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

            <section class="admin-card-box">
                <h2 class="admin-card-title"><?php echo CURRENT_LANG === 'ar' ? 'سجلات مصر' : 'Egypt Chronicles'; ?></h2>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th><?php echo __("dash_cover"); ?></th>
                                <th><?php echo __("dash_title"); ?></th>
                                <th><?php echo __("dash_category"); ?></th>
                                <th><?php echo __("admin_author"); ?></th>
                                <th><?php echo __("admin_read_time"); ?></th>
                                <th><?php echo __("admin_published"); ?></th>
                                <th><?php echo __("admin_actions"); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($stories)): ?>
                                <?php foreach ($stories as $story): 
                                    $editUrl = BASE_URL . '/admin/women-stories?action=edit&id=' . $story['id'];
                                    $deleteUrl = BASE_URL . '/admin/women-stories?action=delete&id=' . $story['id'] . '&csrf_token=' . csrfToken();
                                    $cover = !empty($story['cover_image']) ? $story['cover_image'] : '/images/hero-bg.png';
                                    ?>
                                    <tr>
                                        <td style="width: 80px;">
                                            <img src="<?php echo e($cover); ?>" alt="" style="width: 60px; height: 38px; object-fit: cover; border-radius: 4px;" />
                                        </td>
                                        <td>
                                            <strong style="font-weight: 600;">
                                                <?php echo e(CURRENT_LANG === 'ar' && !empty($story['title_ar']) ? $story['title_ar'] : $story['title_en']); ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <?php echo e(CURRENT_LANG === 'ar' && !empty($story['category_ar']) ? $story['category_ar'] : $story['category_en']); ?>
                                        </td>
                                        <td>
                                            <?php echo e(CURRENT_LANG === 'ar' && !empty($story['author_ar']) ? $story['author_ar'] : $story['author_en']); ?>
                                        </td>
                                        <td><?php echo e($story['read_time']); ?></td>
                                        <td><?php echo formatDate($story['created_at']); ?></td>
                                        <td>
                                            <div class="btn-actions">
                                                <a href="<?php echo $editUrl; ?>" class="btn-sm-action"><?php echo __("admin_edit"); ?></a>
                                                <a href="<?php echo $deleteUrl; ?>" onclick="return confirm('<?php echo __("wstory_confirm_delete"); ?>');" class="btn-sm-action delete"><?php echo __("admin_delete"); ?></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; color: #999; padding: 3rem 0; font-style: italic;">
                                        <?php echo CURRENT_LANG === 'ar' ? 'لم يتم نشر أي قصص بعد. اكتب واحدة جديدة أعلاه!' : 'No stories published yet. Write one above!'; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

        <?php elseif ($action === 'add' || $action === 'edit'): ?>
            <!-- ADD / EDIT FORM VIEW -->
            <div class="admin-header-row" style="border-bottom: 1px solid #e5e7eb; padding-bottom: 1.5rem; margin-bottom: 2rem;">
                <div>
                    <a href="<?php echo BASE_URL; ?>/admin/women-stories" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.1em; color:#999; display: flex; align-items: center; gap: 0.5rem;">
                        <?php echo CURRENT_LANG === 'ar' ? '&rarr;' : '&larr;'; ?> <?php echo __("admin_back"); ?>
                    </a>
                    <h1 class="admin-title" style="margin-top:0.5rem;"><?php echo $action === 'add' ? __("wstory_add_title") : __("wstory_edit_title"); ?></h1>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="form-status-alert error" style="margin-bottom: 1.5rem;">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span><?php echo e($error); ?></span>
                </div>
            <?php endif; ?>

            <form id="storyForm" action="<?php echo BASE_URL; ?>/admin/women-stories?action=<?php echo $action; ?><?php echo !empty($editId) ? '&id='.$editId : ''; ?>" method="POST" style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
                <?php echo Auth::csrfInput(); ?>
                <input type="hidden" id="storyContentInputEn" name="content_en" />
                <input type="hidden" id="storyContentInputAr" name="content_ar" />

                <div class="admin-grid-layout admin-split-22-1">
                    
                    <!-- Left Column: Rich Text Editor & Text Areas -->
                    <div style="display: flex; flex-direction: column; gap: 1.5rem; min-width: 0;">
                        
                        <!-- Titles Row -->
                        <div class="admin-grid-fields-2">
                            <!-- Title EN -->
                            <div class="admin-form-group">
                                <label for="story-title-en"><?php echo __("dash_title"); ?> (EN)</label>
                                <input type="text" id="story-title-en" name="title_en" value="<?php echo e($editItem ? $editItem['title_en'] : ''); ?>" class="admin-form-input" placeholder="Enter story title..." required style="font-size: 1.25rem; font-family: var(--font-serif);" />
                            </div>
                            <!-- Title AR -->
                            <div class="admin-form-group">
                                <label for="story-title-ar"><?php echo __("dash_title"); ?> (AR)</label>
                                <input type="text" id="story-title-ar" name="title_ar" value="<?php echo e($editItem ? $editItem['title_ar'] : ''); ?>" class="admin-form-input" placeholder="اكتب عنوان القصة..." style="font-size: 1.25rem; font-family: var(--font-serif); direction: rtl;" />
                            </div>
                        </div>

                        <!-- Slugs Row -->
                        <div class="admin-grid-fields-2">
                            <!-- Slug EN -->
                            <div class="admin-form-group">
                                <label for="story-slug-en"><?php echo __("admin_slug"); ?> (EN)</label>
                                <input type="text" id="story-slug-en" name="slug_en" value="<?php echo e($editItem ? $editItem['slug_en'] : ''); ?>" class="admin-form-input" placeholder="slug-en" style="font-family: monospace;" />
                            </div>
                            <!-- Slug AR -->
                            <div class="admin-form-group">
                                <label for="story-slug-ar"><?php echo __("admin_slug"); ?> (AR)</label>
                                <input type="text" id="story-slug-ar" name="slug_ar" value="<?php echo e($editItem ? $editItem['slug_ar'] : ''); ?>" class="admin-form-input" placeholder="slug-ar" style="font-family: monospace; direction: rtl;" />
                            </div>
                        </div>

                        <!-- Excerpts Row -->
                        <div class="admin-grid-fields-2">
                            <!-- Excerpt EN -->
                            <div class="admin-form-group">
                                <label for="story-excerpt-en"><?php echo __("admin_description"); ?> (EN)</label>
                                <textarea id="story-excerpt-en" name="excerpt_en" rows="3" class="admin-form-textarea" placeholder="Provide a brief introductory excerpt in English..."><?php echo e($editItem ? $editItem['excerpt_en'] : ''); ?></textarea>
                            </div>
                            <!-- Excerpt AR -->
                            <div class="admin-form-group">
                                <label for="story-excerpt-ar"><?php echo __("admin_description"); ?> (AR)</label>
                                <textarea id="story-excerpt-ar" name="excerpt_ar" rows="3" class="admin-form-textarea" placeholder="اكتب ملخصًا قصيرًا للمقال باللغة العربية..." style="direction: rtl;"><?php echo e($editItem ? $editItem['excerpt_ar'] : ''); ?></textarea>
                            </div>
                        </div>

                        <!-- Content Editor EN -->
                        <div class="admin-form-group">
                            <label><?php echo __("post_field_content_en"); ?></label>
                            <div style="background: #ffffff; border: 1px solid var(--accent-color); border-radius: 8px; min-height: 250px;">
                                <div id="editorAreaEn" style="min-height: 220px; font-size: 1.1rem; line-height: 1.6; font-family: Georgia, serif;">
                                    <?php echo $editItem ? $editItem['content_en'] : ''; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Content Editor AR -->
                        <div class="admin-form-group">
                            <label><?php echo __("post_field_content_ar"); ?></label>
                            <div style="background: #ffffff; border: 1px solid var(--accent-color); border-radius: 8px; min-height: 250px;">
                                <div id="editorAreaAr" style="min-height: 220px; font-size: 1.1rem; line-height: 1.6; font-family: Cairo, Tajawal, Georgia, serif; direction: rtl; text-align: right;">
                                    <?php echo $editItem ? $editItem['content_ar'] : ''; ?>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column: Settings and Cover -->
                    <div style="display: flex; flex-direction: column; gap: 1.5rem; min-width: 0;">
                        
                        <!-- Details Panel -->
                        <div class="admin-card-box" style="margin: 0; padding: 2rem; display: flex; flex-direction: column; gap: 1.25rem;">
                            <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.5rem;"><?php echo CURRENT_LANG === 'ar' ? 'بيانات النشر' : 'Publishing Metadata'; ?></h3>
                            
                            <!-- Categories Row -->
                            <div class="admin-grid-fields-2" style="gap: 1rem;">
                                <div class="admin-form-group">
                                    <label for="story-category-en"><?php echo __("pod_field_cat_en"); ?></label>
                                    <select id="story-category-en" name="category_en" class="admin-form-select">
                                        <option value="Solo Travel" <?php echo ($editItem && $editItem['category_en'] === 'Solo Travel') ? 'selected' : ''; ?>>Solo Travel</option>
                                        <option value="Accessible Adventure" <?php echo ($editItem && $editItem['category_en'] === 'Accessible Adventure') ? 'selected' : ''; ?>>Accessible Adventure</option>
                                        <option value="Cultural Journeys" <?php echo ($editItem && $editItem['category_en'] === 'Cultural Journeys') ? 'selected' : ''; ?>>Cultural Journeys</option>
                                        <option value="General" <?php echo ($editItem && $editItem['category_en'] === 'General') ? 'selected' : ''; ?>>General</option>
                                    </select>
                                </div>
                                <div class="admin-form-group">
                                    <label for="story-category-ar"><?php echo __("pod_field_cat_ar"); ?></label>
                                    <select id="story-category-ar" name="category_ar" class="admin-form-select">
                                        <option value="أدلة ونصائح" <?php echo ($editItem && $editItem['category_ar'] === 'أدلة ونصائح') ? 'selected' : ''; ?>>أدلة ونصائح</option>
                                        <option value="مقابلات" <?php echo ($editItem && $editItem['category_ar'] === 'مقابلات') ? 'selected' : ''; ?>>مقابلات</option>
                                        <option value="الوجهات" <?php echo ($editItem && $editItem['category_ar'] === 'الوجهات') ? 'selected' : ''; ?>>الوجهات</option>
                                        <option value="قصص شخصية" <?php echo ($editItem && $editItem['category_ar'] === 'قصص شخصية') ? 'selected' : ''; ?>>قصص شخصية</option>
                                        <option value="عام" <?php echo ($editItem && $editItem['category_ar'] === 'عام') ? 'selected' : ''; ?>>عام</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Authors Row -->
                            <div class="admin-grid-fields-2" style="gap: 1rem;">
                                <!-- Author EN -->
                                <div class="admin-form-group">
                                    <label for="story-author-en"><?php echo __("wstory_field_author_en"); ?></label>
                                    <input type="text" id="story-author-en" name="author_en" value="<?php echo e($editItem ? $editItem['author_en'] : 'Guest Writer'); ?>" class="admin-form-input" required />
                                </div>
                                <!-- Author AR -->
                                <div class="admin-form-group">
                                    <label for="story-author-ar"><?php echo __("wstory_field_author_ar"); ?></label>
                                    <input type="text" id="story-author-ar" name="author_ar" value="<?php echo e($editItem ? $editItem['author_ar'] : ''); ?>" class="admin-form-input" placeholder="اسم الكاتب بالعربية" style="direction: rtl;" />
                                </div>
                            </div>

                            <!-- Read Time -->
                            <div class="admin-form-group">
                                <label for="story-readtime"><?php echo __("admin_read_time"); ?></label>
                                <input type="text" id="story-readtime" name="read_time" value="<?php echo e($editItem ? $editItem['read_time'] : '5 min read'); ?>" class="admin-form-input" required />
                            </div>
                        </div>

                        <!-- Cover Image Dropzone -->
                        <div class="admin-card-box" style="margin: 0; padding: 2rem;">
                            <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 1.5rem;"><?php echo __("dash_cover"); ?></h3>
                            <div class="admin-form-group">
                                <label for="story-cover"><?php echo __("post_field_cover"); ?></label>
                                <input type="text" id="story-cover" name="cover_image" value="<?php echo e($editItem ? $editItem['cover_image'] : ''); ?>" class="admin-form-input" placeholder="/uploads/..." />
                            </div>

                            <div class="admin-form-group" style="margin-top: 1rem;">
                                <label for="story-alt-en"><?php echo __("post_field_alt_en"); ?></label>
                                <input type="text" id="story-alt-en" name="alt_text_en" value="<?php echo e($editItem ? $editItem['alt_text_en'] : ''); ?>" class="admin-form-input" placeholder="Alt text in English" />
                            </div>
                            <div class="admin-form-group" style="margin-top: 1rem;">
                                <label for="story-alt-ar"><?php echo __("post_field_alt_ar"); ?></label>
                                <input type="text" id="story-alt-ar" name="alt_text_ar" value="<?php echo e($editItem ? $editItem['alt_text_ar'] : ''); ?>" class="admin-form-input" placeholder="النص البديل بالعربية" style="direction: rtl;" />
                            </div>
                            
                            <div id="coverDropzone" class="image-upload-preview-box" style="margin-top: 1.5rem; border: 1px dashed var(--accent-color); padding: 2rem; border-radius: 8px; text-align: center; cursor: pointer;">
                                <p style="font-size: 0.75rem; color: #777;"><?php echo __("settings_click_drop"); ?></p>
                                <span style="font-size: 0.6rem; color:#999; display:block; margin-top:0.25rem;">PNG, JPG, WebP (Max 2MB)</span>
                                <input type="file" id="coverFileSelector" accept="image/*" style="display:none;" />
                                <div id="coverStatusText" style="font-size:0.7rem; color: var(--primary-color); margin-top:0.5rem; display:none;">Uploading...</div>
                                <img id="coverPreview" src="<?php echo $editItem && $editItem['cover_image'] ? BASE_URL . $editItem['cover_image'] : ''; ?>" style="display: <?php echo $editItem && $editItem['cover_image'] ? 'inline-block' : 'none'; ?>; max-width: 100px; height: 60px; object-fit: cover; margin-top: 1rem; border-radius: 4px;" />
                            </div>
                        </div>

                    </div>

                </div>

                <!-- Submission Actions -->
                <div style="border-top: 1px solid #e5e7eb; padding-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
                    <a href="<?php echo BASE_URL; ?>/admin/women-stories" class="btn-reset" style="padding: 1rem 2rem; border-radius: 8px; width:auto; text-decoration:none; display: inline-flex; align-items: center; justify-content: center;"><?php echo __("admin_cancel"); ?></a>
                    <button type="submit" class="btn-primary" style="padding: 1rem 3rem; border-radius: 8px;"><?php echo __("post_btn_save"); ?></button>
                </div>
            </form>

            <!-- Include Quill Editor JS -->
            <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
            <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Initialize Quill Editors
                const quillEn = new Quill('#editorAreaEn', {
                    modules: {
                        toolbar: [
                            [{ 'header': [2, 3, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            ['link', 'blockquote'],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            ['clean']
                        ]
                    },
                    placeholder: 'Compose Egypt travel stories written by women (EN)...',
                    theme: 'snow'
                });

                const quillAr = new Quill('#editorAreaAr', {
                    modules: {
                        toolbar: [
                            [{ 'header': [2, 3, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            ['link', 'blockquote'],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            ['clean']
                        ]
                    },
                    placeholder: 'اكتبي قصص سفر ملهمة باللغة العربية...',
                    theme: 'snow'
                });

                // Title to Slug Generator
                const titleEnInp = document.getElementById('story-title-en');
                const slugEnInp = document.getElementById('story-slug-en');
                if (titleEnInp && slugEnInp) {
                    titleEnInp.addEventListener('input', function() {
                        if (slugEnInp.value === '' || this.getAttribute('data-touched') !== 'true') {
                            slugEnInp.value = this.value.toLowerCase()
                                .replace(/[^\w\s-]/g, '')
                                .replace(/\s+/g, '-')
                                .replace(/--+/g, '-');
                        }
                    });
                    slugEnInp.addEventListener('change', function() {
                        this.setAttribute('data-touched', 'true');
                    });
                }

                const titleArInp = document.getElementById('story-title-ar');
                const slugArInp = document.getElementById('story-slug-ar');
                if (titleArInp && slugArInp) {
                    titleArInp.addEventListener('input', function() {
                        if (slugArInp.value === '' || this.getAttribute('data-touched') !== 'true') {
                            slugArInp.value = this.value.toLowerCase()
                                .replace(/[^\w\s-]/g, '')
                                .replace(/\s+/g, '-')
                                .replace(/--+/g, '-');
                        }
                    });
                    slugArInp.addEventListener('change', function() {
                        this.setAttribute('data-touched', 'true');
                    });
                }

                // Sync Quill Editors output on Form Submit
                const form = document.getElementById('storyForm');
                const contentInputEn = document.getElementById('storyContentInputEn');
                const contentInputAr = document.getElementById('storyContentInputAr');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        if (contentInputEn) contentInputEn.value = quillEn.root.innerHTML;
                        if (contentInputAr) contentInputAr.value = quillAr.root.innerHTML;
                    });
                }

                // File Upload Preview Handler
                const coverDropzone = document.getElementById('coverDropzone');
                const coverSelector = document.getElementById('coverFileSelector');
                const coverInput = document.getElementById('story-cover');
                const coverPreview = document.getElementById('coverPreview');
                const coverStatus = document.getElementById('coverStatusText');

                if (coverDropzone && coverSelector) {
                    coverDropzone.addEventListener('click', () => coverSelector.click());
                    coverSelector.addEventListener('change', function() {
                        if (this.files.length > 0) uploadFile(this.files[0]);
                    });
                    
                    coverDropzone.addEventListener('dragover', (e) => { e.preventDefault(); coverDropzone.style.borderColor = 'var(--primary-color)'; });
                    coverDropzone.addEventListener('dragleave', () => { coverDropzone.style.borderColor = ''; });
                    coverDropzone.addEventListener('drop', (e) => {
                        e.preventDefault();
                        coverDropzone.style.borderColor = '';
                        if (e.dataTransfer.files.length > 0) uploadFile(e.dataTransfer.files[0]);
                    });
                }

                function uploadFile(file) {
                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('csrf_token', '<?php echo csrfToken(); ?>');

                    coverStatus.style.display = 'block';
                    coverStatus.textContent = 'Uploading image...';

                    fetch('<?php echo BASE_URL; ?>/api/upload', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(result => {
                        if (result.success) {
                            coverStatus.textContent = 'Upload complete!';
                            coverInput.value = result.relativeUrl;
                            coverPreview.src = result.url;
                            coverPreview.style.display = 'inline-block';
                        } else {
                            coverStatus.textContent = 'Error: ' + (result.error || 'Upload failed');
                        }
                    })
                    .catch(err => {
                        coverStatus.textContent = 'Upload failed. Network error.';
                        console.error("AJAX Upload failed", err);
                    });
                }
            });
            </script>
        <?php endif; ?>

    </main>
</div>

</body>
</html>
