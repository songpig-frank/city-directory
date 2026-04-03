<?php
/**
 * CityDirectory — Front Controller / Router
 * All requests are routed through this file via .htaccess
 */

// ── Production Setup Sweep ────────────────────────────────────────
// Visit ?sweep=SECRETKEY to initialize the full database on a fresh server.
// IMPORTANT: Change this key or remove this block after setup is complete.
if (isset($_GET['sweep']) && $_GET['sweep'] === 'tpk2026init') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    header('Content-Type: text/plain; charset=utf-8');
    echo "=== Tampakan Directory — Full Production Setup ===\n";
    echo "PHP Version: " . phpversion() . "\n\n";

    try {
        $db_file = __DIR__ . '/database/app.sqlite';
        $db_dir = dirname($db_file);
        if (!is_dir($db_dir)) { mkdir($db_dir, 0755, true); }

        $pdo = new PDO("sqlite:" . $db_file);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('PRAGMA journal_mode=WAL');
        $pdo->exec('PRAGMA foreign_keys=ON');
        echo "[OK] Database connected: $db_file\n";

        // ── Full Schema (matches setup-local.php exactly) ─────────
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

            CREATE TABLE IF NOT EXISTS site_settings (
                setting_key VARCHAR(100) PRIMARY KEY,
                setting_value TEXT,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS media (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                filename TEXT NOT NULL,
                filepath TEXT NOT NULL,
                file_type TEXT,
                file_size INTEGER,
                uploaded_by INTEGER,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS business_claims (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                listing_id INTEGER NOT NULL,
                user_name TEXT NOT NULL,
                user_email TEXT NOT NULL,
                user_phone TEXT,
                proof_text TEXT,
                status TEXT DEFAULT 'pending',
                created_at TEXT DEFAULT (datetime('now'))
            );
        ");
        echo "[OK] All tables created/verified\n";

        // ── Seed Categories ───────────────────────────────────────
        $cat_count = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
        if ($cat_count == 0) {
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
            $stmt = $pdo->prepare("INSERT INTO categories (name, slug, type, icon, sort_order, is_active) VALUES (?,?,?,?,?,1)");
            foreach ($cats as $c) { $stmt->execute($c); }
            echo "[OK] " . count($cats) . " categories seeded\n";
        } else {
            echo "[SKIP] Categories already exist ($cat_count found)\n";
        }

        // ── Admin User ────────────────────────────────────────────
        $admin_exists = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
        if (!$admin_exists) {
            $hash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);
            $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)")
                ->execute(['Admin', 'admin@tampakan.com', $hash, 'admin']);
            echo "[OK] Admin user created (admin@tampakan.com / admin123)\n";
        } else {
            echo "[SKIP] Admin user already exists\n";
        }

        // ── Verify ────────────────────────────────────────────────
        $tables = [];
        $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) { $tables[] = $row['name']; }
        echo "\n[TABLES] " . implode(', ', $tables) . "\n";
        echo "\n=== SETUP COMPLETE ===\n";
        echo "Next: Log in at /login with admin@tampakan.com / admin123\n";
        echo "Then: Remove or change the sweep key in index.php for security.\n";

    } catch (Exception $e) {
        echo "\n[ERROR] " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    }
    exit;
}

// ── Bootstrap ──────────────────────────────────────────────────────
require_once __DIR__ . '/includes/config-loader.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/i18n.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/mailer.php';

// Load dynamic settings from DB
global $_SITE_SETTINGS;
$_SITE_SETTINGS = [];
try {
    $settings_rows = db_query("SELECT setting_key, setting_value FROM site_settings");
    foreach ($settings_rows as $row) {
        $_SITE_SETTINGS[$row['setting_key']] = $row['setting_value'];
    }
} catch (Exception $e) {
    // Table might not exist during installation/updates; gracefully fallback
}

// Start session
auth_init();

// Set timezone from config
date_default_timezone_set(config('timezone') ?? 'UTC');

// Send security headers
send_security_headers();

// ── Parsing and Assets ──────────────────────────────────────────────
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim($request_uri, '/');
$method = $_SERVER['REQUEST_METHOD'];

if (preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|webp|ico|woff2?|ttf|map)$/', $path)) {
    return false;
}

// ── Maintenance Mode ────────────────────────────────────────────────
// Set to true to block public access during setup. Set to false to go live.
$maintenance_mode = false;
if ($maintenance_mode) {
    http_response_code(503);
    echo render_page('errors/coming-soon', [
        'title'   => 'Under Construction',
        'message' => 'The Tampakan Directory is currently undergoing scheduled maintenance. We will be back online shortly!',
    ]);
    exit;
}

// ── Route Definitions ──────────────────────────────────────────────
// Format: [method, pattern, handler_file]
// Patterns use :param for dynamic segments
$routes = [
    // Public pages
    ['GET',  '',                    'pages/home'],
    ['GET',  'directory',           'pages/directory'],
    ['GET',  'directory/:slug',     'pages/directory-category'],
    ['GET',  'business/:slug',      'pages/listing-single'],
    ['GET',  'tourism/:slug',       'pages/listing-single'],
    ['GET',  'creator/:slug',       'pages/listing-single'],
    ['GET',  'tourism',             'pages/tourism'],
    ['GET',  'search',              'pages/search'],
    ['GET',  'community',           'pages/community'],
    ['GET',  'community/blog',      'pages/blog'],
    ['GET',  'community/blog/:slug','pages/blog-post'],
    ['GET',  'community/vloggers',  'pages/vloggers'],
    ['GET',  'community/projects',  'pages/projects'],
    ['GET',  'essential-services',  'pages/essential-services'],
    ['GET',  'contact',             'pages/contact'],
    ['POST', 'contact',             'actions/contact-submit'],
    ['GET',  'map',                 'pages/map-explore'],
    ['GET',  'about',               'pages/about'],
    ['GET',  'terms',               'pages/terms'],
    ['GET',  'privacy',             'pages/privacy'],

    // Listings
    ['GET',  'submit',              'pages/listing-submit'],
    ['POST', 'submit',              'actions/listing-submit'],
    ['POST', 'actions/review-submit',      'actions/review-submit'],
    ['GET',  'add-photo/:slug',           'pages/add-photo'],
    ['POST', 'actions/photo-upload',       'actions/photo-upload'],
    ['GET',  'claim/:slug',               'pages/listing-claim'],
    ['POST', 'actions/claim-submit',      'actions/claim-submit'],
    ['GET',  'renew/:token',        'actions/listing-renew'],

    // Auth
    ['GET',  'login',               'pages/login'],
    ['POST', 'login',               'actions/login'],
    ['GET',  'register',            'pages/register'],
    ['POST', 'register',            'actions/register'],
    ['GET',  'logout',              'actions/logout'],

    // Admin
    ['GET',  'admin',               'admin/dashboard'],
    ['GET',  'admin/listings',      'admin/listings'],
    ['GET',  'admin/import',        'admin/import'],
    ['POST', 'admin/import',        'admin/import-process'],
    ['GET',  'admin/listings/edit/:id', 'admin/listing-edit'],
    ['POST', 'admin/listings/save',     'admin/listing-save'],
    ['POST', 'admin/listings/delete',   'admin/listing-delete'],
    ['POST', 'admin/listings/:id/status', 'admin/listing-status'],
    ['GET',  'admin/promotions',    'admin/promotions'],
    ['POST', 'admin/promotions',    'admin/promotion-create'],
    ['POST', 'admin/promotions/remove', 'admin/promotion-delete'],
    ['GET',  'admin/blog',          'admin/blog'],
    ['GET',  'admin/blog/new',      'admin/blog-edit'],
    ['GET',  'admin/blog/:id',      'admin/blog-edit'],
    ['POST', 'admin/blog/save',     'admin/blog-save'],
    ['POST', 'admin/blog/delete',   'admin/blog-delete'],
    ['GET',  'admin/users',         'admin/users'],
    ['GET',  'admin/messages',      'admin/messages'],
    ['GET',  'admin/settings',      'admin/settings'],
    ['POST', 'admin/settings/save', 'admin/settings-save'],
    ['GET',  'admin/media',         'admin/media'],
    ['POST', 'admin/media/upload',  'admin/media-upload'],
    ['POST', 'admin/media/delete',  'admin/media-delete'],
    ['GET',  'admin/claims',         'admin/claims'],
    ['POST', 'admin/claims/process', 'admin/claim-process'],
    ['GET',  'admin/crawler',        'admin/crawler'],
    ['POST', 'admin/crawler/execute', 'admin/crawl-execute'],
    ['POST', 'admin/crawler/import',  'admin/crawl-import'],
    ['POST', 'admin/crawler/clear',   'admin/crawl-clear'],
    ['GET',  'admin/preview',        'admin/preview'],

    // API (for AJAX)
    ['GET',  'api/listings',        'api/listings'],
    ['GET',  'api/search',          'api/search'],
    ['GET',  'api/categories',      'api/categories'],
    ['POST', 'api/ai-writer',       'api/ai-writer'],

    // SEO
    ['GET',  'sitemap.xml',         'seo/sitemap'],
    ['GET',  'robots.txt',          'seo/robots'],
];

// ── Route Matching ─────────────────────────────────────────────────
$params = [];
$matched_handler = null;

foreach ($routes as [$route_method, $pattern, $handler]) {
    if ($method !== $route_method) continue;

    $pattern_parts = explode('/', $pattern);
    $path_parts = explode('/', $path);

    if (count($pattern_parts) !== count($path_parts)) continue;

    $match = true;
    $route_params = [];

    for ($i = 0; $i < count($pattern_parts); $i++) {
        if (str_starts_with($pattern_parts[$i], ':')) {
            $route_params[ltrim($pattern_parts[$i], ':')] = $path_parts[$i];
        } elseif ($pattern_parts[$i] !== $path_parts[$i]) {
            $match = false;
            break;
        }
    }

    if ($match) {
        $params = $route_params;
        $matched_handler = $handler;
        break;
    }
}

// ── Execute Handler ────────────────────────────────────────────────
if ($matched_handler) {
    $handler_file = __DIR__ . '/handlers/' . $matched_handler . '.php';
    if (file_exists($handler_file)) {
        require $handler_file;
    } else {
        http_response_code(501);
        echo render_page('errors/coming-soon', [
            'title'   => 'Coming Soon',
            'message' => 'This section of the platform is currently under active development. Please check back later!',
        ]);
    }
} else {
    http_response_code(404);
    echo render_page('errors/404', [
        'title' => __('page_not_found') . ' — ' . config('site_name'),
    ]);
}
