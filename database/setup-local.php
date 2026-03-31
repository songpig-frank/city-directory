<?php
/**
 * CityDirectory — SQLite Local Setup + Demo Data
 * Creates tables and seeds demo listings for local preview.
 * Usage: php database/setup-local.php
 */

require_once __DIR__ . '/../includes/config-loader.php';
require_once __DIR__ . '/../includes/db.php';

echo "=== CityDirectory Local Setup (SQLite) ===\n\n";

$pdo = db();

// ── Create Tables ──────────────────────────────────────────────
$pdo->exec("
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    role TEXT DEFAULT 'owner',
    avatar TEXT,
    bio TEXT,
    phone TEXT,
    social_links TEXT,
    is_active INTEGER DEFAULT 1,
    last_login TEXT,
    created_at TEXT DEFAULT (datetime('now')),
    updated_at TEXT DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    slug TEXT UNIQUE NOT NULL,
    description TEXT,
    type TEXT DEFAULT 'business',
    icon TEXT,
    sort_order INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1,
    created_at TEXT DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS listings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    category_id INTEGER NOT NULL,
    owner_id INTEGER,
    type TEXT DEFAULT 'business',
    name TEXT NOT NULL,
    slug TEXT UNIQUE NOT NULL,
    description TEXT,
    address TEXT,
    barangay TEXT,
    city TEXT,
    province TEXT,
    lat REAL,
    lng REAL,
    phone TEXT,
    email TEXT,
    website TEXT,
    facebook TEXT,
    instagram TEXT,
    youtube TEXT,
    tiktok TEXT,
    hours TEXT,
    shopee_link TEXT,
    lazada_link TEXT,
    amazon_link TEXT,
    food_ordering_link TEXT,
    property_type TEXT,
    property_sqm REAL,
    property_price REAL,
    property_terms TEXT,
    broker_license TEXT,
    status TEXT DEFAULT 'active',
    is_featured INTEGER DEFAULT 0,
    is_spotlight INTEGER DEFAULT 0,
    featured_until TEXT,
    spotlight_until TEXT,
    views INTEGER DEFAULT 0,
    expires_at TEXT,
    renewal_token TEXT,
    created_at TEXT DEFAULT (datetime('now')),
    updated_at TEXT DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS listing_images (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    listing_id INTEGER NOT NULL,
    image_path TEXT NOT NULL,
    alt_text TEXT,
    is_primary INTEGER DEFAULT 0,
    sort_order INTEGER DEFAULT 0,
    created_at TEXT DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS reviews (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    listing_id INTEGER NOT NULL,
    user_id INTEGER,
    user_name TEXT NOT NULL,
    user_email TEXT,
    rating INTEGER NOT NULL DEFAULT 5,
    comment TEXT,
    is_approved INTEGER DEFAULT 0,
    created_at TEXT DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    author_id INTEGER,
    title TEXT NOT NULL,
    slug TEXT UNIQUE NOT NULL,
    excerpt TEXT,
    body TEXT,
    featured_image TEXT,
    status TEXT DEFAULT 'draft',
    published_at TEXT,
    created_at TEXT DEFAULT (datetime('now')),
    updated_at TEXT DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS promotions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    listing_id INTEGER NOT NULL,
    type TEXT NOT NULL,
    amount_paid REAL,
    payment_method TEXT,
    starts_at TEXT,
    ends_at TEXT,
    status TEXT DEFAULT 'active',
    created_at TEXT DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS contact_messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL,
    subject TEXT,
    message TEXT NOT NULL,
    is_read INTEGER DEFAULT 0,
    created_at TEXT DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS rate_limits (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    ip_hash TEXT NOT NULL,
    endpoint TEXT NOT NULL,
    hits INTEGER DEFAULT 1,
    window_start TEXT DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS analytics (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    listing_id INTEGER,
    event_type TEXT NOT NULL,
    ip_hash TEXT,
    user_agent TEXT,
    referrer TEXT,
    created_at TEXT DEFAULT (datetime('now'))
);
");
echo "✓ Tables created\n";

// ── Admin User ─────────────────────────────────────────────────
$admin_exists = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
if (!$admin_exists) {
    $hash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);
    $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)")
        ->execute(['Admin', 'admin@tampakan.com', $hash, 'admin']);
    echo "✓ Admin user created (admin@tampakan.com / admin123)\n";
}

// ── Categories ─────────────────────────────────────────────────
$cats = [
    ['Restaurants & Eateries', 'restaurants-eateries', 'business', '🍽️', 1],
    ['Cafes & Milk Tea', 'cafes-milk-tea', 'business', '☕', 2],
    ['Street Food & Carinderias', 'street-food-carinderias', 'business', '🥘', 3],
    ['Hotels & Resorts', 'hotels-resorts', 'business', '🏨', 4],
    ['Barbershops & Salons', 'barbershops-salons', 'business', '💇', 5],
    ['Banks & Financial', 'banks-financial', 'business', '🏦', 6],
    ['Mechanics & Auto Repair', 'mechanics-auto-repair', 'business', '🔧', 7],
    ['Grocery & Sari-Sari', 'grocery-sari-sari', 'business', '🛒', 8],
    ['Hardware & Construction', 'hardware-construction', 'business', '🔨', 9],
    ['Clinics & Hospitals', 'clinics-hospitals', 'business', '🏥', 10],
    ['Pharmacies', 'pharmacies', 'business', '💊', 11],
    ['Schools & Tutorials', 'schools-tutorials', 'business', '🎓', 12],
    ['Churches & Worship', 'churches-worship', 'business', '⛪', 13],
    ['Government Offices', 'government-offices', 'business', '🏛️', 14],
    ['IT & Computer Shops', 'it-computer-shops', 'business', '💻', 15],
    ['Water Refilling', 'water-refilling', 'business', '💧', 16],
    ['Real Estate & Property', 'real-estate-property', 'business', '🏠', 17],
    ['Farm Supplies & Agri', 'farm-supplies-agri', 'business', '🌾', 18],
    ['Waterfalls', 'waterfalls', 'tourism', '🌊', 1],
    ['Farms & Agri-Tourism', 'farms-agri-tourism', 'tourism', '🌿', 2],
    ['View Decks & Scenic', 'view-decks-scenic', 'tourism', '🏔️', 3],
    ['Springs & Resorts', 'springs-resorts', 'tourism', '♨️', 4],
    ['Parks & Nature', 'parks-nature', 'tourism', '🌳', 5],
    ['Cultural & Heritage', 'cultural-heritage', 'tourism', '🏛️', 6],
];

$cat_count = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
if ($cat_count == 0) {
    $stmt = $pdo->prepare("INSERT INTO categories (name, slug, type, icon, sort_order, is_active) VALUES (?,?,?,?,?,1)");
    foreach ($cats as $c) {
        $stmt->execute($c);
    }
    echo "✓ " . count($cats) . " categories seeded\n";
}

// ── Demo Listings ──────────────────────────────────────────────
$listing_count = $pdo->query("SELECT COUNT(*) FROM listings")->fetchColumn();
if ($listing_count == 0) {
    $demos = [
        [1,null,'business','Kolon Cafe','kolon-cafe','The best coffee and snacks in Tampakan town center. Free WiFi, cozy ambiance, and friendly staff. Perfect spot for students and remote workers.',
         'Poblacion, National Highway','Poblacion','Tampakan','South Cotabato',6.4120,125.0435,'0917-123-4567','kolon@example.com','https://koloncafe.ph','https://facebook.com/koloncafe',
         '{"mon":{"open":"07:00","close":"21:00"},"tue":{"open":"07:00","close":"21:00"},"wed":{"open":"07:00","close":"21:00"},"thu":{"open":"07:00","close":"21:00"},"fri":{"open":"07:00","close":"22:00"},"sat":{"open":"08:00","close":"22:00"},"sun":{"open":"08:00","close":"20:00"}}',
         null,null,null,'https://grab.com/ph/food','active',1,0],

        [3,null,'business','Nanay Luz Carinderia','nanay-luz-carinderia','Home-cooked Filipino dishes. Famous for our adobo, sinigang, and fresh lumpia. Affordable meals for the whole family.',
         'Public Market, Poblacion','Poblacion','Tampakan','South Cotabato',6.4108,125.0420,'0918-555-1234',null,null,null,
         '{"mon":{"open":"06:00","close":"19:00"},"tue":{"open":"06:00","close":"19:00"},"wed":{"open":"06:00","close":"19:00"},"thu":{"open":"06:00","close":"19:00"},"fri":{"open":"06:00","close":"19:00"},"sat":{"open":"06:00","close":"15:00"},"sun":null}',
         null,null,null,null,'active',0,0],

        [5,null,'business','Jhon\'s Barbershop','jhons-barbershop','Classic and modern haircuts at affordable prices. Walk-ins welcome. Also offering hot towel shaves and hair treatments.',
         'Barangay Tablu','Tablu','Tampakan','South Cotabato',6.4095,125.0450,'0919-888-7654',null,null,'https://facebook.com/jhonsbarbershop',
         '{"mon":{"open":"08:00","close":"18:00"},"tue":{"open":"08:00","close":"18:00"},"wed":{"open":"08:00","close":"18:00"},"thu":{"open":"08:00","close":"18:00"},"fri":{"open":"08:00","close":"18:00"},"sat":{"open":"08:00","close":"17:00"},"sun":null}',
         null,null,null,null,'active',0,1],

        [4,null,'business','Mountain View Lodge','mountain-view-lodge','Budget-friendly accommodation with stunning views of the surrounding mountains. Clean rooms, hot showers, and complimentary breakfast. Perfect base for exploring Tampakan waterfalls.',
         'Barangay Libadlibad','Libadlibad','Tampakan','South Cotabato',6.4200,125.0380,'0917-111-2233','lodge@example.com','https://mountainviewlodge.ph',null,
         '{"mon":{"open":"00:00","close":"23:59"},"tue":{"open":"00:00","close":"23:59"},"wed":{"open":"00:00","close":"23:59"},"thu":{"open":"00:00","close":"23:59"},"fri":{"open":"00:00","close":"23:59"},"sat":{"open":"00:00","close":"23:59"},"sun":{"open":"00:00","close":"23:59"}}',
         null,null,null,null,'active',1,0],

        [19,null,'tourism','Kling Waterfall','kling-waterfall','One of the most beautiful waterfalls in South Cotabato. Multi-tiered cascading falls surrounded by lush rainforest. A must-visit for nature lovers. Swimming allowed. Entrance fee: ₱30.',
         'Barangay Bong Mal','Bong Mal','Tampakan','South Cotabato',6.3985,125.0580,null,null,null,null,
         null,null,null,null,null,'active',1,0],

        [20,null,'tourism','Agrarian Farm Tour','agrarian-farm-tour','Experience organic farming in the highlands of Tampakan. See coffee plantations, cacao trees, and tropical fruit orchards. Learn about sustainable agriculture from local farmers.',
         'Barangay Kipalbig','Kipalbig','Tampakan','South Cotabato',6.4050,125.0650,'0920-333-4455','agrifarm@example.com',null,'https://facebook.com/tampakanfarmtour',
         '{"mon":{"open":"07:00","close":"16:00"},"tue":{"open":"07:00","close":"16:00"},"wed":{"open":"07:00","close":"16:00"},"thu":{"open":"07:00","close":"16:00"},"fri":{"open":"07:00","close":"16:00"},"sat":{"open":"07:00","close":"16:00"},"sun":null}',
         null,null,null,null,'active',0,0],

        [10,null,'business','Municipal Health Center','municipal-health-center','Primary healthcare facility serving the municipality of Tampakan. Offers general consultation, vaccinations, prenatal care, and emergency first aid.',
         'Municipal Hall Compound, Poblacion','Poblacion','Tampakan','South Cotabato',6.4112,125.0430,'(083) 508-2027',null,null,null,
         '{"mon":{"open":"08:00","close":"17:00"},"tue":{"open":"08:00","close":"17:00"},"wed":{"open":"08:00","close":"17:00"},"thu":{"open":"08:00","close":"17:00"},"fri":{"open":"08:00","close":"17:00"},"sat":null,"sun":null}',
         null,null,null,null,'active',0,0],

        [8,null,'business','Tampakan Mini Mart','tampakan-mini-mart','Your neighborhood grocery. Fresh produce, canned goods, school supplies, and household essentials. Load and e-wallet services available.',
         'National Highway, Poblacion','Poblacion','Tampakan','South Cotabato',6.4118,125.0428,'0918-777-3344',null,null,null,
         '{"mon":{"open":"06:00","close":"21:00"},"tue":{"open":"06:00","close":"21:00"},"wed":{"open":"06:00","close":"21:00"},"thu":{"open":"06:00","close":"21:00"},"fri":{"open":"06:00","close":"21:00"},"sat":{"open":"06:00","close":"21:00"},"sun":{"open":"07:00","close":"20:00"}}',
         null,null,null,null,'active',0,0],
    ];

    $cols = 'category_id,owner_id,type,name,slug,description,address,barangay,city,province,lat,lng,phone,email,website,facebook,hours,shopee_link,lazada_link,amazon_link,food_ordering_link,status,is_featured,is_spotlight';
    $placeholders = implode(',', array_fill(0, 24, '?'));
    $stmt = $pdo->prepare("INSERT INTO listings ({$cols}) VALUES ({$placeholders})");
    foreach ($demos as $d) {
        $stmt->execute($d);
    }
    echo "✓ " . count($demos) . " demo listings seeded\n";

    // Add some demo reviews
    $review_stmt = $pdo->prepare("INSERT INTO reviews (listing_id, user_name, rating, comment, is_approved) VALUES (?,?,?,?,1)");
    $review_stmt->execute([1, 'Maria Santos', 5, 'Best coffee in Tampakan! The wifi is also super fast. My go-to study spot.']);
    $review_stmt->execute([1, 'Juan dela Cruz', 4, 'Great ambiance and affordable. Love the milk tea!']);
    $review_stmt->execute([5, 'Mark Reyes', 5, 'Amazing waterfall! Worth the trek. Bring extra clothes.']);
    $review_stmt->execute([4, 'Sarah Kim', 5, 'Clean rooms and the view is incredible. Staff is very helpful.']);
    echo "✓ Demo reviews seeded\n";

    // Demo blog post
    $pdo->prepare("INSERT INTO posts (author_id, title, slug, excerpt, body, status, published_at) VALUES (?,?,?,?,?,?,datetime('now'))")
        ->execute([1, 'Welcome to Tampakan Directory', 'welcome-to-tampakan-directory',
            'We are excited to launch the first community-powered directory for Tampakan, South Cotabato!',
            'The Tampakan Directory is a free platform where local businesses, tourist spots, and community services can be discovered by locals and visitors alike. Whether you run a carinderia, a barbershop, or know of a hidden waterfall, you can list it here for free!\n\nOur goal is to make Tampakan more accessible and to support local entrepreneurs by giving them an online presence.',
            'published']);
    echo "✓ Demo blog post seeded\n";
}

echo "\n✓ Local setup complete!\n";
echo "  Run: php -S localhost:8080 index.php\n";
echo "  Open: http://localhost:8080\n";
echo "  Admin: admin@tampakan.com / admin123\n\n";
