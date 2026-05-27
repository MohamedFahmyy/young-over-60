<?php
// admin/add-post.php
// Administrator Add Story Screen

$pm = new PostManager();
$categories = $pm->getCategories();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Auth::requireCsrf();
        
        $title = $_POST['title'] ?? '';
        $slug = $_POST['slug'] ?? '';
        $excerpt = $_POST['excerpt'] ?? '';
        $content = $_POST['content'] ?? '';
        $coverImage = $_POST['coverImage'] ?? '';
        $categoryId = $_POST['categoryId'] ?? '';
        $isFeatured = isset($_POST['isFeatured']) ? 1 : 0;
        $status = $_POST['status'] ?? 'DRAFT';

        // Basic validation
        if (empty($title) || empty($content) || empty($categoryId)) {
            $error = "Title, Category, and Content are required fields.";
        } else {
            // Generate slug if empty
            if (empty($slug)) {
                $slug = slugify($title);
            } else {
                $slug = slugify($slug);
            }

            // Ensure unique slug
            $existing = $pm->getPostBySlug($slug, true);
            if ($existing) {
                $slug = $slug . '-' . time();
            }

            $postData = [
                'title' => $title,
                'slug' => $slug,
                'excerpt' => $excerpt,
                'content' => $content,
                'coverImage' => $coverImage,
                'isFeatured' => $isFeatured,
                'status' => $status,
                'categoryId' => $categoryId
            ];

            if ($pm->createPost($postData)) {
                $_SESSION['admin_flash_success'] = "Story successfully published!";
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
                <a href="<?php echo BASE_URL; ?>/admin/dashboard" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.1em; color:#999;">&larr; Back to Dashboard</a>
                <h1 class="admin-title" style="margin-top:0.5rem;">Create Story</h1>
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
            <input type="hidden" id="postContentInput" name="content" />

            <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
                <!-- Left panel (main editor content) -->
                <div style="display:flex; flex-direction:column; gap:1.5rem;">
                    <!-- Title -->
                    <div class="admin-form-group">
                        <label for="post-title">Story Title</label>
                        <input type="text" id="post-title" name="title" class="admin-form-input" placeholder="Enter story title..." required style="font-size: 1.5rem; font-family: var(--font-serif);" />
                    </div>

                    <!-- Slug -->
                    <div class="admin-form-group">
                        <label for="post-slug">URL Slug</label>
                        <input type="text" id="post-slug" name="slug" class="admin-form-input" placeholder="url-slug-here" style="font-family: monospace;" />
                    </div>

                    <!-- Excerpt -->
                    <div class="admin-form-group">
                        <label for="post-excerpt">Excerpt</label>
                        <textarea id="post-excerpt" name="excerpt" rows="3" class="admin-form-textarea" placeholder="Provide a short magazine teaser excerpt..."></textarea>
                    </div>

                    <!-- Content (Rich Text ContentEditable Editor) -->
                    <div class="admin-form-group">
                        <label>Full Content Body</label>
                        <div class="richtext-editor">
                            <!-- Toolbar -->
                            <div class="richtext-toolbar">
                                <button type="button" class="richtext-btn" data-cmd="bold">B</button>
                                <button type="button" class="richtext-btn" data-cmd="italic" style="font-style: italic;">I</button>
                                <button type="button" class="richtext-btn" data-cmd="underline" style="text-decoration: underline;">U</button>
                                <button type="button" class="richtext-btn" data-cmd="formatBlock" data-val="h2">H2</button>
                                <button type="button" class="richtext-btn" data-cmd="formatBlock" data-val="h3">H3</button>
                                <button type="button" class="richtext-btn" data-cmd="createLink">Link</button>
                                <button type="button" class="richtext-btn" data-cmd="removeFormat">Clear</button>
                            </div>
                            <!-- Contenteditable Area -->
                            <div id="editorArea" class="richtext-content" contenteditable="true" placeholder="Compose your accessible travel story..."></div>
                        </div>
                    </div>
                </div>
                
                <!-- Right panel (Sidebar controls) -->
                <div style="display:flex; flex-direction:column; gap:1.5rem;">
                    <div class="admin-card-box" style="margin: 0; padding: 2rem;">
                        <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 1.5rem;">Publishing Settings</h3>
                        
                        <!-- Category Selection -->
                        <div class="admin-form-group">
                            <label for="post-category">Category</label>
                            <select id="post-category" name="categoryId" class="admin-form-select" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo e($cat['id']); ?>"><?php echo e($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Status Toggle -->
                        <div class="admin-form-group" style="margin-top: 1rem;">
                            <label>Status</label>
                            <select name="status" class="admin-form-select">
                                <option value="DRAFT" selected>DRAFT</option>
                                <option value="PUBLISHED">PUBLISHED</option>
                            </select>
                        </div>

                        <!-- Featured Toggle -->
                        <div class="admin-form-group" style="margin-top: 1rem; flex-direction: row; justify-content: space-between; align-items: center;">
                            <div>
                                <label style="display:block; margin:0;">Featured Article</label>
                                <span style="font-size: 0.6rem; color:#aaa;">Promote to homepage banner</span>
                            </div>
                            <input type="checkbox" name="isFeatured" value="1" style="width: 20px; height: 20px;" />
                        </div>
                    </div>

                    <!-- Cover Image Upload Box -->
                    <div class="admin-card-box" style="margin: 0; padding: 2rem;">
                        <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 1.5rem;">Teaser Imagery</h3>
                        
                        <div class="admin-form-group">
                            <label for="post-coverImage">Cover Image URL</label>
                            <input type="text" id="post-coverImage" name="coverImage" class="admin-form-input" placeholder="/uploads/..." />
                        </div>
                        
                        <!-- Drag-and-drop Image Uploader Box -->
                        <div id="dropzone" class="image-upload-preview-box">
                            <p style="font-size: 0.75rem; color: #777;">Drag & drop image here or click to upload</p>
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
                <a href="<?php echo BASE_URL; ?>/admin/dashboard" class="btn-reset" style="padding: 1rem 2rem; border-radius: 8px; width:auto; text-decoration:none;">Cancel</a>
                <button type="submit" class="btn-primary" style="padding: 1rem 3rem; border-radius: 8px;">Publish Story</button>
            </div>
        </form>
    </main>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // 1. Slug Generator
        const titleInp = document.getElementById('post-title');
        const slugInp = document.getElementById('post-slug');
        if (titleInp && slugInp) {
            titleInp.addEventListener('input', function() {
                if (slugInp.value === '' || this.getAttribute('data-touched') !== 'true') {
                    slugInp.value = this.value.toLowerCase()
                        .replace(/[^\w\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/--+/g, '-');
                }
            });
            slugInp.addEventListener('change', function() {
                this.setAttribute('data-touched', 'true');
            });
        }

        // 2. Rich Text Toolbar Command Executor
        const toolbarButtons = document.querySelectorAll('.richtext-btn');
        toolbarButtons.forEach(btn => {
            btn.addEventListener('click', function() {
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

        // Sync editor content editable output to hidden form input before submission
        const addPostForm = document.getElementById('addPostForm');
        if (addPostForm) {
            addPostForm.addEventListener('submit', function() {
                const contentArea = document.getElementById('editorArea');
                const contentHidden = document.getElementById('postContentInput');
                if (contentArea && contentHidden) {
                    contentHidden.value = contentArea.innerHTML;
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
