<?php
/**
 * Add Tampakan Content Creators Association (TCCA) + update Gerame's profile
 * Run: php database/add-tcca.php
 */

require_once __DIR__ . '/../includes/config-loader.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = db();

echo "=== Adding TCCA Organization ===\n\n";

// ── Update Gerame's avatar ──────────────────────────────────────
$pdo->prepare("UPDATE users SET avatar = ? WHERE email = ?")
    ->execute(['/assets/img/profiles/gerame-paquera.jpg', 'gerame.paquera@tampakan.com']);
echo "✓ Updated Gerame Paquera's profile photo\n";

// ── Add "Community Organizations" category if it doesn't exist ──
$cat_exists = $pdo->query("SELECT id FROM categories WHERE slug = 'community-organizations'")->fetchColumn();
if (!$cat_exists) {
    $pdo->prepare("INSERT INTO categories (name, slug, type, icon, sort_order, is_active) VALUES (?,?,?,?,?,1)")
        ->execute(['Community Organizations', 'community-organizations', 'business', '🤝', 19]);
    $cat_id = $pdo->lastInsertId();
    echo "✓ Created category: Community Organizations (🤝)\n";
} else {
    $cat_id = $cat_exists;
    echo "⊘ Category 'Community Organizations' already exists (id={$cat_id})\n";
}

// ── Add TCCA as a listing ──────────────────────────────────────
$exists = $pdo->query("SELECT COUNT(*) FROM listings WHERE slug = 'tampakan-content-creators-association'")->fetchColumn();
if (!$exists) {
    $gerame_id = $pdo->query("SELECT id FROM users WHERE email = 'gerame.paquera@tampakan.com'")->fetchColumn();

    $cols = 'category_id,owner_id,type,name,slug,description,address,barangay,city,province,lat,lng,phone,email,website,facebook,hours,shopee_link,lazada_link,amazon_link,food_ordering_link,status,is_featured,is_spotlight';
    $placeholders = implode(',', array_fill(0, 24, '?'));
    $stmt = $pdo->prepare("INSERT INTO listings ({$cols}) VALUES ({$placeholders})");
    $stmt->execute([
        $cat_id,            // category_id = Community Organizations
        $gerame_id,         // owner_id = Gerame
        'business',         // type
        'Tampakan Content Creators Association (TCCA)',  // name
        'tampakan-content-creators-association',         // slug
        "The Tampakan Content Creators Association (TCCA) is a community organization of vloggers, bloggers, and social media content creators dedicated to showcasing the beauty, culture, and people of Tampakan, South Cotabato.\n\n" .
        "🎯 **Mission:** To use digital media and community engagement to promote Tampakan's businesses, tourism, and culture while uplifting local communities through creative projects and collaborations.\n\n" .
        "👤 **President:** Gerame M. Paquera\n\n" .
        "The TCCA organizes community projects, content creation workshops, local tourism promotion campaigns, and collaborative events that bring together creators from Tampakan and the surrounding area. Members include YouTubers, TikTokers, Facebook content creators, and bloggers who are passionate about telling Tampakan's story.\n\n" .
        "Want to join? Reach out on our Facebook page or contact President Gerame Paquera directly. Whether you're an aspiring creator or an established influencer, there's a place for you in the TCCA! 🎬📸🎤",
        'Poblacion, Tampakan',    // address
        'Poblacion',              // barangay
        'Tampakan',               // city
        'South Cotabato',         // province
        6.4110,                   // lat
        125.0435,                 // lng
        null,                     // phone
        'tcca.tampakan@gmail.com', // email
        null,                     // website
        'https://www.facebook.com/gerame.paquera.5',  // facebook (org page / president's page)
        null,                     // hours (community org, no set hours)
        null,                     // shopee_link
        null,                     // lazada_link
        null,                     // amazon_link
        null,                     // food_ordering_link
        'active',                 // status
        1,                        // is_featured = YES (community spotlight)
        0                         // is_spotlight
    ]);
    $tcca_listing_id = $pdo->lastInsertId();
    echo "✓ Added: Tampakan Content Creators Association (TCCA) — Featured\n";
    echo "  President: Gerame M. Paquera\n";

    // Add the TCCA logo as the primary image
    $pdo->prepare("INSERT INTO listing_images (listing_id, image_path, alt_text, is_primary, sort_order) VALUES (?,?,?,?,?)")
        ->execute([
            $tcca_listing_id,
            '/assets/img/community/tcca-logo.jpg',
            'Tampakan Content Creators Association logo — camera, phone, microphone with mountain backdrop',
            1,
            0
        ]);
    echo "  + Logo image linked\n";

    // Add a review/endorsement
    $review_stmt = $pdo->prepare("INSERT INTO reviews (listing_id, user_name, rating, comment, is_approved) VALUES (?,?,?,?,1)");
    $review_stmt->execute([$tcca_listing_id, 'Mayor\'s Office Tampakan', 5, 'Proud to support the TCCA! These young content creators are doing amazing work putting Tampakan on the digital map. Keep it up!']);
    $review_stmt->execute([$tcca_listing_id, 'Ate Maring', 5, 'Ang galing ng mga members ng TCCA! Lagi silang tumutulong sa community events. Mabuhay ang Tampakan vloggers! 🎉']);
    echo "  + 2 endorsement reviews added\n";
} else {
    echo "⊘ TCCA listing already exists, skipping\n";
}

// ── Also update the blog post about Gerame to mention TCCA presidency ──
$pdo->prepare("UPDATE posts SET body = ? WHERE slug = 'meet-gerame-paquera-tampakan-vlogger'")
    ->execute([
        "Gerame M. Paquera is a proud son of Tampakan, South Cotabato, and serves as the **President of the Tampakan Content Creators Association (TCCA)**.\n\n" .
        "Through vlogs and social media, Gerame showcases the beauty of Tampakan — from its stunning waterfalls and mountain views to the everyday hustle of our markets and barangays. As head of the TCCA, he leads a team of passionate content creators who are putting Tampakan on the digital map.\n\n" .
        "The TCCA organizes community projects, tourism promotion campaigns, and workshops that help aspiring creators develop their skills. Under Gerame's leadership, the association has grown into a vibrant community of YouTubers, TikTokers, and Facebook creators.\n\n" .
        "**Follow Gerame on Facebook:** [facebook.com/gerame.paquera.5](https://www.facebook.com/gerame.paquera.5)\n\n" .
        "**Learn more about TCCA:** [View the TCCA listing](/business/tampakan-content-creators-association)\n\n" .
        "Want to be featured on Tampakan Directory? [Submit your profile today!](/submit)"
    ]);
echo "✓ Updated Gerame's blog post with TCCA presidency info\n";

echo "\n✓ All done! Refresh your browser.\n";
echo "  - TCCA: /business/tampakan-content-creators-association\n";
echo "  - Gerame's post: /community (updated with TCCA president role)\n\n";
