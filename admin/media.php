<?php
// admin/media.php
// Administrator Media Library Browser & File Manager

$db = Database::getInstance()->getConnection();
$error = null;
$success = null;

// Handle File Deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    try {
        Auth::requireCsrf();
        $mediaId = $_GET['id'] ?? '';
        
        // Fetch file details
        $stmt = $db->prepare("SELECT * FROM media WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $mediaId]);
        $media = $stmt->fetch();
        
        if ($media) {
            // Delete actual file in filesystem
            $filePath = PATH_ROOT . $media['url'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
            
            // Delete database row
            $deleteStmt = $db->prepare("DELETE FROM media WHERE id = :id");
            $deleteStmt->execute(['id' => $mediaId]);
            
            $_SESSION['admin_flash_success'] = __("media_success_delete");
        } else {
            $_SESSION['admin_flash_error'] = "Media asset not found in library.";
        }
    } catch (Exception $e) {
        $_SESSION['admin_flash_error'] = "Deletion error: " . $e->getMessage();
    }
    header("Location: " . BASE_URL . "/admin/media");
    exit();
}

// Handle Alt Text Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'update_alt') {
    try {
        Auth::requireCsrf();
        $mediaId = $_GET['id'] ?? '';
        $altEn = trim($_POST['alt_text_en'] ?? '');
        $altAr = trim($_POST['alt_text_ar'] ?? '');
        
        $updateStmt = $db->prepare("UPDATE media SET alt_text_en = :alt_en, alt_text_ar = :alt_ar WHERE id = :id");
        $updateStmt->execute([
            ':alt_en' => $altEn !== '' ? $altEn : null,
            ':alt_ar' => $altAr !== '' ? $altAr : null,
            ':id' => $mediaId
        ]);
        
        $_SESSION['admin_flash_success'] = "Alt texts updated successfully.";
    } catch (Exception $e) {
        $_SESSION['admin_flash_error'] = "Update error: " . $e->getMessage();
    }
    header("Location: " . BASE_URL . "/admin/media");
    exit();
}

// Fetch all media assets
$stmt = $db->query("SELECT * FROM media ORDER BY created_at DESC");
$mediaFiles = $stmt->fetchAll();

// Formatting helper: bytes to human readable
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

// Set SEO Meta
$seoPageType = 'admin';
require_once PATH_ROOT . '/includes/header.php';
?>

<div class="admin-layout">
    <!-- Sidebar -->
    <?php require_once PATH_ROOT . '/includes/admin-sidebar.php'; ?>

    <!-- Main Workspace -->
    <main class="admin-main">
        <div class="admin-header-row">
            <div>
                <h1 class="admin-title"><?php echo __("media_manage_title"); ?></h1>
                <p style="color: #888; font-size: 0.9rem; margin-top: 0.25rem;"><?php echo __("media_manage_desc"); ?></p>
            </div>
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

        <!-- Media Gallery Grid Layout -->
        <section class="admin-card-box">
            <h2 class="admin-card-title"><?php echo CURRENT_LANG === 'ar' ? 'كل الملفات المرفوعة' : 'All Uploads'; ?> (<?php echo count($mediaFiles); ?>)</h2>
            
            <?php if (!empty($mediaFiles)): ?>
                <div class="media-gallery-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
                    <?php foreach ($mediaFiles as $asset): 
                        $isAudio = (strpos($asset['mimeType'], 'audio/') === 0);
                        $deleteUrl = BASE_URL . '/admin/media?action=delete&id=' . $asset['id'] . '&csrf_token=' . csrfToken();
                        ?>
                        <div class="media-asset-card" style="border: 1px solid var(--accent-color); border-radius: 8px; overflow: hidden; background: #ffffff; display: flex; flex-direction: column;">
                            
                            <!-- Thumbnail Preview Box -->
                            <div class="media-asset-preview" style="aspect-ratio: 16/10; width: 100%; background: #f9f9f9; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative; border-bottom: 1px solid var(--accent-color);">
                                <?php if ($isAudio): ?>
                                    <div style="font-size: 3rem;">🎙️</div>
                                    <span style="position: absolute; bottom: 8px; right: 8px; background: rgba(0,0,0,0.7); color: #fff; font-size: 0.55rem; padding: 2px 6px; border-radius: 4px; font-weight: bold; text-transform: uppercase;"><?php echo CURRENT_LANG === 'ar' ? 'ملف صوتي' : 'Audio'; ?></span>
                                <?php else: ?>
                                    <img src="<?php echo e(BASE_URL . $asset['url']); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;" />
                                    <span style="position: absolute; bottom: 8px; right: 8px; background: rgba(0,0,0,0.7); color: #fff; font-size: 0.55rem; padding: 2px 6px; border-radius: 4px; font-weight: bold; text-transform: uppercase;"><?php echo CURRENT_LANG === 'ar' ? 'صورة' : 'Image'; ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- Meta Details Box -->
                            <div class="media-asset-details" style="padding: 1rem; display: flex; flex-direction: column; gap: 0.25rem; flex-grow: 1; justify-content: space-between;">
                                <div>
                                    <h4 style="font-size: 0.75rem; font-weight: 600; word-break: break-all; margin: 0; line-height: 1.3;" title="<?php echo e($asset['filename']); ?>"><?php echo e($asset['filename']); ?></h4>
                                    <div style="font-size: 0.65rem; color: #888; margin-top: 0.5rem; display: flex; flex-direction: column; gap: 0.15rem;">
                                        <span><?php echo CURRENT_LANG === 'ar' ? 'الحجم:' : 'Size:'; ?> <?php echo formatBytes($asset['fileSize']); ?></span>
                                        <?php if (!$isAudio && !empty($asset['width'])): ?>
                                            <span><?php echo CURRENT_LANG === 'ar' ? 'الأبعاد:' : 'Dimensions:'; ?> <?php echo $asset['width']; ?> &times; <?php echo $asset['height']; ?> px</span>
                                        <?php endif; ?>
                                        <span><?php echo CURRENT_LANG === 'ar' ? 'تم الرفع:' : 'Uploaded:'; ?> <?php echo date('Y-m-d H:i', strtotime($asset['created_at'])); ?></span>
                                    </div>
                                    
                                    <!-- Alt Text Editing Form -->
                                    <form method="POST" action="<?php echo BASE_URL; ?>/admin/media?action=update_alt&id=<?php echo $asset['id']; ?>" style="margin-top: 0.75rem; display: flex; flex-direction: column; gap: 0.35rem; border-top: 1px dashed #f0f0f0; padding-top: 0.5rem;">
                                        <?php echo Auth::csrfInput(); ?>
                                        <input type="text" name="alt_text_en" class="admin-form-input" style="font-size: 0.7rem; padding: 4px 8px; height: auto;" placeholder="Alt Text (EN)" value="<?php echo e($asset['alt_text_en'] ?? ''); ?>" />
                                        <input type="text" name="alt_text_ar" class="admin-form-input" style="font-size: 0.7rem; padding: 4px 8px; height: auto;" placeholder="نص بديل (AR)" value="<?php echo e($asset['alt_text_ar'] ?? ''); ?>" dir="rtl" />
                                        <button type="submit" class="btn-primary" style="font-size: 0.65rem; padding: 3px 6px; border-radius: 4px; height: auto; min-height: auto; align-self: flex-end; width: auto; margin-top: 0.25rem;">
                                            <?php echo CURRENT_LANG === 'ar' ? 'حفظ النص' : 'Save Alt'; ?>
                                        </button>
                                    </form>
                                </div>
                                
                                <div style="display: flex; gap: 0.5rem; margin-top: 1rem; border-top: 1px solid #f0f0f0; padding-top: 0.5rem; justify-content: space-between; align-items: center;">
                                    <!-- Copy link -->
                                    <button onclick="navigator.clipboard.writeText('<?php echo e($asset['url']); ?>'); alert('<?php echo CURRENT_LANG === 'ar' ? 'تم نسخ الرابط إلى الحافظة:' : 'File URL copied to clipboard:'; ?> <?php echo e($asset['url']); ?>');" class="btn-sm-action" style="font-size: 0.65rem; cursor: pointer; border: none; background: transparent; padding: 0.25rem 0.5rem; height: auto; min-width: auto; min-height: auto;">
                                        <?php echo CURRENT_LANG === 'ar' ? 'نسخ الرابط' : 'Copy Link'; ?>
                                    </button>
                                    
                                    <!-- Delete -->
                                    <a href="<?php echo $deleteUrl; ?>" onclick="return confirm('<?php echo __("media_confirm_delete"); ?>');" class="btn-sm-action delete" style="font-size: 0.65rem; padding: 0.25rem 0.5rem; height: auto; min-width: auto; min-height: auto;">
                                        <?php echo __("admin_delete"); ?>
                                    </a>
                                </div>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; color: #999; padding: 6rem 0; font-style: italic;">
                    <div style="font-size: 3rem; margin-bottom: 1.5rem;">📁</div>
                    <h3 class="serif-title" style="font-size: 1.3rem; margin-bottom: 0.5rem;"><?php echo CURRENT_LANG === 'ar' ? 'لم يتم العثور على وسائط' : 'No Media Found'; ?></h3>
                    <p style="font-weight: 300; font-size: 0.9rem;"><?php echo CURRENT_LANG === 'ar' ? 'لم تقم برفع أي ملفات وسائط بعد.' : 'You haven\'t uploaded any media attachments yet.'; ?></p>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>

</body>
</html>
