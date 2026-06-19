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
DROP TABLE IF EXISTS `testimonials`;
DROP TABLE IF EXISTS `team_members`;
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
  `name_en` VARCHAR(255) NOT NULL,
  `name_ar` VARCHAR(255) DEFAULT NULL,
  `slug_en` VARCHAR(255) NOT NULL UNIQUE,
  `slug_ar` VARCHAR(255) DEFAULT NULL UNIQUE,
  `description_en` TEXT DEFAULT NULL,
  `description_ar` TEXT DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `alt_text_en` VARCHAR(255) DEFAULT NULL,
  `alt_text_ar` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_categories_slug_en` (`slug_en`),
  INDEX `idx_categories_slug_ar` (`slug_ar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Posts Table
CREATE TABLE `posts` (
  `id` VARCHAR(36) PRIMARY KEY,
  `title_en` VARCHAR(255) NOT NULL,
  `title_ar` VARCHAR(255) DEFAULT NULL,
  `slug_en` VARCHAR(255) NOT NULL UNIQUE,
  `slug_ar` VARCHAR(255) DEFAULT NULL UNIQUE,
  `excerpt_en` TEXT DEFAULT NULL,
  `excerpt_ar` TEXT DEFAULT NULL,
  `content_en` LONGTEXT NOT NULL,
  `content_ar` LONGTEXT DEFAULT NULL,
  `coverImage` VARCHAR(255) DEFAULT NULL,
  `alt_text_en` VARCHAR(255) DEFAULT NULL,
  `alt_text_ar` VARCHAR(255) DEFAULT NULL,
  `isFeatured` TINYINT(1) NOT NULL DEFAULT 0,
  `status` VARCHAR(50) NOT NULL DEFAULT 'DRAFT',
  `publishedAt` DATETIME DEFAULT NULL,
  `categoryId` VARCHAR(36) NOT NULL,
  `authorId` VARCHAR(36) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME DEFAULT NULL,
  INDEX `idx_posts_slug_en` (`slug_en`),
  INDEX `idx_posts_slug_ar` (`slug_ar`),
  INDEX `idx_posts_category` (`categoryId`),
  INDEX `idx_posts_status` (`status`),
  INDEX `idx_posts_published` (`publishedAt`),
  FULLTEXT INDEX `ft_posts_search_en` (`title_en`, `content_en`),
  FULLTEXT INDEX `ft_posts_search_ar` (`title_ar`, `content_ar`),
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
  `title_en` VARCHAR(255) NOT NULL,
  `title_ar` VARCHAR(255) DEFAULT NULL,
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
  `title_en` VARCHAR(255) NOT NULL,
  `title_ar` VARCHAR(255) DEFAULT NULL,
  `order` INT NOT NULL DEFAULT 0,
  `isActive` TINYINT(1) NOT NULL DEFAULT 1,
  FOREIGN KEY (`menuId`) REFERENCES `menus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Menu Links Table
CREATE TABLE `menu_links` (
  `id` VARCHAR(36) PRIMARY KEY,
  `sectionId` VARCHAR(36) NOT NULL,
  `title_en` VARCHAR(255) NOT NULL,
  `title_ar` VARCHAR(255) DEFAULT NULL,
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
  `title_en` VARCHAR(255) DEFAULT NULL,
  `title_ar` VARCHAR(255) DEFAULT NULL,
  `url` VARCHAR(255) DEFAULT NULL,
  `isActive` TINYINT(1) NOT NULL DEFAULT 1,
  FOREIGN KEY (`menuId`) REFERENCES `menus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Site Settings Table (Singleton, id=1)
CREATE TABLE `site_settings` (
  `id` INT PRIMARY KEY DEFAULT 1,
  `siteName_en` VARCHAR(255) NOT NULL DEFAULT 'Travel Without Limits',
  `siteName_ar` VARCHAR(255) DEFAULT NULL,
  `logoUrl` VARCHAR(255) DEFAULT NULL,
  `logoDarkUrl` VARCHAR(255) DEFAULT NULL,
  `heroBackgroundUrl_en` VARCHAR(255) DEFAULT NULL,
  `heroBackgroundUrl_ar` VARCHAR(255) DEFAULT NULL,
  `metaTitle_en` VARCHAR(255) DEFAULT NULL,
  `metaTitle_ar` VARCHAR(255) DEFAULT NULL,
  `metaDescription_en` TEXT DEFAULT NULL,
  `metaDescription_ar` TEXT DEFAULT NULL,
  `metaKeywords_en` VARCHAR(255) DEFAULT NULL,
  `metaKeywords_ar` VARCHAR(255) DEFAULT NULL,
  `ogImage` VARCHAR(255) DEFAULT NULL,
  `faviconUrl` VARCHAR(255) DEFAULT NULL,
  `primaryColor` VARCHAR(50) NOT NULL DEFAULT '#0F4C81',
  `secondaryColor` VARCHAR(50) NOT NULL DEFAULT '#1e3c5a',
  `accentColor` VARCHAR(50) NOT NULL DEFAULT '#D4A75C',
  `backgroundColor` VARCHAR(50) NOT NULL DEFAULT '#F8F6F2',
  `textColor` VARCHAR(50) NOT NULL DEFAULT '#1F1F1F',
  `surfaceColor` VARCHAR(50) NOT NULL DEFAULT '#ffffff',
  `borderColor` VARCHAR(50) NOT NULL DEFAULT 'rgba(0,0,0,0.05)',
  `themeMode` VARCHAR(50) NOT NULL DEFAULT 'light',
  `fontFamily` VARCHAR(255) NOT NULL DEFAULT 'Outfit',
  `fontFamilyBody` VARCHAR(255) NOT NULL DEFAULT 'Inter',
  `fontFamilyAr` VARCHAR(255) NOT NULL DEFAULT 'Cairo',
  `fontFamilyArBody` VARCHAR(255) NOT NULL DEFAULT 'Cairo',
  `fontSize` VARCHAR(50) NOT NULL DEFAULT '16px',
  `headingWeight` VARCHAR(50) NOT NULL DEFAULT '700',
  `bodyWeight` VARCHAR(50) NOT NULL DEFAULT '400',
  `letterSpacing` VARCHAR(50) NOT NULL DEFAULT 'normal',
  `lineHeight` VARCHAR(50) NOT NULL DEFAULT '1.8',
  `nav_show_podcasts` TINYINT(1) NOT NULL DEFAULT 1,
  `nav_show_stories` TINYINT(1) NOT NULL DEFAULT 1,
  `nav_show_news` TINYINT(1) NOT NULL DEFAULT 1,
  `nav_show_accessibility` TINYINT(1) NOT NULL DEFAULT 1,
  `nav_show_contact` TINYINT(1) NOT NULL DEFAULT 1,
  `hero_mode` VARCHAR(50) NOT NULL DEFAULT 'slider',
  `hero_video_en` VARCHAR(255) DEFAULT NULL,
  `hero_video_ar` VARCHAR(255) DEFAULT NULL,
  `hero_video_title_en` VARCHAR(255) DEFAULT NULL,
  `hero_video_title_ar` VARCHAR(255) DEFAULT NULL,
  `hero_video_subtitle_en` VARCHAR(255) DEFAULT NULL,
  `hero_video_subtitle_ar` VARCHAR(255) DEFAULT NULL,
  `hero_video_btn_text_en` VARCHAR(255) DEFAULT NULL,
  `hero_video_btn_text_ar` VARCHAR(255) DEFAULT NULL,
  `hero_video_btn_link` VARCHAR(255) DEFAULT NULL,
  -- About Page Content
  `about_hero_label`             VARCHAR(255) DEFAULT 'Our Story',
  `about_hero_heading_line1`     VARCHAR(255) DEFAULT 'Beyond',
  `about_hero_heading_accent`    VARCHAR(255) DEFAULT ' 60,',
  `about_hero_heading_line2`     VARCHAR(255) DEFAULT 'Life Begins Again.',
  `about_hero_quote`             VARCHAR(500) DEFAULT 'Life Begins Again.',
  `about_hero_desc`              TEXT DEFAULT NULL,
  `about_hero_desc_mobile`       TEXT DEFAULT NULL,
  `about_hero_btn1_text`         VARCHAR(255) DEFAULT 'Discover Our Story →',
  `about_hero_btn2_text`         VARCHAR(255) DEFAULT 'Meet The Team',
  `about_hero_badge_label`       VARCHAR(255) DEFAULT 'Stories Shared',
  `about_hero_badge_number`      VARCHAR(50)  DEFAULT '500+',
  `about_hero_image`             VARCHAR(500) DEFAULT '/assets/images/about-header.jpeg',
  `about_vision_label`           VARCHAR(255) DEFAULT 'About Young Over 60',
  `about_vision_heading`         VARCHAR(255) DEFAULT 'Travel Has',
  `about_vision_heading_accent`  VARCHAR(255) DEFAULT 'No Age Limit',
  `about_vision_quote`           VARCHAR(500) DEFAULT 'Beyond 60, life begins again.',
  `about_vision_card1_title`     VARCHAR(255) DEFAULT 'Young Over 60',
  `about_vision_card1_text1`     TEXT DEFAULT NULL,
  `about_vision_card1_text2`     TEXT DEFAULT NULL,
  `about_vision_card2_title`     VARCHAR(255) DEFAULT 'Our Vision: Travel Without Boundaries',
  `about_vision_card2_text1`     TEXT DEFAULT NULL,
  `about_vision_card2_text2`     TEXT DEFAULT NULL,
  `about_stat1_number`           VARCHAR(50)  DEFAULT '60+',
  `about_stat1_desc`             VARCHAR(255) DEFAULT 'Celebrating life beyond traditional limits.',
  `about_stat2_number`           VARCHAR(50)  DEFAULT '∞',
  `about_stat2_desc`             VARCHAR(255) DEFAULT 'Endless opportunities to discover the world.',
  `about_stat3_number`           VARCHAR(50)  DEFAULT '100%',
  `about_stat3_desc`             VARCHAR(255) DEFAULT 'Dedicated to accessible and inclusive travel.',
  `about_phili_label`            VARCHAR(255) DEFAULT 'Our Philosophy',
  `about_phili_heading`          VARCHAR(255) DEFAULT 'Life Begins',
  `about_phili_heading_accent`   VARCHAR(255) DEFAULT 'After 60',
  `about_phili_quote`            VARCHAR(500) DEFAULT 'True youth lives in a curious mind and an adventurous spirit.',
  `about_phili_text1`            TEXT DEFAULT NULL,
  `about_phili_text2`            TEXT DEFAULT NULL,
  `about_phili_text3`            TEXT DEFAULT NULL,
  `about_founder_name`           VARCHAR(255) DEFAULT 'Zakaria Dawoud',
  `about_founder_role`           VARCHAR(255) DEFAULT 'Founder & Project CEO',
  `about_founder_linkedin`       VARCHAR(500) DEFAULT 'https://www.linkedin.com/in/zakaria-dawoud-26902b180',
  `about_founder_quote`          TEXT DEFAULT NULL,
  `about_founder_image`          VARCHAR(500) DEFAULT '/assets/images/founder.jpeg',
  `about_contact_heading`        VARCHAR(255) DEFAULT 'Let''s Build Something',
  `about_contact_heading_accent` VARCHAR(255) DEFAULT 'Meaningful',
  `about_contact_desc`           TEXT DEFAULT NULL,
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
  `alt_text_en` VARCHAR(255) DEFAULT NULL,
  `alt_text_ar` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Podcasts Table
CREATE TABLE `podcasts` (
  `id` VARCHAR(36) PRIMARY KEY,
  `title_en` VARCHAR(255) NOT NULL,
  `title_ar` VARCHAR(255) DEFAULT NULL,
  `slug_en` VARCHAR(255) NOT NULL UNIQUE,
  `slug_ar` VARCHAR(255) DEFAULT NULL UNIQUE,
  `description_en` TEXT DEFAULT NULL,
  `description_ar` TEXT DEFAULT NULL,
  `audio_file` VARCHAR(255) NOT NULL,
  `cover_image` VARCHAR(255) DEFAULT NULL,
  `alt_text_en` VARCHAR(255) DEFAULT NULL,
  `alt_text_ar` VARCHAR(255) DEFAULT NULL,
  `duration` VARCHAR(50) DEFAULT NULL,
  `category_en` VARCHAR(255) DEFAULT NULL,
  `category_ar` VARCHAR(255) DEFAULT NULL,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_podcasts_slug_en` (`slug_en`),
  INDEX `idx_podcasts_slug_ar` (`slug_ar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Women Stories Table
CREATE TABLE `women_stories` (
  `id` VARCHAR(36) PRIMARY KEY,
  `title_en` VARCHAR(255) NOT NULL,
  `title_ar` VARCHAR(255) DEFAULT NULL,
  `slug_en` VARCHAR(255) NOT NULL UNIQUE,
  `slug_ar` VARCHAR(255) DEFAULT NULL UNIQUE,
  `excerpt_en` TEXT DEFAULT NULL,
  `excerpt_ar` TEXT DEFAULT NULL,
  `content_en` LONGTEXT NOT NULL,
  `content_ar` LONGTEXT DEFAULT NULL,
  `cover_image` VARCHAR(255) DEFAULT NULL,
  `alt_text_en` VARCHAR(255) DEFAULT NULL,
  `alt_text_ar` VARCHAR(255) DEFAULT NULL,
  `category_en` VARCHAR(100) DEFAULT NULL,
  `category_ar` VARCHAR(100) DEFAULT NULL,
  `author_en` VARCHAR(255) DEFAULT NULL,
  `author_ar` VARCHAR(255) DEFAULT NULL,
  `read_time` VARCHAR(50) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_stories_slug_en` (`slug_en`),
  INDEX `idx_stories_slug_ar` (`slug_ar`),
  FULLTEXT INDEX `ft_stories_search_en` (`title_en`, `content_en`),
  FULLTEXT INDEX `ft_stories_search_ar` (`title_ar`, `content_ar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Hero Slides Table
CREATE TABLE `hero_slides` (
  `id` VARCHAR(36) PRIMARY KEY,
  `title_en` VARCHAR(255) NOT NULL,
  `title_ar` VARCHAR(255) DEFAULT NULL,
  `subtitle_en` VARCHAR(255) DEFAULT NULL,
  `subtitle_ar` VARCHAR(255) DEFAULT NULL,
  `button_text_en` VARCHAR(255) DEFAULT NULL,
  `button_text_ar` VARCHAR(255) DEFAULT NULL,
  `button_link` VARCHAR(255) DEFAULT NULL,
  `image_en` VARCHAR(255) DEFAULT NULL,
  `image_ar` VARCHAR(255) DEFAULT NULL,
  `alt_text_en` VARCHAR(255) DEFAULT NULL,
  `alt_text_ar` VARCHAR(255) DEFAULT NULL,
  `overlay_opacity` DECIMAL(3,2) NOT NULL DEFAULT 0.50,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. Testimonials Table
CREATE TABLE `testimonials` (
  `id` VARCHAR(36) PRIMARY KEY,
  `quote_en` TEXT NOT NULL,
  `quote_ar` TEXT NOT NULL,
  `author_en` VARCHAR(255) NOT NULL,
  `author_ar` VARCHAR(255) NOT NULL,
  `role_en` VARCHAR(255) DEFAULT NULL,
  `role_ar` VARCHAR(255) DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 16. Team Members Table
CREATE TABLE `team_members` (
  `id` VARCHAR(36) PRIMARY KEY,
  `name_en` VARCHAR(255) NOT NULL,
  `name_ar` VARCHAR(255) DEFAULT NULL,
  `role_en` VARCHAR(255) NOT NULL,
  `role_ar` VARCHAR(255) DEFAULT NULL,
  `bio_en` TEXT DEFAULT NULL,
  `bio_ar` TEXT DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `linkedin_url` VARCHAR(255) DEFAULT NULL,
  `display_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ==========================================
-- SEED DATA
-- ==========================================

-- Seed Site Settings
INSERT INTO `site_settings` (
  `id`, `siteName_en`, `siteName_ar`, `logoUrl`, `heroBackgroundUrl_en`, `heroBackgroundUrl_ar`, 
  `metaTitle_en`, `metaTitle_ar`, `metaDescription_en`, `metaDescription_ar`, `metaKeywords_en`, `metaKeywords_ar`, 
  `faviconUrl`, `primaryColor`, `secondaryColor`, `accentColor`, `backgroundColor`, `textColor`, `surfaceColor`, `borderColor`, 
  `themeMode`, `fontFamily`, `fontFamilyBody`, `fontFamilyAr`, `fontFamilyArBody`, `fontSize`, `headingWeight`, `bodyWeight`, `letterSpacing`, `lineHeight`,
  `about_hero_label`, `about_hero_heading_line1`, `about_hero_heading_accent`, `about_hero_heading_line2`, `about_hero_quote`, 
  `about_hero_desc`, `about_hero_desc_mobile`, `about_hero_btn1_text`, `about_hero_btn2_text`, `about_hero_badge_label`, `about_hero_badge_number`, `about_hero_image`, 
  `about_vision_label`, `about_vision_heading`, `about_vision_heading_accent`, `about_vision_quote`, 
  `about_vision_card1_title`, `about_vision_card1_text1`, `about_vision_card1_text2`, 
  `about_vision_card2_title`, `about_vision_card2_text1`, `about_vision_card2_text2`, 
  `about_stat1_number`, `about_stat1_desc`, `about_stat2_number`, `about_stat2_desc`, `about_stat3_number`, `about_stat3_desc`, 
  `about_phili_label`, `about_phili_heading`, `about_phili_heading_accent`, `about_phili_quote`, `about_phili_text1`, `about_phili_text2`, `about_phili_text3`, 
  `about_founder_name`, `about_founder_role`, `about_founder_linkedin`, `about_founder_quote`, `about_founder_image`, 
  `about_contact_heading`, `about_contact_heading_accent`, `about_contact_desc`
) VALUES (
  1, 'Young Over 60', 'شباب فوق الستين', NULL, '/images/hero-bg.png', '/images/hero-bg.png', 
  'Young Over 60 | Active Travel & Inspiration', 'شباب فوق الستين | سفر نشط وإلهام', 
  'Premium accessible travel guides, stories, and reviews tailored for active seniors and travelers over 60.', 
  'أدلة سفر متميزة ميسرة لذوي الاحتياجات الخاصة، وقصص ومراجعات مخصصة للمسافرين النشطين فوق الستين من العمر.', 
  'travel, young, traveler, egypt, young over 60, active travel, senior travel, accessible travel, youngover60.com', 
  'السفر, الشباب, مسافر, مصر, شباب فوق الستين, السفر النشط, سفر كبار السن, السياحة الميسرة, youngover60.com', 
  '/favicon.ico', '#0F4C81', '#1e3c5a', '#D4A75C', '#F8F6F2', '#1F1F1F', '#ffffff', 'rgba(0,0,0,0.05)', 
  'light', 'Outfit', 'Inter', 'Cairo', 'Cairo', '16px', '700', '400', 'normal', '1.8',
  'Our Story', 'Beyond', ' 60,', 'Life Begins Again.', 'Life Begins Again.', 
  'The stories, people, and passion behind a community that believes every new chapter can become the greatest adventure yet. We celebrate curiosity, connection, and meaningful journeys at every stage of life.', 
  'A community celebrating curiosity, connection, and meaningful journeys at every stage of life.', 
  'Discover Our Story →', 'Meet The Team', 'Stories Shared', '500+', '/assets/images/about-header.jpeg', 
  'About Young Over 60', 'Travel Has', 'No Age Limit', 'Beyond 60, life begins again.', 
  'Young Over 60', 'We believe that travel is neither a luxury nor a privilege reserved for a specific age group. It is a fundamental right for everyone, regardless of age or physical ability.', 
  'Travel opens windows to the world\'s beauty, creates connections between cultures, and contributes to both mental and physical wellbeing.', 
  'Our Vision: Travel Without Boundaries', 'We strive to make travel accessible, comfortable, and enjoyable for everyone over 60 and for individuals with special needs.', 
  'Our mission is to challenge outdated stereotypes and prove that adventure, curiosity, and exploration have no expiration date.', 
  '60+', 'Celebrating life beyond traditional limits.', '∞', 'Endless opportunities to discover the world.', '100%', 'Dedicated to accessible and inclusive travel.', 
  'Our Philosophy', 'Life Begins', 'After 60', 'True youth lives in a curious mind and an adventurous spirit.', 
  'We reject the idea of reducing a person to the number on their passport. Sixty is not the end of the road—it is a new beginning, filled with opportunities for discovery, growth, and unforgettable experiences.', 
  'We believe that true youth is not measured by age, but by the ability to remain curious, open-minded, and eager to explore the world.', 
  'At Young Over 60, every traveller has a story worth telling and every journey is a chance to rediscover life. The world is far too beautiful to be viewed only from a window, and your right to travel never expires.', 
  'Zakaria Dawoud', 'Founder & Project CEO', 'https://www.linkedin.com/in/zakaria-dawoud-26902b180', 
  'Beyond 60, life begins again. This project was built to prove that travel has no age limit.', '/assets/images/founder.jpeg', 
  'Let\'s Build Something', 'Meaningful', 'Whether you have a question, collaboration idea, or just want to say hello — feel free to reach out.'
);


-- Seed Admin User (password is bcrypt hashed version of 'Password123')
INSERT INTO `users` (`id`, `email`, `password`, `name`, `bio`, `avatar`, `role`, `website`)
VALUES ('admin-uuid-1', 'admin@youngover60.com', '$2y$12$tanBCZuENAxM7yFBhzGIIeIJuZ9Cg0BmL2pqNp72WFRuIpD95ASRa', 'Site Admin', 'A passionate travel enthusiast dedicated to uncovering the world\'s most accessible gems. Sharing stories, tips, and guides to help everyone explore without limits.', '/uploads/admin-avatar.png', 'ADMIN', 'https://youngover60.com');

-- Seed Categories
INSERT INTO `categories` (`id`, `name_en`, `name_ar`, `slug_en`, `slug_ar`, `description_en`, `description_ar`, `image`) VALUES
('cat-australia', 'Australia', 'أستراليا', 'australia', 'أستراليا', 'Everything you need to know about Australia and accessible travel.', 'كل ما تحتاج لمعرفته حول أستراليا والسياحة الميسرة لسهولة الوصول.', '/images/australia.png'),
('cat-nsw', 'New South Wales', 'نيو ساوث ويلز', 'new-south-wales', 'نيو-ساوث-ويلز', 'Everything you need to know about New South Wales and accessible travel.', 'كل ما تحتاج لمعرفته حول نيو ساوث ويلز والسياحة الميسرة.', '/images/hero-bg.png'),
('cat-victoria', 'Victoria', 'فيكتوريا', 'victoria', 'فيكتوريا', 'Everything you need to know about Victoria and accessible travel.', 'كل ما تحتاج لمعرفته حول مقاطعة فيكتوريا الأسترالية والرحلات ميسرة الوصول.', '/images/hero-bg.png'),
('cat-queensland', 'Queensland', 'كوينزلاند', 'queensland', 'كوينزلاند', 'Everything you need to know about Queensland and accessible travel.', 'كل ما تحتاج لمعرفته حول كوينزلاند ورحلاتها الساحلية الممتازة.', '/images/hero-bg.png'),
('cat-wa', 'Western Australia', 'غرب أستراليا', 'western-australia', 'غرب-أستراليا', 'Everything you need to know about Western Australia and accessible travel.', 'كل ما تحتاج لمعرفته حول غرب أستراليا.', '/images/hero-bg.png'),
('cat-sa', 'South Australia', 'جنوب أستراليا', 'south-australia', 'جنوب-أستراليا', 'Everything you need to know about South Australia and accessible travel.', 'كل ما تحتاج لمعرفته حول جنوب أستراليا.', '/images/hero-bg.png'),
('cat-tasmania', 'Tasmania', 'تاسمانيا', 'tasmania', 'تاسمانيا', 'Everything you need to know about Tasmania and accessible travel.', 'كل ما تحتاج لمعرفته حول تسمانيا.', '/images/hero-bg.png'),
('cat-nt', 'Northern Territory', 'الإقليم الشمالي', 'northern-territory', 'الإقليم-الشمالي', 'Everything you need to know about Northern Territory and accessible travel.', 'كل ما تحتاج لمعرفته حول الإقليم الشمالي.', '/images/hero-bg.png'),
('cat-act', 'ACT', 'إقليم العاصمة الأسترالية', 'australian-capital-territory', 'إقليم-العاصمة-الأسترالية', 'Everything you need to know about ACT and accessible travel.', 'كل ما تحتاج لمعرفته حول إقليم العاصمة الأسترالية.', '/images/hero-bg.png'),
('cat-asiapacific', 'Asia Pacific', 'آسيا والمحيط الهادئ', 'asia-pacific', 'آسيا-والمحيط-الهادئ', 'Everything you need to know about Asia Pacific and accessible travel.', 'أدلة السفر الشاملة لمنطقة آسيا والمحيط الهادئ.', '/images/asia.png'),
('cat-europe', 'Europe', 'أوروبا', 'europe', 'أوروبا', 'Everything you need to know about Europe and accessible travel.', 'كل ما تحتاج لمعرفته حول أوروبا وتراثها الغني مع أدلة ميسرة الوصول.', '/images/europe.png'),
('cat-africa', 'Africa', 'أفريقيا', 'africa', 'أفريقيا', 'Everything you need to know about Africa and accessible travel.', 'كل ما تحتاج لمعرفته حول أفريقيا.', '/images/hero-bg.png'),
('cat-americas', 'Americas', 'الأمريكتان', 'americas', 'الأمريكتان', 'Everything you need to know about Americas and accessible travel.', 'أدلة وقصص السفر عبر الأمريكتين الشمالية والجنوبية.', '/images/americas.png'),
('cat-animals', 'Assistance Animals', 'حيوانات المساعدة', 'assistance-animals', 'حيوانات-المساعدة', 'Everything you need to know about Assistance Animals and accessible travel.', 'إرشادات وقوانين السفر بصحبة حيوانات الخدمة والمساعدة.', '/images/animals.png'),
('cat-vision', 'Blind / Low Vision', 'المكفوفين / ضعاف البصر', 'blind-low-vision', 'المكفوفين-وضعاف-البصر', 'Everything you need to know about Blind / Low Vision and accessible travel.', 'أدلة سياحية حسية وخدمات مخصصة لضعاف البصر والمكفوفين.', '/images/vision.png'),
('cat-deaf', 'Deaf / Hard of Hearing', 'الصم / ضعاف السمع', 'deaf-hard-of-hearing', 'الصم-وضعاف-السمع', 'Everything you need to know about Deaf / Hard of Hearing and accessible travel.', 'الصم وضغاف السمع.', '/images/hero-bg.png'),
('cat-hidden', 'Hidden Disabilities', 'الإعاقات غير الظاهرة', 'hidden-disabilities', 'الإعاقات-غير-الظاهرة', 'Everything you need to know about Hidden Disabilities and accessible travel.', 'الإعاقات غير الظاهرة.', '/images/mental.png'),
('cat-mobility', 'Physical / Mobility', 'الحركية والجسدية', 'physical-mobility', 'سهولة-الوصول-الحركي', 'Everything you need to know about Physical / Mobility and accessible travel.', 'نصائح ومصادر للمسافرين ذوي الاحتياجات الحركية ومستخدمي الكراسي المتحركة.', '/images/mobility.png'),
('cat-sensory', 'Sensory Needs', 'الاحتياجات الحسية', 'sensory-needs', 'الاحتياجات-الحسية', 'Everything you need to know about Sensory Needs and accessible travel.', 'الاحتياجات الحسية.', '/images/sensory.png'),
('cat-neurodiversity', 'Neurodiversity', 'التنوع العصبي', 'neurodiversity', 'التنوع-العصبي', 'Everything you need to know about Neurodiversity and accessible travel.', 'التنوع العصبي.', '/images/neurodiversity.png'),
('cat-accommodation', 'Accommodation', 'الإقامة الفندقية', 'accommodation', 'الإقامة-الفندقية', 'Everything you need to know about Accommodation and accessible travel.', 'الإقامة الفندقية.', '/images/hero-bg.png'),
('cat-events', 'Events & Holidays', 'الفعاليات والعطلات', 'events-holidays', 'الفعاليات-والعطلات', 'Everything you need to know about Events & Holidays and accessible travel.', 'الفعاليات والعطلات.', '/images/hero-bg.png'),
('cat-inspiration', 'Inspiration', 'الإلهام والقصص', 'inspiration', 'قصص-ملهمة', 'Everything you need to know about Inspiration and accessible travel.', 'قصص سفر حقيقية ملهمة ومحفزة للمسافرين النشطين فوق الستين.', '/images/hero-bg.png'),
('cat-reviews', 'Resources & Reviews', 'المصادر والمراجعات', 'resources-reviews', 'المصادر-والمراجعات', 'Everything you need to know about Resources & Reviews and accessible travel.', 'المصادر والمراجعات.', '/images/hero-bg.png'),
('cat-tips', 'Tips & Tricks', 'نصائح وحيل', 'tips-tricks', 'نصائح-وحيل', 'Everything you need to know about Tips & Tricks and accessible travel.', 'نصائح وحيل.', '/images/hero-bg.png'),
('cat-transport', 'Transport', 'وسائل النقل', 'transport', 'وسائل-النقل', 'Everything you need to know about Transport and accessible travel.', 'وسائل النقل.', '/images/hero-bg.png'),
('cat-gear', 'Gear & Support', 'المعدات والدعم', 'gear', 'المعدات-والدعم', 'Everything you need to know about Gear & Support and accessible travel.', 'المعدات والدعم.', '/images/hero-bg.png'),
('cat-cruises', 'Cruises', 'الرحلات البحرية', 'cruises', 'الرحلات-البحرية', 'Everything you need to know about Cruises and accessible travel.', 'رحلات بحرية فاخرة مجهزة بجميع وسائل الراحة وسهولة الوصول.', '/images/europe.png'),
('cat-family', 'Family Travel', 'السفر العائلي', 'family-travel', 'السفر-العائلي', 'Everything you need to know about Family Travel and accessible travel.', 'نصائح السفر مع العائلة والأحفاد لرحلة مريحة وممتعة للجميع.', '/images/mobility.png'),
('cat-food', 'Food & Drink', 'الطعام والشراب', 'food-drink', 'الطعام-والشراب', 'Everything you need to know about Food & Drink and accessible travel.', 'تجارب تذوق الطعام الفاخرة حول العالم والمطاعم الميسرة.', '/images/hero-bg.png'),
('cat-animal-encounters', 'Animal Encounters', 'لقاءات الحيوانات', 'animal-encounters', 'لقاءات-الحيوانات', 'Everything you need to know about Animal Encounters and accessible travel.', 'لقاءات الحيوانات.', '/images/animals.png');

-- Seed Posts
INSERT INTO `posts` (`id`, `title_en`, `title_ar`, `slug_en`, `slug_ar`, `excerpt_en`, `excerpt_ar`, `content_en`, `content_ar`, `coverImage`, `isFeatured`, `status`, `publishedAt`, `categoryId`, `authorId`) VALUES
('post-turtle', 'On Turtle Time at Mon Repos Turtle Centre', 'في رحاب السلاحف بمركز مون ريبوس للسلاحف البحرية', 'on-turtle-time-at-mon-repos-turtle-centre', 'السلاحف-البحرية-مركز-مون-ريبوس-ميسر-الوصول', 'A magical encounter where accessible nature meets wonder. Experience the nesting and hatching of marine turtles in the Bundaberg region.', 'لقاء سحري حيث تلتقي الطبيعة الميسرة بالروعة والجمال. استمتع بمشاهدة تعشيش وتفقيس السلاحف البحرية في منطقة بندابيرج الأسترالية.', '<div><p>Kerry Heaney shares a magical moment at Mon Repos Turtle Centre in Queensland, where accessible nature meets wonder. The center is a world-class facility that offers people with mobility needs a front-row seat to the cycle of life.</p><p>From the flat, accessible walkways to the dedicated viewing areas, Mon Repos ensures that everyone can enjoy the spectacular sight of marine turtles nesting and hatching on the beach.</p></div>', '<div><p>تشارك كيري هيني لحظة سحرية في مركز مون ريبوس للسلاحف البحرية في كوينزلاند، حيث تلتقي الطبيعة الميسرة بالروعة والجمال. يعد المركز مرفقاً عالمياً يتيح للأشخاص ذوي الاحتياجات الحركية فرصة فريدة لمشاهدة دورة الحياة الطبيعية عن قرب.</p><p>من الممرات المستوية الميسرة بالكامل إلى مناطق المشاهدة المخصصة، يضمن المركز تمتع الجميع بمشاهدة تعشيش السلاحف البحرية وتفقيسها على الشاطئ دون أي عوائق.</p></div>', '/images/australia.png', 1, 'PUBLISHED', '2026-01-15 20:00:00', 'cat-queensland', 'admin-uuid-1'),
('post-comedy', 'Laughs for all: Melbourne Comedy Festival 2026', 'الضحك للجميع: مهرجان ملبورن الدولي للكوميديا 2026', 'laughs-for-all-melbourne-international-comedy-festival-2026', 'مهرجان-ملبورن-الدولي-للكوميديا-ميسر-الوصول', 'With nearly 800 shows and accessibility at its heart, the Melbourne Comedy Festival proves that laughter is for everyone.', 'مع وجود ما يقرب من 800 عرض وتركيز كامل على سهولة الوصول، يثبت مهرجان ملبورن للكوميديا أن الضحك حق للجميع.', '<div><p>The Melbourne International Comedy Festival is one of the most accessible major festivals in Australia. With a dedicated accessibility team and venues and shows designed for diverse audiences, it\'s a model for inclusive entertainment.</p><p>Key features include Auslan-interpreted shows, captioning, and wheelchair accessible venues, ensuring that no one misses out on the fun.</p></div>', '<div><p>يعد مهرجان ملبورن الدولي للكوميديا واحداً من أكثر المهرجانات الكبرى سهولة في الوصول بأستراليا. فبفضل فريق العمل المخصص لسهولة الوصول والمسارح المهيأة بشكل كامل للجميع، يمثل المهرجان نموذجاً يحتذى به للترفيه الشامل.</p><p>تتضمن المزايا الرئيسية عروضاً مترجمة بلغة الإشارة، وتوفير نصوص مكتوبة، ومقاعد وسياق كامل ميسر للكراسي المتحركة لضمان عدم تفويت المتعة لأي شخص.</p></div>', '/images/australia.png', 0, 'PUBLISHED', '2026-03-22 10:00:00', 'cat-victoria', 'admin-uuid-1'),
('post-japan', 'Konichiwa, Japan: Serenity in Vibrancy', 'أهلاً بك في اليابان: سكينة في قلب الحيوية', 'konichiwa-japan', 'اليابان-سكينة-في-قلب-الحيوية-ميسرة-الوصول', 'Discovering pockets of serenity and world-class accessibility in Japan\'s vibrant cities, from Kyoto\'s temples to Tokyo\'s neon streets.', 'اكتشاف واحات من السكينة وسهولة الوصول العالمية في مدن اليابان النابضة بالحياة، من معابد كيوتو الهادئة إلى شوارع طوكيو المضيئة.', '<div><p>Japan is a masterclass in modern accessibility blending with ancient culture. Even in the busiest parts of Tokyo, the tactile paving, accessible subways, and welcoming attitude make it a favorite for travelers with limited mobility.</p><p>Experience the quiet beauty of Kyoto\'s accessible temple grounds or the high-tech inclusive facilities of Osaka.</p></div>', '<div><p>تقدم اليابان درساً في المزج بين سهولة الوصول الحديثة والثقافة العريقة. ففي أكثر أجزاء طوكيو ازدحاماً، تجعل المسارات اللمسية البارزة ووسائل النقل الميسرة والترحاب الكبير اليابان مكاناً مفضلاً للمسافرين ذوي الحركة المحدودة.</p><p>عش الهدوء الجميل لمعابد كيوتو التاريخية ميسرة الوصول أو المرافق التقنية المتطورة والشاملة في أوساكا.</p></div>', '/images/asia.png', 0, 'PUBLISHED', '2026-04-10 14:30:00', 'cat-asiapacific', 'admin-uuid-1'),
('post-kosci', 'Georgia’s Krazy Kosci Klimb', 'تحّدي كوسي كليمب مع جورجيا', 'georgias-krazy-kosci-klimb', 'تحّدي-كوسي-كليمب-تسلق-جبل-كوسيوسكو', 'More than a fundraiser, the Krazy Kosci Klimb empowers young travelers to reach the highest peaks of Australia.', 'أكثر من مجرد حملة تبرعات، يمكّن تحدي كوزي كليمب المسافرين الشباب من تسلق أعلى قمم أستراليا الشاهقة.', '<div><p>The Krazy Kosci Climb is a unique event that sees participants with disabilities take on the challenge of reaching the summit of Mount Kosciuszko. It\'s a testament to the fact that with the right support, the sky is the limit.</p><p>This annual event raises crucial funds while providing participants with an experience they will never forget.</p></div>', '<div><p>تحدي كوزي كليمب هو حدث فريد يرى المشاركين من ذوي الإعاقة يخوضون غمار تسلق قمة جبل كوسيوسكو، أعلى جبل في أستراليا. إنه دليل حي على أنه مع الدعم المناسب، لا حدود لما يمكن تحقيقه.</p><p>يجمع هذا الحدث السنوي تمويلاً حيوياً كبيراً بينما يقدم للمشاركين تجربة عمر لا تُنسى على الإطلاق.</p></div>', '/images/mobility.png', 0, 'PUBLISHED', '2026-02-18 09:15:00', 'cat-mobility', 'admin-uuid-1'),
('post-propose', 'Top 10 Wheelchair Accessible Places to Propose', 'أفضل 10 مواقع رومانسية ميسرة لطلب الزواج بالكرسي المتحرك', 'top-10-wheelchair-accessible-places-to-propose', 'افضل-١٠-مواقع-رومانسية-ميسرة-لطلب-الزواج', 'Ensure your romantic milestone is perfect for everyone with these stunning, fully-accessible locations around the globe.', 'اضمن أن تكون هذه الخطوة الرومانسية مثالية وميسرة للجميع مع هذه المواقع الخلابة والمهيأة بالكامل حول العالم.', '<div><p>A marriage proposal is a moment to be shared and remembered. These ten locations provide the perfect backdrop, from the accessible beaches of Australia to the scenic overlooks of Europe.</p><p>We\'ve curated sites that offer both breathtaking beauty and seamless access, so you can focus on the moment.</p></div>', '<div><p>طلب الزواج هو لحظة يجب مشاركتها وتذكرها مدى الحياة. تقدم هذه المواقع العشرة الخلفية المثالية، من الشواطئ الميسرة في أستراليا إلى الإطلالات الجبلية الرائعة في أوروبا.</p><p>لقد اخترنا مواقع توفر كلاً من الجمال الخلاب والوصول السهل والآمن حتى تركز فقط على اللحظة الثمينة.</p></div>', '/images/hero-bg.png', 0, 'PUBLISHED', '2026-05-01 11:00:00', 'cat-inspiration', 'admin-uuid-1'),
('post-music', 'A European summer of music and discovery', 'صيف موسيقي ممتع في أوروبا واستكشاف لا يهدأ', 'a-european-summer-of-music-and-discovery', 'صيف-موسيقي-في-اوروبا-ميسر-الوصول', 'DJ Cooper Smith shares his journey through Europe\'s most iconic music festivals, proving that music knows no limits.', 'يشارك دي جي كوبر سميث رحلته عبر أشهر المهرجانات الموسيقية في أوروبا، ليثبت أن الموسيقى لا تعرف حدوداً وعوائق.', '<div><p>DJ Cooper Smith and his mum, Bron, took on a whirlwind tour of Europe\'s festivals. From Glastonbury to the streets of Berlin, they explored how the continent is making its music scene more inclusive.</p><p>They found a world of assistive technology, dedicated viewing platforms, and a community of music lovers who embrace accessibility.</p></div>', '<div><p>خاض دي جي كوبر سميث ووالدته برون رحلة مثيرة في مهرجانات أوروبا. من غلاستونبري إلى شوارع برلين، استكشفا كيف تعمل القارة على جعل المشهد الموسيقي أكثر شمولاً وميسراً للجميع.</p><p>لقد وجدا عالمًا متكاملاً من التقنيات المساعدة، والمنصات المخصصة للمشاهدة، ومجتمعاً من عشاق الموسيقى اللذين يرحبون بسهولة الوصول.</p></div>', '/images/europe.png', 0, 'PUBLISHED', '2026-05-20 16:45:00', 'cat-europe', 'admin-uuid-1');

-- Seed Navigation Menus
INSERT INTO `menus` (`id`, `title_en`, `title_ar`, `slug`, `type`, `layoutType`, `order`, `isActive`) VALUES
('menu-destinations', 'DESTINATIONS', 'الوجهات', 'destinations', 'MEGA', 'LIST', 1, 1),
('menu-experiences', 'EXPERIENCES', 'التجارب', 'experiences', 'MEGA', 'GRID', 2, 1),
('menu-plan', 'PLAN YOUR TRIP', 'خطط لرحلتك', 'plan-your-trip', 'MEGA', 'GRID', 3, 1);

-- Seed Menu Sections
INSERT INTO `menu_sections` (`id`, `menuId`, `title_en`, `title_ar`, `order`, `isActive`) VALUES
('sec-australia', 'menu-destinations', 'AUSTRALIA', 'أستراليا', 1, 1),
('sec-americas', 'menu-destinations', 'AMERICAS', 'الأمريكتان', 2, 1),
('sec-experiences', 'menu-experiences', 'ALL EXPERIENCES', 'جميع التجارب', 1, 1),
('sec-plan', 'menu-plan', 'PLANNING HELP', 'المساعدة في التخطيط', 1, 1);

-- Seed Menu Links
INSERT INTO `menu_links` (`id`, `sectionId`, `title_en`, `title_ar`, `url`, `image`, `order`, `isActive`, `target`) VALUES
('link-act', 'sec-australia', 'ACT', 'إقليم العاصمة', '/category/australian-capital-territory', NULL, 1, 1, NULL),
('link-nsw', 'sec-australia', 'NEW SOUTH WALES', 'نيو ساوث ويلز', '/category/new-south-wales', NULL, 2, 1, NULL),
('link-nt', 'sec-australia', 'NORTHERN TERRITORY', 'الإقليم الشمالي', '/category/northern-territory', NULL, 3, 1, NULL),
('link-qld', 'sec-australia', 'QUEENSLAND', 'كوينزلاند', '/category/queensland', NULL, 4, 1, NULL),
('link-colo', 'sec-americas', 'COLORADO', 'كولورادو', '/category/americas', NULL, 1, 1, NULL),
('link-cali', 'sec-americas', 'CALIFORNIA', 'كاليفورنيا', '/category/americas', NULL, 2, 1, NULL),
('link-texas', 'sec-americas', 'TEXAS', 'تكساس', '/category/americas', NULL, 3, 1, NULL),
('link-encounters', 'sec-experiences', 'ANIMAL ENCOUNTERS', 'لقاءات الحيوانات', '/category/animal-encounters', '/images/animals.png', 1, 1, NULL),
('link-beach', 'sec-experiences', 'BEACH GETAWAYS', 'عطلات الشاطئ', '/category/australia', '/images/australia.png', 2, 1, NULL),
('link-cruises', 'sec-experiences', 'CRUISES', 'الرحلات البحرية', '/category/cruises', '/images/europe.png', 3, 1, NULL),
('link-family', 'sec-experiences', 'FAMILY TRAVEL', 'السفر العائلي', '/category/family-travel', '/images/mobility.png', 4, 1, NULL),
('link-accom', 'sec-plan', 'ACCOMMODATION', 'الإقامة الفندقية', '/category/accommodation', '/images/hero-bg.png', 1, 1, NULL),
('link-tips', 'sec-plan', 'TIPS & TRICKS', 'نصائح وحيل', '/category/tips-tricks', '/images/hero-bg.png', 2, 1, NULL),
('link-transport', 'sec-plan', 'TRANSPORT', 'وسائل النقل', '/category/transport', '/images/hero-bg.png', 3, 1, NULL),
('link-gear', 'sec-plan', 'GEAR & SUPPORT', 'المعدات والدعم', '/category/gear', '/images/hero-bg.png', 4, 1, NULL);

-- Seed Menu Banner
INSERT INTO `menu_banners` (`id`, `menuId`, `image`, `title_en`, `title_ar`, `url`, `isActive`) VALUES
('banner-dest', 'menu-destinations', '/images/hero-bg.png', 'Featured Destination', 'الوجهة المميزة', '/featured', 1);

-- Seed Hero Slides
INSERT INTO `hero_slides` (`id`, `title_en`, `title_ar`, `subtitle_en`, `subtitle_ar`, `button_text_en`, `button_text_ar`, `button_link`, `image_en`, `image_ar`, `sort_order`, `is_active`) VALUES
('slide-1', 'Young Over 60', 'شباب فوق الستين', 'Premium travel guides, reviews, and stories for active seniors', 'أدلة سفر متميزة، مراجعات وقصص ملهمة للمسنين النشطين', 'Discover More', 'اكتشف المزيد', '/destinations', '/images/hero-bg.png', '/images/hero-bg.png', 1, 1),
('slide-2', 'Inspirational Journeys', 'رحلات ملهمة', 'Real stories of adventurers breaking down physical barriers', 'قصص حقيقية لمغامرين يتحدون الحواجز الجسدية والعقبات', 'Read Stories', 'اقرأ القصص', '/news', '/images/australia.png', '/images/australia.png', 2, 1),
('slide-3', 'Explore Accessible Egypt', 'اكتشف مصر الميسرة', 'Pockets of history and magic curated for diverse requirements', 'تفاصيل تاريخية وجمال فريد منسق ليلائم متطلبات الوصول المتنوعة', 'View Guides', 'عرض الأدلة', '/women-stories', '/images/europe.png', '/images/europe.png', 3, 1);

-- Seed Podcasts
INSERT INTO `podcasts` (`id`, `title_en`, `title_ar`, `slug_en`, `slug_ar`, `description_en`, `description_ar`, `audio_file`, `cover_image`, `duration`, `category_en`, `category_ar`, `is_featured`) VALUES
('pod-1', 'Accessible Antiquities: Exploring Giza Wheelchair Access', 'الآثار ميسرة الوصول: استكشاف أهرامات الجيزة', 'accessible-antiquities-exploring-giza-wheelchair-access', 'استكشاف-اهرامات-الجيزة-سهل-الوصول', 'In this episode, we sit down with local experts and international travellers who share their firsthand experiences navigating the Giza Plateau, the pyramids, and the new Grand Egyptian Museum using mobility devices.', 'في هذه الحلقة، نلتقي بخبراء محليين ومسافرين دوليين يشاركون تجاربهم الحية في استكشاف هضبة الجيزة والأهرامات والمتحف المصري الكبير الجديد باستخدام الكراسي المتحركة والأجهزة المساعدة.', '/uploads/podcasts/episode1.mp3', '/images/europe.png', '24:18', 'Accessible Adventure', 'مغامرة ميسرة', 1),
('pod-2', 'Sensory Calm in the Sinai Desert', 'الهدوء الحسي في صحراء سيناء', 'sensory-calm-in-the-sinai-desert', 'الهدوء-الحسي-في-صحراء-سيناء', 'Discover the therapeutic silence of the Sinai. We explore how guided desert excursions are being tailored to provide sensory-friendly wellness retreats for neurodivergent travellers looking to escape the noise.', 'اكتشف الصمت العلاجي لصحراء سيناء. نستكشف كيف يتم تصميم الرحلات الصحراوية لتوفير ملاذ استرخاء حسي مريح ومناسب للمسافرين ذوي الاحتياجات الحسية الفائقة.', '/uploads/podcasts/episode2.mp3', '/images/australia.png', '18:45', 'Solo Travel', 'السفر المنفرد', 0),
('pod-3', 'Breaking Barriers: Solo Adventuring in Alexandria', 'تحدي الحواجز: مغامرة منفردة في الإسكندرية', 'breaking-barriers-solo-adventuring-in-alexandria', 'تحدي-الحواجز-مغامرة-في-الاسكندرية', 'A deep dive into navigating the coastal city of Alexandria as a solo female traveller with low vision. Tips on tactile paths, audio-guided museum exhibits, and local sensory highlights.', 'غوص عميق في استكشاف مدينة الإسكندرية الساحلية كمسافرة منفردة من ضعاف البصر. نصائح حول المسارات اللمسية والمعارض الإرشادية الصوتية والمعالم الحسية المميزة.', '/uploads/podcasts/episode3.mp3', '/images/hero-bg.png', '31:12', 'Cultural Journeys', 'رحلات ثقافية', 0);

-- Seed Women Stories
INSERT INTO `women_stories` (`id`, `title_en`, `title_ar`, `slug_en`, `slug_ar`, `excerpt_en`, `excerpt_ar`, `content_en`, `content_ar`, `cover_image`, `category_en`, `category_ar`, `author_en`, `author_ar`, `read_time`) VALUES
('story-1', 'Solo Travel Through Cairo: A Wheelchair User’s Journal', 'السفر المنفرد في القاهرة: يوميات مستخدمة كرسي متحرك', 'solo-travel-through-cairo-wheelchair-journal', 'يوميات-مسافرة-بكرسي-متحرك-في-القاهرة', 'A firsthand account of navigating Cairo\'s historic sites, bustling streets, and the pyramids using a mobility device.', 'تقرير شخصي عن استكشاف المواقع التاريخية في القاهرة، شوارعها المزدحمة، والأهرامات العظيمة باستخدام الأجهزة المساعدة وحلول سهولة الحركة.', '<h3>A Solo Wheelchair Traveler\'s Encounter with the Pyramids</h3><p>For as long as I can remember, the Pyramids of Giza were a distant dream locked behind an intimidating barrier of sand, stairs, and steep pathways. When I finally decided to embark on a solo trip to Cairo, my wheelchair was packed alongside a healthy dose of anxiety. However, what I discovered was a city eager to open its historic gates to everyone.</p><p>Entering the Giza Plateau with a custom ramp setup and the assistance of local guides who understood my physical needs, the monumental stone structures felt closer than ever. The dry air, the golden sandstone against the clear blue sky, and the sheer majesty of the Sphinx standing guard left me breathless. It was not just about the sites, but the warmth of the people. Everywhere I went, from the busy alleys of Khan el-Khalili to the spacious halls of the new Grand Egyptian Museum, locals went out of their way to ensure I had clear sightlines and easy access.</p><h4>Top 3 Accessibility Tips for Cairo:</h4><ul><li><strong>Hire an accessible-certified local driver:</strong> Cairo\'s traffic is legendary, and having a ramp-equipped van makes all the difference.</li><li><strong>Grand Egyptian Museum (GEM):</strong> This state-of-the-art facility is built to international ADA standards, featuring elevators, tactile maps, and spacious paths.</li><li><strong>Sunset at Giza:</strong> Watch the sunset from the accessible paved viewing platform near the panorama area. It offers a majestic view of all three pyramids without needing to cross deep sand.</li></ul>', '<h3>مسافرة منفردة بالكرسي المتحرك تلتقي بالأهرامات</h3><p>لطالما كانت أهرامات الجيزة حلماً بعيد المنال، يكتنفه الخوف من الرمال والدرج والمسارات الشديدة الانحدار. عندما قررت خوض هذه الرحلة إلى القاهرة، حزمت كرسيي المتحرك ومعي الكثير من القلق. ولكن، ما اكتشفته هو مدينة تفتح أبوابها التاريخية بكل ترحيب للجميع.</p><p>بمساعدة المنصات المهيأت والمرشدين المحليين اللذين تفهموا احتياجاتي الحركية، بدت الأهرامات الحجرية العملاقة أقرب من أي وقت مضى. نسيم الجو الجاف، وأحجار الجيزة الذهبية على خلفية السماء الزرقاء، ومهابة أبو الهول، كلها تفاصيل تركتني مندهشة. لم يكن الأمر يتعلق بالمواقع التاريخية فحسب، بل بدفء الناس؛ ففي كل مكان، من خان الخليلي إلى المتحف المصري الكبير، كان الجميع يبذلون قصارى جهدهم لضمان وضوح رؤيتي وسهولة حركتي.</p>', '/images/europe.png', 'Solo Travel', 'السفر المنفرد', 'Emily Watson', 'إميلي واتسون', '6 min read'),
('story-2', 'Navigating the Temples of Luxor with Sensory Needs', 'استكشاف معابد الأقصر بذوي الاحتياجات الحسية', 'navigating-temples-of-luxor-sensory-needs', 'معابد-الاقصر-وذوي-الاحتياجات-الحسية', 'Luxor\'s ancient monuments are awe-inspiring, but can be sensory-heavy. Here is a guide to finding quiet moments in Karnak Temple.', 'معالم الأقصر القديمة مذهلة، ولكنها قد تكون مزدحمة حسياً. إليك دليلك للعثور على لحظات الهدوء والسلام داخل معبد الكرنك.', '<h3>Luxor Temples: Navigating Sacred Spaces with Sensory Sensitivity</h3><p>The ruins of Luxor and Karnak Temples are visual spectacles, but their colossal scale, busy tour groups, and echoey columns can be overwhelming for travellers with sensory processing sensitivities. As a neurodivergent woman, my journey to Southern Egypt was a lesson in planning, presence, and finding silence within ancient stones.</p><p>I chose to visit the Karnak Temple complex at sunrise. Entering before the crowds, the great Hypostyle Hall felt like a quiet forest of stone. The early morning light cast soft shadows across the giant pillars, each carved with stories of pharaohs and gods. By timing my visits to coincide with typical lunch hours, I managed to explore the Luxor Temple in near solitude. It allowed me to appreciate the intricate hieroglyphics and connect with the spiritual history of the place without the sensory clutter of megaphones and chatter.</p><h4>My Recommendations for Sensory Balance:</h4><ul><li><strong>Noise-canceling headphones:</strong> Essential for busy entrance plazas and bazaar areas.</li><li><strong>Go early or late:</strong> Aim for 6:00 AM or 5:00 PM. Not only is the weather cooler, but the crowd level drops by 80%.</li><li><strong>Take a Felucca:</strong> A traditional sailboat ride on the Nile at sunset provides the perfect sensory reset after a long day of touring.</li></ul>', '<h3>معابد الأقصر: التنقل في المساحات المقدسة مع الحساسية الحسية</h3><p>أطلال معابد الأقصر والكرنك هي تحف بصرية رائعة، ولكن حجمها الضخم ومجموعاتها المزدحمة قد يسبب ضغطاً حسياً للمسافرين الذين يعانون من فرط الحساسية الحسية. كوني امرأة من ذوي الاختلاف العصبي، كانت رحلتي إلى جنوب مصر درساً في التخطيط والبحث عن الهدوء وسط الحجارة القديمة.</p><p>اخترت زيارة مجمع معابد الكرنك عند شروق الشمس. بالدخول قبل بدء المجموعات السياحية، بدت قاعة الأعمدة الكبرى كغابة هادئة من الحجر. ألقى ضوء الصباح الباكر ظلالاً ناعمة على الأعمدة الضخمة، المنقوشة بقصص الفراعنة والآلهة. بتنسيق أوقات الزيارة لتتزامن مع وقت الغداء السياحي المعتاد، تمكنت من استكشاف معبد الأقصر في هدوء تام.</p>', '/images/australia.png', 'Accessible Adventure', 'مغامرة ميسرة', 'Sarah Jenkins', 'سارة جينكينز', '5 min read'),
('story-3', 'A Deaf Traveller’s Guide to the Vibrant Bazaars of Khan el-Khalili', 'دليل المسافرة الكفيفة إلى أسواق خان الخليلي المزدحمة', 'deaf-traveller-guide-khan-el-khalili', 'دليل-المسافرة-الكفيفة-خان-الخليلي', 'Exploring the sights, smells, and textures of Cairo\'s famous marketplace through the lens of visual and tactile connection.', 'استكشاف المناظر والروائح والأقمشة في أشهر أسواق القاهرة القديمة من منظور التواصل البصري الحسي واللمسي الفريد.', '<h3>Sounds and Scents of Cairo: A Blind Traveler\'s Perspective</h3><p>Most travel brochures focus on the sights of Egypt, but the true essence of Cairo lies in its auditory and olfactory landscape. Exploring the historic Islamic Cairo as a blind woman opened my senses to a symphony of sights unheard and textures unfelt.</p><p>Navigating the medieval gates of Bab Zuwayla, the air smelled of roasted cardamom, fresh mint, and the rich earthy scent of tanned leather. Walking through Al-Muizz Street, the voice of the muezzin calling for prayer echoed from towering minarets, wrapping the ancient street in a blanket of peace. Hand-carved brass lamps felt cold and detailed under my fingertips, and the warmth of a freshly baked Aish Baladi (Egyptian flatbread) handed to me by a smiling baker was a moment of pure connection. Cairo does not require sight to be seen; it demands that you listen, smell, and touch.</p><h4>Key Insights for Visually Impaired Visitors:</h4><ul><li><strong>Tactile exploration:</strong> Many museums allow supervised touching of selected replica statues. Always ask for \'tactile guides\'.</li><li><strong>Scent mapping:</strong> Khan el-Khalili\'s spice section is a wonderful sensory journey. Local merchants are proud to let you sample incense, spices, and perfumes.</li><li><strong>Companion guides:</strong> Work with specialized operators who are trained in descriptive narration of architectural details.</li></ul>', '<h3>روائح وأصوات القاهرة: منظور مسافرة كفيفة</h3><p>تركز معظم الكتيبات السياحية على المشاهد المرئية، لكن الجوهر الحقيقي للقاهرة يكمن في أصواتها وروائحها الفريدة. استكشاف القاهرة الإسلامية كمسافرة كفيفة فتح حواسي على سمفونية متناغمة من الروائح التي لا نراها والملامس التي نتحسسها.</p><p>عند المشي بجوار بوابة باب زويلة، تفوح في الهواء رائحة الهيل المحمص والنعناع الطازج ورائحة الجلود العتيقة الدافئة. وخلال السير في شارع المعز، يتردد صدى صوت المؤذن داعياً للصلاة من المآذن الشاهقة، ليغمر الشارع القديم بعباءة من السلام والسكينة. مصابيح النحاس المنقوشة يدوياً كانت باردة ومليئة بالتفاصيل تحت أطراف أصابعي.</p>', '/images/hero-bg.png', 'Cultural Journeys', 'رحلات ثقافية', 'Amina Mansour', 'أمينة منصور', '7 min read');

-- Seed Testimonials
INSERT INTO `testimonials` (`id`, `quote_en`, `quote_ar`, `author_en`, `author_ar`, `role_en`, `role_ar`, `image`) VALUES
('test-1', 'Young Over 60 transformed the way I travel. Their detailed reports on wheelchair accessibility in historic sites let me explore Giza with total confidence.', 'لقد غيّر موقع شباب فوق الستين طريقتي في السفر بالكامل. تقاريرهم الدقيقة والمفصلة حول سهولة حركة الكراسي المتحركة في المواقع الأثرية مكنتني من زيارة الجيزة بثقة تامة.', 'Sarah Jenkins', 'سارة جينكينز', 'Active Adventurer, UK', 'مغامرة نشطة، بريطانيا', '/uploads/admin-avatar.png'),
('test-2', 'Finally, a luxury travel portal that treats accessibility as a core editorial theme rather than an afterthought. The recommendations are spot on!', 'أخيراً، بوابة سفر فاخرة تتعامل مع سهولة الوصول كفكرة تحريرية محورية وجوهرية وليست كفكرة ثانوية طارئة. توصياتهم في غاية الدقة!', 'Amina Mansour', 'أمينة منصور', 'Sensory Explorer, Egypt', 'مستكشفة حسية، مصر', '/uploads/admin-avatar.png');

-- Seed Team Members
INSERT INTO `team_members` (`id`, `name_en`, `name_ar`, `role_en`, `role_ar`, `bio_en`, `bio_ar`, `image`, `linkedin_url`, `display_order`, `is_active`) VALUES
('member-1', 'John Doe', 'جون دو', 'Lead Accessibility Guide', 'مرشد إمكانية الوصول الرائد', 'Passionate about structural and spatial accessibility, John maps and evaluates tourist destinations globally.', 'متحمس لإمكانية الوصول الهيكلية والمكانية، يقوم جون برسم الخرائط وتقييم الوجهات السياحية على مستوى العالم.', '/assets/images/founder.jpeg', 'https://www.linkedin.com/', 1, 1),
('member-2', 'Jane Smith', 'جين سميث', 'Content Strategist', 'مخطط محتوى سياحي', 'A travel writer with 10+ years of experience, specializing in senior-friendly destinations and health advice.', 'كاتبة سفر تتمتع بخبرة تزيد عن 10 سنوات، متخصصة في الوجهات المناسبة لكبار السن والنصائح الصحية.', '/assets/images/founder.jpeg', 'https://www.linkedin.com/', 2, 1);

-- ==========================================================================
-- CUSTOM PAGES CMS SCHEMAS & SEEDS
-- ==========================================================================

CREATE TABLE IF NOT EXISTS `custom_pages` (
  `id` VARCHAR(36) PRIMARY KEY,
  `slug_en` VARCHAR(255) NOT NULL UNIQUE,
  `slug_ar` VARCHAR(255) DEFAULT NULL UNIQUE,
  `title_en` VARCHAR(255) NOT NULL,
  `title_ar` VARCHAR(255) DEFAULT NULL,
  `content_en` LONGTEXT DEFAULT NULL,
  `content_ar` LONGTEXT DEFAULT NULL,
  `excerpt_en` TEXT DEFAULT NULL,
  `excerpt_ar` TEXT DEFAULT NULL,
  `template_type` VARCHAR(50) NOT NULL DEFAULT 'default',
  `sort_order` INT NOT NULL DEFAULT 0,
  `show_in_menu` TINYINT(1) NOT NULL DEFAULT 0,
  `menu_title_en` VARCHAR(255) DEFAULT NULL,
  `menu_title_ar` VARCHAR(255) DEFAULT NULL,
  `hero_title_en` VARCHAR(255) DEFAULT NULL,
  `hero_title_ar` VARCHAR(255) DEFAULT NULL,
  `hero_subtitle_en` VARCHAR(255) DEFAULT NULL,
  `hero_subtitle_ar` VARCHAR(255) DEFAULT NULL,
  `hero_image` VARCHAR(255) DEFAULT NULL,
  `hero_cta_text_en` VARCHAR(255) DEFAULT NULL,
  `hero_cta_text_ar` VARCHAR(255) DEFAULT NULL,
  `hero_cta_link` VARCHAR(255) DEFAULT NULL,
  `meta_title_en` VARCHAR(255) DEFAULT NULL,
  `meta_title_ar` VARCHAR(255) DEFAULT NULL,
  `meta_description_en` TEXT DEFAULT NULL,
  `meta_description_ar` TEXT DEFAULT NULL,
  `canonical_url_en` VARCHAR(255) DEFAULT NULL,
  `canonical_url_ar` VARCHAR(255) DEFAULT NULL,
  `featured_image` VARCHAR(255) DEFAULT NULL,
  `og_title_en` VARCHAR(255) DEFAULT NULL,
  `og_title_ar` VARCHAR(255) DEFAULT NULL,
  `og_description_en` TEXT DEFAULT NULL,
  `og_description_ar` TEXT DEFAULT NULL,
  `og_image` VARCHAR(255) DEFAULT NULL,
  `twitter_title_en` VARCHAR(255) DEFAULT NULL,
  `twitter_title_ar` VARCHAR(255) DEFAULT NULL,
  `twitter_description_en` TEXT DEFAULT NULL,
  `twitter_description_ar` TEXT DEFAULT NULL,
  `is_published` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_custom_pages_slug_en` (`slug_en`),
  INDEX `idx_custom_pages_slug_ar` (`slug_ar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `page_revisions` (
  `id` VARCHAR(36) PRIMARY KEY,
  `page_id` VARCHAR(36) NOT NULL,
  `title_en` VARCHAR(255) NOT NULL,
  `title_ar` VARCHAR(255) DEFAULT NULL,
  `content_en` LONGTEXT DEFAULT NULL,
  `content_ar` LONGTEXT DEFAULT NULL,
  `excerpt_en` TEXT DEFAULT NULL,
  `excerpt_ar` TEXT DEFAULT NULL,
  `template_type` VARCHAR(50) NOT NULL,
  `sort_order` INT NOT NULL,
  `show_in_menu` TINYINT(1) NOT NULL,
  `menu_title_en` VARCHAR(255) DEFAULT NULL,
  `menu_title_ar` VARCHAR(255) DEFAULT NULL,
  `hero_title_en` VARCHAR(255) DEFAULT NULL,
  `hero_title_ar` VARCHAR(255) DEFAULT NULL,
  `hero_subtitle_en` VARCHAR(255) DEFAULT NULL,
  `hero_subtitle_ar` VARCHAR(255) DEFAULT NULL,
  `hero_image` VARCHAR(255) DEFAULT NULL,
  `hero_cta_text_en` VARCHAR(255) DEFAULT NULL,
  `hero_cta_text_ar` VARCHAR(255) DEFAULT NULL,
  `hero_cta_link` VARCHAR(255) DEFAULT NULL,
  `meta_title_en` VARCHAR(255) DEFAULT NULL,
  `meta_title_ar` VARCHAR(255) DEFAULT NULL,
  `meta_description_en` TEXT DEFAULT NULL,
  `meta_description_ar` TEXT DEFAULT NULL,
  `featured_image` VARCHAR(255) DEFAULT NULL,
  `og_title_en` VARCHAR(255) DEFAULT NULL,
  `og_title_ar` VARCHAR(255) DEFAULT NULL,
  `og_description_en` TEXT DEFAULT NULL,
  `og_description_ar` TEXT DEFAULT NULL,
  `og_image` VARCHAR(255) DEFAULT NULL,
  `twitter_title_en` VARCHAR(255) DEFAULT NULL,
  `twitter_title_ar` VARCHAR(255) DEFAULT NULL,
  `twitter_description_en` TEXT DEFAULT NULL,
  `twitter_description_ar` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`page_id`) REFERENCES `custom_pages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `page_redirects` (
  `id` VARCHAR(36) PRIMARY KEY,
  `old_slug` VARCHAR(255) NOT NULL UNIQUE,
  `new_slug` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `custom_pages` (
  `id`, `slug_en`, `slug_ar`, `title_en`, `title_ar`, `content_en`, `content_ar`, 
  `template_type`, `sort_order`, `show_in_menu`, `menu_title_en`, `menu_title_ar`,
  `hero_title_en`, `hero_title_ar`, `hero_subtitle_en`, `hero_subtitle_ar`, `hero_image`,
  `meta_title_en`, `meta_title_ar`, `meta_description_en`, `meta_description_ar`, `is_published`
) VALUES 
('page-about', 'about-us', 'من-نحن', 'About Us', 'من نحن', 
'<h3>Who We Are</h3><p>At <strong>Young Over 60</strong>, we believe that travel is not a luxury or a privilege reserved for a specific age group. Rather, it is a fundamental right for everyone, regardless of age or physical capabilities. It is a window to the beauty of the world, a gateway to connect with diverse cultures, and a vital source of physical and psychological well-being.</p><p>We are not just a travel travel news website; we are a vibrant, active platform specifically designed for those who believe that age is merely a number that should never define one\'s spirit or ambition. We firmly believe that real life begins after sixty—a time when experience expands, passion grows, and the desire to explore matures without limits.</p><blockquote>Our Slogan: Beyond 60, life Begins Again</blockquote><h3>Our Vision: Travel Without Limits</h3><p>We strive to make travel accessible, smooth, and enjoyable for everyone over sixty and for individuals with physical or sensory requirements. We work to break down the social stereotypes that label travel as difficult or unsuitable after a certain age or for individuals with determination. On the contrary, we see in you the energy of everlasting youth, and we want to be your trusted guide to exploring the world with safety, comfort, and luxury.</p><h3>What We Offer</h3><ul><li><strong>Specialized News & Analysis:</strong> The latest trends in accessible senior travel—from comfortable airline policies to hotels equipped with the highest standards of accessibility.</li><li><strong>In-Depth Travel Reports:</strong> Destinations ranked according to strict accessibility criteria, ease of movement, and availability of appropriate medical and recreational services.</li><li><strong>Stories & Inspiration:</strong> Real-life experiences of individuals who traveled beyond sixty or overcame physical challenges, proving that adventure knows no age and no barriers.</li><li><strong>Practical Guides & Tips:</strong> From planning your trip and dealing with international health insurance, to the best transportation options and accommodations tailored for your comfort.</li></ul><h3>Our Philosophy: Life Begins After Sixty</h3><p>We refuse to let a person be reduced to a number on a passport. Sixty is not the end of the road; it is a new departure point toward a life filled with discovery. Real youth is the vibrancy of the soul and the curiosity of the mind, and these never grow old.</p><p>At Young Over 60, we see in every traveler a story worth telling, and in every journey, an opportunity to renew life. Join us—because the world is too beautiful to be viewed only from a window, and because your right to travel never expires.</p>', 
'<h3>من نحن</h3><p>في <strong>Young Over 60</strong>، نؤمن بأن السياحة ليست ترفًا ولا رفاهية مخصصة لفئة عمرية معينة، بل هي حق أساسي للجميع، بغض النظر عن العمر أو القدرات الجسدية. هي نافذة على جمال العالم، ووسيلة للتواصل مع الثقافات، وداعم أساسي للصحة النفسية والجسدية.</p><p>نحن لسنا مجرد موقع سياحي إخباري؛ نحن منصة نابضة بالحياة، صممت خصيصًا لمن يرى أن العمر مجرد رقم لا يحدد الروح ولا الطموح. نؤمن بأن الحياة الحقيقية تبدأ بعد الستين، حيث تتسع الخبرة، ويزداد الشغف، وتنضج الرغبة في الاستكشاف دون قيود.</p><blockquote>شعارنا: Beyond 60, life Begins Again</blockquote><h3>رؤيتنا: سياحة بلا حدود</h3><p>نسعى لجعل تجربة السفر متاحة وميسرة وممتعة لكل من تجاوز الستين، ولكل فرد من ذوي الاحتياجات الخاصة. نعمل على كسر الصور النمطية التي تروّج أن السفر صعب أو غير مناسب بعد عمر معين، أو لأصحاب الهمم. بالعكس، نحن نرى فيكم طاقة الشباب المستمر، ونريد أن نكون دليلكم الموثوق لاستكشاف العالم بأمان ورقي.</p><h3>ماذا نقدم؟</h3><ul><li><strong>أخبار وتحليلات متخصصة:</strong> أحدث الاتجاهات في سياحة كبار السن وذوي الاحتياجات الخاصة، من سياسات الطيران المريحة إلى الفنادق المجهزة بأعلى المعايير.</li><li><strong>تقارير سياحية معمقة:</strong> وجهات مصنفة وفق معايير إمكانية الوصول (Accessibility)، وسهولة الحركة، وتوافر الخدمات الطبية والترفيهية المناسبة.</li><li><strong>قِصص وإلهام:</strong> تجارب حقيقية لأشخاص تجاوزوا الستين أو من ذوي الاحتياجات الخاصة، وأثبتوا أن المغامرة لا تعرف عمرًا ولا عائقًا.</li><li><strong>أدلة ونصائح عملية:</strong> من كيفية التخطيط للرحلة، والتعامل مع التأمين الصحي، إلى أفضل وسائل النقل والإقامة المصممة لراحتكم.</li></ul><h3>فلسفتنا: الحياة تبدأ بعد الستين</h3><p>نحن نرفض أن يُختزل الإنسان في رقم في جواز سفره. الستون ليست نهاية الطريق، بل هي محطة انطلاق جديدة نحو حياة مليئة بالاكتشافات. فالشباب الحقيقي هو نضارة الروح وفضول العقل، وهذان لا يشيخان أبدًا.</p><p>في Young Over 60، نرى في كل مسافر قصة تستحق أن تُروى، وفي كل رحلة فرصة لتجديد الحياة. انضم إلينا، لأن العالم أكبر من أن يُرى من النافذة فقط، ولأن حقك في السياحة لا يسقط بالتقادم.</p>', 
'about-us', 1, 1, 'About Us', 'من نحن', 'About Us', 'من نحن', 'Beyond 60, life Begins Again', 'بعد الستين، تبدأ الحياة من جديد', '/images/australia.png', 
'About Our Mission and Philosophy', 'من نحن ورسالتنا وسياستنا', 'Learn more about Young Over 60, our mission, vision, everlasting youth philosophy, and committed travel guidelines.', 'تعرف على رسالتنا ورؤيتنا وفلسفتنا في شباب فوق الستين نحو سياحة بلا حدود وخدمة أصحاب الهمم وكبار السن.', 1),

('page-medical', 'medical-tourism', 'السياحة-العلاجية', 'Medical Tourism', 'السياحة العلاجية', 
'<h3>Medical Tourism for Seniors: Wellness & Healing Without Limits</h3><p>Medical and wellness tourism represents a vital avenue for active travelers over sixty looking for natural healing, rehabilitation, and dynamic rejuvenation. At <strong>Young Over 60</strong>, we believe that healthcare and preventive treatments should always be paired with luxurious, stress-free, and fully accessible travel experiences.</p><p>From the mineral-rich sulfur springs and natural clay baths of Egypt and the Middle East, to the pioneering medical clinics of Europe, we provide a comprehensive directory specifically designed around your mobility and health requirements.</p><h3>Our Core Medical Tourism Pillars:</h3><ul><li><strong>Seamless Accessibility & Medical Logistics:</strong> We highlight destinations that offer fully accessible transit, support for mobility aids, and immediate proximity to top-tier specialized medical clinics.</li><li><strong>Therapeutic & Holistic Retreats:</strong> Integrating natural treatments like hydrotherapy, mineral baths, medical massages, and senior-focused yoga programs in scenic, tranquil environments.</li><li><strong>Insurance & Care Coordination:</strong> Crucial guidance on selecting international travel medical insurance and coordinating with personal physicians to ensure a completely worry-free journey.</li></ul>', 
'<h3>السياحة العلاجية لكبار السن: استشفاء وراحة بلا حدود</h3><p>تعد السياحة العلاجية والاستشفائية أحد الروافد الهامة للمسافرين النشطين فوق الستين ومن يطمحون إلى الاستشفاء الطبيعي وتجديد الحيوية والنشاط. نؤمن في <strong>Young Over 60</strong> بأن الرعاية الصحية والوقاية الطبيعية يجب أن تقترن بتجارب سفر فاخرة ومريحة وآمنة بالكامل.</p><p>من عيون المياه الكبريتية والمنتجعات الطين الطبيعية في مصر والشرق الأوسط، إلى المصحات الطبية الرائدة في أوروبا، نقدم لكم دليلاً شاملاً مصمماً خصيصاً ليناسب متطلباتكم الحركية والصحية.</p><h3>أهم ركائز السياحة العلاجية لدينا:</h3><ul><li><strong>تيسير الوصول واللوجستيات الطبية:</strong> نركز على الوجهات التي توفر بنية تحتية ميسرة بالكامل، وتسهيلات خاصة لمستخدمي الأجهزة المساعدة، مع قرب المراكز الطبية المتخصصة.</li><li><strong>المنتجعات الصحية والاستشفائية:</strong> دمج العلاجات الطبيعية مثل العلاج بالمياه المعدنية، والتدليك العلاجي، واليوجا المخصصة لكبار السن في بيئات طبيعية خلابة وهادئة.</li><li><strong>استشارات التأمين والرعاية:</strong> نصائح وحيل حول تأمين السفر الطبي الدولي والتنسيق مع الأطباء المعالجين لضمان رحلة خالية من القلق.</li></ul>', 
'medical', 2, 1, 'Medical Tourism', 'السياحة العلاجية', 'Medical Tourism & Wellness', 'السياحة العلاجية والاستشفاء', 'Healing, Rejuvenation and Accessibility Curated for Seniors', 'علاجات طبيعية، استشفاء وسهولة وصول كاملة منسقة لكبار السن', '/images/europe.png', 
'Accessible Medical Tourism and Wellness Springs', 'السياحة العلاجية والاستشفائية الميسرة', 'Discover the best natural spas, therapeutic mineral springs, and specialized accessible medical travel guides globally.', 'اكتشف أفضل منتجعات المياه الكبريتية والمصحات الطبيعية وأدلة السفر الطبي الميسرة حول العالم.', 1);

