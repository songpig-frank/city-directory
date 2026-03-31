<?php
/**
 * Admin: Messages (Inbox)
 */
auth_require('admin');

$messages = db_query("SELECT * FROM messages ORDER BY created_at DESC");

$title = 'Inbox - Admin';
$content = render('admin/messages', [
    'messages' => $messages
]);

echo render('layout', [
    'title' => $title,
    'content' => $content
]);
