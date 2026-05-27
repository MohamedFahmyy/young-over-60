<?php
// scratch/change_brand.php
// Script to update site settings and slider brand values to "Young Over 60"

try {
    require_once dirname(__DIR__) . '/includes/config.php';
    require_once dirname(__DIR__) . '/classes/Database.php';

    $db = Database::getInstance()->getConnection();
    echo "Connecting to database...\n";

    // 1. Update site settings
    $stmt1 = $db->prepare("UPDATE site_settings SET 
        siteName = :siteName, 
        metaTitle = :metaTitle, 
        metaDescription = :metaDesc 
        WHERE id = 1");
    $stmt1->execute([
        'siteName' => 'Young Over 60',
        'metaTitle' => 'Young Over 60 | Active Travel & Inspiration',
        'metaDesc' => 'Premium accessible travel guides, stories, and reviews tailored for active seniors and travelers over 60.'
    ]);
    echo "Site settings updated successfully to 'Young Over 60'.\n";

    // 2. Update default slide
    $stmt2 = $db->prepare("UPDATE hero_slides SET 
        title = :title, 
        subtitle = :subtitle 
        WHERE id = 'slide-1'");
    $stmt2->execute([
        'title' => 'Young Over 60',
        'subtitle' => 'Premium travel guides, reviews, and stories for active seniors'
    ]);
    echo "Hero slide 1 updated successfully.\n";

} catch (Exception $e) {
    echo "Error updating brand: " . $e->getMessage() . "\n";
}
