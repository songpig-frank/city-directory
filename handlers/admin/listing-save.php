<?php
/**
 * Admin: Listing Save (Mega-Editor Handler)
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
if ($id === 0) {
    flash('danger', 'Valid Listing ID is required.');
    header('Location: /admin/listings');
    exit;
}

// Core Identity
$name = trim($_POST['name'] ?? '');
$slug = trim($_POST['slug'] ?? '');
$type = $_POST['type'] ?? 'business';
$category_id = (int)($_POST['category_id'] ?? 0);
$description = trim($_POST['description'] ?? '');

// Location & GPS
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$barangay = trim($_POST['barangay'] ?? '');
$website = trim($_POST['website'] ?? '');
$lat = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null;
$lng = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null;

// Digital Presence
$facebook = trim($_POST['facebook'] ?? '');
$instagram = trim($_POST['instagram'] ?? '');
$tiktok = trim($_POST['tiktok'] ?? '');
$youtube = trim($_POST['youtube'] ?? '');
$shopee = trim($_POST['shopee_link'] ?? '');
$lazada = trim($_POST['lazada_link'] ?? '');

// Status & Featured
$status = $_POST['status'] ?? 'pending';
$is_featured = isset($_POST['is_featured']) ? 1 : 0;
$featured_until = !empty($_POST['featured_until']) ? $_POST['featured_until'] : null;

if (empty($name)) {
    flash('danger', 'Listing name is required.');
    header('Location: /admin/listings/edit/' . $id);
    exit;
}

if (empty($slug)) {
    $slug = slugify($name);
}

// Update the listing
db_execute(
    "UPDATE listings SET 
        name = ?, slug = ?, type = ?, category_id = ?, description = ?, 
        phone = ?, address = ?, barangay = ?, website = ?, 
        lat = ?, lng = ?,
        facebook = ?, instagram = ?, tiktok = ?, youtube = ?,
        shopee_link = ?, lazada_link = ?,
        status = ?, is_featured = ?, featured_until = ?
     WHERE id = ?",
    [
        $name, $slug, $type, $category_id, $description, 
        $phone, $address, $barangay, $website, 
        $lat, $lng,
        $facebook, $instagram, $tiktok, $youtube,
        $shopee, $lazada,
        $status, $is_featured, $featured_until,
        $id
    ]
);

// 2. Sync Secondary Categories
db_execute("DELETE FROM listing_categories WHERE listing_id = ?", [$id]);
$secondary = $_POST['secondary_categories'] ?? [];
if (is_array($secondary)) {
    foreach ($secondary as $cat_id) {
        db_execute("INSERT INTO listing_categories (listing_id, category_id) VALUES (?, ?)", [$id, (int)$cat_id]);
    }
}

flash('success', 'Listing and multi-category mappings updated successfully.');
header('Location: /admin/listings/edit/' . $id);
exit;
