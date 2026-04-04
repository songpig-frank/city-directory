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
 * Attempt login. Returns user array on success, or an error string on failure.
 */
function auth_login(string $email, string $password): array|string {
    $user = db_row("SELECT * FROM users WHERE email = ?", [$email]);
    
    if (!$user) {
        return 'invalid_credentials';
    }

    // 1. Check if Account is DISABLED (Permanent)
    if (isset($user['is_active']) && (int)$user['is_active'] === 0) {
        return 'account_disabled';
    }

    // 2. Check if Account is LOCKED (Temporary)
    if (!empty($user['locked_until'])) {
        $locked_until = strtotime($user['locked_until']);
        if ($locked_until > time()) {
            return 'account_locked';
        }
    }

    // 3. Verify Password
    if (!password_verify($password, $user['password_hash'])) {
        // Increment failed logins
        $failed = ((int)($user['failed_logins'] ?? 0)) + 1;
        $lock_until = null;
        
        // Lock account for 30 mins after 5 failures
        if ($failed >= 5) {
            $lock_until = date('Y-m-d H:i:s', time() + 1800);
        }
        
        db_execute("UPDATE users SET failed_logins = ?, locked_until = ? WHERE id = ?", [$failed, $lock_until, $user['id']]);
        
        return $failed >= 5 ? 'account_locked' : 'invalid_credentials';
    }

    // 4. Success: Reset security counters
    db_execute("UPDATE users SET failed_logins = 0, locked_until = NULL, last_login = CURRENT_TIMESTAMP WHERE id = ?", [$user['id']]);

    // Regenerate session ID to prevent fixation
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role']  = $user['role'];
    $_SESSION['user_name']  = $user['name'];
    $_SESSION['user_trusted'] = $user['is_trusted'] ?? 0;

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
 * Role-Based Access Control: Check if current user has a specific duty.
 */
function auth_can(string $duty_slug): bool {
    if (!auth_check()) return false;
    
    // Super-admin always has all duties
    $user_role = auth_role();
    if ($user_role === 'super_admin') return true;
    
    // Cache perms in session for speed
    if (!isset($_SESSION['user_duties'])) {
        $user_id = auth_id();
        $duties = db_query("
            SELECT p.slug FROM permissions p
            JOIN role_permissions rp ON p.id = rp.permission_id
            JOIN roles r ON rp.role_id = r.id
            JOIN users u ON u.role = r.slug
            WHERE u.id = ?
        ", [$user_id]);
        $_SESSION['user_duties'] = array_column($duties, 'slug');
    }
    
    return in_array($duty_slug, $_SESSION['user_duties']);
}

/**
 * Middleware: Require a specific duty or abort with 403.
 */
function auth_require_duty(string $duty_slug): void {
    if (!auth_can($duty_slug)) {
        http_response_code(403);
        echo render('errors/403', ['title' => 'Access Denied', 'duty' => $duty_slug]);
        exit;
    }
}

/**
 * Check if current user has one of the given roles.
 */
function auth_has_role(string ...$roles): bool {
    return auth_check() && in_array(auth_role(), $roles);
}

/**
 * Require login — redirect to login page if not authenticated.
 * Now handles both Roles and Duties.
 */
function auth_require(...$requirements): void {
    if (!auth_check()) {
        header('Location: /login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
    
    if (empty($requirements)) return;

    // Check if any requirements are met
    foreach ($requirements as $req) {
        // If it looks like a duty (contains ':'), check duty
        if (strpos($req, ':') !== false) {
            if (auth_can($req)) return;
        } else {
            // Else check role
            if (auth_has_role($req)) return;
        }
    }

    // If we got here, no requirements were met
    http_response_code(403);
    echo render('errors/403', ['title' => 'Access Denied']);
    exit;
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
