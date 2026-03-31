<div class="admin-layout">
    <!-- Sidebar -->
    <?php $active_page = 'promotions'; include __DIR__ . '/_sidebar.php'; ?>

    <!-- Content -->
    <div class="admin-content">
        <div class="admin-header">
            <h1 style="font-family:var(--font-heading);font-size:var(--text-2xl);"><i data-lucide="megaphone"></i> Promotions</h1>
            <p class="text-muted">Manage featured listings and spotlight placements.</p>
        </div>

        <!-- Add Promotion -->
        <div class="card p-6" style="margin-bottom:var(--space-8);">
            <h3 style="font-family:var(--font-heading);margin-bottom:var(--space-4);">Feature a Listing</h3>
            <form action="/admin/promotions" method="POST" style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:var(--space-4);align-items:flex-end;">
                <?= csrf_field() ?>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Select Listing</label>
                    <select name="listing_id" class="form-select" required>
                        <option value="">-- Select Listing --</option>
                        <?php foreach ($all_listings as $l): ?>
                        <option value="<?= $l['id'] ?>"><?= clean($l['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Days to Feature</label>
                    <input type="number" name="days" value="30" class="form-input" required>
                </div>
                <button type="submit" class="btn btn-primary">Feature Now</button>
            </form>
        </div>

        <div class="card" style="padding:0;overflow:hidden;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Listing</th>
                        <th>Category</th>
                        <th>Expires</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($featured)): ?>
                    <tr>
                        <td colspan="4" style="text-align:center;padding:var(--space-8);color:var(--gray-400);">No featured listings at the moment.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($featured as $item): ?>
                    <tr>
                        <td style="font-weight:600;"><?= clean($item['name']) ?></td>
                        <td><?= clean($item['category_name']) ?></td>
                        <td class="<?= strtotime($item['featured_until']) < time() ? 'text-danger font-bold' : 'text-muted' ?>">
                            <?= date('M j, Y', strtotime($item['featured_until'])) ?>
                        </td>
                        <td style="text-align:right;">
                            <form action="/admin/promotions/remove" method="POST" style="display:inline;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);">Stop</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
