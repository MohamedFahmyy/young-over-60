<?php
// classes/StoryManager.php
// Women Stories Model & Database Service Layer

class StoryManager {
    private $db;

    public function __construct($db = null) {
        if ($db instanceof PDO) {
            $this->db = $db;
        } else {
            $this->db = Database::getInstance()->getConnection();
        }
    }

    // Fetch list of women stories with pagination
    public function getStories($params = []) {
        $page = isset($params['page']) ? (int)$params['page'] : 1;
        $limit = isset($params['limit']) ? (int)$params['limit'] : 9;
        $offset = ($page - 1) * $limit;
        $search = $params['search'] ?? null;
        $category = $params['category'] ?? null;

        $sql = "SELECT * FROM women_stories WHERE 1=1";
        $countSql = "SELECT COUNT(*) FROM women_stories WHERE 1=1";
        $binds = [];

        if ($search) {
            $sql .= " AND (title LIKE :search1 OR excerpt LIKE :search2 OR content LIKE :search3)";
            $countSql .= " AND (title LIKE :search1 OR excerpt LIKE :search2 OR content LIKE :search3)";
            $binds['search1'] = '%' . $search . '%';
            $binds['search2'] = '%' . $search . '%';
            $binds['search3'] = '%' . $search . '%';
        }

        if ($category) {
            $sql .= " AND category = :category";
            $countSql .= " AND category = :category";
            $binds['category'] = $category;
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

    // Get distinct categories of stories
    public function getCategories() {
        $stmt = $this->db->query("SELECT DISTINCT category FROM women_stories WHERE category IS NOT NULL AND category != '' ORDER BY category ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getStoryById($id) {
        $stmt = $this->db->prepare("SELECT * FROM women_stories WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getStoryBySlug($slug) {
        $stmt = $this->db->prepare("SELECT * FROM women_stories WHERE slug = :slug LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch();
    }

    public function createStory($data) {
        $id = bin2hex(random_bytes(16));
        $sql = "INSERT INTO women_stories (id, title, slug, excerpt, content, cover_image, category, author, read_time) 
                VALUES (:id, :title, :slug, :excerpt, :content, :cover_image, :category, :author, :read_time)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'slug' => $data['slug'],
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'],
            'cover_image' => $data['cover_image'] ?? null,
            'category' => $data['category'] ?? null,
            'author' => $data['author'] ?? 'Guest Writer',
            'read_time' => $data['read_time'] ?? null
        ]);
    }

    public function updateStory($id, $data) {
        $sql = "UPDATE women_stories SET 
                title = :title, 
                slug = :slug, 
                excerpt = :excerpt, 
                content = :content, 
                cover_image = :cover_image, 
                category = :category, 
                author = :author, 
                read_time = :read_time
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'slug' => $data['slug'],
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'],
            'cover_image' => $data['cover_image'] ?? null,
            'category' => $data['category'] ?? null,
            'author' => $data['author'] ?? 'Guest Writer',
            'read_time' => $data['read_time'] ?? null
        ]);
    }

    public function deleteStory($id) {
        $stmt = $this->db->prepare("DELETE FROM women_stories WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // Get related stories (excluding current story)
    public function getRelatedStories($currentStoryId, $limit = 3) {
        $stmt = $this->db->prepare("SELECT * FROM women_stories WHERE id != :id ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindValue(':id', $currentStoryId);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
