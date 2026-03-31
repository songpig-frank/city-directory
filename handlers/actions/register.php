<?php
/**
 * Action: User Registration
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

if (!csrf_validate()) {
    flash('danger', 'Security check failed. Please try again.');
    header('Location: /register');
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

if (empty($name) || empty($email) || empty($password)) {
    flash('danger', 'All fields are required.');
    header('Location: /register');
    exit;
}

if ($password !== $password_confirm) {
    flash('danger', 'Passwords do not match.');
    header('Location: /register');
    exit;
}

// Check if email exists
$exists = db_row("SELECT id FROM users WHERE email = ?", [$email]);
if ($exists) {
    flash('danger', 'Email already registered. Try logging in.');
    header('Location: /register');
    exit;
}

// Hash password
$hash = auth_hash($password);

// Insert user (Self-registered users are 'user' role by default)
db_execute(
    "INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, 'user')",
    [$name, $email, $hash]
);

$user_id = db_last_id();

// Log them in
$_SESSION['user_id'] = $user_id;
$_SESSION['user_role'] = 'user';
$_SESSION['user_name'] = $name;

flash('success', 'Welcome to ' . config('city') . '! Your account has been created.');
header('Location: /');
exit;
