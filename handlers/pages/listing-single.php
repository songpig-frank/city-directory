<?php
/**
 * Handler: Single Listing Page
 */

$slug = $params['slug'] ?? '';
if (!$slug) { http_response_code(404); echo render_page('errors/404', ['title' => 'Not Found']); exit; }

$listing = db_row(
    "SELECT l.*, c.name as category_name, c.icon as category_icon, c.slug as category_slug,
            c.default_image as category_default_image,
            u.name as owner_name
     FROM listings l
     JOIN categories c ON l.category_id = c.id
     LEFT JOIN users u ON l.owner_id = u.id
     WHERE l.slug = ? AND l.status IN ('active', 'expired')",
    [$slug]
);

if ($listing) {
    $all_categories = db_query("
        SELECT c.* FROM categories c
        JOIN listing_categories lc ON c.id = lc.category_id
        WHERE lc.listing_id = ?
        UNION
        SELECT id, name, slug, description, parent_id, type, icon, default_image, sort_order, is_active, created_at
        FROM categories WHERE id = ?
    ", [$listing['id'], $listing['category_id']]);
}

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

// 4. Smart SEO & Social (Open Graph) Fallbacks
$og_title = !empty($listing['og_title']) 
    ? $listing['og_title'] 
    : $listing['name'] . ' — ' . config('site_name');

$og_description = !empty($listing['og_description']) 
    ? $listing['og_description'] 
    : truncate(strip_tags($listing['description'] ?? ''), 160);

// Image logic (Smart Fallback)
$final_og_image = null;
if (!empty($listing['og_image'])) {
    $final_og_image = $listing['og_image'];
} elseif (!empty($images)) {
    $final_og_image = $images[0]['image_path'];
} elseif (!empty($listing['category_default_image'])) {
    $final_og_image = $listing['category_default_image'];
} else {
    $final_og_image = config('default_og_image');
}

echo render_page('listing-single', [
    'title'            => $og_title,
    'meta_description' => $og_description,
    'og_image'         => $final_og_image,
    'og_type'          => 'place',
    'schema'           => site_schema(listing_schema($listing, $images)),
    'listing'          => $listing,
    'all_categories'   => $all_categories ?? [],
    'images'           => $images,
    'reviews'          => $reviews,
    'avg_rating'       => $avg_rating,
    'related'          => $related,
    'path'             => $listing['type'],
]);
