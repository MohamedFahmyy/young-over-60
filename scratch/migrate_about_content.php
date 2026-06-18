<?php
// scratch/migrate_about_content.php
// Safe, idempotent migration to add About Page content columns to site_settings table.

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../classes/Database.php';

$db = Database::getInstance()->getConnection();

function columnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->query("DESCRIBE `$table`");
    foreach ($stmt->fetchAll() as $col) {
        if ($col['Field'] === $column) return true;
    }
    return false;
}

function safeAddCol(PDO $db, string $table, string $col, string $def): void {
    if (!columnExists($db, $table, $col)) {
        $db->exec("ALTER TABLE `$table` ADD COLUMN `$col` $def");
        echo "Added `$col` to `$table`.\n";
    } else {
        echo "Column `$col` already exists — skipped.\n";
    }
}

echo "=== About Page Content Migration ===\n";

$columns = [
    // Hero section
    'about_hero_label'          => "VARCHAR(255) DEFAULT 'Our Story'",
    'about_hero_heading_line1'  => "VARCHAR(255) DEFAULT 'Beyond'",
    'about_hero_heading_accent' => "VARCHAR(255) DEFAULT ' 60,'",
    'about_hero_heading_line2'  => "VARCHAR(255) DEFAULT 'Life Begins Again.'",
    'about_hero_quote'          => "VARCHAR(500) DEFAULT 'Life Begins Again.'",
    'about_hero_desc'           => "TEXT DEFAULT NULL",
    'about_hero_desc_mobile'    => "TEXT DEFAULT NULL",
    'about_hero_btn1_text'      => "VARCHAR(255) DEFAULT 'Discover Our Story →'",
    'about_hero_btn2_text'      => "VARCHAR(255) DEFAULT 'Meet The Team'",
    'about_hero_badge_label'    => "VARCHAR(255) DEFAULT 'Stories Shared'",
    'about_hero_badge_number'   => "VARCHAR(50) DEFAULT '500+'",
    'about_hero_image'          => "VARCHAR(500) DEFAULT '/assets/images/about-header.jpeg'",

    // Vision / Our Story section
    'about_vision_label'        => "VARCHAR(255) DEFAULT 'About Young Over 60'",
    'about_vision_heading'      => "VARCHAR(255) DEFAULT 'Travel Has'",
    'about_vision_heading_accent' => "VARCHAR(255) DEFAULT 'No Age Limit'",
    'about_vision_quote'        => "VARCHAR(500) DEFAULT 'Beyond 60, life begins again.'",
    'about_vision_card1_title'  => "VARCHAR(255) DEFAULT 'Young Over 60'",
    'about_vision_card1_text1'  => "TEXT DEFAULT NULL",
    'about_vision_card1_text2'  => "TEXT DEFAULT NULL",
    'about_vision_card2_title'  => "VARCHAR(255) DEFAULT 'Our Vision: Travel Without Boundaries'",
    'about_vision_card2_text1'  => "TEXT DEFAULT NULL",
    'about_vision_card2_text2'  => "TEXT DEFAULT NULL",
    'about_stat1_number'        => "VARCHAR(50) DEFAULT '60+'",
    'about_stat1_desc'          => "VARCHAR(255) DEFAULT 'Celebrating life beyond traditional limits.'",
    'about_stat2_number'        => "VARCHAR(50) DEFAULT '∞'",
    'about_stat2_desc'          => "VARCHAR(255) DEFAULT 'Endless opportunities to discover the world.'",
    'about_stat3_number'        => "VARCHAR(50) DEFAULT '100%'",
    'about_stat3_desc'          => "VARCHAR(255) DEFAULT 'Dedicated to accessible and inclusive travel.'",

    // Philosophy section
    'about_phili_label'         => "VARCHAR(255) DEFAULT 'Our Philosophy'",
    'about_phili_heading'       => "VARCHAR(255) DEFAULT 'Life Begins'",
    'about_phili_heading_accent'=> "VARCHAR(255) DEFAULT 'After 60'",
    'about_phili_quote'         => "VARCHAR(500) DEFAULT 'True youth lives in a curious mind and an adventurous spirit.'",
    'about_phili_text1'         => "TEXT DEFAULT NULL",
    'about_phili_text2'         => "TEXT DEFAULT NULL",
    'about_phili_text3'         => "TEXT DEFAULT NULL",

    // Founder / Contact section
    'about_founder_name'        => "VARCHAR(255) DEFAULT 'Zakaria Dawoud'",
    'about_founder_role'        => "VARCHAR(255) DEFAULT 'Founder & Project CEO'",
    'about_founder_linkedin'    => "VARCHAR(500) DEFAULT 'https://www.linkedin.com/in/zakaria-dawoud-26902b180'",
    'about_founder_quote'       => "TEXT DEFAULT NULL",
    'about_founder_image'       => "VARCHAR(500) DEFAULT '/assets/images/founder.jpeg'",
    'about_contact_heading'     => "VARCHAR(255) DEFAULT 'Let\\'s Build Something'",
    'about_contact_heading_accent' => "VARCHAR(255) DEFAULT 'Meaningful'",
    'about_contact_desc'        => "TEXT DEFAULT NULL",
];

foreach ($columns as $col => $def) {
    safeAddCol($db, 'site_settings', $col, $def);
}

// Populate defaults
$db->exec("UPDATE `site_settings` SET
    about_hero_desc = 'The stories, people, and passion behind a community that believes every new chapter can become the greatest adventure yet. We celebrate curiosity, connection, and meaningful journeys at every stage of life.',
    about_hero_desc_mobile = 'A community celebrating curiosity, connection, and meaningful journeys at every stage of life.',
    about_vision_card1_text1 = 'We believe that travel is neither a luxury nor a privilege reserved for a specific age group. It is a fundamental right for everyone, regardless of age or physical ability.',
    about_vision_card1_text2 = 'Travel opens windows to the world''s beauty, creates connections between cultures, and contributes to both mental and physical wellbeing.',
    about_vision_card2_text1 = 'We strive to make travel accessible, comfortable, and enjoyable for everyone over 60 and for individuals with special needs.',
    about_vision_card2_text2 = 'Our mission is to challenge outdated stereotypes and prove that adventure, curiosity, and exploration have no expiration date.',
    about_phili_text1 = 'We reject the idea of reducing a person to the number on their passport. Sixty is not the end of the road—it is a new beginning, filled with opportunities for discovery, growth, and unforgettable experiences.',
    about_phili_text2 = 'We believe that true youth is not measured by age, but by the ability to remain curious, open-minded, and eager to explore the world.',
    about_phili_text3 = 'At Young Over 60, every traveller has a story worth telling and every journey is a chance to rediscover life. The world is far too beautiful to be viewed only from a window, and your right to travel never expires.',
    about_founder_quote = 'Beyond 60, life begins again. This project was built to prove that travel has no age limit.',
    about_contact_desc = 'Whether you have a question, collaboration idea, or just want to say hello — feel free to reach out.'
    WHERE id = 1");

echo "\n✅ Defaults populated.\n";
echo "=== Migration Complete ===\n";
