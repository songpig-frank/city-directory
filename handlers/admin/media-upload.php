<?php
/**
 * Admin: Media Upload
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

try {
    if (!isset($_FILES['image'])) {
        throw new Exception('No file uploaded.');
    }

    $path = image_upload($_FILES['image'], 'general');
    flash('success', 'File uploaded successfully: ' . $path);
} catch (Exception $e) {
    flash('danger', 'Upload failed: ' . $e->getMessage());
}

header('Location: /admin/media');
exit;
