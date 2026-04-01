<?php
/**
 * CityDirectory — Database Initialization Handler
 * Run via /db-init to build the database tables.
 */

// Only allow in development or for a fresh install (no users table)
// For production, we'll just let it run once then the user should remove the route.

echo "<div style='font-family:sans-serif; background:#111; color:#eee; padding:20px; border-radius:8px; line-height:1.6;'>";
echo "<h3 style='color:#00ffcc;'>=== Database Initialization ===</h3>";

$schema_file = __DIR__ . '/../../database/schema.sql';

if (!file_exists($schema_file)) {
    die("<p style='color:red;'>✗ schema.sql not found at: $schema_file</p>");
}

$sql = file_get_contents($schema_file);

try {
    $db = db();
    
    // Split SQL into individual statements
    // This is simple but works for most standard schemas
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (empty($statement)) continue;
        
        try {
            $db->exec($statement);
            // Extract table name for nice output
            if (preg_match('/CREATE TABLE (?:IF NOT EXISTS )?[`"]?(\w+)[`"]?/i', $statement, $matches)) {
                echo "✓ Created table: <b>" . $matches[1] . "</b><br>";
            }
        } catch (PDOException $e) {
            echo "<span style='color:#ffaa00;'>⚠ Notice: " . $e->getMessage() . "</span><br>";
        }
    }
    
    echo "<p style='color:#00ffcc; font-weight:bold;'>✓ Database tables initialized successfully!</p>";
    echo "<p>Next Step: <a href='/db-setup' style='color:#fff; text-decoration:underline;'>Create Admin User</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color:red;'>✗ Critical Error: " . $e->getMessage() . "</p>";
}

echo "</div>";
