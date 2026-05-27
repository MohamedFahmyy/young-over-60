<?php
// api/contact.php
// AJAX Contact Submission Handler API Endpoint

try {
    // Only accept POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("HTTP/1.0 405 Method Not Allowed");
        echo json_encode(['success' => false, 'error' => 'Method not allowed.']);
        exit();
    }

    // CSRF verification
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verifyCsrf($csrfToken)) {
        header("HTTP/1.0 403 Forbidden");
        echo json_encode(['success' => false, 'error' => 'Security validation mismatch.']);
        exit();
    }

    // Inputs
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validation
    if (empty($name) || empty($email) || empty($phone) || empty($message)) {
        echo json_encode(['success' => false, 'error' => 'Please fill in all required fields.']);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'Please provide a valid email address.']);
        exit();
    }

    // Save to database
    $db = Database::getInstance()->getConnection();
    $id = bin2hex(random_bytes(16));
    
    $stmt = $db->prepare("INSERT INTO contact_submissions (id, name, email, phone, message) VALUES (:id, :name, :email, :phone, :message)");
    $success = $stmt->execute([
        'id' => $id,
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'message' => $message
    ]);

    if ($success) {
        // Send native plain text notification to admin
        $submission = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'message' => $message
        ];
        sendAdminNotificationEmail($submission);

        echo json_encode(['success' => true, 'message' => 'Message submitted successfully.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to save message to database.']);
    }
} catch (Exception $e) {
    header("HTTP/1.0 500 Internal Server Error");
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
exit();
