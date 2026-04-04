<?php
/**
 * Admin: Delete Category
 */
auth_require('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/admin/categories');

$id = $_POST['id'] ?? null;
if (!$id) redirect('/admin/categories');

// Prevent deleting if it's the only category or if it has many dependencies?
// Better: reassign listings to a default category if any exist.
$listings_count = db_value("SELECT COUNT(*) FROM listings WHERE category_id = ?", [$id]);

if ($listings_count > 0) {
    // Attempt to find a 'General' or other category to move them to
    $fallback_id = db_value("SELECT id FROM categories WHERE slug != ? AND type = 'business' LIMIT 1", [$id]);
    if ($fallback_id) {
        db_execute("UPDATE listings SET category_id = ? WHERE category_id = ?", [$fallback_id, $id]);
    } else {
        flash('error', 'Cannot delete the only available category with active listings.');
        redirect('/admin/categories');
    }
}

db_execute("DELETE FROM categories WHERE id = ?", [$id]);
flash('success', 'Category deleted successfully.');
redirect('/admin/categories');
