<?php
/**
 * CityDirectory — Helper Functions
 */

// ── Security ───────────────────────────────────────────────────────

/**
 * Generate a CSRF token and store it.
 */
function csrf_token(): string {
    auth_init();
    if (empty($_SESSION['csrf_token']) || empty($_SESSION['csrf_time']) || 
        (time() - $_SESSION['csrf_time']) > (config('csrf_token_lifetime') ?? 3600)) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

/**
 * Output a hidden CSRF input field.
 */
function csrf_field(): string {
    return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
}

/**
 * Validate CSRF token from POST data.
 */
function csrf_validate(): bool {
    $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    return !empty($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

/**
 * Check rate limit. Returns true if allowed, false if exceeded.
 */
function rate_limit(string $endpoint, ?int $max = null, ?int $window = null): bool {
    $max = $max ?? config('rate_limit_requests') ?? 60;
    $window = $window ?? config('rate_limit_window') ?? 60;
    $ip_hash = hash('sha256', $_SERVER['REMOTE_ADDR'] ?? '');
    
    // Clean old entries
    db_execute("DELETE FROM rate_limits WHERE window_start < datetime('now', '-' || ? || ' seconds')", [$window]);
    
    $row = db_row(
        "SELECT hits, window_start FROM rate_limits WHERE ip_hash = ? AND endpoint = ?",
        [$ip_hash, $endpoint]
    );
    
    if (!$row) {
        db_execute(
            "INSERT INTO rate_limits (ip_hash, endpoint, hits, window_start) VALUES (?, ?, 1, datetime('now'))",
            [$ip_hash, $endpoint]
        );
        return true;
    }
    
    if ($row['hits'] >= $max) {
        return false;
    }
    
    db_execute(
        "UPDATE rate_limits SET hits = hits + 1 WHERE ip_hash = ? AND endpoint = ?",
        [$ip_hash, $endpoint]
    );
    return true;
}

// ── Text Helpers ───────────────────────────────────────────────────

/**
 * Sanitize user input.
 */
function clean(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate a URL-safe slug.
 */
function slugify(string $text): string {
    $text = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

/**
 * Truncate text to a given length.
 */
function truncate(string $text, int $length = 150, string $suffix = '...'): string {
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Format a date for display.
 */
function format_date(string $date, string $format = 'M j, Y'): string {
    $tz = new DateTimeZone(config('timezone') ?? 'Asia/Manila');
    $dt = new DateTime($date, $tz);
    return $dt->format($format);
}

/**
 * Time ago (e.g., "2 hours ago").
 */
function time_ago(string $datetime): string {
    $now = new DateTime('now', new DateTimeZone(config('timezone') ?? 'UTC'));
    $then = new DateTime($datetime, new DateTimeZone(config('timezone') ?? 'UTC'));
    $diff = $now->diff($then);

    if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    if ($diff->i > 0) return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    return 'just now';
}

/**
 * Render star rating HTML using Lucide icons.
 */
function render_stars(float $rating, int $max = 5): string {
    $full = floor($rating);
    $half = ($rating - $full) >= 0.5 ? 1 : 0;
    $empty = $max - $full - $half;
    
    $html = '<div class="star-rating" title="' . number_format($rating, 1) . ' / ' . $max . '">';
    for ($i = 0; $i < $full; $i++) $html .= '<i data-lucide="star" class="star-full"></i>';
    if ($half) $html .= '<i data-lucide="star-half" class="star-half"></i>';
    for ($i = 0; $i < $empty; $i++) $html .= '<i data-lucide="star" class="star-empty"></i>';
    $html .= '</div>';
    
    return $html;
}

/**
 * Render a single review card.
 */
function render_review_card(array $review): string {
    $stars = render_stars($review['rating']);
    $date = format_date($review['created_at']);
    $name = clean($review['user_name']);
    $comment = nl2br(clean($review['comment'] ?? ''));
    
    return <<<HTML
    <div class="review-card">
        <div class="review-header">
            <div class="review-user">
                <strong>{$name}</strong>
                <span class="review-date">{$date}</span>
            </div>
            {$stars}
        </div>
        <div class="review-body">
            <p>{$comment}</p>
        </div>
    </div>
HTML;
}

// ── Business Hours ─────────────────────────────────────────────────

/**
 * Check if a business is currently open based on its hours JSON.
 * Hours format: {"mon":{"open":"08:00","close":"17:00"},"tue":null,...}
 * null = closed that day
 */
function is_open_now(?string $hours_json): ?bool {
    if (empty($hours_json)) return null;
    $hours = json_decode($hours_json, true);
    if (!$hours) return null;

    $tz = new DateTimeZone(config('timezone') ?? 'Asia/Manila');
    $now = new DateTime('now', $tz);
    $day = strtolower(substr($now->format('D'), 0, 3)); // mon, tue, etc.
    
    if (!isset($hours[$day]) || $hours[$day] === null) return false;
    
    $open = $hours[$day]['open'] ?? null;
    $close = $hours[$day]['close'] ?? null;
    if (!$open || !$close) return null;
    
    $current_time = $now->format('H:i');
    return $current_time >= $open && $current_time <= $close;
}

/**
 * Format hours for display.
 */
function format_hours(?string $hours_json): array {
    if (empty($hours_json)) return [];
    $hours = json_decode($hours_json, true);
    if (!$hours) return [];

    $days = ['mon' => 'Monday', 'tue' => 'Tuesday', 'wed' => 'Wednesday',
             'thu' => 'Thursday', 'fri' => 'Friday', 'sat' => 'Saturday', 'sun' => 'Sunday'];
    $result = [];
    foreach ($days as $key => $label) {
        if (!isset($hours[$key]) || $hours[$key] === null) {
            $result[] = ['day' => $label, 'hours' => 'Closed'];
        } else {
            $result[] = ['day' => $label, 'hours' => $hours[$key]['open'] . ' – ' . $hours[$key]['close']];
        }
    }
    return $result;
}

/**
 * Redirect to a given URL.
 */
function redirect(string $url): void {
    if (strpos($url, 'http') !== 0) {
        $url = base_url($url);
    }
    header("Location: {$url}");
    exit;
}

// ── URL Helpers ────────────────────────────────────────────────────

/**
 * Get listing URL.
 */
function listing_url(array $listing): string {
    return base_url($listing['type'] . '/' . $listing['slug']);
}

/**
 * Get category URL.
 */
function category_url(array $category): string {
    return base_url('directory/' . $category['slug']);
}

/**
 * Get Google Maps directions URL.
 */
function directions_url(float $lat, float $lng, string $name = ''): string {
    $q = urlencode($name);
    return "https://www.google.com/maps/dir/?api=1&destination={$lat},{$lng}&destination_place_id=&travelmode=driving";
}

/**
 * Get Waze directions URL.
 */
function waze_url(float $lat, float $lng): string {
    return "https://waze.com/ul?ll={$lat},{$lng}&navigate=yes";
}

// ── Image Helpers ──────────────────────────────────────────────────

/**
 * Get the image for a listing, with robust fallbacks.
 * 1. listing['primary_image']
 * 2. category['default_image'] (if we add it later)
 * 3. Type-specific placeholder (business, tourism, creator)
 * 4. System default
 */
function get_listing_image(?array $listing): string {
    if (!empty($listing['primary_image'])) {
        return $listing['primary_image'];
    }

    $type = $listing['type'] ?? 'business';
    
    return match($type) {
        'tourism' => '/assets/img/placeholder-tourism.jpg',
        'creator' => '/assets/img/placeholder-creator.jpg',
        'essential'=> '/assets/img/placeholder-essential.jpg',
        default   => '/assets/img/placeholder-business.jpg'
    };
}

/**
 * Handle image upload. Returns path on success, null on failure.
 */
function upload_image(array $file, string $subfolder = 'listings'): ?string {
    $max_size = (config('max_image_size_mb') ?? 2) * 1024 * 1024;
    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    if ($file['size'] > $max_size) return null;
    
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!in_array($mime, $allowed)) return null;
    
    $ext = match($mime) {
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
        default      => 'jpg'
    };
    
    $dir = __DIR__ . '/../uploads/' . $subfolder . '/' . date('Y/m');
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    
    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $path = $dir . '/' . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $path)) return null;
    
    return '/uploads/' . $subfolder . '/' . date('Y/m') . '/' . $filename;
}

// ── SEO Helpers ────────────────────────────────────────────────────



/**
 * Generate site-level JSON-LD.
 */
/**
 * Generate JSON-LD Schema for AEO (AI Engine Optimization).
 */
function site_schema(?array $custom_schema = null): string {
    if ($custom_schema) {
        $schema = $custom_schema;
    } else {
        $schema = [
            '@context' => 'https://schema.org',
            '@id'      => config('base_url') . '#website',
            '@type'    => 'WebSite',
            'name'     => config('site_name'),
            'url'      => config('base_url'),
            'description' => config('description'),
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('site_name'),
                'logo' => ['@type' => 'ImageObject', 'url' => base_url('assets/img/logo.png')]
            ],
            'potentialAction' => [
                '@type'  => 'SearchAction',
                'target' => base_url('search?q={search_term_string}'),
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }
    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
}

/**
 * Generate Schema.org data for a specific listing.
 */
function listing_schema(array $listing, array $images = []): array {
    $type = 'LocalBusiness';
    if (($listing['type'] ?? '') === 'tourism') $type = 'TouristAttraction';
    if (($listing['type'] ?? '') === 'creator') $type = 'Person';

    $schema = [
        '@context' => 'https://schema.org',
        '@type'    => $type,
        'name'     => $listing['name'],
        'description' => clean($listing['description'] ?? ''),
        'url'      => base_url(listing_url($listing)),
    ];

    if (!empty($images)) {
        $schema['image'] = array_map(fn($img) => base_url($img['image_path']), $images);
    } elseif (!empty($listing['primary_image'])) {
        $schema['image'] = base_url($listing['primary_image']);
    }

    if (($listing['type'] ?? '') !== 'creator') {
        $schema['address'] = [
            '@type'           => 'PostalAddress',
            'streetAddress'   => $listing['address'] ?? '',
            'addressLocality' => $listing['barangay'] ?? '',
            'addressRegion'   => config('city'),
            'addressCountry'  => 'PH'
        ];
        
        if (!empty($listing['phone'])) {
            $schema['telephone'] = $listing['phone'];
        }
    }

    // Add aggregate rating if available from the listing data directly or via a joined count
    if (!empty($listing['rating_avg'])) {
        $schema['aggregateRating'] = [
            '@type'       => 'AggregateRating',
            'ratingValue' => $listing['rating_avg'],
            'reviewCount' => $listing['rating_count'] ?: 1
        ];
    }

    // AEO: Potential Action (Reserve, Order, Speak to Agent)
    if (!empty($listing['website'])) {
        $schema['potentialAction'] = [
            '@type' => 'ViewAction',
            'target' => $listing['website']
        ];
    }
    
    // AEO: FAQ Section (Derived from description or common business questions)
    // This gives "AI Juice" by providing direct data for search engine answer boxes
    $schema['mainEntity'] = [
        ['@type' => 'Question', 'name' => "Where is " . $listing['name'] . " located?", 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $listing['name'] . " is located at " . ($listing['address'] ?: "Tampakan") . "."]],
        ['@type' => 'Question', 'name' => "How can I contact " . $listing['name'] . "?", 'acceptedAnswer' => ['@type' => 'Answer', 'text' => "You can reach them at " . ($listing['phone'] ?: "via our directory") . "."]]
    ];
    
    return $schema;
}

// ── Template Rendering ─────────────────────────────────────────────

/**
 * Render a template with data.
 */
function render(string $template, array $data = []): string {
    extract($data);
    ob_start();
    $file = __DIR__ . '/../templates/' . $template . '.php';
    if (!file_exists($file)) {
        ob_end_clean();
        return "Template not found: {$template}";
    }
    include $file;
    return ob_get_clean();
}

/**
 * Render a full page with layout.
 */
function render_page(string $template, array $data = []): string {
    $data['content'] = render($template, $data);
    return render('layout', $data);
}

// ── Flash Messages ─────────────────────────────────────────────────

function flash(string $type, string $message): void {
    auth_init();
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flashes(): array {
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

// ── Pagination ─────────────────────────────────────────────────────

function paginate(int $total, int $per_page = 20, int $current = 1): array {
    $pages = max(1, (int)ceil($total / $per_page));
    $current = max(1, min($current, $pages));
    $offset = ($current - 1) * $per_page;
    return [
        'total'    => $total,
        'per_page' => $per_page,
        'current'  => $current,
        'pages'    => $pages,
        'offset'   => $offset,
        'has_prev' => $current > 1,
        'has_next' => $current < $pages,
    ];
}

// ── Security Headers ───────────────────────────────────────────────

function send_security_headers(): void {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://unpkg.com https://cdn.jsdelivr.net https://www.googletagmanager.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://unpkg.com https://cdn.jsdelivr.net; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https://*.tile.openstreetmap.org https://*.basemaps.cartocdn.com https://unpkg.com; connect-src 'self' https://nominatim.openstreetmap.org https://unpkg.com https://cdn.jsdelivr.net;");
    header('Permissions-Policy: geolocation=(self), camera=(self), microphone=()');
}

// ── Media Management ───────────────────────────────────────────────

/**
 * Handle image upload with validation.
 * Returns public path or throws Exception.
 */
function image_upload(array $file, string $subfolder = 'listings'): string {
    $upload_dir = __DIR__ . '/../public/uploads/' . $subfolder . '/';
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            throw new Exception("Failed to create upload directory: " . $upload_dir);
        }
    }

    $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Upload error code: ' . $file['error']);
    }

    if ($file['size'] > $max_size) {
        throw new Exception('File too large. Max 5MB.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!in_array($mime, $allowed_types)) {
        throw new Exception('Invalid file type: ' . $mime . '. Only JPG, PNG, WEBP, GIF allowed.');
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'jpg';
    $filename = bin2hex(random_bytes(8)) . '_' . time() . '.' . $extension;
    $target_path = $upload_dir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $target_path)) {
        error_log("Failed to move file from " . $file['tmp_name'] . " to " . $target_path);
        throw new Exception('Failed to move uploaded file. Check directory permissions.');
    }

    $public_path = '/uploads/' . $subfolder . '/' . $filename;
    
    // Save to DB
    db_execute(
        "INSERT INTO media (filename, filepath, file_type, file_size, uploaded_by) VALUES (?, ?, ?, ?, ?)",
        [$file['name'], $public_path, $mime, $file['size'], $_SESSION['user_id'] ?? null]
    );

    return $public_path;
}

/**
 * Get media items for the manager.
 */
function get_media(int $limit = 50, int $offset = 0): array {
    return db_query("SELECT * FROM media ORDER BY created_at DESC LIMIT ? OFFSET ?", [$limit, $offset]);
}

/**
 * Get total media count.
 */
function get_media_count(): int {
    $row = db_row("SELECT COUNT(*) as count FROM media");
    return (int)($row['count'] ?? 0);
}
