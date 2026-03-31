<?php
/**
 * CityDirectory — Cron Job: Expire Listings & Send Reminders
 * 
 * Run daily via cPanel cron:
 *   php /home/username/public_html/cron/expire-listings.php
 * 
 * Or via URL (with secret key):
 *   https://tampakan.com/cron/expire-listings.php?key=YOUR_CRON_KEY
 */

// CLI or secret key check
if (php_sapi_name() !== 'cli') {
    $expected_key = 'change-this-to-a-random-string';
    if (($_GET['key'] ?? '') !== $expected_key) {
        http_response_code(403);
        die('Forbidden');
    }
}

require_once __DIR__ . '/../includes/config-loader.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/mailer.php';

date_default_timezone_set(config('timezone') ?? 'UTC');

$reminder_days = config('reminder_days_before') ?? [14, 3];
$today = date('Y-m-d');
$log = [];

// ── Send Expiry Reminders ─────────────────────────────────────
foreach ($reminder_days as $days) {
    $target_date = date('Y-m-d', strtotime("+{$days} days"));
    $listings = db_query(
        "SELECT * FROM listings WHERE status = 'active' AND expires_at = ? AND email IS NOT NULL AND email != ''",
        [$target_date]
    );
    
    foreach ($listings as $listing) {
        $sent = send_expiry_reminder($listing, $days);
        $log[] = ($sent ? '✓' : '✗') . " Reminder ({$days}d): {$listing['name']} → {$listing['email']}";
    }
}

// ── Expire Listings ───────────────────────────────────────────
$expired_count = db_execute(
    "UPDATE listings SET status = 'expired' WHERE status = 'active' AND expires_at IS NOT NULL AND expires_at < ?",
    [$today]
);
$log[] = "Expired {$expired_count} listings";

// ── Expire Promotions ─────────────────────────────────────────
$promo_expired = db_execute(
    "UPDATE promotions SET status = 'expired' WHERE status = 'active' AND ends_at < ?",
    [$today]
);
// Update listing flags for expired promotions
db_execute(
    "UPDATE listings SET is_featured = 0 WHERE is_featured = 1 AND featured_until IS NOT NULL AND featured_until < ?",
    [$today]
);
db_execute(
    "UPDATE listings SET is_spotlight = 0 WHERE is_spotlight = 1 AND spotlight_until IS NOT NULL AND spotlight_until < ?",
    [$today]
);
$log[] = "Expired {$promo_expired} promotions";

// ── Clean Old Rate Limits ─────────────────────────────────────
db_execute("DELETE FROM rate_limits WHERE window_start < DATE_SUB(NOW(), INTERVAL 1 HOUR)");

// ── Clean Old CSRF Tokens ─────────────────────────────────────
db_execute("DELETE FROM csrf_tokens WHERE created_at < DATE_SUB(NOW(), INTERVAL 2 HOUR)");

// ── Output ────────────────────────────────────────────────────
$output = "[" . date('Y-m-d H:i:s') . "] CityDirectory Cron\n" . implode("\n", $log) . "\n";
echo $output;

// Log to file
$log_file = __DIR__ . '/../logs/cron.log';
$log_dir = dirname($log_file);
if (!is_dir($log_dir)) mkdir($log_dir, 0755, true);
file_put_contents($log_file, $output, FILE_APPEND);
