<div class="admin-layout">
    <!-- Sidebar -->
    <?php $active_page = 'listings'; include __DIR__ . '/_sidebar.php'; ?>

    <div class="admin-content">
        <div class="admin-header flex" style="justify-content:space-between;align-items:center;">
            <h1>Manage Listings</h1>
            <a href="/submit" class="btn btn-primary" target="_blank">+ Add New</a>
        </div>

        <div style="margin-bottom:var(--space-8);">
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listings as $item): ?>
                        <tr>
                            <td><?= $item['id'] ?></td>
                            <td>
                                <strong><?= clean($item['name']) ?></strong><br>
                                <span class="text-xs text-muted"><?= $item['owner_name'] ?? 'System' ?></span>
                            </td>
                            <td><?= clean($item['category_name'] ?? 'Uncategorized') ?></td>
                            <td><span class="badge badge-<?= $item['type'] === 'creator' ? 'featured' : 'active' ?>"><?= ucfirst($item['type']) ?></span></td>
                            <td>
                                <span class="badge badge-<?= $item['status'] === 'active' ? 'active' : ($item['status'] === 'pending' ? 'pending' : 'error') ?>">
                                    <?= ucfirst($item['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= listing_url($item) ?>" target="_blank" class="btn btn-ghost" style="padding:var(--space-1) var(--space-2);font-size:var(--text-xs);">View</a>
                                <!-- Support status toggling utilizing the existing backend router -->
                                <form method="POST" action="/admin/listings/<?= $item['id'] ?>/status" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <?php if ($item['status'] === 'pending'): ?>
                                        <input type="hidden" name="status" value="active">
                                        <button type="submit" class="btn btn-ghost" style="color:var(--success);padding:var(--space-1) var(--space-2);font-size:var(--text-xs);">Approve</button>
                                    <?php elseif ($item['status'] === 'active'): ?>
                                        <input type="hidden" name="status" value="expired">
                                        <button type="submit" class="btn btn-ghost" style="color:var(--error);padding:var(--space-1) var(--space-2);font-size:var(--text-xs);">Expire</button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($listings)): ?>
                        <tr><td colspan="6" class="text-center text-muted" style="padding:var(--space-6);">No listings found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div style="margin-top:var(--space-4);display:flex;gap:var(--space-2);">
                <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>" class="btn btn-ghost">← Prev</a>
                <?php endif; ?>
                <?php if ($page * $limit < $total): ?>
                <a href="?page=<?= $page + 1 ?>" class="btn btn-ghost">Next →</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
