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
require_once PATH_ROOT . '/classes/PodcastManager.php';
require_once PATH_ROOT . '/classes/StoryManager.php';

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
        return isset($c['slug_en']) && in_array($c['slug_en'], ['accommodation', 'inspiration', 'tips-tricks', 'transport']);
    });
    if (empty($planCats)) {
        $planCats = array_slice($categories, 0, 4);
    }
}

// Filter categories for "Experiences" (Animal Encounters, Cruises, Family Travel, Food & Drink)
$expCats = [];
if (!empty($categories)) {
    $expCats = array_filter($categories, function($c) {
        return isset($c['slug_en']) && in_array($c['slug_en'], ['animal-encounters', 'cruises', 'family-travel', 'food-drink']);
    });
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

// Fetch Specific Needs Categories Dynamically
$needsCategoriesList = [];
$needsSlugs = ['physical-mobility', 'cruises', 'accommodation', 'gear', 'sensory-needs', 'tips-tricks'];
if ($pm instanceof PostManager) {
    foreach ($needsSlugs as $slug) {
        $catData = $pm->getCategoryBySlug($slug);
        if ($catData) {
            $needsCategoriesList[] = $catData;
        }
    }
}

// Fetch Destinations Regions dynamically
$destSlidersList = [];
$destSlugs = ['australia', 'asia-pacific', 'europe', 'americas'];
if ($pm instanceof PostManager) {
    foreach ($destSlugs as $slug) {
        $catData = $pm->getCategoryBySlug($slug);
        if ($catData) {
            $destSlidersList[] = $catData;
        }
    }
}

// Fetch Podcasts for homepage
$podcastMgr = new PodcastManager();
$homePodcasts = [];
try {
    $podResult = $podcastMgr->getPodcasts(['limit' => 3]);
    $homePodcasts = $podResult['data'] ?? [];
} catch (Exception $e) {
    error_log("Failed to fetch homepage podcasts: " . $e->getMessage());
}

// Fetch Women Stories for homepage
$storyMgr = new StoryManager();
$homeStories = [];
try {
    $storiesResult = $storyMgr->getStories(['limit' => 3]);
    $homeStories = $storiesResult['data'] ?? [];
} catch (Exception $e) {
    error_log("Failed to fetch homepage stories: " . $e->getMessage());
}

// Fetch Testimonials
$testimonials = [];
if ($pm instanceof PostManager) {
    try {
        $testimonials = $pm->getTestimonials() ?: [];
    } catch (Exception $e) {
        error_log("Failed to fetch testimonials: " . $e->getMessage());
    }
}

// Fetch Featured News Posts (Fallback to latest posts if count < 2)
$featuredNews = [];
if ($pm instanceof PostManager) {
    try {
        $newsResult = $pm->getPosts(['isFeatured' => true, 'status' => 'PUBLISHED', 'limit' => 6]);
        $featuredNews = $newsResult['data'] ?? [];
        if (count($featuredNews) < 2) {
            $newsResult = $pm->getPosts(['status' => 'PUBLISHED', 'limit' => 6]);
            $featuredNews = $newsResult['data'] ?? [];
        }
    } catch (Exception $e) {
        error_log("Failed to fetch featured news posts: " . $e->getMessage());
    }
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
                $slideImg = t($slide, 'image');
                if (empty($slideImg)) {
                    $slideImg = !empty($slide['image_en']) ? $slide['image_en'] : '/images/hero-bg.png';
                }
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
                        alt="<?php echo e(t($slide, 'alt_text') ?: t($slide, 'title')); ?>" 
                        class="hero-slide-img" 
                        loading="<?php echo ($index === 0) ? 'eager' : 'lazy'; ?>" 
                    />
                    
                    <div class="hero-slide-overlay" style="background: rgba(0,0,0,<?php echo floatval($slide['overlay_opacity']); ?>);"></div>
                    
                    <div class="hero-slide-content container">
                        <?php if (t($slide, 'subtitle') !== ''): ?>
                            <p class="hero-slide-subtitle"><?php echo e(t($slide, 'subtitle')); ?></p>
                        <?php endif; ?>
                        <h1 class="hero-slide-title"><?php echo e(t($slide, 'title')); ?></h1>
                        <?php if (t($slide, 'button_text') !== '' && !empty($slide['button_link'])): ?>
                            <a href="<?php echo url($slide['button_link']); ?>" class="btn-primary hero-slide-btn" style="border-radius: 8px; margin-top: 1rem;">
                                <?php echo e(t($slide, 'button_text')); ?>
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
        <img src="<?php echo e(t($settings, 'heroBackgroundUrl') ?: ($settings['logoUrl'] ?? '/images/hero-bg.png')); ?>" alt="<?php echo e($siteTitle); ?> Cover" class="hero-img" />
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

<!-- 2. Featured News Slider -->
<?php if (!empty($featuredNews)): ?>
<section class="featured-news-section container" data-scroll-reveal>
    <div class="news-slider-header-row">
        <div class="news-slider-title-wrap">
            <span class="section-label"><?php echo __('featured_news_label'); ?></span>
            <h2 class="serif-title"><?php echo __('featured_news_title'); ?> <span class="italic"><?php echo __('featured_news_title_italic'); ?></span></h2>
        </div>
        <!-- Arrow Navigation -->
        <?php if (count($featuredNews) > 1): ?>
        <div class="news-slider-arrows">
            <button class="news-arrow prev-news" aria-label="<?php echo CURRENT_LANG === 'ar' ? 'السابق' : 'Previous'; ?>">
                <svg viewBox="0 0 24 24" width="24" height="24"><path fill="currentColor" d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
            </button>
            <button class="news-arrow next-news" aria-label="<?php echo CURRENT_LANG === 'ar' ? 'التالي' : 'Next'; ?>">
                <svg viewBox="0 0 24 24" width="24" height="24"><path fill="currentColor" d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
            </button>
        </div>
        <?php endif; ?>
    </div>

    <!-- News Slider Outer -->
    <div class="news-slider-outer">
        <div class="news-slider-wrapper">
            <?php foreach ($featuredNews as $index => $post): 
                $postUrl = url('posts/' . e(t($post, 'slug')));
                $postCover = !empty($post['coverImage']) ? $post['coverImage'] : '/images/hero-bg.png';
                $postTitle = t($post, 'title');
                $postExcerpt = t($post, 'excerpt');
                $isActive = ($index === 0) ? 'active' : '';
                ?>
                <div class="news-slide <?php echo $isActive; ?>" data-index="<?php echo $index; ?>">
                    <div class="news-slide-grid">
                        <!-- Image Column -->
                        <div class="news-slide-media">
                            <a href="<?php echo $postUrl; ?>" class="news-media-link" aria-label="<?php echo e($postTitle); ?>">
                                <img src="<?php echo e(BASE_URL . $postCover); ?>" alt="<?php echo e(t($post, 'alt_text') ?: $postTitle); ?>" class="news-slide-img" loading="lazy" />
                                <div class="news-media-overlay"></div>
                            </a>
                            <div class="news-slide-badge">
                                <span class="badge-text" style="background: var(--primary-color); color: #fff;"><?php echo e(t($post, 'categoryName')); ?></span>
                            </div>
                        </div>

                        <!-- Content Column -->
                        <div class="news-slide-content">
                            <div class="news-meta">
                                <span>By <?php echo e(e($post['authorName'] ?? 'Editor')); ?></span>
                                <span class="meta-dot"></span>
                                <span><?php echo formatDate($post['publishedAt'] ?: $post['created_at']); ?></span>
                            </div>
                            <h3 class="news-title">
                                <a href="<?php echo $postUrl; ?>"><?php echo e($postTitle); ?></a>
                            </h3>
                            <p class="news-excerpt"><?php echo e($postExcerpt); ?></p>
                            <a href="<?php echo $postUrl; ?>" class="btn-underline">
                                <?php echo __('btn_read_full_story'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Dots Navigation -->
    <?php if (count($featuredNews) > 1): ?>
    <div class="news-slider-dots">
        <?php foreach ($featuredNews as $index => $post): ?>
            <button class="news-dot <?php echo ($index === 0) ? 'active' : ''; ?>" data-slide-to="<?php echo $index; ?>" aria-label="Go to slide <?php echo $index + 1; ?>"></button>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>
<?php endif; ?>

<!-- 3. Specific Needs Quick Access Grid -->
<?php if (!empty($needsCategoriesList)): ?>
<section class="needs-section container" data-scroll-reveal>
    <div class="section-header">
        <span class="section-label">Quick Access</span>
        <h3 class="serif-title">Tailored for <span class="italic">Seniors</span></h3>
    </div>
    
    <div class="needs-grid">
        <?php foreach ($needsCategoriesList as $cat): ?>
            <a href="<?php echo url('category/' . e(t($cat, 'slug'))); ?>" class="needs-card" aria-label="Explore stories for <?php echo e(t($cat, 'name')); ?>">
                <img src="<?php echo e(!empty($cat['image']) ? $cat['image'] : '/images/hero-bg.png'); ?>" alt="<?php echo e(t($cat, 'alt_text') ?: t($cat, 'name')); ?>" loading="lazy" />
                <div class="needs-card-overlay">
                    <div class="needs-card-line"></div>
                    <h4 class="needs-card-title"><?php echo e(t($cat, 'name')); ?></h4>
                    <div class="needs-card-hover-text">
                        <span class="hover-label-btn"><?php echo __('btn_view_stories'); ?></span>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- 4. Plan Your Trip / Editor's Picks (Filterable Story Grid) -->
<?php if (!empty($planCats)): ?>
<section class="filterable-section container" data-scroll-reveal>
    <div class="filterable-header-row">
        <div class="intro-content" style="gap: 1rem;">
            <span class="section-label">Editor's Picks</span>
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
                    aria-label="Filter by <?php echo e(t($cat, 'name')); ?>"
                >
                    <?php echo e(t($cat, 'name')); ?>
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
                <?php renderPostCard($post, 'featured'); ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align:center; color:#999; padding: 4rem 0; font-style:italic;">
                No stories found in this category yet.
            </div>
        <?php endif; ?>
    </div>
    
    <div class="section-footer">
        <a href="<?php echo url('destinations'); ?>" class="btn-underline">
            Explore All Guides
        </a>
    </div>
</section>
<?php endif; ?>

<!-- 5. Destinations (Region Slider) -->
<?php if (!empty($destSlidersList)): ?>
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
            <?php foreach ($destSlidersList as $region): ?>
                <a href="<?php echo url('category/' . e(t($region, 'slug'))); ?>" class="slider-card" aria-label="Explore <?php echo e(t($region, 'name')); ?> stories">
                    <img src="<?php echo e(!empty($region['image']) ? $region['image'] : '/images/hero-bg.png'); ?>" alt="<?php echo e(t($region, 'alt_text') ?: t($region, 'name')); ?>" loading="lazy" />
                    <div class="slider-card-overlay">
                        <span class="slider-card-label">Region</span>
                        <h4 class="slider-card-title"><?php echo e(t($region, 'name')); ?></h4>
                        <div class="slider-card-line"></div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 5.2 Women Stories Section (Storytelling Editorial Asymmetric Columns) -->
<?php if (!empty($homeStories)): ?>
<section class="filterable-section container" data-scroll-reveal>
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 3.5rem;">
        <div class="intro-content" style="gap: 1rem;">
            <span class="section-label"><?php echo __('nav_stories'); ?></span>
            <h3 class="serif-title" style="margin:0;">Women <span class="italic">Chronicles</span></h3>
        </div>
        <a href="<?php echo url('women-stories'); ?>" class="btn-underline">
            Read All Chronicles
        </a>
    </div>
    
    <div class="story-grid-asymmetric">
        <!-- Main Large Story -->
        <?php 
        $mainStory = $homeStories[0];
        $otherStories = array_slice($homeStories, 1);
        $mainCover = !empty($mainStory['cover_image']) ? $mainStory['cover_image'] : '/images/hero-bg.png';
        ?>
        <article class="post-card group" style="border: none;">
            <a href="<?php echo BASE_URL; ?>/women-stories/<?php echo e(t($mainStory, 'slug')); ?>" class="post-card-link" aria-label="Read <?php echo e(t($mainStory, 'title')); ?>">
                <div class="post-card-media ratio-story">
                    <div class="progressive-image-placeholder"></div>
                    <img src="<?php echo e(BASE_URL . $mainCover); ?>" alt="<?php echo e(t($mainStory, 'title')); ?>" loading="lazy" class="post-card-img" onload="this.classList.add('loaded');" />
                    <div class="post-card-badge">
                        <span class="badge-text" style="background: var(--primary-color); color: #fff;"><?php echo e(t($mainStory, 'category')); ?></span>
                    </div>
                </div>
                <div class="post-card-body" style="padding: 2rem 0;">
                    <div class="post-card-meta">
                        <span>By <?php echo e(t($mainStory, 'author')); ?></span>
                        <span class="meta-dot"></span>
                        <span><?php echo e($mainStory['read_time']); ?></span>
                    </div>
                    <h3 class="serif-title" style="font-size: 2rem; line-height: 1.2; margin-bottom: 1rem; color: var(--text-color);"><?php echo e(t($mainStory, 'title')); ?></h3>
                    <p class="post-card-excerpt" style="font-size: 1rem; line-height: 1.6; color: var(--text-color); opacity: 0.8;"><?php echo e(t($mainStory, 'excerpt')); ?></p>
                </div>
            </a>
        </article>
        
        <!-- Small Stories List (Side Column) -->
        <div style="display: flex; flex-direction: column; gap: 2.5rem;">
            <?php foreach ($otherStories as $story): 
                $storyCover = !empty($story['cover_image']) ? $story['cover_image'] : '/images/hero-bg.png';
                ?>
                <article style="display: flex; gap: 1.5rem; align-items: flex-start; border-bottom: 1px solid var(--border-color); padding-bottom: 2rem;">
                    <a href="<?php echo BASE_URL; ?>/women-stories/<?php echo e(t($story, 'slug')); ?>" style="display: flex; gap: 1.5rem; width: 100%;">
                        <div style="width: 120px; height: 120px; flex-shrink: 0; border-radius: 8px; overflow: hidden; position: relative;">
                            <img src="<?php echo e(BASE_URL . $storyCover); ?>" alt="" style="width:100%; height:100%; object-fit:cover;" />
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 0.5rem; flex-grow: 1;">
                            <div class="post-card-meta" style="margin: 0;">
                                <span><?php echo e(t($story, 'author')); ?></span>
                                <span class="meta-dot"></span>
                                <span><?php echo e($story['read_time']); ?></span>
                            </div>
                            <h4 class="serif-title" style="font-size: 1.15rem; margin: 0; line-height: 1.3; color: var(--text-color);"><?php echo e(t($story, 'title')); ?></h4>
                            <p style="font-size: 0.85rem; color: var(--text-color); opacity: 0.7; margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <?php echo e(t($story, 'excerpt')); ?>
                            </p>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 5.3 Podcasts Section (Dark Spotify-Inspired layout) -->
<?php if (!empty($homePodcasts)): ?>
<section class="slider-section dark-podcast-theme" style="padding: 6rem 0;" data-scroll-reveal>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 3.5rem; flex-wrap: wrap; gap: 1.5rem;">
            <div class="intro-content" style="gap: 1rem;">
                <span class="section-label" style="color: var(--accent-color);"><?php echo __('nav_podcasts'); ?></span>
                <h3 class="serif-title" style="margin:0; color: #ffffff;">Audio <span class="italic">Journeys</span></h3>
            </div>
            <a href="<?php echo url('podcasts'); ?>" class="btn-underline" style="color: var(--accent-color); border-bottom-color: var(--accent-color);">
                Listen to All Episodes
            </a>
        </div>
        
        <div class="story-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <?php foreach ($homePodcasts as $pod): 
                $cover = !empty($pod['cover_image']) ? $pod['cover_image'] : '/images/hero-bg.png';
                ?>
                <article class="post-card group" style="background: #181818; border-color: #282828;">
                    <div class="post-card-media" style="aspect-ratio: 16/10; position: relative;">
                        <div class="progressive-image-placeholder"></div>
                        <img src="<?php echo e(BASE_URL . $cover); ?>" alt="<?php echo e(t($pod, 'title')); ?>" loading="lazy" class="post-card-img" onload="this.classList.add('loaded');" />
                        <div class="post-card-badge">
                            <span class="badge-text" style="background: var(--primary-color); color: #fff;"><?php echo e(t($pod, 'category')); ?></span>
                        </div>
                        <button class="podcast-card-play-btn" 
                                data-audio="<?php echo e(BASE_URL . $pod['audio_file']); ?>" 
                                data-title="<?php echo e(e(t($pod, 'title'))); ?>" 
                                data-cover="<?php echo e(BASE_URL . $cover); ?>"
                                data-id="<?php echo e($pod['id']); ?>"
                                aria-label="Play <?php echo e(t($pod, 'title')); ?>">
                            <svg style="width: 1.5rem; height: 1.5rem; fill: #ffffff;" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </button>
                    </div>
                    <div class="post-card-body" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 0.75rem;">
                        <div class="post-card-meta">
                            <span style="color: #b3b3b3;"><?php echo formatDate($pod['created_at']); ?></span>
                            <span class="meta-dot"></span>
                            <span style="color: #b3b3b3;"><?php echo e($pod['duration']); ?></span>
                        </div>
                        <h4 class="post-card-title" style="color: #ffffff; font-size: 1.2rem;"><?php echo e(t($pod, 'title')); ?></h4>
                        <p class="post-card-excerpt" style="color: #b3b3b3; font-size: 0.85rem; line-height: 1.5; opacity: 0.8;">
                            <?php echo e(t($pod, 'description')); ?>
                        </p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 5.5 Testimonials Section (Luxury Glassmorphism layout) -->
<?php if (!empty($testimonials)): ?>
<section class="testimonials-section container" data-scroll-reveal style="margin-top: 6rem; margin-bottom: 6rem;">
    <div class="section-header text-center" style="max-width: 600px; margin: 0 auto 4rem auto;">
        <span class="section-label" style="text-align: center; display: block;"><?php echo __('test_label'); ?></span>
        <h3 class="serif-title" style="margin-top: 1rem; text-align: center;"><?php echo __('test_title'); ?></h3>
    </div>
    
    <div class="testimonials-slider-outer">
        <div class="testimonials-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <?php foreach ($testimonials as $t): ?>
                <div class="testimonial-card" style="padding: 2.5rem; display: flex; flex-direction: column; justify-content: space-between; border-radius: 16px;">
                    <div>
                        <div class="testimonial-quote-icon" style="font-family: serif; font-size: 4rem; line-height: 1; color: var(--primary-color); opacity: 0.3; margin-bottom: -1rem; margin-top: -1rem; text-align: start;">“</div>
                        <p class="testimonial-quote" style="font-size: 1.1rem; line-height: 1.6; font-style: italic; margin-bottom: 2rem; color: var(--text-color); font-weight: 300;">
                            <?php echo e(t($t, 'quote')); ?>
                        </p>
                    </div>
                    <div class="testimonial-author-meta" style="display: flex; align-items: center; gap: 1rem; padding-top: 1.5rem;">
                        <?php if (!empty($t['image'])): ?>
                            <img src="<?php echo e(BASE_URL . $t['image']); ?>" alt="<?php echo e(t($t, 'author')); ?>" class="testimonial-avatar" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;" />
                        <?php endif; ?>
                        <div>
                            <h4 class="testimonial-name" style="font-family: var(--base-font); font-size: 1rem; font-weight: 600; margin: 0; color: var(--text-color);"><?php echo e(t($t, 'author')); ?></h4>
                            <p class="testimonial-role" style="font-size: 0.8rem; color: var(--text-color); opacity: 0.6; margin: 0; font-weight: 300;"><?php echo e(t($t, 'role')); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 6. Experiences (Filterable Story Grid) -->
<?php if (!empty($expCats)): ?>
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
                    aria-label="Filter by <?php echo e(t($cat, 'name')); ?>"
                >
                    <?php echo e(t($cat, 'name')); ?>
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
                <?php renderPostCard($post, 'story'); ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align:center; color:#999; padding: 4rem 0; font-style:italic;">
                No stories found in this category yet.
            </div>
        <?php endif; ?>
    </div>
    
    <div class="section-footer">
        <a href="<?php echo url('experiences'); ?>" class="btn-underline">
            Explore All Experiences
        </a>
    </div>
</section>
<?php endif; ?>

<?php 
// Include Footer
require_once PATH_ROOT . '/includes/footer.php'; 
?>
