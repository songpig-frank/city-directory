<?php
/**
 * Handler: Directory Browse + Category Filter
 */

$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 18;
$type_filter = $_GET['type'] ?? null;

// Get all active categories
$categories = db_query(
    "SELECT c.*, COUNT(l.id) as listing_count
     FROM categories c
     LEFT JOIN listings l ON c.id = l.category_id AND l.status = 'active'
     WHERE c.is_active = 1
     GROUP BY c.id
     ORDER BY c.sort_order, c.name"
);

// Build query
$where = "l.status = 'active'";
$params = [];

if ($type_filter && in_array($type_filter, ['business', 'tourism', 'creator'])) {
    $where .= " AND l.type = ?";
    $params[] = $type_filter;
}

$total = db_value("SELECT COUNT(*) FROM listings l WHERE {$where}", $params);
$pagination = paginate($total, $per_page, $page);

$listings = db_query(
    "SELECT l.*, c.name as category_name, c.icon as category_icon,
            (SELECT image_path FROM listing_images WHERE listing_id = l.id AND is_primary = 1 LIMIT 1) as primary_image,
            (SELECT AVG(rating) FROM reviews WHERE listing_id = l.id AND is_approved = 1) as rating_avg,
            (SELECT COUNT(*) FROM reviews WHERE listing_id = l.id AND is_approved = 1) as rating_count
     FROM listings l
     JOIN categories c ON l.category_id = c.id
     WHERE {$where}
     ORDER BY l.is_featured DESC, l.is_spotlight DESC, l.created_at DESC
     LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
    $params
);

echo render_page('directory', [
    'title'      => __('business_directory') . ' — ' . config('site_name'),
    'categories' => $categories,
    'listings'   => $listings,
    'pagination' => $pagination,
    'type_filter'=> $type_filter,
    'path'       => 'directory',
]);
