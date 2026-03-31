<?php
/**
 * Public: Privacy Policy
 */
$title = 'Privacy Policy - ' . config('site_name');
$content = render('privacy');

echo render('layout', [
    'title' => $title,
    'content' => $content
]);
