<?php
/**
 * Handler: Admin CSV Import Processor (POST)
 */
auth_require('admin');

if (!csrf_validate()) {
    flash('error', 'Invalid form submission.');
    header('Location: /admin/import');
    exit;
}

$category_id = (int)($_POST['category_id'] ?? 0);
if ($category_id < 1) {
    flash('error', 'Please select a target category.');
    header('Location: /admin/import');
    exit;
}

if (empty($_FILES['csv_file']['tmp_name']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
    flash('error', 'Please upload a valid CSV file.');
    header('Location: /admin/import');
    exit;
}

$file = $_FILES['csv_file']['tmp_name'];
$handle = fopen($file, 'r');
if (!$handle) {
    flash('error', 'Could not open the uploaded file.');
    header('Location: /admin/import');
    exit;
}

// Expected format: Name, Address, Phone, Email, Description, Facebook, YouTube, TikTok, Instagram
// We assume row 1 might be a header.
$header_skipped = false;
$success_count = 0;
$error_count = 0;

$type = db_value("SELECT type FROM categories WHERE id = ?", [$category_id]) ?: 'business';
$owner_id = auth_id(); 
$status = 'active'; // auto-approve admin imports
$expiry_days = config('default_expiry_days') ?? 90;
$expires_at = date('Y-m-d H:i:s', strtotime("+{$expiry_days} days"));

$city = config('city');
$province = config('province');

while (($data = fgetcsv($handle, 10000, ',')) !== false) {
    // Skip empty rows
    if (empty(array_filter($data))) continue;
    
    // Skip header row if the first column is 'name'
    if (!$header_skipped && strtolower(trim($data[0])) === 'name') {
        $header_skipped = true;
        continue;
    }
    
    // Safety check against malformed rows
    if (count($data) < 1) continue;

    $name        = trim($data[0] ?? '');
    $address     = trim($data[1] ?? '');
    $phone       = trim($data[2] ?? '');
    $email       = trim($data[3] ?? '');
    $description = trim($data[4] ?? '');
    $facebook    = trim($data[5] ?? '');
    $youtube     = trim($data[6] ?? '');
    $tiktok      = trim($data[7] ?? '');
    $instagram   = trim($data[8] ?? '');

    if (empty($name)) {
        $error_count++;
        continue;
    }

    $slug = slugify($name);
    // Ensure unique slug
    $existing = db_value("SELECT COUNT(*) FROM listings WHERE slug = ?", [$slug]);
    if ($existing > 0) {
        $slug .= '-' . substr(bin2hex(random_bytes(3)), 0, 6);
    }
    $renewal_token = bin2hex(random_bytes(32));

    try {
        db_execute(
            "INSERT INTO listings (
                category_id, owner_id, type, name, slug, description, address, city, province, 
                phone, email, facebook, youtube, tiktok, instagram,
                status, expires_at, renewal_token
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $category_id, $owner_id, $type, $name, $slug, $description, $address, $city, $province,
                $phone, $email, $facebook, $youtube, $tiktok, $instagram,
                $status, $expires_at, $renewal_token
            ]
        );
        $success_count++;
    } catch (Exception $e) {
        $error_count++;
    }
    
    $header_skipped = true; // ensure we only try to skip the first row
}

fclose($handle);

if ($success_count > 0) {
    flash('success', "Successfully imported $success_count listings!" . ($error_count > 0 ? " ($error_count skipped/failed)" : ""));
} else {
    flash('error', 'No listings were imported. Please check your CSV format.');
}

header('Location: /admin/import');
exit;
