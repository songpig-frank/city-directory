<?php
/**
 * CityDirectory — Production Database Initialization
 * This script runs the schema.sql to create tables in the production SQLite DB.
 */

// Show errors for this specific script
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/config-loader.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: text/plain');
echo "=== CityDirectory Production Database Initialization ===\n\n";

try {
    $pdo = db();
    echo "✓ Database connection OK\n";
    
    $schema_file = __DIR__ . '/schema.sql';
    if (!file_exists($schema_file)) {
        throw new Exception("Schema file not found at: {$schema_file}");
    }
    
    $sql = file_get_contents($schema_file);
    if (empty($sql)) {
        throw new Exception("Schema file is empty!");
    }
    
    // Split the SQL into individual statements
    // This is a simple split—robust SQL parsers avoid this, but for our schema.sql it works.
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    echo "Creating tables... \n";
    $count = 0;
    foreach ($statements as $statement) {
        $pdo->exec($statement);
        $count++;
    }
    
    echo "✓ Processed {$count} SQL statements successfully.\n";
    echo "✓ All tables created.\n\n";
    
    echo "NEXT STEPS:\n";
    echo "1. Visit https://tampakan.com/database/setup.php to create the Admin user.\n";
    echo "2. Visit https://tampakan.com to view the live site.\n";
    echo "3. RE-LOCK your .htaccess file (remove the # you added earlier).\n";
    echo "4. DELETE this file (database/init-db.php) for security.\n";

} catch (Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}
