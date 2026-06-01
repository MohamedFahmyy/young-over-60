<?php
// admin/hero-slider.php
// Administrator Homepage Hero Slides CRUD & Drag-and-drop Reordering Panel

$sliderMgr = new SliderManager();
$postMgr = new PostManager();
$settings = $postMgr->getSiteSettings();
$error = null;
$success = null;

$action = $_GET['action'] ?? 'list';
$editId = $_GET['id'] ?? '';
$editItem = null;

// Handle AJAX Reorder request
if ($action === 'reorder' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Content-Type: application/json; charset=UTF-8");
    try {
        Auth::requireCsrf();
        
        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true);
        $orders = $payload['orders'] ?? [];
        
        if (empty($orders)) {
            echo json_encode(['success' => false, 'error' => 'No order mapping provided.']);
            exit();
        }

        if ($sliderMgr->updateOrder($orders)) {
            echo json_encode(['success' => true, 'message' => 'Slide orders saved successfully.']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Database transaction failed.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit();
}

// Handle standard Form CRUD submits
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action !== 'reorder') {
    try {
        Auth::requireCsrf();
        
        $formType = $_POST['form_type'] ?? '';
        if ($formType === 'hero_settings') {
            $currentSettings = $postMgr->getSiteSettings();
            $settingsData = array_merge($currentSettings, [
                'hero_mode' => $_POST['hero_mode'] ?? 'slider',
                'hero_video_en' => trim($_POST['hero_video_en'] ?? ''),
                'hero_video_ar' => trim($_POST['hero_video_ar'] ?? ''),
                'hero_video_title_en' => trim($_POST['hero_video_title_en'] ?? ''),
                'hero_video_title_ar' => trim($_POST['hero_video_title_ar'] ?? ''),
                'hero_video_subtitle_en' => trim($_POST['hero_video_subtitle_en'] ?? ''),
                'hero_video_subtitle_ar' => trim($_POST['hero_video_subtitle_ar'] ?? ''),
                'hero_video_btn_text_en' => trim($_POST['hero_video_btn_text_en'] ?? ''),
                'hero_video_btn_text_ar' => trim($_POST['hero_video_btn_text_ar'] ?? ''),
                'hero_video_btn_link' => trim($_POST['hero_video_btn_link'] ?? '')
            ]);
            
            if ($postMgr->updateSiteSettings($settingsData)) {
                $_SESSION['admin_flash_success'] = "Hero Section Configuration saved successfully.";
                header("Location: " . BASE_URL . "/admin/hero-slider");
                exit();
            } else {
                $error = "Failed to update Hero configuration settings.";
            }
        } else {
            $title_en = trim($_POST['title_en'] ?? '');
            $title_ar = trim($_POST['title_ar'] ?? '');
            $subtitle_en = trim($_POST['subtitle_en'] ?? '');
            $subtitle_ar = trim($_POST['subtitle_ar'] ?? '');
            $buttonTextEn = trim($_POST['button_text_en'] ?? '');
            $buttonTextAr = trim($_POST['button_text_ar'] ?? '');
            $buttonLink = trim($_POST['button_link'] ?? '');
            $imageEn = trim($_POST['image_en'] ?? '');
            $imageAr = trim($_POST['image_ar'] ?? '');
            $altTextEn = trim($_POST['alt_text_en'] ?? '');
            $altTextAr = trim($_POST['alt_text_ar'] ?? '');
            $overlayOpacity = isset($_POST['overlay_opacity']) ? floatval($_POST['overlay_opacity']) : 0.50;
            $sortOrder = isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0;
            $isActive = isset($_POST['is_active']) ? 1 : 0;

            if (empty($title_en) || empty($imageEn)) {
                $error = "Title (EN) and English Background Image / Video are required fields.";
            } else {
            $data = [
                'title_en' => $title_en,
                'title_ar' => !empty($title_ar) ? $title_ar : null,
                'subtitle_en' => !empty($subtitle_en) ? $subtitle_en : null,
                'subtitle_ar' => !empty($subtitle_ar) ? $subtitle_ar : null,
                'button_text_en' => !empty($buttonTextEn) ? $buttonTextEn : null,
                'button_text_ar' => !empty($buttonTextAr) ? $buttonTextAr : null,
                'button_link' => $buttonLink,
                'image_en' => $imageEn,
                'image_ar' => !empty($imageAr) ? $imageAr : $imageEn, // Fallback to EN image if empty
                'alt_text_en' => !empty($altTextEn) ? $altTextEn : null,
                'alt_text_ar' => !empty($altTextAr) ? $altTextAr : null,
                'overlay_opacity' => $overlayOpacity,
                'sort_order' => $sortOrder,
                'is_active' => $isActive
            ];

            if ($action === 'add') {
                if ($sliderMgr->createSlide($data)) {
                    $_SESSION['admin_flash_success'] = __("slider_added_success");
                    header("Location: " . BASE_URL . "/admin/hero-slider");
                    exit();
                } else {
                    $error = "Failed to add slide to database.";
                }
            } elseif ($action === 'edit' && !empty($editId)) {
                if ($sliderMgr->updateSlide($editId, $data)) {
                    $_SESSION['admin_flash_success'] = __("slider_updated_success");
                    header("Location: " . BASE_URL . "/admin/hero-slider");
                    exit();
                } else {
                    $error = "Failed to update slide in database.";
                }
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
        if ($sliderMgr->deleteSlide($editId)) {
            $_SESSION['admin_flash_success'] = __("slider_deleted_success");
        } else {
            $_SESSION['admin_flash_error'] = "Failed to delete hero slide.";
        }
    } else {
        $_SESSION['admin_flash_error'] = "Security check failed.";
    }
    header("Location: " . BASE_URL . "/admin/hero-slider");
    exit();
}

// Prepare item for edit
if ($action === 'edit' && !empty($editId)) {
    $editItem = $sliderMgr->getSlideById($editId);
    if (!$editItem) {
        $_SESSION['admin_flash_error'] = "Slide not found.";
        header("Location: " . BASE_URL . "/admin/hero-slider");
        exit();
    }
}

// Fetch slides
$slides = $sliderMgr->getSlides(true); // true includes inactive slides

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
                    <h1 class="admin-title"><?php echo __("admin_hero_slider"); ?></h1>
                    <p style="color: #888; font-size: 0.9rem; margin-top: 0.25rem;"><?php echo __("slider_manage_desc"); ?></p>
                </div>
                <a href="<?php echo BASE_URL; ?>/admin/hero-slider?action=add" class="btn-primary" style="padding: 0.75rem 1.5rem; border-radius: 8px; font-size: 0.75rem; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">
                    <?php echo __("slider_add_title"); ?>
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

            <!-- Hero Layout & Video Settings Card -->
            <section class="admin-card-box" style="margin-bottom: 2rem; padding: 2rem;">
                <h2 class="admin-card-title"><?php echo CURRENT_LANG === 'ar' ? 'تهيئة القسم الرئيسي (Hero Section)' : 'Hero Section Configuration'; ?></h2>
                <p style="color: #666; font-size: 0.8rem; margin-bottom: 1.5rem;">
                    <?php echo CURRENT_LANG === 'ar' ? 'اختر نمط عرض القسم الرئيسي وقم بتهيئة الفيديو الفردي إذا لزم الأمر.' : 'Choose between displaying a rotating mixed-media slider or a single background video.'; ?>
                </p>

                <!-- Guidelines Alert -->
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid var(--accent-color); padding: 1rem 1.25rem; border-radius: 6px; font-size: 0.75rem; margin-bottom: 1.5rem;">
                    <strong>📐 <?php echo CURRENT_LANG === 'ar' ? 'إرشادات الأبعاد الموصى بها:' : 'Recommended Media Guidelines:'; ?></strong>
                    <ul style="margin: 0.5rem 0 0 1.2rem; padding: 0; list-style-type: disc; display: flex; flex-direction: column; gap: 0.25rem;">
                        <li><strong><?php echo CURRENT_LANG === 'ar' ? 'الصور:' : 'Images:'; ?></strong> Aspect Ratio 16:9 or 21:9 (e.g. 1920x1080 / 2560x1440), WebP/JPG format, Max 500KB.</li>
                        <li><strong><?php echo CURRENT_LANG === 'ar' ? 'الفيديوهات:' : 'Videos:'; ?></strong> Aspect Ratio 16:9 (e.g. 1920x1080), MP4 format (H.264 codec), Max 10MB (ideally 2-5MB), loop of 10-20s. <em>Must be muted</em>.</li>
                    </ul>
                </div>

                <form id="heroConfigForm" action="<?php echo BASE_URL; ?>/admin/hero-slider" method="POST">
                    <?php echo Auth::csrfInput(); ?>
                    <input type="hidden" name="form_type" value="hero_settings" />

                    <!-- Layout Mode Selector -->
                    <div class="admin-form-group" style="margin-bottom: 1.5rem;">
                        <label for="hero-mode" style="font-weight: 600; font-size: 0.85rem;"><?php echo CURRENT_LANG === 'ar' ? 'نمط عرض القسم الرئيسي' : 'Hero Section Mode'; ?></label>
                        <select id="hero-mode" name="hero_mode" class="admin-form-input" style="font-size: 0.9rem;" onchange="toggleHeroModeFields()">
                            <option value="slider" <?php echo ($settings['hero_mode'] === 'slider') ? 'selected' : ''; ?>><?php echo CURRENT_LANG === 'ar' ? 'شريط متحرك (Slider Mode)' : 'Carousel Slider Mode'; ?></option>
                            <option value="video" <?php echo ($settings['hero_mode'] === 'video') ? 'selected' : ''; ?>><?php echo CURRENT_LANG === 'ar' ? 'فيديو خلفية واحد (Single Video Mode)' : 'Single Video Mode'; ?></option>
                        </select>
                    </div>

                    <!-- Single Video Fields Group (shown only when video mode is active) -->
                    <div id="video-fields-group" style="display: <?php echo ($settings['hero_mode'] === 'video') ? 'block' : 'none'; ?>; border-top: 1px solid #e2e8f0; padding-top: 1.5rem; margin-top: 1.5rem;">
                        <h3 style="font-size: 0.9rem; font-weight: 700; margin-bottom: 1.25rem; color: var(--primary-color);">
                            <?php echo CURRENT_LANG === 'ar' ? 'تهيئة فيديو الخلفية الفردي' : 'Single Video Configuration'; ?>
                        </h3>

                        <!-- Video Upload Grid (EN/AR) -->
                        <div class="admin-grid-fields-2" style="margin-bottom: 1.5rem;">
                            <!-- English Video URL & Dropzone -->
                            <div class="admin-form-group">
                                <label for="hero-video-en"><?php echo CURRENT_LANG === 'ar' ? 'رابط الفيديو (EN)' : 'Video File URL (EN)'; ?></label>
                                <input type="text" id="hero-video-en" name="hero_video_en" value="<?php echo e($settings['hero_video_en'] ?? ''); ?>" class="admin-form-input" placeholder="/uploads/..." />
                                
                                <div id="videoDropzoneEn" class="image-upload-preview-box" style="margin-top: 0.75rem; border: 1px dashed var(--accent-color); padding: 1.5rem; border-radius: 8px; text-align: center; cursor: pointer;">
                                    <p style="font-size: 0.7rem; color: #777; margin: 0;"><?php echo __("settings_click_drop"); ?> (MP4/WebM)</p>
                                    <span style="font-size: 0.6rem; color:#999; display:block; margin-top:0.25rem;">Max 50MB</span>
                                    <input type="file" id="videoSelectorEn" accept="video/*" style="display:none;" />
                                    <div id="videoStatusEn" style="font-size:0.65rem; color: var(--primary-color); margin-top:0.5rem; display:none;">Uploading...</div>
                                    <video id="videoPreviewEn" src="<?php echo $settings['hero_video_en'] ? BASE_URL . $settings['hero_video_en'] : ''; ?>" style="display: <?php echo $settings['hero_video_en'] ? 'inline-block' : 'none'; ?>; max-width: 150px; height: 80px; object-fit: cover; margin-top: 0.75rem; border-radius: 4px;" autoplay muted loop></video>
                                </div>
                            </div>

                            <!-- Arabic Video URL & Dropzone -->
                            <div class="admin-form-group">
                                <label for="hero-video-ar"><?php echo CURRENT_LANG === 'ar' ? 'رابط الفيديو (AR)' : 'Video File URL (AR)'; ?></label>
                                <input type="text" id="hero-video-ar" name="hero_video_ar" value="<?php echo e($settings['hero_video_ar'] ?? ''); ?>" class="admin-form-input" placeholder="/uploads/..." />
                                
                                <div id="videoDropzoneAr" class="image-upload-preview-box" style="margin-top: 0.75rem; border: 1px dashed var(--accent-color); padding: 1.5rem; border-radius: 8px; text-align: center; cursor: pointer;">
                                    <p style="font-size: 0.7rem; color: #777; margin: 0;"><?php echo __("settings_click_drop"); ?> (MP4/WebM)</p>
                                    <span style="font-size: 0.6rem; color:#999; display:block; margin-top:0.25rem;">Max 50MB</span>
                                    <input type="file" id="videoSelectorAr" accept="video/*" style="display:none;" />
                                    <div id="videoStatusAr" style="font-size:0.65rem; color: var(--primary-color); margin-top:0.5rem; display:none;">Uploading...</div>
                                    <video id="videoPreviewAr" src="<?php echo $settings['hero_video_ar'] ? BASE_URL . $settings['hero_video_ar'] : ''; ?>" style="display: <?php echo $settings['hero_video_ar'] ? 'inline-block' : 'none'; ?>; max-width: 150px; height: 80px; object-fit: cover; margin-top: 0.75rem; border-radius: 4px;" autoplay muted loop></video>
                                </div>
                            </div>
                        </div>

                        <!-- Titles Row -->
                        <div class="admin-grid-fields-2" style="margin-bottom: 1.5rem;">
                            <div class="admin-form-group">
                                <label for="hero-video-title-en"><?php echo CURRENT_LANG === 'ar' ? 'العنوان الرئيسي (EN)' : 'Hero Title (EN)'; ?></label>
                                <input type="text" id="hero-video-title-en" name="hero_video_title_en" value="<?php echo e($settings['hero_video_title_en'] ?? ''); ?>" class="admin-form-input" placeholder="e.g. Travel Without Limits" />
                            </div>
                            <div class="admin-form-group">
                                <label for="hero-video-title-ar"><?php echo CURRENT_LANG === 'ar' ? 'العنوان الرئيسي (AR)' : 'Hero Title (AR)'; ?></label>
                                <input type="text" id="hero-video-title-ar" name="hero_video_title_ar" value="<?php echo e($settings['hero_video_title_ar'] ?? ''); ?>" class="admin-form-input" placeholder="مثال: سفر بلا حدود" style="direction: rtl;" />
                            </div>
                        </div>

                        <!-- Subtitles Row -->
                        <div class="admin-grid-fields-2" style="margin-bottom: 1.5rem;">
                            <div class="admin-form-group">
                                <label for="hero-video-subtitle-en"><?php echo CURRENT_LANG === 'ar' ? 'العنوان الفرعي (EN)' : 'Hero Subtitle (EN)'; ?></label>
                                <input type="text" id="hero-video-subtitle-en" name="hero_video_subtitle_en" value="<?php echo e($settings['hero_video_subtitle_en'] ?? ''); ?>" class="admin-form-input" placeholder="e.g. Accessible travel guides..." />
                            </div>
                            <div class="admin-form-group">
                                <label for="hero-video-subtitle-ar"><?php echo CURRENT_LANG === 'ar' ? 'العنوان الفرعي (AR)' : 'Hero Subtitle (AR)'; ?></label>
                                <input type="text" id="hero-video-subtitle-ar" name="hero_video_subtitle_ar" value="<?php echo e($settings['hero_video_subtitle_ar'] ?? ''); ?>" class="admin-form-input" placeholder="مثال: أدلة سفر ميسرة..." style="direction: rtl;" />
                            </div>
                        </div>

                        <!-- Button Text & Link Row -->
                        <div class="admin-grid-fields-3" style="margin-bottom: 1.5rem;">
                            <div class="admin-form-group">
                                <label for="hero-video-btn-en"><?php echo CURRENT_LANG === 'ar' ? 'نص الزر (EN)' : 'Button Text (EN)'; ?></label>
                                <input type="text" id="hero-video-btn-en" name="hero_video_btn_text_en" value="<?php echo e($settings['hero_video_btn_text_en'] ?? ''); ?>" class="admin-form-input" placeholder="e.g. Discover More" />
                            </div>
                            <div class="admin-form-group">
                                <label for="hero-video-btn-ar"><?php echo CURRENT_LANG === 'ar' ? 'نص الزر (AR)' : 'Button Text (AR)'; ?></label>
                                <input type="text" id="hero-video-btn-ar" name="hero_video_btn_text_ar" value="<?php echo e($settings['hero_video_btn_text_ar'] ?? ''); ?>" class="admin-form-input" placeholder="مثال: اكتشف المزيد" style="direction: rtl;" />
                            </div>
                            <div class="admin-form-group">
                                <label for="hero-video-link"><?php echo CURRENT_LANG === 'ar' ? 'رابط الزر' : 'Button Link'; ?></label>
                                <input type="text" id="hero-video-link" name="hero_video_btn_link" value="<?php echo e($settings['hero_video_btn_link'] ?? ''); ?>" class="admin-form-input" placeholder="e.g. /destinations" />
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1.5rem;">
                        <button type="submit" class="btn-primary" style="padding: 0.75rem 2rem; border-radius: 8px;">
                            <?php echo CURRENT_LANG === 'ar' ? 'حفظ إعدادات البانر' : 'Save Hero Settings'; ?>
                        </button>
                    </div>
                </form>
            </section>

            <script>
            function toggleHeroModeFields() {
                const mode = document.getElementById('hero-mode').value;
                const group = document.getElementById('video-fields-group');
                if (mode === 'video') {
                    group.style.display = 'block';
                } else {
                    group.style.display = 'none';
                }
            }

            document.addEventListener("DOMContentLoaded", function() {
                setupVideoDropzone('videoDropzoneEn', 'videoSelectorEn', 'hero-video-en', 'videoStatusEn', 'videoPreviewEn');
                setupVideoDropzone('videoDropzoneAr', 'videoSelectorAr', 'hero-video-ar', 'videoStatusAr', 'videoPreviewAr');

                function setupVideoDropzone(zoneId, selectId, inputId, statusId, previewId) {
                    const zone = document.getElementById(zoneId);
                    const selector = document.getElementById(selectId);
                    const input = document.getElementById(inputId);
                    const status = document.getElementById(statusId);
                    const preview = document.getElementById(previewId);

                    if (!zone || !selector) return;

                    zone.addEventListener('click', () => selector.click());
                    selector.addEventListener('change', function() {
                        if (this.files.length > 0) uploadVideoFile(this.files[0], input, status, preview);
                    });

                    zone.addEventListener('dragover', (e) => { e.preventDefault(); zone.style.borderColor = 'var(--primary-color)'; });
                    zone.addEventListener('dragleave', () => { zone.style.borderColor = ''; });
                    zone.addEventListener('drop', (e) => {
                        e.preventDefault();
                        zone.style.borderColor = '';
                        if (e.dataTransfer.files.length > 0) uploadVideoFile(e.dataTransfer.files[0], input, status, preview);
                    });
                }

                function uploadVideoFile(file, inputEl, statusEl, previewEl) {
                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('csrf_token', '<?php echo csrfToken(); ?>');

                    statusEl.style.display = 'block';
                    statusEl.textContent = 'Uploading video...';

                    fetch('<?php echo BASE_URL; ?>/api/upload?type=video', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(result => {
                        if (result.success) {
                            statusEl.textContent = 'Upload complete!';
                            inputEl.value = result.relativeUrl;
                            previewEl.src = result.url;
                            previewEl.style.display = 'inline-block';
                        } else {
                            statusEl.textContent = 'Error: ' + (result.error || 'Upload failed');
                        }
                    })
                    .catch(err => {
                        statusEl.textContent = 'Upload failed. Network error.';
                        console.error("AJAX Video Upload failed", err);
                    });
                }
            });
            </script>

            <!-- Drag Reorder Hint Info -->
            <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 1rem 1.5rem; border-radius: 8px; font-size: 0.8rem; margin-bottom: 2rem; display: flex; gap: 0.5rem; align-items: center;">
                💡 <strong><?php echo CURRENT_LANG === 'ar' ? 'تلميح:' : 'Tip:'; ?></strong> <?php echo CURRENT_LANG === 'ar' ? 'اسحب وأفلت الشرائح رأسيًا لإعادة ترتيب ترتيب عرضها على شريط الصفحة الرئيسية.' : 'Drag and drop slides vertically to rearrange their presentation order on the homepage slider.'; ?>
            </div>

            <section class="admin-card-box">
                <h2 class="admin-card-title"><?php echo CURRENT_LANG === 'ar' ? 'الشرائح النشطة' : 'Active Slides'; ?></h2>
                <div class="admin-table-wrapper">
                    <table class="admin-table reorderable-table">
                        <thead>
                            <tr>
                                <th style="width: 40px;"></th>
                                <th style="width: 120px;"><?php echo __("dash_cover"); ?></th>
                                <th><?php echo __("dash_title"); ?></th>
                                <th><?php echo CURRENT_LANG === 'ar' ? 'العنوان الفرعي' : 'Subtitle'; ?></th>
                                <th><?php echo CURRENT_LANG === 'ar' ? 'نص الزر' : 'Button Text'; ?></th>
                                <th><?php echo CURRENT_LANG === 'ar' ? 'الشفافية' : 'Overlay'; ?></th>
                                <th><?php echo __("admin_status"); ?></th>
                                <th><?php echo __("admin_actions"); ?></th>
                            </tr>
                        </thead>
                        <tbody id="sortableSlidesList">
                            <?php if (!empty($slides)): ?>
                                <?php foreach ($slides as $slide): 
                                    $editUrl = BASE_URL . '/admin/hero-slider?action=edit&id=' . $slide['id'];
                                    $deleteUrl = BASE_URL . '/admin/hero-slider?action=delete&id=' . $slide['id'] . '&csrf_token=' . csrfToken();
                                    $image = !empty($slide['image_en']) ? $slide['image_en'] : (!empty($slide['image_ar']) ? $slide['image_ar'] : '/images/hero-bg.png');
                                    ?>
                                    <tr draggable="true" data-id="<?php echo $slide['id']; ?>" class="sortable-row">
                                        <td class="drag-handle" style="cursor: grab; color: #ccc; font-size: 1.2rem; text-align: center;">☰</td>
                                        <td>
                                            <img src="<?php echo e($image); ?>" alt="" style="width: 100px; height: 56px; object-fit: cover; border-radius: 4px;" />
                                        </td>
                                        <td>
                                            <strong style="font-weight: 600;">
                                                <?php echo e(CURRENT_LANG === 'ar' && !empty($slide['title_ar']) ? $slide['title_ar'] : $slide['title_en']); ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <span style="font-size: 0.8rem; color: #666;">
                                                <?php echo e(CURRENT_LANG === 'ar' && !empty($slide['subtitle_ar']) ? $slide['subtitle_ar'] : ($slide['subtitle_en'] ?: '-')); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <code>
                                                <?php echo CURRENT_LANG === 'ar' && !empty($slide['button_text_ar']) ? e($slide['button_text_ar']) : ($slide['button_text_en'] ? e($slide['button_text_en']) : '-'); ?>
                                            </code>
                                        </td>
                                        <td><?php echo (int)($slide['overlay_opacity'] * 100); ?>%</td>
                                        <td>
                                            <span class="status-badge <?php echo $slide['is_active'] ? 'published' : 'draft'; ?>">
                                                <?php echo $slide['is_active'] ? (CURRENT_LANG === 'ar' ? 'نشط' : 'ACTIVE') : (CURRENT_LANG === 'ar' ? 'غير نشط' : 'INACTIVE'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-actions">
                                                <a href="<?php echo $editUrl; ?>" class="btn-sm-action"><?php echo __("admin_edit"); ?></a>
                                                <a href="<?php echo $deleteUrl; ?>" onclick="return confirm('<?php echo __("slider_confirm_delete"); ?>');" class="btn-sm-action delete"><?php echo __("admin_delete"); ?></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; color: #999; padding: 3rem 0; font-style: italic;">
                                        <?php echo CURRENT_LANG === 'ar' ? 'لم يتم العثور على شرائح. أضف شريحة جديدة أعلاه!' : 'No slides created. Create your first slide above!'; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <script>
            document.addEventListener("DOMContentLoaded", function() {
                const list = document.getElementById('sortableSlidesList');
                if (!list) return;

                let dragEl = null;

                list.addEventListener('dragstart', function(e) {
                    dragEl = e.target.closest('tr');
                    if (dragEl) {
                        dragEl.classList.add('dragging');
                        e.dataTransfer.effectAllowed = 'move';
                    }
                });

                list.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'move';
                    const target = e.target.closest('tr');
                    if (target && target !== dragEl && target.classList.contains('sortable-row')) {
                        const rect = target.getBoundingClientRect();
                        const next = (e.clientY - rect.top) / (rect.bottom - rect.top) > 0.5;
                        list.insertBefore(dragEl, next ? target.nextSibling : target);
                    }
                });

                list.addEventListener('dragend', function() {
                    if (dragEl) {
                        dragEl.classList.remove('dragging');
                        saveSlideOrder();
                    }
                });

                function saveSlideOrder() {
                    const rows = list.querySelectorAll('.sortable-row');
                    const orders = {};
                    rows.forEach((row, index) => {
                        orders[row.getAttribute('data-id')] = index + 1;
                    });

                    // Send AJAX post to save orders
                    fetch('<?php echo BASE_URL; ?>/admin/hero-slider?action=reorder', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            orders: orders,
                            csrf_token: '<?php echo csrfToken(); ?>'
                        })
                    })
                    .then(res => res.json())
                    .then(result => {
                        if (!result.success) {
                            alert('Failed to save slider order: ' + result.error);
                        }
                    })
                    .catch(err => {
                        console.error('Reorder network error', err);
                    });
                }
            });
            </script>

            <style>
            .sortable-row.dragging {
                opacity: 0.4;
                background-color: #fafafa;
                border: 2px dashed #ccc;
            }
            </style>

        <?php elseif ($action === 'add' || $action === 'edit'): ?>
            <!-- ADD / EDIT FORM VIEW -->
            <div class="admin-header-row" style="border-bottom: 1px solid #e5e7eb; padding-bottom: 1.5rem; margin-bottom: 2rem;">
                <div>
                    <a href="<?php echo BASE_URL; ?>/admin/hero-slider" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.1em; color:#999; display: flex; align-items: center; gap: 0.5rem;">
                        <?php echo CURRENT_LANG === 'ar' ? '&rarr;' : '&larr;'; ?> <?php echo __("admin_back"); ?>
                    </a>
                    <h1 class="admin-title" style="margin-top:0.5rem;"><?php echo $action === 'add' ? __("slider_add_title") : __("slider_edit_title"); ?></h1>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="form-status-alert error" style="margin-bottom: 1.5rem;">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span><?php echo e($error); ?></span>
                </div>
            <?php endif; ?>

            <form id="slideForm" action="<?php echo BASE_URL; ?>/admin/hero-slider?action=<?php echo $action; ?><?php echo !empty($editId) ? '&id='.$editId : ''; ?>" method="POST" style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
                <?php echo Auth::csrfInput(); ?>

                <div class="admin-grid-layout admin-split-18-1">
                    
                    <!-- Left Column: Details -->
                    <div style="display: flex; flex-direction: column; gap: 1.5rem; min-width: 0;">
                        <div class="admin-card-box" style="margin: 0; padding: 2rem; display: flex; flex-direction: column; gap: 1.25rem;">
                            <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.5rem;"><?php echo CURRENT_LANG === 'ar' ? 'محتوى الشريحة' : 'Slide Content'; ?></h3>
                            
                            <!-- Titles Row -->
                            <div class="admin-grid-fields-2">
                                <!-- Title EN -->
                                <div class="admin-form-group">
                                    <label for="slide-title-en"><?php echo __("dash_title"); ?> (EN)</label>
                                    <input type="text" id="slide-title-en" name="title_en" value="<?php echo e($editItem ? $editItem['title_en'] : ''); ?>" class="admin-form-input" placeholder="e.g. Travel Without Limits" required style="font-size: 1.2rem; font-family: var(--font-serif);" />
                                </div>
                                <!-- Title AR -->
                                <div class="admin-form-group">
                                    <label for="slide-title-ar"><?php echo __("dash_title"); ?> (AR)</label>
                                    <input type="text" id="slide-title-ar" name="title_ar" value="<?php echo e($editItem ? $editItem['title_ar'] : ''); ?>" class="admin-form-input" placeholder="مثال: سفر بلا حدود" style="font-size: 1.2rem; font-family: var(--font-serif); direction: rtl;" />
                                </div>
                            </div>

                            <!-- Subtitles Row -->
                            <div class="admin-grid-fields-2">
                                <!-- Subtitle EN -->
                                <div class="admin-form-group">
                                    <label for="slide-subtitle-en"><?php echo __("slider_field_subtitle_en"); ?></label>
                                    <input type="text" id="slide-subtitle-en" name="subtitle_en" value="<?php echo e($editItem ? $editItem['subtitle_en'] : ''); ?>" class="admin-form-input" placeholder="e.g. Accessible travel guides..." />
                                </div>
                                <!-- Subtitle AR -->
                                <div class="admin-form-group">
                                    <label for="slide-subtitle-ar"><?php echo __("slider_field_subtitle_ar"); ?></label>
                                    <input type="text" id="slide-subtitle-ar" name="subtitle_ar" value="<?php echo e($editItem ? $editItem['subtitle_ar'] : ''); ?>" class="admin-form-input" placeholder="مثال: أدلة سفر ميسرة..." style="direction: rtl;" />
                                </div>
                            </div>

                            <!-- Call to Action Link -->
                            <div class="admin-grid-fields-3">
                                <div class="admin-form-group">
                                    <label for="slide-btntext-en"><?php echo __("slider_field_btn_text_en"); ?></label>
                                    <input type="text" id="slide-btntext-en" name="button_text_en" value="<?php echo e($editItem ? $editItem['button_text_en'] : ''); ?>" class="admin-form-input" placeholder="e.g. Discover More" />
                                </div>
                                <div class="admin-form-group">
                                    <label for="slide-btntext-ar"><?php echo __("slider_field_btn_text_ar"); ?></label>
                                    <input type="text" id="slide-btntext-ar" name="button_text_ar" value="<?php echo e($editItem ? $editItem['button_text_ar'] : ''); ?>" class="admin-form-input" placeholder="مثال: اكتشف المزيد" style="direction: rtl;" />
                                </div>
                                <div class="admin-form-group">
                                    <label for="slide-btnlink"><?php echo __("slider_field_btn_link"); ?></label>
                                    <input type="text" id="slide-btnlink" name="button_link" value="<?php echo e($editItem ? $editItem['button_link'] : ''); ?>" class="admin-form-input" placeholder="e.g. /destinations" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Settings & Background image -->
                    <div style="display: flex; flex-direction: column; gap: 1.5rem; min-width: 0;">
                        
                        <!-- Settings Panel -->
                        <div class="admin-card-box" style="margin: 0; padding: 2rem; display: flex; flex-direction: column; gap: 1.25rem;">
                            <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.5rem;"><?php echo CURRENT_LANG === 'ar' ? 'إعدادات الشريحة' : 'Slide Config'; ?></h3>
                            
                            <!-- Overlay Opacity -->
                            <div class="admin-form-group">
                                <label for="slide-opacity"><?php echo __("slider_field_opacity"); ?></label>
                                <input type="number" id="slide-opacity" name="overlay_opacity" step="0.05" min="0" max="1" value="<?php echo e($editItem ? $editItem['overlay_opacity'] : '0.50'); ?>" class="admin-form-input" />
                            </div>

                            <!-- Sort Order -->
                            <div class="admin-form-group">
                                <label for="slide-sort"><?php echo __("admin_order"); ?></label>
                                <input type="number" id="slide-sort" name="sort_order" min="0" value="<?php echo e($editItem ? $editItem['sort_order'] : '0'); ?>" class="admin-form-input" />
                            </div>

                            <!-- Active Toggle -->
                            <div class="admin-form-group" style="flex-direction: row; justify-content: space-between; align-items: center; margin-top: 0.5rem; display: flex; gap: 0.5rem;">
                                <div>
                                    <label style="display:block; margin:0;"><?php echo CURRENT_LANG === 'ar' ? 'تفعيل الشريحة' : 'Active Slide'; ?></label>
                                    <span style="font-size: 0.6rem; color:#aaa;"><?php echo CURRENT_LANG === 'ar' ? 'تفعيل الشريحة في شريط العرض الدوار' : 'Check to enable in rotating carousel'; ?></span>
                                </div>
                                <input type="checkbox" name="is_active" value="1" <?php echo ($action === 'add' || (isset($editItem['is_active']) && $editItem['is_active'])) ? 'checked' : ''; ?> style="width: 20px; height: 20px;" />
                            </div>
                        </div>

                        <!-- Slide Background Image or Video Dropzone -->
                        <div class="admin-card-box" style="margin: 0; padding: 2rem;">
                            <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 1.5rem;"><?php echo CURRENT_LANG === 'ar' ? 'خلفية الشريحة (صورة أو فيديو)' : 'Background Image or Video'; ?></h3>
                            
                            <!-- Image URL EN & AR -->
                            <div class="admin-form-group">
                                <label for="slide-img-en"><?php echo CURRENT_LANG === 'ar' ? 'رابط الملف (EN)' : 'File URL (EN)'; ?></label>
                                <input type="text" id="slide-img-en" name="image_en" value="<?php echo e($editItem ? $editItem['image_en'] : ''); ?>" class="admin-form-input" placeholder="/uploads/..." required />
                            </div>
                            
                            <div class="admin-form-group" style="margin-top: 1rem;">
                                <label for="slide-img-ar"><?php echo CURRENT_LANG === 'ar' ? 'رابط الملف (AR)' : 'File URL (AR)'; ?></label>
                                <input type="text" id="slide-img-ar" name="image_ar" value="<?php echo e($editItem ? $editItem['image_ar'] : ''); ?>" class="admin-form-input" placeholder="/uploads/..." />
                            </div>

                            <!-- Alt text EN & AR -->
                            <div class="admin-form-group" style="margin-top: 1rem;">
                                <label for="slide-alt-en"><?php echo __("post_field_alt_en"); ?></label>
                                <input type="text" id="slide-alt-en" name="alt_text_en" value="<?php echo e($editItem ? $editItem['alt_text_en'] : ''); ?>" class="admin-form-input" placeholder="Alt text in English (for images)" />
                            </div>
                            <div class="admin-form-group" style="margin-top: 1rem;">
                                <label for="slide-alt-ar"><?php echo __("post_field_alt_ar"); ?></label>
                                <input type="text" id="slide-alt-ar" name="alt_text_ar" value="<?php echo e($editItem ? $editItem['alt_text_ar'] : ''); ?>" class="admin-form-input" placeholder="النص البديل بالعربية (للصور)" style="direction: rtl;" />
                            </div>
                            
                            <?php 
                            $isEnVideo = false;
                            if ($editItem && !empty($editItem['image_en'])) {
                                $isEnVideo = preg_match('/\.(mp4|webm|mov|ogg)$/i', $editItem['image_en']);
                            }
                            ?>
                            
                            <div id="coverDropzone" class="image-upload-preview-box" style="margin-top: 1.5rem; border: 1px dashed var(--accent-color); padding: 2rem; border-radius: 8px; text-align: center; cursor: pointer;">
                                <p style="font-size: 0.75rem; color: #777;"><?php echo __("settings_click_drop"); ?></p>
                                <span style="font-size: 0.6rem; color:#999; display:block; margin-top:0.25rem;">PNG, JPG, WebP, MP4, WebM (Max 50MB)</span>
                                <input type="file" id="coverFileSelector" accept="image/*,video/*" style="display:none;" />
                                <div id="coverStatusText" style="font-size:0.7rem; color: var(--primary-color); margin-top:0.5rem; display:none;">Uploading...</div>
                                <img id="coverPreview" src="<?php echo $editItem && !$isEnVideo && $editItem['image_en'] ? BASE_URL . $editItem['image_en'] : ''; ?>" style="display: <?php echo $editItem && !$isEnVideo && $editItem['image_en'] ? 'inline-block' : 'none'; ?>; max-width: 100px; height: 60px; object-fit: cover; margin-top: 1rem; border-radius: 4px;" />
                                <video id="coverVideoPreview" src="<?php echo $editItem && $isEnVideo ? BASE_URL . $editItem['image_en'] : ''; ?>" style="display: <?php echo $editItem && $isEnVideo ? 'inline-block' : 'none'; ?>; max-width: 100px; height: 60px; object-fit: cover; margin-top: 1rem; border-radius: 4px;" autoplay muted loop></video>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- Submission Actions -->
                <div style="border-top: 1px solid #e5e7eb; padding-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
                    <a href="<?php echo BASE_URL; ?>/admin/hero-slider" class="btn-reset" style="padding: 1rem 2rem; border-radius: 8px; width:auto; text-decoration:none; display: inline-flex; align-items: center; justify-content: center;"><?php echo __("admin_cancel"); ?></a>
                    <button type="submit" class="btn-primary" style="padding: 1rem 3rem; border-radius: 8px;"><?php echo __("post_btn_save"); ?></button>
                </div>
            </form>

            <script>
            document.addEventListener("DOMContentLoaded", function() {
                const coverDropzone = document.getElementById('coverDropzone');
                const coverSelector = document.getElementById('coverFileSelector');
                const coverInputEn = document.getElementById('slide-img-en');
                const coverInputAr = document.getElementById('slide-img-ar');
                const coverPreview = document.getElementById('coverPreview');
                const coverVideoPreview = document.getElementById('coverVideoPreview');
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
                    coverStatus.textContent = 'Uploading slide background...';

                    const isVideo = file.type.startsWith('video/');
                    const uploadUrl = '<?php echo BASE_URL; ?>/api/upload' + (isVideo ? '?type=video' : '');

                    fetch(uploadUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(result => {
                        if (result.success) {
                            coverStatus.textContent = 'Upload complete!';
                            // Put in both image fields if they are empty, otherwise first
                            if (coverInputEn.value === '') {
                                coverInputEn.value = result.relativeUrl;
                            } else if (coverInputAr.value === '') {
                                coverInputAr.value = result.relativeUrl;
                            } else {
                                coverInputEn.value = result.relativeUrl;
                            }
                            
                            if (result.relativeUrl.match(/\.(mp4|webm|mov|ogg)$/i)) {
                                coverPreview.style.display = 'none';
                                coverVideoPreview.src = result.url;
                                coverVideoPreview.style.display = 'inline-block';
                            } else {
                                coverVideoPreview.style.display = 'none';
                                coverPreview.src = result.url;
                                coverPreview.style.display = 'inline-block';
                            }
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
