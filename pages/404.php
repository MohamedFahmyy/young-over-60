<?php
// pages/404.php
// Custom 404 Not Found Page Template

header("HTTP/1.0 404 Not Found");

// Meta override for header
$seoPageType = '404';

// Include global layout files
require_once PATH_ROOT . '/includes/header.php';
require_once PATH_ROOT . '/includes/navbar.php';
?>

<main class="container">
    <div class="notfound-container animate-in fade-in" data-scroll-reveal>
        <h1 class="notfound-code">404</h1>
        <h2 class="notfound-title">Lost in Transition</h2>
        <div class="intro-divider" style="margin: 0 auto 2rem auto;"></div>
        
        <p class="notfound-text">
            The page you are looking for might have been removed, had its name changed, or is temporarily unavailable. 
        </p>

        <!-- Help Search -->
        <div style="max-width: 500px; width: 100%; margin-bottom: 3rem;">
            <p style="font-size: 0.75rem; text-transform: uppercase; font-weight: 900; letter-spacing: 0.15em; color: #999; margin-bottom: 1rem;">
                Try searching our stories instead
            </p>
            <button class="btn-primary" data-open-search style="width: 100%; text-align: left; padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center; border-radius: 8px;">
                <span>Type to search...</span>
                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </button>
        </div>

        <a href="<?php echo BASE_URL; ?>/" class="btn-underline">
            Return to Homepage
        </a>
    </div>
</main>

<?php 
require_once PATH_ROOT . '/includes/footer.php'; 
?>
