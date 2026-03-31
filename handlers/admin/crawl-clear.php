<?php
/**
 * Admin Action: Clear Crawler Results
 */
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';

auth_require('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/crawler');
}

if (!csrf_validate()) {
    flash('error', 'Security token invalid.');
    redirect('/admin/crawler');
}

unset($_SESSION['crawl_results']);
flash('info', 'Crawler results cleared.');

redirect('/admin/crawler');
