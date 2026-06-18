<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../classes/Database.php';
$db = Database::getInstance()->getConnection();
$stmt = $db->query('SELECT about_hero_label, about_founder_name, about_contact_heading FROM site_settings WHERE id=1');
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo "about_hero_label    : " . ($row['about_hero_label']    ?? 'NULL') . "\n";
echo "about_founder_name  : " . ($row['about_founder_name']  ?? 'NULL') . "\n";
echo "about_contact_heading: " . ($row['about_contact_heading'] ?? 'NULL') . "\n";
echo "\n✅ Columns confirmed present.\n";
