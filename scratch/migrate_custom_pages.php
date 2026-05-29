<?php
// scratch/migrate_custom_pages.php
// Enterprise Custom Pages CMS Database Migration Utility

if (!defined('PATH_ROOT')) {
    define('PATH_ROOT', dirname(__DIR__));
}

require_once PATH_ROOT . '/config/database.php';
require_once PATH_ROOT . '/classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "Starting Custom Pages CMS Database Migration...\n";

    // 1. Create custom_pages Table
    $db->exec("CREATE TABLE IF NOT EXISTS `custom_pages` (
      `id` VARCHAR(36) PRIMARY KEY,
      `slug_en` VARCHAR(255) NOT NULL UNIQUE,
      `slug_ar` VARCHAR(255) DEFAULT NULL UNIQUE,
      `title_en` VARCHAR(255) NOT NULL,
      `title_ar` VARCHAR(255) DEFAULT NULL,
      `content_en` LONGTEXT DEFAULT NULL,
      `content_ar` LONGTEXT DEFAULT NULL,
      `template_type` VARCHAR(50) NOT NULL DEFAULT 'default',
      `sort_order` INT NOT NULL DEFAULT 0,
      `show_in_menu` TINYINT(1) NOT NULL DEFAULT 0,
      `menu_title_en` VARCHAR(255) DEFAULT NULL,
      `menu_title_ar` VARCHAR(255) DEFAULT NULL,
      `hero_title_en` VARCHAR(255) DEFAULT NULL,
      `hero_title_ar` VARCHAR(255) DEFAULT NULL,
      `hero_subtitle_en` VARCHAR(255) DEFAULT NULL,
      `hero_subtitle_ar` VARCHAR(255) DEFAULT NULL,
      `hero_image` VARCHAR(255) DEFAULT NULL,
      `hero_cta_text_en` VARCHAR(255) DEFAULT NULL,
      `hero_cta_text_ar` VARCHAR(255) DEFAULT NULL,
      `hero_cta_link` VARCHAR(255) DEFAULT NULL,
      `meta_title_en` VARCHAR(255) DEFAULT NULL,
      `meta_title_ar` VARCHAR(255) DEFAULT NULL,
      `meta_description_en` TEXT DEFAULT NULL,
      `meta_description_ar` TEXT DEFAULT NULL,
      `featured_image` VARCHAR(255) DEFAULT NULL,
      `og_title_en` VARCHAR(255) DEFAULT NULL,
      `og_title_ar` VARCHAR(255) DEFAULT NULL,
      `og_description_en` TEXT DEFAULT NULL,
      `og_description_ar` TEXT DEFAULT NULL,
      `og_image` VARCHAR(255) DEFAULT NULL,
      `twitter_title_en` VARCHAR(255) DEFAULT NULL,
      `twitter_title_ar` VARCHAR(255) DEFAULT NULL,
      `twitter_description_en` TEXT DEFAULT NULL,
      `twitter_description_ar` TEXT DEFAULT NULL,
      `is_published` TINYINT(1) NOT NULL DEFAULT 0,
      `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      INDEX `idx_custom_pages_slug_en` (`slug_en`),
      INDEX `idx_custom_pages_slug_ar` (`slug_ar`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "✓ Table 'custom_pages' created/verified successfully.\n";

    // 2. Create page_revisions Table
    $db->exec("CREATE TABLE IF NOT EXISTS `page_revisions` (
      `id` VARCHAR(36) PRIMARY KEY,
      `page_id` VARCHAR(36) NOT NULL,
      `title_en` VARCHAR(255) NOT NULL,
      `title_ar` VARCHAR(255) DEFAULT NULL,
      `content_en` LONGTEXT DEFAULT NULL,
      `content_ar` LONGTEXT DEFAULT NULL,
      `template_type` VARCHAR(50) NOT NULL,
      `sort_order` INT NOT NULL,
      `show_in_menu` TINYINT(1) NOT NULL,
      `menu_title_en` VARCHAR(255) DEFAULT NULL,
      `menu_title_ar` VARCHAR(255) DEFAULT NULL,
      `hero_title_en` VARCHAR(255) DEFAULT NULL,
      `hero_title_ar` VARCHAR(255) DEFAULT NULL,
      `hero_subtitle_en` VARCHAR(255) DEFAULT NULL,
      `hero_subtitle_ar` VARCHAR(255) DEFAULT NULL,
      `hero_image` VARCHAR(255) DEFAULT NULL,
      `hero_cta_text_en` VARCHAR(255) DEFAULT NULL,
      `hero_cta_text_ar` VARCHAR(255) DEFAULT NULL,
      `hero_cta_link` VARCHAR(255) DEFAULT NULL,
      `meta_title_en` VARCHAR(255) DEFAULT NULL,
      `meta_title_ar` VARCHAR(255) DEFAULT NULL,
      `meta_description_en` TEXT DEFAULT NULL,
      `meta_description_ar` TEXT DEFAULT NULL,
      `featured_image` VARCHAR(255) DEFAULT NULL,
      `og_title_en` VARCHAR(255) DEFAULT NULL,
      `og_title_ar` VARCHAR(255) DEFAULT NULL,
      `og_description_en` TEXT DEFAULT NULL,
      `og_description_ar` TEXT DEFAULT NULL,
      `og_image` VARCHAR(255) DEFAULT NULL,
      `twitter_title_en` VARCHAR(255) DEFAULT NULL,
      `twitter_title_ar` VARCHAR(255) DEFAULT NULL,
      `twitter_description_en` TEXT DEFAULT NULL,
      `twitter_description_ar` TEXT DEFAULT NULL,
      `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (`page_id`) REFERENCES `custom_pages` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "✓ Table 'page_revisions' created/verified successfully.\n";

    // 3. Create page_redirects Table
    $db->exec("CREATE TABLE IF NOT EXISTS `page_redirects` (
      `id` VARCHAR(36) PRIMARY KEY,
      `old_slug` VARCHAR(255) NOT NULL UNIQUE,
      `new_slug` VARCHAR(255) NOT NULL,
      `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "✓ Table 'page_redirects' created/verified successfully.\n";

    // 4. Seed About Us Page
    $aboutId = 'page-about';
    $aboutExists = $db->query("SELECT id FROM custom_pages WHERE id = '$aboutId'")->fetch();
    
    if (!$aboutExists) {
        $contentAr = '<h3>من نحن</h3>
<p>في <strong>Young Over 60</strong>، نؤمن بأن السياحة ليست ترفًا ولا رفاهية مخصصة لفئة عمرية معينة، بل هي حق أساسي للجميع، بغض النظر عن العمر أو القدرات الجسدية. هي نافذة على جمال العالم، ووسيلة للتواصل مع الثقافات، وداعم أساسي للصحة النفسية والجسدية.</p>
<p>نحن لسنا مجرد موقع سياحي إخباري؛ نحن منصة نابضة بالحياة، صممت خصيصًا لمن يرى أن العمر مجرد رقم لا يحدد الروح ولا الطموح. نؤمن بأن الحياة الحقيقية تبدأ بعد الستين، حيث تتسع الخبرة، ويزداد الشغف، وتنضج الرغبة في الاستكشاف دون قيود.</p>
<blockquote>شعارنا: Beyond 60, life Begins Again</blockquote>
<h3>رؤيتنا: سياحة بلا حدود</h3>
<p>نسعى لجعل تجربة السفر متاحة وميسرة وممتعة لكل من تجاوز الستين، ولكل فرد من ذوي الاحتياجات الخاصة. نعمل على كسر الصور النمطية التي تروّج أن السفر صعب أو غير مناسب بعد عمر معين، أو لأصحاب الهمم. بالعكس، نحن نرى فيكم طاقة الشباب المستمر، ونريد أن نكون دليلكم الموثوق لاستكشاف العالم بأمان ورقي.</p>
<h3>ماذا نقدم؟</h3>
<ul>
  <li><strong>أخبار وتحليلات متخصصة:</strong> أحدث الاتجاهات في سياحة كبار السن وذوي الاحتياجات الخاصة، من سياسات الطيران المريحة إلى الفنادق المجهزة بأعلى المعايير.</li>
  <li><strong>تقارير سياحية معمقة:</strong> وجهات مصنفة وفق معايير إمكانية الوصول (Accessibility)، وسهولة الحركة، وتوافر الخدمات الطبية والترفيهية المناسبة.</li>
  <li><strong>قِصص وإلهام:</strong> تجارب حقيقية لأشخاص تجاوزوا الستين أو من ذوي الاحتياجات الخاصة، وأثبتوا أن المغامرة لا تعرف عمرًا ولا عائقًا.</li>
  <li><strong>أدلة ونصائح عملية:</strong> من كيفية التخطيط للرحلة، والتعامل مع التأمين الصحي، إلى أفضل وسائل النقل والإقامة المصممة لراحتكم.</li>
</ul>
<h3>فلسفتنا: الحياة تبدأ بعد الستين</h3>
<p>نحن نرفض أن يُختزل الإنسان في رقم في جواز سفره. الستون ليست نهاية الطريق، بل هي محطة انطلاق جديدة نحو حياة مليئة بالاكتشافات. فالشباب الحقيقي هو نضارة الروح وفضول العقل، وهذان لا يشيخان أبدًا.</p>
<p>في Young Over 60، نرى في كل مسافر قصة تستحق أن تُروى، وفي كل رحلة فرصة لتجديد الحياة. انضم إلينا، لأن العالم أكبر من أن يُرى من النافذة فقط، ولأن حقك في السياحة لا يسقط بالتقادم.</p>';

        $contentEn = '<h3>Who We Are</h3>
<p>At <strong>Young Over 60</strong>, we believe that travel is not a luxury or a privilege reserved for a specific age group. Rather, it is a fundamental right for everyone, regardless of age or physical capabilities. It is a window to the beauty of the world, a gateway to connect with diverse cultures, and a vital source of physical and psychological well-being.</p>
<p>We are not just a travel news website; we are a vibrant, active platform specifically designed for those who believe that age is merely a number that should never define one\'s spirit or ambition. We firmly believe that real life begins after sixty—a time when experience expands, passion grows, and the desire to explore matures without limits.</p>
<blockquote>Our Slogan: Beyond 60, life Begins Again</blockquote>
<h3>Our Vision: Travel Without Limits</h3>
<p>We strive to make travel accessible, smooth, and enjoyable for everyone over sixty and for individuals with physical or sensory requirements. We work to break down the social stereotypes that label travel as difficult or unsuitable after a certain age or for individuals with determination. On the contrary, we see in you the energy of everlasting youth, and we want to be your trusted guide to exploring the world with safety, comfort, and luxury.</p>
<h3>What We Offer</h3>
<ul>
  <li><strong>Specialized News & Analysis:</strong> The latest trends in accessible senior travel—from comfortable airline policies to hotels equipped with the highest standards of accessibility.</li>
  <li><strong>In-Depth Travel Reports:</strong> Destinations ranked according to strict accessibility criteria, ease of movement, and availability of appropriate medical and recreational services.</li>
  <li><strong>Stories & Inspiration:</strong> Real-life experiences of individuals who traveled beyond sixty or overcame physical challenges, proving that adventure knows no age and no barriers.</li>
  <li><strong>Practical Guides & Tips:</strong> From planning your trip and dealing with international health insurance, to the best transportation options and accommodations tailored for your comfort.</li>
</ul>
<h3>Our Philosophy: Life Begins After Sixty</h3>
<p>We refuse to let a person be reduced to a number on a passport. Sixty is not the end of the road; it is a new departure point toward a life filled with discovery. Real youth is the vibrancy of the soul and the curiosity of the mind, and these never grow old.</p>
<p>At Young Over 60, we see in every traveler a story worth telling, and in every journey, an opportunity to renew life. Join us—because the world is too beautiful to be viewed only from a window, and because your right to travel never expires.</p>';

        $stmt = $db->prepare("INSERT INTO custom_pages (
            id, slug_en, slug_ar, title_en, title_ar, content_en, content_ar, 
            template_type, sort_order, show_in_menu, menu_title_en, menu_title_ar,
            hero_title_en, hero_title_ar, hero_subtitle_en, hero_subtitle_ar, hero_image,
            meta_title_en, meta_title_ar, meta_description_en, meta_description_ar, is_published
        ) VALUES (
            :id, :slug_en, :slug_ar, :title_en, :title_ar, :content_en, :content_ar, 
            :template_type, :sort_order, :show_in_menu, :menu_title_en, :menu_title_ar,
            :hero_title_en, :hero_title_ar, :hero_subtitle_en, :hero_subtitle_ar, :hero_image,
            :meta_title_en, :meta_title_ar, :meta_description_en, :meta_description_ar, :is_published
        )");

        $stmt->execute([
            'id' => $aboutId,
            'slug_en' => 'about-us',
            'slug_ar' => 'من-نحن',
            'title_en' => 'About Us',
            'title_ar' => 'من نحن',
            'content_en' => $contentEn,
            'content_ar' => $contentAr,
            'template_type' => 'about-us',
            'sort_order' => 1,
            'show_in_menu' => 1,
            'menu_title_en' => 'About Us',
            'menu_title_ar' => 'من نحن',
            'hero_title_en' => 'About Us',
            'hero_title_ar' => 'من نحن',
            'hero_subtitle_en' => 'Beyond 60, life Begins Again',
            'hero_subtitle_ar' => 'بعد الستين، تبدأ الحياة من جديد',
            'hero_image' => '/images/australia.png',
            'meta_title_en' => 'About Our Mission and Philosophy',
            'meta_title_ar' => 'من نحن ورسالتنا وسياستنا',
            'meta_description_en' => 'Learn more about Young Over 60, our mission, vision, everlasting youth philosophy, and committed travel guidelines.',
            'meta_description_ar' => 'تعرف على رسالتنا ورؤيتنا وفلسفتنا في شباب فوق الستين نحو سياحة بلا حدود وخدمة أصحاب الهمم وكبار السن.',
            'is_published' => 1
        ]);
        echo "✓ Seeded dynamic 'About Us' page record successfully.\n";
    }

    // 5. Seed Medical Tourism Page
    $medId = 'page-medical';
    $medExists = $db->query("SELECT id FROM custom_pages WHERE id = '$medId'")->fetch();
    
    if (!$medExists) {
        $contentAr = '<h3>السياحة العلاجية لكبار السن: استشفاء وراحة بلا حدود</h3>
<p>تعد السياحة العلاجية والاستشفائية أحد الروافد الهامة للمسافرين النشطين فوق الستين ومن يطمحون إلى الاستشفاء الطبيعي وتجديد الحيوية والنشاط. نؤمن في <strong>Young Over 60</strong> بأن الرعاية الصحية والوقاية الطبيعية يجب أن تقترن بتجارب سفر فاخرة ومريحة وآمنة بالكامل.</p>
<p>من عيون المياه الكبريتية والمنتجعات الطين الطبيعية في مصر والشرق الأوسط، إلى المصحات الطبية الرائدة في أوروبا، نقدم لكم دليلاً شاملاً مصمماً خصيصاً ليناسب متطلباتكم الحركية والصحية.</p>
<h3>أهم ركائز السياحة العلاجية لدينا:</h3>
<ul>
  <li><strong>تيسير الوصول واللوجستيات الطبية:</strong> نركز على الوجهات التي توفر بنية تحتية ميسرة بالكامل، وتسهيلات خاصة لمستخدمي الأجهزة المساعدة، مع قرب المراكز الطبية المتخصصة.</li>
  <li><strong>المنتجعات الصحية والاستشفائية:</strong> دمج العلاجات الطبيعية مثل العلاج بالمياه المعدنية، والتدليك العلاجي، واليوجا المخصصة لكبار السن في بيئات طبيعية خلابة وهادئة.</li>
  <li><strong>استشارات التأمين والرعاية:</strong> نصائح وحيل حول تأمين السفر الطبي الدولي والتنسيق مع الأطباء المعالجين لضمان رحلة خالية من القلق.</li>
</ul>';

        $contentEn = '<h3>Medical Tourism for Seniors: Wellness & Healing Without Limits</h3>
<p>Medical and wellness tourism represents a vital avenue for active travelers over sixty looking for natural healing, rehabilitation, and dynamic rejuvenation. At <strong>Young Over 60</strong>, we believe that healthcare and preventive treatments should always be paired with luxurious, stress-free, and fully accessible travel experiences.</p>
<p>From the mineral-rich sulfur springs and natural clay baths of Egypt and the Middle East, to the pioneering medical clinics of Europe, we provide a comprehensive directory specifically designed around your mobility and health requirements.</p>
<h3>Our Core Medical Tourism Pillars:</h3>
<ul>
  <li><strong>Seamless Accessibility & Medical Logistics:</strong> We highlight destinations that offer fully accessible transit, support for mobility aids, and immediate proximity to top-tier specialized medical clinics.</li>
  <li><strong>Therapeutic & Holistic Retreats:</strong> Integrating natural treatments like hydrotherapy, mineral baths, medical massages, and senior-focused yoga programs in scenic, tranquil environments.</li>
  <li><strong>Insurance & Care Coordination:</strong> Crucial guidance on selecting international travel medical insurance and coordinating with personal physicians to ensure a completely worry-free journey.</li>
</ul>';

        $stmt = $db->prepare("INSERT INTO custom_pages (
            id, slug_en, slug_ar, title_en, title_ar, content_en, content_ar, 
            template_type, sort_order, show_in_menu, menu_title_en, menu_title_ar,
            hero_title_en, hero_title_ar, hero_subtitle_en, hero_subtitle_ar, hero_image,
            meta_title_en, meta_title_ar, meta_description_en, meta_description_ar, is_published
        ) VALUES (
            :id, :slug_en, :slug_ar, :title_en, :title_ar, :content_en, :content_ar, 
            :template_type, :sort_order, :show_in_menu, :menu_title_en, :menu_title_ar,
            :hero_title_en, :hero_title_ar, :hero_subtitle_en, :hero_subtitle_ar, :hero_image,
            :meta_title_en, :meta_title_ar, :meta_description_en, :meta_description_ar, :is_published
        )");

        $stmt->execute([
            'id' => $medId,
            'slug_en' => 'medical-tourism',
            'slug_ar' => 'السياحة-العلاجية',
            'title_en' => 'Medical Tourism',
            'title_ar' => 'السياحة العلاجية',
            'content_en' => $contentEn,
            'content_ar' => $contentAr,
            'template_type' => 'medical',
            'sort_order' => 2,
            'show_in_menu' => 1,
            'menu_title_en' => 'Medical Tourism',
            'menu_title_ar' => 'السياحة العلاجية',
            'hero_title_en' => 'Medical Tourism & Wellness',
            'hero_title_ar' => 'السياحة العلاجية والاستشفاء',
            'hero_subtitle_en' => 'Healing, Rejuvenation and Accessibility Curated for Seniors',
            'hero_subtitle_ar' => 'علاجات طبيعية، استشفاء وسهولة وصول كاملة منسقة لكبار السن',
            'hero_image' => '/images/europe.png',
            'meta_title_en' => 'Accessible Medical Tourism and Wellness Springs',
            'meta_title_ar' => 'السياحة العلاجية والاستشفائية الميسرة',
            'meta_description_en' => 'Discover the best natural spas, therapeutic mineral springs, and specialized accessible medical travel guides globally.',
            'meta_description_ar' => 'اكتشف أفضل منتجعات المياه الكبريتية والمصحات الطبيعية وأدلة السفر الطبي الميسرة حول العالم.',
            'is_published' => 1
        ]);
        echo "✓ Seeded dynamic 'Medical Tourism' page record successfully.\n";
    }

    echo "CMS Migration completed successfully!\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
