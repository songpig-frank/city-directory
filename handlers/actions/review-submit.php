<?php
/**
 * CityDirectory — Submit Review Handler
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

// Rate Limiting (1 review per 5 minutes per IP)
if (!rate_limit('review_submit', 1, 300)) {
    flash('error', 'You are submitting reviews too fast. Please wait 5 minutes.');
    redirect($_SERVER['HTTP_REFERER'] ?: '/directory');
}

$listing_id = (int)($_POST['listing_id'] ?? 0);
$rating     = (int)($_POST['rating'] ?? 5);
$comment    = trim($_POST['comment'] ?? '');
$name       = trim($_POST['user_name'] ?? '');

// Validation
if ($listing_id <= 0) {
    flash('error', 'Invalid listing.');
    redirect('/directory');
}

if ($rating < 1 || $rating > 5) {
    flash('error', 'Please provide a rating between 1 and 5.');
    redirect($_SERVER['HTTP_REFERER']);
}

if (empty($name)) {
    flash('error', 'Please provide your name.');
    redirect($_SERVER['HTTP_REFERER']);
}

// Check if listing exists
$listing = db_row("SELECT id FROM listings WHERE id = ? AND status = 'active'", [$listing_id]);
if (!$listing) {
    flash('error', 'Listing not found.');
    redirect('/directory');
}

// User-specific data if logged in
$user_id = auth_user()['id'] ?? null;
$user_email = auth_user()['email'] ?? null;

// Determine if review should be auto-approved (e.g. for admins or if setting is on)
$is_approved = auth_has_role('admin') ? 1 : 0;

try {
    db_execute(
        "INSERT INTO reviews (listing_id, user_id, user_name, user_email, rating, comment, is_approved) 
         VALUES (?, ?, ?, ?, ?, ?, ?)",
        [$listing_id, $user_id, $name, $user_email, $rating, $comment, $is_approved]
    );

    if ($is_approved) {
        flash('success', 'Thank you! Your review has been published.');
    } else {
        flash('success', 'Thank you! Your review has been submitted and is awaiting moderation.');
    }
} catch (Exception $e) {
    error_log("Review Submit Error: " . $e->getMessage());
    flash('error', 'An error occurred while saving your review.');
}

redirect($_SERVER['HTTP_REFERER']);
