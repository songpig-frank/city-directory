<?php
/**
 * Handler: Content Creators & Vloggers Feed
 */

$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 18;
$type_filter = 'creator';

// Get active categories matching this type
$categories = db_query(
    "SELECT c.*, COUNT(l.id) as listing_count
     FROM categories c
     JOIN listings l ON c.id = l.category_id AND l.status = 'active'
     WHERE c.is_active = 1 AND l.type = 'creator'
     GROUP BY c.id
     ORDER BY c.sort_order, c.name"
);

$where = "l.status = 'active' AND l.type = 'creator'";
$total = db_value("SELECT COUNT(*) FROM listings l WHERE {$where}");
$pagination = paginate($total, $per_page, $page);

$listings = db_query(
    "SELECT l.*, c.name as category_name, c.icon as category_icon,
            (SELECT image_path FROM listing_images WHERE listing_id = l.id AND is_primary = 1 LIMIT 1) as primary_image
     FROM listings l
     JOIN categories c ON l.category_id = c.id
     WHERE {$where}
     ORDER BY l.is_featured DESC, l.is_spotlight DESC, l.created_at DESC
     LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}"
);

echo render_page('directory', [
    'title'      => 'Tampakan Content Creators — ' . config('site_name'),
    'categories' => $categories,
    'listings'   => $listings,
    'pagination' => $pagination,
    'type_filter'=> $type_filter,
    'path'       => 'community/vloggers',
]);
