<?php
/**
 * Admin: Category Management List
 */
auth_require('categories:manage');

$categories = db_query("
    SELECT c.*, 
    (SELECT COUNT(*) FROM listings WHERE category_id = c.id) as listing_count
    FROM categories c
    ORDER BY c.type, c.sort_order ASC
");

echo render_page('admin/categories', [
    'title'      => 'Manage Categories',
    'categories' => $categories
]);
