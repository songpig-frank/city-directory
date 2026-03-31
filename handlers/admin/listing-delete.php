<?php
/**
 * Admin: Listing Delete
 */
auth_require('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

if (!csrf_validate()) {
    flash('danger', 'Security check failed.');
    header('Location: /admin/listings');
    exit;
}

$id = (int)($_POST['id'] ?? 0);

if ($id > 0) {
    db_execute("DELETE FROM listings WHERE id = ?", [$id]);
    flash('success', 'Listing deleted successfully.');
}

header('Location: /admin/listings');
exit;
