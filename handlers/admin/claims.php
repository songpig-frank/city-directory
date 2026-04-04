<?php
/**
 * Handler: Admin Business Claims
 */
auth_require('claims:manage');

$status = $_GET['status'] ?? 'pending';
$claims = db_query(
    "SELECT c.*, l.name as listing_name, l.slug as listing_slug, u.name as user_name, u.email as user_email
     FROM business_claims c
     JOIN listings l ON c.listing_id = l.id
     JOIN users u ON c.user_id = u.id
     WHERE c.status = ?
     ORDER BY c.created_at DESC",
    [$status]
);

echo render_page('admin/claims', [
    'title'  => 'Manage Business Claims — Admin',
    'claims' => $claims,
    'status' => $status,
    'path'   => 'admin/claims',
]);
