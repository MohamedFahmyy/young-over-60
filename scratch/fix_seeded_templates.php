<?php
// scratch/fix_seeded_templates.php
define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/config/database.php';
require_once PATH_ROOT . '/classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();

    echo "Updating template types for seeded custom pages...\n";

    // Update about-us -> about
    $stmt1 = $db->prepare("UPDATE custom_pages SET template_type = 'about' WHERE id = 'page-about'");
    $stmt1->execute();
    echo "✓ page-about updated to template 'about'\n";

    // Update medical -> medical-tourism
    $stmt2 = $db->prepare("UPDATE custom_pages SET template_type = 'medical-tourism' WHERE id = 'page-medical'");
    $stmt2->execute();
    echo "✓ page-medical updated to template 'medical-tourism'\n";

    echo "Done!\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
