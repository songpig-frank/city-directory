<?php
/**
 * Owner Portal Sidebar Partial
 */
$active = $active_page ?? '';
?>
<aside class="admin-sidebar portal-sidebar">
    <div style="margin-bottom:var(--space-6);">
        <a href="/" style="color:var(--primary-light);font-weight:700;font-size:var(--text-lg);">
            <i data-lucide="arrow-left" style="width:16px;height:16px;vertical-align:middle;margin-right:4px;"></i>
            <?= clean(config('site_name')) ?>
        </a>
    </div>
    
    <div class="sidebar-label" style="text-transform:uppercase;font-size:10px;color:var(--gray-500);margin-bottom:8px;padding-left:12px;">Owner Services</div>
    
    <a href="/portal" <?= $active === 'dashboard' ? 'class="active"' : '' ?>>
        <i data-lucide="layout-dashboard" style="width:18px;height:18px;"></i> Dashboard
    </a>
    <a href="/portal/listings" <?= $active === 'listings' ? 'class="active"' : '' ?>>
        <i data-lucide="list" style="width:18px;height:18px;"></i> My Listing(s)
    </a>
    <a href="/submit" <?= $active === 'submit' ? 'class="active"' : '' ?>>
        <i data-lucide="plus-circle" style="width:18px;height:18px;"></i> Create New
    </a>
    
    <div class="sidebar-label" style="text-transform:uppercase;font-size:10px;color:var(--gray-500);margin-top:24px;margin-bottom:8px;padding-left:12px;">Account</div>
    
    <a href="/profile" <?= $active === 'profile' ? 'class="active"' : '' ?>>
        <i data-lucide="user" style="width:18px;height:18px;"></i> My Profile
    </a>
    <a href="/logout">
        <i data-lucide="log-out" style="width:18px;height:18px;"></i> Logout
    </a>

    <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
    <div style="margin-top:auto; padding:12px; border-top:1px solid var(--gray-200);">
        <a href="/admin" class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;gap:4px;">
            <i data-lucide="shield"></i> Go to Admin
        </a>
    </div>
    <?php endif; ?>
</aside>
