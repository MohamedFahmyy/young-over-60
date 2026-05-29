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
        
        $title_en = trim($_POST['title_en'] ?? '');
        $title_ar = trim($_POST['title_ar'] ?? '');
        $slug_en = trim($_POST['slug_en'] ?? '');
        $slug_ar = trim($_POST['slug_ar'] ?? '');
        $description_en = trim($_POST['description_en'] ?? '');
        $description_ar = trim($_POST['description_ar'] ?? '');
        $category_en = trim($_POST['category_en'] ?? '');
        $category_ar = trim($_POST['category_ar'] ?? '');
        $duration = trim($_POST['duration'] ?? '');
        $audioFile = trim($_POST['audio_file'] ?? '');
        $coverImage = trim($_POST['cover_image'] ?? '');
        $alt_text_en = trim($_POST['alt_text_en'] ?? '');
        $alt_text_ar = trim($_POST['alt_text_ar'] ?? '');
        $isFeatured = isset($_POST['is_featured']) ? 1 : 0;

        if (empty($title_en) || empty($audioFile)) {
            $error = "Title (EN) and Audio File are required fields.";
        } else {
            $slug_en = slugify(empty($slug_en) ? $title_en : $slug_en);
            
            if (!empty($title_ar)) {
                $slug_ar = slugify(empty($slug_ar) ? $title_ar : $slug_ar);
            } else {
                $slug_ar = null;
            }
            
            $data = [
                'title_en' => $title_en,
                'title_ar' => !empty($title_ar) ? $title_ar : null,
                'slug_en' => $slug_en,
                'slug_ar' => $slug_ar,
                'description_en' => !empty($description_en) ? $description_en : null,
                'description_ar' => !empty($description_ar) ? $description_ar : null,
                'category_en' => $category_en ?: 'General',
                'category_ar' => $category_ar ?: 'عام',
                'duration' => $duration ?: '0:00',
                'audio_file' => $audioFile,
                'cover_image' => $coverImage ?: '/images/hero-bg.png',
                'alt_text_en' => !empty($alt_text_en) ? $alt_text_en : null,
                'alt_text_ar' => !empty($alt_text_ar) ? $alt_text_ar : null,
                'is_featured' => $isFeatured
            ];

            if ($action === 'add') {
                if ($podcastMgr->createPodcast($data)) {
                    $_SESSION['admin_flash_success'] = __("pod_added_success");
                    header("Location: " . BASE_URL . "/admin/podcasts");
                    exit();
                } else {
                    $error = "Failed to add episode to the database.";
                }
            } elseif ($action === 'edit' && !empty($editId)) {
                if ($podcastMgr->updatePodcast($editId, $data)) {
                    $_SESSION['admin_flash_success'] = __("pod_updated_success");
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
            $_SESSION['admin_flash_success'] = __("pod_deleted_success");
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
                    <h1 class="admin-title"><?php echo __("admin_podcasts"); ?></h1>
                    <p style="color: #888; font-size: 0.9rem; margin-top: 0.25rem;"><?php echo __("pod_manage_desc"); ?></p>
                </div>
                <a href="<?php echo BASE_URL; ?>/admin/podcasts?action=add" class="btn-primary" style="padding: 0.75rem 1.5rem; border-radius: 8px; font-size: 0.75rem; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">
                    <?php echo __("pod_add_title"); ?>
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
                <h2 class="admin-card-title"><?php echo __("admin_podcasts"); ?></h2>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th><?php echo __("dash_cover"); ?></th>
                                <th><?php echo __("dash_title"); ?></th>
                                <th><?php echo __("dash_category"); ?></th>
                                <th><?php echo __("admin_duration"); ?></th>
                                <th><?php echo __("admin_featured"); ?></th>
                                <th><?php echo __("admin_published"); ?></th>
                                <th><?php echo __("admin_actions"); ?></th>
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
                                            <strong style="font-weight: 600;">
                                                <?php echo e(CURRENT_LANG === 'ar' && !empty($pod['title_ar']) ? $pod['title_ar'] : $pod['title_en']); ?>
                                            </strong>
                                            <div style="font-size: 0.7rem; color: #888; margin-top: 0.15rem; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                <?php echo e(CURRENT_LANG === 'ar' && !empty($pod['description_ar']) ? $pod['description_ar'] : $pod['description_en']); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php echo e(CURRENT_LANG === 'ar' && !empty($pod['category_ar']) ? $pod['category_ar'] : $pod['category_en']); ?>
                                        </td>
                                        <td><?php echo e($pod['duration']); ?></td>
                                        <td>
                                            <span class="status-badge <?php echo $pod['is_featured'] ? 'published' : 'draft'; ?>">
                                                <?php echo $pod['is_featured'] ? (CURRENT_LANG === 'ar' ? 'نعم' : 'Yes') : (CURRENT_LANG === 'ar' ? 'لا' : 'No'); ?>
                                            </span>
                                        </td>
                                        <td><?php echo formatDate($pod['created_at']); ?></td>
                                        <td>
                                            <div class="btn-actions">
                                                <a href="<?php echo $editUrl; ?>" class="btn-sm-action"><?php echo __("admin_edit"); ?></a>
                                                <a href="<?php echo $deleteUrl; ?>" onclick="return confirm('<?php echo __("pod_confirm_delete"); ?>');" class="btn-sm-action delete"><?php echo __("admin_delete"); ?></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; color: #999; padding: 3rem 0; font-style: italic;">
                                        <?php echo CURRENT_LANG === 'ar' ? 'لم يتم العثور على حلقات بودكاست. أضف حلقة جديدة أعلاه!' : 'No podcast episodes found. Add one above!'; ?>
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
                    <a href="<?php echo BASE_URL; ?>/admin/podcasts" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.1em; color:#999; display: flex; align-items: center; gap: 0.5rem;">
                        <?php echo CURRENT_LANG === 'ar' ? '&rarr;' : '&larr;'; ?> <?php echo __("admin_back"); ?>
                    </a>
                    <h1 class="admin-title" style="margin-top:0.5rem;"><?php echo $action === 'add' ? __("pod_add_title") : __("pod_edit_title"); ?></h1>
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

                <div class="admin-grid-layout" style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; align-items: start;">
                    
                    <!-- Left Column: Details -->
                    <div style="display: flex; flex-direction: column; gap: 1.5rem; min-width: 0;">
                        <div class="admin-card-box" style="margin: 0; padding: 2rem; display: flex; flex-direction: column; gap: 1.25rem;">
                            <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.5rem;"><?php echo CURRENT_LANG === 'ar' ? 'معلومات الحلقة' : 'Episode Information'; ?></h3>
                            
                            <!-- Titles Row -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                                <!-- Title EN -->
                                <div class="admin-form-group">
                                    <label for="pod-title-en"><?php echo __("dash_title"); ?> (EN)</label>
                                    <input type="text" id="pod-title-en" name="title_en" value="<?php echo e($editItem ? $editItem['title_en'] : ''); ?>" class="admin-form-input" placeholder="e.g. Accessible Travel in Alexandria" required />
                                </div>
                                <!-- Title AR -->
                                <div class="admin-form-group">
                                    <label for="pod-title-ar"><?php echo __("dash_title"); ?> (AR)</label>
                                    <input type="text" id="pod-title-ar" name="title_ar" value="<?php echo e($editItem ? $editItem['title_ar'] : ''); ?>" class="admin-form-input" placeholder="مثال: السياحة الميسرة في الإسكندرية" style="direction: rtl;" />
                                </div>
                            </div>

                            <!-- Slugs Row -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                                <!-- Slug EN -->
                                <div class="admin-form-group">
                                    <label for="pod-slug-en"><?php echo __("admin_slug"); ?> (EN)</label>
                                    <input type="text" id="pod-slug-en" name="slug_en" value="<?php echo e($editItem ? $editItem['slug_en'] : ''); ?>" class="admin-form-input" placeholder="slug-en" />
                                </div>
                                <!-- Slug AR -->
                                <div class="admin-form-group">
                                    <label for="pod-slug-ar"><?php echo __("admin_slug"); ?> (AR)</label>
                                    <input type="text" id="pod-slug-ar" name="slug_ar" value="<?php echo e($editItem ? $editItem['slug_ar'] : ''); ?>" class="admin-form-input" placeholder="slug-ar" style="direction: rtl;" />
                                </div>
                            </div>

                            <!-- Descriptions Row -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                                <!-- Description EN -->
                                <div class="admin-form-group">
                                    <label for="pod-desc-en"><?php echo __("admin_description"); ?> (EN)</label>
                                    <textarea id="pod-desc-en" name="description_en" rows="4" class="admin-form-textarea" placeholder="Brief summary of the conversation in English..."><?php echo e($editItem ? $editItem['description_en'] : ''); ?></textarea>
                                </div>
                                <!-- Description AR -->
                                <div class="admin-form-group">
                                    <label for="pod-desc-ar"><?php echo __("admin_description"); ?> (AR)</label>
                                    <textarea id="pod-desc-ar" name="description_ar" rows="4" class="admin-form-textarea" placeholder="ملخص قصير للحلقة باللغة العربية..." style="direction: rtl;"><?php echo e($editItem ? $editItem['description_ar'] : ''); ?></textarea>
                                </div>
                            </div>

                            <!-- Category & Duration -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                                <div class="admin-form-group">
                                    <label for="pod-category-en"><?php echo __("pod_field_cat_en"); ?></label>
                                    <input type="text" id="pod-category-en" name="category_en" value="<?php echo e($editItem ? $editItem['category_en'] : ''); ?>" class="admin-form-input" placeholder="e.g. Solo Travel" />
                                </div>
                                <div class="admin-form-group">
                                    <label for="pod-category-ar"><?php echo __("pod_field_cat_ar"); ?></label>
                                    <input type="text" id="pod-category-ar" name="category_ar" value="<?php echo e($editItem ? $editItem['category_ar'] : ''); ?>" class="admin-form-input" placeholder="مثال: السفر المنفرد" style="direction: rtl;" />
                                </div>
                                <div class="admin-form-group">
                                    <label for="pod-duration"><?php echo __("pod_field_duration"); ?></label>
                                    <input type="text" id="pod-duration" name="duration" value="<?php echo e($editItem ? $editItem['duration'] : ''); ?>" class="admin-form-input" placeholder="MM:SS" />
                                </div>
                            </div>

                            <!-- Featured -->
                            <div class="admin-form-group" style="flex-direction: row; justify-content: space-between; align-items: center; margin-top: 0.5rem; display: flex; gap: 0.5rem;">
                                <div>
                                    <label style="display:block; margin:0;"><?php echo __("admin_featured"); ?></label>
                                    <span style="font-size: 0.6rem; color:#aaa;"><?php echo CURRENT_LANG === 'ar' ? 'عرض على البانر الرئيسي للبودكاست' : 'Promote to the top showcase carousel'; ?></span>
                                </div>
                                <input type="checkbox" name="is_featured" value="1" <?php echo ($editItem && $editItem['is_featured']) ? 'checked' : ''; ?> style="width: 20px; height: 20px;" />
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Media Dropzones -->
                    <div style="display: flex; flex-direction: column; gap: 1.5rem; min-width: 0;">
                        <!-- Cover Image Dropzone -->
                        <div class="admin-card-box" style="margin: 0; padding: 2rem;">
                            <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 1.5rem;"><?php echo __("dash_cover"); ?></h3>
                            <div class="admin-form-group">
                                <label for="pod-cover"><?php echo __("post_field_cover"); ?></label>
                                <input type="text" id="pod-cover" name="cover_image" value="<?php echo e($editItem ? $editItem['cover_image'] : ''); ?>" class="admin-form-input" placeholder="/uploads/..." />
                            </div>

                            <div class="admin-form-group" style="margin-top: 1rem;">
                                <label for="pod-alt-en"><?php echo __("post_field_alt_en"); ?></label>
                                <input type="text" id="pod-alt-en" name="alt_text_en" value="<?php echo e($editItem ? $editItem['alt_text_en'] : ''); ?>" class="admin-form-input" placeholder="Alt text in English" />
                            </div>
                            <div class="admin-form-group" style="margin-top: 1rem;">
                                <label for="pod-alt-ar"><?php echo __("post_field_alt_ar"); ?></label>
                                <input type="text" id="pod-alt-ar" name="alt_text_ar" value="<?php echo e($editItem ? $editItem['alt_text_ar'] : ''); ?>" class="admin-form-input" placeholder="النص البديل بالعربية" style="direction: rtl;" />
                            </div>
                            
                            <div id="coverDropzone" class="image-upload-preview-box" style="margin-top: 1.5rem; border: 1px dashed var(--accent-color); padding: 2rem; border-radius: 8px; text-align: center; cursor: pointer;">
                                <p style="font-size: 0.75rem; color: #777;"><?php echo __("settings_click_drop"); ?></p>
                                <span style="font-size: 0.6rem; color:#999; display:block; margin-top:0.25rem;">PNG, JPG, WebP (Max 2MB)</span>
                                <input type="file" id="coverFileSelector" accept="image/*" style="display:none;" />
                                <div id="coverStatusText" style="font-size:0.7rem; color: var(--primary-color); margin-top:0.5rem; display:none;">Uploading...</div>
                                <img id="coverPreview" src="<?php echo $editItem && $editItem['cover_image'] ? BASE_URL . $editItem['cover_image'] : ''; ?>" style="display: <?php echo $editItem && $editItem['cover_image'] ? 'inline-block' : 'none'; ?>; max-width: 100px; height: 60px; object-fit: cover; margin-top: 1rem; border-radius: 4px;" />
                            </div>
                        </div>

                        <!-- Audio File Dropzone -->
                        <div class="admin-card-box" style="margin: 0; padding: 2rem;">
                            <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 1.5rem;"><?php echo __("pod_field_audio"); ?></h3>
                            <div class="admin-form-group">
                                <label for="pod-audio"><?php echo __("pod_field_audio"); ?> URL</label>
                                <input type="text" id="pod-audio" name="audio_file" value="<?php echo e($editItem ? $editItem['audio_file'] : ''); ?>" class="admin-form-input" placeholder="/uploads/..." required />
                            </div>
                            
                            <div id="audioDropzone" class="image-upload-preview-box" style="margin-top: 1rem; border: 1px dashed var(--accent-color); padding: 2rem; border-radius: 8px; text-align: center; cursor: pointer;">
                                <p style="font-size: 0.75rem; color: #777;"><?php echo __("pod_drag_audio"); ?></p>
                                <span style="font-size: 0.6rem; color:#999; display:block; margin-top:0.25rem;">MP3, WAV, AAC, OGG (Max 50MB)</span>
                                <input type="file" id="audioFileSelector" accept="audio/*" style="display:none;" />
                                <div id="audioStatusText" style="font-size:0.7rem; color: var(--primary-color); margin-top:0.5rem; display:none;">Uploading...</div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Submission Actions -->
                <div style="border-top: 1px solid #e5e7eb; padding-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
                    <a href="<?php echo BASE_URL; ?>/admin/podcasts" class="btn-reset" style="padding: 1rem 2rem; border-radius: 8px; width:auto; text-decoration:none; display: inline-flex; align-items: center; justify-content: center;"><?php echo __("admin_cancel"); ?></a>
                    <button type="submit" class="btn-primary" style="padding: 1rem 3rem; border-radius: 8px;"><?php echo __("pod_btn_save"); ?></button>
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
