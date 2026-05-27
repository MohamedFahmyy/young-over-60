<?php
// api/newsletter.php
// AJAX Newsletter Subscription API Endpoint

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("HTTP/1.0 405 Method Not Allowed");
        echo json_encode(['success' => false, 'error' => 'Method not allowed.']);
        exit();
    }

    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verifyCsrf($csrfToken)) {
        header("HTTP/1.0 403 Forbidden");
        echo json_encode(['success' => false, 'error' => 'Security token invalid.']);
        exit();
    }

    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        echo json_encode(['success' => false, 'error' => 'Please fill in a valid email address.']);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'Invalid email address format.']);
        exit();
    }

    $db = Database::getInstance()->getConnection();
    
    // Check if already subscribed to avoid duplicate DB key errors
    $checkStmt = $db->prepare("SELECT id FROM newsletters WHERE email = :email LIMIT 1");
    $checkStmt->execute(['email' => $email]);
    if ($checkStmt->fetch()) {
        echo json_encode(['success' => true, 'message' => 'You are already subscribed.']);
        exit();
    }

    // Insert new subscriber
    $id = bin2hex(random_bytes(16));
    $stmt = $db->prepare("INSERT INTO newsletters (id, email) VALUES (:id, :email)");
    $success = $stmt->execute([
        'id' => $id,
        'email' => $email
    ]);

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Thank you for subscribing!']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database write error.']);
    }
} catch (Exception $e) {
    header("HTTP/1.0 500 Internal Server Error");
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
exit();
