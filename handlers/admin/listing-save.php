<?php
/**
 * Admin: Listing Save
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
$name = trim($_POST['name'] ?? '');
$type = $_POST['type'] ?? 'business';
$category_id = (int)($_POST['category_id'] ?? 0);
$description = trim($_POST['description'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$barangay = trim($_POST['barangay'] ?? '');
$website = trim($_POST['website'] ?? '');
$status = $_POST['status'] ?? 'pending';
$is_featured = isset($_POST['is_featured']) ? 1 : 0;

if (empty($name) || $id === 0) {
    flash('danger', 'Listing name and ID are required.');
    header('Location: /admin/listings');
    exit;
}

db_execute(
    "UPDATE listings SET 
        name = ?, type = ?, category_id = ?, description = ?, 
        phone = ?, address = ?, barangay = ?, website = ?, 
        status = ?, is_featured = ?
     WHERE id = ?",
    [$name, $type, $category_id, $description, $phone, $address, $barangay, $website, $status, $is_featured, $id]
);

flash('success', 'Listing updated successfully.');
header('Location: /admin/listings/edit/' . $id);
exit;
