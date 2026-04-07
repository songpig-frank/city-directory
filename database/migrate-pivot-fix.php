<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/config-loader.php'; // Required for config() inside db()

// Mock config() if config-loader.php is not enough or fails in CLI
if (!function_exists('config')) {
    function config($key = null) {
        $cfg = include __DIR__ . '/../config.php';
        return $key ? ($cfg[$key] ?? null) : $cfg;
    }
}

$pdo = db();
echo "🚀 Starting category pivot migration...\n";

try {
    // 1. Check if table exists 
    $table_exists = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='listing_categories'")->fetchColumn();
    
    if (!$table_exists) {
        echo "🔧 Creating listing_categories table...\n";
        $pdo->exec("CREATE TABLE listing_categories (
            listing_id INTEGER NOT NULL,
            category_id INTEGER NOT NULL,
            is_primary INTEGER DEFAULT 0,
            PRIMARY KEY (listing_id, category_id),
            FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
        )");
    } else {
        // Check if is_primary exists
        $columns = $pdo->query("PRAGMA table_info(listing_categories)")->fetchAll(PDO::FETCH_ASSOC);
        $has_primary = false;
        foreach ($columns as $col) {
            if ($col['name'] === 'is_primary') {
                $has_primary = true;
                break;
            }
        }
        
        if (!$has_primary) {
            echo "🔧 Adding 'is_primary' column via table recreation...\n";
            $pdo->exec("ALTER TABLE listing_categories RENAME TO listing_categories_old");
            $pdo->exec("CREATE TABLE listing_categories (
                listing_id INTEGER NOT NULL,
                category_id INTEGER NOT NULL,
                is_primary INTEGER DEFAULT 0,
                PRIMARY KEY (listing_id, category_id),
                FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
            )");
            $pdo->exec("INSERT INTO listing_categories (listing_id, category_id) SELECT listing_id, category_id FROM listing_categories_old");
            $pdo->exec("DROP TABLE listing_categories_old");
        }
    }

    // 2. Migrate data from listings table
    echo "📦 Migrating data from listings.category_id...\n";
    $stmt = $pdo->query("SELECT id, category_id FROM listings WHERE category_id IS NOT NULL");
    $listings_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $insert = $pdo->prepare("INSERT OR IGNORE INTO listing_categories (listing_id, category_id, is_primary) VALUES (?, ?, 1)");
    $count = 0;
    foreach ($listings_data as $l) {
        $insert->execute([$l['id'], $l['category_id']]);
        if ($insert->rowCount() > 0) $count++;
    }
    
    echo "✅ Successfully migrated {$count} listings to the pivot table.\n";
    
    // Verify any listings are now visible
    $visible_count = $pdo->query("SELECT COUNT(DISTINCT l.id) FROM listings l JOIN listing_categories lc ON l.id = lc.listing_id WHERE l.status = 'active'")->fetchColumn();
    echo "📊 Total visible listings now: {$visible_count}\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
