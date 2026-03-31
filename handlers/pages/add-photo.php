<?php
/**
 * Handler: Add Photo Page
 */

$slug = $params['slug'] ?? '';
if (!$slug) {
    redirect('/directory');
}

$listing = db_row("SELECT id, name, slug, type FROM listings WHERE slug = ? AND status = 'active'", [$slug]);
if (!$listing) {
    http_response_code(404);
    echo render_page('errors/404', ['title' => 'Listing Not Found']);
    exit;
}

echo render_page('add-photo', [
    'title'   => 'Add Photo — ' . $listing['name'],
    'listing' => $listing,
    'path'    => 'add-photo'
]);
