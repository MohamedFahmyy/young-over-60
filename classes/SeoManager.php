<?php
// classes/SeoManager.php
// Dynamic SEO & Metadata Manager Class

class SeoManager {
    
    // Helper to clean and normalize canonical URLs by stripping tracking parameters
    public static function cleanCanonicalUrl($url) {
        $parsed = parse_url($url);
        if (!$parsed) return $url;
        
        $scheme = isset($parsed['scheme']) ? $parsed['scheme'] . '://' : '';
        $host = $parsed['host'] ?? '';
        $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
        $path = $parsed['path'] ?? '';
        
        $query = '';
        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $queryParams);
            // Keep pagination or category filtering, but discard tracking params (utm_*, gclid, etc.)
            $essentialParams = ['page', 'category', 'search'];
            $filteredParams = [];
            foreach ($essentialParams as $param) {
                if (isset($queryParams[$param])) {
                    $filteredParams[$param] = $queryParams[$param];
                }
            }
            if (!empty($filteredParams)) {
                $query = '?' . http_build_query($filteredParams);
            }
        }
        
        return $scheme . $host . $port . $path . $query;
    }

    // Helper to build canonical language specific URLs
    public static function getAlternateUrls($type, $data) {
        $urls = [
            'en' => BASE_URL,
            'ar' => BASE_URL . '/ar',
            'nl' => BASE_URL . '/nl'
        ];

        if ($type === 'post' && $data) {
            // Distinguish blog post vs women's story
            if (isset($data['categoryId'])) {
                $slug_en = !empty($data['slug_en']) ? $data['slug_en'] : ($data['slug_ar'] ?? '');
                $slug_ar = !empty($data['slug_ar']) ? $data['slug_ar'] : ($data['slug_en'] ?? '');
                $slug_nl = !empty($data['slug_nl']) ? $data['slug_nl'] : $slug_en;
                $urls['en'] = BASE_URL . '/posts/' . $slug_en;
                $urls['ar'] = BASE_URL . '/ar/posts/' . $slug_ar;
                $urls['nl'] = BASE_URL . '/nl/posts/' . $slug_nl;
            } else {
                $slug_en = !empty($data['slug_en']) ? $data['slug_en'] : ($data['slug_ar'] ?? '');
                $slug_ar = !empty($data['slug_ar']) ? $data['slug_ar'] : ($data['slug_en'] ?? '');
                $slug_nl = !empty($data['slug_nl']) ? $data['slug_nl'] : $slug_en;
                $urls['en'] = BASE_URL . '/women-stories/' . $slug_en;
                $urls['ar'] = BASE_URL . '/ar/women-stories/' . $slug_ar;
                $urls['nl'] = BASE_URL . '/nl/women-stories/' . $slug_nl;
            }
        } elseif ($type === 'category' && $data) {
            $slug_en = !empty($data['slug_en']) ? $data['slug_en'] : ($data['slug_ar'] ?? '');
            $slug_ar = !empty($data['slug_ar']) ? $data['slug_ar'] : ($data['slug_en'] ?? '');
            $slug_nl = !empty($data['slug_nl']) ? $data['slug_nl'] : $slug_en;
            $urls['en'] = BASE_URL . '/category/' . $slug_en;
            $urls['ar'] = BASE_URL . '/ar/category/' . $slug_ar;
            $urls['nl'] = BASE_URL . '/nl/category/' . $slug_nl;
        } elseif ($type === 'custom_page' && $data) {
            $slug_en = !empty($data['slug_en']) ? $data['slug_en'] : ($data['slug_ar'] ?? '');
            $slug_ar = !empty($data['slug_ar']) ? $data['slug_ar'] : ($data['slug_en'] ?? '');
            $slug_nl = !empty($data['slug_nl']) ? $data['slug_nl'] : $slug_en;
            $urls['en'] = BASE_URL . '/pages/' . $slug_en;
            $urls['ar'] = BASE_URL . '/ar/pages/' . $slug_ar;
            $urls['nl'] = BASE_URL . '/nl/pages/' . $slug_nl;
        } else {
            // Simple inner pages
            $pages = ['destinations', 'experiences', 'news', 'accessibility', 'contact', 'podcasts', 'about'];
            if (in_array($type, $pages)) {
                $urls['en'] = BASE_URL . '/' . $type;
                $urls['ar'] = BASE_URL . '/ar/' . $type;
                $urls['nl'] = BASE_URL . '/nl/' . $type;
            }
        }
        
        return $urls;
    }

    // Helper to format duration to ISO 8601 duration format (e.g. PT24M18S)
    private static function formatISO8601Duration($durationStr) {
        if (empty($durationStr)) return 'PT0S';
        $parts = explode(':', $durationStr);
        if (count($parts) === 3) {
            return 'PT' . (int)$parts[0] . 'H' . (int)$parts[1] . 'M' . (int)$parts[2] . 'S';
        } elseif (count($parts) === 2) {
            return 'PT' . (int)$parts[0] . 'M' . (int)$parts[1] . 'S';
        }
        return 'PT' . (int)$durationStr . 'S';
    }
    
    // Renders meta tags for the header based on type and current record data
    public static function renderMeta($type = 'home', $data = null) {
        $activeLang = defined('CURRENT_LANG') ? CURRENT_LANG : 'en';
        $pm = new PostManager();
        $settings = $pm->getSiteSettings();

        // Defaults
        $siteName = t($settings, 'siteName') ?: 'Young Over 60';
        $title = t($settings, 'metaTitle') ?: $siteName;
        $description = t($settings, 'metaDescription') ?: 'Active Travel & Inspiration for Seniors';
        $ogImage = !empty($settings['ogImage']) ? $settings['ogImage'] : ($settings['logoUrl'] ?? '/images/hero-bg.png');
        $url = BASE_URL . $_SERVER['REQUEST_URI'];
        $robots = 'index, follow';
        $twitterCard = 'summary_large_image';
        
        // Custom overrides from DB page / data values
        $customOgTitle = null;
        $customOgDesc = null;
        $customOgImage = null;
        $customTwitterTitle = null;
        $customTwitterDesc = null;
        $customTwitterImage = null;
        $customCanonical = null;

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
        } elseif ($type === 'destinations') {
            $title = 'Accessible Travel Destinations & Guides | ' . $siteName;
            $description = 'Explore accessible senior travel guides and hand-picked destinations across Egypt and beyond.';
        } elseif ($type === 'experiences') {
            $title = 'Travel Experiences & Senior Adventures | ' . $siteName;
            $description = 'Curated travel experiences for active senior travelers. Discover historical tours, cruises, and retreats.';
        } elseif ($type === 'contact') {
            $title = 'Get In Touch | ' . $siteName;
            $description = 'Have a question or want to share your travel story? Contact the Young Over 60 team.';
        } elseif ($type === 'about') {
            $title = 'About Us — Beyond 60, Life Begins Again | ' . $siteName;
            $description = 'Discover the story, vision, and people behind Young Over 60. We believe travel has no age limit and every journey is a chance to rediscover life.';
        } elseif ($type === 'accessibility') {
            $title = 'Accessibility Compliance & Tools | ' . $siteName;
            $description = 'Our commitment to accessibility. Use our custom accessibility widget to adjust font size, contrast, and layout.';
        } elseif ($type === 'admin') {
            $title = 'Admin Dashboard' . ' | ' . $siteName;
            $robots = 'noindex, nofollow';
        } elseif ($type === 'custom_page' && $data) {
            // Custom CMS Page
            $rawTitle = t($data, 'meta_title') ?: t($data, 'title');
            $title = !empty($rawTitle) ? ($rawTitle . ' | ' . $siteName) : ( ($data['title'] ?? '') . ' | ' . $siteName );
            
            $rawDesc = t($data, 'meta_description') ?: t($data, 'excerpt');
            $description = !empty($rawDesc) ? $rawDesc : ($data['description'] ?? $description);
            
            // Canonical redirects in page record
            $dbCanonical = t($data, 'canonical_url');
            if (!empty($dbCanonical)) {
                $customCanonical = $dbCanonical;
            }
            
            // Dynamic OG / Twitter overrides from DB
            $customOgTitle = t($data, 'og_title');
            $customOgDesc = t($data, 'og_description');
            $customOgImage = !empty($data['og_image']) ? $data['og_image'] : null;
            
            $customTwitterTitle = t($data, 'twitter_title');
            $customTwitterDesc = t($data, 'twitter_description');
            $customTwitterImage = !empty($data['og_image']) ? $data['og_image'] : null;
            
            $featured = !empty($data['hero_image']) ? $data['hero_image'] : (!empty($data['featured_image']) ? $data['featured_image'] : null);
            if ($featured) {
                $ogImage = $featured;
            }
            $robots = 'index, follow';
        } elseif ($type === '404') {
            $title = 'Page Not Found | ' . $siteName;
            $robots = 'noindex, nofollow';
        }

        // Normalize ogImage URL to absolute path
        if (!empty($ogImage) && !str_starts_with($ogImage, 'http') && !str_starts_with($ogImage, '//')) {
            $ogImage = BASE_URL . $ogImage;
        }
        if (!empty($customOgImage) && !str_starts_with($customOgImage, 'http') && !str_starts_with($customOgImage, '//')) {
            $customOgImage = BASE_URL . $customOgImage;
        }
        if (!empty($customTwitterImage) && !str_starts_with($customTwitterImage, 'http') && !str_starts_with($customTwitterImage, '//')) {
            $customTwitterImage = BASE_URL . $customTwitterImage;
        }

        // Generate clean canonical and hreflang alternates
        $alternateUrls = self::getAlternateUrls($type, $data);
        $url_en = self::cleanCanonicalUrl($alternateUrls['en']);
        $url_ar = self::cleanCanonicalUrl($alternateUrls['ar']);
        $url_nl = self::cleanCanonicalUrl($alternateUrls['nl']);
        
        $canonical = $alternateUrls[$activeLang] ?? $url_en;
        if (!empty($customCanonical)) {
            $canonical = $customCanonical;
        } elseif (is_array($data) && !empty($data['canonical'])) {
            $canonical = $data['canonical'];
        }
        $canonical = self::cleanCanonicalUrl($canonical);

        // Render meta outputs
        echo '<!-- SEO Meta Tags -->' . "\n";
        echo '    <title>' . e($title) . '</title>' . "\n";
        echo '    <meta name="description" content="' . e($description) . '">' . "\n";
        
        // Keywords fallback for search engine compatibility
        $metaKeywords = t($settings, 'metaKeywords');
        if (!empty($metaKeywords)) {
            echo '    <meta name="keywords" content="' . e($metaKeywords) . '">' . "\n";
        }
        
        echo '    <meta name="robots" content="' . e($robots) . '">' . "\n";
        echo '    <link rel="canonical" href="' . e($canonical) . '">' . "\n";
        
        // Multilingual alternates
        if ($type !== 'admin' && $type !== '404') {
            echo '    <link rel="alternate" hreflang="en" href="' . e($url_en) . '">' . "\n";
            echo '    <link rel="alternate" hreflang="ar" href="' . e($url_ar) . '">' . "\n";
            echo '    <link rel="alternate" hreflang="nl" href="' . e($url_nl) . '">' . "\n";
            echo '    <link rel="alternate" hreflang="x-default" href="' . e($url_en) . '">' . "\n";
        }
        
        // Open Graph
        $ogTitleOutput = !empty($customOgTitle) ? $customOgTitle : $title;
        $ogDescOutput = !empty($customOgDesc) ? $customOgDesc : $description;
        $ogImageOutput = !empty($customOgImage) ? $customOgImage : $ogImage;
        $ogTypeOutput = ($type === 'post') ? 'article' : 'website';

        echo '    <!-- Open Graph / Facebook -->' . "\n";
        echo '    <meta property="og:type" content="' . e($ogTypeOutput) . '">' . "\n";
        echo '    <meta property="og:url" content="' . e(self::cleanCanonicalUrl(BASE_URL . $_SERVER['REQUEST_URI'])) . '">' . "\n";
        echo '    <meta property="og:title" content="' . e($ogTitleOutput) . '">' . "\n";
        echo '    <meta property="og:description" content="' . e($ogDescOutput) . '">' . "\n";
        if ($ogImageOutput) {
            echo '    <meta property="og:image" content="' . e($ogImageOutput) . '">' . "\n";
        }
        echo '    <meta property="og:site_name" content="' . e($siteName) . '">' . "\n";
        echo '    <meta property="og:locale" content="' . ($activeLang === 'ar' ? 'ar_EG' : 'en_US') . '">' . "\n";

        // Twitter
        $twTitleOutput = !empty($customTwitterTitle) ? $customTwitterTitle : $title;
        $twDescOutput = !empty($customTwitterDesc) ? $customTwitterDesc : $description;
        $twImageOutput = !empty($customTwitterImage) ? $customTwitterImage : $ogImage;

        echo '    <!-- Twitter -->' . "\n";
        echo '    <meta name="twitter:card" content="' . e($twitterCard) . '">' . "\n";
        echo '    <meta name="twitter:url" content="' . e(self::cleanCanonicalUrl(BASE_URL . $_SERVER['REQUEST_URI'])) . '">' . "\n";
        echo '    <meta name="twitter:title" content="' . e($twTitleOutput) . '">' . "\n";
        echo '    <meta name="twitter:description" content="' . e($twDescOutput) . '">' . "\n";
        if ($twImageOutput) {
            echo '    <meta name="twitter:image" content="' . e($twImageOutput) . '">' . "\n";
        }

        // Favicon
        $favicon = $settings['faviconUrl'] ?? '/favicon.ico';
        if (!empty($favicon) && !str_starts_with($favicon, 'http') && !str_starts_with($favicon, '//')) {
            $favicon = BASE_URL . $favicon;
        }
        echo '    <link rel="shortcut icon" href="' . e($favicon) . '" type="image/x-icon">' . "\n";

        // Dynamic JSON-LD Structured Data
        self::renderSchema($type, $data, $canonical, $activeLang, $siteName, $settings);
    }

    // Dynamic JSON-LD structured data generators
    private static function renderSchema($type, $data, $canonical, $activeLang, $siteName, $settings) {
        if ($type === 'admin') return;

        $schemas = [];

        // 1. Breadcrumbs List (for inner pages)
        if ($type !== 'home' && $type !== '404') {
            $items = [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => ($activeLang === 'ar' ? 'الرئيسية' : 'Home'),
                    'item' => BASE_URL . ($activeLang === 'ar' ? '/ar' : '/')
                ]
            ];

            if ($type === 'post' && $data) {
                if (isset($data['categoryId'])) {
                    // Blog Post
                    $items[] = [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => t($data, 'categoryName') ?: ($activeLang === 'ar' ? 'التصنيفات' : 'Categories'),
                        'item' => BASE_URL . ($activeLang === 'ar' ? '/ar/category/' : '/category/') . ($data['categorySlug'] ?? '')
                    ];
                    $items[] = [
                        '@type' => 'ListItem',
                        'position' => 3,
                        'name' => t($data, 'title'),
                        'item' => $canonical
                    ];
                } else {
                    // Women's story
                    $items[] = [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => ($activeLang === 'ar' ? 'قصص النساء' : "Women's Stories"),
                        'item' => BASE_URL . ($activeLang === 'ar' ? '/ar/women-stories' : '/women-stories')
                    ];
                    $items[] = [
                        '@type' => 'ListItem',
                        'position' => 3,
                        'name' => t($data, 'title'),
                        'item' => $canonical
                    ];
                }
            } elseif ($type === 'category' && $data) {
                $items[] = [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => t($data, 'name'),
                    'item' => $canonical
                ];
            } else {
                $pageTitles = [
                    'destinations' => ($activeLang === 'ar' ? 'الوجهات' : 'Destinations'),
                    'experiences' => ($activeLang === 'ar' ? 'تجارب السفر' : 'Experiences'),
                    'news' => ($activeLang === 'ar' ? 'الأخبار' : 'News'),
                    'accessibility' => ($activeLang === 'ar' ? 'إمكانية الوصول' : 'Accessibility'),
                    'contact' => ($activeLang === 'ar' ? 'اتصل بنا' : 'Contact'),
                    'podcasts' => ($activeLang === 'ar' ? 'البودكاست' : 'Podcasts'),
                    'about' => ($activeLang === 'ar' ? 'عن الموقع' : 'About Us')
                ];
                $name = $pageTitles[$type] ?? (is_array($data) && isset($data['title']) ? $data['title'] : ($data['title_en'] ?? 'Page'));
                $items[] = [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $name,
                    'item' => $canonical
                ];
            }

            $schemas[] = [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => $items
            ];
        }

        // 2. WebSite Schema (Homepage)
        if ($type === 'home') {
            $schemas[] = [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => $siteName,
                'url' => BASE_URL . ($activeLang === 'ar' ? '/ar' : '/'),
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => BASE_URL . ($activeLang === 'ar' ? '/ar/news' : '/news') . '?search={search_term_string}',
                    'query-input' => 'required name=search_term_string'
                ]
            ];
        }

        // 3. Organization & TravelAgency Schemas (Homepage or Contact Page)
        if ($type === 'home' || $type === 'contact') {
            $logoUrl = !empty($settings['logoUrl']) ? (str_starts_with($settings['logoUrl'], 'http') ? $settings['logoUrl'] : BASE_URL . $settings['logoUrl']) : BASE_URL . '/images/hero-bg.png';
            
            $schemas[] = [
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => $siteName,
                'url' => BASE_URL . ($activeLang === 'ar' ? '/ar' : '/'),
                'logo' => $logoUrl,
                'sameAs' => [
                    'https://www.facebook.com/youngover60',
                    'https://www.instagram.com/youngover60'
                ]
            ];

            $schemas[] = [
                '@context' => 'https://schema.org',
                '@type' => 'TravelAgency',
                'name' => $siteName,
                'image' => BASE_URL . '/images/hero-bg.png',
                'priceRange' => '$$',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => 'Cairo',
                    'addressCountry' => 'EG'
                ],
                'telephone' => '+201000000000'
            ];
        }

        // 4. Article / BlogPosting (for single post / women story view)
        if ($type === 'post' && $data) {
            $isBlog = isset($data['categoryId']);
            $schemaType = $isBlog ? 'BlogPosting' : 'Article';
            
            $cover = !empty($data['coverImage']) ? $data['coverImage'] : (!empty($data['cover_image']) ? $data['cover_image'] : '/images/hero-bg.png');
            if (!str_starts_with($cover, 'http')) {
                $cover = BASE_URL . $cover;
            }
            
            $logoUrl = !empty($settings['logoUrl']) ? (str_starts_with($settings['logoUrl'], 'http') ? $settings['logoUrl'] : BASE_URL . $settings['logoUrl']) : BASE_URL . '/images/hero-bg.png';

            $schemas[] = [
                '@context' => 'https://schema.org',
                '@type' => $schemaType,
                'headline' => t($data, 'title'),
                'description' => t($data, 'excerpt') ?: substr(strip_tags(t($data, 'content')), 0, 160),
                'image' => $cover,
                'datePublished' => $data['publishedAt'] ?? ($data['created_at'] ?? ''),
                'dateModified' => $data['updated_at'] ?? ($data['created_at'] ?? ''),
                'author' => [
                    '@type' => 'Person',
                    'name' => t($data, 'authorName') ?: ($data['authorName'] ?? ($data['author'] ?? 'Site Admin'))
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => $siteName,
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => $logoUrl
                    ]
                ],
                'mainEntityOfPage' => $canonical
            ];
        }

        // 5. FAQPage (for Custom page with FAQ template)
        if ($type === 'custom_page' && $data) {
            // Check if this custom page is FAQ page type or has Q&A content
            $content = $data['content'] ?? ($data['content_en'] ?? '');
            if (!empty($content)) {
                preg_match_all('/<h[34]>(.*?)<\/h[34]>\s*<p>(.*?)<\/p>/is', $content, $faqMatches, PREG_SET_ORDER);
                if (!empty($faqMatches)) {
                    $faqItems = [];
                    foreach ($faqMatches as $match) {
                        $q = trim(strip_tags($match[1]));
                        $a = trim(strip_tags($match[2]));
                        if (!empty($q) && !empty($a)) {
                            $faqItems[] = [
                                '@type' => 'Question',
                                'name' => $q,
                                'acceptedAnswer' => [
                                    '@type' => 'Answer',
                                    'text' => $a
                                ]
                            ];
                        }
                    }
                    if (!empty($faqItems)) {
                        $schemas[] = [
                            '@context' => 'https://schema.org',
                            '@type' => 'FAQPage',
                            'mainEntity' => $faqItems
                        ];
                    }
                }
            }
        }

        // 6. AudioObject (for podcasts page)
        if ($type === 'podcasts' && is_array($data)) {
            // Generate AudioObject schemas for dynamic podcast episodes
            foreach ($data as $podcast) {
                if (empty($podcast['audio_file'])) continue;
                $audio = $podcast['audio_file'];
                if (!str_starts_with($audio, 'http')) {
                    $audio = BASE_URL . $audio;
                }
                
                $cover = !empty($podcast['cover_image']) ? $podcast['cover_image'] : '/images/hero-bg.png';
                if (!str_starts_with($cover, 'http')) {
                    $cover = BASE_URL . $cover;
                }

                $schemas[] = [
                    '@context' => 'https://schema.org',
                    '@type' => 'AudioObject',
                    'name' => t($podcast, 'title'),
                    'description' => t($podcast, 'description'),
                    'contentUrl' => $audio,
                    'thumbnailUrl' => $cover,
                    'duration' => self::formatISO8601Duration($podcast['duration'] ?? '')
                ];
            }
        }

        // 7. TouristDestination (for destinations page)
        if ($type === 'destinations' && is_array($data)) {
            foreach ($data as $dest) {
                $image = !empty($dest['image']) ? $dest['image'] : '/images/hero-bg.png';
                if (!str_starts_with($image, 'http')) {
                    $image = BASE_URL . $image;
                }

                $schemas[] = [
                    '@context' => 'https://schema.org',
                    '@type' => 'TouristDestination',
                    'name' => t($dest, 'name'),
                    'description' => t($dest, 'description') ?: "Explore travel accessibility reviews and stories for " . t($dest, 'name'),
                    'image' => $image,
                    'url' => BASE_URL . ($activeLang === 'ar' ? '/ar/category/' : '/category/') . $dest['slug']
                ];
            }
        }

        // Print all collected schemas inside head
        if (!empty($schemas)) {
            echo "    <!-- Structured JSON-LD Data -->\n";
            foreach ($schemas as $schema) {
                echo "    <script type=\"application/ld+json\">\n";
                echo json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
                echo "    </script>\n";
            }
        }
    }
}
