<?php
/**
 * CityDirectory — Initial Setup Script
 * Run once to create admin user and verify database.
 * Usage: php database/setup.php
 */

require_once __DIR__ . '/../includes/config-loader.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

echo "=== CityDirectory Setup ===\n\n";

// Check DB connection
try {
    db();
    echo "✓ Database connection OK\n";
} catch (Exception $e) {
    die("✗ Database connection failed: " . $e->getMessage() . "\n");
}

// Check if admin exists
$admin = db_row("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
if ($admin) {
    echo "✓ Admin user already exists\n";
} else {
    // Create default admin
    $id = auth_create_user('Admin', 'admin@' . config('domain'), 'changeme123', 'admin');
    echo "✓ Admin user created\n";
    echo "  Email: admin@" . config('domain') . "\n";
    echo "  Password: changeme123\n";
    echo "  ⚠️  CHANGE THIS PASSWORD IMMEDIATELY!\n";
}

echo "\n✓ Setup complete. Next steps:\n";
echo "  1. Run: php database/seed-categories.php tampakan\n";
echo "  2. Visit: " . config('base_url') . "/admin\n";
echo "  3. Login and change the admin password\n\n";
