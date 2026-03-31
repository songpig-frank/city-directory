<?php
/**
 * Admin Sidebar Partial
 * Shared across all admin templates. Uses Lucide icons.
 * Expects: $active_page (string), $stats (array, optional)
 */
$active = $active_page ?? '';
$role = $_SESSION['user_role'] ?? 'owner';
$msg_count = $stats['messages'] ?? 0;
?>
<aside class="admin-sidebar">
    <div style="margin-bottom:var(--space-6);">
        <a href="/" style="color:var(--primary-light);font-weight:700;font-size:var(--text-lg);">
            <i data-lucide="arrow-left" style="width:16px;height:16px;vertical-align:middle;margin-right:4px;"></i>
            <?= clean(config('site_name')) ?>
        </a>
    </div>
    <a href="/admin" <?= $active === 'dashboard' ? 'class="active"' : '' ?>>
        <i data-lucide="layout-dashboard" style="width:18px;height:18px;"></i> Dashboard
    </a>
    <a href="/admin/listings" <?= $active === 'listings' ? 'class="active"' : '' ?>>
        <i data-lucide="list" style="width:18px;height:18px;"></i> Listings
    </a>
    <?php if ($role === 'admin'): ?>
    <a href="/admin/import" <?= $active === 'import' ? 'class="active"' : '' ?>>
        <i data-lucide="upload" style="width:18px;height:18px;"></i> Bulk Import
    </a>
    <a href="/admin/promotions" <?= $active === 'promotions' ? 'class="active"' : '' ?>>
        <i data-lucide="badge-dollar-sign" style="width:18px;height:18px;"></i> Promotions
    </a>
    <?php endif; ?>
    <a href="/admin/blog" <?= $active === 'blog' ? 'class="active"' : '' ?>>
        <i data-lucide="file-text" style="width:18px;height:18px;"></i> Blog Posts
    </a>
    <?php if ($role === 'admin'): ?>
    <a href="/admin/claims" <?= $active === 'admin/claims' ? 'class="active"' : '' ?>>
        <i data-lucide="shield-check" style="width:18px;height:18px;"></i> Business Claims
        <?php if (($stats['pending_claims'] ?? 0) > 0): ?>
        <span class="badge badge-pending" style="margin-left:auto;"><?= $stats['pending_claims'] ?></span>
        <?php endif; ?>
    </a>
    <a href="/admin/users" <?= $active === 'users' ? 'class="active"' : '' ?>>
        <i data-lucide="users" style="width:18px;height:18px;"></i> Users
    </a>
    <?php endif; ?>
    <a href="/admin/messages" <?= $active === 'messages' ? 'class="active"' : '' ?>>
        <i data-lucide="mail" style="width:18px;height:18px;"></i> Messages
        <?php if ($msg_count > 0): ?>
        <span class="badge badge-pending" style="margin-left:auto;"><?= $msg_count ?></span>
        <?php endif; ?>
    </a>
    <?php if ($role === 'admin'): ?>
    <a href="/admin/settings" <?= $active === 'settings' ? 'class="active"' : '' ?>>
        <i data-lucide="settings" style="width:18px;height:18px;"></i> Settings
    </a>
    <?php endif; ?>
</aside>
