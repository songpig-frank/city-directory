<?php
/**
 * Migration: SEO & Social Sharing (Open Graph)
 * Adds `og_image` and `og_description` to listings.
 */

require_once __DIR__ . '/../includes/config-loader.php';
require_once __DIR__ . '/../includes/db.php';

echo "<pre>🚀 Starting SEO & Open Graph Migration...\n\n";

try {
    $cfg = config();
    $driver = $cfg['db_driver'] ?? 'mysql';
    $is_sqlite = ($driver === 'sqlite');

    // 1. Update `listings` table
    echo "📝 Updating `listings` table...\n";
    if ($is_sqlite) {
        $table_info = db_query("PRAGMA table_info(`listings`)");
        $cols = array_column($table_info, 'name');
        
        if (!in_array('og_image', $cols)) {
            db_execute("ALTER TABLE `listings` ADD COLUMN `og_image` TEXT;");
            echo "✅ Added `og_image` (SQLite).\n";
        }
        if (!in_array('og_title', $cols)) {
            db_execute("ALTER TABLE `listings` ADD COLUMN `og_title` TEXT;");
            echo "✅ Added `og_title` (SQLite).\n";
        }
        if (!in_array('og_description', $cols)) {
            db_execute("ALTER TABLE `listings` ADD COLUMN `og_description` TEXT;");
            echo "✅ Added `og_description` (SQLite).\n";
        }
    } else {
        $cols = db_query("SHOW COLUMNS FROM `listings` LIKE 'og_image'");
        if (empty($cols)) {
            db_execute("ALTER TABLE `listings` ADD COLUMN `og_image` VARCHAR(255) AFTER `youtube`;");
            echo "✅ Added `og_image` (MySQL).\n";
        }
        $cols = db_query("SHOW COLUMNS FROM `listings` LIKE 'og_title'");
        if (empty($cols)) {
            db_execute("ALTER TABLE `listings` ADD COLUMN `og_title` VARCHAR(255) AFTER `og_image`;");
            echo "✅ Added `og_title` (MySQL).\n";
        }
        $cols = db_query("SHOW COLUMNS FROM `listings` LIKE 'og_description'");
        if (empty($cols)) {
            db_execute("ALTER TABLE `listings` ADD COLUMN `og_description` TEXT AFTER `og_title`;");
            echo "✅ Added `og_description` (MySQL).\n";
        }
    }

    // 2. Update `categories` table
    echo "📂 Updating `categories` table...\n";
    if ($is_sqlite) {
        $table_info = db_query("PRAGMA table_info(`categories`)");
        $cols = array_column($table_info, 'name');
        if (!in_array('default_image', $cols)) {
            db_execute("ALTER TABLE `categories` ADD COLUMN `default_image` TEXT;");
            echo "✅ Added `default_image` (SQLite).\n";
        }
    } else {
        $cols = db_query("SHOW COLUMNS FROM `categories` LIKE 'default_image'");
        if (empty($cols)) {
            db_execute("ALTER TABLE `categories` ADD COLUMN `default_image` VARCHAR(255) AFTER `icon`;");
            echo "✅ Added `default_image` (MySQL).\n";
        }
    }

    // 3. Update `site_settings` table defaults
    echo "⚙️ Initializing SEO settings...\n";
    $seo_defaults = [
        'default_og_image'       => '',
        'default_og_description' => config('description')
    ];

    foreach ($seo_defaults as $key => $val) {
        $exists = db_value("SELECT COUNT(*) FROM site_settings WHERE setting_key = ?", [$key]);
        if (!$exists) {
            db_execute("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)", [$key, $val]);
            echo "➕ Initialized metadata setting: {$key}\n";
        }
    }

    echo "\n🎉 SEO Migration Successful!\n";

} catch (Exception $e) {
    echo "\n❌ Migration Failed: " . $e->getMessage() . "\n";
}

echo "</pre>";
