<?php
// sitemaps/podcasts.php
// Dynamic Podcasts Sitemap with Multilingual Alternates

header("Content-Type: application/xml; charset=utf-8");
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">

   <!-- Main Podcasts Listing Page -->
   <url>
      <loc><?php echo BASE_URL; ?>/podcasts</loc>
      <xhtml:link rel="alternate" hreflang="en" href="<?php echo BASE_URL; ?>/podcasts"/>
      <xhtml:link rel="alternate" hreflang="ar" href="<?php echo BASE_URL; ?>/ar/podcasts"/>
      <xhtml:link rel="alternate" hreflang="x-default" href="<?php echo BASE_URL; ?>/podcasts"/>
      <lastmod><?php echo date('Y-m-d'); ?></lastmod>
      <changefreq>weekly</changefreq>
      <priority>0.8</priority>
   </url>
   <url>
      <loc><?php echo BASE_URL; ?>/ar/podcasts</loc>
      <xhtml:link rel="alternate" hreflang="en" href="<?php echo BASE_URL; ?>/podcasts"/>
      <xhtml:link rel="alternate" hreflang="ar" href="<?php echo BASE_URL; ?>/ar/podcasts"/>
      <xhtml:link rel="alternate" hreflang="x-default" href="<?php echo BASE_URL; ?>/podcasts"/>
      <lastmod><?php echo date('Y-m-d'); ?></lastmod>
      <changefreq>weekly</changefreq>
      <priority>0.8</priority>
   </url>

   <?php
   try {
       $db = Database::getInstance()->getConnection();
       $stmt = $db->prepare("SELECT slug_en, slug_ar, created_at FROM podcasts ORDER BY created_at DESC");
       $stmt->execute();
       $podcasts = $stmt->fetchAll();
       
       foreach ($podcasts as $pod):
           $slug_en = !empty($pod['slug_en']) ? $pod['slug_en'] : $pod['slug_ar'];
           $slug_ar = !empty($pod['slug_ar']) ? $pod['slug_ar'] : $pod['slug_en'];
           
           $enUrl = BASE_URL . '/podcasts/' . $slug_en;
           $arUrl = BASE_URL . '/ar/podcasts/' . $slug_ar;
           $lastMod = date('Y-m-d', strtotime($pod['created_at']));
   ?>
   <url>
      <loc><?php echo $enUrl; ?></loc>
      <xhtml:link rel="alternate" hreflang="en" href="<?php echo $enUrl; ?>"/>
      <xhtml:link rel="alternate" hreflang="ar" href="<?php echo $arUrl; ?>"/>
      <xhtml:link rel="alternate" hreflang="x-default" href="<?php echo $enUrl; ?>"/>
      <lastmod><?php echo $lastMod; ?></lastmod>
      <changefreq>monthly</changefreq>
      <priority>0.5</priority>
   </url>
   <url>
      <loc><?php echo $arUrl; ?></loc>
      <xhtml:link rel="alternate" hreflang="en" href="<?php echo $enUrl; ?>"/>
      <xhtml:link rel="alternate" hreflang="ar" href="<?php echo $arUrl; ?>"/>
      <xhtml:link rel="alternate" hreflang="x-default" href="<?php echo $enUrl; ?>"/>
      <lastmod><?php echo $lastMod; ?></lastmod>
      <changefreq>monthly</changefreq>
      <priority>0.5</priority>
   </url>
   <?php 
       endforeach;
   } catch (Exception $e) {
       error_log("Sitemap generation error (podcasts): " . $e->getMessage());
   }
   ?>
</urlset>
