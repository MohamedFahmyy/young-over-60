<?php
// sitemaps/stories.php
// Dynamic Women Stories Sitemap with Multilingual Alternates

header("Content-Type: application/xml; charset=utf-8");
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">

   <?php
   try {
       $db = Database::getInstance()->getConnection();
       $stmt = $db->prepare("SELECT slug_en, slug_ar, created_at FROM women_stories ORDER BY created_at DESC");
       $stmt->execute();
       $stories = $stmt->fetchAll();
       
       foreach ($stories as $story):
           $slug_en = !empty($story['slug_en']) ? $story['slug_en'] : $story['slug_ar'];
           $slug_ar = !empty($story['slug_ar']) ? $story['slug_ar'] : $story['slug_en'];
           
           $enUrl = BASE_URL . '/women-stories/' . $slug_en;
           $arUrl = BASE_URL . '/ar/women-stories/' . $slug_ar;
           $lastMod = date('Y-m-d', strtotime($story['created_at']));
   ?>
   <url>
      <loc><?php echo $enUrl; ?></loc>
      <xhtml:link rel="alternate" hreflang="en" href="<?php echo $enUrl; ?>"/>
      <xhtml:link rel="alternate" hreflang="ar" href="<?php echo $arUrl; ?>"/>
      <xhtml:link rel="alternate" hreflang="x-default" href="<?php echo $enUrl; ?>"/>
      <lastmod><?php echo $lastMod; ?></lastmod>
      <changefreq>weekly</changefreq>
      <priority>0.6</priority>
   </url>
   <url>
      <loc><?php echo $arUrl; ?></loc>
      <xhtml:link rel="alternate" hreflang="en" href="<?php echo $enUrl; ?>"/>
      <xhtml:link rel="alternate" hreflang="ar" href="<?php echo $arUrl; ?>"/>
      <xhtml:link rel="alternate" hreflang="x-default" href="<?php echo $enUrl; ?>"/>
      <lastmod><?php echo $lastMod; ?></lastmod>
      <changefreq>weekly</changefreq>
      <priority>0.6</priority>
   </url>
   <?php 
       endforeach;
   } catch (Exception $e) {
       error_log("Sitemap generation error (stories): " . $e->getMessage());
   }
   ?>
</urlset>
