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
            $sql .= " AND category = :category";
            $countSql .= " AND category = :category";
            $binds['category'] = $category;
        }

        if ($isFeatured !== null) {
            $sql .= " AND is_featured = :is_featured";
            $countSql .= " AND is_featured = :is_featured";
            $binds['is_featured'] = $isFeatured ? 1 : 0;
        }

        if ($search) {
            $sql .= " AND (title LIKE :search1 OR description LIKE :search2)";
            $countSql .= " AND (title LIKE :search1 OR description LIKE :search2)";
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
        $stmt = $this->db->prepare("SELECT * FROM podcasts WHERE slug = :slug LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch();
    }

    public function createPodcast($data) {
        $id = bin2hex(random_bytes(16));
        $sql = "INSERT INTO podcasts (id, title, slug, description, audio_file, cover_image, duration, category, is_featured) 
                VALUES (:id, :title, :slug, :description, :audio_file, :cover_image, :duration, :category, :is_featured)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'audio_file' => $data['audio_file'],
            'cover_image' => $data['cover_image'] ?? null,
            'duration' => $data['duration'] ?? null,
            'category' => $data['category'] ?? null,
            'is_featured' => !empty($data['is_featured']) ? 1 : 0
        ]);
    }

    public function updatePodcast($id, $data) {
        $sql = "UPDATE podcasts SET 
                title = :title, 
                slug = :slug, 
                description = :description, 
                audio_file = :audio_file, 
                cover_image = :cover_image, 
                duration = :duration, 
                category = :category, 
                is_featured = :is_featured
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'audio_file' => $data['audio_file'],
            'cover_image' => $data['cover_image'] ?? null,
            'duration' => $data['duration'] ?? null,
            'category' => $data['category'] ?? null,
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
        return [
            'Guides & Tips',
            'Interviews',
            'Destinations',
            'Personal Stories',
            'Inspiration'
        ];
    }
}
