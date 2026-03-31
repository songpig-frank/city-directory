<?php
/**
 * Admin Action: Process Business Claim
 */
require_once __DIR__ . '/../../includes/config-loader.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';

// Auth Check
auth_require('admin');

// Only POST allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/claims');
}

// CSRF Validation
if (!csrf_validate()) {
    flash('error', 'Security token invalid.');
    redirect('/admin/claims');
}

$claim_id = (int)($_POST['claim_id'] ?? 0);
$action = $_POST['action'] ?? ''; // 'approve' or 'reject'

if ($claim_id <= 0 || !in_array($action, ['approve', 'reject'])) {
    flash('error', 'Invalid action.');
    redirect('/admin/claims');
}

// Fetch Claim
$claim = db_row("SELECT * FROM business_claims WHERE id = ?", [$claim_id]);
if (!$claim) {
    flash('error', 'Claim not found.');
    redirect('/admin/claims');
}

if ($claim['status'] !== 'pending') {
    flash('info', 'This claim has already been processed.');
    redirect('/admin/claims');
}

try {
    if ($action === 'approve') {
        // 1. Update listing owner
        db_execute(
            "UPDATE listings SET owner_id = ?, updated_at = datetime('now') WHERE id = ?",
            [$claim['user_id'], $claim['listing_id']]
        );
        
        // 2. Mark claim as approved
        db_execute("UPDATE business_claims SET status = 'approved' WHERE id = ?", [$claim_id]);
        
        flash('success', 'Ownership claim approved successfully!');
    } else {
        // Mark as rejected
        db_execute("UPDATE business_claims SET status = 'rejected' WHERE id = ?", [$claim_id]);
        flash('info', 'Ownership claim rejected.');
    }

} catch (Exception $e) {
    error_log("Claim Process Error: " . $e->getMessage());
    flash('error', 'Processing failed: ' . $e->getMessage());
}

redirect('/admin/claims');
