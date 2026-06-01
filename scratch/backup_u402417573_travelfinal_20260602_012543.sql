-- Travel Without Limits Database Backup (Hero Video Migration)
-- Generated on 2026-06-02 01:25:43

SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------
-- Table structure for table `categories`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_en` text COLLATE utf8mb4_unicode_ci,
  `description_ar` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_text_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_text_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug_en`),
  KEY `idx_categories_slug` (`slug_en`),
  KEY `idx_categories_slug_en` (`slug_en`),
  KEY `idx_categories_slug_ar` (`slug_ar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `categories`
INSERT INTO `categories` (`id`, `name_en`, `name_ar`, `slug_en`, `slug_ar`, `description_en`, `description_ar`, `image`, `alt_text_en`, `alt_text_ar`, `created_at`, `updated_at`) VALUES 
('cat-accommodation', 'Accommodation', NULL, 'accommodation', 'accommodation', 'Everything you need to know about Accommodation and accessible travel.', NULL, '/images/hero-bg.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-act', 'ACT', NULL, 'australian-capital-territory', 'australian-capital-territory', 'Everything you need to know about ACT and accessible travel.', NULL, '/images/hero-bg.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-africa', 'Africa', NULL, 'africa', 'africa', 'Everything you need to know about Africa and accessible travel.', NULL, '/images/hero-bg.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-americas', 'Americas', 'الأمريكتان', 'americas', 'الأمريكتان', 'Everything you need to know about Americas and accessible travel.', 'أدلة وقصص السفر عبر الأمريكتين الشمالية والجنوبية.', '/images/americas.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-animal-encounters', 'Animal Encounters', NULL, 'animal-encounters', 'animal-encounters', 'Everything you need to know about Animal Encounters and accessible travel.', NULL, '/images/animals.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-animals', 'Assistance Animals', 'حيوانات المساعدة', 'assistance-animals', 'حيوانات-المساعدة', 'Everything you need to know about Assistance Animals and accessible travel.', 'إرشادات وقوانين السفر بصحبة حيوانات الخدمة والمساعدة.', '/images/animals.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-asiapacific', 'Asia Pacific', 'آسيا والمحيط الهادئ', 'asia-pacific', 'آسيا-والمحيط-الهادئ', 'Everything you need to know about Asia Pacific and accessible travel.', 'أدلة السفر الشاملة لمنطقة آسيا والمحيط الهادئ.', '/images/asia.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-australia', 'Australia', 'أستراليا', 'australia', 'أستراليا', 'Everything you need to know about Australia and accessible travel.', 'كل ما تحتاج لمعرفته حول أستراليا والسياحة الميسرة لسهولة الوصول.', '/images/australia.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-cruises', 'Cruises', 'الرحلات البحرية', 'cruises', 'الرحلات-البحرية', 'Everything you need to know about Cruises and accessible travel.', 'رحلات بحرية فاخرة مجهزة بجميع وسائل الراحة وسهولة الوصول.', '/images/europe.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-deaf', 'Deaf / Hard of Hearing', NULL, 'deaf-hard-of-hearing', 'deaf-hard-of-hearing', 'Everything you need to know about Deaf / Hard of Hearing and accessible travel.', NULL, '/images/hero-bg.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-europe', 'Europe', 'أوروبا', 'europe', 'أوروبا', 'Everything you need to know about Europe and accessible travel.', 'كل ما تحتاج لمعرفته حول أوروبا وتراثها الغني مع أدلة ميسرة الوصول.', '/images/europe.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-events', 'Events & Holidays', NULL, 'events-holidays', 'events-holidays', 'Everything you need to know about Events & Holidays and accessible travel.', NULL, '/images/hero-bg.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-family', 'Family Travel', 'السفر العائلي', 'family-travel', 'السفر-العائلي', 'Everything you need to know about Family Travel and accessible travel.', 'نصائح السفر مع العائلة والأحفاد لرحلة مريحة وممتعة للجميع.', '/images/mobility.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-food', 'Food & Drink', 'الطعام والشراب', 'food-drink', 'الطعام-والشراب', 'Everything you need to know about Food & Drink and accessible travel.', 'تجارب تذوق الطعام الفاخرة حول العالم والمطاعم الميسرة.', '/images/hero-bg.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-gear', 'Gear & Support', NULL, 'gear', 'gear', 'Everything you need to know about Gear & Support and accessible travel.', NULL, '/images/hero-bg.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-hidden', 'Hidden Disabilities', NULL, 'hidden-disabilities', 'hidden-disabilities', 'Everything you need to know about Hidden Disabilities and accessible travel.', NULL, '/images/mental.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-inspiration', 'Inspiration', 'الإلهام والقصص', 'inspiration', 'قصص-ملهمة', 'Everything you need to know about Inspiration and accessible travel.', 'قصص سفر حقيقية ملهمة ومحفزة للمسافرين النشطين فوق الستين.', '/images/hero-bg.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-mobility', 'Physical / Mobility', 'الحركية والجسدية', 'physical-mobility', 'سهولة-الوصول-الحركي', 'Everything you need to know about Physical / Mobility and accessible travel.', 'نصائح ومصادر للمسافرين ذوي الاحتياجات الحركية ومستخدمي الكراسي المتحركة.', '/images/mobility.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-neurodiversity', 'Neurodiversity', NULL, 'neurodiversity', 'neurodiversity', 'Everything you need to know about Neurodiversity and accessible travel.', NULL, '/images/neurodiversity.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-nsw', 'New South Wales', 'نيو ساوث ويلز', 'new-south-wales', 'نيو-ساوث-ويلز', 'Everything you need to know about New South Wales and accessible travel.', 'كل ما تحتاج لمعرفته حول نيو ساوث ويلز والسياحة الميسرة.', '/images/hero-bg.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-nt', 'Northern Territory', NULL, 'northern-territory', 'northern-territory', 'Everything you need to know about Northern Territory and accessible travel.', NULL, '/images/hero-bg.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-queensland', 'Queensland', 'كوينزلاند', 'queensland', 'كوينزلاند', 'Everything you need to know about Queensland and accessible travel.', 'كل ما تحتاج لمعرفته حول كوينزلاند ورحلاتها الساحلية الممتازة.', '/images/hero-bg.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-reviews', 'Resources & Reviews', NULL, 'resources-reviews', 'resources-reviews', 'Everything you need to know about Resources & Reviews and accessible travel.', NULL, '/images/hero-bg.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-sa', 'South Australia', NULL, 'south-australia', 'south-australia', 'Everything you need to know about South Australia and accessible travel.', NULL, '/images/hero-bg.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-sensory', 'Sensory Needs', NULL, 'sensory-needs', 'sensory-needs', 'Everything you need to know about Sensory Needs and accessible travel.', NULL, '/images/sensory.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-tasmania', 'Tasmania', NULL, 'tasmania', 'tasmania', 'Everything you need to know about Tasmania and accessible travel.', NULL, '/images/hero-bg.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-tips', 'Tips & Tricks', NULL, 'tips-tricks', 'tips-tricks', 'Everything you need to know about Tips & Tricks and accessible travel.', NULL, '/images/hero-bg.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-transport', 'Transport', NULL, 'transport', 'transport', 'Everything you need to know about Transport and accessible travel.', NULL, '/images/hero-bg.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-victoria', 'Victoria', 'فيكتوريا', 'victoria', 'فيكتوريا', 'Everything you need to know about Victoria and accessible travel.', 'كل ما تحتاج لمعرفته حول مقاطعة فيكتوريا الأسترالية والرحلات ميسرة الوصول.', '/images/hero-bg.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-vision', 'Blind / Low Vision', 'المكفوفين وضعاف البصر', 'blind-low-vision', 'المكفوفين-وضعاف-البصر', 'Everything you need to know about Blind / Low Vision and accessible travel.', 'أدلة سياحية حسية وخدمات مخصصة لضعاف البصر والمكفوفين.', '/images/vision.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('cat-wa', 'Western Australia', NULL, 'western-australia', 'western-australia', 'Everything you need to know about Western Australia and accessible travel.', NULL, '/images/hero-bg.png', NULL, NULL, '2026-05-27 04:27:01', '2026-05-27 06:23:01');

-- -----------------------------------------------------
-- Table structure for table `contact_submissions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `contact_submissions`;
CREATE TABLE `contact_submissions` (
  `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `contact_submissions`

-- -----------------------------------------------------
-- Table structure for table `custom_pages`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `custom_pages`;
CREATE TABLE `custom_pages` (
  `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content_en` longtext COLLATE utf8mb4_unicode_ci,
  `content_ar` longtext COLLATE utf8mb4_unicode_ci,
  `excerpt_en` text COLLATE utf8mb4_unicode_ci,
  `excerpt_ar` text COLLATE utf8mb4_unicode_ci,
  `template_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default',
  `sort_order` int NOT NULL DEFAULT '0',
  `show_in_menu` tinyint(1) NOT NULL DEFAULT '0',
  `menu_title_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `menu_title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_title_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_subtitle_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_subtitle_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_cta_text_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_cta_text_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_cta_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_title_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description_en` text COLLATE utf8mb4_unicode_ci,
  `meta_description_ar` text COLLATE utf8mb4_unicode_ci,
  `featured_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_title_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_description_en` text COLLATE utf8mb4_unicode_ci,
  `og_description_ar` text COLLATE utf8mb4_unicode_ci,
  `og_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_title_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_description_en` text COLLATE utf8mb4_unicode_ci,
  `twitter_description_ar` text COLLATE utf8mb4_unicode_ci,
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug_en` (`slug_en`),
  UNIQUE KEY `slug_ar` (`slug_ar`),
  KEY `idx_custom_pages_slug_en` (`slug_en`),
  KEY `idx_custom_pages_slug_ar` (`slug_ar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `custom_pages`
INSERT INTO `custom_pages` (`id`, `slug_en`, `slug_ar`, `title_en`, `title_ar`, `content_en`, `content_ar`, `excerpt_en`, `excerpt_ar`, `template_type`, `sort_order`, `show_in_menu`, `menu_title_en`, `menu_title_ar`, `hero_title_en`, `hero_title_ar`, `hero_subtitle_en`, `hero_subtitle_ar`, `hero_image`, `hero_cta_text_en`, `hero_cta_text_ar`, `hero_cta_link`, `meta_title_en`, `meta_title_ar`, `meta_description_en`, `meta_description_ar`, `featured_image`, `og_title_en`, `og_title_ar`, `og_description_en`, `og_description_ar`, `og_image`, `twitter_title_en`, `twitter_title_ar`, `twitter_description_en`, `twitter_description_ar`, `is_published`, `created_at`, `updated_at`) VALUES 
('page-about', 'about-us', 'من-نحن', 'About Us', 'من نحن', '<h3>Who We Are</h3>\n<p>At <strong>Young Over 60</strong>, we believe that travel is not a luxury or a privilege reserved for a specific age group. Rather, it is a fundamental right for everyone, regardless of age or physical capabilities. It is a window to the beauty of the world, a gateway to connect with diverse cultures, and a vital source of physical and psychological well-being.</p>\n<p>We are not just a travel news website; we are a vibrant, active platform specifically designed for those who believe that age is merely a number that should never define one\'s spirit or ambition. We firmly believe that real life begins after sixty—a time when experience expands, passion grows, and the desire to explore matures without limits.</p>\n<blockquote>Our Slogan: Beyond 60, life Begins Again</blockquote>\n<h3>Our Vision: Travel Without Limits</h3>\n<p>We strive to make travel accessible, smooth, and enjoyable for everyone over sixty and for individuals with physical or sensory requirements. We work to break down the social stereotypes that label travel as difficult or unsuitable after a certain age or for individuals with determination. On the contrary, we see in you the energy of everlasting youth, and we want to be your trusted guide to exploring the world with safety, comfort, and luxury.</p>\n<h3>What We Offer</h3>\n<ul>\n  <li><strong>Specialized News & Analysis:</strong> The latest trends in accessible senior travel—from comfortable airline policies to hotels equipped with the highest standards of accessibility.</li>\n  <li><strong>In-Depth Travel Reports:</strong> Destinations ranked according to strict accessibility criteria, ease of movement, and availability of appropriate medical and recreational services.</li>\n  <li><strong>Stories & Inspiration:</strong> Real-life experiences of individuals who traveled beyond sixty or overcame physical challenges, proving that adventure knows no age and no barriers.</li>\n  <li><strong>Practical Guides & Tips:</strong> From planning your trip and dealing with international health insurance, to the best transportation options and accommodations tailored for your comfort.</li>\n</ul>\n<h3>Our Philosophy: Life Begins After Sixty</h3>\n<p>We refuse to let a person be reduced to a number on a passport. Sixty is not the end of the road; it is a new departure point toward a life filled with discovery. Real youth is the vibrancy of the soul and the curiosity of the mind, and these never grow old.</p>\n<p>At Young Over 60, we see in every traveler a story worth telling, and in every journey, an opportunity to renew life. Join us—because the world is too beautiful to be viewed only from a window, and because your right to travel never expires.</p>', '<h3>من نحن</h3>\n<p>في <strong>Young Over 60</strong>، نؤمن بأن السياحة ليست ترفًا ولا رفاهية مخصصة لفئة عمرية معينة، بل هي حق أساسي للجميع، بغض النظر عن العمر أو القدرات الجسدية. هي نافذة على جمال العالم، ووسيلة للتواصل مع الثقافات، وداعم أساسي للصحة النفسية والجسدية.</p>\n<p>نحن لسنا مجرد موقع سياحي إخباري؛ نحن منصة نابضة بالحياة، صممت خصيصًا لمن يرى أن العمر مجرد رقم لا يحدد الروح ولا الطموح. نؤمن بأن الحياة الحقيقية تبدأ بعد الستين، حيث تتسع الخبرة، ويزداد الشغف، وتنضج الرغبة في الاستكشاف دون قيود.</p>\n<blockquote>شعارنا: Beyond 60, life Begins Again</blockquote>\n<h3>رؤيتنا: سياحة بلا حدود</h3>\n<p>نسعى لجعل تجربة السفر متاحة وميسرة وممتعة لكل من تجاوز الستين، ولكل فرد من ذوي الاحتياجات الخاصة. نعمل على كسر الصور النمطية التي تروّج أن السفر صعب أو غير مناسب بعد عمر معين، أو لأصحاب الهمم. بالعكس، نحن نرى فيكم طاقة الشباب المستمر، ونريد أن نكون دليلكم الموثوق لاستكشاف العالم بأمان ورقي.</p>\n<h3>ماذا نقدم؟</h3>\n<ul>\n  <li><strong>أخبار وتحليلات متخصصة:</strong> أحدث الاتجاهات في سياحة كبار السن وذوي الاحتياجات الخاصة، من سياسات الطيران المريحة إلى الفنادق المجهزة بأعلى المعايير.</li>\n  <li><strong>تقارير سياحية معمقة:</strong> وجهات مصنفة وفق معايير إمكانية الوصول (Accessibility)، وسهولة الحركة، وتوافر الخدمات الطبية والترفيهية المناسبة.</li>\n  <li><strong>قِصص وإلهام:</strong> تجارب حقيقية لأشخاص تجاوزوا الستين أو من ذوي الاحتياجات الخاصة، وأثبتوا أن المغامرة لا تعرف عمرًا ولا عائقًا.</li>\n  <li><strong>أدلة ونصائح عملية:</strong> من كيفية التخطيط للرحلة، والتعامل مع التأمين الصحي، إلى أفضل وسائل النقل والإقامة المصممة لراحتكم.</li>\n</ul>\n<h3>فلسفتنا: الحياة تبدأ بعد الستين</h3>\n<p>نحن نرفض أن يُختزل الإنسان في رقم في جواز سفره. الستون ليست نهاية الطريق، بل هي محطة انطلاق جديدة نحو حياة مليئة بالاكتشافات. فالشباب الحقيقي هو نضارة الروح وفضول العقل، وهذان لا يشيخان أبدًا.</p>\n<p>في Young Over 60، نرى في كل مسافر قصة تستحق أن تُروى، وفي كل رحلة فرصة لتجديد الحياة. انضم إلينا، لأن العالم أكبر من أن يُرى من النافذة فقط، ولأن حقك في السياحة لا يسقط بالتقادم.</p>', NULL, NULL, 'about', '1', '1', 'About Us', 'من نحن', 'About Us', 'من نحن', 'Beyond 60, life Begins Again', 'بعد الستين، تبدأ الحياة من جديد', '/images/australia.png', NULL, NULL, NULL, 'About Our Mission and Philosophy', 'من نحن ورسالتنا وسياستنا', 'Learn more about Young Over 60, our mission, vision, everlasting youth philosophy, and committed travel guidelines.', 'تعرف على رسالتنا ورؤيتنا وفلسفتنا في شباب فوق الستين نحو سياحة بلا حدود وخدمة أصحاب الهمم وكبار السن.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', '2026-05-29 23:39:39', '2026-05-30 11:43:58'),
('page-medical', 'medical-tourism', 'السياحة-العلاجية', 'Medical Tourism', 'السياحة العلاجية', '<h3>Medical Tourism for Seniors: Wellness & Healing Without Limits</h3>\n<p>Medical and wellness tourism represents a vital avenue for active travelers over sixty looking for natural healing, rehabilitation, and dynamic rejuvenation. At <strong>Young Over 60</strong>, we believe that healthcare and preventive treatments should always be paired with luxurious, stress-free, and fully accessible travel experiences.</p>\n<p>From the mineral-rich sulfur springs and natural clay baths of Egypt and the Middle East, to the pioneering medical clinics of Europe, we provide a comprehensive directory specifically designed around your mobility and health requirements.</p>\n<h3>Our Core Medical Tourism Pillars:</h3>\n<ul>\n  <li><strong>Seamless Accessibility & Medical Logistics:</strong> We highlight destinations that offer fully accessible transit, support for mobility aids, and immediate proximity to top-tier specialized medical clinics.</li>\n  <li><strong>Therapeutic & Holistic Retreats:</strong> Integrating natural treatments like hydrotherapy, mineral baths, medical massages, and senior-focused yoga programs in scenic, tranquil environments.</li>\n  <li><strong>Insurance & Care Coordination:</strong> Crucial guidance on selecting international travel medical insurance and coordinating with personal physicians to ensure a completely worry-free journey.</li>\n</ul>', '<h3>السياحة العلاجية لكبار السن: استشفاء وراحة بلا حدود</h3>\n<p>تعد السياحة العلاجية والاستشفائية أحد الروافد الهامة للمسافرين النشطين فوق الستين ومن يطمحون إلى الاستشفاء الطبيعي وتجديد الحيوية والنشاط. نؤمن في <strong>Young Over 60</strong> بأن الرعاية الصحية والوقاية الطبيعية يجب أن تقترن بتجارب سفر فاخرة ومريحة وآمنة بالكامل.</p>\n<p>من عيون المياه الكبريتية والمنتجعات الطين الطبيعية في مصر والشرق الأوسط، إلى المصحات الطبية الرائدة في أوروبا، نقدم لكم دليلاً شاملاً مصمماً خصيصاً ليناسب متطلباتكم الحركية والصحية.</p>\n<h3>أهم ركائز السياحة العلاجية لدينا:</h3>\n<ul>\n  <li><strong>تيسير الوصول واللوجستيات الطبية:</strong> نركز على الوجهات التي توفر بنية تحتية ميسرة بالكامل، وتسهيلات خاصة لمستخدمي الأجهزة المساعدة، مع قرب المراكز الطبية المتخصصة.</li>\n  <li><strong>المنتجعات الصحية والاستشفائية:</strong> دمج العلاجات الطبيعية مثل العلاج بالمياه المعدنية، والتدليك العلاجي، واليوجا المخصصة لكبار السن في بيئات طبيعية خلابة وهادئة.</li>\n  <li><strong>استشارات التأمين والرعاية:</strong> نصائح وحيل حول تأمين السفر الطبي الدولي والتنسيق مع الأطباء المعالجين لضمان رحلة خالية من القلق.</li>\n</ul>', NULL, NULL, 'medical-tourism', '2', '1', 'Medical Tourism', 'السياحة العلاجية', 'Medical Tourism & Wellness', 'السياحة العلاجية والاستشفاء', 'Healing, Rejuvenation and Accessibility Curated for Seniors', 'علاجات طبيعية، استشفاء وسهولة وصول كاملة منسقة لكبار السن', '/images/europe.png', NULL, NULL, NULL, 'Accessible Medical Tourism and Wellness Springs', 'السياحة العلاجية والاستشفائية الميسرة', 'Discover the best natural spas, therapeutic mineral springs, and specialized accessible medical travel guides globally.', 'اكتشف أفضل منتجعات المياه الكبريتية والمصحات الطبيعية وأدلة السفر الطبي الميسرة حول العالم.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', '2026-05-29 23:39:39', '2026-05-30 11:43:58');

-- -----------------------------------------------------
-- Table structure for table `hero_slides`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hero_slides`;
CREATE TABLE `hero_slides` (
  `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtitle_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtitle_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button_text_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button_text_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_text_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_text_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `overlay_opacity` decimal(3,2) NOT NULL DEFAULT '0.50',
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `hero_slides`
INSERT INTO `hero_slides` (`id`, `title_en`, `title_ar`, `subtitle_en`, `subtitle_ar`, `button_text_en`, `button_text_ar`, `button_link`, `image_en`, `image_ar`, `alt_text_en`, `alt_text_ar`, `overlay_opacity`, `sort_order`, `is_active`, `created_at`) VALUES 
('slide-1', 'Young Over 60', 'شباب فوق الستين', 'Premium travel guides, reviews, and stories for active seniors', 'أدلة سفر متميزة، مراجعات وقصص ملهمة للمسنين النشطين', 'Discover More', 'اكتشف المزيد', '/destinations', '/images/hero-bg.png', '/images/hero-bg.png', NULL, NULL, '0.40', '1', '1', '2026-05-27 04:27:01'),
('slide-2', 'Inspirational Journeys', 'رحلات ملهمة', 'Real stories of adventurers breaking down physical barriers', 'قصص حقيقية لمغامرين يتحدون الحواجز الجسدية والعقبات', 'Read Stories', 'اقرأ القصص', '/news', '/images/australia.png', '/images/australia.png', NULL, NULL, '0.45', '2', '1', '2026-05-27 04:27:01'),
('slide-3', 'Explore Accessible Egypt', 'اكتشف مصر الميسرة', 'Pockets of history and magic curated for diverse requirements', 'تفاصيل تاريخية وجمال فريد منسق ليلائم متطلبات الوصول المتنوعة', 'View Guides', 'عرض الأدلة', '/women-stories', '/images/europe.png', '/images/europe.png', NULL, NULL, '0.50', '3', '1', '2026-05-27 04:27:01');

-- -----------------------------------------------------
-- Table structure for table `media`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `media`;
CREATE TABLE `media` (
  `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mimeType` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fileSize` int NOT NULL,
  `width` int DEFAULT NULL,
  `height` int DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `filename` (`filename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `media`
INSERT INTO `media` (`id`, `filename`, `url`, `mimeType`, `fileSize`, `width`, `height`, `created_at`) VALUES 
('ce4691c636751740e2ffbab614b7a419', '7d95b713a6130942_1780084681.jpeg', '/uploads/7d95b713a6130942_1780084681.jpeg', 'image/jpeg', '121385', '1024', '1024', '2026-05-29 22:58:01');

-- -----------------------------------------------------
-- Table structure for table `menu_banners`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `menu_banners`;
CREATE TABLE `menu_banners` (
  `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menuId` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `menuId` (`menuId`),
  CONSTRAINT `menu_banners_ibfk_1` FOREIGN KEY (`menuId`) REFERENCES `menus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `menu_banners`
INSERT INTO `menu_banners` (`id`, `menuId`, `image`, `title_en`, `title_ar`, `url`, `isActive`) VALUES 
('banner-dest', 'menu-destinations', '/images/hero-bg.png', 'Featured Destination', NULL, '/featured', '1');

-- -----------------------------------------------------
-- Table structure for table `menu_links`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `menu_links`;
CREATE TABLE `menu_links` (
  `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sectionId` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `target` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sectionId` (`sectionId`),
  CONSTRAINT `menu_links_ibfk_1` FOREIGN KEY (`sectionId`) REFERENCES `menu_sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `menu_links`
INSERT INTO `menu_links` (`id`, `sectionId`, `title_en`, `title_ar`, `url`, `image`, `order`, `isActive`, `target`) VALUES 
('link-accom', 'sec-plan', 'ACCOMMODATION', 'الإقامة الفندقية', '/category/accommodation', '/images/hero-bg.png', '1', '1', NULL),
('link-act', 'sec-australia', 'ACT', 'إقليم العاصمة', '/category/australian-capital-territory', NULL, '1', '1', NULL),
('link-beach', 'sec-experiences', 'BEACH GETAWAYS', 'عطلات الشاطئ', '/category/australia', '/images/australia.png', '2', '1', NULL),
('link-cali', 'sec-americas', 'CALIFORNIA', 'كاليفورニア', '/category/americas', NULL, '2', '1', NULL),
('link-colo', 'sec-americas', 'COLORADO', 'كولورادو', '/category/americas', NULL, '1', '1', NULL),
('link-cruises', 'sec-experiences', 'CRUISES', 'الرحلات البحرية', '/category/cruises', '/images/europe.png', '3', '1', NULL),
('link-encounters', 'sec-experiences', 'ANIMAL ENCOUNTERS', 'لقاءات الحيوانات', '/category/animal-encounters', '/images/animals.png', '1', '1', NULL),
('link-family', 'sec-experiences', 'FAMILY TRAVEL', 'السفر العائلي', '/category/family-travel', '/images/mobility.png', '4', '1', NULL),
('link-gear', 'sec-plan', 'GEAR & SUPPORT', 'المعدات والدعم', '/category/gear', '/images/hero-bg.png', '4', '1', NULL),
('link-nsw', 'sec-australia', 'NEW SOUTH WALES', 'نيو ساوث ويلز', '/category/new-south-wales', NULL, '2', '1', NULL),
('link-nt', 'sec-australia', 'NORTHERN TERRITORY', 'الإقليم الشمالي', '/category/northern-territory', NULL, '3', '1', NULL),
('link-qld', 'sec-australia', 'QUEENSLAND', 'كوينزلاند', '/category/queensland', NULL, '4', '1', NULL),
('link-texas', 'sec-americas', 'TEXAS', 'تكساس', '/category/americas', NULL, '3', '1', NULL),
('link-tips', 'sec-plan', 'TIPS & TRICKS', 'نصائح وحيل', '/category/tips-tricks', '/images/hero-bg.png', '2', '1', NULL),
('link-transport', 'sec-plan', 'TRANSPORT', 'وسائل النقل', '/category/transport', '/images/hero-bg.png', '3', '1', NULL);

-- -----------------------------------------------------
-- Table structure for table `menu_sections`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `menu_sections`;
CREATE TABLE `menu_sections` (
  `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menuId` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `menuId` (`menuId`),
  CONSTRAINT `menu_sections_ibfk_1` FOREIGN KEY (`menuId`) REFERENCES `menus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `menu_sections`
INSERT INTO `menu_sections` (`id`, `menuId`, `title_en`, `title_ar`, `order`, `isActive`) VALUES 
('sec-americas', 'menu-destinations', 'AMERICAS', 'الأمريكتان', '2', '1'),
('sec-australia', 'menu-destinations', 'AUSTRALIA', 'أستراليا', '1', '1'),
('sec-experiences', 'menu-experiences', 'ALL EXPERIENCES', 'جميع التجارب', '1', '1'),
('sec-plan', 'menu-plan', 'PLANNING HELP', 'المساعدة في التخطيط', '1', '1');

-- -----------------------------------------------------
-- Table structure for table `menus`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
  `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `layoutType` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'LIST',
  `layoutConfig` longtext COLLATE utf8mb4_unicode_ci,
  `order` int NOT NULL DEFAULT '0',
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `menus`
INSERT INTO `menus` (`id`, `title_en`, `title_ar`, `slug`, `type`, `layoutType`, `layoutConfig`, `order`, `isActive`, `created_at`, `updated_at`) VALUES 
('menu-destinations', 'DESTINATIONS', 'الوجهات', 'destinations', 'MEGA', 'LIST', NULL, '1', '1', '2026-05-27 04:27:01', '2026-06-01 18:17:45'),
('menu-experiences', 'EXPERIENCES', 'التجارب', 'experiences', 'MEGA', 'GRID', NULL, '2', '1', '2026-05-27 04:27:01', '2026-05-27 06:23:01'),
('menu-plan', 'PLAN YOUR TRIP', 'خطط لرحلتك', 'plan-your-trip', 'MEGA', 'GRID', NULL, '3', '1', '2026-05-27 04:27:01', '2026-05-27 06:23:01');

-- -----------------------------------------------------
-- Table structure for table `newsletters`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `newsletters`;
CREATE TABLE `newsletters` (
  `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `newsletters`

-- -----------------------------------------------------
-- Table structure for table `page_redirects`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `page_redirects`;
CREATE TABLE `page_redirects` (
  `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `old_slug` (`old_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `page_redirects`

-- -----------------------------------------------------
-- Table structure for table `page_revisions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `page_revisions`;
CREATE TABLE `page_revisions` (
  `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content_en` longtext COLLATE utf8mb4_unicode_ci,
  `content_ar` longtext COLLATE utf8mb4_unicode_ci,
  `excerpt_en` text COLLATE utf8mb4_unicode_ci,
  `excerpt_ar` text COLLATE utf8mb4_unicode_ci,
  `template_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int NOT NULL,
  `show_in_menu` tinyint(1) NOT NULL,
  `menu_title_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `menu_title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_title_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_subtitle_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_subtitle_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_cta_text_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_cta_text_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_cta_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_title_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description_en` text COLLATE utf8mb4_unicode_ci,
  `meta_description_ar` text COLLATE utf8mb4_unicode_ci,
  `featured_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_title_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_description_en` text COLLATE utf8mb4_unicode_ci,
  `og_description_ar` text COLLATE utf8mb4_unicode_ci,
  `og_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_title_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_description_en` text COLLATE utf8mb4_unicode_ci,
  `twitter_description_ar` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`),
  CONSTRAINT `page_revisions_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `custom_pages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `page_revisions`

-- -----------------------------------------------------
-- Table structure for table `podcasts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `podcasts`;
CREATE TABLE `podcasts` (
  `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_en` text COLLATE utf8mb4_unicode_ci,
  `description_ar` text COLLATE utf8mb4_unicode_ci,
  `audio_file` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_text_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_text_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug_en`),
  KEY `idx_podcasts_slug_en` (`slug_en`),
  KEY `idx_podcasts_slug_ar` (`slug_ar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `podcasts`
INSERT INTO `podcasts` (`id`, `title_en`, `title_ar`, `slug_en`, `slug_ar`, `description_en`, `description_ar`, `audio_file`, `cover_image`, `alt_text_en`, `alt_text_ar`, `duration`, `category_en`, `category_ar`, `is_featured`, `created_at`) VALUES 
('pod-1', 'Accessible Antiquities: Exploring Giza Wheelchair Access', 'الآثار ميسرة الوصول: استكشاف أهرامات الجيزة', 'accessible-antiquities-exploring-giza-wheelchair-access', 'استكشاف-اهرامات-الجيزة-سهل-الوصول', 'In this episode, we sit down with local experts and international travellers who share their firsthand experiences navigating the Giza Plateau, the pyramids, and the new Grand Egyptian Museum using mobility devices.', 'في هذه الحلقة، نلتقي بخبراء محليين ومسافرين دوليين يشاركون تجاربهم الحية في استكشاف هضبة الجيزة والأهرامات والمتحف المصري الكبير الجديد باستخدام الكراسي المتحركة والأجهزة المساعدة.', '/uploads/podcasts/episode1.mp3', '/images/europe.png', NULL, NULL, '24:18', 'Accessible Adventure', 'مغامرة ميسرة', '1', '2026-05-27 04:27:01'),
('pod-2', 'Sensory Calm in the Sinai Desert', 'الهدوء الحسي في صحراء سيناء', 'sensory-calm-in-the-sinai-desert', 'الهدوء-الحسي-في-صحراء-سيناء', 'Discover the therapeutic silence of the Sinai. We explore how guided desert excursions are being tailored to provide sensory-friendly wellness retreats for neurodivergent travellers looking to escape the noise.', 'اكتشف الصمت العلاجي لصحراء سيناء. نستكشف كيف يتم تصميم الرحلات الصحراوية لتوفير ملاذ استرخاء حسي مريح ومناسب للمسافرين ذوي الاحتياجات الحسية الفائقة.', '/uploads/podcasts/episode2.mp3', '/images/australia.png', NULL, NULL, '18:45', 'Solo Travel', 'السفر المنفرد', '0', '2026-05-27 04:27:01'),
('pod-3', 'Breaking Barriers: Solo Adventuring in Alexandria', 'تحدي الحواجز: مغامرة منفردة في الإسكندرية', 'breaking-barriers-solo-adventuring-in-alexandria', 'تحدي-الحواجز-مغامرة-في-الاسكندرية', 'A deep dive into navigating the coastal city of Alexandria as a solo female traveller with low vision. Tips on tactile paths, audio-guided museum exhibits, and local sensory highlights.', 'غوص عميق في استكشاف مدينة الإسكندرية الساحلية كمسافرة منفردة من ضعاف البصر. نصائح حول المسارات اللمسية والمعارض الإرشادية الصوتية والمعالم الحسية المميزة.', '/uploads/podcasts/episode3.mp3', '/images/hero-bg.png', NULL, NULL, '31:12', 'Cultural Journeys', 'رحلات ثقافية', '0', '2026-05-27 04:27:01');

-- -----------------------------------------------------
-- Table structure for table `posts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `excerpt_en` text COLLATE utf8mb4_unicode_ci,
  `excerpt_ar` text COLLATE utf8mb4_unicode_ci,
  `content_en` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_ar` longtext COLLATE utf8mb4_unicode_ci,
  `coverImage` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_text_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_text_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isFeatured` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DRAFT',
  `publishedAt` datetime DEFAULT NULL,
  `categoryId` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `authorId` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug_en`),
  KEY `idx_posts_slug` (`slug_en`),
  KEY `idx_posts_category` (`categoryId`),
  KEY `idx_posts_status` (`status`),
  KEY `idx_posts_published` (`publishedAt`),
  KEY `authorId` (`authorId`),
  KEY `idx_posts_slug_en` (`slug_en`),
  KEY `idx_posts_slug_ar` (`slug_ar`),
  FULLTEXT KEY `ft_posts_search_en` (`title_en`,`content_en`),
  FULLTEXT KEY `ft_posts_search_ar` (`title_ar`,`content_ar`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`categoryId`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`authorId`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `posts`
INSERT INTO `posts` (`id`, `title_en`, `title_ar`, `slug_en`, `slug_ar`, `excerpt_en`, `excerpt_ar`, `content_en`, `content_ar`, `coverImage`, `alt_text_en`, `alt_text_ar`, `isFeatured`, `status`, `publishedAt`, `categoryId`, `authorId`, `created_at`, `updated_at`, `deleted_at`) VALUES 
('3ad5cbd496bf97bcc952ee3574452d0c', 'test', 'تجربي', 'test', 'n-a', 'jkaajcnsdvnisdvnsinv', 'ٍتؤىستيهبرىسعبصسنتب', 'kjbadjvdeuirfnawoflsd vlk sKLDMFIOfom DFKLsd fkjn&nbsp;', 'متيسبىلخشسةلىهخستبخهسةيبمكوشسحخيلهشسعلهخةلب', '/uploads/7d95b713a6130942_1780084681.jpeg', NULL, NULL, '1', 'PUBLISHED', '2026-05-30 05:58:05', 'cat-vision', 'admin-uuid-1', '2026-05-29 22:58:05', '2026-05-29 22:58:05', NULL),
('post-comedy', 'Laughs for all: Melbourne Comedy Festival 2026', 'الضحك للجميع: مهرجان ملبورن الدولي للكوميديا 2026', 'laughs-for-all-melbourne-international-comedy-festival-2026', 'مهرجان-ملبورن-الدولي-للكوميديا-ميسر-الوصول', 'With nearly 800 shows and accessibility at its heart, the Melbourne Comedy Festival proves that laughter is for everyone.', 'مع وجود ما يقرب من 800 عرض وتركيز كامل على سهولة الوصول، يثبت مهرجان ملبورن للكوميديا أن الضحك حق للجميع.', '<div><p>The Melbourne International Comedy Festival is one of the most accessible major festivals in Australia. With a dedicated accessibility team and venues and shows designed for diverse audiences, it\'s a model for inclusive entertainment.</p><p>Key features include Auslan-interpreted shows, captioning, and wheelchair accessible venues, ensuring that no one misses out on the fun.</p></div>', '<div><p>يعد مهرجان ملبورن الدولي للكوميديا واحداً من أكثر المهرجانات الكبرى سهولة في الوصول بأستراليا. فبفضل فريق العمل المخصص لسهولة الوصول والمسارح المهيأة بشكل كامل للجميع، يمثل المهرجان نموذجاً يحتذى به للترفيه الشامل.</p><p>تتضمن المزايا الرئيسية عروضاً مترجمة بلغة الإشارة، وتوفير نصوص مكتوبة، ومقاعد وسياق كامل ميسر للكراسي المتحركة لضمان عدم تفويت المتعة لأي شخص.</p></div>', '/images/australia.png', NULL, NULL, '0', 'PUBLISHED', '2026-03-22 10:00:00', 'cat-victoria', 'admin-uuid-1', '2026-05-27 04:27:01', '2026-05-27 06:23:01', NULL),
('post-japan', 'Konichiwa, Japan: Serenity in Vibrancy', 'أهلاً بك في اليابان: سكينة في قلب الحيوية', 'konichiwa-japan', 'اليابان-سكينة-في-قلب-الحيوية-ميسرة-الوصول', 'Discovering pockets of serenity and world-class accessibility in Japan\'s vibrant cities, from Kyoto\'s temples to Tokyo\'s neon streets.', 'اكتشاف واحات من السكينة وسهولة الوصول العالمية في مدن اليابان النابضة بالحياة، من معابد كيوتو الهادئة إلى شوارع طوكيو المضيئة.', '<div><p>Japan is a masterclass in modern accessibility blending with ancient culture. Even in the busiest parts of Tokyo, the tactile paving, accessible subways, and welcoming attitude make it a favorite for travelers with limited mobility.</p><p>Experience the quiet beauty of Kyoto\'s accessible temple grounds or the high-tech inclusive facilities of Osaka.</p></div>', '<div><p>تقدم اليابان درساً في المزج بين سهولة الوصول الحديثة والثقافة العريقة. ففي أكثر أجزاء طوكيو ازدحاماً، تجعل المسارات اللمسية البارزة ووسائل النقل الميسرة والترحاب الكبير اليابان مكاناً مفضلاً للمسافرين ذوي الحركة المحدودة.</p><p>عش الهدوء الجميل لمعابد كيوتو التاريخية ميسرة الوصول أو المرافق التقنية المتطورة والشاملة في أوساكا.</p></div>', '/images/asia.png', NULL, NULL, '0', 'PUBLISHED', '2026-04-10 14:30:00', 'cat-asiapacific', 'admin-uuid-1', '2026-05-27 04:27:01', '2026-05-27 06:23:01', NULL),
('post-kosci', 'Georgia’s Krazy Kosci Klimb', 'تحدي كوسي كليمب مع جورجيا', 'georgias-krazy-kosci-klimb', 'تحدي-كوسي-كليمب-تسلق-جبل-كوسيوسكو', 'More than a fundraiser, the Krazy Kosci Klimb empowers young travelers to reach the highest peaks of Australia.', 'أكثر من مجرد حملة تبرعات، يمكّن تحدي كوزي كليمب المسافرين الشباب من تسلق أعلى قمم أستراليا الشاهقة.', '<div><p>The Krazy Kosci Klimb is a unique event that sees participants with disabilities take on the challenge of reaching the summit of Mount Kosciuszko. It\'s a testament to the fact that with the right support, the sky is the limit.</p><p>This annual event raises crucial funds while providing participants with an experience they will never forget.</p></div>', '<div><p>تحدي كوزي كليمب هو حدث فريد يرى المشاركين من ذوي الإعاقة يخوضون غمار تسلق قمة جبل كوسيوسكو، أعلى جبل في أستراليا. إنه دليل حي على أنه مع الدعم المناسب، لا حدود لما يمكن تحقيقه.</p><p>يجمع هذا الحدث السنوي تمويلاً حيوياً كبيراً بينما يقدم للمشاركين تجربة عمر لا تُنسى على الإطلاق.</p></div>', '/images/mobility.png', NULL, NULL, '0', 'PUBLISHED', '2026-02-18 09:15:00', 'cat-mobility', 'admin-uuid-1', '2026-05-27 04:27:01', '2026-05-27 06:23:01', NULL),
('post-music', 'A European summer of music and discovery', 'صيف موسيقي ممتع في أوروبا واستكشاف لا يهدأ', 'a-european-summer-of-music-and-discovery', 'صيف-موسيقي-في-اوروبا-ميسر-الوصول', 'DJ Cooper Smith shares his journey through Europe\'s most iconic music festivals, proving that music knows no limits.', 'يشارك دي جي كوبر سميث رحلته عبر أشهر المهرجانات الموسيقية في أوروبا، ليثبت أن الموسيقى لا تعرف حدوداً وعوائق.', '<div><p>DJ Cooper Smith and his mum, Bron, took on a whirlwind tour of Europe\'s festivals. From Glastonbury to the streets of Berlin, they explored how the continent is making its music scene more inclusive.</p><p>They found a world of assistive technology, dedicated viewing platforms, and a community of music lovers who embrace accessibility.</p></div>', '<div><p>خاض دي جي كوبر سميث ووالدته برون رحلة مثيرة في مهرجانات أوروبا. من غلاستونبري إلى شوارع برلين، استكشفا كيف تعمل القارة على جعل المشهد الموسيقي أكثر شمولاً وميسراً للجميع.</p><p>لقد وجدا عالمًا متكاملاً من التقنيات المساعدة، والمنصات المخصصة للمشاهدة، ومجتمعاً من عشاق الموسيقى اللذين يرحبون بسهولة الوصول.</p></div>', '/images/europe.png', NULL, NULL, '0', 'PUBLISHED', '2026-05-20 16:45:00', 'cat-europe', 'admin-uuid-1', '2026-05-27 04:27:01', '2026-05-27 06:23:01', NULL),
('post-propose', 'Top 10 Wheelchair Accessible Places to Propose', 'أفضل 10 مواقع رومانسية ميسرة لطلب الزواج بالكرسي المتحرك', 'top-10-wheelchair-accessible-places-to-propose', 'افضل-١٠-مواقع-رومانسية-ميسرة-لطلب-الزواج', 'Ensure your romantic milestone is perfect for everyone with these stunning, fully-accessible locations around the globe.', 'اضمن أن تكون هذه الخطوة الرومانسية مثالية وميسرة للجميع مع هذه المواقع الخلابة والمهيأة بالكامل حول العالم.', '<div><p>A marriage proposal is a moment to be shared and remembered. These ten locations provide the perfect backdrop, from the accessible beaches of Australia to the scenic overlooks of Europe.</p><p>We\'ve curated sites that offer both breathtaking beauty and seamless access, so you can focus on the moment.</p></div>', '<div><p>طلب الزواج هو لحظة يجب مشاركتها وتذكرها مدى الحياة. تقدم هذه المواقع العشرة الخلفية المثالية، من الشواطئ الميسرة في أستراليا إلى الإطلالات الجبلية الرائعة في أوروبا.</p><p>لقد اخترنا مواقع توفر كلاً من الجمال الخلاب والوصول السهل والآمن حتى تركز فقط على اللحظة الثمينة.</p></div>', '/images/hero-bg.png', NULL, NULL, '0', 'PUBLISHED', '2026-05-01 11:00:00', 'cat-inspiration', 'admin-uuid-1', '2026-05-27 04:27:01', '2026-05-27 06:23:01', NULL),
('post-turtle', 'On Turtle Time at Mon Repos Turtle Centre', 'في رحاب السلاحف بمركز مون ريبوس للسلاحف البحرية', 'on-turtle-time-at-mon-repos-turtle-centre', 'السلاحف-البحرية-مركز-مون-ريبوس-ميسر-الوصول', 'A magical encounter where accessible nature meets wonder. Experience the nesting and hatching of marine turtles in the Bundaberg region.', 'لقاء سحري حيث تلتقي الطبيعة الميسرة بالروعة والجمال. استمتع بمشاهدة تعشيش وتفقيس السلاحف البحرية في منطقة بندابيرج الأسترالية.', '<div><p>Kerry Heaney shares a magical moment at Mon Repos Turtle Centre in Queensland, where accessible nature meets wonder. The center is a world-class facility that offers people with mobility needs a front-row seat to the cycle of life.</p><p>From the flat, accessible walkways to the dedicated viewing areas, Mon Repos ensures that everyone can enjoy the spectacular sight of marine turtles nesting and hatching on the beach.</p></div>', '<div><p>تشارك كيري هيني لحظة سحرية في مركز مون ريبوس للسلاحف البحرية في كوينزلاند، حيث تلتقي الطبيعة الميسرة بالروعة والجمال. يعد المركز مرفقاً عالمياً يتيح للأشخاص ذوي الاحتياجات الحركية فرصة فريدة لمشاهدة دورة الحياة الطبيعية عن قرب.</p><p>من الممرات المستوية الميسرة بالكامل إلى مناطق المشاهدة المخصصة، يضمن المركز تمتع الجميع بمشاهدة تعشيش السلاحف البحرية وتفقيسها على الشاطئ دون أي عوائق.</p></div>', '/images/australia.png', NULL, NULL, '1', 'PUBLISHED', '2026-01-15 20:00:00', 'cat-queensland', 'admin-uuid-1', '2026-05-27 04:27:01', '2026-05-27 06:23:01', NULL);

-- -----------------------------------------------------
-- Table structure for table `site_settings`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `site_settings`;
CREATE TABLE `site_settings` (
  `id` int NOT NULL DEFAULT '1',
  `siteName_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Travel Without Limits',
  `siteName_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logoUrl` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logoDarkUrl` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `heroBackgroundUrl_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `heroBackgroundUrl_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metaTitle_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metaTitle_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metaDescription_en` text COLLATE utf8mb4_unicode_ci,
  `metaDescription_ar` text COLLATE utf8mb4_unicode_ci,
  `ogImage` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `faviconUrl` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primaryColor` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#2d5a88',
  `secondaryColor` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#1e3c5a',
  `accentColor` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#eaeaea',
  `backgroundColor` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ffffff',
  `textColor` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#111111',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `themeMode` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'light',
  `fontFamily` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Outfit',
  `fontFamilyBody` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Inter',
  `fontFamilyAr` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Cairo',
  `fontFamilyArBody` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Cairo',
  `fontSize` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '16px',
  `headingWeight` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '700',
  `bodyWeight` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '400',
  `letterSpacing` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `lineHeight` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1.8',
  `surfaceColor` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ffffff',
  `borderColor` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'rgba(0,0,0,0.05)',
  `nav_show_podcasts` tinyint(1) NOT NULL DEFAULT '1',
  `nav_show_stories` tinyint(1) NOT NULL DEFAULT '1',
  `nav_show_news` tinyint(1) NOT NULL DEFAULT '1',
  `nav_show_accessibility` tinyint(1) NOT NULL DEFAULT '1',
  `nav_show_contact` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `site_settings`
INSERT INTO `site_settings` (`id`, `siteName_en`, `siteName_ar`, `logoUrl`, `logoDarkUrl`, `heroBackgroundUrl_en`, `heroBackgroundUrl_ar`, `metaTitle_en`, `metaTitle_ar`, `metaDescription_en`, `metaDescription_ar`, `ogImage`, `faviconUrl`, `primaryColor`, `secondaryColor`, `accentColor`, `backgroundColor`, `textColor`, `updated_at`, `themeMode`, `fontFamily`, `fontFamilyBody`, `fontFamilyAr`, `fontFamilyArBody`, `fontSize`, `headingWeight`, `bodyWeight`, `letterSpacing`, `lineHeight`, `surfaceColor`, `borderColor`, `nav_show_podcasts`, `nav_show_stories`, `nav_show_news`, `nav_show_accessibility`, `nav_show_contact`) VALUES 
('1', 'Young Over 60', NULL, NULL, NULL, '/images/hero-bg.png', '/images/hero-bg.png', '', NULL, '', NULL, NULL, '/favicon.ico', '#2d5a88', '#1e3c5a', '#eaeaea', '#ffffff', '#111111', '2026-06-01 18:17:45', 'light', 'Outfit', 'Inter', 'Almarai', 'Almarai', '16px', '700', '400', 'normal', '1.8', '#ffffff', 'rgba(0,0,0,0.05)', '1', '1', '1', '1', '1');

-- -----------------------------------------------------
-- Table structure for table `testimonials`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `testimonials`;
CREATE TABLE `testimonials` (
  `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quote_en` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `quote_ar` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_ar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `testimonials`
INSERT INTO `testimonials` (`id`, `quote_en`, `quote_ar`, `author_en`, `author_ar`, `role_en`, `role_ar`, `image`, `created_at`) VALUES 
('test-1', 'Young Over 60 transformed the way I travel. Their detailed reports on wheel-chair accessibility in historic sites let me explore Giza with total confidence.', 'لقد غيّر موقع شباب فوق الستين طريقتي في السفر بالكامل. تقاريرهم الدقيقة والمفصلة حول سهولة حركة الكراسي المتحركة في المواقع الأثرية مكنتني من زيارة الجيزة بثقة تامة.', 'Sarah Jenkins', 'سارة جينكينز', 'Active Adventurer, UK', 'مغامرة نشطة، بريطانيا', '/uploads/admin-avatar.png', '2026-05-27 06:23:01'),
('test-2', 'Finally, a luxury travel portal that treats accessibility as a core editorial theme rather than an afterthought. The recommendations are spot on!', 'أخيراً، بوابة سفر فاخرة تتعامل مع سهولة الوصول كفكرة تحريرية محورية وجوهرية وليست كفكرة ثانوية طارئة. توصياتهم في غاية الدقة!', 'Amina Mansour', 'أمينة منصور', 'Sensory Explorer, Egypt', 'مستكشفة حسية، مصر', '/uploads/admin-avatar.png', '2026-05-27 06:23:01');

-- -----------------------------------------------------
-- Table structure for table `users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ADMIN',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `users`
INSERT INTO `users` (`id`, `email`, `password`, `name`, `bio`, `avatar`, `twitter`, `facebook`, `instagram`, `linkedin`, `website`, `role`, `created_at`, `updated_at`) VALUES 
('admin-uuid-1', 'admin@youngover60.com', '$2y$12$tanBCZuENAxM7yFBhzGIIeIJuZ9Cg0BmL2pqNp72WFRuIpD95ASRa', 'Site Admin', 'A passionate travel enthusiast dedicated to uncovering the world\'s most accessible gems. Sharing stories, tips, and guides to help everyone explore without limits.', '/uploads/admin-avatar.png', NULL, NULL, NULL, NULL, 'https://youngover60.com', 'ADMIN', '2026-05-27 04:27:01', '2026-05-27 04:27:01');

-- -----------------------------------------------------
-- Table structure for table `women_stories`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `women_stories`;
CREATE TABLE `women_stories` (
  `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `excerpt_en` text COLLATE utf8mb4_unicode_ci,
  `excerpt_ar` text COLLATE utf8mb4_unicode_ci,
  `content_en` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_ar` longtext COLLATE utf8mb4_unicode_ci,
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_text_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_text_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_en` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_ar` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `read_time` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug_en`),
  KEY `idx_stories_slug_en` (`slug_en`),
  KEY `idx_stories_slug_ar` (`slug_ar`),
  FULLTEXT KEY `ft_stories_search_en` (`title_en`,`content_en`),
  FULLTEXT KEY `ft_stories_search_ar` (`title_ar`,`content_ar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `women_stories`
INSERT INTO `women_stories` (`id`, `title_en`, `title_ar`, `slug_en`, `slug_ar`, `excerpt_en`, `excerpt_ar`, `content_en`, `content_ar`, `cover_image`, `alt_text_en`, `alt_text_ar`, `category_en`, `category_ar`, `author_en`, `author_ar`, `read_time`, `created_at`) VALUES 
('story-1', 'Solo Travel Through Cairo: A Wheelchair User’s Journal', 'السفر المنفرد في القاهرة: يوميات مستخدمة كرسي متحرك', 'solo-travel-through-cairo-wheelchair-journal', 'يوميات-مسافرة-بكرسي-متحرك-في-القاهرة', 'A firsthand account of navigating Cairo\'s historic sites, bustling streets, and the pyramids using a mobility device.', 'تقرير شخصي عن استكشاف المواقع التاريخية في القاهرة، شوارعها المزدحمة، والأهرامات العظيمة باستخدام الأجهزة المساعدة وحلول سهولة الحركة.', '<h3>A Solo Wheelchair Traveler\'s Encounter with the Pyramids</h3><p>For as long as I can remember, the Pyramids of Giza were a distant dream locked behind an intimidating barrier of sand, stairs, and steep pathways. When I finally decided to embark on a solo trip to Cairo, my wheelchair was packed alongside a healthy dose of anxiety. However, what I discovered was a city eager to open its historic gates to everyone.</p><p>Entering the Giza Plateau with a custom ramp setup and the assistance of local guides who understood my physical needs, the monumental stone structures felt closer than ever. The dry air, the golden sandstone against the clear blue sky, and the sheer majesty of the Sphinx standing guard left me breathless. It was not just about the sites, but the warmth of the people. Everywhere I went, from the busy alleys of Khan el-Khalili to the spacious halls of the new Grand Egyptian Museum, locals went out of their way to ensure I had clear sightlines and easy access.</p><h4>Top 3 Accessibility Tips for Cairo:</h4><ul><li><strong>Hire an accessible-certified local driver:</strong> Cairo\'s traffic is legendary, and having a ramp-equipped van makes all the difference.</li><li><strong>Grand Egyptian Museum (GEM):</strong> This state-of-the-art facility is built to international ADA standards, featuring elevators, tactile maps, and spacious paths.</li><li><strong>Sunset at Giza:</strong> Watch the sunset from the accessible paved viewing platform near the panorama area. It offers a majestic view of all three pyramids without needing to cross deep sand.</li></ul>', '<h3>مسافرة منفردة بالكرسي المتحرك تلتقي بالأهرامات</h3><p>لطالما كانت أهرامات الجيزة حلماً بعيد المنال، يكتنفه الخوف من الرمال والدرج والمسارات الشديدة الانحدار. عندما قررت خوض هذه الرحلة إلى القاهرة، حزمت كرسيي المتحرك ومعي الكثير من القلق. ولكن، ما اكتشفته هو مدينة تفتح أبوابها التاريخية بكل ترحيب للجميع.</p><p>بمساعدة المنصات المهيأة والمرشدين المحليين اللذين تفهموا احتياجاتي الحركية، بدت الأهرامات الحجرية العملاقة أقرب من أي وقت مضى. نسيم الجو الجاف، وأحجار الجيزة الذهبية على خلفية السماء الزرقاء، ومهابة أبو الهول، كلها تفاصيل تركتني مندهشة. لم يكن الأمر يتعلق بالمواقع التاريخية فحسب، بل بدفء الناس؛ ففي كل مكان، من خان الخليلي إلى المتحف المصري الكبير، كان الجميع يبذلون قصارى جهدهم لضمان وضوح رؤيتي وسهولة حركتي.</p>', '/images/europe.png', NULL, NULL, 'Solo Travel', 'السفر المنفرد', 'Emily Watson', 'إميلي واتسون', '6 min read', '2026-05-27 04:27:01'),
('story-2', 'Navigating the Temples of Luxor with Sensory Needs', 'استكشاف معابد الأقصر بذوي الاحتياجات الحسية', 'navigating-temples-of-luxor-sensory-needs', 'معابد-الاقصر-وذوي-الاحتياجات-الحسية', 'Luxor\'s ancient monuments are awe-inspiring, but can be sensory-heavy. Here is a guide to finding quiet moments in Karnak Temple.', 'معالم الأقصر القديمة مذهلة، ولكنها قد تكون مزدحمة حسياً. إليك دليلك للعثور على لحظات الهدوء والسلام داخل معبد الكرنك.', '<h3>Luxor Temples: Navigating Sacred Spaces with Sensory Sensitivity</h3><p>The ruins of Luxor and Karnak Temples are visual spectacles, but their colossal scale, busy tour groups, and echoey columns can be overwhelming for travellers with sensory processing sensitivities. As a neurodivergent woman, my journey to Southern Egypt was a lesson in planning, presence, and finding silence within ancient stones.</p><p>I chose to visit the Karnak Temple complex at sunrise. Entering before the crowds, the great Hypostyle Hall felt like a quiet forest of stone. The early morning light cast soft shadows across the giant pillars, each carved with stories of pharaohs and gods. By timing my visits to coincide with typical lunch hours, I managed to explore the Luxor Temple in near solitude. It allowed me to appreciate the intricate hieroglyphics and connect with the spiritual history of the place without the sensory clutter of megaphones and chatter.</p><h4>My Recommendations for Sensory Balance:</h4><ul><li><strong>Noise-canceling headphones:</strong> Essential for busy entrance plazas and bazaar areas.</li><li><strong>Go early or late:</strong> Aim for 6:00 AM or 5:00 PM. Not only is the weather cooler, but the crowd level drops by 80%.</li><li><strong>Take a Felucca:</strong> A traditional sailboat ride on the Nile at sunset provides the perfect sensory reset after a long day of touring.</li></ul>', '<h3>معابد الأقصر: التنقل في المساحات المقدسة مع الحساسية الحسية</h3><p>أطلال معابد الأقصر والكرنك هي تحف بصرية رائعة، ولكن حجمها الضخم ومجموعاتها المزدحمة قد يسبب ضغطاً حسياً للمسافرين الذين يعانون من فرط الحساسية الحسية. كوني امرأة من ذوي الاختلاف العصبي، كانت رحلتي إلى جنوب مصر درساً في التخطيط والبحث عن الهدوء وسط الحجارة القديمة.</p><p>اخترت زيارة مجمع معابد الكرنك عند شروق الشمس. بالدخول قبل بدء المجموعات السياحية، بدت قاعة الأعمدة الكبرى كغابة هادئة من الحجر. ألقى ضوء الصباح الباكر ظلالاً ناعمة على الأعمدة الضخمة، المنقوشة بقصص الفراعنة والآلهة. بتنسيق أوقات الزيارة لتتزامن مع وقت الغداء السياحي المعتاد، تمكنت من استكشاف معبد الأقصر في هدوء تام.</p>', '/images/australia.png', NULL, NULL, 'Accessible Adventure', 'مغامرة ميسرة', 'Sarah Jenkins', 'سارة جينكينز', '5 min read', '2026-05-27 04:27:01'),
('story-3', 'A Deaf Traveller’s Guide to the Vibrant Bazaars of Khan el-Khalili', 'دليل المسافرة الكفيفة إلى أسواق خان الخليلي المزدحمة', 'deaf-traveller-guide-khan-el-khalili', 'دليل-المسافرة-الكفيفة-خان-الخليلي', 'Exploring the sights, smells, and textures of Cairo\'s famous marketplace through the lens of visual and tactile connection.', 'استكشاف المناظر والروائح والأقمشة في أشهر أسواق القاهرة القديمة من منظور التواصل البصري الحسي واللمسي الفريد.', '<h3>Sounds and Scents of Cairo: A Blind Traveler\'s Perspective</h3><p>Most travel brochures focus on the sights of Egypt, but the true essence of Cairo lies in its auditory and olfactory landscape. Exploring the historic Islamic Cairo as a blind woman opened my senses to a symphony of sights unheard and textures unfelt.</p><p>Navigating the medieval gates of Bab Zuwayla, the air smelled of roasted cardamom, fresh mint, and the rich earthy scent of tanned leather. Walking through Al-Muizz Street, the voice of the muezzin calling for prayer echoed from towering minarets, wrapping the ancient street in a blanket of peace. Hand-carved brass lamps felt cold and detailed under my fingertips, and the warmth of a freshly baked Aish Baladi (Egyptian flatbread) handed to me by a smiling baker was a moment of pure connection. Cairo does not require sight to be seen; it demands that you listen, smell, and touch.</p><h4>Key Insights for Visually Impaired Visitors:</h4><ul><li><strong>Tactile exploration:</strong> Many museums allow supervised touching of selected replica statues. Always ask for \'tactile guides\'.</li><li><strong>Scent mapping:</strong> Khan el-Khalili\'s spice section is a wonderful sensory journey. Local merchants are proud to let you sample incense, spices, and perfumes.</li><li><strong>Companion guides:</strong> Work with specialized operators who are trained in descriptive narration of architectural details.</li></ul>', '<h3>روائح وأصوات القاهرة: منظور مسافرة كفيفة</h3><p>تركز معظم الكتيبات السياحية على المشاهد المرئية، لكن الجوهر الحقيقي للقاهرة يكمن في أصواتها وروائحها الفريدة. استكشاف القاهرة الإسلامية كمسافرة كفيفة فتح حواسي على سمفونية متناغمة من الروائح التي لا نراها والملامس التي نتحسسها.</p><p>عند المشي بجوار بوابة باب زويلة، تفوح في الهواء رائحة الهيل المحمص والنعناع الطازج ورائحة الجلود العتيقة الدافئة. وخلال السير في شارع المعز، يتردد صدى صوت المؤذن داعياً للصلاة من المآذن الشاهقة، ليغمر الشارع القديم بعباءة من السلام والسكينة. مصابيح النحاس المنقوشة يدوياً كانت باردة ومليئة بالتفاصيل تحت أطراف أصابعي.</p>', '/images/hero-bg.png', NULL, NULL, 'Cultural Journeys', 'رحلات ثقافية', 'Amina Mansour', 'أمينة منصور', '7 min read', '2026-05-27 04:27:01');

SET FOREIGN_KEY_CHECKS = 1;
