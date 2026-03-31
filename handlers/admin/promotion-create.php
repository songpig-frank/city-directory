<?php
/**
 * Admin: Promotion Create
 */
auth_require('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

if (!csrf_validate()) {
    flash('danger', 'Security check failed.');
    header('Location: /admin/promotions');
    exit;
}

$listing_id = (int)($_POST['listing_id'] ?? 0);
$days = (int)($_POST['days'] ?? 30);

if ($listing_id > 0) {
    $until = date('Y-m-d H:i:s', strtotime("+{$days} days"));
    db_execute(
        "UPDATE listings SET is_featured = 1, featured_until = ? WHERE id = ?",
        [$until, $listing_id]
    );
    flash('success', 'Listing is now featured for ' . $days . ' days.');
}

header('Location: /admin/promotions');
exit;
