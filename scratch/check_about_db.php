<?php
// Direct DB check using same credentials as web server
$pdo = new PDO("mysql:host=localhost;port=3306;dbname=u402417573_travelfinal", 'root', '');
$stmt = $pdo->query("SELECT id, updated_at, substring(content_en, 1, 400) as content_preview FROM custom_pages WHERE slug_en = 'about-us'");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Updated: " . $row['updated_at'] . "\n";
echo "Content: " . $row['content_preview'] . "\n";
