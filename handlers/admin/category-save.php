<?php
/**
 * Admin: Save Category
 */
auth_require('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/admin/categories');

$id = $_POST['id'] ?? null;
$name = trim($_POST['name'] ?? '');
$slug = trim($_POST['slug'] ?? '');
$type = $_POST['type'] ?? 'business';
$icon = trim($_POST['icon'] ?? 'tag');
$sort = (int)($_POST['sort_order'] ?? 0);
$active = isset($_POST['is_active']) ? 1 : 0;

if (empty($name)) {
    flash('error', 'Category name is required.');
    redirect($id ? "/admin/categories/edit/{$id}" : "/admin/categories/add");
}

if (empty($slug)) $slug = slugify($name);

if ($id) {
    db_execute(
        "UPDATE categories SET name = ?, slug = ?, type = ?, icon = ?, sort_order = ?, is_active = ? WHERE id = ?",
        [$name, $slug, $type, $icon, $sort, $active, $id]
    );
    flash('success', 'Category updated successfully.');
} else {
    db_execute(
        "INSERT INTO categories (name, slug, type, icon, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?)",
        [$name, $slug, $type, $icon, $sort, $active]
    );
    flash('success', 'Category created successfully.');
}

redirect('/admin/categories');
