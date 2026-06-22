<?php
define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/includes/config.php';
require_once PATH_ROOT . '/classes/Database.php';
require_once PATH_ROOT . '/classes/PostManager.php';

try {
    $pm = new PostManager();
    $originalSettings = $pm->getSiteSettings();
    echo "Original Settings:\n";
    echo "WhatsApp: " . ($originalSettings['contact_whatsapp'] ?? 'NULL') . "\n";
    echo "Phone: " . ($originalSettings['contact_phone'] ?? 'NULL') . "\n";

    // Test data
    $testData = $originalSettings;
    $testData['contact_whatsapp'] = '+61 411 222 333';
    $testData['contact_phone'] = '+61 411 222 333';

    echo "\nUpdating settings...\n";
    $success = $pm->updateSiteSettings($testData);
    if ($success) {
        echo "Site settings updated successfully!\n";
    } else {
        echo "Failed to update site settings!\n";
    }

    $newSettings = $pm->getSiteSettings();
    echo "\nNew Settings:\n";
    echo "WhatsApp: " . ($newSettings['contact_whatsapp'] ?? 'NULL') . "\n";
    echo "Phone: " . ($newSettings['contact_phone'] ?? 'NULL') . "\n";

    // Restore original values
    $pm->updateSiteSettings($originalSettings);
    echo "\nRestored original values.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
