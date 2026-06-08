<?php
// sitemaps/posts.php
// Dynamic Blog Posts Sitemap with Multilingual Alternates

header("Content-Type: application/xml; charset=utf-8");
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">

   <?php
   try {
       $db = Database::getInstance()->getConnection();
       $stmt = $db->prepare("SELECT slug_en, slug_ar, updated_at FROM posts WHERE status = 'PUBLISHED' AND deleted_at IS NULL ORDER BY publishedAt DESC");
       $stmt->execute();
       $posts = $stmt->fetchAll();
       
       foreach ($posts as $post):
           $slug_en = !empty($post['slug_en']) ? $post['slug_en'] : $post['slug_ar'];
           $slug_ar = !empty($post['slug_ar']) ? $post['slug_ar'] : $post['slug_en'];
           
           $enUrl = BASE_URL . '/posts/' . $slug_en;
           $arUrl = BASE_URL . '/ar/posts/' . $slug_ar;
           $lastMod = date('Y-m-d', strtotime($post['updated_at']));
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
       error_log("Sitemap generation error (posts): " . $e->getMessage());
   }
   ?>
</urlset>
