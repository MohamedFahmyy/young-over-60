<?php
// classes/TeamManager.php
// Team Members Service Layer (with prepared statements and input sanitization)

class TeamManager {
    private $db;

    public function __construct($db = null) {
        if ($db instanceof PDO) {
            $this->db = $db;
        } else {
            $this->db = Database::getInstance()->getConnection();
        }
    }

    /**
     * Sanitize text input values
     */
    private function sanitize($value, $allowHtml = false) {
        if ($value === null) {
            return null;
        }
        $val = trim($value);
        if (!$allowHtml) {
            $val = strip_tags($val);
        }
        return $val;
    }

    /**
     * Retrieve all team members
     * @param bool $includeInactive If true, retrieves hidden members as well
     * @return array
     */
    public function getTeamMembers($includeInactive = false) {
        try {
            $sql = "SELECT * FROM team_members";
            if (!$includeInactive) {
                $sql .= " WHERE is_active = 1";
            }
            $sql .= " ORDER BY display_order ASC, created_at DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("TeamManager::getTeamMembers failure: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetch a team member by ID
     * @param string $id
     * @return array|null
     */
    public function getTeamMemberById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM team_members WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $member = $stmt->fetch(PDO::FETCH_ASSOC);
            return $member ?: null;
        } catch (PDOException $e) {
            error_log("TeamManager::getTeamMemberById failure: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a team member record
     * @param array $data
     * @return bool
     */
    public function createTeamMember($data) {
        try {
            $id = bin2hex(random_bytes(16)); // UUID-like 32 char hex string
            
            $name_en = $this->sanitize($data['name_en'] ?? '');
            $name_ar = $this->sanitize($data['name_ar'] ?? null);
            $role_en = $this->sanitize($data['role_en'] ?? '');
            $role_ar = $this->sanitize($data['role_ar'] ?? null);
            $bio_en = $this->sanitize($data['bio_en'] ?? null, true); // Allow basic HTML formatting if needed
            $bio_ar = $this->sanitize($data['bio_ar'] ?? null, true);
            $image = $this->sanitize($data['image'] ?? null);
            $linkedin_url = $this->sanitize($data['linkedin_url'] ?? null);
            $display_order = isset($data['display_order']) ? (int)$data['display_order'] : 0;
            $is_active = isset($data['is_active']) ? (int)$data['is_active'] : 1;

            if (empty($name_en) || empty($role_en)) {
                return false;
            }

            $sql = "INSERT INTO team_members 
                    (id, name_en, name_ar, role_en, role_ar, bio_en, bio_ar, image, linkedin_url, display_order, is_active, created_at, updated_at) 
                    VALUES 
                    (:id, :name_en, :name_ar, :role_en, :role_ar, :bio_en, :bio_ar, :image, :linkedin_url, :display_order, :is_active, NOW(), NOW())";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':name_en' => $name_en,
                ':name_ar' => $name_ar,
                ':role_en' => $role_en,
                ':role_ar' => $role_ar,
                ':bio_en' => $bio_en,
                ':bio_ar' => $bio_ar,
                ':image' => $image,
                ':linkedin_url' => $linkedin_url,
                ':display_order' => $display_order,
                ':is_active' => $is_active
            ]);
        } catch (Exception $e) {
            error_log("TeamManager::createTeamMember failure: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update a team member record
     * @param string $id
     * @param array $data
     * @return bool
     */
    public function updateTeamMember($id, $data) {
        try {
            $member = $this->getTeamMemberById($id);
            if (!$member) {
                return false;
            }

            $name_en = $this->sanitize($data['name_en'] ?? '');
            $name_ar = $this->sanitize($data['name_ar'] ?? null);
            $role_en = $this->sanitize($data['role_en'] ?? '');
            $role_ar = $this->sanitize($data['role_ar'] ?? null);
            $bio_en = $this->sanitize($data['bio_en'] ?? null, true);
            $bio_ar = $this->sanitize($data['bio_ar'] ?? null, true);
            $image = $this->sanitize($data['image'] ?? null);
            $linkedin_url = $this->sanitize($data['linkedin_url'] ?? null);
            $display_order = isset($data['display_order']) ? (int)$data['display_order'] : 0;
            $is_active = isset($data['is_active']) ? (int)$data['is_active'] : 1;

            if (empty($name_en) || empty($role_en)) {
                return false;
            }

            // Handle image replacement / old image deletion
            if (!empty($member['image']) && $member['image'] !== $image) {
                $this->deleteImageFile($member['image']);
            }

            $sql = "UPDATE team_members SET 
                    name_en = :name_en, 
                    name_ar = :name_ar, 
                    role_en = :role_en, 
                    role_ar = :role_ar, 
                    bio_en = :bio_en, 
                    bio_ar = :bio_ar, 
                    image = :image, 
                    linkedin_url = :linkedin_url, 
                    display_order = :display_order, 
                    is_active = :is_active,
                    updated_at = NOW() 
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':name_en' => $name_en,
                ':name_ar' => $name_ar,
                ':role_en' => $role_en,
                ':role_ar' => $role_ar,
                ':bio_en' => $bio_en,
                ':bio_ar' => $bio_ar,
                ':image' => $image,
                ':linkedin_url' => $linkedin_url,
                ':display_order' => $display_order,
                ':is_active' => $is_active
            ]);
        } catch (Exception $e) {
            error_log("TeamManager::updateTeamMember failure: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a team member and cleanup their uploaded image
     * @param string $id
     * @return bool
     */
    public function deleteTeamMember($id) {
        try {
            $member = $this->getTeamMemberById($id);
            if (!$member) {
                return false;
            }

            // Delete the database entry first
            $stmt = $this->db->prepare("DELETE FROM team_members WHERE id = :id");
            $result = $stmt->execute([':id' => $id]);

            if ($result && !empty($member['image'])) {
                $this->deleteImageFile($member['image']);
            }

            return $result;
        } catch (PDOException $e) {
            error_log("TeamManager::deleteTeamMember failure: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update display orders in bulk
     * @param array $orders Array mapping member ID => display_order INT
     * @return bool
     */
    public function updateOrder($orders) {
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare("UPDATE team_members SET display_order = :order WHERE id = :id");
            foreach ($orders as $id => $order) {
                $stmt->execute([
                    ':order' => (int)$order,
                    ':id' => $id
                ]);
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("TeamManager::updateOrder failure: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Gracefully delete physical image file if not in use by other members
     */
    private function deleteImageFile($imagePath) {
        if (empty($imagePath)) return;
        
        // Prevent directory traversal and check if it starts with /uploads/
        if (strpos($imagePath, '/uploads/') === 0 && strpos($imagePath, '..') === false) {
            $fullPath = PATH_ROOT . $imagePath;
            if (file_exists($fullPath) && is_file($fullPath)) {
                // Ensure no other member is referencing the exact same image URL
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM team_members WHERE image = :image");
                $stmt->execute([':image' => $imagePath]);
                $count = (int)$stmt->fetchColumn();
                if ($count === 0) { // Should be 0 since database deletion just succeeded
                    @unlink($fullPath);
                }
            }
        }
    }
}
