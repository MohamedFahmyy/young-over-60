<?php
define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/config/database.php';
require_once PATH_ROOT . '/classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();

    // Check if columns already exist
    $checkQuery = $db->query("SHOW COLUMNS FROM `custom_pages` LIKE 'excerpt_en'");
    $exists = $checkQuery->fetch();

    if (!$exists) {
        echo "Adding excerpt_en and excerpt_ar columns to custom_pages...\n";
        $db->exec("ALTER TABLE `custom_pages` ADD COLUMN `excerpt_en` TEXT DEFAULT NULL AFTER `content_ar`");
        $db->exec("ALTER TABLE `custom_pages` ADD COLUMN `excerpt_ar` TEXT DEFAULT NULL AFTER `excerpt_en`");
        echo "✓ custom_pages altered successfully.\n";
    } else {
        echo "Columns excerpt_en and excerpt_ar already exist in custom_pages.\n";
    }

    // Also for page_revisions
    $checkQuery2 = $db->query("SHOW COLUMNS FROM `page_revisions` LIKE 'excerpt_en'");
    $exists2 = $checkQuery2->fetch();

    if (!$exists2) {
        echo "Adding excerpt_en and excerpt_ar columns to page_revisions...\n";
        $db->exec("ALTER TABLE `page_revisions` ADD COLUMN `excerpt_en` TEXT DEFAULT NULL AFTER `content_ar`");
        $db->exec("ALTER TABLE `page_revisions` ADD COLUMN `excerpt_ar` TEXT DEFAULT NULL AFTER `excerpt_en`");
        echo "✓ page_revisions altered successfully.\n";
    } else {
        echo "Columns excerpt_en and excerpt_ar already exist in page_revisions.\n";
    }

    echo "Migration completed successfully!\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
