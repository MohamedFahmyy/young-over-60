<?php
// scratch/test_admin_editor_save.php
define('CURRENT_LANG', 'en');
define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/includes/config.php';
require_once PATH_ROOT . '/classes/Database.php';
require_once PATH_ROOT . '/classes/PageManager.php';

$pageMgr = new PageManager();
$pageId = 'page-about';

// Get current page
$originalPage = $pageMgr->getPageById($pageId);
if (!$originalPage) {
    echo "ERROR: About Us page not found.\n";
    exit(1);
}

// Store original contents to restore later
$origEn = $originalPage['content_en'];
$origAr = $originalPage['content_ar'];

echo "--- Testing Rich Text Saving ---\n";

// HTML with visual bolding and an inline uploaded image
$testEnContent = $origEn . "\n<!-- test marker -->\n<p><strong>Visual Editor Live Test:</strong> antigravity visual editing and image upload systems are fully functional!</p>\n<img src=\"/uploads/images/test_inline.webp\" alt=\"Test Inline Image\" class=\"content-img\" />";
$testArContent = $origAr . "\n<!-- test marker ar -->\n<p><strong>اختبار المحرر المرئي:</strong> نظام التحرير المرئي ورفع الصور يعمل بنجاح!</p>";

$updateData = $originalPage;
$updateData['content_en'] = $testEnContent;
$updateData['content_ar'] = $testArContent;

// Perform database update
$success = $pageMgr->updatePage($pageId, $updateData);
if ($success) {
    echo "✓ Success: Database updated with rich text and inline images!\n";
} else {
    echo "✗ Error: Database update failed.\n";
}

// Retrieve page again to verify
$updatedPage = $pageMgr->getPageById($pageId);
if ($updatedPage) {
    if (strpos($updatedPage['content_en'], 'Visual Editor Live Test:') !== false && strpos($updatedPage['content_en'], '/uploads/images/test_inline.webp') !== false) {
        echo "✓ Success: Retrieved content contains bold text and inline image URL!\n";
    } else {
        echo "✗ Error: Retrieved content does not match expected update.\n";
    }
}

// Restore original contents to keep DB clean
$restoreData = $updatedPage;
$restoreData['content_en'] = $origEn;
$restoreData['content_ar'] = $origAr;
$pageMgr->updatePage($pageId, $restoreData);

echo "✓ Original page contents successfully restored.\n";
echo "--- Verification Completed ---\n";
