<?php
/**
 * Admin Action: Import Crawler Results
 */
require_once __DIR__ . '/../../includes/config-loader.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';

auth_require('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/crawler');
}

if (!csrf_validate()) {
    flash('error', 'Security token invalid.');
    redirect('/admin/crawler');
}

$import_indices = $_POST['import_idx'] ?? [];
$all_data = $_POST['data'] ?? [];

if (empty($import_indices)) {
    flash('info', 'No listings selected for import.');
    redirect('/admin/crawler');
}

$imported_count = 0;
$skipped_count = 0;

try {
    foreach ($import_indices as $idx) {
        $item = $all_data[$idx] ?? null;
        if (!$item || empty($item['name']) || empty($item['category_id'])) continue;

        $slug = slugify($item['name']);
        
        // Check for duplicates
        if (db_value("SELECT id FROM listings WHERE slug = ?", [$slug])) {
            $skipped_count++;
            continue;
        }

        db_execute(
            "INSERT INTO listings (name, slug, description, address, phone, website, facebook, category_id, status, type) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active', 'business')",
            [
                $item['name'],
                $slug,
                $item['description'] ?? '',
                $item['address'] ?? '',
                $item['phone'] ?? '',
                $item['website'] ?? '',
                $item['facebook'] ?? '',
                (int)$item['category_id']
            ]
        );
        $imported_count++;
    }

    // Clear session results after successful import
    unset($_SESSION['crawl_results']);

    $msg = "Successfully imported $imported_count listings!";
    if ($skipped_count > 0) $msg .= " ($skipped_count skipped due to duplicates)";
    flash('success', $msg);

} catch (Exception $e) {
    error_log("Import Error: " . $e->getMessage());
    flash('error', 'Import failed: ' . $e->getMessage());
}

redirect('/admin/crawler');
