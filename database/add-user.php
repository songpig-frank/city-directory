<?php
require_once __DIR__ . '/../includes/config-loader.php';
require_once __DIR__ . '/../includes/db.php';
$pdo = db();
$hash = password_hash('user123', PASSWORD_BCRYPT, ['cost' => 12]);
$exists = $pdo->query("SELECT COUNT(*) FROM users WHERE email = 'juan@example.com'")->fetchColumn();
if (!$exists) {
    $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)")
        ->execute(['Juan (Regular User)', 'juan@example.com', $hash, 'user']);
    echo "Regular user created.\n";
} else {
    echo "Regular user already exists.\n";
}
