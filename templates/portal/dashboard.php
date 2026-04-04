<div class="admin-layout portal-layout">
    <!-- Sidebar -->
    <?php $active_page = 'dashboard'; include __DIR__ . '/_sidebar.php'; ?>

    <div class="admin-content">
        <div class="admin-header">
            <h1 style="font-family:var(--font-heading);font-size:var(--text-2xl);">🏠 Owner Dashboard</h1>
            <p class="text-muted">Welcome back! Manage your business presence in <?= clean(config('city')) ?>.</p>
        </div>

        <div class="grid grid-3 gap-6 mt-8">
            <div class="card p-6" style="border-left: 4px solid var(--primary);">
                <div class="text-sm text-muted mb-1 text-uppercase" style="letter-spacing:1px;font-size:10px;">Total Listings</div>
                <div class="text-3xl font-bold"><?= count($listings) ?></div>
            </div>
            <div class="card p-6" style="border-left: 4px solid var(--success);">
                <div class="text-sm text-muted mb-1 text-uppercase" style="letter-spacing:1px;font-size:10px;">Active Publicly</div>
                <div class="text-3xl font-bold">
                    <?= count(array_filter($listings, fn($l) => $l['status'] === 'active')) ?>
                </div>
            </div>
            <div class="card p-6" style="border-left: 4px solid var(--indigo-500);">
                <div class="text-sm text-muted mb-1 text-uppercase" style="letter-spacing:1px;font-size:10px;">Total Page Views</div>
                <div class="text-3xl font-bold">
                    <?= array_sum(array_column($listings, 'view_count')) ?>
                </div>
            </div>
        </div>

        <div class="mt-12">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--space-4);">
                <h2 style="font-family:var(--font-heading);font-size:var(--text-lg);">Your Business Listings</h2>
                <a href="/submit" class="btn btn-primary btn-sm">+ Add New</a>
            </div>

            <?php if (empty($listings)): ?>
            <div class="card p-12 text-center">
                <i data-lucide="plus-circle" style="width:48px;height:48px;color:var(--gray-300);margin-bottom:16px;"></i>
                <p class="text-muted">You haven't added any businesses yet.</p>
                <a href="/submit" class="btn btn-primary mt-4">Create Your First Listing</a>
            </div>
            <?php else: ?>
            <div class="grid grid-1 gap-4">
                <?php foreach ($listings as $item): ?>
                <div class="card p-4" style="display:flex;align-items:center;gap:16px; transition:transform 0.2s ease;">
                    <div style="width:80px;height:60px;background:var(--gray-100);border-radius:var(--radius-md);overflow:hidden;flex-shrink:0;">
                        <img src="<?= get_listing_image($item) ?>" style="width:100%;height:100%;object-fit:cover;">
                    </div>
                    <div style="flex-grow:1;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <h3 style="font-size:var(--text-base);margin:0;"><?= clean($item['name']) ?></h3>
                            <span class="badge badge-<?= $item['status'] === 'active' ? 'active' : ($item['status'] === 'pending' ? 'pending' : 'expired') ?>">
                                <?= ucfirst($item['status']) ?>
                            </span>
                            <?php if ($item['is_featured']): ?>
                            <span class="badge badge-featured">⭐ Featured</span>
                            <?php endif; ?>
                        </div>
                        <div class="text-xs text-muted mt-1">
                            <?= clean($item['category_icon']) ?> <?= clean($item['category_name']) ?> • <?= $item['view_count'] ?> views
                        </div>
                    </div>
                    <div style="display:flex;gap:8px;">
                        <a href="<?= listing_url($item) ?>" target="_blank" class="btn btn-ghost" title="View Page">
                            <i data-lucide="external-link" style="width:16px;height:16px;"></i>
                        </a>
                        <a href="/portal/listings/edit/<?= $item['id'] ?>" class="btn btn-ghost" title="Edit Business Details">
                            <i data-lucide="edit-3" style="width:16px;height:16px;"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
