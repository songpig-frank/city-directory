<?php
/**
 * SEO: Dynamic Sitemap Generator
 */
header('Content-Type: application/xml; charset=UTF-8');

$base = config('base_url');

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url><loc><?= $base ?></loc><priority>1.0</priority><changefreq>daily</changefreq></url>
    <url><loc><?= $base ?>/directory</loc><priority>0.9</priority><changefreq>daily</changefreq></url>
    <url><loc><?= $base ?>/tourism</loc><priority>0.8</priority><changefreq>weekly</changefreq></url>
    <url><loc><?= $base ?>/community</loc><priority>0.7</priority><changefreq>weekly</changefreq></url>
    <url><loc><?= $base ?>/community/vloggers</loc><priority>0.8</priority><changefreq>weekly</changefreq></url>
    <url><loc><?= $base ?>/essential-services</loc><priority>0.8</priority><changefreq>monthly</changefreq></url>
    <url><loc><?= $base ?>/contact</loc><priority>0.5</priority><changefreq>monthly</changefreq></url>
    <url><loc><?= $base ?>/map</loc><priority>0.7</priority><changefreq>daily</changefreq></url>

    <?php
    // Categories
    $categories = db_query("SELECT slug FROM categories WHERE is_active = 1");
    foreach ($categories as $cat):
    ?>
    <url><loc><?= $base ?>/directory/<?= $cat['slug'] ?></loc><priority>0.8</priority><changefreq>daily</changefreq></url>
    <?php endforeach; ?>

    <?php
    // Active listings
    $listings = db_query("SELECT slug, type, updated_at FROM listings WHERE status = 'active' ORDER BY updated_at DESC");
    foreach ($listings as $listing):
    ?>
    <url>
        <loc><?= $base ?>/<?= $listing['type'] ?>/<?= $listing['slug'] ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($listing['updated_at'])) ?></lastmod>
        <priority>0.6</priority>
        <changefreq>weekly</changefreq>
    </url>
    <?php endforeach; ?>

    <?php
    // Blog posts
    $posts = db_query("SELECT slug, published_at FROM posts WHERE status = 'published' ORDER BY published_at DESC");
    foreach ($posts as $post):
    ?>
    <url>
        <loc><?= $base ?>/community/blog/<?= $post['slug'] ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($post['published_at'])) ?></lastmod>
        <priority>0.5</priority>
        <changefreq>monthly</changefreq>
    </url>
    <?php endforeach; ?>
</urlset>
<?php exit;
