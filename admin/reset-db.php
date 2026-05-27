<?php
// admin/reset-db.php
// Secure Administrator Database Reset and Re-seed Utility

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Auth::requireCsrf();
        
        $confirmPassword = $_POST['reset_password'] ?? '';
        
        if ($confirmPassword !== 'Mido@123') {
            $error = "Incorrect reset password. Access denied.";
        } else {
            $db = Database::getInstance()->getConnection();
            
            // 1. Load schema.sql
            $schemaFile = PATH_ROOT . '/schema.sql';
            if (!file_exists($schemaFile)) {
                throw new Exception("Database schema file schema.sql not found.");
            }
            
            $sqlContent = file_get_contents($schemaFile);
            
            // Disable foreign key checks
            $db->exec("SET FOREIGN_KEY_CHECKS = 0;");
            
            // Execute the schema SQL. Since schema.sql might contain multiple statements, 
            // we split by statement delimiter (semicolon + newline) or execute directly if PDO config allows.
            // Using a simple command split to execute sequentially and log progress
            $queries = preg_split("/;[ \t]*\r?\n/", $sqlContent);
            
            $executedCount = 0;
            foreach ($queries as $query) {
                $query = trim($query);
                if (empty($query)) continue;
                
                // Skip comments
                if (str_starts_with($query, '--') || str_starts_with($query, '#')) {
                    continue;
                }
                
                $db->exec($query);
                $executedCount++;
            }
            
            // 2. Perform Post-Seed Customizations (Young Over 60 Branding)
            $db->exec("UPDATE site_settings SET 
                siteName = 'Young Over 60', 
                metaTitle = 'Young Over 60 | Active Travel & Inspiration', 
                metaDescription = 'Premium accessible travel guides, stories, and reviews tailored for active seniors and travelers over 60.'
                WHERE id = 1");
                
            $db->exec("UPDATE hero_slides SET 
                title = 'Young Over 60', 
                subtitle = 'Premium travel guides, reviews, and stories for active seniors' 
                WHERE id = 'slide-1'");
                
            // 3. Reset Admin User Credentials
            $adminEmail = 'admin@youngover60.com';
            $adminPass = password_hash('Password123', PASSWORD_BCRYPT);
            
            $stmt = $db->prepare("UPDATE users SET 
                email = :email, 
                password = :password,
                name = :name,
                website = :website
                WHERE role = 'ADMIN' OR id = 'admin-uuid-1'");
                
            $stmt->execute([
                'email' => $adminEmail,
                'password' => $adminPass,
                'name' => 'Site Admin',
                'website' => 'https://youngover60.com'
            ]);
            
            // Re-enable foreign key checks
            $db->exec("SET FOREIGN_KEY_CHECKS = 1;");
            
            // Clear caching to ensure brand refresh
            $pm->clearCache();
            
            $success = "Database successfully reset and re-seeded! Admin email is set to 'admin@youngover60.com' and password to 'Password123'.";
        }
    } catch (Exception $e) {
        $error = "Reset failed: " . $e->getMessage();
    }
}

// Set Header and SEO Meta
$seoPageType = 'admin';
require_once PATH_ROOT . '/includes/header.php';
?>

<div class="admin-layout">
    <!-- Sidebar -->
    <?php require_once PATH_ROOT . '/includes/admin-sidebar.php'; ?>

    <!-- Main Workspace -->
    <main class="admin-main">
        <div class="admin-header-row" style="border-bottom: 1px solid #e5e7eb; padding-bottom: 1.5rem; margin-bottom: 2rem;">
            <div>
                <h1 class="admin-title">Reset & Seed Database</h1>
                <p style="color: #888; font-size: 0.9rem; margin-top: 0.25rem;">Wipe the current database tables, rebuild them from the schema file, and populate with default seeded records.</p>
            </div>
        </div>

        <!-- Alert messages -->
        <?php if ($success): ?>
            <div class="form-status-alert success" style="margin-bottom: 2rem;">
                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span><?php echo e($success); ?></span>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="form-status-alert error" style="margin-bottom: 2rem;">
                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span><?php echo e($error); ?></span>
            </div>
        <?php endif; ?>

        <div style="max-width: 600px; margin: 0 auto;">
            <div class="admin-card-box" style="padding: 2.5rem; border: 1px solid #ef4444; border-radius: 12px; box-shadow: 0 10px 25px rgba(239, 68, 68, 0.05);">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <div style="display: inline-flex; align-items: center; justify-content: center; width: 4rem; height: 4rem; border-radius: 50%; background-color: #fef2f2; color: #ef4444; margin-bottom: 1rem;">
                        <svg style="width: 2rem; height: 2rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h2 style="font-size: 1.25rem; font-weight: 700; color: #111;">Destructive Action Warning</h2>
                    <p style="color: #666; font-size: 0.85rem; margin-top: 0.5rem; line-height: 1.5;">
                        Resetting the database will permanently delete all custom changes, articles, uploads records, sliders, podcasts, and newsletter subscribers.
                    </p>
                </div>

                <form action="<?php echo BASE_URL; ?>/admin/reset-db" method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <?php echo Auth::csrfInput(); ?>

                    <div class="admin-form-group">
                        <label for="reset-pass" style="color: #ef4444; font-weight: bold;">Authorization Password Required</label>
                        <input type="password" id="reset-pass" name="reset_password" class="admin-form-input" placeholder="Enter password to authorize reset" required style="border-color: #fca5a5;" />
                    </div>

                    <button type="submit" class="btn-primary" style="background-color: #ef4444; border-color: #ef4444; padding: 1rem; border-radius: 8px; font-weight: bold; width: 100%;" onclick="return confirm('WARNING: Are you absolutely sure you want to completely wipe the database? This cannot be undone.');">
                        WIP & SEED DATABASE
                    </button>
                </form>
            </div>
        </div>
    </main>
</div>

</body>
</html>
