<div class="admin-layout">
    <!-- Sidebar -->
    <?php $active_page = 'dashboard'; include __DIR__ . '/_sidebar.php'; ?>

    <!-- Content -->
    <div class="admin-content">
        <div class="admin-header" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h1 style="font-family:var(--font-heading); font-size:var(--text-2xl);">🚀 Dashboard (V3 Active)</h1>
                <span class="text-sm text-muted">Welcome back, <?= clean($_SESSION['user_name'] ?? 'Admin') ?></span>
            </div>
            <div style="display:flex; gap:var(--space-3);">
                <a href="/seed-tampakan.php?key=tampakan2026" target="_blank" class="btn btn-ghost btn-sm" style="color:var(--primary); border:1px solid var(--primary-light);">
                    <i data-lucide="refresh-ccw" style="width:16px;height:16px;margin-right:6px;"></i> Sync Production
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value" style="color:var(--primary);"><?= $stats['active'] ?></div>
                <div class="stat-label">Active Listings</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color:var(--warning);"><?= $stats['pending'] ?></div>
                <div class="stat-label">Pending Review</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color:var(--error);"><?= $stats['expired'] ?></div>
                <div class="stat-label">Expired</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color:var(--featured);"><?= $stats['featured'] ?></div>
                <div class="stat-label">Featured</div>
            </div>
        </div>

        <!-- Pending Listings -->
        <?php if (!empty($pending)): ?>
        <div style="margin-bottom:var(--space-8);">
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending as $item): ?>
                        <tr>
                            <td><strong><?= clean($item['name']) ?></strong></td>
                            <td><?= clean($item['category_name']) ?></td>
                            <td><span class="badge badge-category"><?= ucfirst($item['type']) ?></span></td>
                            <td><?= time_ago($item['created_at']) ?></td>
                            <td>
                                <form method="POST" action="/admin/listings/<?= $item['id'] ?>/status" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="status" value="active">
                                    <button type="submit" class="btn btn-primary btn-sm">✓ Approve</button>
                                </form>
                                <form method="POST" action="/admin/listings/<?= $item['id'] ?>/status" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="btn btn-ghost btn-sm">✗ Reject</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Expiring Soon -->
        <?php if (!empty($expiring)): ?>
        <div>
            <h2 style="font-family:var(--font-heading);margin-bottom:var(--space-4);">⚠️ Expiring Within 7 Days</h2>
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Expires</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($expiring as $item): ?>
                        <tr>
                            <td><strong><?= clean($item['name']) ?></strong></td>
                            <td><?= clean($item['category_name']) ?></td>
                            <td style="color:var(--error);font-weight:600;"><?= format_date($item['expires_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
