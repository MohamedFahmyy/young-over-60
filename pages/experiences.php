<?php
// pages/experiences.php
// Experiences Categories Landing Page

$seoPageType = 'experiences';

// Fetch all categories
$categories = $pm->getCategories();

// Filter experiences
$expList = array_filter($categories, function($c) {
    return in_array($c['slug'], [
        'animal-encounters', 'cruises', 'disney', 'family-travel', 
        'festivals-events', 'food-drink', 'road-trips', 'sports', 
        'theme-parks', 'wellness'
    ]);
});

require_once PATH_ROOT . '/includes/header.php';
require_once PATH_ROOT . '/includes/navbar.php';
?>

<main class="container">
    <!-- Breadcrumbs -->
    <?php renderBreadcrumbs([__('nav_experiences') => '']); ?>

    <section class="content-list-section">
        <div class="mb-16 text-center max-w-3xl mx-auto" data-scroll-reveal>
            <span class="section-label"><?= __('exp_label') ?></span>
            <h1 class="serif-title" style="margin-top: 1rem;"><?= __('exp_title') ?></h1>
            <p style="color: #666666; font-size: 1.1rem; font-weight: 300; margin-top: 1rem;">
                <?= __('exp_desc') ?>
            </p>
        </div>

        <div class="needs-grid" style="margin-top: 4rem;">
            <?php foreach ($expList as $exp): 
                // Count posts in this category
                $postCountRes = $pm->getPosts(['categoryId' => $exp['id'], 'status' => 'PUBLISHED', 'limit' => 1]);
                $count = $postCountRes['meta']['total'];
                ?>
                <a href="<?php echo BASE_URL; ?>/category/<?php echo $exp['slug']; ?>" class="needs-card" data-scroll-reveal aria-label="Explore <?php echo e(t($exp, 'name')); ?>">
                    <img src="<?php echo e(!empty($exp['image']) ? $exp['image'] : '/images/hero-bg.png'); ?>" alt="<?php echo e(t($exp, 'name')); ?>" loading="lazy" />
                    <div class="needs-card-overlay">
                        <div class="needs-card-line"></div>
                        <h2 class="needs-card-title"><?php echo e(t($exp, 'name')); ?></h2>
                        <p style="color:rgba(255,255,255,0.7); font-size: 0.7rem; text-transform:uppercase; letter-spacing:0.15em; margin-top:0.5rem;">
                            <?php echo $count; ?> <?php echo $count === 1 ? __('story_single') : __('story_plural'); ?>
                        </p>
                        <div class="needs-card-hover-text">
                            <span class="hover-label-btn"><?= __('btn_view_stories') ?></span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?php 
require_once PATH_ROOT . '/includes/footer.php'; 
?>
