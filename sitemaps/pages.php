<?php
// sitemaps/pages.php
// Dynamic Pages Sitemap with Multilingual Alternates

header("Content-Type: application/xml; charset=utf-8");
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">

   <!-- Home Page -->
   <url>
      <loc><?php echo BASE_URL; ?>/</loc>
      <xhtml:link rel="alternate" hreflang="en" href="<?php echo BASE_URL; ?>/"/>
      <xhtml:link rel="alternate" hreflang="ar" href="<?php echo BASE_URL; ?>/ar"/>
      <xhtml:link rel="alternate" hreflang="x-default" href="<?php echo BASE_URL; ?>/"/>
      <lastmod><?php echo date('Y-m-d'); ?></lastmod>
      <changefreq>daily</changefreq>
      <priority>1.0</priority>
   </url>
   <url>
      <loc><?php echo BASE_URL; ?>/ar</loc>
      <xhtml:link rel="alternate" hreflang="en" href="<?php echo BASE_URL; ?>/"/>
      <xhtml:link rel="alternate" hreflang="ar" href="<?php echo BASE_URL; ?>/ar"/>
      <xhtml:link rel="alternate" hreflang="x-default" href="<?php echo BASE_URL; ?>/"/>
      <lastmod><?php echo date('Y-m-d'); ?></lastmod>
      <changefreq>daily</changefreq>
      <priority>1.0</priority>
   </url>

   <!-- Static Routes -->
   <?php
   $staticRoutes = ['destinations', 'experiences', 'news', 'accessibility', 'contact', 'podcasts', 'women-stories'];
   foreach ($staticRoutes as $route):
       $enUrl = BASE_URL . '/' . $route;
       $arUrl = BASE_URL . '/ar/' . $route;
   ?>
   <url>
      <loc><?php echo $enUrl; ?></loc>
      <xhtml:link rel="alternate" hreflang="en" href="<?php echo $enUrl; ?>"/>
      <xhtml:link rel="alternate" hreflang="ar" href="<?php echo $arUrl; ?>"/>
      <xhtml:link rel="alternate" hreflang="x-default" href="<?php echo $enUrl; ?>"/>
      <lastmod><?php echo date('Y-m-d'); ?></lastmod>
      <changefreq>weekly</changefreq>
      <priority>0.8</priority>
   </url>
   <url>
      <loc><?php echo $arUrl; ?></loc>
      <xhtml:link rel="alternate" hreflang="en" href="<?php echo $enUrl; ?>"/>
      <xhtml:link rel="alternate" hreflang="ar" href="<?php echo $arUrl; ?>"/>
      <xhtml:link rel="alternate" hreflang="x-default" href="<?php echo $enUrl; ?>"/>
      <lastmod><?php echo date('Y-m-d'); ?></lastmod>
      <changefreq>weekly</changefreq>
      <priority>0.8</priority>
   </url>
   <?php endforeach; ?>

   <!-- Custom CMS Pages -->
   <?php
   try {
       $db = Database::getInstance()->getConnection();
       $stmt = $db->prepare("SELECT slug_en, slug_ar, updated_at FROM custom_pages WHERE is_published = 1 ORDER BY created_at DESC");
       $stmt->execute();
       $customPages = $stmt->fetchAll();
       
       foreach ($customPages as $cp):
           $slug_en = !empty($cp['slug_en']) ? $cp['slug_en'] : $cp['slug_ar'];
           $slug_ar = !empty($cp['slug_ar']) ? $cp['slug_ar'] : $cp['slug_en'];
           
           $enUrl = BASE_URL . '/pages/' . $slug_en;
           $arUrl = BASE_URL . '/ar/pages/' . $slug_ar;
           $lastMod = date('Y-m-d', strtotime($cp['updated_at']));
   ?>
   <url>
      <loc><?php echo $enUrl; ?></loc>
      <xhtml:link rel="alternate" hreflang="en" href="<?php echo $enUrl; ?>"/>
      <xhtml:link rel="alternate" hreflang="ar" href="<?php echo $arUrl; ?>"/>
      <xhtml:link rel="alternate" hreflang="x-default" href="<?php echo $enUrl; ?>"/>
      <lastmod><?php echo $lastMod; ?></lastmod>
      <changefreq>monthly</changefreq>
      <priority>0.7</priority>
   </url>
   <url>
      <loc><?php echo $arUrl; ?></loc>
      <xhtml:link rel="alternate" hreflang="en" href="<?php echo $enUrl; ?>"/>
      <xhtml:link rel="alternate" hreflang="ar" href="<?php echo $arUrl; ?>"/>
      <xhtml:link rel="alternate" hreflang="x-default" href="<?php echo $enUrl; ?>"/>
      <lastmod><?php echo $lastMod; ?></lastmod>
      <changefreq>monthly</changefreq>
      <priority>0.7</priority>
   </url>
   <?php 
       endforeach;
   } catch (Exception $e) {
       error_log("Sitemap generation error (custom pages): " . $e->getMessage());
   }
   ?>
</urlset>
