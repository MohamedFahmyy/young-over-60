<?php
// admin/login.php
// Secure Administrator Login Screen - Young Over 60

// Redirect if already logged in
if (Auth::check()) {
    header("Location: " . BASE_URL . "/admin/dashboard");
    exit();
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate CSRF
        Auth::requireCsrf();
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $error = __('login_fields_required');
        } else {
            if (Auth::login($email, $password)) {
                // Success redirect
                header("Location: " . BASE_URL . "/admin/dashboard");
                exit();
            } else {
                $error = __('login_invalid');
            }
        }
    } catch (Exception $e) {
        $error = "An error occurred: " . $e->getMessage();
    }
}

// Meta title override
$seoPageType = 'admin';
require_once PATH_ROOT . '/includes/header.php';
?>

<style>
    body {
        background: radial-gradient(circle at 50% 50%, #fcfbf9 0%, #f1edf7 100%) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-height: 100vh !important;
        margin: 0 !important;
        font-family: 'Inter', ui-sans-serif, system-ui, sans-serif !important;
    }
    
    .admin-login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        min-height: 100vh;
        padding: 2rem;
        box-sizing: border-box;
    }
    
    .admin-login-card {
        max-width: 440px;
        width: 100%;
        background-color: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        box-shadow: 0 25px 60px rgba(27, 38, 59, 0.06), 0 10px 20px rgba(27, 38, 59, 0.02);
        padding: 3.5rem;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.7);
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        box-sizing: border-box;
        text-align: left;
    }
    
    html.rtl .admin-login-card {
        text-align: right;
    }
    
    .admin-login-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 35px 70px rgba(27, 38, 59, 0.09), 0 15px 30px rgba(27, 38, 59, 0.03);
    }
    
    .brand-title {
        font-family: 'DM Serif Text', Georgia, serif;
        font-size: 2.25rem;
        font-weight: 400;
        color: #2d5a88;
        text-decoration: none;
        line-height: 1.1;
        display: inline-block;
        transition: color 0.3s ease;
    }
    
    .brand-title:hover {
        color: #1e3c5a;
    }
    
    .brand-sub {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.25em;
        color: #888888;
        margin-top: 0.6rem;
        font-weight: 700;
    }
    
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .form-input-label {
        font-size: 0.65rem;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.12em;
        color: #888888;
        display: block;
    }
    
    .login-input {
        width: 100%;
        padding: 1rem 1.25rem;
        background-color: #faf9f6;
        border: 1px solid #dcdad5;
        border-radius: 8px !important;
        outline: none;
        font-size: 0.95rem;
        color: #111111;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        box-sizing: border-box;
    }
    
    .login-input:focus {
        border-color: #2d5a88;
        background-color: #ffffff;
        box-shadow: 0 0 0 4px rgba(45, 90, 136, 0.1);
    }
    
    .login-btn {
        width: 100%;
        margin-top: 1.5rem;
        background-color: #2d5a88;
        color: #ffffff;
        border: none;
        padding: 1.1rem;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        cursor: pointer;
        border-radius: 8px !important;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 4px 12px rgba(45, 90, 136, 0.15);
        box-sizing: border-box;
    }
    
    .login-btn:hover {
        background-color: #1e3c5a;
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(45, 90, 136, 0.25);
    }
    
    .login-btn:active {
        transform: translateY(0);
    }
    
    .back-link {
        font-size: 0.75rem;
        color: #888888;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        display: inline-block;
        position: relative;
        padding-bottom: 2px;
    }
    
    .back-link::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 1px;
        background-color: #2d5a88;
        transition: width 0.3s ease, left 0.3s ease;
    }
    
    .back-link:hover {
        color: #2d5a88;
    }
    
    .back-link:hover::after {
        width: 100%;
        left: 0;
    }
    
    .alert-error {
        background-color: #fef2f2;
        border: 1px solid #fee2e2;
        color: #b91c1c;
        padding: 1rem;
        border-radius: 8px;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        box-sizing: border-box;
    }
</style>

<div class="admin-login-container">
    <div class="admin-login-card">
        <div style="text-align: center; margin-bottom: 2.5rem;">
            <a href="<?php echo BASE_URL; ?>/" class="brand-title">
                <?php echo e($settings['siteName'] ?? 'Young Over 60'); ?>
            </a>
            <p class="brand-sub">
                <?php echo __('login_console'); ?>
            </p>
        </div>

        <?php if ($error): ?>
            <div class="alert-error">
                <svg style="width: 1.25rem; height: 1.25rem; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span><?php echo e($error); ?></span>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>/admin/login" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem;">
            <?php echo Auth::csrfInput(); ?>
            
            <div class="form-group">
                <label for="login-email" class="form-input-label"><?php echo __('login_email'); ?></label>
                <input type="email" id="login-email" name="email" class="login-input" placeholder="admin@youngover60.com" required />
            </div>

            <div class="form-group">
                <label for="login-password" class="form-input-label"><?php echo __('login_password'); ?></label>
                <input type="password" id="login-password" name="password" class="login-input" placeholder="••••••••" required />
            </div>

            <button type="submit" class="login-btn">
                <?php echo __('login_sign_in'); ?>
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 2rem;">
            <a href="<?php echo BASE_URL; ?>/" class="back-link">
                <?php echo __('login_back_home'); ?>
            </a>
        </div>
    </div>
</div>

</body>
</html>
