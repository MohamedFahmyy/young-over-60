<?php
// scratch/add_font_columns.php
// Safe one-time migration to add style customization columns to site_settings

define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/config/database.php';
require_once PATH_ROOT . '/classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "Connected to the database successfully.\n";

    // Define columns to add
    $columns = [
        'themeMode' => "VARCHAR(50) NOT NULL DEFAULT 'light'",
        'fontFamily' => "VARCHAR(255) NOT NULL DEFAULT 'Outfit'",
        'fontFamilyBody' => "VARCHAR(255) NOT NULL DEFAULT 'Inter'",
        'fontFamilyAr' => "VARCHAR(255) NOT NULL DEFAULT 'Cairo'",
        'fontFamilyArBody' => "VARCHAR(255) NOT NULL DEFAULT 'Cairo'",
        'fontSize' => "VARCHAR(50) NOT NULL DEFAULT '16px'",
        'headingWeight' => "VARCHAR(50) NOT NULL DEFAULT '700'",
        'bodyWeight' => "VARCHAR(50) NOT NULL DEFAULT '400'",
        'letterSpacing' => "VARCHAR(50) NOT NULL DEFAULT 'normal'",
        'lineHeight' => "VARCHAR(50) NOT NULL DEFAULT '1.8'",
        'surfaceColor' => "VARCHAR(50) NOT NULL DEFAULT '#ffffff'",
        'borderColor' => "VARCHAR(50) NOT NULL DEFAULT 'rgba(0,0,0,0.05)'"
    ];

    // Check existing columns
    $stmt = $db->query("DESCRIBE `site_settings`");
    $existing = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($columns as $name => $definition) {
        if (!in_array($name, $existing)) {
            $db->exec("ALTER TABLE `site_settings` ADD COLUMN `{$name}` {$definition}");
            echo "Added column `{$name}` to `site_settings`.\n";
        } else {
            echo "Column `{$name}` already exists in `site_settings`.\n";
        }
    }

    // Set brand colors as default if not updated
    $db->exec("UPDATE `site_settings` SET 
        `primaryColor` = '#0F4C81', 
        `accentColor` = '#D4A75C', 
        `backgroundColor` = '#F8F6F2', 
        `textColor` = '#1F1F1F',
        `surfaceColor` = '#ffffff',
        `borderColor` = 'rgba(0,0,0,0.05)'
        WHERE `id` = 1");
    echo "Updated default branding colors in `site_settings` table.\n";

    echo "Migration completed successfully!\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
