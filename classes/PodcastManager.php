<?php
// classes/PodcastManager.php
// Podcasts Model & Database Service Layer

class PodcastManager {
    private $db;

    public function __construct($db = null) {
        if ($db instanceof PDO) {
            $this->db = $db;
        } else {
            $this->db = Database::getInstance()->getConnection();
        }
    }

    // Fetch list of podcasts with pagination, search, and category filters
    public function getPodcasts($params = []) {
        $page = isset($params['page']) ? (int)$params['page'] : 1;
        $limit = isset($params['limit']) ? (int)$params['limit'] : 9;
        $offset = ($page - 1) * $limit;
        $category = $params['category'] ?? null;
        $search = $params['search'] ?? null;
        $isFeatured = isset($params['is_featured']) ? $params['is_featured'] : null;

        $sql = "SELECT * FROM podcasts WHERE 1=1";
        $countSql = "SELECT COUNT(*) FROM podcasts WHERE 1=1";
        $binds = [];

        if ($category) {
            $sql .= " AND (category_en = :category_en OR category_ar = :category_ar)";
            $countSql .= " AND (category_en = :category_en OR category_ar = :category_ar)";
            $binds['category_en'] = $category;
            $binds['category_ar'] = $category;
        }

        if ($isFeatured !== null) {
            $sql .= " AND is_featured = :is_featured";
            $countSql .= " AND is_featured = :is_featured";
            $binds['is_featured'] = $isFeatured ? 1 : 0;
        }

        if ($search) {
            $lang = defined('CURRENT_LANG') ? CURRENT_LANG : 'en';
            $sql .= " AND (title_{$lang} LIKE :search1 OR description_{$lang} LIKE :search2)";
            $countSql .= " AND (title_{$lang} LIKE :search1 OR description_{$lang} LIKE :search2)";
            $binds['search1'] = '%' . $search . '%';
            $binds['search2'] = '%' . $search . '%';
        }

        // Count total matching items
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($binds);
        $total = (int)$countStmt->fetchColumn();

        // Sort by created date descending
        $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($binds as $key => $val) {
            $stmt->bindValue(':' . $key, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetchAll();
        $totalPages = ceil($total / $limit);

        return [
            'data' => $data,
            'meta' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'totalPages' => $totalPages > 0 ? $totalPages : 1
            ]
        ];
    }

    public function getPodcastById($id) {
        $stmt = $this->db->prepare("SELECT * FROM podcasts WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getPodcastBySlug($slug) {
        $stmt = $this->db->prepare("SELECT * FROM podcasts WHERE slug_en = :slug_en OR slug_ar = :slug_ar LIMIT 1");
        $stmt->execute(['slug_en' => $slug, 'slug_ar' => $slug]);
        return $stmt->fetch();
    }

    public function createPodcast($data) {
        $id = bin2hex(random_bytes(16));
        $sql = "INSERT INTO podcasts (id, title_en, title_ar, slug_en, slug_ar, description_en, description_ar, audio_file, cover_image, alt_text_en, alt_text_ar, duration, category_en, category_ar, is_featured) 
                VALUES (:id, :title_en, :title_ar, :slug_en, :slug_ar, :description_en, :description_ar, :audio_file, :cover_image, :alt_text_en, :alt_text_ar, :duration, :category_en, :category_ar, :is_featured)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title_en' => $data['title_en'],
            'title_ar' => $data['title_ar'] ?? null,
            'slug_en' => $data['slug_en'],
            'slug_ar' => $data['slug_ar'] ?? null,
            'description_en' => $data['description_en'] ?? null,
            'description_ar' => $data['description_ar'] ?? null,
            'audio_file' => $data['audio_file'],
            'cover_image' => $data['cover_image'] ?? null,
            'alt_text_en' => $data['alt_text_en'] ?? null,
            'alt_text_ar' => $data['alt_text_ar'] ?? null,
            'duration' => $data['duration'] ?? null,
            'category_en' => $data['category_en'] ?? null,
            'category_ar' => $data['category_ar'] ?? null,
            'is_featured' => !empty($data['is_featured']) ? 1 : 0
        ]);
    }

    public function updatePodcast($id, $data) {
        $sql = "UPDATE podcasts SET 
                title_en = :title_en, 
                title_ar = :title_ar, 
                slug_en = :slug_en, 
                slug_ar = :slug_ar, 
                description_en = :description_en, 
                description_ar = :description_ar, 
                audio_file = :audio_file, 
                cover_image = :cover_image, 
                alt_text_en = :alt_text_en,
                alt_text_ar = :alt_text_ar,
                duration = :duration, 
                category_en = :category_en, 
                category_ar = :category_ar, 
                is_featured = :is_featured
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title_en' => $data['title_en'],
            'title_ar' => $data['title_ar'] ?? null,
            'slug_en' => $data['slug_en'],
            'slug_ar' => $data['slug_ar'] ?? null,
            'description_en' => $data['description_en'] ?? null,
            'description_ar' => $data['description_ar'] ?? null,
            'audio_file' => $data['audio_file'],
            'cover_image' => $data['cover_image'] ?? null,
            'alt_text_en' => $data['alt_text_en'] ?? null,
            'alt_text_ar' => $data['alt_text_ar'] ?? null,
            'duration' => $data['duration'] ?? null,
            'category_en' => $data['category_en'] ?? null,
            'category_ar' => $data['category_ar'] ?? null,
            'is_featured' => !empty($data['is_featured']) ? 1 : 0
        ]);
    }

    public function deletePodcast($id) {
        $stmt = $this->db->prepare("DELETE FROM podcasts WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // Fetch featured podcasts for carousels
    public function getFeaturedPodcasts() {
        $stmt = $this->db->prepare("SELECT * FROM podcasts WHERE is_featured = 1 ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Static Categories List
    public function getCategories() {
        if (defined('CURRENT_LANG') && CURRENT_LANG === 'ar') {
            return [
                'Guides & Tips' => 'أدلة ونصائح',
                'Interviews' => 'مقابلات',
                'Destinations' => 'الوجهات',
                'Personal Stories' => 'قصص شخصية',
                'Inspiration' => 'الإلهام'
            ];
        }
        return [
            'Guides & Tips' => 'Guides & Tips',
            'Interviews' => 'Interviews',
            'Destinations' => 'Destinations',
            'Personal Stories' => 'Personal Stories',
            'Inspiration' => 'Inspiration'
        ];
    }
}
