<?php
/**
 * Owner Portal: Dashboard
 */
auth_require(); // Require login

$user_id = $_SESSION['user_id'];

// Fetch user's listings with basic stats
$listings = db_query("
    SELECT l.*, c.name as category_name, c.icon as category_icon, 
    (SELECT COUNT(*) FROM page_views WHERE listing_id = l.id) as view_count,
    (SELECT COUNT(*) FROM reviews WHERE listing_id = l.id AND is_approved = 1) as review_count
    FROM listings l
    JOIN categories c ON l.category_id = c.id
    WHERE l.owner_id = ?
    ORDER BY l.created_at DESC
", [$user_id]);

echo render_page('portal/dashboard', [
    'title'    => 'Business Dashboard',
    'listings' => $listings
]);
