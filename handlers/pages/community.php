<?php
/**
 * Public: Community Hub
 */
$latest_posts = db_query("SELECT * FROM blog_posts WHERE status = 'published' ORDER BY created_at DESC LIMIT 3");
$latest_vloggers = db_query("SELECT * FROM listings WHERE type = 'creator' AND status = 'active' ORDER BY created_at DESC LIMIT 4");

$title = 'Community Hub - ' . config('city');
$content = render('community', [
    'posts' => $latest_posts,
    'vloggers' => $latest_vloggers
]);

echo render('layout', [
    'title' => $title,
    'content' => $content
]);
