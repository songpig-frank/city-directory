<?php
/**
 * Admin: Settings Dashboard
 */
auth_require('admin');

$title = 'Site Settings - Admin';
$settings = db_query("SELECT setting_key, setting_value FROM site_settings");
$current_settings = [];
foreach ($settings as $row) {
    $current_settings[$row['setting_key']] = $row['setting_value'];
}

$content = render('admin/settings', [
    'settings' => $current_settings
]);

echo render('layout', [
    'title' => $title,
    'content' => $content
]);
