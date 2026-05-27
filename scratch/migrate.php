<?php
// scratch/migrate.php
// Pure PHP database migrations to set up podcasts, women stories, and hero slider.

try {
    require_once dirname(__DIR__) . '/includes/config.php';
    require_once dirname(__DIR__) . '/classes/Database.php';

    $db = Database::getInstance()->getConnection();
    echo "Successfully connected to database: " . DB_NAME . "\n";

    // Disable foreign key checks
    $db->exec("SET FOREIGN_KEY_CHECKS = 0;");

    // 1. Recreate contact_submissions table with phone and message
    echo "Recreating contact_submissions table...\n";
    $db->exec("DROP TABLE IF EXISTS `contact_submissions`;");
    $db->exec("CREATE TABLE `contact_submissions` (
      `id` VARCHAR(36) PRIMARY KEY,
      `name` VARCHAR(255) NOT NULL,
      `email` VARCHAR(255) NOT NULL,
      `phone` VARCHAR(255) NOT NULL,
      `message` TEXT NOT NULL,
      `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "contact_submissions table setup completed.\n";

    // 2. Recreate podcasts table
    echo "Recreating podcasts table...\n";
    $db->exec("DROP TABLE IF EXISTS `podcasts`;");
    $db->exec("CREATE TABLE `podcasts` (
      `id` VARCHAR(36) PRIMARY KEY,
      `title` VARCHAR(255) NOT NULL,
      `slug` VARCHAR(255) NOT NULL UNIQUE,
      `description` TEXT DEFAULT NULL,
      `audio_file` VARCHAR(255) NOT NULL,
      `cover_image` VARCHAR(255) DEFAULT NULL,
      `duration` VARCHAR(50) DEFAULT NULL,
      `category` VARCHAR(255) DEFAULT NULL,
      `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
      `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "podcasts table created.\n";

    // 3. Recreate women_stories table
    echo "Recreating women_stories table...\n";
    $db->exec("DROP TABLE IF EXISTS `women_stories`;");
    $db->exec("CREATE TABLE `women_stories` (
      `id` VARCHAR(36) PRIMARY KEY,
      `title` VARCHAR(255) NOT NULL,
      `slug` VARCHAR(255) NOT NULL UNIQUE,
      `excerpt` TEXT DEFAULT NULL,
      `content` LONGTEXT NOT NULL,
      `cover_image` VARCHAR(255) DEFAULT NULL,
      `category` VARCHAR(100) DEFAULT NULL,
      `author` VARCHAR(255) DEFAULT NULL,
      `read_time` VARCHAR(50) DEFAULT NULL,
      `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "women_stories table created.\n";

    // 4. Recreate hero_slides table
    echo "Recreating hero_slides table...\n";
    $db->exec("DROP TABLE IF EXISTS `hero_slides`;");
    $db->exec("CREATE TABLE `hero_slides` (
      `id` VARCHAR(36) PRIMARY KEY,
      `title` VARCHAR(255) NOT NULL,
      `subtitle` VARCHAR(255) DEFAULT NULL,
      `button_text` VARCHAR(255) DEFAULT NULL,
      `button_link` VARCHAR(255) DEFAULT NULL,
      `image` VARCHAR(255) DEFAULT NULL,
      `overlay_opacity` DECIMAL(3,2) NOT NULL DEFAULT 0.50,
      `sort_order` INT NOT NULL DEFAULT 0,
      `is_active` TINYINT(1) NOT NULL DEFAULT 1,
      `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "hero_slides table created.\n";

    // Re-enable foreign key checks
    $db->exec("SET FOREIGN_KEY_CHECKS = 1;");

    // 5. Seed initial Hero Slides
    echo "Seeding initial slides...\n";
    $db->exec("INSERT INTO `hero_slides` (`id`, `title`, `subtitle`, `button_text`, `button_link`, `image`, `overlay_opacity`, `sort_order`, `is_active`) VALUES
    ('slide-1', 'Travel Without Limits', 'Accessible travel guides, stories, and reviews', 'Discover More', '/destinations', '/images/hero-bg.png', 0.40, 1, 1),
    ('slide-2', 'Inspirational Journeys', 'Real stories of adventurers breaking down physical barriers', 'Read Stories', '/news', '/images/australia.png', 0.45, 2, 1),
    ('slide-3', 'Explore Accessible Egypt', 'Pockets of history and magic curated for diverse requirements', 'View Stories', '/women-stories', '/images/europe.png', 0.50, 3, 1)");
    echo "Hero slides seeded successfully.\n";

    // 6. Seed initial Podcasts
    echo "Seeding initial podcasts...\n";
    $db->exec("INSERT INTO `podcasts` (`id`, `title`, `slug`, `description`, `audio_file`, `cover_image`, `duration`, `category`, `is_featured`) VALUES
    ('pod-1', 'Accessible Antiquities: Exploring Giza Wheelchair Access', 'accessible-antiquities-exploring-giza-wheelchair-access', 'In this episode, we sit down with local experts and international travellers who share their firsthand experiences navigating the Giza Plateau, the pyramids, and the new Grand Egyptian Museum using mobility devices.', '/uploads/podcasts/episode1.mp3', '/images/europe.png', '24:18', 'Accessible Adventure', 1),
    ('pod-2', 'Sensory Calm in the Sinai Desert', 'sensory-calm-in-the-sinai-desert', 'Discover the therapeutic silence of the Sinai. We explore how guided desert excursions are being tailored to provide sensory-friendly wellness retreats for neurodivergent travellers looking to escape the noise.', '/uploads/podcasts/episode2.mp3', '/images/australia.png', '18:45', 'Solo Travel', 0),
    ('pod-3', 'Breaking Barriers: Solo Adventuring in Alexandria', 'breaking-barriers-solo-adventuring-in-alexandria', 'A deep dive into navigating the coastal city of Alexandria as a solo female traveller with low vision. Tips on tactile paths, audio-guided museum exhibits, and local sensory highlights.', '/uploads/podcasts/episode3.mp3', '/images/hero-bg.png', '31:12', 'Cultural Journeys', 0)");
    echo "Podcasts seeded successfully.\n";

    // Create dummy audio files if they don't exist, to prevent player errors
    $audioDir = dirname(__DIR__) . '/uploads/podcasts';
    if (!is_dir($audioDir)) {
        mkdir($audioDir, 0755, true);
    }
    // Create tiny dummy MP3s (using empty space or placeholder)
    file_put_contents($audioDir . '/episode1.mp3', '');
    file_put_contents($audioDir . '/episode2.mp3', '');
    file_put_contents($audioDir . '/episode3.mp3', '');

    // 7. Seed initial Women Stories (Egypt-focused)
    echo "Seeding initial women stories...\n";
    $storyContent1 = "<h3>A Solo Wheelchair Traveler's Encounter with the Pyramids</h3><p>For as long as I can remember, the Pyramids of Giza were a distant dream locked behind an intimidating barrier of sand, stairs, and steep pathways. When I finally decided to embark on a solo trip to Cairo, my wheelchair was packed alongside a healthy dose of anxiety. However, what I discovered was a city eager to open its historic gates to everyone.</p><p>Entering the Giza Plateau with a custom ramp setup and the assistance of local guides who understood my physical needs, the monumental stone structures felt closer than ever. The dry air, the golden sandstone against the clear blue sky, and the sheer majesty of the Sphinx standing guard left me breathless. It was not just about the sites, but the warmth of the people. Everywhere I went, from the busy alleys of Khan el-Khalili to the spacious halls of the new Grand Egyptian Museum, locals went out of their way to ensure I had clear sightlines and easy access.</p><h4>Top 3 Accessibility Tips for Cairo:</h4><ul><li><strong>Hire an accessible-certified local driver:</strong> Cairo's traffic is legendary, and having a ramp-equipped van makes all the difference.</li><li><strong>Grand Egyptian Museum (GEM):</strong> This state-of-the-art facility is built to international ADA standards, featuring elevators, tactile maps, and spacious paths.</li><li><strong>Sunset at Giza:</strong> Watch the sunset from the accessible paved viewing platform near the panorama area. It offers a majestic view of all three pyramids without needing to cross deep sand.</li></ul>";
    
    $storyContent2 = "<h3>Luxor Temples: Navigating Sacred Spaces with Sensory Sensitivity</h3><p>The ruins of Luxor and Karnak Temples are visual spectacles, but their colossal scale, busy tour groups, and echoey columns can be overwhelming for travellers with sensory processing sensitivities. As a neurodivergent woman, my journey to Southern Egypt was a lesson in planning, presence, and finding silence within ancient stones.</p><p>I chose to visit the Karnak Temple complex at sunrise. Entering before the crowds, the great Hypostyle Hall felt like a quiet forest of stone. The early morning light cast soft shadows across the giant pillars, each carved with stories of pharaohs and gods. By timing my visits to coincide with typical lunch hours, I managed to explore the Luxor Temple in near solitude. It allowed me to appreciate the intricate hieroglyphics and connect with the spiritual history of the place without the sensory clutter of megaphones and chatter.</p><h4>My Recommendations for Sensory Balance:</h4><ul><li><strong>Noise-canceling headphones:</strong> Essential for busy entrance plazas and bazaar areas.</li><li><strong>Go early or late:</strong> Aim for 6:00 AM or 5:00 PM. Not only is the weather cooler, but the crowd level drops by 80%.</li><li><strong>Take a Felucca:</strong> A traditional sailboat ride on the Nile at sunset provides the perfect sensory reset after a long day of touring.</li></ul>";

    $storyContent3 = "<h3>Sounds and Scents of Cairo: A Blind Traveler's Perspective</h3><p>Most travel brochures focus on the sights of Egypt, but the true essence of Cairo lies in its auditory and olfactory landscape. Exploring the historic Islamic Cairo as a blind woman opened my senses to a symphony of sights unheard and textures unfelt.</p><p>Navigating the medieval gates of Bab Zuwayla, the air smelled of roasted cardamom, fresh mint, and the rich earthy scent of tanned leather. Walking through Al-Muizz Street, the voice of the muezzin calling for prayer echoed from towering minarets, wrapping the ancient street in a blanket of peace. Hand-carved brass lamps felt cold and detailed under my fingertips, and the warmth of a freshly baked Aish Baladi (Egyptian flatbread) handed to me by a smiling baker was a moment of pure connection. Cairo does not require sight to be seen; it demands that you listen, smell, and touch.</p><h4>Key Insights for Visually Impaired Visitors:</h4><ul><li><strong>Tactile exploration:</strong> Many museums allow supervised touching of selected replica statues. Always ask for 'tactile guides'.</li><li><strong>Scent mapping:</strong> Khan el-Khalili's spice section is a wonderful sensory journey. Local merchants are proud to let you sample incense, spices, and perfumes.</li><li><strong>Companion guides:</strong> Work with specialized operators who are trained in descriptive narration of architectural details.</li></ul>";

    $stmt = $db->prepare("INSERT INTO `women_stories` (`id`, `title`, `slug`, `excerpt`, `content`, `cover_image`, `category`, `author`, `read_time`) VALUES
    (:id1, 'Solo Travel Through Cairo: A Wheelchair User’s Journal', 'solo-travel-through-cairo-wheelchair-journal', 'A firsthand account of navigating Cairo\'s historic sites, bustling streets, and the pyramids using a mobility device.', :content1, '/images/europe.png', 'Solo Travel', 'Emily Watson', '6 min read'),
    (:id2, 'Navigating the Temples of Luxor with Sensory Needs', 'navigating-temples-of-luxor-sensory-needs', 'Luxor\'s ancient monuments are awe-inspiring, but can be sensory-heavy. Here is a guide to finding quiet moments in Karnak Temple.', :content2, '/images/australia.png', 'Accessible Adventure', 'Sarah Jenkins', '5 min read'),
    (:id3, 'A Deaf Traveller’s Guide to the Vibrant Bazaars of Khan el-Khalili', 'deaf-traveller-guide-khan-el-khalili', 'Exploring the sights, smells, and textures of Cairo\'s famous marketplace through the lens of visual and tactile connection.', :content3, '/images/hero-bg.png', 'Cultural Journeys', 'Amina Mansour', '7 min read')");

    $stmt->execute([
        'id1' => 'story-1',
        'content1' => $storyContent1,
        'id2' => 'story-2',
        'content2' => $storyContent2,
        'id3' => 'story-3',
        'content3' => $storyContent3
    ]);
    echo "Women stories seeded successfully.\n";

    echo "Migration completed successfully!\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
