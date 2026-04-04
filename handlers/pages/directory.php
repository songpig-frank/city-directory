<?php
/**
 * Handler: Directory Browse + Category Filter
 */

$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 18;
$type_filter = $_GET['type'] ?? null;

// Get all active categories with counts from the pivot table
$categories = db_query(
    "SELECT c.*, COUNT(DISTINCT lc.listing_id) as listing_count
     FROM categories c
     LEFT JOIN listing_categories lc ON c.id = lc.category_id
     LEFT JOIN listings l ON lc.listing_id = l.id AND l.status = 'active'
     WHERE c.is_active = 1
     GROUP BY c.id
     HAVING listing_count > 0
     ORDER BY c.sort_order, c.name"
);

// Build filter condition
$where = "l.status = 'active'";
$params = [];

if ($type_filter && in_array($type_filter, ['business', 'tourism', 'creator'])) {
    $where .= " AND l.type = ?";
    $params[] = $type_filter;
}

// Support category filtering via pivot table (if a slug is provided)
$cat_slug = $_GET['category'] ?? null;
$join = "";
if ($cat_slug) {
    $join = "JOIN listing_categories filters ON l.id = filters.listing_id 
             JOIN categories fc ON filters.category_id = fc.id AND fc.slug = ?";
    $params[] = $cat_slug;
}

$total = db_value("SELECT COUNT(DISTINCT l.id) FROM listings l {$join} WHERE {$where}", $params);
$pagination = paginate($total, $per_page, $page);

$listings = db_query(
    "SELECT DISTINCT l.*, c.name as category_name, c.icon as category_icon,
            (SELECT image_path FROM listing_images WHERE listing_id = l.id AND is_primary = 1 LIMIT 1) as primary_image,
            (SELECT AVG(rating) FROM reviews WHERE listing_id = l.id AND is_approved = 1) as rating_avg,
            (SELECT COUNT(*) FROM reviews WHERE listing_id = l.id AND is_approved = 1) as rating_count
     FROM listings l
     JOIN listing_categories lc ON l.id = lc.listing_id AND lc.is_primary = 1
     JOIN categories c ON lc.category_id = c.id
     {$join}
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
