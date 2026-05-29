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
            $error = CURRENT_LANG === 'ar' ? 'كلمة المرور غير صحيحة. تم رفض الوصول.' : 'Incorrect reset password. Access denied.';
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
            
            // Execute the schema SQL.
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
            
            // Re-enable foreign key checks
            $db->exec("SET FOREIGN_KEY_CHECKS = 1;");
            
            // Clear caching to ensure brand refresh
            $pm = new PostManager();
            $pm->clearCache();
            
            $success = __("reset_db_success");
        }
    } catch (Exception $e) {
        $error = (CURRENT_LANG === 'ar' ? 'فشل إعادة التعيين: ' : 'Reset failed: ') . $e->getMessage();
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
                <h1 class="admin-title"><?php echo __("reset_db_title"); ?></h1>
                <p style="color: #888; font-size: 0.9rem; margin-top: 0.25rem;"><?php echo __("reset_db_desc"); ?></p>
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
                    <h2 style="font-size: 1.25rem; font-weight: 700; color: #ef4444;"><?php echo __("reset_db_warning_title"); ?></h2>
                    <p style="color: #666; font-size: 0.85rem; margin-top: 0.5rem; line-height: 1.5;">
                        <?php echo __("reset_db_warning_desc"); ?>
                    </p>
                </div>

                <form action="<?php echo BASE_URL; ?>/admin/reset-db" method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <?php echo Auth::csrfInput(); ?>

                    <div class="admin-form-group">
                        <label for="reset-pass" style="color: #ef4444; font-weight: bold;"><?php echo CURRENT_LANG === 'ar' ? 'مطلوب كلمة مرور التفويض' : 'Authorization Password Required'; ?></label>
                        <input type="password" id="reset-pass" name="reset_password" class="admin-form-input" placeholder="<?php echo CURRENT_LANG === 'ar' ? 'أدخل كلمة المرور لتفويض إعادة التعيين' : 'Enter password to authorize reset'; ?>" required style="border-color: #fca5a5;" />
                    </div>

                    <button type="submit" class="btn-primary" style="background-color: #ef4444; border-color: #ef4444; padding: 1rem; border-radius: 8px; font-weight: bold; width: 100%;" onclick="return confirm('<?php echo CURRENT_LANG === 'ar' ? 'تحذير: هل أنت متأكد تمامًا من رغبتك في مسح قاعدة البيانات بالكامل؟ لا يمكن التراجع عن هذا الإجراء.' : 'WARNING: Are you absolutely sure you want to completely wipe the database? This cannot be undone.'; ?>');">
                        <?php echo __("reset_db_btn"); ?>
                    </button>
                </form>
            </div>
        </div>
    </main>
</div>

</body>
</html>
