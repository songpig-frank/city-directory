<?php
/**
 * Public: User Registration
 */
if (isset($_SESSION['user_id'])) {
    header('Location: /admin');
    exit;
}

$title = 'Join ' . config('site_name');
$content = render('register');

echo render('layout', [
    'title' => $title,
    'content' => $content
]);
