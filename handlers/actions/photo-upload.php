<?php
/**
 * CityDirectory — Photo Upload Action
 */
require_once __DIR__ . '/../../includes/config-loader.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';

// Only POST allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/directory');
}

// CSRF Validation
if (!csrf_validate()) {
    flash('error', 'Security token invalid. Please try again.');
    redirect($_SERVER['HTTP_REFERER'] ?: '/directory');
}

// Rate Limiting (2 photos per 10 minutes per IP)
if (!rate_limit('photo_upload', 2, 600)) {
    flash('error', 'You are uploading photos too fast. Please wait 10 minutes.');
    redirect($_SERVER['HTTP_REFERER'] ?: '/directory');
}

$listing_id = (int)($_POST['listing_id'] ?? 0);
$uploader_name = trim($_POST['uploader_name'] ?? '');

if ($listing_id <= 0 || empty($uploader_name)) {
    flash('error', 'Invalid submission details.');
    redirect($_SERVER['HTTP_REFERER']);
}

// Check if listing exists
$listing = db_row("SELECT id, slug FROM listings WHERE id = ? AND status = 'active'", [$listing_id]);
if (!$listing) {
    flash('error', 'Listing not found.');
    redirect('/directory');
}

// Handle Image Upload
if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    flash('error', 'Please select a valid photo to upload.');
    redirect($_SERVER['HTTP_REFERER']);
}

try {
    // Using image_upload from helpers.php (it saves to /public/uploads/listings/)
    $public_path = image_upload($_FILES['photo'], 'listings');
    
    // Check if listing has a primary image already
    $has_primary = db_value("SELECT COUNT(*) FROM listing_images WHERE listing_id = ? AND is_primary = 1", [$listing_id]);
    $is_primary = $has_primary > 0 ? 0 : 1;

    // Save to listing_images
    db_execute(
        "INSERT INTO listing_images (listing_id, image_path, alt_text, is_primary, sort_order) 
         VALUES (?, ?, ?, ?, ?)",
        [$listing_id, $public_path, "Storefront photo by " . $uploader_name, $is_primary, 10]
    );

    // Update listing coordinates if provided (Trust-based crowd-sourcing)
    $lat = (float)($_POST['lat'] ?? 0);
    $lng = (float)($_POST['lng'] ?? 0);
    if ($lat != 0 && $lng != 0) {
        // We only update if the listing currently has no coordinates or if we want to "Refine" them
        db_execute(
            "UPDATE listings SET lat = ?, lng = ? WHERE id = ? AND (lat IS NULL OR lat = 0 OR lat = 6.45)", // 6.45 is the default center
            [$lat, $lng, $listing_id]
        );
    }

    flash('success', 'Thank you! Your storefront photo has been uploaded successfully.');
    redirect('/' . $listing['slug']);

} catch (Exception $e) {
    error_log("Photo Upload Error: " . $e->getMessage());
    flash('error', 'Upload failed: ' . $e->getMessage());
    redirect($_SERVER['HTTP_REFERER']);
}
