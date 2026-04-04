<?php
/**
 * Admin: User Management
 */
auth_require('users:manage');

$users = db_query("SELECT * FROM users ORDER BY created_at DESC");
$roles = db_query("SELECT * FROM roles ORDER BY name");

$title = 'User Management - Admin';
$content = render('admin/users', [
    'users' => $users
]);

echo render('layout', [
    'title' => $title,
    'content' => $content
]);
