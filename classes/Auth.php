<?php
// classes/Auth.php
// Secure Session Authentication & Middleware Guard Class

class Auth {
    
    // Check if the current user is authenticated as administrator
    public static function check() {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }

    // Authenticate admin credentials
    public static function login($email, $password) {
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Regulate roles if necessary
            if ($user['role'] !== 'ADMIN') {
                return false;
            }
            
            // Set secure session variables
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_email'] = $user['email'];
            $_SESSION['admin_name'] = $user['name'];
            
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);
            return true;
        }
        
        return false;
    }

    // Log the user out safely
    public static function logout() {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    // Middleware: protect routes and redirect unauthorized actions to login
    public static function requireAdmin() {
        if (!self::check()) {
            header("Location: " . BASE_URL . "/admin/login");
            exit();
        }
    }

    // Inject secure CSRF token input
    public static function csrfInput() {
        return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '" />';
    }

    // Middleware: validate CSRF submission on POST actions
    public static function requireCsrf() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            if (!verifyCsrf($token)) {
                header("HTTP/1.0 403 Forbidden");
                die("CSRF Security Validation Failed.");
            }
        }
    }
}
