<?php
/**
 * Handler: Category directory page
 */
$slug = $params['slug'] ?? '';
if (!$slug) { http_response_code(404); echo render_page('errors/404', ['title' => 'Not Found']); exit; }

$category = db_row("SELECT * FROM categories WHERE slug = ? AND is_active = 1", [$slug]);
if (!$category) { http_response_code(404); echo render_page('errors/404', ['title' => 'Category Not Found']); exit; }

$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 18;

$total = db_value("SELECT COUNT(*) FROM listings WHERE category_id = ? AND status = 'active'", [$category['id']]);
$pagination = paginate($total, $per_page, $page);

$listings = db_query(
    "SELECT l.*, c.name as category_name, c.icon as category_icon,
            (SELECT image_path FROM listing_images WHERE listing_id = l.id AND is_primary = 1 LIMIT 1) as primary_image
     FROM listings l
     JOIN categories c ON l.category_id = c.id
     WHERE l.category_id = ? AND l.status = 'active'
     ORDER BY l.is_featured DESC, l.is_spotlight DESC, l.created_at DESC
     LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
    [$category['id']]
);

echo render_page('directory-category', [
    'title'      => $category['icon'] . ' ' . $category['name'] . ' — ' . config('site_name'),
    'category'   => $category,
    'listings'   => $listings,
    'pagination' => $pagination,
    'path'       => 'directory',
]);
