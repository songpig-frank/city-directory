<?php
/**
 * Admin: Blog Save
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
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$excerpt = trim($_POST['excerpt'] ?? '');
$status = $_POST['status'] ?? 'draft';
$featured_image = trim($_POST['featured_image'] ?? '');
$slug = trim($_POST['slug'] ?? '');

if (empty($slug)) {
    $slug = slugify($title);
}

if ($id > 0) {
    // Update
    db_execute(
        "UPDATE blog_posts SET title = ?, slug = ?, content = ?, excerpt = ?, status = ?, featured_image = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?",
        [$title, $slug, $content, $excerpt, $status, $featured_image, $id]
    );
    flash('success', 'Blog post updated.');
} else {
    // Insert
    db_execute(
        "INSERT INTO blog_posts (title, slug, content, excerpt, status, featured_image, author_id) VALUES (?, ?, ?, ?, ?, ?, ?)",
        [$title, $slug, $content, $excerpt, $status, $featured_image, $_SESSION['user_id']]
    );
    flash('success', 'New blog post created.');
}

header('Location: /admin/blog');
exit;
