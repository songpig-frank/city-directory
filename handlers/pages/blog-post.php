<?php
/**
 * Public: Single Blog Post
 */
$slug = $params['slug'] ?? '';
$post = db_row("SELECT * FROM blog_posts WHERE slug = ? AND status = 'published'", [$slug]);

if (!$post) {
    http_response_code(404);
    die(render('layout', ['title' => 'Post not found', 'content' => '<div class="container py-20 text-center"><h1>Post Not Found</h1><p>The story you are looking for might have been moved.</p><a href="/community/blog" class="btn btn-primary mt-4">Back to Blog</a></div>']));
}

$title = clean($post['title']) . ' - ' . config('site_name');
$content = render('blog-single', [
    'post' => $post
]);

echo render('layout', [
    'title' => $title,
    'content' => $content
]);
