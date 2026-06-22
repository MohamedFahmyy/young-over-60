<?php
define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/config/database.php';
require_once PATH_ROOT . '/classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // First, let's modify some large VARCHAR columns in site_settings to TEXT to save row size
    echo "Modifying columns to TEXT to save row size...\n";
    $db->exec("ALTER TABLE `site_settings` MODIFY COLUMN `about_hero_quote` TEXT DEFAULT NULL");
    $db->exec("ALTER TABLE `site_settings` MODIFY COLUMN `about_vision_quote` TEXT DEFAULT NULL");
    $db->exec("ALTER TABLE `site_settings` MODIFY COLUMN `about_phili_quote` TEXT DEFAULT NULL");
    $db->exec("ALTER TABLE `site_settings` MODIFY COLUMN `about_founder_linkedin` TEXT DEFAULT NULL");
    $db->exec("ALTER TABLE `site_settings` MODIFY COLUMN `about_founder_image` TEXT DEFAULT NULL");
    $db->exec("ALTER TABLE `site_settings` MODIFY COLUMN `about_hero_image` TEXT DEFAULT NULL");
    echo "Columns modified successfully.\n";

    // Check if contact_whatsapp column exists
    $stmt = $db->query("SHOW COLUMNS FROM `site_settings` LIKE 'contact_whatsapp'");
    $exists = $stmt->fetch();
    if (!$exists) {
        $db->exec("ALTER TABLE `site_settings` ADD COLUMN `contact_whatsapp` VARCHAR(255) DEFAULT NULL");
        echo "Added contact_whatsapp column.\n";
    } else {
        echo "contact_whatsapp column already exists.\n";
    }
    
    // Check if contact_phone column exists
    $stmt = $db->query("SHOW COLUMNS FROM `site_settings` LIKE 'contact_phone'");
    $exists = $stmt->fetch();
    if (!$exists) {
        $db->exec("ALTER TABLE `site_settings` ADD COLUMN `contact_phone` VARCHAR(255) DEFAULT NULL");
        echo "Added contact_phone column.\n";
    } else {
        echo "contact_phone column already exists.\n";
    }

    // Set some default values for testing if they are empty
    $db->exec("UPDATE `site_settings` SET `contact_whatsapp` = '+61 400 000 000' WHERE id = 1 AND (contact_whatsapp IS NULL OR contact_whatsapp = '')");
    $db->exec("UPDATE `site_settings` SET `contact_phone` = '+61 400 000 000' WHERE id = 1 AND (contact_phone IS NULL OR contact_phone = '')");
    echo "Migration completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
