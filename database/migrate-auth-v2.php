<?php
/**
 * Migration: Security & Account Management
 * Adds `reset_token`, `reset_expires`, `failed_logins`, and `locked_until` to `users`.
 */

require_once __DIR__ . '/../includes/config-loader.php';
require_once __DIR__ . '/../includes/db.php';

echo "<pre>🚀 Starting Security & Account Migration...\n\n";

try {
    $cfg = config();
    $driver = $cfg['db_driver'] ?? 'mysql';
    $is_sqlite = ($driver === 'sqlite');

    // 1. Update `users` table
    echo "📝 Updating `users` table with security columns...\n";
    if ($is_sqlite) {
        $table_info = db_query("PRAGMA table_info(`users`)");
        $cols = array_column($table_info, 'name');
        
        $new_cols = [
            'reset_token'   => 'TEXT',
            'reset_expires' => 'TEXT',
            'failed_logins' => 'INTEGER DEFAULT 0',
            'locked_until'  => 'TEXT'
        ];

        foreach ($new_cols as $col => $type) {
            if (!in_array($col, $cols)) {
                db_execute("ALTER TABLE `users` ADD COLUMN `{$col}` {$type};");
                echo "✅ Added `{$col}` (SQLite).\n";
            }
        }
    } else {
        $existing_cols = db_query("SHOW COLUMNS FROM `users` LIKE 'reset_token'");
        if (empty($existing_cols)) {
            db_execute("ALTER TABLE `users` ADD COLUMN `reset_token` VARCHAR(100) AFTER `social_links`;");
            echo "✅ Added `reset_token` (MySQL).\n";
        }
        $existing_cols = db_query("SHOW COLUMNS FROM `users` LIKE 'reset_expires'");
        if (empty($existing_cols)) {
            db_execute("ALTER TABLE `users` ADD COLUMN `reset_expires` DATETIME AFTER `reset_token`;");
            echo "✅ Added `reset_expires` (MySQL).\n";
        }
        $existing_cols = db_query("SHOW COLUMNS FROM `users` LIKE 'failed_logins'");
        if (empty($existing_cols)) {
            db_execute("ALTER TABLE `users` ADD COLUMN `failed_logins` INT DEFAULT 0 AFTER `reset_expires`;");
            echo "✅ Added `failed_logins` (MySQL).\n";
        }
        $existing_cols = db_query("SHOW COLUMNS FROM `users` LIKE 'locked_until'");
        if (empty($existing_cols)) {
            db_execute("ALTER TABLE `users` ADD COLUMN `locked_until` DATETIME NULL AFTER `failed_logins`;");
            echo "✅ Added `locked_until` (MySQL).\n";
        }
    }

    echo "\n🎉 Security Migration Successful!\n";

} catch (Exception $e) {
    echo "\n❌ Migration Failed: " . $e->getMessage() . "\n";
}

echo "</pre>";
