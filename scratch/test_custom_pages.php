<?php
if (php_sapi_name() === 'cli' && !defined('DB_HOST')) {
    define('DB_HOST', '127.0.0.1');
}

define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/includes/config.php';
require_once PATH_ROOT . '/config/database.php';
require_once PATH_ROOT . '/classes/Database.php';
require_once PATH_ROOT . '/classes/PageManager.php';

try {
    $db = Database::getInstance()->getConnection();
    $pageMgr = new PageManager($db);
    $pageMgr->clearCache();

    echo "=== RUNNING CUSTOM PAGES VERIFICATION ===\n\n";

    // 1. Column Existence Check
    echo "1. Checking database columns...\n";
    $tablesToCheck = ['custom_pages', 'page_revisions'];
    foreach ($tablesToCheck as $table) {
        $stmt = $db->query("SHOW COLUMNS FROM `$table` LIKE 'excerpt_en'");
        if ($stmt->fetch()) {
            echo " ✓ Column 'excerpt_en' exists in '$table'.\n";
        } else {
            throw new Exception("FAIL: Column 'excerpt_en' NOT found in '$table'.");
        }

        $stmt = $db->query("SHOW COLUMNS FROM `$table` LIKE 'excerpt_ar'");
        if ($stmt->fetch()) {
            echo " ✓ Column 'excerpt_ar' exists in '$table'.\n";
        } else {
            throw new Exception("FAIL: Column 'excerpt_ar' NOT found in '$table'.");
        }
    }

    // 2. CRUD Operations & Excerpts Testing
    echo "\n2. Testing Page CRUD and Excerpts...\n";
    $tempSlug = 'test-temp-page-' . uniqid();
    $pageData = [
        'title_en' => 'Test Temp Page',
        'title_ar' => 'صفحة تجريبية مؤقتة',
        'slug_en' => $tempSlug,
        'slug_ar' => 'صفحة-تجريبية-مؤقتة',
        'content_en' => '<p>Test content English</p>',
        'content_ar' => '<p>محتوى تجريبي عربي</p>',
        'excerpt_en' => 'This is a test English excerpt.',
        'excerpt_ar' => 'هذا مقتطف تجريبي عربي.',
        'template_type' => 'default',
        'is_published' => 1,
        'show_in_menu' => 0
    ];

    // Create Page
    $pageId = $pageMgr->createPage($pageData);
    if (!$pageId) {
        throw new Exception("FAIL: Failed to create test page.");
    }
    echo " ✓ Page created successfully with ID: $pageId\n";

    // Retrieve Page
    $createdPage = $pageMgr->getPageById($pageId);
    if (!$createdPage) {
        throw new Exception("FAIL: Failed to retrieve page by ID.");
    }
    if ($createdPage['excerpt_en'] !== $pageData['excerpt_en'] || $createdPage['excerpt_ar'] !== $pageData['excerpt_ar']) {
        throw new Exception("FAIL: Created page excerpts do not match initial data.");
    }
    echo " ✓ Retrieved page excerpts match input data.\n";

    // 3. Revisions and Restoration Verification
    echo "\n3. Testing Page Revisions...\n";
    
    // Check initial revisions count (should be 0)
    $revisions = $pageMgr->getRevisions($pageId);
    echo "   Initial revision count: " . count($revisions) . "\n";

    // Update Page (should trigger saveRevision)
    $updatedData = $pageData;
    $updatedData['title_en'] = 'Test Temp Page v2';
    $updatedData['excerpt_en'] = 'Updated English Excerpt';
    $updatedData['excerpt_ar'] = 'مقتطف عربي محدث';

    $updateSuccess = $pageMgr->updatePage($pageId, $updatedData);
    if (!$updateSuccess) {
        throw new Exception("FAIL: Failed to update page.");
    }
    echo " ✓ Page updated successfully.\n";

    // Check revisions list (should have 1 revision of the old v1 state)
    $revisions = $pageMgr->getRevisions($pageId);
    if (count($revisions) !== 1) {
        throw new Exception("FAIL: Revision not saved during update.");
    }
    echo " ✓ Revision created. Old state saved successfully.\n";

    $revision = $revisions[0];
    if ($revision['excerpt_en'] !== $pageData['excerpt_en'] || $revision['excerpt_ar'] !== $pageData['excerpt_ar']) {
        throw new Exception("FAIL: Revision excerpt does not match original page state.");
    }
    echo " ✓ Saved revision contains original v1 excerpts.\n";

    // Restore Revision
    echo "   Restoring original revision...\n";
    $restoreSuccess = $pageMgr->restoreRevision($revision['id']);
    if (!$restoreSuccess) {
        throw new Exception("FAIL: Failed to restore revision.");
    }
    
    // Verify Page reverted back to original
    $restoredPage = $pageMgr->getPageById($pageId);
    if ($restoredPage['title_en'] !== $pageData['title_en']) {
        throw new Exception("FAIL: Page title not restored.");
    }
    if ($restoredPage['excerpt_en'] !== $pageData['excerpt_en'] || $restoredPage['excerpt_ar'] !== $pageData['excerpt_ar']) {
        throw new Exception("FAIL: Restored page excerpts do not match the old revision.");
    }
    echo " ✓ Restored page successfully. Excerpts reverted to original values.\n";

    // Delete Page
    echo "\n4. Cleaning up test page...\n";
    if ($pageMgr->deletePage($pageId)) {
        echo " ✓ Test page deleted successfully.\n";
    } else {
        echo " ⚠️ Failed to delete test page.\n";
    }

    echo "\n=== ALL VERIFICATIONS PASSED SUCCESSFULLY! ===\n";

} catch (Exception $e) {
    echo "\n❌ VERIFICATION FAILED: " . $e->getMessage() . "\n";
    exit(1);
}
