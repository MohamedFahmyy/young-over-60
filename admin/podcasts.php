<?php
// admin/podcasts.php
// Administrator Podcast Episodes CRUD Panel

$podcastMgr = new PodcastManager();
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
        $description = trim($_POST['description'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $duration = trim($_POST['duration'] ?? '');
        $audioFile = trim($_POST['audio_file'] ?? '');
        $coverImage = trim($_POST['cover_image'] ?? '');
        $isFeatured = isset($_POST['is_featured']) ? 1 : 0;

        if (empty($title) || empty($audioFile)) {
            $error = "Title and Audio File are required fields.";
        } else {
            $slug = slugify($title);
            
            $data = [
                'title' => $title,
                'slug' => $slug,
                'description' => $description,
                'category' => $category ?: 'General',
                'duration' => $duration ?: '0:00',
                'audio_file' => $audioFile,
                'cover_image' => $coverImage ?: '/images/hero-bg.png',
                'is_featured' => $isFeatured
            ];

            if ($action === 'add') {
                if ($podcastMgr->createPodcast($data)) {
                    $_SESSION['admin_flash_success'] = "Podcast episode successfully added!";
                    header("Location: " . BASE_URL . "/admin/podcasts");
                    exit();
                } else {
                    $error = "Failed to add episode to the database.";
                }
            } elseif ($action === 'edit' && !empty($editId)) {
                if ($podcastMgr->updatePodcast($editId, $data)) {
                    $_SESSION['admin_flash_success'] = "Podcast episode successfully updated!";
                    header("Location: " . BASE_URL . "/admin/podcasts");
                    exit();
                } else {
                    $error = "Failed to update episode in the database.";
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
        if ($podcastMgr->deletePodcast($editId)) {
            $_SESSION['admin_flash_success'] = "Podcast episode successfully deleted.";
        } else {
            $_SESSION['admin_flash_error'] = "Failed to delete the episode.";
        }
    } else {
        $_SESSION['admin_flash_error'] = "Security validation failed.";
    }
    header("Location: " . BASE_URL . "/admin/podcasts");
    exit();
}

// Prepare item for edit
if ($action === 'edit' && !empty($editId)) {
    $editItem = $podcastMgr->getPodcastById($editId);
    if (!$editItem) {
        $_SESSION['admin_flash_error'] = "Episode not found.";
        header("Location: " . BASE_URL . "/admin/podcasts");
        exit();
    }
}

// Fetch list of podcasts
$podcastsResult = $podcastMgr->getPodcasts(['limit' => 100]);
$podcasts = $podcastsResult['data'];

// Set SEO Meta
$seoPageType = 'admin';
require_once PATH_ROOT . '/includes/header.php';
?>

<div class="admin-layout">
    <!-- Sidebar -->
    <?php require_once PATH_ROOT . '/includes/admin-sidebar.php'; ?>

    <!-- Main Workspace -->
    <main class="admin-main">
        
        <?php if ($action === 'list'): ?>
            <!-- LIST VIEW -->
            <div class="admin-header-row">
                <div>
                    <h1 class="admin-title">Podcasts</h1>
                    <p style="color: #888; font-size: 0.9rem; margin-top: 0.25rem;">Manage your audio journeys, edit episodes, and mark features.</p>
                </div>
                <a href="<?php echo BASE_URL; ?>/admin/podcasts?action=add" class="btn-primary" style="padding: 0.75rem 1.5rem; border-radius: 8px; font-size: 0.75rem; text-decoration: none;">
                    Add Episode
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
                <h2 class="admin-card-title">Podcast Episodes</h2>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Cover</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Duration</th>
                                <th>Featured</th>
                                <th>Published</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($podcasts)): ?>
                                <?php foreach ($podcasts as $pod): 
                                    $editUrl = BASE_URL . '/admin/podcasts?action=edit&id=' . $pod['id'];
                                    $deleteUrl = BASE_URL . '/admin/podcasts?action=delete&id=' . $pod['id'] . '&csrf_token=' . csrfToken();
                                    $cover = !empty($pod['cover_image']) ? $pod['cover_image'] : '/images/hero-bg.png';
                                    ?>
                                    <tr>
                                        <td style="width: 80px;">
                                            <img src="<?php echo e($cover); ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;" />
                                        </td>
                                        <td>
                                            <strong style="font-weight: 600;"><?php echo e($pod['title']); ?></strong>
                                            <div style="font-size: 0.7rem; color: #888; margin-top: 0.15rem; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo e($pod['description']); ?></div>
                                        </td>
                                        <td><?php echo e($pod['category']); ?></td>
                                        <td><?php echo e($pod['duration']); ?></td>
                                        <td>
                                            <span class="status-badge <?php echo $pod['is_featured'] ? 'published' : 'draft'; ?>">
                                                <?php echo $pod['is_featured'] ? 'Yes' : 'No'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo formatDate($pod['created_at']); ?></td>
                                        <td>
                                            <div class="btn-actions">
                                                <a href="<?php echo $editUrl; ?>" class="btn-sm-action">Edit</a>
                                                <a href="<?php echo $deleteUrl; ?>" onclick="return confirm('Are you sure you want to delete this podcast episode?');" class="btn-sm-action delete">Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; color: #999; padding: 3rem 0; font-style: italic;">
                                        No podcast episodes found. Add one above!
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
                    <a href="<?php echo BASE_URL; ?>/admin/podcasts" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.1em; color:#999;">&larr; Back to Podcasts</a>
                    <h1 class="admin-title" style="margin-top:0.5rem;"><?php echo $action === 'add' ? 'Add Episode' : 'Edit Episode'; ?></h1>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="form-status-alert error" style="margin-bottom: 1.5rem;">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span><?php echo e($error); ?></span>
                </div>
            <?php endif; ?>

            <form id="podcastForm" action="<?php echo BASE_URL; ?>/admin/podcasts?action=<?php echo $action; ?><?php echo !empty($editId) ? '&id='.$editId : ''; ?>" method="POST" style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
                <?php echo Auth::csrfInput(); ?>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: start;">
                    
                    <!-- Left Column: Details -->
                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                        <div class="admin-card-box" style="margin: 0; padding: 2rem; display: flex; flex-direction: column; gap: 1.25rem;">
                            <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.5rem;">Episode Information</h3>
                            
                            <!-- Title -->
                            <div class="admin-form-group">
                                <label for="pod-title">Episode Title</label>
                                <input type="text" id="pod-title" name="title" value="<?php echo e($editItem ? $editItem['title'] : ''); ?>" class="admin-form-input" placeholder="e.g. Accessible Travel in Alexandria" required />
                            </div>

                            <!-- Description -->
                            <div class="admin-form-group">
                                <label for="pod-desc">Teaser Description</label>
                                <textarea id="pod-desc" name="description" rows="4" class="admin-form-textarea" placeholder="Brief summary of the conversation..."><?php echo e($editItem ? $editItem['description'] : ''); ?></textarea>
                            </div>

                            <!-- Category & Duration -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="admin-form-group">
                                    <label for="pod-category">Category</label>
                                    <input type="text" id="pod-category" name="category" value="<?php echo e($editItem ? $editItem['category'] : ''); ?>" class="admin-form-input" placeholder="e.g. Solo Travel" />
                                </div>
                                <div class="admin-form-group">
                                    <label for="pod-duration">Duration (e.g. 24:15)</label>
                                    <input type="text" id="pod-duration" name="duration" value="<?php echo e($editItem ? $editItem['duration'] : ''); ?>" class="admin-form-input" placeholder="MM:SS" />
                                </div>
                            </div>

                            <!-- Featured -->
                            <div class="admin-form-group" style="flex-direction: row; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
                                <div>
                                    <label style="display:block; margin:0;">Featured Podcast</label>
                                    <span style="font-size: 0.6rem; color:#aaa;">Promote to the top showcase carousel</span>
                                </div>
                                <input type="checkbox" name="is_featured" value="1" <?php echo ($editItem && $editItem['is_featured']) ? 'checked' : ''; ?> style="width: 20px; height: 20px;" />
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Media Dropzones -->
                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                        <!-- Cover Image Dropzone -->
                        <div class="admin-card-box" style="margin: 0; padding: 2rem;">
                            <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 1.5rem;">Episode Cover</h3>
                            <div class="admin-form-group">
                                <label for="pod-cover">Cover Image URL</label>
                                <input type="text" id="pod-cover" name="cover_image" value="<?php echo e($editItem ? $editItem['cover_image'] : ''); ?>" class="admin-form-input" placeholder="/uploads/..." />
                            </div>
                            
                            <div id="coverDropzone" class="image-upload-preview-box" style="margin-top: 1rem; border: 1px dashed var(--accent-color); padding: 2rem; border-radius: 8px; text-align: center; cursor: pointer;">
                                <p style="font-size: 0.75rem; color: #777;">Click or drop image here</p>
                                <span style="font-size: 0.6rem; color:#999; display:block; margin-top:0.25rem;">PNG, JPG, WebP (Max 2MB)</span>
                                <input type="file" id="coverFileSelector" accept="image/*" style="display:none;" />
                                <div id="coverStatusText" style="font-size:0.7rem; color: var(--primary-color); margin-top:0.5rem; display:none;">Uploading...</div>
                                <img id="coverPreview" src="<?php echo $editItem && $editItem['cover_image'] ? BASE_URL . $editItem['cover_image'] : ''; ?>" style="display: <?php echo $editItem && $editItem['cover_image'] ? 'inline-block' : 'none'; ?>; max-width: 100px; height: 60px; object-fit: cover; margin-top: 1rem; border-radius: 4px;" />
                            </div>
                        </div>

                        <!-- Audio File Dropzone -->
                        <div class="admin-card-box" style="margin: 0; padding: 2rem;">
                            <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 1.5rem;">Audio Episode File</h3>
                            <div class="admin-form-group">
                                <label for="pod-audio">Audio File URL</label>
                                <input type="text" id="pod-audio" name="audio_file" value="<?php echo e($editItem ? $editItem['audio_file'] : ''); ?>" class="admin-form-input" placeholder="/uploads/..." required />
                            </div>
                            
                            <div id="audioDropzone" class="image-upload-preview-box" style="margin-top: 1rem; border: 1px dashed var(--accent-color); padding: 2rem; border-radius: 8px; text-align: center; cursor: pointer;">
                                <p style="font-size: 0.75rem; color: #777;">🎙️ Click or drop MP3/WAV here</p>
                                <span style="font-size: 0.6rem; color:#999; display:block; margin-top:0.25rem;">MP3, WAV, AAC, OGG (Max 50MB)</span>
                                <input type="file" id="audioFileSelector" accept="audio/*" style="display:none;" />
                                <div id="audioStatusText" style="font-size:0.7rem; color: var(--primary-color); margin-top:0.5rem; display:none;">Uploading...</div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Submission Actions -->
                <div style="border-top: 1px solid #e5e7eb; padding-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
                    <a href="<?php echo BASE_URL; ?>/admin/podcasts" class="btn-reset" style="padding: 1rem 2rem; border-radius: 8px; width:auto; text-decoration:none;">Cancel</a>
                    <button type="submit" class="btn-primary" style="padding: 1rem 3rem; border-radius: 8px;">Save Episode</button>
                </div>
            </form>

            <script>
            document.addEventListener("DOMContentLoaded", function() {
                const coverDropzone = document.getElementById('coverDropzone');
                const coverSelector = document.getElementById('coverFileSelector');
                const coverInput = document.getElementById('pod-cover');
                const coverPreview = document.getElementById('coverPreview');
                const coverStatus = document.getElementById('coverStatusText');

                const audioDropzone = document.getElementById('audioDropzone');
                const audioSelector = document.getElementById('audioFileSelector');
                const audioInput = document.getElementById('pod-audio');
                const audioStatus = document.getElementById('audioStatusText');

                // 1. Cover Image Upload
                if (coverDropzone && coverSelector) {
                    coverDropzone.addEventListener('click', () => coverSelector.click());
                    coverSelector.addEventListener('change', function() {
                        if (this.files.length > 0) uploadFile(this.files[0], 'image');
                    });
                    
                    coverDropzone.addEventListener('dragover', (e) => { e.preventDefault(); coverDropzone.style.borderColor = 'var(--primary-color)'; });
                    coverDropzone.addEventListener('dragleave', () => { coverDropzone.style.borderColor = ''; });
                    coverDropzone.addEventListener('drop', (e) => {
                        e.preventDefault();
                        coverDropzone.style.borderColor = '';
                        if (e.dataTransfer.files.length > 0) uploadFile(e.dataTransfer.files[0], 'image');
                    });
                }

                // 2. Audio File Upload
                if (audioDropzone && audioSelector) {
                    audioDropzone.addEventListener('click', () => audioSelector.click());
                    audioSelector.addEventListener('change', function() {
                        if (this.files.length > 0) uploadFile(this.files[0], 'audio');
                    });

                    audioDropzone.addEventListener('dragover', (e) => { e.preventDefault(); audioDropzone.style.borderColor = 'var(--primary-color)'; });
                    audioDropzone.addEventListener('dragleave', () => { audioDropzone.style.borderColor = ''; });
                    audioDropzone.addEventListener('drop', (e) => {
                        e.preventDefault();
                        audioDropzone.style.borderColor = '';
                        if (e.dataTransfer.files.length > 0) uploadFile(e.dataTransfer.files[0], 'audio');
                    });
                }

                function uploadFile(file, type) {
                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('csrf_token', '<?php echo csrfToken(); ?>');

                    let url = '<?php echo BASE_URL; ?>/api/upload';
                    let statusBox = coverStatus;

                    if (type === 'audio') {
                        url += '?type=audio';
                        statusBox = audioStatus;
                    }

                    statusBox.style.display = 'block';
                    statusBox.textContent = 'Uploading file...';

                    fetch(url, {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(result => {
                        if (result.success) {
                            statusBox.textContent = 'Upload complete!';
                            if (type === 'image') {
                                coverInput.value = result.relativeUrl;
                                coverPreview.src = result.url;
                                coverPreview.style.display = 'inline-block';
                            } else {
                                audioInput.value = result.relativeUrl;
                            }
                        } else {
                            statusBox.textContent = 'Error: ' + (result.error || 'Upload failed');
                        }
                    })
                    .catch(err => {
                        statusBox.textContent = 'Upload failed. Network error.';
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
