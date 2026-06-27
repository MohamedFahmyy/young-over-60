<?php
/**
 * deploy_dutch_localization.php
 * 
 * Production Deployment & Migration Script for Dutch Localization
 * Run via CLI: php deploy_dutch_localization.php
 */

define('PATH_ROOT', __DIR__);

// Load config and connection
require_once PATH_ROOT . '/includes/config.php';
require_once PATH_ROOT . '/classes/Database.php';
require_once PATH_ROOT . '/classes/PageManager.php';
require_once PATH_ROOT . '/classes/PostManager.php';

$isCli = (php_sapi_name() === 'cli');
$lineBreak = $isCli ? "\n" : "<br>";

echo "=== starting production deployment script ===" . $lineBreak;

try {
    $db = Database::getInstance()->getConnection();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // -------------------------------------------------------------
    // STEP 1: Add Dutch Slug Indexes for Optimization
    // -------------------------------------------------------------
    echo $lineBreak . "[STEP 1] Hardening Database Indexes..." . $lineBreak;
    
    $indexes = [
        ['table' => 'custom_pages', 'column' => 'slug_nl', 'name' => 'idx_custom_pages_slug_nl'],
        ['table' => 'women_stories', 'column' => 'slug_nl', 'name' => 'idx_stories_slug_nl'],
        ['table' => 'podcasts', 'column' => 'slug_nl', 'name' => 'idx_podcasts_slug_nl']
    ];

    foreach ($indexes as $idx) {
        $table = $idx['table'];
        $column = $idx['column'];
        $name = $idx['name'];
        
        // Check if index exists
        $stmt = $db->prepare("SHOW INDEX FROM `$table` WHERE Key_name = :indexName");
        $stmt->execute(['indexName' => $name]);
        if ($stmt->fetch()) {
            echo " - Index '$name' already exists on table '$table'." . $lineBreak;
        } else {
            $db->exec("CREATE INDEX `$name` ON `$table` (`$column`)");
            echo " - Created index '$name' on table '$table' ($column)." . $lineBreak;
        }
    }

    // -------------------------------------------------------------
    // STEP 2: Idempotent Seeding of Dutch Menus & Navigation
    // -------------------------------------------------------------
    echo $lineBreak . "[STEP 2] Seeding Dutch Menus & Navigation..." . $lineBreak;

    // Menus
    $menus = [
        'menu-destinations' => 'BESTEMMINGEN',
        'menu-experiences' => 'ERVARINGEN',
        'menu-plan' => 'PLAN UW REIS'
    ];
    foreach ($menus as $id => $title_nl) {
        $stmt = $db->prepare("UPDATE menus SET title_nl = :title_nl WHERE id = :id");
        $stmt->execute(['title_nl' => $title_nl, 'id' => $id]);
        echo " - Updated menu '$id' -> '$title_nl'" . $lineBreak;
    }

    // Menu Sections
    $sections = [
        'sec-americas' => 'AMERIKA\'S',
        'sec-australia' => 'AUSTRALIË',
        'sec-experiences' => 'ALLE ERVARINGEN',
        'sec-plan' => 'HULP BIJ HET PLAN'
    ];
    foreach ($sections as $id => $title_nl) {
        $stmt = $db->prepare("UPDATE menu_sections SET title_nl = :title_nl WHERE id = :id");
        $stmt->execute(['title_nl' => $title_nl, 'id' => $id]);
        echo " - Updated section '$id' -> '$title_nl'" . $lineBreak;
    }

    // Menu Links
    $links = [
        'link-accom' => 'ACCOMMODATIE',
        'link-act' => 'ACT',
        'link-beach' => 'STRANDUITSTAPJES',
        'link-cali' => 'CALIFORNIË',
        'link-colo' => 'COLORADO',
        'link-cruises' => 'CRUISES',
        'link-encounters' => 'ONTMOETINGEN MET DIEREN',
        'link-family' => 'FAMILIEREIZEN',
        'link-gear' => 'UITRUSTING & ONDERSTEUNING',
        'link-nsw' => 'NEW SOUTH WALES',
        'link-nt' => 'NOORDELIJK TERRITORIUM',
        'link-qld' => 'QUEENSLAND',
        'link-texas' => 'TEXAS',
        'link-tips' => 'TIPS & TRICKS',
        'link-transport' => 'VERVOER'
    ];
    foreach ($links as $id => $title_nl) {
        $stmt = $db->prepare("UPDATE menu_links SET title_nl = :title_nl WHERE id = :id");
        $stmt->execute(['title_nl' => $title_nl, 'id' => $id]);
        echo " - Updated link '$id' -> '$title_nl'" . $lineBreak;
    }

    // -------------------------------------------------------------
    // STEP 3: Idempotent Seeding of Dutch Categories
    // -------------------------------------------------------------
    echo $lineBreak . "[STEP 3] Seeding Dutch Categories..." . $lineBreak;

    $categories = [
        'cat-accommodation' => ['Accommodatie', 'accommodation'],
        'cat-act' => ['ACT', 'australian-capital-territory'],
        'cat-africa' => ['Afrika', 'africa'],
        'cat-americas' => ['Amerika\'s', 'americas'],
        'cat-animal-encounters' => ['Ontmoetingen met dieren', 'animal-encounters'],
        'cat-animals' => ['Assistentiedieren', 'assistance-animals'],
        'cat-asiapacific' => ['Azië-Pacific', 'asia-pacific'],
        'cat-australia' => ['Australië', 'australia'],
        'cat-cruises' => ['Cruises', 'cruises'],
        'cat-deaf' => ['Doof / Slechthorend', 'deaf-hard-of-hearing'],
        'cat-europe' => ['Europa', 'europe'],
        'cat-events' => ['Evenementen & Feestdagen', 'events-holidays'],
        'cat-family' => ['Familiereizen', 'family-travel'],
        'cat-food' => ['Eten & Drinken', 'food-drink'],
        'cat-gear' => ['Uitrusting & Ondersteuning', 'gear'],
        'cat-hidden' => ['Onzichtbare Beperkingen', 'hidden-disabilities'],
        'cat-inspiration' => ['Inspiratie', 'inspiration'],
        'cat-mobility' => ['Fysiek / Mobiliteit', 'physical-mobility'],
        'cat-neurodiversity' => ['Neurodiversiteit', 'neurodiversity'],
        'cat-nsw' => ['New South Wales', 'new-south-wales'],
        'cat-nt' => ['Noordelijk Territorium', 'northern-territory'],
        'cat-queensland' => ['Queensland', 'queensland'],
        'cat-reviews' => ['Bronnen & Beoordelingen', 'resources-reviews'],
        'cat-sa' => ['Zuid-Australië', 'south-australia'],
        'cat-sensory' => ['Sensorische Behoeften', 'sensory-needs'],
        'cat-tasmania' => ['Tasmanië', 'tasmania'],
        'cat-tips' => ['Tips & Trucs', 'tips-tricks'],
        'cat-transport' => ['Vervoer', 'transport'],
        'cat-victoria' => ['Victoria', 'victoria'],
        'cat-vision' => ['Blind / Slechtziend', 'blind-low-vision'],
        'cat-wa' => ['West-Australië', 'western-australia']
    ];

    foreach ($categories as $id => $info) {
        $name_nl = $info[0];
        $slug_nl = $info[1];
        $stmt = $db->prepare("UPDATE categories SET name_nl = :name_nl, slug_nl = :slug_nl WHERE id = :id");
        $stmt->execute(['name_nl' => $name_nl, 'slug_nl' => $slug_nl, 'id' => $id]);
        echo " - Updated category '$id' -> '$name_nl' (slug: $slug_nl)" . $lineBreak;
    }

    // -------------------------------------------------------------
    // STEP 4: Invalidate Session and JSON Caches
    // -------------------------------------------------------------
    echo $lineBreak . "[STEP 4] Invalidating Server Caches..." . $lineBreak;

    // PageManager Cache Invalidation
    $pageMgr = new PageManager();
    $pageMgr->clearCache();

    // PostManager Cache Invalidation
    $postMgr = new PostManager();
    $postMgr->clearCache();

    echo " - Cache cleared successfully." . $lineBreak;

    echo $lineBreak . "=== SUCCESS: Production Deployment Finished! ===" . $lineBreak;

} catch (Exception $e) {
    echo $lineBreak . "=== ERROR: Deployment Failed ===" . $lineBreak;
    echo $e->getMessage() . $lineBreak;
    exit(1);
}
