<?php
/**
 * Owner Portal: Save Listing (with Trusted User Logic)
 */
auth_require();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/portal');

if (!csrf_validate()) {
    flash('danger', 'Security check failed.');
    redirect('/portal');
}

$user_id = $_SESSION['user_id'];
$id = (int)($_POST['id'] ?? 0);

if (!$id) {
    flash('danger', 'Listing ID is required.');
    redirect('/portal');
}

// 1. Verify ownership
$listing = db_row("SELECT * FROM listings WHERE id = ? AND owner_id = ?", [$id, $user_id]);
if (!$listing) {
    flash('danger', 'Access denied.');
    redirect('/portal');
}

// 2. Fetch User Trust Status
$user = db_row("SELECT is_trusted FROM users WHERE id = ?", [$user_id]);
$is_trusted = (bool)($user['is_trusted'] ?? 0);

// 3. Process Fields
$name = trim($_POST['name'] ?? '');
$category_id = (int)($_POST['category_id'] ?? 0);
$description = trim($_POST['description'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$barangay = trim($_POST['barangay'] ?? '');
$website = trim($_POST['website'] ?? '');
$lat = !empty($_POST['lat']) ? (float)$_POST['lat'] : null;
$lng = !empty($_POST['lng']) ? (float)$_POST['lng'] : null;

$facebook = trim($_POST['facebook'] ?? '');
$instagram = trim($_POST['instagram'] ?? '');
$tiktok = trim($_POST['tiktok'] ?? '');
$youtube = trim($_POST['youtube'] ?? '');
$shopee = trim($_POST['shopee_link'] ?? '');
$lazada = trim($_POST['lazada_link'] ?? '');

if (empty($name)) {
    flash('danger', 'Name is required.');
    redirect("/portal/listings/edit/{$id}");
}

// 4. Determine Status
// If already active and NOT trusted, we might move it to pending to review changes
// but and even if it was pending, it stays pending.
$new_status = $is_trusted ? 'active' : 'pending';

// Update the listing
db_execute(
    "UPDATE listings SET 
        name = ?, category_id = ?, description = ?, 
        phone = ?, address = ?, barangay = ?, website = ?, 
        lat = ?, lng = ?,
        facebook = ?, instagram = ?, tiktok = ?, youtube = ?,
        shopee_link = ?, lazada_link = ?,
        status = ?
     WHERE id = ? AND owner_id = ?",
    [
        $name, $category_id, $description, 
        $phone, $address, $barangay, $website, 
        $lat, $lng,
        $facebook, $instagram, $tiktok, $youtube,
        $shopee, $lazada,
        $new_status,
        $id, $user_id
    ]
);

if ($is_trusted) {
    flash('success', 'Information updated and live! Your status as a Trusted User bypassed moderation.');
} else {
    flash('warning', 'Edits saved successfully. Your changes are now pending moderator approval.');
}

redirect('/portal');
