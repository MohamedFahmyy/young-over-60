<?php
// classes/Database.php
// Pure PHP PDO Connection Manager

class Database {
    private static $instance = null;
    private $pdo;
    private const DB_UPGRADE_VERSION = '2026_07_04';

    private function __construct() {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ];

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
            // Self-healing database upgrades for Advanced SEO with caching and locking
            $cacheDir = defined('PATH_CACHE') ? PATH_CACHE : dirname(__DIR__) . '/cache';
            if (!is_dir($cacheDir)) {
                @mkdir($cacheDir, 0755, true);
            }
            
            $flagFile = $cacheDir . '/db_upgraded_' . self::DB_UPGRADE_VERSION . '.flag';
            
            if (!file_exists($flagFile)) {
                $lockFile = $cacheDir . '/db_upgrade.lock';
                $fp = fopen($lockFile, 'c');
                if ($fp) {
                    try {
                        if (flock($fp, LOCK_EX)) {
                            // Double-check flag file inside lock to prevent race conditions
                            if (!file_exists($flagFile)) {
                                // 1. site_settings keywords
                                $checkKeywords = $this->pdo->query("SHOW COLUMNS FROM `site_settings` LIKE 'metaKeywords_en'");
                                if ($checkKeywords->rowCount() === 0) {
                                    $this->pdo->exec("ALTER TABLE `site_settings` ADD COLUMN `metaKeywords_en` VARCHAR(255) DEFAULT NULL AFTER `metaDescription_en`");
                                    $this->pdo->exec("ALTER TABLE `site_settings` ADD COLUMN `metaKeywords_ar` VARCHAR(255) DEFAULT NULL AFTER `metaKeywords_en`");
                                    $this->pdo->exec("UPDATE `site_settings` SET `metaKeywords_en` = 'travel, young, traveler, egypt, young over 60, active travel, senior travel, accessible travel, youngover60.com', `metaKeywords_ar` = 'السفر, الشباب, مسافر, مصر, شباب فوق الستين, السفر النشط, سفر كبار السن, السياحة الميسرة, youngover60.com' WHERE `id` = 1");
                                }
                                
                                // 2. custom_pages canonical URLs
                                $checkCanonical = $this->pdo->query("SHOW COLUMNS FROM `custom_pages` LIKE 'canonical_url_en'");
                                if ($checkCanonical->rowCount() === 0) {
                                    $this->pdo->exec("ALTER TABLE `custom_pages` ADD COLUMN `canonical_url_en` VARCHAR(255) DEFAULT NULL AFTER `meta_description_ar`");
                                    $this->pdo->exec("ALTER TABLE `custom_pages` ADD COLUMN `canonical_url_ar` VARCHAR(255) DEFAULT NULL AFTER `canonical_url_en`");
                                }
                                
                                // 3. media alt text columns
                                $checkMediaAlt = $this->pdo->query("SHOW COLUMNS FROM `media` LIKE 'alt_text_en'");
                                if ($checkMediaAlt->rowCount() === 0) {
                                    $this->pdo->exec("ALTER TABLE `media` ADD COLUMN `alt_text_en` VARCHAR(255) DEFAULT NULL AFTER `height`");
                                    $this->pdo->exec("ALTER TABLE `media` ADD COLUMN `alt_text_ar` VARCHAR(255) DEFAULT NULL AFTER `alt_text_en`");
                                }

                                // 4. team_members table self-healing migration
                                $this->pdo->exec("CREATE TABLE IF NOT EXISTS `team_members` (
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
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

                                // 5. Index checks for posts(slug_nl) and composite status+slug indexes
                                $checkPostSlugNl = $this->pdo->query("SHOW COLUMNS FROM `posts` LIKE 'slug_nl'");
                                if ($checkPostSlugNl->rowCount() > 0) {
                                    // 5a. idx_posts_slug_nl
                                    $checkIndexNl = $this->pdo->query("SHOW INDEX FROM `posts` WHERE Key_name = 'idx_posts_slug_nl'");
                                    if ($checkIndexNl->rowCount() === 0) {
                                        $this->pdo->exec("ALTER TABLE `posts` ADD INDEX `idx_posts_slug_nl` (`slug_nl`)");
                                    }
                                    
                                    // 5b. idx_posts_status_slug_nl
                                    $checkIdxStatusNl = $this->pdo->query("SHOW INDEX FROM `posts` WHERE Key_name = 'idx_posts_status_slug_nl'");
                                    if ($checkIdxStatusNl->rowCount() === 0) {
                                        $this->pdo->exec("ALTER TABLE `posts` ADD INDEX `idx_posts_status_slug_nl` (`status`, `slug_nl`)");
                                    }
                                }

                                // 5c. idx_posts_status_slug (on status, slug_en)
                                $checkIdxStatusEn = $this->pdo->query("SHOW INDEX FROM `posts` WHERE Key_name = 'idx_posts_status_slug'");
                                if ($checkIdxStatusEn->rowCount() === 0) {
                                    $this->pdo->exec("ALTER TABLE `posts` ADD INDEX `idx_posts_status_slug` (`status`, `slug_en`)");
                                }

                                // 5d. idx_posts_status_slug_ar
                                $checkIdxStatusAr = $this->pdo->query("SHOW INDEX FROM `posts` WHERE Key_name = 'idx_posts_status_slug_ar'");
                                if ($checkIdxStatusAr->rowCount() === 0) {
                                    $this->pdo->exec("ALTER TABLE `posts` ADD INDEX `idx_posts_status_slug_ar` (`status`, `slug_ar`)");
                                }

                                file_put_contents($flagFile, date('Y-m-d H:i:s'));
                            }
                        }
                    } finally {
                        flock($fp, LOCK_UN);
                        fclose($fp);
                        @unlink($lockFile);
                    }
                }
            }
        } catch (PDOException $e) {
            // In production, log this error rather than printing it directly
            die("Database Connection Failure: " . $e->getMessage());
        }
    }

    // Get active database instance
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Get PDO connection object
    public function getConnection() {
        return $this->pdo;
    }

    // Clone and wakeup disabled for Singleton integrity
    private function __clone() {}
    public function __wakeup() {}
}
