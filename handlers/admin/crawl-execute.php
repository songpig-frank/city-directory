<?php
/**
 * Admin Action: Execute Crawl
 */
require_once __DIR__ . '/../../includes/config-loader.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/CrawlService.php';

auth_require('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/crawler');
}

if (!csrf_validate()) {
    flash('error', 'Security token invalid.');
    redirect('/admin/crawler');
}

$url = trim($_POST['url'] ?? '');
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    flash('error', 'Please enter a valid URL.');
    redirect('/admin/crawler');
}

try {
    $service = new CrawlService();
    $results = $service->scrape($url);
    
    if (empty($results)) {
        flash('info', 'Crawl completed but no structured data was found at that URL.');
    } else {
        $_SESSION['crawl_results'] = $results;
        flash('success', 'Successfully extracted ' . count($results) . ' potential listings!');
    }

} catch (Exception $e) {
    error_log("Crawl Error: " . $e->getMessage());
    flash('error', 'Crawl failed: ' . $e->getMessage());
}

redirect('/admin/crawler');
