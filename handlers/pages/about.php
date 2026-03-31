<?php
/**
 * Public: About Page
 */
$title = 'About Tampakan Directory - ' . config('site_name');
$content = render('about');

echo render('layout', [
    'title' => $title,
    'content' => $content
]);
