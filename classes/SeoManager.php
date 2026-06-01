<?php
// classes/SeoManager.php
// Dynamic SEO & Metadata Manager Class

class SeoManager {
    
    // Renders meta tags for the header based on type and current record data
    public static function renderMeta($type = 'home', $data = null) {
        $pm = new PostManager();
        $settings = $pm->getSiteSettings();

        // Defaults
        $siteName = $settings['siteName'] ?? 'Young Over 60';
        $title = $settings['metaTitle'] ?? $siteName;
        $description = $settings['metaDescription'] ?? 'Active Travel & Inspiration for Seniors';
        $ogImage = !empty($settings['ogImage']) ? $settings['ogImage'] : ($settings['logoUrl'] ?? '/images/hero-bg.png');
        $url = BASE_URL . $_SERVER['REQUEST_URI'];
        $canonical = !empty($data['canonical']) ? $data['canonical'] : $url;
        $robots = 'index, follow';
        $twitterCard = 'summary_large_image';

        // Parse dynamic pages
        if ($type === 'post' && $data) {
            $title = t($data, 'title') . ' | ' . $siteName;
            $excerpt = t($data, 'excerpt');
            $content = t($data, 'content');
            $description = !empty($excerpt) ? $excerpt : substr(strip_tags($content), 0, 160);
            $coverField = !empty($data['coverImage']) ? $data['coverImage'] : ($data['cover_image'] ?? null);
            if ($coverField) {
                $ogImage = $coverField;
            }
        } elseif ($type === 'category' && $data) {
            $catName = t($data, 'name');
            $catDesc = t($data, 'description');
            $title = $catName . ' | ' . $siteName;
            $description = !empty($catDesc) ? $catDesc : "Read accessible stories and destinations in {$catName}.";
            if (!empty($data['image'])) {
                $ogImage = $data['image'];
            }
        } elseif ($type === 'news') {
            $title = 'Latest News & Updates | ' . $siteName;
            $description = 'Stay up-to-date with the latest travel accessibility news, inclusive destination announcements, and company updates.';
        } elseif ($type === 'women-stories') {
            $title = 'Women\'s Travel Stories & Egypt Chronicles | ' . $siteName;
            $description = 'Inspiring travel memoirs, sensory roadmaps, and accessibility reviews written by women exploring the world.';
        } elseif ($type === 'podcasts') {
            $title = 'Audio Journeys & Podcasts | ' . $siteName;
            $description = 'Listen to inspiring conversations, travel guides, and real accounts of travellers navigating the globe.';
        } elseif ($type === 'contact') {
            $title = 'Get In Touch | ' . $siteName;
            $description = 'Have a question or want to share your travel story? Contact the Young Over 60 team.';
        } elseif ($type === 'accessibility') {
            $title = 'Accessibility Compliance & Tools | ' . $siteName;
            $description = 'Our commitment to accessibility. Use our custom accessibility widget to adjust font size, contrast, and layout.';
        } elseif ($type === 'admin') {
            $title = __('admin_dashboard_area') . ' | ' . $siteName;
            $robots = 'noindex, nofollow';
        } elseif ($type === 'custom_page' && $data) {
            // Custom CMS Page
            $title = !empty($data['title']) ? $data['title'] : $siteName;
            $description = !empty($data['description']) ? $data['description'] : $description;
            if (!empty($data['ogImage'])) {
                $ogImage = $data['ogImage'];
            }
            $robots = 'index, follow';
        } elseif ($type === '404') {
            $title = 'Page Not Found | ' . $siteName;
            $robots = 'noindex, nofollow';
        }

        // Normalize ogImage URL
        if (!empty($ogImage) && !str_starts_with($ogImage, 'http')) {
            $ogImage = BASE_URL . $ogImage;
        }

        // Output html
        echo '<!-- SEO Meta Tags -->' . "\n";
        echo '<title>' . e($title) . '</title>' . "\n";
        echo '<meta name="description" content="' . e($description) . '">' . "\n";
        echo '<meta name="robots" content="' . e($robots) . '">' . "\n";
        echo '<link rel="canonical" href="' . e($canonical) . '">' . "\n";
        
        echo '<!-- Open Graph / Facebook -->' . "\n";
        echo '<meta property="og:type" content="' . ($type === 'post' ? 'article' : 'website') . '">' . "\n";
        echo '<meta property="og:url" content="' . e($url) . '">' . "\n";
        echo '<meta property="og:title" content="' . e($title) . '">' . "\n";
        echo '<meta property="og:description" content="' . e($description) . '">' . "\n";
        if ($ogImage) {
            echo '<meta property="og:image" content="' . e($ogImage) . '">' . "\n";
        }
        echo '<meta property="og:site_name" content="' . e($siteName) . '">' . "\n";

        echo '<!-- Twitter -->' . "\n";
        echo '<meta property="twitter:card" content="' . e($twitterCard) . '">' . "\n";
        echo '<meta property="twitter:url" content="' . e($url) . '">' . "\n";
        echo '<meta property="twitter:title" content="' . e($title) . '">' . "\n";
        echo '<meta property="twitter:description" content="' . e($description) . '">' . "\n";
        if ($ogImage) {
            echo '<meta property="twitter:image" content="' . e($ogImage) . '">' . "\n";
        }
        
        // Output Favicon
        $favicon = $settings['faviconUrl'] ?? '/favicon.ico';
        if (!empty($favicon) && !str_starts_with($favicon, 'http') && !str_starts_with($favicon, '//')) {
            $favicon = BASE_URL . $favicon;
        }
        echo '<link rel="shortcut icon" href="' . e($favicon) . '" type="image/x-icon">' . "\n";
    }
}
