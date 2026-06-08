<?php
// pages/destinations.php
// Destinations Category Landing Page

$seoPageType = 'destinations';

// Fetch all categories
$categories = $pm->getCategories();

// Filter destinations
$destList = array_filter($categories, function($c) {
    return in_array($c['slug'], [
        'australia', 'new-south-wales', 'victoria', 'queensland', 'western-australia', 
        'south-australia', 'tasmania', 'northern-territory', 'australian-capital-territory', 
        'asia-pacific', 'europe', 'africa', 'americas'
    ]);
});

// Sort Australia to the top, then others
usort($destList, function($a, $b) {
    if ($a['slug'] === 'australia') return -1;
    if ($b['slug'] === 'australia') return 1;
    return strcmp($a['name'], $b['name']);
});

$seoPageData = $destList;

require_once PATH_ROOT . '/includes/header.php';
require_once PATH_ROOT . '/includes/navbar.php';
?>

<main class="container">
    <!-- Breadcrumbs -->
    <?php renderBreadcrumbs(['Destinations' => '']); ?>

    <section class="content-list-section">
        <div class="mb-16 text-center max-w-3xl mx-auto" data-scroll-reveal>
            <span class="section-label">Explore Regions</span>
            <h1 class="serif-title" style="margin-top: 1rem;">Destinations</h1>
            <p style="color: #666666; font-size: 1.1rem; font-weight: 300; margin-top: 1rem;">
                Browse by geography to locate accessible accommodations, transportation guides, and local stories customized for travellers with mobility, sensory, and cognitive requirements.
            </p>
        </div>

        <div class="needs-grid" style="margin-top: 4rem;">
            <?php foreach ($destList as $dest): 
                // Count posts in this category
                $postCountRes = $pm->getPosts(['categoryId' => $dest['id'], 'status' => 'PUBLISHED', 'limit' => 1]);
                $count = $postCountRes['meta']['total'];
                ?>
                <a href="<?php echo BASE_URL; ?>/category/<?php echo $dest['slug']; ?>" class="needs-card" data-scroll-reveal aria-label="Explore <?php echo e($dest['name']); ?>">
                    <img src="<?php echo e(!empty($dest['image']) ? $dest['image'] : '/images/hero-bg.png'); ?>" alt="<?php echo e($dest['name']); ?>" loading="lazy" />
                    <div class="needs-card-overlay">
                        <div class="needs-card-line"></div>
                        <h2 class="needs-card-title"><?php echo e($dest['name']); ?></h2>
                        <p style="color:rgba(255,255,255,0.7); font-size: 0.7rem; text-transform:uppercase; letter-spacing:0.15em; margin-top:0.5rem;">
                            <?php echo $count; ?> <?php echo $count === 1 ? 'Story' : 'Stories'; ?>
                        </p>
                        <div class="needs-card-hover-text">
                            <span class="hover-label-btn">View Destinations</span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Featured Guides & Stories Section (Internal Linking & Crawl Depth) -->
        <section style="margin-top: 5rem; border-top: 1px solid var(--border-color); padding-top: 4rem;">
            <div class="mb-16 text-center max-w-3xl mx-auto" data-scroll-reveal>
                <span class="section-label">Read Stories</span>
                <h3 class="serif-title" style="margin-top: 1rem;">Featured Guides &amp; Experiences</h3>
                <p style="color: #666666; font-size: 1.1rem; font-weight: 300; margin-top: 0.5rem;">
                    Get practical, real-world accessibility details and travel guides from our experienced travelers.
                </p>
            </div>
            
            <div class="posts-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; margin-top: 2rem;">
                <?php
                if ($pm instanceof PostManager) {
                    $latestPostsRes = $pm->getPosts(['status' => 'PUBLISHED', 'limit' => 3]);
                    $latestPosts = $latestPostsRes['data'] ?? [];
                    foreach ($latestPosts as $post) {
                        renderPostCard($post);
                    }
                }
                ?>
            </div>
        </section>
    </section>
</main>

<?php 
require_once PATH_ROOT . '/includes/footer.php'; 
?>
