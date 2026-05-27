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
        
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $readTime = trim($_POST['read_time'] ?? '');
        $coverImage = trim($_POST['cover_image'] ?? '');

        if (empty($title) || empty($content)) {
            $error = "Title and Content are required fields.";
        } else {
            if (empty($slug)) {
                $slug = slugify($title);
            } else {
                $slug = slugify($slug);
            }

            // Verify slug uniqueness if new or changed
            $existing = $storyMgr->getStoryBySlug($slug);
            if ($existing && ($action === 'add' || $existing['id'] !== $editId)) {
                $slug = $slug . '-' . time();
            }
            
            $data = [
                'title' => $title,
                'slug' => $slug,
                'excerpt' => $excerpt,
                'content' => $content,
                'category' => $category ?: 'General',
                'author' => $author ?: 'Guest Writer',
                'read_time' => $readTime ?: '5 min read',
                'cover_image' => $coverImage ?: '/images/hero-bg.png'
            ];

            if ($action === 'add') {
                if ($storyMgr->createStory($data)) {
                    $_SESSION['admin_flash_success'] = "Story successfully published!";
                    header("Location: " . BASE_URL . "/admin/women-stories");
                    exit();
                } else {
                    $error = "Failed to save story to the database.";
                }
            } elseif ($action === 'edit' && !empty($editId)) {
                if ($storyMgr->updateStory($editId, $data)) {
                    $_SESSION['admin_flash_success'] = "Story successfully updated!";
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
            $_SESSION['admin_flash_success'] = "Story successfully deleted.";
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
                    <h1 class="admin-title">Women Stories</h1>
                    <p style="color: #888; font-size: 0.9rem; margin-top: 0.25rem;">Manage Egypt travel diaries and safety reviews written by women explorers.</p>
                </div>
                <a href="<?php echo BASE_URL; ?>/admin/women-stories?action=add" class="btn-primary" style="padding: 0.75rem 1.5rem; border-radius: 8px; font-size: 0.75rem; text-decoration: none;">
                    Write Story
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
                <h2 class="admin-card-title">Egypt Chronicles</h2>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Cover</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Author</th>
                                <th>Read Time</th>
                                <th>Published Date</th>
                                <th>Actions</th>
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
                                            <strong style="font-weight: 600;"><?php echo e($story['title']); ?></strong>
                                        </td>
                                        <td><?php echo e($story['category']); ?></td>
                                        <td><?php echo e($story['author']); ?></td>
                                        <td><?php echo e($story['read_time']); ?></td>
                                        <td><?php echo formatDate($story['created_at']); ?></td>
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
                                    <td colspan="7" style="text-align: center; color: #999; padding: 3rem 0; font-style: italic;">
                                        No stories published yet. Write one above!
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
                    <a href="<?php echo BASE_URL; ?>/admin/women-stories" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.1em; color:#999;">&larr; Back to Stories</a>
                    <h1 class="admin-title" style="margin-top:0.5rem;"><?php echo $action === 'add' ? 'Publish New Story' : 'Edit Story'; ?></h1>
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
                <input type="hidden" id="storyContentInput" name="content" />

                <div style="display: grid; grid-template-columns: 2.2fr 1fr; gap: 2rem; align-items: start;">
                    
                    <!-- Left Column: Rich Text Editor & Text Areas -->
                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                        
                        <!-- Title -->
                        <div class="admin-form-group">
                            <label for="story-title">Story Title</label>
                            <input type="text" id="story-title" name="title" value="<?php echo e($editItem ? $editItem['title'] : ''); ?>" class="admin-form-input" placeholder="Enter story title..." required style="font-size: 1.5rem; font-family: var(--font-serif);" />
                        </div>

                        <!-- Slug -->
                        <div class="admin-form-group">
                            <label for="story-slug">URL Slug</label>
                            <input type="text" id="story-slug" name="slug" value="<?php echo e($editItem ? $editItem['slug'] : ''); ?>" class="admin-form-input" placeholder="url-slug-here" style="font-family: monospace;" />
                        </div>

                        <!-- Excerpt -->
                        <div class="admin-form-group">
                            <label for="story-excerpt">Excerpt / Magazine Teaser</label>
                            <textarea id="story-excerpt" name="excerpt" rows="3" class="admin-form-textarea" placeholder="Provide a brief introductory excerpt..."><?php echo e($editItem ? $editItem['excerpt'] : ''); ?></textarea>
                        </div>

                        <!-- Content Editor -->
                        <div class="admin-form-group">
                            <label>Full Content Body</label>
                            <div style="background: #ffffff; border: 1px solid var(--accent-color); border-radius: 8px; min-height: 380px;">
                                <div id="editorArea" style="min-height: 350px; font-size: 1.1rem; line-height: 1.6; font-family: Georgia, serif;">
                                    <?php echo $editItem ? $editItem['content'] : ''; ?>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column: Settings and Cover -->
                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                        
                        <!-- Details Panel -->
                        <div class="admin-card-box" style="margin: 0; padding: 2rem; display: flex; flex-direction: column; gap: 1.25rem;">
                            <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.5rem;">Publishing Metadata</h3>
                            
                            <!-- Category Selection -->
                            <div class="admin-form-group">
                                <label for="story-category">Category</label>
                                <select id="story-category" name="category" class="admin-form-select">
                                    <option value="Solo Travel" <?php echo ($editItem && $editItem['category'] === 'Solo Travel') ? 'selected' : ''; ?>>Solo Travel</option>
                                    <option value="Accessible Adventure" <?php echo ($editItem && $editItem['category'] === 'Accessible Adventure') ? 'selected' : ''; ?>>Accessible Adventure</option>
                                    <option value="Cultural Journeys" <?php echo ($editItem && $editItem['category'] === 'Cultural Journeys') ? 'selected' : ''; ?>>Cultural Journeys</option>
                                    <option value="General" <?php echo ($editItem && $editItem['category'] === 'General') ? 'selected' : ''; ?>>General</option>
                                </select>
                            </div>

                            <!-- Author -->
                            <div class="admin-form-group">
                                <label for="story-author">Author Name</label>
                                <input type="text" id="story-author" name="author" value="<?php echo e($editItem ? $editItem['author'] : 'Guest Writer'); ?>" class="admin-form-input" required />
                            </div>

                            <!-- Read Time -->
                            <div class="admin-form-group">
                                <label for="story-readtime">Read Time (e.g. 5 min read)</label>
                                <input type="text" id="story-readtime" name="read_time" value="<?php echo e($editItem ? $editItem['read_time'] : '5 min read'); ?>" class="admin-form-input" required />
                            </div>
                        </div>

                        <!-- Cover Image Dropzone -->
                        <div class="admin-card-box" style="margin: 0; padding: 2rem;">
                            <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 1.5rem;">Story Cover</h3>
                            <div class="admin-form-group">
                                <label for="story-cover">Cover Image URL</label>
                                <input type="text" id="story-cover" name="cover_image" value="<?php echo e($editItem ? $editItem['cover_image'] : ''); ?>" class="admin-form-input" placeholder="/uploads/..." />
                            </div>
                            
                            <div id="coverDropzone" class="image-upload-preview-box" style="margin-top: 1rem; border: 1px dashed var(--accent-color); padding: 2rem; border-radius: 8px; text-align: center; cursor: pointer;">
                                <p style="font-size: 0.75rem; color: #777;">Click or drop image here</p>
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
                    <a href="<?php echo BASE_URL; ?>/admin/women-stories" class="btn-reset" style="padding: 1rem 2rem; border-radius: 8px; width:auto; text-decoration:none;">Cancel</a>
                    <button type="submit" class="btn-primary" style="padding: 1rem 3rem; border-radius: 8px;">Publish Story</button>
                </div>
            </form>

            <!-- Include Quill Editor JS -->
            <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
            <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Initialize Quill Editor
                const quill = new Quill('#editorArea', {
                    modules: {
                        toolbar: [
                            [{ 'header': [2, 3, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            ['link', 'blockquote'],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            ['clean']
                        ]
                    },
                    placeholder: 'Compose Egypt travel stories written by women...',
                    theme: 'snow'
                });

                // Title to Slug Generator
                const titleInp = document.getElementById('story-title');
                const slugInp = document.getElementById('story-slug');
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

                // Sync Quill Editor output on Form Submit
                const form = document.getElementById('storyForm');
                const contentInput = document.getElementById('storyContentInput');
                if (form && contentInput) {
                    form.addEventListener('submit', function(e) {
                        contentInput.value = quill.root.innerHTML;
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
