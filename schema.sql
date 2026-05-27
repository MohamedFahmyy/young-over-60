-- u402417573_travelfinal-- schema.sql
-- pure PHP Travel Without Limits Database Migrations

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `media`;
DROP TABLE IF EXISTS `site_settings`;
DROP TABLE IF EXISTS `menu_banners`;
DROP TABLE IF EXISTS `menu_links`;
DROP TABLE IF EXISTS `menu_sections`;
DROP TABLE IF EXISTS `menus`;
DROP TABLE IF EXISTS `newsletters`;
DROP TABLE IF EXISTS `contact_submissions`;
DROP TABLE IF EXISTS `posts`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `podcasts`;
DROP TABLE IF EXISTS `women_stories`;
DROP TABLE IF EXISTS `hero_slides`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Users Table
CREATE TABLE `users` (
  `id` VARCHAR(36) PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) DEFAULT NULL,
  `bio` TEXT DEFAULT NULL,
  `avatar` VARCHAR(255) DEFAULT NULL,
  `twitter` VARCHAR(255) DEFAULT NULL,
  `facebook` VARCHAR(255) DEFAULT NULL,
  `instagram` VARCHAR(255) DEFAULT NULL,
  `linkedin` VARCHAR(255) DEFAULT NULL,
  `website` VARCHAR(255) DEFAULT NULL,
  `role` VARCHAR(50) NOT NULL DEFAULT 'ADMIN',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Categories Table
CREATE TABLE `categories` (
  `id` VARCHAR(36) PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `description` TEXT DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_categories_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Posts Table
CREATE TABLE `posts` (
  `id` VARCHAR(36) PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `excerpt` TEXT DEFAULT NULL,
  `content` LONGTEXT NOT NULL,
  `coverImage` VARCHAR(255) DEFAULT NULL,
  `isFeatured` TINYINT(1) NOT NULL DEFAULT 0,
  `status` VARCHAR(50) NOT NULL DEFAULT 'DRAFT',
  `publishedAt` DATETIME DEFAULT NULL,
  `categoryId` VARCHAR(36) NOT NULL,
  `authorId` VARCHAR(36) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME DEFAULT NULL,
  INDEX `idx_posts_slug` (`slug`),
  INDEX `idx_posts_category` (`categoryId`),
  INDEX `idx_posts_status` (`status`),
  INDEX `idx_posts_published` (`publishedAt`),
  FOREIGN KEY (`categoryId`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`authorId`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Contact Submissions Table
CREATE TABLE `contact_submissions` (
  `id` VARCHAR(36) PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Newsletters Table
CREATE TABLE `newsletters` (
  `id` VARCHAR(36) PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Menus Table
CREATE TABLE `menus` (
  `id` VARCHAR(36) PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `type` VARCHAR(50) NOT NULL, -- e.g. MEGA, SIMPLE
  `layoutType` VARCHAR(50) NOT NULL DEFAULT 'LIST', -- e.g. LIST, GRID
  `layoutConfig` LONGTEXT DEFAULT NULL,
  `order` INT NOT NULL DEFAULT 0,
  `isActive` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Menu Sections Table
CREATE TABLE `menu_sections` (
  `id` VARCHAR(36) PRIMARY KEY,
  `menuId` VARCHAR(36) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `order` INT NOT NULL DEFAULT 0,
  `isActive` TINYINT(1) NOT NULL DEFAULT 1,
  FOREIGN KEY (`menuId`) REFERENCES `menus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Menu Links Table
CREATE TABLE `menu_links` (
  `id` VARCHAR(36) PRIMARY KEY,
  `sectionId` VARCHAR(36) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `url` VARCHAR(255) NOT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `order` INT NOT NULL DEFAULT 0,
  `isActive` TINYINT(1) NOT NULL DEFAULT 1,
  `target` VARCHAR(50) DEFAULT NULL,
  FOREIGN KEY (`sectionId`) REFERENCES `menu_sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Menu Banners Table
CREATE TABLE `menu_banners` (
  `id` VARCHAR(36) PRIMARY KEY,
  `menuId` VARCHAR(36) NOT NULL,
  `image` VARCHAR(255) NOT NULL,
  `title` VARCHAR(255) DEFAULT NULL,
  `url` VARCHAR(255) DEFAULT NULL,
  `isActive` TINYINT(1) NOT NULL DEFAULT 1,
  FOREIGN KEY (`menuId`) REFERENCES `menus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Site Settings Table (Singleton, id=1)
CREATE TABLE `site_settings` (
  `id` INT PRIMARY KEY DEFAULT 1,
  `siteName` VARCHAR(255) NOT NULL DEFAULT 'Travel Without Limits',
  `logoUrl` VARCHAR(255) DEFAULT NULL,
  `logoDarkUrl` VARCHAR(255) DEFAULT NULL,
  `heroBackgroundUrl` VARCHAR(255) DEFAULT NULL,
  `metaTitle` VARCHAR(255) DEFAULT NULL,
  `metaDescription` TEXT DEFAULT NULL,
  `ogImage` VARCHAR(255) DEFAULT NULL,
  `faviconUrl` VARCHAR(255) DEFAULT NULL,
  `primaryColor` VARCHAR(50) NOT NULL DEFAULT '#2d5a88',
  `secondaryColor` VARCHAR(50) NOT NULL DEFAULT '#1e3c5a',
  `accentColor` VARCHAR(50) NOT NULL DEFAULT '#eaeaea',
  `backgroundColor` VARCHAR(50) NOT NULL DEFAULT '#ffffff',
  `textColor` VARCHAR(50) NOT NULL DEFAULT '#111111',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Media Table
CREATE TABLE `media` (
  `id` VARCHAR(36) PRIMARY KEY,
  `filename` VARCHAR(255) NOT NULL UNIQUE,
  `url` VARCHAR(255) NOT NULL,
  `mimeType` VARCHAR(50) NOT NULL,
  `fileSize` INT NOT NULL,
  `width` INT DEFAULT NULL,
  `height` INT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ==========================================
-- SEED DATA
-- ==========================================

-- Seed Site Settings
INSERT INTO `site_settings` (`id`, `siteName`, `logoUrl`, `heroBackgroundUrl`, `metaTitle`, `metaDescription`, `faviconUrl`, `primaryColor`, `secondaryColor`, `accentColor`, `backgroundColor`, `textColor`)
VALUES (1, 'Young Over 60', NULL, '/images/hero-bg.png', 'Young Over 60 | Active Travel & Inspiration', 'Premium accessible travel guides, stories, and reviews tailored for active seniors and travelers over 60.', '/favicon.ico', '#2d5a88', '#1e3c5a', '#eaeaea', '#ffffff', '#111111');

-- Seed Admin User (password is bcrypt hashed version of 'Password123')
INSERT INTO `users` (`id`, `email`, `password`, `name`, `bio`, `avatar`, `role`, `website`)
VALUES ('admin-uuid-1', 'admin@youngover60.com', '$2y$12$tanBCZuENAxM7yFBhzGIIeIJuZ9Cg0BmL2pqNp72WFRuIpD95ASRa', 'Site Admin', 'A passionate travel enthusiast dedicated to uncovering the world\'s most accessible gems. Sharing stories, tips, and guides to help everyone explore without limits.', '/uploads/admin-avatar.png', 'ADMIN', 'https://youngover60.com');

-- Seed Categories
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image`) VALUES
('cat-australia', 'Australia', 'australia', 'Everything you need to know about Australia and accessible travel.', '/images/australia.png'),
('cat-nsw', 'New South Wales', 'new-south-wales', 'Everything you need to know about New South Wales and accessible travel.', '/images/hero-bg.png'),
('cat-victoria', 'Victoria', 'victoria', 'Everything you need to know about Victoria and accessible travel.', '/images/hero-bg.png'),
('cat-queensland', 'Queensland', 'queensland', 'Everything you need to know about Queensland and accessible travel.', '/images/hero-bg.png'),
('cat-wa', 'Western Australia', 'western-australia', 'Everything you need to know about Western Australia and accessible travel.', '/images/hero-bg.png'),
('cat-sa', 'South Australia', 'south-australia', 'Everything you need to know about South Australia and accessible travel.', '/images/hero-bg.png'),
('cat-tasmania', 'Tasmania', 'tasmania', 'Everything you need to know about Tasmania and accessible travel.', '/images/hero-bg.png'),
('cat-nt', 'Northern Territory', 'northern-territory', 'Everything you need to know about Northern Territory and accessible travel.', '/images/hero-bg.png'),
('cat-act', 'ACT', 'australian-capital-territory', 'Everything you need to know about ACT and accessible travel.', '/images/hero-bg.png'),
('cat-asiapacific', 'Asia Pacific', 'asia-pacific', 'Everything you need to know about Asia Pacific and accessible travel.', '/images/asia.png'),
('cat-europe', 'Europe', 'europe', 'Everything you need to know about Europe and accessible travel.', '/images/europe.png'),
('cat-africa', 'Africa', 'africa', 'Everything you need to know about Africa and accessible travel.', '/images/hero-bg.png'),
('cat-americas', 'Americas', 'americas', 'Everything you need to know about Americas and accessible travel.', '/images/americas.png'),
('cat-animals', 'Assistance Animals', 'assistance-animals', 'Everything you need to know about Assistance Animals and accessible travel.', '/images/animals.png'),
('cat-vision', 'Blind / Low Vision', 'blind-low-vision', 'Everything you need to know about Blind / Low Vision and accessible travel.', '/images/vision.png'),
('cat-deaf', 'Deaf / Hard of Hearing', 'deaf-hard-of-hearing', 'Everything you need to know about Deaf / Hard of Hearing and accessible travel.', '/images/hero-bg.png'),
('cat-hidden', 'Hidden Disabilities', 'hidden-disabilities', 'Everything you need to know about Hidden Disabilities and accessible travel.', '/images/mental.png'),
('cat-mobility', 'Physical / Mobility', 'physical-mobility', 'Everything you need to know about Physical / Mobility and accessible travel.', '/images/mobility.png'),
('cat-sensory', 'Sensory Needs', 'sensory-needs', 'Everything you need to know about Sensory Needs and accessible travel.', '/images/sensory.png'),
('cat-neurodiversity', 'Neurodiversity', 'neurodiversity', 'Everything you need to know about Neurodiversity and accessible travel.', '/images/neurodiversity.png'),
('cat-accommodation', 'Accommodation', 'accommodation', 'Everything you need to know about Accommodation and accessible travel.', '/images/hero-bg.png'),
('cat-events', 'Events & Holidays', 'events-holidays', 'Everything you need to know about Events & Holidays and accessible travel.', '/images/hero-bg.png'),
('cat-inspiration', 'Inspiration', 'inspiration', 'Everything you need to know about Inspiration and accessible travel.', '/images/hero-bg.png'),
('cat-reviews', 'Resources & Reviews', 'resources-reviews', 'Everything you need to know about Resources & Reviews and accessible travel.', '/images/hero-bg.png'),
('cat-tips', 'Tips & Tricks', 'tips-tricks', 'Everything you need to know about Tips & Tricks and accessible travel.', '/images/hero-bg.png'),
('cat-transport', 'Transport', 'transport', 'Everything you need to know about Transport and accessible travel.', '/images/hero-bg.png'),
('cat-gear', 'Gear & Support', 'gear', 'Everything you need to know about Gear & Support and accessible travel.', '/images/hero-bg.png'),
('cat-cruises', 'Cruises', 'cruises', 'Everything you need to know about Cruises and accessible travel.', '/images/europe.png'),
('cat-family', 'Family Travel', 'family-travel', 'Everything you need to know about Family Travel and accessible travel.', '/images/mobility.png'),
('cat-food', 'Food & Drink', 'food-drink', 'Everything you need to know about Food & Drink and accessible travel.', '/images/hero-bg.png'),
('cat-animal-encounters', 'Animal Encounters', 'animal-encounters', 'Everything you need to know about Animal Encounters and accessible travel.', '/images/animals.png');

-- Seed Posts
INSERT INTO `posts` (`id`, `title`, `slug`, `excerpt`, `content`, `coverImage`, `isFeatured`, `status`, `publishedAt`, `categoryId`, `authorId`) VALUES
('post-turtle', 'On Turtle Time at Mon Repos Turtle Centre', 'on-turtle-time-at-mon-repos-turtle-centre', 'A magical encounter where accessible nature meets wonder. Experience the nesting and hatching of marine turtles in the Bundaberg region.', '<div><p>Kerry Heaney shares a magical moment at Mon Repos Turtle Centre in Queensland, where accessible nature meets wonder. The center is a world-class facility that offers people with mobility needs a front-row seat to the cycle of life.</p><p>From the flat, accessible walkways to the dedicated viewing areas, Mon Repos ensures that everyone can enjoy the spectacular sight of marine turtles nesting and hatching on the beach.</p></div>', '/images/australia.png', 1, 'PUBLISHED', '2026-01-15 20:00:00', 'cat-queensland', 'admin-uuid-1'),
('post-comedy', 'Laughs for all: Melbourne Comedy Festival 2026', 'laughs-for-all-melbourne-international-comedy-festival-2026', 'With nearly 800 shows and accessibility at its heart, the Melbourne Comedy Festival proves that laughter is for everyone.', '<div><p>The Melbourne International Comedy Festival is one of the most accessible major festivals in Australia. With a dedicated accessibility team and venues and shows designed for diverse audiences, it\'s a model for inclusive entertainment.</p><p>Key features include Auslan-interpreted shows, captioning, and wheelchair accessible venues, ensuring that no one misses out on the fun.</p></div>', '/images/australia.png', 0, 'PUBLISHED', '2026-03-22 10:00:00', 'cat-victoria', 'admin-uuid-1'),
('post-japan', 'Konichiwa, Japan: Serenity in Vibrancy', 'konichiwa-japan', 'Discovering pockets of serenity and world-class accessibility in Japan\'s vibrant cities, from Kyoto\'s temples to Tokyo\'s neon streets.', '<div><p>Japan is a masterclass in modern accessibility blending with ancient culture. Even in the busiest parts of Tokyo, the tactile paving, accessible subways, and welcoming attitude make it a favorite for travelers with limited mobility.</p><p>Experience the quiet beauty of Kyoto\'s accessible temple grounds or the high-tech inclusive facilities of Osaka.</p></div>', '/images/asia.png', 0, 'PUBLISHED', '2026-04-10 14:30:00', 'cat-asiapacific', 'admin-uuid-1'),
('post-kosci', 'Georgia’s Krazy Kosci Klimb', 'georgias-krazy-kosci-klimb', 'More than a fundraiser, the Krazy Kosci Klimb empowers young travelers to reach the highest peaks of Australia.', '<div><p>The Krazy Kosci Klimb is a unique event that sees participants with disabilities take on the challenge of reaching the summit of Mount Kosciuszko. It\'s a testament to the fact that with the right support, the sky is the limit.</p><p>This annual event raises crucial funds while providing participants with an experience they will never forget.</p></div>', '/images/mobility.png', 0, 'PUBLISHED', '2026-02-18 09:15:00', 'cat-mobility', 'admin-uuid-1'),
('post-propose', 'Top 10 Wheelchair Accessible Places to Propose', 'top-10-wheelchair-accessible-places-to-propose', 'Ensure your romantic milestone is perfect for everyone with these stunning, fully-accessible locations around the globe.', '<div><p>A marriage proposal is a moment to be shared and remembered. These ten locations provide the perfect backdrop, from the accessible beaches of Australia to the scenic overlooks of Europe.</p><p>We\'ve curated sites that offer both breathtaking beauty and seamless access, so you can focus on the moment.</p></div>', '/images/hero-bg.png', 0, 'PUBLISHED', '2026-05-01 11:00:00', 'cat-inspiration', 'admin-uuid-1'),
('post-music', 'A European summer of music and discovery', 'a-european-summer-of-music-and-discovery', 'DJ Cooper Smith shares his journey through Europe\'s most iconic music festivals, proving that music knows no limits.', '<div><p>DJ Cooper Smith and his mum, Bron, took on a whirlwind tour of Europe\'s festivals. From Glastonbury to the streets of Berlin, they explored how the continent is making its music scene more inclusive.</p><p>They found a world of assistive technology, dedicated viewing platforms, and a community of music lovers who embrace accessibility.</p></div>', '/images/europe.png', 0, 'PUBLISHED', '2026-05-20 16:45:00', 'cat-europe', 'admin-uuid-1');

-- Seed Navigation Menus
INSERT INTO `menus` (`id`, `title`, `slug`, `type`, `layoutType`, `order`, `isActive`) VALUES
('menu-destinations', 'DESTINATIONS', 'destinations', 'MEGA', 'LIST', 1, 1),
('menu-experiences', 'EXPERIENCES', 'experiences', 'MEGA', 'GRID', 2, 1),
('menu-plan', 'PLAN YOUR TRIP', 'plan-your-trip', 'MEGA', 'GRID', 3, 1);

-- Seed Menu Sections
INSERT INTO `menu_sections` (`id`, `menuId`, `title`, `order`, `isActive`) VALUES
('sec-australia', 'menu-destinations', 'AUSTRALIA', 1, 1),
('sec-americas', 'menu-destinations', 'AMERICAS', 2, 1),
('sec-experiences', 'menu-experiences', 'ALL EXPERIENCES', 1, 1),
('sec-plan', 'menu-plan', 'PLANNING HELP', 1, 1);

-- Seed Menu Links
INSERT INTO `menu_links` (`id`, `sectionId`, `title`, `url`, `image`, `order`, `isActive`, `target`) VALUES
-- Destinations Link
('link-act', 'sec-australia', 'ACT', '/category/australian-capital-territory', NULL, 1, 1, NULL),
('link-nsw', 'sec-australia', 'NEW SOUTH WALES', '/category/new-south-wales', NULL, 2, 1, NULL),
('link-nt', 'sec-australia', 'NORTHERN TERRITORY', '/category/northern-territory', NULL, 3, 1, NULL),
('link-qld', 'sec-australia', 'QUEENSLAND', '/category/queensland', NULL, 4, 1, NULL),
('link-colo', 'sec-americas', 'COLORADO', '/category/americas', NULL, 1, 1, NULL),
('link-cali', 'sec-americas', 'CALIFORNIA', '/category/americas', NULL, 2, 1, NULL),
('link-texas', 'sec-americas', 'TEXAS', '/category/americas', NULL, 3, 1, NULL),
-- Experiences Links (GRID Layout)
('link-encounters', 'sec-experiences', 'ANIMAL ENCOUNTERS', '/category/animal-encounters', '/images/animals.png', 1, 1, NULL),
('link-beach', 'sec-experiences', 'BEACH GETAWAYS', '/category/australia', '/images/australia.png', 2, 1, NULL),
('link-cruises', 'sec-experiences', 'CRUISES', '/category/cruises', '/images/europe.png', 3, 1, NULL),
('link-family', 'sec-experiences', 'FAMILY TRAVEL', '/category/family-travel', '/images/mobility.png', 4, 1, NULL),
-- Plan Links (GRID Layout)
('link-accom', 'sec-plan', 'ACCOMMODATION', '/category/accommodation', '/images/hero-bg.png', 1, 1, NULL),
('link-tips', 'sec-plan', 'TIPS & TRICKS', '/category/tips-tricks', '/images/hero-bg.png', 2, 1, NULL),
('link-transport', 'sec-plan', 'TRANSPORT', '/category/transport', '/images/hero-bg.png', 3, 1, NULL),
('link-gear', 'sec-plan', 'GEAR & SUPPORT', '/category/gear', '/images/hero-bg.png', 4, 1, NULL);

-- Seed Menu Banner
INSERT INTO `menu_banners` (`id`, `menuId`, `image`, `title`, `url`, `isActive`) VALUES
('banner-dest', 'menu-destinations', '/images/hero-bg.png', 'Featured Destination', '/featured', 1);

-- 12. Podcasts Table
CREATE TABLE IF NOT EXISTS `podcasts` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Women Stories Table
CREATE TABLE IF NOT EXISTS `women_stories` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Hero Slides Table
CREATE TABLE IF NOT EXISTS `hero_slides` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed Hero Slides
INSERT INTO `hero_slides` (`id`, `title`, `subtitle`, `button_text`, `button_link`, `image`, `overlay_opacity`, `sort_order`, `is_active`) VALUES
('slide-1', 'Young Over 60', 'Premium travel guides, reviews, and stories for active seniors', 'Discover More', '/destinations', '/images/hero-bg.png', 0.40, 1, 1),
('slide-2', 'Inspirational Journeys', 'Real stories of adventurers breaking down physical barriers', 'Read Stories', '/news', '/images/australia.png', 0.45, 2, 1),
('slide-3', 'Explore Accessible Egypt', 'Pockets of history and magic curated for diverse requirements', 'View Guides', '/women-stories', '/images/europe.png', 0.50, 3, 1);

-- Seed Podcasts
INSERT INTO `podcasts` (`id`, `title`, `slug`, `description`, `audio_file`, `cover_image`, `duration`, `category`, `is_featured`) VALUES
('pod-1', 'Accessible Antiquities: Exploring Giza Wheelchair Access', 'accessible-antiquities-exploring-giza-wheelchair-access', 'In this episode, we sit down with local experts and international travellers who share their firsthand experiences navigating the Giza Plateau, the pyramids, and the new Grand Egyptian Museum using mobility devices.', '/uploads/podcasts/episode1.mp3', '/images/europe.png', '24:18', 'Accessible Adventure', 1),
('pod-2', 'Sensory Calm in the Sinai Desert', 'sensory-calm-in-the-sinai-desert', 'Discover the therapeutic silence of the Sinai. We explore how guided desert excursions are being tailored to provide sensory-friendly wellness retreats for neurodivergent travellers looking to escape the noise.', '/uploads/podcasts/episode2.mp3', '/images/australia.png', '18:45', 'Solo Travel', 0),
('pod-3', 'Breaking Barriers: Solo Adventuring in Alexandria', 'breaking-barriers-solo-adventuring-in-alexandria', 'A deep dive into navigating the coastal city of Alexandria as a solo female traveller with low vision. Tips on tactile paths, audio-guided museum exhibits, and local sensory highlights.', '/uploads/podcasts/episode3.mp3', '/images/hero-bg.png', '31:12', 'Cultural Journeys', 0);

-- Seed Women Stories
INSERT INTO `women_stories` (`id`, `title`, `slug`, `excerpt`, `content`, `cover_image`, `category`, `author`, `read_time`) VALUES
('story-1', 'Solo Travel Through Cairo: A Wheelchair User’s Journal', 'solo-travel-through-cairo-wheelchair-journal', 'A firsthand account of navigating Cairo\'s historic sites, bustling streets, and the pyramids using a mobility device.', '<h3>A Solo Wheelchair Traveler\'s Encounter with the Pyramids</h3><p>For as long as I can remember, the Pyramids of Giza were a distant dream locked behind an intimidating barrier of sand, stairs, and steep pathways. When I finally decided to embark on a solo trip to Cairo, my wheelchair was packed alongside a healthy dose of anxiety. However, what I discovered was a city eager to open its historic gates to everyone.</p><p>Entering the Giza Plateau with a custom ramp setup and the assistance of local guides who understood my physical needs, the monumental stone structures felt closer than ever. The dry air, the golden sandstone against the clear blue sky, and the sheer majesty of the Sphinx standing guard left me breathless. It was not just about the sites, but the warmth of the people. Everywhere I went, from the busy alleys of Khan el-Khalili to the spacious halls of the new Grand Egyptian Museum, locals went out of their way to ensure I had clear sightlines and easy access.</p><h4>Top 3 Accessibility Tips for Cairo:</h4><ul><li><strong>Hire an accessible-certified local driver:</strong> Cairo\'s traffic is legendary, and having a ramp-equipped van makes all the difference.</li><li><strong>Grand Egyptian Museum (GEM):</strong> This state-of-the-art facility is built to international ADA standards, featuring elevators, tactile maps, and spacious paths.</li><li><strong>Sunset at Giza:</strong> Watch the sunset from the accessible paved viewing platform near the panorama area. It offers a majestic view of all three pyramids without needing to cross deep sand.</li></ul>', '/images/europe.png', 'Solo Travel', 'Emily Watson', '6 min read'),
('story-2', 'Navigating the Temples of Luxor with Sensory Needs', 'navigating-temples-of-luxor-sensory-needs', 'Luxor\'s ancient monuments are awe-inspiring, but can be sensory-heavy. Here is a guide to finding quiet moments in Karnak Temple.', '<h3>Luxor Temples: Navigating Sacred Spaces with Sensory Sensitivity</h3><p>The ruins of Luxor and Karnak Temples are visual spectacles, but their colossal scale, busy tour groups, and echoey columns can be overwhelming for travellers with sensory processing sensitivities. As a neurodivergent woman, my journey to Southern Egypt was a lesson in planning, presence, and finding silence within ancient stones.</p><p>I chose to visit the Karnak Temple complex at sunrise. Entering before the crowds, the great Hypostyle Hall felt like a quiet forest of stone. The early morning light cast soft shadows across the giant pillars, each carved with stories of pharaohs and gods. By timing my visits to coincide with typical lunch hours, I managed to explore the Luxor Temple in near solitude. It allowed me to appreciate the intricate hieroglyphics and connect with the spiritual history of the place without the sensory clutter of megaphones and chatter.</p><h4>My Recommendations for Sensory Balance:</h4><ul><li><strong>Noise-canceling headphones:</strong> Essential for busy entrance plazas and bazaar areas.</li><li><strong>Go early or late:</strong> Aim for 6:00 AM or 5:00 PM. Not only is the weather cooler, but the crowd level drops by 80%.</li><li><strong>Take a Felucca:</strong> A traditional sailboat ride on the Nile at sunset provides the perfect sensory reset after a long day of touring.</li></ul>', '/images/australia.png', 'Accessible Adventure', 'Sarah Jenkins', '5 min read'),
('story-3', 'A Deaf Traveller’s Guide to the Vibrant Bazaars of Khan el-Khalili', 'deaf-traveller-guide-khan-el-khalili', 'Exploring the sights, smells, and textures of Cairo\'s famous marketplace through the lens of visual and tactile connection.', '<h3>Sounds and Scents of Cairo: A Blind Traveler\'s Perspective</h3><p>Most travel brochures focus on the sights of Egypt, but the true essence of Cairo lies in its auditory and olfactory landscape. Exploring the historic Islamic Cairo as a blind woman opened my senses to a symphony of sights unheard and textures unfelt.</p><p>Navigating the medieval gates of Bab Zuwayla, the air smelled of roasted cardamom, fresh mint, and the rich earthy scent of tanned leather. Walking through Al-Muizz Street, the voice of the muezzin calling for prayer echoed from towering minarets, wrapping the ancient street in a blanket of peace. Hand-carved brass lamps felt cold and detailed under my fingertips, and the warmth of a freshly baked Aish Baladi (Egyptian flatbread) handed to me by a smiling baker was a moment of pure connection. Cairo does not require sight to be seen; it demands that you listen, smell, and touch.</p><h4>Key Insights for Visually Impaired Visitors:</h4><ul><li><strong>Tactile exploration:</strong> Many museums allow supervised touching of selected replica statues. Always ask for \'tactile guides\'.</li><li><strong>Scent mapping:</strong> Khan el-Khalili\'s spice section is a wonderful sensory journey. Local merchants are proud to let you sample incense, spices, and perfumes.</li><li><strong>Companion guides:</strong> Work with specialized operators who are trained in descriptive narration of architectural details.</li></ul>', '/images/hero-bg.png', 'Cultural Journeys', 'Amina Mansour', '7 min read');
