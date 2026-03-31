<?php
/**
 * Phase 3 Seeder: Tourist Spots & Vloggers
 * Run: php database/seed-phase3.php
 */

require_once __DIR__ . '/../includes/config-loader.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = db();
echo "=== Seeding Phase 3: Creators & Tourism ===\n\n";

$creators = [
    ['name' => 'The Tampakan Explorer', 'slug' => 'the-tampakan-explorer', 'desc' => 'Discovering hidden gems across South Cotabato.', 'yt' => 'https://youtube.com/@tampakanexplorer', 'ig' => 'https://instagram.com/tampakanexplorer'],
    ['name' => 'Chef Maria\'s Kitchen', 'slug' => 'chef-marias-kitchen', 'desc' => 'Cooking up the best local delicacies and sharing recipes!', 'yt' => 'https://youtube.com/@chefmaria', 'ig' => null],
    ['name' => 'Tech Talk South Cotabato', 'slug' => 'tech-talk-sc', 'desc' => 'Your local source for tech reviews and unboxings.', 'yt' => 'https://youtube.com/@techtalksc', 'ig' => 'https://instagram.com/techtalksc'],
];

$creator_cat = $pdo->query("SELECT id FROM categories WHERE slug = 'creative-vloggers'")->fetchColumn();

if ($creator_cat) {
    foreach ($creators as $c) {
        $exists = $pdo->query("SELECT COUNT(*) FROM listings WHERE slug = '{$c['slug']}'")->fetchColumn();
        if (!$exists) {
            $stmt = $pdo->prepare("INSERT INTO listings (category_id, type, name, slug, description, youtube, instagram, status) VALUES (?, 'creator', ?, ?, ?, ?, ?, 'active')");
            $stmt->execute([$creator_cat, $c['name'], $c['slug'], $c['desc'], $c['yt'], $c['ig']]);
            echo "✓ Added Creator: {$c['name']}\n";
        }
    }
}

$tourism = [
    ['name' => 'Mountain View Farm Resort', 'slug' => 'mountain-view-farm', 'desc' => 'A serene getaway with majestic mountain views.', 'lat' => 6.42, 'lng' => 124.99],
    ['name' => 'Hidden Springs of Bong Mal', 'slug' => 'hidden-springs', 'desc' => 'Crystal clear natural springs wrapped in lush forest.', 'lat' => 6.44, 'lng' => 125.01],
    ['name' => 'Sunrise Peak View Deck', 'slug' => 'sunrise-peak', 'desc' => 'The best spot to catch the morning sun over Tampakan.', 'lat' => 6.46, 'lng' => 125.05],
];

// Fallback to general category if specific tourism ones aren't available
$tourism_cat = $pdo->query("SELECT id FROM categories WHERE type = 'tourism' LIMIT 1")->fetchColumn() ?: 1;

foreach ($tourism as $t) {
    $exists = $pdo->query("SELECT COUNT(*) FROM listings WHERE slug = '{$t['slug']}'")->fetchColumn();
    if (!$exists) {
        $stmt = $pdo->prepare("INSERT INTO listings (category_id, type, name, slug, description, lat, lng, status) VALUES (?, 'tourism', ?, ?, ?, ?, ?, 'active')");
        $stmt->execute([$tourism_cat, $t['name'], $t['slug'], $t['desc'], $t['lat'], $t['lng']]);
        echo "✓ Added Tourism Spot: {$t['name']}\n";
    }
}

// Ensure the new pages are active right away by publishing a generic blog post too
$user_id = $pdo->query("SELECT id FROM users LIMIT 1")->fetchColumn() ?: 1;
$post_exists = $pdo->query("SELECT COUNT(*) FROM posts WHERE slug = 'welcome-to-tampakan-directory'")->fetchColumn();
if (!$post_exists) {
    $pdo->prepare("INSERT INTO posts (author_id, title, slug, excerpt, body, status, published_at) VALUES (?, 'Welcome to Tampakan Directory', 'welcome-to-tampakan-directory', 'Launch announcement', 'Welcome to the official Tampakan Directory!', 'published', datetime('now'))")->execute([$user_id]);
    echo "✓ Added Welcome Blog Post\n";
}

echo "\nSeeding complete!\n";
