<?php
/**
 * Handler: Homepage
 */

// Get featured listings
$featured = db_query(
    "SELECT l.*, c.name as category_name, c.icon as category_icon,
            (SELECT image_path FROM listing_images WHERE listing_id = l.id AND is_primary = 1 LIMIT 1) as primary_image
     FROM listings l
     JOIN categories c ON l.category_id = c.id
     WHERE l.status = 'active' AND l.is_featured = 1 AND (l.featured_until IS NULL OR l.featured_until >= date('now'))
     ORDER BY RANDOM()
     LIMIT 6"
);

// Get spotlight listings
$spotlight = db_query(
    "SELECT l.*, c.name as category_name,
            (SELECT image_path FROM listing_images WHERE listing_id = l.id AND is_primary = 1 LIMIT 1) as primary_image
     FROM listings l
     JOIN categories c ON l.category_id = c.id
     WHERE l.status = 'active' AND l.is_spotlight = 1 AND (l.spotlight_until IS NULL OR l.spotlight_until >= date('now'))
     ORDER BY RANDOM()
     LIMIT 3"
);

// Get latest listings
$latest = db_query(
    "SELECT l.*, c.name as category_name, c.icon as category_icon,
            (SELECT image_path FROM listing_images WHERE listing_id = l.id AND is_primary = 1 LIMIT 1) as primary_image
     FROM listings l
     JOIN categories c ON l.category_id = c.id
     WHERE l.status = 'active'
     ORDER BY l.created_at DESC
     LIMIT 6"
);

// Get categories with count
$categories = db_query(
    "SELECT c.*, COUNT(l.id) as listing_count
     FROM categories c
     LEFT JOIN listings l ON c.id = l.category_id AND l.status = 'active'
     WHERE c.is_active = 1
     GROUP BY c.id
     ORDER BY c.sort_order, c.name"
);

// Get latest blog posts
$posts = db_query(
    "SELECT p.*, u.name as author_name
     FROM posts p
     LEFT JOIN users u ON p.author_id = u.id
     WHERE p.status = 'published'
     ORDER BY p.published_at DESC
     LIMIT 3"
);

// Get stats
$total_listings = db_value("SELECT COUNT(*) FROM listings WHERE status = 'active'") ?? 0;
$total_businesses = db_value("SELECT COUNT(*) FROM listings WHERE status = 'active' AND type = 'business'") ?? 0;
$total_tourism = db_value("SELECT COUNT(*) FROM listings WHERE status = 'active' AND type = 'tourism'") ?? 0;

$city = config('city');
$province = config('province');

echo render_page('home', [
    'title'            => config('site_name') . ' — ' . __('hero_subtitle', ['city' => $city, 'province' => $province]),
    'meta_description' => config('description'),
    'featured'         => $featured,
    'spotlight'        => $spotlight,
    'latest'           => $latest,
    'categories'       => $categories,
    'posts'            => $posts,
    'total_listings'   => $total_listings,
    'total_businesses' => $total_businesses,
    'total_tourism'    => $total_tourism,
    'city'             => $city,
    'province'         => $province,
    'path'             => '',
]);
