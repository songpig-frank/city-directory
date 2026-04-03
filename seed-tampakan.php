<?php
/**
 * Add Real Tampakan Businesses and Content Creators
 * Secure: Requires a secret key to run on production.
 */
require_once __DIR__ . '/includes/config-loader.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

// Allow CLI or a secret key in URL
$secret_key = 'tampakan2026';
if (php_sapi_name() !== 'cli' && ($_GET['key'] ?? '') !== $secret_key) {
    die("Access Denied: Please provide the correct key (?key=XXXX) to run this script.");
}

$pdo = db();
echo "<pre>=== Adding Real Tampakan Entries ===\n\n";

$cols = 'category_id,owner_id,type,name,slug,description,address,barangay,city,province,lat,lng,phone,facebook,status,is_featured,is_spotlight';
$placeholders = implode(',', array_fill(0, 17, '?'));
$stmt = $pdo->prepare("INSERT INTO listings ({$cols}) VALUES ({$placeholders})");

function get_cat_id($slug, $pdo) {
    $id = $pdo->query("SELECT id FROM categories WHERE slug = '$slug'")->fetchColumn();
    return $id ?: 1; // fallback to 1
}

$cat_resto = get_cat_id('restaurants-cafes', $pdo);
$cat_tour = get_cat_id('natural-attractions', $pdo);
$cat_resort = get_cat_id('springs-resorts', $pdo);
$cat_creator = get_cat_id('vloggers', $pdo) ?: get_cat_id('creators', $pdo) ?: 1; 

$entries = [
    // ── CREATORS ──────────────────────────────────────────────────
    [
        'cat' => $cat_creator, 'type' => 'creator', 'name' => 'Tampakan Content Creators', 'slug' => 'tampakan-content-creators',
        'desc' => "Official Facebook page for Tampakan Content Creators. Highlighting the collective creativity of our local digital talents.",
        'fb' => 'https://www.facebook.com/profile.php?id=61576653280424'
    ],
    [
        'cat' => $cat_creator, 'type' => 'creator', 'name' => 'Gerame M. Paquera (Kuya Ram)', 'slug' => 'gerame-m-paquera',
        'desc' => "Tampakan community vlogger and content creator. Showcasing the beauty of Tampakan, its people, and culture.",
        'fb' => 'https://www.facebook.com/profile.php?id=100063625442544' // Kuya Ram Facebook
    ],
    [
        'cat' => $cat_creator, 'type' => 'creator', 'name' => 'Toto Mondragon Belleza', 'slug' => 'toto-mondragon-belleza',
        'desc' => "Local public figure and community creator sharing the everyday life and vibrant spirit of Tampakan, South Cotabato.",
        'fb' => 'https://www.facebook.com/toto.mondragon.belleza'
    ],
    [
        'cat' => $cat_creator, 'type' => 'creator', 'name' => 'Connie Tabano', 'slug' => 'connie-tabano',
        'desc' => "Tampakan personality and creator connecting the community through engaging posts, stories, and local updates.",
        'fb' => 'https://www.facebook.com/jhocontabanoadto27'
    ],

    // ── RESTAURANTS / DINING ──────────────────────────────────────
    [
        'cat' => $cat_resto, 'type' => 'business', 'name' => 'Kolon Cafe', 'slug' => 'kolon-cafe',
        'desc' => "Nestled in the mountains of Lampitak, Kolon Cafe offers an unforgettable experience with a sea of clouds and delicious coffee. A must-visit Instagrammable spot in Tampakan!",
        'fb' => ''
    ],
    [
        'cat' => $cat_resto, 'type' => 'business', 'name' => "Santa's Clubhouse", 'slug' => 'santas-clubhouse',
        'desc' => "A widely recognized local dining establishment featuring great food and a welcoming atmosphere.",
        'fb' => ''
    ],
    [
        'cat' => $cat_resto, 'type' => 'business', 'name' => "Highway Star Grill", 'slug' => 'highway-star-grill',
        'desc' => "Filipino and Asian fusion, fast food, and nightcap drinks. A highway favorite!",
        'fb' => ''
    ],

    // ── TOURISM & ATTRACTIONS ──────────────────────────────────────
    [
        'cat' => $cat_tour, 'type' => 'tourism', 'name' => 'Kalon Barak Skyline Ridge', 'slug' => 'kalon-barak',
        'desc' => "Breathtaking level views and the legendary sea of clouds. South Cotabato's pride.",
        'fb' => ''
    ],
    [
        'cat' => $cat_tour, 'type' => 'tourism', 'name' => 'Kolondatal Nature Park', 'slug' => 'kolondatal-nature-park',
        'desc' => "Escape the heat and enjoy the lush green mountains and hiking trails.",
        'fb' => ''
    ],
    [
        'cat' => $cat_resort, 'type' => 'tourism', 'name' => 'Taniongon Spring Resort', 'slug' => 'taniongon-spring-resort',
        'desc' => "Cool mountain spring water in public and private pools. Perfect for family outings.",
        'fb' => ''
    ],
];

foreach ($entries as $e) {
    $exists = $pdo->query("SELECT COUNT(*) FROM listings WHERE slug = '{$e['slug']}'")->fetchColumn();
    if (!$exists) {
        $stmt->execute([
            $e['cat'],
            null, // owner_id
            $e['type'],
            $e['name'],
            $e['slug'],
            $e['desc'], // description
            'Tampakan', // address
            null, // barangay
            'Tampakan', // city
            'South Cotabato', // province
            '6.4283', // lat
            '124.9478', // lng
            null, // phone
            $e['fb'] ?? '', // facebook
            'active', // status
            1, // is_featured
            1  // is_spotlight
        ]);
        echo "✓ Added: {$e['name']}\n";
    } else {
        echo "⊘ Skipping: {$e['name']} (Already exists)\n";
    }
}

echo "\n✓ Success! Visit the homepage to see your entries.\n</pre>";
