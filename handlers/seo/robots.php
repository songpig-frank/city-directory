<?php
/**
 * SEO: robots.txt
 */
header('Content-Type: text/plain');
$base = config('base_url');
echo "User-agent: *\n";
echo "Allow: /\n";
echo "Disallow: /admin/\n";
echo "Disallow: /handlers/\n";
echo "Disallow: /includes/\n";
echo "Disallow: /api/\n";
echo "\n";
echo "Sitemap: {$base}/sitemap.xml\n";
exit;
