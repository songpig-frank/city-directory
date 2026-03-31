<?php
/**
 * Public: Terms of Service
 */
$title = 'Terms of Service - ' . config('site_name');
$content = render('terms');

echo render('layout', [
    'title' => $title,
    'content' => $content
]);
