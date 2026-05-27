<?php
// includes/navbar.php
// Premium Responsive Mega-Menu and Sidebar Overlays Include

// 1. Fetch Logo and Site Branding
$siteTitle = $settings['siteName'] ?? 'Young Over 60';
$logoUrl = $settings['logoUrl'] ?? null;

// Parse Title into parts
$titleParts = explode(' ', $siteTitle);
$firstWord = $titleParts[0] ?? '';
$remainingWords = implode(' ', array_slice($titleParts, 1));

// 2. Fetch Navigation Menus from Database
$db = Database::getInstance()->getConnection();
$menuQuery = $db->query("SELECT * FROM menus WHERE isActive = 1 ORDER BY `order` ASC");
$menus = $menuQuery->fetchAll();

$navigationData = [];
foreach ($menus as $menu) {
    // Fetch sections
    $secStmt = $db->prepare("SELECT * FROM menu_sections WHERE menuId = :menuId AND isActive = 1 ORDER BY `order` ASC");
    $secStmt->execute(['menuId' => $menu['id']]);
    $sections = $secStmt->fetchAll();

    $sectionsData = [];
    foreach ($sections as $section) {
        // Fetch links
        $linkStmt = $db->prepare("SELECT * FROM menu_links WHERE sectionId = :sectionId AND isActive = 1 ORDER BY `order` ASC");
        $linkStmt->execute(['sectionId' => $section['id']]);
        $links = $linkStmt->fetchAll();
        
        $sectionsData[] = [
            'id' => $section['id'],
            'title' => $section['title'],
            'links' => $links
        ];
    }

    // Fetch banners
    $bannerStmt = $db->prepare("SELECT * FROM menu_banners WHERE menuId = :menuId AND isActive = 1 LIMIT 1");
    $bannerStmt->execute(['menuId' => $menu['id']]);
    $banner = $bannerStmt->fetch();

    $navigationData[] = [
        'id' => $menu['id'],
        'title' => $menu['title'],
        'type' => $menu['type'],
        'layoutType' => $menu['layoutType'],
        'sections' => $sectionsData,
        'banner' => $banner ? $banner : null
    ];
}
?>
<nav class="navbar-sticky">
    <div class="container navbar-container">
        <!-- Brand Logo -->
        <a href="<?php echo BASE_URL; ?>/" class="logo-link" aria-label="Go to homepage">
            <?php 
            $logoDarkUrl = $settings['logoDarkUrl'] ?? null;
            if ($logoUrl || $logoDarkUrl): 
            ?>
                <div class="logo-container" style="display: flex; align-items: center; height: 100%;">
                    <?php if ($logoUrl): ?>
                        <img src="<?php echo e($logoUrl); ?>" alt="<?php echo e($siteTitle); ?>" class="logo-light" style="height:100%; width:100%; object-fit: contain;" />
                    <?php endif; ?>
                    <?php if ($logoDarkUrl): ?>
                        <img src="<?php echo e($logoDarkUrl); ?>" alt="<?php echo e($siteTitle); ?>" class="logo-dark" style="height:100%; width:100%; object-fit: contain; <?php echo $logoUrl ? 'display: none;' : ''; ?>" />
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <span class="logo-primary"><?php echo e($firstWord); ?></span>
                <span class="logo-sub"><?php echo e($remainingWords); ?></span>
            <?php endif; ?>
        </a>

        <!-- Desktop Menu -->
        <ul class="nav-menu" role="menubar">
            <?php foreach ($navigationData as $item): ?>
                <li class="nav-item" role="none">
                    <?php if ($item['type'] === 'SIMPLE' && !empty($item['sections'][0]['links'][0]['url'])): ?>
                        <!-- Simple Nav Link -->
                        <a href="<?php echo BASE_URL . $item['sections'][0]['links'][0]['url']; ?>" class="nav-link" role="menuitem">
                            <?php echo e($item['title']); ?>
                        </a>
                    <?php else: ?>
                        <!-- Mega Menu Trigger -->
                        <div class="nav-link" role="menuitem" aria-haspopup="true" tabindex="0">
                            <?php echo e($item['title']); ?>
                            <svg class="nav-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        
                        <!-- Dropdown panel -->
                        <div class="mega-menu" role="menu" aria-label="<?php echo e($item['title']); ?> Submenu">
                            <?php if ($item['layoutType'] === 'LIST'): ?>
                                <!-- List columns layout -->
                                <div class="mega-list-grid">
                                    <?php foreach ($item['sections'] as $sec): ?>
                                        <div>
                                            <h4 class="mega-column-title"><?php echo e($sec['title']); ?></h4>
                                            <ul class="mega-list">
                                                <?php foreach ($sec['links'] as $link): ?>
                                                    <li>
                                                        <a href="<?php echo BASE_URL . $link['url']; ?>" class="mega-list-link">
                                                            <?php echo e($link['title']); ?>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endforeach; ?>
                                    
                                    <!-- Banner side -->
                                    <?php if ($item['banner']): ?>
                                        <div>
                                            <a href="<?php echo BASE_URL . $item['banner']['url']; ?>" class="mega-banner" aria-label="Explore Featured">
                                                <img src="<?php echo e($item['banner']['image']); ?>" alt="" class="mega-banner-img" />
                                                <div class="mega-banner-overlay">
                                                    <span class="logo-sub" style="color: rgba(255,255,255,0.7); margin-bottom: 0.25rem;">Spotlight</span>
                                                    <h5 class="mega-banner-title"><?php echo e($item['banner']['title']); ?></h5>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php elseif ($item['layoutType'] === 'GRID'): ?>
                                <!-- Visual grid cards layout -->
                                <div class="mega-grid-layout">
                                    <?php 
                                    $linkCount = 0;
                                    foreach ($item['sections'] as $sec) {
                                        foreach ($sec['links'] as $link) {
                                            $linkCount++;
                                            if ($linkCount > 4) break 2; // Cap at 4 items
                                            ?>
                                            <a href="<?php echo BASE_URL . $link['url']; ?>" class="mega-grid-card">
                                                <img src="<?php echo e($link['image'] ?? '/images/hero-bg.png'); ?>" alt="" />
                                                <div class="mega-grid-overlay">
                                                    <h5 class="mega-grid-title"><?php echo e($link['title']); ?></h5>
                                                </div>
                                            </a>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
            
            <!-- Core pages static routes -->
            <li class="nav-item" role="none">
                <a href="<?php echo BASE_URL; ?>/podcasts" class="nav-link" role="menuitem">PODCASTS</a>
            </li>
            <li class="nav-item" role="none">
                <a href="<?php echo BASE_URL; ?>/women-stories" class="nav-link" role="menuitem">WOMEN STORIES</a>
            </li>
            <li class="nav-item" role="none">
                <a href="<?php echo BASE_URL; ?>/news" class="nav-link" role="menuitem">NEWS</a>
            </li>
            <li class="nav-item" role="none">
                <a href="<?php echo BASE_URL; ?>/accessibility" class="nav-link" role="menuitem">ACCESSIBILITY</a>
            </li>
            <li class="nav-item" role="none">
                <a href="<?php echo BASE_URL; ?>/contact" class="nav-link" role="menuitem">CONTACT</a>
            </li>
        </ul>

        <!-- Action Items (Search + Accessibility) -->
        <div class="navbar-actions">
            <button class="action-btn" data-open-search aria-label="Search posts">
                <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>
            <button class="action-btn action-btn-primary" data-open-accessibility aria-label="Open accessibility widget">
                <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                </svg>
            </button>
            <button class="mobile-toggle" aria-label="Toggle mobile menu">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </button>
        </div>
    </div>
</nav>

<!-- Backdrop Overlay for modals/sidebars -->
<div class="overlay-backdrop"></div>

<!-- Mobile Navigation Drawer -->
<div class="mobile-menu-drawer">
    <button class="btn-reset" data-open-accessibility style="margin-bottom: 2rem;">Accessibility Settings Widget</button>
    <ul class="mobile-nav-list">
        <?php foreach ($navigationData as $item): ?>
            <li class="mobile-nav-item">
                <?php if ($item['type'] === 'SIMPLE' && !empty($item['sections'][0]['links'][0]['url'])): ?>
                    <a href="<?php echo BASE_URL . $item['sections'][0]['links'][0]['url']; ?>" class="mobile-sub-link" style="font-weight:900; text-transform:uppercase;">
                        <?php echo e($item['title']); ?>
                    </a>
                <?php else: ?>
                    <button class="mobile-nav-btn" data-target="mob-<?php echo $item['id']; ?>">
                        <?php echo e($item['title']); ?>
                        <svg style="width: 1rem; height: 1rem; transition: transform 0.3s;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    
                    <div id="mob-<?php echo $item['id']; ?>" class="mobile-submenu">
                        <?php foreach ($item['sections'] as $sec): ?>
                            <h4 class="mobile-submenu-title"><?php echo e($sec['title']); ?></h4>
                            <?php foreach ($sec['links'] as $link): ?>
                                <a href="<?php echo BASE_URL . $link['url']; ?>" class="mobile-sub-link">
                                    <?php echo e($link['title']); ?>
                                </a>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
        
        <li class="mobile-nav-item">
            <a href="<?php echo BASE_URL; ?>/podcasts" class="mobile-sub-link" style="font-weight:900;">PODCASTS</a>
        </li>
        <li class="mobile-nav-item">
            <a href="<?php echo BASE_URL; ?>/women-stories" class="mobile-sub-link" style="font-weight:900;">WOMEN STORIES</a>
        </li>
        <li class="mobile-nav-item">
            <a href="<?php echo BASE_URL; ?>/news" class="mobile-sub-link" style="font-weight:900;">NEWS</a>
        </li>
        <li class="mobile-nav-item">
            <a href="<?php echo BASE_URL; ?>/accessibility" class="mobile-sub-link" style="font-weight:900;">ACCESSIBILITY</a>
        </li>
        <li class="mobile-nav-item">
            <a href="<?php echo BASE_URL; ?>/contact" class="mobile-sub-link" style="font-weight:900;">CONTACT</a>
        </li>
    </ul>
</div>

<!-- Search Overlay Modal -->
<div class="search-modal">
    <div class="container">
        <div class="search-container">
            <input type="text" class="search-input" placeholder="Type to search stories..." aria-label="Search stories query" />
            <button class="search-close" data-close-search aria-label="Close search">
                <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="search-results"></div>
    </div>
</div>

<!-- Accessibility Settings Sidebar Widget -->
<div class="accessibility-widget" role="dialog" aria-labelledby="acc-widget-title">
    <div class="widget-header">
        <span id="acc-widget-title" class="widget-title">Accessibility Tools</span>
        <button class="action-btn" data-close-accessibility style="color:#ffffff;" aria-label="Close accessibility options">
            <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    
    <div class="widget-body">
        <!-- Font Sizing -->
        <div class="widget-group">
            <span class="widget-label">Font Size Control</span>
            <div class="size-controls">
                <button class="size-btn size-btn-minus" data-accessibility-action="decrease-font" aria-label="Decrease text size">-</button>
                <div class="size-dots">
                    <div class="size-dot"></div>
                    <div class="size-dot"></div>
                    <div class="size-dot"></div>
                    <div class="size-dot"></div>
                    <div class="size-dot"></div>
                </div>
                <button class="size-btn size-btn-plus" data-accessibility-action="increase-font" aria-label="Increase text size">+</button>
            </div>
        </div>
        
        <!-- Accessibility Modes -->
        <div class="widget-group">
            <span class="widget-label">Toggle Layout Adjustments</span>
            <div class="widget-toggles">
                <button class="toggle-card" data-toggle="readableFont" aria-label="Toggle Arial readable font">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span class="toggle-card-label">Readable Font</span>
                </button>
                <button class="toggle-card" data-toggle="highContrast" aria-label="Toggle high contrast black and yellow mode">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    <span class="toggle-card-label">Contrast Mode</span>
                </button>
                <button class="toggle-card" data-toggle="underlineLinks" aria-label="Toggle underlines on all links">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                    <span class="toggle-card-label">Underline Links</span>
                </button>
                <button class="toggle-card" data-toggle="highlightLinks" aria-label="Toggle yellow highlights on all links">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    <span class="toggle-card-label">Highlight Links</span>
                </button>
            </div>
        </div>
    </div>
    
    <div class="widget-footer">
        <button class="btn-reset" data-accessibility-action="reset" aria-label="Reset all accessibility settings to default">Reset All Settings</button>
        <div class="widget-footer-brand">Accessibility powered by TWL Intelligence</div>
    </div>
</div>
