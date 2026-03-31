<?php
/**
 * CityDirectory — Database Connection (PDO)
 * Singleton wrapper with prepared statement helpers.
 * Supports MySQL (production) and SQLite (local dev).
 */

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $cfg = config();
        $driver = $cfg['db_driver'] ?? 'mysql';

        if ($driver === 'sqlite') {
            $db_path = $cfg['db_path'] ?? __DIR__ . '/../database/local.sqlite';
            $pdo = new PDO("sqlite:{$db_path}", null, null, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            $pdo->exec('PRAGMA journal_mode=WAL');
            $pdo->exec('PRAGMA foreign_keys=ON');
        } else {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $cfg['db_host'],
                $cfg['db_name'],
                $cfg['db_charset']
            );
            $pdo = new PDO($dsn, $cfg['db_user'], $cfg['db_pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            ]);
        }
    }
    return $pdo;
}

/**
 * Execute a prepared query and return all rows.
 */
function db_query(string $sql, array $params = []): array {
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Execute a prepared query and return a single row.
 */
function db_row(string $sql, array $params = []): ?array {
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row ?: null;
}

/**
 * Execute a prepared query and return a single column value.
 */
function db_value(string $sql, array $params = []) {
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn();
}

/**
 * Execute a prepared INSERT/UPDATE/DELETE and return affected rows.
 */
function db_execute(string $sql, array $params = []): int {
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
}

/**
 * Get the last inserted ID.
 */
function db_last_id(): string {
    return db()->lastInsertId();
}

/**
 * Get average rating and review count for a listing.
 */
function db_get_rating_summary(int $listing_id): array {
    return db_row(
        "SELECT COUNT(*) as count, AVG(rating) as average FROM reviews WHERE listing_id = ? AND is_approved = 1",
        [$listing_id]
    ) ?: ['count' => 0, 'average' => 0];
}

/**
 * Get approved reviews for a listing.
 */
function db_get_reviews(int $listing_id, int $limit = 10): array {
    return db_query(
        "SELECT * FROM reviews WHERE listing_id = ? AND is_approved = 1 ORDER BY created_at DESC LIMIT ?",
        [$listing_id, $limit]
    );
}
