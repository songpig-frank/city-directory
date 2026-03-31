<?php
/**
 * Handler: Login page (GET)
 */
if (auth_check()) {
    header('Location: ' . (auth_has_role('admin', 'manager') ? '/admin' : '/'));
    exit;
}

echo render_page('login', [
    'title' => __('login_title') . ' — ' . config('site_name'),
    'path'  => 'login',
]);
