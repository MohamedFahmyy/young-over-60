<?php
// scratch/migrate_multilingual.php
// Indempotent Database Migration Script for Bilingual CMS (EN/AR)

require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "Connected to database successfully.\n";

    // Helper functions for safe migrations
    function columnExists($db, $table, $column) {
        $stmt = $db->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
        return $stmt->rowCount() > 0;
    }

    function safeExec($db, $sql) {
        try {
            $db->exec($sql);
            echo "SUCCESS: " . substr(trim(preg_replace('/\s+/', ' ', $sql)), 0, 80) . "...\n";
        } catch (PDOException $e) {
            echo "WARNING: " . $e->getMessage() . "\n";
        }
    }

    // 1. Alter site_settings table
    echo "\n--- Modifying site_settings ---\n";
    if (columnExists($db, 'site_settings', 'siteName') && !columnExists($db, 'site_settings', 'siteName_en')) {
        safeExec($db, "ALTER TABLE `site_settings` CHANGE `siteName` `siteName_en` VARCHAR(255) NOT NULL DEFAULT 'Travel Without Limits'");
    }
    if (!columnExists($db, 'site_settings', 'siteName_ar')) {
        safeExec($db, "ALTER TABLE `site_settings` ADD COLUMN `siteName_ar` VARCHAR(255) DEFAULT NULL AFTER `siteName_en`");
    }
    if (columnExists($db, 'site_settings', 'metaTitle') && !columnExists($db, 'site_settings', 'metaTitle_en')) {
        safeExec($db, "ALTER TABLE `site_settings` CHANGE `metaTitle` `metaTitle_en` VARCHAR(255) DEFAULT NULL");
    }
    if (!columnExists($db, 'site_settings', 'metaTitle_ar')) {
        safeExec($db, "ALTER TABLE `site_settings` ADD COLUMN `metaTitle_ar` VARCHAR(255) DEFAULT NULL AFTER `metaTitle_en`");
    }
    if (columnExists($db, 'site_settings', 'metaDescription') && !columnExists($db, 'site_settings', 'metaDescription_en')) {
        safeExec($db, "ALTER TABLE `site_settings` CHANGE `metaDescription` `metaDescription_en` TEXT DEFAULT NULL");
    }
    if (!columnExists($db, 'site_settings', 'metaDescription_ar')) {
        safeExec($db, "ALTER TABLE `site_settings` ADD COLUMN `metaDescription_ar` TEXT DEFAULT NULL AFTER `metaDescription_en`");
    }
    // Localization of Hero Background images per language
    if (columnExists($db, 'site_settings', 'heroBackgroundUrl') && !columnExists($db, 'site_settings', 'heroBackgroundUrl_en')) {
        safeExec($db, "ALTER TABLE `site_settings` CHANGE `heroBackgroundUrl` `heroBackgroundUrl_en` VARCHAR(255) DEFAULT NULL");
    }
    if (!columnExists($db, 'site_settings', 'heroBackgroundUrl_ar')) {
        safeExec($db, "ALTER TABLE `site_settings` ADD COLUMN `heroBackgroundUrl_ar` VARCHAR(255) DEFAULT NULL AFTER `heroBackgroundUrl_en`");
    }

    // Populate initial site_settings EN/AR values
    safeExec($db, "UPDATE `site_settings` SET 
        `siteName_ar` = 'شباب فوق الستين', 
        `metaTitle_ar` = 'شباب فوق الستين | سفر نشط وإلهام', 
        `metaDescription_ar` = 'أدلة سفر متميزة ميسرة لذوي الاحتياجات الخاصة، وقصص ومراجعات مخصصة للمسافرين النشطين فوق الستين من العمر.',
        `heroBackgroundUrl_en` = COALESCE(`heroBackgroundUrl_en`, '/images/hero-bg.png'),
        `heroBackgroundUrl_ar` = COALESCE(`heroBackgroundUrl_ar`, '/images/hero-bg.png')
        WHERE `id` = 1");

    // 2. Alter categories table
    echo "\n--- Modifying categories ---\n";
    if (columnExists($db, 'categories', 'name') && !columnExists($db, 'categories', 'name_en')) {
        safeExec($db, "ALTER TABLE `categories` CHANGE `name` `name_en` VARCHAR(255) NOT NULL");
    }
    if (!columnExists($db, 'categories', 'name_ar')) {
        safeExec($db, "ALTER TABLE `categories` ADD COLUMN `name_ar` VARCHAR(255) DEFAULT NULL AFTER `name_en`");
    }
    if (columnExists($db, 'categories', 'slug') && !columnExists($db, 'categories', 'slug_en')) {
        safeExec($db, "ALTER TABLE `categories` CHANGE `slug` `slug_en` VARCHAR(255) NOT NULL");
    }
    if (!columnExists($db, 'categories', 'slug_ar')) {
        safeExec($db, "ALTER TABLE `categories` ADD COLUMN `slug_ar` VARCHAR(255) DEFAULT NULL AFTER `slug_en`");
    }
    if (columnExists($db, 'categories', 'description') && !columnExists($db, 'categories', 'description_en')) {
        safeExec($db, "ALTER TABLE `categories` CHANGE `description` `description_en` TEXT DEFAULT NULL");
    }
    if (!columnExists($db, 'categories', 'description_ar')) {
        safeExec($db, "ALTER TABLE `categories` ADD COLUMN `description_ar` TEXT DEFAULT NULL AFTER `description_en`");
    }
    // Alt text for image
    if (!columnExists($db, 'categories', 'alt_text_en')) {
        safeExec($db, "ALTER TABLE `categories` ADD COLUMN `alt_text_en` VARCHAR(255) DEFAULT NULL AFTER `image`");
    }
    if (!columnExists($db, 'categories', 'alt_text_ar')) {
        safeExec($db, "ALTER TABLE `categories` ADD COLUMN `alt_text_ar` VARCHAR(255) DEFAULT NULL AFTER `alt_text_en`");
    }

    // 3. Alter posts table
    echo "\n--- Modifying posts ---\n";
    if (columnExists($db, 'posts', 'title') && !columnExists($db, 'posts', 'title_en')) {
        safeExec($db, "ALTER TABLE `posts` CHANGE `title` `title_en` VARCHAR(255) NOT NULL");
    }
    if (!columnExists($db, 'posts', 'title_ar')) {
        safeExec($db, "ALTER TABLE `posts` ADD COLUMN `title_ar` VARCHAR(255) DEFAULT NULL AFTER `title_en`");
    }
    if (columnExists($db, 'posts', 'slug') && !columnExists($db, 'posts', 'slug_en')) {
        safeExec($db, "ALTER TABLE `posts` CHANGE `slug` `slug_en` VARCHAR(255) NOT NULL");
    }
    if (!columnExists($db, 'posts', 'slug_ar')) {
        safeExec($db, "ALTER TABLE `posts` ADD COLUMN `slug_ar` VARCHAR(255) DEFAULT NULL AFTER `slug_en`");
    }
    if (columnExists($db, 'posts', 'excerpt') && !columnExists($db, 'posts', 'excerpt_en')) {
        safeExec($db, "ALTER TABLE `posts` CHANGE `excerpt` `excerpt_en` TEXT DEFAULT NULL");
    }
    if (!columnExists($db, 'posts', 'excerpt_ar')) {
        safeExec($db, "ALTER TABLE `posts` ADD COLUMN `excerpt_ar` TEXT DEFAULT NULL AFTER `excerpt_en`");
    }
    if (columnExists($db, 'posts', 'content') && !columnExists($db, 'posts', 'content_en')) {
        safeExec($db, "ALTER TABLE `posts` CHANGE `content` `content_en` LONGTEXT NOT NULL");
    }
    if (!columnExists($db, 'posts', 'content_ar')) {
        safeExec($db, "ALTER TABLE `posts` ADD COLUMN `content_ar` LONGTEXT DEFAULT NULL AFTER `content_en`");
    }
    // Alt text for coverImage
    if (!columnExists($db, 'posts', 'alt_text_en')) {
        safeExec($db, "ALTER TABLE `posts` ADD COLUMN `alt_text_en` VARCHAR(255) DEFAULT NULL AFTER `coverImage`");
    }
    if (!columnExists($db, 'posts', 'alt_text_ar')) {
        safeExec($db, "ALTER TABLE `posts` ADD COLUMN `alt_text_ar` VARCHAR(255) DEFAULT NULL AFTER `alt_text_en`");
    }

    // 4. Alter menus, menu_sections, menu_links, menu_banners tables
    echo "\n--- Modifying menus and navigation ---\n";
    if (columnExists($db, 'menus', 'title') && !columnExists($db, 'menus', 'title_en')) {
        safeExec($db, "ALTER TABLE `menus` CHANGE `title` `title_en` VARCHAR(255) NOT NULL");
    }
    if (!columnExists($db, 'menus', 'title_ar')) {
        safeExec($db, "ALTER TABLE `menus` ADD COLUMN `title_ar` VARCHAR(255) DEFAULT NULL AFTER `title_en`");
    }
    
    if (columnExists($db, 'menu_sections', 'title') && !columnExists($db, 'menu_sections', 'title_en')) {
        safeExec($db, "ALTER TABLE `menu_sections` CHANGE `title` `title_en` VARCHAR(255) NOT NULL");
    }
    if (!columnExists($db, 'menu_sections', 'title_ar')) {
        safeExec($db, "ALTER TABLE `menu_sections` ADD COLUMN `title_ar` VARCHAR(255) DEFAULT NULL AFTER `title_en`");
    }

    if (columnExists($db, 'menu_links', 'title') && !columnExists($db, 'menu_links', 'title_en')) {
        safeExec($db, "ALTER TABLE `menu_links` CHANGE `title` `title_en` VARCHAR(255) NOT NULL");
    }
    if (!columnExists($db, 'menu_links', 'title_ar')) {
        safeExec($db, "ALTER TABLE `menu_links` ADD COLUMN `title_ar` VARCHAR(255) DEFAULT NULL AFTER `title_en`");
    }

    if (columnExists($db, 'menu_banners', 'title') && !columnExists($db, 'menu_banners', 'title_en')) {
        safeExec($db, "ALTER TABLE `menu_banners` CHANGE `title` `title_en` VARCHAR(255) DEFAULT NULL");
    }
    if (!columnExists($db, 'menu_banners', 'title_ar')) {
        safeExec($db, "ALTER TABLE `menu_banners` ADD COLUMN `title_ar` VARCHAR(255) DEFAULT NULL AFTER `title_en`");
    }

    // 5. Alter podcasts table
    echo "\n--- Modifying podcasts ---\n";
    if (columnExists($db, 'podcasts', 'title') && !columnExists($db, 'podcasts', 'title_en')) {
        safeExec($db, "ALTER TABLE `podcasts` CHANGE `title` `title_en` VARCHAR(255) NOT NULL");
    }
    if (!columnExists($db, 'podcasts', 'title_ar')) {
        safeExec($db, "ALTER TABLE `podcasts` ADD COLUMN `title_ar` VARCHAR(255) DEFAULT NULL AFTER `title_en`");
    }
    if (columnExists($db, 'podcasts', 'slug') && !columnExists($db, 'podcasts', 'slug_en')) {
        safeExec($db, "ALTER TABLE `podcasts` CHANGE `slug` `slug_en` VARCHAR(255) NOT NULL");
    }
    if (!columnExists($db, 'podcasts', 'slug_ar')) {
        safeExec($db, "ALTER TABLE `podcasts` ADD COLUMN `slug_ar` VARCHAR(255) DEFAULT NULL AFTER `slug_en`");
    }
    if (columnExists($db, 'podcasts', 'description') && !columnExists($db, 'podcasts', 'description_en')) {
        safeExec($db, "ALTER TABLE `podcasts` CHANGE `description` `description_en` TEXT DEFAULT NULL");
    }
    if (!columnExists($db, 'podcasts', 'description_ar')) {
        safeExec($db, "ALTER TABLE `podcasts` ADD COLUMN `description_ar` TEXT DEFAULT NULL AFTER `description_en`");
    }
    if (columnExists($db, 'podcasts', 'category') && !columnExists($db, 'podcasts', 'category_en')) {
        safeExec($db, "ALTER TABLE `podcasts` CHANGE `category` `category_en` VARCHAR(255) DEFAULT NULL");
    }
    if (!columnExists($db, 'podcasts', 'category_ar')) {
        safeExec($db, "ALTER TABLE `podcasts` ADD COLUMN `category_ar` VARCHAR(255) DEFAULT NULL AFTER `category_en`");
    }
    // Alt text for cover_image
    if (!columnExists($db, 'podcasts', 'alt_text_en')) {
        safeExec($db, "ALTER TABLE `podcasts` ADD COLUMN `alt_text_en` VARCHAR(255) DEFAULT NULL AFTER `cover_image`");
    }
    if (!columnExists($db, 'podcasts', 'alt_text_ar')) {
        safeExec($db, "ALTER TABLE `podcasts` ADD COLUMN `alt_text_ar` VARCHAR(255) DEFAULT NULL AFTER `alt_text_en`");
    }

    // 6. Alter women_stories table
    echo "\n--- Modifying women_stories ---\n";
    if (columnExists($db, 'women_stories', 'title') && !columnExists($db, 'women_stories', 'title_en')) {
        safeExec($db, "ALTER TABLE `women_stories` CHANGE `title` `title_en` VARCHAR(255) NOT NULL");
    }
    if (!columnExists($db, 'women_stories', 'title_ar')) {
        safeExec($db, "ALTER TABLE `women_stories` ADD COLUMN `title_ar` VARCHAR(255) DEFAULT NULL AFTER `title_en`");
    }
    if (columnExists($db, 'women_stories', 'slug') && !columnExists($db, 'women_stories', 'slug_en')) {
        safeExec($db, "ALTER TABLE `women_stories` CHANGE `slug` `slug_en` VARCHAR(255) NOT NULL");
    }
    if (!columnExists($db, 'women_stories', 'slug_ar')) {
        safeExec($db, "ALTER TABLE `women_stories` ADD COLUMN `slug_ar` VARCHAR(255) DEFAULT NULL AFTER `slug_en`");
    }
    if (columnExists($db, 'women_stories', 'excerpt') && !columnExists($db, 'women_stories', 'excerpt_en')) {
        safeExec($db, "ALTER TABLE `women_stories` CHANGE `excerpt` `excerpt_en` TEXT DEFAULT NULL");
    }
    if (!columnExists($db, 'women_stories', 'excerpt_ar')) {
        safeExec($db, "ALTER TABLE `women_stories` ADD COLUMN `excerpt_ar` TEXT DEFAULT NULL AFTER `excerpt_en`");
    }
    if (columnExists($db, 'women_stories', 'content') && !columnExists($db, 'women_stories', 'content_en')) {
        safeExec($db, "ALTER TABLE `women_stories` CHANGE `content` `content_en` LONGTEXT NOT NULL");
    }
    if (!columnExists($db, 'women_stories', 'content_ar')) {
        safeExec($db, "ALTER TABLE `women_stories` ADD COLUMN `content_ar` LONGTEXT DEFAULT NULL AFTER `content_en`");
    }
    if (columnExists($db, 'women_stories', 'category') && !columnExists($db, 'women_stories', 'category_en')) {
        safeExec($db, "ALTER TABLE `women_stories` CHANGE `category` `category_en` VARCHAR(100) DEFAULT NULL");
    }
    if (!columnExists($db, 'women_stories', 'category_ar')) {
        safeExec($db, "ALTER TABLE `women_stories` ADD COLUMN `category_ar` VARCHAR(100) DEFAULT NULL AFTER `category_en`");
    }
    if (columnExists($db, 'women_stories', 'author') && !columnExists($db, 'women_stories', 'author_en')) {
        safeExec($db, "ALTER TABLE `women_stories` CHANGE `author` `author_en` VARCHAR(255) DEFAULT NULL");
    }
    if (!columnExists($db, 'women_stories', 'author_ar')) {
        safeExec($db, "ALTER TABLE `women_stories` ADD COLUMN `author_ar` VARCHAR(255) DEFAULT NULL AFTER `author_en`");
    }
    // Alt text for cover_image
    if (!columnExists($db, 'women_stories', 'alt_text_en')) {
        safeExec($db, "ALTER TABLE `women_stories` ADD COLUMN `alt_text_en` VARCHAR(255) DEFAULT NULL AFTER `cover_image`");
    }
    if (!columnExists($db, 'women_stories', 'alt_text_ar')) {
        safeExec($db, "ALTER TABLE `women_stories` ADD COLUMN `alt_text_ar` VARCHAR(255) DEFAULT NULL AFTER `alt_text_en`");
    }

    // 7. Alter hero_slides table
    echo "\n--- Modifying hero_slides ---\n";
    if (columnExists($db, 'hero_slides', 'title') && !columnExists($db, 'hero_slides', 'title_en')) {
        safeExec($db, "ALTER TABLE `hero_slides` CHANGE `title` `title_en` VARCHAR(255) NOT NULL");
    }
    if (!columnExists($db, 'hero_slides', 'title_ar')) {
        safeExec($db, "ALTER TABLE `hero_slides` ADD COLUMN `title_ar` VARCHAR(255) DEFAULT NULL AFTER `title_en`");
    }
    if (columnExists($db, 'hero_slides', 'subtitle') && !columnExists($db, 'hero_slides', 'subtitle_en')) {
        safeExec($db, "ALTER TABLE `hero_slides` CHANGE `subtitle` `subtitle_en` VARCHAR(255) DEFAULT NULL");
    }
    if (!columnExists($db, 'hero_slides', 'subtitle_ar')) {
        safeExec($db, "ALTER TABLE `hero_slides` ADD COLUMN `subtitle_ar` VARCHAR(255) DEFAULT NULL AFTER `subtitle_en`");
    }
    if (columnExists($db, 'hero_slides', 'button_text') && !columnExists($db, 'hero_slides', 'button_text_en')) {
        safeExec($db, "ALTER TABLE `hero_slides` CHANGE `button_text` `button_text_en` VARCHAR(255) DEFAULT NULL");
    }
    if (!columnExists($db, 'hero_slides', 'button_text_ar')) {
        safeExec($db, "ALTER TABLE `hero_slides` ADD COLUMN `button_text_ar` VARCHAR(255) DEFAULT NULL AFTER `button_text_en`");
    }
    // Localization of hero slides images per language
    if (columnExists($db, 'hero_slides', 'image') && !columnExists($db, 'hero_slides', 'image_en')) {
        safeExec($db, "ALTER TABLE `hero_slides` CHANGE `image` `image_en` VARCHAR(255) DEFAULT NULL");
    }
    if (!columnExists($db, 'hero_slides', 'image_ar')) {
        safeExec($db, "ALTER TABLE `hero_slides` ADD COLUMN `image_ar` VARCHAR(255) DEFAULT NULL AFTER `image_en`");
    }
    // Alt text for image
    if (!columnExists($db, 'hero_slides', 'alt_text_en')) {
        safeExec($db, "ALTER TABLE `hero_slides` ADD COLUMN `alt_text_en` VARCHAR(255) DEFAULT NULL AFTER `image_ar`");
    }
    if (!columnExists($db, 'hero_slides', 'alt_text_ar')) {
        safeExec($db, "ALTER TABLE `hero_slides` ADD COLUMN `alt_text_ar` VARCHAR(255) DEFAULT NULL AFTER `alt_text_en`");
    }

    // 8. Create testimonials table
    echo "\n--- Creating testimonials table ---\n";
    safeExec($db, "CREATE TABLE IF NOT EXISTS `testimonials` (
        `id` VARCHAR(36) PRIMARY KEY,
        `quote_en` TEXT NOT NULL,
        `quote_ar` TEXT NOT NULL,
        `author_en` VARCHAR(255) NOT NULL,
        `author_ar` VARCHAR(255) NOT NULL,
        `role_en` VARCHAR(255) DEFAULT NULL,
        `role_ar` VARCHAR(255) DEFAULT NULL,
        `image` VARCHAR(255) DEFAULT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 9. Add database optimization indexes
    echo "\n--- Adding indexes and keys ---\n";
    // Check if index exists before creating it (wrapped in safeExec to handle existing gracefully)
    safeExec($db, "ALTER TABLE `categories` ADD INDEX `idx_categories_slug_en` (`slug_en`)");
    safeExec($db, "ALTER TABLE `categories` ADD INDEX `idx_categories_slug_ar` (`slug_ar`)");
    safeExec($db, "ALTER TABLE `posts` ADD INDEX `idx_posts_slug_en` (`slug_en`)");
    safeExec($db, "ALTER TABLE `posts` ADD INDEX `idx_posts_slug_ar` (`slug_ar`)");
    safeExec($db, "ALTER TABLE `podcasts` ADD INDEX `idx_podcasts_slug_en` (`slug_en`)");
    safeExec($db, "ALTER TABLE `podcasts` ADD INDEX `idx_podcasts_slug_ar` (`slug_ar`)");
    safeExec($db, "ALTER TABLE `women_stories` ADD INDEX `idx_stories_slug_en` (`slug_en`)");
    safeExec($db, "ALTER TABLE `women_stories` ADD INDEX `idx_stories_slug_ar` (`slug_ar`)");

    // Add FULLTEXT indexes for search engine performance
    safeExec($db, "ALTER TABLE `posts` ADD FULLTEXT INDEX `ft_posts_search_en` (`title_en`, `content_en`)");
    safeExec($db, "ALTER TABLE `posts` ADD FULLTEXT INDEX `ft_posts_search_ar` (`title_ar`, `content_ar`)");
    safeExec($db, "ALTER TABLE `women_stories` ADD FULLTEXT INDEX `ft_stories_search_en` (`title_en`, `content_en`)");
    safeExec($db, "ALTER TABLE `women_stories` ADD FULLTEXT INDEX `ft_stories_search_ar` (`title_ar`, `content_ar`)");

    // 10. Seed Dynamic Translated Content & Fallback slugs
    echo "\n--- Seeding dynamic translated values ---\n";
    
    // Slugs & defaults fallback migration
    safeExec($db, "UPDATE `categories` SET `slug_en` = COALESCE(`slug_en`, `slug_en`), `slug_ar` = COALESCE(`slug_ar`, `slug_en`)");
    safeExec($db, "UPDATE `posts` SET `slug_en` = COALESCE(`slug_en`, `slug_en`), `slug_ar` = COALESCE(`slug_ar`, `slug_en`)");
    safeExec($db, "UPDATE `podcasts` SET `slug_en` = COALESCE(`slug_en`, `slug`), `slug_ar` = COALESCE(`slug_ar`, `slug`)");
    safeExec($db, "UPDATE `women_stories` SET `slug_en` = COALESCE(`slug_en`, `slug`), `slug_ar` = COALESCE(`slug_ar`, `slug`)");

    // Copy title/name to title_en/name_en if empty
    safeExec($db, "UPDATE `categories` SET `name_en` = COALESCE(`name_en`, `name_en`), `description_en` = COALESCE(`description_en`, `description_en`)");

    // Translate main categories into Arabic
    $catTranslations = [
        'australia' => ['name_ar' => 'أستراليا', 'slug_ar' => 'أستراليا', 'description_ar' => 'كل ما تحتاج لمعرفته حول أستراليا والسياحة الميسرة لسهولة الوصول.'],
        'new-south-wales' => ['name_ar' => 'نيو ساوث ويلز', 'slug_ar' => 'نيو-ساوث-ويلز', 'description_ar' => 'كل ما تحتاج لمعرفته حول نيو ساوث ويلز والسياحة الميسرة.'],
        'victoria' => ['name_ar' => 'فيكتوريا', 'slug_ar' => 'فيكتوريا', 'description_ar' => 'كل ما تحتاج لمعرفته حول مقاطعة فيكتوريا الأسترالية والرحلات ميسرة الوصول.'],
        'queensland' => ['name_ar' => 'كوينزلاند', 'slug_ar' => 'كوينزلاند', 'description_ar' => 'كل ما تحتاج لمعرفته حول كوينزلاند ورحلاتها الساحلية الممتازة.'],
        'asia-pacific' => ['name_ar' => 'آسيا والمحيط الهادئ', 'slug_ar' => 'آسيا-والمحيط-الهادئ', 'description_ar' => 'أدلة السفر الشاملة لمنطقة آسيا والمحيط الهادئ.'],
        'europe' => ['name_ar' => 'أوروبا', 'slug_ar' => 'أوروبا', 'description_ar' => 'كل ما تحتاج لمعرفته حول أوروبا وتراثها الغني مع أدلة ميسرة الوصول.'],
        'americas' => ['name_ar' => 'الأمريكتان', 'slug_ar' => 'الأمريكتان', 'description_ar' => 'أدلة وقصص السفر عبر الأمريكتين الشمالية والجنوبية.'],
        'physical-mobility' => ['name_ar' => 'الحركية والجسدية', 'slug_ar' => 'سهولة-الوصول-الحركي', 'description_ar' => 'نصائح ومصادر للمسافرين ذوي الاحتياجات الحركية ومستخدمي الكراسي المتحركة.'],
        'inspiration' => ['name_ar' => 'الإلهام والقصص', 'slug_ar' => 'قصص-ملهمة', 'description_ar' => 'قصص سفر حقيقية ملهمة ومحفزة للمسافرين النشطين فوق الستين.'],
        'assistance-animals' => ['name_ar' => 'حيوانات المساعدة', 'slug_ar' => 'حيوانات-المساعدة', 'description_ar' => 'إرشادات وقوانين السفر بصحبة حيوانات الخدمة والمساعدة.'],
        'blind-low-vision' => ['name_ar' => 'المكفوفين وضعاف البصر', 'slug_ar' => 'المكفوفين-وضعاف-البصر', 'description_ar' => 'أدلة سياحية حسية وخدمات مخصصة لضعاف البصر والمكفوفين.'],
        'cruises' => ['name_ar' => 'الرحلات البحرية', 'slug_ar' => 'الرحلات-البحرية', 'description_ar' => 'رحلات بحرية فاخرة مجهزة بجميع وسائل الراحة وسهولة الوصول.'],
        'family-travel' => ['name_ar' => 'السفر العائلي', 'slug_ar' => 'السفر-العائلي', 'description_ar' => 'نصائح السفر مع العائلة والأحفاد لرحلة مريحة وممتعة للجميع.'],
        'food-drink' => ['name_ar' => 'الطعام والشراب', 'slug_ar' => 'الطعام-والشراب', 'description_ar' => 'تجارب تذوق الطعام الفاخرة حول العالم والمطاعم الميسرة.']
    ];

    foreach ($catTranslations as $slug => $data) {
        $stmt = $db->prepare("UPDATE `categories` SET `name_ar` = :name_ar, `slug_ar` = :slug_ar, `description_ar` = :description_ar WHERE `slug_en` = :slug");
        $stmt->execute([
            'name_ar' => $data['name_ar'],
            'slug_ar' => $data['slug_ar'],
            'description_ar' => $data['description_ar'],
            'slug' => $slug
        ]);
    }

    // Seed/translate menu items into Arabic
    $menusTranslations = [
        'destinations' => 'الوجهات',
        'experiences' => 'التجارب',
        'plan-your-trip' => 'خطط لرحلتك'
    ];
    foreach ($menusTranslations as $slug => $titleAr) {
        $db->prepare("UPDATE `menus` SET `title_ar` = :title_ar WHERE `slug` = :slug")
           ->execute(['title_ar' => $titleAr, 'slug' => $slug]);
    }

    // Seed menu sections
    $sectionsTranslations = [
        'sec-australia' => 'أستراليا',
        'sec-americas' => 'الأمريكتان',
        'sec-experiences' => 'جميع التجارب',
        'sec-plan' => 'المساعدة في التخطيط'
    ];
    foreach ($sectionsTranslations as $id => $titleAr) {
        $db->prepare("UPDATE `menu_sections` SET `title_ar` = :title_ar WHERE `id` = :id")
           ->execute(['title_ar' => $titleAr, 'id' => $id]);
    }

    // Seed menu links
    $linksTranslations = [
        'link-act' => 'إقليم العاصمة',
        'link-nsw' => 'نيو ساوث ويلز',
        'link-nt' => 'الإقليم الشمالي',
        'link-qld' => 'كوينزلاند',
        'link-colo' => 'كولورادو',
        'link-cali' => 'كاليفورニア',
        'link-texas' => 'تكساس',
        'link-encounters' => 'لقاءات الحيوانات',
        'link-beach' => 'عطلات الشاطئ',
        'link-cruises' => 'الرحلات البحرية',
        'link-family' => 'السفر العائلي',
        'link-accom' => 'الإقامة الفندقية',
        'link-tips' => 'نصائح وحيل',
        'link-transport' => 'وسائل النقل',
        'link-gear' => 'المعدات والدعم'
    ];
    foreach ($linksTranslations as $id => $titleAr) {
        $db->prepare("UPDATE `menu_links` SET `title_ar` = :title_ar WHERE `id` = :id")
           ->execute(['title_ar' => $titleAr, 'id' => $id]);
    }

    // Seed/translate hero slides
    $slidesTranslations = [
        'slide-1' => [
            'title_ar' => 'شباب فوق الستين',
            'subtitle_ar' => 'أدلة سفر متميزة، مراجعات وقصص ملهمة للمسنين النشطين',
            'button_text_ar' => 'اكتشف المزيد',
            'image_en' => '/images/hero-bg.png',
            'image_ar' => '/images/hero-bg.png'
        ],
        'slide-2' => [
            'title_ar' => 'رحلات ملهمة',
            'subtitle_ar' => 'قصص حقيقية لمغامرين يتحدون الحواجز الجسدية والعقبات',
            'button_text_ar' => 'اقرأ القصص',
            'image_en' => '/images/australia.png',
            'image_ar' => '/images/australia.png'
        ],
        'slide-3' => [
            'title_ar' => 'اكتشف مصر الميسرة',
            'subtitle_ar' => 'تفاصيل تاريخية وجمال فريد منسق ليلائم متطلبات الوصول المتنوعة',
            'button_text_ar' => 'عرض الأدلة',
            'image_en' => '/images/europe.png',
            'image_ar' => '/images/europe.png'
        ]
    ];
    foreach ($slidesTranslations as $id => $data) {
        $db->prepare("UPDATE `hero_slides` SET 
            `title_ar` = :title_ar, 
            `subtitle_ar` = :subtitle_ar, 
            `button_text_ar` = :button_text_ar,
            `image_en` = :image_en,
            `image_ar` = :image_ar
            WHERE `id` = :id")
           ->execute([
               'title_ar' => $data['title_ar'],
               'subtitle_ar' => $data['subtitle_ar'],
               'button_text_ar' => $data['button_text_ar'],
               'image_en' => $data['image_en'],
               'image_ar' => $data['image_ar'],
               'id' => $id
           ]);
    }

    // Seed/translate podcasts
    $podcastTranslations = [
        'pod-1' => [
            'title_ar' => 'الآثار ميسرة الوصول: استكشاف أهرامات الجيزة',
            'slug_ar' => 'استكشاف-اهرامات-الجيزة-سهل-الوصول',
            'category_ar' => 'مغامرة ميسرة',
            'description_ar' => 'في هذه الحلقة، نلتقي بخبراء محليين ومسافرين دوليين يشاركون تجاربهم الحية في استكشاف هضبة الجيزة والأهرامات والمتحف المصري الكبير الجديد باستخدام الكراسي المتحركة والأجهزة المساعدة.'
        ],
        'pod-2' => [
            'title_ar' => 'الهدوء الحسي في صحراء سيناء',
            'slug_ar' => 'الهدوء-الحسي-في-صحراء-سيناء',
            'category_ar' => 'السفر المنفرد',
            'description_ar' => 'اكتشف الصمت العلاجي لصحراء سيناء. نستكشف كيف يتم تصميم الرحلات الصحراوية لتوفير ملاذ استرخاء حسي مريح ومناسب للمسافرين ذوي الاحتياجات الحسية الفائقة.'
        ],
        'pod-3' => [
            'title_ar' => 'تحدي الحواجز: مغامرة منفردة في الإسكندرية',
            'slug_ar' => 'تحدي-الحواجز-مغامرة-في-الاسكندرية',
            'category_ar' => 'رحلات ثقافية',
            'description_ar' => 'غوص عميق في استكشاف مدينة الإسكندرية الساحلية كمسافرة منفردة من ضعاف البصر. نصائح حول المسارات اللمسية والمعارض الإرشادية الصوتية والمعالم الحسية المميزة.'
        ]
    ];
    foreach ($podcastTranslations as $id => $data) {
        $db->prepare("UPDATE `podcasts` SET 
            `title_ar` = :title_ar, 
            `slug_ar` = :slug_ar,
            `category_ar` = :category_ar, 
            `description_ar` = :description_ar 
            WHERE `id` = :id")
           ->execute([
               'title_ar' => $data['title_ar'],
               'slug_ar' => $data['slug_ar'],
               'category_ar' => $data['category_ar'],
               'description_ar' => $data['description_ar'],
               'id' => $id
           ]);
    }

    // Seed/translate women stories
    $storyTranslations = [
        'story-1' => [
            'title_ar' => 'السفر المنفرد في القاهرة: يوميات مستخدمة كرسي متحرك',
            'slug_ar' => 'يوميات-مسافرة-بكرسي-متحرك-في-القاهرة',
            'category_ar' => 'السفر المنفرد',
            'author_ar' => 'إميلي واتسون',
            'excerpt_ar' => 'تقرير شخصي عن استكشاف المواقع التاريخية في القاهرة، شوارعها المزدحمة، والأهرامات العظيمة باستخدام الأجهزة المساعدة وحلول سهولة الحركة.',
            'content_ar' => '<h3>مسافرة منفردة بالكرسي المتحرك تلتقي بالأهرامات</h3><p>لطالما كانت أهرامات الجيزة حلماً بعيد المنال، يكتنفه الخوف من الرمال والدرج والمسارات الشديدة الانحدار. عندما قررت خوض هذه الرحلة إلى القاهرة، حزمت كرسيي المتحرك ومعي الكثير من القلق. ولكن، ما اكتشفته هو مدينة تفتح أبوابها التاريخية بكل ترحيب للجميع.</p><p>بمساعدة المنصات المهيأة والمرشدين المحليين اللذين تفهموا احتياجاتي الحركية، بدت الأهرامات الحجرية العملاقة أقرب من أي وقت مضى. نسيم الجو الجاف، وأحجار الجيزة الذهبية على خلفية السماء الزرقاء، ومهابة أبو الهول، كلها تفاصيل تركتني مندهشة. لم يكن الأمر يتعلق بالمواقع التاريخية فحسب، بل بدفء الناس؛ ففي كل مكان، من خان الخليلي إلى المتحف المصري الكبير، كان الجميع يبذلون قصارى جهدهم لضمان وضوح رؤيتي وسهولة حركتي.</p>'
        ],
        'story-2' => [
            'title_ar' => 'استكشاف معابد الأقصر بذوي الاحتياجات الحسية',
            'slug_ar' => 'معابد-الاقصر-وذوي-الاحتياجات-الحسية',
            'category_ar' => 'مغامرة ميسرة',
            'author_ar' => 'سارة جينكينز',
            'excerpt_ar' => 'معالم الأقصر القديمة مذهلة، ولكنها قد تكون مزدحمة حسياً. إليك دليلك للعثور على لحظات الهدوء والسلام داخل معبد الكرنك.',
            'content_ar' => '<h3>معابد الأقصر: التنقل في المساحات المقدسة مع الحساسية الحسية</h3><p>أطلال معابد الأقصر والكرنك هي تحف بصرية رائعة، ولكن حجمها الضخم ومجموعاتها المزدحمة قد يسبب ضغطاً حسياً للمسافرين الذين يعانون من فرط الحساسية الحسية. كوني امرأة من ذوي الاختلاف العصبي، كانت رحلتي إلى جنوب مصر درساً في التخطيط والبحث عن الهدوء وسط الحجارة القديمة.</p><p>اخترت زيارة مجمع معابد الكرنك عند شروق الشمس. بالدخول قبل بدء المجموعات السياحية، بدت قاعة الأعمدة الكبرى كغابة هادئة من الحجر. ألقى ضوء الصباح الباكر ظلالاً ناعمة على الأعمدة الضخمة، المنقوشة بقصص الفراعنة والآلهة. بتنسيق أوقات الزيارة لتتزامن مع وقت الغداء السياحي المعتاد، تمكنت من استكشاف معبد الأقصر في هدوء تام.</p>'
        ],
        'story-3' => [
            'title_ar' => 'دليل المسافرة الكفيفة إلى أسواق خان الخليلي المزدحمة',
            'slug_ar' => 'دليل-المسافرة-الكفيفة-خان-الخليلي',
            'category_ar' => 'رحلات ثقافية',
            'author_ar' => 'أمينة منصور',
            'excerpt_ar' => 'استكشاف المناظر والروائح والأقمشة في أشهر أسواق القاهرة القديمة من منظور التواصل البصري الحسي واللمسي الفريد.',
            'content_ar' => '<h3>روائح وأصوات القاهرة: منظور مسافرة كفيفة</h3><p>تركز معظم الكتيبات السياحية على المشاهد المرئية، لكن الجوهر الحقيقي للقاهرة يكمن في أصواتها وروائحها الفريدة. استكشاف القاهرة الإسلامية كمسافرة كفيفة فتح حواسي على سمفونية متناغمة من الروائح التي لا نراها والملامس التي نتحسسها.</p><p>عند المشي بجوار بوابة باب زويلة، تفوح في الهواء رائحة الهيل المحمص والنعناع الطازج ورائحة الجلود العتيقة الدافئة. وخلال السير في شارع المعز، يتردد صدى صوت المؤذن داعياً للصلاة من المآذن الشاهقة، ليغمر الشارع القديم بعباءة من السلام والسكينة. مصابيح النحاس المنقوشة يدوياً كانت باردة ومليئة بالتفاصيل تحت أطراف أصابعي.</p>'
        ]
    ];
    foreach ($storyTranslations as $id => $data) {
        $db->prepare("UPDATE `women_stories` SET 
            `title_ar` = :title_ar, 
            `slug_ar` = :slug_ar,
            `category_ar` = :category_ar, 
            `author_ar` = :author_ar, 
            `excerpt_ar` = :excerpt_ar, 
            `content_ar` = :content_ar 
            WHERE `id` = :id")
           ->execute([
               'title_ar' => $data['title_ar'],
               'slug_ar' => $data['slug_ar'],
               'category_ar' => $data['category_ar'],
               'author_ar' => $data['author_ar'],
               'excerpt_ar' => $data['excerpt_ar'],
               'content_ar' => $data['content_ar'],
               'id' => $id
           ]);
    }

    // Seed post translations
    $postTranslations = [
        'post-turtle' => [
            'title_ar' => 'في رحاب السلاحف بمركز مون ريبوس للسلاحف البحرية',
            'slug_ar' => 'السلاحف-البحرية-مركز-مون-ريبوس-ميسر-الوصول',
            'excerpt_ar' => 'لقاء سحري حيث تلتقي الطبيعة الميسرة بالروعة والجمال. استمتع بمشاهدة تعشيش وتفقيس السلاحف البحرية في منطقة بندابيرج الأسترالية.',
            'content_ar' => '<div><p>تشارك كيري هيني لحظة سحرية في مركز مون ريبوس للسلاحف البحرية في كوينزلاند، حيث تلتقي الطبيعة الميسرة بالروعة والجمال. يعد المركز مرفقاً عالمياً يتيح للأشخاص ذوي الاحتياجات الحركية فرصة فريدة لمشاهدة دورة الحياة الطبيعية عن قرب.</p><p>من الممرات المستوية الميسرة بالكامل إلى مناطق المشاهدة المخصصة، يضمن المركز تمتع الجميع بمشاهدة تعشيش السلاحف البحرية وتفقيسها على الشاطئ دون أي عوائق.</p></div>'
        ],
        'post-comedy' => [
            'title_ar' => 'الضحك للجميع: مهرجان ملبورن الدولي للكوميديا 2026',
            'slug_ar' => 'مهرجان-ملبورن-الدولي-للكوميديا-ميسر-الوصول',
            'excerpt_ar' => 'مع وجود ما يقرب من 800 عرض وتركيز كامل على سهولة الوصول، يثبت مهرجان ملبورن للكوميديا أن الضحك حق للجميع.',
            'content_ar' => '<div><p>يعد مهرجان ملبورن الدولي للكوميديا واحداً من أكثر المهرجانات الكبرى سهولة في الوصول بأستراليا. فبفضل فريق العمل المخصص لسهولة الوصول والمسارح المهيأة بشكل كامل للجميع، يمثل المهرجان نموذجاً يحتذى به للترفيه الشامل.</p><p>تتضمن المزايا الرئيسية عروضاً مترجمة بلغة الإشارة، وتوفير نصوص مكتوبة، ومقاعد وسياق كامل ميسر للكراسي المتحركة لضمان عدم تفويت المتعة لأي شخص.</p></div>'
        ],
        'post-japan' => [
            'title_ar' => 'أهلاً بك في اليابان: سكينة في قلب الحيوية',
            'slug_ar' => 'اليابان-سكينة-في-قلب-الحيوية-ميسرة-الوصول',
            'excerpt_ar' => 'اكتشاف واحات من السكينة وسهولة الوصول العالمية في مدن اليابان النابضة بالحياة، من معابد كيوتو الهادئة إلى شوارع طوكيو المضيئة.',
            'content_ar' => '<div><p>تقدم اليابان درساً في المزج بين سهولة الوصول الحديثة والثقافة العريقة. ففي أكثر أجزاء طوكيو ازدحاماً، تجعل المسارات اللمسية البارزة ووسائل النقل الميسرة والترحاب الكبير اليابان مكاناً مفضلاً للمسافرين ذوي الحركة المحدودة.</p><p>عش الهدوء الجميل لمعابد كيوتو التاريخية ميسرة الوصول أو المرافق التقنية المتطورة والشاملة في أوساكا.</p></div>'
        ],
        'post-kosci' => [
            'title_ar' => 'تحدي كوسي كليمب مع جورجيا',
            'slug_ar' => 'تحدي-كوسي-كليمب-تسلق-جبل-كوسيوسكو',
            'excerpt_ar' => 'أكثر من مجرد حملة تبرعات، يمكّن تحدي كوزي كليمب المسافرين الشباب من تسلق أعلى قمم أستراليا الشاهقة.',
            'content_ar' => '<div><p>تحدي كوزي كليمب هو حدث فريد يرى المشاركين من ذوي الإعاقة يخوضون غمار تسلق قمة جبل كوسيوسكو، أعلى جبل في أستراليا. إنه دليل حي على أنه مع الدعم المناسب، لا حدود لما يمكن تحقيقه.</p><p>يجمع هذا الحدث السنوي تمويلاً حيوياً كبيراً بينما يقدم للمشاركين تجربة عمر لا تُنسى على الإطلاق.</p></div>'
        ],
        'post-propose' => [
            'title_ar' => 'أفضل 10 مواقع رومانسية ميسرة لطلب الزواج بالكرسي المتحرك',
            'slug_ar' => 'افضل-١٠-مواقع-رومانسية-ميسرة-لطلب-الزواج',
            'excerpt_ar' => 'اضمن أن تكون هذه الخطوة الرومانسية مثالية وميسرة للجميع مع هذه المواقع الخلابة والمهيأة بالكامل حول العالم.',
            'content_ar' => '<div><p>طلب الزواج هو لحظة يجب مشاركتها وتذكرها مدى الحياة. تقدم هذه المواقع العشرة الخلفية المثالية، من الشواطئ الميسرة في أستراليا إلى الإطلالات الجبلية الرائعة في أوروبا.</p><p>لقد اخترنا مواقع توفر كلاً من الجمال الخلاب والوصول السهل والآمن حتى تركز فقط على اللحظة الثمينة.</p></div>'
        ],
        'post-music' => [
            'title_ar' => 'صيف موسيقي ممتع في أوروبا واستكشاف لا يهدأ',
            'slug_ar' => 'صيف-موسيقي-في-اوروبا-ميسر-الوصول',
            'excerpt_ar' => 'يشارك دي جي كوبر سميث رحلته عبر أشهر المهرجانات الموسيقية في أوروبا، ليثبت أن الموسيقى لا تعرف حدوداً وعوائق.',
            'content_ar' => '<div><p>خاض دي جي كوبر سميث ووالدته برون رحلة مثيرة في مهرجانات أوروبا. من غلاستونبري إلى شوارع برلين، استكشفا كيف تعمل القارة على جعل المشهد الموسيقي أكثر شمولاً وميسراً للجميع.</p><p>لقد وجدا عالمًا متكاملاً من التقنيات المساعدة، والمنصات المخصصة للمشاهدة، ومجتمعاً من عشاق الموسيقى اللذين يرحبون بسهولة الوصول.</p></div>'
        ]
    ];
    foreach ($postTranslations as $id => $data) {
        $db->prepare("UPDATE `posts` SET 
            `title_ar` = :title_ar, 
            `slug_ar` = :slug_ar,
            `excerpt_ar` = :excerpt_ar, 
            `content_ar` = :content_ar 
            WHERE `id` = :id")
           ->execute([
               'title_ar' => $data['title_ar'],
               'slug_ar' => $data['slug_ar'],
               'excerpt_ar' => $data['excerpt_ar'],
               'content_ar' => $data['content_ar'],
               'id' => $id
           ]);
    }

    // Seed testimonials
    $db->exec("DELETE FROM `testimonials` WHERE 1=1");
    $testimonials = [
        [
            'id' => 'test-1',
            'quote_en' => 'Young Over 60 transformed the way I travel. Their detailed reports on wheel-chair accessibility in historic sites let me explore Giza with total confidence.',
            'quote_ar' => 'لقد غيّر موقع شباب فوق الستين طريقتي في السفر بالكامل. تقاريرهم الدقيقة والمفصلة حول سهولة حركة الكراسي المتحركة في المواقع الأثرية مكنتني من زيارة الجيزة بثقة تامة.',
            'author_en' => 'Sarah Jenkins',
            'author_ar' => 'سارة جينكينز',
            'role_en' => 'Active Adventurer, UK',
            'role_ar' => 'مغامرة نشطة، بريطانيا',
            'image' => '/uploads/admin-avatar.png'
        ],
        [
            'id' => 'test-2',
            'quote_en' => 'Finally, a luxury travel portal that treats accessibility as a core editorial theme rather than an afterthought. The recommendations are spot on!',
            'quote_ar' => 'أخيراً، بوابة سفر فاخرة تتعامل مع سهولة الوصول كفكرة تحريرية محورية وجوهرية وليست كفكرة ثانوية طارئة. توصياتهم في غاية الدقة!',
            'author_en' => 'Amina Mansour',
            'author_ar' => 'أمينة منصور',
            'role_en' => 'Sensory Explorer, Egypt',
            'role_ar' => 'مستكشفة حسية، مصر',
            'image' => '/uploads/admin-avatar.png'
        ]
    ];
    foreach ($testimonials as $t) {
        $db->prepare("INSERT INTO `testimonials` (`id`, `quote_en`, `quote_ar`, `author_en`, `author_ar`, `role_en`, `role_ar`, `image`) 
            VALUES (:id, :quote_en, :quote_ar, :author_en, :author_ar, :role_en, :role_ar, :image)")
           ->execute($t);
    }

    echo "\nMigrations completed successfully.\n";

} catch (PDOException $e) {
    echo "ERROR: Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
