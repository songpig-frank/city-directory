<?php
/**
 * Admin: Blog Create/Edit
 */
auth_require('admin');

$id = $params['id'] ?? null;
$post = null;

if ($id && $id !== 'new') {
    $post = db_row("SELECT * FROM posts WHERE id = ?", [$id]);
}

$title = ($post ? 'Edit Post' : 'New Post') . ' - Admin';
$content = render('admin/blog-edit', [
    'post' => $post
]);

echo render('layout', [
    'title' => $title,
    'content' => $content
]);
