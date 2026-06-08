<?php
// sitemaps/index.php
// Dynamic Sitemap Index

header("Content-Type: application/xml; charset=utf-8");
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
   <sitemap>
      <loc><?php echo BASE_URL; ?>/sitemap-pages.xml</loc>
   </sitemap>
   <sitemap>
      <loc><?php echo BASE_URL; ?>/sitemap-posts.xml</loc>
   </sitemap>
   <sitemap>
      <loc><?php echo BASE_URL; ?>/sitemap-podcasts.xml</loc>
   </sitemap>
   <sitemap>
      <loc><?php echo BASE_URL; ?>/sitemap-stories.xml</loc>
   </sitemap>
</sitemapindex>
