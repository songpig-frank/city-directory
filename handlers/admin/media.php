<?php
/**
 * Admin: Media Manager
 */
auth_require('admin');

$page = (int)($_GET['page'] ?? 1);
$per_page = 24;
$total = get_media_count();
$pagination = paginate($total, $per_page, $page);

$media_items = get_media($per_page, $pagination['offset']);

$title = 'Media Library - Admin';
$content = render('admin/media', [
    'media_items' => $media_items,
    'pagination'  => $pagination
]);

echo render('layout', [
    'title' => $title,
    'content' => $content
]);
