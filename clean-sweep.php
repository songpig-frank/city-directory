<?php
/**
 * CityDirectory — The "Clean Sweep" Setup Script
 * Use this to build your database and admin user in one go.
 */

// 1. Load Bootstrap
require_once __DIR__ . '/includes/config-loader.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

echo "<div style='font-family:sans-serif; background:#111; color:#eee; padding:20px; border-radius:8px; line-height:1.6; max-width:800px; margin:20px auto; border:1px solid #333;'>";
echo "<h2 style='color:#00ffcc; margin-top:0;'>✨ Tampakan Clean Sweep Setup</h2>";

try {
    $db = db();
    echo "✓ Database Connected (" . config('db_driver') . ")<br>";

    // 2. Load Schema
    $schema_file = __DIR__ . '/database/schema.sql';
    if (!file_exists($schema_file)) {
        die("<p style='color:red;'>✗ schema.sql not found at: $schema_file</p>");
    }

    $sql = file_get_contents($schema_file);
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    echo "<h4 style='color:#00ffcc;'>1. Building Tables...</h4>";
    foreach ($statements as $statement) {
        if (empty($statement)) continue;
        try {
            $db->exec($statement);
            if (preg_match('/CREATE TABLE (?:IF NOT EXISTS )?[`"]?(\w+)[`"]?/i', $statement, $matches)) {
                echo "— Table <b>" . $matches[1] . "</b> created/verified.<br>";
            }
        } catch (PDOException $e) {
            // Ignore "Table already exists" errors
            if (strpos($e->getMessage(), 'already exists') === false) {
                echo "— <span style='color:#ffaa00;'>Notice: " . $e->getMessage() . "</span><br>";
            }
        }
    }

    echo "<h4 style='color:#00ffcc;'>2. Creating Admin...</h4>";
    $admin = db_row("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
    if ($admin) {
        echo "✓ Admin user already exists.<br>";
    } else {
        $admin_email = 'admin@' . config('domain');
        $id = auth_create_user('Admin', $admin_email, 'changeme123', 'admin');
        echo "✓ Admin user created: <b>$admin_email</b> (Pass: changeme123)<br>";
    }

    echo "<h3 style='color:#00ffcc; border-top:1px solid #333; padding-top:20px;'>🎉 All Done!</h3>";
    echo "<p>Your site is ready. You can now login at: <a href='/login' style='color:#00ffcc;'>tampakan.com/login</a></p>";
    echo "<p style='color:#ffaa00;'><b>⚠️ SECURITY WARNING:</b> Please delete this file (<code>clean-sweep.php</code>) from your server now!</p>";

} catch (Exception $e) {
    echo "<p style='color:red;'>✗ Critical Error: " . $e->getMessage() . "</p>";
}

echo "</div>";
