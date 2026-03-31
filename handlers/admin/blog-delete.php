<?php
/**
 * Admin: Blog Delete
 */
auth_require('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

if (!csrf_validate()) {
    flash('danger', 'Invalid CSRF token.');
    header('Location: /admin/blog');
    exit;
}

$id = (int)($_POST['id'] ?? 0);
db_execute("DELETE FROM blog_posts WHERE id = ?", [$id]);

flash('success', 'Blog post deleted permanently.');
header('Location: /admin/blog');
exit;
