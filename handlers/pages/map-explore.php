<?php
/**
 * Public: Map Explore
 */
$title = 'Explore ' . config('city') . ' Map - ' . config('site_name');

// We load the view which will handle the Leaflet integration via JS
$content = render('map-explore');

echo render('layout', [
    'title' => $title,
    'content' => $content,
    'no_footer' => true, // Optional: full screen maps often look better without footer
    'full_width' => true
]);
