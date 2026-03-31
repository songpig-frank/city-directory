<?php
/**
 * Handler: Admin CSV Import UI
 */
auth_require('admin');

// Fetch the categories so they can map the CSV to a specific category
$categories = db_query("SELECT id, name FROM categories ORDER BY name ASC");

echo render_page('admin/import', [
    'title'      => 'Import Listings — ' . config('site_name'),
    'categories' => $categories,
    'path'       => 'admin/import',
]);
