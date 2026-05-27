<?php
// includes/header.php
// Global SEO & Theme HTML Header Layout Include

// Instantiate PostManager to fetch site settings
$pm = new PostManager();
$settings = $pm->getSiteSettings();
$pageType = $seoPageType ?? 'home';
$pageData = $seoPageData ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Dynamic SEO and Meta Output -->
    <?php SeoManager::renderMeta($pageType, $pageData); ?>
    
    <!-- Global CSS stylesheet -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css?v=<?php echo time(); ?>">

    <!-- Dynamic Theme Styling Overrides -->
    <style id="dynamic-theme-overrides">
        :root {
            --primary-color: <?php echo e($settings['primaryColor'] ?? '#2d5a88'); ?>;
            --secondary-color: <?php echo e($settings['secondaryColor'] ?? '#1e3c5a'); ?>;
            --accent-color: <?php echo e($settings['accentColor'] ?? '#eaeaea'); ?>;
            --background-color: <?php echo e($settings['backgroundColor'] ?? '#ffffff'); ?>;
            --text-color: <?php echo e($settings['textColor'] ?? '#111111'); ?>;
        }

        /* Dynamic Theme Logo Swaps */
        <?php if (!empty($settings['logoDarkUrl'])): ?>
        html.acc-high-contrast .logo-light {
            display: none !important;
        }
        html.acc-high-contrast .logo-dark {
            display: inline-block !important;
        }
        <?php endif; ?>
    </style>
    
    <!-- Load accessibility configuration immediately to prevent theme FOUC -->
    <script src="<?php echo BASE_URL; ?>/assets/js/accessibility.js"></script>
    
    <!-- Set Global Environment variables for AJAX scripts -->
    <script>
        window.BASE_URL = <?php echo json_encode(BASE_URL); ?>;
    </script>
</head>
<body>
