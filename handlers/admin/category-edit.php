<?php
/**
 * Admin: Category Add/Edit Form
 */
auth_require('admin');

$id = $params['id'] ?? null;
$category = null;

if ($id) {
    $category = db_row("SELECT * FROM categories WHERE id = ?", [$id]);
    if (!$category) {
        flash('error', 'Category not found.');
        redirect('/admin/categories');
    }
}

echo render_page('admin/category-edit', [
    'title'    => $id ? 'Edit Category' : 'Add New Category',
    'category' => $category
]);
