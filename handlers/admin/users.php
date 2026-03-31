<?php
/**
 * Admin: User Management
 */
auth_require('admin');

$users = db_query("SELECT * FROM users ORDER BY created_at DESC");

$title = 'User Management - Admin';
$content = render('admin/users', [
    'users' => $users
]);

echo render('layout', [
    'title' => $title,
    'content' => $content
]);
