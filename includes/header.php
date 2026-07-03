<?php
// includes/header.php
// Global SEO & Theme HTML Header Layout Include

// Instantiate PostManager to fetch site settings
$pm = new PostManager();
$settings = $pm->getSiteSettings();
$pageType = $seoPageType ?? 'home';
$pageData = $seoPageData ?? null;

$activeLang = defined('CURRENT_LANG') ? CURRENT_LANG : 'en';
$langCfg = SUPPORTED_LANGUAGES[$activeLang] ?? ['dir' => 'ltr', 'font' => 'Outfit'];
?>
<?php
$isAdmin = (isset($seoPageType) && $seoPageType === 'admin') || (isset($pageType) && $pageType === 'admin');

// Build Google Fonts request URL
$fontsToLoad = [];
if ($isAdmin) {
    $fontsToLoad = ['Outfit', 'Inter', 'Cairo', 'Tajawal', 'Playfair Display', 'Lora', 'Merriweather', 'Amiri'];
} else {
    if (!empty($settings['fontFamily'])) {
        $fontsToLoad[] = $settings['fontFamily'];
    }
    if (!empty($settings['fontFamilyBody'])) {
        $fontsToLoad[] = $settings['fontFamilyBody'];
    }
    if (!empty($settings['fontFamilyAr'])) {
        $fontsToLoad[] = $settings['fontFamilyAr'];
    }
    if (!empty($settings['fontFamilyArBody'])) {
        $fontsToLoad[] = $settings['fontFamilyArBody'];
    }
}
$fontsToLoad = array_unique(array_filter($fontsToLoad));

$googleFontsUrl = "https://fonts.googleapis.com/css2";
$familyParams = [];
foreach ($fontsToLoad as $font) {
    $formattedFont = str_replace(' ', '+', $font);
    if (in_array($font, ['Playfair Display', 'Lora', 'Merriweather'])) {
        $familyParams[] = "family=" . $formattedFont . ":ital,wght@0,300..900;1,300..900";
    } elseif ($font === 'Cairo') {
        $familyParams[] = "family=" . $formattedFont . ":wght@200..1000";
    } elseif ($font === 'Tajawal') {
        $familyParams[] = "family=" . $formattedFont . ":wght@200;300;400;500;700;800;900";
    } elseif ($font === 'Almarai') {
        $familyParams[] = "family=" . $formattedFont . ":wght@300;400;700;800";
    } elseif ($font === 'Amiri') {
        $familyParams[] = "family=" . $formattedFont . ":ital,wght@0,400;0,700;1,400;1,700";
    } else {
        $familyParams[] = "family=" . $formattedFont . ":wght@100..900";
    }
}
$googleFontsUrl .= "?" . implode('&', $familyParams) . "&display=swap";
$themeClass = ($settings['themeMode'] ?? 'light') === 'dark' ? 'theme-dark' : 'theme-light';
?>
<!DOCTYPE html>
<html lang="<?php echo $activeLang; ?>" dir="<?php echo $langCfg['dir']; ?>" class="<?php echo $langCfg['dir'] === 'rtl' ? 'rtl' : ''; ?> <?php echo $themeClass; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Dynamic SEO and Meta Output -->
    <?php SeoManager::renderMeta($pageType, $pageData); ?>

    <!-- Preload Hero Image -->
    <?php if (!empty($heroImageToPreload)): ?>
    <link rel="preload" as="image" href="<?php echo e($heroImageToPreload); ?>">
    <?php endif; ?>
    
    <!-- Dynamic Google Fonts Link -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="<?php echo $googleFontsUrl; ?>" rel="stylesheet">

    <!-- Dynamic CSS Minification Compiler with Caching -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles-min.php?v=<?php echo file_exists(PATH_ROOT . '/assets/css/styles.css') ? filemtime(PATH_ROOT . '/assets/css/styles.css') : time(); ?>">

    <!-- Dynamic Theme Styling Overrides -->
    <style id="dynamic-theme-overrides">
        :root {
            --primary-color: <?php echo e($settings['primaryColor'] ?? '#0F4C81'); ?>;
            --secondary-color: <?php echo e($settings['secondaryColor'] ?? '#1e3c5a'); ?>;
            --accent-color: <?php echo e($settings['accentColor'] ?? '#D4A75C'); ?>;
            --background-color: <?php echo e($settings['backgroundColor'] ?? '#F8F6F2'); ?>;
            --surface-color: <?php echo e($settings['surfaceColor'] ?? '#ffffff'); ?>;
            --text-color: <?php echo e($settings['textColor'] ?? '#1F1F1F'); ?>;
            --border-color: <?php echo e($settings['borderColor'] ?? 'rgba(0,0,0,0.05)'); ?>;
            
            <?php if ($isAdmin): ?>
                --base-font-size: 16px;
                --heading-font: <?php echo $activeLang === 'ar' ? "'Cairo', sans-serif" : "'Outfit', sans-serif"; ?>;
                --body-font: <?php echo $activeLang === 'ar' ? "'Tajawal', sans-serif" : "'Inter', sans-serif"; ?>;
                
                --heading-weight: 700;
                --body-weight: 400;
                --letter-spacing: normal;
                --line-height: 1.6;
            <?php else: ?>
                --base-font-size: <?php echo e($settings['fontSize'] ?? '16px'); ?>;
                --heading-font: <?php echo $activeLang === 'ar' ? "'" . e($settings['fontFamilyAr'] ?? 'Cairo') . "', sans-serif" : "'" . e($settings['fontFamily'] ?? 'Outfit') . "', sans-serif"; ?>;
                --body-font: <?php echo $activeLang === 'ar' ? "'" . e($settings['fontFamilyArBody'] ?? 'Cairo') . "', sans-serif" : "'" . e($settings['fontFamilyBody'] ?? 'Inter') . "', sans-serif"; ?>;
                
                --heading-weight: <?php echo e($settings['headingWeight'] ?? '700'); ?>;
                --body-weight: <?php echo e($settings['bodyWeight'] ?? '400'); ?>;
                --letter-spacing: <?php echo e($settings['letterSpacing'] ?? 'normal'); ?>;
                --line-height: <?php echo e($settings['lineHeight'] ?? '1.8'); ?>;
            <?php endif; ?>
        }

        body {
            font-family: var(--body-font);
            font-weight: var(--body-weight);
            line-height: var(--line-height);
            background-color: var(--background-color);
            color: var(--text-color);
        }

        h1, h2, h3, h4, h5, h6, .editorial-heading {
            font-family: var(--heading-font);
            font-weight: var(--heading-weight);
            letter-spacing: var(--letter-spacing);
        }

        /* Dynamic Theme Logo Swaps */
        <?php if (!empty($settings['logoDarkUrl'])): ?>
        html.acc-high-contrast .logo-light,
        html.theme-dark .logo-light {
            display: none !important;
        }
        html.acc-high-contrast .logo-dark,
        html.theme-dark .logo-dark {
            display: inline-block !important;
        }
        <?php endif; ?>
    </style>
    
    <!-- Load accessibility configuration immediately to prevent theme FOUC -->
    <script src="<?php echo BASE_URL; ?>/assets/js/accessibility.js"></script>
    
    <!-- Set Global Environment variables for AJAX scripts -->
    <script>
        window.BASE_URL = <?php echo json_encode(BASE_URL); ?>;
        window.CURRENT_LANG = <?php echo json_encode($activeLang); ?>;
    </script>
</head>
<body>
