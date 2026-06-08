<?php
// classes/Database.php
// Pure PHP PDO Connection Manager

class Database {
    private static $instance = null;
    private $pdo;

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
            
            // Self-healing database upgrades for Advanced SEO
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
