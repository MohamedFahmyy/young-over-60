<?php
// admin/edit-post.php
// Administrator Edit Story Screen

$pm = new PostManager();
$categories = $pm->getCategories();
$error = null;

// Get post ID from path
$postId = $_GET['id'] ?? '';
$post = $pm->getPostById($postId);

if (!$post) {
    $_SESSION['admin_flash_error'] = "Story not found.";
    header("Location: " . BASE_URL . "/admin/dashboard");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Auth::requireCsrf();
        
        $title_en = $_POST['title_en'] ?? '';
        $title_ar = $_POST['title_ar'] ?? '';
        $slug_en = $_POST['slug_en'] ?? '';
        $slug_ar = $_POST['slug_ar'] ?? '';
        $excerpt_en = $_POST['excerpt_en'] ?? '';
        $excerpt_ar = $_POST['excerpt_ar'] ?? '';
        $content_en = $_POST['content_en'] ?? '';
        $content_ar = $_POST['content_ar'] ?? '';
        $coverImage = $_POST['coverImage'] ?? '';
        $alt_text_en = $_POST['alt_text_en'] ?? '';
        $alt_text_ar = $_POST['alt_text_ar'] ?? '';
        $categoryId = $_POST['categoryId'] ?? '';
        $isFeatured = isset($_POST['isFeatured']) ? 1 : 0;
        $status = $_POST['status'] ?? 'DRAFT';

        // Basic validation
        if (empty($title_en) || empty($content_en) || empty($categoryId)) {
            $error = __("post_fields_required");
        } else {
            $slug_en = slugify(empty($slug_en) ? $title_en : $slug_en);

            // Ensure unique English slug if changed
            if ($slug_en !== $post['slug_en']) {
                $existingEn = $pm->getPostBySlug($slug_en, true);
                if ($existingEn) {
                    $slug_en = $slug_en . '-' . time();
                }
            }

            // Ensure unique Arabic slug if provided and changed
            if (!empty($title_ar)) {
                $slug_ar = slugify(empty($slug_ar) ? $title_ar : $slug_ar);
                if ($slug_ar !== $post['slug_ar']) {
                    $existingAr = $pm->getPostBySlug($slug_ar, true);
                    if ($existingAr) {
                        $slug_ar = $slug_ar . '-' . time();
                    }
                }
            } else {
                $slug_ar = null;
            }

            $postData = [
                'title_en' => $title_en,
                'title_ar' => !empty($title_ar) ? $title_ar : null,
                'slug_en' => $slug_en,
                'slug_ar' => $slug_ar,
                'excerpt_en' => !empty($excerpt_en) ? $excerpt_en : null,
                'excerpt_ar' => !empty($excerpt_ar) ? $excerpt_ar : null,
                'content_en' => $content_en,
                'content_ar' => !empty($content_ar) ? $content_ar : null,
                'coverImage' => $coverImage,
                'alt_text_en' => !empty($alt_text_en) ? $alt_text_en : null,
                'alt_text_ar' => !empty($alt_text_ar) ? $alt_text_ar : null,
                'isFeatured' => $isFeatured,
                'status' => $status,
                'categoryId' => $categoryId
            ];

            if ($pm->updatePost($postId, $postData)) {
                $_SESSION['admin_flash_success'] = __("post_updated_success");
                header("Location: " . BASE_URL . "/admin/dashboard");
                exit();
            } else {
                $error = "Failed to update story in database.";
            }
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Meta Title Override
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
                <a href="<?php echo BASE_URL; ?>/admin/dashboard" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.1em; color:#999; display: flex; align-items: center; gap: 0.5rem;">
                    <?php echo CURRENT_LANG === 'ar' ? '&rarr;' : '&larr;'; ?> <?php echo __("post_back_dash"); ?>
                </a>
                <h1 class="admin-title" style="margin-top:0.5rem;"><?php echo __("post_edit_title"); ?></h1>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="form-status-alert error" style="margin-bottom: 1.5rem;">
                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span><?php echo e($error); ?></span>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form id="editPostForm" action="<?php echo BASE_URL; ?>/admin/edit-post/<?php echo $post['id']; ?>" method="POST" style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
            <?php echo Auth::csrfInput(); ?>
            <input type="hidden" id="postContentInputEn" name="content_en" value="<?php echo e($post['content_en']); ?>" />
            <input type="hidden" id="postContentInputAr" name="content_ar" value="<?php echo e($post['content_ar'] ?? ''); ?>" />

            <div class="admin-grid-layout" style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
                <!-- Left panel (main editor content) -->
                <div style="display:flex; flex-direction:column; gap:1.5rem; min-width: 0;">
                    <!-- Titles Row -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <!-- Title EN -->
                        <div class="admin-form-group">
                            <label for="post-title-en"><?php echo __("post_field_title_en"); ?></label>
                            <input type="text" id="post-title-en" name="title_en" value="<?php echo e($post['title_en']); ?>" class="admin-form-input" placeholder="<?php echo __("post_field_title_en"); ?>..." required style="font-size: 1.25rem; font-family: var(--font-serif);" />
                        </div>
                        <!-- Title AR -->
                        <div class="admin-form-group">
                            <label for="post-title-ar"><?php echo __("post_field_title_ar"); ?></label>
                            <input type="text" id="post-title-ar" name="title_ar" value="<?php echo e($post['title_ar'] ?? ''); ?>" class="admin-form-input" placeholder="<?php echo __("post_field_title_ar"); ?>..." style="font-size: 1.25rem; font-family: var(--font-serif); direction: rtl;" />
                        </div>
                    </div>

                    <!-- Slugs Row -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <!-- Slug EN -->
                        <div class="admin-form-group">
                            <label for="post-slug-en"><?php echo __("post_field_slug_en"); ?></label>
                            <input type="text" id="post-slug-en" name="slug_en" value="<?php echo e($post['slug_en']); ?>" class="admin-form-input" placeholder="english-slug-here" style="font-family: monospace;" />
                        </div>
                        <!-- Slug AR -->
                        <div class="admin-form-group">
                            <label for="post-slug-ar"><?php echo __("post_field_slug_ar"); ?></label>
                            <input type="text" id="post-slug-ar" name="slug_ar" value="<?php echo e($post['slug_ar'] ?? ''); ?>" class="admin-form-input" placeholder="arabic-slug-here" style="font-family: monospace; direction: rtl;" />
                        </div>
                    </div>

                    <!-- Excerpts Row -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <!-- Excerpt EN -->
                        <div class="admin-form-group">
                            <label for="post-excerpt-en"><?php echo __("admin_description"); ?> (EN)</label>
                            <textarea id="post-excerpt-en" name="excerpt_en" rows="3" class="admin-form-textarea" placeholder="Provide a short magazine teaser excerpt in English..."><?php echo e($post['excerpt_en'] ?? ''); ?></textarea>
                        </div>
                        <!-- Excerpt AR -->
                        <div class="admin-form-group">
                            <label for="post-excerpt-ar"><?php echo __("admin_description"); ?> (AR)</label>
                            <textarea id="post-excerpt-ar" name="excerpt_ar" rows="3" class="admin-form-textarea" placeholder="اكتب مقتطفًا قصيرًا للمقال باللغة العربية..." style="direction: rtl;"><?php echo e($post['excerpt_ar'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <!-- Content EN (Rich Text ContentEditable Editor) -->
                    <div class="admin-form-group">
                        <label><?php echo __("post_field_content_en"); ?></label>
                        <div class="richtext-editor">
                            <!-- Toolbar -->
                            <div class="richtext-toolbar" data-editor="editorAreaEn">
                                <button type="button" class="richtext-btn" data-cmd="bold">B</button>
                                <button type="button" class="richtext-btn" data-cmd="italic" style="font-style: italic;">I</button>
                                <button type="button" class="richtext-btn" data-cmd="underline" style="text-decoration: underline;">U</button>
                                <button type="button" class="richtext-btn" data-cmd="formatBlock" data-val="h2">H2</button>
                                <button type="button" class="richtext-btn" data-cmd="formatBlock" data-val="h3">H3</button>
                                <button type="button" class="richtext-btn" data-cmd="createLink">Link</button>
                                <button type="button" class="richtext-btn" data-cmd="removeFormat">Clear</button>
                            </div>
                            <!-- Contenteditable Area -->
                            <div id="editorAreaEn" class="richtext-content" contenteditable="true" placeholder="Compose your accessible travel story in English..."><?php echo $post['content_en']; ?></div>
                        </div>
                    </div>

                    <!-- Content AR (Rich Text ContentEditable Editor) -->
                    <div class="admin-form-group">
                        <label><?php echo __("post_field_content_ar"); ?></label>
                        <div class="richtext-editor">
                            <!-- Toolbar -->
                            <div class="richtext-toolbar" data-editor="editorAreaAr">
                                <button type="button" class="richtext-btn" data-cmd="bold">B</button>
                                <button type="button" class="richtext-btn" data-cmd="italic" style="font-style: italic;">I</button>
                                <button type="button" class="richtext-btn" data-cmd="underline" style="text-decoration: underline;">U</button>
                                <button type="button" class="richtext-btn" data-cmd="formatBlock" data-val="h2">H2</button>
                                <button type="button" class="richtext-btn" data-cmd="formatBlock" data-val="h3">H3</button>
                                <button type="button" class="richtext-btn" data-cmd="createLink">Link</button>
                                <button type="button" class="richtext-btn" data-cmd="removeFormat">Clear</button>
                            </div>
                            <!-- Contenteditable Area -->
                            <div id="editorAreaAr" class="richtext-content" contenteditable="true" placeholder="Compose your accessible travel story in Arabic..." style="direction: rtl;"><?php echo $post['content_ar'] ?? ''; ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Right panel (Sidebar controls) -->
                <div style="display:flex; flex-direction:column; gap:1.5rem; min-width: 0;">
                    <div class="admin-card-box" style="margin: 0; padding: 2rem;">
                        <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 1.5rem;"><?php echo __("manage_desc"); ?></h3>
                        
                        <!-- Category Selection -->
                        <div class="admin-form-group">
                            <label for="post-category"><?php echo __("post_field_category"); ?></label>
                            <select id="post-category" name="categoryId" class="admin-form-select" required>
                                <option value=""><?php echo __("post_field_select_cat"); ?></option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo e($cat['id']); ?>" <?php echo $cat['id'] === $post['categoryId'] ? 'selected' : ''; ?>>
                                        <?php echo e(CURRENT_LANG === 'ar' && !empty($cat['name_ar']) ? $cat['name_ar'] : $cat['name_en']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Status Toggle -->
                        <div class="admin-form-group" style="margin-top: 1rem;">
                            <label><?php echo __("post_field_status"); ?></label>
                            <select name="status" class="admin-form-select">
                                <option value="DRAFT" <?php echo $post['status'] === 'DRAFT' ? 'selected' : ''; ?>><?php echo __("admin_draft"); ?></option>
                                <option value="PUBLISHED" <?php echo $post['status'] === 'PUBLISHED' ? 'selected' : ''; ?>><?php echo __("admin_published"); ?></option>
                            </select>
                        </div>

                        <!-- Featured Toggle -->
                        <div class="admin-form-group" style="margin-top: 1rem; display: flex; flex-direction: row; justify-content: space-between; align-items: center; gap: 0.5rem;">
                            <div>
                                <label style="display:block; margin:0;"><?php echo __("post_field_featured"); ?></label>
                                <span style="font-size: 0.6rem; color:#aaa;"><?php echo CURRENT_LANG === 'ar' ? 'عرض على البانر الرئيسي للموقع' : 'Promote to homepage banner'; ?></span>
                            </div>
                            <input type="checkbox" name="isFeatured" value="1" <?php echo !empty($post['isFeatured']) ? 'checked' : ''; ?> style="width: 20px; height: 20px;" />
                        </div>
                    </div>

                    <!-- Cover Image Upload Box -->
                    <div class="admin-card-box" style="margin: 0; padding: 2rem;">
                        <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 1.5rem;"><?php echo CURRENT_LANG === 'ar' ? 'صور الغلاف والترويج' : 'Cover & Teaser Imagery'; ?></h3>
                        
                        <div class="admin-form-group">
                            <label for="post-coverImage"><?php echo __("post_field_cover"); ?></label>
                            <input type="text" id="post-coverImage" name="coverImage" value="<?php echo e($post['coverImage'] ?? ''); ?>" class="admin-form-input" placeholder="/uploads/..." />
                        </div>

                        <!-- Image Alt Text EN & AR -->
                        <div class="admin-form-group" style="margin-top: 1rem;">
                            <label for="post-alt-en"><?php echo __("post_field_alt_en"); ?></label>
                            <input type="text" id="post-alt-en" name="alt_text_en" value="<?php echo e($post['alt_text_en'] ?? ''); ?>" class="admin-form-input" placeholder="Alt text in English" />
                        </div>
                        <div class="admin-form-group" style="margin-top: 1rem;">
                            <label for="post-alt-ar"><?php echo __("post_field_alt_ar"); ?></label>
                            <input type="text" id="post-alt-ar" name="alt_text_ar" value="<?php echo e($post['alt_text_ar'] ?? ''); ?>" class="admin-form-input" placeholder="النص البديل بالعربية" style="direction: rtl;" />
                        </div>
                        
                        <!-- Drag-and-drop Image Uploader Box -->
                        <div id="dropzone" class="image-upload-preview-box" style="margin-top: 1.5rem;">
                            <p style="font-size: 0.75rem; color: #777;"><?php echo __("settings_click_drop"); ?></p>
                            <span style="font-size: 0.6rem; color:#999; display:block; margin-top:0.25rem;">PNG, JPG, WebP (Max 2MB)</span>
                            <input type="file" id="fileSelector" accept="image/*" style="display:none;" />
                            <div id="uploadStatusText" style="font-size:0.7rem; color: #2d5a88; margin-top:0.5rem; display:none;">Uploading...</div>
                            <img id="imagePreview" src="<?php echo !empty($post['coverImage']) ? BASE_URL . $post['coverImage'] : ''; ?>" class="preview-img" style="<?php echo !empty($post['coverImage']) ? 'display:inline-block;' : 'display:none;'; ?>" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submission Actions -->
            <div style="border-top: 1px solid #e5e7eb; padding-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
                <a href="<?php echo BASE_URL; ?>/admin/dashboard" class="btn-reset" style="padding: 1rem 2rem; border-radius: 8px; width:auto; text-decoration:none; display: inline-flex; align-items: center; justify-content: center;"><?php echo __("admin_cancel"); ?></a>
                <button type="submit" class="btn-primary" style="padding: 1rem 3rem; border-radius: 8px;"><?php echo __("post_btn_save"); ?></button>
            </div>
        </form>
    </main>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // 1. Slug Generator Touch Listener
        const slugEnInp = document.getElementById('post-slug-en');
        if (slugEnInp) {
            slugEnInp.addEventListener('change', function() {
                this.setAttribute('data-touched', 'true');
            });
        }
        const slugArInp = document.getElementById('post-slug-ar');
        if (slugArInp) {
            slugArInp.addEventListener('change', function() {
                this.setAttribute('data-touched', 'true');
            });
        }

        // 2. Rich Text Toolbar Command Executor (targets active editor block)
        const toolbars = document.querySelectorAll('.richtext-toolbar');
        toolbars.forEach(tb => {
            const editorId = tb.getAttribute('data-editor');
            const editor = document.getElementById(editorId);
            
            tb.querySelectorAll('.richtext-btn').forEach(btn => {
                btn.addEventListener('mousedown', function(e) {
                    e.preventDefault(); // Keep focus inside contenteditable
                });
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Focus the editor first
                    editor.focus();

                    const cmd = this.getAttribute('data-cmd');
                    const val = this.getAttribute('data-val') || null;

                    if (cmd === 'createLink') {
                        const url = prompt("Enter hyperlink URL (e.g. https://example.com):");
                        if (url) document.execCommand(cmd, false, url);
                    } else if (cmd === 'formatBlock') {
                        document.execCommand(cmd, false, `<${val}>`);
                    } else {
                        document.execCommand(cmd, false, val);
                    }
                });
            });
        });

        // Sync editor content editable output to hidden form input before submission
        const editPostForm = document.getElementById('editPostForm');
        if (editPostForm) {
            editPostForm.addEventListener('submit', function() {
                const contentAreaEn = document.getElementById('editorAreaEn');
                const contentHiddenEn = document.getElementById('postContentInputEn');
                if (contentAreaEn && contentHiddenEn) {
                    contentHiddenEn.value = contentAreaEn.innerHTML;
                }
                const contentAreaAr = document.getElementById('editorAreaAr');
                const contentHiddenAr = document.getElementById('postContentInputAr');
                if (contentAreaAr && contentHiddenAr) {
                    contentHiddenAr.value = contentAreaAr.innerHTML;
                }
            });
        }

        // 3. AJAX Image Upload Preview
        const dropzone = document.getElementById('dropzone');
        const fileSelector = document.getElementById('fileSelector');
        const coverImageInput = document.getElementById('post-coverImage');
        const imagePreview = document.getElementById('imagePreview');
        const statusText = document.getElementById('uploadStatusText');

        if (dropzone && fileSelector && coverImageInput) {
            dropzone.addEventListener('click', () => fileSelector.click());

            dropzone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropzone.classList.add('dragover');
            });
            dropzone.addEventListener('dragleave', () => {
                dropzone.classList.remove('dragover');
            });
            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropzone.classList.remove('dragover');
                if (e.dataTransfer.files.length > 0) {
                    uploadFile(e.dataTransfer.files[0]);
                }
            });

            fileSelector.addEventListener('change', function() {
                if (this.files.length > 0) {
                    uploadFile(this.files[0]);
                }
            });
        }

        function uploadFile(file) {
            if (!file) return;

            statusText.style.display = 'block';
            statusText.textContent = 'Uploading...';
            if (imagePreview) imagePreview.style.display = 'none';

            const formData = new FormData();
            formData.append('file', file);
            formData.append('csrf_token', '<?php echo csrfToken(); ?>');

            fetch('<?php echo BASE_URL; ?>/api/upload', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    statusText.textContent = 'Upload complete!';
                    coverImageInput.value = result.relativeUrl;
                    if (imagePreview) {
                        imagePreview.src = result.url;
                        imagePreview.style.display = 'inline-block';
                    }
                } else {
                    statusText.textContent = 'Error: ' + (result.error || 'Upload failed');
                }
            })
            .catch(err => {
                statusText.textContent = 'Connection error.';
                console.error("AJAX Upload failed", err);
            });
        }
    });
</script>
<?php 
// Clean admin layout close
?>
    </main>
</div>
</body>
</html>
