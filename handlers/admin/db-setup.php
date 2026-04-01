<?php
/**
 * CityDirectory — Admin User Setup Handler
 * Run via /db-setup to create the initial admin user.
 */

echo "<div style='font-family:sans-serif; background:#111; color:#eee; padding:20px; border-radius:8px; line-height:1.6;'>";
echo "<h3 style='color:#00ffcc;'>=== Admin User Setup ===</h3>";

try {
    $db = db();
    
    // Check if admin already exists
    $admin = db_row("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
    
    if ($admin) {
        echo "<p style='color:#00ffcc;'>✓ Admin user already exists.</p>";
    } else {
        // Create default admin
        $admin_email = 'admin@' . config('domain');
        $id = auth_create_user('Admin', $admin_email, 'changeme123', 'admin');
        
        echo "<p style='color:#00ffcc; font-weight:bold;'>✓ Admin user created successfully!</p>";
        echo "<ul style='list-style:none; padding:0; margin:0;'>";
        echo "<li><b>Email:</b> $admin_email</li>";
        echo "<li><b>Password:</b> changeme123</li>";
        echo "</ul>";
        echo "<p style='color:#ffaa00;'>⚠️ PLEASE CHANGE THIS PASSWORD IMMEDIATELY IN THE ADMIN DASHBOARD!</p>";
    }
    
    echo "<p><a href='/admin' style='display:inline-block; margin-top:10px; background:#00ffcc; color:#111; padding:10px 20px; border-radius:4px; text-decoration:none; font-weight:bold;'>Go to Admin Dashboard</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color:red;'>✗ Critical Error: " . $e->getMessage() . "</p>";
}

echo "</div>";
