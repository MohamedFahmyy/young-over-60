<?php
// scratch/test_hero_settings.php
// Verification script for database CRUD and cache invalidation of hero video/mode configurations.

if (php_sapi_name() === 'cli' && !defined('DB_HOST')) {
    define('DB_HOST', '127.0.0.1');
}

define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/includes/config.php';
require_once PATH_ROOT . '/classes/Database.php';
require_once PATH_ROOT . '/classes/PostManager.php';

try {
    $pm = new PostManager();
    echo "1. Fetching original site settings...\n";
    $original = $pm->getSiteSettings();
    if (!$original) {
        throw new Exception("Failed to fetch initial settings.");
    }
    echo "   Current Mode: " . ($original['hero_mode'] ?? 'N/A') . "\n";
    echo "   Current Video (EN): " . ($original['hero_video_en'] ?? 'N/A') . "\n";

    echo "\n2. Attempting to update settings with test video parameters...\n";
    $testData = array_merge($original, [
        'hero_mode' => 'video',
        'hero_video_en' => '/uploads/test_promo.mp4',
        'hero_video_ar' => '/uploads/test_promo_ar.mp4',
        'hero_video_title_en' => 'Test Promo EN',
        'hero_video_title_ar' => 'فيديو تجريبي',
        'hero_video_subtitle_en' => 'Test Subtitle EN',
        'hero_video_subtitle_ar' => 'ترجمة تجريبية',
        'hero_video_btn_text_en' => 'Click Here',
        'hero_video_btn_text_ar' => 'اضغط هنا',
        'hero_video_btn_link' => '/destinations'
    ]);

    $updateSuccess = $pm->updateSiteSettings($testData);
    if (!$updateSuccess) {
        throw new Exception("Update failed.");
    }
    echo "   SUCCESS: Settings updated in database.\n";

    echo "\n3. Retrieving updated settings to check cache invalidation...\n";
    $updated = $pm->getSiteSettings();
    if ($updated['hero_mode'] !== 'video' || $updated['hero_video_en'] !== '/uploads/test_promo.mp4') {
        throw new Exception("Cache invalidation failed. Got stale data.");
    }
    echo "   SUCCESS: Stored settings retrieved correctly:\n";
    echo "   Retrieved Mode: " . $updated['hero_mode'] . "\n";
    echo "   Retrieved Title: " . $updated['hero_video_title_en'] . "\n";

    echo "\n4. Restoring original settings...\n";
    $restoreSuccess = $pm->updateSiteSettings($original);
    if (!$restoreSuccess) {
        throw new Exception("Restoration failed.");
    }
    echo "   SUCCESS: Original settings restored successfully.\n";

    $finalCheck = $pm->getSiteSettings();
    echo "   Final Mode: " . $finalCheck['hero_mode'] . "\n";
    echo "\nALL TESTS PASSED SUCCESSFULLY!\n";

} catch (Exception $e) {
    echo "\nTEST FAILED: " . $e->getMessage() . "\n";
    exit(1);
}
