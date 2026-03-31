<?php
/**
 * Handler: Admin Business Crawler
 */
auth_require('admin');

$categories = db_query("SELECT id, name FROM categories WHERE is_active = 1 ORDER BY name");
$results = $_SESSION['crawl_results'] ?? [];

echo render_page('admin/crawler', [
    'title'      => 'AI Business Crawler — Admin',
    'categories' => $categories,
    'results'    => $results,
    'path'       => 'admin/crawler',
]);
