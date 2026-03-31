<?php
/**
 * Handler: Listing Submission (POST)
 */

// Rate limit
if (!rate_limit('submit', 5, 300)) {
    flash('error', 'Too many submissions. Please wait a few minutes.');
    header('Location: /submit');
    exit;
}

// CSRF
if (!csrf_validate()) {
    flash('error', 'Invalid form submission. Please try again.');
    header('Location: /submit');
    exit;
}

// Validate required fields
$name = trim($_POST['name'] ?? '');
$category_id = (int)($_POST['category_id'] ?? 0);
$type = in_array($_POST['type'] ?? '', ['business', 'tourism', 'creator']) ? $_POST['type'] : 'business';
$description = trim($_POST['description'] ?? '');
$address = trim($_POST['address'] ?? '');
$barangay = trim($_POST['barangay'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$website = trim($_POST['website'] ?? '');
$facebook = trim($_POST['facebook'] ?? '');
$youtube = trim($_POST['youtube'] ?? '');
$tiktok = trim($_POST['tiktok'] ?? '');
$instagram = trim($_POST['instagram'] ?? '');
$lat = !empty($_POST['lat']) ? (float)$_POST['lat'] : null;
$lng = !empty($_POST['lng']) ? (float)$_POST['lng'] : null;

// External links
$shopee = trim($_POST['shopee_link'] ?? '');
$lazada = trim($_POST['lazada_link'] ?? '');
$amazon = trim($_POST['amazon_link'] ?? '');
$food_ordering = trim($_POST['food_ordering_link'] ?? '');

// Real estate fields
$property_type = $_POST['property_type'] ?? null;
$property_sqm = !empty($_POST['property_sqm']) ? (float)$_POST['property_sqm'] : null;
$property_price = !empty($_POST['property_price']) ? (float)$_POST['property_price'] : null;
$property_terms = trim($_POST['property_terms'] ?? '');
$broker_license = trim($_POST['broker_license'] ?? '');

// Basic content moderation — keyword filter
$spam_keywords = ['casino', 'viagra', 'lottery', 'xxx', 'porn', 'gambling', 'buy followers'];
$content_to_check = strtolower($name . ' ' . $description);
foreach ($spam_keywords as $word) {
    if (str_contains($content_to_check, $word)) {
        flash('error', 'Your submission was flagged. Please contact us if this is an error.');
        header('Location: /submit');
        exit;
    }
}

if (empty($name) || $category_id < 1) {
    flash('error', 'Business name and category are required.');
    header('Location: /submit');
    exit;
}

// Build hours JSON
$hours = [];
$days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
foreach ($days as $day) {
    if (!empty($_POST["hours_{$day}_open"]) && !empty($_POST["hours_{$day}_close"])) {
        $hours[$day] = [
            'open'  => $_POST["hours_{$day}_open"],
            'close' => $_POST["hours_{$day}_close"],
        ];
    } else {
        $hours[$day] = null;
    }
}

$slug = slugify($name);
// Ensure unique slug
$existing = db_value("SELECT COUNT(*) FROM listings WHERE slug = ?", [$slug]);
if ($existing > 0) {
    $slug .= '-' . substr(bin2hex(random_bytes(3)), 0, 6);
}

$expiry_days = config('default_expiry_days') ?? 90;
$expires_at = date('Y-m-d H:i:s', strtotime("+{$expiry_days} days"));
$renewal_token = bin2hex(random_bytes(32));
$owner_id = auth_id();

db_execute(
    "INSERT INTO listings (category_id, owner_id, type, name, slug, description, address, barangay, city, province,
     lat, lng, phone, email, website, facebook, youtube, tiktok, instagram, hours, shopee_link, lazada_link, amazon_link, food_ordering_link,
     property_type, property_sqm, property_price, property_terms, broker_license,
     status, expires_at, renewal_token)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
    [
        $category_id, $owner_id, $type, $name, $slug, $description, $address, $barangay,
        config('city'), config('province'), $lat, $lng, $phone, $email, $website, $facebook, $youtube, $tiktok, $instagram,
        json_encode($hours), $shopee, $lazada, $amazon, $food_ordering,
        $property_type ?: null, $property_sqm, $property_price, $property_terms ?: null, $broker_license ?: null,
        config('require_approval') ? 'pending' : 'active',
        $expires_at, $renewal_token
    ]
);

$listing_id = (int)db_last_id();

// Handle image uploads
if (!empty($_FILES['images'])) {
    $max_images = config('max_images_per_listing') ?? 5;
    $uploaded = 0;
    foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
        if ($uploaded >= $max_images) break;
        if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;
        $file = [
            'tmp_name' => $tmp,
            'size'     => $_FILES['images']['size'][$i],
            'error'    => $_FILES['images']['error'][$i],
        ];
        $path = upload_image($file);
        if ($path) {
            db_execute(
                "INSERT INTO listing_images (listing_id, image_path, is_primary, sort_order) VALUES (?, ?, ?, ?)",
                [$listing_id, $path, $uploaded === 0 ? 1 : 0, $uploaded]
            );
            $uploaded++;
        }
    }
}

// Send admin notification
$cat_name = db_value("SELECT name FROM categories WHERE id = ?", [$category_id]);
send_new_listing_notification([
    'name' => $name,
    'type' => $type,
    'category_name' => $cat_name,
]);

flash('success', __('submit_success'));
header('Location: /directory');
exit;
