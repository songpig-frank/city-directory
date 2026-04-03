<?php
/**
 * Admin: Blog List
 */
auth_require('admin');

$posts = db_query("SELECT * FROM posts ORDER BY created_at DESC");

$title = 'Blog Management - Admin';
$content = render('admin/blog', [
    'posts' => $posts
]);

echo render('layout', [
    'title' => $title,
    'content' => $content
]);
