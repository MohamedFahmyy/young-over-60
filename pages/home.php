<?php
// pages/home.php
// Premium Travel Website Homepage View

// Meta data settings
$seoPageType = 'home';

// Ensure system paths are defined
if (!defined('PATH_ROOT')) {
    define('PATH_ROOT', dirname(__DIR__));
}

// 1. Properly include required files
require_once PATH_ROOT . '/config/database.php';
require_once PATH_ROOT . '/classes/Database.php';
require_once PATH_ROOT . '/classes/PostManager.php';

// 2. Initialize the PostManager object safely
if (!isset($pm) || !($pm instanceof PostManager)) {
    try {
        $database = Database::getInstance();
        $pdo = $database ? $database->getConnection() : null;
        if ($pdo instanceof PDO) {
            $pm = new PostManager($pdo);
        } else {
            $pm = null;
        }
    } catch (Exception $e) {
        $pm = null;
        error_log("Database / PostManager initialization failed: " . $e->getMessage());
    }
}

// 3. Add null safety checks for method calls
$categories = [];
if ($pm instanceof PostManager) {
    try {
        $categories = $pm->getCategories() ?: [];
    } catch (Exception $e) {
        error_log("Failed to fetch categories: " . $e->getMessage());
    }
}

// Filter categories for "Plan Your Trip" (Accommodation, Inspiration, Tips, Transport)
$planCats = [];
if (!empty($categories)) {
    $planCats = array_filter($categories, function($c) {
        return isset($c['slug']) && in_array($c['slug'], ['accommodation', 'inspiration', 'tips-tricks', 'transport']);
    });
    // Fallback if none exist
    if (empty($planCats)) {
        $planCats = array_slice($categories, 0, 4);
    }
}

// Filter categories for "Experiences" (Animal Encounters, Cruises, Family Travel, Food & Drink)
$expCats = [];
if (!empty($categories)) {
    $expCats = array_filter($categories, function($c) {
        return isset($c['slug']) && in_array($c['slug'], ['animal-encounters', 'cruises', 'family-travel', 'food-drink']);
    });
    // Fallback if none exist
    if (empty($expCats)) {
        $expCats = array_slice($categories, 4, 3);
    }
}

// Fetch initial posts for the first tab in each filter grid
$firstPlanCat = reset($planCats);
$initialPlanPosts = [];
if ($firstPlanCat && $pm instanceof PostManager) {
    try {
        $planPostsResult = $pm->getPosts(['categoryId' => $firstPlanCat['id'], 'limit' => 3, 'status' => 'PUBLISHED']);
        $initialPlanPosts = $planPostsResult['data'] ?? [];
    } catch (Exception $e) {
        error_log("Failed to fetch plan posts: " . $e->getMessage());
    }
}

$firstExpCat = reset($expCats);
$initialExpPosts = [];
if ($firstExpCat && $pm instanceof PostManager) {
    try {
        $expPostsResult = $pm->getPosts(['categoryId' => $firstExpCat['id'], 'limit' => 3, 'status' => 'PUBLISHED']);
        $initialExpPosts = $expPostsResult['data'] ?? [];
    } catch (Exception $e) {
        error_log("Failed to fetch experience posts: " . $e->getMessage());
    }
}

// Fetch Hero Slides
$sliderMgr = new SliderManager();
$slides = [];
try {
    $slides = $sliderMgr->getSlides() ?: [];
} catch (Exception $e) {
    error_log("Failed to fetch hero slides: " . $e->getMessage());
}

// Include Header & Navbar
require_once PATH_ROOT . '/includes/header.php';
require_once PATH_ROOT . '/includes/navbar.php';
?>

<!-- 1. Immersive Hero Section (Dynamic Slider) -->
<?php if (!empty($slides)): ?>
    <section class="hero-slider-section" aria-label="Featured content slider">
        <div class="hero-slider-container">
            <?php foreach ($slides as $index => $slide): 
                $slideImg = !empty($slide['image']) ? $slide['image'] : '/images/hero-bg.png';
                $isActive = ($index === 0) ? 'active' : '';
                ?>
                <div class="hero-slide <?php echo $isActive; ?>" data-index="<?php echo $index; ?>" role="group" aria-roledescription="slide" aria-label="<?php echo $index + 1; ?> of <?php echo count($slides); ?>">
                    <!-- Ken Burns zoom image -->
                    <img 
                        <?php if ($index === 0): ?>
                            src="<?php echo e(BASE_URL . $slideImg); ?>" 
                        <?php else: ?>
                            src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 9'%3E%3C/svg%3E" 
                            data-src="<?php echo e(BASE_URL . $slideImg); ?>" 
                        <?php endif; ?>
                        alt="<?php echo e($slide['title']); ?>" 
                        class="hero-slide-img" 
                        loading="<?php echo ($index === 0) ? 'eager' : 'lazy'; ?>" 
                    />
                    
                    <div class="hero-slide-overlay" style="background: rgba(0,0,0,<?php echo floatval($slide['overlay_opacity']); ?>);"></div>
                    
                    <div class="hero-slide-content container">
                        <?php if (!empty($slide['subtitle'])): ?>
                            <p class="hero-slide-subtitle"><?php echo e($slide['subtitle']); ?></p>
                        <?php endif; ?>
                        <h1 class="hero-slide-title"><?php echo e($slide['title']); ?></h1>
                        <?php if (!empty($slide['button_text']) && !empty($slide['button_link'])): ?>
                            <a href="<?php echo e(BASE_URL . $slide['button_link']); ?>" class="btn-primary hero-slide-btn" style="border-radius: 8px; margin-top: 1rem;">
                                <?php echo e($slide['button_text']); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Controls (arrows) -->
        <?php if (count($slides) > 1): ?>
            <button class="slider-arrow prev" aria-label="Previous slide">
                <svg viewBox="0 0 24 24"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
            </button>
            <button class="slider-arrow next" aria-label="Next slide">
                <svg viewBox="0 0 24 24"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
            </button>
            
            <!-- Indicators (dots) -->
            <div class="slider-dots">
                <?php foreach ($slides as $index => $slide): ?>
                    <button class="slider-dot <?php echo ($index === 0) ? 'active' : ''; ?>" data-slide-to="<?php echo $index; ?>" aria-label="Go to slide <?php echo $index + 1; ?>"></button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
<?php else: ?>
    <!-- Fallback if no slides exist in DB -->
    <section class="hero-section">
        <img src="<?php echo e($settings['heroBackgroundUrl'] ?? '/images/hero-bg.png'); ?>" alt="<?php echo e($siteTitle); ?> Cover" class="hero-img" />
        <div class="hero-gradient"></div>
        <div class="hero-content">
            <h1 class="hero-title animate-in fade-in" data-scroll-reveal>
                Travel <br>
                <span style="opacity: 0.95;">Without</span> <br>
                Limits
            </h1>
            <div class="hero-divider"></div>
            <p class="hero-sub">Accessible Travel</p>
        </div>
    </section>
<?php endif; ?>

<!-- 2. Brand Introduction -->
<section class="intro-section container" data-scroll-reveal>
    <div class="intro-grid">
        <!-- Content Side -->
        <div class="intro-content">
            <span class="section-label">The Story</span>
            <h2 class="serif-title">Travel Without <br><span class="italic">Limits</span></h2>
            <div class="intro-divider"></div>
            <div class="intro-text">
                <p>
                    We believe that everyone deserves to experience the wonder of travel. Our mission is to break down barriers and provide the ultimate resource for accessible exploration.
                </p>
                <p>
                    From hidden local gems to iconic global destinations, we share the stories, tips, and insights needed to navigate the world with confidence and joy. No matter your needs, the world is waiting for you.
                </p>
            </div>
            <a href="<?php echo BASE_URL; ?>/accessibility" class="btn-underline">
                Discover Our Mission
            </a>
        </div>
        
        <!-- Polaroid Collage Side -->
        <div class="polaroid-collage">
            <!-- Polaroid 1 -->
            <div class="polaroid-card polaroid-1">
                <div class="polaroid-media">
                    <img src="<?php echo BASE_URL; ?>/images/europe.png" alt="Venice, Italy" loading="lazy" />
                </div>
                <p class="polaroid-caption">Venice, Italy</p>
            </div>
            
            <!-- Polaroid 2 -->
            <div class="polaroid-card polaroid-2">
                <div class="polaroid-media">
                    <img src="<?php echo BASE_URL; ?>/images/australia.png" alt="Queensland Shore" loading="lazy" />
                </div>
                <p class="polaroid-caption">Queensland Shore</p>
            </div>
            
            <div class="collage-blob"></div>
        </div>
    </div>
</section>

<!-- 3. Specific Needs Quick Access Grid -->
<section class="needs-section container" data-scroll-reveal>
    <div class="section-header">
        <span class="section-label">Quick Access</span>
        <h3 class="serif-title">Explore by <span class="italic">Special Needs</span></h3>
    </div>
    
    <div class="needs-grid">
        <?php
        $needsCategories = [
            ['title' => 'Assistance Animals', 'slug' => 'assistance-animals', 'image' => '/images/animals.png'],
            ['title' => 'Blind / Low Vision', 'slug' => 'blind-low-vision', 'image' => '/images/vision.png'],
            ['title' => 'Mental Health', 'slug' => 'hidden-disabilities', 'image' => '/images/mental.png'],
            ['title' => 'Neurodiversity', 'slug' => 'neurodiversity', 'image' => '/images/neurodiversity.png'],
            ['title' => 'Physical / Mobility', 'slug' => 'physical-mobility', 'image' => '/images/mobility.png'],
            ['title' => 'Sensory Needs', 'slug' => 'sensory-needs', 'image' => '/images/sensory.png']
        ];
        foreach ($needsCategories as $cat):
            ?>
            <a href="<?php echo BASE_URL; ?>/category/<?php echo $cat['slug']; ?>" class="needs-card" aria-label="Explore stories for <?php echo e($cat['title']); ?>">
                <img src="<?php echo BASE_URL . $cat['image']; ?>" alt="<?php echo e($cat['title']); ?>" loading="lazy" />
                <div class="needs-card-overlay">
                    <div class="needs-card-line"></div>
                    <h4 class="needs-card-title"><?php echo e($cat['title']); ?></h4>
                    <div class="needs-card-hover-text">
                        <span class="hover-label-btn">View Stories</span>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- 4. Plan Your Trip (Filterable Story Grid) -->
<section class="filterable-section container" data-scroll-reveal>
    <div class="filterable-header-row">
        <div class="intro-content" style="gap: 1rem;">
            <span class="section-label">Resources & Guides</span>
            <h3 class="serif-title" style="margin:0;">Plan Your Trip</h3>
        </div>
        
        <!-- Tabs -->
        <div class="tabs-container" role="tablist" aria-label="Planning tabs">
            <?php 
            $isFirst = true;
            foreach ($planCats as $cat): 
                ?>
                <button 
                    class="tab-btn <?php echo $isFirst ? 'active' : ''; ?>" 
                    data-grid="plan-grid" 
                    data-category="<?php echo e($cat['id']); ?>"
                    role="tab"
                    aria-selected="<?php echo $isFirst ? 'true' : 'false'; ?>"
                    aria-label="Filter by <?php echo e($cat['name']); ?>"
                >
                    <?php echo e($cat['name']); ?>
                </button>
                <?php 
                $isFirst = false;
            endforeach; 
            ?>
        </div>
    </div>

    <!-- Posts Grid -->
    <div id="plan-grid" class="story-grid">
        <?php if (!empty($initialPlanPosts)): ?>
            <?php foreach ($initialPlanPosts as $post): ?>
                <?php renderPostCard($post); ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align:center; color:#999; padding: 4rem 0; font-style:italic;">
                No stories found in this category yet.
            </div>
        <?php endif; ?>
    </div>
    
    <div class="section-footer">
        <a href="<?php echo BASE_URL; ?>/destinations" class="btn-underline">
            Explore All Guides
        </a>
    </div>
</section>

<!-- 5. Destinations (Region Slider) -->
<section class="slider-section">
    <div class="container slider-header">
        <div class="intro-content" style="gap: 1rem;">
            <span class="section-label">Where to next?</span>
            <h3 class="serif-title" style="margin:0;">Browse by <span class="italic">Destinations</span></h3>
        </div>
        
        <div class="slider-controls">
            <button class="slider-btn slider-btn-left" aria-label="Scroll left">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <button class="slider-btn slider-btn-right" aria-label="Scroll right">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>
    </div>
    
    <div class="slider-outer">
        <div class="slider-inner">
            <?php
            $regionSliders = [
                ['name' => 'Australia', 'slug' => 'australia', 'image' => '/images/australia.png'],
                ['name' => 'Asia Pacific', 'slug' => 'asia-pacific', 'image' => '/images/asia.png'],
                ['name' => 'Europe', 'slug' => 'europe', 'image' => '/images/europe.png'],
                ['name' => 'Americas', 'slug' => 'americas', 'image' => '/images/americas.png']
            ];
            foreach ($regionSliders as $region):
                ?>
                <a href="<?php echo BASE_URL; ?>/category/<?php echo $region['slug']; ?>" class="slider-card" aria-label="Explore <?php echo e($region['name']); ?> stories">
                    <img src="<?php echo BASE_URL . $region['image']; ?>" alt="" loading="lazy" />
                    <div class="slider-card-overlay">
                        <span class="slider-card-label">Region</span>
                        <h4 class="slider-card-title"><?php echo e($region['name']); ?></h4>
                        <div class="slider-card-line"></div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 6. Experiences (Filterable Story Grid) -->
<section class="filterable-section container" data-scroll-reveal>
    <div class="filterable-header-row">
        <div class="intro-content" style="gap: 1rem;">
            <span class="section-label">Moments that Matter</span>
            <h3 class="serif-title" style="margin:0;">Experiences</h3>
        </div>
        
        <!-- Tabs -->
        <div class="tabs-container" role="tablist" aria-label="Experiences tabs">
            <?php 
            $isFirst = true;
            foreach ($expCats as $cat): 
                ?>
                <button 
                    class="tab-btn <?php echo $isFirst ? 'active' : ''; ?>" 
                    data-grid="exp-grid" 
                    data-category="<?php echo e($cat['id']); ?>"
                    role="tab"
                    aria-selected="<?php echo $isFirst ? 'true' : 'false'; ?>"
                    aria-label="Filter by <?php echo e($cat['name']); ?>"
                >
                    <?php echo e($cat['name']); ?>
                </button>
                <?php 
                $isFirst = false;
            endforeach; 
            ?>
        </div>
    </div>

    <!-- Posts Grid -->
    <div id="exp-grid" class="story-grid">
        <?php if (!empty($initialExpPosts)): ?>
            <?php foreach ($initialExpPosts as $post): ?>
                <?php renderPostCard($post); ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align:center; color:#999; padding: 4rem 0; font-style:italic;">
                No stories found in this category yet.
            </div>
        <?php endif; ?>
    </div>
    
    <div class="section-footer">
        <a href="<?php echo BASE_URL; ?>/experiences" class="btn-underline">
            Explore All Experiences
        </a>
    </div>
</section>

<?php 
// Include Footer
require_once PATH_ROOT . '/includes/footer.php'; 
?>
