<?php
// classes/UploadManager.php
// Secure File Upload & Dynamic GD Image Compression Service

class UploadManager {
    private $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg', 'image/x-icon', 'image/vnd.microsoft.icon', 'image/svg+xml', 'image/gif'];
    private $maxSize = 2097152; // 2MB
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Handles the file upload, applies compression and stores metadata in media database
    public function upload($file) {
        // 1. Error check
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new Exception("Invalid upload parameters.");
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new Exception("No file sent.");
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception("Exceeded file size limit.");
            default:
                throw new Exception("Unknown upload error.");
        }

        // 2. Validate Size
        if ($file['size'] > $this->maxSize) {
            throw new Exception("Exceeded 2MB size limit.");
        }

        // 3. Validate MIME Type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!in_array($mime, $this->allowedMimes)) {
            throw new Exception("Invalid file type. Only JPG, PNG, WebP, ICO, SVG, and GIF images are allowed.");
        }

        // 4. Generate Target Filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (empty($ext)) {
            $extMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            $ext = $extMap[$mime] ?? 'bin';
        }
        $filename = bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
        $targetPath = PATH_UPLOADS . '/' . $filename;
        $relativeUrl = '/uploads/' . $filename;

        // 5. Compress and Save
        $this->compressImage($file['tmp_name'], $targetPath, $mime);

        // Get Dimensions if image
        $width = null;
        $height = null;
        if ($dimensions = getimagesize($targetPath)) {
            $width = $dimensions[0];
            $height = $dimensions[1];
        }

        // 6. Save to Database
        $id = bin2hex(random_bytes(16));
        $stmt = $this->db->prepare("INSERT INTO media (id, filename, url, mimeType, fileSize, width, height) 
                                    VALUES (:id, :filename, :url, :mime, :size, :width, :height)");
        $stmt->execute([
            'id' => $id,
            'filename' => $filename,
            'url' => $relativeUrl,
            'mime' => $mime,
            'size' => filesize($targetPath),
            'width' => $width,
            'height' => $height
        ]);

        return [
            'id' => $id,
            'url' => BASE_URL . $relativeUrl,
            'relativeUrl' => $relativeUrl,
            'filename' => $filename
        ];
    }

    // Handles secure upload of audio files for podcasts
    public function uploadAudio($file) {
        // 1. Error check
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new Exception("Invalid upload parameters.");
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new Exception("No file sent.");
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception("Exceeded file size limit.");
            default:
                throw new Exception("Unknown upload error.");
        }

        // 2. Validate Size (allow up to 50MB for audio files)
        $maxAudioSize = 52428800; // 50MB
        if ($file['size'] > $maxAudioSize) {
            throw new Exception("Exceeded 50MB audio size limit.");
        }

        // 3. Validate MIME Type
        $allowedAudioMimes = ['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/x-wav', 'audio/ogg', 'audio/aac'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!in_array($mime, $allowedAudioMimes)) {
            throw new Exception("Invalid file type. Only MP3, WAV, AAC, and OGG audio files are allowed.");
        }

        // 4. Generate Target Filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (empty($ext)) {
            $extMap = ['audio/mpeg' => 'mp3', 'audio/mp3' => 'mp3', 'audio/wav' => 'wav', 'audio/ogg' => 'ogg'];
            $ext = $extMap[$mime] ?? 'bin';
        }
        $filename = bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
        $targetPath = PATH_UPLOADS . '/' . $filename;
        $relativeUrl = '/uploads/' . $filename;

        // 5. Move file directly (no compression for audio files)
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception("Failed to save audio file.");
        }

        // 6. Save to Database
        $id = bin2hex(random_bytes(16));
        $stmt = $this->db->prepare("INSERT INTO media (id, filename, url, mimeType, fileSize, width, height) 
                                    VALUES (:id, :filename, :url, :mime, :size, NULL, NULL)");
        $stmt->execute([
            'id' => $id,
            'filename' => $filename,
            'url' => $relativeUrl,
            'mime' => $mime,
            'size' => filesize($targetPath)
        ]);

        return [
            'id' => $id,
            'url' => BASE_URL . $relativeUrl,
            'relativeUrl' => $relativeUrl,
            'filename' => $filename
        ];
    }

    // Handles secure upload of video files for backgrounds
    public function uploadVideo($file) {
        // 1. Error check
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new Exception("Invalid upload parameters.");
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new Exception("No file sent.");
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception("Exceeded file size limit.");
            default:
                throw new Exception("Unknown upload error.");
        }

        // 2. Validate Size (allow up to 50MB for video files)
        $maxVideoSize = 52428800; // 50MB
        if ($file['size'] > $maxVideoSize) {
            throw new Exception("Exceeded 50MB video size limit.");
        }

        // 3. Validate MIME Type
        $allowedVideoMimes = ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime', 'video/x-matroska', 'video/avi', 'video/mpeg'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!in_array($mime, $allowedVideoMimes)) {
            throw new Exception("Invalid file type. Only MP4, WebM, OGG, and MOV video files are allowed.");
        }

        // 4. Generate Target Filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (empty($ext)) {
            $extMap = ['video/mp4' => 'mp4', 'video/webm' => 'webm', 'video/ogg' => 'ogg', 'video/quicktime' => 'mov'];
            $ext = $extMap[$mime] ?? 'bin';
        }
        $filename = bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
        $targetPath = PATH_UPLOADS . '/' . $filename;
        $relativeUrl = '/uploads/' . $filename;

        // 5. Move file directly
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception("Failed to save video file.");
        }

        // 6. Save to Database
        $id = bin2hex(random_bytes(16));
        $stmt = $this->db->prepare("INSERT INTO media (id, filename, url, mimeType, fileSize, width, height) 
                                    VALUES (:id, :filename, :url, :mime, :size, NULL, NULL)");
        $stmt->execute([
            'id' => $id,
            'filename' => $filename,
            'url' => $relativeUrl,
            'mime' => $mime,
            'size' => filesize($targetPath)
        ]);

        return [
            'id' => $id,
            'url' => BASE_URL . $relativeUrl,
            'relativeUrl' => $relativeUrl,
            'filename' => $filename
        ];
    }

    // Compress JPEG, PNG, and WebP using GD library
    private function compressImage($sourcePath, $destinationPath, $mime) {
        if (!extension_loaded('gd')) {
            // If GD is missing, fallback to standard moving
            move_uploaded_file($sourcePath, $destinationPath);
            return;
        }

        // Extract image source
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = @imagecreatefromjpeg($sourcePath);
                if ($image) {
                    // Save with 75% quality
                    imagejpeg($image, $destinationPath, 75);
                    imagedestroy($image);
                } else {
                    move_uploaded_file($sourcePath, $destinationPath);
                }
                break;

            case 'image/png':
                $image = @imagecreatefrompng($sourcePath);
                if ($image) {
                    // Enable transparency preservation
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                    // Compress (quality level 6 for PNG)
                    imagepng($image, $destinationPath, 6);
                    imagedestroy($image);
                } else {
                    move_uploaded_file($sourcePath, $destinationPath);
                }
                break;

            case 'image/webp':
                $image = @imagecreatefromwebp($sourcePath);
                if ($image) {
                    imagewebp($image, $destinationPath, 75);
                    imagedestroy($image);
                } else {
                    move_uploaded_file($sourcePath, $destinationPath);
                }
                break;

            default:
                move_uploaded_file($sourcePath, $destinationPath);
        }
    }
}
