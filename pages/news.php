<?php
// pages/news.php
// News Archive and Search Result Page

$seoPageType = 'news';

// 1. Fetch current filters and pagination values
$search = $_GET['search'] ?? null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 9; // Grid of 3x3 posts

$params = [
    'status' => 'PUBLISHED',
    'page' => $page,
    'limit' => $limit,
    'search' => $search
];

// Fetch posts
$postsResult = $pm->getPosts($params);
$posts = $postsResult['data'];
$meta = $postsResult['meta'];

require_once PATH_ROOT . '/includes/header.php';
require_once PATH_ROOT . '/includes/navbar.php';
?>

<main class="container">
    <!-- Breadcrumbs -->
    <?php renderBreadcrumbs(['News & Updates' => '']); ?>

    <section class="content-list-section">
        <!-- Header -->
        <div class="mb-16 text-center max-w-3xl mx-auto" data-scroll-reveal>
            <span class="section-label">Latest Content</span>
            <h1 class="serif-title" style="margin-top: 1rem;">News & Stories</h1>
            <p style="color: #666666; font-size: 1.1rem; font-weight: 300; margin-top: 1rem; margin-bottom: 3rem;">
                Stay updated with the latest accessibility news, travel advice, inclusive guidelines, and editor announcements.
            </p>
            
            <!-- Search bar -->
            <form action="<?php echo BASE_URL; ?>/news" method="GET" style="max-width: 600px; margin: 0 auto; display: flex; gap: 0.5rem;">
                <input 
                    type="text" 
                    name="search" 
                    value="<?php echo e($search); ?>" 
                    placeholder="Search stories..." 
                    class="form-input" 
                    aria-label="Search stories query"
                    style="border-radius: 8px; flex-grow: 1;"
                />
                <button type="submit" class="btn-primary" style="padding: 0 2rem; border-radius: 8px; font-size: 0.75rem;">
                    Search
                </button>
            </form>
            <?php if ($search): ?>
                <p style="margin-top: 1rem; font-size: 0.85rem; color: #888;">
                    Showing results for "<strong><?php echo e($search); ?></strong>" &bull; <a href="<?php echo BASE_URL; ?>/news" style="text-decoration: underline;">Clear search</a>
                </p>
            <?php endif; ?>
        </div>

        <div class="content-list-header" data-scroll-reveal>
            <h2 class="serif-title" style="font-size: 1.75rem; margin:0;">
                <?php echo $search ? 'Search Results' : 'Recent Articles'; ?>
            </h2>
            <span class="list-count-label">
                <?php echo $meta['total']; ?> <?php echo $meta['total'] === 1 ? 'Story' : 'Stories'; ?> found
            </span>
        </div>

        <!-- Grid -->
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
                        <a href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="page-link" aria-label="Previous Page">&larr;</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $meta['totalPages']; $i++): ?>
                        <a href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $meta['totalPages']): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="page-link" aria-label="Next Page">&rarr;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div style="text-align: center; padding: 6rem 0; border: 1px dashed var(--accent-color); background-color: #fafafa;" data-scroll-reveal>
                <div style="font-size: 3rem; margin-bottom: 1.5rem;">📰</div>
                <h3 class="serif-title" style="font-size: 1.5rem; margin-bottom: 0.5rem;">No Stories Found</h3>
                <p style="color: #888; font-weight: 300;">We couldn't find any stories matching your search parameter.</p>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php 
require_once PATH_ROOT . '/includes/footer.php'; 
?>
