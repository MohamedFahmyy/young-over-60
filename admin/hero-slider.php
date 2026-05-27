<?php
// admin/hero-slider.php
// Administrator Homepage Hero Slides CRUD & Drag-and-drop Reordering Panel

$sliderMgr = new SliderManager();
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
        
        $title = trim($_POST['title'] ?? '');
        $subtitle = trim($_POST['subtitle'] ?? '');
        $buttonText = trim($_POST['button_text'] ?? '');
        $buttonLink = trim($_POST['button_link'] ?? '');
        $image = trim($_POST['image'] ?? '');
        $overlayOpacity = isset($_POST['overlay_opacity']) ? floatval($_POST['overlay_opacity']) : 0.50;
        $sortOrder = isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0;
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($title) || empty($image)) {
            $error = "Title and Slide Background Image are required fields.";
        } else {
            $data = [
                'title' => $title,
                'subtitle' => $subtitle,
                'button_text' => $buttonText,
                'button_link' => $buttonLink,
                'image' => $image,
                'overlay_opacity' => $overlayOpacity,
                'sort_order' => $sortOrder,
                'is_active' => $isActive
            ];

            if ($action === 'add') {
                if ($sliderMgr->createSlide($data)) {
                    $_SESSION['admin_flash_success'] = "Hero slide added successfully!";
                    header("Location: " . BASE_URL . "/admin/hero-slider");
                    exit();
                } else {
                    $error = "Failed to add slide to database.";
                }
            } elseif ($action === 'edit' && !empty($editId)) {
                if ($sliderMgr->updateSlide($editId, $data)) {
                    $_SESSION['admin_flash_success'] = "Hero slide updated successfully!";
                    header("Location: " . BASE_URL . "/admin/hero-slider");
                    exit();
                } else {
                    $error = "Failed to update slide in database.";
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
            $_SESSION['admin_flash_success'] = "Hero slide deleted.";
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
                    <h1 class="admin-title">Hero Slider</h1>
                    <p style="color: #888; font-size: 0.9rem; margin-top: 0.25rem;">Arrange, edit, and publish dynamic background slides for the homepage hero carousel.</p>
                </div>
                <a href="<?php echo BASE_URL; ?>/admin/hero-slider?action=add" class="btn-primary" style="padding: 0.75rem 1.5rem; border-radius: 8px; font-size: 0.75rem; text-decoration: none;">
                    Add Slide
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

            <!-- Drag Reorder Hint Info -->
            <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 1rem 1.5rem; border-radius: 8px; font-size: 0.8rem; margin-bottom: 2rem; display: flex; gap: 0.5rem; align-items: center;">
                💡 <strong>Tip:</strong> Drag and drop slides vertically to rearrange their presentation order on the homepage slider.
            </div>

            <section class="admin-card-box">
                <h2 class="admin-card-title">Active slides</h2>
                <div class="admin-table-wrapper">
                    <table class="admin-table reorderable-table">
                        <thead>
                            <tr>
                                <th style="width: 40px;"></th>
                                <th style="width: 120px;">Image</th>
                                <th>Title</th>
                                <th>Subtitle</th>
                                <th>Button Text</th>
                                <th>Overlay</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="sortableSlidesList">
                            <?php if (!empty($slides)): ?>
                                <?php foreach ($slides as $slide): 
                                    $editUrl = BASE_URL . '/admin/hero-slider?action=edit&id=' . $slide['id'];
                                    $deleteUrl = BASE_URL . '/admin/hero-slider?action=delete&id=' . $slide['id'] . '&csrf_token=' . csrfToken();
                                    $image = !empty($slide['image']) ? $slide['image'] : '/images/hero-bg.png';
                                    ?>
                                    <tr draggable="true" data-id="<?php echo $slide['id']; ?>" class="sortable-row">
                                        <td class="drag-handle" style="cursor: grab; color: #ccc; font-size: 1.2rem; text-align: center;">☰</td>
                                        <td>
                                            <img src="<?php echo e($image); ?>" alt="" style="width: 100px; height: 56px; object-fit: cover; border-radius: 4px;" />
                                        </td>
                                        <td>
                                            <strong style="font-weight: 600;"><?php echo e($slide['title']); ?></strong>
                                        </td>
                                        <td><span style="font-size: 0.8rem; color: #666;"><?php echo e($slide['subtitle'] ?: '-'); ?></span></td>
                                        <td><code><?php echo $slide['button_text'] ? e($slide['button_text']) : '-'; ?></code></td>
                                        <td><?php echo (int)($slide['overlay_opacity'] * 100); ?>%</td>
                                        <td>
                                            <span class="status-badge <?php echo $slide['is_active'] ? 'published' : 'draft'; ?>">
                                                <?php echo $slide['is_active'] ? 'ACTIVE' : 'INACTIVE'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-actions">
                                                <a href="<?php echo $editUrl; ?>" class="btn-sm-action">Edit</a>
                                                <a href="<?php echo $deleteUrl; ?>" onclick="return confirm('Are you sure you want to delete this slide?');" class="btn-sm-action delete">Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; color: #999; padding: 3rem 0; font-style: italic;">
                                        No slides created. Create your first slide above!
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
                    <a href="<?php echo BASE_URL; ?>/admin/hero-slider" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.1em; color:#999;">&larr; Back to Hero Slides</a>
                    <h1 class="admin-title" style="margin-top:0.5rem;"><?php echo $action === 'add' ? 'Add Slide' : 'Edit Slide'; ?></h1>
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

                <div style="display: grid; grid-template-columns: 1.8fr 1fr; gap: 2rem; align-items: start;">
                    
                    <!-- Left Column: Details -->
                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                        <div class="admin-card-box" style="margin: 0; padding: 2rem; display: flex; flex-direction: column; gap: 1.25rem;">
                            <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.5rem;">Slide Content</h3>
                            
                            <!-- Title -->
                            <div class="admin-form-group">
                                <label for="slide-title">Hero Title</label>
                                <input type="text" id="slide-title" name="title" value="<?php echo e($editItem ? $editItem['title'] : ''); ?>" class="admin-form-input" placeholder="e.g. Travel Without Limits" required style="font-size: 1.35rem; font-family: var(--font-serif);" />
                            </div>

                            <!-- Subtitle -->
                            <div class="admin-form-group">
                                <label for="slide-subtitle">Subtitle Teaser</label>
                                <input type="text" id="slide-subtitle" name="subtitle" value="<?php echo e($editItem ? $editItem['subtitle'] : ''); ?>" class="admin-form-input" placeholder="e.g. Accessible travel guides, stories, and reviews" />
                            </div>

                            <!-- Call to Action Link -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="admin-form-group">
                                    <label for="slide-btntext">Button Callout Text</label>
                                    <input type="text" id="slide-btntext" name="button_text" value="<?php echo e($editItem ? $editItem['button_text'] : ''); ?>" class="admin-form-input" placeholder="e.g. Discover More" />
                                </div>
                                <div class="admin-form-group">
                                    <label for="slide-btnlink">Button Target URL</label>
                                    <input type="text" id="slide-btnlink" name="button_link" value="<?php echo e($editItem ? $editItem['button_link'] : ''); ?>" class="admin-form-input" placeholder="e.g. /destinations" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Settings & Background image -->
                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                        
                        <!-- Settings Panel -->
                        <div class="admin-card-box" style="margin: 0; padding: 2rem; display: flex; flex-direction: column; gap: 1.25rem;">
                            <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.5rem;">Slide Config</h3>
                            
                            <!-- Overlay Opacity -->
                            <div class="admin-form-group">
                                <label for="slide-opacity">Dark Overlay Opacity (0.00 to 1.00)</label>
                                <input type="number" id="slide-opacity" name="overlay_opacity" step="0.05" min="0" max="1" value="<?php echo e($editItem ? $editItem['overlay_opacity'] : '0.50'); ?>" class="admin-form-input" />
                            </div>

                            <!-- Sort Order -->
                            <div class="admin-form-group">
                                <label for="slide-sort">Sort Order Index</label>
                                <input type="number" id="slide-sort" name="sort_order" min="0" value="<?php echo e($editItem ? $editItem['sort_order'] : '0'); ?>" class="admin-form-input" />
                            </div>

                            <!-- Active Toggle -->
                            <div class="admin-form-group" style="flex-direction: row; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
                                <div>
                                    <label style="display:block; margin:0;">Active Slide</label>
                                    <span style="font-size: 0.6rem; color:#aaa;">Check to enable in rotating carousel</span>
                                </div>
                                <input type="checkbox" name="is_active" value="1" <?php echo ($action === 'add' || ($editItem && $editItem['is_active'])) ? 'checked' : ''; ?> style="width: 20px; height: 20px;" />
                            </div>
                        </div>

                        <!-- Slide Background Image Dropzone -->
                        <div class="admin-card-box" style="margin: 0; padding: 2rem;">
                            <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 1.5rem;">Background image</h3>
                            <div class="admin-form-group">
                                <label for="slide-img">Background Image URL</label>
                                <input type="text" id="slide-img" name="image" value="<?php echo e($editItem ? $editItem['image'] : ''); ?>" class="admin-form-input" placeholder="/uploads/..." required />
                            </div>
                            
                            <div id="coverDropzone" class="image-upload-preview-box" style="margin-top: 1rem; border: 1px dashed var(--accent-color); padding: 2rem; border-radius: 8px; text-align: center; cursor: pointer;">
                                <p style="font-size: 0.75rem; color: #777;">Click or drop background here</p>
                                <span style="font-size: 0.6rem; color:#999; display:block; margin-top:0.25rem;">PNG, JPG, WebP (Max 2MB)</span>
                                <input type="file" id="coverFileSelector" accept="image/*" style="display:none;" />
                                <div id="coverStatusText" style="font-size:0.7rem; color: var(--primary-color); margin-top:0.5rem; display:none;">Uploading...</div>
                                <img id="coverPreview" src="<?php echo $editItem && $editItem['image'] ? BASE_URL . $editItem['image'] : ''; ?>" style="display: <?php echo $editItem && $editItem['image'] ? 'inline-block' : 'none'; ?>; max-width: 100px; height: 60px; object-fit: cover; margin-top: 1rem; border-radius: 4px;" />
                            </div>
                        </div>

                    </div>

                </div>

                <!-- Submission Actions -->
                <div style="border-top: 1px solid #e5e7eb; padding-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
                    <a href="<?php echo BASE_URL; ?>/admin/hero-slider" class="btn-reset" style="padding: 1rem 2rem; border-radius: 8px; width:auto; text-decoration:none;">Cancel</a>
                    <button type="submit" class="btn-primary" style="padding: 1rem 3rem; border-radius: 8px;">Save Hero Slide</button>
                </div>
            </form>

            <script>
            document.addEventListener("DOMContentLoaded", function() {
                const coverDropzone = document.getElementById('coverDropzone');
                const coverSelector = document.getElementById('coverFileSelector');
                const coverInput = document.getElementById('slide-img');
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
                    coverStatus.textContent = 'Uploading slide background...';

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
