<?php
/**
 * Admin: Promotions (Featured Listings)
 */
auth_require('admin');

$featured = db_query(
    "SELECT l.*, c.name as category_name 
     FROM listings l 
     JOIN categories c ON l.category_id = c.id 
     WHERE l.is_featured = 1 AND l.status = 'active'
     ORDER BY l.featured_until ASC"
);

$all_listings = db_query("SELECT id, name FROM listings WHERE status = 'active' ORDER BY name ASC");

$title = 'Promotions - Admin';
$content = render('admin/promotions', [
    'featured' => $featured,
    'all_listings' => $all_listings
]);

echo render('layout', [
    'title' => $title,
    'content' => $content
]);
