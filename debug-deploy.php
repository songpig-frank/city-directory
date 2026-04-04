<?php
/**
 * DEBUG: Deployment Sync Checker
 */
header('Content-Type: text/plain');

echo "--- Server Codebase Debug ---\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";
echo "Path: " . __DIR__ . "\n";

// 1. Check Git Branch (if available)
$branch = shell_exec('git rev-parse --abbrev-ref HEAD 2>&1');
echo "Current Branch: " . trim($branch) . "\n";

// 2. Check for "V3 Active" in dashboard
$dashboard = __DIR__ . '/templates/admin/dashboard.php';
if (file_exists($dashboard)) {
    $content = file_get_contents($dashboard);
    if (strpos($content, '(V3 Active)') !== false) {
        echo "V3 Status: ✅ Code contains '(V3 Active)' marker.\n";
    } else {
        echo "V3 Status: ❌ Code does NOT contain '(V3 Active)' marker.\n";
    }
} else {
    echo "Filesystem Error: Dashboard template not found at $dashboard\n";
}

// 3. User Table Info
require_once __DIR__ . '/includes/config-loader.php';
require_once __DIR__ . '/includes/db.php';
try {
    $count = db_value("SELECT COUNT(*) FROM users");
    echo "User Count in DB: $count\n";
} catch (Exception $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}
