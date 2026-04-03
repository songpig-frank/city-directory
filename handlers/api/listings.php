<?php
/**
 * API: Listings
 */
header('Content-Type: application/json');

$type = $_GET['type'] ?? '';
$category = $_GET['category'] ?? '';
$limit = (int)($_GET['limit'] ?? 20);

$query = "SELECT l.*, c.name as category_name, c.icon as category_icon,
                 (SELECT image_path FROM listing_images WHERE listing_id = l.id AND is_primary = 1 LIMIT 1) as primary_image,
                 (SELECT AVG(rating) FROM reviews WHERE listing_id = l.id AND is_approved = 1) as rating_avg,
                 (SELECT COUNT(*) FROM reviews WHERE listing_id = l.id AND is_approved = 1) as rating_count
          FROM listings l
          JOIN categories c ON l.category_id = c.id
          WHERE l.status = 'active'";
$params = [];

$exclude_type = $_GET['exclude_type'] ?? '';

if ($type) {
    $query .= " AND l.type = ?";
    $params[] = $type;
}

if ($exclude_type) {
    $query .= " AND l.type != ?";
    $params[] = $exclude_type;
}

if ($category) {
    $query .= " AND c.slug = ?";
    $params[] = $category;
}

$query .= " ORDER BY l.is_featured DESC, l.created_at DESC LIMIT ?";
$params[] = $limit;

$listings = db_query($query, $params);

// Transform for API
$data = array_map(function($l) {
    return [
        'id' => $l['id'],
        'name' => $l['name'],
        'slug' => $l['slug'],
        'type' => $l['type'],
        'category' => $l['category_name'],
        'icon' => $l['category_icon'],
        'description' => truncate(strip_tags($l['description']), 100),
        'image' => $l['primary_image'] ? base_url($l['primary_image']) : null,
        'url' => listing_url($l),
        'phone' => $l['phone'] ?? null,
        'website' => $l['website'] ?? null,
        'facebook' => $l['facebook'] ?? null,
        'rating' => [
            'average' => (float)($l['rating_avg'] ?? 0),
            'count' => (int)($l['rating_count'] ?? 0)
        ],
        'lat' => $l['lat'],
        'lng' => $l['lng'],
        'location' => [
            'address' => $l['address'],
            'barangay' => $l['barangay'],
            'city' => config('city')
        ]
    ];
}, $listings);

echo json_encode(['success' => true, 'data' => $data]);
