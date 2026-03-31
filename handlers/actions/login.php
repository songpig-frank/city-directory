<?php
/**
 * Handler: Login (POST)
 */
if (!csrf_validate()) {
    flash('error', 'Invalid form submission.');
    header('Location: /login');
    exit;
}

if (!rate_limit('login', 10, 300)) {
    flash('error', 'Too many login attempts. Please wait.');
    header('Location: /login');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$user = auth_login($email, $password);

if (!$user) {
    flash('error', __('login_error'));
    header('Location: /login');
    exit;
}

$redirect = $_GET['redirect'] ?? '/';
if (auth_has_role('admin', 'manager')) {
    $redirect = '/admin';
}
header('Location: ' . $redirect);
exit;
