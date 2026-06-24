<?php
// scratch/test_dutch_db.php
// Programmatic test script to verify Dutch database CRUD operations, slugify diacritic conversion, and translation fallbacks.

define('PATH_ROOT', dirname(__DIR__));
require_once PATH_ROOT . '/includes/config.php';
require_once PATH_ROOT . '/includes/helpers.php';
require_once PATH_ROOT . '/classes/Database.php';
require_once PATH_ROOT . '/classes/PostManager.php';

header('Content-Type: text/plain');

echo "=== Dutch Language Integration Verification ===\n\n";

// 1. Verify slugify accented characters conversion
echo "1. Testing Dutch slugification...\n";
$accentedTitle = "Toegankelijkheid & Reizen in België en Curaçao 2026!";
$expectedSlug = "toegankelijkheid-reizen-in-belgie-en-curacao-2026";
$actualSlug = slugify($accentedTitle);

echo "Input:  \"$accentedTitle\"\n";
echo "Output: \"$actualSlug\"\n";
if ($actualSlug === $expectedSlug) {
    echo "✔ Success: Accented Dutch characters and spaces converted safely.\n\n";
} else {
    echo "❌ Failure: Expected \"$expectedSlug\", got \"$actualSlug\".\n\n";
}

// 2. Database connection & preparation
try {
    $pm = new PostManager();
    $db = Database::getInstance()->getConnection();
} catch (Exception $e) {
    die("❌ DB connection failed: " . $e->getMessage() . "\n");
}

// Find a valid category
$categories = $pm->getCategories();
if (empty($categories)) {
    die("❌ No categories found in database. Cannot run test.\n");
}
$catId = $categories[0]['id'];
echo "Using category: " . $categories[0]['name_en'] . " (ID: $catId)\n\n";

// Generate a unique ID
$testId = bin2hex(random_bytes(16));

// 3. Test post creation
echo "2. Creating test post with Dutch content...\n";
$postData = [
    'id' => $testId,
    'title_en' => 'Test Post EN',
    'title_ar' => 'Test Post AR',
    'title_nl' => 'Test Post NL - Curaçao',
    'slug_en' => 'test-post-en-' . time(),
    'slug_ar' => 'test-post-ar-' . time(),
    'slug_nl' => 'test-post-nl-' . time(),
    'excerpt_en' => 'Teaser EN',
    'excerpt_ar' => 'Teaser AR',
    'excerpt_nl' => 'Teaser NL',
    'content_en' => '<p>Content EN</p>',
    'content_ar' => '<p>Content AR</p>',
    'content_nl' => '<p>Content NL</p>',
    'coverImage' => '/uploads/test.png',
    'alt_text_en' => 'Alt EN',
    'alt_text_ar' => 'Alt AR',
    'alt_text_nl' => 'Alt NL',
    'isFeatured' => 1,
    'status' => 'DRAFT',
    'categoryId' => $catId
];

if ($pm->createPost($postData)) {
    echo "✔ Success: Test post created in database.\n";
} else {
    die("❌ Failure: Failed to create test post.\n");
}

// 4. Verify post retrieval and Dutch fields matching
echo "\n3. Retrieving post and verifying Dutch fields...\n";
$retrieved = $pm->getPostById($testId);

if ($retrieved) {
    $fieldsToVerify = ['title_nl', 'slug_nl', 'excerpt_nl', 'content_nl', 'alt_text_nl'];
    $allOk = true;
    foreach ($fieldsToVerify as $field) {
        if ($retrieved[$field] === $postData[$field]) {
            echo "✔ Field '$field' matches: " . $retrieved[$field] . "\n";
        } else {
            echo "❌ Field '$field' mismatch: expected \"" . $postData[$field] . "\", got \"" . $retrieved[$field] . "\"\n";
            $allOk = false;
        }
    }
    if ($allOk) {
        echo "✔ Success: All Dutch fields correctly loaded.\n\n";
    } else {
        echo "❌ Failure: Dutch field mismatch detected.\n\n";
    }
} else {
    die("❌ Failure: Could not retrieve post from database.\n");
}

// 5. Test post update
echo "4. Updating test post Dutch content...\n";
$updateData = $postData;
$updateData['title_nl'] = 'Updated Dutch Title';
$updateData['content_nl'] = '<p>Updated Dutch Content</p>';

if ($pm->updatePost($testId, $updateData)) {
    echo "✔ Success: Test post updated in database.\n";
    
    $updated = $pm->getPostById($testId);
    if ($updated['title_nl'] === 'Updated Dutch Title' && $updated['content_nl'] === '<p>Updated Dutch Content</p>') {
        echo "✔ Success: Updated fields correctly matched.\n\n";
    } else {
        echo "❌ Failure: Updated fields mismatch.\n\n";
    }
} else {
    echo "❌ Failure: Failed to update post.\n\n";
}

// 6. Verify fallback behavior order (NL -> EN -> AR)
echo "5. Verifying localization helper t() fallback...\n";
// Let's mock a temporary translation config with CURRENT_LANG = 'nl'
if (!defined('CURRENT_LANG')) {
    define('CURRENT_LANG', 'nl');
}

// Scenario A: Dutch value is present
echo "Scenario A (NL present): ";
$valA = t($updated, 'title');
if ($valA === 'Updated Dutch Title') {
    echo "✔ Passed (value is: \"$valA\")\n";
} else {
    echo "❌ Failed (value is: \"$valA\")\n";
}

// Scenario B: Dutch value is empty, falls back to English
echo "Scenario B (NL empty, fallback to EN): ";
$postWithEmptyNl = $updated;
$postWithEmptyNl['title_nl'] = '';
$valB = t($postWithEmptyNl, 'title');
if ($valB === 'Test Post EN') {
    echo "✔ Passed (value is: \"$valB\")\n";
} else {
    echo "❌ Failed (value is: \"$valB\")\n";
}

// Scenario C: Dutch and English empty, falls back to Arabic
echo "Scenario C (NL & EN empty, fallback to AR): ";
$postWithEmptyNlAndEn = $updated;
$postWithEmptyNlAndEn['title_nl'] = '';
$postWithEmptyNlAndEn['title_en'] = '';
$valC = t($postWithEmptyNlAndEn, 'title');
if ($valC === 'Test Post AR') {
    echo "✔ Passed (value is: \"$valC\")\n";
} else {
    echo "❌ Failed (value is: \"$valC\")\n";
}

// Clean up
echo "\n6. Cleaning up test post...\n";
if ($pm->deletePost($testId, false)) {
    echo "✔ Success: Test post deleted from database.\n";
} else {
    echo "❌ Warning: Failed to clean up test post.\n";
}

echo "\nVerification complete.\n";
