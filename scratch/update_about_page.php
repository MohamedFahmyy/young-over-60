<?php
if (php_sapi_name() === 'cli' && !defined('DB_HOST')) {
    define('DB_HOST', '127.0.0.1');
}

define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/config/database.php';
require_once PATH_ROOT . '/classes/Database.php';

$cssStyles = '<style>
    .custom-page-about {
      width: 100% !important;
      max-width: 100% !important;
      margin: 0 !important;
      padding: 0 !important;
    }

    .custom-page-main {
      padding: 0 !important;
    }

    .about-container {
      background: #f0f4f8;
      width: 100%;
      padding: 2rem 1rem;
    }

    @media (min-width: 768px) {
      .about-container {
        padding: 3rem 2rem;
      }
    }

    .about-container .about-inner-wrap {
      max-width: 1280px;
      margin: 0 auto;
      background: #ffffff;
      border-radius: 2rem;
      box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.02);
      overflow: hidden;
      transition: all 0.2s ease;
      font-family: var(--body-font), system-ui, -apple-system, sans-serif;
      line-height: 1.5;
      color: #1a2c3e;
      text-align: left;
    }

    /* brand bar — now blue */
    .about-container .brand-bar {
      background: #1e5a7d;  /* deep blue, calm and trustworthy */
      height: 6px;
      width: 100%;
    }

    .about-container .about-inner {
      padding: 3rem 2rem;
    }

    @media (min-width: 768px) {
      .about-container .about-inner {
        padding: 4rem 3rem;
      }
    }

    /* slogan highlight — blue accent */
    .about-container .slogan {
      text-align: center;
      margin-bottom: 2.5rem;
      font-size: 1.6rem;
      font-weight: 500;
      letter-spacing: -0.01em;
      color: #1e5a7d;
      border-bottom: 1px solid #e2e8f0;
      padding-bottom: 1.5rem;
    }

    .about-container .slogan span {
      background: #eef4fc;
      display: inline-block;
      padding: 0.3rem 1.2rem;
      border-radius: 60px;
      font-style: italic;
      font-weight: 500;
      font-size: 1.5rem;
      color: #0f4a6e;
    }

    .about-container .about-logo {
      display: block;
      margin: 0 auto 1.5rem auto;
      max-height: 80px;
      width: auto;
      object-fit: contain;
    }

    /* headings in blue tones */
    .about-container h1 {
      font-family: var(--heading-font), system-ui, -apple-system, sans-serif;
      font-size: 2.2rem;
      font-weight: 700;
      letter-spacing: -0.02em;
      color: #144a6b;
      margin-top: 0;
      margin-bottom: 1rem;
      line-height: 1.2;
    }

    .about-container h2 {
      font-family: var(--heading-font), system-ui, -apple-system, sans-serif;
      font-size: 1.6rem;
      font-weight: 600;
      margin-top: 2rem;
      margin-bottom: 0.75rem;
      color: #1e5a7d;
      border-left: 4px solid #6c9ebf;
      padding-left: 1rem;
    }

    .about-container h3 {
      font-family: var(--heading-font), system-ui, -apple-system, sans-serif;
      font-size: 1.3rem;
      font-weight: 600;
      margin: 1.5rem 0 0.5rem 0;
      color: #2c6e9e;
    }

    .about-container .intro-text {
      font-size: 1.1rem;
      color: #1e3a5f;
      margin-bottom: 1.5rem;
    }

    .about-container p {
      margin-bottom: 1.2rem;
      color: #1e3a5f;
      font-size: 1rem;
    }

    .about-container .highlight-quote {
      background: #f0f6fe;
      padding: 1.2rem 1.8rem;
      border-radius: 2rem;
      font-style: normal;
      margin: 1.8rem 0;
      border-left: 4px solid #6c9ebf;
      font-weight: 450;
    }

    /* offer grid */
    .about-container .offer-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 1.5rem;
      margin: 2rem 0 1rem 0;
    }

    @media (min-width: 640px) {
      .about-container .offer-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    .about-container .offer-card {
      background: #f9fcff;
      padding: 1.5rem;
      border-radius: 1.5rem;
      transition: transform 0.2s, box-shadow 0.2s;
      border: 1px solid #dee9f2;
    }

    .about-container .offer-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 20px -12px rgba(0, 0, 0, 0.1);
      border-color: #9bbdd4;
    }

    .about-container .offer-card strong {
      font-size: 1.2rem;
      display: block;
      margin-bottom: 0.5rem;
      color: #1e5a7d;
      font-weight: 700;
    }

    .about-container .offer-card p {
      margin-bottom: 0;
      color: #2c4b6e;
    }

    .about-container .philosophy-block {
      background: #eaf3fa;
      padding: 2rem 1.8rem;
      border-radius: 2rem;
      margin: 2rem 0 1.8rem 0;
      border: 1px solid #d4e2f0;
    }

    .about-container .join-cta {
      text-align: center;
      margin-top: 2.5rem;
      background: #ffffff;
      border-top: 2px solid #e2edf7;
      padding-top: 2rem;
    }

    .about-container .join-cta p {
      font-size: 1.2rem;
      font-weight: 500;
      margin-bottom: 0;
      color: #144a6b;
    }

    .about-container .signature {
      margin-top: 2rem;
      text-align: center;
      font-size: 0.85rem;
      color: #5b7f9e;
      border-top: 1px solid #e2edf7;
      padding-top: 1.5rem;
    }

    .about-container hr {
      margin: 1.5rem 0;
      border: none;
      border-top: 1px solid #e2e8f0;
    }

    @media (max-width: 480px) {
      .about-container .about-inner {
        padding: 2rem 1.25rem;
      }
      .about-container h1 {
        font-size: 1.8rem;
      }
      .about-container .slogan span {
        font-size: 1.2rem;
      }
    }

    /* RTL specific overrides */
    html[dir="rtl"] .about-container {
      text-align: right;
    }
    html[dir="rtl"] .about-container h2 {
      border-left: none;
      padding-left: 0;
      border-right: 4px solid #6c9ebf;
      padding-right: 1rem;
    }
    html[dir="rtl"] .about-container .philosophy-block h2 {
      border-left: none !important;
      padding-left: 0 !important;
      border-right: 4px solid #6c9ebf !important;
      padding-right: 1rem !important;
    }
</style>';

$contentEn = $cssStyles . "\n" . '<div class="about-container">
  <div class="about-inner-wrap">
  <div class="brand-bar"></div>
  <div class="about-inner">
    <!-- slogan -->
    <div class="slogan">
      {logo}
      <span>✨ Beyond 60, life begins again. ✨</span>
    </div>

    <!-- main title -->
    <h1>Young over 60</h1>

    <p class="intro-text">
      We believe that travel is neither a luxury nor a privilege reserved for a specific age group. 
      It is a fundamental right for everyone, regardless of age or physical ability. 
      Travel is a window to the world’s beauty, a bridge to other cultures, and a vital support for mental and physical well-being.
    </p>

    <p>
      We are not just another travel news website. We are a vibrant platform, designed especially for those who see age as just a number—one that does not define spirit or ambition. 
      We truly believe that life begins after 60, when experience deepens, passion grows, and the desire to explore matures without limits.
    </p>

    <!-- Vision section -->
    <h2>Our vision: Travel without boundaries</h2>
    <p>
      We strive to make travel accessible, comfortable, and enjoyable for everyone over 60 and for individuals with special needs. 
      We work to break the stereotypes that traveling is difficult or unsuitable after a certain age—or for people with disabilities. 
      On the contrary, we see in you an enduring youthful energy, and we want to be your trusted guide to exploring the world safely and with dignity.
    </p>

    <!-- What we offer grid -->
    <h2>What we offer</h2>
    <div class="offer-grid">
      <div class="offer-card">
        <strong>📰 Specialized news & insights</strong>
        <p>The latest trends in senior and accessible travel—from airline policies to hotels equipped with the highest standards.</p>
      </div>
      <div class="offer-card">
        <strong>🗺️ In-depth travel reports</strong>
        <p>Destinations rated by accessibility, ease of movement, and availability of suitable medical and recreational services.</p>
      </div>
      <div class="offer-card">
        <strong>✨ Stories & inspiration</strong>
        <p>Real-life experiences of people over 60 or with special needs who prove that adventure knows no age or obstacle.</p>
      </div>
      <div class="offer-card">
        <strong>📘 Practical guides & tips</strong>
        <p>From planning your trip and handling health insurance to the best means of transport and accommodation designed for your comfort.</p>
      </div>
    </div>

    <!-- Philosophy block with blue theme -->
    <div class="philosophy-block">
      <h2 style="border-left-color: #6c9ebf; margin-top: 0;">Our philosophy: Life begins after 60</h2>
      <p>
        We reject the idea of reducing a person to the number on their passport. Sixty is not the end of the road—it is a new starting point toward a life full of discovery. 
        True youth lies in a fresh spirit and a curious mind, and these never grow old.
      </p>
      <p style="margin-bottom: 0;">
        At <strong>Young over 60</strong>, we see every traveler as a story worth telling, and every journey as a chance to renew life. 
        Join us, because the world is too big to be seen only from a window, and because your right to travel never expires.
      </p>
    </div>

    <!-- final call to action -->
    <div class="join-cta">
      <p>🌊 <strong>Join our community</strong> — your next chapter starts here.</p>
      <div class="signature">
        Young over 60 · Beyond 60, life begins again.
      </div>
    </div>
    </div>
  </div>
</div>';

$contentAr = $cssStyles . "\n" . '<div class="about-container">
  <div class="about-inner-wrap">
  <div class="brand-bar"></div>
  <div class="about-inner">
    <!-- slogan -->
    <div class="slogan">
      {logo}
      <span>✨ بعد الستين، تبدأ الحياة من جديد. ✨</span>
    </div>

    <!-- main title -->
    <h1>شباب فوق الستين</h1>

    <p class="intro-text">
      نؤمن بأن السياحة ليست ترفًا ولا رفاهية مخصصة لفئة عمرية معينة، بل هي حق أساسي للجميع، بغض النظر عن العمر أو القدرات الجسدية. هي نافذة على جمال العالم، ووسيلة للتواصل مع الثقافات، وداعم أساسي للصحة النفسية والجسدية.
    </p>

    <p>
      نحن لسنا مجرد موقع سياحي إخباري؛ نحن منصة نابضة بالحياة، صممت خصيصًا لمن يرى أن العمر مجرد رقم لا يحدد الروح ولا الطموح. نؤمن بأن الحياة الحقيقية تبدأ بعد الستين، حيث تتسع الخبرة، ويزداد الشغف، وتنضج الرغبة في الاستكشاف دون قيود.
    </p>

    <!-- Vision section -->
    <h2>رؤيتنا: سياحة بلا حدود</h2>
    <p>
      نسعى لجعل تجربة السفر متاحة وميسرة وممتعة لكل من تجاوز الستين، ولكل فرد من ذوي الاحتياجات الخاصة. نعمل على كسر الصور النمطية التي تروّج أن السفر صعب أو غير مناسب بعد عمر معين، أو لأصحاب الهمم. بالعكس، نحن نرى فيكم طاقة الشباب المستمر، ونريد أن نكون دليلكم الموثوق لاستكشاف العالم بأمان ورقي.
    </p>

    <!-- What we offer grid -->
    <h2>ماذا نقدم؟</h2>
    <div class="offer-grid">
      <div class="offer-card">
        <strong>📰 أخبار وتحليلات متخصصة</strong>
        <p>أحدث الاتجاهات في سياحة كبار السن وذوي الاحتياجات الخاصة، من سياسات الطيران المريحة إلى الفنادق المجهزة بأعلى المعايير.</p>
      </div>
      <div class="offer-card">
        <strong>🗺️ تقارير سياحية معمقة</strong>
        <p>وجهات مصنفة وفق معايير إمكانية الوصول (Accessibility)، وسهولة الحركة، وتوافر الخدمات الطبية والترفيهية المناسبة.</p>
      </div>
      <div class="offer-card">
        <strong>✨ قِصص وإلهام</strong>
        <p>تجارب حقيقية لأشخاص تجاوزوا الستين أو من ذوي الاحتياجات الخاصة، وأثبتوا أن المغامرة لا تعرف عمرًا ولا عائقًا.</p>
      </div>
      <div class="offer-card">
        <strong>📘 أدلة ونصائح عملية</strong>
        <p>من كيفية التخطيط للرحلة، والتعامل مع التأمين الصحي، إلى أفضل وسائل النقل والإقامة المصممة لراحتكم.</p>
      </div>
    </div>

    <!-- Philosophy block with blue theme -->
    <div class="philosophy-block">
      <h2 style="border-left-color: #6c9ebf; margin-top: 0;">فلسفتنا: الحياة تبدأ بعد الستين</h2>
      <p>
        نحن نرفض أن يُختزل الإنسان في رقم في جواز سفره. الستون ليست نهاية الطريق، بل هي محطة انطلاق جديدة نحو حياة مليئة بالاكتشافات. فالشباب الحقيقي هو نضارة الروح وفضول العقل، وهذان لا يشيخان أبدًا.
      </p>
      <p style="margin-bottom: 0;">
        في <strong>شباب فوق الستين</strong>، نرى في كل مسافر قصة تستحق أن تُروى، وفي كل رحلة فرصة لتجديد الحياة. انضم إلينا، لأن العالم أكبر من أن يُرى من النافذة فقط، ولأن حقك في السياحة لا يسقط بالتقادم.
      </p>
    </div>

    <!-- final call to action -->
    <div class="join-cta">
      <p>🌊 <strong>انضم إلى مجتمعنا</strong> — فصلك الجديد يبدأ من هنا.</p>
      <div class="signature">
        شباب فوق الستين · بعد الستين، تبدأ الحياة من جديد.
      </div>
      </div>
    </div>
  </div>
</div>';

try {
    $db = Database::getInstance()->getConnection();
    
    // Check if about page exists
    $stmt = $db->prepare("SELECT id FROM custom_pages WHERE id = 'page-about' OR slug_en = 'about-us' LIMIT 1");
    $stmt->execute();
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        $pageId = $existing['id'];
        echo "Updating existing About Us page (ID: {$pageId})...\n";
        
        $updateStmt = $db->prepare("
            UPDATE custom_pages SET
                title_en = 'About Us',
                title_ar = 'من نحن',
                slug_en = 'about-us',
                slug_ar = 'من-نحن',
                content_en = :content_en,
                content_ar = :content_ar,
                template_type = 'about',
                hero_title_en = 'About Us',
                hero_title_ar = 'من نحن',
                hero_subtitle_en = 'Beyond 60, life Begins Again',
                hero_subtitle_ar = 'بعد الستين، تبدأ الحياة من جديد',
                hero_image = '/images/australia.png',
                featured_image = '',
                is_published = 1,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        $updateStmt->execute([
            ':content_en' => $contentEn,
            ':content_ar' => $contentAr,
            ':id' => $pageId
        ]);
        echo "✓ About Us page updated successfully.\n";
    } else {
        echo "About Us page not found, creating new page...\n";
        $pageId = 'page-about';
        
        $insertStmt = $db->prepare("
            INSERT INTO custom_pages (
                id, slug_en, slug_ar, title_en, title_ar, content_en, content_ar, 
                template_type, sort_order, show_in_menu, menu_title_en, menu_title_ar,
                hero_title_en, hero_title_ar, hero_subtitle_en, hero_subtitle_ar, hero_image,
                meta_title_en, meta_title_ar, meta_description_en, meta_description_ar, is_published,
                created_at, updated_at
            ) VALUES (
                :id, 'about-us', 'من-نحن', 'About Us', 'من نحن', :content_en, :content_ar, 
                'about', 1, 1, 'About Us', 'من نحن',
                '', '', '', '', '',
                'About Us | Travel Without Boundaries', 'من نحن | شباب فوق الستين', 
                'Beyond 60, life begins again. Learn more about Young Over 60, our mission, vision, and philosophy.',
                'بعد الستين، تبدأ الحياة من جديد. تعرف على رسالتنا ورؤيتنا وفلسفتنا في شباب فوق الستين.',
                1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
            )
        ");
        $insertStmt->execute([
            ':id' => $pageId,
            ':content_en' => $contentEn,
            ':content_ar' => $contentAr
        ]);
        echo "✓ About Us page created successfully.\n";
    }

    // Clear page cache so changes take effect immediately
    $cacheDir = PATH_ROOT . '/cache';
    $cacheFiles = glob($cacheDir . '/page_*.json');
    if ($cacheFiles) {
        foreach ($cacheFiles as $f) { @unlink($f); }
        echo "✓ Page cache cleared (" . count($cacheFiles) . " files).\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
