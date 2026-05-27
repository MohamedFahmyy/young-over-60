<?php
// pages/category.php
// Dynamic Category Posts Archive Grid

// 1. Fetch category by slug
$slug = $_GET['slug'] ?? '';
$category = $pm->getCategoryBySlug($slug);

if (!$category) {
    // Return 404 if category doesn't exist
    require_once PATH_ROOT . '/pages/404.php';
    exit();
}

// Set SEO Page Meta context
$seoPageType = 'category';
$seoPageData = $category;

// 2. Fetch posts in this category with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 9;

$params = [
    'categoryId' => $category['id'],
    'status' => 'PUBLISHED',
    'page' => $page,
    'limit' => $limit
];

$postsResult = $pm->getPosts($params);
$posts = $postsResult['data'];
$meta = $postsResult['meta'];

require_once PATH_ROOT . '/includes/header.php';
require_once PATH_ROOT . '/includes/navbar.php';
?>

<!-- Category Hero Banner -->
<section class="category-hero">
    <img src="<?php echo e(!empty($category['image']) ? $category['image'] : '/images/hero-bg.png'); ?>" alt="<?php echo e($category['name']); ?> Cover" />
    <div class="category-hero-overlay"></div>
    
    <div class="container category-hero-content">
        <a href="<?php echo BASE_URL; ?>/" class="back-home-link">
            <span>&larr;</span> Back to Home
        </a>
        <div style="max-width: 48rem;" data-scroll-reveal>
            <h1 class="category-title"><?php echo e($category['name']); ?></h1>
            <p class="category-desc">
                <?php echo e($category['description']); ?>
            </p>
        </div>
    </div>
</section>

<!-- Content Grid List -->
<main class="container">
    <section class="content-list-section">
        <div class="content-list-header" data-scroll-reveal>
            <h2 class="serif-title" style="font-size: 1.75rem; margin:0;">
                Latest in <?php echo e($category['name']); ?>
            </h2>
            <span class="list-count-label">
                <?php echo $meta['total']; ?> <?php echo $meta['total'] === 1 ? 'Story' : 'Stories'; ?> available
            </span>
        </div>

        <!-- Posts Grid -->
        <?php if (!empty($posts)): ?>
            <div class="story-grid">
                <?php foreach ($posts as $post): ?>
                    <?php renderPostCard($post); ?>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination Controls -->
            <?php if ($meta['totalPages'] > 1): ?>
                <div class="pagination-row" data-scroll-reveal>
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>" class="page-link" aria-label="Previous Page">&larr;</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $meta['totalPages']; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $meta['totalPages']): ?>
                        <a href="?page=<?php echo $page + 1; ?>" class="page-link" aria-label="Next Page">&rarr;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div style="text-align: center; padding: 6rem 0; border: 1px dashed var(--accent-color); background-color: #fafafa;" data-scroll-reveal>
                <div style="font-size: 3rem; margin-bottom: 1.5rem;">📰</div>
                <h3 class="serif-title" style="font-size: 1.5rem; margin-bottom: 0.5rem;">No Stories Yet</h3>
                <p style="color: #888; font-weight: 300;">We are currently researching and drafting stories for <?php echo e($category['name']); ?>. Check back soon!</p>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php 
require_once PATH_ROOT . '/includes/footer.php'; 
?>
