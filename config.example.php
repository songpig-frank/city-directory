<?php
/**
 * CityDirectory — Site Configuration
 * Copy this file to config.php and fill in your city-specific values.
 * NEVER commit config.php to version control.
 */
return [
    // ── Site Identity ──────────────────────────────────────────────
    'site_name'     => 'Tampakan Directory',
    'city'          => 'Tampakan',
    'province'      => 'South Cotabato',
    'country'       => 'Philippines',
    'country_code'  => 'PH',
    'domain'        => 'tampakan.com',
    'base_url'      => 'https://tampakan.com',
    'tagline'       => 'Explore, Connect, Support Tampakan',
    'description'   => 'Official community directory for businesses, tourism, and local services in Tampakan, South Cotabato, Philippines.',
    'logo'          => '/assets/img/logo.png',
    'favicon'       => '/assets/img/favicon.ico',

    // ── Map Defaults ───────────────────────────────────────────────
    'map_center_lat' => 6.4283,
    'map_center_lng' => 124.9478,
    'map_zoom'       => 13,

    // ── Locale ─────────────────────────────────────────────────────
    'timezone'      => 'Asia/Manila',
    'currency'      => '₱',
    'currency_code' => 'PHP',
    'languages'     => [
        'en'  => 'English',
        'tl'  => 'Tagalog',
        'ceb' => 'Bisaya',
    ],
    'default_lang'  => 'en',

    // ── Listings ───────────────────────────────────────────────────
    'default_expiry_days'   => 90,
    'reminder_days_before'  => [14, 3],    // send reminders N days before expiry
    'max_images_per_listing'=> 5,
    'max_image_size_mb'     => 2,
    'require_approval'      => true,

    // ── Paid Prominence ────────────────────────────────────────────
    'prominence_tiers' => [
        'featured'     => ['label' => 'Featured',     'badge_color' => '#FFD700'],
        'spotlight'    => ['label' => 'Spotlight',     'badge_color' => '#FF6B35'],
        'top_category' => ['label' => 'Top of Category','badge_color' => '#4ECDC4'],
    ],

    // ── Sister Sites ───────────────────────────────────────────────
    'sister_sites' => [
        ['name' => 'General Santos Directory', 'url' => 'https://generalsantos.org', 'icon' => '🏙️'],
    ],

    // ── Essential Services ─────────────────────────────────────────
    'emergency_numbers' => [
        ['label' => 'Emergency', 'number' => '911'],
        ['label' => 'Police (PNP)', 'number' => '(083) 552-8888'],
        ['label' => 'Fire Station', 'number' => '(083) 228-3000'],
        ['label' => 'Municipal Hall', 'number' => '(083) 228-3001'],
        ['label' => 'Red Cross', 'number' => '143'],
    ],

    // ── Legal Disclaimers (per-country) ────────────────────────────
    'disclaimers' => [
        'general'     => 'Listings are advertisements placed by third parties. This site does not endorse, verify, or guarantee any listed business or service.',
        'real_estate' => 'This site does not broker real estate transactions. Property listings are classified advertisements. In the Philippines, real estate brokers must hold a valid PRC license and DHSUD registration. Verify all credentials independently.',
        'medical'     => 'Medical listings are for informational purposes only. Always consult a licensed healthcare professional.',
        'affiliate'   => 'Some links may be affiliate links. We may earn a small commission at no extra cost to you.',
    ],

    // ── Social Media ───────────────────────────────────────────────
    'social' => [
        'facebook'  => '',
        'instagram' => '',
        'youtube'   => '',
        'tiktok'    => '',
    ],

    // ── Database ───────────────────────────────────────────────────
    'db_host'   => 'localhost',
    'db_name'   => 'citydirectory',
    'db_user'   => 'root',
    'db_pass'   => '',
    'db_charset'=> 'utf8mb4',

    // ── Email ──────────────────────────────────────────────────────
    'admin_email'   => 'admin@tampakan.com',
    'from_email'    => 'noreply@tampakan.com',
    'from_name'     => 'Tampakan Directory',

    // ── Security ───────────────────────────────────────────────────
    'csrf_token_lifetime' => 3600,      // 1 hour
    'rate_limit_requests' => 60,        // per minute
    'rate_limit_window'   => 60,        // seconds
    'bcrypt_cost'         => 12,
    'session_lifetime'    => 86400,     // 24 hours
    'allowed_origins'     => [],        // CORS origins, empty = same-origin only

    // ── SEO & AI ───────────────────────────────────────────────────
    'google_analytics'    => '',        // GA4 measurement ID
    'google_verification' => '',        // Google Search Console verification
    'schema_org_type'     => 'WebSite', // or 'Organization'
];
