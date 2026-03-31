<?php
/**
 * Handler: Search
 */
$q = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 18;

$listings = [];
$total = 0;

if ($q !== '') {
    $search = "%{$q}%";
    $total = db_value(
        "SELECT COUNT(*) FROM listings l WHERE l.status = 'active' AND (l.name LIKE ? OR l.description LIKE ? OR l.address LIKE ? OR l.barangay LIKE ?)",
        [$search, $search, $search, $search]
    );
    $pagination = paginate($total, $per_page, $page);

    $listings = db_query(
        "SELECT l.*, c.name as category_name, c.icon as category_icon,
                (SELECT image_path FROM listing_images WHERE listing_id = l.id AND is_primary = 1 LIMIT 1) as primary_image
         FROM listings l
         JOIN categories c ON l.category_id = c.id
         WHERE l.status = 'active' AND (l.name LIKE ? OR l.description LIKE ? OR l.address LIKE ? OR l.barangay LIKE ?)
         ORDER BY l.is_featured DESC, l.name ASC
         LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
        [$search, $search, $search, $search]
    );
} else {
    $pagination = paginate(0, $per_page, 1);
}

echo render_page('search', [
    'title'      => ($q ? __('search_results') . ': ' . $q : __('search_results')) . ' — ' . config('site_name'),
    'listings'   => $listings,
    'pagination' => $pagination,
    'query'      => $q,
    'total'      => $total,
    'path'       => 'search',
]);
