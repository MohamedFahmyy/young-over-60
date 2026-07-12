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
            $lang = defined('CURRENT_LANG') ? CURRENT_LANG : 'en';
            $sql .= " AND (title_{$lang} LIKE :search1 OR excerpt_{$lang} LIKE :search2 OR content_{$lang} LIKE :search3)";
            $countSql .= " AND (title_{$lang} LIKE :search1 OR excerpt_{$lang} LIKE :search2 OR content_{$lang} LIKE :search3)";
            $binds['search1'] = '%' . $search . '%';
            $binds['search2'] = '%' . $search . '%';
            $binds['search3'] = '%' . $search . '%';
        }

        if ($category) {
            $sql .= " AND (category_en = :category_en OR category_ar = :category_ar)";
            $countSql .= " AND (category_en = :category_en OR category_ar = :category_ar)";
            $binds['category_en'] = $category;
            $binds['category_ar'] = $category;
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
        $lang = defined('CURRENT_LANG') ? CURRENT_LANG : 'en';
        $stmt = $this->db->query("SELECT DISTINCT category_{$lang} FROM women_stories WHERE category_{$lang} IS NOT NULL AND category_{$lang} != '' ORDER BY category_{$lang} ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getStoryById($id) {
        $stmt = $this->db->prepare("SELECT * FROM women_stories WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getStoryBySlug($slug) {
        if (empty($slug)) return null;
        $stmt = $this->db->prepare("SELECT * FROM women_stories WHERE slug_en = :slug_en OR slug_ar = :slug_ar OR slug_nl = :slug_nl LIMIT 1");
        $stmt->execute(['slug_en' => $slug, 'slug_ar' => $slug, 'slug_nl' => $slug]);
        return $stmt->fetch();
    }

    public function slugExists($slug, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM women_stories WHERE (slug_en = :slug_en OR slug_ar = :slug_ar OR slug_nl = :slug_nl)";
        $params = [
            'slug_en' => $slug,
            'slug_ar' => $slug,
            'slug_nl' => $slug
        ];
        if ($excludeId !== null) {
            $sql .= " AND id != :excludeId";
            $params['excludeId'] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function createStory($data) {
        $id = bin2hex(random_bytes(16));
        
        $slug_en = $data['slug_en'];
        $originalSlugEn = $slug_en;
        $counter = 1;
        while ($this->slugExists($slug_en)) {
            $slug_en = $originalSlugEn . '-' . $counter++;
        }
        
        $slug_ar = $data['slug_ar'] ?? null;
        if (!empty($slug_ar)) {
            $originalSlugAr = $slug_ar;
            $counter = 1;
            while ($this->slugExists($slug_ar)) {
                $slug_ar = $originalSlugAr . '-' . $counter++;
            }
        }
        
        $slug_nl = $data['slug_nl'] ?? null;
        if (!empty($slug_nl)) {
            $originalSlugNl = $slug_nl;
            $counter = 1;
            while ($this->slugExists($slug_nl)) {
                $slug_nl = $originalSlugNl . '-' . $counter++;
            }
        }

        $sql = "INSERT INTO women_stories (id, title_en, title_ar, title_nl, slug_en, slug_ar, slug_nl, excerpt_en, excerpt_ar, excerpt_nl, content_en, content_ar, content_nl, cover_image, alt_text_en, alt_text_ar, alt_text_nl, category_en, category_ar, category_nl, author_en, author_ar, author_nl, read_time) 
                VALUES (:id, :title_en, :title_ar, :title_nl, :slug_en, :slug_ar, :slug_nl, :excerpt_en, :excerpt_ar, :excerpt_nl, :content_en, :content_ar, :content_nl, :cover_image, :alt_text_en, :alt_text_ar, :alt_text_nl, :category_en, :category_ar, :category_nl, :author_en, :author_ar, :author_nl, :read_time)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title_en' => $data['title_en'],
            'title_ar' => $data['title_ar'] ?? null,
            'title_nl' => $data['title_nl'] ?? null,
            'slug_en' => $slug_en,
            'slug_ar' => $slug_ar,
            'slug_nl' => $slug_nl,
            'excerpt_en' => $data['excerpt_en'] ?? null,
            'excerpt_ar' => $data['excerpt_ar'] ?? null,
            'excerpt_nl' => $data['excerpt_nl'] ?? null,
            'content_en' => $data['content_en'],
            'content_ar' => $data['content_ar'] ?? null,
            'content_nl' => $data['content_nl'] ?? null,
            'cover_image' => $data['cover_image'] ?? null,
            'alt_text_en' => $data['alt_text_en'] ?? null,
            'alt_text_ar' => $data['alt_text_ar'] ?? null,
            'alt_text_nl' => $data['alt_text_nl'] ?? null,
            'category_en' => $data['category_en'] ?? null,
            'category_ar' => $data['category_ar'] ?? null,
            'category_nl' => $data['category_nl'] ?? null,
            'author_en' => $data['author_en'] ?? 'Guest Writer',
            'author_ar' => $data['author_ar'] ?? null,
            'author_nl' => $data['author_nl'] ?? null,
            'read_time' => $data['read_time'] ?? null
        ]);
    }

    public function updateStory($id, $data) {
        $slug_en = $data['slug_en'];
        $originalSlugEn = $slug_en;
        $counter = 1;
        while ($this->slugExists($slug_en, $id)) {
            $slug_en = $originalSlugEn . '-' . $counter++;
        }
        
        $slug_ar = $data['slug_ar'] ?? null;
        if (!empty($slug_ar)) {
            $originalSlugAr = $slug_ar;
            $counter = 1;
            while ($this->slugExists($slug_ar, $id)) {
                $slug_ar = $originalSlugAr . '-' . $counter++;
            }
        }
        
        $slug_nl = $data['slug_nl'] ?? null;
        if (!empty($slug_nl)) {
            $originalSlugNl = $slug_nl;
            $counter = 1;
            while ($this->slugExists($slug_nl, $id)) {
                $slug_nl = $originalSlugNl . '-' . $counter++;
            }
        }

        $sql = "UPDATE women_stories SET 
                title_en = :title_en, 
                title_ar = :title_ar, 
                title_nl = :title_nl, 
                slug_en = :slug_en, 
                slug_ar = :slug_ar, 
                slug_nl = :slug_nl, 
                excerpt_en = :excerpt_en, 
                excerpt_ar = :excerpt_ar, 
                excerpt_nl = :excerpt_nl, 
                content_en = :content_en, 
                content_ar = :content_ar, 
                content_nl = :content_nl, 
                cover_image = :cover_image, 
                alt_text_en = :alt_text_en,
                alt_text_ar = :alt_text_ar,
                alt_text_nl = :alt_text_nl,
                category_en = :category_en, 
                category_ar = :category_ar, 
                category_nl = :category_nl, 
                author_en = :author_en, 
                author_ar = :author_ar, 
                author_nl = :author_nl, 
                read_time = :read_time
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title_en' => $data['title_en'],
            'title_ar' => $data['title_ar'] ?? null,
            'title_nl' => $data['title_nl'] ?? null,
            'slug_en' => $slug_en,
            'slug_ar' => $slug_ar,
            'slug_nl' => $slug_nl,
            'excerpt_en' => $data['excerpt_en'] ?? null,
            'excerpt_ar' => $data['excerpt_ar'] ?? null,
            'excerpt_nl' => $data['excerpt_nl'] ?? null,
            'content_en' => $data['content_en'],
            'content_ar' => $data['content_ar'] ?? null,
            'content_nl' => $data['content_nl'] ?? null,
            'cover_image' => $data['cover_image'] ?? null,
            'alt_text_en' => $data['alt_text_en'] ?? null,
            'alt_text_ar' => $data['alt_text_ar'] ?? null,
            'alt_text_nl' => $data['alt_text_nl'] ?? null,
            'category_en' => $data['category_en'] ?? null,
            'category_ar' => $data['category_ar'] ?? null,
            'category_nl' => $data['category_nl'] ?? null,
            'author_en' => $data['author_en'] ?? 'Guest Writer',
            'author_ar' => $data['author_ar'] ?? null,
            'author_nl' => $data['author_nl'] ?? null,
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
