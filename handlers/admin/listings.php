<?php
/**
 * Handler: Admin All Listings
 */
auth_require('admin', 'manager');

// Pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

// Sorting
$sort = $_GET['sort'] ?? 'newest';
$order_by = 'l.created_at DESC';
if ($sort === 'oldest') $order_by = 'l.created_at ASC';
if ($sort === 'name') $order_by = 'l.name ASC';

// Fetch
$total = db_value("SELECT COUNT(*) FROM listings");
$listings = db_query(
    "SELECT l.*, c.name as category_name, u.name as owner_name
     FROM listings l
     LEFT JOIN categories c ON l.category_id = c.id
     LEFT JOIN users u ON l.owner_id = u.id
     ORDER BY $order_by LIMIT $limit OFFSET $offset"
);

echo render_page('admin/listings', [
    'title'    => 'Manage Listings — ' . config('site_name'),
    'listings' => $listings,
    'page'     => $page,
    'total'    => $total,
    'limit'    => $limit,
    'path'     => 'admin/listings',
]);
