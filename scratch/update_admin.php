<?php
// scratch/update_admin.php
// Script to update Administrator account credentials in the database

try {
    require_once dirname(__DIR__) . '/includes/config.php';
    require_once dirname(__DIR__) . '/classes/Database.php';

    $db = Database::getInstance()->getConnection();
    echo "Connecting to database...\n";

    $email = 'admin@youngover60.com';
    $password = 'Password123';
    
    // Hash the password securely using BCrypt
    $hash = password_hash($password, PASSWORD_BCRYPT);

    // Update the admin user record
    $stmt = $db->prepare("UPDATE users SET 
        email = :email, 
        password = :password, 
        website = :website 
        WHERE role = 'ADMIN' OR id = 'admin-uuid-1'");
        
    $result = $stmt->execute([
        'email' => $email,
        'password' => $hash,
        'website' => 'https://youngover60.com'
    ]);

    if ($result) {
        echo "Admin account credentials successfully updated!\n";
        echo "New Email: " . $email . "\n";
        echo "New Password: " . $password . "\n";
    } else {
        echo "Failed to update credentials in database.\n";
    }

} catch (Exception $e) {
    echo "Error updating admin credentials: " . $e->getMessage() . "\n";
}
