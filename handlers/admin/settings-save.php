<?php
/**
 * Admin: Save Settings
 */
auth_require('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

$keys = [
    'site_name', 'description', 'city', 'province', 'currency',
    'theme_primary', 'hero_title', 'hero_subtitle'
];

foreach ($keys as $key) {
    if (isset($_POST[$key])) {
        $val = trim($_POST[$key]);
        db_execute(
            "INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) 
             ON CONFLICT(setting_key) DO UPDATE SET setting_value = excluded.setting_value",
            [$key, $val]
        );
    }
}

flash('success', 'Settings saved successfully. Changes are now live.');
header('Location: /admin/settings');
exit;
