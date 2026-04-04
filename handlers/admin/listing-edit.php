<?php
/**
 * Admin: Listing Edit
 */
auth_require('admin');

$id = (int)($params['id'] ?? 0);
$listing = db_row("SELECT * FROM listings WHERE id = ?", [$id]);

if (!$listing) {
    flash('danger', 'Listing not found.');
    header('Location: /admin/listings');
    exit;
}

$categories = db_query("SELECT id, name, type, icon FROM categories ORDER BY type, name");
$secondary_categories = db_query("SELECT category_id FROM listing_categories WHERE listing_id = ?", [$id]);
$secondary_ids = array_column($secondary_categories, 'category_id');

$title = 'Edit ' . $listing['name'] . ' - Admin';
$content = render('admin/listing-edit', [
    'listing' => $listing,
    'categories' => $categories,
    'secondary_ids' => $secondary_ids
]);

echo render('layout', [
    'title' => $title,
    'content' => $content
]);
