<?php
/**
 * Handler: Single Listing Page
 */

$slug = $params['slug'] ?? '';
if (!$slug) { http_response_code(404); echo render_page('errors/404', ['title' => 'Not Found']); exit; }

$listing = db_row(
    "SELECT l.*, c.name as category_name, c.icon as category_icon, c.slug as category_slug,
            u.name as owner_name
     FROM listings l
     JOIN categories c ON l.category_id = c.id
     LEFT JOIN users u ON l.owner_id = u.id
     WHERE l.slug = ? AND l.status = 'active'",
    [$slug]
);

if (!$listing) { http_response_code(404); echo render_page('errors/404', ['title' => 'Listing Not Found']); exit; }

// Increment views
db_execute("UPDATE listings SET views = views + 1 WHERE id = ?", [$listing['id']]);

// Get images
$images = db_query(
    "SELECT * FROM listing_images WHERE listing_id = ? ORDER BY is_primary DESC, sort_order",
    [$listing['id']]
);

// Get approved reviews
$reviews = db_query(
    "SELECT * FROM reviews WHERE listing_id = ? AND is_approved = 1 ORDER BY created_at DESC LIMIT 20",
    [$listing['id']]
);

$avg_rating = 0;
if (!empty($reviews)) {
    $avg_rating = array_sum(array_column($reviews, 'rating')) / count($reviews);
}

// Related listings in same category
$related = db_query(
    "SELECT l.*, c.icon as category_icon,
            (SELECT image_path FROM listing_images WHERE listing_id = l.id AND is_primary = 1 LIMIT 1) as primary_image
     FROM listings l
     JOIN categories c ON l.category_id = c.id
     WHERE l.category_id = ? AND l.id != ? AND l.status = 'active'
     ORDER BY l.is_featured DESC, RANDOM()
     LIMIT 3",
    [$listing['category_id'], $listing['id']]
);

echo render_page('listing-single', [
    'title'            => $listing['name'] . ' — ' . config('site_name'),
    'meta_description' => truncate(strip_tags($listing['description'] ?? ''), 160),
    'og_image'         => !empty($images) ? $images[0]['image_path'] : null,
    'og_type'          => 'place',
    'schema'           => site_schema(listing_schema($listing, $images)),
    'listing'          => $listing,
    'images'           => $images,
    'reviews'          => $reviews,
    'avg_rating'       => $avg_rating,
    'related'          => $related,
    'path'             => $listing['type'],
]);
