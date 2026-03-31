<?php
/**
 * Admin: Media Delete
 */
auth_require('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

if (!csrf_validate()) {
    flash('danger', 'Invalid CSRF token.');
    header('Location: /admin/media');
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$media = db_row("SELECT * FROM media WHERE id = ?", [$id]);

if ($media) {
    // Delete file
    $fullpath = __DIR__ . '/../../public' . $media['filepath'];
    if (file_exists($fullpath)) {
        unlink($fullpath);
    }
    
    // Delete from DB
    db_execute("DELETE FROM media WHERE id = ?", [$id]);
    flash('success', 'Media deleted successfully.');
} else {
    flash('danger', 'Media item not found.');
}

header('Location: /admin/media');
exit;
