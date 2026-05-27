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
        $cacheKey = "posts_list_p_{$page}_l_{$limit}_c_{$categoryId}_s_{$status}_f_{$isFeatured}_q_" . md5($safeSearch) . "_d_{$includeDeleted}";
        $cached = $this->cacheGet($cacheKey);
        if ($cached) return $cached;

        // Base Query
        $sql = "SELECT p.*, c.name as categoryName, c.slug as categorySlug, u.name as authorName 
                FROM posts p 
                INNER JOIN categories c ON p.categoryId = c.id 
                LEFT JOIN users u ON p.authorId = u.id 
                WHERE 1=1";
        
        $countSql = "SELECT COUNT(*) FROM posts p WHERE 1=1";
        $binds = [];

        // Exclude soft deleted posts by default
        if (!$includeDeleted) {
            $sql .= " AND p.deleted_at IS NULL";
            $countSql .= " AND p.deleted_at IS NULL";
        }

        if ($categoryId) {
            $sql .= " AND (p.categoryId = :categoryId OR c.slug = :categorySlug)";
            $countSql .= " AND (p.categoryId = :categoryId OR p.categoryId IN (SELECT id FROM categories WHERE slug = :categorySlug))";
            $binds['categoryId'] = $categoryId;
            $binds['categorySlug'] = $categoryId;
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
            $sql .= " AND (p.title LIKE :search1 OR p.excerpt LIKE :search2 OR p.content LIKE :search3)";
            $countSql .= " AND (p.title LIKE :search1 OR p.excerpt LIKE :search2 OR p.content LIKE :search3)";
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
        $cacheKey = "post_slug_{$slug}_a_{$isAdmin}";
        $cached = $this->cacheGet($cacheKey);
        if ($cached) return $cached;

        $sql = "SELECT p.*, c.name as categoryName, c.slug as categorySlug, 
                       u.name as authorName, u.bio as authorBio, u.avatar as authorAvatar, 
                       u.twitter as authorTwitter, u.facebook as authorFacebook, 
                       u.instagram as authorInstagram, u.linkedin as authorLinkedin, u.website as authorWebsite
                FROM posts p 
                INNER JOIN categories c ON p.categoryId = c.id 
                LEFT JOIN users u ON p.authorId = u.id 
                WHERE p.slug = :slug AND p.deleted_at IS NULL";
        
        if (!$isAdmin) {
            $sql .= " AND p.status = 'PUBLISHED'";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['slug' => $slug]);
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

        $sql = "INSERT INTO posts (id, title, slug, excerpt, content, coverImage, isFeatured, status, publishedAt, categoryId, authorId)
                VALUES (:id, :title, :slug, :excerpt, :content, :coverImage, :isFeatured, :status, :publishedAt, :categoryId, :authorId)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'slug' => $data['slug'],
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'],
            'coverImage' => $data['coverImage'] ?? null,
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
                title = :title, 
                slug = :slug, 
                excerpt = :excerpt, 
                content = :content, 
                coverImage = :coverImage, 
                isFeatured = :isFeatured, 
                status = :status, 
                publishedAt = :publishedAt, 
                categoryId = :categoryId
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'slug' => $data['slug'],
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'],
            'coverImage' => $data['coverImage'] ?? null,
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

        $stmt = $this->db->prepare("SELECT * FROM categories ORDER BY name ASC");
        $stmt->execute();
        $categories = $stmt->fetchAll();

        $this->cacheSet($cacheKey, $categories);
        return $categories;
    }

    public function getCategoryBySlug($slug) {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE slug = :slug LIMIT 1");
        $stmt->execute(['slug' => $slug]);
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
        $stmt = $this->db->prepare("INSERT INTO categories (id, name, slug, description, image) VALUES (:id, :name, :slug, :description, :image)");
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'image' => $data['image'] ?? null
        ]);
    }

    public function updateCategory($id, $data) {
        $this->clearCache();
        $stmt = $this->db->prepare("UPDATE categories SET name = :name, slug = :slug, description = :description, image = :image WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'image' => $data['image'] ?? null
        ]);
    }

    public function deleteCategory($id) {
        $this->clearCache();
        
        // Remove or reassign posts under this category to avoid DB crashes, or DB restrict constraint triggers
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
                siteName = :siteName,
                logoUrl = :logoUrl,
                logoDarkUrl = :logoDarkUrl,
                heroBackgroundUrl = :heroBackgroundUrl,
                metaTitle = :metaTitle,
                metaDescription = :metaDescription,
                ogImage = :ogImage,
                faviconUrl = :faviconUrl,
                primaryColor = :primaryColor,
                secondaryColor = :secondaryColor,
                accentColor = :accentColor,
                backgroundColor = :backgroundColor,
                textColor = :textColor
                WHERE id = 1";
        
        $stmt = $this->db->prepare($sql);
        $params = [
            'siteName' => $data['siteName'],
            'logoUrl' => !empty($data['logoUrl']) ? $data['logoUrl'] : null,
            'logoDarkUrl' => !empty($data['logoDarkUrl']) ? $data['logoDarkUrl'] : null,
            'heroBackgroundUrl' => !empty($data['heroBackgroundUrl']) ? $data['heroBackgroundUrl'] : null,
            'metaTitle' => !empty($data['metaTitle']) ? $data['metaTitle'] : null,
            'metaDescription' => !empty($data['metaDescription']) ? $data['metaDescription'] : null,
            'ogImage' => !empty($data['ogImage']) ? $data['ogImage'] : null,
            'faviconUrl' => !empty($data['faviconUrl']) ? $data['faviconUrl'] : null,
            'primaryColor' => $data['primaryColor'] ?? '#2d5a88',
            'secondaryColor' => $data['secondaryColor'] ?? '#1e3c5a',
            'accentColor' => $data['accentColor'] ?? '#eaeaea',
            'backgroundColor' => $data['backgroundColor'] ?? '#ffffff',
            'textColor' => $data['textColor'] ?? '#111111'
        ];

        try {
            return $stmt->execute($params);
        } catch (PDOException $e) {
            // Check if error is missing column (code 42S22 or message contains logoDarkUrl)
            if ($e->getCode() == '42S22' || str_contains($e->getMessage(), 'logoDarkUrl')) {
                try {
                    $this->db->exec("ALTER TABLE site_settings ADD COLUMN logoDarkUrl VARCHAR(255) DEFAULT NULL AFTER logoUrl");
                    // Retry execution after self-healing migration
                    return $stmt->execute($params);
                } catch (PDOException $innerEx) {
                    throw $e; // throw original if we fail to alter
                }
            }
            throw $e;
        }
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
