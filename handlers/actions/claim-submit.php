<?php
/**
 * CityDirectory — Claim Submission Action
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

// Ensure logged in
$user = auth_user();
if (!$user) {
    flash('error', 'You must be logged in to claim a business.');
    redirect('/login');
}

// Rate Limiting (1 claim per day per user)
if (!rate_limit('claim_submission', 1, 86400)) {
    flash('error', 'You have already submitted a claim recently. Please wait for our review.');
    redirect($_SERVER['HTTP_REFERER'] ?: '/directory');
}

$listing_id = (int)($_POST['listing_id'] ?? 0);
$full_name = trim($_POST['full_name'] ?? '');
$contact_phone = trim($_POST['contact_phone'] ?? '');
$proof_text = trim($_POST['proof_text'] ?? '');

if ($listing_id <= 0 || empty($full_name) || empty($contact_phone)) {
    flash('error', 'Please fill in all required fields.');
    redirect($_SERVER['HTTP_REFERER']);
}

// Check if listing exists and is not already claimed
$listing = db_row("SELECT id, slug, owner_id FROM listings WHERE id = ?", [$listing_id]);
if (!$listing) {
    flash('error', 'Business not found.');
    redirect('/directory');
}

if (!empty($listing['owner_id'])) {
    flash('error', 'This business has already been claimed.');
    redirect('/' . $listing['slug']);
}

// Check for existing pending claim
$existing = db_value(
    "SELECT id FROM business_claims WHERE listing_id = ? AND user_id = ? AND status = 'pending'",
    [$listing_id, $user['id']]
);
if ($existing) {
    flash('error', 'You already have a pending claim for this business.');
    redirect('/' . $listing['slug']);
}

try {
    db_execute(
        "INSERT INTO business_claims (listing_id, user_id, full_name, contact_phone, proof_text, status) 
         VALUES (?, ?, ?, ?, ?, 'pending')",
        [$listing_id, $user['id'], $full_name, $contact_phone, $proof_text]
    );

    flash('success', 'Thank you! Your ownership claim has been submitted and is under review.');
    redirect('/' . $listing['slug']);

} catch (Exception $e) {
    error_log("Claim Submission Error: " . $e->getMessage());
    flash('error', 'Submission failed. Please try again later.');
    redirect($_SERVER['HTTP_REFERER']);
}
