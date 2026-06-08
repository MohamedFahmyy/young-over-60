<?php
// pages/women-stories.php
// Premium Magazine-Style Women Stories Landing & Single Reader View

$seoPageType = 'women-stories';
$storyMgr = new StoryManager();

// Read route parameters from index.php front controller
$slug = $parts[1] ?? '';

if (!empty($slug)) {
    // ==========================================
    // SINGLE STORY READER VIEW
    // ==========================================
    $story = $storyMgr->getStoryBySlug($slug);
    
    if (!$story) {
        header("HTTP/1.0 404 Not Found");
        require_once PATH_ROOT . '/pages/404.php';
        exit();
    }
    
    $relatedStories = $storyMgr->getRelatedStories($story['id'], 3);
    $cover = !empty($story['cover_image']) ? $story['cover_image'] : '/images/hero-bg.png';
    $storyUrl   = urlencode(BASE_URL . '/women-stories/' . ($story['slug_en'] ?? ''));
    $settings   = $pm->getSiteSettings();
    $storyTitle   = urlencode(t($story, 'title') . " - " . ($settings['siteName'] ?? 'Young Over 60'));
    $storyExcerpt = urlencode(t($story, 'excerpt') ?? '');
    $storyCoverUrl = urlencode(BASE_URL . $cover);

    // Custom SEO Meta for this Story
    $seoPageType = 'post';
    $seoPageData = $story;
    $heroImageToPreload = !empty($cover) ? $cover : '/images/hero-bg.png';
    if (!empty($heroImageToPreload) && !str_starts_with($heroImageToPreload, 'http') && !str_starts_with($heroImageToPreload, '//')) {
        $heroImageToPreload = BASE_URL . $heroImageToPreload;
    }

    require_once PATH_ROOT . '/includes/header.php';
    require_once PATH_ROOT . '/includes/navbar.php';
    ?>

    <article class="single-story-article">
        <!-- Parallax Hero Cover Header -->
        <div class="story-parallax-hero" style="background-image: url('<?php echo e(BASE_URL . $cover); ?>');">
            <div class="story-hero-overlay"></div>
            <div class="story-hero-content container">
                <span class="badge-text" style="background: var(--primary-color); color: #fff; align-self: flex-start; margin-bottom: 1rem; text-transform: uppercase;">
                    <?php echo e($story['category'] ?? 'Egypt Travel'); ?>
                </span>
                <h1 class="serif-title story-main-title"><?php echo e(t($story, 'title')); ?></h1>
                <div class="story-hero-meta">
                    <span>Written by <strong><?php echo e($story['author'] ?? 'Guest Writer'); ?></strong></span>
                    <span class="meta-dot"></span>
                    <span><?php echo e($story['read_time'] ?? '5 min read'); ?></span>
                    <span class="meta-dot"></span>
                    <time datetime="<?php echo e($story['created_at']); ?>"><?php echo formatDate($story['created_at']); ?></time>
                </div>
            </div>
            <!-- Scroll indicator -->
            <div class="scroll-down-hint">
                <span style="font-size: 0.6rem; letter-spacing: 0.2em; text-transform: uppercase; opacity: 0.7;">Scroll to read</span>
                <div class="indicator-line"></div>
            </div>
        </div>

        <div class="container story-reader-container" style="display: grid; grid-template-columns: 1fr; gap: 3rem; padding-top: 4rem; padding-bottom: 5rem;">
            <div class="story-reader-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 4rem; align-items: start;">
                <!-- Main Content Body -->
                <div class="story-content-body-wrapper">
                    <!-- Drop cap styles will be applied to the first paragraph -->
                    <div class="story-full-content serif-body-text">
                        <?php echo $story['content']; // HTML rich text from database ?>
                    </div>

                    <!-- Social Share Widget -->
                    <div class="story-share-bar" style="margin-top: 4rem; padding-top: 2rem; border-top: 1px solid var(--accent-color);">
                        <h4 style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.15em; color: #888; margin-bottom: 1rem;">Share This Story</h4>
                        <div class="share-buttons-list" style="display: flex; gap: 1rem; flex-wrap: wrap;">
                            <!-- Facebook -->
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $storyUrl; ?>" target="_blank" rel="noopener noreferrer" class="btn-share facebook" aria-label="Share on Facebook">
                                <svg style="width:1rem; height:1rem; fill:currentColor;" viewBox="0 0 24 24"><path d="M9 8H7v3h2v9h4v-9h3.6l.4-3H13V6c0-.5.5-1 1-1h2V1h-3a5 5 0 00-5 5v2z"/></svg> Facebook
                            </a>
                            <!-- Twitter / X -->
                            <a href="https://twitter.com/intent/tweet?url=<?php echo $storyUrl; ?>&text=<?php echo $storyTitle; ?>" target="_blank" rel="noopener noreferrer" class="btn-share twitter" aria-label="Share on Twitter">
                                <svg style="width:1rem; height:1rem; fill:currentColor;" viewBox="0 0 24 24"><path d="M18.2 2.4h3.3L14.3 11l8.5 11.3h-6.7L11 15.6l-6 6.8H1.7l7.6-8.7L1.2 2.4h6.9l4.7 6.2 5.4-6.2zm-1.2 17.5h1.8L7.1 4.7H5.2l11.8 15.2z"/></svg> X / Twitter
                            </a>
                            <!-- Pinterest -->
                            <a href="https://pinterest.com/pin/create/button/?url=<?php echo $storyUrl; ?>&media=<?php echo $storyCoverUrl; ?>&description=<?php echo $storyExcerpt; ?>" target="_blank" rel="noopener noreferrer" class="btn-share pinterest" aria-label="Pin this story">
                                <svg style="width:1rem; height:1rem; fill:currentColor;" viewBox="0 0 24 24"><path d="M12 2a10 10 0 00-3.6 19.3c-.1-.8-.2-2 .1-2.9.2-.9 1.6-6.7 1.6-6.7s-.4-.8-.4-2c0-1.9 1.1-3.3 2.5-3.3 1.2 0 1.7.9 1.7 1.9 0 1.2-.7 2.9-1.1 4.5-.3 1.4.7 2.5 2.1 2.5 2.5 0 4.4-2.6 4.4-6.4 0-3.3-2.4-5.6-5.8-5.6-3.9 0-6.2 3-6.2 6 0 1.2.5 2.5 1 3 .1.1.1.3 0 .4l-.4 1.6c-.1.2-.2.3-.4.2-1.3-.6-2.1-2.5-2.1-4 0-4.6 3.3-8.8 9.6-8.8 5 0 9 3.6 9 8.4 0 5-3.2 9.1-7.6 9.1-1.5 0-2.9-.8-3.4-1.7l-1 3.7c-.4 1.4-1.3 3.1-2 4.1A10 10 0 1012 2z"/></svg> Pinterest
                            </a>
                            <!-- WhatsApp -->
                            <a href="https://api.whatsapp.com/send?text=<?php echo $storyTitle . '%20' . $storyUrl; ?>" target="_blank" rel="noopener noreferrer" class="btn-share whatsapp" aria-label="Share on WhatsApp">
                                <svg style="width:1rem; height:1rem; fill:currentColor;" viewBox="0 0 24 24"><path d="M12.012 2C6.48 2 2 6.48 2 12.012a9.9 9.9 0 001.374 5.03L2 22l5.124-1.344a9.92 9.92 0 004.888 1.356c5.532 0 10.012-4.48 10.012-10.012C22.024 6.48 17.544 2 12.012 2zm0 16.518a6.5 6.5 0 01-3.306-.9l-.234-.144-3.048.8.816-2.97-.156-.252a6.49 6.49 0 01-1-3.438c0-3.588 2.922-6.51 6.516-6.51h.006c3.588 0 6.51 2.922 6.51 6.516 0 3.588-2.922 6.51-6.51 6.51h-.004zm3.564-4.86c-.198-.102-1.17-.576-1.35-.642-.18-.066-.312-.102-.444.096-.132.198-.51.642-.624.774-.114.132-.228.15-.426.048-.198-.102-.84-.306-1.596-.984-.588-.528-.984-1.176-1.104-1.374-.114-.198-.012-.306.084-.402.09-.09.198-.228.3-.342.096-.114.132-.198.198-.33.066-.132.03-.252-.018-.354-.048-.102-.444-1.068-.606-1.464-.162-.384-.324-.33-.444-.336-.114-.006-.246-.006-.378-.006-.132 0-.348.048-.528.246-.18.198-.69.678-.69 1.656 0 .978.708 1.92.81 2.058.096.138 1.392 2.13 3.378 2.988.474.204.84.324 1.128.42.48.15.912.132 1.26-.018.384-.168 1.17-.48 1.338-.948.168-.468.168-.87.12-.948-.048-.078-.18-.126-.378-.228z"/></svg> WhatsApp
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Egypt-Focused Accessibility Tips Sidebar -->
                <aside class="story-reader-sidebar">
                    <div style="font-size: 0.6rem; font-weight: bold; text-transform: uppercase; letter-spacing: 0.2em; color: var(--primary-color);">Accessible Egypt</div>
                    <h3 class="serif-title" style="font-size: 1.4rem; margin: 0; line-height: 1.2; color: var(--text-color);">Egypt Quick Guidelines</h3>
                    <div style="width: 2rem; height: 1px; background: var(--primary-color);"></div>
                    <p style="color: var(--text-color); opacity: 0.8; font-size: 0.85rem; line-height: 1.6; margin: 0;">
                        Navigating historic locations can present physical bottlenecks. Here are vital guides to planning a seamless adventure:
                    </p>
                    
                    <ul class="guidelines-bullets-list" style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 1.25rem;">
                        <li style="display: flex; gap: 0.75rem; font-size: 0.8rem; line-height: 1.5; color: var(--text-color); opacity: 0.9;">
                            <span style="font-size: 1.2rem; line-height: 1; color: var(--primary-color);">♿</span>
                            <div>
                                <strong style="color: var(--text-color);">Terrain & Wheelchairs:</strong>
                                Giza pyramids, Karnak, and Luxor temples have paved tactile access corridors, but historic paths remain sandy. Hire helper guides.
                            </div>
                        </li>
                        <li style="display: flex; gap: 0.75rem; font-size: 0.8rem; line-height: 1.5; color: var(--text-color); opacity: 0.9;">
                            <span style="font-size: 1.2rem; line-height: 1; color: var(--primary-color);">👁️</span>
                            <div>
                                <strong style="color: var(--text-color);">Sensory Landscapes:</strong>
                                The sights of Khan el-Khalili are rich. Use local registered guides trained in descriptive narrations for sensory mappings.
                            </div>
                        </li>
                        <li style="display: flex; gap: 0.75rem; font-size: 0.8rem; line-height: 1.5; color: var(--text-color); opacity: 0.9;">
                            <span style="font-size: 1.2rem; line-height: 1; color: var(--primary-color);">🚆</span>
                            <div>
                                <strong style="color: var(--text-color);">Metro Accessibility:</strong>
                                Cairo Metro Line 3 is equipped with elevators and modern wheelchair-accessible cabins at most terminal stations.
                            </div>
                        </li>
                        <li style="display: flex; gap: 0.75rem; font-size: 0.8rem; line-height: 1.5; color: var(--text-color); opacity: 0.9;">
                            <span style="font-size: 1.2rem; line-height: 1; color: var(--primary-color);">🦮</span>
                            <div>
                                <strong style="color: var(--text-color);">Service Animals:</strong>
                                Ensure international veterinary certifications are carried. While hotels are obliging, many archaeological sites restrict animal entry.
                            </div>
                        </li>
                    </ul>
                </aside>
            </div>
        </div>

        <!-- Related Stories Recommendations -->
        <?php if (!empty($relatedStories)): ?>
            <section class="related-stories-section">
                <div class="container">
                    <div class="intro-content" style="margin-bottom: 3rem;">
                        <span class="section-label">Read Next</span>
                        <h3 class="serif-title" style="margin: 0; font-size: 2rem; color: var(--text-color);">Related Chronicles</h3>
                    </div>
                    
                    <div class="story-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem;">
                        <?php foreach ($relatedStories as $rel): 
                            $relCover = !empty($rel['cover_image']) ? $rel['cover_image'] : '/images/hero-bg.png';
                            ?>
                            <article class="post-card group">
                                <a href="<?php echo BASE_URL; ?>/women-stories/<?php echo e($rel['slug']); ?>" class="post-card-link" aria-label="Read <?php echo e($rel['title']); ?>">
                                    <div class="post-card-media ratio-landscape">
                                        <div class="progressive-image-placeholder"></div>
                                        <img src="<?php echo e(BASE_URL . $relCover); ?>" alt="<?php echo e($rel['title']); ?>" class="post-card-img" onload="this.classList.add('loaded');" />
                                    </div>
                                    <div class="post-card-body" style="padding: 1.5rem;">
                                        <div class="post-card-meta">
                                            <span><?php echo e($rel['author']); ?></span>
                                            <span class="meta-dot"></span>
                                            <span><?php echo e($rel['read_time']); ?></span>
                                        </div>
                                        <h4 class="serif-title" style="font-size: 1.1rem; margin: 0.5rem 0; color: var(--text-color);"><?php echo e($rel['title']); ?></h4>
                                    </div>
                                </a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </article>

    <?php
    require_once PATH_ROOT . '/includes/footer.php';
} else {
    // ==========================================
    // STORIES LISTING VIEW
    // ==========================================
    $category = $_GET['category'] ?? null;
    $search = $_GET['search'] ?? null;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 6;
    
    $categories = $storyMgr->getCategories();
    
    $storiesResult = $storyMgr->getStories([
        'category' => $category,
        'search' => $search,
        'page' => $page,
        'limit' => $limit
    ]);

    $stories = $storiesResult['data'];
    $meta = $storiesResult['meta'];

    // Get the absolute most recent story for the cinematic showcase
    $featuredStory = null;
    if ($page === 1 && empty($category) && empty($search) && !empty($stories)) {
        $featuredStory = array_shift($stories); // Take first out of list
    }

    require_once PATH_ROOT . '/includes/header.php';
    require_once PATH_ROOT . '/includes/navbar.php';
    ?>

    <main class="container">
        <!-- Breadcrumbs -->
        <?php renderBreadcrumbs(['Women Stories' => '']); ?>

        <!-- Grand Cinematic Parallax Header -->
        <section class="women-stories-hero-banner" data-scroll-reveal style="aspect-ratio: 21/9; display: flex; align-items: center; justify-content: center; padding: 2rem;">
            <div class="parallax-bg-wrapper" style="background-image: url('<?php echo BASE_URL; ?>/images/hero-bg.png'); background-size: cover; background-position: center; transform: translateY(-5%); z-index: 1;"></div>
            <div class="banner-gradient-overlay" style="z-index: 2;"></div>
            <div class="banner-content" style="position: relative; z-index: 3; text-align: center; max-width: 700px;">
                <span class="section-label" style="color: #ffffff; opacity: 0.9; letter-spacing: 0.3em;">Egypt Chronicles</span>
                <h1 class="serif-title" style="font-size: 3rem; margin-top: 1rem; margin-bottom: 1rem; text-shadow: 0 4px 12px rgba(0,0,0,0.4); color: #ffffff;">Women's Travel Stories</h1>
                <p style="font-weight: 300; font-size: 1.1rem; line-height: 1.6; opacity: 0.9; margin: 0; color: #ffffff;">
                    Inspiring travel memoirs, sensory roadmaps, and accessibility reviews written by women exploring the ancient mysteries of Egypt.
                </p>
            </div>
        </section>

        <!-- Search Bar and Category Tabs -->
        <section class="filterable-section" style="padding-top: 0; margin-bottom: 4rem; border: none;">
            <div style="display: flex; flex-direction: column; gap: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 2rem;">
                <!-- Search bar -->
                <form action="<?php echo BASE_URL; ?>/women-stories" method="GET" style="max-width: 500px; display: flex; gap: 0.5rem; align-self: flex-start; width: 100%;">
                    <?php if ($category): ?>
                        <input type="hidden" name="category" value="<?php echo e($category); ?>" />
                    <?php endif; ?>
                    <input 
                        type="text" 
                        name="search" 
                        value="<?php echo e($search); ?>" 
                        placeholder="Search stories..." 
                        class="form-input" 
                        aria-label="Search stories query"
                        style="border-radius: 8px; flex-grow: 1;"
                    />
                    <button type="submit" class="btn-primary" style="padding: 0 1.5rem; border-radius: 8px; font-size: 0.7rem;">
                        Search
                    </button>
                </form>

                <!-- Category Tabs -->
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
                    <div class="tabs-container" role="tablist" aria-label="Story categories">
                        <a href="<?php echo BASE_URL; ?>/women-stories" class="tab-btn <?php echo !$category ? 'active' : ''; ?>" role="tab">ALL STORIES</a>
                        <?php foreach ($categories as $cat): ?>
                            <a href="<?php echo BASE_URL; ?>/women-stories?category=<?php echo urlencode($cat); ?>" class="tab-btn <?php echo $category === $cat ? 'active' : ''; ?>" role="tab">
                                <?php echo e(strtoupper($cat)); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

        <div class="stories-main-layout" style="display: grid; grid-template-columns: 1fr; gap: 4rem;">
            <!-- 1. Cinematic Fullscreen Featured Story Section -->
            <?php if ($featuredStory): 
                $featCover = !empty($featuredStory['cover_image']) ? $featuredStory['cover_image'] : '/images/hero-bg.png';
                ?>
                <section class="cinematic-featured-story" data-scroll-reveal>
                    <div class="featured-story-grid">
                        <div class="featured-story-media">
                            <div class="progressive-image-placeholder"></div>
                            <img src="<?php echo e(BASE_URL . $featCover); ?>" alt="<?php echo e($featuredStory['title']); ?>" class="post-card-img" onload="this.classList.add('loaded');" />
                            <div class="story-overlay-gradient"></div>
                            <span class="badge-text" style="position: absolute; top: 1.5rem; left: 1.5rem; background: var(--primary-color); color: #fff; z-index: 10;"><?php echo e($featuredStory['category']); ?></span>
                        </div>
                        <div class="featured-story-body">
                            <span class="section-label" style="letter-spacing: 0.2em;">FEATURED MEMOIR</span>
                            <h2 class="serif-title" style="font-size: 2.2rem; line-height: 1.2; margin: 0; color: var(--text-color);"><?php echo e($featuredStory['title']); ?></h2>
                            <p style="color: var(--text-color); opacity: 0.8; font-size: 1rem; font-weight: 300; line-height: 1.6; margin: 0;"><?php echo e($featuredStory['excerpt']); ?></p>
                            <div style="display: flex; gap: 1.5rem; align-items: center; font-size: 0.75rem; color: var(--text-color); opacity: 0.6; font-weight: bold; margin-top: 0.5rem;">
                                <span>By <?php echo e($featuredStory['author']); ?></span>
                                <span class="meta-dot"></span>
                                <span><?php echo e($featuredStory['read_time']); ?></span>
                            </div>
                            <a href="<?php echo BASE_URL; ?>/women-stories/<?php echo e($featuredStory['slug']); ?>" class="btn-primary" style="margin-top: 1.5rem; align-self: flex-start; padding: 1rem 2rem; border-radius: 8px; font-size: 0.7rem;">
                                READ STORY
                            </a>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <!-- 2. Grid & Sidebar Layout -->
            <div class="grid-sidebar-layout">
                <div class="content-and-sidebar-grid">
                    
                    <!-- Stories Cards Grid -->
                    <div class="stories-cards-section">
                        <h3 class="serif-title" style="font-size: 1.5rem; margin-bottom: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; color: var(--text-color);">Latest Diaries</h3>
                        
                        <?php if (!empty($stories)): ?>
                            <div class="story-list-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2.5rem;">
                                <?php foreach ($stories as $story): 
                                    $storyCover = !empty($story['cover_image']) ? $story['cover_image'] : '/images/hero-bg.png';
                                    ?>
                                    <article class="post-card group" data-scroll-reveal>
                                        <a href="<?php echo BASE_URL; ?>/women-stories/<?php echo e($story['slug']); ?>" class="post-card-link" aria-label="Read <?php echo e($story['title']); ?>">
                                            <div class="post-card-media ratio-landscape">
                                                <div class="progressive-image-placeholder"></div>
                                                <img src="<?php echo e(BASE_URL . $storyCover); ?>" alt="<?php echo e($story['title']); ?>" loading="lazy" class="post-card-img" onload="this.classList.add('loaded');" />
                                                <div class="post-card-badge">
                                                    <span class="badge-text" style="background: var(--primary-color); color: #fff;"><?php echo e($story['category']); ?></span>
                                                </div>
                                            </div>
                                            
                                            <div class="post-card-body" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 0.75rem;">
                                                <div class="post-card-meta" style="margin-bottom: 0;">
                                                    <time datetime="<?php echo e($story['created_at']); ?>"><?php echo formatDate($story['created_at']); ?></time>
                                                    <span class="meta-dot"></span>
                                                    <span>By <?php echo e($story['author']); ?></span>
                                                </div>
                                                
                                                <h3 class="post-card-title" style="font-size: 1.2rem; margin-bottom: 0; line-height: 1.3; color: var(--text-color);"><?php echo e($story['title']); ?></h3>
                                                
                                                <p class="post-card-excerpt" style="font-size: 0.85rem; line-height: 1.5; color: var(--text-color); opacity: 0.8; -webkit-line-clamp: 2; flex-grow: 1; margin-bottom: 0;">
                                                    <?php echo e($story['excerpt']); ?>
                                                </p>
                                                
                                                <div class="post-card-cta" style="margin-top: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                                                    <span class="cta-text" style="font-size: 0.65rem; font-weight: 900; color: var(--primary-color);">
                                                        READ STORY <span class="cta-arrow" aria-hidden="true">&rarr;</span>
                                                    </span>
                                                    <span style="font-size: 0.65rem; color: var(--text-color); opacity: 0.6; font-weight: bold;"><?php echo e($story['read_time']); ?></span>
                                                </div>
                                            </div>
                                        </a>
                                    </article>
                                <?php endforeach; ?>
                            </div>

                            <!-- Pagination Row -->
                            <?php if ($meta['totalPages'] > 1): ?>
                                <div class="pagination-row" data-scroll-reveal style="margin-top: 4rem;">
                                    <?php if ($page > 1): ?>
                                        <a href="?page=<?php echo $page - 1; ?><?php echo $category ? '&category='.urlencode($category) : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" class="page-link">&larr;</a>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $meta['totalPages']; $i++): ?>
                                        <a href="?page=<?php echo $i; ?><?php echo $category ? '&category='.urlencode($category) : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>

                                    <?php if ($page < $meta['totalPages']): ?>
                                        <a href="?page=<?php echo $page + 1; ?><?php echo $category ? '&category='.urlencode($category) : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" class="page-link">&rarr;</a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                        <?php else: ?>
                            <div style="text-align: center; padding: 5rem 0; border: 1px dashed var(--border-color); background-color: var(--surface-color); border-radius: 12px; margin-top: 1rem;">
                                <div style="font-size: 3rem; margin-bottom: 1rem;">📖</div>
                                <h3 class="serif-title" style="font-size: 1.3rem; margin-bottom: 0.5rem; color: var(--text-color);">No Stories Found</h3>
                                <p style="color: var(--text-color); opacity: 0.6; font-weight: 300; font-size: 0.9rem;">We couldn't find any travel logs matching your criteria.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Sidebar Guidelines -->
                    <aside class="stories-index-sidebar">
                        <div style="font-size: 0.6rem; font-weight: bold; text-transform: uppercase; letter-spacing: 0.2em; color: var(--primary-color);">Accessible Egypt</div>
                        <h3 class="serif-title" style="font-size: 1.4rem; margin: 0; line-height: 1.2; color: var(--text-color);">Egypt Quick Guidelines</h3>
                        <div style="width: 2rem; height: 1px; background: var(--primary-color);"></div>
                        <p style="color: var(--text-color); opacity: 0.8; font-size: 0.85rem; line-height: 1.6; margin: 0;">
                            Plan your journey to the historic marvels of Egypt with ease. Quick highlights for accessible route planning:
                        </p>
                        
                        <ul class="guidelines-bullets-list" style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 1.25rem;">
                            <li style="display: flex; gap: 0.75rem; font-size: 0.8rem; line-height: 1.5; color: var(--text-color); opacity: 0.9;">
                                <span style="font-size: 1.2rem; line-height: 1; color: var(--primary-color);">♿</span>
                                <div>
                                    <strong style="color: var(--text-color);">Ancient Sites:</strong>
                                    Giza pyramids, Karnak, and Luxor temples now feature paved mobility corridors. Deep sand paths remain; helper guides are recommended.
                                </div>
                            </li>
                            <li style="display: flex; gap: 0.75rem; font-size: 0.8rem; line-height: 1.5; color: var(--text-color); opacity: 0.9;">
                                <span style="font-size: 1.2rem; line-height: 1; color: var(--primary-color);">👁️</span>
                                <div>
                                    <strong style="color: var(--text-color);">Sensory Assistance:</strong>
                                    Traditional markets like Khan el-Khalili are rich. Hiring descriptive guides can enhance the navigation experience significantly.
                                </div>
                            </li>
                            <li style="display: flex; gap: 0.75rem; font-size: 0.8rem; line-height: 1.5; color: var(--text-color); opacity: 0.9;">
                                <span style="font-size: 1.2rem; line-height: 1; color: var(--primary-color);">🚆</span>
                                <div>
                                    <strong style="color: var(--text-color);">Transit Systems:</strong>
                                    Cairo Metro Line 3 is modern and offers passenger lifts, accessible restrooms, and wheelchair space inside train cabins.
                                </div>
                            </li>
                        </ul>
                    </aside>

                </div>
            </div>

        </div>
    </main>

    <?php
    require_once PATH_ROOT . '/includes/footer.php';
}
?>
