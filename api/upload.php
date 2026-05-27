<?php
// api/upload.php
// Secure Administrator File Upload API Endpoint

try {
    // 1. Guard route: Admin session authorization check
    if (!Auth::check()) {
        header("HTTP/1.0 401 Unauthorized");
        echo json_encode(['success' => false, 'error' => 'Unauthorized action. Please log in first.']);
        exit();
    }

    // 2. Accept POST requests only
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("HTTP/1.0 405 Method Not Allowed");
        echo json_encode(['success' => false, 'error' => 'Method not allowed.']);
        exit();
    }

    // 3. CSRF Verification
    $token = $_POST['csrf_token'] ?? '';
    if (!verifyCsrf($token)) {
        header("HTTP/1.0 403 Forbidden");
        echo json_encode(['success' => false, 'error' => 'CSRF verification failed.']);
        exit();
    }

    // 4. Validate file existence
    if (!isset($_FILES['file'])) {
        echo json_encode(['success' => false, 'error' => 'No file uploaded.']);
        exit();
    }

    // 5. Trigger UploadManager
    $uploader = new UploadManager();
    $type = $_GET['type'] ?? '';
    
    if ($type === 'audio' || strpos($_FILES['file']['type'], 'audio/') === 0) {
        $result = $uploader->uploadAudio($_FILES['file']);
        echo json_encode([
            'success' => true,
            'url' => $result['url'],
            'relativeUrl' => $result['relativeUrl'],
            'filename' => $result['filename'],
            'message' => 'Audio file uploaded successfully.'
        ]);
    } else {
        $result = $uploader->upload($_FILES['file']);
        echo json_encode([
            'success' => true,
            'url' => $result['url'],
            'relativeUrl' => $result['relativeUrl'],
            'filename' => $result['filename'],
            'message' => 'Image optimized and uploaded successfully.'
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
exit();
