<?php
/**
 * Handler: Listing Submission Form (GET)
 */

$categories = db_query(
    "SELECT * FROM categories WHERE is_active = 1 ORDER BY type, sort_order, name"
);

echo render_page('listing-submit', [
    'title'      => __('submit_title') . ' — ' . config('site_name'),
    'categories' => $categories,
    'path'       => 'submit',
    'city'       => config('city'),
]);
