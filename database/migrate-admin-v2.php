<?php
/**
 * Migration: Admin & Portal Overhaul (v2.0)
 * Adds multi-category support and trusted user flags.
 */

require_once __DIR__ . '/../includes/config-loader.php';
require_once __DIR__ . '/../includes/db.php';

echo "<pre>🚀 Starting Admin V2.0 Migration...\n\n";

try {
    $cfg = config();
    $driver = $cfg['db_driver'] ?? 'mysql';
    $is_sqlite = ($driver === 'sqlite');

    // 1. Create listing_categories pivot table
    echo "🏗️ Creating `listing_categories` pivot table...\n";
    $table_options = $is_sqlite ? "" : " ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    db_execute("CREATE TABLE IF NOT EXISTS `listing_categories` (
        `listing_id` INT UNSIGNED NOT NULL,
        `category_id` INT UNSIGNED NOT NULL,
        PRIMARY KEY (`listing_id`, `category_id`),
        FOREIGN KEY (`listing_id`) REFERENCES `listings`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
    )$table_options;");

    // 2. Add `parent_id` to `categories` table (missing in initial v2.0 schema)
    echo "🏗️ Adding `parent_id` to `categories` table...\n";
    if ($is_sqlite) {
        $table_info = db_query("PRAGMA table_info(`categories`)");
        $exists = false;
        foreach ($table_info as $col) {
            if ($col['name'] === 'parent_id') { $exists = true; break; }
        }
        if (!$exists) {
            db_execute("ALTER TABLE `categories` ADD COLUMN `parent_id` INTEGER DEFAULT NULL;");
            echo "✅ Added `parent_id` column (SQLite).\n";
        } else {
            echo "ℹ️ `parent_id` column already exists.\n";
        }
    } else {
        $cols = db_query("SHOW COLUMNS FROM `categories` LIKE 'parent_id'");
        if (empty($cols)) {
            db_execute("ALTER TABLE `categories` ADD COLUMN `parent_id` INT UNSIGNED DEFAULT NULL AFTER `description`;");
            echo "✅ Added `parent_id` column (MySQL).\n";
        } else {
            echo "ℹ️ `parent_id` column already exists.\n";
        }
    }

    // 3. Add `is_trusted` to `users` table
    echo "🛂 Adding `is_trusted` flag to `users` table...\n";
    
    if ($is_sqlite) {
        // SQLite doesn't support SHOW COLUMNS or AFTER easily. 
        // We can check existence via PRAGMA table_info
        $table_info = db_query("PRAGMA table_info(`users`)");
        $exists = false;
        foreach ($table_info as $col) {
            if ($col['name'] === 'is_trusted') { $exists = true; break; }
        }
        if (!$exists) {
            db_execute("ALTER TABLE `users` ADD COLUMN `is_trusted` TINYINT(1) NOT NULL DEFAULT 0;");
            echo "✅ Added `is_trusted` column (SQLite).\n";
        } else {
            echo "ℹ️ `is_trusted` column already exists.\n";
        }
    } else {
        $cols = db_query("SHOW COLUMNS FROM `users` LIKE 'is_trusted'");
        if (empty($cols)) {
            db_execute("ALTER TABLE `users` ADD COLUMN `is_trusted` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_active`;");
            echo "✅ Added `is_trusted` column (MySQL).\n";
        } else {
            echo "ℹ️ `is_trusted` column already exists.\n";
        }
    }

    // 3. Ensure `site_settings` table is ready
    echo "⚙️ Verifying `site_settings` table...\n";
    db_execute("CREATE TABLE IF NOT EXISTS `site_settings` (
        `setting_key` VARCHAR(100) PRIMARY KEY,
        `setting_value` TEXT,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )$table_options;");

    // 4. Initialize common settings if missing
    $default_settings = [
        'google_analytics_id' => '',
        'google_adsense_id'   => '',
        'contact_email'       => 'admin@tampakan.com',
        'footer_text'         => '© ' . date('Y') . ' Tampakan Directory'
    ];

    foreach ($default_settings as $key => $val) {
        $exists = db_value("SELECT COUNT(*) FROM site_settings WHERE setting_key = ?", [$key]);
        if (!$exists) {
            db_execute("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)", [$key, $val]);
            echo "➕ Initialized setting: {$key}\n";
        }
    }

    echo "\n🎉 Migration Successful!\n";

} catch (Exception $e) {
    echo "\n❌ Migration Failed: " . $e->getMessage() . "\n";
}

echo "</pre>";
