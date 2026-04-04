<?php
/**
 * Admin: Save User Configuration
 */
auth_require('users:manage');

if (!csrf_validate()) {
    flash('danger', 'Invalid security token.');
    header('Location: /admin/users');
    exit;
}

$user_id = (int)($_POST['user_id'] ?? 0);
$role_slug = $_POST['role'] ?? 'user';
$is_active = (int)($_POST['is_active'] ?? 1);
$new_password = $_POST['new_password'] ?? '';

// Basic Validation
$user = db_row("SELECT * FROM users WHERE id = ?", [$user_id]);
if (!$user) {
    flash('danger', 'User not found.');
    header('Location: /admin/users');
    exit;
}

// Don't allow demoting the last super_admin (safety)
if ($user['role'] === 'super_admin' && $role_slug !== 'super_admin') {
    $admin_count = db_value("SELECT COUNT(*) FROM users WHERE role = 'super_admin' AND is_active = 1");
    if ($admin_count <= 1) {
        flash('danger', 'Cannot demote the last active Super Admin.');
        header('Location: /admin/users');
        exit;
    }
}

try {
    // Update basic fields
    db_execute(
        "UPDATE users SET role = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?",
        [$role_slug, $is_active, $user_id]
    );

    // Update password if provided
    if (!empty($new_password)) {
        if (strlen($new_password) < 6) {
            flash('warning', 'User updated, but password was too short (min 6 characters).');
        } else {
            $hash = auth_hash($new_password);
            db_execute("UPDATE users SET password_hash = ? WHERE id = ?", [$hash, $user_id]);
            flash('success', 'User configuration and password updated successfully.');
        }
    } else {
        flash('success', 'User configuration updated successfully.');
    }

} catch (Exception $e) {
    flash('danger', 'Error updating user: ' . $e->getMessage());
}

header('Location: /admin/users');
exit;
