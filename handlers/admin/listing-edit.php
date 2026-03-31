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

$categories = db_query("SELECT id, name, type FROM categories ORDER BY type, name");

$title = 'Edit ' . $listing['name'] . ' - Admin';
$content = render('admin/listing-edit', [
    'listing' => $listing,
    'categories' => $categories
]);

echo render('layout', [
    'title' => $title,
    'content' => $content
]);
