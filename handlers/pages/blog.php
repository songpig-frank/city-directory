<?php
/**
 * Public: Blog Index
 */
$page = (int)($_GET['page'] ?? 1);
$per_page = 9;
$total_row = db_row("SELECT COUNT(*) as count FROM blog_posts WHERE status = 'published'");
$total = (int)$total_row['count'];
$pagination = paginate($total, $per_page, $page);

$posts = db_query("SELECT * FROM blog_posts WHERE status = 'published' ORDER BY created_at DESC LIMIT ? OFFSET ?", [$per_page, $pagination['offset']]);

$title = 'Community Blog - ' . config('city');
$content = render('blog', [
    'posts' => $posts,
    'pagination' => $pagination
]);

echo render('layout', [
    'title' => $title,
    'content' => $content
]);
