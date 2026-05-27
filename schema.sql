u402417573_travelfinal-- schema.sql
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
VALUES (1, 'Travel Without Limits', NULL, '/images/hero-bg.png', 'Travel Without Limits | Accessible Travel for Everyone', 'Discover accessible travel destinations, tips, and stories for people with diverse needs. Travel without boundaries.', '/favicon.ico', '#2d5a88', '#1e3c5a', '#eaeaea', '#ffffff', '#111111');

-- Seed Admin User (password is bcrypt hashed version of 'password123')
INSERT INTO `users` (`id`, `email`, `password`, `name`, `bio`, `avatar`, `role`, `website`)
VALUES ('admin-uuid-1', 'admin@travelwithoutlimits.com.au', '$2b$10$w8T0uU3HszG1X7bYlYv7l.Ksk528d84m12aG2m0O7g13KxL/uS3h.', 'Site Admin', 'A passionate travel enthusiast dedicated to uncovering the world\'s most accessible gems. Sharing stories, tips, and guides to help everyone explore without limits.', '/uploads/admin-avatar.png', 'ADMIN', 'https://travelwithoutlimits.com.au');

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
CREATE TABLE `podcasts` (
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
CREATE TABLE `women_stories` (
  `id` VARCHAR(36) PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `excerpt` TEXT DEFAULT NULL,
  `content` LONGTEXT NOT NULL,
  `cover_image` VARCHAR(255) DEFAULT NULL,
  `author` VARCHAR(255) DEFAULT NULL,
  `read_time` VARCHAR(50) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Hero Slides Table
CREATE TABLE `hero_slides` (
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
('slide-1', 'Travel Without Limits', 'Accessible travel guides, stories, and reviews', 'Discover More', '/destinations', '/images/hero-bg.png', 0.40, 1, 1),
('slide-2', 'Inspirational Journeys', 'Real stories of adventurers breaking down physical barriers', 'Read Stories', '/news', '/images/australia.png', 0.45, 2, 1),
('slide-3', 'Explore Accessible Egypt', 'Pockets of history and magic curated for diverse requirements', 'View Guides', '/women-stories', '/images/europe.png', 0.50, 3, 1);
