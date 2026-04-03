<?php
/**
 * CityDirectory — Claim Listing Page Handler
 */

$slug = $params['slug'] ?? '';
$listing = db_row("SELECT * FROM listings WHERE slug = ? AND status = 'active'", [$slug]);

if (!$listing) {
    flash('error', 'The listing you are trying to claim was not found. Please Search for your business below.');
    redirect('/directory');
}

// Ensure listing isn't already claimed
if (!empty($listing['owner_id'])) {
    flash('info', 'This business is already claimed and verified.');
    redirect('/' . $listing['slug']);
}

// Require Login
if (!auth_user()) {
    flash('info', 'Please login or create an account to claim this business.');
    redirect('/login?redirect=/claim/' . $slug);
}

// Check if user already has a pending claim for this listing
$existing_claim = db_value(
    "SELECT id FROM business_claims WHERE listing_id = ? AND user_id = ? AND status = 'pending'",
    [$listing['id'], auth_user()['id']]
);

if ($existing_claim) {
    flash('info', 'Your ownership claim for this business is already pending review.');
    redirect('/' . $listing['slug']);
}

echo render_page('listing-claim', [
    'title'   => __('claim_this_business') . ' — ' . $listing['name'],
    'listing' => $listing,
    'user'    => auth_user(),
]);
