<?php
// admin/settings.php
// Administrator Site Settings & Branding Control Panel

$error = null;
$success = null;

// Handle Settings Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Auth::requireCsrf();
        
        $data = [
            'siteName' => trim($_POST['siteName'] ?? 'Young Over 60'),
            'logoUrl' => trim($_POST['logoUrl'] ?? ''),
            'logoDarkUrl' => trim($_POST['logoDarkUrl'] ?? ''),
            'faviconUrl' => trim($_POST['faviconUrl'] ?? ''),
            'heroBackgroundUrl' => trim($_POST['heroBackgroundUrl'] ?? '/images/hero-bg.png'),
            'metaTitle' => trim($_POST['metaTitle'] ?? ''),
            'metaDescription' => trim($_POST['metaDescription'] ?? ''),
            'ogImage' => trim($_POST['ogImage'] ?? ''),
            'primaryColor' => trim($_POST['primaryColor'] ?? '#2d5a88'),
            'secondaryColor' => trim($_POST['secondaryColor'] ?? '#1e3c5a'),
            'accentColor' => trim($_POST['accentColor'] ?? '#eaeaea'),
            'backgroundColor' => trim($_POST['backgroundColor'] ?? '#ffffff'),
            'textColor' => trim($_POST['textColor'] ?? '#111111')
        ];

        if (empty($data['siteName'])) {
            $error = "Site Name is a required field.";
        } else {
            if ($pm->updateSiteSettings($data)) {
                $success = "Site settings successfully updated!";
                // Refresh settings array
                $settings = $pm->getSiteSettings();
            } else {
                $error = "Failed to update settings in the database.";
            }
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Set SEO Meta & Header
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
                <h1 class="admin-title">Site Settings</h1>
                <p style="color: #888; font-size: 0.9rem; margin-top: 0.25rem;">Configure your platform logo, favicon, theme design system, and global SEO targets.</p>
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

        <form id="settingsForm" action="<?php echo BASE_URL; ?>/admin/settings" method="POST" style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
            <?php echo Auth::csrfInput(); ?>

            <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 2rem; align-items: start;">
                
                <!-- Left Column: Settings Fields -->
                <div style="display: flex; flex-direction: column; gap: 2rem;">
                    
                    <!-- General Settings -->
                    <div class="admin-card-box" style="margin: 0; padding: 2rem; display: flex; flex-direction: column; gap: 1.25rem;">
                        <h3 style="font-size: 1.05rem; font-weight: 700; border-bottom: 1px solid #eee; padding-bottom: 0.75rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            General Site Identity
                        </h3>
                        
                        <div class="admin-form-group">
                            <label for="set-sitename">Site Name</label>
                            <input type="text" id="set-sitename" name="siteName" value="<?php echo e($settings['siteName'] ?? 'Young Over 60'); ?>" class="admin-form-input" required />
                        </div>

                        <div class="admin-form-group">
                            <label for="set-herobg">Hero Section Background Image URL</label>
                            <input type="text" id="set-herobg" name="heroBackgroundUrl" value="<?php echo e($settings['heroBackgroundUrl'] ?? '/images/hero-bg.png'); ?>" class="admin-form-input" />
                        </div>
                    </div>

                    <!-- SEO Meta Configuration -->
                    <div class="admin-card-box" style="margin: 0; padding: 2rem; display: flex; flex-direction: column; gap: 1.25rem;">
                        <h3 style="font-size: 1.05rem; font-weight: 700; border-bottom: 1px solid #eee; padding-bottom: 0.75rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            Global SEO Configuration (Search Engine Optimization)
                        </h3>
                        
                        <div class="admin-form-group">
                            <label for="set-metatitle">Default Meta Title</label>
                            <input type="text" id="set-metatitle" name="metaTitle" value="<?php echo e($settings['metaTitle'] ?? ''); ?>" class="admin-form-input" placeholder="e.g. Young Over 60 | Active Senior Inspiration" />
                            <span style="font-size: 0.65rem; color: #888; margin-top: 0.25rem;">Recommended length is under 60 characters.</span>
                        </div>

                        <div class="admin-form-group">
                            <label for="set-metadesc">Default Meta Description</label>
                            <textarea id="set-metadesc" name="metaDescription" rows="4" class="admin-form-textarea" placeholder="Describe the focus of the website for search results..."><?php echo e($settings['metaDescription'] ?? ''); ?></textarea>
                            <span style="font-size: 0.65rem; color: #888; margin-top: 0.25rem;">Recommended length is 150-160 characters.</span>
                        </div>
                    </div>

                    <!-- Brand Color Palette -->
                    <div class="admin-card-box" style="margin: 0; padding: 2rem; display: flex; flex-direction: column; gap: 1.25rem;">
                        <h3 style="font-size: 1.05rem; font-weight: 700; border-bottom: 1px solid #eee; padding-bottom: 0.75rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                            Design System Palette
                        </h3>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            
                            <!-- Primary -->
                            <div class="admin-form-group">
                                <label for="color-primary">Primary Branding Color</label>
                                <div style="display: flex; gap: 0.5rem;">
                                    <input type="color" value="<?php echo e($settings['primaryColor'] ?? '#2d5a88'); ?>" oninput="document.getElementById('color-primary-text').value = this.value" style="width: 42px; height: 42px; border: 1px solid #ddd; border-radius: 8px; cursor: pointer; padding: 0;" />
                                    <input type="text" id="color-primary-text" name="primaryColor" value="<?php echo e($settings['primaryColor'] ?? '#2d5a88'); ?>" class="admin-form-input" style="flex-grow:1;" />
                                </div>
                            </div>

                            <!-- Secondary -->
                            <div class="admin-form-group">
                                <label for="color-secondary">Secondary Color</label>
                                <div style="display: flex; gap: 0.5rem;">
                                    <input type="color" value="<?php echo e($settings['secondaryColor'] ?? '#1e3c5a'); ?>" oninput="document.getElementById('color-secondary-text').value = this.value" style="width: 42px; height: 42px; border: 1px solid #ddd; border-radius: 8px; cursor: pointer; padding: 0;" />
                                    <input type="text" id="color-secondary-text" name="secondaryColor" value="<?php echo e($settings['secondaryColor'] ?? '#1e3c5a'); ?>" class="admin-form-input" style="flex-grow:1;" />
                                </div>
                            </div>

                            <!-- Accent -->
                            <div class="admin-form-group">
                                <label for="color-accent">Accent Borders/Lines Color</label>
                                <div style="display: flex; gap: 0.5rem;">
                                    <input type="color" value="<?php echo e($settings['accentColor'] ?? '#eaeaea'); ?>" oninput="document.getElementById('color-accent-text').value = this.value" style="width: 42px; height: 42px; border: 1px solid #ddd; border-radius: 8px; cursor: pointer; padding: 0;" />
                                    <input type="text" id="color-accent-text" name="accentColor" value="<?php echo e($settings['accentColor'] ?? '#eaeaea'); ?>" class="admin-form-input" style="flex-grow:1;" />
                                </div>
                            </div>

                            <!-- Background -->
                            <div class="admin-form-group">
                                <label for="color-bg">Page Background Color</label>
                                <div style="display: flex; gap: 0.5rem;">
                                    <input type="color" value="<?php echo e($settings['backgroundColor'] ?? '#ffffff'); ?>" oninput="document.getElementById('color-bg-text').value = this.value" style="width: 42px; height: 42px; border: 1px solid #ddd; border-radius: 8px; cursor: pointer; padding: 0;" />
                                    <input type="text" id="color-bg-text" name="backgroundColor" value="<?php echo e($settings['backgroundColor'] ?? '#ffffff'); ?>" class="admin-form-input" style="flex-grow:1;" />
                                </div>
                            </div>

                            <!-- Text -->
                            <div class="admin-form-group" style="grid-column: span 2;">
                                <label for="color-text">Base Body Typography Color</label>
                                <div style="display: flex; gap: 0.5rem;">
                                    <input type="color" value="<?php echo e($settings['textColor'] ?? '#111111'); ?>" oninput="document.getElementById('color-text-text').value = this.value" style="width: 42px; height: 42px; border: 1px solid #ddd; border-radius: 8px; cursor: pointer; padding: 0;" />
                                    <input type="text" id="color-text-text" name="textColor" value="<?php echo e($settings['textColor'] ?? '#111111'); ?>" class="admin-form-input" style="flex-grow:1;" />
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Right Column: Logos & Favicon Upload Cards -->
                <div style="display: flex; flex-direction: column; gap: 2rem;">
                    
                    <!-- Light Theme Logo -->
                    <div class="admin-card-box" style="margin: 0; padding: 2rem;">
                        <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                            ☀️ Light Theme Logo
                        </h3>
                        <p style="font-size: 0.65rem; color:#888; margin-bottom: 1.25rem;">Used on white or light backgrounds across the site header and menus.</p>
                        
                        <div class="admin-form-group">
                            <label for="set-logo">Logo Image URL</label>
                            <input type="text" id="set-logo" name="logoUrl" value="<?php echo e($settings['logoUrl'] ?? ''); ?>" class="admin-form-input" placeholder="/uploads/..." />
                        </div>
                        
                        <div id="logoDropzone" class="image-upload-preview-box" style="margin-top: 1rem; border: 1px dashed var(--accent-color); padding: 1.5rem 1rem; border-radius: 8px; text-align: center; cursor: pointer; transition: var(--transition-smooth);">
                            <p style="font-size: 0.7rem; color: #777; margin: 0;">Click or drop image here</p>
                            <span style="font-size: 0.55rem; color:#999; display:block; margin-top:0.25rem;">PNG, JPG, WebP, SVG (Max 2MB)</span>
                            <input type="file" id="logoFileSelector" accept="image/*" style="display:none;" />
                            <div id="logoStatusText" style="font-size:0.65rem; color: var(--primary-color); margin-top:0.5rem; display:none;">Uploading...</div>
                            <img id="logoPreview" src="<?php echo $settings['logoUrl'] ? BASE_URL . $settings['logoUrl'] : ''; ?>" style="display: <?php echo $settings['logoUrl'] ? 'inline-block' : 'none'; ?>; max-height: 50px; max-width: 100%; object-fit: contain; margin-top: 1rem; border-radius: 4px; background: #f9f9f9; padding: 0.25rem;" />
                        </div>
                    </div>

                    <!-- Dark Theme Logo -->
                    <div class="admin-card-box" style="margin: 0; padding: 2rem;">
                        <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                            🌙 Dark/Contrast Theme Logo
                        </h3>
                        <p style="font-size: 0.65rem; color:#888; margin-bottom: 1.25rem;">Swapped automatically when High Contrast accessibility mode is active.</p>
                        
                        <div class="admin-form-group">
                            <label for="set-logo-dark">Dark Logo Image URL</label>
                            <input type="text" id="set-logo-dark" name="logoDarkUrl" value="<?php echo e($settings['logoDarkUrl'] ?? ''); ?>" class="admin-form-input" placeholder="/uploads/..." />
                        </div>
                        
                        <div id="logoDarkDropzone" class="image-upload-preview-box" style="margin-top: 1rem; border: 1px dashed var(--accent-color); padding: 1.5rem 1rem; border-radius: 8px; text-align: center; cursor: pointer; transition: var(--transition-smooth);">
                            <p style="font-size: 0.7rem; color: #777; margin: 0;">Click or drop image here</p>
                            <span style="font-size: 0.55rem; color:#999; display:block; margin-top:0.25rem;">PNG, JPG, WebP, SVG (Max 2MB)</span>
                            <input type="file" id="logoDarkFileSelector" accept="image/*" style="display:none;" />
                            <div id="logoDarkStatusText" style="font-size:0.65rem; color: var(--primary-color); margin-top:0.5rem; display:none;">Uploading...</div>
                            <img id="logoDarkPreview" src="<?php echo !empty($settings['logoDarkUrl']) ? BASE_URL . $settings['logoDarkUrl'] : ''; ?>" style="display: <?php echo !empty($settings['logoDarkUrl']) ? 'inline-block' : 'none'; ?>; max-height: 50px; max-width: 100%; object-fit: contain; margin-top: 1rem; border-radius: 4px; background: #222; padding: 0.25rem;" />
                        </div>
                    </div>

                    <!-- Favicon Upload Card -->
                    <div class="admin-card-box" style="margin: 0; padding: 2rem;">
                        <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                            🎯 Browser Favicon
                        </h3>
                        <p style="font-size: 0.65rem; color:#888; margin-bottom: 1.25rem;">The tiny tab icon displayed in browsers next to your page title.</p>
                        
                        <div class="admin-form-group">
                            <label for="set-favicon">Favicon URL</label>
                            <input type="text" id="set-favicon" name="faviconUrl" value="<?php echo e($settings['faviconUrl'] ?? ''); ?>" class="admin-form-input" placeholder="/favicon.ico or /uploads/..." />
                        </div>
                        
                        <div id="faviconDropzone" class="image-upload-preview-box" style="margin-top: 1rem; border: 1px dashed var(--accent-color); padding: 1.5rem 1rem; border-radius: 8px; text-align: center; cursor: pointer; transition: var(--transition-smooth);">
                            <p style="font-size: 0.7rem; color: #777; margin: 0;">Click or drop favicon here</p>
                            <span style="font-size: 0.55rem; color:#999; display:block; margin-top:0.25rem;">ICO, PNG, SVG, GIF (Max 2MB)</span>
                            <input type="file" id="faviconFileSelector" accept="image/x-icon,image/vnd.microsoft.icon,image/png,image/svg+xml,image/gif" style="display:none;" />
                            <div id="faviconStatusText" style="font-size:0.65rem; color: var(--primary-color); margin-top:0.5rem; display:none;">Uploading...</div>
                            <img id="faviconPreview" src="<?php echo $settings['faviconUrl'] ? BASE_URL . $settings['faviconUrl'] : ''; ?>" style="display: <?php echo $settings['faviconUrl'] ? 'inline-block' : 'none'; ?>; max-height: 32px; max-width: 32px; object-fit: contain; margin-top: 1rem; border-radius: 4px; padding: 0.15rem; background: #fff;" />
                        </div>
                    </div>

                    <!-- SEO Open Graph Image Card -->
                    <div class="admin-card-box" style="margin: 0; padding: 2rem;">
                        <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                            🔗 Social Share Image (OG Image)
                        </h3>
                        <p style="font-size: 0.65rem; color:#888; margin-bottom: 1.25rem;">The fallback preview banner shown when sharing your site link on Facebook, Twitter, WhatsApp.</p>
                        
                        <div class="admin-form-group">
                            <label for="set-ogimage">Social Share Image URL</label>
                            <input type="text" id="set-ogimage" name="ogImage" value="<?php echo e($settings['ogImage'] ?? ''); ?>" class="admin-form-input" placeholder="/uploads/..." />
                        </div>
                        
                        <div id="ogDropzone" class="image-upload-preview-box" style="margin-top: 1rem; border: 1px dashed var(--accent-color); padding: 1.5rem 1rem; border-radius: 8px; text-align: center; cursor: pointer; transition: var(--transition-smooth);">
                            <p style="font-size: 0.7rem; color: #777; margin: 0;">Click or drop image here</p>
                            <span style="font-size: 0.55rem; color:#999; display:block; margin-top:0.25rem;">PNG, JPG, WebP (Max 2MB)</span>
                            <input type="file" id="ogFileSelector" accept="image/*" style="display:none;" />
                            <div id="ogStatusText" style="font-size:0.65rem; color: var(--primary-color); margin-top:0.5rem; display:none;">Uploading...</div>
                            <img id="ogPreview" src="<?php echo $settings['ogImage'] ? BASE_URL . $settings['ogImage'] : ''; ?>" style="display: <?php echo $settings['ogImage'] ? 'inline-block' : 'none'; ?>; max-height: 80px; max-width: 100%; object-fit: cover; margin-top: 1rem; border-radius: 4px;" />
                        </div>
                    </div>

                </div>
            </div>

            <!-- Form Actions -->
            <div style="border-top: 1px solid #e5e7eb; padding-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
                <button type="submit" class="btn-primary" style="padding: 1rem 3.5rem; border-radius: 8px;">Save Settings</button>
            </div>

        </form>

        <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Drag and drop helper binds
            setupUploader('logoDropzone', 'logoFileSelector', 'set-logo', 'logoPreview', 'logoStatusText');
            setupUploader('logoDarkDropzone', 'logoDarkFileSelector', 'set-logo-dark', 'logoDarkPreview', 'logoDarkStatusText');
            setupUploader('faviconDropzone', 'faviconFileSelector', 'set-favicon', 'faviconPreview', 'faviconStatusText');
            setupUploader('ogDropzone', 'ogFileSelector', 'set-ogimage', 'ogPreview', 'ogStatusText');

            function setupUploader(dropzoneId, selectorId, inputId, previewId, statusTextId) {
                const dropzone = document.getElementById(dropzoneId);
                const selector = document.getElementById(selectorId);
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);
                const statusBox = document.getElementById(statusTextId);

                if (!dropzone || !selector) return;

                dropzone.addEventListener('click', () => selector.click());
                selector.addEventListener('change', function() {
                    if (this.files.length > 0) uploadFile(this.files[0], input, preview, statusBox);
                });

                dropzone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropzone.style.borderColor = 'var(--primary-color)';
                    dropzone.style.backgroundColor = 'rgba(45, 90, 136, 0.03)';
                });

                dropzone.addEventListener('dragleave', () => {
                    dropzone.style.borderColor = '';
                    dropzone.style.backgroundColor = '';
                });

                dropzone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropzone.style.borderColor = '';
                    dropzone.style.backgroundColor = '';
                    if (e.dataTransfer.files.length > 0) {
                        uploadFile(e.dataTransfer.files[0], input, preview, statusBox);
                    }
                });
            }

            function uploadFile(file, input, preview, statusBox) {
                const formData = new FormData();
                formData.append('file', file);
                formData.append('csrf_token', '<?php echo csrfToken(); ?>');

                statusBox.style.display = 'block';
                statusBox.textContent = 'Uploading...';
                statusBox.style.color = 'var(--primary-color)';

                const url = '<?php echo BASE_URL; ?>/api/upload';

                fetch(url, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(result => {
                    if (result.success) {
                        statusBox.textContent = 'Upload complete!';
                        statusBox.style.color = '#10b981';
                        
                        // Set value in text input
                        input.value = result.relativeUrl;
                        
                        // Display visual preview
                        preview.src = result.url;
                        preview.style.display = 'inline-block';
                    } else {
                        statusBox.textContent = 'Error: ' + (result.error || 'Upload failed');
                        statusBox.style.color = '#ef4444';
                    }
                })
                .catch(err => {
                    statusBox.textContent = 'Upload failed. Network error.';
                    statusBox.style.color = '#ef4444';
                    console.error("AJAX Upload failed", err);
                });
            }
        });
        </script>
    </main>
</div>

</body>
</html>
