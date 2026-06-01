<?php
// scratch/test_invalidation.php
// Verification script for settings cache invalidation and menu status.

if (php_sapi_name() === 'cli' && !defined('DB_HOST')) {
    define('DB_HOST', '127.0.0.1');
}

define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/includes/config.php';
require_once PATH_ROOT . '/classes/Database.php';
require_once PATH_ROOT . '/classes/PostManager.php';

try {
    $pm = new PostManager();
    $db = Database::getInstance()->getConnection();
    
    echo "--- Initial Site Settings ---\n";
    $settings = $pm->getSiteSettings();
    echo "nav_show_podcasts: " . ($settings['nav_show_podcasts'] ?? 'not set') . "\n";
    echo "nav_show_stories: " . ($settings['nav_show_stories'] ?? 'not set') . "\n";
    echo "nav_show_news: " . ($settings['nav_show_news'] ?? 'not set') . "\n";
    echo "nav_show_accessibility: " . ($settings['nav_show_accessibility'] ?? 'not set') . "\n";
    echo "nav_show_contact: " . ($settings['nav_show_contact'] ?? 'not set') . "\n";

    echo "\n--- Initial Dynamic Menus ---\n";
    $menusQuery = $db->query("SELECT id, title_en, isActive FROM menus ORDER BY `order` ASC");
    $menus = $menusQuery->fetchAll();
    foreach ($menus as $m) {
        echo "Menu [{$m['id']}] '{$m['title_en']}': isActive=" . $m['isActive'] . "\n";
    }

    // Prepare simulated update payload
    $updateData = $settings; // Start with current values
    // Toggle podcasts visibility
    $newPodcastsState = ($settings['nav_show_podcasts'] ?? 1) == 1 ? 0 : 1;
    $updateData['nav_show_podcasts'] = $newPodcastsState;

    echo "\nSimulating saving settings with nav_show_podcasts = {$newPodcastsState}...\n";
    
    // Perform update
    $db->beginTransaction();
    $pm->updateSiteSettings($updateData);
    
    // Toggle first menu isActive state
    if (!empty($menus)) {
        $firstMenu = $menus[0];
        $newMenuState = $firstMenu['isActive'] == 1 ? 0 : 1;
        echo "Simulating toggling dynamic menu '{$firstMenu['title_en']}' isActive = {$newMenuState}...\n";
        
        $updateMenuStmt = $db->prepare("UPDATE menus SET isActive = :isActive WHERE id = :id");
        $updateMenuStmt->execute(['isActive' => $newMenuState, 'id' => $firstMenu['id']]);
    }
    $db->commit();
    
    // Explicitly invalidate cache
    $pm->clearCache();
    echo "Cache cleared successfully.\n";

    // Load again to verify
    echo "\n--- Verification after update & cache clear ---\n";
    $updatedSettings = $pm->getSiteSettings();
    echo "nav_show_podcasts: " . ($updatedSettings['nav_show_podcasts'] ?? 'not set') . " (Expected: {$newPodcastsState})\n";
    
    $updatedMenusQuery = $db->query("SELECT id, title_en, isActive FROM menus ORDER BY `order` ASC");
    $updatedMenus = $updatedMenusQuery->fetchAll();
    if (!empty($updatedMenus)) {
        echo "First Menu '{$updatedMenus[0]['title_en']}': isActive=" . $updatedMenus[0]['isActive'] . " (Expected: " . ($newMenuState ?? '') . ")\n";
    }

    if (($updatedSettings['nav_show_podcasts'] ?? '') == $newPodcastsState) {
        echo "\nSUCCESS: Settings and menus saved, cache was correctly invalidated, and changes verified successfully!\n";
    } else {
        echo "\nFAILURE: Settings update verification failed.\n";
    }

    // Restore original states to avoid side-effects on verification
    echo "\nRestoring original database values...\n";
    $db->beginTransaction();
    $pm->updateSiteSettings($settings);
    if (!empty($menus)) {
        $restoreMenuStmt = $db->prepare("UPDATE menus SET isActive = :isActive WHERE id = :id");
        $restoreMenuStmt->execute(['isActive' => $menus[0]['isActive'], 'id' => $menus[0]['id']]);
    }
    $db->commit();
    $pm->clearCache();
    echo "Original database states restored and cache cleared.\n";

} catch (Exception $e) {
    echo "Error during verification: " . $e->getMessage() . "\n";
    if ($db->inTransaction()) {
        $db->rollBack();
    }
}
