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
                                <div style="display:flex;gap:4px;flex-wrap:wrap;">
                                    <a href="<?= listing_url($item) ?>" target="_blank" class="btn btn-ghost" title="View Public Page" style="padding:4px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;"><i data-lucide="external-link" style="width:16px;height:16px;"></i></a>
                                    
                                    <a href="/admin/listings/edit/<?= $item['id'] ?>" class="btn btn-ghost" title="Edit Listing" style="padding:4px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;color:var(--primary);"><i data-lucide="edit-3" style="width:16px;height:16px;"></i></a>

                                    <form method="POST" action="/admin/listings/<?= $item['id'] ?>/status" style="display:inline;">
                                        <?= csrf_field() ?>
                                        <?php if ($item['status'] === 'pending'): ?>
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit" class="btn btn-ghost" title="Approve" style="padding:4px;width:32px;height:32px;color:var(--success);"><i data-lucide="check-circle" style="width:16px;height:16px;"></i></button>
                                        <?php elseif ($item['status'] === 'active'): ?>
                                            <input type="hidden" name="status" value="expired">
                                            <button type="submit" class="btn btn-ghost" title="Archive/Expire" style="padding:4px;width:32px;height:32px;color:var(--error);"><i data-lucide="archive" style="width:16px;height:16px;"></i></button>
                                        <?php endif; ?>
                                    </form>
                                </div>
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
