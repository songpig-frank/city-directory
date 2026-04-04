<?php
/**
 * Owner Portal: Edit Listing
 */
auth_require();

$user_id = $_SESSION['user_id'];
$id = (int)($params['id'] ?? 0);

if (!$id) {
    flash('danger', 'Listing ID is required.');
    redirect('/portal');
}

// Fetch listing ensuring ownership
$listing = db_row("SELECT * FROM listings WHERE id = ? AND owner_id = ?", [$id, $user_id]);

if (!$listing) {
    flash('danger', 'Listing not found or access denied.');
    redirect('/portal');
}

// Fetch categories for the dropdown
$categories = db_query("SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order ASC");

echo render_page('portal/listing-edit', [
    'title'      => 'Edit: ' . clean($listing['name']),
    'listing'    => $listing,
    'categories' => $categories
]);
