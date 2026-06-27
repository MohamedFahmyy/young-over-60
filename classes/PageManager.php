<?php
// classes/PageManager.php
// Custom CMS Pages Service Layer

class PageManager {
    private $db;
    private $cacheEnabled = true;
    private $cacheTtl = 3600; // 1 hour

    public function __construct($db = null) {
        if ($db instanceof PDO) {
            $this->db = $db;
        } else {
            $this->db = Database::getInstance()->getConnection();
        }
    }

    // --- Simple File Cache Helpers ---
    private function getCacheFilename($key) {
        return PATH_CACHE . '/page_' . md5($key) . '.json';
    }

    private function cacheGet($key) {
        if (!$this->cacheEnabled) return null;
        $file = $this->getCacheFilename($key);
        if (file_exists($file) && (time() - filemtime($file) < $this->cacheTtl)) {
            return json_decode(file_get_contents($file), true);
        }
        return null;
    }

    private function cacheSet($key, $data) {
        if (!$this->cacheEnabled) return;
        file_put_contents($this->getCacheFilename($key), json_encode($data));
    }

    public function clearCache() {
        $files = glob(PATH_CACHE . '/page_*.json');
        if ($files) {
            foreach ($files as $f) { @unlink($f); }
        }
    }

    // ===========================
    // PAGE CRUD
    // ===========================

    /**
     * Get a single page by slug (supports bilingual slugs)
     */
    public function getPageBySlug($slug, $lang = null) {
        $lang = $lang ?? (defined('CURRENT_LANG') ? CURRENT_LANG : 'en');
        $cacheKey = "page_slug_{$slug}_{$lang}";
        $cached = $this->cacheGet($cacheKey);
        if ($cached) return $cached;

        $stmt = $this->db->prepare(
            "SELECT * FROM custom_pages 
             WHERE (slug_en = :slug1 OR slug_ar = :slug2 OR slug_nl = :slug3) 
             AND is_published = 1 
             LIMIT 1"
        );
        $stmt->execute([':slug1' => $slug, ':slug2' => $slug, ':slug3' => $slug]);
        $page = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($page) {
            $this->cacheSet($cacheKey, $page);
        }
        return $page ?: null;
    }

    /**
     * Get page by ID
     */
    public function getPageById($id) {
        $cacheKey = "page_id_{$id}";
        $cached = $this->cacheGet($cacheKey);
        if ($cached) return $cached;

        $stmt = $this->db->prepare("SELECT * FROM custom_pages WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $page = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($page) {
            $this->cacheSet($cacheKey, $page);
        }
        return $page ?: null;
    }

    /**
     * List all pages with optional filters
     */
    public function getPages($params = []) {
        $publishedOnly = $params['published_only'] ?? false;
        $menuOnly      = $params['menu_only'] ?? false;
        $limit         = isset($params['limit']) ? (int)$params['limit'] : 100;
        $search        = $params['search'] ?? null;
        $template      = $params['template'] ?? null;

        $cacheKey = "pages_list_" . md5(json_encode($params));
        $cached = $this->cacheGet($cacheKey);
        if ($cached) return $cached;

        $where = ['1=1'];
        $bind  = [];

        if ($publishedOnly) {
            $where[] = "is_published = 1";
        }
        if ($menuOnly) {
            $where[] = "show_in_menu = 1";
        }
        if ($template) {
            $where[] = "template_type = :template";
            $bind[':template'] = $template;
        }
        if ($search) {
            $where[] = "(title_en LIKE :search OR title_ar LIKE :search OR slug_en LIKE :search)";
            $bind[':search'] = '%' . $search . '%';
        }

        $sql = "SELECT * FROM custom_pages WHERE " . implode(' AND ', $where) . " ORDER BY sort_order ASC, created_at DESC LIMIT {$limit}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($bind);
        $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->cacheSet($cacheKey, $pages);
        return $pages;
    }

    /**
     * Create a new custom page
     */
    public function createPage($data) {
        $now = date('Y-m-d H:i:s');

        // Auto-generate slugs if missing
        if (empty($data['slug_en']) && !empty($data['title_en'])) {
            $data['slug_en'] = $this->generateSlug($data['title_en']);
        }
        if (empty($data['slug_ar']) && !empty($data['title_ar'])) {
            $data['slug_ar'] = $this->generateSlug($data['title_ar']);
        }
        if (empty($data['slug_nl']) && !empty($data['title_nl'])) {
            $data['slug_nl'] = $this->generateSlug($data['title_nl']);
        }

        // Ensure slug uniqueness
        $data['slug_en'] = $this->ensureUniqueSlug($data['slug_en'] ?? '', 'slug_en');
        if (!empty($data['slug_ar'])) {
            $data['slug_ar'] = $this->ensureUniqueSlug($data['slug_ar'], 'slug_ar');
        }
        if (!empty($data['slug_nl'])) {
            $data['slug_nl'] = $this->ensureUniqueSlug($data['slug_nl'], 'slug_nl');
        }

        // Generate a unique string ID (the DB schema uses VARCHAR(36) primary key)
        $pageId = 'page-' . uniqid();

        $stmt = $this->db->prepare("
            INSERT INTO custom_pages 
                (id, title_en, title_ar, title_nl, slug_en, slug_ar, slug_nl, content_en, content_ar, content_nl,
                 excerpt_en, excerpt_ar, excerpt_nl,
                 meta_title_en, meta_title_ar, meta_title_nl, meta_description_en, meta_description_ar, meta_description_nl,
                 canonical_url_en, canonical_url_ar, canonical_url_nl,
                 hero_title_en, hero_title_ar, hero_title_nl, hero_subtitle_en, hero_subtitle_ar, hero_subtitle_nl,
                 hero_image, featured_image, template_type, is_published, show_in_menu,
                 menu_title_en, menu_title_ar, menu_title_nl, sort_order, created_at, updated_at)
            VALUES
                (:id, :title_en, :title_ar, :title_nl, :slug_en, :slug_ar, :slug_nl, :content_en, :content_ar, :content_nl,
                 :excerpt_en, :excerpt_ar, :excerpt_nl,
                 :meta_title_en, :meta_title_ar, :meta_title_nl, :meta_description_en, :meta_description_ar, :meta_description_nl,
                 :canonical_url_en, :canonical_url_ar, :canonical_url_nl,
                 :hero_title_en, :hero_title_ar, :hero_title_nl, :hero_subtitle_en, :hero_subtitle_ar, :hero_subtitle_nl,
                 :hero_image, :featured_image, :template_type, :is_published, :show_in_menu,
                 :menu_title_en, :menu_title_ar, :menu_title_nl, :sort_order, :created_at, :updated_at)
        ");

        $result = $stmt->execute([
            ':id'                 => $pageId,
            ':title_en'           => $data['title_en'] ?? '',
            ':title_ar'           => $data['title_ar'] ?? '',
            ':title_nl'           => $data['title_nl'] ?? '',
            ':slug_en'            => $data['slug_en'],
            ':slug_ar'            => $data['slug_ar'] ?? '',
            ':slug_nl'            => $data['slug_nl'] ?? '',
            ':content_en'         => $data['content_en'] ?? '',
            ':content_ar'         => $data['content_ar'] ?? '',
            ':content_nl'         => $data['content_nl'] ?? '',
            ':excerpt_en'         => $data['excerpt_en'] ?? '',
            ':excerpt_ar'         => $data['excerpt_ar'] ?? '',
            ':excerpt_nl'         => $data['excerpt_nl'] ?? '',
            ':meta_title_en'      => $data['meta_title_en'] ?? '',
            ':meta_title_ar'      => $data['meta_title_ar'] ?? '',
            ':meta_title_nl'      => $data['meta_title_nl'] ?? '',
            ':meta_description_en'=> $data['meta_description_en'] ?? '',
            ':meta_description_ar'=> $data['meta_description_ar'] ?? '',
            ':meta_description_nl'=> $data['meta_description_nl'] ?? '',
            ':canonical_url_en'   => $data['canonical_url_en'] ?? '',
            ':canonical_url_ar'   => $data['canonical_url_ar'] ?? '',
            ':canonical_url_nl'   => $data['canonical_url_nl'] ?? '',
            ':hero_title_en'      => $data['hero_title_en'] ?? '',
            ':hero_title_ar'      => $data['hero_title_ar'] ?? '',
            ':hero_title_nl'      => $data['hero_title_nl'] ?? '',
            ':hero_subtitle_en'   => $data['hero_subtitle_en'] ?? '',
            ':hero_subtitle_ar'   => $data['hero_subtitle_ar'] ?? '',
            ':hero_subtitle_nl'   => $data['hero_subtitle_nl'] ?? '',
            ':hero_image'         => $data['hero_image'] ?? '',
            ':featured_image'     => $data['featured_image'] ?? '',
            ':template_type'      => $data['template_type'] ?? 'default',
            ':is_published'       => isset($data['is_published']) ? (int)$data['is_published'] : 0,
            ':show_in_menu'       => isset($data['show_in_menu']) ? (int)$data['show_in_menu'] : 0,
            ':menu_title_en'      => $data['menu_title_en'] ?? '',
            ':menu_title_ar'      => $data['menu_title_ar'] ?? '',
            ':menu_title_nl'      => $data['menu_title_nl'] ?? '',
            ':sort_order'         => isset($data['sort_order']) ? (int)$data['sort_order'] : 0,
            ':created_at'         => $now,
            ':updated_at'         => $now,
        ]);

        if ($result) {
            $newId = $pageId; // return the UUID we generated
            $this->clearCache();
            return $newId;
        }
        return false;
    }

    /**
     * Update an existing page
     */
    public function updatePage($id, $data) {
        // Fetch existing page for redirect tracking
        $existing = $this->getPageById($id);
        if (!$existing) return false;

        $now = date('Y-m-d H:i:s');

        // If slug changed, create redirect
        if (!empty($data['slug_en']) && $data['slug_en'] !== $existing['slug_en']) {
            $this->createRedirect($existing['slug_en'], $data['slug_en'], $id);
        }
        if (!empty($data['slug_nl']) && $data['slug_nl'] !== $existing['slug_nl']) {
            $this->createRedirect($existing['slug_nl'], $data['slug_nl'], $id);
        }

        // Ensure slug uniqueness (excluding current page)
        if (!empty($data['slug_en'])) {
            $data['slug_en'] = $this->ensureUniqueSlug($data['slug_en'], 'slug_en', $id);
        }
        if (!empty($data['slug_ar'])) {
            $data['slug_ar'] = $this->ensureUniqueSlug($data['slug_ar'], 'slug_ar', $id);
        }
        if (!empty($data['slug_nl'])) {
            $data['slug_nl'] = $this->ensureUniqueSlug($data['slug_nl'], 'slug_nl', $id);
        }

        // Save revision before updating
        $this->saveRevision($id, $existing);

        $stmt = $this->db->prepare("
            UPDATE custom_pages SET
                title_en = :title_en, title_ar = :title_ar, title_nl = :title_nl,
                slug_en = :slug_en, slug_ar = :slug_ar, slug_nl = :slug_nl,
                content_en = :content_en, content_ar = :content_ar, content_nl = :content_nl,
                excerpt_en = :excerpt_en, excerpt_ar = :excerpt_ar, excerpt_nl = :excerpt_nl,
                meta_title_en = :meta_title_en, meta_title_ar = :meta_title_ar, meta_title_nl = :meta_title_nl,
                meta_description_en = :meta_description_en, meta_description_ar = :meta_description_ar, meta_description_nl = :meta_description_nl,
                canonical_url_en = :canonical_url_en, canonical_url_ar = :canonical_url_ar, canonical_url_nl = :canonical_url_nl,
                hero_title_en = :hero_title_en, hero_title_ar = :hero_title_ar, hero_title_nl = :hero_title_nl,
                hero_subtitle_en = :hero_subtitle_en, hero_subtitle_ar = :hero_subtitle_ar, hero_subtitle_nl = :hero_subtitle_nl,
                hero_image = :hero_image, featured_image = :featured_image,
                template_type = :template_type, is_published = :is_published,
                show_in_menu = :show_in_menu, menu_title_en = :menu_title_en, menu_title_ar = :menu_title_ar, menu_title_nl = :menu_title_nl,
                sort_order = :sort_order, updated_at = :updated_at
            WHERE id = :id
        ");

        $result = $stmt->execute([
            ':id'                  => $id,
            ':title_en'           => $data['title_en'] ?? $existing['title_en'],
            ':title_ar'           => $data['title_ar'] ?? $existing['title_ar'],
            ':title_nl'           => $data['title_nl'] ?? $existing['title_nl'],
            ':slug_en'            => $data['slug_en'] ?? $existing['slug_en'],
            ':slug_ar'            => $data['slug_ar'] ?? $existing['slug_ar'],
            ':slug_nl'            => $data['slug_nl'] ?? $existing['slug_nl'],
            ':content_en'         => $data['content_en'] ?? $existing['content_en'],
            ':content_ar'         => $data['content_ar'] ?? $existing['content_ar'],
            ':content_nl'         => $data['content_nl'] ?? $existing['content_nl'],
            ':excerpt_en'         => $data['excerpt_en'] ?? $existing['excerpt_en'],
            ':excerpt_ar'         => $data['excerpt_ar'] ?? $existing['excerpt_ar'],
            ':excerpt_nl'         => $data['excerpt_nl'] ?? $existing['excerpt_nl'],
            ':meta_title_en'      => $data['meta_title_en'] ?? $existing['meta_title_en'],
            ':meta_title_ar'      => $data['meta_title_ar'] ?? $existing['meta_title_ar'],
            ':meta_title_nl'      => $data['meta_title_nl'] ?? $existing['meta_title_nl'],
            ':meta_description_en'=> $data['meta_description_en'] ?? $existing['meta_description_en'],
            ':meta_description_ar'=> $data['meta_description_ar'] ?? $existing['meta_description_ar'],
            ':meta_description_nl'=> $data['meta_description_nl'] ?? $existing['meta_description_nl'],
            ':canonical_url_en'   => $data['canonical_url_en'] ?? $existing['canonical_url_en'],
            ':canonical_url_ar'   => $data['canonical_url_ar'] ?? $existing['canonical_url_ar'],
            ':canonical_url_nl'   => $data['canonical_url_nl'] ?? $existing['canonical_url_nl'],
            ':hero_title_en'      => $data['hero_title_en'] ?? $existing['hero_title_en'],
            ':hero_title_ar'      => $data['hero_title_ar'] ?? $existing['hero_title_ar'],
            ':hero_title_nl'      => $data['hero_title_nl'] ?? $existing['hero_title_nl'],
            ':hero_subtitle_en'   => $data['hero_subtitle_en'] ?? $existing['hero_subtitle_en'],
            ':hero_subtitle_ar'   => $data['hero_subtitle_ar'] ?? $existing['hero_subtitle_ar'],
            ':hero_subtitle_nl'   => $data['hero_subtitle_nl'] ?? $existing['hero_subtitle_nl'],
            ':hero_image'         => $data['hero_image'] ?? $existing['hero_image'],
            ':featured_image'     => $data['featured_image'] ?? $existing['featured_image'],
            ':template_type'      => $data['template_type'] ?? $existing['template_type'],
            ':is_published'       => isset($data['is_published']) ? (int)$data['is_published'] : (int)$existing['is_published'],
            ':show_in_menu'       => isset($data['show_in_menu']) ? (int)$data['show_in_menu'] : (int)$existing['show_in_menu'],
            ':menu_title_en'      => $data['menu_title_en'] ?? $existing['menu_title_en'],
            ':menu_title_ar'      => $data['menu_title_ar'] ?? $existing['menu_title_ar'],
            ':menu_title_nl'      => $data['menu_title_nl'] ?? $existing['menu_title_nl'],
            ':sort_order'         => isset($data['sort_order']) ? (int)$data['sort_order'] : (int)$existing['sort_order'],
            ':updated_at'         => $now,
        ]);

        if ($result) {
            $this->clearCache();
        }
        return $result;
    }

    /**
     * Delete a custom page
     */
    public function deletePage($id) {
        $stmt = $this->db->prepare("DELETE FROM custom_pages WHERE id = :id");
        $result = $stmt->execute([':id' => $id]);
        if ($result) {
            $this->clearCache();
        }
        return $result;
    }

    // ===========================
    // REVISIONS
    // ===========================

    public function saveRevision($pageId, $pageData) {
        $revId = 'rev-' . uniqid();
        $stmt = $this->db->prepare("
            INSERT INTO page_revisions 
                (id, page_id, title_en, title_ar, content_en, content_ar, excerpt_en, excerpt_ar, template_type,
                 sort_order, show_in_menu, menu_title_en, menu_title_ar,
                 hero_title_en, hero_title_ar, hero_subtitle_en, hero_subtitle_ar, hero_image,
                 meta_title_en, meta_title_ar, meta_description_en, meta_description_ar, featured_image, created_at)
            VALUES 
                (:id, :page_id, :title_en, :title_ar, :content_en, :content_ar, :excerpt_en, :excerpt_ar, :template_type,
                 :sort_order, :show_in_menu, :menu_title_en, :menu_title_ar,
                 :hero_title_en, :hero_title_ar, :hero_subtitle_en, :hero_subtitle_ar, :hero_image,
                 :meta_title_en, :meta_title_ar, :meta_description_en, :meta_description_ar, :featured_image, :created_at)
        ");
        return $stmt->execute([
            ':id'                  => $revId,
            ':page_id'             => $pageId,
            ':title_en'            => $pageData['title_en'] ?? '',
            ':title_ar'            => $pageData['title_ar'] ?? '',
            ':content_en'          => $pageData['content_en'] ?? '',
            ':content_ar'          => $pageData['content_ar'] ?? '',
            ':excerpt_en'          => $pageData['excerpt_en'] ?? '',
            ':excerpt_ar'          => $pageData['excerpt_ar'] ?? '',
            ':template_type'       => $pageData['template_type'] ?? 'default',
            ':sort_order'          => $pageData['sort_order'] ?? 0,
            ':show_in_menu'        => $pageData['show_in_menu'] ?? 0,
            ':menu_title_en'       => $pageData['menu_title_en'] ?? '',
            ':menu_title_ar'       => $pageData['menu_title_ar'] ?? '',
            ':hero_title_en'       => $pageData['hero_title_en'] ?? '',
            ':hero_title_ar'       => $pageData['hero_title_ar'] ?? '',
            ':hero_subtitle_en'    => $pageData['hero_subtitle_en'] ?? '',
            ':hero_subtitle_ar'    => $pageData['hero_subtitle_ar'] ?? '',
            ':hero_image'          => $pageData['hero_image'] ?? '',
            ':meta_title_en'       => $pageData['meta_title_en'] ?? '',
            ':meta_title_ar'       => $pageData['meta_title_ar'] ?? '',
            ':meta_description_en' => $pageData['meta_description_en'] ?? '',
            ':meta_description_ar' => $pageData['meta_description_ar'] ?? '',
            ':featured_image'      => $pageData['featured_image'] ?? '',
            ':created_at'          => date('Y-m-d H:i:s'),
        ]);
    }

    public function getRevisions($pageId, $limit = 10) {
        $stmt = $this->db->prepare(
            "SELECT * FROM page_revisions WHERE page_id = :page_id ORDER BY created_at DESC LIMIT {$limit}"
        );
        $stmt->execute([':page_id' => $pageId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function restoreRevision($revisionId) {
        $stmt = $this->db->prepare("SELECT * FROM page_revisions WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $revisionId]);
        $rev = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$rev || empty($rev['page_id'])) return false;
        return $this->updatePage($rev['page_id'], $rev);
    }

    // ===========================
    // REDIRECTS
    // ===========================

    public function createRedirect($fromSlug, $toSlug, $pageId = null) {
        // Avoid duplicate
        $check = $this->db->prepare("SELECT id FROM page_redirects WHERE old_slug = :old_slug LIMIT 1");
        $check->execute([':old_slug' => $fromSlug]);
        if ($check->fetch()) return true;

        $redirId = 'redir-' . uniqid();
        $stmt = $this->db->prepare("
            INSERT INTO page_redirects (id, old_slug, new_slug, created_at) 
            VALUES (:id, :old_slug, :new_slug, :created_at)
        ");
        return $stmt->execute([
            ':id'         => $redirId,
            ':old_slug'   => $fromSlug,
            ':new_slug'   => $toSlug,
            ':created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function findRedirect($slug) {
        $stmt = $this->db->prepare(
            "SELECT new_slug FROM page_redirects WHERE old_slug = :old_slug LIMIT 1"
        );
        $stmt->execute([':old_slug' => $slug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['new_slug'] : null;
    }

    // ===========================
    // MENU PAGES
    // ===========================

    public function getMenuPages() {
        $cacheKey = 'pages_menu_' . (defined('CURRENT_LANG') ? CURRENT_LANG : 'en');
        $cached = $this->cacheGet($cacheKey);
        if ($cached) return $cached;

        $stmt = $this->db->prepare(
            "SELECT * FROM custom_pages WHERE show_in_menu = 1 AND is_published = 1 ORDER BY sort_order ASC"
        );
        $stmt->execute();
        $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->cacheSet($cacheKey, $pages);
        return $pages;
    }

    // ===========================
    // HELPERS
    // ===========================

    public function generateSlug($text) {
        // Transliterate Arabic characters to latin equivalent for slug
        $text = mb_strtolower(trim($text));
        // Replace non-alphanumeric characters (supporting ASCII)
        $text = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        $text = trim($text, '-');
        // Fallback for purely arabic text
        if (empty($text)) {
            $text = 'page-' . time();
        }
        return $text;
    }

    private function ensureUniqueSlug($slug, $column = 'slug_en', $excludeId = null) {
        $original = $slug;
        $i = 1;
        while (true) {
            $sql = "SELECT id FROM custom_pages WHERE {$column} = :slug";
            $bind = [':slug' => $slug];
            if ($excludeId) {
                $sql .= " AND id != :exc_id";
                $bind[':exc_id'] = $excludeId;
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute($bind);
            if (!$stmt->fetch()) break;
            $slug = $original . '-' . $i++;
        }
        return $slug;
    }

    /**
     * Get translated field from custom page row
     */
    public function getField($page, $field) {
        $lang = defined('CURRENT_LANG') ? CURRENT_LANG : 'en';
        $langKey   = $field . '_' . $lang;
        $fallback  = $field . '_en';

        if (!empty($page[$langKey])) return $page[$langKey];
        if (!empty($page[$fallback])) return $page[$fallback];
        if (!empty($page[$field])) return $page[$field];
        return '';
    }
}
