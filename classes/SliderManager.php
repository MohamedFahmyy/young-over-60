<?php
// classes/SliderManager.php
// Hero Slides Model & Database Service Layer

class SliderManager {
    private $db;

    public function __construct($db = null) {
        if ($db instanceof PDO) {
            $this->db = $db;
        } else {
            $this->db = Database::getInstance()->getConnection();
        }
    }

    // Fetch active hero slides ordered by sort_order
    public function getSlides($includeInactive = false) {
        $sql = "SELECT * FROM hero_slides";
        if (!$includeInactive) {
            $sql .= " WHERE is_active = 1";
        }
        $sql .= " ORDER BY sort_order ASC, created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getSlideById($id) {
        $stmt = $this->db->prepare("SELECT * FROM hero_slides WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function createSlide($data) {
        $id = bin2hex(random_bytes(16));
        $sql = "INSERT INTO hero_slides (id, title, subtitle, button_text, button_link, image, overlay_opacity, sort_order, is_active) 
                VALUES (:id, :title, :subtitle, :button_text, :button_link, :image, :overlay_opacity, :sort_order, :is_active)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? null,
            'button_text' => $data['button_text'] ?? null,
            'button_link' => $data['button_link'] ?? null,
            'image' => $data['image'] ?? null,
            'overlay_opacity' => isset($data['overlay_opacity']) ? floatval($data['overlay_opacity']) : 0.50,
            'sort_order' => isset($data['sort_order']) ? (int)$data['sort_order'] : 0,
            'is_active' => !empty($data['is_active']) ? 1 : 0
        ]);
    }

    public function updateSlide($id, $data) {
        $sql = "UPDATE hero_slides SET 
                title = :title, 
                subtitle = :subtitle, 
                button_text = :button_text, 
                button_link = :button_link, 
                image = :image, 
                overlay_opacity = :overlay_opacity, 
                sort_order = :sort_order, 
                is_active = :is_active
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? null,
            'button_text' => $data['button_text'] ?? null,
            'button_link' => $data['button_link'] ?? null,
            'image' => $data['image'] ?? null,
            'overlay_opacity' => isset($data['overlay_opacity']) ? floatval($data['overlay_opacity']) : 0.50,
            'sort_order' => isset($data['sort_order']) ? (int)$data['sort_order'] : 0,
            'is_active' => !empty($data['is_active']) ? 1 : 0
        ]);
    }

    public function deleteSlide($id) {
        $stmt = $this->db->prepare("DELETE FROM hero_slides WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // Bulk update slides order
    public function updateOrder($orders) {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("UPDATE hero_slides SET sort_order = :sort_order WHERE id = :id");
            foreach ($orders as $id => $sortOrder) {
                $stmt->execute([
                    'id' => $id,
                    'sort_order' => (int)$sortOrder
                ]);
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
