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
        $sql = "INSERT INTO hero_slides (id, title_en, title_ar, subtitle_en, subtitle_ar, button_text_en, button_text_ar, button_link, image_en, image_ar, alt_text_en, alt_text_ar, overlay_opacity, sort_order, is_active) 
                VALUES (:id, :title_en, :title_ar, :subtitle_en, :subtitle_ar, :button_text_en, :button_text_ar, :button_link, :image_en, :image_ar, :alt_text_en, :alt_text_ar, :overlay_opacity, :sort_order, :is_active)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title_en' => $data['title_en'],
            'title_ar' => $data['title_ar'] ?? null,
            'subtitle_en' => $data['subtitle_en'] ?? null,
            'subtitle_ar' => $data['subtitle_ar'] ?? null,
            'button_text_en' => $data['button_text_en'] ?? null,
            'button_text_ar' => $data['button_text_ar'] ?? null,
            'button_link' => $data['button_link'] ?? null,
            'image_en' => $data['image_en'] ?? null,
            'image_ar' => $data['image_ar'] ?? null,
            'alt_text_en' => $data['alt_text_en'] ?? null,
            'alt_text_ar' => $data['alt_text_ar'] ?? null,
            'overlay_opacity' => isset($data['overlay_opacity']) ? floatval($data['overlay_opacity']) : 0.50,
            'sort_order' => isset($data['sort_order']) ? (int)$data['sort_order'] : 0,
            'is_active' => !empty($data['is_active']) ? 1 : 0
        ]);
    }

    public function updateSlide($id, $data) {
        $sql = "UPDATE hero_slides SET 
                title_en = :title_en, 
                title_ar = :title_ar, 
                subtitle_en = :subtitle_en, 
                subtitle_ar = :subtitle_ar, 
                button_text_en = :button_text_en, 
                button_text_ar = :button_text_ar, 
                button_link = :button_link, 
                image_en = :image_en, 
                image_ar = :image_ar, 
                alt_text_en = :alt_text_en,
                alt_text_ar = :alt_text_ar,
                overlay_opacity = :overlay_opacity, 
                sort_order = :sort_order, 
                is_active = :is_active
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title_en' => $data['title_en'],
            'title_ar' => $data['title_ar'] ?? null,
            'subtitle_en' => $data['subtitle_en'] ?? null,
            'subtitle_ar' => $data['subtitle_ar'] ?? null,
            'button_text_en' => $data['button_text_en'] ?? null,
            'button_text_ar' => $data['button_text_ar'] ?? null,
            'button_link' => $data['button_link'] ?? null,
            'image_en' => $data['image_en'] ?? null,
            'image_ar' => $data['image_ar'] ?? null,
            'alt_text_en' => $data['alt_text_en'] ?? null,
            'alt_text_ar' => $data['alt_text_ar'] ?? null,
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
