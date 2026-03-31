<?php
/**
 * Handler: Admin — Update listing status (approve/reject)
 */
auth_require('admin', 'manager');

if (!csrf_validate()) {
    flash('error', 'Invalid request.');
    header('Location: /admin/listings');
    exit;
}

$id = (int)($params['id'] ?? 0);
$status = $_POST['status'] ?? '';

if (!$id || !in_array($status, ['active', 'rejected', 'expired', 'pending'])) {
    flash('error', 'Invalid status.');
    header('Location: /admin');
    exit;
}

db_execute("UPDATE listings SET status = ? WHERE id = ?", [$status, $id]);

$listing_name = db_value("SELECT name FROM listings WHERE id = ?", [$id]);
flash('success', "Listing \"{$listing_name}\" has been {$status}.");
header('Location: /admin');
exit;
