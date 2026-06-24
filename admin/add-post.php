<?php
// admin/add-post.php
// Administrator Add Story Screen

$pm = new PostManager();
$categories = $pm->getCategories();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Auth::requireCsrf();
        
        $title_en = $_POST['title_en'] ?? '';
        $title_ar = $_POST['title_ar'] ?? '';
        $title_nl = $_POST['title_nl'] ?? '';
        $slug_en = $_POST['slug_en'] ?? '';
        $slug_ar = $_POST['slug_ar'] ?? '';
        $slug_nl = $_POST['slug_nl'] ?? '';
        $excerpt_en = $_POST['excerpt_en'] ?? '';
        $excerpt_ar = $_POST['excerpt_ar'] ?? '';
        $excerpt_nl = $_POST['excerpt_nl'] ?? '';
        $content_en = $_POST['content_en'] ?? '';
        $content_ar = $_POST['content_ar'] ?? '';
        $content_nl = $_POST['content_nl'] ?? '';
        $coverImage = $_POST['coverImage'] ?? '';
        $alt_text_en = $_POST['alt_text_en'] ?? '';
        $alt_text_ar = $_POST['alt_text_ar'] ?? '';
        $alt_text_nl = $_POST['alt_text_nl'] ?? '';
        $categoryId = $_POST['categoryId'] ?? '';
        $isFeatured = isset($_POST['isFeatured']) ? 1 : 0;
        $status = $_POST['status'] ?? 'DRAFT';

        // Basic validation
        if (empty($title_en) || empty($content_en) || empty($categoryId)) {
            $error = __("post_fields_required");
        } else {
            // Generate slug if empty for English
            if (empty($slug_en)) {
                $slug_en = slugify($title_en);
            } else {
                $slug_en = slugify($slug_en);
            }

            // Ensure unique English slug
            $existingEn = $pm->getPostBySlug($slug_en, true);
            if ($existingEn) {
                $slug_en = $slug_en . '-' . time();
            }

            // Generate slug if empty for Arabic (only if Arabic title is provided)
            if (!empty($title_ar)) {
                if (empty($slug_ar)) {
                    $slug_ar = slugify($title_ar);
                } else {
                    $slug_ar = slugify($slug_ar);
                }
                // Ensure unique Arabic slug
                $existingAr = $pm->getPostBySlug($slug_ar, true);
                if ($existingAr) {
                    $slug_ar = $slug_ar . '-' . time();
                }
            } else {
                $slug_ar = null;
            }

            // Generate slug if empty for Dutch (only if Dutch title is provided)
            if (!empty($title_nl)) {
                if (empty($slug_nl)) {
                    $slug_nl = slugify($title_nl);
                } else {
                    $slug_nl = slugify($slug_nl);
                }
                // Ensure unique Dutch slug
                $existingNl = $pm->getPostBySlug($slug_nl, true);
                if ($existingNl) {
                    $slug_nl = $slug_nl . '-' . time();
                }
            } else {
                $slug_nl = null;
            }

            $postData = [
                'title_en' => $title_en,
                'title_ar' => !empty($title_ar) ? $title_ar : null,
                'title_nl' => !empty($title_nl) ? $title_nl : null,
                'slug_en' => $slug_en,
                'slug_ar' => $slug_ar,
                'slug_nl' => $slug_nl,
                'excerpt_en' => !empty($excerpt_en) ? $excerpt_en : null,
                'excerpt_ar' => !empty($excerpt_ar) ? $excerpt_ar : null,
                'excerpt_nl' => !empty($excerpt_nl) ? $excerpt_nl : null,
                'content_en' => $content_en,
                'content_ar' => !empty($content_ar) ? $content_ar : null,
                'content_nl' => $content_nl,
                'coverImage' => $coverImage,
                'alt_text_en' => !empty($alt_text_en) ? $alt_text_en : null,
                'alt_text_ar' => !empty($alt_text_ar) ? $alt_text_ar : null,
                'alt_text_nl' => !empty($alt_text_nl) ? $alt_text_nl : null,
                'isFeatured' => $isFeatured,
                'status' => $status,
                'categoryId' => $categoryId
            ];

            if ($pm->createPost($postData)) {
                $_SESSION['admin_flash_success'] = __("post_added_success");
                header("Location: " . BASE_URL . "/admin/dashboard");
                exit();
            } else {
                $error = "Failed to write story to database. Please review parameters.";
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
                <h1 class="admin-title" style="margin-top:0.5rem;"><?php echo __("post_create_title"); ?></h1>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="form-status-alert error" style="margin-bottom: 1.5rem;">
                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span><?php echo e($error); ?></span>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form id="addPostForm" action="<?php echo BASE_URL; ?>/admin/add-post" method="POST" style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
            <?php echo Auth::csrfInput(); ?>
            <input type="hidden" id="postContentInputEn" name="content_en" />
            <input type="hidden" id="postContentInputAr" name="content_ar" />
            <input type="hidden" id="postContentInputNl" name="content_nl" />

            <div class="admin-grid-layout admin-split-2-1">
                <!-- Left panel (main editor content) -->
                <div style="display:flex; flex-direction:column; gap:1.5rem; min-width: 0;">
                    <!-- Titles Row -->
                    <div class="admin-grid-fields-3">
                        <!-- Title EN -->
                        <div class="admin-form-group">
                            <label for="post-title-en"><?php echo __("post_field_title_en"); ?></label>
                            <input type="text" id="post-title-en" name="title_en" class="admin-form-input" placeholder="<?php echo __("post_field_title_en"); ?>..." required style="font-size: 1.25rem; font-family: var(--font-serif);" />
                        </div>
                        <!-- Title AR -->
                        <div class="admin-form-group">
                            <label for="post-title-ar"><?php echo __("post_field_title_ar"); ?></label>
                            <input type="text" id="post-title-ar" name="title_ar" class="admin-form-input" placeholder="<?php echo __("post_field_title_ar"); ?>..." style="font-size: 1.25rem; font-family: var(--font-serif); direction: rtl;" />
                        </div>
                        <!-- Title NL -->
                        <div class="admin-form-group">
                            <label for="post-title-nl"><?php echo __("post_field_title_nl"); ?></label>
                            <input type="text" id="post-title-nl" name="title_nl" class="admin-form-input" placeholder="<?php echo __("post_field_title_nl"); ?>..." style="font-size: 1.25rem; font-family: var(--font-serif);" />
                        </div>
                    </div>

                    <!-- Slugs Row -->
                    <div class="admin-grid-fields-3">
                        <!-- Slug EN -->
                        <div class="admin-form-group">
                            <label for="post-slug-en"><?php echo __("post_field_slug_en"); ?></label>
                            <input type="text" id="post-slug-en" name="slug_en" class="admin-form-input" placeholder="english-slug-here" style="font-family: monospace;" />
                        </div>
                        <!-- Slug AR -->
                        <div class="admin-form-group">
                            <label for="post-slug-ar"><?php echo __("post_field_slug_ar"); ?></label>
                            <input type="text" id="post-slug-ar" name="slug_ar" class="admin-form-input" placeholder="arabic-slug-here" style="font-family: monospace; direction: rtl;" />
                        </div>
                        <!-- Slug NL -->
                        <div class="admin-form-group">
                            <label for="post-slug-nl"><?php echo __("post_field_slug_nl"); ?></label>
                            <input type="text" id="post-slug-nl" name="slug_nl" class="admin-form-input" placeholder="dutch-slug-here" style="font-family: monospace;" />
                        </div>
                    </div>

                    <!-- Excerpts Row -->
                    <div class="admin-grid-fields-3">
                        <!-- Excerpt EN -->
                        <div class="admin-form-group">
                            <label for="post-excerpt-en"><?php echo __("admin_description"); ?> (EN)</label>
                            <textarea id="post-excerpt-en" name="excerpt_en" rows="3" class="admin-form-textarea" placeholder="Provide a short magazine teaser excerpt in English..."></textarea>
                        </div>
                        <!-- Excerpt AR -->
                        <div class="admin-form-group">
                            <label for="post-excerpt-ar"><?php echo __("admin_description"); ?> (AR)</label>
                            <textarea id="post-excerpt-ar" name="excerpt_ar" rows="3" class="admin-form-textarea" placeholder="اكتب مقتطفًا قصيرًا للمقال باللغة العربية..." style="direction: rtl;"></textarea>
                        </div>
                        <!-- Excerpt NL -->
                        <div class="admin-form-group">
                            <label for="post-excerpt-nl"><?php echo __("admin_description"); ?> (NL)</label>
                            <textarea id="post-excerpt-nl" name="excerpt_nl" rows="3" class="admin-form-textarea" placeholder="Korte samenvatting in het Nederlands..."></textarea>
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
                            <div id="editorAreaEn" class="richtext-content" contenteditable="true" placeholder="Compose your accessible travel story in English..."></div>
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
                            <div id="editorAreaAr" class="richtext-content" contenteditable="true" placeholder="Compose your accessible travel story in Arabic..." style="direction: rtl;"></div>
                        </div>
                    </div>

                    <!-- Content NL (Rich Text ContentEditable Editor) -->
                    <div class="admin-form-group">
                        <label><?php echo __("post_field_content_nl"); ?></label>
                        <div class="richtext-editor">
                            <!-- Toolbar -->
                            <div class="richtext-toolbar" data-editor="editorAreaNl">
                                <button type="button" class="richtext-btn" data-cmd="bold">B</button>
                                <button type="button" class="richtext-btn" data-cmd="italic" style="font-style: italic;">I</button>
                                <button type="button" class="richtext-btn" data-cmd="underline" style="text-decoration: underline;">U</button>
                                <button type="button" class="richtext-btn" data-cmd="formatBlock" data-val="h2">H2</button>
                                <button type="button" class="richtext-btn" data-cmd="formatBlock" data-val="h3">H3</button>
                                <button type="button" class="richtext-btn" data-cmd="createLink">Link</button>
                                <button type="button" class="richtext-btn" data-cmd="removeFormat">Clear</button>
                            </div>
                            <!-- Contenteditable Area -->
                            <div id="editorAreaNl" class="richtext-content" contenteditable="true" placeholder="Schrijf uw toegankelijke reisverhaal in het Nederlands..."></div>
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
                                    <option value="<?php echo e($cat['id']); ?>">
                                        <?php echo e(CURRENT_LANG === 'ar' && !empty($cat['name_ar']) ? $cat['name_ar'] : $cat['name_en']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Status Toggle -->
                        <div class="admin-form-group" style="margin-top: 1rem;">
                            <label><?php echo __("post_field_status"); ?></label>
                            <select name="status" class="admin-form-select">
                                <option value="DRAFT" selected><?php echo __("admin_draft"); ?></option>
                                <option value="PUBLISHED"><?php echo __("admin_published"); ?></option>
                            </select>
                        </div>

                        <!-- Featured Toggle -->
                        <div class="admin-form-group" style="margin-top: 1rem; display: flex; flex-direction: row; justify-content: space-between; align-items: center; gap: 0.5rem;">
                            <div>
                                <label style="display:block; margin:0;"><?php echo __("post_field_featured"); ?></label>
                                <span style="font-size: 0.6rem; color:#aaa;"><?php echo CURRENT_LANG === 'ar' ? 'عرض على البانر الرئيسي للموقع' : 'Promote to homepage banner'; ?></span>
                            </div>
                            <input type="checkbox" name="isFeatured" value="1" style="width: 20px; height: 20px;" />
                        </div>
                    </div>

                    <!-- Cover Image Upload Box -->
                    <div class="admin-card-box" style="margin: 0; padding: 2rem;">
                        <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 1.5rem;"><?php echo CURRENT_LANG === 'ar' ? 'صور الغلاف والترويج' : 'Cover & Teaser Imagery'; ?></h3>
                        
                        <div class="admin-form-group">
                            <label for="post-coverImage"><?php echo __("post_field_cover"); ?></label>
                            <input type="text" id="post-coverImage" name="coverImage" class="admin-form-input" placeholder="/uploads/..." />
                        </div>

                        <!-- Image Alt Text EN & AR -->
                        <div class="admin-form-group" style="margin-top: 1rem;">
                            <label for="post-alt-en"><?php echo __("post_field_alt_en"); ?></label>
                            <input type="text" id="post-alt-en" name="alt_text_en" class="admin-form-input" placeholder="Alt text in English" />
                        </div>
                        <div class="admin-form-group" style="margin-top: 1rem;">
                            <label for="post-alt-ar"><?php echo __("post_field_alt_ar"); ?></label>
                            <input type="text" id="post-alt-ar" name="alt_text_ar" class="admin-form-input" placeholder="النص البديل بالعربية" style="direction: rtl;" />
                        </div>
                        <div class="admin-form-group" style="margin-top: 1rem;">
                            <label for="post-alt-nl"><?php echo __("post_field_alt_nl"); ?></label>
                            <input type="text" id="post-alt-nl" name="alt_text_nl" class="admin-form-input" placeholder="Alt text in Dutch" />
                        </div>
                        
                        <!-- Drag-and-drop Image Uploader Box -->
                        <div id="dropzone" class="image-upload-preview-box" style="margin-top: 1.5rem;">
                            <p style="font-size: 0.75rem; color: #777;"><?php echo __("settings_click_drop"); ?></p>
                            <span style="font-size: 0.6rem; color:#999; display:block; margin-top:0.25rem;">PNG, JPG, WebP (Max 2MB)</span>
                            <input type="file" id="fileSelector" accept="image/*" style="display:none;" />
                            <div id="uploadStatusText" style="font-size:0.7rem; color: #2d5a88; margin-top:0.5rem; display:none;">Uploading...</div>
                            <img id="imagePreview" class="preview-img" style="display:none;" />
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
        
        // 1. Slug Generator for EN
        const titleEnInp = document.getElementById('post-title-en');
        const slugEnInp = document.getElementById('post-slug-en');
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

        // Slug Generator for AR
        const titleArInp = document.getElementById('post-title-ar');
        const slugArInp = document.getElementById('post-slug-ar');
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

        // Slug Generator for NL (Dutch)
        const titleNlInp = document.getElementById('post-title-nl');
        const slugNlInp = document.getElementById('post-slug-nl');
        if (titleNlInp && slugNlInp) {
            titleNlInp.addEventListener('input', function() {
                if (slugNlInp.value === '' || this.getAttribute('data-touched') !== 'true') {
                    slugNlInp.value = this.value.toLowerCase()
                        .normalize('NFD')
                        .replace(/[\u0300-\u036f]/g, '')
                        .replace(/[^\w\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/--+/g, '-');
                }
            });
            slugNlInp.addEventListener('change', function() {
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
        const addPostForm = document.getElementById('addPostForm');
        if (addPostForm) {
            addPostForm.addEventListener('submit', function() {
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
                const contentAreaNl = document.getElementById('editorAreaNl');
                const contentHiddenNl = document.getElementById('postContentInputNl');
                if (contentAreaNl && contentHiddenNl) {
                    contentHiddenNl.value = contentAreaNl.innerHTML;
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
            // Trigger selection on click
            dropzone.addEventListener('click', () => fileSelector.click());

            // Dragover effects
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
