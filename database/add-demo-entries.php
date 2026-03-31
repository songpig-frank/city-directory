<?php
/**
 * Add Tito Jojo's Sari-Sari Store + Gerame Paquera (vlogger)
 * Run: php database/add-demo-entries.php
 */

require_once __DIR__ . '/../includes/config-loader.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = db();

echo "=== Adding Demo Entries ===\n\n";

// ── 1. Tito Jojo's Sari-Sari Store ──────────────────────────────
$cols = 'category_id,owner_id,type,name,slug,description,address,barangay,city,province,lat,lng,phone,email,website,facebook,hours,shopee_link,lazada_link,amazon_link,food_ordering_link,status,is_featured,is_spotlight';

// Check if already exists
$exists = $pdo->query("SELECT COUNT(*) FROM listings WHERE slug = 'tito-jojos-sari-sari-store'")->fetchColumn();
if (!$exists) {
    $placeholders = implode(',', array_fill(0, 24, '?'));
    $stmt = $pdo->prepare("INSERT INTO listings ({$cols}) VALUES ({$placeholders})");
    $stmt->execute([
        8,              // category_id = Grocery & Sari-Sari
        null,           // owner_id
        'business',     // type
        "Tito Jojo's Sari-Sari Store",  // name
        'tito-jojos-sari-sari-store',   // slug
        "Your friendly neighborhood sari-sari store in Tampakan! We carry snacks, canned goods, softdrinks, personal care, school supplies, and household essentials. Load available (Smart, Globe, TNT, TM). GCash cash-in/out accepted. Open early and late — because tito never sleeps! Come visit Tito Jojo for everything you need, tindahan ng bayan! 🏪",
        'Purok 3, National Highway, Poblacion',  // address
        'Poblacion',    // barangay
        'Tampakan',     // city
        'South Cotabato', // province
        6.4115,         // lat (near town center)
        125.0432,       // lng
        '0917-456-7890', // phone
        null,           // email
        null,           // website
        'https://www.facebook.com/gerame.paquera.5', // facebook (placeholder for now)
        '{"mon":{"open":"05:30","close":"22:00"},"tue":{"open":"05:30","close":"22:00"},"wed":{"open":"05:30","close":"22:00"},"thu":{"open":"05:30","close":"22:00"},"fri":{"open":"05:30","close":"22:00"},"sat":{"open":"05:30","close":"22:00"},"sun":{"open":"06:00","close":"21:00"}}',
        null,           // shopee_link
        null,           // lazada_link
        null,           // amazon_link
        null,           // food_ordering_link
        'active',       // status
        0,              // is_featured
        0               // is_spotlight
    ]);
    echo "✓ Added: Tito Jojo's Sari-Sari Store\n";

    // Add a review
    $listing_id = $pdo->lastInsertId();
    $review_stmt = $pdo->prepare("INSERT INTO reviews (listing_id, user_name, rating, comment, is_approved) VALUES (?,?,?,?,1)");
    $review_stmt->execute([$listing_id, 'Ate Rosario', 5, 'Tito Jojo ang bait! Always has stock and open late. Best sari-sari store sa Poblacion!']);
    $review_stmt->execute([$listing_id, 'Kevin Magallanes', 4, 'Complete ang items, may GCash pa. Malapit sa school so very convenient.']);
    echo "  + 2 reviews added\n";
} else {
    echo "⊘ Tito Jojo's Sari-Sari Store already exists, skipping\n";
}

// ── 2. Gerame Paquera — Community Vlogger Profile ─────────────
// First, create a user account for Gerame
$user_exists = $pdo->query("SELECT COUNT(*) FROM users WHERE email = 'gerame.paquera@tampakan.com'")->fetchColumn();
if (!$user_exists) {
    $hash = password_hash('gerame2024', PASSWORD_BCRYPT, ['cost' => 12]);
    $pdo->prepare("INSERT INTO users (name, email, password_hash, role, bio, social_links) VALUES (?,?,?,?,?,?)")
        ->execute([
            'Gerame Paquera',
            'gerame.paquera@tampakan.com',
            $hash,
            'owner',
            'Tampakan community vlogger and content creator. Showcasing the beauty of Tampakan, its people, and culture. Follow my journey! 🎥🌿',
            json_encode([
                'facebook' => 'https://www.facebook.com/gerame.paquera.5',
                'youtube' => null,
                'tiktok' => null
            ])
        ]);
    echo "✓ Created user: Gerame Paquera (gerame.paquera@tampakan.com)\n";
}

// Create a blog post from Gerame
$post_exists = $pdo->query("SELECT COUNT(*) FROM posts WHERE slug = 'meet-gerame-paquera-tampakan-vlogger'")->fetchColumn();
if (!$post_exists) {
    $author_id = $pdo->query("SELECT id FROM users WHERE email = 'gerame.paquera@tampakan.com'")->fetchColumn();
    $pdo->prepare("INSERT INTO posts (author_id, title, slug, excerpt, body, status, published_at) VALUES (?,?,?,?,?,?,datetime('now'))")
        ->execute([
            $author_id,
            'Meet Gerame Paquera — Tampakan Community Vlogger',
            'meet-gerame-paquera-tampakan-vlogger',
            'Get to know Gerame Paquera, one of Tampakan\'s rising content creators helping showcase our beautiful municipality to the world!',
            "Gerame Paquera is a proud son of Tampakan, South Cotabato, and one of the community's most active content creators. Through vlogs and social media, Gerame showcases the beauty of Tampakan — from its stunning waterfalls and mountain views to the everyday hustle of our markets and barangays.\n\nAs a member of the Tampakan Vloggers Organization, Gerame is committed to community projects that uplift local businesses and create positive impact. His content covers everything from local food trips to cultural events and community initiatives.\n\n**Follow Gerame on Facebook:** [facebook.com/gerame.paquera.5](https://www.facebook.com/gerame.paquera.5)\n\nWant to be featured on Tampakan Directory? [Submit your profile today!](/submit)",
            'published'
        ]);
    echo "✓ Blog post created: 'Meet Gerame Paquera — Tampakan Community Vlogger'\n";
}

echo "\n✓ Done! Refresh your browser to see the new entries.\n";
echo "  - Business: /business/tito-jojos-sari-sari-store\n";
echo "  - Blog: /community (Gerame's profile post)\n\n";
