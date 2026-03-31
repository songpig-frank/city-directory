<?php
/**
 * Admin: Responsive Preview Wrapper
 */
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';

auth_require('admin');

$preview_url = $_GET['url'] ?? '/';
$device = $_GET['device'] ?? 'desktop';

$title = "Preview: " . $preview_url;

// In preview mode, we use a custom minimal layout or just the wrapper template
include __DIR__ . '/../../templates/admin/preview-wrapper.php';
