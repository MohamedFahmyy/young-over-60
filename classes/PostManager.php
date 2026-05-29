<?php
// classes/PostManager.php
// Post & Content Model Service Layer (with Caching & Soft Deletes)

class PostManager {
    private $db;
    private $cacheEnabled = true;
    private $cacheTtl = 3600; // 1 Hour default Cache lifetime

    public function __construct($db = null) {
        if ($db instanceof PDO) {
            $this->db = $db;
        } else {
            $this->db = Database::getInstance()->getConnection();
        }
    }

    // --- Simple Cache Helpers ---
    private function getCacheFilename($key) {
        $safeKey = '';
        if (isset($key) && is_string($key)) {
            $safeKey = $key;
        } elseif (isset($key)) {
            $safeKey = (string)$key;
        }
        return PATH_CACHE . '/twl_' . md5($safeKey) . '.json';
    }

    public function cacheGet($key) {
        if (!$this->cacheEnabled) return null;
        $file = $this->getCacheFilename($key);
        if (file_exists($file) && (time() - filemtime($file) < $this->cacheTtl)) {
            $content = file_get_contents($file);
            return json_decode($content, true);
        }
        return null;
    }

    public function cacheSet($key, $data) {
        if (!$this->cacheEnabled) return;
        $file = $this->getCacheFilename($key);
        file_put_contents($file, json_encode($data));
    }

    public function clearCache() {
        $files = glob(PATH_CACHE . '/twl_*.json');
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    // --- Posts Management ---
    public function getPosts($params = []) {
        $page = isset($params['page']) ? (int)$params['page'] : 1;
        $limit = isset($params['limit']) ? (int)$params['limit'] : 10;
        $offset = ($page - 1) * $limit;
        $categoryId = $params['categoryId'] ?? null;
        $status = $params['status'] ?? null;
        $isFeatured = isset($params['isFeatured']) ? (bool)$params['isFeatured'] : null;
        $search = $params['search'] ?? null;
        $includeDeleted = $params['includeDeleted'] ?? false;

        // Build Cache Key
        $safeSearch = '';
        if (isset($search) && is_string($search)) {
            $safeSearch = $search;
        } elseif (isset($search)) {
            $safeSearch = (string)$search;
        }
        $lang = defined('CURRENT_LANG') ? CURRENT_LANG : 'en';
        $cacheKey = "posts_list_lang_{$lang}_p_{$page}_l_{$limit}_c_{$categoryId}_s_{$status}_f_{$isFeatured}_q_" . md5($safeSearch) . "_d_{$includeDeleted}";
        $cached = $this->cacheGet($cacheKey);
        if ($cached) return $cached;

        // Base Query selecting bilingual columns
        $sql = "SELECT p.*, 
                       c.name_en as categoryName_en, c.name_ar as categoryName_ar, 
                       c.slug_en as categorySlug_en, c.slug_ar as categorySlug_ar, 
                       u.name as authorName 
                FROM posts p 
                INNER JOIN categories c ON p.categoryId = c.id 
                LEFT JOIN users u ON p.authorId = u.id 
                WHERE 1=1";
        
        $countSql = "SELECT COUNT(*) FROM posts p 
                     INNER JOIN categories c ON p.categoryId = c.id
                     WHERE 1=1";
        $binds = [];

        // Exclude soft deleted posts by default
        if (!$includeDeleted) {
            $sql .= " AND p.deleted_at IS NULL";
            $countSql .= " AND p.deleted_at IS NULL";
        }

        if ($categoryId) {
            $sql .= " AND (p.categoryId = :categoryId OR c.slug_en = :categorySlugEn OR c.slug_ar = :categorySlugAr)";
            $countSql .= " AND (p.categoryId = :categoryId OR c.slug_en = :categorySlugEn OR c.slug_ar = :categorySlugAr)";
            $binds['categoryId'] = $categoryId;
            $binds['categorySlugEn'] = $categoryId;
            $binds['categorySlugAr'] = $categoryId;
        }

        if ($status) {
            $sql .= " AND p.status = :status";
            $countSql .= " AND p.status = :status";
            $binds['status'] = $status;
        }

        if ($isFeatured !== null) {
            $sql .= " AND p.isFeatured = :isFeatured";
            $countSql .= " AND p.isFeatured = :isFeatured";
            $binds['isFeatured'] = $isFeatured ? 1 : 0;
        }

        if ($search) {
            // Search active language fields
            $sql .= " AND (p.title_{$lang} LIKE :search1 OR p.excerpt_{$lang} LIKE :search2 OR p.content_{$lang} LIKE :search3)";
            $countSql .= " AND (p.title_{$lang} LIKE :search1 OR p.excerpt_{$lang} LIKE :search2 OR p.content_{$lang} LIKE :search3)";
            $binds['search1'] = '%' . $search . '%';
            $binds['search2'] = '%' . $search . '%';
            $binds['search3'] = '%' . $search . '%';
        }

        // Calculate count
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($binds);
        $total = (int)$countStmt->fetchColumn();

        // Add sorting and pagination
        $sql .= " ORDER BY p.isFeatured DESC, p.publishedAt DESC, p.created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        
        // Re-bind all parameters for execution
        foreach ($binds as $key => $val) {
            $stmt->bindValue(':' . $key, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $posts = $stmt->fetchAll();

        $totalPages = ceil($total / $limit);
        $result = [
            'data' => $posts,
            'meta' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'totalPages' => $totalPages > 0 ? $totalPages : 1
            ]
        ];

        $this->cacheSet($cacheKey, $result);
        return $result;
    }

    public function getPostBySlug($slug, $isAdmin = false) {
        $lang = defined('CURRENT_LANG') ? CURRENT_LANG : 'en';
        $cacheKey = "post_slug_{$slug}_lang_{$lang}_a_{$isAdmin}";
        $cached = $this->cacheGet($cacheKey);
        if ($cached) return $cached;

        $sql = "SELECT p.*, 
                       c.name_en as categoryName_en, c.name_ar as categoryName_ar, 
                       c.slug_en as categorySlug_en, c.slug_ar as categorySlug_ar, 
                       u.name as authorName, u.bio as authorBio, u.avatar as authorAvatar, 
                       u.twitter as authorTwitter, u.facebook as authorFacebook, 
                       u.instagram as authorInstagram, u.linkedin as authorLinkedin, u.website as authorWebsite
                FROM posts p 
                INNER JOIN categories c ON p.categoryId = c.id 
                LEFT JOIN users u ON p.authorId = u.id 
                WHERE (p.slug_en = :slug_en OR p.slug_ar = :slug_ar) AND p.deleted_at IS NULL";
        
        if (!$isAdmin) {
            $sql .= " AND p.status = 'PUBLISHED'";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['slug_en' => $slug, 'slug_ar' => $slug]);
        $post = $stmt->fetch();

        if ($post) {
            $this->cacheSet($cacheKey, $post);
        }
        return $post;
    }

    public function getPostById($id) {
        $stmt = $this->db->prepare("SELECT * FROM posts WHERE id = :id AND deleted_at IS NULL");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function createPost($data) {
        $this->clearCache();
        
        $id = isset($data['id']) ? $data['id'] : bin2hex(random_bytes(16)); // UUID fallback
        $publishedAt = ($data['status'] === 'PUBLISHED') ? date('Y-m-d H:i:s') : null;

        $sql = "INSERT INTO posts (id, title_en, title_ar, slug_en, slug_ar, excerpt_en, excerpt_ar, content_en, content_ar, coverImage, alt_text_en, alt_text_ar, isFeatured, status, publishedAt, categoryId, authorId)
                VALUES (:id, :title_en, :title_ar, :slug_en, :slug_ar, :excerpt_en, :excerpt_ar, :content_en, :content_ar, :coverImage, :alt_text_en, :alt_text_ar, :isFeatured, :status, :publishedAt, :categoryId, :authorId)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title_en' => $data['title_en'],
            'title_ar' => $data['title_ar'] ?? null,
            'slug_en' => $data['slug_en'],
            'slug_ar' => $data['slug_ar'] ?? null,
            'excerpt_en' => $data['excerpt_en'] ?? null,
            'excerpt_ar' => $data['excerpt_ar'] ?? null,
            'content_en' => $data['content_en'],
            'content_ar' => $data['content_ar'] ?? null,
            'coverImage' => $data['coverImage'] ?? null,
            'alt_text_en' => $data['alt_text_en'] ?? null,
            'alt_text_ar' => $data['alt_text_ar'] ?? null,
            'isFeatured' => !empty($data['isFeatured']) ? 1 : 0,
            'status' => $data['status'] ?? 'DRAFT',
            'publishedAt' => $publishedAt,
            'categoryId' => $data['categoryId'],
            'authorId' => $_SESSION['admin_id'] ?? null
        ]);
    }

    public function updatePost($id, $data) {
        $this->clearCache();

        $post = $this->getPostById($id);
        if (!$post) return false;

        $status = $data['status'] ?? $post['status'];
        $publishedAt = $post['publishedAt'];
        if ($status === 'PUBLISHED' && empty($publishedAt)) {
            $publishedAt = date('Y-m-d H:i:s');
        } elseif ($status === 'DRAFT') {
            $publishedAt = null;
        }

        $sql = "UPDATE posts SET 
                title_en = :title_en, 
                title_ar = :title_ar, 
                slug_en = :slug_en, 
                slug_ar = :slug_ar, 
                excerpt_en = :excerpt_en, 
                excerpt_ar = :excerpt_ar, 
                content_en = :content_en, 
                content_ar = :content_ar, 
                coverImage = :coverImage, 
                alt_text_en = :alt_text_en,
                alt_text_ar = :alt_text_ar,
                isFeatured = :isFeatured, 
                status = :status, 
                publishedAt = :publishedAt, 
                categoryId = :categoryId
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title_en' => $data['title_en'],
            'title_ar' => $data['title_ar'] ?? null,
            'slug_en' => $data['slug_en'],
            'slug_ar' => $data['slug_ar'] ?? null,
            'excerpt_en' => $data['excerpt_en'] ?? null,
            'excerpt_ar' => $data['excerpt_ar'] ?? null,
            'content_en' => $data['content_en'],
            'content_ar' => $data['content_ar'] ?? null,
            'coverImage' => $data['coverImage'] ?? null,
            'alt_text_en' => $data['alt_text_en'] ?? null,
            'alt_text_ar' => $data['alt_text_ar'] ?? null,
            'isFeatured' => !empty($data['isFeatured']) ? 1 : 0,
            'status' => $status,
            'publishedAt' => $publishedAt,
            'categoryId' => $data['categoryId']
        ]);
    }

    public function deletePost($id, $softDelete = true) {
        $this->clearCache();
        if ($softDelete) {
            $stmt = $this->db->prepare("UPDATE posts SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id");
        } else {
            $stmt = $this->db->prepare("DELETE FROM posts WHERE id = :id");
        }
        return $stmt->execute(['id' => $id]);
    }

    // --- Categories Management ---
    public function getCategories() {
        $cacheKey = "categories_all";
        $cached = $this->cacheGet($cacheKey);
        if ($cached) return $cached;

        $stmt = $this->db->prepare("SELECT * FROM categories ORDER BY name_en ASC");
        $stmt->execute();
        $categories = $stmt->fetchAll();

        $this->cacheSet($cacheKey, $categories);
        return $categories;
    }

    public function getCategoryBySlug($slug) {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE slug_en = :slug_en OR slug_ar = :slug_ar LIMIT 1");
        $stmt->execute(['slug_en' => $slug, 'slug_ar' => $slug]);
        return $stmt->fetch();
    }

    public function getCategoryById($id) {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function createCategory($data) {
        $this->clearCache();
        $id = bin2hex(random_bytes(16));
        $stmt = $this->db->prepare("INSERT INTO categories (id, name_en, name_ar, slug_en, slug_ar, description_en, description_ar, image, alt_text_en, alt_text_ar) VALUES (:id, :name_en, :name_ar, :slug_en, :slug_ar, :description_en, :description_ar, :image, :alt_text_en, :alt_text_ar)");
        return $stmt->execute([
            'id' => $id,
            'name_en' => $data['name_en'],
            'name_ar' => $data['name_ar'] ?? null,
            'slug_en' => $data['slug_en'],
            'slug_ar' => $data['slug_ar'] ?? null,
            'description_en' => $data['description_en'] ?? null,
            'description_ar' => $data['description_ar'] ?? null,
            'image' => $data['image'] ?? null,
            'alt_text_en' => $data['alt_text_en'] ?? null,
            'alt_text_ar' => $data['alt_text_ar'] ?? null
        ]);
    }

    public function updateCategory($id, $data) {
        $this->clearCache();
        $stmt = $this->db->prepare("UPDATE categories SET name_en = :name_en, name_ar = :name_ar, slug_en = :slug_en, slug_ar = :slug_ar, description_en = :description_en, description_ar = :description_ar, image = :image, alt_text_en = :alt_text_en, alt_text_ar = :alt_text_ar WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'name_en' => $data['name_en'],
            'name_ar' => $data['name_ar'] ?? null,
            'slug_en' => $data['slug_en'],
            'slug_ar' => $data['slug_ar'] ?? null,
            'description_en' => $data['description_en'] ?? null,
            'description_ar' => $data['description_ar'] ?? null,
            'image' => $data['image'] ?? null,
            'alt_text_en' => $data['alt_text_en'] ?? null,
            'alt_text_ar' => $data['alt_text_ar'] ?? null
        ]);
    }

    public function deleteCategory($id) {
        $this->clearCache();
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // --- Site Settings ---
    public function getSiteSettings() {
        $cacheKey = "site_settings_singleton";
        $cached = $this->cacheGet($cacheKey);
        if ($cached) return $cached;

        $stmt = $this->db->prepare("SELECT * FROM site_settings WHERE id = 1 LIMIT 1");
        $stmt->execute();
        $settings = $stmt->fetch();

        if ($settings) {
            $this->cacheSet($cacheKey, $settings);
        }
        return $settings;
    }

    public function updateSiteSettings($data) {
        $this->clearCache();
        $sql = "UPDATE site_settings SET 
                siteName_en = :siteName_en,
                siteName_ar = :siteName_ar,
                logoUrl = :logoUrl,
                logoDarkUrl = :logoDarkUrl,
                heroBackgroundUrl_en = :heroBackgroundUrl_en,
                heroBackgroundUrl_ar = :heroBackgroundUrl_ar,
                metaTitle_en = :metaTitle_en,
                metaTitle_ar = :metaTitle_ar,
                metaDescription_en = :metaDescription_en,
                metaDescription_ar = :metaDescription_ar,
                ogImage = :ogImage,
                faviconUrl = :faviconUrl,
                primaryColor = :primaryColor,
                secondaryColor = :secondaryColor,
                accentColor = :accentColor,
                backgroundColor = :backgroundColor,
                textColor = :textColor,
                surfaceColor = :surfaceColor,
                borderColor = :borderColor,
                themeMode = :themeMode,
                fontFamily = :fontFamily,
                fontFamilyBody = :fontFamilyBody,
                fontFamilyAr = :fontFamilyAr,
                fontFamilyArBody = :fontFamilyArBody,
                fontSize = :fontSize,
                headingWeight = :headingWeight,
                bodyWeight = :bodyWeight,
                letterSpacing = :letterSpacing,
                lineHeight = :lineHeight
                WHERE id = 1";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'siteName_en' => $data['siteName_en'] ?? $data['siteName'] ?? 'Young Over 60',
            'siteName_ar' => $data['siteName_ar'] ?? null,
            'logoUrl' => !empty($data['logoUrl']) ? $data['logoUrl'] : null,
            'logoDarkUrl' => !empty($data['logoDarkUrl']) ? $data['logoDarkUrl'] : null,
            'heroBackgroundUrl_en' => $data['heroBackgroundUrl_en'] ?? $data['heroBackgroundUrl'] ?? null,
            'heroBackgroundUrl_ar' => $data['heroBackgroundUrl_ar'] ?? $data['heroBackgroundUrl'] ?? null,
            'metaTitle_en' => $data['metaTitle_en'] ?? $data['metaTitle'] ?? null,
            'metaTitle_ar' => $data['metaTitle_ar'] ?? null,
            'metaDescription_en' => $data['metaDescription_en'] ?? $data['metaDescription'] ?? null,
            'metaDescription_ar' => $data['metaDescription_ar'] ?? null,
            'ogImage' => !empty($data['ogImage']) ? $data['ogImage'] : null,
            'faviconUrl' => !empty($data['faviconUrl']) ? $data['faviconUrl'] : null,
            'primaryColor' => $data['primaryColor'] ?? '#0F4C81',
            'secondaryColor' => $data['secondaryColor'] ?? '#1e3c5a',
            'accentColor' => $data['accentColor'] ?? '#D4A75C',
            'backgroundColor' => $data['backgroundColor'] ?? '#F8F6F2',
            'textColor' => $data['textColor'] ?? '#1F1F1F',
            'surfaceColor' => $data['surfaceColor'] ?? '#ffffff',
            'borderColor' => $data['borderColor'] ?? 'rgba(0,0,0,0.05)',
            'themeMode' => $data['themeMode'] ?? 'light',
            'fontFamily' => $data['fontFamily'] ?? 'Outfit',
            'fontFamilyBody' => $data['fontFamilyBody'] ?? 'Inter',
            'fontFamilyAr' => $data['fontFamilyAr'] ?? 'Cairo',
            'fontFamilyArBody' => $data['fontFamilyArBody'] ?? 'Cairo',
            'fontSize' => $data['fontSize'] ?? '16px',
            'headingWeight' => $data['headingWeight'] ?? '700',
            'bodyWeight' => $data['bodyWeight'] ?? '400',
            'letterSpacing' => $data['letterSpacing'] ?? 'normal',
            'lineHeight' => $data['lineHeight'] ?? '1.8'
        ]);
    }

    // --- Testimonials Management ---
    public function getTestimonials() {
        $cacheKey = "testimonials_all";
        $cached = $this->cacheGet($cacheKey);
        if ($cached) return $cached;

        $stmt = $this->db->prepare("SELECT * FROM testimonials ORDER BY created_at DESC");
        $stmt->execute();
        $testimonials = $stmt->fetchAll();

        $this->cacheSet($cacheKey, $testimonials);
        return $testimonials;
    }

    public function getTestimonialById($id) {
        $stmt = $this->db->prepare("SELECT * FROM testimonials WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function createTestimonial($data) {
        $this->clearCache();
        $id = bin2hex(random_bytes(16));
        $stmt = $this->db->prepare("INSERT INTO testimonials (id, quote_en, quote_ar, author_en, author_ar, role_en, role_ar, image) VALUES (:id, :quote_en, :quote_ar, :author_en, :author_ar, :role_en, :role_ar, :image)");
        return $stmt->execute([
            'id' => $id,
            'quote_en' => $data['quote_en'],
            'quote_ar' => $data['quote_ar'],
            'author_en' => $data['author_en'],
            'author_ar' => $data['author_ar'],
            'role_en' => $data['role_en'] ?? null,
            'role_ar' => $data['role_ar'] ?? null,
            'image' => $data['image'] ?? null
        ]);
    }

    public function updateTestimonial($id, $data) {
        $this->clearCache();
        $stmt = $this->db->prepare("UPDATE testimonials SET quote_en = :quote_en, quote_ar = :quote_ar, author_en = :author_en, author_ar = :author_ar, role_en = :role_en, role_ar = :role_ar, image = :image WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'quote_en' => $data['quote_en'],
            'quote_ar' => $data['quote_ar'],
            'author_en' => $data['author_en'],
            'author_ar' => $data['author_ar'],
            'role_en' => $data['role_en'] ?? null,
            'role_ar' => $data['role_ar'] ?? null,
            'image' => $data['image'] ?? null
        ]);
    }

    public function deleteTestimonial($id) {
        $this->clearCache();
        $stmt = $this->db->prepare("DELETE FROM testimonials WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // --- Submissions & Newsletter Helpers ---
    public function getSubscribers() {
        $stmt = $this->db->prepare("SELECT * FROM newsletters ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getSubmissions() {
        $stmt = $this->db->prepare("SELECT * FROM contact_submissions ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
