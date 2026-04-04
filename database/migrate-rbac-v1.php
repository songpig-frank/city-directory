<?php
/**
 * Migration: Role-Based Access Control (RBAC)
 * Adds `permissions`, `roles`, and `role_permissions` tables.
 */

require_once __DIR__ . '/../includes/config-loader.php';
require_once __DIR__ . '/../includes/db.php';

echo "<pre>🚀 Starting RBAC (Duties & Groups) Migration...\n\n";

try {
    $cfg = config();
    $driver = $cfg['db_driver'] ?? 'mysql';
    $is_sqlite = ($driver === 'sqlite');

    // 1. Create permissions table (The "Duties")
    echo "📝 Creating permissions table...\n";
    $sql_perm = $is_sqlite 
        ? "CREATE TABLE IF NOT EXISTS permissions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            slug TEXT UNIQUE NOT NULL,
            name TEXT NOT NULL,
            duty_group TEXT NOT NULL,
            description TEXT
          );"
        : "CREATE TABLE IF NOT EXISTS permissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            slug VARCHAR(50) UNIQUE NOT NULL,
            name VARCHAR(100) NOT NULL,
            duty_group VARCHAR(50) NOT NULL,
            description TEXT
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    db_execute($sql_perm);

    // 2. Create roles table (The "Duty Groups")
    echo "📝 Creating roles table...\n";
    $sql_roles = $is_sqlite
        ? "CREATE TABLE IF NOT EXISTS roles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            slug TEXT UNIQUE NOT NULL,
            name TEXT NOT NULL,
            description TEXT,
            is_system INTEGER DEFAULT 0
          );"
        : "CREATE TABLE IF NOT EXISTS roles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            slug VARCHAR(50) UNIQUE NOT NULL,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            is_system TINYINT(1) DEFAULT 0
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    db_execute($sql_roles);

    // 3. Create role_permissions junction table
    echo "📝 Creating role_permissions pivot...\n";
    $sql_pivot = $is_sqlite
        ? "CREATE TABLE IF NOT EXISTS role_permissions (
            role_id INTEGER NOT NULL,
            permission_id INTEGER NOT NULL,
            PRIMARY KEY (role_id, permission_id)
          );"
        : "CREATE TABLE IF NOT EXISTS role_permissions (
            role_id INT NOT NULL,
            permission_id INT NOT NULL,
            PRIMARY KEY (role_id, permission_id)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    db_execute($sql_pivot);

    // 4. Seed Duties (Permissions)
    echo "🌱 Seeding individual Duties...\n";
    $duties = [
        // Listings
        ['listings:view', 'View Listings', 'Listings'],
        ['listings:edit', 'Edit/Save Listings', 'Listings'],
        ['listings:approve', 'Approve/Reject Listings', 'Listings'],
        ['listings:delete', 'Delete Listings', 'Listings'],
        // Categories
        ['categories:manage', 'Manage Categories', 'Content'],
        // Blog
        ['blog:manage', 'Manage Blog Posts', 'Content'],
        // Claims
        ['claims:manage', 'Process Business Claims', 'Moderation'],
        // Reviews
        ['reviews:manage', 'Moderate Customer Reviews', 'Moderation'],
        // Users
        ['users:view', 'View Users', 'Users'],
        ['users:manage', 'Manage Users & Roles', 'Users'],
        // System
        ['settings:manage', 'Manage Site Settings', 'System'],
        ['system:logs', 'View System Logs/Crawl', 'System'],
    ];

    foreach ($duties as $d) {
        $exists = db_value("SELECT id FROM permissions WHERE slug = ?", [$d[0]]);
        if (!$exists) {
            db_execute("INSERT INTO permissions (slug, name, duty_group) VALUES (?, ?, ?)", [$d[0], $d[1], $d[2]]);
        }
    }

    // 5. Seed Duty Groups (Roles)
    echo "🌱 Seeding initial Duty Groups (Roles)...\n";
    $roles = [
        ['super_admin', 'Super Admin', 'Full access to all segments.', 1],
        ['content_manager', 'Content Manager', 'Duty: Listings, Blog, Categories.', 0],
        ['moderator', 'Moderator', 'Duty: Claims, Reviews, Messages.', 0],
        ['support', 'Support Staff', 'Duty: View Listings, View Users, Messages.', 0],
    ];

    foreach ($roles as $r) {
        $exists = db_value("SELECT id FROM roles WHERE slug = ?", [$r[0]]);
        if (!$exists) {
            db_execute("INSERT INTO roles (slug, name, description, is_system) VALUES (?, ?, ?, ?)", [$r[0], $r[1], $r[2], $r[3]]);
        }
    }

    // 6. Assign Super Admin all permissions
    echo "👑 Granting all duties to Super Admin...\n";
    $super_id = db_value("SELECT id FROM roles WHERE slug = 'super_admin'");
    $all_perms = db_query("SELECT id FROM permissions");
    foreach ($all_perms as $p) {
        $exists = db_value("SELECT 1 FROM role_permissions WHERE role_id = ? AND permission_id = ?", [$super_id, $p['id']]);
        if (!$exists) {
            db_execute("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)", [$super_id, $p['id']]);
        }
    }

    // 7. Sync current Admin users to Super Admin role
    echo "🔄 Syncing current Admin users to Super Admin role...\n";
    db_execute("UPDATE users SET role = 'super_admin' WHERE role = 'admin'");

    echo "\n🎉 RBAC Migration Successful!\n";

} catch (Exception $e) {
    echo "\n❌ Migration Failed: " . $e->getMessage() . "\n";
}

echo "</pre>";
