<?php
/**
 * Handler: Admin Dashboard
 */
auth_require('admin', 'manager');

$stats = [
    'total_listings' => db_value("SELECT COUNT(*) FROM listings") ?? 0,
    'active'         => db_value("SELECT COUNT(*) FROM listings WHERE status = 'active'") ?? 0,
    'pending'        => db_value("SELECT COUNT(*) FROM listings WHERE status = 'pending'") ?? 0,
    'expired'        => db_value("SELECT COUNT(*) FROM listings WHERE status = 'expired'") ?? 0,
    'featured'       => db_value("SELECT COUNT(*) FROM listings WHERE is_featured = 1 AND status = 'active'") ?? 0,
    'blog_posts'     => db_value("SELECT COUNT(*) FROM posts") ?? 0,
    'users'          => db_value("SELECT COUNT(*) FROM users") ?? 0,
    'messages'       => db_value("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0") ?? 0,
    'pending_claims' => db_value("SELECT COUNT(*) FROM business_claims WHERE status = 'pending'") ?? 0,
];

// Recent pending listings
$pending = db_query(
    "SELECT l.*, c.name as category_name
     FROM listings l JOIN categories c ON l.category_id = c.id
     WHERE l.status = 'pending'
     ORDER BY l.created_at DESC LIMIT 10"
);

// Expiring soon
$expiring = db_query(
    "SELECT l.*, c.name as category_name
     FROM listings l JOIN categories c ON l.category_id = c.id
     WHERE l.status = 'active' AND l.expires_at IS NOT NULL AND l.expires_at <= ?
     ORDER BY l.expires_at ASC LIMIT 10",
    [date('Y-m-d H:i:s', strtotime('+7 days'))]
);

echo render_page('admin/dashboard', [
    'title'    => 'Admin Dashboard — ' . config('site_name'),
    'stats'    => $stats,
    'pending'  => $pending,
    'expiring' => $expiring,
    'path'     => 'admin',
]);
