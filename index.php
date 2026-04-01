<?php
/**
 * CityDirectory — Front Controller / Router
 * All requests are routed through this file via .htaccess
 */

// ── SELF-SUSTAINING RESCUE HOOK ────────────────────────────────────
if (isset($_GET['sweep']) && $_GET['sweep'] === 'now') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    echo "<pre style='background:#111; color:#0f0; padding:20px; font-family:monospace;'>";
    echo "=== Tampakan Rescue Sweep Started ===\n";
    
    try {
        $config = require __DIR__ . '/config.php';
        $db_file = __DIR__ . '/database/app.sqlite';
        $dsn = "sqlite:" . $db_file;
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "✓ Database Connected ($db_file)\n";

        $schema = file_get_contents(__DIR__ . '/database/schema.sql');
        $pdo->exec($schema);
        echo "✓ Schema Applied\n";

        $pass = password_hash('changeme123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT OR IGNORE INTO users (name, email, password, role) VALUES ('Admin', 'admin@tampakan.com', ?, 'admin')");
        $stmt->execute([$pass]);
        echo "✓ Admin User Verified/Created (admin@tampakan.com / changeme123)\n";
        
        echo "\n=== SUCCESS! Site is initialized. ===\n";
        echo "Go to: <a href='/' style='color:#fff;'>Tampakan Home</a>";
    } catch (Exception $e) {
        die("\n✗ CRITICAL ERROR: " . $e->getMessage());
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

// ── Setup Bypass (Nuclear Option for Production) ────────────────────
if ($path === 'db-init' || $path === 'db-setup') {
    $handler = $path === 'db-init' ? 'admin/db-init' : 'admin/db-setup';
    require __DIR__ . '/handlers/' . $handler . '.php';
    exit;
}

// ── Maintenance Mode (Digital Blackout) ──────────────────────────────
// This blocks the "garbage" state from the world while you finish setup.
// SECRET SWEEP: Visit /?sweep=now to run initialization and create admin.
if (strpos($_SERVER['REQUEST_URI'], 'sweep=now') !== false) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    require_once __DIR__ . '/handlers/admin/db-init.php';
    require_once __DIR__ . '/handlers/admin/db-setup.php';
    exit;
}

if (true) { // Set to false to disable maintenance mode
     http_response_code(503); // Service Unavailable
     echo render_page('errors/coming-soon', [
         'title'   => 'Under Construction',
         'message' => 'The Tampakan Directory is currently undergoing scheduled maintenance and database synchronization. We will be back online shortly!',
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

    // Temporary Setup routes
    ['GET',  'db-init',             'admin/db-init'],
    ['GET',  'db-setup',            'admin/db-setup'],
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
