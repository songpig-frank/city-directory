<?php
/**
 * CityDirectory — Authentication & Session Management
 */

function auth_init(): void {
    if (session_status() === PHP_SESSION_NONE) {
        $lifetime = config('session_lifetime') ?? 86400;
        ini_set('session.gc_maxlifetime', $lifetime);
        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path'     => '/',
            'secure'   => $isSecure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

/**
 * Attempt login. Returns user array on success, null on failure.
 */
function auth_login(string $email, string $password): ?array {
    $user = db_row("SELECT * FROM users WHERE email = ? AND is_active = 1", [$email]);
    if (!$user || !password_verify($password, $user['password_hash'])) {
        return null;
    }

    // Regenerate session ID to prevent fixation
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role']  = $user['role'];
    $_SESSION['user_name']  = $user['name'];
    $_SESSION['user_trusted'] = $user['is_trusted'] ?? 0;

    // Update last login
    db_execute("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?", [$user['id']]);

    return $user;
}

/**
 * Log out the current user.
 */
function auth_logout(): void {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

/**
 * Check if a user is logged in.
 */
function auth_check(): bool {
    return !empty($_SESSION['user_id']);
}

/**
 * Get the current user's data.
 */
function auth_user(): ?array {
    if (!auth_check()) return null;
    return db_row("SELECT id, name, email, role, avatar, bio, phone, social_links FROM users WHERE id = ?", [$_SESSION['user_id']]);
}

/**
 * Get the current user's ID.
 */
function auth_id(): ?int {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get the current user's role.
 */
function auth_role(): ?string {
    return $_SESSION['user_role'] ?? null;
}

/**
 * Check if current user has one of the given roles.
 */
function auth_has_role(string ...$roles): bool {
    return auth_check() && in_array(auth_role(), $roles);
}

/**
 * Require login — redirect to login page if not authenticated.
 */
function auth_require(string ...$roles): void {
    if (!auth_check()) {
        header('Location: /login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
    if (!empty($roles) && !auth_has_role(...$roles)) {
        http_response_code(403);
        echo render('errors/403');
        exit;
    }
}

/**
 * Hash a password using bcrypt.
 */
function auth_hash(string $password): string {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => config('bcrypt_cost') ?? 12]);
}

/**
 * Create a new user.
 */
function auth_create_user(string $name, string $email, string $password, string $role = 'owner'): int {
    db_execute(
        "INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)",
        [$name, $email, auth_hash($password), $role]
    );
    return (int)db_last_id();
}
