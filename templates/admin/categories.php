<div class="admin-layout">
    <!-- Sidebar -->
    <?php $active_page = 'categories'; include __DIR__ . '/_sidebar.php'; ?>

    <!-- Content -->
    <div class="admin-content">
        <div class="admin-header" style="display:flex;justify-content:space-between;align-items:center;">
            <div>
                <h1 style="font-family:var(--font-heading);font-size:var(--text-2xl);">🏷️ Category Manager</h1>
                <p class="text-muted">Manage business, tourism, and creator taxonomies.</p>
            </div>
            <a href="/admin/categories/add" class="btn btn-primary"><i data-lucide="plus" style="width:18px;height:18px;margin-right:4px;"></i> Add Category</a>
        </div>

        <div class="card" style="margin-top:var(--space-8);overflow:hidden;padding:0;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:60px;">Icon</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Type</th>
                        <th style="text-align:center;">Listings</th>
                        <th style="text-align:center;">Order</th>
                        <th style="text-align:center;">Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td style="font-size:24px;text-align:center;"><?= clean($cat['icon']) ?></td>
                        <td>
                            <strong><?= clean($cat['name']) ?></strong>
                        </td>
                        <td><code><?= clean($cat['slug']) ?></code></td>
                        <td>
                            <span class="badge" style="background:var(--gray-100);color:var(--gray-700);text-transform:capitalize;">
                                <?= clean($cat['type']) ?>
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <span class="badge badge-secondary"><?= $cat['listing_count'] ?></span>
                        </td>
                        <td style="text-align:center;"><?= $cat['sort_order'] ?></td>
                        <td style="text-align:center;">
                            <?php if ($cat['is_active']): ?>
                                <span class="badge badge-success">Active</span>
                            <?php else: ?>
                                <span class="badge badge-closed">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:right;">
                            <div style="display:flex;justify-content:flex-end;gap:8px;">
                                <a href="/admin/categories/edit/<?= $cat['id'] ?>" class="btn btn-icon" title="Edit">
                                    <i data-lucide="edit-3" style="width:16px;height:16px;"></i>
                                </a>
                                <form action="/admin/categories/delete" method="POST" onsubmit="return confirm('Archive this category? Listings will be moved to a default category.');" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                    <button type="submit" class="btn btn-icon text-danger" title="Delete">
                                        <i data-lucide="trash-2" style="width:16px;height:16px;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
